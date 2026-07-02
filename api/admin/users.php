<?php
/**
 * Bloombit - Admin User Management API
 * GET /api/admin/users.php - List users (pagination, search, status filter)
 * GET /api/admin/users.php?id=X - Single user detail with wallet, investments
 * POST /api/admin/users.php - Actions: update, block, unblock, reset_password, adjust_balance, adjust_profit, adjust_referral_bonus
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
require_once dirname(__DIR__, 2) . '/includes/admin-audit-log.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id > 0) {
        // Single user detail (avatar_url optional - added in migration)
        $hasAvatar = false;
        try {
            $colCheck = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
            $hasAvatar = $colCheck && $colCheck->rowCount() > 0;
        } catch (Throwable $e) {}
        $cols = 'id, email, name, role, email_verified, active, created_at, updated_at';
        if ($hasAvatar) $cols .= ', avatar_url';
        $hasCachedUsd = false;
        try {
            $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
            $hasCachedUsd = $bc && $bc->rowCount() > 0;
        } catch (Throwable $e) {}
        if ($hasCachedUsd) $cols .= ', last_balance_usd, last_balance_usd_updated_at';
        $stmt = $pdo->prepare("SELECT {$cols} FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'User not found']);
            exit;
        }

        $user['id'] = (int) $user['id'];
        $user['active'] = (bool) $user['active'];
        $user['email_verified'] = (bool) $user['email_verified'];
        $user['two_factor_enabled'] = (bool) (isset($user['two_factor_enabled']) ? $user['two_factor_enabled'] : 0);
        if (!isset($user['avatar_url'])) $user['avatar_url'] = null;

        // Check if two_factor_enabled, kyc_status columns exist
        try {
            $colStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'");
            $kycCol = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
            $extraCols = 'two_factor_enabled, admin_notes';
            if ($kycCol && $kycCol->rowCount() > 0) $extraCols .= ', kyc_status';
            if ($colStmt->rowCount() > 0) {
                $fullStmt = $pdo->prepare("SELECT {$extraCols} FROM users WHERE id = ?");
                $fullStmt->execute([$id]);
                $extra = $fullStmt->fetch(PDO::FETCH_ASSOC);
                $user['two_factor_enabled'] = (bool) ($extra['two_factor_enabled'] ?? 0);
                $user['admin_notes'] = $extra['admin_notes'] ?? '';
                $user['kyc_status'] = $extra['kyc_status'] ?? 'none';
            } else {
                $user['two_factor_enabled'] = false;
                $user['admin_notes'] = '';
                $user['kyc_status'] = 'none';
            }
        } catch (Throwable $e) {
            $user['two_factor_enabled'] = false;
            $user['admin_notes'] = '';
            $user['kyc_status'] = 'none';
        }

        // Centralized USD wallet
        $usdBal = get_user_usd_balance($pdo, $id);
        $user['wallet_balances'] = $usdBal > 0 ? [
            ['currency' => user_usd_wallet_currency(), 'amount' => $usdBal],
        ] : [];
        $user['total_balance_usd'] = round($usdBal, 2);
        $user['total_balance_usd_updated_at'] = $hasCachedUsd ? ($user['last_balance_usd_updated_at'] ?? null) : null;
        $user['total_profit'] = get_user_total_profit($pdo, $id);
        $user['total_referral_bonus'] = get_user_total_referral_bonus($pdo, $id);

        // Active + paused investments with plan names
        $stmt = $pdo->prepare('
            SELECT ui.id, ui.amount, ui.start_date, ui.status, ui.duration_days AS investment_duration_days, p.name AS plan_name, p.yield_min, p.yield_max, p.duration_days AS plan_duration_days
            FROM user_investments ui
            JOIN plans p ON p.id = ui.plan_id
            WHERE ui.user_id = ? AND ui.status IN ("active", "paused")
            ORDER BY ui.status ASC, ui.created_at DESC
        ');
        $stmt->execute([$id]);
        $user['investments'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user['investments'][] = [
                'id' => (int) $row['id'],
                'amount' => (float) $row['amount'],
                'plan_name' => $row['plan_name'],
                'start_date' => $row['start_date'],
                'status' => $row['status'],
                'yield_min' => (float) $row['yield_min'],
                'yield_max' => (float) $row['yield_max'],
                'duration_days' => (int) ($row['investment_duration_days'] ?? $row['plan_duration_days'] ?? 30),
            ];
        }

        // Recent transactions (last 10)
        $stmt = $pdo->prepare('SELECT id, type, amount, currency, status, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
        $stmt->execute([$id]);
        $user['recent_transactions'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user['recent_transactions'][] = [
                'id' => (int) $row['id'],
                'type' => $row['type'],
                'amount' => (float) $row['amount'],
                'currency' => $row['currency'],
                'status' => $row['status'],
                'created_at' => $row['created_at'],
            ];
        }

        echo json_encode(['success' => true, 'data' => $user]);
        exit;
    }

    // List users
    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? 'all';
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = min(50, max(10, (int) ($_GET['per_page'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    $where = ['u.role = "user"'];
    $params = [];

    if ($search !== '') {
        $where[] = '(u.name LIKE ? OR u.email LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
    }

    if ($status === 'active') {
        $where[] = 'u.active = 1';
    } elseif ($status === 'suspended') {
        $where[] = 'u.active = 0';
    }

    $whereClause = implode(' AND ', $where);

    $hasAvatarCol = false;
    $hasCachedUsdCol = false;
    try {
        $ac = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
        $hasAvatarCol = $ac && $ac->rowCount() > 0;
        $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        $hasCachedUsdCol = $bc && $bc->rowCount() > 0;
    } catch (Throwable $e) {}

    $countSql = "SELECT COUNT(*) FROM users u WHERE {$whereClause}";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $avatarCol = $hasAvatarCol ? ', u.avatar_url' : '';
    $balCol = $hasCachedUsdCol ? ', u.last_balance_usd, u.last_balance_usd_updated_at' : '';
    $sql = "
        SELECT u.id, u.email, u.name, u.active, u.email_verified, u.created_at, u.updated_at{$avatarCol}{$balCol},
               (SELECT COUNT(*) FROM user_investments WHERE user_id = u.id AND status = 'active') AS active_plans_count
        FROM users u
        WHERE {$whereClause}
        ORDER BY u.created_at DESC
        LIMIT " . (int) $perPage . " OFFSET " . (int) $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kyc = 'pending';
        if (!$row['active']) {
            $kyc = 'suspended';
        } elseif ($row['email_verified']) {
            $kyc = 'verified';
        }
        $users[] = [
            'id' => (int) $row['id'],
            'email' => $row['email'],
            'name' => $row['name'] ?: 'User #' . $row['id'],
            'active' => (bool) $row['active'],
            'email_verified' => (bool) $row['email_verified'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'avatar_url' => $row['avatar_url'] ?? null,
            'total_balance_usd' => $hasCachedUsdCol ? (float) ($row['last_balance_usd'] ?? 0) : 0.0,
            'total_balance_usd_updated_at' => $hasCachedUsdCol ? ($row['last_balance_usd_updated_at'] ?? null) : null,
            'active_plans_count' => (int) $row['active_plans_count'],
            'kyc_status' => $kyc,
        ];
    }
    // NOTE: We intentionally do NOT call CoinGecko here.
    // List view uses users.last_balance_usd (cached snapshot) for stable display.

    echo json_encode([
        'success' => true,
        'data' => $users,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $total > 0 ? (int) ceil($total / $perPage) : 1,
        ],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;
    if (empty($input) || !isset($input['action'])) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    }
    $action = trim($input['action'] ?? '');

    if ($action === 'add_user') {
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $phone = trim($input['phone'] ?? '');
        $referral = trim($input['referral'] ?? '');
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email and password are required']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid email address']);
            exit;
        }
        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
            exit;
        }
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email already registered']);
            exit;
        }
        $avatarUrl = null;
        if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            if (isset($allowed[$mime]) && $file['size'] <= 2 * 1024 * 1024) {
                $baseDir = dirname(__DIR__, 2) . '/uploads/avatars';
                if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);
                $ext = $allowed[$mime];
                $filename = 'admin_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $baseDir . '/' . $filename)) {
                    $avatarUrl = '/uploads/avatars/' . $filename;
                }
            }
        }
        $cols = ['email', 'password_hash', 'name', 'role', 'email_verified', 'active'];
        $vals = [$email, password_hash($password, PASSWORD_DEFAULT), $name ?: '', 'user', 1, 1];
        $ph = ['?', '?', '?', '?', '?', '?'];
        try {
            if ($pdo->query("SHOW COLUMNS FROM users LIKE 'phone_number'")->rowCount() > 0) {
                $cols[] = 'phone_number';
                $vals[] = $phone ?: null;
                $ph[] = '?';
            }
        } catch (Throwable $e) {}
        try {
            if ($pdo->query("SHOW COLUMNS FROM users LIKE 'referral_code'")->rowCount() > 0) {
                $cols[] = 'referral_code';
                $vals[] = $referral ?: null;
                $ph[] = '?';
            }
        } catch (Throwable $e) {}
        if ($avatarUrl) {
            $cols[] = 'avatar_url';
            $vals[] = $avatarUrl;
            $ph[] = '?';
        }
        // Login 2FA is opt-in (user profile). Do not force two_factor_enabled on admin-created users.
        $sql = 'INSERT INTO users (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $ph) . ')';
        $pdo->prepare($sql)->execute($vals);
        $newId = (int) $pdo->lastInsertId();
        admin_audit_log(
            $pdo,
            'create',
            'user',
            $newId,
            'Created user #' . $newId . ': ' . $email,
            null,
            ['id' => $newId, 'email' => $email, 'name' => $name ?: null]
        );
        echo json_encode(['success' => true, 'data' => ['message' => 'User added', 'user_id' => $newId]]);
        exit;
    }

    $userId = isset($input['user_id']) ? (int) $input['user_id'] : 0;
    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT id, role FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$target) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    if ($target['role'] === 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Cannot modify admin user']);
        exit;
    }

    switch ($action) {
        case 'update':
            $name = trim($input['name'] ?? '');
            $email = trim($input['email'] ?? '');
            if ($email === '') {
                echo json_encode(['success' => false, 'error' => 'Email is required']);
                exit;
            }
            $beforeUser = $pdo->prepare('SELECT id, email, name, active, email_verified FROM users WHERE id = ?');
            $beforeUser->execute([$userId]);
            $beforeSnapshot = $beforeUser->fetch(PDO::FETCH_ASSOC) ?: null;
            $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $checkStmt->execute([$email, $userId]);
            if ($checkStmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Email already in use']);
                exit;
            }
            $pdo->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?')->execute([$name, $email, $userId]);
            if (!empty($input['password']) && strlen(trim($input['password'])) >= 8) {
                $hash = password_hash(trim($input['password']), PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $userId]);
            }
            try {
                if (isset($input['two_factor_enabled'])) {
                    $pdo->prepare('UPDATE users SET two_factor_enabled = ? WHERE id = ?')->execute([$input['two_factor_enabled'] ? 1 : 0, $userId]);
                }
                if (isset($input['admin_notes'])) {
                    $pdo->prepare('UPDATE users SET admin_notes = ? WHERE id = ?')->execute([$input['admin_notes'], $userId]);
                }
            } catch (Throwable $e) {
                // Columns may not exist yet
            }
            $afterUser = $pdo->prepare('SELECT id, email, name, active, email_verified FROM users WHERE id = ?');
            $afterUser->execute([$userId]);
            $afterSnapshot = $afterUser->fetch(PDO::FETCH_ASSOC) ?: null;
            admin_audit_log(
                $pdo,
                'update',
                'user',
                $userId,
                'Updated user profile #' . $userId,
                $beforeSnapshot,
                $afterSnapshot,
                ['password_changed' => !empty($input['password']) && strlen(trim($input['password'])) >= 8]
            );
            echo json_encode(['success' => true, 'data' => ['message' => 'Profile updated']]);
            exit;

        case 'block':
            $pdo->prepare('UPDATE users SET active = 0 WHERE id = ?')->execute([$userId]);
            admin_audit_log($pdo, 'block', 'user', $userId, 'Blocked user #' . $userId);
            echo json_encode(['success' => true, 'data' => ['message' => 'User blocked']]);
            exit;

        case 'unblock':
            $pdo->prepare('UPDATE users SET active = 1 WHERE id = ?')->execute([$userId]);
            admin_audit_log($pdo, 'unblock', 'user', $userId, 'Unblocked user #' . $userId);
            echo json_encode(['success' => true, 'data' => ['message' => 'User unblocked']]);
            exit;

        case 'reset_password':
            $newPassword = trim($input['password'] ?? '');
            if (strlen($newPassword) < 8) {
                echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
                exit;
            }
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $userId]);
            admin_audit_log($pdo, 'update', 'user', $userId, 'Reset password for user #' . $userId, null, null, ['field' => 'password']);
            echo json_encode(['success' => true, 'data' => ['message' => 'Password reset']]);
            exit;

        case 'adjust_balance':
            $type = strtolower(trim($input['type'] ?? ''));
            $amount = (float) ($input['amount'] ?? 0);
            if ($type !== 'credit' && $type !== 'debit') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Type must be credit or debit']);
                exit;
            }
            if ($amount <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Amount must be greater than 0']);
                exit;
            }
            $amountStr = number_format($amount, 18, '.', '');
            $adminId = (int) ($_SESSION['user_id'] ?? 0);
            $ref = 'admin_' . ($type === 'credit' ? 'credit' : 'debit') . '_' . $adminId . '_' . $userId . '_' . date('Ymd_His');
            $userStmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ?');
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            $userEmail = $user['email'] ?? null;
            $userName = $user['name'] ?? 'User';

            $pdo->beginTransaction();
            try {
                require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
                $walletCurrency = user_usd_wallet_currency();
                if ($type === 'credit') {
                    credit_user_usd($pdo, $userId, (float) $amountStr);
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'deposit', $amountStr, $walletCurrency, 'completed', $ref]);
                    $pdo->commit();

                    if ($userEmail) {
                        try {
                            require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
                            $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
                            $mail->clearAddresses();
                            $mail->addAddress($userEmail);
                            $mail->Subject = 'Account Balance Credited - ' . get_site_name();
                            $mail->Body = renderEmailTemplate('balance-adjustment.php', [
                                'name' => $userName,
                                'type' => 'credit',
                                'amount' => $amountStr,
                                'currency' => 'USD',
                                'amountUsd' => (float) $amountStr,
                            ]);
                            $mail->AltBody = 'Your account has been credited with USD ' . number_format((float) $amountStr, 2, '.', ',') . '.';
                            $mail->isHTML(true);
                            $mail->send();
                        } catch (Throwable $e) {
                        }
                    }

                    admin_audit_log(
                        $pdo,
                        $type === 'credit' ? 'credit' : 'debit',
                        'user',
                        $userId,
                        ucfirst($type) . 'ed USD balance $' . format_usd_amount((float) $amountStr) . ' for user #' . $userId,
                        null,
                        ['amount_usd' => (float) $amountStr, 'reference' => $ref]
                    );
                    echo json_encode(['success' => true, 'data' => ['message' => 'Balance credited']]);
                } else {
                    if (!debit_user_usd($pdo, $userId, (float) $amountStr)) {
                        $pdo->rollBack();
                        http_response_code(400);
                        echo json_encode(['success' => false, 'error' => 'Insufficient USD balance']);
                        exit;
                    }
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'withdrawal', $amountStr, $walletCurrency, 'completed', $ref]);
                    $pdo->commit();

                    if ($userEmail) {
                        try {
                            require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
                            $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
                            $mail->clearAddresses();
                            $mail->addAddress($userEmail);
                            $mail->Subject = 'Account Balance Debited - ' . get_site_name();
                            $mail->Body = renderEmailTemplate('balance-adjustment.php', [
                                'name' => $userName,
                                'type' => 'debit',
                                'amount' => $amountStr,
                                'currency' => 'USD',
                                'amountUsd' => (float) $amountStr,
                            ]);
                            $mail->AltBody = 'Your account has been debited with USD ' . number_format((float) $amountStr, 2, '.', ',') . '.';
                            $mail->isHTML(true);
                            $mail->send();
                        } catch (Throwable $e) {
                        }
                    }

                    admin_audit_log(
                        $pdo,
                        'debit',
                        'user',
                        $userId,
                        'Debited USD balance $' . format_usd_amount((float) $amountStr) . ' for user #' . $userId,
                        null,
                        ['amount_usd' => (float) $amountStr, 'reference' => $ref]
                    );
                    echo json_encode(['success' => true, 'data' => ['message' => 'Balance debited']]);
                }
            } catch (Throwable $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to adjust balance']);
            }
            exit;

        case 'adjust_profit':
            $type = strtolower(trim($input['type'] ?? ''));
            $amountUsd = round((float) ($input['amount'] ?? $input['amount_usd'] ?? 0), 2);
            if ($type !== 'credit' && $type !== 'debit') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Type must be credit or debit']);
                exit;
            }
            if ($amountUsd <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Amount must be greater than 0']);
                exit;
            }
            $currentProfit = round(get_user_total_profit($pdo, $userId), 2);
            if ($type === 'debit' && $currentProfit + 0.001 < $amountUsd) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Insufficient profit to debit. User total profit is $' . format_usd_amount($currentProfit) . ' USD.',
                ]);
                exit;
            }
            $signedUsd = $type === 'credit' ? $amountUsd : -$amountUsd;
            $amountStr = number_format($signedUsd, 18, '.', '');
            $adminId = (int) ($_SESSION['user_id'] ?? 0);
            $ref = 'admin_profit_' . $type . '_' . $adminId . '_' . $userId . '_' . date('Ymd_His');
            $hasAmountUsdCol = false;
            try {
                $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
                $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
            } catch (Throwable $e) {}
            try {
                if ($hasAmountUsdCol) {
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'profit_adjustment', $amountStr, $signedUsd, 'USD', 'completed', $ref]);
                } else {
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'profit_adjustment', $amountStr, 'USD', 'completed', $ref]);
                }
                $newProfit = get_user_total_profit($pdo, $userId);
                admin_audit_log(
                    $pdo,
                    $type === 'credit' ? 'credit' : 'debit',
                    'user',
                    $userId,
                    ucfirst($type) . 'ed profit $' . format_usd_amount($amountUsd) . ' for user #' . $userId,
                    ['total_profit' => $currentProfit],
                    ['total_profit' => $newProfit, 'amount_usd' => $signedUsd]
                );
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'message' => $type === 'credit' ? 'Profit credited' : 'Profit debited',
                        'total_profit' => $newProfit,
                    ],
                ]);
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to adjust profit. Run database migration if profit_adjustment type is missing.']);
            }
            exit;

        case 'adjust_referral_bonus':
            $type = strtolower(trim($input['type'] ?? ''));
            $amountUsd = round((float) ($input['amount'] ?? $input['amount_usd'] ?? 0), 2);
            if ($type !== 'credit' && $type !== 'debit') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Type must be credit or debit']);
                exit;
            }
            if ($amountUsd <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Amount must be greater than 0']);
                exit;
            }
            $currentReferral = round(get_user_total_referral_bonus($pdo, $userId), 2);
            if ($type === 'debit' && $currentReferral + 0.001 < $amountUsd) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Insufficient referral bonus to debit. User referral bonus (earned) is $' . format_usd_amount($currentReferral) . ' USD.',
                ]);
                exit;
            }
            $signedUsd = $type === 'credit' ? $amountUsd : -$amountUsd;
            $amountStr = number_format($signedUsd, 18, '.', '');
            $adminId = (int) ($_SESSION['user_id'] ?? 0);
            $ref = 'admin_ref_bonus_' . $type . '_' . $adminId . '_' . $userId . '_' . date('Ymd_His');
            $hasAmountUsdCol = false;
            try {
                $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
                $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
            } catch (Throwable $e) {}
            try {
                if ($hasAmountUsdCol) {
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'referral_bonus_adjustment', $amountStr, $signedUsd, 'USD', 'completed', $ref]);
                } else {
                    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                        ->execute([$userId, 'referral_bonus_adjustment', $amountStr, 'USD', 'completed', $ref]);
                }
                $newReferral = get_user_total_referral_bonus($pdo, $userId);
                admin_audit_log(
                    $pdo,
                    $type === 'credit' ? 'credit' : 'debit',
                    'user',
                    $userId,
                    ucfirst($type) . 'ed referral bonus $' . format_usd_amount($amountUsd) . ' for user #' . $userId,
                    ['total_referral_bonus' => $currentReferral],
                    ['total_referral_bonus' => $newReferral, 'amount_usd' => $signedUsd]
                );
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'message' => $type === 'credit' ? 'Referral bonus (earned) credited' : 'Referral bonus (earned) debited',
                        'total_referral_bonus' => $newReferral,
                    ],
                ]);
            } catch (Throwable $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to adjust referral bonus. Run database migration if referral_bonus_adjustment type is missing.']);
            }
            exit;

        case 'pause_plan':
            $invId = (int) ($input['investment_id'] ?? 0);
            if ($invId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid investment ID']);
                exit;
            }
            $stmt = $pdo->prepare('SELECT id, user_id, status FROM user_investments WHERE id = ? AND user_id = ?');
            $stmt->execute([$invId, $userId]);
            $inv = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$inv || $inv['status'] !== 'active') {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Active investment not found']);
                exit;
            }
            $pdo->prepare('UPDATE user_investments SET status = ? WHERE id = ?')->execute(['paused', $invId]);
            admin_audit_log($pdo, 'pause', 'investment', $invId, 'Paused investment #' . $invId . ' for user #' . $userId, null, ['status' => 'paused']);
            echo json_encode(['success' => true, 'data' => ['message' => 'Plan paused – daily earnings will not be credited until resumed']]);
            exit;

        case 'resume_plan':
            $invId = (int) ($input['investment_id'] ?? 0);
            if ($invId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid investment ID']);
                exit;
            }
            $stmt = $pdo->prepare('SELECT id, user_id, status FROM user_investments WHERE id = ? AND user_id = ?');
            $stmt->execute([$invId, $userId]);
            $inv = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$inv || $inv['status'] !== 'paused') {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Paused investment not found']);
                exit;
            }
            $pdo->prepare('UPDATE user_investments SET status = ? WHERE id = ?')->execute(['active', $invId]);
            admin_audit_log($pdo, 'resume', 'investment', $invId, 'Resumed investment #' . $invId . ' for user #' . $userId, null, ['status' => 'active']);
            echo json_encode(['success' => true, 'data' => ['message' => 'Plan resumed']]);
            exit;

        case 'cancel_plan':
            $invId = (int) ($input['investment_id'] ?? 0);
            if ($invId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid investment ID']);
                exit;
            }
            $stmt = $pdo->prepare('SELECT ui.id, ui.user_id, ui.plan_id, ui.amount, ui.status FROM user_investments ui WHERE ui.id = ? AND ui.user_id = ?');
            $stmt->execute([$invId, $userId]);
            $inv = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$inv || $inv['status'] !== 'active') {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Active investment not found']);
                exit;
            }
            $refundAmount = (float) $inv['amount'];
            $pdo->beginTransaction();
            try {
                $pdo->prepare('UPDATE user_investments SET status = ? WHERE id = ?')->execute(['cancelled', $invId]);
                require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
                credit_user_usd($pdo, $userId, $refundAmount);
                $walletCurrency = user_usd_wallet_currency();
                $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$userId, 'deposit', $refundAmount, $walletCurrency, 'completed']);
                $pdo->commit();
                admin_audit_log(
                    $pdo,
                    'cancel',
                    'investment',
                    $invId,
                    'Cancelled investment #' . $invId . ' and refunded $' . format_usd_amount($refundAmount) . ' to user #' . $userId,
                    ['status' => 'active', 'amount' => $refundAmount],
                    ['status' => 'cancelled', 'refund_usd' => $refundAmount]
                );
                echo json_encode(['success' => true, 'data' => ['message' => 'Plan cancelled and amount refunded to USD wallet']]);
            } catch (Throwable $e) {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to cancel plan']);
            }
            exit;

        case 'delete':
            $delStmt = $pdo->prepare('SELECT id, email, name, active FROM users WHERE id = ?');
            $delStmt->execute([$userId]);
            $deletedUser = $delStmt->fetch(PDO::FETCH_ASSOC) ?: null;
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
            admin_audit_log(
                $pdo,
                'delete',
                'user',
                $userId,
                'Deleted user #' . $userId . ($deletedUser ? (': ' . ($deletedUser['email'] ?? '')) : ''),
                $deletedUser,
                null
            );
            echo json_encode(['success' => true, 'data' => ['message' => 'User deleted']]);
            exit;

        case 'toggle_2fa':
            try {
                $colStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'");
                if ($colStmt->rowCount() === 0) {
                    echo json_encode(['success' => false, 'error' => '2FA not supported']);
                    exit;
                }
                $stmt = $pdo->prepare('UPDATE users SET two_factor_enabled = 1 - COALESCE(two_factor_enabled, 0) WHERE id = ?');
                $stmt->execute([$userId]);
                admin_audit_log($pdo, 'toggle', 'user', $userId, 'Toggled 2FA for user #' . $userId);
                echo json_encode(['success' => true, 'data' => ['message' => '2FA toggled']]);
            } catch (Throwable $e) {
                echo json_encode(['success' => false, 'error' => '2FA not supported']);
            }
            exit;

        case 'verify_kyc':
            $kycCol = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
            if (!$kycCol || $kycCol->rowCount() === 0) {
                echo json_encode(['success' => false, 'error' => 'KYC not configured']);
                exit;
            }
            $pdo->prepare('UPDATE users SET kyc_status = ? WHERE id = ?')->execute(['verified', $userId]);
            admin_audit_log($pdo, 'approve', 'user', $userId, 'Manually verified KYC for user #' . $userId);
            echo json_encode(['success' => true, 'data' => ['message' => 'KYC verified (bypass)']]);
            exit;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

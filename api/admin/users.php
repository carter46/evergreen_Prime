<?php
/**
 * Bloombit - Admin User Management API
 * GET /api/admin/users.php - List users (pagination, search, status filter)
 * GET /api/admin/users.php?id=X - Single user detail with wallet, investments
 * POST /api/admin/users.php - Actions: update, block, unblock, reset_password, adjust_balance
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
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

        // Check if two_factor_enabled column exists
        try {
            $colStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'");
            if ($colStmt->rowCount() > 0) {
                $fullStmt = $pdo->prepare('SELECT two_factor_enabled, admin_notes FROM users WHERE id = ?');
                $fullStmt->execute([$id]);
                $extra = $fullStmt->fetch(PDO::FETCH_ASSOC);
                $user['two_factor_enabled'] = (bool) ($extra['two_factor_enabled'] ?? 0);
                $user['admin_notes'] = $extra['admin_notes'] ?? '';
            } else {
                $user['two_factor_enabled'] = false;
                $user['admin_notes'] = '';
            }
        } catch (Throwable $e) {
            $user['two_factor_enabled'] = false;
            $user['admin_notes'] = '';
        }

        // Wallet balances
        $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
        $stmt->execute([$id]);
        $user['wallet_balances'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user['wallet_balances'][] = [
                'currency' => $row['currency'],
                'amount' => (float) $row['amount'],
            ];
        }
        require_once dirname(__DIR__, 2) . '/includes/helpers.php';
        $user['total_balance_usd'] = wallet_balances_to_usd($user['wallet_balances']);

        // Active investments with plan names
        $stmt = $pdo->prepare('
            SELECT ui.id, ui.amount, ui.start_date, ui.status, p.name AS plan_name, p.yield_min, p.yield_max, p.duration_days
            FROM user_investments ui
            JOIN plans p ON p.id = ui.plan_id
            WHERE ui.user_id = ? AND ui.status = "active"
            ORDER BY ui.created_at DESC
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
                'duration_days' => (int) $row['duration_days'],
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
    try {
        $ac = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
        $hasAvatarCol = $ac && $ac->rowCount() > 0;
    } catch (Throwable $e) {}

    $countSql = "SELECT COUNT(*) FROM users u WHERE {$whereClause}";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $avatarCol = $hasAvatarCol ? ', u.avatar_url' : '';
    $sql = "
        SELECT u.id, u.email, u.name, u.active, u.email_verified, u.created_at, u.updated_at{$avatarCol},
               (SELECT COUNT(*) FROM user_investments WHERE user_id = u.id AND status = 'active') AS active_plans_count
        FROM users u
        WHERE {$whereClause}
        ORDER BY u.created_at DESC
        LIMIT " . (int) $perPage . " OFFSET " . (int) $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = [];
    $userIds = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kyc = 'pending';
        if (!$row['active']) {
            $kyc = 'suspended';
        } elseif ($row['email_verified']) {
            $kyc = 'verified';
        }
        $userIds[] = (int) $row['id'];
        $users[] = [
            'id' => (int) $row['id'],
            'email' => $row['email'],
            'name' => $row['name'] ?: 'User #' . $row['id'],
            'active' => (bool) $row['active'],
            'email_verified' => (bool) $row['email_verified'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'avatar_url' => $row['avatar_url'] ?? null,
            'total_balance_usd' => 0.0,
            'active_plans_count' => (int) $row['active_plans_count'],
            'kyc_status' => $kyc,
        ];
    }
    if (!empty($userIds)) {
        require_once dirname(__DIR__, 2) . '/includes/helpers.php';
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        $wbStmt = $pdo->prepare("SELECT user_id, currency, amount FROM wallet_balances WHERE user_id IN ($placeholders)");
        $wbStmt->execute($userIds);
        $balancesByUser = [];
        while ($r = $wbStmt->fetch(PDO::FETCH_ASSOC)) {
            $uid = (int) $r['user_id'];
            if (!isset($balancesByUser[$uid])) $balancesByUser[$uid] = [];
            $balancesByUser[$uid][] = ['currency' => $r['currency'], 'amount' => (float) $r['amount']];
        }
        $prices = get_coingecko_prices_usd();
        foreach ($users as &$u) {
            $u['total_balance_usd'] = wallet_balances_to_usd($balancesByUser[$u['id']] ?? [], $prices);
        }
        unset($u);
    }

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
        $sql = 'INSERT INTO users (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $ph) . ')';
        $pdo->prepare($sql)->execute($vals);
        $newId = (int) $pdo->lastInsertId();
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
            echo json_encode(['success' => true, 'data' => ['message' => 'Profile updated']]);
            exit;

        case 'block':
            $pdo->prepare('UPDATE users SET active = 0 WHERE id = ?')->execute([$userId]);
            echo json_encode(['success' => true, 'data' => ['message' => 'User blocked']]);
            exit;

        case 'unblock':
            $pdo->prepare('UPDATE users SET active = 1 WHERE id = ?')->execute([$userId]);
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
            echo json_encode(['success' => true, 'data' => ['message' => 'Password reset']]);
            exit;

        case 'adjust_balance':
            $type = strtolower(trim($input['type'] ?? ''));
            $currency = strtoupper(trim($input['currency'] ?? ''));
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
            if ($currency === '' || !in_array($currency, ['BTC', 'ETH', 'USDT', 'USD'], true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid currency']);
                exit;
            }
            if ($type === 'credit') {
                $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                    ->execute([$userId, $currency, $amount]);
                echo json_encode(['success' => true, 'data' => ['message' => 'Balance credited']]);
            } else {
                $stmt = $pdo->prepare('SELECT amount FROM wallet_balances WHERE user_id = ? AND currency = ?');
                $stmt->execute([$userId, $currency]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $current = $row ? (float) $row['amount'] : 0;
                if ($current < $amount) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Insufficient balance']);
                    exit;
                }
                $pdo->prepare('UPDATE wallet_balances SET amount = amount - ? WHERE user_id = ? AND currency = ?')
                    ->execute([$amount, $userId, $currency]);
                echo json_encode(['success' => true, 'data' => ['message' => 'Balance debited']]);
            }
            exit;

        case 'delete':
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
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
                echo json_encode(['success' => true, 'data' => ['message' => '2FA toggled']]);
            } catch (Throwable $e) {
                echo json_encode(['success' => false, 'error' => '2FA not supported']);
            }
            exit;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

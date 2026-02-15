<?php
/**
 * Bloombit - Admin User Management API
 * GET /api/admin/users.php - List users (pagination, search, status filter)
 * GET /api/admin/users.php?id=X - Single user detail with wallet, investments
 * POST /api/admin/users.php - Actions: update, block, unblock, reset_password, adjust_balance
 */

header('Content-Type: application/json');

session_start();
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
        // Single user detail
        $stmt = $pdo->prepare('SELECT id, email, name, role, email_verified, active, created_at, updated_at FROM users WHERE id = ?');
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

    $countSql = "SELECT COUNT(*) FROM users u WHERE {$whereClause}";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $sql = "
        SELECT u.id, u.email, u.name, u.active, u.email_verified, u.created_at, u.updated_at,
               COALESCE(SUM(wb.amount), 0) AS total_btc,
               (SELECT COUNT(*) FROM user_investments WHERE user_id = u.id AND status = 'active') AS active_plans_count
        FROM users u
        LEFT JOIN wallet_balances wb ON wb.user_id = u.id AND wb.currency = 'BTC'
        WHERE {$whereClause}
        GROUP BY u.id
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
            'total_balance_btc' => (float) $row['total_btc'],
            'active_plans_count' => (int) $row['active_plans_count'],
            'kyc_status' => $kyc,
        ];
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
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = trim($input['action'] ?? '');
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
            $currency = strtoupper(trim($input['currency'] ?? ''));
            $amount = (float) ($input['amount'] ?? 0);
            if ($currency === '' || !in_array($currency, ['BTC', 'ETH', 'USDT', 'USD'], true)) {
                echo json_encode(['success' => false, 'error' => 'Invalid currency']);
                exit;
            }
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = VALUES(amount)')
                ->execute([$userId, $currency, $amount]);
            echo json_encode(['success' => true, 'data' => ['message' => 'Balance updated']]);
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

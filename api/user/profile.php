<?php
/**
 * Bloombit - User Profile API
 * GET /api/user/profile.php - Fetch profile
 * PUT /api/user/profile.php - Update profile
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
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
    $cols = 'id, name, email, email_verified';
    foreach (['avatar_url', 'phone_number', 'country', 'two_factor_enabled', 'kyc_status'] as $c) {
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM users LIKE '{$c}'");
            if ($chk && $chk->rowCount() > 0) $cols .= ', ' . $c;
        } catch (Throwable $e) {}
    }
    $stmt = $pdo->prepare("SELECT {$cols} FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    $profile = [
        'name' => $row['name'] ?? '',
        'email' => $row['email'],
        'user_id' => 'BB-' . $row['id'],
        'verified' => (bool) $row['email_verified'],
        'avatar' => $row['avatar_url'] ?? null,
        'phone_number' => $row['phone_number'] ?? null,
        'country' => $row['country'] ?? null,
        'two_factor_enabled' => (bool) ($row['two_factor_enabled'] ?? false),
        'kyc_status' => $row['kyc_status'] ?? 'none',
    ];
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $currentPassword = trim($input['current_password'] ?? '');
    $newPassword = trim($input['password'] ?? '');

    if ($newPassword !== '' && strlen($newPassword) >= 8) {
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$u || !password_verify($currentPassword, $u['password_hash'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            exit;
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $userId]);
    }

    $updates = [];
    $params = [];
    if (array_key_exists('name', $input)) {
        $updates[] = 'name = ?';
        $params[] = trim($input['name'] ?? '');
    }
    if (array_key_exists('phone_number', $input)) {
        $updates[] = 'phone_number = ?';
        $params[] = trim($input['phone_number'] ?? '') ?: null;
    }
    if (array_key_exists('country', $input)) {
        $updates[] = 'country = ?';
        $params[] = trim($input['country'] ?? '') ?: null;
    }
    if (!empty($updates)) {
        $params[] = $userId;
        $pdo->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?')->execute($params);
    }

    $cols = 'id, name, email, email_verified';
    foreach (['avatar_url', 'phone_number', 'country', 'two_factor_enabled', 'kyc_status'] as $c) {
        try {
            $chk = $pdo->query("SHOW COLUMNS FROM users LIKE '{$c}'");
            if ($chk && $chk->rowCount() > 0) $cols .= ', ' . $c;
        } catch (Throwable $e) {}
    }
    $stmt = $pdo->prepare("SELECT {$cols} FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile = [
        'name' => $row['name'] ?? '',
        'email' => $row['email'],
        'user_id' => 'BB-' . $row['id'],
        'verified' => (bool) $row['email_verified'],
        'avatar' => $row['avatar_url'] ?? null,
        'phone_number' => $row['phone_number'] ?? null,
        'country' => $row['country'] ?? null,
        'two_factor_enabled' => (bool) ($row['two_factor_enabled'] ?? false),
        'kyc_status' => $row['kyc_status'] ?? 'none',
    ];
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

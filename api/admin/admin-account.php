<?php
/**
 * Bloombit - Admin Account API
 * POST /api/admin/admin-account.php - Update admin email and/or password
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? [];
$newEmail = trim($input['email'] ?? '');
$currentPassword = trim($input['current_password'] ?? '');
$newPassword = trim($input['password'] ?? '');

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE id = ? AND role = ?');
$stmt->execute([$userId, 'admin']);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admin not found']);
    exit;
}

$updates = [];
$params = [];

if ($newEmail !== '' && $newEmail !== $admin['email']) {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email address']);
        exit;
    }
    $chk = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $chk->execute([$newEmail, $userId]);
    if ($chk->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email already in use']);
        exit;
    }
    $updates[] = 'email = ?';
    $params[] = $newEmail;
}

if ($newPassword !== '' && strlen($newPassword) >= 8) {
    if (empty($currentPassword) || !password_verify($currentPassword, $admin['password_hash'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
        exit;
    }
    $updates[] = 'password_hash = ?';
    $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
}

if (empty($updates)) {
    echo json_encode(['success' => true, 'data' => ['message' => 'No changes made']]);
    exit;
}

$params[] = $userId;
$pdo->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?')->execute($params);

if ($newEmail !== '') {
    $_SESSION['email'] = $newEmail;
}

echo json_encode(['success' => true, 'data' => ['message' => 'Account updated']]);

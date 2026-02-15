<?php
/**
 * Bloombit - User Profile API
 * GET /api/user/profile.php - Fetch profile
 * PUT /api/user/profile.php - Update profile
 */

header('Content-Type: application/json');

session_start();
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
    $stmt = $pdo->prepare('SELECT id, name, email, email_verified FROM users WHERE id = ?');
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
        'avatar' => null,
    ];
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $name = isset($input['name']) ? trim($input['name']) : null;
    if ($name !== null) {
        $stmt = $pdo->prepare('UPDATE users SET name = ? WHERE id = ?');
        $stmt->execute([$name, $userId]);
    }
    $stmt = $pdo->prepare('SELECT id, name, email, email_verified FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $profile = [
        'name' => $row['name'] ?? '',
        'email' => $row['email'],
        'user_id' => 'BB-' . $row['id'],
        'verified' => (bool) $row['email_verified'],
        'avatar' => null,
    ];
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

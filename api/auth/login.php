<?php
/**
 * Bloombit - Login API
 * POST /api/auth/login.php
 * Authenticates user and creates session.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($input['email'] ?? $_POST['email'] ?? '');
$password = $input['password'] ?? $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, email, password_hash, role FROM users WHERE email = ? AND active = 1');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
    exit;
}

session_start();
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

echo json_encode([
    'success' => true,
    'data' => [
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'redirect' => '/dashboard'
    ]
]);

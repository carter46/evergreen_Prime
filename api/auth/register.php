<?php
/**
 * Bloombit - Registration API
 * POST /api/auth/register.php
 * Registers user and sends verification email.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$name = trim($input['name'] ?? '');

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

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email already registered']);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name, role, email_verified, active) VALUES (?, ?, ?, ?, 0, 1)');
$stmt->execute([$email, $passwordHash, $name ?: '', 'user']);

session_start();
$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['email'] = $email;
$_SESSION['role'] = 'user';

echo json_encode([
    'success' => true,
    'data' => [
        'message' => 'Registration successful. Welcome!',
        'redirect' => '/dashboard',
        'user_id' => $_SESSION['user_id'],
        'email' => $email,
    ]
]);

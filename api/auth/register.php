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

// Stub: In production, create user in DB and send verification email via PHPMailer
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['email'] = $email;

echo json_encode([
    'success' => true,
    'data' => [
        'message' => 'Registration successful. Welcome to Bloombit!',
        'redirect' => '/dashboard'
    ]
]);

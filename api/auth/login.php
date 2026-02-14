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

// Stub: In production, validate against database and create session
// For now return mock success for development
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['email'] = $email;

echo json_encode([
    'success' => true,
    'data' => [
        'user_id' => 1,
        'email' => $email,
        'redirect' => '/dashboard'
    ]
]);

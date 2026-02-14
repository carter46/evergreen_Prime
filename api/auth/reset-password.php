<?php
/**
 * Bloombit - Reset Password API
 * POST /api/auth/reset-password.php
 * Validates token and updates password.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$token = trim($input['token'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if (empty($token) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Token, email, and password are required']);
    exit;
}
if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Passwords do not match']);
    exit;
}
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
}

// Validate token
$tokensFile = dirname(__DIR__, 2) . '/data/password_reset_tokens.json';
$tokens = [];
if (file_exists($tokensFile)) {
    $tokens = json_decode(file_get_contents($tokensFile), true) ?: [];
}

if (!isset($tokens[$token])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired reset link']);
    exit;
}

$data = $tokens[$token];
if ($data['email'] !== $email || ($data['expires'] ?? 0) < time()) {
    unset($tokens[$token]);
    file_put_contents($tokensFile, json_encode($tokens));
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired reset link']);
    exit;
}

// Stub: In production, update password in DB
unset($tokens[$token]);
file_put_contents($tokensFile, json_encode($tokens));

echo json_encode([
    'success' => true,
    'message' => 'Password updated successfully',
    'redirect' => '/login'
]);

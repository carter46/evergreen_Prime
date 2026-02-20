<?php
/**
 * Bloombit - Forgot Password API
 * POST /api/auth/forgot-password.php
 * Sends password reset email with HTML template.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($input['email'] ?? '');

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email is required']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/helpers.php';
$config = include dirname(__DIR__, 2) . '/config.php';
$siteUrl = get_base_url();
$expiryMinutes = $config['email_verification']['password_reset_expiry_minutes'] ?? 60;

// Generate reset token (stub: in production, store in DB and validate on reset)
$token = bin2hex(random_bytes(32));
$resetUrl = rtrim($siteUrl, '/') . '/reset-password?token=' . $token . '&email=' . urlencode($email);

// Store token for validation (stub: use file)
$dataDir = dirname(__DIR__, 2) . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}
$tokensFile = $dataDir . '/password_reset_tokens.json';
$tokens = [];
if (file_exists($tokensFile)) {
    $tokens = json_decode(file_get_contents($tokensFile), true) ?: [];
}
$tokens[$token] = ['email' => $email, 'expires' => time() + ($expiryMinutes * 60)];
file_put_contents($tokensFile, json_encode($tokens));

try {
    require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';

    $brand = get_site_name();
    $mail->addAddress($email);
    $mail->Subject = 'Reset your ' . $brand . ' password';
    $mail->Body = renderEmailTemplate('password-reset.php', [
        'reset_url' => $resetUrl,
        'name' => explode('@', $email)[0],
        'expiry_minutes' => $expiryMinutes
    ]);
    $mail->AltBody = "Reset your $brand password by visiting: $resetUrl\n\nThis link expires in $expiryMinutes minutes.";
    $mail->isHTML(true);

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'If that email exists, we\'ve sent a reset link.']);
} catch (Exception $e) {
    $errorMsg = ($config['site']['debug'] ?? false) ? $e->getMessage() : 'Failed to send reset email. Please try again.';
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}

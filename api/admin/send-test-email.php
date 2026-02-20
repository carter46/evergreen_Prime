<?php
/**
 * Bloombit - Send Test Email API
 * POST /api/admin/send-test-email.php - Sends a test email to a specified address
 * Body: { "to": "email@example.com" } (optional; defaults to admin email)
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

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $stmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

if (!$admin || empty($admin['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Admin email not found']);
    exit;
}

$json = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$toEmail = trim((string) ($json['to'] ?? ''));
if ($toEmail === '') {
    $toEmail = $admin['email'];
}
if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';

try {
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
    $brand = get_site_name();
    $mail->clearAddresses();
    $mail->addAddress($toEmail);
    $mail->Subject = $brand . ' - Test Email (' . date('Y-m-d H:i') . ')';
    $mail->Body = renderEmailTemplate('otp.php', [
        'otp' => '123456',
        'name' => $admin['name'] ?? 'Admin',
        'purpose_label' => 'test email',
    ]);
    $mail->AltBody = 'This is a test email from ' . $brand . '. If you received this, your email configuration is working.';
    $mail->isHTML(true);
    $mail->send();
    echo json_encode(['success' => true, 'data' => ['message' => 'Test email sent to ' . $toEmail]]);
} catch (Exception $e) {
    $config = include dirname(__DIR__, 2) . '/config.php';
    $msg = ($config['site']['debug'] ?? false) ? $e->getMessage() : 'Failed to send test email. Check mail configuration.';
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $msg]);
}

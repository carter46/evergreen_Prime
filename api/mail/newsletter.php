<?php
/**
 * Bloombit - Newsletter Signup API
 * POST /api/mail/newsletter.php
 * Subscribes email to newsletter (sends confirmation/notification).
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$email = trim($_POST['email'] ?? json_decode(file_get_contents('php://input'), true)['email'] ?? '');

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

try {
    require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
    $config = include dirname(__DIR__, 2) . '/config.php';
    require_once dirname(__DIR__, 2) . '/includes/helpers.php';
    $replyTo = $config['mail']['reply_to'] ?? 'support@bloombit.com';
    $siteName = $config['site']['name'] ?? 'Bloombit';
    $siteUrl = get_base_url();
    $date = date('Y-m-d H:i:s') . ' UTC';

    // Email to admin: new signup notification
    $mail->addAddress($replyTo);
    $mail->Subject = "[$siteName Newsletter] New signup: $email";
    $mail->Body = renderEmailTemplate('newsletter-admin.php', ['email' => $email, 'date' => $date]);
    $mail->AltBody = "New newsletter subscription:\n\nEmail: $email\nDate: $date";
    $mail->isHTML(true);
    $mail->send();
    $mail->clearAddresses();
    $mail->clearReplyTos();

    // Email to subscriber: welcome
    $mail->addAddress($email);
    $mail->Subject = "Welcome to $siteName – You're subscribed!";
    $mail->Body = renderEmailTemplate('newsletter-welcome.php', ['email' => $email]);
    $mail->AltBody = "Thanks for subscribing to $siteName. Visit $siteUrl to get started.";
    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
} catch (Exception $e) {
    $config = include dirname(__DIR__, 2) . '/config.php';
    $errorMsg = ($config['site']['debug'] ?? false) ? $e->getMessage() : 'Subscription failed. Please try again later.';
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}

<?php
/**
 * Bloombit - Contact Form API
 * POST /api/mail/contact.php
 * Sends contact form submission via email.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address';
if (empty($subject)) $errors[] = 'Subject is required';
if (empty($message)) $errors[] = 'Message is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode('. ', $errors)]);
    exit;
}

try {
    require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
    $config = include dirname(__DIR__, 2) . '/config.php';
    $replyTo = $config['mail']['reply_to'] ?? 'support@bloombit.com';

    $mail->addAddress($replyTo);
    $mail->Subject = '[Bloombit Contact] ' . $subject;
    $mail->Body = renderEmailTemplate('contact-notification.php', [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message
    ]);
    $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";
    $mail->isHTML(true);
    $mail->addReplyTo($email, $name);

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    $config = include dirname(__DIR__, 2) . '/config.php';
    $errorMsg = ($config['site']['debug'] ?? false) ? $e->getMessage() : 'Failed to send message. Please try again later.';
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}

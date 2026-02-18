<?php
/**
 * Bloombit - Admin Mail Reply
 * POST /api/admin/mail-reply.php
 * Body: { "id": 123, "body": "...", "subject": optional }
 */
header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$id = (int) ($input['id'] ?? 0);
$body = trim((string) ($input['body'] ?? ''));
$subjectOverride = trim((string) ($input['subject'] ?? ''));

if ($id <= 0 || $body === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Message id and body are required']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $stmt = $pdo->prepare("SELECT * FROM admin_mailbox WHERE id = ? AND direction = 'in' LIMIT 1");
    $stmt->execute([$id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

if (!$msg || empty($msg['from_email'])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Inbox message not found']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';

$toEmail = (string) $msg['from_email'];
$toName = (string) ($msg['from_name'] ?? '');
$origSubject = (string) ($msg['subject'] ?? '');
$subject = $subjectOverride !== '' ? $subjectOverride : $origSubject;
if ($subject === '') $subject = '(no subject)';
if (stripos($subject, 're:') !== 0) $subject = 'Re: ' . $subject;

$inReplyTo = trim((string) ($msg['message_id'] ?? ''));
$refs = trim((string) ($msg['references'] ?? ''));
if ($inReplyTo !== '' && stripos($refs, $inReplyTo) === false) {
    $refs = trim(($refs ? $refs . ' ' : '') . $inReplyTo);
}

try {
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
    $mail->clearAddresses();
    $mail->addAddress($toEmail, $toName);
    $mail->Subject = $subject;

    $mail->Body = renderEmailTemplate('broadcast.php', [
        'subject' => $subject,
        'body' => $body,
    ]);
    $mail->AltBody = $body;
    $mail->isHTML(true);

    if ($inReplyTo !== '') {
        $mail->addCustomHeader('In-Reply-To', $inReplyTo);
    }
    if ($refs !== '') {
        $mail->addCustomHeader('References', $refs);
    }

    $mail->send();

    // Log to admin_mailbox (out)
    try {
        $fromEmail = get_site_setting('mail_from_email', null);
        $fromName = get_site_setting('mail_from_name', null);
        $stmt = $pdo->prepare("INSERT INTO admin_mailbox (direction, source, to_emails, subject, body_text, status, in_reply_to, `references`, created_at) VALUES ('out','reply',?,?,?,?,?,?,?,NOW())");
        $stmt->execute([
            $toEmail,
            $subject,
            $body,
            'sent',
            $inReplyTo ?: null,
            $refs ?: null,
        ]);
    } catch (Throwable $e) {}

    echo json_encode(['success' => true, 'data' => ['message' => 'Reply sent']]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send reply']);
}


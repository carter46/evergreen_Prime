<?php
/**
 * Bloombit - Admin IMAP Reply (Live)
 * POST /api/admin/imap-reply.php
 * Body: { "folder": "INBOX", "uid": 123, "body": "...", "subject": optional }
 * Fetches original from IMAP, sends reply via SMTP, appends to Sent, logs to DB.
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
if (!function_exists('imap_open')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'PHP IMAP extension is not enabled.']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/imap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';

$input = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$folder = trim($input['folder'] ?? 'INBOX');
$uid = (int) ($input['uid'] ?? 0);
$body = trim((string) ($input['body'] ?? ''));
$subjectOverride = trim((string) ($input['subject'] ?? ''));

if ($uid <= 0 || $body === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'folder, uid, and body are required']);
    exit;
}

$config = bloombit_imap_config();
if (!$config || $config['pass'] === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IMAP settings incomplete or password not set.']);
    exit;
}
$allowedFolders = ['INBOX', 'SPAM', $config['sent_folder']];
if (!in_array($folder, $allowedFolders, true)) {
    $folder = 'INBOX';
}

$mbox = bloombit_imap_open_folder($folder, true);
if (!$mbox) {
    http_response_code(502);
    echo json_encode(['success' => false, 'error' => 'Failed to connect to mailbox.']);
    exit;
}

$ovArr = @imap_fetch_overview($mbox, (string) $uid, FT_UID);
$ov = (is_array($ovArr) && isset($ovArr[0])) ? $ovArr[0] : null;
if (!$ov) {
    imap_close($mbox);
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Message not found.']);
    exit;
}

$fromEmail = '';
$fromName = '';
$replyToEmail = '';
$replyToName = '';
$messageId = '';
$references = '';

if (!empty($ov->from)) {
    $addrs = bloombit_imap_parse_addresses($ov->from);
    if (!empty($addrs)) {
        $fromEmail = $addrs[0]['email'];
        $fromName = $addrs[0]['name'] ?: $addrs[0]['email'];
    }
}
if (!empty($ov->reply_to)) {
    $addrs = bloombit_imap_parse_addresses($ov->reply_to);
    if (!empty($addrs)) {
        $replyToEmail = $addrs[0]['email'];
        $replyToName = $addrs[0]['name'] ?: $addrs[0]['email'];
    }
}
if ($replyToEmail === '' && $fromEmail !== '') {
    $replyToEmail = $fromEmail;
    $replyToName = $fromName;
}
if ($replyToEmail === '' || !filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
    imap_close($mbox);
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid reply address.']);
    exit;
}

if (!empty($ov->message_id)) {
    $messageId = trim((string) $ov->message_id);
}
$hdr = @imap_fetchheader($mbox, $uid, FT_UID);
if ($hdr) {
    $parsed = @imap_rfc822_parse_headers($hdr);
    if ($parsed && !empty($parsed->references)) {
        $references = trim((string) $parsed->references);
    }
}
if ($messageId !== '' && strpos($references, $messageId) === false) {
    $references = trim(($references ? $references . ' ' : '') . $messageId);
}

imap_close($mbox);

$origSubject = !empty($ov->subject) ? (string) imap_utf8((string) $ov->subject) : '';
$subject = $subjectOverride !== '' ? $subjectOverride : $origSubject;
if ($subject === '') $subject = '(no subject)';
if (stripos($subject, 're:') !== 0) {
    $subject = 'Re: ' . $subject;
}

try {
    $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
    $mail->clearAddresses();
    $mail->addAddress($replyToEmail, $replyToName);
    $mail->Subject = $subject;
    $mail->Body = renderEmailTemplate('broadcast.php', [
        'subject' => $subject,
        'body' => $body,
    ]);
    $mail->AltBody = $body;
    $mail->isHTML(true);
    if ($messageId !== '') {
        $mail->addCustomHeader('In-Reply-To', $messageId);
    }
    if ($references !== '') {
        $mail->addCustomHeader('References', $references);
    }

    $mail->send();
    $mime = $mail->getSentMIMEMessage();
    if ($mime !== '' && $config['pass'] !== '') {
        bloombit_imap_append_to_sent($mime, $config['sent_folder']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send reply: ' . $e->getMessage()]);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $chk = $pdo->query("SHOW TABLES LIKE 'admin_mailbox'");
    if ($chk && $chk->rowCount() > 0) {
        $fromE = get_site_setting('mail_from_email', null);
        $fromN = get_site_setting('mail_from_name', null);
        $stmt = $pdo->prepare("INSERT INTO admin_mailbox (direction, source, from_email, from_name, to_emails, subject, body_text, status, in_reply_to, `references`, created_at) VALUES ('out','reply',?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([
            $fromE,
            $fromN,
            $replyToEmail,
            $subject,
            $body,
            'sent',
            $messageId ?: null,
            $references ?: null,
        ]);
    }
} catch (Throwable $e) {}

echo json_encode(['success' => true, 'data' => ['message' => 'Reply sent']]);

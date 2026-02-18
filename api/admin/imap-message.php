<?php
/**
 * Bloombit - Admin IMAP Message (Live)
 * GET /api/admin/imap-message.php?folder=INBOX&uid=123
 * Returns full message content from IMAP by UID.
 */
header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
if (!function_exists('imap_open')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'PHP IMAP extension is not enabled.']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/imap.php';

$folder = trim($_GET['folder'] ?? 'INBOX');
$uid = (int) ($_GET['uid'] ?? 0);
$markRead = !isset($_GET['mark_read']) || (bool)($_GET['mark_read'] ?? true);

if ($uid <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Valid uid parameter required.']);
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

$mbox = bloombit_imap_open_folder($folder, false);
if (!$mbox) {
    $err = imap_last_error();
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to connect: ' . ($err ?: 'Unknown error'),
    ]);
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
$toEmails = [];
$subject = '(No Subject)';
$date = '';
$messageId = '';
$inReplyTo = '';
$references = '';

if (!empty($ov->from)) {
    $addrs = bloombit_imap_parse_addresses($ov->from);
    if (!empty($addrs)) {
        $fromEmail = $addrs[0]['email'];
        $fromName = $addrs[0]['name'] ?: $fromEmail;
    }
}
if (!empty($ov->reply_to)) {
    $addrs = bloombit_imap_parse_addresses($ov->reply_to);
    if (!empty($addrs)) {
        $replyToEmail = $addrs[0]['email'];
        $replyToName = $addrs[0]['name'] ?: $replyToEmail;
    }
}
if ($replyToEmail === '' && $fromEmail !== '') {
    $replyToEmail = $fromEmail;
    $replyToName = $fromName;
}
if (!empty($ov->to)) {
    $toList = bloombit_imap_parse_addresses($ov->to);
    $toEmails = array_column($toList, 'email');
}
if (!empty($ov->subject)) {
    $subject = (string) imap_utf8((string) $ov->subject);
}
if (!empty($ov->date)) {
    $date = (string) $ov->date;
}
if (!empty($ov->message_id)) {
    $messageId = trim((string) $ov->message_id);
}

$hdr = @imap_fetchheader($mbox, $uid, FT_UID);
if ($hdr) {
    $parsed = @imap_rfc822_parse_headers($hdr);
    if ($parsed) {
        if (!empty($parsed->in_reply_to)) {
            $inReplyTo = trim((string) $parsed->in_reply_to);
        }
        if (!empty($parsed->references)) {
            $references = trim((string) $parsed->references);
        }
    }
}

$extracted = bloombit_imap_extract_best_body($mbox, $uid);
$body = $extracted['body'];
$isHtml = $extracted['is_html'];

$body = (string) imap_utf8($body);
if (!$isHtml) {
    $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
    $body = nl2br($body);
} else {
    $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

if ($markRead) {
    @imap_setflag_full($mbox, (string) $uid, "\\Seen", ST_UID);
}

imap_close($mbox);

echo json_encode([
    'success' => true,
    'email' => [
        'uid' => $uid,
        'folder' => $folder,
        'from' => ['email' => $fromEmail, 'name' => $fromName],
        'reply_to' => ['email' => $replyToEmail, 'name' => $replyToName],
        'to' => $toEmails,
        'subject' => $subject,
        'date' => $date,
        'body' => $body,
        'is_html' => $isHtml,
        'message_id' => $messageId,
        'in_reply_to' => $inReplyTo,
        'references' => $references,
    ],
]);

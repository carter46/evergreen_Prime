<?php
/**
 * Bloombit - Admin IMAP List (Live Mailbox)
 * GET /api/admin/imap-list.php?folder=INBOX&limit=20&offset=0
 * Returns message list from IMAP folder (UID-based, live from server).
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
$limit = max(1, min(100, (int)($_GET['limit'] ?? 20)));
$offset = max(0, (int)($_GET['offset'] ?? 0));
$unreadOnly = isset($_GET['unread_only']) && (bool)$_GET['unread_only'];
$criteria = $unreadOnly ? 'UNSEEN' : 'ALL';

// Allow INBOX and configured Sent folder; also common folder names
$config = bloombit_imap_config();
if (!$config) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IMAP settings are incomplete. Configure IMAP in Admin Settings.']);
    exit;
}
if ($config['pass'] === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IMAP password is not set. Save it in Admin Settings.']);
    exit;
}
$allowedFolders = ['INBOX', 'SPAM', $config['sent_folder']];
if (!in_array($folder, $allowedFolders, true)) {
    $folder = 'INBOX';
}

$mbox = bloombit_imap_open_folder($folder, true);
if (!$mbox) {
    $err = imap_last_error();
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to connect to mailbox: ' . ($err ?: 'Unknown error'),
    ]);
    exit;
}

$uids = bloombit_imap_list_uids($mbox, $criteria, $limit, $offset);
$emails = [];
foreach ($uids as $uid) {
    $uid = (int) $uid;
    if ($uid <= 0) continue;
    $ovArr = @imap_fetch_overview($mbox, (string) $uid, FT_UID);
    $ov = (is_array($ovArr) && isset($ovArr[0])) ? $ovArr[0] : null;
    if (!$ov) continue;

    $subject = isset($ov->subject) ? (string) imap_utf8((string) $ov->subject) : '(No Subject)';
    $date = isset($ov->date) ? (string) $ov->date : '';
    $messageId = isset($ov->message_id) ? trim((string) $ov->message_id) : '';
    $unseen = empty($ov->seen);

    $fromEmail = '';
    $fromName = '';
    $replyToEmail = '';
    $replyToName = '';
    $toEmails = [];

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

    $preview = '';
    try {
        $extracted = bloombit_imap_extract_best_body($mbox, $uid);
        $raw = $extracted['body'];
        $raw = strip_tags($raw);
        $raw = preg_replace("/\s+/", ' ', $raw);
        $preview = mb_substr(trim($raw), 0, 200);
    } catch (Throwable $e) {}

    $emails[] = [
        'uid' => $uid,
        'folder' => $folder,
        'from' => ['email' => $fromEmail, 'name' => $fromName],
        'reply_to' => ['email' => $replyToEmail, 'name' => $replyToName],
        'to' => $toEmails,
        'subject' => $subject,
        'date' => $date,
        'unread' => $unseen,
        'message_id' => $messageId,
        'preview' => $preview,
    ];
}

imap_close($mbox);

echo json_encode([
    'success' => true,
    'emails' => $emails,
    'folder' => $folder,
    'count' => count($emails),
]);

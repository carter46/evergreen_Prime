<?php
/**
 * Bloombit - Admin Mail Sync (IMAP)
 * POST /api/admin/mail-sync.php
 * Syncs real mailbox emails (INBOX and Sent folder) into admin_mailbox table.
 *
 * Requirements:
 * - PHP IMAP extension enabled (imap_open)
 * - IMAP settings saved in Admin Settings (site_settings mail_imap_*)
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
    echo json_encode(['success' => false, 'error' => 'PHP IMAP extension is not enabled (imap_open not found).']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/helpers.php';

$host = (string) (get_site_setting('mail_imap_host', '') ?? '');
$port = (int) (get_site_setting('mail_imap_port', '993') ?? 993);
$user = (string) (get_site_setting('mail_imap_username', '') ?? '');
$pass = (string) (get_site_setting('mail_imap_password', '') ?? '');
$enc  = strtolower((string) (get_site_setting('mail_imap_encryption', 'ssl') ?? 'ssl'));
$sentFolder = (string) (get_site_setting('mail_imap_sent_folder', 'Sent') ?? 'Sent');

$input = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];
$folders = $input['folders'] ?? ['INBOX', $sentFolder];
if (is_string($folders)) $folders = preg_split('/[,\s]+/', $folders) ?: [];
if (!is_array($folders)) $folders = ['INBOX', $sentFolder];
$folders = array_values(array_filter(array_map('trim', $folders), fn($x) => $x !== ''));
$limit = (int) ($input['limit'] ?? 30);
if ($limit < 1) $limit = 30;
if ($limit > 200) $limit = 200;

if ($host === '' || $user === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IMAP settings are incomplete. Set IMAP host and username in Admin Settings.']);
    exit;
}
if ($pass === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'IMAP password is not set. Save it in Admin Settings (write-only field).']);
    exit;
}
if ($port <= 0 || $port > 65535) $port = 993;
if (!in_array($enc, ['ssl', 'tls', 'none'], true)) $enc = 'ssl';

function decode_imap_part(string $data, int $encoding): string {
    if ($encoding === 3) return (string) imap_base64($data); // base64
    if ($encoding === 4) return (string) quoted_printable_decode($data); // quoted-printable
    return $data;
}

function parse_address_list(?string $raw): string {
    $raw = trim((string)$raw);
    if ($raw === '') return '';
    $addrs = imap_rfc822_parse_adrlist($raw, '');
    if (!is_array($addrs)) return $raw;
    $out = [];
    foreach ($addrs as $a) {
        if (!is_object($a)) continue;
        $mailbox = $a->mailbox ?? '';
        $host = $a->host ?? '';
        $email = ($mailbox && $host) ? ($mailbox . '@' . $host) : '';
        if ($email !== '') $out[] = $email;
    }
    return implode(', ', array_values(array_unique($out)));
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $chk = $pdo->query("SHOW TABLES LIKE 'admin_mailbox'");
    if (!$chk || $chk->rowCount() === 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'admin_mailbox table not found. Run the updated migration.sql.']);
        exit;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$results = [];
$importedTotal = 0;
$skippedTotal = 0;

foreach ($folders as $folder) {
    $folderName = $folder;
    $flags = '/imap';
    if ($enc === 'ssl') $flags .= '/ssl';
    elseif ($enc === 'tls') $flags .= '/tls';
    elseif ($enc === 'none') $flags .= '/notls';
    $mailboxStr = '{' . $host . ':' . $port . $flags . '}' . $folderName;

    $mbox = @imap_open($mailboxStr, $user, $pass, OP_READONLY);
    if (!$mbox) {
        $results[] = ['folder' => $folderName, 'imported' => 0, 'skipped' => 0, 'error' => imap_last_error() ?: 'Failed to open folder'];
        continue;
    }

    $uids = imap_search($mbox, 'ALL', SE_UID);
    if (!is_array($uids)) $uids = [];
    rsort($uids);
    $uids = array_slice($uids, 0, $limit);

    $imported = 0;
    $skipped = 0;

    foreach ($uids as $uid) {
        $uid = (int) $uid;
        if ($uid <= 0) continue;

        // Skip if already imported by folder+uid
        try {
            $stmt = $pdo->prepare('SELECT id FROM admin_mailbox WHERE mailbox_folder = ? AND imap_uid = ? LIMIT 1');
            $stmt->execute([$folderName, $uid]);
            if ($stmt->fetch()) { $skipped++; continue; }
        } catch (Throwable $e) {}

        $overviewArr = imap_fetch_overview($mbox, (string)$uid, FT_UID);
        $ov = (is_array($overviewArr) && isset($overviewArr[0])) ? $overviewArr[0] : null;
        if (!$ov) { $skipped++; continue; }

        $subject = isset($ov->subject) ? (string) imap_utf8((string)$ov->subject) : '';
        $dateStr = isset($ov->date) ? (string)$ov->date : '';
        $mailDate = $dateStr ? date('Y-m-d H:i:s', strtotime($dateStr)) : date('Y-m-d H:i:s');
        $messageId = isset($ov->message_id) ? trim((string)$ov->message_id) : '';

        $hdr = @imap_fetchheader($mbox, $uid, FT_UID);
        $toEmails = '';
        $fromEmail = '';
        $fromName = '';
        $inReplyTo = '';
        $refs = '';
        if ($hdr) {
            $h = @imap_rfc822_parse_headers($hdr);
            if ($h) {
                if (!empty($h->toaddress)) $toEmails = parse_address_list($h->toaddress);
                if (!empty($h->fromaddress)) {
                    $fromList = @imap_rfc822_parse_adrlist($h->fromaddress, '');
                    if (is_array($fromList) && isset($fromList[0])) {
                        $a = $fromList[0];
                        $mb = $a->mailbox ?? '';
                        $ho = $a->host ?? '';
                        $fromEmail = ($mb && $ho) ? ($mb . '@' . $ho) : '';
                        $fromName = isset($a->personal) ? (string) imap_utf8((string)$a->personal) : '';
                    }
                }
                $inReplyTo = isset($h->in_reply_to) ? trim((string)$h->in_reply_to) : '';
                $refs = isset($h->references) ? trim((string)$h->references) : '';
            }
        }

        // Body extraction (plain + html)
        $bodyText = '';
        $bodyHtml = '';
        $structure = @imap_fetchstructure($mbox, $uid, FT_UID);
        if ($structure) {
            $parts = $structure->parts ?? null;
            if (is_array($parts)) {
                foreach ($parts as $idx => $p) {
                    $partNum = (string)($idx + 1);
                    $subtype = strtolower((string)($p->subtype ?? ''));
                    $type = (int)($p->type ?? 0);
                    $encg = (int)($p->encoding ?? 0);
                    if ($type === 0 && $subtype === 'plain' && $bodyText === '') {
                        $raw = (string) imap_fetchbody($mbox, $uid, $partNum, FT_UID);
                        $bodyText = decode_imap_part($raw, $encg);
                    }
                    if ($type === 0 && $subtype === 'html' && $bodyHtml === '') {
                        $raw = (string) imap_fetchbody($mbox, $uid, $partNum, FT_UID);
                        $bodyHtml = decode_imap_part($raw, $encg);
                    }
                }
            } else {
                // Single-part message
                $subtype = strtolower((string)($structure->subtype ?? ''));
                $encg = (int)($structure->encoding ?? 0);
                $raw = (string) imap_body($mbox, $uid, FT_UID);
                $decoded = decode_imap_part($raw, $encg);
                if ($subtype === 'html') $bodyHtml = $decoded; else $bodyText = $decoded;
            }
        }
        if ($bodyText === '' && $bodyHtml !== '') {
            $bodyText = trim(strip_tags($bodyHtml));
        }

        $direction = (strcasecmp($folderName, 'INBOX') === 0) ? 'in' : 'out';
        $source = (strcasecmp($folderName, 'INBOX') === 0) ? 'imap' : 'imap_sent';
        $status = (strcasecmp($folderName, 'INBOX') === 0) ? 'received' : 'sent';

        try {
            $stmt = $pdo->prepare('INSERT INTO admin_mailbox (direction, source, mailbox_folder, imap_uid, message_id, in_reply_to, `references`, mail_date, from_email, from_name, to_emails, subject, body_html, body_text, status, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $direction,
                $source,
                $folderName,
                $uid,
                $messageId ?: null,
                $inReplyTo ?: null,
                $refs ?: null,
                $mailDate,
                $fromEmail ?: null,
                $fromName ?: null,
                $toEmails ?: null,
                $subject ?: '(no subject)',
                $bodyHtml ?: null,
                $bodyText ?: null,
                $status,
                $mailDate,
            ]);
            $imported++;
        } catch (Throwable $e) {
            $skipped++;
        }
    }

    imap_close($mbox);
    $results[] = ['folder' => $folderName, 'imported' => $imported, 'skipped' => $skipped];
    $importedTotal += $imported;
    $skippedTotal += $skipped;
}

echo json_encode([
    'success' => true,
    'data' => [
        'folders' => $results,
        'imported' => $importedTotal,
        'skipped' => $skippedTotal,
    ],
]);


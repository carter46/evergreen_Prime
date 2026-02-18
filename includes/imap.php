<?php
/**
 * Bloombit - IMAP Helper
 * Shared IMAP connection, listing, body extraction, and APPEND utilities.
 * Uses site_settings (mail_imap_*) for configuration.
 */

if (!function_exists('imap_open')) {
    return; // IMAP extension not available - callers must check
}

require_once __DIR__ . '/helpers.php';

/**
 * Build IMAP connection parameters from site settings.
 * @return array{host:string, port:int, user:string, pass:string, enc:string, sent_folder:string}|null
 */
function bloombit_imap_config(): ?array {
    $host = (string) (get_site_setting('mail_imap_host', '') ?? '');
    $user = (string) (get_site_setting('mail_imap_username', '') ?? '');
    if ($host === '' || $user === '') {
        return null;
    }
    $port = (int) (get_site_setting('mail_imap_port', '993') ?? 993);
    if ($port <= 0 || $port > 65535) $port = 993;
    $pass = (string) (get_site_setting('mail_imap_password', '') ?? '');
    $enc = strtolower((string) (get_site_setting('mail_imap_encryption', 'ssl') ?? 'ssl'));
    if (!in_array($enc, ['ssl', 'tls', 'none'], true)) $enc = 'ssl';
    $sentFolder = (string) (get_site_setting('mail_imap_sent_folder', 'Sent') ?? 'Sent');
    return [
        'host' => $host,
        'port' => $port,
        'user' => $user,
        'pass' => $pass,
        'enc' => $enc,
        'sent_folder' => $sentFolder,
    ];
}

/**
 * Build IMAP mailbox connection string for a folder.
 * @param string $folder Folder name (INBOX, Sent, etc.)
 * @param array $config From bloombit_imap_config()
 * @return string e.g. "{imap.example.com:993/imap/ssl}INBOX"
 */
function bloombit_imap_mailbox_str(string $folder, array $config): string {
    $flags = '/imap';
    if ($config['enc'] === 'ssl') $flags .= '/ssl';
    elseif ($config['enc'] === 'tls') $flags .= '/tls';
    else $flags .= '/notls';
    return '{' . $config['host'] . ':' . $config['port'] . $flags . '}' . $folder;
}

/**
 * Open IMAP folder. Returns resource or false.
 * @param string $folder Folder name
 * @param bool $readonly Use OP_READONLY
 */
function bloombit_imap_open_folder(string $folder, bool $readonly = true) {
    $config = bloombit_imap_config();
    if (!$config || $config['pass'] === '') {
        return false;
    }
    $mailboxStr = bloombit_imap_mailbox_str($folder, $config);
    return @imap_open($mailboxStr, $config['user'], $config['pass'], $readonly ? OP_READONLY : 0);
}

/**
 * Get UIDs for a folder, optionally filtered by criteria.
 * Returns array of UIDs (most recent first), limited and offset.
 * @param resource $mbox imap stream
 * @param string $criteria e.g. 'ALL' or 'UNSEEN'
 * @param int $limit
 * @param int $offset
 * @return int[]
 */
function bloombit_imap_list_uids($mbox, string $criteria = 'ALL', int $limit = 20, int $offset = 0): array {
    $uids = imap_search($mbox, $criteria, SE_UID);
    if (!is_array($uids)) $uids = [];
    rsort($uids);
    return array_slice($uids, $offset, $limit);
}

/**
 * Decode IMAP body part by encoding type.
 */
function bloombit_imap_decode_part(string $data, int $encoding): string {
    if ($encoding === 3) return (string) imap_base64($data);
    if ($encoding === 4) return (string) quoted_printable_decode($data);
    return $data;
}

/**
 * Parse address string to array of [email, name].
 * @return array<array{email:string, name:string}>
 */
function bloombit_imap_parse_addresses(?string $raw): array {
    $raw = trim((string) $raw);
    if ($raw === '') return [];
    $addrs = @imap_rfc822_parse_adrlist($raw, '');
    if (!is_array($addrs)) return [];
    $out = [];
    foreach ($addrs as $a) {
        if (!is_object($a)) continue;
        $mailbox = trim((string)($a->mailbox ?? ''));
        $host = trim((string)($a->host ?? ''));
        $email = ($mailbox && $host) ? ($mailbox . '@' . $host) : '';
        if ($email === '') continue;
        $name = isset($a->personal) ? (string) imap_utf8((string) $a->personal) : '';
        $out[] = ['email' => $email, 'name' => $name];
    }
    return $out;
}

/**
 * Parse addresses to comma-separated email string.
 */
function bloombit_imap_address_list(?string $raw): string {
    $addrs = bloombit_imap_parse_addresses($raw);
    $emails = array_column($addrs, 'email');
    return implode(', ', array_values(array_unique($emails)));
}

/**
 * Extract best body (HTML preferred, else plain) for a message by UID.
 * @param resource $mbox
 * @param int $uid
 * @return array{body:string, is_html:bool}
 */
function bloombit_imap_extract_best_body($mbox, int $uid): array {
    $bodyText = '';
    $bodyHtml = '';
    $structure = @imap_fetchstructure($mbox, (string) $uid, FT_UID);
    if (!$structure) {
        return ['body' => '', 'is_html' => false];
    }
    $parts = $structure->parts ?? null;
    if (is_array($parts)) {
        foreach ($parts as $idx => $p) {
            $partNum = (string) ($idx + 1);
            $subtype = strtolower((string)($p->subtype ?? ''));
            $type = (int)($p->type ?? 0);
            $encg = (int)($p->encoding ?? 0);
            if ($type === 0 && $subtype === 'plain' && $bodyText === '') {
                $raw = (string) imap_fetchbody($mbox, $uid, $partNum, FT_UID);
                $bodyText = bloombit_imap_decode_part($raw, $encg);
            }
            if ($type === 0 && $subtype === 'html' && $bodyHtml === '') {
                $raw = (string) imap_fetchbody($mbox, $uid, $partNum, FT_UID);
                $bodyHtml = bloombit_imap_decode_part($raw, $encg);
            }
        }
    } else {
        $subtype = strtolower((string)($structure->subtype ?? ''));
        $encg = (int)($structure->encoding ?? 0);
        $raw = (string) imap_body($mbox, $uid, FT_UID);
        $decoded = bloombit_imap_decode_part($raw, $encg);
        if ($subtype === 'html') $bodyHtml = $decoded; else $bodyText = $decoded;
    }
    if ($bodyText === '' && $bodyHtml !== '') {
        $bodyText = trim(strip_tags($bodyHtml));
    }
    if ($bodyHtml !== '') {
        return ['body' => $bodyHtml, 'is_html' => true];
    }
    return ['body' => $bodyText, 'is_html' => false];
}

/**
 * Append raw MIME message to Sent folder.
 * @param string $mime Full RFC 2822 message
 * @param string|null $sentFolder Override sent folder (default from config)
 * @return bool Success
 */
function bloombit_imap_append_to_sent(string $mime, ?string $sentFolder = null): bool {
    $config = bloombit_imap_config();
    if (!$config || $config['pass'] === '') {
        return false;
    }
    $folder = $sentFolder ?? $config['sent_folder'];
    $mailboxStr = bloombit_imap_mailbox_str($folder, $config);
    $mbox = @imap_open($mailboxStr, $config['user'], $config['pass'], 0);
    if (!$mbox) {
        return false;
    }
    // Ensure proper line endings
    $mime = str_replace(["\r\n", "\r"], "\n", $mime);
    $mime = str_replace("\n", "\r\n", $mime);
    $result = @imap_append($mbox, $mailboxStr, $mime, "\\Seen");
    imap_close($mbox);
    return (bool) $result;
}

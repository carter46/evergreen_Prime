<?php
/**
 * Bloombit - Admin Site Settings API
 * GET /api/admin/site-settings.php - Get global parameters
 * POST /api/admin/site-settings.php - Update global parameters
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$allowedKeys = [
    'max_active_plans_per_user',
    'compounding_enabled',
    'referral_enabled',
    'referral_percentage',
    'deposit_bonus_percentage',
    'site_name',
    'site_logo',
    'site_favicon',
    'contact_email',
    // Mail (SMTP + identity)
    'mail_smtp_host',
    'mail_smtp_port',
    'mail_smtp_username',
    'mail_smtp_password',
    'mail_smtp_encryption',
    'mail_from_email',
    'mail_from_name',
    'mail_reply_to',
    // Mail receiving (IMAP) - stored for future sync tools
    'mail_imap_host',
    'mail_imap_port',
    'mail_imap_username',
    'mail_imap_password',
    'mail_imap_encryption',
    'mail_imap_sent_folder',
    'homepage_youtube_url',
    'about_youtube_url',
    'homepage_modal_image',
    'header_image',
    'office_title',
    'office_address',
    'smartsupp_key',
    'deposit_countdown_minutes',
];
$sensitiveKeys = ['mail_smtp_password', 'mail_imap_password'];

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $placeholders = implode(',', array_fill(0, count($allowedKeys), '?'));
    $stmt = $pdo->prepare("SELECT `key`, value FROM site_settings WHERE `key` IN ($placeholders)");
    $stmt->execute($allowedKeys);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = [
        'max_active_plans_per_user' => '3',
        'compounding_enabled' => '0',
        'referral_enabled' => '0',
        'referral_percentage' => '15',
        'deposit_bonus_percentage' => '10',
        'site_name' => '',
        'site_logo' => '',
        'site_favicon' => '',
        'contact_email' => '',
        'mail_smtp_host' => '',
        'mail_smtp_port' => '587',
        'mail_smtp_username' => '',
        'mail_smtp_encryption' => 'tls',
        'mail_from_email' => '',
        'mail_from_name' => '',
        'mail_reply_to' => '',
        'mail_imap_host' => '',
        'mail_imap_port' => '993',
        'mail_imap_username' => '',
        'mail_imap_encryption' => 'ssl',
        'mail_imap_sent_folder' => 'Sent',
        'homepage_youtube_url' => '',
        'about_youtube_url' => '',
        'homepage_modal_image' => '',
        'header_image' => '/bloombit.jpg',
        'office_title' => 'London Office',
        'office_address' => '40 Bank Street, Canary Wharf<br/>London, E14 5NR<br/>United Kingdom',
        'smartsupp_key' => '6fe6ebe5789e92d09f1a2fd405bd5b7d7967835d',
        'deposit_countdown_minutes' => '30',
        // write-only flags
        'mail_smtp_password_set' => '0',
        'mail_imap_password_set' => '0',
    ];
    foreach ($rows as $r) {
        if (in_array($r['key'], $allowedKeys, true)) {
            if (in_array($r['key'], $sensitiveKeys, true)) {
                if (!empty($r['value'])) {
                    if ($r['key'] === 'mail_smtp_password') $data['mail_smtp_password_set'] = '1';
                    if ($r['key'] === 'mail_imap_password') $data['mail_imap_password_set'] = '1';
                }
                continue;
            }
            $data[$r['key']] = $r['value'] ?? '';
        }
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $updates = [];
    foreach ($allowedKeys as $k) {
        if (!array_key_exists($k, $input)) continue;
        $v = trim((string) $input[$k]);
        if (in_array($k, $sensitiveKeys, true)) {
            // Passwords are write-only; blank means "keep existing"
            if ($v === '') continue;
        }
        if ($k === 'compounding_enabled') {
            $v = in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
        }
        if (in_array($k, ['mail_smtp_port', 'mail_imap_port'], true)) {
            $port = (int) $v;
            if ($port <= 0 || $port > 65535) continue;
            $v = (string) $port;
        }
        if (in_array($k, ['mail_smtp_encryption', 'mail_imap_encryption'], true)) {
            $vv = strtolower($v);
            if ($vv === 'starttls') $vv = 'tls';
            if (!in_array($vv, ['tls', 'ssl', 'none'], true)) continue;
            $v = $vv;
        }
        if ($k === 'deposit_countdown_minutes') {
            $m = (int) $v;
            if (!in_array($m, [5, 15, 30], true)) continue;
            $v = (string) $m;
        }
        if ($k === 'referral_enabled') {
            $v = in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
        }
        if ($k === 'referral_percentage' || $k === 'deposit_bonus_percentage') {
            $pct = (float) $v;
            $pct = max(0, min(100, $pct));
            $v = (string) round($pct, 2);
        }
        $updates[$k] = $v;
    }
    $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    foreach ($updates as $k => $v) {
        $stmt->execute([$k, $v]);
    }
    echo json_encode(['success' => true, 'data' => ['message' => 'Settings updated']]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

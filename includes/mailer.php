<?php
/**
 * Bloombit - PHPMailer Helper
 * Returns a configured PHPMailer instance.
 * Priority:
 * 1) Admin settings stored in DB (site_settings mail_* keys)
 * 2) config.php mail settings (env/file fallback)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Load PHPMailer from project PHPMailer folder
require_once dirname(__DIR__) . '/PHPMailer/Exception.php';
require_once dirname(__DIR__) . '/PHPMailer/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer/SMTP.php';

$configPath = dirname(__DIR__) . '/config.php';
if (!file_exists($configPath)) {
    throw new RuntimeException('config.php not found');
}
$config = include $configPath;
$mailConfig = $config['mail'] ?? [];

// Prefer DB-stored settings when available
require_once dirname(__DIR__) . '/includes/helpers.php';
try {
    $dbMail = [
        'smtp_host' => get_site_setting('mail_smtp_host', '') ?: null,
        'smtp_port' => get_site_setting('mail_smtp_port', '') ?: null,
        'smtp_username' => get_site_setting('mail_smtp_username', '') ?: null,
        'smtp_password' => get_site_setting('mail_smtp_password', '') ?: null,
        'smtp_encryption' => get_site_setting('mail_smtp_encryption', '') ?: null,
        'from_email' => get_site_setting('mail_from_email', '') ?: null,
        'from_name' => get_site_setting('mail_from_name', '') ?: null,
        'reply_to' => get_site_setting('mail_reply_to', '') ?: null,
    ];
    foreach ($dbMail as $k => $v) {
        if ($v === null || $v === '') continue;
        if ($k === 'smtp_port') $mailConfig[$k] = (int)$v;
        else $mailConfig[$k] = $v;
    }
} catch (Throwable $e) {
    // DB not ready; fallback to config.php
}

$mail = new PHPMailer(true);
$mail->CharSet = PHPMailer::CHARSET_UTF8;
$mail->isSMTP();
$mail->Host       = $mailConfig['smtp_host'] ?? 'smtp.example.com';
$mail->SMTPAuth   = !empty($mailConfig['smtp_username']);
$mail->Username   = $mailConfig['smtp_username'] ?? '';
$mail->Password   = $mailConfig['smtp_password'] ?? '';
$enc = strtolower((string)($mailConfig['smtp_encryption'] ?? 'tls'));
if ($enc === 'starttls') $enc = 'tls';
if ($enc === 'none') $enc = '';
$mail->SMTPSecure = $enc;
$mail->Port       = (int)($mailConfig['smtp_port'] ?? 587);
$defaultBrand = get_site_name();
$mail->setFrom($mailConfig['from_email'] ?? 'noreply@example.com', $mailConfig['from_name'] ?? $defaultBrand);
$mail->addReplyTo($mailConfig['reply_to'] ?? 'support@example.com', $mailConfig['from_name'] ?? $defaultBrand);

return $mail;

<?php
/**
 * Bloombit - PHPMailer Helper
 * Returns a configured PHPMailer instance using config.php settings.
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

$mail = new PHPMailer(true);
$mail->CharSet = PHPMailer::CHARSET_UTF8;
$mail->isSMTP();
$mail->Host       = $mailConfig['smtp_host'] ?? 'smtp.example.com';
$mail->SMTPAuth   = !empty($mailConfig['smtp_username']);
$mail->Username   = $mailConfig['smtp_username'] ?? '';
$mail->Password   = $mailConfig['smtp_password'] ?? '';
$mail->SMTPSecure = $mailConfig['smtp_encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = (int)($mailConfig['smtp_port'] ?? 587);
$mail->setFrom($mailConfig['from_email'] ?? 'noreply@bloombit.com', $mailConfig['from_name'] ?? 'Bloombit');
$mail->addReplyTo($mailConfig['reply_to'] ?? 'support@bloombit.com', $mailConfig['from_name'] ?? 'Bloombit');

return $mail;

<?php
/**
 * Bloombit - Central Configuration
 * Use environment variables or edit values directly for your deployment.
 */

$config = [
    'mail' => [
        'smtp_host' => getenv('MAIL_SMTP_HOST') ?: 'smtp.example.com',
        'smtp_port' => (int)(getenv('MAIL_SMTP_PORT') ?: 587),
        'smtp_username' => getenv('MAIL_SMTP_USERNAME') ?: '',
        'smtp_password' => getenv('MAIL_SMTP_PASSWORD') ?: '',
        'smtp_encryption' => getenv('MAIL_SMTP_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'noreply@bloombit.com',
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Site',
        'reply_to' => getenv('MAIL_REPLY_TO') ?: 'support@bloombit.com',
    ],
    'email_verification' => [
        'expiry_hours' => (int)(getenv('EMAIL_VERIFICATION_EXPIRY_HOURS') ?: 24),
        'secret' => getenv('EMAIL_VERIFICATION_SECRET') ?: 'change-me-in-production',
        'password_reset_expiry_minutes' => (int)(getenv('PASSWORD_RESET_EXPIRY_MINUTES') ?: 60),
    ],
    'site' => [
        'name' => getenv('SITE_NAME') ?: 'Site',
        'debug' => filter_var(getenv('DEBUG_MODE') ?? false, FILTER_VALIDATE_BOOLEAN),
        'timezone' => getenv('TIMEZONE') ?: 'UTC',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'u502532383_bloombit',
        'user' => getenv('DB_USER') ?: 'u502532383_bloombit',
        'pass' => getenv('DB_PASS') ?: 'Secretpass0721//',
    ],
    'admin' => [
        'user_ids' => array_map('intval', array_filter(explode(',', getenv('ADMIN_USER_IDS') ?: '1'))),
    ],
];

return $config;

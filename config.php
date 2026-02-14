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
        'from_name' => getenv('MAIL_FROM_NAME') ?: 'Bloombit',
        'reply_to' => getenv('MAIL_REPLY_TO') ?: 'support@bloombit.com',
    ],
    'email_verification' => [
        'expiry_hours' => (int)(getenv('EMAIL_VERIFICATION_EXPIRY_HOURS') ?: 24),
        'secret' => getenv('EMAIL_VERIFICATION_SECRET') ?: 'change-me-in-production',
        'base_url' => getenv('CONFIRMATION_BASE_URL') ?: (getenv('SITE_URL') ?: 'https://bloombit.com'),
        'password_reset_expiry_minutes' => (int)(getenv('PASSWORD_RESET_EXPIRY_MINUTES') ?: 60),
    ],
    'site' => [
        'url' => getenv('SITE_URL') ?: 'https://bloombit.com',
        'name' => getenv('SITE_NAME') ?: 'Bloombit',
        'debug' => filter_var(getenv('DEBUG_MODE') ?? false, FILTER_VALIDATE_BOOLEAN),
        'timezone' => getenv('TIMEZONE') ?: 'UTC',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'name' => getenv('DB_NAME') ?: 'bloombit',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
    ],
];

return $config;

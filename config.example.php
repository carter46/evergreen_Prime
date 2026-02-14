<?php
/**
 * Bloombit - Configuration Template
 * Use as reference. Configure config.php directly or set environment variables.
 */
return [
    'mail' => [
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 587,
        'smtp_username' => 'your-smtp-username',
        'smtp_password' => 'your-smtp-password',
        'smtp_encryption' => 'tls',
        'from_email' => 'noreply@bloombit.com',
        'from_name' => 'Bloombit',
        'reply_to' => 'support@bloombit.com',
    ],
    'email_verification' => [
        'expiry_hours' => 24,
        'secret' => 'generate-a-random-secret-key-here',
        'base_url' => 'https://bloombit.com',
        'password_reset_expiry_minutes' => 60,
    ],
    'site' => [
        'url' => 'https://bloombit.com',
        'name' => 'Bloombit',
        'debug' => false,
        'timezone' => 'UTC',
    ],
    'db' => [
        'host' => 'localhost',
        'name' => 'bloombit',
        'user' => 'db_username',
        'pass' => 'db_password',
    ],
];

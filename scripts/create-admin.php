<?php
/**
 * Bloombit - Create Admin Account
 * Run: php scripts/create-admin.php
 * Creates admin@mail.com with password Secretpass0721//
 * Then sets role to admin.
 */

$root = dirname(__DIR__);
$email = 'admin@mail.com';
$password = 'Secretpass0721//';
$name = 'Admin';

try {
    $pdo = require $root . '/includes/db.php';
} catch (Throwable $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

$stmt = $pdo->prepare('SELECT id, role FROM users WHERE email = ?');
$stmt->execute([$email]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $pdo->prepare('UPDATE users SET role = ? WHERE email = ?')->execute(['admin', $email]);
    echo "Admin account already exists. Role updated to admin.\n";
    exit(0);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name, role, email_verified, active) VALUES (?, ?, ?, ?, 1, 1)');
$stmt->execute([$email, $passwordHash, $name, 'admin']);

echo "Admin account created: {$email}\n";
echo "Password: {$password}\n";
echo "Login at /login\n";

<?php
/**
 * Bloombit - Create/Update Admin Account (Web)
 * Visit: /scripts/create-admin.php
 * Creates or updates admin@mail.com with the correct password hash so you can log in.
 */

$root = dirname(__DIR__);
require_once $root . '/includes/helpers.php';
$siteName = get_site_name();
$email = 'admin@mail.com';
$password = 'Secretpass0721//';
$name = 'Admin';

header('Content-Type: text/html; charset=utf-8');

$error = null;
$success = false;

try {
    $pdo = require $root . '/includes/db.php';
} catch (Throwable $e) {
    $error = 'Database connection failed: ' . $e->getMessage();
}

if (!$error) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('SELECT id, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $pdo->prepare('UPDATE users SET password_hash = ?, name = ?, role = ?, email_verified = 1, active = 1 WHERE email = ?');
        $stmt->execute([$passwordHash, $name, 'admin', $email]);
        $success = true;
        $message = 'Admin account updated. Password has been set.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, name, role, email_verified, active) VALUES (?, ?, ?, ?, 1, 1)');
        $stmt->execute([$email, $passwordHash, $name, 'admin']);
        $success = true;
        $message = 'Admin account created.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Create Admin – <?php echo htmlspecialchars($siteName); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <style>body { font-family: 'Space Grotesk', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
<div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Create Admin Account</h1>
    <?php if ($error): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif ($success): ?>
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 mb-6">
            <p class="font-semibold"><?php echo htmlspecialchars($message); ?></p>
            <p class="mt-3 text-sm">You can now log in with:</p>
            <p class="mt-2 font-mono text-sm bg-slate-100 p-3 rounded">Email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
            <p class="mt-1 font-mono text-sm bg-slate-100 p-3 rounded">Password: <strong><?php echo htmlspecialchars($password); ?></strong></p>
            <a href="/login" class="mt-6 inline-block w-full py-3 bg-amber-500 hover:bg-amber-600 text-black font-bold rounded-lg text-center transition-colors">Go to Login</a>
        </div>
        <p class="text-xs text-slate-500">For security, consider deleting or restricting access to this script after use.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php
/**
 * Bloombit - Server-side Auth Guard
 * Include at top of protected dashboard pages. Redirects to /login if not authenticated.
 */

$config = include __DIR__ . '/../config.php';
if (!($config['site']['debug'] ?? false)) {
    session_set_cookie_params([
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
require_once __DIR__ . '/session-bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    $redirect = '/login?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/dashboard');
    header('Location: ' . $redirect);
    exit;
}

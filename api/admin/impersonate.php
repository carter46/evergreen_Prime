<?php
/**
 * Bloombit - Admin Impersonate User
 * GET /api/admin/impersonate.php?user_id=X
 * Switches session to act as the target user. Admin can then view the user dashboard.
 */

session_start();
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /login');
    exit;
}

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
if ($userId <= 0) {
    header('Location: /dashboard/admin/users');
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    header('Location: /dashboard/admin/users');
    exit;
}

$stmt = $pdo->prepare('SELECT id, email, name, role FROM users WHERE id = ?');
$stmt->execute([$userId]);
$target = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$target || $target['role'] === 'admin') {
    header('Location: /dashboard/admin/users');
    exit;
}

$_SESSION['impersonate_admin_id'] = $_SESSION['user_id'];
$_SESSION['impersonate_admin_email'] = $_SESSION['email'];
$_SESSION['impersonate_admin_role'] = $_SESSION['role'];

$_SESSION['user_id'] = (int) $target['id'];
$_SESSION['email'] = $target['email'];
$_SESSION['role'] = $target['role'];

header('Location: /dashboard/user/dashboard');
exit;

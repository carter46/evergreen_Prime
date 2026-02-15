<?php
/**
 * Bloombit - Admin Stop Impersonating
 * GET /api/admin/stop-impersonate.php
 * Restores admin session after impersonating a user.
 */

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (!isset($_SESSION['impersonate_admin_id'])) {
    header('Location: /dashboard');
    exit;
}

$_SESSION['user_id'] = $_SESSION['impersonate_admin_id'];
$_SESSION['email'] = $_SESSION['impersonate_admin_email'];
$_SESSION['role'] = $_SESSION['impersonate_admin_role'];

unset($_SESSION['impersonate_admin_id'], $_SESSION['impersonate_admin_email'], $_SESSION['impersonate_admin_role']);

header('Location: /dashboard/admin/users');
exit;

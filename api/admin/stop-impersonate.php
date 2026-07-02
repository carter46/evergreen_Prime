<?php
/**
 * Bloombit - Admin Stop Impersonating
 * GET /api/admin/stop-impersonate.php
 * Restores admin session after impersonating a user.
 */

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/admin-audit-log.php';

if (!isset($_SESSION['impersonate_admin_id'])) {
    header('Location: /dashboard');
    exit;
}

$impersonatedUserId = (int) ($_SESSION['user_id'] ?? 0);

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    admin_audit_log(
        $pdo,
        'stop_impersonate',
        'session',
        $impersonatedUserId > 0 ? $impersonatedUserId : null,
        'Stopped impersonating user #' . $impersonatedUserId,
        null,
        ['admin_id' => (int) $_SESSION['impersonate_admin_id']]
    );
} catch (Throwable $e) {
}

$_SESSION['user_id'] = $_SESSION['impersonate_admin_id'];
$_SESSION['email'] = $_SESSION['impersonate_admin_email'];
$_SESSION['role'] = $_SESSION['impersonate_admin_role'];

unset($_SESSION['impersonate_admin_id'], $_SESSION['impersonate_admin_email'], $_SESSION['impersonate_admin_role']);

header('Location: /dashboard/admin/users');
exit;

<?php
/**
 * Bloombit - Dashboard Router
 * Redirects to user or admin dashboard based on role.
 */
require_once __DIR__ . '/../includes/auth-check.php';

if (($_SESSION['role'] ?? '') === 'admin') {
    header('Location: /dashboard/admin');
} else {
    header('Location: /dashboard/user/dashboard');
}
exit;

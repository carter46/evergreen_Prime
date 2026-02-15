<?php
/**
 * Bloombit - Admin-Only Access Guard
 * Include at top of admin dashboard pages. Redirects non-admins to user dashboard.
 * Depends on auth-check for login.
 */
require_once __DIR__ . '/auth-check.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /dashboard/user/dashboard');
    exit;
}

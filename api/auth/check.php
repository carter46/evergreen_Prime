<?php
/**
 * Bloombit - Auth Check API
 * GET /api/auth/check.php
 * Returns session status. Used by dashboard pages to protect routes.
 */

header('Content-Type: application/json');

session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'authenticated' => true,
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'] ?? null
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'authenticated' => false,
        'redirect' => '/login'
    ]);
}

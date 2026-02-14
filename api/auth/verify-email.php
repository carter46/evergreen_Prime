<?php
/**
 * Bloombit - Email Verification Handler
 * GET /api/auth/verify-email.php?token=xxx
 * Verifies email from link and redirects.
 */

header('Content-Type: application/json');

$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or missing verification token']);
    exit;
}

// Stub: In production, validate token against DB and mark email verified
$config = include dirname(__DIR__, 2) . '/config.php';
$baseUrl = $config['site']['url'] ?? '/';

header('Location: ' . rtrim($baseUrl, '/') . '/dashboard?verified=1');
exit;

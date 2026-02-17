<?php
/**
 * Bloombit - Admin KYC Document Viewer
 * GET /api/admin/kyc-view.php?path=uploads/kyc/1/front_xxx.jpg - Serve KYC document (admin only)
 */

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Forbidden');
}

$path = $_GET['path'] ?? '';
$path = preg_replace('/[^a-zA-Z0-9_\-\/\.]/', '', $path);
if (strpos($path, 'uploads/kyc/') !== 0) {
    http_response_code(400);
    exit('Invalid path');
}

$baseDir = dirname(__DIR__, 2);
$fullPath = $baseDir . '/' . $path;
$realPath = realpath($fullPath);
$realBase = realpath($baseDir . '/uploads/kyc');

// Prevent directory traversal
if (!$realPath || !$realBase || strpos($realPath, $realBase) !== 0) {
    http_response_code(403);
    exit('Forbidden');
}

if (!is_file($realPath) || !is_readable($realPath)) {
    http_response_code(404);
    exit('Not found');
}

$ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
$mimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];
$mime = $mimes[$ext] ?? 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($realPath));
readfile($realPath);
exit;

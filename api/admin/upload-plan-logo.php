<?php
/**
 * Upload plan logo image.
 * POST /api/admin/upload-plan-logo.php
 * FormData: file=image, plan_id=optional
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime]) || $file['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file. Use PNG, JPG, or WEBP. Max 2MB.']);
    exit;
}

$planId = isset($_POST['plan_id']) ? (int) $_POST['plan_id'] : 0;
$baseDir = dirname(__DIR__, 2) . '/uploads/plans';
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$ext = $allowed[$mime];
$prefix = $planId > 0 ? 'plan_' . $planId : 'plan_new';
$filename = $prefix . '_' . time() . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $baseDir . '/' . $filename)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
    exit;
}

$url = '/uploads/plans/' . $filename;
echo json_encode(['success' => true, 'data' => ['url' => $url]]);

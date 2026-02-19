<?php
/**
 * Bloombit - Upload Site Asset (logo, favicon)
 * POST /api/admin/upload-site-asset.php
 * FormData: type=logo|favicon, file=image
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

$type = trim($_POST['type'] ?? 'logo');
if (!in_array($type, ['logo', 'favicon', 'modal_image'], true)) {
    $type = 'logo';
}

$file = $_FILES['file'];
$allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp', 'image/x-icon' => 'ico'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowed[$mime]) || $file['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file. Use PNG, JPG, WEBP or ICO. Max 2MB.']);
    exit;
}

$baseDir = dirname(__DIR__, 2) . '/uploads/site';
if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);

$ext = $allowed[$mime];
$filename = $type . '_' . time() . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], $baseDir . '/' . $filename)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Upload failed']);
    exit;
}

$url = '/uploads/site/' . $filename;

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $keyMap = ['logo' => 'site_logo', 'favicon' => 'site_favicon', 'modal_image' => 'homepage_modal_image'];
    $key = $keyMap[$type] ?? 'site_logo';
    $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    $stmt->execute([$key, $url]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save setting']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['url' => $url, 'key' => $key]]);

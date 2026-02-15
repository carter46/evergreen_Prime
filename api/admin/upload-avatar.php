<?php
/**
 * Bloombit - Admin Upload User Avatar
 * POST /api/admin/upload-avatar.php
 * multipart/form-data: user_id, avatar (file)
 * Accepts PNG, JPEG, WEBP. Max 2MB.
 */
header('Content-Type: application/json');

session_start();
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
if ($userId < 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload failed']);
    exit;
}

$file = $_FILES['avatar'];
$allowedMimes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowedMimes[$mime])) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Use PNG, JPEG, or WEBP.']);
    exit;
}
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'error' => 'File too large. Max 2MB.']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, role FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}
if ($user['role'] === 'admin') {
    echo json_encode(['success' => false, 'error' => 'Cannot modify admin avatar']);
    exit;
}

$baseDir = dirname(__DIR__, 2) . '/uploads/avatars';
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

$ext = $allowedMimes[$mime];
$filename = $userId . '_' . time() . '.' . $ext;
$destPath = $baseDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

$avatarUrl = '/uploads/avatars/' . $filename;

try {
    $colStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
    if ($colStmt->rowCount() > 0) {
        $pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?')->execute([$avatarUrl, $userId]);
    }
} catch (Throwable $e) {
    unlink($destPath);
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['avatar_url' => $avatarUrl]]);

<?php
/**
 * Bloombit - Registration API
 * POST /api/auth/register.php
 * Accepts JSON or FormData. Fields: name, email, password, phone (opt), referral (opt), avatar (opt file).
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = $_POST;
if (empty($input)) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
}
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$referral = trim($input['referral'] ?? '');

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email already registered']);
    exit;
}

$avatarUrl = null;
if (!empty($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['avatar'];
    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (isset($allowed[$mime]) && $file['size'] <= 2 * 1024 * 1024) {
        $baseDir = dirname(__DIR__, 2) . '/uploads/avatars';
        if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);
        $ext = $allowed[$mime];
        $filename = 'reg_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $baseDir . '/' . $filename)) {
            $avatarUrl = '/uploads/avatars/' . $filename;
        }
    }
}

$cols = ['email', 'password_hash', 'name', 'role', 'email_verified', 'active'];
$vals = [$email, password_hash($password, PASSWORD_DEFAULT), $name ?: '', 'user', 0, 1];
$placeholders = ['?', '?', '?', '?', '?', '?'];

try {
    $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone_number'");
    if ($chk && $chk->rowCount() > 0) {
        $cols[] = 'phone_number';
        $vals[] = $phone ?: null;
        $placeholders[] = '?';
    }
} catch (Throwable $e) {}
try {
    $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'referral_code'");
    if ($chk && $chk->rowCount() > 0) {
        $cols[] = 'referral_code';
        $vals[] = $referral ?: null;
        $placeholders[] = '?';
    }
} catch (Throwable $e) {}
if ($avatarUrl) {
    $cols[] = 'avatar_url';
    $vals[] = $avatarUrl;
    $placeholders[] = '?';
}

$sql = 'INSERT INTO users (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
$stmt = $pdo->prepare($sql);
$stmt->execute($vals);

session_start();
$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['email'] = $email;
$_SESSION['role'] = 'user';

echo json_encode([
    'success' => true,
    'data' => [
        'message' => 'Registration successful. Welcome!',
        'redirect' => '/dashboard',
        'user_id' => $_SESSION['user_id'],
        'email' => $email,
    ]
]);

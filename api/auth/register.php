<?php
/**
 * Bloombit - Registration API
 * POST /api/auth/register.php
 * Accepts JSON or FormData. Fields: name, email, password, phone (opt), referral (opt), avatar (opt file).
 */

header('Content-Type: application/json');

// #region agent log
@file_put_contents(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'debug-e091ef.log', json_encode([
    'sessionId' => 'e091ef',
    'runId' => 'pre-fix',
    'hypothesisId' => 'H2',
    'location' => 'api/auth/register.php:entry',
    'message' => 'Register endpoint hit',
    'data' => [
        'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        'hasPost' => !empty($_POST),
        'contentType' => $_SERVER['CONTENT_TYPE'] ?? null,
    ],
    'timestamp' => (int) round(microtime(true) * 1000),
]) . "\n", FILE_APPEND);
// #endregion

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Handle both FormData and JSON input
$input = [];
if (!empty($_POST)) {
    $input = $_POST;
} else {
    $jsonInput = json_decode(file_get_contents('php://input'), true);
    if ($jsonInput) {
        $input = $jsonInput;
    }
}

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$referral = trim($input['referral'] ?? '');

// #region agent log
@file_put_contents(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'debug-e091ef.log', json_encode([
    'sessionId' => 'e091ef',
    'runId' => 'pre-fix',
    'hypothesisId' => 'H2',
    'location' => 'api/auth/register.php:parsed',
    'message' => 'Parsed registration fields (no PII)',
    'data' => [
        'emailHash8' => substr(sha1($email), 0, 8),
        'hasName' => ($name !== ''),
        'nameLen' => strlen($name),
        'nameIsDbName' => ($name === 'u502532383_bloombit'),
        'emailLen' => strlen($email),
        'hasPassword' => ($password !== ''),
        'hasPhone' => ($phone !== ''),
        'hasReferral' => ($referral !== ''),
        'hasAvatarUpload' => !empty($_FILES['avatar']['tmp_name']),
    ],
    'timestamp' => (int) round(microtime(true) * 1000),
]) . "\n", FILE_APPEND);
// #endregion

if ($name === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Full name is required']);
    exit;
}

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

// Cleanup expired pending registrations
try {
    $pdo->exec("DELETE FROM pending_registrations WHERE expires_at < NOW()");
} catch (Throwable $e) {}

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

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$expiresAt = date('Y-m-d H:i:s', time() + 24 * 3600);

// Store in pending_registrations (no users row until OTP verified)
try {
    $pdo->prepare('INSERT INTO pending_registrations (email, password_hash, name, phone_number, referral_code, avatar_url, expires_at) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), name = VALUES(name), phone_number = VALUES(phone_number), referral_code = VALUES(referral_code), avatar_url = VALUES(avatar_url), expires_at = VALUES(expires_at)')
        ->execute([$email, $passwordHash, $name, $phone ?: null, $referral ?: null, $avatarUrl, $expiresAt]);
    // #region agent log
    try {
        $s = $pdo->prepare('SELECT name FROM pending_registrations WHERE email = ?');
        $s->execute([$email]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        $stored = is_array($r) ? (string)($r['name'] ?? '') : '';
        @file_put_contents(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'debug-e091ef.log', json_encode([
            'sessionId' => 'e091ef',
            'runId' => 'pre-fix',
            'hypothesisId' => 'H6',
            'location' => 'api/auth/register.php:pending_check',
            'message' => 'Pending registration stored name check (no PII)',
            'data' => [
                'emailHash8' => substr(sha1($email), 0, 8),
                'storedNameLen' => strlen($stored),
                'storedNameIsDbName' => ($stored === 'u502532383_bloombit'),
                'storedNameMatchesInput' => ($stored === $name),
            ],
            'timestamp' => (int) round(microtime(true) * 1000),
        ]) . "\n", FILE_APPEND);
    } catch (Throwable $e) {}
    // #endregion
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Registration failed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/otp-helper.php';
$otp = createOtp($email, 'register');
if ($otp) {
    sendOtpEmail($email, $otp, 'register', $name ?: null);
}

echo json_encode([
    'success' => true,
    'data' => [
        'step' => 'verify_otp',
        'email' => $email,
        'message' => 'We sent a 6-digit code to your email. Enter it below to verify your account.',
    ]
]);

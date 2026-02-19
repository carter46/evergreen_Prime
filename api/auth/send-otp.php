<?php
/**
 * Bloombit - Send OTP API
 * POST /api/auth/send-otp.php
 * Body: { email, purpose } where purpose is 'register' | 'login' | 'disable_2fa'
 * For disable_2fa, requires authenticated user session.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? [];
$email = trim($input['email'] ?? '');
$purpose = trim($input['purpose'] ?? '');

// #region agent log
@file_put_contents(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'debug-e091ef.log', json_encode([
    'sessionId' => 'e091ef',
    'runId' => 'pre-fix',
    'hypothesisId' => 'H5',
    'location' => 'api/auth/send-otp.php:entry',
    'message' => 'Send-otp endpoint hit',
    'data' => [
        'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        'contentType' => $_SERVER['CONTENT_TYPE'] ?? null,
        'purpose' => $purpose,
        'emailHash8' => substr(sha1($email), 0, 8),
    ],
    'timestamp' => (int) round(microtime(true) * 1000),
]) . "\n", FILE_APPEND);
// #endregion

$allowed = ['register', 'login', 'disable_2fa'];
if (!in_array($purpose, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid purpose']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email']);
    exit;
}

if ($purpose === 'disable_2fa') {
    require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
    if (!isset($_SESSION['user_id']) || ($_SESSION['email'] ?? '') !== $email) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
}

require_once dirname(__DIR__, 2) . '/includes/otp-helper.php';

$otp = createOtp($email, $purpose);
if (!$otp) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create OTP']);
    exit;
}

$name = null;
if ($purpose === 'register' || $purpose === 'login') {
    try {
        $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
        if ($purpose === 'register') {
            // During registration, the user may not exist yet. Use pending_registrations name.
            $stmt = $pdo->prepare('SELECT name FROM pending_registrations WHERE email = ? AND expires_at > NOW()');
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $row['name'] ?? null;
        } else {
            $stmt = $pdo->prepare('SELECT name FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $name = $row['name'] ?? null;
        }
    } catch (Throwable $e) {}
} elseif ($purpose === 'disable_2fa') {
    try {
        $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
        $stmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $row['name'] ?? null;
    } catch (Throwable $e) {}
}

// #region agent log
@file_put_contents(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'debug-e091ef.log', json_encode([
    'sessionId' => 'e091ef',
    'runId' => 'pre-fix',
    'hypothesisId' => 'H5',
    'location' => 'api/auth/send-otp.php:name',
    'message' => 'Resolved name for OTP (no PII)',
    'data' => [
        'purpose' => $purpose,
        'emailHash8' => substr(sha1($email), 0, 8),
        'hasName' => ($name !== null && $name !== ''),
        'nameLen' => is_string($name) ? strlen($name) : null,
        'nameIsDbName' => ($name === 'u502532383_bloombit'),
    ],
    'timestamp' => (int) round(microtime(true) * 1000),
]) . "\n", FILE_APPEND);
// #endregion

$sent = sendOtpEmail($email, $otp, $purpose, $name);
if (!$sent) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send email']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['message' => 'OTP sent']]);

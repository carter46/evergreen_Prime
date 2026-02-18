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
        $stmt = $pdo->prepare('SELECT name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $name = $row['name'] ?? null;
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

$sent = sendOtpEmail($email, $otp, $purpose, $name);
if (!$sent) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send email']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['message' => 'OTP sent']]);

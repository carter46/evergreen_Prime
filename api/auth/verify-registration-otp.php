<?php
/**
 * Bloombit - Verify Registration OTP API
 * POST /api/auth/verify-registration-otp.php
 * Body: { email, otp }
 * Validates OTP, sets email_verified=1, creates session, returns redirect.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? [];
$email = trim($input['email'] ?? '');
$otp = trim($input['otp'] ?? '');

if (empty($email) || empty($otp)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and OTP are required']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/otp-helper.php';

if (!validateOtp($email, $otp, 'register')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired OTP']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $stmt = $pdo->prepare('SELECT id, email, role FROM users WHERE email = ? AND active = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    $pdo->prepare('UPDATE users SET email_verified = 1 WHERE id = ?')->execute([$user['id']]);

    require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    echo json_encode([
        'success' => true,
        'data' => [
            'message' => 'Email verified. Welcome!',
            'redirect' => '/dashboard',
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Verification failed']);
}

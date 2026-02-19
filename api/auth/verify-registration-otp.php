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

    // Cleanup expired pending registrations
    $pdo->exec("DELETE FROM pending_registrations WHERE expires_at < NOW()");

    $stmt = $pdo->prepare('SELECT email, password_hash, name, phone_number, referral_code, avatar_url FROM pending_registrations WHERE email = ? AND expires_at > NOW()');
    $stmt->execute([$email]);
    $pending = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pending) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Registration expired or not found. Please register again.']);
        exit;
    }

    // Create user only after OTP verified (email_verified = 1)
    $userName = trim($pending['name'] ?? '');
    // Fallback to email prefix if name is empty
    if (empty($userName) && !empty($pending['email'])) {
        $userName = explode('@', $pending['email'])[0];
    }
    $cols = ['email', 'password_hash', 'name', 'role', 'email_verified', 'active'];
    $vals = [$pending['email'], $pending['password_hash'], $userName, 'user', 1, 1];
    $placeholders = ['?', '?', '?', '?', '?', '?'];

    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone_number'");
        if ($chk && $chk->rowCount() > 0) {
            $cols[] = 'phone_number';
            $vals[] = $pending['phone_number'] ?? null;
            $placeholders[] = '?';
        }
    } catch (Throwable $e) {}
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'referral_code'");
        if ($chk && $chk->rowCount() > 0) {
            $cols[] = 'referral_code';
            $vals[] = $pending['referral_code'] ?? null;
            $placeholders[] = '?';
        }
    } catch (Throwable $e) {}
    if (!empty($pending['avatar_url'])) {
        $cols[] = 'avatar_url';
        $vals[] = $pending['avatar_url'];
        $placeholders[] = '?';
    }

    $sql = 'INSERT INTO users (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ')';
    $pdo->prepare($sql)->execute($vals);
    $userId = (int) $pdo->lastInsertId();

    $pdo->prepare('DELETE FROM pending_registrations WHERE email = ?')->execute([$email]);

    require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
    $_SESSION['user_id'] = $userId;
    $_SESSION['email'] = $pending['email'];
    $_SESSION['role'] = 'user';

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

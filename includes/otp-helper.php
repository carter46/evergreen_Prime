<?php
/**
 * Bloombit - OTP Helper Functions
 * Creates, validates, and sends 6-digit OTP codes via email.
 */

const OTP_EXPIRY_MINUTES = 10;

/**
 * Generate a 6-digit numeric OTP and store it for the given email/purpose.
 * Returns the OTP string on success, false on failure.
 */
function createOtp(string $email, string $purpose): ?string {
    $allowed = ['register', 'login', 'disable_2fa'];
    if (!in_array($purpose, $allowed, true)) return null;

    $otp = (string) random_int(100000, 999999);
    $expiresAt = date('Y-m-d H:i:s', time() + OTP_EXPIRY_MINUTES * 60);

    try {
        $pdo = require __DIR__ . '/db.php';
        // Invalidate any existing OTPs for this email+purpose
        $pdo->prepare('UPDATE email_otp_codes SET used = 1 WHERE email = ? AND purpose = ?')
            ->execute([$email, $purpose]);
        $pdo->prepare('INSERT INTO email_otp_codes (email, otp, purpose, expires_at) VALUES (?, ?, ?, ?)')
            ->execute([$email, $otp, $purpose, $expiresAt]);
        return $otp;
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Validate OTP: returns true if valid and unexpired, marks as used.
 * Returns false otherwise.
 */
function validateOtp(string $email, string $otp, string $purpose): bool {
    $allowed = ['register', 'login', 'disable_2fa'];
    if (!in_array($purpose, $allowed, true)) return false;
    $otp = preg_replace('/\D/', '', $otp);
    if (strlen($otp) !== 6) return false;

    try {
        $pdo = require __DIR__ . '/db.php';
        $stmt = $pdo->prepare('SELECT id FROM email_otp_codes WHERE email = ? AND otp = ? AND purpose = ? AND used = 0 AND expires_at > NOW()');
        $stmt->execute([$email, $otp, $purpose]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        $pdo->prepare('UPDATE email_otp_codes SET used = 1 WHERE id = ?')->execute([$row['id']]);
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Send OTP email using PHPMailer.
 * Returns true on success, false on failure.
 */
function sendOtpEmail(string $email, string $otp, string $purpose, ?string $name = null): bool {
    require_once __DIR__ . '/helpers.php';
    require_once __DIR__ . '/email-templates/render.php';

    $labels = [
        'register' => 'email verification',
        'login' => 'login',
        'disable_2fa' => 'disabling 2FA',
    ];
    $purpose_label = $labels[$purpose] ?? 'verification';

    try {
        $mail = require __DIR__ . '/mailer.php';
        $mail->clearAddresses();
        $mail->addAddress($email);
        $mail->Subject = 'Your verification code - ' . get_site_name();
        $mail->Body = renderEmailTemplate('otp.php', [
            'otp' => $otp,
            'name' => $name ?: 'User',
            'purpose_label' => $purpose_label,
        ]);
        $mail->AltBody = "Your verification code is: $otp\n\nThis code expires in " . OTP_EXPIRY_MINUTES . " minutes.";
        $mail->isHTML(true);
        $mail->send();
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

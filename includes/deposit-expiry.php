<?php
/**
 * Bloombit - Deposit countdown expiry helpers
 *
 * Implements:
 * - Admin-configurable countdown minutes (5/15/30)
 * - Auto-fail pending deposits that expire without user confirmation
 * - Email notification on failure
 */

require_once __DIR__ . '/helpers.php';

function get_deposit_countdown_minutes(): int {
    $raw = get_site_setting('deposit_countdown_minutes', '30');
    $m = (int) ($raw ?? 30);
    return in_array($m, [5, 15, 30], true) ? $m : 30;
}

function send_deposit_failed_email(PDO $pdo, array $txRow): void {
    try {
        $userId = (int)($txRow['user_id'] ?? 0);
        if ($userId <= 0) return;
        $userStmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ?');
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $userEmail = $user['email'] ?? null;
        if (!$userEmail) return;

        require_once __DIR__ . '/email-templates/render.php';
        $mail = require __DIR__ . '/mailer.php';
        $mail->clearAddresses();
        $mail->addAddress($userEmail);
        $mail->Subject = 'Deposit Failed - ' . get_site_name();
        $mail->Body = renderEmailTemplate('deposit-failed.php', [
            'name' => ($user['name'] ?? 'User') ?: 'User',
            'amount' => $txRow['amount'] ?? 0,
            'amountUsd' => $txRow['amount_usd'] ?? null,
            'currency' => $txRow['currency'] ?? 'USD',
            'reference' => $txRow['reference'] ?? '',
            'expires_at' => $txRow['expires_at'] ?? '',
        ]);
        $mail->AltBody = "Your deposit request has expired and was marked as failed. Please create a new deposit request from your wallet page.";
        $mail->isHTML(true);
        $mail->send();
    } catch (Throwable $e) {
        // Email failure should never block expiration.
    }
}

/**
 * Expire old pending deposits safely (idempotent).
 * Returns number of deposits transitioned to failed.
 */
function expire_pending_deposits(PDO $pdo, int $limit = 50): int {
    // Only run if countdown columns exist (safe on older DBs).
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'expires_at'");
        if (!$chk || $chk->rowCount() === 0) return 0;
        $chk2 = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'user_confirmed_at'");
        if (!$chk2 || $chk2->rowCount() === 0) return 0;
    } catch (Throwable $e) {
        return 0;
    }

    $amtUsdCol = '';
    try {
        $chk3 = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        if ($chk3 && $chk3->rowCount() > 0) $amtUsdCol = ', amount_usd';
    } catch (Throwable $e) {}

    $sql = "SELECT id, user_id, amount{$amtUsdCol}, currency, reference, expires_at
            FROM transactions
            WHERE type = 'deposit'
              AND status = 'pending'
              AND expires_at IS NOT NULL
              AND expires_at <= NOW()
              AND user_confirmed_at IS NULL
            ORDER BY expires_at ASC
            LIMIT " . (int)$limit;
    $rows = [];
    try {
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        return 0;
    }

    $expired = 0;
    foreach ($rows as $tx) {
        $txId = (int)($tx['id'] ?? 0);
        if ($txId <= 0) continue;
        try {
            $stmt = $pdo->prepare("UPDATE transactions
                                   SET status = 'failed'
                                   WHERE id = ?
                                     AND type = 'deposit'
                                     AND status = 'pending'
                                     AND expires_at IS NOT NULL
                                     AND expires_at <= NOW()
                                     AND user_confirmed_at IS NULL");
            $stmt->execute([$txId]);
            if ($stmt->rowCount() === 1) {
                $expired++;
                send_deposit_failed_email($pdo, $tx);
            }
        } catch (Throwable $e) {
            // continue
        }
    }
    return $expired;
}


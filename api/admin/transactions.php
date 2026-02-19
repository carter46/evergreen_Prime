<?php
/**
 * Bloombit - Admin Transactions API
 * POST /api/admin/transactions.php - Approve or reject transactions (deposits/withdrawals)
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$action = strtolower(trim($input['action'] ?? ''));
$transactionId = (int) ($input['transaction_id'] ?? 0);

if (!in_array($action, ['approve', 'reject'], true) || $transactionId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action or transaction ID']);
    exit;
}

// Fetch transaction (include amount_usd and reference when columns exist)
$cols = 'id, user_id, type, amount, currency, status';
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
    if ($chk && $chk->rowCount() > 0) $cols .= ', amount_usd';
    $chk2 = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'reference'");
    if ($chk2 && $chk2->rowCount() > 0) $cols .= ', reference';
} catch (Throwable $e) {}
$stmt = $pdo->prepare("SELECT $cols FROM transactions WHERE id = ?");
$stmt->execute([$transactionId]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tx) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Transaction not found']);
    exit;
}

if ($tx['status'] !== 'pending') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Transaction is not pending']);
    exit;
}

// Fetch user email and name for email notification
$userStmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ?');
$userStmt->execute([$tx['user_id']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
$userEmail = $user['email'] ?? null;
$userName = $user['name'] ?? 'User';

// Get transaction reference if available
$reference = $tx['reference'] ?? '';

$pdo->beginTransaction();
try {
    if ($action === 'approve') {
        // Update transaction status
        $pdo->prepare('UPDATE transactions SET status = ? WHERE id = ?')->execute(['completed', $transactionId]);
        
        // For deposits, credit user wallet
        if ($tx['type'] === 'deposit') {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$tx['user_id'], $tx['currency'], $tx['amount']]);
            require_once dirname(__DIR__, 2) . '/includes/helpers.php';
            if (isset($tx['amount_usd']) && $tx['amount_usd'] !== null && (float)$tx['amount_usd'] > 0) {
                bump_user_last_balance_usd($pdo, (int)$tx['user_id'], (float)$tx['amount_usd']);
            } else {
                $cur = strtoupper((string) $tx['currency']);
                if (in_array($cur, ['USD','USDT','USDC','BUSD','DAI'], true)) {
                    bump_user_last_balance_usd($pdo, (int)$tx['user_id'], (float)$tx['amount']);
                } else {
                    refresh_user_last_balance_usd($pdo, (int)$tx['user_id']);
                }
            }
        }
        // For withdrawals, status update is sufficient (balance already debited on request)
        
        $pdo->commit();
        
        // Send email notification
        if ($userEmail) {
            try {
                require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
                $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
                $mail->clearAddresses();
                $mail->addAddress($userEmail);
                $mail->Subject = ucfirst($tx['type']) . ' Approved - ' . get_site_name();
                $amountUsd = isset($tx['amount_usd']) && $tx['amount_usd'] !== null ? (float)$tx['amount_usd'] : (float)$tx['amount'];
                $mail->Body = renderEmailTemplate('transaction-status.php', [
                    'name' => $userName,
                    'status' => 'approved',
                    'type' => $tx['type'],
                    'amount' => $tx['amount'],
                    'currency' => $tx['currency'],
                    'amountUsd' => $amountUsd,
                    'reference' => $reference,
                ]);
                $mail->AltBody = "Your {$tx['type']} request has been approved. Amount: {$tx['currency']} " . number_format((float)$tx['amount'], 8, '.', ',') . ".";
                $mail->isHTML(true);
                $mail->send();
            } catch (Throwable $e) {
                // Email failure should not block the operation
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Transaction approved successfully'],
        ]);
    } else { // reject
        // Update transaction status
        $pdo->prepare('UPDATE transactions SET status = ? WHERE id = ?')->execute(['rejected', $transactionId]);
        
        // For withdrawals, credit back the user balance
        if ($tx['type'] === 'withdrawal') {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$tx['user_id'], $tx['currency'], $tx['amount']]);
            require_once dirname(__DIR__, 2) . '/includes/helpers.php';
            if (isset($tx['amount_usd']) && $tx['amount_usd'] !== null && (float)$tx['amount_usd'] > 0) {
                bump_user_last_balance_usd($pdo, (int)$tx['user_id'], (float)$tx['amount_usd']);
            } else {
                $cur = strtoupper((string) $tx['currency']);
                if (in_array($cur, ['USD','USDT','USDC','BUSD','DAI'], true)) {
                    bump_user_last_balance_usd($pdo, (int)$tx['user_id'], (float)$tx['amount']);
                } else {
                    refresh_user_last_balance_usd($pdo, (int)$tx['user_id']);
                }
            }
        }
        // For deposits, no balance change needed (user hasn't been credited yet)
        
        $pdo->commit();
        
        // Send email notification
        if ($userEmail) {
            try {
                require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';
                $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
                $mail->clearAddresses();
                $mail->addAddress($userEmail);
                $mail->Subject = ucfirst($tx['type']) . ' Rejected - ' . get_site_name();
                $amountUsd = isset($tx['amount_usd']) && $tx['amount_usd'] !== null ? (float)$tx['amount_usd'] : (float)$tx['amount'];
                $mail->Body = renderEmailTemplate('transaction-status.php', [
                    'name' => $userName,
                    'status' => 'rejected',
                    'type' => $tx['type'],
                    'amount' => $tx['amount'],
                    'currency' => $tx['currency'],
                    'amountUsd' => $amountUsd,
                    'reference' => $reference,
                ]);
                $mail->AltBody = "Your {$tx['type']} request has been rejected. Amount: {$tx['currency']} " . number_format((float)$tx['amount'], 8, '.', ',') . ".";
                $mail->isHTML(true);
                $mail->send();
            } catch (Throwable $e) {
                // Email failure should not block the operation
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Transaction rejected'],
        ]);
    }
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process transaction']);
}

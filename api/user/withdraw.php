<?php
/**
 * Bloombit - Withdraw API
 * POST /api/user/withdraw.php
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$currency = strtoupper(trim($input['currency'] ?? ''));
$amountUsd = isset($input['amount_usd']) ? (float) $input['amount_usd'] : null;
$amountCoin = isset($input['amount']) ? (float) $input['amount'] : null;
$address = trim($input['address'] ?? '');

if (empty($currency) || empty($address)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency and address are required']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$amount = null;
$amountUsdVal = null;

if ($amountUsd !== null && $amountUsd > 0) {
    $quote = quote_coin_amount_from_usd($pdo, $currency, $amountUsd);
    if (empty($quote)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $currency . '. Please try again later.']);
        exit;
    }
    $amountStr = $quote['coin_amount'];
    $amount = (float) $amountStr;
    $amountUsdVal = round($amountUsd, 2);
} elseif ($amountCoin !== null && $amountCoin > 0) {
    $amount = (float) $amountCoin;
    $amountStr = number_format($amount, 18, '.', '');
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount or coin amount']);
    exit;
}

// Validate min withdrawal limit (USD)
$minUsd = (float) (get_site_setting('min_withdrawal_limit', '10') ?: '10');
$checkUsd = $amountUsdVal !== null ? $amountUsdVal : ($amount * (get_coin_usd_price($pdo, $currency) ?? 1));
if ($checkUsd < $minUsd) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minimum withdrawal is $' . format_usd_amount($minUsd) . ' USD.']);
    exit;
}

$maxUsd = (float) (get_site_setting('max_withdrawal_limit', '') ?: 0);
if ($maxUsd > 0 && $checkUsd > $maxUsd) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Maximum withdrawal is $' . format_usd_amount($maxUsd) . ' USD.']);
    exit;
}

// Validate currency exists in wallet_addresses (admin-managed coins with addresses)
$stmt = $pdo->prepare('SELECT 1 FROM wallet_addresses wa INNER JOIN coins c ON c.id = wa.coin_id AND c.enabled = 1 WHERE UPPER(c.symbol) = ? LIMIT 1');
$stmt->execute([$currency]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or unsupported currency']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    // Check balance WITHIN transaction to prevent race conditions
    $stmt = $pdo->prepare('SELECT amount FROM wallet_balances WHERE user_id = ? AND currency = ? FOR UPDATE');
    $stmt->execute([$userId, $currency]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $row ? (float) $row['amount'] : 0;
    
    if ($balance < $amount) {
        $pdo->rollBack();
        http_response_code(400);
        $availFmt = rtrim(rtrim(number_format($balance, 8, '.', ''), '0'), '.');
        $needFmt = rtrim(rtrim(number_format($amount, 8, '.', ''), '0'), '.');
        echo json_encode([
            'success' => false,
            'error' => 'Insufficient ' . $currency . ' balance. You have ' . $availFmt . ' ' . $currency . ' but this withdrawal requires ' . $needFmt . ' ' . $currency . '. Withdrawals only use the selected coin wallet, not your total account value or active investments.',
        ]);
        exit;
    }

    $hasAmountUsdCol = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) {}
    
    // Create transaction record
    if ($hasAmountUsdCol && $amountUsdVal !== null) {
        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, 'withdrawal', $amountStr, $amountUsdVal, $currency, 'pending', $address]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, 'withdrawal', $amountStr, $currency, 'pending', $address]);
    }
    $txId = (int) $pdo->lastInsertId();

    // Debit user balance immediately (admin will credit back on reject)
    // Update existing balance or create new row with negative amount
    $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, -?) ON DUPLICATE KEY UPDATE amount = amount - ?')
        ->execute([$userId, $currency, $amount, $amount]);

    // Update cached USD balance using user-entered USD amount when available
    if ($amountUsdVal !== null) {
        bump_user_last_balance_usd($pdo, $userId, -1 * (float)$amountUsdVal);
    } else {
        $cur = strtoupper($currency);
        if (in_array($cur, ['USD','USDT','USDC','BUSD','DAI'], true)) {
            bump_user_last_balance_usd($pdo, $userId, -1 * (float)$amount);
        } else {
            refresh_user_last_balance_usd($pdo, $userId);
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process withdrawal']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => [
        'transaction_id' => $txId,
        'message' => 'Withdrawal request submitted. Awaiting admin approval.',
    ],
]);

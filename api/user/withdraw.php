<?php
/**
 * Bloombit - Withdraw API
 * POST /api/user/withdraw.php
 * Debits centralized USD wallet; payout is sent in selected crypto.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$method = strtolower(trim($input['withdrawal_method'] ?? $input['method'] ?? 'crypto'));
$currency = strtoupper(trim($input['currency'] ?? ''));
$amountUsd = isset($input['amount_usd']) ? (float) $input['amount_usd'] : null;
$address = trim($input['address'] ?? '');

if ($method !== 'crypto') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid withdrawal method']);
    exit;
}

if (empty($currency) || empty($address)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency and address are required']);
    exit;
}

if ($amountUsd === null || $amountUsd <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$quote = quote_coin_amount_from_usd($pdo, $currency, $amountUsd);
if (empty($quote)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $currency . '. Please try again later.']);
    exit;
}
$amountStr = $quote['coin_amount'];
$amountUsdVal = round($amountUsd, 2);

$minUsd = (float) (get_site_setting('min_withdrawal_limit', '10') ?: '10');
if ($amountUsdVal < $minUsd) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minimum withdrawal is $' . format_usd_amount($minUsd) . ' USD.']);
    exit;
}

$maxUsd = (float) (get_site_setting('max_withdrawal_limit', '') ?: 0);
if ($maxUsd > 0 && $amountUsdVal > $maxUsd) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Maximum withdrawal is $' . format_usd_amount($maxUsd) . ' USD.']);
    exit;
}

$stmt = $pdo->prepare('SELECT 1 FROM wallet_addresses wa INNER JOIN coins c ON c.id = wa.coin_id AND c.enabled = 1 WHERE UPPER(c.symbol) = ? LIMIT 1');
$stmt->execute([$currency]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or unsupported payout currency']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    if (!debit_user_usd($pdo, $userId, $amountUsdVal)) {
        $pdo->rollBack();
        http_response_code(400);
        $avail = get_user_usd_balance($pdo, $userId);
        echo json_encode([
            'success' => false,
            'error' => 'Insufficient USD balance. You have $' . format_usd_amount($avail) . ' available.',
        ]);
        exit;
    }

    $hasAmountUsdCol = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) {}

    if ($hasAmountUsdCol) {
        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, 'withdrawal', $amountStr, $amountUsdVal, $currency, 'pending', $address]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, 'withdrawal', $amountStr, $currency, 'pending', $address]);
    }
    $txId = (int) $pdo->lastInsertId();

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

<?php
/**
 * Bloombit - Deposit API
 * POST /api/user/deposit.php - Create pending deposit transaction
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
$reference = trim($input['reference'] ?? '');

if (empty($currency)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency is required']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
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
$amount = null;
$amountUsdVal = null;

if ($amountUsd !== null && $amountUsd > 0) {
    $quote = quote_coin_amount_from_usd($pdo, $currency, $amountUsd);
    if (empty($quote)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $currency . '. Please try again later.']);
        exit;
    }
    $amount = $quote['coin_amount'];
    $amountUsdVal = round($amountUsd, 2);
} elseif ($amountCoin !== null && $amountCoin > 0) {
    $amount = number_format($amountCoin, 18, '.', '');
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount or coin amount']);
    exit;
}

$hasAmountUsdCol = false;
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
    $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
} catch (Throwable $e) {}

if ($hasAmountUsdCol) {
    $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, 'deposit', $amount, $amountUsdVal, $currency, 'pending', $reference ?: null]);
} else {
    $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, 'deposit', $amount, $currency, 'pending', $reference ?: null]);
}

$txId = (int) $pdo->lastInsertId();

echo json_encode([
    'success' => true,
    'data' => [
        'transaction_id' => $txId,
        'message' => 'Deposit request submitted. Awaiting admin approval.',
        'coin_amount' => (float) $amount,
        'amount_usd' => $amountUsdVal,
    ],
]);

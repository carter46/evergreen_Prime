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
require_once dirname(__DIR__, 2) . '/includes/deposit-expiry.php';
require_once dirname(__DIR__, 2) . '/includes/payment-methods.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$paymentMethodId = isset($input['payment_method_id']) ? (int) $input['payment_method_id'] : 0;
$currency = strtoupper(trim($input['currency'] ?? ''));
$amountUsd = isset($input['amount_usd']) ? (float) $input['amount_usd'] : null;
$amountCoin = isset($input['amount']) ? (float) $input['amount'] : null;
$reference = trim($input['reference'] ?? '');

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    ensure_payment_methods_schema($pdo);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

try { expire_pending_deposits($pdo); } catch (Throwable $e) {}

$method = null;
if ($paymentMethodId > 0) {
    $method = get_payment_method_by_id($pdo, $paymentMethodId, false);
    if (!$method || empty($method['enabled'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid payment method']);
        exit;
    }
} elseif ($currency !== '') {
    $stmt = $pdo->prepare(
        "SELECT pm.id FROM payment_methods pm
         INNER JOIN coins c ON c.id = pm.coin_id AND c.enabled = 1
         WHERE pm.method_type = 'crypto' AND pm.enabled = 1 AND UPPER(c.symbol) = ?
         LIMIT 1"
    );
    $stmt->execute([$currency]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $method = get_payment_method_by_id($pdo, (int) $row['id'], false);
    }
}

if (!$method) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Select a valid payment method']);
    exit;
}

$methodType = $method['method_type'];
$userId = (int) $_SESSION['user_id'];
$amount = null;
$amountUsdVal = null;
$txCurrency = $currency;

if ($methodType === 'crypto') {
    $txCurrency = strtoupper((string) ($method['symbol'] ?? $currency));
    if ($amountUsd !== null && $amountUsd > 0) {
        $quote = quote_coin_amount_from_usd($pdo, $txCurrency, $amountUsd);
        if (empty($quote)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $txCurrency . '. Please try again later.']);
            exit;
        }
        $amount = $quote['coin_amount'];
        $amountUsdVal = round($amountUsd, 2);
    } elseif ($amountCoin !== null && $amountCoin > 0) {
        $amount = number_format($amountCoin, 18, '.', '');
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount']);
        exit;
    }
} else {
    if ($amountUsd === null || $amountUsd <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount']);
        exit;
    }
    $amountUsdVal = round($amountUsd, 2);
    $amount = number_format($amountUsdVal, 18, '.', '');
    $txCurrency = $methodType === 'bank' ? 'BANK' : 'CARD';
}

$hasAmountUsdCol = false;
$hasExpiresAtCol = false;
$hasPaymentMethodCol = false;
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
    $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'expires_at'");
    $hasExpiresAtCol = $chk && $chk->rowCount() > 0;
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'payment_method_id'");
    $hasPaymentMethodCol = $chk && $chk->rowCount() > 0;
} catch (Throwable $e) {}

$countdownMinutes = get_deposit_countdown_minutes();
$expiresAt = date('Y-m-d H:i:s', time() + ($countdownMinutes * 60));

$cols = ['user_id', 'type', 'amount', 'currency', 'status', 'reference'];
$vals = [$userId, 'deposit', $amount, $txCurrency, 'pending', $reference ?: null];
if ($hasAmountUsdCol) {
    $cols[] = 'amount_usd';
    $vals[] = $amountUsdVal;
}
if ($hasPaymentMethodCol) {
    $cols[] = 'payment_method_id';
    $vals[] = $paymentMethodId > 0 ? $paymentMethodId : (int) $method['id'];
}
if ($hasExpiresAtCol) {
    $cols[] = 'expires_at';
    $vals[] = $expiresAt;
}

$placeholders = implode(', ', array_fill(0, count($cols), '?'));
$stmt = $pdo->prepare('INSERT INTO transactions (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')');
$stmt->execute($vals);
$txId = (int) $pdo->lastInsertId();

$response = [
    'transaction_id' => $txId,
    'message' => 'Deposit request submitted. Awaiting admin approval.',
    'payment_method' => $method,
    'amount_usd' => $amountUsdVal,
    'expires_at' => $hasExpiresAtCol ? $expiresAt : null,
    'countdown_minutes' => $countdownMinutes,
];
if ($methodType === 'crypto') {
    $response['coin_amount'] = (float) $amount;
}

echo json_encode(['success' => true, 'data' => $response]);

<?php
/**
 * Bloombit - Withdraw API
 * POST /api/user/withdraw.php
 * Debits centralized USD wallet; payout via crypto, bank, or card.
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
require_once dirname(__DIR__, 2) . '/includes/payment-methods.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$method = strtolower(trim($input['withdrawal_method'] ?? $input['method'] ?? 'crypto'));
$paymentMethodId = isset($input['payment_method_id']) ? (int) $input['payment_method_id'] : 0;
$currency = strtoupper(trim($input['currency'] ?? ''));
$amountUsd = isset($input['amount_usd']) ? (float) $input['amount_usd'] : null;
$address = trim($input['address'] ?? '');
$payoutDetails = $input['payout_details'] ?? null;

if (!in_array($method, ['crypto', 'bank', 'card'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid withdrawal method']);
    exit;
}

if ($amountUsd === null || $amountUsd <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid USD amount']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    ensure_payment_methods_schema($pdo);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

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

$pmMethod = null;
$amountStr = number_format($amountUsdVal, 18, '.', '');
$txCurrency = 'USD';
$reference = $address;
$payoutJson = null;

if ($method === 'crypto') {
    if (empty($currency) || empty($address)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Currency and address are required']);
        exit;
    }
    $stmt = $pdo->prepare(
        "SELECT pm.id FROM payment_methods pm
         INNER JOIN coins c ON c.id = pm.coin_id AND c.enabled = 1
         WHERE pm.method_type = 'crypto' AND pm.enabled = 1 AND UPPER(c.symbol) = ?
         LIMIT 1"
    );
    $stmt->execute([$currency]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or unsupported payout currency']);
        exit;
    }
    $paymentMethodId = (int) $row['id'];
    $quote = quote_coin_amount_from_usd($pdo, $currency, $amountUsdVal);
    if (empty($quote)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $currency . '. Please try again later.']);
        exit;
    }
    $amountStr = $quote['coin_amount'];
    $txCurrency = $currency;
} elseif ($method === 'bank') {
    $details = is_array($payoutDetails) ? $payoutDetails : [];
    $bankName = trim((string) ($details['bank_name'] ?? ''));
    $accountName = trim((string) ($details['account_name'] ?? ''));
    $accountNumber = trim((string) ($details['account_number'] ?? ''));
    if ($bankName === '' || $accountName === '' || $accountNumber === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Bank name, account name, and account number are required']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM payment_methods WHERE method_type = 'bank' AND enabled = 1 LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Bank transfer withdrawals are not available']);
        exit;
    }
    if ($paymentMethodId <= 0) {
        $paymentMethodId = (int) $row['id'];
    }
    $txCurrency = 'BANK';
    $payoutJson = json_encode([
        'bank_name' => $bankName,
        'account_name' => $accountName,
        'account_number' => $accountNumber,
        'routing_number' => trim((string) ($details['routing_number'] ?? '')) ?: null,
        'swift_code' => trim((string) ($details['swift_code'] ?? '')) ?: null,
        'iban' => trim((string) ($details['iban'] ?? '')) ?: null,
        'bank_address' => trim((string) ($details['bank_address'] ?? '')) ?: null,
    ]);
    $reference = $accountNumber;
} else {
    $details = is_array($payoutDetails) ? $payoutDetails : [];
    $brand = strtolower(trim((string) ($details['card_brand'] ?? '')));
    $cardNumber = preg_replace('/\D+/', '', (string) ($details['card_number'] ?? ''));
    $cardHolder = trim((string) ($details['card_holder_name'] ?? ''));
    if (!in_array($brand, payment_method_card_brands(), true) || $cardNumber === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Card brand and card number are required']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id FROM payment_methods WHERE method_type = 'card' AND enabled = 1 LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Card withdrawals are not available']);
        exit;
    }
    if ($paymentMethodId <= 0) {
        $paymentMethodId = (int) $row['id'];
    }
    $txCurrency = 'CARD';
    $payoutJson = json_encode([
        'card_brand' => $brand,
        'card_holder_name' => $cardHolder ?: null,
        'card_number' => $cardNumber,
        'card_expiry' => trim((string) ($details['card_expiry'] ?? '')) ?: null,
    ]);
    $reference = mask_card_number($cardNumber);
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
    $hasPaymentMethodCol = false;
    $hasPayoutDetailsCol = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        $hasAmountUsdCol = $chk && $chk->rowCount() > 0;
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'payment_method_id'");
        $hasPaymentMethodCol = $chk && $chk->rowCount() > 0;
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'payout_details'");
        $hasPayoutDetailsCol = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) {}

    $cols = ['user_id', 'type', 'amount', 'currency', 'status', 'reference'];
    $vals = [$userId, 'withdrawal', $amountStr, $txCurrency, 'pending', $reference ?: null];
    if ($hasAmountUsdCol) {
        $cols[] = 'amount_usd';
        $vals[] = $amountUsdVal;
    }
    if ($hasPaymentMethodCol && $paymentMethodId > 0) {
        $cols[] = 'payment_method_id';
        $vals[] = $paymentMethodId;
    }
    if ($hasPayoutDetailsCol && $payoutJson) {
        $cols[] = 'payout_details';
        $vals[] = $payoutJson;
    }

    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $stmt = $pdo->prepare('INSERT INTO transactions (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')');
    $stmt->execute($vals);
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

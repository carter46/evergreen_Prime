<?php
/**
 * Bloombit - Subscribe to Investment Plan API
 * POST /api/user/subscribe-plan.php
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
$planId = (int) ($input['plan_id'] ?? 0);
$amountUsd = (float) ($input['amount'] ?? 0);
$currency = strtoupper(trim($input['currency'] ?? 'USD'));

if ($planId <= 0 || $amountUsd <= 0 || empty($currency)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid plan ID, amount, or currency']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

// Validate plan exists and is enabled
$stmt = $pdo->prepare('SELECT id, name, min_deposit, max_deposit, enabled FROM plans WHERE id = ?');
$stmt->execute([$planId]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Plan not found']);
    exit;
}

if (!$plan['enabled']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Plan is not available']);
    exit;
}

// Validate amount is within range
if ($amountUsd < $plan['min_deposit']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Amount is below minimum deposit']);
    exit;
}

if ($plan['max_deposit'] !== null && $amountUsd > $plan['max_deposit']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Amount exceeds maximum deposit']);
    exit;
}

// Convert USD amount to selected currency for debit
$amountToDebit = $amountUsd;
if (!in_array($currency, ['USDT','USDC','USD','BUSD'], true)) {
    $prices = get_coingecko_prices_usd();
    $cgId = currency_to_coingecko($currency);
    if (!$cgId || !isset($prices[$cgId]) || $prices[$cgId] <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to convert ' . $currency . ' to USD. Try USDT or USD.']);
        exit;
    }
    $amountToDebit = $amountUsd / $prices[$cgId];
}

// Check user balance in selected currency
$userId = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT amount FROM wallet_balances WHERE user_id = ? AND currency = ?');
$stmt->execute([$userId, $currency]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$balance = $row ? (float)$row['amount'] : 0;

if ($balance < $amountToDebit) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Insufficient ' . $currency . ' balance. Need ' . number_format($amountToDebit, 8) . ' ' . $currency]);
    exit;
}

// Check max active plans per user
$maxPlans = (int) get_site_setting('max_active_plans_per_user', '3');
$stmt = $pdo->prepare('SELECT COUNT(*) FROM user_investments WHERE user_id = ? AND status = ?');
$stmt->execute([$userId, 'active']);
$activeCount = (int) $stmt->fetchColumn();

if ($activeCount >= $maxPlans) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Maximum active plans limit reached']);
    exit;
}

$pdo->beginTransaction();
try {
    // Create investment record (amount stored in USD for plan value)
    $stmt = $pdo->prepare('INSERT INTO user_investments (user_id, plan_id, amount, start_date, status) VALUES (?, ?, ?, CURDATE(), ?)');
    $stmt->execute([$userId, $planId, $amountUsd, 'active']);
    
    // Debit user balance (deduct from selected currency)
    $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, -?) ON DUPLICATE KEY UPDATE amount = amount - ?')->execute([$userId, $currency, $amountToDebit, $amountToDebit]);
    
    // Create transaction record (amount debited in selected currency)
    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status) VALUES (?, ?, ?, ?, ?)')->execute([$userId, 'investment', $amountToDebit, $currency, 'completed']);
    
    $pdo->commit();
    echo json_encode([
        'success' => true,
        'data' => ['message' => 'Successfully subscribed to investment plan'],
    ]);
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to subscribe to plan']);
}

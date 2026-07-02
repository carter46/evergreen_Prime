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
require_once dirname(__DIR__, 2) . '/includes/plan-types.php';
require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$planId = (int) ($input['plan_id'] ?? 0);
$amountUsd = (float) ($input['amount'] ?? 0);
$durationDays = isset($input['duration_days']) ? (int) $input['duration_days'] : null;

if ($planId <= 0 || $amountUsd <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid plan ID or amount']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, name, min_deposit, max_deposit, duration_days, min_duration_days, max_duration_days, min_duration_months, max_duration_months, enabled FROM plans WHERE id = ?');
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

$planFixedDays = plan_duration_days($plan);
if ($durationDays === null || $durationDays < 1) {
    $durationDays = $planFixedDays;
}
if ($durationDays !== $planFixedDays) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'This plan has a fixed duration of ' . $planFixedDays . ' days']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$usdBalance = get_user_spendable_usd_balance($pdo, $userId);
if ($usdBalance < $amountUsd) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Insufficient USD balance. Available: $' . format_usd_amount($usdBalance)]);
    exit;
}

$dup = $pdo->prepare('SELECT id FROM user_investments WHERE user_id = ? AND plan_id = ? AND status IN (?, ?) LIMIT 1');
$dup->execute([$userId, $planId, 'active', 'paused']);
if ($dup->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'You already have an active subscription to this plan']);
    exit;
}

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
    if (!debit_user_usd($pdo, $userId, $amountUsd)) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Insufficient USD balance']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO user_investments (user_id, plan_id, amount, duration_days, start_date, status) VALUES (?, ?, ?, ?, CURDATE(), ?)');
    $stmt->execute([$userId, $planId, $amountUsd, $durationDays, 'active']);

    $walletCurrency = user_usd_wallet_currency();
    $amountStr = number_format($amountUsd, 18, '.', '');
    $hasAmountUsdCol = false;
    try {
        $colChk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        $hasAmountUsdCol = $colChk && $colChk->rowCount() > 0;
    } catch (Throwable $e) {}
    if ($hasAmountUsdCol) {
        $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$userId, 'investment', $amountStr, round($amountUsd, 2), $walletCurrency, 'completed']);
    } else {
        $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status) VALUES (?, ?, ?, ?, ?)')
            ->execute([$userId, 'investment', $amountStr, $walletCurrency, 'completed']);
    }

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

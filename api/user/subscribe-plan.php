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
$durationDays = isset($input['duration_days']) ? (int) $input['duration_days'] : null;

if ($planId <= 0 || $amountUsd <= 0 || empty($currency)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid plan ID, amount, or currency']);
    exit;
}

$stablecoins = ['USDT','USDC','USD','BUSD','DAI'];
$isStable = in_array($currency, $stablecoins, true);

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

// Validate plan exists and is enabled
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

// Resolve plan min/max duration (days)
$planMinDays = isset($plan['min_duration_days']) && $plan['min_duration_days'] !== null ? (int) $plan['min_duration_days'] : (isset($plan['min_duration_months']) && $plan['min_duration_months'] !== null ? (int) $plan['min_duration_months'] * 30 : (int) $plan['duration_days']);
$planMaxDays = isset($plan['max_duration_days']) && $plan['max_duration_days'] !== null ? (int) $plan['max_duration_days'] : (isset($plan['max_duration_months']) && $plan['max_duration_months'] !== null ? (int) $plan['max_duration_months'] * 30 : (int) $plan['duration_days']);

if ($durationDays === null || $durationDays < 1) {
    $durationDays = $planMinDays;
}
if ($durationDays < $planMinDays || $durationDays > $planMaxDays) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Duration must be between ' . $planMinDays . ' and ' . $planMaxDays . ' days']);
    exit;
}

// For stablecoins: 1:1 with USD. For volatile coins: convert USD to coin amount.
if ($isStable) {
    $amountToDebit = $amountUsd;
} else {
    $quote = quote_coin_amount_from_usd($pdo, $currency, $amountUsd);
    if (empty($quote)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to get price for ' . $currency . '. Try USDT, USDC, or DAI.']);
        exit;
    }
    $amountToDebit = (float) $quote['coin_amount'];
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

// Prevent duplicate subscription to the same plan (active or paused)
$dup = $pdo->prepare('SELECT id FROM user_investments WHERE user_id = ? AND plan_id = ? AND status IN (?, ?) LIMIT 1');
$dup->execute([$userId, $planId, 'active', 'paused']);
if ($dup->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'You already have an active subscription to this plan']);
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
    // Create investment record (amount stored in USD, duration_days from user choice)
    $stmt = $pdo->prepare('INSERT INTO user_investments (user_id, plan_id, amount, duration_days, start_date, status) VALUES (?, ?, ?, ?, CURDATE(), ?)');
    $stmt->execute([$userId, $planId, $amountUsd, $durationDays, 'active']);
    $investmentId = (int) $pdo->lastInsertId();

    // Debit user balance (deduct from selected currency)
    $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, -?) ON DUPLICATE KEY UPDATE amount = amount - ?')->execute([$userId, $currency, $amountToDebit, $amountToDebit]);

    // Create transaction record (amount debited in selected currency)
    $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status) VALUES (?, ?, ?, ?, ?)')->execute([$userId, 'investment', $amountToDebit, $currency, 'completed']);

    // Update cached USD balance snapshot
    if ($isStable) {
        bump_user_last_balance_usd($pdo, $userId, -1 * (float)$amountToDebit);
    } else {
        bump_user_last_balance_usd($pdo, $userId, -1 * (float)$amountUsd);
    }

    // Referral bonus: pay referrer on first plan subscription (when referral system is enabled)
    if (get_site_setting('referral_enabled', '0') === '1' && $amountUsd > 0) {
        try {
            $chk = $pdo->query("SHOW TABLES LIKE 'referral_earnings'");
            if ($chk && $chk->rowCount() > 0) {
                $userRow = $pdo->prepare('SELECT referred_by_user_id FROM users WHERE id = ?');
                $userRow->execute([$userId]);
                $userRow = $userRow->fetch(PDO::FETCH_ASSOC);
                $referrerId = isset($userRow['referred_by_user_id']) ? (int) $userRow['referred_by_user_id'] : 0;
                if ($referrerId > 0 && $referrerId !== $userId) {
                    $paid = $pdo->prepare('SELECT id FROM referral_earnings WHERE referred_user_id = ? AND source = ? LIMIT 1');
                    $paid->execute([$userId, 'plan_subscription']);
                    if (!$paid->fetch()) {
                        $pct = (float) (get_site_setting('referral_percentage', '15') ?: '15');
                        $pct = max(0, min(100, $pct));
                        $bonusUsd = round($amountUsd * ($pct / 100), 2);
                        if ($bonusUsd > 0) {
                            $refCurrency = 'USDT';
                            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                                ->execute([$referrerId, $refCurrency, $bonusUsd]);
                            $hasAmountUsd = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")->rowCount() > 0;
                            if ($hasAmountUsd) {
                                $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)')
                                    ->execute([$referrerId, 'referral_bonus', $bonusUsd, $bonusUsd, $refCurrency, 'completed', 'ref_inv_' . $investmentId]);
                            } else {
                                $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                                    ->execute([$referrerId, 'referral_bonus', $bonusUsd, $refCurrency, 'completed', 'ref_inv_' . $investmentId]);
                            }
                            bump_user_last_balance_usd($pdo, $referrerId, (float) $bonusUsd);
                            $pdo->prepare('INSERT INTO referral_earnings (referrer_user_id, referred_user_id, source, amount_usd, currency, percent_used, reference_id) VALUES (?, ?, ?, ?, ?, ?, ?)')
                                ->execute([$referrerId, $userId, 'plan_subscription', $bonusUsd, $refCurrency, $pct, $investmentId]);
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // Don't fail the subscription if referral logic fails (e.g. table missing)
        }
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

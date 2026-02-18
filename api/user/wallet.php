<?php
/**
 * Bloombit - User Wallet API
 * GET /api/user/wallet.php - Fetch balances and transactions
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$balances = [];
$totalUsd = 0.0;
$totalUsdUpdatedAt = null;

// Prefer cached USD balance from users table (stable, consistent display)
$hasCachedUsd = false;
try {
    $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
    $hasCachedUsd = $bc && $bc->rowCount() > 0;
} catch (Throwable $e) {}
if ($hasCachedUsd) {
    $s = $pdo->prepare('SELECT last_balance_usd, last_balance_usd_updated_at FROM users WHERE id = ?');
    $s->execute([(int)$userId]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $totalUsd = (float) ($r['last_balance_usd'] ?? 0);
        $totalUsdUpdatedAt = $r['last_balance_usd_updated_at'] ?? null;
    }
}

$stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
$stmt->execute([$userId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $amt = (float) $row['amount'];
    $cur = strtoupper($row['currency']);
    // No placeholder pricing. Only stable assets have a deterministic USD value.
    $usd = in_array($cur, ['USDT', 'USDC', 'BUSD', 'USD', 'DAI'], true) ? $amt : null;
    $balances[] = [
        'currency' => $row['currency'],
        'amount' => (string) $row['amount'],
        'usd_value' => $usd === null ? null : round($usd, 2),
    ];
}

$transactions = [];
$stmt = $pdo->prepare('SELECT id, type, amount, currency, status, reference, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20');
$stmt->execute([$userId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transactions[] = [
        'id' => (int) $row['id'],
        'type' => $row['type'],
        'amount' => (float) $row['amount'],
        'currency' => $row['currency'],
        'status' => $row['status'],
        'reference' => $row['reference'],
        'created_at' => $row['created_at'],
    ];
}

echo json_encode([
    'success' => true,
    'data' => [
        'balances' => $balances,
        'total_usd' => round($totalUsd, 2),
        'total_usd_updated_at' => $totalUsdUpdatedAt,
        'transactions' => $transactions,
    ],
]);

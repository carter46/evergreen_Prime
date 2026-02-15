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
$totalUsd = 0;
$stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
$stmt->execute([$userId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $amt = (float) $row['amount'];
    $usd = $amt;
    if (in_array(strtoupper($row['currency']), ['USDT', 'USDC', 'BUSD', 'USD'], true)) {
        $usd = $amt;
    } elseif (strtoupper($row['currency']) === 'BTC') {
        $usd = $amt * 65000; // placeholder rate
    } elseif (strtoupper($row['currency']) === 'ETH') {
        $usd = $amt * 3500; // placeholder rate
    }
    $balances[] = [
        'currency' => $row['currency'],
        'amount' => (string) $row['amount'],
        'usd_value' => round($usd, 2),
    ];
    $totalUsd += $usd;
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
        'transactions' => $transactions,
    ],
]);

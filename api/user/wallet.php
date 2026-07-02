<?php
/**
 * Bloombit - User Wallet API
 * GET /api/user/wallet.php - Fetch balances and transactions
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/usd-wallet.php';
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
$totalUsd = get_user_spendable_usd_balance($pdo, (int) $userId);
$totalUsdUpdatedAt = null;
try {
    $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd_updated_at'");
    if ($bc && $bc->rowCount() > 0) {
        $s = $pdo->prepare('SELECT last_balance_usd_updated_at FROM users WHERE id = ?');
        $s->execute([(int) $userId]);
        $totalUsdUpdatedAt = $s->fetchColumn() ?: null;
    }
} catch (Throwable $e) {}
if ($totalUsd > 0) {
    $balances[] = [
        'currency' => user_usd_wallet_currency(),
        'amount' => number_format($totalUsd, 18, '.', ''),
        'usd_value' => round($totalUsd, 2),
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

<?php
/**
 * Bloombit - User Wallet API
 * GET /api/user/wallet.php - Fetch balances and transactions
 */

header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Stub: Mock wallet data
$data = [
    'balances' => [
        ['currency' => 'BTC', 'amount' => '1.45028', 'usd_value' => 64231],
        ['currency' => 'ETH', 'amount' => '12.5', 'usd_value' => 3421],
        ['currency' => 'USDT', 'amount' => '5000', 'usd_value' => 5000]
    ],
    'total_usd' => 148652,
    'transactions' => []
];

echo json_encode(['success' => true, 'data' => $data]);

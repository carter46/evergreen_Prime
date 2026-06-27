<?php
/**
 * Bloombit - Public Payment Methods API (user deposit / withdraw rails)
 * GET /api/addresses.php
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/payment-methods.php';

try {
    $pdo = require __DIR__ . '/../includes/db.php';
    $methods = list_payment_methods($pdo, null, true, false);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load payment methods']);
    exit;
}

$crypto = [];
$bank = [];
$card = [];
$addressMap = [];
$bySymbol = [];

foreach ($methods as $m) {
    if ($m['method_type'] === 'crypto') {
        $crypto[] = $m;
        if (!empty($m['coin_key'])) {
            $addressMap[$m['coin_key']] = $m['wallet_address'] ?? '';
        }
        if (!empty($m['symbol'])) {
            $bySymbol[$m['symbol']] = $m;
        }
    } elseif ($m['method_type'] === 'bank') {
        $bank[] = $m;
    } else {
        $card[] = $m;
    }
}

echo json_encode([
    'success' => true,
    'methods' => $methods,
    'addresses' => $crypto,
    'crypto' => $crypto,
    'bank' => $bank,
    'card' => $card,
    'addressMap' => $addressMap,
    'bySymbol' => $bySymbol,
], JSON_UNESCAPED_UNICODE);
exit;

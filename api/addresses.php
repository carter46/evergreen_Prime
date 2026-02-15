<?php
/**
 * Bloombit - Public Deposit Addresses API
 * GET /api/addresses.php - List wallet addresses by coin (for user deposit flow)
 * Returns addresses keyed by coin_key and symbol for easy lookup.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = require __DIR__ . '/../includes/db.php';
    $stmt = $pdo->query(
    'SELECT wa.id, wa.address, wa.coin_id, c.coin_key, c.display_name, c.symbol, c.logo
     FROM wallet_addresses wa
     INNER JOIN coins c ON c.id = wa.coin_id AND c.enabled = 1
     ORDER BY c.sort_order, c.display_name'
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load addresses']);
    exit;
}

$addresses = [];
$addressMap = [];
$bySymbol = [];
foreach ($rows as $r) {
    $addr = [
        'id' => (int) $r['id'],
        'coin_id' => (int) $r['coin_id'],
        'coin_key' => $r['coin_key'],
        'display_name' => $r['display_name'],
        'symbol' => $r['symbol'],
        'logo' => $r['logo'] ?? null,
        'address' => $r['address'],
    ];
    $addresses[] = $addr;
    $addressMap[$r['coin_key']] = $r['address'];
    $bySymbol[$r['symbol']] = $addr;
}

echo json_encode([
    'success' => true,
    'addresses' => $addresses,
    'addressMap' => $addressMap,
    'bySymbol' => $bySymbol,
], JSON_UNESCAPED_UNICODE);
exit;

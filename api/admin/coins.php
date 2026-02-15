<?php
/**
 * Bloombit - Admin Coins API
 * GET /api/admin/coins.php - List all coins
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query('SELECT id, coin_key, display_name, symbol, logo, enabled, sort_order, created_at FROM coins ORDER BY sort_order, id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $coins = [];
    foreach ($rows as $r) {
        $coins[] = [
            'id' => (int) $r['id'],
            'coin_key' => $r['coin_key'],
            'display_name' => $r['display_name'],
            'symbol' => $r['symbol'],
            'logo' => $r['logo'] ?? null,
            'enabled' => (bool) $r['enabled'],
            'sort_order' => (int) $r['sort_order'],
            'created_at' => $r['created_at'],
        ];
    }
    echo json_encode(['success' => true, 'coins' => $coins]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

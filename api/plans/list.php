<?php
/**
 * Bloombit - Plans List API
 * GET /api/plans/list.php
 */

header('Content-Type: application/json');

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->query('SELECT id, name, slug, min_deposit AS min, max_deposit AS max, yield_min, yield_max, duration_days, withdrawal_days, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$plans = [];
foreach ($rows as $r) {
    $plans[] = [
        'id' => (int) $r['id'],
        'name' => $r['name'],
        'slug' => $r['slug'],
        'min' => (float) $r['min'],
        'max' => $r['max'] !== null ? (float) $r['max'] : null,
        'yield_min' => (float) $r['yield_min'],
        'yield_max' => (float) $r['yield_max'],
        'duration_days' => (int) $r['duration_days'],
        'withdrawal_days' => (int) $r['withdrawal_days'],
        'features' => $r['features_json'] ? json_decode($r['features_json'], true) : [],
    ];
}

echo json_encode(['success' => true, 'data' => $plans]);

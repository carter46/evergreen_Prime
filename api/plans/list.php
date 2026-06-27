<?php
/**
 * Bloombit - Plans List API
 * GET /api/plans/list.php
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/plan-types.php';

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    ensure_plan_schema($pdo);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$stmt = $pdo->query('SELECT id, name, slug, plan_type, description, logo_url, investment_risk, min_deposit AS min, max_deposit AS max, yield_min, yield_max, duration_days, min_duration_days, max_duration_days, withdrawal_days, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$plans = [];
foreach ($rows as $r) {
    $typeKey = normalize_plan_type($r['plan_type'] ?? 'crypto');
    $plans[] = [
        'id' => (int) $r['id'],
        'name' => $r['name'],
        'slug' => $r['slug'],
        'plan_type' => $typeKey,
        'plan_type_label' => plan_type_label($typeKey),
        'description' => $r['description'] ?? null,
        'logo_url' => $r['logo_url'] ?? null,
        'investment_risk' => normalize_investment_risk($r['investment_risk'] ?? 'mid'),
        'min' => (float) $r['min'],
        'max' => $r['max'] !== null ? (float) $r['max'] : null,
        'yield_min' => (float) $r['yield_min'],
        'yield_max' => (float) $r['yield_max'],
        'duration_days' => (int) $r['duration_days'],
        'min_duration_days' => isset($r['min_duration_days']) && $r['min_duration_days'] !== null ? (int) $r['min_duration_days'] : (int) $r['duration_days'],
        'max_duration_days' => isset($r['min_duration_days']) && $r['min_duration_days'] !== null ? (int) $r['min_duration_days'] : (int) $r['duration_days'],
        'expected_return' => format_plan_period_return((float) $r['yield_min'], isset($r['min_duration_days']) && $r['min_duration_days'] !== null ? (int) $r['min_duration_days'] : (int) $r['duration_days']),
        'withdrawal_days' => (int) $r['withdrawal_days'],
        'features' => $r['features_json'] ? json_decode($r['features_json'], true) : [],
    ];
}

echo json_encode(['success' => true, 'data' => $plans]);

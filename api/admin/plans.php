<?php
/**
 * Bloombit - Admin Plan Management API
 * GET /api/admin/plans.php - List all plans
 * POST /api/admin/plans.php - Create/update plan
 */

header('Content-Type: application/json');

session_start();
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
    $stmt = $pdo->query('SELECT * FROM plans ORDER BY sort_order, id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $plans = [];
    foreach ($rows as $r) {
        $plans[] = [
            'id' => (int) $r['id'],
            'name' => $r['name'],
            'slug' => $r['slug'],
            'min_deposit' => (float) $r['min_deposit'],
            'max_deposit' => $r['max_deposit'] !== null ? (float) $r['max_deposit'] : null,
            'yield_min' => (float) $r['yield_min'],
            'yield_max' => (float) $r['yield_max'],
            'duration_days' => (int) $r['duration_days'],
            'withdrawal_days' => (int) $r['withdrawal_days'],
            'features_json' => $r['features_json'],
            'features' => $r['features_json'] ? json_decode($r['features_json'], true) : [],
            'enabled' => (bool) $r['enabled'],
            'sort_order' => (int) $r['sort_order'],
        ];
    }
    echo json_encode(['success' => true, 'data' => $plans]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    $name = trim($input['name'] ?? '');
    $slug = trim($input['slug'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $minDeposit = (float) ($input['min_deposit'] ?? 0);
    $maxDeposit = isset($input['max_deposit']) && $input['max_deposit'] !== '' ? (float) $input['max_deposit'] : null;
    $yieldMin = (float) ($input['yield_min'] ?? 0);
    $yieldMax = (float) ($input['yield_max'] ?? 0);
    $durationDays = (int) ($input['duration_days'] ?? 30);
    $withdrawalDays = (int) ($input['withdrawal_days'] ?? 7);
    $features = $input['features'] ?? [];
    $featuresJson = is_array($features) ? json_encode($features) : (is_string($features) ? $features : '[]');
    $enabled = isset($input['enabled']) ? (bool) $input['enabled'] : true;
    $sortOrder = (int) ($input['sort_order'] ?? 0);

    if ($id > 0 && $name === '' && array_key_exists('enabled', $input)) {
        $stmt = $pdo->prepare('UPDATE plans SET enabled=? WHERE id=?');
        $stmt->execute([$enabled ? 1 : 0, $id]);
        echo json_encode(['success' => true, 'data' => ['message' => 'Plan updated']]);
        exit;
    }

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Plan name is required']);
        exit;
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE plans SET name=?, slug=?, min_deposit=?, max_deposit=?, yield_min=?, yield_max=?, duration_days=?, withdrawal_days=?, features_json=?, enabled=?, sort_order=? WHERE id=?');
        $stmt->execute([$name, $slug, $minDeposit, $maxDeposit, $yieldMin, $yieldMax, $durationDays, $withdrawalDays, $featuresJson, $enabled ? 1 : 0, $sortOrder, $id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO plans (name, slug, min_deposit, max_deposit, yield_min, yield_max, duration_days, withdrawal_days, features_json, enabled, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $slug, $minDeposit, $maxDeposit, $yieldMin, $yieldMax, $durationDays, $withdrawalDays, $featuresJson, $enabled ? 1 : 0, $sortOrder]);
    }
    echo json_encode(['success' => true, 'data' => ['message' => 'Plan saved successfully']]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

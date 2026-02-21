<?php
/**
 * Bloombit - Admin Plan Management API
 * GET /api/admin/plans.php - List all plans
 * POST /api/admin/plans.php - Create/update plan
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
    $stmt = $pdo->query('SELECT * FROM plans ORDER BY sort_order, id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $plans = [];
    foreach ($rows as $r) {
        $plans[] = [
            'id' => (int) $r['id'],
            'name' => $r['name'],
            'slug' => $r['slug'],
            'description' => $r['description'] ?? null,
            'icon' => $r['icon'] ?? null,
            'min_deposit' => (float) $r['min_deposit'],
            'max_deposit' => $r['max_deposit'] !== null ? (float) $r['max_deposit'] : null,
            'yield_min' => (float) $r['yield_min'],
            'yield_max' => (float) $r['yield_max'],
            'duration_days' => (int) $r['duration_days'],
            'withdrawal_days' => (int) $r['withdrawal_days'],
            'min_duration_months' => isset($r['min_duration_months']) && $r['min_duration_months'] !== null ? (int) $r['min_duration_months'] : null,
            'max_duration_months' => isset($r['max_duration_months']) && $r['max_duration_months'] !== null ? (int) $r['max_duration_months'] : null,
            'min_duration_days' => isset($r['min_duration_days']) && $r['min_duration_days'] !== null ? (int) $r['min_duration_days'] : (isset($r['min_duration_months']) && $r['min_duration_months'] !== null ? (int) $r['min_duration_months'] * 30 : null),
            'max_duration_days' => isset($r['max_duration_days']) && $r['max_duration_days'] !== null ? (int) $r['max_duration_days'] : (isset($r['max_duration_months']) && $r['max_duration_months'] !== null ? (int) $r['max_duration_months'] * 30 : null),
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
    $action = trim((string)($input['action'] ?? ''));
    $id = isset($input['id']) ? (int) $input['id'] : 0;

    // Delete plan (safe: only if there are no investments at all for the plan)
    if ($action === 'delete') {
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid plan id']);
            exit;
        }
        try {
            $cnt = $pdo->prepare('SELECT COUNT(*) FROM user_investments WHERE plan_id = ?');
            $cnt->execute([$id]);
            $total = (int) $cnt->fetchColumn();
            if ($total > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Cannot delete: this plan has ' . $total . ' investment record(s). Disable it instead to preserve history.']);
                exit;
            }
            $del = $pdo->prepare('DELETE FROM plans WHERE id = ?');
            $del->execute([$id]);
            echo json_encode(['success' => true, 'data' => ['message' => 'Plan deleted']]);
            exit;
        } catch (Throwable $e) {
            $config = include dirname(__DIR__, 2) . '/config.php';
            $msg = ($config['site']['debug'] ?? false)
                ? ('Unable to delete plan: ' . $e->getMessage())
                : 'Unable to delete plan.';
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $msg]);
            exit;
        }
    }
    $name = trim($input['name'] ?? '');
    $slug = trim($input['slug'] ?? '') ?: strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    if ($slug === '') $slug = 'plan-' . time();
    $minDeposit = (float) ($input['min_deposit'] ?? 0);
    $maxDeposit = isset($input['max_deposit']) && $input['max_deposit'] !== '' ? (float) $input['max_deposit'] : null;
    $yield = (float) ($input['yield'] ?? $input['yield_min'] ?? 0);
    $yieldMin = $yield;
    $yieldMax = $yield;
    $withdrawalDays = (int) ($input['withdrawal_days'] ?? 7);

    // Features can come from JS as `features` (array) or from a textarea as `features_text` (string).
    // Only update features_json when the request explicitly provides features, to avoid accidental wiping.
    $featuresProvided = array_key_exists('features', $input) || array_key_exists('features_text', $input);
    $features = $input['features'] ?? ($input['features_text'] ?? []);
    if (is_string($features)) {
        $features = array_values(array_filter(array_map('trim', explode("\n", $features))));
    }
    if (is_array($features)) {
        $cleanFeatures = [];
        foreach ($features as $feature) {
            if (!is_scalar($feature)) continue;
            $line = trim((string) $feature);
            if ($line === '') continue;
            $cleanFeatures[] = $line;
        }
        $features = array_values(array_unique($cleanFeatures));
    } else {
        $features = [];
    }
    $featuresJson = null;
    if ($featuresProvided || $id <= 0) {
        // Encode features robustly (avoid silently wiping features on invalid UTF-8 copy/paste)
        $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }
        $featuresJson = json_encode($features, $jsonFlags);
        if ($featuresJson === false) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Could not save features (unsupported characters). Please retype the features or remove special symbols and try again.'
            ]);
            exit;
        }
    }

    // Prevent silent truncation on older DB schemas (e.g., features_json as VARCHAR/TEXT).
    if ($featuresJson !== null) {
        try {
            $lenStmt = $pdo->query("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'features_json' LIMIT 1");
            $maxLen = $lenStmt ? $lenStmt->fetchColumn() : null;
            $maxLen = is_numeric($maxLen) ? (int) $maxLen : null;
            if ($maxLen !== null && $maxLen > 0 && strlen($featuresJson) > $maxLen) {
                // Try to auto-upgrade the column in-place (admin endpoint).
                try {
                    $pdo->exec("ALTER TABLE plans MODIFY COLUMN features_json LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL");
                    $lenStmt2 = $pdo->query("SELECT CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'features_json' LIMIT 1");
                    $maxLen2 = $lenStmt2 ? $lenStmt2->fetchColumn() : null;
                    $maxLen2 = is_numeric($maxLen2) ? (int) $maxLen2 : null;
                    if ($maxLen2 !== null && $maxLen2 > 0 && strlen($featuresJson) > $maxLen2) {
                        throw new RuntimeException('Column still too small after upgrade');
                    }
                } catch (Throwable $e2) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Database column for plan features is too small. Please run schema/migration.sql to upgrade plans.features_json, then try again.'
                    ]);
                    exit;
                }
            }
        } catch (Throwable $e) {
            // Ignore if INFORMATION_SCHEMA is restricted; DB will enforce its own limits.
        }
    }
    $description = trim($input['description'] ?? '') ?: null;
    $allowedIcons = ['trending_up', 'rocket_launch', 'diamond', 'currency_bitcoin', 'token'];
    $icon = trim($input['icon'] ?? '') ?: null;
    if ($icon && !in_array($icon, $allowedIcons, true)) $icon = 'trending_up';
    $minDurationDays = isset($input['min_duration_days']) && $input['min_duration_days'] !== '' && $input['min_duration_days'] !== null ? (int) $input['min_duration_days'] : null;
    $maxDurationDays = isset($input['max_duration_days']) && $input['max_duration_days'] !== '' && $input['max_duration_days'] !== null ? (int) $input['max_duration_days'] : null;
    $enabled = isset($input['enabled']) ? (bool) $input['enabled'] : true;
    $sortOrder = (int) ($input['sort_order'] ?? 0);

    if ($id > 0 && $name === '' && array_key_exists('enabled', $input)) {
        if (!$enabled) {
            $cnt = $pdo->prepare('SELECT COUNT(*) FROM user_investments WHERE plan_id=? AND status=?');
            $cnt->execute([$id, 'active']);
            $activeCount = (int) $cnt->fetchColumn();
            if ($activeCount > 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Cannot disable: plan has ' . $activeCount . ' active user(s)']);
                exit;
            }
        }
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

    if ($minDurationDays === null || $maxDurationDays === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Min. and Max. Duration (Days) are required']);
        exit;
    }
    if ($minDurationDays > $maxDurationDays) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Min. duration cannot exceed max. duration']);
        exit;
    }
    if ($minDurationDays < 1 || $maxDurationDays < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Min. and Max. duration must be at least 1 day']);
        exit;
    }

    $durationDays = max(1, (int) round(($minDurationDays + $maxDurationDays) / 2));

    try {
        $slugCheckSql = 'SELECT id FROM plans WHERE slug = ?';
        $slugParams = [$slug];
        if ($id > 0) {
            $slugCheckSql .= ' AND id <> ?';
            $slugParams[] = $id;
        }
        $slugCheckSql .= ' LIMIT 1';
        $slugStmt = $pdo->prepare($slugCheckSql);
        $slugStmt->execute($slugParams);
        if ($slugStmt->fetch(PDO::FETCH_ASSOC)) {
            $slug .= '-' . substr((string) time(), -4);
        }

        if ($id > 0) {
            if ($featuresJson !== null) {
                $stmt = $pdo->prepare('UPDATE plans SET name=?, slug=?, description=?, icon=?, min_deposit=?, max_deposit=?, yield_min=?, yield_max=?, duration_days=?, withdrawal_days=?, min_duration_days=?, max_duration_days=?, features_json=?, enabled=?, sort_order=? WHERE id=?');
                $stmt->execute([$name, $slug, $description, $icon, $minDeposit, $maxDeposit, $yieldMin, $yieldMax, $durationDays, $withdrawalDays, $minDurationDays, $maxDurationDays, $featuresJson, $enabled ? 1 : 0, $sortOrder, $id]);
            } else {
                // Preserve existing features_json
                $stmt = $pdo->prepare('UPDATE plans SET name=?, slug=?, description=?, icon=?, min_deposit=?, max_deposit=?, yield_min=?, yield_max=?, duration_days=?, withdrawal_days=?, min_duration_days=?, max_duration_days=?, enabled=?, sort_order=? WHERE id=?');
                $stmt->execute([$name, $slug, $description, $icon, $minDeposit, $maxDeposit, $yieldMin, $yieldMax, $durationDays, $withdrawalDays, $minDurationDays, $maxDurationDays, $enabled ? 1 : 0, $sortOrder, $id]);
            }
        } else {
            if ($featuresJson === null) $featuresJson = '[]';
            $stmt = $pdo->prepare('INSERT INTO plans (name, slug, description, icon, min_deposit, max_deposit, yield_min, yield_max, duration_days, withdrawal_days, min_duration_days, max_duration_days, features_json, enabled, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $slug, $description, $icon, $minDeposit, $maxDeposit, $yieldMin, $yieldMax, $durationDays, $withdrawalDays, $minDurationDays, $maxDurationDays, $featuresJson, $enabled ? 1 : 0, $sortOrder]);
        }
        echo json_encode(['success' => true, 'data' => ['message' => 'Plan saved successfully']]);
        exit;
    } catch (Throwable $e) {
        $config = include dirname(__DIR__, 2) . '/config.php';
        $msg = ($config['site']['debug'] ?? false)
            ? ('Unable to save plan: ' . $e->getMessage())
            : 'Unable to save plan. Please verify all fields and try again.';
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $msg]);
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

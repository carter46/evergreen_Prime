<?php
/**
 * Bloombit - AI Bot Config API
 * GET - Fetch all AI bot settings
 * POST - Update settings or trigger manual distribution
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';

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

$aiKeys = [
    'min_withdrawal_limit' => '10',
    'max_withdrawal_limit' => '50000',
    'earnings_paused' => '0',
    'distribution_interval' => 'daily',
    'distribution_start_time' => '09:00:00',
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $placeholders = implode(',', array_fill(0, count($aiKeys), '?'));
    $stmt = $pdo->prepare("SELECT `key`, value FROM site_settings WHERE `key` IN ($placeholders)");
    $stmt->execute(array_keys($aiKeys));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = $aiKeys;
    foreach ($rows as $r) {
        if (isset($aiKeys[$r['key']])) {
            $data[$r['key']] = $r['value'] ?? $aiKeys[$r['key']];
        }
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if (isset($input['action']) && $input['action'] === 'manual_distribute') {
        require_once dirname(__DIR__, 2) . '/includes/earnings-engine.php';
        $result = run_earnings_distribution($pdo, true);
        echo json_encode([
            'success' => true,
            'data' => [
                'message' => 'Manual distribution completed. Credits: ' . $result['credits'] . ', Total: $' . number_format($result['total_amount'], 2) . ' USDT',
                'credits' => $result['credits'],
                'total_amount' => $result['total_amount'],
                'errors' => $result['errors'],
            ],
        ]);
        exit;
    }

    $updates = [];
    foreach (['min_withdrawal_limit', 'max_withdrawal_limit', 'earnings_paused', 'distribution_interval', 'distribution_start_time'] as $k) {
        if (!array_key_exists($k, $input)) continue;
        $v = trim((string) $input[$k]);
        if ($k === 'earnings_paused') {
            $v = in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
        }
        if ($k === 'distribution_interval') {
            $allowed = ['5min', '12h', 'daily', 'weekly', 'monthly'];
            if (!in_array($v, $allowed, true)) $v = 'daily';
        }
        if ($k === 'distribution_start_time') {
            // Accept HH:MM or HH:MM:SS, store as HH:MM:SS
            if (preg_match('/^([01]\\d|2[0-3]):([0-5]\\d)$/', $v, $m)) {
                $v = $m[1] . ':' . $m[2] . ':00';
            } elseif (!preg_match('/^([01]\\d|2[0-3]):([0-5]\\d):([0-5]\\d)$/', $v)) {
                $v = '09:00:00';
            }
        }
        if ($k === 'min_withdrawal_limit' || $k === 'max_withdrawal_limit') {
            $num = (float) $v;
            if ($num < 0) $num = 0;
            $v = (string) $num;
        }
        $updates[$k] = $v;
    }

    // Enforce max >= min when both provided
    if (isset($updates['min_withdrawal_limit'], $updates['max_withdrawal_limit'])) {
        $min = (float) $updates['min_withdrawal_limit'];
        $max = (float) $updates['max_withdrawal_limit'];
        if ($max > 0 && $max < $min) {
            $updates['max_withdrawal_limit'] = (string) $min;
        }
    }

    $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    foreach ($updates as $k => $v) {
        $stmt->execute([$k, $v]);
    }
    echo json_encode(['success' => true, 'data' => ['message' => 'Settings updated']]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

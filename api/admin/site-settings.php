<?php
/**
 * Bloombit - Admin Site Settings API
 * GET /api/admin/site-settings.php - Get global parameters
 * POST /api/admin/site-settings.php - Update global parameters
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$allowedKeys = ['max_active_plans_per_user', 'compounding_enabled', 'site_name', 'site_logo', 'site_favicon'];

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $placeholders = implode(',', array_fill(0, count($allowedKeys), '?'));
    $stmt = $pdo->prepare("SELECT `key`, value FROM site_settings WHERE `key` IN ($placeholders)");
    $stmt->execute($allowedKeys);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $data = [
        'max_active_plans_per_user' => '3',
        'compounding_enabled' => '0',
        'site_name' => '',
        'site_logo' => '',
        'site_favicon' => '',
    ];
    foreach ($rows as $r) {
        if (in_array($r['key'], $allowedKeys, true)) {
            $data[$r['key']] = $r['value'] ?? '';
        }
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $updates = [];
    foreach ($allowedKeys as $k) {
        if (!array_key_exists($k, $input)) continue;
        $v = trim((string) $input[$k]);
        if ($k === 'compounding_enabled') {
            $v = in_array(strtolower($v), ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
        }
        $updates[$k] = $v;
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

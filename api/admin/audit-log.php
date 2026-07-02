<?php
/**
 * Admin audit log API
 * GET /api/admin/audit-log.php?page=1&per_page=30&entity_type=&action=&search=
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/admin-audit-log.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = (int) ($_GET['per_page'] ?? 30);
    $entityType = trim((string) ($_GET['entity_type'] ?? ''));
    $action = trim((string) ($_GET['action'] ?? ''));
    $search = trim((string) ($_GET['search'] ?? ''));
    $result = list_admin_audit_logs(
        $pdo,
        $page,
        $perPage,
        $entityType !== '' ? $entityType : null,
        $action !== '' ? $action : null,
        $search !== '' ? $search : null
    );
    echo json_encode([
        'success' => true,
        'data' => $result['data'],
        'pagination' => $result['pagination'],
        'entity_labels' => admin_audit_entity_labels(),
        'action_labels' => admin_audit_action_labels(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load audit log']);
}

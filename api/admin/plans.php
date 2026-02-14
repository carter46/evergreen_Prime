<?php
/**
 * Bloombit - Admin Plan Management API
 * GET /api/admin/plans.php - List plans
 * POST /api/admin/plans.php - Create/update plan
 */

header('Content-Type: application/json');

session_start();
// Stub: In production, check admin role
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $plans = [
        ['id' => 1, 'name' => 'Starter', 'min' => 100, 'max' => 2500, 'yield_min' => 0.5, 'yield_max' => 1.2],
        ['id' => 2, 'name' => 'Pro', 'min' => 2500, 'max' => 25000, 'yield_min' => 1.0, 'yield_max' => 2.5],
        ['id' => 3, 'name' => 'Institutional', 'min' => 25000, 'max' => null, 'yield_min' => 1.5, 'yield_max' => 3.0]
    ];
    echo json_encode(['success' => true, 'data' => $plans]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    echo json_encode(['success' => true, 'data' => ['message' => 'Plan saved successfully']]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

<?php
/**
 * POST /api/user/liquidate-plan.php — early liquidation of an active investment.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/investment-lifecycle.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$invId = isset($input['investment_id']) ? (int) $input['investment_id'] : 0;
if ($invId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid investment']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$result = liquidate_user_investment($pdo, $userId, $invId);

if (!empty($result['success'])) {
    echo json_encode(['success' => true, 'data' => $result]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Failed', 'data' => $result]);
exit;

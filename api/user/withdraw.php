<?php
/**
 * Bloombit - Withdraw API
 * POST /api/user/withdraw.php
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$currency = $input['currency'] ?? '';
$amount = $input['amount'] ?? '';
$address = $input['address'] ?? '';

if (empty($currency) || empty($amount) || empty($address)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency, amount, and address are required']);
    exit;
}

// Stub: In production, validate and process withdrawal
echo json_encode([
    'success' => true,
    'data' => ['message' => 'Withdrawal request submitted']
]);

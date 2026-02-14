<?php
/**
 * Bloombit - KYC API
 * GET /api/user/kyc.php - Fetch KYC status
 * POST /api/user/kyc.php - Submit KYC documents
 */

header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Stub: Mock KYC data
$kyc = [
    'status' => 'verified',
    'submitted_at' => '2023-10-24T14:22:00Z'
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'data' => $kyc]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => true, 'data' => ['message' => 'KYC documents submitted for review']]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

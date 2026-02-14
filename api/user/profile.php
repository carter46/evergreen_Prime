<?php
/**
 * Bloombit - User Profile API
 * GET /api/user/profile.php - Fetch profile
 * PUT /api/user/profile.php - Update profile
 */

header('Content-Type: application/json');

session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Stub: Mock profile data
$profile = [
    'name' => 'John Doe',
    'email' => $_SESSION['email'] ?? 'john.doe@bloombit.io',
    'user_id' => 'BB-9823412',
    'verified' => true,
    'avatar' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $profile['name'] = $input['name'] ?? $profile['name'];
    echo json_encode(['success' => true, 'data' => $profile]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

<?php
/**
 * Bloombit - Admin Email Broadcast API
 * POST /api/admin/broadcast.php
 * Sends email broadcast to users.
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$subject = trim($input['subject'] ?? '');
$body = trim($input['body'] ?? '');
$recipients = $input['recipients'] ?? 'all';

if (empty($subject) || empty($body)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Subject and body are required']);
    exit;
}

// Stub: In production, use PHPMailer to send to user list
echo json_encode([
    'success' => true,
    'data' => ['message' => 'Broadcast queued for delivery']
]);

<?php
/**
 * Bloombit - Deposit Done API
 * POST /api/user/deposit-done.php - Mark user-confirmed deposit (clicked "Done")
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (!isset($_SESSION['user_id'])) {
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

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$txId = (int)($input['transaction_id'] ?? 0);
if ($txId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid transaction ID']);
    exit;
}

// Ensure column exists (safe on older DB)
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'user_confirmed_at'");
    if (!$chk || $chk->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Deposit confirmation not available yet. Please update your database (migration.sql).']);
        exit;
    }
} catch (Throwable $e) {}

$userId = (int)$_SESSION['user_id'];

// Only allow confirming own pending deposit
$stmt = $pdo->prepare("UPDATE transactions
                       SET user_confirmed_at = NOW()
                       WHERE id = ?
                         AND user_id = ?
                         AND type = 'deposit'
                         AND status = 'pending'
                         AND user_confirmed_at IS NULL");
$stmt->execute([$txId, $userId]);

if ($stmt->rowCount() !== 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unable to mark this deposit as done. It may already be confirmed or no longer pending.']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['message' => 'Deposit marked as done. Awaiting admin approval.']]);
exit;


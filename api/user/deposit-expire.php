<?php
/**
 * Bloombit - Deposit Expire API
 * POST /api/user/deposit-expire.php - Force-expire a specific pending deposit when countdown hits 0
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/deposit-expiry.php';
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

// Fetch transaction (must be user's) — check optional columns first
$cols = "id, user_id, type, amount, currency, status, reference";
$hasExpiresAt = false;
$hasConfirmedAt = false;
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'expires_at'");
    if ($chk && $chk->rowCount() > 0) { $cols .= ", expires_at"; $hasExpiresAt = true; }
    $chk2 = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'user_confirmed_at'");
    if ($chk2 && $chk2->rowCount() > 0) { $cols .= ", user_confirmed_at"; $hasConfirmedAt = true; }
    $chk3 = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
    if ($chk3 && $chk3->rowCount() > 0) $cols .= ", amount_usd";
} catch (Throwable $e) {}

if (!$hasExpiresAt || !$hasConfirmedAt) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Deposit countdown not available. Please run the database migration.']);
    exit;
}

$stmt = $pdo->prepare("SELECT $cols FROM transactions WHERE id = ? AND user_id = ?");
$stmt->execute([$txId, (int)$_SESSION['user_id']]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$tx || ($tx['type'] ?? '') !== 'deposit') {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Deposit not found']);
    exit;
}

// If already not pending, nothing to do
if (($tx['status'] ?? '') !== 'pending') {
    echo json_encode(['success' => true, 'data' => ['status' => $tx['status'], 'message' => 'Deposit already processed']]);
    exit;
}

// Expire if eligible
$upd = $pdo->prepare("UPDATE transactions
                      SET status = 'failed'
                      WHERE id = ?
                        AND user_id = ?
                        AND type = 'deposit'
                        AND status = 'pending'
                        AND expires_at IS NOT NULL
                        AND expires_at <= NOW()
                        AND (user_confirmed_at IS NULL)");
$upd->execute([$txId, (int)$_SESSION['user_id']]);

if ($upd->rowCount() === 1) {
    send_deposit_failed_email($pdo, $tx);
    echo json_encode(['success' => true, 'data' => ['status' => 'failed', 'message' => 'Deposit marked as failed (expired)']]);
    exit;
}

echo json_encode(['success' => true, 'data' => ['status' => 'pending', 'message' => 'Deposit has not expired yet']]);
exit;


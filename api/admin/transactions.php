<?php
/**
 * Bloombit - Admin Transactions API
 * POST /api/admin/transactions.php - Approve or reject transactions (deposits/withdrawals)
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
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
$action = strtolower(trim($input['action'] ?? ''));
$transactionId = (int) ($input['transaction_id'] ?? 0);

if (!in_array($action, ['approve', 'reject'], true) || $transactionId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action or transaction ID']);
    exit;
}

// Fetch transaction
$stmt = $pdo->prepare('SELECT id, user_id, type, amount, currency, status FROM transactions WHERE id = ?');
$stmt->execute([$transactionId]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tx) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Transaction not found']);
    exit;
}

if ($tx['status'] !== 'pending') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Transaction is not pending']);
    exit;
}

$pdo->beginTransaction();
try {
    if ($action === 'approve') {
        // Update transaction status
        $pdo->prepare('UPDATE transactions SET status = ? WHERE id = ?')->execute(['completed', $transactionId]);
        
        // For deposits, credit user wallet
        if ($tx['type'] === 'deposit') {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$tx['user_id'], $tx['currency'], $tx['amount']]);
        }
        // For withdrawals, status update is sufficient (balance already debited on request)
        
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Transaction approved successfully'],
        ]);
    } else { // reject
        // Update transaction status
        $pdo->prepare('UPDATE transactions SET status = ? WHERE id = ?')->execute(['rejected', $transactionId]);
        
        // For withdrawals, credit back the user balance
        if ($tx['type'] === 'withdrawal') {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$tx['user_id'], $tx['currency'], $tx['amount']]);
        }
        // For deposits, no balance change needed (user hasn't been credited yet)
        
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Transaction rejected'],
        ]);
    }
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process transaction']);
}

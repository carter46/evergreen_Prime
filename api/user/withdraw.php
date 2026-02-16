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

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$currency = strtoupper(trim($input['currency'] ?? ''));
$amount = (float) ($input['amount'] ?? 0);
$address = trim($input['address'] ?? '');

if (empty($currency) || $amount <= 0 || empty($address)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency, amount, and address are required']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

// Validate currency exists in wallet_addresses (admin-managed coins with addresses)
$stmt = $pdo->prepare('SELECT 1 FROM wallet_addresses wa INNER JOIN coins c ON c.id = wa.coin_id AND c.enabled = 1 WHERE UPPER(c.symbol) = ? LIMIT 1');
$stmt->execute([$currency]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or unsupported currency']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    // Check balance WITHIN transaction to prevent race conditions
    $stmt = $pdo->prepare('SELECT amount FROM wallet_balances WHERE user_id = ? AND currency = ? FOR UPDATE');
    $stmt->execute([$userId, $currency]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $row ? (float) $row['amount'] : 0;
    
    if ($balance < $amount) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Insufficient balance']);
        exit;
    }
    
    // Create transaction record
    $stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userId, 'withdrawal', $amount, $currency, 'pending', $address]);
    $txId = (int) $pdo->lastInsertId();

    // Debit user balance immediately (admin will credit back on reject)
    // Update existing balance or create new row with negative amount
    $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, -?) ON DUPLICATE KEY UPDATE amount = amount - ?')
        ->execute([$userId, $currency, $amount, $amount]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to process withdrawal']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => [
        'transaction_id' => $txId,
        'message' => 'Withdrawal request submitted. Awaiting admin approval.',
    ],
]);

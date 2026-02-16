<?php
/**
 * Bloombit - Deposit API
 * POST /api/user/deposit.php - Create pending deposit transaction
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
$reference = trim($input['reference'] ?? '');

if (empty($currency) || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Currency and a valid amount are required']);
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

$stmt = $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$userId, 'deposit', $amount, $currency, 'pending', $reference ?: null]);

$txId = (int) $pdo->lastInsertId();

echo json_encode([
    'success' => true,
    'data' => [
        'transaction_id' => $txId,
        'message' => 'Deposit request submitted. Awaiting admin approval.',
    ],
]);

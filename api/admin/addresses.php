<?php
/**
 * Bloombit - Admin Payment Methods API
 * GET    /api/admin/addresses.php - List payment methods
 * POST   /api/admin/addresses.php - Create payment method
 * PUT    /api/admin/addresses.php?id=X - Update payment method
 * DELETE /api/admin/addresses.php?id=X - Delete payment method
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/payment-methods.php';

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    ensure_payment_methods_schema($pdo);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

function sanitizeText(?string $s, int $max = 255): string
{
    $s = trim(preg_replace('/\s+/', ' ', (string) $s));
    if (strlen($s) > $max) {
        $s = substr($s, 0, $max);
    }
    return $s;
}

function parsePaymentMethodInput(array $input, bool $isUpdate = false): array
{
    $type = strtolower(trim($input['method_type'] ?? ''));
    if (!$isUpdate && !in_array($type, payment_method_types(), true)) {
        return ['error' => 'method_type must be crypto, bank, or card'];
    }

    $data = [
        'method_type' => $type ?: null,
        'label' => sanitizeText($input['label'] ?? '', 120) ?: null,
        'enabled' => array_key_exists('enabled', $input) ? ((int) (bool) $input['enabled']) : 1,
    ];

    if ($type === 'crypto' || ($isUpdate && isset($input['coin_id']))) {
        $coinId = isset($input['coin_id']) ? (int) $input['coin_id'] : 0;
        $address = sanitizeText($input['wallet_address'] ?? $input['address'] ?? '', 255);
        if (!$isUpdate && ($coinId <= 0 || $address === '')) {
            return ['error' => 'Coin and wallet address are required for crypto'];
        }
        if ($coinId > 0) {
            $data['coin_id'] = $coinId;
        }
        if ($address !== '') {
            $data['wallet_address'] = $address;
        }
    }

    if ($type === 'bank' || $isUpdate) {
        $bankName = sanitizeText($input['bank_name'] ?? '', 120);
        $accountName = sanitizeText($input['account_name'] ?? '', 120);
        $accountNumber = sanitizeText($input['account_number'] ?? '', 80);
        if ($type === 'bank' && ($bankName === '' || $accountName === '' || $accountNumber === '')) {
            return ['error' => 'Bank name, account name, and account number are required'];
        }
        foreach ([
            'bank_name' => $bankName,
            'account_name' => $accountName,
            'account_number' => $accountNumber,
            'routing_number' => sanitizeText($input['routing_number'] ?? '', 80) ?: null,
            'swift_code' => sanitizeText($input['swift_code'] ?? '', 50) ?: null,
            'iban' => sanitizeText($input['iban'] ?? '', 80) ?: null,
            'bank_branch' => sanitizeText($input['bank_branch'] ?? '', 120) ?: null,
        ] as $k => $v) {
            if ($v !== null && $v !== '') {
                $data[$k] = $v;
            } elseif ($type === 'bank') {
                $data[$k] = $v;
            }
        }
        if (array_key_exists('bank_address', $input)) {
            $data['bank_address'] = trim((string) $input['bank_address']) ?: null;
        }
        if (array_key_exists('bank_notes', $input)) {
            $data['bank_notes'] = trim((string) $input['bank_notes']) ?: null;
        }
    }

    if ($type === 'card' || $isUpdate) {
        $brand = strtolower(trim($input['card_brand'] ?? ''));
        if ($type === 'card' && !in_array($brand, payment_method_card_brands(), true)) {
            return ['error' => 'Card brand must be visa, mastercard, or amex'];
        }
        if ($brand !== '') {
            $data['card_brand'] = $brand;
        }
        foreach ([
            'card_holder_name' => sanitizeText($input['card_holder_name'] ?? '', 120),
            'card_number' => preg_replace('/\D+/', '', (string) ($input['card_number'] ?? '')),
            'card_expiry' => sanitizeText($input['card_expiry'] ?? '', 10),
            'card_cvc' => sanitizeText($input['card_cvc'] ?? '', 10),
        ] as $k => $v) {
            if ($v !== '') {
                $data[$k] = $v;
            }
        }
        if ($type === 'card' && empty($data['card_number'])) {
            return ['error' => 'Card number is required'];
        }
    }

    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $methods = list_payment_methods($pdo, null, false, true);
    echo json_encode(['success' => true, 'methods' => $methods, 'addresses' => $methods]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $parsed = parsePaymentMethodInput($input, false);
    if (isset($parsed['error'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $parsed['error']]);
        exit;
    }

    if ($parsed['method_type'] === 'crypto' && !empty($parsed['coin_id'])) {
        $check = $pdo->prepare('SELECT id FROM coins WHERE id = ?');
        $check->execute([(int) $parsed['coin_id']]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Coin not found']);
            exit;
        }
    }

    $cols = array_keys($parsed);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $sql = 'INSERT INTO payment_methods (' . implode(', ', $cols) . ') VALUES (' . $placeholders . ')';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($parsed));
        echo json_encode(['success' => true, 'id' => (int) $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'A crypto method already exists for this coin']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unable to create payment method']);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($input['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Method ID is required']);
        exit;
    }

    $existing = get_payment_method_by_id($pdo, $id, true);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Payment method not found']);
        exit;
    }

    $input['method_type'] = $input['method_type'] ?? $existing['method_type'];
    $parsed = parsePaymentMethodInput($input, true);
    if (isset($parsed['error'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $parsed['error']]);
        exit;
    }
    unset($parsed['method_type']);

    if (empty($parsed)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No data to update']);
        exit;
    }

    $sets = [];
    $params = [];
    foreach ($parsed as $col => $val) {
        $sets[] = $col . ' = ?';
        $params[] = $val;
    }
    $params[] = $id;

    try {
        $stmt = $pdo->prepare('UPDATE payment_methods SET ' . implode(', ', $sets) . ' WHERE id = ?');
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Payment method updated']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'A crypto method already exists for this coin']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unable to update payment method']);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Method ID is required']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM payment_methods WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Payment method not found']);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Payment method deleted']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

<?php
/**
 * Bloombit - Admin Wallet Addresses API
 * GET /api/admin/addresses.php - List all wallet addresses
 * POST /api/admin/addresses.php - Create address
 * PUT /api/admin/addresses.php?id=X - Update address
 * DELETE /api/admin/addresses.php?id=X - Delete address
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
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

function sanitizeAddress(string $s): string {
    return trim(preg_replace('/\s+/', ' ', $s));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query(
        'SELECT wa.id, wa.address, wa.coin_id, wa.created_at, c.coin_key, c.display_name, c.symbol, c.logo
         FROM wallet_addresses wa
         INNER JOIN coins c ON c.id = wa.coin_id
         ORDER BY c.display_name, wa.id'
    );
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $addresses = [];
    foreach ($rows as $r) {
        $addresses[] = [
            'id' => (int) $r['id'],
            'coin_id' => (int) $r['coin_id'],
            'coin_key' => $r['coin_key'],
            'display_name' => $r['display_name'],
            'symbol' => $r['symbol'],
            'logo' => $r['logo'] ?? null,
            'address' => $r['address'],
            'created_at' => $r['created_at'],
        ];
    }
    echo json_encode(['success' => true, 'addresses' => $addresses]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $coinId = isset($input['coin_id']) ? (int) $input['coin_id'] : 0;
    $address = sanitizeAddress($input['address'] ?? '');

    if ($coinId <= 0 || $address === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Coin and address are required']);
        exit;
    }

    $check = $pdo->prepare('SELECT id FROM coins WHERE id = ?');
    $check->execute([$coinId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Coin not found']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO wallet_addresses (coin_id, address) VALUES (?, ?)');
        $stmt->execute([$coinId, $address]);
        echo json_encode(['success' => true, 'id' => (int) $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Address already exists for this coin']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unable to create address']);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($input['id'] ?? 0);

    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Address ID is required']);
        exit;
    }

    $updates = [];
    $params = [];
    if (isset($input['coin_id']) && (int) $input['coin_id'] > 0) {
        $updates[] = 'coin_id = ?';
        $params[] = (int) $input['coin_id'];
    }
    if (array_key_exists('address', $input)) {
        $updates[] = 'address = ?';
        $params[] = sanitizeAddress($input['address']);
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No data to update']);
        exit;
    }

    $params[] = $id;
    $sql = 'UPDATE wallet_addresses SET ' . implode(', ', $updates) . ' WHERE id = ?';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Address updated']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Address already exists for this coin']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unable to update address']);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Address ID is required']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM wallet_addresses WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Address not found']);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Address deleted']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

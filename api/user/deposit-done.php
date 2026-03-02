<?php
/**
 * Bloombit - Deposit Done API
 * POST /api/user/deposit-done.php - Mark user-confirmed deposit (clicked "Done")
 * Accepts JSON: { transaction_id } or multipart/form-data: transaction_id, reference (optional), proof (file optional).
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

$txId = 0;
$reference = '';
$proofUrl = null;

if (!empty($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
    // Multipart: transaction_id, reference (optional), proof (file optional)
    $txId = (int)($_POST['transaction_id'] ?? 0);
    $reference = trim((string)($_POST['reference'] ?? ''));
    $file = $_FILES['proof'];
    $allowedMimes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowedMimes[$mime])) {
        echo json_encode(['success' => false, 'error' => 'Invalid file type. Use PNG, JPEG, WEBP, or PDF.']);
        exit;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'File too large. Max 5MB.']);
        exit;
    }
    $baseDir = dirname(__DIR__, 2) . '/uploads/deposit-proofs';
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }
    $ext = $allowedMimes[$mime];
    $filename = 'tx_' . $txId . '_' . time() . '.' . $ext;
    $destPath = $baseDir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $proofUrl = '/uploads/deposit-proofs/' . $filename;
    }
} else {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $txId = (int)($input['transaction_id'] ?? 0);
    $reference = trim((string)($input['reference'] ?? ''));
}

if ($txId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid transaction ID']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'user_confirmed_at'");
    if (!$chk || $chk->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Deposit confirmation not available yet. Please update your database (migration.sql).']);
        exit;
    }
} catch (Throwable $e) {}

$userId = (int)$_SESSION['user_id'];

$hasProofCol = false;
try {
    $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'proof_url'");
    $hasProofCol = $chk && $chk->rowCount() > 0;
} catch (Throwable $e) {}

// Update: user_confirmed_at, optionally reference, optionally proof_url
$referenceVal = $reference !== '' ? $reference : null;
if ($hasProofCol) {
    $stmt = $pdo->prepare("UPDATE transactions
                           SET user_confirmed_at = NOW(),
                               reference = COALESCE(?, reference),
                               proof_url = COALESCE(?, proof_url)
                           WHERE id = ? AND user_id = ? AND type = 'deposit' AND status = 'pending' AND user_confirmed_at IS NULL");
    $stmt->execute([$referenceVal, $proofUrl, $txId, $userId]);
} else {
    $stmt = $pdo->prepare("UPDATE transactions
                           SET user_confirmed_at = NOW(),
                               reference = COALESCE(?, reference)
                           WHERE id = ? AND user_id = ? AND type = 'deposit' AND status = 'pending' AND user_confirmed_at IS NULL");
    $stmt->execute([$referenceVal, $txId, $userId]);
}

if ($stmt->rowCount() !== 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unable to mark this deposit as done. It may already be confirmed or no longer pending.']);
    exit;
}

echo json_encode(['success' => true, 'data' => ['message' => 'Deposit marked as done. Awaiting admin approval.']]);
exit;

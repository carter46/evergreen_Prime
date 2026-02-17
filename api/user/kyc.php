<?php
/**
 * Bloombit - KYC API
 * GET /api/user/kyc.php - Fetch KYC status
 * POST /api/user/kyc.php - Submit KYC documents (multipart/form-data)
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
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

// Check kyc_status column exists
$colChk = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
if (!$colChk || $colChk->rowCount() === 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode(['success' => true, 'data' => ['status' => 'none', 'rejection_reason' => null]]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'KYC system not configured']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT kyc_status FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $status = $row['kyc_status'] ?? 'none';

    $rejectionReason = null;
    if ($status === 'rejected') {
        $tblChk = $pdo->query("SHOW TABLES LIKE 'kyc_submissions'");
        if ($tblChk && $tblChk->rowCount() > 0) {
            $stmt2 = $pdo->prepare('SELECT rejection_reason FROM kyc_submissions WHERE user_id = ? AND status = ? ORDER BY created_at DESC LIMIT 1');
            $stmt2->execute([$userId, 'rejected']);
            $r = $stmt2->fetch(PDO::FETCH_ASSOC);
            $rejectionReason = $r['rejection_reason'] ?? null;
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'status' => $status,
            'rejection_reason' => $rejectionReason,
        ],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tblChk = $pdo->query("SHOW TABLES LIKE 'kyc_submissions'");
    if (!$tblChk || $tblChk->rowCount() === 0) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'KYC system not configured']);
        exit;
    }

    $documentType = strtolower(trim($_POST['document_type'] ?? ''));
    $fullName = trim($_POST['full_name'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $allowedTypes = ['passport', 'id_card', 'driver_license'];
    if (!in_array($documentType, $allowedTypes, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid document type']);
        exit;
    }
    if (empty($fullName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Full name is required']);
        exit;
    }

    $frontFile = $_FILES['document_front'] ?? null;
    if (!$frontFile || empty($frontFile['tmp_name']) || $frontFile['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Document front image is required']);
        exit;
    }

    $allowedMimes = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($frontFile['tmp_name']);
    if (!isset($allowedMimes[$mime]) || $frontFile['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid front document. Use JPG, PNG or PDF, max 5MB']);
        exit;
    }

    // Check if back file is required
    $requiresBack = in_array($documentType, ['id_card', 'driver_license'], true);
    $backFile = $_FILES['document_back'] ?? null;
    if ($requiresBack && (!$backFile || empty($backFile['tmp_name']) || $backFile['error'] !== UPLOAD_ERR_OK)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Document back image is required for ID card and driver license']);
        exit;
    }

    // Process front file first
    $baseDir = dirname(__DIR__, 2) . '/uploads/kyc/' . (int) $userId;
    if (!is_dir($baseDir)) {
        if (!mkdir($baseDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create upload directory']);
            exit;
        }
    }
    $ext = $allowedMimes[$mime];
    $filename = 'front_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($frontFile['tmp_name'], $baseDir . '/' . $filename)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to save front document']);
        exit;
    }
    $frontPath = 'uploads/kyc/' . (int) $userId . '/' . $filename;

    // Process back file if provided
    $backPath = null;
    if ($backFile && !empty($backFile['tmp_name']) && $backFile['error'] === UPLOAD_ERR_OK) {
        $bmime = $finfo->file($backFile['tmp_name']);
        if (!isset($allowedMimes[$bmime]) || $backFile['size'] > 5 * 1024 * 1024) {
            @unlink($baseDir . '/' . $filename);
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid back document. Use JPG, PNG or PDF, max 5MB']);
            exit;
        }
        $bext = $allowedMimes[$bmime];
        $bfilename = 'back_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $bext;
        if (!move_uploaded_file($backFile['tmp_name'], $baseDir . '/' . $bfilename)) {
            @unlink($baseDir . '/' . $filename);
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to save back document']);
            exit;
        }
        $backPath = 'uploads/kyc/' . (int) $userId . '/' . $bfilename;
    }

    $dobFormatted = null;
    if (!empty($dateOfBirth)) {
        $d = date_create($dateOfBirth);
        $dobFormatted = $d ? $d->format('Y-m-d') : null;
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO kyc_submissions (user_id, document_type, front_path, back_path, full_name, date_of_birth, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $documentType, $frontPath, $backPath, $fullName, $dobFormatted, $address ?: null, 'pending']);

        $pdo->prepare('UPDATE users SET kyc_status = ? WHERE id = ?')->execute(['pending', $userId]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        @unlink(dirname(__DIR__, 2) . '/' . $frontPath);
        if ($backPath) @unlink(dirname(__DIR__, 2) . '/' . $backPath);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to submit KYC']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => ['message' => 'KYC documents submitted for review'],
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

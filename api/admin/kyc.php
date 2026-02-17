<?php
/**
 * Bloombit - Admin KYC API
 * GET /api/admin/kyc.php?filter=pending|approved|rejected - List KYC submissions
 * POST /api/admin/kyc.php - Actions: approve, reject
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden']);
    exit;
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$tblChk = $pdo->query("SHOW TABLES LIKE 'kyc_submissions'");
if (!$tblChk || $tblChk->rowCount() === 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode(['success' => true, 'data' => []]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'KYC system not configured']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filter = $_GET['filter'] ?? 'pending';
    $allowed = ['pending', 'approved', 'rejected'];
    if (!in_array($filter, $allowed, true)) $filter = 'pending';

    $statusMap = ['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected'];
    $status = $statusMap[$filter];

    $stmt = $pdo->prepare('SELECT k.id, k.user_id, k.document_type, k.front_path, k.back_path, k.full_name, k.date_of_birth, k.address, k.status, k.rejection_reason, k.created_at, u.name, u.email FROM kyc_submissions k JOIN users u ON u.id = k.user_id WHERE k.status = ? ORDER BY k.created_at DESC LIMIT 200');
    $stmt->execute([$status]);
    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['user_display'] = $row['name'] ?: $row['email'];
        $row['document_type_label'] = ucfirst(str_replace('_', ' ', $row['document_type']));
        $rows[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = trim($input['action'] ?? '');
    $submissionId = (int) ($input['submission_id'] ?? 0);

    if (!$submissionId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Submission ID required']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT k.id, k.user_id, k.status FROM kyc_submissions k WHERE k.id = ?');
    $stmt->execute([$submissionId]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$sub) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Submission not found']);
        exit;
    }
    if ($sub['status'] !== 'pending') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Submission already processed']);
        exit;
    }

    $adminId = (int) $_SESSION['user_id'];

    if ($action === 'approve') {
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE kyc_submissions SET status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?')->execute(['approved', $adminId, $submissionId]);
            $pdo->prepare('UPDATE users SET kyc_status = ? WHERE id = ?')->execute(['verified', $sub['user_id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to approve']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => ['message' => 'KYC approved']]);
        exit;
    }

    if ($action === 'reject') {
        $reason = trim($input['reason'] ?? '');
        if (empty($reason)) $reason = 'Document verification failed';

        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE kyc_submissions SET status = ?, rejection_reason = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?')->execute(['rejected', $reason, $adminId, $submissionId]);
            $pdo->prepare('UPDATE users SET kyc_status = ? WHERE id = ?')->execute(['rejected', $sub['user_id']]);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to reject']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => ['message' => 'KYC rejected']]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);

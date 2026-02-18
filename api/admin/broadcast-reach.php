<?php
/**
 * Bloombit - Broadcast Reach API
 * GET /api/admin/broadcast-reach.php?recipients=all|active_investors|kyc_verified
 * Returns estimated user count for targeting.
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$recipients = trim($_GET['recipients'] ?? 'all');
if (!in_array($recipients, ['all', 'active_investors', 'kyc_verified'], true)) {
    $recipients = 'all';
}

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$chkKyc = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
$hasKyc = $chkKyc && $chkKyc->rowCount() > 0;

if ($recipients === 'all') {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND active = 1");
} elseif ($recipients === 'active_investors') {
    $stmt = $pdo->query("SELECT COUNT(DISTINCT u.id) FROM users u INNER JOIN user_investments ui ON ui.user_id = u.id AND ui.status = 'active' WHERE u.role = 'user' AND u.active = 1");
} elseif ($recipients === 'kyc_verified') {
    if ($hasKyc) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND active = 1 AND kyc_status = 'verified'");
    }
}

$count = isset($stmt) ? (int) $stmt->fetchColumn() : 0;

echo json_encode(['success' => true, 'data' => ['count' => $count]]);

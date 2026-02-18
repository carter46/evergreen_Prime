<?php
/**
 * Bloombit - Admin Email Broadcast API
 * POST /api/admin/broadcast.php
 * Sends email broadcast to users.
 * Body: subject, body, recipients (all|active_investors|kyc_verified), test (optional - send only to admin)
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

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? [];
$subject = trim($input['subject'] ?? '');
$body = trim($input['body'] ?? '');
$recipients = trim($input['recipients'] ?? 'all');
$testOnly = !empty($input['test']);

if (empty($subject) || empty($body)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Subject and body are required']);
    exit;
}

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

require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/email-templates/render.php';

$chkKyc = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
$hasKyc = $chkKyc && $chkKyc->rowCount() > 0;
$chkBal = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
$hasBal = $chkBal && $chkBal->rowCount() > 0;

if ($testOnly) {
    $adminId = (int) ($_SESSION['user_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ?');
    $stmt->execute([$adminId]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    if ($recipients === 'all') {
        $stmt = $pdo->query("SELECT id, email, name FROM users WHERE role = 'user' AND active = 1");
    } elseif ($recipients === 'active_investors') {
        $stmt = $pdo->query("SELECT DISTINCT u.id, u.email, u.name FROM users u INNER JOIN user_investments ui ON ui.user_id = u.id AND ui.status = 'active' WHERE u.role = 'user' AND u.active = 1");
    } elseif ($recipients === 'kyc_verified' && $hasKyc) {
        $stmt = $pdo->query("SELECT id, email, name FROM users WHERE role = 'user' AND active = 1 AND kyc_status = 'verified'");
    } else {
        $stmt = $pdo->query("SELECT id, email, name FROM users WHERE role = 'user' AND active = 1");
    }
    $users = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

$sent = 0;
$errors = [];

foreach ($users as $u) {
    $userName = $u['name'] ?? 'User';
    $balance = '$0.00';
    if ($hasBal) {
        $bStmt = $pdo->prepare('SELECT last_balance_usd FROM users WHERE id = ?');
        $bStmt->execute([$u['id']]);
        $row = $bStmt->fetch(PDO::FETCH_ASSOC);
        $balance = '$' . number_format((float) ($row['last_balance_usd'] ?? 0), 2);
    }
    $bodyPersonalized = str_replace(['{user_name}', '{balance}'], [$userName, $balance], $body);
    try {
        $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
        $mail->clearAddresses();
        $mail->addAddress($u['email']);
        $mail->Subject = $subject;
        $mail->Body = renderEmailTemplate('broadcast.php', [
            'subject' => $subject,
            'body' => $bodyPersonalized,
        ]);
        $mail->AltBody = strip_tags($bodyPersonalized);
        $mail->isHTML(true);
        $mail->send();
        $sent++;
    } catch (Throwable $e) {
        $errors[] = $u['email'] . ': ' . $e->getMessage();
    }
}

$total = count($users);
if (!$testOnly && $sent > 0) {
    try {
        $chk = $pdo->query("SHOW TABLES LIKE 'broadcast_campaigns'");
        if ($chk && $chk->rowCount() > 0) {
            $pdo->prepare('INSERT INTO broadcast_campaigns (subject, recipients_filter, total_recipients, status) VALUES (?, ?, ?, ?)')
                ->execute([$subject, $recipients, $sent, 'sent']);
        }
    } catch (Throwable $e) {}
}
$msg = $testOnly
    ? "Test email sent to {$sent} recipient(s)."
    : "Broadcast sent to {$sent} of {$total} recipients.";
if (!empty($errors)) {
    $msg .= ' Some failed: ' . implode('; ', array_slice($errors, 0, 3));
}

echo json_encode([
    'success' => true,
    'data' => [
        'message' => $msg,
        'sent' => $sent,
        'total' => $total,
    ]
]);

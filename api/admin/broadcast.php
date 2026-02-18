<?php
/**
 * Bloombit - Admin Email Broadcast API
 * POST /api/admin/broadcast.php
 * Sends email broadcast to registered users and/or external emails.
 * Body (JSON):
 * - subject (required)
 * - body (required)
 * - recipients: all|active_investors|kyc_verified (optional; default all)
 * - to: string (comma/space separated external emails) OR array of emails (optional)
 * - include_users: bool (optional; default true if recipients provided, otherwise false when only external recipients are supplied)
 * - test: bool (optional; send only to current admin)
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
$includeUsers = array_key_exists('include_users', $input) ? (bool) $input['include_users'] : (!empty($input['recipients']));
$toRaw = $input['to'] ?? null;

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

// Parse external recipients
$externalEmails = [];
if (is_string($toRaw)) {
    $parts = preg_split('/[,\s]+/', $toRaw) ?: [];
    foreach ($parts as $p) {
        $e = trim($p);
        if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) $externalEmails[] = strtolower($e);
    }
} elseif (is_array($toRaw)) {
    foreach ($toRaw as $p) {
        $e = trim((string) $p);
        if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) $externalEmails[] = strtolower($e);
    }
}
$externalEmails = array_values(array_unique($externalEmails));

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
    $users = [];
    if ($includeUsers) {
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
}

$sent = 0;
$errors = [];

// Build a unified recipient list
$targets = [];
if (!$testOnly) {
    foreach ($externalEmails as $e) {
        $targets[] = ['email' => $e, 'name' => null, 'id' => null, 'is_user' => false];
    }
}
foreach ($users as $u) {
    $targets[] = ['email' => strtolower((string) ($u['email'] ?? '')), 'name' => $u['name'] ?? 'User', 'id' => $u['id'] ?? null, 'is_user' => true];
}
// Deduplicate by email
$seen = [];
$uniqueTargets = [];
foreach ($targets as $t) {
    if (empty($t['email']) || !filter_var($t['email'], FILTER_VALIDATE_EMAIL)) continue;
    if (isset($seen[$t['email']])) continue;
    $seen[$t['email']] = true;
    $uniqueTargets[] = $t;
}

foreach ($uniqueTargets as $t) {
    $userName = $t['name'] ?? 'User';
    $balance = '$0.00';
    if ($t['is_user'] && $hasBal && !empty($t['id'])) {
        $bStmt = $pdo->prepare('SELECT last_balance_usd FROM users WHERE id = ?');
        $bStmt->execute([(int)$t['id']]);
        $row = $bStmt->fetch(PDO::FETCH_ASSOC);
        $balance = '$' . number_format((float) ($row['last_balance_usd'] ?? 0), 2);
    }
    $bodyPersonalized = str_replace(['{user_name}', '{balance}'], [$userName, $balance], $body);
    try {
        $mail = require dirname(__DIR__, 2) . '/includes/mailer.php';
        $mail->clearAddresses();
        $mail->addAddress($t['email']);
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
        $errors[] = $t['email'] . ': ' . $e->getMessage();
    }
}

$total = count($uniqueTargets);

if (!$testOnly && $sent > 0) {
    try {
        $chk = $pdo->query("SHOW TABLES LIKE 'broadcast_campaigns'");
        if ($chk && $chk->rowCount() > 0) {
            $filter = 'manual';
            if ($includeUsers) $filter = $recipients;
            if (!empty($externalEmails) && $includeUsers) $filter = $recipients . '+manual';
            $pdo->prepare('INSERT INTO broadcast_campaigns (subject, recipients_filter, total_recipients, status) VALUES (?, ?, ?, ?)')
                ->execute([$subject, $filter, $sent, 'sent']);
        }
    } catch (Throwable $e) {}
}

// Store in admin mailbox (outbox)
if (!$testOnly) {
    try {
        $chk = $pdo->query("SHOW TABLES LIKE 'admin_mailbox'");
        if ($chk && $chk->rowCount() > 0) {
            $stmt = $pdo->prepare('INSERT INTO admin_mailbox (direction, source, from_email, from_name, to_emails, subject, body_html, body_text, status, error_text) VALUES (?,?,?,?,?,?,?,?,?,?)');
            $fromEmail = null;
            $fromName = null;
            try {
                $config = include dirname(__DIR__, 2) . '/config.php';
                $fromEmail = get_site_setting('mail_from_email', $config['mail']['from_email'] ?? null) ?: ($config['mail']['from_email'] ?? null);
                $fromName = get_site_setting('mail_from_name', $config['mail']['from_name'] ?? null) ?: ($config['mail']['from_name'] ?? null);
            } catch (Throwable $e) {}
            $stmt->execute([
                'out',
                'admin_compose',
                $fromEmail,
                $fromName,
                implode(', ', array_keys($seen)),
                $subject,
                null,
                $body,
                empty($errors) ? 'sent' : 'failed',
                empty($errors) ? null : implode('; ', array_slice($errors, 0, 5)),
            ]);
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

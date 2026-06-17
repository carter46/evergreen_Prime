<?php
/**
 * Bloombit - User Referral Stats API
 * GET /api/user/referral-stats.php
 * Returns: my_referral_code, share_url, referred_count, total_earned_usd, referrals (list), referral_enabled.
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$referralEnabled = get_site_setting('referral_enabled', '0') === '1';
$myCode = null;
$referredCount = 0;
$totalEarnedUsd = 0.0;
$referrals = [];

try {
    $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'my_referral_code'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT my_referral_code FROM users WHERE id = ?');
        $st->execute([$userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $myCode = $row['my_referral_code'] ?? null;
        if (empty($myCode)) {
            $pdo->prepare('UPDATE users SET my_referral_code = CONCAT(\'REF\', id) WHERE id = ? AND (my_referral_code IS NULL OR my_referral_code = \'\')')->execute([$userId]);
            $myCode = 'REF' . $userId;
        }
    }
} catch (Throwable $e) {}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$shareUrl = $myCode ? rtrim($baseUrl, '/') . '/register?ref=' . urlencode($myCode) : null;

try {
    $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'referred_by_user_id'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE referred_by_user_id = ? AND role = ? ORDER BY created_at DESC');
        $st->execute([$userId, 'user']);
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $referrals[] = [
                'id' => (int) $r['id'],
                'name' => $r['name'] ?? '',
                'email' => $r['email'] ?? '',
                'created_at' => $r['created_at'] ?? null,
            ];
        }
        $referredCount = count($referrals);
    }
} catch (Throwable $e) {}

$earningsHistory = [];
$totalEarnedUsd = get_user_total_referral_bonus($pdo, $userId);
try {
    $chk = $pdo->query("SHOW TABLES LIKE 'referral_earnings'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT re.id, re.referred_user_id, re.amount_usd, re.source, re.percent_used, re.created_at, u.name AS referred_name, u.email AS referred_email FROM referral_earnings re LEFT JOIN users u ON u.id = re.referred_user_id WHERE re.referrer_user_id = ? ORDER BY re.created_at DESC LIMIT 50');
        $st->execute([$userId]);
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $earningsHistory[] = [
                'id' => (int) $r['id'],
                'referred_user_id' => (int) $r['referred_user_id'],
                'amount_usd' => (float) $r['amount_usd'],
                'source' => $r['source'] ?? '',
                'percent_used' => isset($r['percent_used']) ? (float) $r['percent_used'] : null,
                'created_at' => $r['created_at'] ?? null,
                'referred_name' => $r['referred_name'] ?? '',
                'referred_email' => $r['referred_email'] ?? '',
            ];
        }
    }
} catch (Throwable $e) {}

echo json_encode([
    'success' => true,
    'data' => [
        'referral_enabled' => $referralEnabled,
        'my_referral_code' => $myCode,
        'share_url' => $shareUrl,
        'referred_count' => $referredCount,
        'total_earned_usd' => round($totalEarnedUsd, 2),
        'referrals' => $referrals,
        'earnings_history' => $earningsHistory,
    ],
]);

<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'referrals';
$siteName = get_site_name();

$referralEnabled = get_site_setting('referral_enabled', '0') === '1';
$referralPctRaw = max(0, min(100, (float) (get_site_setting('referral_percentage', '15') ?: '15')));
$referralPctDisplay = (floor($referralPctRaw) == $referralPctRaw)
    ? (string) (int) $referralPctRaw
    : rtrim(rtrim(number_format($referralPctRaw, 2, '.', ''), '0'), '.');
$myCode = null;
$shareUrl = null;
$referredCount = 0;
$totalEarnedUsd = 0.0;
$referrals = [];

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = (int) $_SESSION['user_id'];

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
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $shareUrl = $myCode ? rtrim($baseUrl, '/') . '/register?ref=' . urlencode($myCode) : null;

    $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'referred_by_user_id'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE referred_by_user_id = ? AND role = ? ORDER BY created_at DESC');
        $st->execute([$userId, 'user']);
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $referrals[] = ['id' => (int)$r['id'], 'name' => $r['name'] ?? '', 'email' => $r['email'] ?? '', 'created_at' => $r['created_at'] ?? null];
        }
        $referredCount = count($referrals);
    }

    $referralEarningsHistory = [];
    $totalLast24h = 0;
    $chk = $pdo->query("SHOW TABLES LIKE 'referral_earnings'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT COALESCE(SUM(amount_usd), 0) FROM referral_earnings WHERE referrer_user_id = ?');
        $st->execute([$userId]);
        $totalEarnedUsd = (float) $st->fetchColumn();
        $st = $pdo->prepare('SELECT COALESCE(SUM(amount_usd), 0) FROM referral_earnings WHERE referrer_user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)');
        $st->execute([$userId]);
        $totalLast24h = (float) $st->fetchColumn();
        $st = $pdo->prepare('SELECT re.id, re.referred_user_id, re.amount_usd, re.source, re.created_at, u.name AS referred_name, u.email AS referred_email FROM referral_earnings re LEFT JOIN users u ON u.id = re.referred_user_id WHERE re.referrer_user_id = ? ORDER BY re.created_at DESC LIMIT 50');
        $st->execute([$userId]);
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $referralEarningsHistory[] = ['id' => (int)$r['id'], 'referred_user_id' => (int)$r['referred_user_id'], 'amount_usd' => (float)$r['amount_usd'], 'source' => $r['source'] ?? '', 'created_at' => $r['created_at'] ?? null, 'referred_name' => $r['referred_name'] ?? '', 'referred_email' => $r['referred_email'] ?? ''];
        }
    }
    if ($totalLast24h == 0 && isset($pdo)) {
        $st = $pdo->prepare("SELECT COALESCE(SUM(COALESCE(amount_usd, amount)), 0) FROM transactions WHERE user_id = ? AND type = 'referral_bonus' AND status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $st->execute([$userId]);
        $totalLast24h = (float) $st->fetchColumn();
    }
} catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Referrals</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#f9bd0b", "background-light": "#f8f8f5", "background-dark": "#231e0f" }, fontFamily: { "display": ["Space Grotesk", "sans-serif"] } } } };
</script>
<style>body { font-family: 'Space Grotesk', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-4xl mx-auto py-6 space-y-8">
<nav class="flex text-xs text-slate-400 gap-2 mb-2"><a href="/dashboard/user/dashboard" class="hover:text-primary">Dashboard</a><span>/</span><span class="text-slate-600 dark:text-slate-300">Referrals</span></nav>
<h1 class="text-2xl sm:text-3xl font-bold mb-1">Referral Program</h1>
<p class="text-slate-500 dark:text-zinc-400 text-sm sm:text-base max-w-2xl">Earn <strong class="text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($referralPctDisplay); ?>%</strong> of your referees&rsquo; daily earnings (same commission rate the platform uses for qualifying referral rewards). When someone you refer invests and receives payouts, that percentage of their daily earning is credited to your wallet.</p>

<?php if (!$referralEnabled): ?>
<div class="bg-amber-50/80 dark:bg-amber-900/15 rounded-xl p-4 flex items-start gap-3 border border-amber-200/60 dark:border-amber-800/50">
<span class="material-icons-round text-amber-600 dark:text-amber-400 mt-0.5">info</span>
<p class="text-sm text-amber-800 dark:text-amber-200">Referral bonuses are currently paused. You can still copy and share your link; when the program is enabled, you will earn from qualifying referrals.</p>
</div>
<?php endif; ?>

<!-- 1. Stats first -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div class="rounded-xl p-6 bg-white/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/50 shadow-sm">
<p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 uppercase tracking-wider font-medium">Referred users</p>
<p class="text-2xl sm:text-3xl font-bold text-primary mt-2"><?php echo (int) $referredCount; ?></p>
</div>
<div class="rounded-xl p-6 bg-white/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/50 shadow-sm">
<p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 uppercase tracking-wider font-medium">Total earned (Bonus)</p>
<p class="text-2xl sm:text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">$<?php echo format_usd_amount($totalEarnedUsd); ?></p>
<?php if ($totalEarnedUsd > 0): ?><p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Last 24h: $<?php echo format_usd_amount($totalLast24h ?? 0); ?></p><?php endif; ?>
</div>
</div>

<!-- 2. Referral earnings history -->
<div class="rounded-xl overflow-hidden bg-white/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/50 shadow-sm">
<h2 class="text-base sm:text-lg font-semibold px-5 sm:px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/50 flex items-center gap-2"><span class="material-icons-round text-primary text-xl">payments</span> Referral earnings history</h2>
<?php if (empty($referralEarningsHistory)): ?>
<p class="p-6 text-slate-500 dark:text-zinc-400 text-center text-sm">No referral earnings yet. You earn when referred users get daily payouts (a percentage of their earning is credited to you).</p>
<?php else: ?>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50/80 dark:bg-slate-800/50">
<tr>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Date</th>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">From</th>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Source</th>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-right">Amount</th>
</tr>
</thead>
<tbody>
<?php
$sourceLabels = ['plan_subscription' => 'Plan subscription', 'first_deposit' => 'Deposit', 'referred_payout' => "Referee's earning"];
foreach ($referralEarningsHistory as $e):
    $sourceLabel = $sourceLabels[$e['source']] ?? $e['source'];
?>
<tr class="border-t border-slate-100 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
<td class="px-4 sm:px-6 py-3 text-sm text-slate-600 dark:text-zinc-400"><?php echo $e['created_at'] ? date('M j, Y H:i', strtotime($e['created_at'])) : '—'; ?></td>
<td class="px-4 sm:px-6 py-3 text-sm"><?php echo htmlspecialchars($e['referred_name'] ?: $e['referred_email'] ?: '—'); ?></td>
<td class="px-4 sm:px-6 py-3 text-xs text-slate-500 dark:text-zinc-500"><?php echo htmlspecialchars($sourceLabel); ?></td>
<td class="px-4 sm:px-6 py-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400 text-right">+$<?php echo format_usd_amount($e['amount_usd']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>

<!-- 3. People you referred -->
<div class="rounded-xl overflow-hidden bg-white/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/50 shadow-sm">
<h2 class="text-base sm:text-lg font-semibold px-5 sm:px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/50 flex items-center gap-2"><span class="material-icons-round text-primary text-xl">people</span> People you referred</h2>
<?php if (empty($referrals)): ?>
<p class="p-6 text-slate-500 dark:text-zinc-400 text-center text-sm">No referrals yet. Share your link below to get started.</p>
<?php else: ?>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50/80 dark:bg-slate-800/50">
<tr>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Name</th>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Email</th>
<th class="px-4 sm:px-6 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Joined</th>
</tr>
</thead>
<tbody>
<?php foreach ($referrals as $r): ?>
<tr class="border-t border-slate-100 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
<td class="px-4 sm:px-6 py-3 font-medium text-sm"><?php echo htmlspecialchars($r['name'] ?: '—'); ?></td>
<td class="px-4 sm:px-6 py-3 text-sm text-slate-600 dark:text-zinc-400"><?php echo htmlspecialchars($r['email']); ?></td>
<td class="px-4 sm:px-6 py-3 text-sm text-slate-500 dark:text-zinc-500"><?php echo $r['created_at'] ? date('M j, Y', strtotime($r['created_at'])) : '—'; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>

<!-- 4. Your referral code & share link last -->
<div class="rounded-xl p-6 sm:p-8 bg-white/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/50 shadow-sm">
<h2 class="text-base sm:text-lg font-semibold mb-4 flex items-center gap-2"><span class="material-icons-round text-primary text-xl">link</span> Your referral code &amp; link</h2>
<?php if ($myCode): ?>
<div class="space-y-4">
<div>
<label class="block text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mb-1.5">Code</label>
<div class="flex flex-wrap items-center gap-2 sm:gap-3">
<input type="text" id="referral-code" readonly value="<?php echo htmlspecialchars($myCode); ?>" class="flex-1 min-w-0 px-4 py-2.5 sm:py-3 bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-600 rounded-lg font-mono text-base sm:text-lg tracking-widest uppercase"/>
<button type="button" id="copy-code" class="shrink-0 px-4 py-2.5 sm:py-3 bg-primary text-black font-bold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2 text-sm"><span class="material-icons-round text-lg">content_copy</span> Copy code</button>
</div>
</div>
<div>
<label class="block text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mb-1.5">Share link (goes to registration and fills your code)</label>
<div class="flex flex-wrap items-center gap-2 sm:gap-3">
<input type="text" id="share-url" readonly value="<?php echo htmlspecialchars($shareUrl ?? ''); ?>" class="flex-1 min-w-0 px-4 py-2.5 sm:py-3 bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-600 rounded-lg text-sm"/>
<button type="button" id="copy-url" class="shrink-0 px-4 py-2.5 sm:py-3 bg-primary text-black font-bold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2 text-sm"><span class="material-icons-round text-lg">content_copy</span> Copy link</button>
</div>
</div>
</div>
<?php else: ?>
<p class="text-slate-500 dark:text-zinc-400 text-sm">Your referral code is being generated. <a href="/dashboard/user/referrals" class="text-primary font-semibold hover:underline">Refresh the page</a> in a moment.</p>
<?php endif; ?>
</div>
</div>
</main>
</div>
<script>
(function(){
  function copy(el) {
    if (!el || !el.value) return;
    el.select();
    el.setSelectionRange(0, 99999);
    try {
      navigator.clipboard.writeText(el.value);
      return true;
    } catch (e) {
      return false;
    }
  }
  var codeEl = document.getElementById('referral-code');
  var urlEl = document.getElementById('share-url');
  var copyCode = document.getElementById('copy-code');
  var copyUrl = document.getElementById('copy-url');
  if (copyCode && codeEl) copyCode.addEventListener('click', function(){ if (copy(codeEl)) { copyCode.innerHTML = '<span class="material-icons-round text-lg">content_copy</span> Copied!'; setTimeout(function(){ copyCode.innerHTML = '<span class="material-icons-round text-lg">content_copy</span> Copy code'; }, 1500); } });
  if (copyUrl && urlEl) copyUrl.addEventListener('click', function(){ if (copy(urlEl)) { copyUrl.innerHTML = '<span class="material-icons-round text-lg">content_copy</span> Copied!'; setTimeout(function(){ copyUrl.innerHTML = '<span class="material-icons-round text-lg">content_copy</span> Copy link'; }, 1500); } });
})();
</script>
</body>
</html>

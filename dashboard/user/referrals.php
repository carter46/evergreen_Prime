<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'referrals';
$siteName = get_site_name();

$referralEnabled = get_site_setting('referral_enabled', '0') === '1';
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
    $chk = $pdo->query("SHOW TABLES LIKE 'referral_earnings'");
    if ($chk && $chk->rowCount() > 0) {
        $st = $pdo->prepare('SELECT COALESCE(SUM(amount_usd), 0) FROM referral_earnings WHERE referrer_user_id = ?');
        $st->execute([$userId]);
        $totalEarnedUsd = (float) $st->fetchColumn();
        $st = $pdo->prepare('SELECT re.id, re.referred_user_id, re.amount_usd, re.source, re.created_at, u.name AS referred_name, u.email AS referred_email FROM referral_earnings re LEFT JOIN users u ON u.id = re.referred_user_id WHERE re.referrer_user_id = ? ORDER BY re.created_at DESC LIMIT 50');
        $st->execute([$userId]);
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $referralEarningsHistory[] = ['id' => (int)$r['id'], 'referred_user_id' => (int)$r['referred_user_id'], 'amount_usd' => (float)$r['amount_usd'], 'source' => $r['source'] ?? '', 'created_at' => $r['created_at'] ?? null, 'referred_name' => $r['referred_name'] ?? '', 'referred_email' => $r['referred_email'] ?? ''];
        }
    }
} catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Referrals</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#f9bd0b", "background-light": "#f8f8f5", "background-dark": "#231e0f" }, fontFamily: { "display": ["Inter", "sans-serif"] } } } };
</script>
<style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-4xl mx-auto py-6">
<nav class="flex text-xs text-slate-400 gap-2 mb-4"><a href="/dashboard/user/dashboard" class="hover:text-primary">Dashboard</a><span>/</span><span class="text-slate-600 dark:text-slate-300">Referrals</span></nav>
<h1 class="text-2xl font-bold mb-2">Referral Program</h1>
<p class="text-slate-500 dark:text-zinc-400 mb-6">Share your link and earn a percentage of your referees' first plan subscription (when the program is enabled).</p>

<?php if (!$referralEnabled): ?>
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6 flex items-center gap-3">
<span class="material-icons-round text-amber-600 dark:text-amber-400">info</span>
<p class="text-sm text-amber-800 dark:text-amber-200">Referral bonuses are currently paused. You can still copy and share your link; when the program is enabled, you will earn from qualifying referrals.</p>
</div>
<?php endif; ?>

<!-- Code & share link (always shown so user can copy) -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6 mb-6">
<h2 class="text-lg font-semibold mb-4 flex items-center gap-2"><span class="material-icons-round text-primary">link</span> Your referral code</h2>
<?php if ($myCode): ?>
<div class="flex flex-wrap items-center gap-3 mb-4">
<input type="text" id="referral-code" readonly value="<?php echo htmlspecialchars($myCode); ?>" class="flex-1 min-w-0 px-4 py-3 bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg font-mono text-lg tracking-widest uppercase"/>
<button type="button" id="copy-code" class="px-4 py-3 bg-primary text-black font-bold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"><span class="material-icons-round text-lg">content_copy</span> Copy code</button>
</div>
<p class="text-sm text-slate-500 dark:text-zinc-400 mb-4">Share link:</p>
<div class="flex flex-wrap items-center gap-3">
<input type="text" id="share-url" readonly value="<?php echo htmlspecialchars($shareUrl ?? ''); ?>" class="flex-1 min-w-0 px-4 py-3 bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg text-sm"/>
<button type="button" id="copy-url" class="px-4 py-3 bg-primary text-black font-bold rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"><span class="material-icons-round text-lg">content_copy</span> Copy link</button>
</div>
<?php else: ?>
<p class="text-slate-500 dark:text-zinc-400">Your referral code is being generated. <a href="/dashboard/user/referrals" class="text-primary font-semibold hover:underline">Refresh the page</a> in a moment.</p>
<?php endif; ?>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-5">
<p class="text-sm text-slate-500 dark:text-zinc-400 uppercase tracking-wider font-semibold">Referred users</p>
<p class="text-2xl font-bold text-primary mt-1"><?php echo (int) $referredCount; ?></p>
</div>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-5">
<p class="text-sm text-slate-500 dark:text-zinc-400 uppercase tracking-wider font-semibold">Total earned (USDT)</p>
<p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">$<?php echo number_format($totalEarnedUsd, 2); ?></p>
</div>
</div>

<!-- Referrals list -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 overflow-hidden mb-6">
<h2 class="text-lg font-semibold px-6 py-4 border-b border-slate-200 dark:border-zinc-800 flex items-center gap-2"><span class="material-icons-round text-primary">people</span> People you referred</h2>
<?php if (empty($referrals)): ?>
<p class="p-6 text-slate-500 dark:text-zinc-400 text-center">No referrals yet. Share your link to get started.</p>
<?php else: ?>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 dark:bg-zinc-800/50">
<tr>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Name</th>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Email</th>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Joined</th>
</tr>
</thead>
<tbody>
<?php foreach ($referrals as $r): ?>
<tr class="border-t border-slate-100 dark:border-zinc-800 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
<td class="px-6 py-3 font-medium"><?php echo htmlspecialchars($r['name'] ?: '—'); ?></td>
<td class="px-6 py-3 text-sm text-slate-600 dark:text-zinc-400"><?php echo htmlspecialchars($r['email']); ?></td>
<td class="px-6 py-3 text-sm text-slate-500 dark:text-zinc-500"><?php echo $r['created_at'] ? date('M j, Y', strtotime($r['created_at'])) : '—'; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>

<!-- Referral earnings history -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 overflow-hidden">
<h2 class="text-lg font-semibold px-6 py-4 border-b border-slate-200 dark:border-zinc-800 flex items-center gap-2"><span class="material-icons-round text-primary">payments</span> Referral earnings history</h2>
<?php if (empty($referralEarningsHistory)): ?>
<p class="p-6 text-slate-500 dark:text-zinc-400 text-center">No referral earnings yet. When your referred users subscribe to a plan, you will see earnings here.</p>
<?php else: ?>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 dark:bg-zinc-800/50">
<tr>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Date</th>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">From</th>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Source</th>
<th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-zinc-400 uppercase tracking-wider text-right">Amount (USD)</th>
</tr>
</thead>
<tbody>
<?php foreach ($referralEarningsHistory as $e): ?>
<tr class="border-t border-slate-100 dark:border-zinc-800 hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
<td class="px-6 py-3 text-sm text-slate-600 dark:text-zinc-400"><?php echo $e['created_at'] ? date('M j, Y H:i', strtotime($e['created_at'])) : '—'; ?></td>
<td class="px-6 py-3 text-sm"><?php echo htmlspecialchars($e['referred_name'] ?: $e['referred_email'] ?: '—'); ?></td>
<td class="px-6 py-3 text-xs text-slate-500 dark:text-zinc-500"><?php echo $e['source'] === 'plan_subscription' ? 'Plan subscription' : htmlspecialchars($e['source']); ?></td>
<td class="px-6 py-3 text-sm font-bold text-emerald-600 dark:text-emerald-400 text-right">+$<?php echo number_format($e['amount_usd'], 2); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
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

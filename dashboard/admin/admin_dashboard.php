<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'dashboard';
$siteName = get_site_name();
$pageTitle = $siteName . ' | Command Center';

$totalUsers = 0;
$totalEarnings = 0;
$activeInv = 0;
$pendingDepositsCount = 0;
$pendingDepositsSum = 0;
$totalDeposits = 0;
$pendingWithdrawalsCount = 0;
$pendingWithdrawalsSum = 0;
$planDist = [];
$pendingList = [];
$recentActivity = [];

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $totalEarnings = (float) get_platform_total_profit($pdo);
    $activeInv = (int) $pdo->query("SELECT COUNT(*) FROM user_investments WHERE status = 'active'")->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'deposit' AND status = 'pending'");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $pendingDepositsCount = (int) $row[0];
    $pendingDepositsSum = (float) $row[1];
    $totalDeposits = (float) $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'deposit' AND status = 'completed'")->fetchColumn();
    $withSumCol = 'amount';
    try {
        if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) {
            $withSumCol = 'COALESCE(amount_usd, amount)';
        }
    } catch (Throwable $e) {}
    $stmtWith = $pdo->query("SELECT COUNT(*), COALESCE(SUM($withSumCol), 0) FROM transactions WHERE type = 'withdrawal' AND status = 'pending'");
    $rowWith = $stmtWith->fetch(PDO::FETCH_NUM);
    $pendingWithdrawalsCount = (int) $rowWith[0];
    $pendingWithdrawalsSum = (float) $rowWith[1];
    $stmt = $pdo->query('SELECT p.name, ui.plan_id, COUNT(*) AS cnt, COALESCE(SUM(ui.amount), 0) AS cap FROM user_investments ui JOIN plans p ON p.id = ui.plan_id GROUP BY ui.plan_id, p.name');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $planDist[] = $row;
    }
    $txCols = 't.id, t.amount, t.currency, t.status, u.name, u.id AS user_id';
    try {
        if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) {
            $txCols .= ', t.amount_usd';
        }
    } catch (Throwable $e) {}
    $stmt = $pdo->query("SELECT $txCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' AND t.status = 'pending' ORDER BY t.created_at DESC LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pendingList[] = $row;
    }
    $activities = [];
    $stmt = $pdo->query("SELECT 'registration' AS type, u.name, u.created_at, NULL AS amount, NULL AS plan_name FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $actCols = 'u.name, t.created_at, t.amount, NULL AS plan_name';
    try {
        if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) {
            $actCols = 'u.name, t.created_at, t.amount, t.amount_usd, NULL AS plan_name';
        }
    } catch (Throwable $e) {}
    $stmt = $pdo->query("SELECT 'withdrawal' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'withdrawal' ORDER BY t.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'deposit' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' AND t.status = 'completed' ORDER BY t.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'plan_activated' AS type, u.name, ui.created_at, ui.amount, p.name AS plan_name FROM user_investments ui JOIN users u ON u.id = ui.user_id JOIN plans p ON p.id = ui.plan_id ORDER BY ui.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    usort($activities, function ($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $recentActivity = array_slice($activities, 0, 5);
} catch (Throwable $e) {}

$planMax = 1;
foreach ($planDist as $p) {
    if ((int) $p['cnt'] > $planMax) {
        $planMax = (int) $p['cnt'];
    }
}

$adminPendingNotifCount = $pendingDepositsCount + $pendingWithdrawalsCount;

$coinDot = function (string $currency): string {
    $cu = strtoupper($currency);
    if ($cu === 'BTC') return 'bg-orange-500';
    if (in_array($cu, ['USDT', 'USDC', 'USD', 'BUSD', 'DAI'], true)) return 'bg-emerald-500';
    if ($cu === 'ETH') return 'bg-indigo-400';
    return 'bg-primary-container';
};

require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
$pageHeading = 'Mission Control';
$pageSubtitle = 'Real-time institutional oversight and system orchestration.';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-gutter mb-8 md:mb-10">
<div class="glass-panel p-6 rounded-xl flex flex-col justify-between">
<div class="flex justify-between items-start">
<p class="font-label-xs text-label-xs text-on-surface-variant">TOTAL USERS</p>
<span class="text-success text-[10px] font-bold flex items-center gap-1">
<span class="material-symbols-outlined text-xs">trending_up</span> +12%
</span>
</div>
<div class="mt-4">
<h3 class="font-display text-headline-lg text-text-primary"><?php echo number_format($totalUsers); ?></h3>
<p class="text-xs text-on-surface-variant/60 mt-1">Global Active Accounts</p>
</div>
</div>
<div class="glass-panel p-6 rounded-xl flex flex-col justify-between border-l-2 border-l-primary-container">
<div class="flex justify-between items-start">
<p class="font-label-xs text-label-xs text-on-surface-variant">TOTAL EARNINGS</p>
<span class="material-symbols-outlined text-primary-container text-lg">payments</span>
</div>
<div class="mt-4">
<h3 class="font-display text-headline-lg text-text-primary">$<?php echo format_usd_amount($totalEarnings); ?></h3>
<p class="text-xs text-on-surface-variant/60 mt-1">Platform Revenue Flow</p>
</div>
</div>
<div class="glass-panel p-6 rounded-xl flex flex-col justify-between">
<div class="flex justify-between items-start">
<p class="font-label-xs text-label-xs text-on-surface-variant">ACTIVE INVESTMENTS</p>
<span class="text-on-surface-variant text-[10px] font-bold uppercase">Stable</span>
</div>
<div class="mt-4">
<h3 class="font-display text-headline-lg text-text-primary"><?php echo number_format($activeInv); ?></h3>
<p class="text-xs text-on-surface-variant/60 mt-1">Live Asset Allocation</p>
</div>
</div>
<div class="glass-panel p-6 rounded-xl flex flex-col justify-between border-l-2 border-l-critical bg-critical/5">
<div class="flex justify-between items-start">
<p class="font-label-xs text-label-xs text-critical">PENDING DEPOSITS</p>
<span class="material-symbols-outlined text-critical text-lg animate-pulse">priority_high</span>
</div>
<div class="mt-4">
<h3 class="font-display text-headline-lg text-text-primary">$<?php echo format_usd_amount($pendingDepositsSum); ?></h3>
<p class="text-xs text-critical/80 mt-1">Action Required<?php if ($pendingDepositsCount > 0): ?> · <?php echo (int) $pendingDepositsCount; ?> pending<?php endif; ?></p>
</div>
</div>
<div class="glass-panel p-6 rounded-xl flex flex-col justify-between">
<div class="flex justify-between items-start">
<p class="font-label-xs text-label-xs text-on-surface-variant">PENDING WITHDRAWALS</p>
<span class="material-symbols-outlined text-on-surface-variant text-lg">outbox</span>
</div>
<div class="mt-4">
<h3 class="font-display text-headline-lg text-text-primary"><?php echo number_format($pendingWithdrawalsCount); ?></h3>
<p class="text-xs text-on-surface-variant/60 mt-1">$<?php echo format_usd_amount($pendingWithdrawalsSum); ?> total volume</p>
</div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter mb-8 md:mb-10">
<div class="lg:col-span-2 glass-panel p-6 md:p-8 rounded-xl">
<div class="flex justify-between items-center mb-8">
<h4 class="font-headline-md text-headline-md text-text-primary">Investments per Plan</h4>
<a href="/dashboard/admin/plans" class="font-label-xs text-label-xs text-primary-container border border-primary-container/20 px-3 py-1 hover:bg-primary-container/10 transition-colors rounded">PLAN ANALYTICS</a>
</div>
<?php if (empty($planDist)): ?>
<p class="text-sm text-on-surface-variant">No investments yet.</p>
<?php else: ?>
<div class="flex items-end justify-between h-48 gap-3 md:gap-4 px-2 md:px-4">
<?php foreach ($planDist as $p):
    $cnt = (int) $p['cnt'];
    $pct = $planMax > 0 ? max(12, (int) round($cnt / $planMax * 100)) : 12;
    $label = strtoupper(preg_replace('/\s+plan$/i', '', $p['name']));
?>
<div class="flex-1 flex flex-col items-center gap-3 min-w-0">
<div class="w-full bg-surface-container rounded-t relative group" style="height: <?php echo $pct; ?>%">
<div class="absolute inset-0 bg-primary-container/40 group-hover:bg-primary-container/60 transition-colors rounded-t"></div>
<span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold text-on-surface"><?php echo number_format($cnt); ?></span>
</div>
<span class="font-label-xs text-[10px] text-on-surface-variant truncate w-full text-center"><?php echo htmlspecialchars($label); ?></span>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<div class="glass-panel p-6 md:p-8 rounded-xl flex flex-col justify-center items-center text-center relative overflow-hidden">
<div class="w-16 h-16 rounded-full bg-primary-container/10 border border-primary-container/30 flex items-center justify-center mb-6 z-10">
<span class="material-symbols-outlined text-primary-container text-3xl" style="font-variation-settings: 'FILL' 1;">smart_toy</span>
</div>
<h4 class="font-headline-md text-headline-md text-text-primary z-10">AI Bot Performance</h4>
<p class="text-on-surface-variant text-sm mt-2 mb-6 z-10">Active Neural Network Arbitrage</p>
<div class="z-10">
<span class="font-display text-4xl text-primary-container">2.4%</span>
<p class="font-label-sm text-label-sm text-success mt-1 tracking-widest">AVG. DAILY ROI</p>
</div>
<div class="mt-8 pt-6 border-t border-low w-full z-10">
<div class="flex justify-between text-xs font-bold">
<span class="text-on-surface-variant">UPTIME</span>
<span class="text-primary-container">99.98%</span>
</div>
</div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
<div class="lg:col-span-2 glass-panel rounded-xl overflow-hidden">
<div class="px-6 md:px-8 py-6 border-b border-low flex flex-wrap justify-between items-center gap-3">
<h4 class="font-headline-md text-headline-md text-text-primary">Pending Deposits</h4>
<div class="flex items-center gap-3">
<span class="bg-critical/10 text-critical px-3 py-1 rounded text-[10px] font-bold uppercase">Manual Verification Required</span>
<a href="/dashboard/admin/transactions" class="font-label-xs text-label-xs text-primary-container hover:underline">View All</a>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left min-w-[640px]">
<thead class="bg-surface-container/30">
<tr>
<th class="px-6 md:px-8 py-4 font-label-xs text-label-xs text-on-surface-variant">USER</th>
<th class="px-6 md:px-8 py-4 font-label-xs text-label-xs text-on-surface-variant">AMOUNT</th>
<th class="px-6 md:px-8 py-4 font-label-xs text-label-xs text-on-surface-variant">METHOD</th>
<th class="px-6 md:px-8 py-4 font-label-xs text-label-xs text-on-surface-variant">STATUS</th>
<th class="px-6 md:px-8 py-4 font-label-xs text-label-xs text-on-surface-variant text-right">ACTIONS</th>
</tr>
</thead>
<tbody class="divide-y divide-low">
<?php foreach ($pendingList as $tx):
    $cu = strtoupper($tx['currency']);
    $usdAmt = isset($tx['amount_usd']) && $tx['amount_usd'] !== null ? (float) $tx['amount_usd'] : null;
    $coinAmt = (float) $tx['amount'];
    $initials = strtoupper(substr(preg_replace('/\s+/', '', $tx['name'] ?: 'U'), 0, 2));
?>
<tr class="hover:bg-bg-subtle transition-colors group">
<td class="px-6 md:px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center text-[10px] font-bold shrink-0"><?php echo htmlspecialchars($initials); ?></div>
<span class="text-sm font-bold"><?php echo htmlspecialchars($tx['name'] ?: 'User'); ?></span>
</div>
</td>
<td class="px-6 md:px-8 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold">$<?php echo format_usd_amount($usdAmt !== null ? $usdAmt : $coinAmt); ?></span>
<span class="text-[10px] text-on-surface-variant"><?php echo ($coinAmt >= 1 ? number_format($coinAmt, 4) : number_format($coinAmt, 6)) . ' ' . htmlspecialchars($tx['currency']); ?></span>
</div>
</td>
<td class="px-6 md:px-8 py-5">
<span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-on-surface uppercase border border-low px-2 py-0.5 rounded">
<span class="w-2 h-2 rounded-full <?php echo $coinDot($cu); ?>"></span> <?php echo htmlspecialchars($cu); ?>
</span>
</td>
<td class="px-6 md:px-8 py-5">
<span class="text-[10px] font-bold text-primary-container uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
</td>
<td class="px-6 md:px-8 py-5 text-right">
<div class="relative inline-block">
<button type="button" class="pd-actions-btn material-symbols-outlined text-on-surface-variant hover:text-primary-container transition-colors p-1" aria-label="Actions">more_vert</button>
<div class="pd-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-surface-container-high border border-low rounded-lg shadow-lg z-20 min-w-[110px]">
<button type="button" class="pd-action-approve block w-full text-left px-3 py-2 text-sm text-primary-container hover:bg-bg-subtle" data-tx-id="<?php echo (int) $tx['id']; ?>">Approve</button>
<button type="button" class="pd-action-reject block w-full text-left px-3 py-2 text-sm text-critical hover:bg-bg-subtle" data-tx-id="<?php echo (int) $tx['id']; ?>">Reject</button>
</div>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($pendingList)): ?>
<tr><td class="px-8 py-10 text-center text-on-surface-variant" colspan="5">No pending deposits.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<div class="glass-panel rounded-xl flex flex-col min-h-[320px]">
<div class="px-6 md:px-8 py-6 border-b border-low">
<h4 class="font-headline-md text-headline-md text-text-primary">Recent Activity</h4>
</div>
<div class="flex-1 overflow-y-auto p-6 md:p-8 admin-scrollbar">
<?php if (empty($recentActivity)): ?>
<p class="text-sm text-on-surface-variant">No recent activity.</p>
<?php else: ?>
<div class="space-y-8 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-low">
<?php
$actMeta = [
    'registration' => ['title' => 'New Registration', 'border' => 'border-primary-container', 'dot' => 'bg-primary-container'],
    'withdrawal' => ['title' => 'Withdrawal Request Initiated', 'border' => 'border-critical', 'dot' => 'bg-critical'],
    'deposit' => ['title' => 'Deposit Confirmation', 'border' => 'border-success', 'dot' => 'bg-success'],
    'plan_activated' => ['title' => 'Plan Activated', 'border' => 'border-primary-container', 'dot' => 'bg-primary-container'],
];
foreach ($recentActivity as $i => $a):
    $type = $a['type'];
    $meta = $actMeta[$type] ?? ['title' => ucfirst($type), 'border' => 'border-low', 'dot' => 'bg-on-surface-variant'];
    $name = $a['name'] ?: 'User';
    if ($type === 'registration') {
        $desc = 'User: ' . $name . ' joined the platform.';
    } elseif ($type === 'withdrawal') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float) $a['amount_usd'] : (float) $a['amount'];
        $desc = 'Amount: $' . format_usd_amount($amt);
    } elseif ($type === 'deposit') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float) $a['amount_usd'] : (float) $a['amount'];
        $desc = $name . ' deposited $' . format_usd_amount($amt);
    } elseif ($type === 'plan_activated') {
        $desc = 'User: ' . $name . ' ($' . format_usd_amount($a['amount']) . ' · ' . ($a['plan_name'] ?? 'Plan') . ')';
    } else {
        $desc = $name;
    }
    $opacity = $i >= 3 ? ' opacity-60' : '';
?>
<div class="relative pl-8<?php echo $opacity; ?>">
<div class="absolute left-0 top-1 w-[22px] h-[22px] rounded-full bg-surface border-2 <?php echo $meta['border']; ?> z-10 flex items-center justify-center">
<div class="w-1.5 h-1.5 rounded-full <?php echo $meta['dot']; ?>"></div>
</div>
<p class="text-sm font-bold text-on-surface"><?php echo htmlspecialchars($meta['title']); ?></p>
<p class="text-xs text-on-surface-variant mt-1"><?php echo htmlspecialchars($desc); ?></p>
<p class="text-[10px] font-bold text-primary-container mt-2 uppercase"><?php echo strtoupper(time_ago($a['created_at'])); ?></p>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
<div class="p-6 mt-auto border-t border-low">
<a href="/dashboard/admin/activity-log" class="w-full py-3 bg-surface-container hover:bg-surface-container-high text-on-surface text-xs font-bold uppercase tracking-widest border border-low transition-colors flex items-center justify-center gap-2 rounded-lg">
<span class="material-symbols-outlined text-sm">terminal</span>
View System Log
</a>
</div>
</div>
</div>

<a href="/dashboard/admin/transactions" class="fixed bottom-8 right-8 w-14 h-14 bg-primary-container text-on-primary rounded-full shadow-lg shadow-primary-container/20 flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-[100] group" title="Quick transactions">
<span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">add</span>
<span class="absolute right-16 bg-surface-container-high text-on-surface px-4 py-2 rounded text-xs font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity border border-low pointer-events-none hidden md:block">Review Transactions</span>
</a>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  function closePdDropdowns() {
    document.querySelectorAll('.pd-actions-dropdown').forEach(function (d) { d.classList.add('hidden'); });
  }
  document.querySelectorAll('.pd-actions-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      closePdDropdowns();
      var dd = btn.nextElementSibling;
      if (dd) dd.classList.toggle('hidden');
    });
  });
  document.addEventListener('click', closePdDropdowns);
  function doPdAction(txId, action) {
    fetch('/api/admin/transactions.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ action: action, transaction_id: parseInt(txId, 10) })
    }).then(function (r) { return r.json(); }).then(function (res) {
      if (res.success) window.location.reload();
      else alert(res.error || 'Failed to update transaction');
    }).catch(function () { alert('Request failed'); });
  }
  document.querySelectorAll('.pd-action-approve').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      closePdDropdowns();
      doPdAction(btn.getAttribute('data-tx-id'), 'approve');
    });
  });
  document.querySelectorAll('.pd-action-reject').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      closePdDropdowns();
      doPdAction(btn.getAttribute('data-tx-id'), 'reject');
    });
  });
});
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>

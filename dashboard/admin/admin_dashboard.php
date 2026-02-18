<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'dashboard';
$siteName = get_site_name();

$totalUsers = 0;
$totalEarnings = 0;
$activeInv = 0;
$pendingDepositsCount = 0;
$pendingDepositsSum = 0;
$totalDeposits = 0;
$planDist = [];
$pendingList = [];
$recentActivity = [];

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $r = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $totalUsers = (int) $r;
    $r = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'payout' AND status = 'completed'")->fetchColumn();
    $totalEarnings = (float) $r;
    $r = $pdo->query("SELECT COUNT(*) FROM user_investments WHERE status = 'active'")->fetchColumn();
    $activeInv = (int) $r;
    $stmt = $pdo->query("SELECT COUNT(*), COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'deposit' AND status = 'pending'");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $pendingDepositsCount = (int) $row[0];
    $pendingDepositsSum = (float) $row[1];
    $r = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE type = 'deposit' AND status = 'completed'")->fetchColumn();
    $totalDeposits = (float) $r;
    $stmt = $pdo->query('SELECT p.name, ui.plan_id, COUNT(*) AS cnt, COALESCE(SUM(ui.amount), 0) AS cap FROM user_investments ui JOIN plans p ON p.id = ui.plan_id GROUP BY ui.plan_id, p.name');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $planDist[] = $row;
    }
    $txCols = 't.id, t.amount, t.currency, t.status, u.name, u.id AS user_id';
    try { if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) $txCols .= ', t.amount_usd'; } catch (Throwable $e) {}
    $stmt = $pdo->query("SELECT $txCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' AND t.status = 'pending' ORDER BY t.created_at DESC LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pendingList[] = $row;
    }
    // Recent activity: registrations, withdrawals, deposits, plan activations
    $activities = [];
    $stmt = $pdo->query("SELECT 'registration' AS type, u.name, u.created_at, NULL AS amount, NULL AS plan_name FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $actCols = 'u.name, t.created_at, t.amount, NULL AS plan_name';
    try { if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) $actCols = 'u.name, t.created_at, t.amount, t.amount_usd, NULL AS plan_name'; } catch (Throwable $e) {}
    $stmt = $pdo->query("SELECT 'withdrawal' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'withdrawal' ORDER BY t.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'deposit' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' AND t.status = 'completed' ORDER BY t.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'plan_activated' AS type, u.name, ui.created_at, ui.amount, p.name AS plan_name FROM user_investments ui JOIN users u ON u.id = ui.user_id JOIN plans p ON p.id = ui.plan_id ORDER BY ui.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    usort($activities, function ($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $recentActivity = array_slice($activities, 0, 15);
} catch (Throwable $e) {
    // DB unavailable - use defaults
}
$planMax = 1;
foreach ($planDist as $p) { if ((int)$p['cnt'] > $planMax) $planMax = (int)$p['cnt']; }
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> Admin Command Center</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f9bd0b",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8">
<!-- Top Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
<!-- Card 1 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Users</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center">+12% <span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold"><?php echo number_format($totalUsers); ?></p>
</div>
<!-- Card 2 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Earnings</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center"><span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold">$<?php echo number_format($totalEarnings); ?></p>
</div>
<!-- Card 3 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Active Inv.</span>
<span class="text-slate-400 text-[10px] font-bold">Stable</span>
</div>
<p class="text-2xl font-bold"><?php echo number_format($activeInv); ?></p>
</div>
<!-- Card 4 -->
<div class="bg-primary/5 dark:bg-primary/10 p-6 rounded-xl border border-primary/20 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-primary font-bold text-xs uppercase tracking-wider">Pending Deposits</span>
<span class="bg-primary text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">ACTION</span>
</div>
<p class="text-2xl font-bold text-primary">$<?php echo number_format($pendingDepositsSum); ?></p>
</div>
<!-- Card 5 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Deposits</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center"><span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold">$<?php echo number_format($totalDeposits); ?></p>
</div>
</div>
<!-- Mid Section - Analytics -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
<!-- Platform Growth Chart -->
<div class="lg:col-span-2 bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-6">
<div>
<h3 class="font-bold text-lg">Platform Growth vs. Payouts</h3>
<p class="text-xs text-slate-500">Historical performance tracking over the last 30 days</p>
</div>
<div class="flex gap-4">
<div class="flex items-center gap-1.5">
<div class="w-3 h-3 rounded-full bg-primary"></div>
<span class="text-[10px] font-bold text-slate-500">GROWTH</span>
</div>
<div class="flex items-center gap-1.5">
<div class="w-3 h-3 rounded-full bg-slate-300"></div>
<span class="text-[10px] font-bold text-slate-500">PAYOUTS</span>
</div>
</div>
</div>
<!-- Chart Placeholder -->
<div class="h-64 flex items-end justify-between gap-1 mt-4 relative">
<div class="absolute inset-0 flex flex-col justify-between">
<div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
<div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
<div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
<div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
<div class="border-b border-slate-100 dark:border-white/5 w-full h-0"></div>
</div>
<!-- Bars Representation -->
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[40%]"></div>
<div class="w-full bg-primary rounded-t h-[60%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[35%]"></div>
<div class="w-full bg-primary rounded-t h-[55%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[50%]"></div>
<div class="w-full bg-primary rounded-t h-[70%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[45%]"></div>
<div class="w-full bg-primary rounded-t h-[80%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[55%]"></div>
<div class="w-full bg-primary rounded-t h-[65%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[60%]"></div>
<div class="w-full bg-primary rounded-t h-[90%]"></div>
</div>
<div class="flex-1 flex flex-col justify-end items-center gap-1 z-0">
<div class="w-full bg-primary/20 rounded-t h-[50%]"></div>
<div class="w-full bg-primary rounded-t h-[75%]"></div>
</div>
</div>
</div>
<!-- Plan Distribution -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<h3 class="font-bold text-lg mb-6">Investments per Plan</h3>
<div class="space-y-6">
<?php foreach ($planDist as $p): $pct = $planMax > 0 ? (int)((int)$p['cnt'] / $planMax * 100) : 0; ?>
<div>
<div class="flex justify-between items-center mb-2">
<span class="text-sm font-medium"><?php echo htmlspecialchars($p['name']); ?> Plan</span>
<span class="text-sm font-bold"><?php echo number_format((int)$p['cnt']); ?></span>
</div>
<div class="w-full bg-primary/10 h-2 rounded-full">
<div class="bg-primary h-full rounded-full" style="width:<?php echo $pct; ?>%"></div>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($planDist)): ?><p class="text-sm text-slate-500">No investments yet.</p><?php endif; ?>
</div>
<!-- Bot Widget -->
<div class="mt-10 p-4 bg-primary rounded-xl text-white">
<div class="flex items-center gap-2 mb-2">
<span class="material-icons text-lg">smart_toy</span>
<p class="text-xs font-bold uppercase tracking-widest">AI Bot Performance</p>
</div>
<div class="flex items-baseline gap-2">
<span class="text-2xl font-bold">2.4%</span>
<span class="text-[10px] opacity-80 uppercase">Avg. Daily ROI</span>
</div>
</div>
</div>
</div>
<!-- Bottom Grid: Table & Activity -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
<!-- Pending Deposits Table -->
<div class="xl:col-span-2 bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="p-6 border-b border-primary/10 flex items-center justify-between">
<h3 class="font-bold text-lg">Pending Deposits</h3>
<button class="text-xs font-bold text-primary hover:underline">View All</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-background-light dark:bg-white/5 border-b border-primary/10">
<tr>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">User</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Amount</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Method</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Status</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-primary/5">
<?php
$coinLogos = ['BTC'=>'https://assets.coingecko.com/coins/images/1/large/bitcoin.png','ETH'=>'https://assets.coingecko.com/coins/images/279/large/ethereum.png','USDT'=>'https://assets.coingecko.com/coins/images/325/large/Tether.png'];
foreach ($pendingList as $tx):
  $cu = strtoupper($tx['currency']);
  $logo = $coinLogos[$cu] ?? null;
?>
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden flex items-center justify-center text-slate-500 font-bold text-xs"><?php echo strtoupper(substr($tx['name'] ?: 'U', 0, 1)); ?></div>
<div>
<p class="text-sm font-bold"><?php echo htmlspecialchars($tx['name'] ?: 'User'); ?></p>
<p class="text-[10px] text-slate-500">ID: BB-<?php echo (int)$tx['user_id']; ?></p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm">
<?php $usdAmt = isset($tx['amount_usd']) && $tx['amount_usd'] !== null ? (float)$tx['amount_usd'] : null; $coinAmt = (float)$tx['amount']; ?>
<div class="font-bold"><?php echo $usdAmt !== null ? '$' . number_format($usdAmt, 2) . ' USD' : '$' . number_format($coinAmt, 2); ?></div>
<?php if ($usdAmt !== null): ?><div class="text-xs text-slate-500"><?php echo ($coinAmt >= 1 ? number_format($coinAmt, 4) : number_format($coinAmt, 6)) . ' ' . htmlspecialchars($tx['currency']); ?></div><?php endif; ?>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5">
<?php if ($logo): ?><img alt="<?php echo $cu; ?>" class="w-5 h-5" src="<?php echo htmlspecialchars($logo); ?>"/><?php endif; ?>
<span class="text-xs font-medium"><?php echo htmlspecialchars($tx['currency']); ?></span>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold rounded-full uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<button class="px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/90">APPROVE</button>
<button class="px-3 py-1.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">REJECT</button>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($pendingList)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="5">No pending deposits.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<!-- Recent Activity Log -->
<div class="bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm flex flex-col">
<div class="p-6 border-b border-primary/10">
<h3 class="font-bold text-lg">Recent Activity</h3>
</div>
<div class="p-6 flex-1 space-y-6">
<?php
$actTitles = ['registration' => 'New Registration', 'withdrawal' => 'Withdrawal Request', 'deposit' => 'Deposit', 'plan_activated' => 'Plan Activated'];
$actColors = ['registration' => 'bg-emerald-500', 'withdrawal' => 'bg-primary', 'deposit' => 'bg-blue-500', 'plan_activated' => 'bg-slate-300'];
$siteNameAct = $siteName;
foreach ($recentActivity as $i => $a):
    $type = $a['type'];
    $title = $actTitles[$type] ?? ucfirst($type);
    $color = $actColors[$type] ?? 'bg-slate-300';
    $name = $a['name'] ?: 'User';
    if ($type === 'registration') $desc = $name . ' just joined ' . $siteNameAct . '.';
    elseif ($type === 'withdrawal') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float)$a['amount_usd'] : (float)$a['amount'];
        $desc = $name . ' requested a $' . number_format($amt, 0) . ' payout.';
    } elseif ($type === 'deposit') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float)$a['amount_usd'] : (float)$a['amount'];
        $desc = $name . ' deposited $' . number_format($amt, 0) . '.';
    }
    elseif ($type === 'plan_activated') $desc = ($a['plan_name'] ?? 'Plan') . ' started for ' . $name . ' ($' . number_format((float)$a['amount'], 0) . ').';
    else $desc = $name;
?>
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full <?php echo $color; ?> mt-1.5"></div>
<?php if ($i < count($recentActivity) - 1): ?><div class="absolute top-4 left-1.25 w-px h-full bg-slate-200 dark:bg-white/5"></div><?php endif; ?>
</div>
<div class="flex-1">
<p class="text-sm font-medium"><?php echo htmlspecialchars($title); ?></p>
<p class="text-xs text-slate-500 mb-1"><?php echo htmlspecialchars($desc); ?></p>
<p class="text-[10px] text-primary font-bold"><?php echo strtoupper(time_ago($a['created_at'])); ?></p>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($recentActivity)): ?>
<p class="text-sm text-slate-500">No recent activity.</p>
<?php endif; ?>
</div>
<div class="p-4 bg-background-light dark:bg-white/5 text-center">
<button class="text-xs font-bold text-slate-500 hover:text-primary transition-colors uppercase">View System Log</button>
</div>
</div>
</div>
</div>
</main>
</div>
<script src="/js/app.js"></script>
</body></html>
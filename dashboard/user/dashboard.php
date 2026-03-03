<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'dashboard';
$siteName = get_site_name();
$userBalance = 0;
$userBalanceUpdatedAt = null;
$btcAmount = 0;
$walletBalances = [];
$highestCoin = 'BTC';
$highestAmount = 0;
$highestCoinLogo = 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png';
$totalProfit = 0;
$activeCapital = 0;
$dailyEarning = 0;
$referralBonus = 0;
$referralBonusLast24h = 0;
$activeInvestments = [];
$chartData = [];
$period = $_GET['period'] ?? '1M';
$days = match($period) {
    '1D' => 1,
    '1W' => 7,
    '1M' => 30,
    '1Y' => 365,
    default => 30
};
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];

    // Prefer cached USD balance from users table (stable, consistent display)
    $hasCachedUsd = false;
    try {
        $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        $hasCachedUsd = $bc && $bc->rowCount() > 0;
    } catch (Throwable $e) {}
    if ($hasCachedUsd) {
        $s = $pdo->prepare('SELECT last_balance_usd, last_balance_usd_updated_at FROM users WHERE id = ?');
        $s->execute([(int)$userId]);
        $rr = $s->fetch(PDO::FETCH_ASSOC);
        if ($rr) {
            $userBalance = (float) ($rr['last_balance_usd'] ?? 0);
            $userBalanceUpdatedAt = $rr['last_balance_usd_updated_at'] ?? null;
        }
    }
    $coinLogos = ['BTC'=>'https://assets.coingecko.com/coins/images/1/large/bitcoin.png','ETH'=>'https://assets.coingecko.com/coins/images/279/large/ethereum.png','USDT'=>'https://assets.coingecko.com/coins/images/325/large/Tether.png','USDC'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png','BUSD'=>'https://assets.coingecko.com/coins/images/9576/large/BUSD.png','XRP'=>'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png','SOL'=>'https://assets.coingecko.com/coins/images/4128/large/solana.png','BNB'=>'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png','USD'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png'];
    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    $stableTotalUsd = 0.0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amt = (float)$row['amount'];
        $currency = strtoupper($row['currency']);
        // No placeholder pricing: only stable assets have deterministic USD value.
        $usdVal = in_array($currency, ['USDT','USDC','USD','BUSD','DAI'], true) ? $amt : null;
        if ($usdVal !== null) {
            $stableTotalUsd += $usdVal;
        }
        $walletBalances[] = ['currency' => $currency, 'amount' => $amt, 'usd_value' => $usdVal];
    }
    // If cached column doesn't exist yet, fall back to stable-only total.
    if (!$userBalance && $stableTotalUsd > 0) {
        $userBalance = $stableTotalUsd;
    }
    usort($walletBalances, function($a, $b) {
        return ((float)($b['usd_value'] ?? 0) <=> (float)($a['usd_value'] ?? 0));
    });
    $topCoins = array_slice(array_filter($walletBalances, function($b) { return ((float)($b['usd_value'] ?? 0)) > 0; }), 0, 3);
    $extraCoinCount = max(0, count(array_filter($walletBalances, function($b) { return ((float)($b['usd_value'] ?? 0)) > 0; })) - 3);
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ? AND type = 'payout' AND status = 'completed'");
    $r->execute([$userId]); $totalProfit = (float)$r->fetchColumn();
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM user_investments WHERE user_id = ? AND status = 'active'");
    $r->execute([$userId]); $activeCapital = (float)$r->fetchColumn();
    $stmt = $pdo->prepare('SELECT ui.id, ui.plan_id, ui.amount, ui.start_date, ui.status, ui.duration_days as investment_duration_days, p.name as plan_name, p.yield_min, p.yield_max, p.duration_days as plan_duration_days FROM user_investments ui JOIN plans p ON p.id = ui.plan_id WHERE ui.user_id = ? AND ui.status = ? ORDER BY ui.created_at DESC LIMIT 5');
    $stmt->execute([$userId, 'active']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $activeInvestments[] = $row;
        $yieldMin = (float)($row['yield_min'] ?? 0);
        $yieldMax = (float)($row['yield_max'] ?? 0);
        $avgYield = ($yieldMin + $yieldMax) / 2;
        if ($avgYield <= 0) $avgYield = $yieldMin;
        $dailyEarning += (float)$row['amount'] * ($avgYield / 100);
    }
    try {
        $tbl = $pdo->query("SHOW TABLES LIKE 'referral_earnings'");
        if ($tbl && $tbl->rowCount() > 0) {
            $r = $pdo->prepare('SELECT COALESCE(SUM(amount_usd), 0) FROM referral_earnings WHERE referrer_user_id = ?');
            $r->execute([$userId]); $referralBonus = (float)$r->fetchColumn();
            $r = $pdo->prepare('SELECT COALESCE(SUM(amount_usd), 0) FROM referral_earnings WHERE referrer_user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)');
            $r->execute([$userId]); $referralBonusLast24h = (float)$r->fetchColumn();
        }
    } catch (Throwable $e) {}
    if ($referralBonus == 0) {
        $r = $pdo->prepare("SELECT COALESCE(SUM(COALESCE(amount_usd, amount)), 0) FROM transactions WHERE user_id = ? AND type = 'referral_bonus' AND status = 'completed'");
        $r->execute([$userId]); $referralBonus = (float)$r->fetchColumn();
        $r = $pdo->prepare("SELECT COALESCE(SUM(COALESCE(amount_usd, amount)), 0) FROM transactions WHERE user_id = ? AND type = 'referral_bonus' AND status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $r->execute([$userId]); $referralBonusLast24h = (float)$r->fetchColumn();
    }
    // activeInvestments and dailyEarning already populated above
    // Fetch transaction data for chart based on selected period
    $stmt = $pdo->prepare("SELECT DATE(created_at) as date, type, SUM(amount) as total FROM transactions WHERE user_id = ? AND type IN ('deposit', 'withdrawal') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at), type ORDER BY date ASC");
    $stmt->execute([$userId, $days]);
    $dailyData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['date'];
        if (!isset($dailyData[$date])) $dailyData[$date] = ['deposit' => 0, 'withdrawal' => 0];
        $dailyData[$date][$row['type']] = (float)$row['total'];
    }
    // Build cumulative chart data
    $cumulative = 0;
    foreach ($dailyData as $date => $amounts) {
        $cumulative += $amounts['deposit'] - $amounts['withdrawal'];
        $chartData[] = ['date' => $date, 'value' => $cumulative];
    }
} catch (Throwable $e) { }
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | AI Trading Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ffc105",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                    },
                    fontFamily: {
                        "display": ["Space Grotesk"]
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
<style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 193, 5, 0.1);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ffc10544; border-radius: 10px; }
        .trading-graph-bg {
            background: linear-gradient(180deg, rgba(255,193,5,0.1) 0%, rgba(255,193,5,0) 100%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<!-- Dashboard Grid -->
<div class="grid grid-cols-12 gap-6">
<!-- Row 1: Total Estimated Balance (60%) | Live AI Trades (40%) -->
<div class="col-span-12 lg:col-span-7 relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-800 to-black p-6 text-white shadow-2xl">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
<div class="relative z-10">
<div>
<p class="text-slate-400 text-sm font-medium mb-1">USD Balance</p>
<h1 class="text-5xl font-bold tracking-tight">$<?php echo format_usd_amount($userBalance); ?> <span class="text-lg font-normal text-slate-400 ml-2">USD</span></h1>
<p class="text-primary mt-2 flex items-center gap-1 flex-wrap">
<?php
$fmtCoinAmt = function($amt) {
    if ($amt <= 0) return '0';
    if ($amt >= 1000) return (string) round($amt, 0);
    if ($amt >= 1) return format_usd_amount($amt);
    if ($amt >= 0.01) return format_usd_amount($amt);
    return rtrim(rtrim(number_format($amt, 6), '0'), '.');
};
$parts = [];
foreach ($topCoins as $c) {
    $logo = $coinLogos[$c['currency']] ?? 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png';
    $amt = $c['amount'] > 0 ? $fmtCoinAmt($c['amount']) : '0';
    $parts[] = '<img class="w-5 h-5" src="' . htmlspecialchars($logo) . '" alt="' . htmlspecialchars($c['currency']) . '"/>' . $amt . ' ' . htmlspecialchars($c['currency']);
}
echo implode(' <span class="text-white/60 mx-1">|</span> ', $parts);
if ($extraCoinCount > 0) echo ' <span class="text-white/80 font-bold ml-1">+'.$extraCoinCount.'</span>';
?>
</p>
<div class="flex flex-wrap gap-2 sm:gap-3 mt-4">
<button type="button" id="deposit-btn-dash" class="bg-primary hover:bg-primary/90 text-black px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-bold flex items-center gap-1.5 sm:gap-2 transition-all text-sm">
<span class="material-icons text-xs sm:text-sm">add</span> Deposit
</button>
<a href="/dashboard/user/transactions" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-bold flex items-center gap-1.5 sm:gap-2 transition-all backdrop-blur-sm text-sm">
<span class="material-icons text-xs sm:text-sm">history</span> Transactions
</a>
</div>
</div>
<div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 border-t border-white/10 pt-6">
<div>
<p class="text-slate-400 text-xs mb-1">Total Profit</p>
<p class="font-bold text-emerald-400">$<?php echo format_usd_amount($totalProfit); ?></p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Active Capital</p>
<p class="font-bold">$<?php echo format_usd_amount($activeCapital); ?></p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Daily Earning</p>
<p class="font-bold text-primary">$<?php echo format_usd_amount($dailyEarning); ?></p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Referral Bonus (earned)</p>
<p class="font-bold text-amber-400">$<?php echo format_usd_amount($referralBonus); ?></p>
<?php if ($referralBonus > 0): ?><p class="text-[10px] text-slate-500 mt-0.5">Last 24h: $<?php echo format_usd_amount($referralBonusLast24h); ?></p><?php endif; ?>
</div>
</div>
</div>
</div>
<!-- Live AI Trades (40%) -->
<div class="col-span-12 lg:col-span-5 bg-white dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col h-[320px]">
<div class="p-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
<h3 class="font-bold flex items-center gap-2">
<span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Live AI Trades
                        </h3>
<span class="text-xs font-bold text-primary">SCANNING...</span>
</div>
<div class="flex-1 overflow-y-auto custom-scrollbar p-4 flex flex-col gap-3">
<!-- Trade 1 -->
<div class="live-trade-card flex-1 min-h-0 flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="trade-icon-container w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
<span class="trade-icon material-icons-round text-emerald-500 text-sm">trending_up</span>
</div>
<div>
<p class="trade-pair text-xs font-bold">BTC/USDT Long</p>
<p class="trade-time text-[10px] text-slate-400">2 mins ago</p>
</div>
</div>
<span class="live-trade-amount text-sm font-bold text-emerald-500">+$245.00</span>
</div>
<!-- Trade 2 -->
<div class="live-trade-card flex-1 min-h-0 flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="trade-icon-container w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
<span class="trade-icon material-icons-round text-red-500 text-sm">trending_down</span>
</div>
<div>
<p class="trade-pair text-xs font-bold">ETH/USDT Short</p>
<p class="trade-time text-[10px] text-slate-400">8 mins ago</p>
</div>
</div>
<span class="live-trade-amount text-sm font-bold text-red-500">-$12.40</span>
</div>
<!-- Trade 3 -->
<div class="live-trade-card flex-1 min-h-0 flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="trade-icon-container w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
<span class="trade-icon material-icons-round text-emerald-500 text-sm">trending_up</span>
</div>
<div>
<p class="trade-pair text-xs font-bold">SOL/USDT Long</p>
<p class="trade-time text-[10px] text-slate-400">15 mins ago</p>
</div>
</div>
<span class="live-trade-amount text-sm font-bold text-emerald-500">+$89.15</span>
</div>
</div>
</div>
<!-- Performance Growth (50%) -->
<div class="col-span-12 lg:col-span-6 bg-white dark:bg-white/5 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-white/5">
<div class="flex justify-between items-center mb-6">
<h3 class="text-lg font-bold">Performance Growth</h3>
<div class="flex bg-slate-100 dark:bg-white/5 p-1 rounded-lg">
<button type="button" data-period="1D" class="chart-filter-btn px-4 py-1.5 rounded-md text-xs font-bold hover:bg-white dark:hover:bg-white/10 transition-all <?php echo $period === '1D' ? 'bg-white dark:bg-white/10 shadow-sm text-black dark:text-white' : 'text-slate-500'; ?>">1D</button>
<button type="button" data-period="1W" class="chart-filter-btn px-4 py-1.5 rounded-md text-xs font-bold hover:bg-white dark:hover:bg-white/10 transition-all <?php echo $period === '1W' ? 'bg-white dark:bg-white/10 shadow-sm text-black dark:text-white' : 'text-slate-500'; ?>">1W</button>
<button type="button" data-period="1M" class="chart-filter-btn px-4 py-1.5 rounded-md text-xs font-bold hover:bg-white dark:hover:bg-white/10 transition-all <?php echo $period === '1M' ? 'bg-white dark:bg-white/10 shadow-sm text-black dark:text-white' : 'text-slate-500'; ?>">1M</button>
<button type="button" data-period="1Y" class="chart-filter-btn px-4 py-1.5 rounded-md text-xs font-bold hover:bg-white dark:hover:bg-white/10 transition-all <?php echo $period === '1Y' ? 'bg-white dark:bg-white/10 shadow-sm text-black dark:text-white' : 'text-slate-500'; ?>">1Y</button>
</div>
</div>
<div class="h-40 relative flex flex-col" id="performance-chart-wrapper">
<div class="flex-1 relative min-h-0" id="performance-chart">
<?php
$dates = [];
if (!empty($chartData)) {
    $maxVal = max(array_column($chartData, 'value'));
    $minVal = min(array_column($chartData, 'value'));
    $range = $maxVal - $minVal;
    if ($range == 0) $range = 1;
    $points = [];
    $dates = [];
    $count = count($chartData);
    foreach ($chartData as $i => $point) {
        $x = $count > 1 ? ($i / ($count - 1)) * 100 : 50;
        $y = 100 - (($point['value'] - $minVal) / $range) * 80;
        $points[] = $x . ',' . $y;
        if ($i === 0 || $i === floor($count / 4) || $i === floor($count / 2) || $i === floor($count * 3 / 4) || $i === $count - 1) {
            $dates[] = date('M j', strtotime($point['date']));
        }
    }
    $pathD = 'M' . implode(' L', $points);
    $areaD = $pathD . ' L' . ($count > 1 ? 100 : 50) . ',100 L0,100 Z';
?>
<div class="absolute inset-0 trading-graph-bg rounded-lg"></div>
<svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
<defs>
<linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%">
<stop offset="0%" style="stop-color:#ffc105;stop-opacity:0.2"></stop>
<stop offset="100%" style="stop-color:#ffc105;stop-opacity:0"></stop>
</linearGradient>
</defs>
<path d="<?php echo htmlspecialchars($areaD); ?>" fill="url(#chartGradient)"></path>
<path d="<?php echo htmlspecialchars($pathD); ?>" fill="none" stroke="#ffc105" stroke-width="2"></path>
</svg>
<?php } else { ?>
<div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No data available</div>
<?php } ?>
</div>
<div class="flex justify-between mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest" id="chart-dates">
<?php if (!empty($chartData) && isset($dates)) { foreach ($dates as $d): ?><span><?php echo htmlspecialchars($d); ?></span><?php endforeach; } ?>
</div>
</div>
</div>
<!-- My Active Plans (50%) -->
<div class="col-span-12 lg:col-span-6 bg-white dark:bg-white/5 rounded-2xl p-6 border border-slate-100 dark:border-white/5">
<div class="flex justify-between items-center mb-6">
<h3 class="text-lg font-bold">My Active Plans</h3>
<a href="/dashboard/user/analytics" class="text-primary text-sm font-bold flex items-center gap-1 hover:underline">
                            View All <span class="material-icons-round text-sm">arrow_forward</span>
</a>
</div>
<div class="space-y-4">
<?php foreach ($activeInvestments as $inv):
    $startDate = new DateTime($inv['start_date']);
    $now = new DateTime();
    $daysElapsed = $now->diff($startDate)->days;
    $durationDays = (int)($inv['investment_duration_days'] ?? $inv['plan_duration_days'] ?? 30);
    $progress = min(100, ($daysElapsed / $durationDays) * 100);
    $avgYield = (($inv['yield_min'] ?? 0) + ($inv['yield_max'] ?? 0)) / 2;
    $roi = ($inv['amount'] * $avgYield / 100) * ($daysElapsed / 30);
?>
<div class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 dark:border-white/10 hover:border-primary/50 transition-all">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
<span class="material-icons-round text-primary">auto_graph</span>
</div>
<div>
<h4 class="font-bold"><?php echo htmlspecialchars($inv['plan_name']); ?></h4>
<p class="text-xs text-slate-400">Start Date: <?php echo date('M j, Y', strtotime($inv['start_date'])); ?></p>
</div>
</div>
<div class="w-48 text-right px-6">
<div class="flex justify-between text-[10px] font-bold mb-1">
<span>PROGRESS</span>
<span><?php echo number_format($progress, 0); ?>%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-white/10 h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full rounded-full" style="width:<?php echo min(100, $progress); ?>%"></div>
</div>
</div>
<div class="text-right">
<p class="text-sm font-bold">$<?php echo format_usd_amount($inv['amount']); ?></p>
<p class="text-xs text-emerald-500">+<?php echo number_format($avgYield, 1); ?>% ROI</p>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($activeInvestments)): ?>
<div class="text-center py-8 text-slate-500 text-sm">No active investments</div>
<?php endif; ?>
<!-- Add New -->
<a href="/dashboard/user/investment-plans" class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl text-slate-400 font-bold hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2">
<span class="material-icons-round">add_circle_outline</span>
                            Subscribe to New Investment Plan
                        </a>
</div>
</div>
</div>
</main>
</div>
<script src="/js/app.js"></script>
<script>window.BLOOMBIT_API_BASE = '';</script>
<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.BloombitCryptoPrices) {
        window.BloombitCryptoPrices.init(['bitcoin'], {
            refreshInterval: 300000
        });
    }
    
    // Live AI Trades Animation
    var tradeElements = document.querySelectorAll('.live-trade-amount');
    var pairs = ['BTC/USDT', 'ETH/USDT', 'SOL/USDT', 'BNB/USDT', 'ADA/USDT', 'DOT/USDT'];
    var directions = ['Long', 'Short'];
    
    function animateTradeAmount(el) {
        var current = parseFloat(el.textContent.replace(/[^0-9.-]/g, '')) || 0;
        var isPositive = (el.textContent || '').indexOf('+') !== -1;
        var change = (Math.random() * 200 - 100);
        var newVal = Math.max(0, current + change);
        var absVal = Math.abs(newVal);
        
        var colorClass = '';
        if (absVal < 50) colorClass = 'text-red-500';
        else if (absVal >= 100) colorClass = 'text-emerald-500';
        else colorClass = isPositive ? 'text-emerald-500' : 'text-red-500';
        
        el.className = 'text-sm font-bold ' + colorClass;
        el.textContent = (newVal >= 0 ? '+' : '-') + '$' + absVal.toFixed(2);
    }
    
    function updateTrade(el) {
        if (!el) return;
        var pairEl = el.querySelector('.trade-pair');
        var timeEl = el.querySelector('.trade-time');
        var iconEl = el.querySelector('.trade-icon');
        var amountEl = el.querySelector('.live-trade-amount');
        var iconContainer = el.querySelector('.trade-icon-container');
        if (!pairEl || !timeEl || !iconEl || !iconContainer) return;
        
        var pair = pairs[Math.floor(Math.random() * pairs.length)];
        var direction = directions[Math.floor(Math.random() * directions.length)];
        var isLong = direction === 'Long';
        var mins = Math.floor(Math.random() * 30) + 1;
        
        pairEl.textContent = pair + ' ' + direction;
        timeEl.textContent = mins + ' min' + (mins > 1 ? 's' : '') + ' ago';
        
        if (isLong) {
            iconContainer.className = 'w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center';
            iconEl.className = 'material-icons-round text-emerald-500 text-sm';
            iconEl.textContent = 'trending_up';
        } else {
            iconContainer.className = 'w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center';
            iconEl.className = 'material-icons-round text-red-500 text-sm';
            iconEl.textContent = 'trending_down';
        }
        
        if (amountEl) animateTradeAmount(amountEl);
    }
    
    var tradeCards = document.querySelectorAll('.live-trade-card');
    tradeCards.forEach(function(card, i) {
        // GTranslate can rewrite DOM nodes; re-query by index each tick to avoid stale references.
        setInterval(function () {
            var cards = document.querySelectorAll('.live-trade-card');
            var c = cards && cards.length > i ? cards[i] : null;
            if (!c) return;
            var amountEl = c.querySelector('.live-trade-amount');
            if (amountEl) animateTradeAmount(amountEl);
        }, 3000 + (i * 500));

        setInterval(function () {
            var cards = document.querySelectorAll('.live-trade-card');
            var c = cards && cards.length > i ? cards[i] : null;
            if (!c) return;
            updateTrade(c);
        }, 8000 + (i * 1000));
    });
    
    // Chart filter buttons - AJAX
    var chartContainer = document.getElementById('performance-chart');
    var chartDates = document.getElementById('chart-dates');
    var currentPeriod = '<?php echo htmlspecialchars($period); ?>';
    
    function updateChart(data) {
        if (!chartContainer) return;
        if (!data || data.length === 0) {
            chartContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No data available</div>';
            if (chartDates) chartDates.innerHTML = '';
            return;
        }
        var maxVal = Math.max.apply(null, data.map(function(d){ return d.value; }));
        var minVal = Math.min.apply(null, data.map(function(d){ return d.value; }));
        var range = maxVal - minVal;
        if (range === 0) range = 1;
        var count = data.length;
        var points = [];
        var dates = [];
        data.forEach(function(point, i) {
            var x = count > 1 ? (i / (count - 1)) * 100 : 50;
            var y = 100 - ((point.value - minVal) / range) * 80;
            points.push(x + ',' + y);
            if (i === 0 || i === Math.floor(count / 4) || i === Math.floor(count / 2) || i === Math.floor(count * 3 / 4) || i === count - 1) {
                dates.push(new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            }
        });
        var pathD = 'M' + points.join(' L');
        var areaD = pathD + ' L' + (count > 1 ? 100 : 50) + ',100 L0,100 Z';
        chartContainer.innerHTML = '<div class="absolute inset-0 trading-graph-bg rounded-lg"></div><svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100"><defs><linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%"><stop offset="0%" style="stop-color:#ffc105;stop-opacity:0.2"></stop><stop offset="100%" style="stop-color:#ffc105;stop-opacity:0"></stop></linearGradient></defs><path d="' + areaD + '" fill="url(#chartGradient)"></path><path d="' + pathD + '" fill="none" stroke="#ffc105" stroke-width="2"></path></svg>';
        if (chartDates) chartDates.innerHTML = dates.map(function(d){ return '<span>' + d + '</span>'; }).join('');
    }
    
    document.querySelectorAll('.chart-filter-btn').forEach(function(btn) {
        var p = btn.getAttribute('data-period');
        if (p === currentPeriod) {
            btn.classList.add('bg-white', 'dark:bg-white/10', 'shadow-sm', 'text-black', 'dark:text-white');
            btn.classList.remove('text-slate-500');
        }
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var period = this.getAttribute('data-period');
            document.querySelectorAll('.chart-filter-btn').forEach(function(b) {
                b.classList.remove('bg-white', 'dark:bg-white/10', 'shadow-sm', 'text-black', 'dark:text-white');
                b.classList.add('text-slate-500');
            });
            this.classList.add('bg-white', 'dark:bg-white/10', 'shadow-sm', 'text-black', 'dark:text-white');
            this.classList.remove('text-slate-500');
            fetch('/api/user/chart-data.php?period=' + period, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(res){
                if (res.success && res.data) updateChart(res.data);
            }).catch(function(){ if (chartContainer) chartContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">Failed to load chart</div>'; });
        });
    });
    
    // Deposit button - redirect to wallet
    var depositBtnDash = document.getElementById('deposit-btn-dash');
    if (depositBtnDash) {
        depositBtnDash.addEventListener('click', function() {
            window.location.href = '/dashboard/user/wallet?action=deposit';
        });
    }
});
</script>
</body></html>

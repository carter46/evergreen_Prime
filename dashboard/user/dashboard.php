<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'dashboard';
$siteName = get_site_name();
$userBalance = 0;
$activeInvestments = [];
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amt = (float)$row['amount'];
        if (in_array(strtoupper($row['currency']), ['USDT','USDC','USD','BUSD'], true)) $userBalance += $amt;
        elseif (strtoupper($row['currency']) === 'BTC') $userBalance += $amt * 65000;
        elseif (strtoupper($row['currency']) === 'ETH') $userBalance += $amt * 3500;
        else $userBalance += $amt;
    }
    $stmt = $pdo->prepare('SELECT ui.*, p.name as plan_name FROM user_investments ui JOIN plans p ON p.id = ui.plan_id WHERE ui.user_id = ? AND ui.status = ? ORDER BY ui.created_at DESC LIMIT 5');
    $stmt->execute([$userId, 'active']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $activeInvestments[] = $row;
} catch (Throwable $e) { }
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | AI Trading Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
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
<main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<!-- Dashboard Grid -->
<div class="grid grid-cols-12 gap-6">
<!-- Wallet Balance Card (Glassmorphism) -->
<div class="col-span-4 glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex flex-col justify-between h-64 border-primary/20">
<div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
<div class="relative z-10">
<div class="flex justify-between items-start">
<p class="text-slate-500 font-medium">Total Balance</p>
<span class="material-icons-round text-primary">account_balance_wallet</span>
</div>
<h2 class="text-4xl font-bold mt-2">$<?php echo number_format($userBalance, 2); ?></h2>
<p class="text-emerald-500 font-medium flex items-center gap-1 mt-1">
<span class="material-icons-round text-sm">trending_up</span>
                            +$1,240.20 (24h)
                        </p>
</div>
<div class="flex gap-3 relative z-10">
<button class="flex-1 bg-primary text-black font-bold py-3 rounded-xl hover:shadow-lg hover:shadow-primary/30 transition-all flex items-center justify-center gap-2">
<span class="material-icons-round text-sm">add</span> Deposit
                        </button>
<button class="flex-1 bg-white/50 dark:bg-white/10 border border-primary/30 font-bold py-3 rounded-xl hover:bg-white transition-all flex items-center justify-center gap-2">
<span class="material-icons-round text-sm">file_download</span> Withdraw
                        </button>
</div>
</div>
<!-- Performance Chart Section -->
<div class="col-span-8 bg-white dark:bg-white/5 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-white/5">
<div class="flex justify-between items-center mb-6">
<h3 class="text-lg font-bold">Performance Growth</h3>
<div class="flex bg-slate-100 dark:bg-white/5 p-1 rounded-lg">
<button class="px-4 py-1.5 rounded-md text-xs font-bold text-slate-500">1D</button>
<button class="px-4 py-1.5 rounded-md text-xs font-bold text-slate-500">1W</button>
<button class="px-4 py-1.5 rounded-md text-xs font-bold bg-white dark:bg-white/10 shadow-sm text-black dark:text-white">1M</button>
<button class="px-4 py-1.5 rounded-md text-xs font-bold text-slate-500">1Y</button>
</div>
</div>
<div class="h-40 relative flex items-end gap-1">
<!-- Simulated Line Graph Path via Gradient + Shapes -->
<div class="absolute inset-0 trading-graph-bg rounded-lg"></div>
<div class="relative w-full h-full flex items-end">
<svg class="w-full h-full" preserveaspectratio="none" viewbox="0 0 400 100">
<path d="M0 80 Q 50 20, 100 60 T 200 40 T 300 10 T 400 30" fill="none" stroke="#ffc105" stroke-width="3"></path>
<path d="M0 80 Q 50 20, 100 60 T 200 40 T 300 10 T 400 30 V 100 H 0 Z" fill="url(#gradient)" opacity="0.2"></path>
<defs>
<lineargradient id="gradient" x1="0%" x2="0%" y1="0%" y2="100%">
<stop offset="0%" style="stop-color:#ffc105;stop-opacity:1"></stop>
<stop offset="100%" style="stop-color:#ffc105;stop-opacity:0"></stop>
</lineargradient>
</defs>
</svg>
</div>
</div>
<div class="flex justify-between mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-widest">
<span>Jan 01</span>
<span>Jan 08</span>
<span>Jan 15</span>
<span>Jan 22</span>
<span>Today</span>
</div>
</div>
<!-- Active AI Bots Feed -->
<div class="col-span-4 bg-white dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 overflow-hidden flex flex-col h-[400px]">
<div class="p-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
<h3 class="font-bold flex items-center gap-2">
<span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Live AI Trades
                        </h3>
<span class="text-xs font-bold text-primary">SCANNING...</span>
</div>
<div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-4">
<!-- Trade 1 -->
<div class="flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
<span class="material-icons-round text-emerald-500 text-sm">trending_up</span>
</div>
<div>
<p class="text-xs font-bold">BTC/USDT Long</p>
<p class="text-[10px] text-slate-400">2 mins ago</p>
</div>
</div>
<span class="text-sm font-bold text-emerald-500">+$245.00</span>
</div>
<!-- Trade 2 -->
<div class="flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
<span class="material-icons-round text-red-500 text-sm">trending_down</span>
</div>
<div>
<p class="text-xs font-bold">ETH/USDT Short</p>
<p class="text-[10px] text-slate-400">8 mins ago</p>
</div>
</div>
<span class="text-sm font-bold text-red-500">-$12.40</span>
</div>
<!-- Trade 3 -->
<div class="flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
<span class="material-icons-round text-emerald-500 text-sm">trending_up</span>
</div>
<div>
<p class="text-xs font-bold">SOL/USDT Long</p>
<p class="text-[10px] text-slate-400">15 mins ago</p>
</div>
</div>
<span class="text-sm font-bold text-emerald-500">+$89.15</span>
</div>
<!-- Trade 4 -->
<div class="flex items-center justify-between p-3 rounded-xl bg-background-light dark:bg-background-dark/50 border border-slate-100 dark:border-white/5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
<span class="material-icons-round text-emerald-500 text-sm">trending_up</span>
</div>
<div>
<p class="text-xs font-bold">BNB/USDT Long</p>
<p class="text-[10px] text-slate-400">24 mins ago</p>
</div>
</div>
<span class="text-sm font-bold text-emerald-500">+$156.40</span>
</div>
</div>
</div>
<!-- My Investments -->
<div class="col-span-8 bg-white dark:bg-white/5 rounded-2xl p-6 border border-slate-100 dark:border-white/5">
<div class="flex justify-between items-center mb-6">
<h3 class="text-lg font-bold">My Active Plans</h3>
<button class="text-primary text-sm font-bold flex items-center gap-1">
                            View All <span class="material-icons-round text-sm">arrow_forward</span>
</button>
</div>
<div class="space-y-4">
<!-- Plan 1 -->
<div class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 dark:border-white/10 hover:border-primary/50 transition-all">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
<span class="material-icons-round text-primary">auto_graph</span>
</div>
<div>
<h4 class="font-bold">Alpha Growth AI</h4>
<p class="text-xs text-slate-400">Start Date: Oct 12, 2023</p>
</div>
</div>
<div class="w-48 text-right px-6">
<div class="flex justify-between text-[10px] font-bold mb-1">
<span>PROGRESS</span>
<span>75%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-white/10 h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full w-[75%] rounded-full"></div>
</div>
</div>
<div class="text-right">
<p class="text-sm font-bold">$12,400.00</p>
<p class="text-xs text-emerald-500">+12.5% ROI</p>
</div>
</div>
<!-- Plan 2 -->
<div class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 dark:border-white/10 hover:border-primary/50 transition-all">
<div class="flex items-center gap-4">
<div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
<span class="material-icons-round text-primary">security</span>
</div>
<div>
<h4 class="font-bold">Stable Shield AI</h4>
<p class="text-xs text-slate-400">Start Date: Nov 05, 2023</p>
</div>
</div>
<div class="w-48 text-right px-6">
<div class="flex justify-between text-[10px] font-bold mb-1">
<span>PROGRESS</span>
<span>32%</span>
</div>
<div class="w-full bg-slate-100 dark:bg-white/10 h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full w-[32%] rounded-full"></div>
</div>
</div>
<div class="text-right">
<p class="text-sm font-bold">$25,000.00</p>
<p class="text-xs text-emerald-500">+4.2% ROI</p>
</div>
</div>
<!-- Add New -->
<button class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-white/10 rounded-xl text-slate-400 font-bold hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2">
<span class="material-icons-round">add_circle_outline</span>
                            Subscribe to New Investment Plan
                        </button>
</div>
</div>
</div>
<!-- Footer Metrics -->
<footer class="mt-12 pt-6 border-t border-slate-100 dark:border-white/5 grid grid-cols-4 gap-6">
<div class="flex items-center gap-3">
<span class="material-icons-round text-slate-400">history_toggle_off</span>
<div>
<p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Active Runtime</p>
<p class="text-sm font-bold">142 Days 04:22:12</p>
</div>
</div>
<div class="flex items-center gap-3">
<span class="material-icons-round text-slate-400">psychology</span>
<div>
<p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">AI Accuracy</p>
<p class="text-sm font-bold">94.8% Monthly Avg</p>
</div>
</div>
<div class="flex items-center gap-3">
<span class="material-icons-round text-slate-400">hub</span>
<div>
<p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Node Region</p>
<p class="text-sm font-bold">Frankfurt-DE #4</p>
</div>
</div>
<div class="flex items-center gap-3">
<span class="material-icons-round text-slate-400">verified_user</span>
<div>
<p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Security Level</p>
<p class="text-sm font-bold text-emerald-500">Tier 3 - Advanced</p>
</div>
</div>
</footer>
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
});
</script>
</body></html>

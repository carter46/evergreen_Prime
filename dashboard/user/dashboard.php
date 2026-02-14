<?php require_once __DIR__ . '/../../includes/auth-check.php'; ?>
<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bloombit | AI Trading Dashboard</title>
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ffc10544;
            border-radius: 10px;
        }
        .trading-graph-bg {
            background: linear-gradient(180deg, rgba(255,193,5,0.1) 0%, rgba(255,193,5,0) 100%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
<div class="flex min-h-screen">
<!-- Sidebar -->
<aside class="w-64 border-r border-primary/10 bg-white/50 dark:bg-background-dark/50 flex flex-col fixed h-full z-50">
<a class="p-6 flex items-center gap-3" href="/">
<div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
<span class="material-icons-round text-white">bolt</span>
</div>
<span class="text-2xl font-bold tracking-tight">Bloombit</span>
</a>
<nav class="flex-1 px-4 py-4 space-y-2">
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-black font-semibold rounded-xl transition-all" href="/dashboard/user/dashboard">
<span class="material-icons-round text-[20px]">grid_view</span>
                    Dashboard
                </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-primary/10 hover:text-primary rounded-xl transition-all" href="/dashboard/user/wallet">
<span class="material-icons-round text-[20px]">account_balance_wallet</span>
                    Wallet
                </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-primary/10 hover:text-primary rounded-xl transition-all" href="/dashboard/user/analytics">
<span class="material-icons-round text-[20px]">insights</span>
                    My Investments
                </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-primary/10 hover:text-primary rounded-xl transition-all" href="/dashboard/user/analytics">
<span class="material-icons-round text-[20px]">history</span>
                    Trade History
                </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-primary/10 hover:text-primary rounded-xl transition-all" href="/dashboard/user/profile">
<span class="material-icons-round text-[20px]">settings</span>
                    Settings
                </a>
</nav>
<div class="p-6">
<div class="bg-primary/10 rounded-2xl p-4 border border-primary/20">
<p class="text-xs font-medium text-primary mb-1 uppercase tracking-wider">Plan Status</p>
<p class="text-sm font-bold" data-plan-status>Pro Trader AI active</p>
<div class="mt-3 w-full bg-primary/20 h-1.5 rounded-full">
<div class="bg-primary h-1.5 rounded-full w-[85%] shadow-[0_0_8px_rgba(255,193,5,0.6)]"></div>
</div>
</div>
<button data-logout class="mt-6 flex items-center gap-3 px-4 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all w-full">
<span class="material-icons-round text-[20px]">logout</span>
                    Sign Out
                </button>
</div>
</aside>
<!-- Main Content Area -->
<main class="flex-1 ml-64 p-8">
<!-- Top Bar -->
<header class="flex items-center justify-between mb-8">
<div>
<h1 class="text-3xl font-bold">Good morning, Alex</h1>
<p class="text-slate-500">System status: <span class="text-emerald-500 font-medium">AI Core Online</span></p>
</div>
<div class="flex items-center gap-6">
<div class="flex gap-4">
<div class="bg-white dark:bg-white/5 border border-primary/10 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm">
<span class="text-xs text-slate-400 uppercase font-bold">BTC/USD</span>
<span class="font-bold" data-coin="bitcoin" data-price="">--</span>
<span class="text-xs font-bold crypto-change text-emerald-500" data-coin="bitcoin" data-change="">--</span>
</div>
</div>
<div class="flex items-center gap-4 border-l border-slate-200 dark:border-white/10 pl-6">
<button class="relative w-10 h-10 flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
<span class="material-icons-round">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
</button>
<div class="flex items-center gap-3">
<div class="text-right">
<p class="text-sm font-bold leading-none">Alex Rivera</p>
<p class="text-xs text-slate-500">Verified User</p>
</div>
<img alt="User" class="w-10 h-10 rounded-full border-2 border-primary" data-alt="Professional headshot of a male user" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAOC4BkOfAIELCclJ8x7GDF7rJChGJelN25tkIftuO8Gvct9ZmJ7X284HMhELI2rEIOdft7rKTJeJPNEnX6pzMWQuZtPSEMqN5QLBmtq0Kn46y11RrclC4mNabZ-Y5wcp9xD-qcIKBcdpMAku3Yt47oHbk_JPCzHGPN8ciroIDnk7K_kpqPqUfr1GoqxIyhofa4pjGCcfmbzW0pBKoVf9fQVgJKjxLN7ZMdX3BJCTAowB9oTO_kbTEY5jR5C-_TRlPtCGhsTnPsHFw"/>
</div>
</div>
</div>
</header>
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
<h2 class="text-4xl font-bold mt-2">$42,050.84</h2>
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

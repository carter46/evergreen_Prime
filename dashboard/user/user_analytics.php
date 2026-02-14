<?php require_once __DIR__ . '/../../includes/auth-check.php'; ?>
<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bloombit | Earnings Analytics &amp; History</title>
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
                        "primary": "#f9bd0b",
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
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #f8f8f5;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(249, 189, 11, 0.1);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e2e2;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 font-display min-h-screen">
<!-- Navigation Sidebar (Partial for context) -->
<aside class="fixed left-0 top-0 h-full w-64 bg-white dark:bg-zinc-900 border-r border-primary/10 hidden lg:flex flex-col z-50">
<div class="p-6">
<a class="p-6 flex items-center gap-2" href="/">
<div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
<span class="material-icons-round text-white">bolt</span>
</div>
<span class="text-xl font-bold tracking-tight">Bloombit</span>
</a>
</div>
<nav class="flex-1 px-4 space-y-1 mt-4">
<a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:bg-primary/5 transition-colors" href="/dashboard/user/dashboard">
<span class="material-icons-round">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-primary border border-primary/20" href="/dashboard/user/analytics">
<span class="material-icons-round">analytics</span>
<span class="font-medium">Earnings History</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:bg-primary/5 transition-colors" href="/dashboard/user/wallet">
<span class="material-icons-round">account_balance_wallet</span>
<span class="font-medium">Wallet</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-500 hover:bg-primary/5 transition-colors" href="/dashboard/user/profile">
<span class="material-icons-round">security</span>
<span class="font-medium">AI Bot Center</span>
</a>
</nav>
<div class="p-6 border-t border-primary/5">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-primary/20 overflow-hidden">
<img alt="User Avatar" data-alt="User profile avatar placeholder" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAhSX3_pnwMHWyowiCEQI5bis1r8ds6r3lMJh8xQAAuN8e44PWiIk1W08k6FfcOWPy9QVmiEWDxOHSFuhFbeZKdUl7XcjtFqkL9n_f1bXqXSjXXWP3c6c_fSNS11g3BGDiwpIXnRYiPu-SF7IEWGuQrW6BdBLdB1l81aHbp74WeMLSezdesLRiJjGEeRbBL5iqyYZeutPh_0ntKfLnvL3k4R5Pg4RLl5D5tcxcIbBVZeOtizozCR6njiJDGd33Vb6-ehbuidI-i1jw"/>
</div>
<div class="flex-1 min-w-0">
<p class="text-sm font-semibold truncate">Alex Chen</p>
<p class="text-xs text-slate-400">Pro Tier</p>
</div>
</div>
</div>
</aside>
<!-- Main Content -->
<main class="lg:ml-64 p-8">
<!-- Header -->
<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<h1 class="text-3xl font-bold">Earnings Analytics</h1>
<p class="text-slate-500 mt-1">Detailed performance tracking and profit distribution history.</p>
</div>
<div class="flex items-center gap-3">
<button class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center gap-2 hover:bg-white transition-all text-sm font-medium">
<span class="material-icons-round text-lg">calendar_today</span>
                    Last 30 Days
                </button>
<button class="px-4 py-2 bg-primary text-black rounded-lg flex items-center gap-2 hover:bg-primary/90 transition-all text-sm font-bold shadow-sm shadow-primary/20">
<span class="material-icons-round text-lg">download</span>
                    Export PDF
                </button>
</div>
</header>
<!-- Top Stats Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="glass-card bg-white dark:bg-zinc-900 p-5 rounded-xl">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-primary/10 rounded-lg">
<span class="material-icons-round text-primary">payments</span>
</div>
<span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full">+12.4%</span>
</div>
<h3 class="text-slate-400 text-sm font-medium">Total Profit</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight">$42,912.80</span>
</div>
<div class="mt-4 h-8 w-full">
<div class="w-full h-full bg-primary/5 rounded relative overflow-hidden">
<div class="absolute bottom-0 left-0 w-full h-4 bg-primary/20" style="clip-path: polygon(0 80%, 10% 60%, 20% 75%, 30% 40%, 40% 50%, 50% 30%, 60% 45%, 70% 20%, 80% 35%, 90% 10%, 100% 25%, 100% 100%, 0 100%);"></div>
</div>
</div>
</div>
<div class="glass-card bg-white dark:bg-zinc-900 p-5 rounded-xl">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-primary/10 rounded-lg">
<span class="material-icons-round text-primary">trending_up</span>
</div>
<span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full">+1.2%</span>
</div>
<h3 class="text-slate-400 text-sm font-medium">Daily Avg. Return</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight">2.45%</span>
</div>
<div class="mt-4 h-8 w-full">
<div class="w-full h-full bg-primary/5 rounded relative overflow-hidden">
<div class="absolute bottom-0 left-0 w-full h-4 bg-primary/20" style="clip-path: polygon(0 50%, 10% 55%, 20% 45%, 30% 60%, 40% 40%, 50% 55%, 60% 45%, 70% 50%, 80% 40%, 90% 60%, 100% 50%, 100% 100%, 0 100%);"></div>
</div>
</div>
</div>
<div class="glass-card bg-white dark:bg-zinc-900 p-5 rounded-xl">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-primary/10 rounded-lg">
<span class="material-icons-round text-primary">account_balance</span>
</div>
<span class="text-xs font-bold text-slate-400 bg-slate-100 dark:bg-zinc-800 px-2 py-1 rounded-full">Stable</span>
</div>
<h3 class="text-slate-400 text-sm font-medium">Active Capital</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight">$125,500</span>
</div>
<div class="mt-4 h-8 w-full">
<div class="w-full h-full bg-primary/5 rounded relative overflow-hidden">
<div class="absolute bottom-0 left-0 w-full h-4 bg-primary/20" style="clip-path: polygon(0 20%, 100% 20%, 100% 100%, 0 100%);"></div>
</div>
</div>
</div>
<div class="glass-card bg-white dark:bg-zinc-900 p-5 rounded-xl border-primary/20 border">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-primary/10 rounded-lg">
<span class="material-icons-round text-primary">auto_graph</span>
</div>
<span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full">Projected</span>
</div>
<h3 class="text-slate-400 text-sm font-medium">Est. Monthly Earnings</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight">$9,240.00</span>
</div>
<div class="mt-4 h-8 w-full">
<div class="w-full h-full bg-primary/5 rounded relative overflow-hidden">
<div class="absolute bottom-0 left-0 w-full h-4 bg-primary/20" style="clip-path: polygon(0 80%, 25% 60%, 50% 40%, 75% 20%, 100% 0%, 100% 100%, 0 100%);"></div>
</div>
</div>
</div>
</section>
<!-- Main Analytics Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
<!-- Performance Chart -->
<div class="xl:col-span-2 glass-card bg-white dark:bg-zinc-900 p-6 rounded-xl">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
<h2 class="text-lg font-bold flex items-center gap-2">
                        Cumulative Performance
                        <span class="material-icons-round text-slate-400 text-base cursor-help" title="Visualizes your total earnings growth over time">info</span>
</h2>
<div class="flex bg-slate-100 dark:bg-zinc-800 p-1 rounded-lg">
<button class="px-3 py-1 text-xs font-semibold rounded hover:bg-white dark:hover:bg-zinc-700 transition-all">1D</button>
<button class="px-3 py-1 text-xs font-semibold rounded hover:bg-white dark:hover:bg-zinc-700 transition-all">1W</button>
<button class="px-3 py-1 text-xs font-semibold rounded bg-white dark:bg-zinc-700 shadow-sm transition-all">1M</button>
<button class="px-3 py-1 text-xs font-semibold rounded hover:bg-white dark:hover:bg-zinc-700 transition-all">1Y</button>
<button class="px-3 py-1 text-xs font-semibold rounded hover:bg-white dark:hover:bg-zinc-700 transition-all">ALL</button>
</div>
</div>
<div class="relative h-[300px] w-full">
<!-- Abstract Visualization of Area Chart -->
<div class="absolute inset-0 flex items-end">
<svg class="w-full h-full" preserveaspectratio="none" viewbox="0 0 1000 300">
<defs>
<lineargradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
<stop offset="0%" stop-color="#f9bd0b" stop-opacity="0.2"></stop>
<stop offset="100%" stop-color="#f9bd0b" stop-opacity="0"></stop>
</lineargradient>
</defs>
<path d="M0,250 Q100,240 200,220 T400,180 T600,130 T800,80 T1000,40 L1000,300 L0,300 Z" fill="url(#chartGradient)"></path>
<path d="M0,250 Q100,240 200,220 T400,180 T600,130 T800,80 T1000,40" fill="none" stroke="#f9bd0b" stroke-width="3"></path>
<!-- Dots for points -->
<circle cx="200" cy="220" fill="#f9bd0b" r="4"></circle>
<circle cx="400" cy="180" fill="#f9bd0b" r="4"></circle>
<circle cx="600" cy="130" fill="#f9bd0b" r="4"></circle>
<circle cx="800" cy="80" fill="#f9bd0b" r="4"></circle>
</svg>
</div>
<!-- Tooltip Simulation -->
<div class="absolute left-1/2 top-1/4 -translate-x-1/2 -translate-y-full bg-zinc-900 text-white p-3 rounded-lg shadow-xl text-xs flex flex-col items-center pointer-events-none after:content-[''] after:absolute after:top-full after:left-1/2 after:-translate-x-1/2 after:border-8 after:border-transparent after:border-t-zinc-900">
<span class="text-zinc-400 font-medium">May 14, 2024</span>
<span class="text-primary font-bold text-sm">$32,450.00 (+4.2%)</span>
</div>
</div>
<div class="flex justify-between mt-4 px-2 text-xs text-slate-400 font-medium">
<span>14 May</span>
<span>21 May</span>
<span>28 May</span>
<span>04 Jun</span>
<span>Today</span>
</div>
</div>
<!-- Side Widgets -->
<div class="space-y-6">
<!-- Winning Streak -->
<div class="glass-card bg-white dark:bg-zinc-900 p-6 rounded-xl flex items-center gap-6">
<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center border-4 border-primary/20">
<span class="material-icons-round text-3xl text-primary">workspace_premium</span>
</div>
<div>
<h3 class="text-slate-400 text-sm font-medium">Winning Streak</h3>
<p class="text-3xl font-bold">14 Days</p>
<p class="text-xs text-emerald-500 mt-1 flex items-center gap-1">
<span class="material-icons-round text-xs">keyboard_double_arrow_up</span>
                            Personal Best
                        </p>
</div>
</div>
<!-- Max Drawdown -->
<div class="glass-card bg-white dark:bg-zinc-900 p-6 rounded-xl flex items-center gap-6">
<div class="w-16 h-16 bg-slate-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
<span class="material-icons-round text-3xl text-slate-400">warning_amber</span>
</div>
<div>
<h3 class="text-slate-400 text-sm font-medium">Max Drawdown</h3>
<p class="text-3xl font-bold">3.2%</p>
<p class="text-xs text-slate-400 mt-1">Market stability high</p>
</div>
</div>
<!-- Earnings Breakdown Doughnut Simulation -->
<div class="glass-card bg-white dark:bg-zinc-900 p-6 rounded-xl">
<h2 class="text-sm font-bold mb-4">Profit by Asset</h2>
<div class="flex items-center gap-6">
<div class="relative w-24 h-24">
<svg class="w-full h-full transform -rotate-90">
<circle class="dark:stroke-zinc-800" cx="48" cy="48" fill="transparent" r="40" stroke="#f1f1f1" stroke-width="12"></circle>
<circle cx="48" cy="48" fill="transparent" r="40" stroke="#f9bd0b" stroke-dasharray="251.2" stroke-dashoffset="62.8" stroke-width="12"></circle>
<circle cx="48" cy="48" fill="transparent" r="40" stroke="#f59e0b" stroke-dasharray="251.2" stroke-dashoffset="188.4" stroke-width="12"></circle>
</svg>
</div>
<div class="flex-1 space-y-2">
<div class="flex items-center justify-between text-xs">
<span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-primary"></span>BTC</span>
<span class="font-bold">65%</span>
</div>
<div class="flex items-center justify-between text-xs">
<span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span>ETH</span>
<span class="font-bold">25%</span>
</div>
<div class="flex items-center justify-between text-xs">
<span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-200"></span>Other</span>
<span class="font-bold">10%</span>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- History Table Section -->
<div class="glass-card bg-white dark:bg-zinc-900 rounded-xl overflow-hidden">
<div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
<h2 class="text-lg font-bold">Distribution History</h2>
<div class="flex items-center gap-3">
<div class="relative">
<span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
<input class="pl-10 pr-4 py-2 bg-slate-100 dark:bg-zinc-800 border-none rounded-lg text-sm w-full md:w-64 focus:ring-2 focus:ring-primary" placeholder="Search entries..." type="text"/>
</div>
<button class="p-2 border border-slate-200 dark:border-zinc-700 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800">
<span class="material-icons-round text-slate-500">filter_list</span>
</button>
</div>
</div>
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-zinc-800/50 text-slate-400 text-xs font-bold uppercase tracking-wider">
<th class="px-6 py-4">Date &amp; Time</th>
<th class="px-6 py-4">Investment Plan</th>
<th class="px-6 py-4">Asset</th>
<th class="px-6 py-4">Amount (USD)</th>
<th class="px-6 py-4">ROI %</th>
<th class="px-6 py-4">Status</th>
</tr>
</thead>
<tbody class="text-sm divide-y divide-slate-100 dark:divide-zinc-800">
<!-- Row 1 -->
<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-6 py-4">
<p class="font-semibold">Jun 14, 2024</p>
<p class="text-xs text-slate-400">14:22 PM</p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-primary"></div>
<span class="font-medium">AI Quantum Yield</span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<img alt="BTC" class="w-5 h-5" data-alt="Bitcoin cryptocurrency logo icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuATLC5DAe1SMTpRwPSl6-WbRJ9H8f3Lduu3sJZckVmMwkeE8axBYzWKNKGHM0iQjw1-25a6oIrxTyGVBhu0hyjQ0fz8uOBpOPLiCh9X2g0GGPzNY8iYYf-Z5fYq0C5TG_TSv7uhk05VJVp1yF37Uvp7wGH-eGN4b9R0sl6ngNFdXofXNChmZY8F9w61i8gfzgnOd7qfF1FjHaiHAAUboxwrRVn35CP1OE6_AIs3ZjhTHCb2CuA2DniqTvFR7-e0V4ND0K-uJJTHy8Q"/>
<span class="font-medium">BTC</span>
</div>
</td>
<td class="px-6 py-4 font-bold text-emerald-500">+$450.25</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded font-bold text-xs">2.1%</span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 text-emerald-500 font-medium">
<span class="material-icons-round text-sm">check_circle</span>
                                    Completed
                                </span>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-6 py-4">
<p class="font-semibold">Jun 13, 2024</p>
<p class="text-xs text-slate-400">09:15 AM</p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-blue-400"></div>
<span class="font-medium">Stable Edge</span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<img alt="USDT" class="w-5 h-5" data-alt="Tether USDT cryptocurrency logo icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXbirqtCgP5z0-96K_eOK2m1hfUTVy9ejBbRt10LN-KIXkpUuDXes5977rgZX1xlbTJpVYZM8a2iqzY6uaFuAaQeVXf7JjkoP65Zu0wzvguBJBWCcig4Cmx1z1s0xvdsyoXL0P8jzS7kYRE9dbYyXpCE0x_j6fSqJgfj5o2tFKdIU5gvFXQEMIXuq8WHECQtoi7EdvLO3lz4Dz9Wkg9iBGogpqKp8O0xLa_rEMCIoz8EX8lZE1zK-ZZrpdpyxJkAH_8rYm6xartu8"/>
<span class="font-medium">USDT</span>
</div>
</td>
<td class="px-6 py-4 font-bold text-emerald-500">+$122.10</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded font-bold text-xs">1.8%</span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 text-emerald-500 font-medium">
<span class="material-icons-round text-sm">check_circle</span>
                                    Completed
                                </span>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-6 py-4">
<p class="font-semibold">Jun 12, 2024</p>
<p class="text-xs text-slate-400">22:45 PM</p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-primary"></div>
<span class="font-medium">AI Quantum Yield</span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<img alt="ETH" class="w-5 h-5" data-alt="Ethereum cryptocurrency logo icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBX1r6QdOhsd1JZv4tdFtJPyiTiX4SzNfM7PHdsrRwh-z6c63xc2I0mwcnYtxrrFayK2PmKwchoja7hnrlgpWNc3J6neP8wgJCa7sfnxPTO-JJ6UfM-fnH227lvij-mqPNXU_MpCrUy133tY-2znlk3PG2TYRbVHcA0UcuObpoAwqti-lEcEtHjt-peSSrQZnTNMDxv4LKsPB56Z5C_AQbMbwsAsc7rbdt8zdEGZRCwZaDoXd-OdM6pEbLfu9krRuPmw_r1qm1L-a0"/>
<span class="font-medium">ETH</span>
</div>
</td>
<td class="px-6 py-4 font-bold text-emerald-500">+$310.44</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded font-bold text-xs">2.4%</span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 text-emerald-500 font-medium">
<span class="material-icons-round text-sm">check_circle</span>
                                    Completed
                                </span>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-6 py-4">
<p class="font-semibold">Jun 12, 2024</p>
<p class="text-xs text-slate-400">11:02 AM</p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-purple-400"></div>
<span class="font-medium">DeFi Harvester</span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<img alt="SOL" class="w-5 h-5" data-alt="Solana cryptocurrency logo icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAU0WF3DbCqZhvW5qtHAJkUsYEBC5awNHJqwvWjUSCLGQsQ4O0_4-2y0ZDUvXtTObVtfwPHI_Y066HsyDH9FiD2VaXySPQj_Fq3KBz8pbhduqeZq4B6_-o4qzX_zAit3dFvsFubhdb0S7G56sCBYc4ZyjRcgTtt9pEmTYuMb8RJY0A1THynSV6s3fDOJopvqQV-qkEPC5xTsucRxg_G_RzS4aHmhJYWz9u9pHnzdlUkikDcP_ecPsKEjeKzPjw44-hUgIij0QRs3tI"/>
<span class="font-medium">SOL</span>
</div>
</td>
<td class="px-6 py-4 font-bold text-emerald-500">+$89.50</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded font-bold text-xs">3.1%</span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 text-emerald-500 font-medium">
<span class="material-icons-round text-sm">check_circle</span>
                                    Completed
                                </span>
</td>
</tr>
<!-- Row 5 -->
<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-6 py-4">
<p class="font-semibold">Jun 11, 2024</p>
<p class="text-xs text-slate-400">18:30 PM</p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-primary"></div>
<span class="font-medium">AI Quantum Yield</span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<img alt="BTC" class="w-5 h-5" data-alt="Bitcoin cryptocurrency logo icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDqLW9aqfbKvyI4mbvZxQ-haSYzye_ezVej7L1FoEGBIDJNcuABHO5Byif-wrMRsVnUdhlBuAHr_mI2aLgwQzznHolXalJPTdVaUDERJtqdwzRTbKo7azL2FrVtMK1HboSokVs_rcnK1-Ha5qJKRilrB_KqYMFh7Ur40M3eyYmKwYtLMPa96wnI0k45K3z4OJ2QJtMyNNjr8YSi780A34hm8ZVBs2ZI9JBO2HQuKCqhGtCwc19vBieiqTMq1TDnsjA02-XprG24Bnw"/>
<span class="font-medium">BTC</span>
</div>
</td>
<td class="px-6 py-4 font-bold text-emerald-500">+$214.18</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 rounded font-bold text-xs">1.9%</span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 text-emerald-500 font-medium">
<span class="material-icons-round text-sm">check_circle</span>
                                    Completed
                                </span>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-between">
<span class="text-xs text-slate-400 font-medium">Showing 1-10 of 124 entries</span>
<div class="flex gap-2">
<button class="p-2 border border-slate-200 dark:border-zinc-700 rounded-lg opacity-50 cursor-not-allowed">
<span class="material-icons-round text-sm">chevron_left</span>
</button>
<button class="px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg text-xs font-bold bg-primary text-black">1</button>
<button class="px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg text-xs font-bold hover:bg-slate-50 dark:hover:bg-zinc-800">2</button>
<button class="px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg text-xs font-bold hover:bg-slate-50 dark:hover:bg-zinc-800">3</button>
<button class="p-2 border border-slate-200 dark:border-zinc-700 rounded-lg hover:bg-slate-50 dark:hover:bg-zinc-800">
<span class="material-icons-round text-sm">chevron_right</span>
</button>
</div>
</div>
</div>
</main>
<!-- Floating Help Button -->
<button class="fixed bottom-6 right-6 w-14 h-14 bg-black text-white rounded-full flex items-center justify-center shadow-xl hover:scale-105 transition-transform z-50">
<span class="material-icons-round">support_agent</span>
</button>
<script src="/js/app.js"></script>
</body></html>
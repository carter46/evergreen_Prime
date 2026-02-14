<?php require_once __DIR__ . '/../../includes/auth-check.php'; ?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bloombit | Investment Plan Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f2df0d",
                        "background-light": "#f8f8f5",
                        "background-dark": "#222110",
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
<style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
<!-- Sidebar Navigation (Mock) -->
<aside class="fixed left-0 top-0 h-screen w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 hidden lg:flex flex-col">
<a class="p-6 flex items-center gap-3" href="/">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-round text-zinc-900">bolt</span>
</div>
<span class="font-bold text-xl tracking-tight">Bloombit</span>
</a>
<nav class="flex-1 px-4 py-4 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg transition-colors" href="/dashboard/admin">
<span class="material-icons-round">dashboard</span> Dashboard
            </a>
<a class="flex items-center gap-3 px-4 py-3 bg-primary/10 text-slate-900 dark:text-primary font-semibold rounded-lg border-l-4 border-primary" href="/dashboard/admin/plans">
<span class="material-icons-round">account_balance_wallet</span> Investment Plans
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg transition-colors" href="/dashboard/admin/users">
<span class="material-icons-round">people</span> Users
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg transition-colors" href="#">
<span class="material-icons-round">psychology</span> AI Strategies
            </a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 dark:hover:bg-zinc-800 rounded-lg transition-colors" href="#">
<span class="material-icons-round">settings</span> Settings
            </a>
</nav>
<div class="p-4 border-t border-slate-200 dark:border-zinc-800">
<div class="flex items-center gap-3 px-2">
<img class="w-10 h-10 rounded-full border-2 border-primary" data-alt="Admin profile avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZ_1l8dQopy9g-0pbTpq--ddfJMvhYcJg7Qx9yz3kZEAym3H1ycItT7TPuBGzxhLR6ekMmxOZSYk_9Hnnlc9JCa1RLu6AG9N_C0hJIyr5_LNm0E2CTbQkRV_prOQy1Nlo9zEI7ue8c0OFi3Sjal2_Y8y-SmD78Wb3u0gCVTDiw54jv2r-IQTN1U8hILuYQgAzmUQq49Y8FydhoB6aYDLRGdeA87UCV2-4GpBTrYYA-HWkPAHz-GLqJk1mqzh5fjJP5dxVZH2KXFjk"/>
<div>
<p class="text-sm font-semibold">Alex Rivera</p>
<p class="text-xs text-slate-500">Super Admin</p>
</div>
</div>
</div>
</aside>
<!-- Main Content Area -->
<main class="lg:ml-64 p-8">
<!-- Top Header -->
<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<h1 class="text-2xl font-bold">Investment Plan Management</h1>
<p class="text-slate-500 dark:text-zinc-400">Manage and configure Bloombit's crypto investment offerings.</p>
</div>
<button class="bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
<span class="material-icons-round text-lg">add</span> Add New Plan
            </button>
</header>
<!-- Stats Overview Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Total Active Users</p>
<p class="text-2xl font-bold">825</p>
</div>
<div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-full flex items-center justify-center">
<span class="material-icons-round">group</span>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Total Capital Invested</p>
<p class="text-2xl font-bold">$1,248,390</p>
</div>
<div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center">
<span class="material-icons-round">payments</span>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Avg. Daily Payout</p>
<p class="text-2xl font-bold">2.4%</p>
</div>
<div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-full flex items-center justify-center">
<span class="material-icons-round">trending_up</span>
</div>
</div>
</div>
<!-- Plan Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-12">
<!-- Starter Plan Card -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-colors group relative overflow-hidden">
<div class="p-6">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 bg-slate-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-slate-600 dark:text-zinc-400 group-hover:bg-primary transition-colors group-hover:text-zinc-900">
<span class="material-icons-round">rocket_launch</span>
</div>
<div class="flex flex-col items-end">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">
<span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Enabled
                            </span>
</div>
</div>
<h3 class="text-xl font-bold mb-1">Starter Plan</h3>
<p class="text-sm text-slate-500 mb-6 italic">Low risk entry tier</p>
<div class="space-y-4 mb-6">
<div class="flex justify-between text-sm">
<span class="text-slate-500">Active Users</span>
<span class="font-semibold">500</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Total Capital</span>
<span class="font-semibold">$125,400</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Daily ROI</span>
<span class="font-semibold text-green-600">1.5%</span>
</div>
</div>
<!-- Payout/Deposit Performance Sparkline -->
<div class="mb-6">
<p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-2">Payout-to-Deposit Ratio</p>
<div class="h-10 w-full flex items-end gap-1">
<div class="bg-primary/40 w-full h-[40%] rounded-sm"></div>
<div class="bg-primary/40 w-full h-[55%] rounded-sm"></div>
<div class="bg-primary/40 w-full h-[45%] rounded-sm"></div>
<div class="bg-primary/60 w-full h-[70%] rounded-sm"></div>
<div class="bg-primary/60 w-full h-[60%] rounded-sm"></div>
<div class="bg-primary w-full h-[85%] rounded-sm"></div>
<div class="bg-primary w-full h-[75%] rounded-sm"></div>
</div>
</div>
<div class="flex items-center justify-between gap-3 pt-6 border-t border-slate-100 dark:border-zinc-800">
<div class="flex items-center gap-2">
<button class="w-10 h-10 rounded-lg flex items-center justify-center border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 transition-colors">
<span class="material-icons-round text-sm">edit</span>
</button>
<label class="relative inline-flex items-center cursor-pointer">
<input checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<span class="text-xs text-slate-400 font-medium">AI Level: Low</span>
</div>
</div>
</div>
<!-- Growth Plan Card -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-colors group relative overflow-hidden">
<div class="p-6">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 bg-slate-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-slate-600 dark:text-zinc-400 group-hover:bg-primary transition-colors group-hover:text-zinc-900">
<span class="material-icons-round">trending_up</span>
</div>
<div class="flex flex-col items-end">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium">
<span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Enabled
                            </span>
</div>
</div>
<h3 class="text-xl font-bold mb-1">Growth Plan</h3>
<p class="text-sm text-slate-500 mb-6 italic">Balanced performance strategy</p>
<div class="space-y-4 mb-6">
<div class="flex justify-between text-sm">
<span class="text-slate-500">Active Users</span>
<span class="font-semibold">240</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Total Capital</span>
<span class="font-semibold">$458,900</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Daily ROI</span>
<span class="font-semibold text-green-600">2.5%</span>
</div>
</div>
<div class="mb-6">
<p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-2">Payout-to-Deposit Ratio</p>
<div class="h-10 w-full flex items-end gap-1">
<div class="bg-primary/40 w-full h-[30%] rounded-sm"></div>
<div class="bg-primary/40 w-full h-[40%] rounded-sm"></div>
<div class="bg-primary/40 w-full h-[35%] rounded-sm"></div>
<div class="bg-primary/60 w-full h-[50%] rounded-sm"></div>
<div class="bg-primary/60 w-full h-[80%] rounded-sm"></div>
<div class="bg-primary w-full h-[95%] rounded-sm"></div>
<div class="bg-primary w-full h-[90%] rounded-sm"></div>
</div>
</div>
<div class="flex items-center justify-between gap-3 pt-6 border-t border-slate-100 dark:border-zinc-800">
<div class="flex items-center gap-2">
<button class="w-10 h-10 rounded-lg flex items-center justify-center border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 transition-colors">
<span class="material-icons-round text-sm">edit</span>
</button>
<label class="relative inline-flex items-center cursor-pointer">
<input checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<span class="text-xs text-slate-400 font-medium">AI Level: Medium</span>
</div>
</div>
</div>
<!-- Premium Plan Card -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-colors group relative overflow-hidden">
<div class="p-6">
<div class="flex justify-between items-start mb-4">
<div class="w-12 h-12 bg-slate-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-slate-600 dark:text-zinc-400 group-hover:bg-primary transition-colors group-hover:text-zinc-900">
<span class="material-icons-round">diamond</span>
</div>
<div class="flex flex-col items-end">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 text-xs font-medium">
<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Disabled
                            </span>
</div>
</div>
<h3 class="text-xl font-bold mb-1 text-slate-400">Premium Plan</h3>
<p class="text-sm text-slate-500 mb-6 italic">High yield aggressive strategy</p>
<div class="space-y-4 mb-6 opacity-60">
<div class="flex justify-between text-sm">
<span class="text-slate-500">Active Users</span>
<span class="font-semibold">85</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Total Capital</span>
<span class="font-semibold">$664,090</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Daily ROI</span>
<span class="font-semibold text-green-600">4.0%</span>
</div>
</div>
<div class="mb-6 opacity-40">
<p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-2">Payout-to-Deposit Ratio</p>
<div class="h-10 w-full flex items-end gap-1">
<div class="bg-slate-300 w-full h-[50%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[40%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[60%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[45%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[55%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[40%] rounded-sm"></div>
<div class="bg-slate-300 w-full h-[50%] rounded-sm"></div>
</div>
</div>
<div class="flex items-center justify-between gap-3 pt-6 border-t border-slate-100 dark:border-zinc-800">
<div class="flex items-center gap-2">
<button class="w-10 h-10 rounded-lg flex items-center justify-center border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 transition-colors">
<span class="material-icons-round text-sm">edit</span>
</button>
<label class="relative inline-flex items-center cursor-pointer">
<input class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<span class="text-xs text-slate-400 font-medium">AI Level: High</span>
</div>
</div>
</div>
</div>
<!-- Global Settings Section -->
<section class="mb-12">
<h2 class="text-lg font-bold mb-6 flex items-center gap-2">
<span class="material-icons-round text-primary">public</span> Global Parameters
            </h2>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Min. Withdrawal Limit ($)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" type="number" value="10.00"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Max. Active Plans / User</label>
<input class="w-full bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" type="number" value="3"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Compounding Availability</label>
<div class="flex items-center gap-4 mt-2">
<span class="text-xs text-slate-500">Disabled</span>
<label class="relative inline-flex items-center cursor-pointer">
<input checked="" class="sr-only peer" type="checkbox"/>
<div class="w-10 h-5 bg-slate-200 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
</label>
<span class="text-xs text-slate-500">Enabled</span>
</div>
</div>
<div class="flex items-end">
<button class="w-full bg-primary/20 hover:bg-primary/30 text-zinc-900 font-semibold py-2.5 rounded-lg transition-colors">
                            Update Global Settings
                        </button>
</div>
</div>
</div>
</section>
</main>
<!-- Side Slide-out Panel (Configuration Drawer) -->
<div class="fixed inset-0 z-50 overflow-hidden hidden"> <!-- Toggle 'hidden' for view -->
<div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
<div class="absolute inset-y-0 right-0 max-w-lg w-full bg-white dark:bg-zinc-950 shadow-2xl flex flex-col">
<div class="p-6 border-b border-slate-100 dark:border-zinc-800 flex items-center justify-between">
<div>
<h2 class="text-xl font-bold">Edit Plan: Growth Plan</h2>
<p class="text-xs text-slate-500 uppercase tracking-widest mt-1">PLAN ID: BLMB-GP-024</p>
</div>
<button class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-full">
<span class="material-icons-round">close</span>
</button>
</div>
<div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
<form id="admin-plan-form" class="space-y-8">
<!-- Basic Info -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Basic Information</p>
<div class="grid grid-cols-2 gap-4">
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Plan Name</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg focus:ring-primary focus:border-primary" type="text" value="Growth Plan"/>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-2">Icon Selection</label>
<div class="flex gap-3">
<div class="w-10 h-10 border-2 border-primary bg-primary/10 rounded flex items-center justify-center cursor-pointer">
<span class="material-icons-round">trending_up</span>
</div>
<div class="w-10 h-10 border-2 border-slate-100 dark:border-zinc-800 rounded flex items-center justify-center text-slate-400 cursor-pointer hover:border-primary/50">
<span class="material-icons-round">rocket_launch</span>
</div>
<div class="w-10 h-10 border-2 border-slate-100 dark:border-zinc-800 rounded flex items-center justify-center text-slate-400 cursor-pointer hover:border-primary/50">
<span class="material-icons-round">diamond</span>
</div>
<div class="w-10 h-10 border-2 border-slate-100 dark:border-zinc-800 rounded flex items-center justify-center text-slate-400 cursor-pointer hover:border-primary/50">
<span class="material-icons-round">currency_bitcoin</span>
</div>
<div class="w-10 h-10 border-2 border-slate-100 dark:border-zinc-800 rounded flex items-center justify-center text-slate-400 cursor-pointer hover:border-primary/50">
<span class="material-icons-round">token</span>
</div>
</div>
</div>
</div>
</div>
<!-- Financial Bounds -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Financial Parameters</p>
<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium mb-1.5">Min. Deposit ($)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="1000"/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Max. Deposit ($)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="10000"/>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Daily ROI (%)</label>
<div class="flex items-center gap-4">
<input class="w-full accent-primary" max="10" min="0" step="0.1" type="range" value="2.5"/>
<span class="font-bold text-lg min-w-[50px]">2.5%</span>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Duration (Days)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="30"/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Referral Comm. (%)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="5"/>
</div>
</div>
</div>
<!-- AI Strategy Selection -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">AI Strategy Engine</p>
<div class="grid grid-cols-3 gap-3">
<label class="relative group cursor-pointer">
<input class="sr-only peer" name="ai-lvl" type="radio"/>
<div class="p-3 border-2 border-slate-100 dark:border-zinc-800 rounded-lg text-center peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
<p class="text-xs font-bold uppercase">Low</p>
<p class="text-[10px] text-slate-500">Stability focus</p>
</div>
</label>
<label class="relative group cursor-pointer">
<input checked="" class="sr-only peer" name="ai-lvl" type="radio"/>
<div class="p-3 border-2 border-slate-100 dark:border-zinc-800 rounded-lg text-center peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
<p class="text-xs font-bold uppercase">Med</p>
<p class="text-[10px] text-slate-500">Market balance</p>
</div>
</label>
<label class="relative group cursor-pointer">
<input class="sr-only peer" name="ai-lvl" type="radio"/>
<div class="p-3 border-2 border-slate-100 dark:border-zinc-800 rounded-lg text-center peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
<p class="text-xs font-bold uppercase">High</p>
<p class="text-[10px] text-slate-500">Max volatility</p>
</div>
</label>
</div>
</div>
</form>
</div>
<div class="p-6 border-t border-slate-100 dark:border-zinc-800 grid grid-cols-2 gap-4">
<button type="button" class="px-6 py-3 border border-slate-200 dark:border-zinc-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">Discard</button>
<button type="submit" form="admin-plan-form" class="px-6 py-3 bg-primary text-zinc-900 rounded-lg font-bold shadow-lg shadow-primary/20">Save Changes</button>
</div>
</div>
</div>
<!-- Success Toast Notification (Mock) -->
<div class="fixed bottom-8 right-8 z-50 animate-bounce">
<div class="bg-zinc-900 dark:bg-primary text-white dark:text-zinc-900 px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3">
<span class="material-icons-round text-primary dark:text-zinc-900">check_circle</span>
<span class="font-medium">Plan updated successfully</span>
</div>
</div>
<script src="/js/app.js"></script>
</body></html>
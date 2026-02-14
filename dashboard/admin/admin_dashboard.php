<?php require_once __DIR__ . '/../../includes/auth-check.php'; ?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bloombit Admin Command Center</title>
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
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
<!-- Wrapper -->
<div class="flex min-h-screen">
<!-- Sidebar Navigation -->
<aside class="w-64 bg-white dark:bg-black/20 border-r border-primary/10 flex flex-col shrink-0">
<a class="p-6 flex items-center gap-3" href="/">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons text-white">bolt</span>
</div>
<h1 class="font-bold text-xl tracking-tight">Bloom<span class="text-primary">bit</span></h1>
</a>
<nav class="flex-1 px-4 py-4 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-sm" href="/dashboard/admin">
<span class="material-icons text-[20px]">dashboard</span>
<span class="font-medium">Command Center</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg" href="/dashboard/admin/users">
<span class="material-icons text-[20px]">people</span>
<span class="font-medium">User Management</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg" href="/dashboard/admin/plans">
<span class="material-icons text-[20px]">account_balance_wallet</span>
<span class="font-medium">Plan Management</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg" href="/dashboard/admin">
<span class="material-icons text-[20px]">receipt_long</span>
<span class="font-medium">Transactions</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg" href="/dashboard/admin">
<span class="material-icons text-[20px]">smart_toy</span>
<span class="font-medium">AI Bot Config</span>
</a>
</nav>
<div class="p-4 border-t border-primary/10">
<a class="flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg" href="/dashboard/admin/communication">
<span class="material-icons text-[20px]">settings</span>
<span class="font-medium">Communication Hub</span>
</a>
</div>
</aside>
<!-- Main Content -->
<main class="flex-1 overflow-y-auto">
<!-- Top Bar -->
<header class="h-16 bg-white/80 dark:bg-black/10 backdrop-blur-md border-b border-primary/10 flex items-center justify-between px-8 sticky top-0 z-10">
<div class="flex items-center gap-4">
<div class="relative">
<input class="w-64 pl-10 pr-4 py-2 bg-background-light dark:bg-white/5 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50" placeholder="Search data..." type="text"/>
<span class="material-icons absolute left-3 top-2.5 text-slate-400 text-sm">search</span>
</div>
</div>
<div class="flex items-center gap-6">
<!-- Date Picker Placeholder -->
<div class="flex items-center gap-2 px-3 py-1.5 bg-background-light dark:bg-white/5 border border-primary/10 rounded-lg text-xs font-medium cursor-pointer">
<span class="material-icons text-sm">calendar_today</span>
<span>Oct 01, 2023 - Oct 31, 2023</span>
</div>
<button class="relative text-slate-500 hover:text-primary">
<span class="material-icons">notifications</span>
<span class="absolute -top-1 -right-1 w-4 h-4 bg-primary text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-background-dark font-bold">12</span>
</button>
<div class="flex items-center gap-3 pl-6 border-l border-primary/10">
<div class="text-right">
<p class="text-xs font-bold leading-none">Admin Bloombit</p>
<p class="text-[10px] text-slate-500">Super Admin</p>
</div>
<div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center overflow-hidden">
<img alt="Admin Profile" class="w-full h-full object-cover" data-alt="Close up portrait of a male professional admin" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAllv2arw4CT5sh5se3HKMFVKyYXOAW1324wKxujkeuffmY1DhhhTUb1llYzAk_sM7va9f_KPJb5zWOKBQD2TJHPAWkHyzECqLiN2iLHvU_rfybow80K5_hH3w4qrMTwioK102J1bJ8_1J9XyNSbcvlSvwXKmpwg-zMnGWXlKkHWg2SGjXf8kRz78h-7YwhWISO8lzfSxTK5-jedWr5c7-8zqU8QckddM_pMegUm6540ceVN0QEQqbK05hVdzt1j25SMveouqEJl9k"/>
</div>
</div>
</div>
</header>
<div class="p-8">
<!-- Top Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
<!-- Card 1 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Users</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center">+12% <span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold">14,290</p>
</div>
<!-- Card 2 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Earnings</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center">+8.4% <span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold">$1.2M</p>
</div>
<!-- Card 3 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Active Inv.</span>
<span class="text-slate-400 text-[10px] font-bold">Stable</span>
</div>
<p class="text-2xl font-bold">3,842</p>
</div>
<!-- Card 4 -->
<div class="bg-primary/5 dark:bg-primary/10 p-6 rounded-xl border border-primary/20 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-primary font-bold text-xs uppercase tracking-wider">Pending Deposits</span>
<span class="bg-primary text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">ACTION</span>
</div>
<p class="text-2xl font-bold text-primary">$42,910</p>
</div>
<!-- Card 5 -->
<div class="bg-white dark:bg-white/5 p-6 rounded-xl border border-primary/10 shadow-sm">
<div class="flex items-center justify-between mb-2">
<span class="text-slate-500 text-xs font-medium uppercase tracking-wider">Total Deposits</span>
<span class="text-emerald-500 text-[10px] font-bold flex items-center">+22% <span class="material-icons text-[12px]">trending_up</span></span>
</div>
<p class="text-2xl font-bold">$4.8M</p>
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
<div>
<div class="flex justify-between items-center mb-2">
<span class="text-sm font-medium">Starter Plan</span>
<span class="text-sm font-bold">1,240</span>
</div>
<div class="w-full bg-primary/10 h-2 rounded-full">
<div class="bg-primary w-[65%] h-full rounded-full"></div>
</div>
</div>
<div>
<div class="flex justify-between items-center mb-2">
<span class="text-sm font-medium">Growth Plan</span>
<span class="text-sm font-bold">852</span>
</div>
<div class="w-full bg-primary/10 h-2 rounded-full">
<div class="bg-primary w-[45%] h-full rounded-full"></div>
</div>
</div>
<div>
<div class="flex justify-between items-center mb-2">
<span class="text-sm font-medium">Premium Plan</span>
<span class="text-sm font-bold">1,750</span>
</div>
<div class="w-full bg-primary/10 h-2 rounded-full">
<div class="bg-primary w-[80%] h-full rounded-full"></div>
</div>
</div>
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
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
<img alt="User" class="w-full h-full object-cover" data-alt="Portrait of a male crypto investor" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCqIPv-31uhDj7Xw9biLYUl8x5WuYZFp6-8QIv8AEnJzVWlwPvfCnfaoFT8HYl-6e4X9QVgAzvQCSxM9epQcAs9fPAlGwdhU59c4sZmhFE-5lUaIVRWiDrj5T3Hd5_kG_PirkdCQzz4KV2lgtnON8-7pRzYhkFw786oV0thf3TjITk9PLs8lvX12WdDVyyUV8E6JWzpIdXEnkdy-N_n5WQ9B2sot24HpNYANQcfUp_8UMKW6l3AZlT3GTHQsb9xHRrWST0xZTM0x0s"/>
</div>
<div>
<p class="text-sm font-bold">Alex Thompson</p>
<p class="text-[10px] text-slate-500">ID: #BT-8821</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-bold">$12,400.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5">
<span class="material-icons text-primary text-sm">currency_bitcoin</span>
<span class="text-xs font-medium">BTC</span>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold rounded-full uppercase">Reviewing</span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<button class="px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/90">APPROVE</button>
<button class="px-3 py-1.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">REJECT</button>
</div>
</td>
</tr>
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
<img alt="User" class="w-full h-full object-cover" data-alt="Portrait of a female executive investor" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQ45I909e3ZRtdife0fdrQYylz6AmXDJHJ58q2It8gwl69PKfNpJu3dvTfjqCuSebbpuRXJvjed6L9Gpy4-cXY4akVVrm5Sh_r20FjHcBfiS3Tvo9biNjnzDrXSYImE1JPPqSoqXzg2ZcsZjH5RdKjYDbx8zebCK-upTu1xp1aiVmFKL6-ZrwQOiRVQALziTWO9JIZt_tz-38ea34NwRHmgYBTVmsTk0bG9FRBD3qfl-apGJlMZdDmpT9rk3N3pVcAwFrPBZ9Nk3I"/>
</div>
<div>
<p class="text-sm font-bold">Sarah Jenkins</p>
<p class="text-[10px] text-slate-500">ID: #BT-4211</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-bold">$5,000.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5">
<span class="material-icons text-primary text-sm">payments</span>
<span class="text-xs font-medium">USDT</span>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold rounded-full uppercase">Reviewing</span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<button class="px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/90">APPROVE</button>
<button class="px-3 py-1.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">REJECT</button>
</div>
</td>
</tr>
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-slate-200 overflow-hidden">
<img alt="User" class="w-full h-full object-cover" data-alt="Portrait of a businessman" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRiiOOd2RmMDbiYP1n_VlmlWJ5wSBGb-mLkyY08oDMjwFo3BPW99zgsuHO8vUiiNA3Rml9f8CGoC6kyOzHlsHdQg5v7c13vV5Qh8m41sqLqJc3OGsb8g8TeAz5CU0MFicmgBvaHrWnSuMbwTUVnPSKNccnTqGrQbF62yQZqw8PJ4Jas-ghaYLDw-aTk6It8mohWxe1z7yZwDA6c5XFIxhBy71w89nzpVe4ZH7demwLbIKO2E1PJGqRHHgfgwK5_YALMC2FAJljl2Y"/>
</div>
<div>
<p class="text-sm font-bold">Marcus Vane</p>
<p class="text-[10px] text-slate-500">ID: #BT-0912</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-bold">$1,250.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5">
<span class="material-icons text-primary text-sm">account_balance</span>
<span class="text-xs font-medium">Bank</span>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-bold rounded-full uppercase">Reviewing</span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<button class="px-3 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:bg-primary/90">APPROVE</button>
<button class="px-3 py-1.5 bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">REJECT</button>
</div>
</td>
</tr>
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
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1.5"></div>
<div class="absolute top-4 left-1.25 w-px h-full bg-slate-200 dark:bg-white/5"></div>
</div>
<div class="flex-1">
<p class="text-sm font-medium">New Registration</p>
<p class="text-xs text-slate-500 mb-1">Emily Clarke just joined Bloombit.</p>
<p class="text-[10px] text-primary font-bold">2 MINUTES AGO</p>
</div>
</div>
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full bg-primary mt-1.5"></div>
<div class="absolute top-4 left-1.25 w-px h-full bg-slate-200 dark:bg-white/5"></div>
</div>
<div class="flex-1">
<p class="text-sm font-medium">Withdrawal Request</p>
<p class="text-xs text-slate-500 mb-1">John Doe requested a $500 payout.</p>
<p class="text-[10px] text-primary font-bold">14 MINUTES AGO</p>
</div>
</div>
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-1.5"></div>
<div class="absolute top-4 left-1.25 w-px h-full bg-slate-200 dark:bg-white/5"></div>
</div>
<div class="flex-1">
<p class="text-sm font-medium">System Alert</p>
<p class="text-xs text-slate-500 mb-1">AI Trading Bot 04 re-balanced tiers.</p>
<p class="text-[10px] text-primary font-bold">1 HOUR AGO</p>
</div>
</div>
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full bg-slate-300 mt-1.5"></div>
</div>
<div class="flex-1">
<p class="text-sm font-medium">Plan Activated</p>
<p class="text-xs text-slate-500 mb-1">Premium Plan started for ID #9201.</p>
<p class="text-[10px] text-primary font-bold">3 HOURS AGO</p>
</div>
</div>
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
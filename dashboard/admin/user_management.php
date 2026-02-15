<?php require_once __DIR__ . '/../../includes/admin-check.php'; require_once __DIR__ . '/../../includes/helpers.php'; $siteName = get_site_name();
$currentPage = 'users';
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Admin User Directory</title>
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
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 max-w-7xl mx-auto">
<!-- Header & Search -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<h1 class="text-2xl font-bold">User Directory</h1>
<p class="text-slate-500 text-sm">Manage and monitor 12,482 platform users</p>
</div>
<div class="flex items-center gap-3">
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-background-dark font-semibold rounded-lg hover:brightness-105 transition-all shadow-sm">
<span class="material-icons text-sm">person_add</span>
                    Manual Add User
                </button>
</div>
</div>
<!-- Filters -->
<div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-slate-200 dark:border-zinc-800 flex flex-wrap items-center gap-4 mb-6 shadow-sm">
<div class="flex-1 min-w-[300px] relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-zinc-800 border-none rounded-lg focus:ring-2 focus:ring-primary text-sm" placeholder="Search by name, email, or wallet address..." type="text"/>
</div>
<div class="flex items-center gap-2">
<label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Status</label>
<select class="bg-slate-50 dark:bg-zinc-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary py-2 pr-10">
<option>All Statuses</option>
<option>Active</option>
<option>Suspended</option>
<option>Pending KYC</option>
</select>
</div>
<div class="flex items-center gap-2 border-l border-slate-200 dark:border-zinc-700 pl-4">
<button class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg text-slate-600 transition-colors">
<span class="material-icons">filter_list</span>
</button>
<button class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg text-slate-600 transition-colors">
<span class="material-icons">download</span>
</button>
</div>
</div>
<!-- User Table -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden relative">
<div class="overflow-x-auto">
<table class="w-full text-left text-sm">
<thead class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
<tr>
<th class="px-6 py-4 w-10">
<input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Name</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Total Balance</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Active Plans</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Registration</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">KYC Status</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
<!-- User Row 1 (Selected/Active State Example) -->
<tr class="hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent">
<td class="px-6 py-4">
<input checked="" class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-600 text-xs">JD</div>
<div>
<div class="font-semibold text-slate-900 dark:text-white">James Donovan</div>
<div class="text-xs text-slate-500">j.donovan@gmail.com</div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white">$42,500.20</div>
<div class="text-[10px] text-slate-400 font-mono tracking-tighter">1.2482 BTC</div>
</td>
<td class="px-6 py-4">
<span class="bg-primary/20 text-slate-900 dark:text-primary px-2 py-0.5 rounded-full text-xs font-bold">3 Plans</span>
</td>
<td class="px-6 py-4 text-slate-500">Oct 12, 2023</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Verified
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500" title="Edit">
<span class="material-icons text-lg">edit</span>
</button>
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500" title="Reset Password">
<span class="material-icons text-lg">lock_reset</span>
</button>
<button class="p-1.5 hover:bg-red-50 text-red-400 rounded-md" title="Suspend">
<span class="material-icons text-lg">block</span>
</button>
</div>
</td>
</tr>
<!-- User Row 2 -->
<tr class="hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent">
<td class="px-6 py-4">
<input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<img alt="Sarah" class="w-9 h-9 rounded-full object-cover" data-alt="Female user avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDt_dnbIcOm0YdaD7AoksOzFFifELFe8O2yTj-utvHzV_MOTbDfQ4l_JYoYgMJU-Emh-GRLC7xtPufmGhrxu8pUH_94JYwS49MSygbUQd3LvFZ5aelaGbqQUuD_ehmz6E8AJIcfQxoADLwk7Lfo-rEIprBDzypBrm-CcckYe3VT0l2wKMEOGvHBqTw8Ru5PwiBzOmXBCQmt7JNc1vQx2En6OJsyYHPSLjKrJuCQDJUoVuGDjziw-gt6ciPBXT238oJqZUR922rdXBE"/>
<div>
<div class="font-semibold text-slate-900 dark:text-white">Sarah Jenkins</div>
<div class="text-xs text-slate-500">sarah.j@outlook.com</div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white">$8,210.00</div>
<div class="text-[10px] text-slate-400 font-mono tracking-tighter">0.2410 BTC</div>
</td>
<td class="px-6 py-4">
<span class="bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded-full text-xs font-bold">1 Plan</span>
</td>
<td class="px-6 py-4 text-slate-500">Nov 01, 2023</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>
                                    Pending
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">edit</span></button>
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">lock_reset</span></button>
<button class="p-1.5 hover:bg-red-50 text-red-400 rounded-md"><span class="material-icons text-lg">block</span></button>
</div>
</td>
</tr>
<!-- User Row 3 -->
<tr class="hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent">
<td class="px-6 py-4">
<input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center font-bold text-purple-600 text-xs">MA</div>
<div>
<div class="font-semibold text-slate-900 dark:text-white">Marcus Aurelius</div>
<div class="text-xs text-slate-500">stoic.trader@proton.me</div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white">$0.00</div>
<div class="text-[10px] text-slate-400 font-mono tracking-tighter">0.0000 BTC</div>
</td>
<td class="px-6 py-4">
<span class="bg-slate-100 dark:bg-zinc-800 text-slate-500 px-2 py-0.5 rounded-full text-xs font-bold">0 Plans</span>
</td>
<td class="px-6 py-4 text-slate-500">Oct 28, 2023</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Suspended
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">edit</span></button>
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">lock_reset</span></button>
<button class="p-1.5 bg-red-100 text-red-600 rounded-md"><span class="material-icons text-lg">lock_open</span></button>
</div>
</td>
</tr>
<!-- User Row 4 -->
<tr class="hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent">
<td class="px-6 py-4">
<input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<img alt="David" class="w-9 h-9 rounded-full object-cover" data-alt="Male user avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAMXIPfwMqi9TkFqN-DPFdyBla0kEfU9Yfbei9KFMWALS0KzbY2IglMHFh9ybOQFry8hE5fGp5E0XEsGvp4byD4f-acAYBkLJ4Wdq0SYu3ywQ1aal2wClRlTBzpf_FD2R6dLWO7a9hHdkNXP35NeE4cgoG7zM0Pp-3k1vbZNiPEMTLYEe5ABUkiHOCT3t92X9RpYGpxYgbZoj_uM2hBEnermSXymJnN6MA0UmDFgii6Snj4QRjZUja3tcj0fXJhE1Cae7dEm-kPTvE"/>
<div>
<div class="font-semibold text-slate-900 dark:text-white">David Chen</div>
<div class="text-xs text-slate-500">dchen.finance@yahoo.com</div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white">$120,400.00</div>
<div class="text-[10px] text-slate-400 font-mono tracking-tighter">3.5200 BTC</div>
</td>
<td class="px-6 py-4">
<span class="bg-primary/20 text-slate-900 dark:text-primary px-2 py-0.5 rounded-full text-xs font-bold">5 Plans</span>
</td>
<td class="px-6 py-4 text-slate-500">Sep 15, 2023</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Verified
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">edit</span></button>
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500"><span class="material-icons text-lg">lock_reset</span></button>
<button class="p-1.5 hover:bg-red-50 text-red-400 rounded-md"><span class="material-icons text-lg">block</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="px-6 py-4 border-t border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<span class="text-xs text-slate-500">Showing 1-10 of 12,482 users</span>
<div class="flex items-center gap-2">
<button class="p-1 border border-slate-200 dark:border-zinc-800 rounded hover:bg-slate-50 text-slate-400"><span class="material-icons text-sm">chevron_left</span></button>
<button class="px-3 py-1 bg-primary text-background-dark font-bold text-xs rounded">1</button>
<button class="px-3 py-1 hover:bg-slate-100 dark:hover:bg-zinc-800 text-xs rounded">2</button>
<button class="px-3 py-1 hover:bg-slate-100 dark:hover:bg-zinc-800 text-xs rounded">3</button>
<span class="text-xs px-1 text-slate-400">...</span>
<button class="px-3 py-1 hover:bg-slate-100 dark:hover:bg-zinc-800 text-xs rounded">1,249</button>
<button class="p-1 border border-slate-200 dark:border-zinc-800 rounded hover:bg-slate-50 text-slate-400"><span class="material-icons text-sm">chevron_right</span></button>
</div>
</div>
</div>
</main>
<!-- Floating Batch Actions Bar (Visible when rows selected) -->
<div class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-zinc-900 dark:bg-zinc-800 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-6 z-50 animate-bounce-subtle">
<div class="flex items-center gap-2 pr-6 border-r border-zinc-700">
<span class="bg-primary text-background-dark w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
<span class="text-sm font-medium">User Selected</span>
</div>
<div class="flex items-center gap-4">
<button class="flex items-center gap-2 text-sm font-medium hover:text-primary transition-colors">
<span class="material-icons text-lg">mail</span>
                Send Email
            </button>
<button class="flex items-center gap-2 text-sm font-medium hover:text-primary transition-colors">
<span class="material-icons text-lg">account_balance_wallet</span>
                Adjust Balance
            </button>
<button class="flex items-center gap-2 text-sm font-medium text-red-400 hover:text-red-300 transition-colors">
<span class="material-icons text-lg">delete</span>
                Delete
            </button>
</div>
<button class="ml-4 p-1 hover:bg-zinc-700 rounded-full text-zinc-400">
<span class="material-icons text-sm">close</span>
</button>
</div>
<!-- Right Side Profile Drawer -->
<div class="fixed inset-y-0 right-0 w-[420px] bg-white dark:bg-zinc-900 shadow-2xl z-50 border-l border-slate-200 dark:border-zinc-800 flex flex-col translate-x-0 transition-transform">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">User Profile</h2>
<button class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-full transition-colors text-slate-400">
<span class="material-icons">close</span>
</button>
</div>
<div class="flex-1 overflow-y-auto p-6 space-y-8">
<!-- Profile Header -->
<div class="flex items-center gap-4">
<div class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center text-background-dark text-2xl font-bold">JD</div>
<div>
<h3 class="text-xl font-bold">James Donovan</h3>
<p class="text-slate-500 text-sm">UID: #7728190332</p>
<div class="flex items-center gap-2 mt-1">
<span class="bg-green-100 text-green-700 text-[10px] font-bold uppercase px-2 py-0.5 rounded tracking-widest">Active</span>
<span class="text-[10px] text-slate-400">• Last active: 2 hours ago</span>
</div>
</div>
</div>
<!-- Wallet Breakdown -->
<div>
<div class="flex items-center justify-between mb-4">
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Wallet Breakdown</h4>
<button class="text-primary text-xs font-bold hover:underline">Full History</button>
</div>
<div class="grid grid-cols-1 gap-3">
<div class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-xl flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="bg-orange-100 p-2 rounded-lg"><span class="material-icons text-orange-600">currency_bitcoin</span></div>
<div>
<div class="text-xs font-bold">Bitcoin</div>
<div class="text-[10px] text-slate-500">BTC</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold">1.24820000</div>
<div class="text-[10px] text-slate-400">$34,210.00</div>
</div>
</div>
<div class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-xl flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="bg-blue-100 p-2 rounded-lg"><span class="material-icons text-blue-600">diamond</span></div>
<div>
<div class="text-xs font-bold">Ethereum</div>
<div class="text-[10px] text-slate-500">ETH</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold">4.82100000</div>
<div class="text-[10px] text-slate-400">$8,290.20</div>
</div>
</div>
</div>
</div>
<!-- Active Investments -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Active Investments</h4>
<div class="space-y-3">
<div class="p-4 border border-slate-200 dark:border-zinc-800 rounded-xl">
<div class="flex justify-between items-start mb-2">
<div>
<div class="text-sm font-bold">Alpha Multi-Asset Yield</div>
<p class="text-[10px] text-slate-500">18.5% Fixed APY</p>
</div>
<span class="text-xs font-bold text-primary">$12,000</span>
</div>
<div class="w-full bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full w-2/3"></div>
</div>
<div class="flex justify-between mt-1.5">
<span class="text-[10px] text-slate-400">Day 62 of 90</span>
<span class="text-[10px] font-bold text-green-500">+$1,420.00 earned</span>
</div>
</div>
<div class="p-4 border border-slate-200 dark:border-zinc-800 rounded-xl">
<div class="flex justify-between items-start mb-2">
<div>
<div class="text-sm font-bold">BTC Halving Strategy</div>
<p class="text-[10px] text-slate-500">Long Term Growth</p>
</div>
<span class="text-xs font-bold text-primary">$25,000</span>
</div>
<div class="w-full bg-slate-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full w-1/4"></div>
</div>
<div class="flex justify-between mt-1.5">
<span class="text-[10px] text-slate-400">Day 14 of 365</span>
<span class="text-[10px] font-bold text-green-500">+$210.10 earned</span>
</div>
</div>
</div>
</div>
<!-- Security & Login -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Security Settings</h4>
<div class="space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-icons text-green-500">verified_user</span>
<div class="text-sm font-medium">Two-Factor Auth (2FA)</div>
</div>
<span class="text-xs font-bold text-green-600">ENABLED</span>
</div>
<div class="bg-slate-50 dark:bg-zinc-800 p-4 rounded-xl">
<p class="text-[10px] font-bold text-slate-400 uppercase mb-3">Recent Login Activity</p>
<div class="space-y-3">
<div class="flex justify-between text-xs">
<span class="text-slate-600 dark:text-zinc-400">192.168.1.44 (London, UK)</span>
<span class="text-slate-400">Today, 14:22</span>
</div>
<div class="flex justify-between text-xs">
<span class="text-slate-600 dark:text-zinc-400">88.12.9.201 (VPN - France)</span>
<span class="text-slate-400">Yesterday, 09:15</span>
</div>
</div>
</div>
</div>
</div>
<!-- Internal Notes -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Internal Admin Notes</h4>
<div class="relative">
<textarea class="w-full h-32 bg-slate-50 dark:bg-zinc-800 border-none rounded-xl text-sm p-4 focus:ring-1 focus:ring-primary resize-none" placeholder="Add a note about this user..."></textarea>
<div class="absolute bottom-3 right-3 flex items-center gap-2 text-[10px] text-slate-400">
<span class="material-icons text-sm">cloud_done</span>
                        Auto-saved
                    </div>
</div>
</div>
</div>
<!-- Drawer Actions -->
<div class="p-6 border-t border-slate-200 dark:border-zinc-800 grid grid-cols-2 gap-3">
<button class="px-4 py-2 bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-bold rounded-lg hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                View Activity Log
            </button>
<button class="px-4 py-2 bg-primary text-background-dark font-bold rounded-lg hover:brightness-105 transition-all">
                Update Profile
            </button>
</div>
</div>
<script src="/js/app.js"></script>
</body></html>
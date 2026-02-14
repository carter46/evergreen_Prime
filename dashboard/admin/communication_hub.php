<?php require_once __DIR__ . '/../../includes/auth-check.php'; ?>
<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bloombit | Admin Broadcast Hub</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
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
                        "display": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        .editor-toolbar button:hover { background-color: rgba(242, 223, 13, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 transition-colors duration-300">
<!-- Sidebar Navigation (Static) -->
<aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 hidden lg:flex flex-col">
<a class="p-6 flex items-center gap-3" href="/">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-outlined text-slate-900 font-bold">account_balance_wallet</span>
</div>
<span class="text-xl font-bold tracking-tight">Bloombit</span>
</a>
<nav class="flex-1 px-4 py-4 space-y-1">
<a class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors" href="/dashboard/admin">
<span class="material-icons-outlined text-sm">dashboard</span> Dashboard
            </a>
<a class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors" href="/dashboard/admin/users">
<span class="material-icons-outlined text-sm">people</span> Users
            </a>
<a class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors" href="/dashboard/admin/plans">
<span class="material-icons-outlined text-sm">account_balance</span> Investments
            </a>
<a class="flex items-center gap-3 px-4 py-2.5 bg-primary/20 text-slate-900 font-semibold rounded-lg" href="/dashboard/admin/communication">
<span class="material-icons-outlined text-sm">campaign</span> Communications
            </a>
<a class="flex items-center gap-3 px-4 py-2.5 text-slate-500 hover:bg-slate-50 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-sm">settings</span> Settings
            </a>
</nav>
<div class="p-4 border-t border-slate-100">
<div class="flex items-center gap-3 p-2">
<img class="w-10 h-10 rounded-full object-cover" data-alt="Admin user profile picture portrait" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCNRkkunVUkkFHc_pxCKD0XsJ7mzNBkGHiP6Q32VqLJgxUieyxwyGx3J0ZQZXiMz0XBXw2cxwVzp-fHPX1wlYjWeVIbhYrEBSY45VGjxnSb-tRvwFlQwDNrxR2WtIdLgotmPxrSyDYXIvn-vfTES0vbO8bFlSZAyetPZf6Arc-KxnemMqc3IJUijB6RlZxtw4M4WWVrBb1kJz9ftkDd2nEvtMJ8VxA39a3a--WRi0JRWOqAfiWubK70VbYyFsVfFWjLWKM1io9dr9k"/>
<div>
<p class="text-sm font-semibold">Alex Rivera</p>
<p class="text-xs text-slate-400">System Admin</p>
</div>
</div>
</div>
</aside>
<!-- Main Content -->
<main class="lg:ml-64 p-8 min-h-screen">
<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<nav class="flex text-xs text-slate-400 gap-2 mb-1">
<span>Admin</span>
<span>/</span>
<span class="text-slate-600">Communications Hub</span>
</nav>
<h1 class="text-2xl font-bold text-slate-900">Broadcast &amp; Communication Hub</h1>
</div>
<div class="flex items-center gap-3">
<button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-lg flex items-center gap-2 hover:bg-slate-50 transition-colors">
<span class="material-icons-outlined text-base">save</span> Save Draft
                </button>
<button type="submit" form="broadcast-form" class="px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-icons-outlined text-base">send</span> Send Broadcast
                </button>
</div>
</header>
<div class="grid grid-cols-12 gap-8">
<!-- Composition Area -->
<div class="col-span-12 xl:col-span-8 space-y-6">
<!-- Tabs -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
<div class="flex border-b border-slate-100">
<button class="flex-1 px-6 py-4 flex items-center justify-center gap-2 font-semibold text-slate-900 border-b-2 border-primary">
<span class="material-icons-outlined text-xl">email</span> Email Broadcast
                        </button>
<button class="flex-1 px-6 py-4 flex items-center justify-center gap-2 font-medium text-slate-400 hover:text-slate-600 transition-colors">
<span class="material-icons-outlined text-xl">notifications</span> In-App Notifications
                        </button>
</div>
<form id="broadcast-form" class="p-6 space-y-6">
<!-- Message Metadata -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Campaign Name</label>
<input name="campaign" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. Q4 Growth Update" type="text"/>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Subject Line</label>
<input name="subject" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:ring-primary focus:border-primary" placeholder="Exciting updates for your Bloombit portfolio" type="text" required/>
</div>
</div>
<!-- Rich Text Editor -->
<div class="space-y-2">
<div class="flex items-center justify-between">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Message Body</label>
<div class="flex gap-2">
<button class="px-2 py-1 bg-slate-100 text-[10px] font-bold text-slate-500 rounded hover:bg-slate-200 uppercase tracking-tighter">Insert {user_name}</button>
<button class="px-2 py-1 bg-slate-100 text-[10px] font-bold text-slate-500 rounded hover:bg-slate-200 uppercase tracking-tighter">Insert {balance}</button>
</div>
</div>
<div class="border border-slate-200 rounded-lg overflow-hidden">
<div class="editor-toolbar bg-slate-50 border-b border-slate-200 p-2 flex gap-1">
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_bold</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_italic</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_list_bulleted</span></button>
<div class="w-px h-4 bg-slate-300 mx-1 self-center"></div>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">link</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">image</span></button>
</div>
<textarea name="body" class="w-full border-none focus:ring-0 p-4 text-slate-700 leading-relaxed" placeholder="Write your message here. Use placeholders for dynamic content..." rows="8" required></textarea>
</div>
</div>
<!-- Targeting Logic -->
<div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
<h3 class="text-sm font-bold text-slate-700 flex items-center gap-2 mb-4">
<span class="material-icons-outlined text-base">filter_alt</span> Recipient Targeting
                            </h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="space-y-2">
<label class="text-[11px] font-bold text-slate-500 uppercase">User Segment</label>
<select class="w-full text-sm bg-white border-slate-200 rounded-lg">
<option>Active Investors Only</option>
<option>All Users</option>
<option>KYC Verified Only</option>
<option>Custom Segment...</option>
</select>
</div>
<div class="space-y-2">
<label class="text-[11px] font-bold text-slate-500 uppercase">Minimum Balance</label>
<input class="w-full text-sm bg-white border-slate-200 rounded-lg" placeholder="$ 0.00" type="number"/>
</div>
<div class="space-y-2">
<label class="text-[11px] font-bold text-slate-500 uppercase">Last Login</label>
<select class="w-full text-sm bg-white border-slate-200 rounded-lg">
<option>Last 7 Days</option>
<option>Last 30 Days</option>
<option>Inactive &gt; 90 Days</option>
</select>
</div>
</div>
<div class="mt-4 flex items-center justify-between text-xs font-medium text-slate-500">
<span>Estimated Reach: <strong class="text-slate-900">4,285 Users</strong></span>
<button class="text-primary-dark font-bold hover:underline">Refine Segments</button>
</div>
</div>
</div>
<div id="broadcast-message" class="text-sm hidden"></div>
<button type="submit" class="px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-icons-outlined text-base">send</span> Send Broadcast
                </button>
</form>
</div>
<!-- Scheduling Section -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
<h3 class="text-sm font-bold text-slate-700 flex items-center gap-2 mb-6">
<span class="material-icons-outlined text-base">schedule</span> Schedule Broadcast
                    </h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
<div class="flex gap-4">
<label class="flex-1 cursor-pointer">
<input checked="" class="hidden peer" name="send_time" type="radio"/>
<div class="p-4 rounded-xl border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all text-center">
<p class="font-bold text-sm">Send Immediately</p>
<p class="text-[10px] text-slate-400">Deploy as soon as possible</p>
</div>
</label>
<label class="flex-1 cursor-pointer">
<input class="hidden peer" name="send_time" type="radio"/>
<div class="p-4 rounded-xl border-2 border-slate-100 peer-checked:border-primary peer-checked:bg-primary/5 transition-all text-center">
<p class="font-bold text-sm">Schedule Later</p>
<p class="text-[10px] text-slate-400">Pick a specific date/time</p>
</div>
</label>
</div>
<div class="flex items-center gap-4">
<div class="flex-1 relative">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">calendar_today</span>
<input class="w-full pl-10 bg-slate-50 border-slate-200 rounded-lg text-sm text-slate-400 cursor-not-allowed" disabled="" type="date"/>
</div>
<div class="flex-1 relative">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">schedule</span>
<input class="w-full pl-10 bg-slate-50 border-slate-200 rounded-lg text-sm text-slate-400 cursor-not-allowed" disabled="" type="time"/>
</div>
</div>
</div>
</div>
</div>
<!-- Preview Sidebar -->
<div class="col-span-12 xl:col-span-4 space-y-6">
<div class="bg-white rounded-xl shadow-sm border border-slate-200 sticky top-8">
<div class="p-4 border-b border-slate-100 flex items-center justify-between">
<span class="text-sm font-bold text-slate-700 uppercase tracking-tight">Real-Time Preview</span>
<div class="flex bg-slate-100 p-1 rounded-lg">
<button class="p-1 px-3 bg-white rounded shadow-sm text-xs font-bold flex items-center gap-1">
<span class="material-icons-outlined text-sm">desktop_windows</span> Desktop
                            </button>
<button class="p-1 px-3 text-slate-400 hover:text-slate-600 text-xs font-bold flex items-center gap-1">
<span class="material-icons-outlined text-sm">smartphone</span> Mobile
                            </button>
</div>
</div>
<div class="p-6 bg-slate-50 aspect-[3/4] flex items-center justify-center">
<!-- Email Mockup Container -->
<div class="bg-white w-full h-full shadow-lg rounded border border-slate-200 flex flex-col overflow-hidden">
<div class="h-1 bg-primary"></div>
<div class="p-4 flex items-center justify-center border-b border-slate-50 bg-white">
<div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-outlined text-xs">account_balance_wallet</span>
</div>
</div>
<div class="p-4 space-y-4 custom-scrollbar overflow-y-auto">
<h4 class="text-lg font-bold text-slate-900 leading-tight">Hello {user_name},</h4>
<div class="space-y-3">
<div class="h-2 w-full bg-slate-100 rounded"></div>
<div class="h-2 w-full bg-slate-100 rounded"></div>
<div class="h-2 w-3/4 bg-slate-100 rounded"></div>
</div>
<div class="bg-slate-50 p-3 rounded-lg border border-slate-100 text-center space-y-1">
<p class="text-[10px] text-slate-400 font-bold uppercase">Current Balance</p>
<p class="text-lg font-bold text-slate-900">{balance} BTC</p>
</div>
<div class="h-2 w-full bg-slate-100 rounded mt-4"></div>
<div class="h-10 bg-primary rounded flex items-center justify-center text-xs font-bold text-slate-900">
                                    Access Your Dashboard
                                </div>
</div>
<div class="mt-auto p-4 border-t border-slate-50 text-[10px] text-slate-400 text-center">
<p>© 2024 Bloombit Fintech. All rights reserved.</p>
<p class="mt-1">Unsubscribe | View in Browser</p>
</div>
</div>
</div>
<div class="p-4 bg-slate-50 border-t border-slate-100">
<button class="w-full py-2.5 text-xs font-bold text-slate-600 border border-slate-200 rounded-lg hover:bg-white transition-all flex items-center justify-center gap-2">
<span class="material-icons-outlined text-sm">send_to_mobile</span> Send Test to My Email
                        </button>
</div>
</div>
</div>
<!-- History Table -->
<div class="col-span-12">
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
<div class="p-6 border-b border-slate-100 flex items-center justify-between">
<h3 class="text-lg font-bold text-slate-900">Broadcast History</h3>
<div class="flex gap-2">
<div class="relative">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
<input class="pl-9 pr-4 py-1.5 text-xs bg-slate-50 border-slate-200 rounded-lg" placeholder="Search campaigns..." type="text"/>
</div>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<tr>
<th class="px-6 py-4">Campaign Name &amp; Type</th>
<th class="px-6 py-4">Total Recipients</th>
<th class="px-6 py-4">Performance</th>
<th class="px-6 py-4">Status</th>
<th class="px-6 py-4">Date Sent</th>
<th class="px-6 py-4 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100">
<tr class="hover:bg-slate-50/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
<span class="material-icons-outlined text-sm">email</span>
</div>
<div>
<p class="text-sm font-semibold text-slate-900">Security Update 2.1</p>
<p class="text-xs text-slate-400">Email Broadcast</p>
</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm font-medium text-slate-700">12,450</span>
</td>
<td class="px-6 py-4">
<div class="space-y-1">
<div class="flex items-center gap-4 text-[11px]">
<span class="text-slate-500">Open Rate: <strong class="text-emerald-600">42%</strong></span>
<span class="text-slate-500">Click Rate: <strong class="text-emerald-600">8%</strong></span>
</div>
<div class="w-32 h-1 bg-slate-100 rounded-full overflow-hidden">
<div class="bg-emerald-500 h-full w-[42%]"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">Sent</span>
</td>
<td class="px-6 py-4">
<p class="text-xs text-slate-600">Oct 24, 2023</p>
<p class="text-[10px] text-slate-400">09:15 AM</p>
</td>
<td class="px-6 py-4 text-right">
<button class="p-1 hover:bg-slate-100 rounded text-slate-400"><span class="material-icons-outlined text-sm">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-slate-50/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
<span class="material-icons-outlined text-sm">notifications</span>
</div>
<div>
<p class="text-sm font-semibold text-slate-900">New Token Listing: $BTC</p>
<p class="text-xs text-slate-400">In-App Notification</p>
</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm font-medium text-slate-700">8,200</span>
</td>
<td class="px-6 py-4">
<div class="space-y-1">
<div class="flex items-center gap-4 text-[11px]">
<span class="text-slate-500">Read Rate: <strong class="text-amber-600">65%</strong></span>
</div>
<div class="w-32 h-1 bg-slate-100 rounded-full overflow-hidden">
<div class="bg-amber-400 h-full w-[65%]"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">Scheduled</span>
</td>
<td class="px-6 py-4">
<p class="text-xs text-slate-600">Oct 28, 2023</p>
<p class="text-[10px] text-slate-400">14:00 PM</p>
</td>
<td class="px-6 py-4 text-right">
<button class="p-1 hover:bg-slate-100 rounded text-slate-400"><span class="material-icons-outlined text-sm">more_vert</span></button>
</td>
</tr>
<tr class="hover:bg-slate-50/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="p-2 bg-slate-100 text-slate-600 rounded-lg">
<span class="material-icons-outlined text-sm">drafts</span>
</div>
<div>
<p class="text-sm font-semibold text-slate-900">Holiday Rewards Bonus</p>
<p class="text-xs text-slate-400">Email Broadcast</p>
</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm font-medium text-slate-700">—</span>
</td>
<td class="px-6 py-4">
<span class="text-[10px] text-slate-400 italic">No data yet</span>
</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full uppercase">Draft</span>
</td>
<td class="px-6 py-4">
<p class="text-xs text-slate-600">—</p>
</td>
<td class="px-6 py-4 text-right">
<button class="p-1 hover:bg-slate-100 rounded text-slate-400"><span class="material-icons-outlined text-sm">more_vert</span></button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 border-t border-slate-100 flex items-center justify-between">
<p class="text-xs text-slate-500 font-medium">Showing 1-3 of 42 campaigns</p>
<div class="flex gap-2">
<button class="p-1 px-3 border border-slate-200 rounded-lg text-xs font-bold hover:bg-slate-50">Prev</button>
<button class="p-1 px-3 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold">Next</button>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- Success Confirmation Overlay (Hidden by default, for visualization of context) -->
<div class="fixed bottom-8 right-8 bg-slate-900 text-white p-4 rounded-xl shadow-2xl flex items-center gap-4 border-l-4 border-primary max-w-sm hidden">
<div class="w-8 h-8 bg-primary text-slate-900 rounded-full flex items-center justify-center">
<span class="material-icons-outlined text-sm">check</span>
</div>
<div>
<p class="text-sm font-bold">Broadcast Sent Successfully</p>
<p class="text-xs text-slate-400">Your campaign has been added to the queue for 4,285 recipients.</p>
</div>
<button class="text-slate-500 hover:text-white"><span class="material-icons-outlined text-lg">close</span></button>
</div>
<script src="/js/app.js"></script>
</body></html>
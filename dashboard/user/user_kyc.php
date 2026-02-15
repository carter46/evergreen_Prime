<?php require_once __DIR__ . '/../../includes/auth-check.php'; require_once __DIR__ . '/../../includes/helpers.php'; $siteName = get_site_name();
$currentPage = 'kyc';
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | KYC Verification</title>
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
              borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
          },
        }
    </script>
<style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e2d5; border-radius: 10px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-800 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 overflow-y-auto">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="p-4 sm:p-6 max-w-[1600px] mx-auto grid grid-cols-12 gap-6">
<!-- Header & Top Controls -->
<div class="col-span-12 flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h1 class="text-2xl font-bold">Identity Verification Requests</h1>
<p class="text-sm text-zinc-500 dark:text-zinc-400">Manage and audit user KYC submissions for regulatory compliance.</p>
</div>
<div class="flex items-center gap-3">
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-sm">search</span>
<input class="pl-9 pr-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded text-sm w-64 focus:ring-1 focus:ring-primary focus:border-primary outline-none" placeholder="Search User ID or Name..." type="text"/>
</div>
<button class="bg-primary hover:bg-yellow-400 text-zinc-900 px-4 py-2 rounded text-sm font-semibold flex items-center gap-2 transition-colors">
<span class="material-icons text-sm">filter_list</span>
                    Advanced Filters
                </button>
</div>
</div>
<!-- Left Column: Request List & History -->
<div class="col-span-12 lg:col-span-4 space-y-6">
<!-- Filter Tabs -->
<div class="bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg flex gap-1">
<button class="flex-1 py-1.5 text-xs font-semibold rounded bg-white dark:bg-zinc-700 shadow-sm">Pending (12)</button>
<button class="flex-1 py-1.5 text-xs font-semibold rounded hover:bg-white/50 dark:hover:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400">Approved</button>
<button class="flex-1 py-1.5 text-xs font-semibold rounded hover:bg-white/50 dark:hover:bg-zinc-700/50 text-zinc-500 dark:text-zinc-400">Rejected</button>
</div>
<!-- Request Table/List -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
<div class="max-h-[500px] overflow-y-auto custom-scrollbar">
<table class="w-full text-left text-sm">
<thead class="bg-zinc-50 dark:bg-zinc-800/50 sticky top-0 z-10">
<tr class="text-zinc-500 dark:text-zinc-400 border-b border-zinc-200 dark:border-zinc-800">
<th class="px-4 py-3 font-medium">User</th>
<th class="px-4 py-3 font-medium">Type</th>
<th class="px-4 py-3 text-right">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
<!-- Active Selection Row -->
<tr class="bg-primary/5 border-l-4 border-primary">
<td class="px-4 py-4">
<p class="font-semibold text-zinc-900 dark:text-white">Marcus Thorne</p>
<p class="text-[10px] text-zinc-500">ID: #BB-89210 • 12m ago</p>
</td>
<td class="px-4 py-4">
<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[10px] font-bold uppercase">Passport</span>
</td>
<td class="px-4 py-4 text-right">
<button class="text-xs font-bold text-zinc-900 bg-primary px-3 py-1 rounded">Reviewing</button>
</td>
</tr>
<!-- Regular Row -->
<tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-4 py-4">
<p class="font-medium">Elena Rodriguez</p>
<p class="text-[10px] text-zinc-500">ID: #BB-89215 • 45m ago</p>
</td>
<td class="px-4 py-4">
<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[10px] font-bold uppercase">ID Card</span>
</td>
<td class="px-4 py-4 text-right">
<button class="text-xs font-semibold text-zinc-500 hover:text-primary">Review</button>
</td>
</tr>
<tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-4 py-4">
<p class="font-medium">Sarah Jenkins</p>
<p class="text-[10px] text-zinc-500">ID: #BB-89218 • 1h ago</p>
</td>
<td class="px-4 py-4">
<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[10px] font-bold uppercase">License</span>
</td>
<td class="px-4 py-4 text-right">
<button class="text-xs font-semibold text-zinc-500 hover:text-primary">Review</button>
</td>
</tr>
<tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
<td class="px-4 py-4">
<p class="font-medium">David Kim</p>
<p class="text-[10px] text-zinc-500">ID: #BB-89222 • 2h ago</p>
</td>
<td class="px-4 py-4">
<span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[10px] font-bold uppercase">Passport</span>
</td>
<td class="px-4 py-4 text-right">
<button class="text-xs font-semibold text-zinc-500 hover:text-primary">Review</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- Compliance History Widget -->
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5">
<div class="flex items-center justify-between mb-4">
<h3 class="text-xs font-bold uppercase tracking-widest text-zinc-400">Recent Activity</h3>
<span class="material-icons text-zinc-300 text-sm">history</span>
</div>
<div class="space-y-4">
<div class="flex items-start gap-3">
<div class="w-1.5 h-1.5 mt-1.5 rounded-full bg-green-500"></div>
<div>
<p class="text-[11px] font-medium leading-none">Approved: Alex Wong</p>
<p class="text-[9px] text-zinc-400 mt-1">By Admin: Sarah M. • 5m ago</p>
</div>
</div>
<div class="flex items-start gap-3">
<div class="w-1.5 h-1.5 mt-1.5 rounded-full bg-red-500"></div>
<div>
<p class="text-[11px] font-medium leading-none">Rejected: Tom H.</p>
<p class="text-[9px] text-zinc-400 mt-1">Reason: Blurry Image • 14m ago</p>
</div>
</div>
<div class="flex items-start gap-3">
<div class="w-1.5 h-1.5 mt-1.5 rounded-full bg-green-500"></div>
<div>
<p class="text-[11px] font-medium leading-none">Approved: Maria G.</p>
<p class="text-[9px] text-zinc-400 mt-1">By Admin: System OCR • 22m ago</p>
</div>
</div>
</div>
</div>
</div>
<!-- Right Column: Review Workspace (Main Area) -->
<div class="col-span-12 lg:col-span-8 flex flex-col gap-6">
<div class="grid grid-cols-1 md:grid-cols-2 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm flex-grow">
<!-- Left: Document Viewer -->
<div class="bg-zinc-100 dark:bg-zinc-950 p-6 relative flex flex-col items-center justify-center min-h-[500px]">
<div class="absolute top-4 left-4 flex gap-2">
<span class="px-2 py-1 bg-white dark:bg-zinc-800 rounded shadow-sm text-[10px] font-bold text-zinc-600 dark:text-zinc-300 flex items-center gap-1">
<span class="material-icons text-xs">verified</span> HIGH CONFIDENCE
                        </span>
<span class="px-2 py-1 bg-zinc-900 text-white rounded shadow-sm text-[10px] font-bold flex items-center gap-1">
<span class="material-icons text-xs">document_scanner</span> OCR ACTIVE
                        </span>
</div>
<!-- Main Image Canvas -->
<div class="w-full h-full flex items-center justify-center bg-zinc-200 dark:bg-zinc-900 rounded-lg overflow-hidden border border-dashed border-zinc-300 dark:border-zinc-700 relative group">
<img class="max-w-full max-h-full object-contain shadow-2xl transition-transform duration-300" data-alt="High quality specimen passport document scan" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAgCK-O1Inq1bmIBGQPYaThEAErEy7fsCAEBTYpMsJjqzeTZ7joP9zAuEwTd-6Ssda9dsI3LYTmsmWRQKjSHgio4Z_q73QyWIukALd9a9JO8ImNkSB6UMNIAanm1u3L2-6_J9Oyan7c5_Xm8MSNNDM_G1YY1NqtM19aWHM_Q_chJyT1hyTcej7OXvAg_xXeYs0QvCMKKC_kMe_13mE1CC1u5UG6XZV-Dmb1LlMLv2pCmFyW_e3YPq1I_0vRoQ7mxOyjyz0Oi9P8ezQ"/>
<!-- Floating Controls -->
<div class="absolute bottom-6 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full shadow-lg flex items-center justify-center hover:text-primary"><span class="material-icons">zoom_in</span></button>
<button class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full shadow-lg flex items-center justify-center hover:text-primary"><span class="material-icons">zoom_out</span></button>
<button class="w-10 h-10 bg-white dark:bg-zinc-800 rounded-full shadow-lg flex items-center justify-center hover:text-primary"><span class="material-icons">rotate_right</span></button>
<button class="w-10 h-10 bg-white dark:bg-zinc-900 rounded-full shadow-lg flex items-center justify-center hover:text-primary"><span class="material-icons">fullscreen</span></button>
</div>
</div>
<div class="mt-4 flex gap-4">
<div class="w-20 h-14 bg-white dark:bg-zinc-800 rounded border-2 border-primary overflow-hidden cursor-pointer">
<img class="w-full h-full object-cover" data-alt="Passport front page thumbnail" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2_ZHEFfEZURTWVvxgyk9HzA9YSX8IiN2Z2gU2jpuop_ROhQfuodlp9-PaMqkkQ_Q2w78Ly_3UvrbBTi48eZbsd_Haa2rNhJNe3Mvg50eM8qKUyJ6vJVI9zmwjm-t8JiK_I9enGhfq84eXiTj7NJ5ch_prLXKQdc_qFl0SBarYftylUWrLhUoFXrS3V5vBx48KjdetugZWuAlmKI3KO0Jx87-jzIFSR_J-jO3xZzC2priDYLtcfDq1tWD0brLZ_Si1DZFcyxxlLbQ"/>
</div>
<div class="w-20 h-14 bg-white dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 overflow-hidden opacity-60 cursor-pointer hover:opacity-100">
<img class="w-full h-full object-cover" data-alt="Passport back page thumbnail" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpLnXZhdHBznAfyd7AZTun3y8p_g58loNwBiBh2ztDYV_9wIQd0JV29U2noOlUe3VfZXCTPKnDX41hv9qPdw65jBjS0IoHil5EPXu5m95Yio2LN7OFCrGFrDT2XqpNa9idBNubmNFm6jH-O0nPsfY3RN4UcT3mXFYghBgDAlRr9z_ThH83UrmiDKWyOkjlqBKzsA84Wrg7SeoFzzHj5NDfvQ9g5VTUltbwanz1GVjBqulxsSgmwVozq19dzhl7XTKD4Az-Cvaxlhs"/>
</div>
<div class="w-20 h-14 bg-white dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700 overflow-hidden opacity-60 cursor-pointer hover:opacity-100 flex items-center justify-center">
<span class="material-icons text-zinc-400">face</span>
</div>
</div>
</div>
<!-- Right: User Data Profile -->
<div class="p-8 border-l border-zinc-200 dark:border-zinc-800 flex flex-col">
<div class="flex items-center gap-4 mb-8">
<div class="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center">
<span class="material-icons text-primary text-2xl">person</span>
</div>
<div>
<h2 class="text-xl font-bold">Marcus Thorne</h2>
<p class="text-xs text-zinc-500 font-medium">SUBMITTED: OCT 24, 2023 • 14:22 UTC</p>
</div>
</div>
<div class="space-y-6 flex-grow">
<div class="grid grid-cols-2 gap-6">
<div>
<label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Full Name</label>
<p class="text-sm font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2">Marcus Thorne</p>
<p class="text-[9px] text-green-600 dark:text-green-400 mt-1 flex items-center gap-1"><span class="material-icons text-[10px]">check_circle</span> OCR Match</p>
</div>
<div>
<label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Date of Birth</label>
<p class="text-sm font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2">May 12, 1988</p>
<p class="text-[9px] text-green-600 dark:text-green-400 mt-1 flex items-center gap-1"><span class="material-icons text-[10px]">check_circle</span> Over 18</p>
</div>
</div>
<div>
<label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Residential Address</label>
<p class="text-sm font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2 leading-relaxed">221B Baker Street, London,<br/>NW1 6XE, United Kingdom</p>
</div>
<div class="grid grid-cols-2 gap-6">
<div>
<label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Document Number</label>
<p class="text-sm font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2 tracking-widest">P77203491</p>
</div>
<div>
<label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Expiry Date</label>
<p class="text-sm font-semibold border-b border-zinc-100 dark:border-zinc-800 pb-2">JAN 15, 2028</p>
<p class="text-[9px] text-green-600 dark:text-green-400 mt-1 flex items-center gap-1"><span class="material-icons text-[10px]">event_available</span> Valid Document</p>
</div>
</div>
<div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
<div class="flex items-center gap-2 mb-2 text-zinc-600 dark:text-zinc-300">
<span class="material-icons text-sm">security</span>
<span class="text-[10px] font-bold uppercase tracking-widest">Risk Assessment</span>
</div>
<div class="flex items-center justify-between">
<span class="text-xs">IP Geo-location Match</span>
<span class="text-xs font-bold text-green-500">YES</span>
</div>
<div class="flex items-center justify-between mt-2">
<span class="text-xs">Known Sanctions List</span>
<span class="text-xs font-bold text-green-500">CLEAN</span>
</div>
</div>
</div>
<!-- Action Panel -->
<div class="mt-8 pt-6 border-t border-zinc-200 dark:border-zinc-800">
<div class="flex gap-4">
<button class="flex-[2] py-4 bg-green-600 hover:bg-green-700 text-white rounded font-bold flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-lg shadow-green-600/20">
<span class="material-icons">check_circle</span>
                                APPROVE USER
                            </button>
<div class="flex-1 group relative">
<button class="w-full h-full py-4 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/50 rounded font-bold flex items-center justify-center transition-all">
                                    REJECT
                                </button>
<!-- Reject Dropdown Placeholder -->
<div class="absolute bottom-full mb-2 left-0 w-64 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-xl p-2 hidden group-hover:block z-20">
<p class="text-[10px] font-bold text-zinc-400 p-2 uppercase">Reason for rejection</p>
<button class="w-full text-left p-2 text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded">Blurry Image</button>
<button class="w-full text-left p-2 text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded">Expired Document</button>
<button class="w-full text-left p-2 text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded">Name Mismatch</button>
<button class="w-full text-left p-2 text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded">Incomplete Data</button>
<div class="h-px bg-zinc-200 dark:bg-zinc-700 my-1"></div>
<button class="w-full text-left p-2 text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded font-medium text-red-500">Other (Manual Note)</button>
</div>
</div>
</div>
<p class="text-[10px] text-center text-zinc-400 mt-4 italic">Action will be logged in the permanent audit trail for compliance review.</p>
</div>
</div>
</div>
<!-- Bottom Context Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center gap-4">
<div class="w-10 h-10 bg-primary/10 rounded flex items-center justify-center text-primary">
<span class="material-icons">speed</span>
</div>
<div>
<p class="text-[10px] font-bold text-zinc-400 uppercase">Avg Review Time</p>
<p class="text-lg font-bold">1m 24s</p>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center gap-4">
<div class="w-10 h-10 bg-primary/10 rounded flex items-center justify-center text-primary">
<span class="material-icons">hourglass_empty</span>
</div>
<div>
<p class="text-[10px] font-bold text-zinc-400 uppercase">Current Queue</p>
<p class="text-lg font-bold">128 Total</p>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center gap-4">
<div class="w-10 h-10 bg-primary/10 rounded flex items-center justify-center text-primary">
<span class="material-icons">task_alt</span>
</div>
<div>
<p class="text-[10px] font-bold text-zinc-400 uppercase">Daily Approval</p>
<p class="text-lg font-bold">94.2%</p>
</div>
</div>
</div>
</div>
</main>
<footer class="mt-12 py-8 border-t border-zinc-200 dark:border-zinc-800 text-center">
<p class="text-xs text-zinc-400 font-medium tracking-widest uppercase">© 2023 Bloombit Financial Compliance Systems • High-Security Environment</p>
</footer>
<script src="/js/app.js"></script>
</body></html>
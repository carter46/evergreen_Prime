<?php require_once __DIR__ . '/../../includes/auth-check.php'; require_once __DIR__ . '/../../includes/helpers.php'; $siteName = get_site_name();
$currentPage = 'profile';
$profileUser = get_current_user_data() ?? [];
$profileName = $profileUser['name'] ?? 'User';
$profileEmail = $profileUser['email'] ?? '';
$profileAvatar = $profileUser['avatar_url'] ?? null;
$profileInitials = strtoupper(substr($profileName ?: 'U', 0, 2));
$profileUserId = isset($_SESSION['user_id']) ? 'BB-' . $_SESSION['user_id'] : '';
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Profile and Security Settings</title>
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
        body { font-family: 'Space Grotesk', sans-serif; }
        .tab-active { border-bottom: 2px solid #f9bd0b; color: #f9bd0b; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 overflow-y-auto">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
<!-- Profile Header Section -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 mb-8 shadow-sm">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
<div class="flex items-center gap-6">
<div class="relative group">
<?php if ($profileAvatar): ?><img alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-primary/10" src="<?php echo htmlspecialchars($profileAvatar); ?>"/><?php else: ?><div class="w-24 h-24 rounded-full bg-primary/20 border-4 border-primary/10 flex items-center justify-center text-primary text-3xl font-bold"><?php echo htmlspecialchars($profileInitials); ?></div><?php endif; ?>
<button class="absolute bottom-0 right-0 bg-primary text-white p-1.5 rounded-full shadow-lg hover:scale-105 transition-transform">
<span class="material-icons text-sm">edit</span>
</button>
</div>
<div>
<div class="flex items-center gap-3">
<h1 class="text-2xl font-bold" data-profile-name><?php echo htmlspecialchars($profileName); ?></h1>
<span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs px-2.5 py-0.5 rounded-full font-bold flex items-center gap-1">
<span class="material-icons text-[14px]">verified</span>
                                Verified
                            </span>
</div>
<p class="text-slate-500 dark:text-slate-400" data-profile-email><?php echo htmlspecialchars($profileEmail); ?></p>
<p class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">User ID: <span data-user-id><?php echo htmlspecialchars($profileUserId); ?></span></p>
</div>
</div>
<div class="flex gap-3">
<button class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2">
<span class="material-icons text-sm">shield</span>
                        Verify Identity
                    </button>
</div>
</div>
</div>
<!-- Navigation Tabs -->
<div class="border-b border-primary/10 mb-8 overflow-x-auto">
<nav class="flex gap-8 min-w-max">
<button class="pb-4 px-1 text-sm font-semibold tab-active flex items-center gap-2">
<span class="material-icons text-sm">person</span>
                    Profile
                </button>
<button class="pb-4 px-1 text-sm font-semibold text-slate-500 hover:text-primary transition-colors flex items-center gap-2">
<span class="material-icons text-sm">lock</span>
                    Security
                </button>
<button class="pb-4 px-1 text-sm font-semibold text-slate-500 hover:text-primary transition-colors flex items-center gap-2">
<span class="material-icons text-sm">notifications</span>
                    Notifications
                </button>
<button class="pb-4 px-1 text-sm font-semibold text-slate-500 hover:text-primary transition-colors flex items-center gap-2">
<span class="material-icons text-sm">terminal</span>
                    API Management
                </button>
</nav>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
<!-- Left Column: Settings Forms -->
<div class="lg:col-span-2 space-y-8">
<!-- Profile Details -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<h2 class="text-lg font-bold mb-6 flex items-center gap-2">
                        Personal Information
                    </h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-medium text-slate-500">Full Name</label>
<input class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all" type="text" value="John Doe"/>
</div>
<div class="space-y-2">
<label class="text-sm font-medium text-slate-500">Phone Number</label>
<input class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all" type="text" value="+44 7700 900077"/>
</div>
<div class="space-y-2 md:col-span-2">
<label class="text-sm font-medium text-slate-500">Country/Region</label>
<select class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all">
<option>United Kingdom</option>
<option>United States</option>
<option>Germany</option>
<option>Singapore</option>
</select>
</div>
</div>
<div class="mt-8 flex justify-end">
<button class="bg-primary text-white px-8 py-2.5 rounded-lg font-bold hover:bg-primary/90 transition-all">
                            Save Changes
                        </button>
</div>
</section>
<!-- Security Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- 2FA Card -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<div class="flex justify-between items-start mb-4">
<div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
<span class="material-icons">vibration</span>
</div>
<label class="relative inline-flex items-center cursor-pointer">
<input checked="" class="sr-only peer" type="checkbox" value=""/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-background-dark/60 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
</label>
</div>
<h3 class="font-bold text-lg mb-1">Two-Factor Auth</h3>
<p class="text-sm text-slate-500 mb-6 leading-relaxed">Secure your account with Google Authenticator or SMS.</p>
<button class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all font-bold py-2 rounded-lg">
                            Setup 2FA
                        </button>
</div>
<!-- Password Card -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<div class="flex justify-between items-start mb-4">
<div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
<span class="material-icons">key</span>
</div>
</div>
<h3 class="font-bold text-lg mb-1">Password</h3>
<p class="text-sm text-slate-500 mb-6 leading-relaxed">Last updated 32 days ago. Use a strong password.</p>
<button class="w-full bg-slate-900 text-white dark:bg-primary dark:text-white transition-all font-bold py-2 rounded-lg">
                            Change Password
                        </button>
</div>
</div>
<!-- Login Activity -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="p-6 border-b border-primary/10 flex justify-between items-center">
<h2 class="text-lg font-bold">Recent Login Activity</h2>
<button class="text-sm text-primary font-bold hover:underline">Logout all other sessions</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 dark:bg-background-dark/20 text-slate-500 text-xs uppercase tracking-wider font-bold">
<tr>
<th class="px-6 py-4">Device</th>
<th class="px-6 py-4">IP Address</th>
<th class="px-6 py-4">Location</th>
<th class="px-6 py-4">Time</th>
<th class="px-6 py-4"></th>
</tr>
</thead>
<tbody class="divide-y divide-primary/5">
<tr class="hover:bg-slate-50/50 dark:hover:bg-background-dark/30 transition-colors">
<td class="px-6 py-4 flex items-center gap-3">
<span class="material-icons text-slate-400">desktop_windows</span>
<span class="font-medium text-sm">Chrome (MacOS)</span>
</td>
<td class="px-6 py-4 text-sm font-mono text-slate-500">192.168.1.42</td>
<td class="px-6 py-4 text-sm text-slate-500">London, UK</td>
<td class="px-6 py-4 text-sm text-slate-500">Current Session</td>
<td class="px-6 py-4 text-right">
<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-background-dark/30 transition-colors">
<td class="px-6 py-4 flex items-center gap-3">
<span class="material-icons text-slate-400">smartphone</span>
<span class="font-medium text-sm">iPhone 14 Pro</span>
</td>
<td class="px-6 py-4 text-sm font-mono text-slate-500">172.16.254.1</td>
<td class="px-6 py-4 text-sm text-slate-500">Manchester, UK</td>
<td class="px-6 py-4 text-sm text-slate-500">2 hours ago</td>
<td class="px-6 py-4 text-right">
<button class="text-slate-400 hover:text-red-500 transition-colors">
<span class="material-icons text-sm">close</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>
</section>
</div>
<!-- Right Column: KYC Status -->
<div class="space-y-8">
<!-- KYC Progress Card -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<h3 class="font-bold text-lg mb-4 flex items-center justify-between">
                        Identity Verification
                        <span class="text-primary text-sm font-medium">80%</span>
</h3>
<div class="w-full bg-slate-100 dark:bg-background-dark/60 rounded-full h-2 mb-6">
<div class="bg-primary h-2 rounded-full" style="width: 80%"></div>
</div>
<div class="space-y-4">
<div class="flex items-center gap-4">
<div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 flex items-center justify-center">
<span class="material-icons text-sm">check</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Email Verified</p>
<p class="text-xs text-slate-400">Confirmed on Jan 12, 2024</p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 flex items-center justify-center">
<span class="material-icons text-sm">check</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Personal Info</p>
<p class="text-xs text-slate-400">Completed</p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center">
<span class="material-icons text-sm">hourglass_empty</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Government ID</p>
<p class="text-xs text-slate-400">Processing verification...</p>
</div>
</div>
<div class="flex items-center gap-4 opacity-50">
<div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-background-dark/60 text-slate-400 flex items-center justify-center">
<span class="material-icons text-sm">lock</span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Proof of Address</p>
<p class="text-xs text-slate-400">Locked</p>
</div>
</div>
</div>
<button class="w-full mt-8 bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all font-bold py-3 rounded-lg flex items-center justify-center gap-2">
<span class="material-icons text-sm">upload</span>
                        Upload Documents
                    </button>
</section>
<!-- Security Tips Card -->
<section class="bg-slate-900 text-white rounded-xl p-6 shadow-sm border border-slate-800">
<h3 class="font-bold text-lg mb-3">Security Tips</h3>
<ul class="space-y-4 text-sm text-slate-400">
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Never share your API keys or passwords with anyone.</p>
</li>
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Enable anti-phishing codes in your notification settings.</p>
</li>
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Check the URL is always <span class="text-white">bloombit.io</span>.</p>
</li>
</ul>
</section>
</div>
</div>
</main>
<!-- 2FA QR Code Modal (Hidden in Flow, Visualized here) -->
<div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
<div class="bg-white dark:bg-background-dark rounded-xl max-w-md w-full p-8 shadow-2xl">
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-bold">Setup Google Authenticator</h2>
<button class="text-slate-400 hover:text-slate-600 transition-colors">
<span class="material-icons">close</span>
</button>
</div>
<div class="space-y-6 text-center">
<div class="mx-auto w-48 h-48 bg-white border-8 border-slate-50 rounded-lg p-2 flex items-center justify-center">
<div class="w-full h-full bg-slate-900 flex flex-col items-center justify-center text-white rounded">
<span class="material-icons text-6xl">qr_code_2</span>
<p class="text-[10px] mt-1 font-mono uppercase">BLOOMBIT-2FA</p>
</div>
</div>
<div class="space-y-2">
<p class="text-sm text-slate-600 dark:text-slate-400">Scan this QR code with your authenticator app.</p>
<div class="bg-slate-50 dark:bg-background-dark/40 p-3 rounded-lg border border-slate-100 dark:border-primary/10 flex items-center justify-between">
<span class="font-mono text-sm tracking-widest uppercase">K7JN-90X3-PL92-BA10</span>
<button class="text-primary material-icons text-sm">content_copy</button>
</div>
</div>
<div class="space-y-3">
<input class="w-full text-center text-2xl tracking-[1em] font-bold border-slate-200 focus:ring-primary focus:border-primary rounded-lg py-3" placeholder="Enter 6-digit code" type="text"/>
<button class="w-full bg-primary text-white font-bold py-3 rounded-lg">Verify &amp; Enable</button>
</div>
</div>
</div>
</div>
<script src="/js/app.js"></script>
</body></html>
<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
$currentPage = 'users';

$users = [];
$pagination = ['page' => 1, 'per_page' => 20, 'total' => 0, 'total_pages' => 1];
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = min(50, max(10, (int)($_GET['per_page'] ?? 20)));

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $where = ['u.role = "user"'];
    $params = [];
    if ($search !== '') {
        $where[] = '(u.name LIKE ? OR u.email LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
    }
    if ($statusFilter === 'active') $where[] = 'u.active = 1';
    elseif ($statusFilter === 'suspended') $where[] = 'u.active = 0';
    $whereClause = implode(' AND ', $where);

    $hasAvatar = false;
    $hasKyc = false;
    $hasCachedUsd = false;
    try {
        $ac = $pdo->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
        $hasAvatar = $ac && $ac->rowCount() > 0;
        $kc = $pdo->query("SHOW COLUMNS FROM users LIKE 'kyc_status'");
        $hasKyc = $kc && $kc->rowCount() > 0;
        $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        $hasCachedUsd = $bc && $bc->rowCount() > 0;
    } catch (Throwable $e) {}
    $avatarCol = $hasAvatar ? ', u.avatar_url' : '';
    $kycCol = $hasKyc ? ', u.kyc_status' : '';
    $balCol = $hasCachedUsd ? ', u.last_balance_usd, u.last_balance_usd_updated_at' : '';

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE {$whereClause}");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
    $offset = ($page - 1) * $perPage;

    $sql = "SELECT u.id, u.email, u.name, u.active, u.email_verified, u.created_at, u.updated_at{$avatarCol}{$kycCol}{$balCol},
            (SELECT COUNT(*) FROM user_investments WHERE user_id = u.id AND status = 'active') AS active_plans_count
            FROM users u WHERE {$whereClause} ORDER BY u.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $userIds = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!$row['active']) {
            $kyc = 'suspended';
        } elseif ($hasKyc && isset($row['kyc_status'])) {
            $kyc = $row['kyc_status'] ?? 'none';
        } else {
            $kyc = $row['email_verified'] ? 'verified' : 'pending';
        }
        $users[] = [
            'id' => (int)$row['id'],
            'email' => $row['email'],
            'name' => $row['name'] ?: 'User #' . $row['id'],
            'active' => (bool)$row['active'],
            'created_at' => $row['created_at'],
            'avatar_url' => $row['avatar_url'] ?? null,
            'total_balance_usd' => $hasCachedUsd ? (float)($row['last_balance_usd'] ?? 0) : 0.0,
            'active_plans_count' => (int)$row['active_plans_count'],
            'kyc_status' => $kyc,
        ];
    }
    // NOTE: We intentionally do NOT call CoinGecko here.
    // Admin UI uses users.last_balance_usd (cached snapshot) for stable display.
    $pagination = ['page' => $page, 'per_page' => $perPage, 'total' => $total, 'total_pages' => $total > 0 ? (int)ceil($total / $perPage) : 1];
} catch (Throwable $e) {}
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
<p class="text-slate-500 text-sm">Manage and monitor <?php echo number_format($pagination['total']); ?> platform users</p>
</div>
<div class="flex items-center gap-3">
<button type="button" id="add-user-btn" class="flex items-center gap-2 px-4 py-2 bg-primary text-background-dark font-semibold rounded-lg hover:brightness-105 transition-all shadow-sm">
<span class="material-icons text-sm">person_add</span>
                    Add User
                </button>
</div>
</div>
<!-- Filters -->
<form method="get" action="/dashboard/admin/users" class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-slate-200 dark:border-zinc-800 flex flex-wrap items-center gap-4 mb-6 shadow-sm">
<div class="flex-1 min-w-[300px] relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input name="search" value="<?php echo htmlspecialchars($search); ?>" class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-zinc-800 border-none rounded-lg focus:ring-2 focus:ring-primary text-sm" placeholder="Search by name or email..." type="text"/>
</div>
<div class="flex items-center gap-2">
<label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Status</label>
<select name="status" class="bg-slate-50 dark:bg-zinc-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary py-2 pr-10">
<option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
<option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
<option value="suspended" <?php echo $statusFilter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
</select>
</div>
<div class="flex items-center gap-2 border-l border-slate-200 dark:border-zinc-700 pl-4">
<button type="submit" class="px-4 py-2 bg-primary text-background-dark font-semibold rounded-lg text-sm">Filter</button>
</div>
</form>
<!-- User Table (Desktop) -->
<div class="hidden md:block bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden relative">
<div class="overflow-x-auto">
<table class="w-full text-left text-sm">
<thead class="bg-slate-50 dark:bg-zinc-800/50 border-b border-slate-200 dark:border-zinc-800">
<tr>
<th class="px-6 py-4 w-10">
<input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox"/>
</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Name</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Cached USD Balance</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">Active Plans</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400">KYC Status</th>
<th class="px-6 py-4 font-semibold text-slate-600 dark:text-zinc-400 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
<?php
$avatarClasses = ['bg-blue-100 text-blue-600', 'bg-emerald-100 text-emerald-600', 'bg-purple-100 text-purple-600', 'bg-amber-100 text-amber-600'];
$kycClasses = ['verified' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', 'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'none' => 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-slate-400', 'suspended' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'];
foreach ($users as $i => $u):
    $initials = strtoupper(substr($u['name'], 0, 2)) ?: 'U';
    $avClass = $avatarClasses[$i % 4];
    $kc = $kycClasses[$u['kyc_status']] ?? 'bg-slate-100 dark:bg-zinc-800 text-slate-500';
?>
<tr class="user-row hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent" data-user-id="<?php echo $u['id']; ?>">
<td class="px-6 py-4" onclick="event.stopPropagation()"><input class="rounded border-slate-300 text-primary focus:ring-primary user-checkbox" type="checkbox"/></td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<?php if (!empty($u['avatar_url'])): ?><img src="<?php echo htmlspecialchars($u['avatar_url']); ?>" alt="" class="w-9 h-9 rounded-full object-cover shrink-0"/><?php else: ?><div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs <?php echo $avClass; ?> shrink-0"><?php echo htmlspecialchars($initials); ?></div><?php endif; ?>
<div>
<div class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($u['name']); ?></div>
<div class="text-xs text-slate-500"><?php echo htmlspecialchars($u['email']); ?></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white">$<?php echo number_format($u['total_balance_usd'], 2); ?></div>
</td>
<td class="px-6 py-4">
<span class="<?php echo $u['active_plans_count'] > 0 ? 'bg-primary/20 text-slate-900 dark:text-primary' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500'; ?> px-2 py-0.5 rounded-full text-xs font-bold"><?php echo $u['active_plans_count']; ?> Plan<?php echo $u['active_plans_count'] !== 1 ? 's' : ''; ?></span>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full <?php echo $kc; ?> text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span><?php echo ucfirst($u['kyc_status']); ?></span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500 user-edit-btn" title="Edit" data-user-id="<?php echo $u['id']; ?>"><span class="material-icons text-lg">edit</span></button>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($users)): ?>
<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">No users found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<!-- Pagination (Desktop) -->
<div class="px-6 py-4 border-t border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<?php
$start = $pagination['total'] > 0 ? (($pagination['page'] - 1) * $pagination['per_page']) + 1 : 0;
$end = min($pagination['page'] * $pagination['per_page'], $pagination['total']);
$q = http_build_query(array_filter(['search' => $search ?: null, 'status' => $statusFilter !== 'all' ? $statusFilter : null]));
$baseUrl = '/dashboard/admin/users' . ($q ? '?' . $q . '&' : '?');
?>
<span class="text-xs text-slate-500">Showing <?php echo $start; ?>-<?php echo $end; ?> of <?php echo number_format($pagination['total']); ?> users</span>
<div class="flex items-center gap-2">
<?php if ($pagination['page'] > 1): ?><a href="<?php echo $baseUrl; ?>page=<?php echo $pagination['page'] - 1; ?>" class="p-1 border border-slate-200 dark:border-zinc-800 rounded hover:bg-slate-50 text-slate-400"><span class="material-icons text-sm">chevron_left</span></a><?php endif; ?>
<?php for ($p = 1; $p <= min(5, $pagination['total_pages']); $p++): ?>
<a href="<?php echo $baseUrl; ?>page=<?php echo $p; ?>" class="px-3 py-1 <?php echo $p === $pagination['page'] ? 'bg-primary text-background-dark font-bold' : 'hover:bg-slate-100 dark:hover:bg-zinc-800'; ?> text-xs rounded"><?php echo $p; ?></a>
<?php endfor; ?>
<?php if ($pagination['page'] < $pagination['total_pages']): ?><a href="<?php echo $baseUrl; ?>page=<?php echo $pagination['page'] + 1; ?>" class="p-1 border border-slate-200 dark:border-zinc-800 rounded hover:bg-slate-50 text-slate-400"><span class="material-icons text-sm">chevron_right</span></a><?php endif; ?>
</div>
</div>
</div>
<!-- Mobile User Cards -->
<div class="block md:hidden space-y-3">
<?php foreach ($users as $i => $u):
    $initials = strtoupper(substr($u['name'], 0, 2)) ?: 'U';
    $avClass = ['bg-blue-100 text-blue-600', 'bg-emerald-100 text-emerald-600', 'bg-purple-100 text-purple-600', 'bg-amber-100 text-amber-600'][$i % 4];
    $kc = ['verified' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'rejected' => 'bg-red-100 text-red-700', 'none' => 'bg-slate-100 text-slate-500', 'suspended' => 'bg-red-100 text-red-700'][$u['kyc_status']] ?? 'bg-slate-100 text-slate-500';
?>
<div class="user-mobile-card bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-sm overflow-hidden" data-user-id="<?php echo $u['id']; ?>">
<button type="button" class="user-mobile-toggle w-full px-4 py-4 flex items-center justify-between gap-3 text-left">
<div class="flex items-center gap-3 min-w-0 flex-1">
<?php if (!empty($u['avatar_url'])): ?><img src="<?php echo htmlspecialchars($u['avatar_url']); ?>" alt="" class="w-10 h-10 rounded-full object-cover shrink-0"/><?php else: ?><div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm <?php echo $avClass; ?> shrink-0"><?php echo htmlspecialchars($initials); ?></div><?php endif; ?>
<div class="min-w-0">
<div class="font-semibold text-slate-900 dark:text-white truncate"><?php echo htmlspecialchars($u['name']); ?></div>
<div class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($u['email']); ?></div>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full <?php echo $kc; ?> text-[10px] font-bold uppercase mt-1"><?php echo ucfirst($u['kyc_status']); ?></span>
</div>
</div>
<span class="material-icons text-slate-400 user-mobile-chevron transition-transform shrink-0">expand_more</span>
</button>
<div class="user-mobile-expand hidden border-t border-slate-100 dark:border-zinc-800 px-4 py-4 space-y-3">
<div class="flex justify-between items-center"><span class="text-xs text-slate-500">Cached USD Balance</span><span class="font-bold text-sm">$<?php echo number_format($u['total_balance_usd'], 2); ?></span></div>
<div class="flex justify-between items-center"><span class="text-xs text-slate-500">Active Plans</span><span class="<?php echo $u['active_plans_count'] > 0 ? 'bg-primary/20 text-primary' : 'text-slate-500'; ?> text-xs font-bold"><?php echo $u['active_plans_count']; ?> Plan<?php echo $u['active_plans_count'] !== 1 ? 's' : ''; ?></span></div>
<button type="button" class="user-edit-btn w-full flex items-center justify-center gap-2 py-2.5 bg-primary text-zinc-900 font-bold rounded-lg text-sm" data-user-id="<?php echo $u['id']; ?>"><span class="material-icons text-lg">edit</span>Edit User</button>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($users)): ?>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-8 text-center text-slate-500">No users found.</div>
<?php endif; ?>
<div class="flex items-center justify-between px-2 py-4">
<span class="text-xs text-slate-500"><?php echo $start; ?>-<?php echo $end; ?> of <?php echo number_format($pagination['total']); ?></span>
<div class="flex items-center gap-2">
<?php if ($pagination['page'] > 1): ?><a href="<?php echo $baseUrl; ?>page=<?php echo $pagination['page'] - 1; ?>" class="p-1.5 border rounded"><span class="material-icons text-sm">chevron_left</span></a><?php endif; ?>
<?php for ($p = 1; $p <= min(5, $pagination['total_pages']); $p++): ?><a href="<?php echo $baseUrl; ?>page=<?php echo $p; ?>" class="px-2 py-1 text-xs <?php echo $p === $pagination['page'] ? 'bg-primary font-bold' : 'hover:bg-slate-100'; ?> rounded"><?php echo $p; ?></a><?php endfor; ?>
<?php if ($pagination['page'] < $pagination['total_pages']): ?><a href="<?php echo $baseUrl; ?>page=<?php echo $pagination['page'] + 1; ?>" class="p-1.5 border rounded"><span class="material-icons text-sm">chevron_right</span></a><?php endif; ?>
</div>
</div>
</div>
</main>
<!-- Floating Batch Actions Bar (Visible when rows selected) -->
<div id="batch-actions-bar" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-zinc-900 dark:bg-zinc-800 text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-6 z-50 hidden">
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
<!-- Toast for success messages -->
<div id="user-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-[60] hidden transition-opacity duration-300">Profile updated successfully</div>
<!-- Drawer backdrop - click to close -->
<div id="user-drawer-backdrop" class="fixed inset-0 bg-black/20 z-40 hidden transition-opacity" aria-hidden="true"></div>
<!-- Add User Sidebar (same fields as registration) -->
<div id="add-user-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[420px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-50 border-l border-slate-200 dark:border-zinc-800 flex flex-col transform translate-x-full transition-transform duration-300" style="transform: translateX(100%);">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Add User</h2>
<button type="button" id="add-user-close" class="p-2 rounded-lg bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-slate-300 transition-colors" aria-label="Close"><span class="material-icons text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6">
<form id="add-user-form" class="space-y-4">
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Full Name</label><input type="text" name="name" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" placeholder="John Doe"/></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Email <span class="text-red-400">*</span></label><input type="email" name="email" required class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" placeholder="john@example.com"/></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Phone <span class="text-slate-400">(Optional)</span></label><input type="tel" name="phone" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" placeholder="+1 234 567 8900"/></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Password <span class="text-red-400">*</span></label><div class="relative"><input type="password" name="password" required minlength="8" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 pr-10 text-sm" placeholder="••••••••" autocomplete="new-password"/><button type="button" data-password-toggle class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 p-1"><span class="material-icons text-lg">visibility</span></button></div></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Confirm Password <span class="text-red-400">*</span></label><div class="relative"><input type="password" name="confirm_password" required minlength="8" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 pr-10 text-sm" placeholder="••••••••"/><button type="button" data-password-toggle class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 p-1"><span class="material-icons text-lg">visibility</span></button></div></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Referral Code <span class="text-slate-400">(Optional)</span></label><input type="text" name="referral" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm uppercase tracking-widest" placeholder="CODE2024"/></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Profile Photo <span class="text-slate-400">(Optional)</span></label><div class="flex items-center gap-4"><div id="add-user-avatar-preview" class="w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 overflow-hidden"><span class="material-icons text-zinc-400">person</span></div><input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="flex-1 text-sm text-zinc-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary file:text-black file:text-xs"/></div><p class="text-[10px] text-zinc-400 mt-1">PNG, JPEG or WEBP. Max 2MB.</p></div>
<div id="add-user-message" class="text-sm hidden"></div>
<button type="submit" class="w-full px-3 py-2.5 text-sm font-bold rounded-lg bg-primary text-zinc-900 hover:brightness-105">Create User</button>
</form>
</div>
</div>
<!-- Right Side Profile Drawer (hidden by default) -->
<div id="user-profile-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[420px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-50 border-l border-slate-200 dark:border-zinc-800 flex flex-col transform translate-x-full transition-transform duration-300" style="transform: translateX(100%);">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">User Profile</h2>
<button id="drawer-close-btn" type="button" class="p-2 rounded-lg bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-slate-300 transition-colors" aria-label="Close"><span class="material-icons text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6 space-y-8">
<input type="hidden" id="drawer-user-id" value=""/>
<!-- Profile Header -->
<div class="flex items-center gap-4">
<div class="relative group cursor-pointer" id="drawer-avatar-wrap" title="Click to update profile picture">
<div id="drawer-avatar" class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center text-background-dark text-2xl font-bold overflow-hidden shrink-0"></div>
<span class="absolute bottom-0 right-0 w-5 h-5 bg-slate-700 text-white rounded-full flex items-center justify-center"><span class="material-icons text-xs">photo_camera</span></span>
<input type="file" id="drawer-avatar-input" accept="image/png,image/jpeg,image/webp" class="hidden" />
</div>
<div>
<h3 id="drawer-name" class="text-xl font-bold"></h3>
<p id="drawer-uid" class="text-slate-500 text-sm"></p>
<p id="drawer-registration" class="text-[10px] text-slate-400 mt-0.5"></p>
<div class="flex items-center gap-2 mt-1">
<span id="drawer-status" class="text-[10px] font-bold uppercase px-2 py-0.5 rounded tracking-widest"></span>
<span id="drawer-last-active" class="text-[10px] text-slate-400"></span>
</div>
</div>
</div>
<!-- Edit Profile Form -->
<form id="profile-update-form" class="space-y-4">
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Name</label><input type="text" id="drawer-edit-name" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" /></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">Email</label><input type="email" id="drawer-edit-email" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" /></div>
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">New Password</label><div class="relative"><input type="password" id="drawer-edit-password" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 pr-10 text-sm" placeholder="Leave blank to keep current" autocomplete="new-password" /><button type="button" data-password-toggle class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"><span class="material-icons text-lg">visibility</span></button></div></div>
</form>
<!-- User Wallet -->
<div>
  <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Cached USD Balance</h4>
  <div id="drawer-total-balance" class="text-2xl font-bold text-emerald-600 mb-2">$0.00</div>
  <div id="drawer-wallet-breakdown" class="text-sm text-slate-600 dark:text-slate-400 space-y-0.5 mb-4"></div>
  <button type="button" id="drawer-adjust-balance-btn" class="text-sm text-black font-medium hover:underline">Adjust balance</button>
  <div id="drawer-adjust-panel" class="hidden mt-3 p-4 bg-slate-50 dark:bg-zinc-800 rounded-lg space-y-3">
    <div>
      <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Type</label>
      <select id="drawer-adjust-type" class="w-full bg-white dark:bg-zinc-900 rounded-lg px-3 py-2 text-sm">
        <option value="credit">Credit</option>
        <option value="debit">Debit</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Currency</label>
      <select id="drawer-adjust-currency" class="w-full bg-white dark:bg-zinc-900 rounded-lg px-3 py-2 text-sm">
        <option value="">Loading...</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Amount</label>
      <input type="number" id="drawer-adjust-amount" step="any" min="0" class="w-full bg-white dark:bg-zinc-900 rounded-lg px-3 py-2 text-sm" placeholder="0"/>
    </div>
    <div id="drawer-adjust-error" class="text-sm text-red-500 hidden"></div>
    <button type="button" id="drawer-adjust-go" class="w-full py-2 bg-primary text-zinc-900 font-bold rounded-lg text-sm">Go</button>
  </div>
</div>
<!-- Active Investments -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Active &amp; Paused Plans</h4>
<div id="drawer-investments" class="space-y-3"></div>
</div>
<!-- Security -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Security Settings</h4>
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-3"><span class="material-icons">verified_user</span><span class="text-sm font-medium">Two-Factor Auth (2FA)</span></div>
<label class="relative inline-flex items-center cursor-pointer">
<input type="checkbox" id="drawer-2fa-toggle" class="sr-only peer" />
<div class="w-11 h-6 bg-slate-200 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
</label>
</div>
<div class="flex items-center justify-between mb-3">
<div class="flex items-center gap-3"><span class="material-icons text-sm">badge</span><span class="text-sm font-medium">KYC Status</span></div>
<span id="drawer-kyc-status" class="text-xs font-bold px-2 py-0.5 rounded-full uppercase">—</span>
</div>
<button type="button" id="drawer-verify-kyc-btn" class="w-full mt-2 px-3 py-2 text-xs font-bold rounded-lg bg-primary/20 text-primary hover:bg-primary hover:text-black transition-colors">Verify KYC (Bypass)</button>
</div>
</div>
<!-- Drawer Actions -->
<div class="p-4 border-t border-slate-200 dark:border-zinc-800 space-y-2">
<button type="button" id="drawer-update-profile" class="w-full px-3 py-2 text-xs font-bold rounded-lg bg-primary text-zinc-900 hover:brightness-105">Update Profile</button>
<div class="grid grid-cols-3 gap-2">
<button type="button" id="drawer-login-as-user" class="px-2 py-2 text-xs font-bold rounded-lg bg-green-600 text-white hover:bg-green-700">Login as User</button>
<button type="button" id="drawer-block-btn" class="px-2 py-2 text-xs font-bold rounded-lg"></button>
<button type="button" id="drawer-delete-user" class="px-2 py-2 text-xs font-bold rounded-lg bg-red-600 text-white hover:bg-red-700">Delete</button>
</div>
</div>
<script src="/js/app.js"></script>
<script>
(function(){
var drawer = document.getElementById('user-profile-drawer');
var backdrop = document.getElementById('user-drawer-backdrop');
if (!drawer) return;

var allCoinsCache = [];
var currentUserWallet = [];
function populateCurrencySelect(isDebit, walletBalances) {
  var sel = document.getElementById('drawer-adjust-currency');
  if (!sel) return;
  if (isDebit && walletBalances && walletBalances.length > 0) {
    var withBalance = walletBalances.filter(function(b){ return (parseFloat(b.amount) || 0) > 0; });
    if (withBalance.length === 0) { sel.innerHTML = '<option value="">No balance to debit</option>'; return; }
    sel.innerHTML = withBalance.map(function(b){ return '<option value="'+b.currency+'">'+b.currency+' ('+parseFloat(b.amount).toFixed(4)+')</option>'; }).join('');
  } else {
    if (allCoinsCache.length > 0) {
      sel.innerHTML = allCoinsCache.map(function(c){ return '<option value="'+c.symbol+'">'+c.display_name+' ('+c.symbol+')</option>'; }).join('');
      return;
    }
    fetch('/api/admin/coins.php').then(function(r){ return r.json(); }).then(function(d){
      if (d.success && d.coins && d.coins.length > 0) {
        allCoinsCache = d.coins.filter(function(c){ return c.enabled; });
        if (allCoinsCache.length === 0) allCoinsCache = [{symbol:'USD',display_name:'US Dollar'},{symbol:'USDT',display_name:'Tether'},{symbol:'BTC',display_name:'Bitcoin'},{symbol:'ETH',display_name:'Ethereum'}];
      } else {
        allCoinsCache = [{symbol:'USD',display_name:'US Dollar'},{symbol:'USDT',display_name:'Tether'},{symbol:'BTC',display_name:'Bitcoin'},{symbol:'ETH',display_name:'Ethereum'}];
      }
      sel.innerHTML = allCoinsCache.map(function(c){ return '<option value="'+c.symbol+'">'+c.display_name+' ('+c.symbol+')</option>'; }).join('');
    }).catch(function(){ sel.innerHTML = '<option value="USD">USD</option><option value="USDT">USDT</option><option value="BTC">BTC</option>'; });
  }
}
fetch('/api/admin/coins.php').then(function(r){ return r.json(); }).then(function(d){
  if (d.success && d.coins) allCoinsCache = d.coins.filter(function(c){ return c.enabled; });
  if (allCoinsCache.length === 0) allCoinsCache = [{symbol:'USD',display_name:'US Dollar'},{symbol:'USDT',display_name:'Tether'},{symbol:'BTC',display_name:'Bitcoin'},{symbol:'ETH',display_name:'Ethereum'}];
}).catch(function(){});

function openDrawer() { closeAddUserDrawer(); drawer.style.transform = 'translateX(0)'; if (backdrop) backdrop.classList.remove('hidden'); }
function closeDrawer() { drawer.style.transform = 'translateX(100%)'; if (backdrop) backdrop.classList.add('hidden'); }

function loadUser(id) {
  fetch('/api/admin/users.php?id=' + id).then(function(r){ return r.json(); }).then(function(res){
    if (!res.success || !res.data) return;
    var u = res.data;
    document.getElementById('drawer-user-id').value = u.id;
    var avEl = document.getElementById('drawer-avatar');
    var initials = (u.name || 'U').substring(0, 2).toUpperCase();
    if (u.avatar_url) {
      avEl.innerHTML = '<img src="' + u.avatar_url + '" alt="" class="w-full h-full object-cover" />';
    } else {
      avEl.textContent = initials;
      avEl.className = 'w-16 h-16 rounded-2xl bg-primary flex items-center justify-center text-background-dark text-2xl font-bold overflow-hidden shrink-0';
    }
    document.getElementById('drawer-name').textContent = u.name || 'User #' + u.id;
    document.getElementById('drawer-uid').textContent = 'UID: #' + u.id;
    var regEl = document.getElementById('drawer-registration');
    if (regEl) regEl.textContent = u.created_at ? 'Registered: ' + u.created_at.substring(0, 10) : '';
    document.getElementById('drawer-status').textContent = u.active ? 'Active' : 'Suspended';
    document.getElementById('drawer-status').className = 'text-[10px] font-bold uppercase px-2 py-0.5 rounded tracking-widest ' + (u.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
    document.getElementById('drawer-last-active').textContent = u.updated_at ? 'Last: ' + u.updated_at.substring(0, 10) : '';
    document.getElementById('drawer-edit-name').value = u.name || '';
    document.getElementById('drawer-edit-email').value = u.email || '';
    document.getElementById('drawer-edit-password').value = '';

    var totalBal = document.getElementById('drawer-total-balance');
    if (totalBal) totalBal.textContent = '$' + (parseFloat(u.total_balance_usd || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })) + ' USD (cached)';
    var breakdownEl = document.getElementById('drawer-wallet-breakdown');
    if (breakdownEl) {
      var wb = (u.wallet_balances || []).filter(function(b){ return (parseFloat(b.amount) || 0) > 0; });
      if (wb.length > 0) {
        var fmtAmt = function(amt, cur) {
          amt = parseFloat(amt);
          if (amt <= 0) return '0';
          if (['USD','USDT','USDC','BUSD'].indexOf(cur) >= 0) return '$' + amt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          if (amt >= 1000) return amt.toLocaleString('en-US', { maximumFractionDigits: 0 });
          if (amt >= 1) return amt.toFixed(2);
          if (amt >= 0.01) return amt.toFixed(4);
          return amt.toFixed(6).replace(/\.?0+$/, '');
        };
        breakdownEl.innerHTML = wb.map(function(b){ return '<div class="flex justify-between"><span>'+b.currency+'</span><span>'+fmtAmt(b.amount, b.currency)+'</span></div>'; }).join('');
      } else {
        breakdownEl.innerHTML = '<span class="text-slate-400">No balances</span>';
      }
    }
    var adjustPanel = document.getElementById('drawer-adjust-panel');
    if (adjustPanel) adjustPanel.classList.add('hidden');
    var amountInput = document.getElementById('drawer-adjust-amount');
    if (amountInput) amountInput.value = '';
    var errEl = document.getElementById('drawer-adjust-error');
    if (errEl) { errEl.classList.add('hidden'); errEl.textContent = ''; }
    currentUserWallet = u.wallet_balances || [];
    var typeSel = document.getElementById('drawer-adjust-type');
    populateCurrencySelect(typeSel && typeSel.value === 'debit', currentUserWallet);

    var inv = document.getElementById('drawer-investments');
    inv.innerHTML = '';
    (u.investments || []).forEach(function(i){
      var avg = ((i.yield_min + i.yield_max) / 2).toFixed(1);
      var isPaused = (i.status || '').toLowerCase() === 'paused';
      var statusBadge = isPaused ? '<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">Paused</span>' : '<span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">Active</span>';
      var pauseOrResume = isPaused ? '<button type="button" class="drawer-resume-investment px-2 py-1 text-[10px] font-bold bg-emerald-500 text-white rounded hover:bg-emerald-600" data-inv-id="'+i.id+'">Resume</button>' : '<button type="button" class="drawer-pause-investment px-2 py-1 text-[10px] font-bold bg-amber-500 text-white rounded hover:bg-amber-600" data-inv-id="'+i.id+'">Pause</button>';
      inv.innerHTML += '<div class="drawer-investment-card p-4 border border-slate-200 dark:border-zinc-800 rounded-xl hover:border-primary/50 transition-colors group" data-inv-id="'+i.id+'"><div class="flex justify-between items-start mb-2"><div><div class="text-sm font-bold flex items-center gap-2">'+i.plan_name+' '+statusBadge+'</div><p class="text-[10px] text-slate-500">'+avg+'% Daily ROI</p></div><span class="text-xs font-bold text-primary">$'+parseFloat(i.amount).toLocaleString()+'</span></div><div class="flex gap-2 mt-2 flex-wrap">'+pauseOrResume+'<button type="button" class="drawer-cancel-investment px-2 py-1 text-[10px] font-bold bg-red-500 text-white rounded hover:bg-red-600" data-inv-id="'+i.id+'">Cancel & Refund</button></div></div>';
    });
    if (!u.investments || u.investments.length === 0) inv.innerHTML = '<p class="text-sm text-slate-500">No active investments</p>';

    var tfaToggle = document.getElementById('drawer-2fa-toggle');
    if (tfaToggle) tfaToggle.checked = !!u.two_factor_enabled;
    var kycEl = document.getElementById('drawer-kyc-status');
    if (kycEl) {
      var ks = (u.kyc_status || 'none').toLowerCase();
      kycEl.textContent = ks;
      kycEl.className = 'text-xs font-bold px-2 py-0.5 rounded-full uppercase ' + (ks === 'verified' ? 'bg-emerald-100 text-emerald-700' : (ks === 'pending' ? 'bg-amber-100 text-amber-700' : (ks === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600')));
    }
    var blockBtn = document.getElementById('drawer-block-btn');
    blockBtn.textContent = u.active ? 'Block User' : 'Unblock User';
    blockBtn.className = 'px-2 py-2 text-xs font-bold rounded-lg ' + (u.active ? 'bg-red-500 text-black hover:bg-red-600' : 'bg-green-600 text-white hover:bg-green-700');
    openDrawer();
  }).catch(function(){});
}

document.getElementById('drawer-close-btn').addEventListener('click', closeDrawer);
if (backdrop) backdrop.addEventListener('click', function(){ closeDrawer(); closeAddUserDrawer(); });

document.getElementById('drawer-adjust-balance-btn').addEventListener('click', function(){
  var panel = document.getElementById('drawer-adjust-panel');
  var err = document.getElementById('drawer-adjust-error');
  if (err) { err.classList.add('hidden'); err.textContent = ''; }
  if (panel) panel.classList.toggle('hidden');
});
document.getElementById('drawer-adjust-type').addEventListener('change', function(){
  populateCurrencySelect(this.value === 'debit', currentUserWallet);
});
function doPlanAction(action, invId, userId, confirmMsg) {
  if (!invId || !userId) return;
  if (confirmMsg && !confirm(confirmMsg)) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: action, user_id: parseInt(userId, 10), investment_id: parseInt(invId, 10) }) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        var toast = document.getElementById('user-toast');
        if (toast) { toast.textContent = res.data && res.data.message ? res.data.message : 'Done'; toast.classList.remove('hidden'); setTimeout(function(){ toast.classList.add('hidden'); }, 2000); }
        loadUser(parseInt(userId, 10));
      } else { alert(res.error || 'Failed'); }
    })
    .catch(function(){ alert('Request failed'); });
}
document.addEventListener('click', function(e){
  var btn = e.target.closest('.drawer-pause-investment');
  if (btn) { e.preventDefault(); e.stopPropagation(); doPlanAction('pause_plan', btn.getAttribute('data-inv-id'), document.getElementById('drawer-user-id').value, 'Pause this plan? Daily earnings will not be credited until resumed.'); return; }
  btn = e.target.closest('.drawer-resume-investment');
  if (btn) { e.preventDefault(); e.stopPropagation(); doPlanAction('resume_plan', btn.getAttribute('data-inv-id'), document.getElementById('drawer-user-id').value); return; }
  btn = e.target.closest('.drawer-cancel-investment');
  if (!btn) return;
  e.preventDefault();
  e.stopPropagation();
  var invId = btn.getAttribute('data-inv-id');
  var userId = document.getElementById('drawer-user-id').value;
  if (!invId || !userId) return;
  if (!confirm('Cancel this plan and refund the full amount to the user\'s wallet?')) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'cancel_plan', user_id: parseInt(userId, 10), investment_id: parseInt(invId, 10) }) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        var toast = document.getElementById('user-toast');
        if (toast) { toast.textContent = res.data && res.data.message ? res.data.message : 'Plan cancelled'; toast.classList.remove('hidden'); setTimeout(function(){ toast.classList.add('hidden'); }, 2000); }
        loadUser(parseInt(userId, 10));
      } else { alert(res.error || 'Failed'); }
    })
    .catch(function(){ alert('Request failed'); });
});
document.getElementById('drawer-adjust-go').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var type = document.getElementById('drawer-adjust-type').value;
  var currency = document.getElementById('drawer-adjust-currency').value;
  var amt = parseFloat(document.getElementById('drawer-adjust-amount').value) || 0;
  var errEl = document.getElementById('drawer-adjust-error');
  if (amt <= 0) { if (errEl) { errEl.textContent = 'Amount must be greater than 0'; errEl.classList.remove('hidden'); } return; }
  if (errEl) { errEl.classList.add('hidden'); errEl.textContent = ''; }
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'adjust_balance', user_id: parseInt(id, 10), type: type, currency: currency, amount: amt }) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        document.getElementById('drawer-adjust-amount').value = '';
        document.getElementById('drawer-adjust-panel').classList.add('hidden');
        var toast = document.getElementById('user-toast');
        if (toast) { toast.textContent = res.data && res.data.message ? res.data.message : 'Balance updated'; toast.classList.remove('hidden'); setTimeout(function(){ toast.classList.add('hidden'); }, 2000); }
        loadUser(parseInt(id, 10));
      } else { if (errEl) { errEl.textContent = res.error || 'Failed'; errEl.classList.remove('hidden'); } }
    })
    .catch(function(){ if (errEl) { errEl.textContent = 'Request failed'; errEl.classList.remove('hidden'); } });
});

var addUserDrawer = document.getElementById('add-user-drawer');
function openAddUserDrawer() { closeDrawer(); addUserDrawer.style.transform = 'translateX(0)'; if (backdrop) backdrop.classList.remove('hidden'); }
function closeAddUserDrawer() { addUserDrawer.style.transform = 'translateX(100%)'; if (backdrop) backdrop.classList.add('hidden'); }
var addUserBtn = document.getElementById('add-user-btn');
var addUserClose = document.getElementById('add-user-close');
var addUserForm = document.getElementById('add-user-form');
if (addUserBtn) addUserBtn.addEventListener('click', openAddUserDrawer);
if (addUserClose) addUserClose.addEventListener('click', closeAddUserDrawer);

if (addUserForm) addUserForm.addEventListener('submit', function(e){
  e.preventDefault();
  var f = this;
  var msgEl = document.getElementById('add-user-message');
  var pw = f.querySelector('[name="password"]').value;
  var cpw = f.querySelector('[name="confirm_password"]').value;
  if (pw !== cpw) { if (msgEl) { msgEl.textContent = 'Passwords do not match.'; msgEl.className = 'text-sm text-red-500'; msgEl.classList.remove('hidden'); } return; }
  if (msgEl) { msgEl.classList.add('hidden'); msgEl.textContent = ''; }
  var fd = new FormData(f);
  fd.append('action', 'add_user');
  fd.delete('confirm_password');
  var btn = f.querySelector('button[type="submit"]');
  if (btn) btn.disabled = true;
  fetch('/api/admin/users.php', { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        document.getElementById('user-toast').textContent = 'User added successfully';
        document.getElementById('user-toast').classList.remove('hidden');
        closeAddUserDrawer();
        f.reset();
        document.getElementById('add-user-avatar-preview').innerHTML = '<span class="material-icons text-zinc-400">person</span>';
        window.location.reload();
      } else { if (msgEl) { msgEl.textContent = res.error || 'Failed'; msgEl.className = 'text-sm text-red-500'; msgEl.classList.remove('hidden'); } }
    })
    .catch(function(){ if (msgEl) { msgEl.textContent = 'Request failed.'; msgEl.className = 'text-sm text-red-500'; msgEl.classList.remove('hidden'); } })
    .finally(function(){ if (btn) btn.disabled = false; });
});

var addUserAvatarInput = document.querySelector('#add-user-form [name="avatar"]');
if (addUserAvatarInput) addUserAvatarInput.addEventListener('change', function(){
  var file = this.files[0];
  var p = document.getElementById('add-user-avatar-preview');
  if (file && /^image\/(png|jpeg|webp)$/.test(file.type)) {
    var r = new FileReader();
    r.onload = function(){ p.innerHTML = '<img src="'+r.result+'" alt="" class="w-full h-full object-cover"/>'; };
    r.readAsDataURL(file);
  } else { p.innerHTML = '<span class="material-icons text-zinc-400">person</span>'; }
});

document.querySelectorAll('.user-edit-btn').forEach(function(btn){
  btn.addEventListener('click', function(e){ e.stopPropagation(); var id = btn.getAttribute('data-user-id'); if (id) loadUser(id); });
});
document.querySelectorAll('.user-mobile-toggle').forEach(function(btn){
  btn.addEventListener('click', function(e){
    e.stopPropagation();
    var card = btn.closest('.user-mobile-card');
    if (!card) return;
    var expand = card.querySelector('.user-mobile-expand');
    var chevron = card.querySelector('.user-mobile-chevron');
    if (expand && expand.classList.contains('hidden')) {
      expand.classList.remove('hidden');
      if (chevron) chevron.style.transform = 'rotate(180deg)';
    } else if (expand) {
      expand.classList.add('hidden');
      if (chevron) chevron.style.transform = '';
    }
  });
});

document.getElementById('drawer-update-profile').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var payload = { action: 'update', user_id: id, name: document.getElementById('drawer-edit-name').value, email: document.getElementById('drawer-edit-email').value };
  var pw = document.getElementById('drawer-edit-password').value.trim();
  if (pw.length >= 8) payload.password = pw;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
    .then(function(r){ return r.json(); }).then(function(res){
      if (res.success) {
        document.getElementById('drawer-edit-password').value = '';
        var toast = document.getElementById('user-toast');
        if (toast) { toast.classList.remove('hidden'); toast.textContent = 'Profile updated successfully'; }
        setTimeout(function(){ closeDrawer(); if (toast) toast.classList.add('hidden'); }, 1500);
      } else { alert(res.error || 'Failed'); }
    }).catch(function(){ alert('Error'); });
});

document.getElementById('drawer-block-btn').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var act = document.getElementById('drawer-status').textContent.trim() === 'Active' ? 'block' : 'unblock';
  if (!confirm((act === 'block' ? 'Block' : 'Unblock') + ' this user?')) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: act, user_id: id }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { loadUser(id); window.location.reload(); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
});

document.getElementById('drawer-verify-kyc-btn').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  if (!confirm('Verify this user\'s KYC without documents? They will be able to withdraw.')) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'verify_kyc', user_id: parseInt(id, 10) }) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        var toast = document.getElementById('user-toast');
        if (toast) { toast.textContent = 'KYC verified'; toast.classList.remove('hidden'); setTimeout(function(){ toast.classList.add('hidden'); }, 2000); }
        loadUser(parseInt(id, 10));
      } else { alert(res.error || 'Failed'); }
    })
    .catch(function(){ alert('Request failed'); });
});
document.getElementById('drawer-2fa-toggle').addEventListener('change', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var cb = this;
  var desired = cb.checked;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'toggle_2fa', user_id: id }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) cb.checked = desired; else { cb.checked = !desired; alert(res.error || '2FA not supported'); } }).catch(function(){ cb.checked = !desired; });
});

document.getElementById('drawer-login-as-user').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (id) { closeDrawer(); window.location.href = '/api/admin/impersonate.php?user_id=' + id; }
});

document.getElementById('drawer-delete-user').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  if (!confirm('Permanently delete this user? This cannot be undone.')) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'delete', user_id: id }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { closeDrawer(); window.location.reload(); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
});

document.getElementById('drawer-avatar-wrap').addEventListener('click', function(){ document.getElementById('drawer-avatar-input').click(); });
document.getElementById('drawer-avatar-input').addEventListener('change', function(){
  var file = this.files[0];
  if (!file) return;
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var fd = new FormData();
  fd.append('user_id', id);
  fd.append('avatar', file);
  fetch('/api/admin/upload-avatar.php', { method: 'POST', body: fd, credentials: 'same-origin' })
    .then(function(r){ return r.json(); }).then(function(res){
      if (res.success && res.data && res.data.avatar_url) {
        var avEl = document.getElementById('drawer-avatar');
        avEl.innerHTML = '<img src="' + res.data.avatar_url + '" alt="" class="w-full h-full object-cover" />';
      } else { alert(res.error || 'Upload failed'); }
    }).catch(function(){ alert('Upload failed'); });
  this.value = '';
});
})();
</script>
</body></html>
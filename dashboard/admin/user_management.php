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

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE {$whereClause}");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();
    $offset = ($page - 1) * $perPage;

    $sql = "SELECT u.id, u.email, u.name, u.active, u.email_verified, u.created_at, u.updated_at,
            COALESCE((SELECT SUM(amount) FROM wallet_balances WHERE user_id = u.id AND currency = 'BTC'), 0) AS total_btc,
            (SELECT COUNT(*) FROM user_investments WHERE user_id = u.id AND status = 'active') AS active_plans_count
            FROM users u WHERE {$whereClause} ORDER BY u.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $kyc = !$row['active'] ? 'suspended' : ($row['email_verified'] ? 'verified' : 'pending');
        $users[] = [
            'id' => (int)$row['id'],
            'email' => $row['email'],
            'name' => $row['name'] ?: 'User #' . $row['id'],
            'active' => (bool)$row['active'],
            'created_at' => $row['created_at'],
            'total_btc' => (float)$row['total_btc'],
            'active_plans_count' => (int)$row['active_plans_count'],
            'kyc_status' => $kyc,
        ];
    }
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
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-background-dark font-semibold rounded-lg hover:brightness-105 transition-all shadow-sm">
<span class="material-icons text-sm">person_add</span>
                    Manual Add User
                </button>
</div>
</div>
<!-- Filters -->
<form method="get" class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-slate-200 dark:border-zinc-800 flex flex-wrap items-center gap-4 mb-6 shadow-sm">
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
<button type="button" class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg text-slate-600 transition-colors"><span class="material-icons">filter_list</span></button>
<button type="button" class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg text-slate-600 transition-colors"><span class="material-icons">download</span></button>
</div>
</form>
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
<?php
$avatarClasses = ['bg-blue-100 text-blue-600', 'bg-emerald-100 text-emerald-600', 'bg-purple-100 text-purple-600', 'bg-amber-100 text-amber-600'];
$kycClasses = ['verified' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400', 'suspended' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'];
foreach ($users as $i => $u):
    $initials = strtoupper(substr($u['name'], 0, 2)) ?: 'U';
    $avClass = $avatarClasses[$i % 4];
    $kc = $kycClasses[$u['kyc_status']] ?? 'bg-slate-100 dark:bg-zinc-800 text-slate-500';
?>
<tr class="user-row hover:bg-primary/5 cursor-pointer transition-colors border-l-4 border-l-transparent" data-user-id="<?php echo $u['id']; ?>">
<td class="px-6 py-4" onclick="event.stopPropagation()"><input class="rounded border-slate-300 text-primary focus:ring-primary user-checkbox" type="checkbox"/></td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs <?php echo $avClass; ?>"><?php echo htmlspecialchars($initials); ?></div>
<div>
<div class="font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($u['name']); ?></div>
<div class="text-xs text-slate-500"><?php echo htmlspecialchars($u['email']); ?></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="font-bold text-slate-900 dark:text-white"><?php echo number_format($u['total_btc'], 4); ?> BTC</div>
</td>
<td class="px-6 py-4">
<span class="<?php echo $u['active_plans_count'] > 0 ? 'bg-primary/20 text-slate-900 dark:text-primary' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500'; ?> px-2 py-0.5 rounded-full text-xs font-bold"><?php echo $u['active_plans_count']; ?> Plan<?php echo $u['active_plans_count'] !== 1 ? 's' : ''; ?></span>
</td>
<td class="px-6 py-4 text-slate-500"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
<td class="px-6 py-4">
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full <?php echo $kc; ?> text-xs font-bold uppercase tracking-wider">
<span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span><?php echo ucfirst($u['kyc_status']); ?></span>
</td>
<td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
<div class="flex items-center justify-end gap-1">
<button class="p-1.5 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-md text-slate-500 user-edit-btn" title="Edit" data-user-id="<?php echo $u['id']; ?>"><span class="material-icons text-lg">edit</span></button>
<button class="p-1.5 <?php echo $u['active'] ? 'hover:bg-red-50 text-red-400' : 'bg-green-100 text-green-600'; ?> rounded-md user-block-btn" title="<?php echo $u['active'] ? 'Suspend' : 'Unblock'; ?>" data-user-id="<?php echo $u['id']; ?>" data-active="<?php echo $u['active'] ? '1' : '0'; ?>"><span class="material-icons text-lg"><?php echo $u['active'] ? 'block' : 'lock_open'; ?></span></button>
</div>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($users)): ?>
<tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">No users found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="px-6 py-4 border-t border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<?php
$start = $pagination['total'] > 0 ? (($pagination['page'] - 1) * $pagination['per_page']) + 1 : 0;
$end = min($pagination['page'] * $pagination['per_page'], $pagination['total']);
$q = http_build_query(array_filter(['search' => $search, 'status' => $statusFilter !== 'all' ? $statusFilter : null]));
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
<!-- Right Side Profile Drawer (hidden by default) -->
<div id="user-profile-drawer" class="fixed inset-y-0 right-0 w-[420px] bg-white dark:bg-zinc-900 shadow-2xl z-50 border-l border-slate-200 dark:border-zinc-800 flex flex-col transform translate-x-full transition-transform duration-300" style="transform: translateX(100%);">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">User Profile</h2>
<button id="drawer-close-btn" type="button" class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-full transition-colors text-slate-400"><span class="material-icons">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6 space-y-8">
<input type="hidden" id="drawer-user-id" value=""/>
<!-- Profile Header -->
<div class="flex items-center gap-4">
<div id="drawer-avatar" class="w-16 h-16 rounded-2xl bg-primary flex items-center justify-center text-background-dark text-2xl font-bold"></div>
<div>
<h3 id="drawer-name" class="text-xl font-bold"></h3>
<p id="drawer-uid" class="text-slate-500 text-sm"></p>
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
<div><label class="block text-xs font-bold text-slate-400 uppercase mb-1">New Password</label><input type="password" id="drawer-edit-password" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm" placeholder="Leave blank to keep current" autocomplete="new-password" /></div>
</form>
<!-- Wallet Breakdown -->
<div>
<div class="flex items-center justify-between mb-4"><h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Wallet Breakdown</h4></div>
<div id="drawer-wallet" class="grid grid-cols-1 gap-3"></div>
</div>
<!-- Active Investments -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Active Investments</h4>
<div id="drawer-investments" class="space-y-3"></div>
</div>
<!-- Security -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Security Settings</h4>
<div class="flex items-center justify-between">
<div class="flex items-center gap-3"><span class="material-icons">verified_user</span><span class="text-sm font-medium">Two-Factor Auth (2FA)</span></div>
<button type="button" id="drawer-2fa-toggle" class="text-xs font-bold px-2 py-1 rounded"></button>
</div>
</div>
<!-- Internal Notes -->
<div>
<h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Internal Admin Notes</h4>
<textarea id="drawer-notes" class="w-full h-24 bg-slate-50 dark:bg-zinc-800 border-none rounded-xl text-sm p-4 focus:ring-1 focus:ring-primary resize-none" placeholder="Add a note about this user..."></textarea>
</div>
</div>
<!-- Drawer Actions -->
<div class="p-4 border-t border-slate-200 dark:border-zinc-800 grid grid-cols-2 gap-2">
<button type="button" id="drawer-update-profile" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-primary text-background-dark hover:brightness-105 col-span-2">Update Profile</button>
<button type="button" id="drawer-block-btn" class="px-3 py-1.5 text-xs font-bold rounded-lg"></button>
<button type="button" id="drawer-adjust-balance" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700">Adjust Balance</button>
</div>
<script src="/js/app.js"></script>
<script>
(function(){
var drawer = document.getElementById('user-profile-drawer');
if (!drawer) return;

function openDrawer() { drawer.style.transform = 'translateX(0)'; }
function closeDrawer() { drawer.style.transform = 'translateX(100%)'; }

function loadUser(id) {
  fetch('/api/admin/users.php?id=' + id).then(function(r){ return r.json(); }).then(function(res){
    if (!res.success || !res.data) return;
    var u = res.data;
    document.getElementById('drawer-user-id').value = u.id;
    var initials = (u.name || 'U').substring(0, 2).toUpperCase();
    document.getElementById('drawer-avatar').textContent = initials;
    document.getElementById('drawer-name').textContent = u.name || 'User #' + u.id;
    document.getElementById('drawer-uid').textContent = 'UID: #' + u.id;
    document.getElementById('drawer-status').textContent = u.active ? 'Active' : 'Suspended';
    document.getElementById('drawer-status').className = 'text-[10px] font-bold uppercase px-2 py-0.5 rounded tracking-widest ' + (u.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
    document.getElementById('drawer-last-active').textContent = u.updated_at ? 'Last: ' + u.updated_at.substring(0, 10) : '';
    document.getElementById('drawer-edit-name').value = u.name || '';
    document.getElementById('drawer-edit-email').value = u.email || '';
    document.getElementById('drawer-edit-password').value = '';
    document.getElementById('drawer-notes').value = u.admin_notes || '';

    var w = document.getElementById('drawer-wallet');
    w.innerHTML = '';
    var coinClasses = { BTC: 'bg-orange-100 text-orange-600', ETH: 'bg-blue-100 text-blue-600', USDT: 'bg-emerald-100 text-emerald-600' };
    var coinNames = { BTC: 'Bitcoin', ETH: 'Ethereum', USDT: 'Tether' };
    var coinIcons = { BTC: 'currency_bitcoin', ETH: 'diamond', USDT: 'attach_money' };
    (u.wallet_balances || []).forEach(function(b){
      var cls = coinClasses[b.currency] || 'bg-slate-100 text-slate-600';
      var nm = coinNames[b.currency] || b.currency;
      var ic = coinIcons[b.currency] || 'payments';
      w.innerHTML += '<div class="p-3 bg-slate-50 dark:bg-zinc-800 rounded-xl flex items-center justify-between"><div class="flex items-center gap-3"><div class="'+cls+' p-2 rounded-lg"><span class="material-icons">'+ic+'</span></div><div><div class="text-xs font-bold">'+nm+'</div><div class="text-[10px] text-slate-500">'+b.currency+'</div></div></div><div class="text-right"><div class="text-sm font-bold">'+parseFloat(b.amount).toFixed(8)+'</div></div></div>';
    });
    if (!u.wallet_balances || u.wallet_balances.length === 0) w.innerHTML = '<p class="text-sm text-slate-500">No balances</p>';

    var inv = document.getElementById('drawer-investments');
    inv.innerHTML = '';
    (u.investments || []).forEach(function(i){
      var avg = ((i.yield_min + i.yield_max) / 2).toFixed(1);
      inv.innerHTML += '<div class="p-4 border border-slate-200 dark:border-zinc-800 rounded-xl"><div class="flex justify-between items-start mb-2"><div><div class="text-sm font-bold">'+i.plan_name+'</div><p class="text-[10px] text-slate-500">'+avg+'% Daily ROI</p></div><span class="text-xs font-bold text-primary">$'+parseFloat(i.amount).toLocaleString()+'</span></div></div>';
    });
    if (!u.investments || u.investments.length === 0) inv.innerHTML = '<p class="text-sm text-slate-500">No active investments</p>';

    document.getElementById('drawer-2fa-toggle').textContent = u.two_factor_enabled ? 'ENABLED' : 'Disabled';
    document.getElementById('drawer-2fa-toggle').className = 'text-xs font-bold px-2 py-1 rounded ' + (u.two_factor_enabled ? 'text-green-600 bg-green-100' : 'text-slate-500 bg-slate-100');
    var blockBtn = document.getElementById('drawer-block-btn');
    blockBtn.textContent = u.active ? 'Block User' : 'Unblock User';
    blockBtn.className = 'px-3 py-1.5 text-xs font-bold rounded-lg ' + (u.active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200');
    openDrawer();
  }).catch(function(){});
}

document.getElementById('drawer-close-btn').addEventListener('click', closeDrawer);

document.querySelectorAll('.user-row, .user-edit-btn').forEach(function(el){
  el.addEventListener('click', function(e){
    if (e.target.closest('.user-block-btn')) return;
    var id = el.getAttribute('data-user-id') || el.closest('[data-user-id]')?.getAttribute('data-user-id');
    if (id) loadUser(id);
  });
});

document.querySelectorAll('.user-block-btn').forEach(function(btn){
  btn.addEventListener('click', function(e){ e.stopPropagation(); var id = btn.getAttribute('data-user-id'); if (id) loadUser(id); });
});

document.getElementById('drawer-update-profile').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var payload = { action: 'update', user_id: id, name: document.getElementById('drawer-edit-name').value, email: document.getElementById('drawer-edit-email').value, admin_notes: document.getElementById('drawer-notes').value };
  var pw = document.getElementById('drawer-edit-password').value.trim();
  if (pw.length >= 8) payload.password = pw;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { document.getElementById('drawer-edit-password').value = ''; loadUser(id); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
});

document.getElementById('drawer-block-btn').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  var act = document.getElementById('drawer-status').textContent.trim() === 'Active' ? 'block' : 'unblock';
  if (!confirm((act === 'block' ? 'Block' : 'Unblock') + ' this user?')) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: act, user_id: id }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { loadUser(id); window.location.reload(); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
});

document.getElementById('drawer-2fa-toggle').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  if (!id) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'toggle_2fa', user_id: id }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) loadUser(id); else alert(res.error || '2FA not supported'); }).catch(function(){});
});

document.getElementById('drawer-adjust-balance').addEventListener('click', function(){
  var id = document.getElementById('drawer-user-id').value;
  var currency = prompt('Currency (BTC, ETH, USDT, USD):', 'BTC');
  var amount = prompt('New amount:');
  if (!id || !currency || amount === null) return;
  fetch('/api/admin/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'adjust_balance', user_id: id, currency: currency.toUpperCase(), amount: parseFloat(amount) || 0 }) })
    .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { alert('Balance updated'); loadUser(id); window.location.reload(); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
});
})();
</script>
</body></html>
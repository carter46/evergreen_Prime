<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'transactions';
$siteName = get_site_name();

$pendingDeposits = [];
$pendingWithdrawals = [];
$completedDeposits = [];
$completedWithdrawals = [];
$rejectedDeposits = [];
$rejectedWithdrawals = [];
$failedDeposits = [];
$failedWithdrawals = [];

$filter = $_GET['filter'] ?? 'pending';
$type = $_GET['type'] ?? 'deposit';

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $cols = 't.id, t.user_id, t.type, t.amount, t.currency, t.status, t.reference, t.created_at, u.name, u.email';
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        if ($chk && $chk->rowCount() > 0) $cols .= ', t.amount_usd';
    } catch (Throwable $e) {}
    
    // Fetch all deposits
    $stmt = $pdo->query("SELECT $cols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' ORDER BY t.created_at DESC LIMIT 200");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['status'] === 'pending') $pendingDeposits[] = $row;
        elseif ($row['status'] === 'completed') $completedDeposits[] = $row;
        elseif ($row['status'] === 'rejected') $rejectedDeposits[] = $row;
        elseif ($row['status'] === 'failed') $failedDeposits[] = $row;
    }
    
    // Fetch all withdrawals
    $stmt = $pdo->query("SELECT $cols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'withdrawal' ORDER BY t.created_at DESC LIMIT 200");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['status'] === 'pending') $pendingWithdrawals[] = $row;
        elseif ($row['status'] === 'completed') $completedWithdrawals[] = $row;
        elseif ($row['status'] === 'rejected') $rejectedWithdrawals[] = $row;
        elseif ($row['status'] === 'failed') $failedWithdrawals[] = $row;
    }
    
    // Get counts for tabs
    $pendingDepCount = count($pendingDeposits);
    $pendingWithCount = count($pendingWithdrawals);
} catch (Throwable $e) {
    // DB unavailable
}

$coinLogos = [
    'BTC' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
    'ETH' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png',
    'USDT' => 'https://assets.coingecko.com/coins/images/325/large/Tether.png',
];

function getTransactionsForFilter($filter, $type, $pendingDeposits, $pendingWithdrawals, $completedDeposits, $completedWithdrawals, $rejectedDeposits, $rejectedWithdrawals, $failedDeposits, $failedWithdrawals) {
    if ($type === 'deposit') {
        if ($filter === 'pending') return $pendingDeposits;
        if ($filter === 'completed') return $completedDeposits;
        if ($filter === 'rejected') return $rejectedDeposits;
        if ($filter === 'failed') return $failedDeposits;
    } else {
        if ($filter === 'pending') return $pendingWithdrawals;
        if ($filter === 'completed') return $completedWithdrawals;
        if ($filter === 'rejected') return $rejectedWithdrawals;
        if ($filter === 'failed') return $failedWithdrawals;
    }
    return [];
}

$currentTransactions = getTransactionsForFilter($filter, $type, $pendingDeposits, $pendingWithdrawals, $completedDeposits, $completedWithdrawals, $rejectedDeposits, $rejectedWithdrawals, $failedDeposits, $failedWithdrawals);
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> Admin | Transactions</title>
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
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8">
<div class="mb-6">
<h1 class="text-2xl font-bold mb-2">Transaction Management</h1>
<p class="text-slate-500">Approve or reject user deposits and withdrawal requests</p>
</div>

<!-- Type Tabs -->
<div class="flex gap-2 mb-6 border-b border-slate-200 dark:border-slate-800">
<a href="?type=deposit&filter=<?php echo htmlspecialchars($filter); ?>" class="px-4 py-2 text-sm font-bold <?php echo $type === 'deposit' ? 'text-primary border-b-2 border-primary' : 'text-slate-500 hover:text-primary'; ?>">
    Deposits <?php if ($pendingDepCount > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-primary text-black text-[10px] rounded-full"><?php echo $pendingDepCount; ?></span><?php endif; ?>
</a>
<a href="?type=withdrawal&filter=<?php echo htmlspecialchars($filter); ?>" class="px-4 py-2 text-sm font-bold <?php echo $type === 'withdrawal' ? 'text-primary border-b-2 border-primary' : 'text-slate-500 hover:text-primary'; ?>">
    Withdrawals <?php if ($pendingWithCount > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-primary text-black text-[10px] rounded-full"><?php echo $pendingWithCount; ?></span><?php endif; ?>
</a>
</div>

<!-- Filter Tabs -->
<div class="flex gap-2 mb-6">
<a href="?type=<?php echo htmlspecialchars($type); ?>&filter=pending" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'pending' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">
    Pending
</a>
<a href="?type=<?php echo htmlspecialchars($type); ?>&filter=completed" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'completed' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">
    Completed
</a>
<a href="?type=<?php echo htmlspecialchars($type); ?>&filter=rejected" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'rejected' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">
    Rejected
</a>
<a href="?type=<?php echo htmlspecialchars($type); ?>&filter=failed" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'failed' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">
    Failed
</a>
</div>

<!-- Transactions Table -->
<div class="bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-background-light dark:bg-white/5 border-b border-primary/10">
<tr>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">User</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Amount</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Currency</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Date</th>
<?php if ($type !== 'withdrawal'): ?><th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Reference</th><?php endif; ?>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-primary/5">
<?php foreach ($currentTransactions as $tx):
  $logo = $coinLogos[strtoupper($tx['currency'])] ?? null;
?>
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<div>
<p class="text-sm font-bold"><?php echo htmlspecialchars($tx['name'] ?: 'User'); ?></p>
<p class="text-[10px] text-slate-500"><?php echo htmlspecialchars($tx['email'] ?? ''); ?></p>
</div>
</td>
<td class="px-6 py-4 text-sm">
<?php 
$usdAmt = isset($tx['amount_usd']) && $tx['amount_usd'] !== null ? (float)$tx['amount_usd'] : null;
$coinAmt = (float)$tx['amount'];
?>
<div class="font-bold"><?php echo $usdAmt !== null ? '$' . number_format($usdAmt, 2) . ' USD' : '$' . number_format($coinAmt, 2); ?></div>
<div class="text-xs text-slate-500">
<?php if ($logo): ?><img alt="<?php echo htmlspecialchars($tx['currency']); ?>" class="inline w-4 h-4 align-middle mr-0.5" src="<?php echo htmlspecialchars($logo); ?>"/><?php endif; ?>
<?php 
$fmt = $coinAmt >= 1 ? number_format($coinAmt, 4) : ($coinAmt >= 0.01 ? number_format($coinAmt, 6) : number_format($coinAmt, 8));
echo $fmt . ' ' . htmlspecialchars($tx['currency']); 
?>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5">
<?php if ($logo): ?><img alt="<?php echo htmlspecialchars($tx['currency']); ?>" class="w-5 h-5" src="<?php echo htmlspecialchars($logo); ?>"/><?php endif; ?>
<span class="text-xs font-medium"><?php echo htmlspecialchars($tx['currency']); ?></span>
</div>
</td>
<td class="px-6 py-4 text-xs text-slate-500"><?php echo date('M j, Y H:i', strtotime($tx['created_at'])); ?></td>
<?php if ($type !== 'withdrawal'): ?><td class="px-6 py-4 font-mono text-[10px] text-slate-400"><?php echo $tx['reference'] ? substr($tx['reference'], 0, 8) . '...' : '—'; ?></td><?php endif; ?>
<td class="px-6 py-4 text-right">
<?php if ($tx['status'] === 'pending'): ?>
<div class="relative inline-block">
<button type="button" class="tx-actions-btn p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-500" data-tx-id="<?php echo (int)$tx['id']; ?>" aria-label="Actions"><span class="material-icons text-lg">more_vert</span></button>
<div class="tx-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg shadow-lg z-10 min-w-[100px]">
<button type="button" class="tx-action-approve block w-full text-left px-3 py-2 text-sm text-primary hover:bg-slate-50 dark:hover:bg-zinc-700" data-tx-id="<?php echo (int)$tx['id']; ?>">Approve</button>
<button type="button" class="tx-action-reject block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-tx-id="<?php echo (int)$tx['id']; ?>">Reject</button>
</div>
</div>
<?php else: ?>
<?php $pill = $tx['status'] === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'; ?>
<span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase <?php echo $pill; ?>"><?php echo htmlspecialchars($tx['status']); ?></span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($currentTransactions)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="<?php echo $type === 'withdrawal' ? 5 : 6; ?>">No <?php echo htmlspecialchars($filter); ?> <?php echo htmlspecialchars($type); ?>s found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Toast -->
<div id="tx-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-[60] hidden transition-opacity duration-300">Transaction updated</div>

</div>
</main>
</div>
<script src="/js/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function closeTxDropdowns() {
        document.querySelectorAll('.tx-actions-dropdown').forEach(function(d){ d.classList.add('hidden'); });
    }
    document.querySelectorAll('.tx-actions-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeTxDropdowns();
            var dd = btn.nextElementSibling;
            if (dd) dd.classList.toggle('hidden');
        });
    });
    document.addEventListener('click', closeTxDropdowns);
    function doTxAction(txId, action) {
        fetch('/api/admin/transactions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, transaction_id: parseInt(txId, 10) })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                var toast = document.getElementById('tx-toast');
                if (toast) {
                    toast.textContent = res.data.message || 'Transaction updated';
                    toast.classList.remove('hidden');
                    setTimeout(function(){ toast.classList.add('hidden'); }, 2000);
                }
                setTimeout(function(){ window.location.reload(); }, 500);
            } else {
                alert(res.error || 'Failed to update transaction');
            }
        }).catch(function(){ alert('Request failed'); });
    }
    document.querySelectorAll('.tx-action-approve').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            doTxAction(btn.getAttribute('data-tx-id'), 'approve');
            closeTxDropdowns();
        });
    });
    document.querySelectorAll('.tx-action-reject').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            doTxAction(btn.getAttribute('data-tx-id'), 'reject');
            closeTxDropdowns();
        });
    });
});
</script>
</body></html>

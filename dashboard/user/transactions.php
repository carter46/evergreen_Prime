<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'history';
$siteName = get_site_name();
$transactions = [];
$totalCount = 0;
$perPage = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$totalPages = 1;
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE user_id = ?');
    $countStmt->execute([$userId]);
    $totalCount = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($totalCount / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare('SELECT id, type, amount, currency, status, reference, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset);
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $transactions[] = $row;
    }
} catch (Throwable $e) { }
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Transaction History</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
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
                    fontFamily: { "display": ["Space Grotesk"] },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>body { font-family: 'Space Grotesk', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen font-display overflow-x-hidden">
<div class="flex min-h-screen overflow-x-hidden">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 flex flex-col overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="flex-1 max-w-[1440px] w-full mx-auto">
<div class="mb-6">
<h1 class="text-2xl sm:text-3xl font-bold">Transaction History</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">All your deposits, withdrawals, payouts, and investment payments.</p>
</div>
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead>
<tr class="text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-50 dark:border-slate-800">
<th class="px-6 py-4 font-semibold">Type / Date</th>
<th class="px-6 py-4 font-semibold">Asset</th>
<th class="px-6 py-4 font-semibold text-right">Amount</th>
<th class="px-6 py-4 font-semibold text-center">Status</th>
<th class="px-6 py-4 font-semibold text-right">TXID</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50 dark:divide-slate-800">
<?php foreach ($transactions as $tx):
  $isIncoming = in_array($tx['type'], ['deposit', 'payout']);
  $statusClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
  if ($tx['status'] === 'completed') $statusClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
  elseif ($tx['status'] === 'rejected') $statusClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
?>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-icons <?php echo $isIncoming ? 'text-emerald-500' : 'text-red-500'; ?> text-lg"><?php echo $isIncoming ? 'arrow_downward' : 'arrow_upward'; ?></span>
<div>
<p class="text-sm font-bold"><?php echo htmlspecialchars(ucfirst($tx['type'])); ?></p>
<p class="text-[10px] text-slate-400"><?php echo date('M j, Y H:i', strtotime($tx['created_at'])); ?></p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-medium"><?php echo htmlspecialchars($tx['currency']); ?></td>
<td class="px-6 py-4 text-right text-sm font-bold <?php echo $isIncoming ? 'text-emerald-500' : 'text-red-500'; ?>"><?php echo $isIncoming ? '+' : '-'; ?><?php echo number_format((float)$tx['amount'], 4); ?></td>
<td class="px-6 py-4 text-center">
<span class="px-2 py-1 <?php echo $statusClass; ?> text-[10px] font-bold rounded-full uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
</td>
<td class="px-6 py-4 text-right font-mono text-[10px] text-slate-400"><?php echo $tx['reference'] ? substr($tx['reference'], 0, 6) . '...' . substr($tx['reference'], -4) : '—'; ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($transactions)): ?>
<tr><td class="px-6 py-12 text-center text-slate-500" colspan="5">No transactions yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php if ($totalPages > 1): ?>
<div class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-4">
<span class="text-xs text-slate-400 font-medium">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $totalCount; ?> total)</span>
<div class="flex items-center gap-2">
<?php if ($page > 1): ?>
<a href="/dashboard/user/transactions?page=<?php echo $page - 1; ?>" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Prev</a>
<?php endif; ?>
<?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
<a href="/dashboard/user/transactions?page=<?php echo $i; ?>" class="px-3 py-1.5 rounded-lg text-sm font-bold <?php echo $i === $page ? 'bg-primary text-black' : 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
<?php if ($page < $totalPages): ?>
<a href="/dashboard/user/transactions?page=<?php echo $page + 1; ?>" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Next</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</div>
</div>
</main>
</div>
</body></html>

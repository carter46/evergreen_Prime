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
$pageTitle = $siteName . ' | Transaction History';
$pageHeading = 'Transaction History';
$pageSubtitle = 'All your deposits, withdrawals, payouts, and investment payments.';
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
include __DIR__ . '/../../includes/dashboard/user-page-title.php';
?>
<div class="glass-panel rounded-xl overflow-hidden min-w-0">
<div class="min-w-0">
<table class="w-full text-left table-fixed min-w-0">
<thead>
<tr class="text-on-surface-variant text-[10px] uppercase tracking-wider border-b border-low">
<th class="px-3 sm:px-6 py-3 sm:py-4 font-semibold w-[38%]">Type / Date</th>
<th class="px-3 sm:px-6 py-3 sm:py-4 font-semibold w-[18%]">Asset</th>
<th class="px-3 sm:px-6 py-3 sm:py-4 font-semibold text-right w-[22%]">Amount</th>
<th class="px-3 sm:px-6 py-3 sm:py-4 font-semibold text-center w-[22%]">Status</th>
</tr>
</thead>
<tbody class="divide-y divide-low">
<?php
  $txTypeLabels = ['referral_bonus' => 'Referral bonus', 'deposit_bonus' => 'Deposit bonus', 'profit_adjustment' => 'Profit adjustment', 'referral_bonus_adjustment' => 'Referral bonus adjustment'];
  foreach ($transactions as $tx):
  $txAmt = (float)($tx['amount'] ?? 0);
  if ($tx['type'] === 'profit_adjustment' || $tx['type'] === 'referral_bonus_adjustment') {
    $isIncoming = $txAmt >= 0;
  } else {
    $isIncoming = in_array($tx['type'], ['deposit', 'payout', 'referral_bonus', 'deposit_bonus']);
  }
  $displayAmt = in_array($tx['type'], ['profit_adjustment', 'referral_bonus_adjustment'], true) ? abs($txAmt) : $txAmt;
  $statusClass = 'bg-primary-container/15 text-primary-container';
  if ($tx['status'] === 'completed') $statusClass = 'bg-success/15 text-success';
  elseif (in_array($tx['status'], ['rejected', 'failed'], true)) $statusClass = 'bg-critical/15 text-critical';
  $typeLabel = $txTypeLabels[$tx['type']] ?? ucfirst(str_replace('_', ' ', $tx['type']));
?>
<tr class="hover:bg-white/[0.02] transition-colors">
<td class="px-3 sm:px-6 py-3 sm:py-4 min-w-0">
<div class="flex items-center gap-2 min-w-0">
<span class="material-symbols-outlined <?php echo $isIncoming ? 'text-success' : 'text-critical'; ?> text-lg shrink-0"><?php echo $isIncoming ? 'arrow_downward' : 'arrow_upward'; ?></span>
<div class="min-w-0">
<p class="text-sm font-bold text-on-surface truncate"><?php echo htmlspecialchars($typeLabel); ?></p>
<p class="text-[10px] text-on-surface-variant truncate"><?php echo date('M j, Y H:i', strtotime($tx['created_at'])); ?></p>
</div>
</div>
</td>
<td class="px-3 sm:px-6 py-3 sm:py-4 text-sm font-medium"><?php echo htmlspecialchars($tx['currency']); ?></td>
<td class="px-3 sm:px-6 py-3 sm:py-4 text-right text-sm font-bold whitespace-nowrap <?php echo $isIncoming ? 'text-success' : 'text-critical'; ?>"><?php echo $isIncoming ? '+' : '-'; ?><?php echo format_usd_amount($displayAmt); ?></td>
<td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
<span class="px-2 py-1 <?php echo $statusClass; ?> text-[10px] font-bold rounded-full uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($transactions)): ?>
<tr><td class="px-3 sm:px-6 py-12 text-center text-on-surface-variant" colspan="4">No transactions yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php if ($totalPages > 1): ?>
<div class="p-4 border-t border-low flex items-center justify-between flex-wrap gap-4">
<span class="text-xs text-on-surface-variant font-medium">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $totalCount; ?> total)</span>
<div class="flex items-center gap-2">
<?php if ($page > 1): ?>
<a href="/dashboard/user/transactions?page=<?php echo $page - 1; ?>" class="px-3 py-1.5 border border-low rounded-lg text-sm font-bold hover:bg-surface-container-high transition-colors text-on-surface">Prev</a>
<?php endif; ?>
<?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
<a href="/dashboard/user/transactions?page=<?php echo $i; ?>" class="px-3 py-1.5 rounded-lg text-sm font-bold <?php echo $i === $page ? 'bg-primary-container text-on-primary' : 'border border-low hover:bg-surface-container-high text-on-surface'; ?>"><?php echo $i; ?></a>
<?php endfor; ?>
<?php if ($page < $totalPages): ?>
<a href="/dashboard/user/transactions?page=<?php echo $page + 1; ?>" class="px-3 py-1.5 border border-low rounded-lg text-sm font-bold hover:bg-surface-container-high transition-colors text-on-surface">Next</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
</body></html>

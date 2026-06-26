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
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'proof_url'");
        if ($chk && $chk->rowCount() > 0) $cols .= ', t.proof_url';
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

$pageTitle = $siteName . ' Admin | Transactions';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
$pageHeading = 'Transaction Management';
$pageSubtitle = 'Approve or reject user deposits and withdrawal requests';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>

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
<?php if ($type !== 'withdrawal'): ?><th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Reference</th><th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Proof</th><?php endif; ?>
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
<div class="font-bold"><?php echo $usdAmt !== null ? '$' . format_usd_amount($usdAmt) . ' USD' : '$' . format_usd_amount($coinAmt); ?></div>
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
<?php if ($type !== 'withdrawal'): ?><td class="px-6 py-4 font-mono text-[10px] text-slate-400 max-w-[120px]" title="<?php echo $tx['reference'] ? htmlspecialchars($tx['reference']) : ''; ?>"><?php echo $tx['reference'] ? (strlen($tx['reference']) > 16 ? substr($tx['reference'], 0, 16) . '…' : $tx['reference']) : '—'; ?></td><td class="px-6 py-4"><?php if (!empty($tx['proof_url'])): ?><a href="<?php echo htmlspecialchars($tx['proof_url']); ?>" target="_blank" rel="noopener" class="text-primary font-medium text-xs hover:underline">View proof</a><?php else: ?>—<?php endif; ?></td><?php endif; ?>
<td class="px-6 py-4 text-right">
<?php if ($tx['status'] === 'pending'): ?>
<div class="relative inline-block">
<button type="button" class="tx-actions-btn p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-500" data-tx-id="<?php echo (int)$tx['id']; ?>" aria-label="Actions"><span class="material-symbols-outlined text-lg">more_vert</span></button>
<div class="tx-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg shadow-lg z-10 min-w-[160px]">
<?php if ($type === 'withdrawal'): ?>
<button type="button" class="tx-action-view-address block w-full text-left px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-zinc-700"
  data-tx-id="<?php echo (int)$tx['id']; ?>"
  data-user-name="<?php echo htmlspecialchars($tx['name'] ?: 'User', ENT_QUOTES); ?>"
  data-user-email="<?php echo htmlspecialchars($tx['email'] ?? '', ENT_QUOTES); ?>"
  data-currency="<?php echo htmlspecialchars($tx['currency'], ENT_QUOTES); ?>"
  data-amount-usd="<?php echo $usdAmt !== null ? htmlspecialchars((string)$usdAmt, ENT_QUOTES) : ''; ?>"
  data-amount-coin="<?php echo htmlspecialchars((string)$coinAmt, ENT_QUOTES); ?>"
  data-address="<?php echo htmlspecialchars($tx['reference'] ?? '', ENT_QUOTES); ?>"
  data-date="<?php echo htmlspecialchars(date('M j, Y H:i', strtotime($tx['created_at'])), ENT_QUOTES); ?>">View wallet address</button>
<?php endif; ?>
<button type="button" class="tx-action-approve block w-full text-left px-3 py-2 text-sm text-primary hover:bg-slate-50 dark:hover:bg-zinc-700" data-tx-id="<?php echo (int)$tx['id']; ?>">Approve</button>
<button type="button" class="tx-action-reject block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-tx-id="<?php echo (int)$tx['id']; ?>">Reject</button>
</div>
</div>
<?php elseif ($type === 'withdrawal' && !empty($tx['reference'])): ?>
<button type="button" class="tx-action-view-address text-xs font-medium text-primary hover:underline"
  data-tx-id="<?php echo (int)$tx['id']; ?>"
  data-user-name="<?php echo htmlspecialchars($tx['name'] ?: 'User', ENT_QUOTES); ?>"
  data-user-email="<?php echo htmlspecialchars($tx['email'] ?? '', ENT_QUOTES); ?>"
  data-currency="<?php echo htmlspecialchars($tx['currency'], ENT_QUOTES); ?>"
  data-amount-usd="<?php echo $usdAmt !== null ? htmlspecialchars((string)$usdAmt, ENT_QUOTES) : ''; ?>"
  data-amount-coin="<?php echo htmlspecialchars((string)$coinAmt, ENT_QUOTES); ?>"
  data-address="<?php echo htmlspecialchars($tx['reference'] ?? '', ENT_QUOTES); ?>"
  data-date="<?php echo htmlspecialchars(date('M j, Y H:i', strtotime($tx['created_at'])), ENT_QUOTES); ?>">View address</button>
<?php else: ?>
<?php $pill = $tx['status'] === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'; ?>
<span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase <?php echo $pill; ?>"><?php echo htmlspecialchars($tx['status']); ?></span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($currentTransactions)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="<?php echo $type === 'withdrawal' ? 5 : 7; ?>">No <?php echo htmlspecialchars($filter); ?> <?php echo htmlspecialchars($type); ?>s found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Toast -->
<div id="tx-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-[60] hidden transition-opacity duration-300">Transaction updated</div>

<!-- Withdrawal address preview -->
<div id="withdraw-address-modal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
<div class="absolute inset-0 bg-black/50" id="withdraw-address-backdrop"></div>
<div class="absolute inset-0 flex items-center justify-center p-4">
<div class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-700 shadow-xl p-6">
<button type="button" id="withdraw-address-close" class="absolute top-3 right-3 p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-zinc-800" aria-label="Close"><span class="material-symbols-outlined">close</span></button>
<h2 class="text-lg font-bold mb-1 pr-8">Withdrawal destination</h2>
<p class="text-xs text-slate-500 mb-4">Wallet address submitted by the user for this withdrawal request.</p>
<div class="space-y-3 text-sm mb-5">
<div class="flex justify-between gap-4"><span class="text-slate-500 shrink-0">User</span><span id="withdraw-address-user" class="font-medium text-right break-all"></span></div>
<div class="flex justify-between gap-4"><span class="text-slate-500 shrink-0">Amount</span><span id="withdraw-address-amount" class="font-medium text-right"></span></div>
<div class="flex justify-between gap-4"><span class="text-slate-500 shrink-0">Date</span><span id="withdraw-address-date" class="text-right"></span></div>
</div>
<label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Wallet address</label>
<div id="withdraw-address-value" class="font-mono text-xs sm:text-sm break-all bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg p-3 text-slate-800 dark:text-slate-100 select-all"></div>
<p id="withdraw-address-empty" class="hidden text-sm text-amber-600 dark:text-amber-400">No wallet address was stored for this withdrawal.</p>
<div class="flex gap-2 mt-5">
<button type="button" id="withdraw-address-copy" class="flex-1 py-2.5 bg-primary text-zinc-900 font-bold rounded-lg text-sm hover:brightness-105">Copy address</button>
<button type="button" id="withdraw-address-close-btn" class="px-4 py-2.5 border border-slate-200 dark:border-zinc-700 rounded-lg text-sm font-medium hover:bg-slate-50 dark:hover:bg-zinc-800">Close</button>
</div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
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

    var addressModal = document.getElementById('withdraw-address-modal');
    var addressValueEl = document.getElementById('withdraw-address-value');
    var addressEmptyEl = document.getElementById('withdraw-address-empty');
    var addressCopyBtn = document.getElementById('withdraw-address-copy');
    var currentWithdrawAddress = '';

    function closeAddressModal() {
        if (addressModal) {
            addressModal.classList.add('hidden');
            addressModal.setAttribute('aria-hidden', 'true');
        }
    }
    function openAddressModal(btn) {
        if (!addressModal || !btn) return;
        var name = btn.getAttribute('data-user-name') || 'User';
        var email = btn.getAttribute('data-user-email') || '';
        var currency = btn.getAttribute('data-currency') || '';
        var amountUsd = btn.getAttribute('data-amount-usd');
        var amountCoin = btn.getAttribute('data-amount-coin') || '';
        var address = (btn.getAttribute('data-address') || '').trim();
        var date = btn.getAttribute('data-date') || '';
        currentWithdrawAddress = address;

        document.getElementById('withdraw-address-user').textContent = email ? (name + ' (' + email + ')') : name;
        var amountText = amountUsd ? ('$' + parseFloat(amountUsd).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD') : '';
        if (amountCoin && currency) {
            amountText += (amountText ? ' · ' : '') + parseFloat(amountCoin).toLocaleString('en-US', { maximumFractionDigits: 8 }) + ' ' + currency;
        }
        document.getElementById('withdraw-address-amount').textContent = amountText || '—';
        document.getElementById('withdraw-address-date').textContent = date || '—';

        if (address) {
            addressValueEl.textContent = address;
            addressValueEl.classList.remove('hidden');
            addressEmptyEl.classList.add('hidden');
            if (addressCopyBtn) addressCopyBtn.classList.remove('hidden');
        } else {
            addressValueEl.classList.add('hidden');
            addressEmptyEl.classList.remove('hidden');
            if (addressCopyBtn) addressCopyBtn.classList.add('hidden');
        }

        addressModal.classList.remove('hidden');
        addressModal.setAttribute('aria-hidden', 'false');
        closeTxDropdowns();
    }

    document.querySelectorAll('.tx-action-view-address').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            openAddressModal(btn);
        });
    });

    ['withdraw-address-close', 'withdraw-address-close-btn', 'withdraw-address-backdrop'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', closeAddressModal);
    });

    if (addressCopyBtn) {
        addressCopyBtn.addEventListener('click', function() {
            if (!currentWithdrawAddress) return;
            var done = function() {
                var toast = document.getElementById('tx-toast');
                if (toast) {
                    toast.textContent = 'Address copied';
                    toast.classList.remove('hidden');
                    setTimeout(function(){ toast.classList.add('hidden'); }, 2000);
                }
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(currentWithdrawAddress).then(done).catch(function() {
                    window.prompt('Copy address:', currentWithdrawAddress);
                });
            } else {
                window.prompt('Copy address:', currentWithdrawAddress);
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAddressModal();
    });
});
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>

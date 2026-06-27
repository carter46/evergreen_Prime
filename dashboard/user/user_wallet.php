<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/usd-wallet.php';
$currentPage = 'wallet';
$siteName = get_site_name();
$walletBalances = [];
$walletTotalUsd = 0;
$walletTotalUsdUpdatedAt = null;
$walletTransactions = [];
$totalProfit = 0;
$activeCapital = 0;
$dailyEarning = 0;
$referralBonus = 0;
$referralBonusLast24h = 0;
$coinLogosMap = ['BTC'=>'https://assets.coingecko.com/coins/images/1/large/bitcoin.png','ETH'=>'https://assets.coingecko.com/coins/images/279/large/ethereum.png','USDT'=>'https://assets.coingecko.com/coins/images/325/large/Tether.png','USDC'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png','BUSD'=>'https://assets.coingecko.com/coins/images/9576/large/BUSD.png','USD'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png','XRP'=>'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png','SOL'=>'https://assets.coingecko.com/coins/images/4128/large/solana.png','BNB'=>'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png','ADA'=>'https://assets.coingecko.com/coins/images/975/large/cardano.png','DOGE'=>'https://assets.coingecko.com/coins/images/5/large/dogecoin.png','TRX'=>'https://assets.coingecko.com/coins/images/1094/large/tron-logo.png'];
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    require_once __DIR__ . '/../../includes/deposit-expiry.php';
    // Best-effort cleanup: expire old pending deposits (idempotent)
    try { expire_pending_deposits($pdo); } catch (Throwable $e) {}
    $userId = $_SESSION['user_id'];
    $walletTotalUsd = get_user_usd_balance($pdo, (int) $userId);
    try {
        $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd_updated_at'");
        if ($bc && $bc->rowCount() > 0) {
            $s = $pdo->prepare('SELECT last_balance_usd_updated_at FROM users WHERE id = ?');
            $s->execute([(int) $userId]);
            $walletTotalUsdUpdatedAt = $s->fetchColumn() ?: null;
        }
    } catch (Throwable $e) {}
    if ($walletTotalUsd > 0) {
        $walletBalances[] = [
            'currency' => 'USD',
            'amount' => $walletTotalUsd,
            'usd_value' => round($walletTotalUsd, 2),
        ];
    }

    $totalProfit = get_user_total_profit($pdo, (int) $userId);
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM user_investments WHERE user_id = ? AND status = 'active'");
    $r->execute([$userId]); $activeCapital = (float)$r->fetchColumn();
    $stmtDaily = $pdo->prepare('SELECT ui.amount, p.yield_min, p.yield_max FROM user_investments ui JOIN plans p ON p.id = ui.plan_id WHERE ui.user_id = ? AND ui.status = ?');
    $stmtDaily->execute([$userId, 'active']);
    while ($row = $stmtDaily->fetch(PDO::FETCH_ASSOC)) {
        $yieldMin = (float)($row['yield_min'] ?? 0);
        $yieldMax = (float)($row['yield_max'] ?? 0);
        $avgYield = ($yieldMin + $yieldMax) / 2;
        if ($avgYield <= 0) $avgYield = $yieldMin;
        $dailyEarning += (float)$row['amount'] * ($avgYield / 100);
    }
    $referralBonus = get_user_total_referral_bonus($pdo, (int) $userId);
    $referralBonusLast24h = get_user_total_referral_bonus($pdo, (int) $userId, null, 24);
    $stmt = $pdo->prepare('SELECT id, type, amount, currency, status, reference, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $walletTransactions[] = $row;
    }
} catch (Throwable $e) { }
$coinNames = ['BTC'=>'Bitcoin','ETH'=>'Ethereum','USDT'=>'Tether','USDC'=>'USD Coin','BUSD'=>'Binance USD','USD'=>'US Dollar','XRP'=>'XRP','SOL'=>'Solana','BNB'=>'BNB','ADA'=>'Cardano','DOGE'=>'Dogecoin','TRX'=>'TRON'];
$pageTitle = $siteName . ' | Wallet & Withdrawals';
$pageHeading = 'Wallet';
$pageSubtitle = 'Manage deposits, withdrawals, and your USD balance.';
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
include __DIR__ . '/../../includes/dashboard/user-page-title.php';
?>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
@media (min-width: 640px) {
    .wallet-drawer-content label { font-size: 0.9375rem; }
    #deposit-coin-quote, #withdraw-coin-quote { font-size: 1.125rem; font-weight: 700; }
}
</style>
<div class="flex-1 max-w-[1440px] w-full mx-auto">
<div class="grid grid-cols-12 gap-8">
<!-- Row 1: USD Balance (65%) | Security Checklist (35%) -->
<div class="col-span-12 grid grid-cols-1 lg:grid-cols-[1.86fr_1fr] gap-6">
<div class="balance-gradient-card relative overflow-hidden rounded-xl p-8 text-white shadow-2xl">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary-container/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
<div class="absolute top-0 right-0 p-4 opacity-10">
<span class="material-symbols-outlined text-6xl">account_balance_wallet</span>
</div>
<div class="relative z-10">
<div>
<p class="text-on-surface-variant text-sm font-medium mb-1">Available Balance</p>
<h1 class="text-5xl md:text-6xl font-bold tracking-tight">$<?php echo format_usd_amount($walletTotalUsd); ?> <span class="text-xl font-normal text-on-surface-variant ml-2">USD</span></h1>
<p class="text-primary-container mt-2 text-sm">Centralized USD wallet — invest and withdraw from one balance.</p>
<div class="flex flex-wrap gap-2 sm:gap-3 mt-4">
<button type="button" id="deposit-btn" class="bg-primary-container hover:bg-primary-container/90 text-on-primary px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-bold flex items-center gap-1.5 sm:gap-2 transition-all text-sm">
<span class="material-symbols-outlined text-xs sm:text-sm">add</span> Deposit
</button>
<button type="button" id="withdraw-btn" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-bold flex items-center gap-1.5 sm:gap-2 transition-all backdrop-blur-sm text-sm">
<span class="material-symbols-outlined text-xs sm:text-sm">file_upload</span> Withdraw
</button>
<a href="/dashboard/user/transactions" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 sm:px-6 sm:py-2.5 rounded-lg font-bold flex items-center gap-1.5 sm:gap-2 transition-all backdrop-blur-sm text-sm">
<span class="material-symbols-outlined text-xs sm:text-sm">history</span> History
</a>
</div>
</div>
<div class="mt-8 sm:mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 border-t border-white/10 pt-6 sm:pt-8">
<div>
<p class="text-on-surface-variant text-xs mb-1">Total Profit</p>
<p class="font-bold text-success">$<?php echo format_usd_amount($totalProfit); ?></p>
</div>
<div>
<p class="text-on-surface-variant text-xs mb-1">Active Capital</p>
<p class="font-bold">$<?php echo format_usd_amount($activeCapital); ?></p>
</div>
<div>
<p class="text-on-surface-variant text-xs mb-1">Daily Earning</p>
<p class="font-bold text-primary-container">$<?php echo format_usd_amount($dailyEarning); ?></p>
</div>
<div>
<p class="text-on-surface-variant text-xs mb-1">Referral Bonus (earned)</p>
<p class="font-bold text-primary-container">$<?php echo format_usd_amount($referralBonus); ?></p>
<?php if ($referralBonus > 0): ?><p class="text-[10px] text-on-surface-variant mt-0.5">Last 24h: $<?php echo format_usd_amount($referralBonusLast24h); ?></p><?php endif; ?>
</div>
</div>
</div>
</div>
<!-- Coming Soon (35%) -->
<div class="rounded-xl overflow-hidden relative group w-full">
<div class="bg-slate-900 p-6 h-full">
<h5 class="text-primary text-xs font-bold uppercase mb-1">Coming Soon</h5>
<h4 class="text-white font-bold mb-4">Earn up to 12% APY with <?php echo htmlspecialchars($siteName); ?> Staking</h4>
<img alt="Staking" class="w-full h-32 object-cover rounded-lg opacity-60 group-hover:opacity-100 transition-opacity" src="/uploads/images/crypto-assets.jpg" onerror="this.src='/uploads/images/crypto-assets.png';this.onerror=null"/>
<button class="w-full mt-4 py-2 border border-white/20 text-white text-xs font-bold rounded hover:bg-white/10 transition-colors">Join Waitlist</button>
</div>
</div>
</div>

<!-- Row 2: Your Assets (65%) | Security Checklist (35%) -->
<div class="col-span-12 grid grid-cols-1 lg:grid-cols-[1.86fr_1fr] gap-6">
<!-- Your Assets -->
<div class="glass-panel rounded-xl overflow-hidden min-w-0">
<div class="p-4 border-b border-low flex items-center justify-between gap-2">
<h2 class="text-base font-bold text-on-surface">USD Wallet</h2>
</div>
<div class="overflow-hidden">
<table class="w-full text-left table-fixed min-w-0">
<thead>
<tr class="text-slate-400 text-[9px] uppercase tracking-wider border-b border-slate-50 dark:border-slate-800">
<th class="px-3 py-2 font-semibold w-1/4">Asset</th>
<th class="px-3 py-2 font-semibold text-right w-1/4">Balance</th>
<th class="px-3 py-2 font-semibold text-right w-1/4">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50 dark:divide-slate-800 wallet-assets-table">
<?php if ($walletTotalUsd > 0): ?>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-3 py-3">
<div class="flex items-center gap-2 min-w-0">
<div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center overflow-hidden bg-slate-100 dark:bg-slate-800"><img alt="USD" class="w-5 h-5 object-contain" src="https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png" loading="lazy"/></div>
<div class="min-w-0">
<p class="font-bold text-xs truncate">US Dollar</p>
<p class="text-[10px] text-slate-500">USD</p>
</div>
</div>
</td>
<td class="px-3 py-3 text-right font-medium text-xs truncate">$<?php echo format_usd_amount($walletTotalUsd); ?></td>
<td class="px-3 py-3 text-right">
<a href="/dashboard/user/investment-plans" class="text-[10px] font-bold px-2 py-1 rounded bg-primary/10 text-primary hover:bg-primary hover:text-black transition-all">INVEST</a>
</td>
</tr>
<?php else: ?>
<tr><td class="px-3 py-6 text-center text-slate-500 text-xs" colspan="3">No balance yet. Deposit funds to get started.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<!-- Security Checklist (35%) -->
<div class="glass-panel border border-primary-container/20 rounded-xl p-4 flex gap-4 self-stretch">
<div class="w-10 h-10 rounded-lg bg-primary-container/20 flex-shrink-0 flex items-center justify-center">
<span class="material-symbols-outlined text-primary-container">gpp_maybe</span>
</div>
<div>
<h4 class="text-sm font-bold text-on-surface">Security Checklist</h4>
<p class="text-xs text-text-secondary mt-1 leading-relaxed">
                            Ensure 2FA is active before withdrawing. Double-check the recipient address; crypto transfers are irreversible.
                        </p>
</div>
</div>
</div>

<!-- Row 3: Full-width Recent History -->
<div class="col-span-12">
<div class="glass-panel rounded-xl overflow-hidden">
<div class="p-6 border-b border-low flex items-center justify-between">
<h2 class="text-lg font-bold text-on-surface">Recent History</h2>
<a href="/dashboard/user/transactions" class="text-primary-container text-xs font-bold hover:underline">View All</a>
</div>
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
<?php
  $txTypeLabels = ['referral_bonus' => 'Referral bonus', 'deposit_bonus' => 'Deposit bonus', 'profit_adjustment' => 'Profit adjustment', 'referral_bonus_adjustment' => 'Referral bonus adjustment'];
  foreach ($walletTransactions as $tx):
  $txAmt = (float)($tx['amount'] ?? 0);
  if ($tx['type'] === 'profit_adjustment' || $tx['type'] === 'referral_bonus_adjustment') {
    $isDeposit = $txAmt >= 0;
  } else {
    $isDeposit = in_array($tx['type'], ['deposit','payout','referral_bonus','deposit_bonus']);
  }
  $displayAmt = in_array($tx['type'], ['profit_adjustment', 'referral_bonus_adjustment'], true) ? abs($txAmt) : $txAmt;
  $date = !empty($tx['created_at']) ? date('M j, Y H:i', strtotime($tx['created_at'])) : '';
  $typeLabel = $txTypeLabels[$tx['type']] ?? ucfirst(str_replace('_', ' ', $tx['type']));
?>
<tr>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined <?php echo $isDeposit ? 'text-success' : 'text-critical'; ?> text-lg"><?php echo $isDeposit ? 'arrow_downward' : 'arrow_upward'; ?></span>
<div>
<p class="text-sm font-bold"><?php echo htmlspecialchars($typeLabel); ?></p>
<p class="text-[10px] text-slate-400"><?php echo date('M j, Y H:i', strtotime($tx['created_at'])); ?></p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-medium"><?php echo htmlspecialchars($tx['currency']); ?></td>
<td class="px-6 py-4 text-right text-sm font-bold <?php echo $isDeposit ? 'text-emerald-500' : 'text-red-500'; ?>"><?php echo $isDeposit ? '+' : '-'; ?><?php echo format_usd_amount($displayAmt); ?></td>
<td class="px-6 py-4 text-center">
<?php
$statusClass = 'bg-amber-100 text-amber-700';
if ($tx['status'] === 'completed') $statusClass = 'bg-emerald-100 text-emerald-700';
elseif ($tx['status'] === 'rejected') $statusClass = 'bg-red-100 text-red-700';
elseif ($tx['status'] === 'failed') $statusClass = 'bg-red-100 text-red-700';
?>
<span class="px-2 py-1 <?php echo $statusClass; ?> text-[10px] font-bold rounded-full uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
</td>
<td class="px-6 py-4 text-right font-mono text-[10px] text-slate-400"><?php echo $tx['reference'] ? substr($tx['reference'], 0, 6) . '...' . substr($tx['reference'], -4) : '—'; ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($walletTransactions)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="5">No transactions yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>

<div id="wallet-drawer-backdrop" class="fixed inset-0 bg-black/50 z-[45] hidden" aria-hidden="true" style="backdrop-filter:blur(2px)"></div>
<div id="deposit-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[520px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-[50] border-l border-slate-200 dark:border-zinc-800 flex flex-col transition-transform duration-300 ease-out" style="transform:translateX(100%)">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-xl font-bold">Deposit Funds</h2>
<button type="button" id="deposit-drawer-close" class="p-2 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface-variant transition-colors"><span class="material-symbols-outlined text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6 wallet-drawer-content">
<div id="deposit-form-step1">
<div class="space-y-5">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Amount (USD)</label>
<input type="number" id="deposit-amount" step="0.01" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-semibold" placeholder="0.00"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Deposit Method</label>
<select id="deposit-method" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="crypto" selected>Crypto</option>
<option value="bank">Bank Transfer</option>
<option value="card">Card</option>
</select>
</div>
<div id="deposit-crypto-fields" class="space-y-5">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Deposit Currency</label>
<select id="deposit-currency" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="">Loading...</option>
</select>
<p class="mt-3 text-base sm:text-lg font-bold text-primary dark:text-primary min-h-[1.5em]" id="deposit-coin-quote">—</p>
</div>
</div>
<div id="deposit-bank-fields" class="hidden space-y-3">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Bank Account</label>
<select id="deposit-bank-option" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="">Loading...</option>
</select>
</div>
<p class="text-sm text-slate-500 dark:text-slate-400">Bank transfer details will be shown after you submit your deposit request.</p>
<p class="text-base sm:text-lg font-bold text-primary dark:text-primary min-h-[1.5em]" id="deposit-bank-quote">—</p>
</div>
<div id="deposit-card-fields" class="hidden space-y-3">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Card Option</label>
<select id="deposit-card-option" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="">Loading...</option>
</select>
</div>
<p class="text-sm text-slate-500 dark:text-slate-400">Card payment details will be shown after you submit your deposit request.</p>
<p class="text-base sm:text-lg font-bold text-primary dark:text-primary min-h-[1.5em]" id="deposit-card-quote">—</p>
</div>
<div id="deposit-error" class="text-sm text-red-500 hidden"></div>
<button type="button" id="deposit-submit-btn" class="w-full py-3 bg-primary text-black font-bold rounded-lg text-base">Submit Deposit Request</button>
</div>
</div>
<div id="deposit-form-step2" class="hidden">
<div class="space-y-5">
<div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
<p class="text-base font-bold text-emerald-700 dark:text-emerald-300 mb-2">Deposit request submitted!</p>
<p id="deposit-step2-instructions" class="text-sm text-emerald-600 dark:text-emerald-400">Complete your payment using the details below. Your deposit will be credited after admin approval.</p>
</div>
<div id="deposit-crypto-address-wrap">
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Send to Address</label>
<div class="flex items-center gap-2">
<input type="text" id="deposit-address-display" readonly class="flex-1 bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-sm font-mono border border-slate-200 dark:border-zinc-700"/>
<button type="button" id="deposit-copy-addr" class="px-4 py-3 bg-primary text-black font-bold rounded-lg text-sm">Copy</button>
</div>
</div>
<div id="deposit-bank-details-wrap" class="hidden space-y-2 text-sm bg-slate-50 dark:bg-zinc-800 rounded-lg p-4 border border-slate-200 dark:border-zinc-700"></div>
<div id="deposit-card-details-wrap" class="hidden space-y-2 text-sm bg-slate-50 dark:bg-zinc-800 rounded-lg p-4 border border-slate-200 dark:border-zinc-700"></div>
<div class="pt-4 border-t border-slate-200 dark:border-zinc-800">
<p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Payment Method: <span id="deposit-selected-currency" class="font-bold"></span></p>
<p class="text-sm text-slate-600 dark:text-slate-400">Amount: <span id="deposit-selected-amount" class="font-bold"></span></p>
</div>
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
<p class="text-sm font-bold text-amber-800 dark:text-amber-300 mb-1">Confirm within <span id="deposit-countdown-mins">30</span> minutes</p>
<p class="text-sm text-amber-700 dark:text-amber-400">Time remaining: <span id="deposit-countdown-timer" class="font-mono font-bold">--:--</span></p>
</div>
<button type="button" id="deposit-i-have-paid-btn" class="w-full py-3 bg-primary text-black font-bold rounded-lg text-base mt-4 flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-lg">check_circle</span> I have made this payment
</button>
<div id="deposit-after-payment-section" class="hidden pt-4 border-t border-slate-200 dark:border-zinc-800 space-y-4 mt-4">
<p class="text-xs text-slate-500 dark:text-zinc-400">Add your transaction details below (optional). Then click Done.</p>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Reference / TX Hash <span class="text-slate-400 font-normal">(Optional)</span></label>
<input type="text" id="deposit-reference-step2" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700" placeholder="Transaction hash or reference"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Upload transaction proof <span class="text-slate-400 font-normal">(Optional)</span></label>
<input type="file" id="deposit-proof-file" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-black file:font-medium file:cursor-pointer hover:file:bg-primary/90" accept="image/*,.pdf"/>
<p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">PNG, JPEG, WEBP or PDF. Max 5MB. Shown to admin for approval.</p>
</div>
<div id="deposit-done-message" class="text-sm hidden"></div>
<button type="button" id="deposit-close-btn" class="w-full py-3 bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-base mt-2">Done</button>
</div>
</div>
</div>
</div>

</div>

<div id="withdraw-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[520px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-[50] border-l border-slate-200 dark:border-zinc-800 flex flex-col transition-transform duration-300 ease-out" style="transform:translateX(100%)">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-xl font-bold">Withdraw Funds</h2>
<button type="button" id="withdraw-drawer-close" class="p-2 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface-variant transition-colors"><span class="material-symbols-outlined text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6 wallet-drawer-content">
<form id="withdrawal-form" class="space-y-5">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Amount (USD)</label>
<div class="relative">
<input name="amount_usd" id="withdraw-amount" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-semibold" placeholder="0.00" step="any" type="number" required/>
<span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-500">USD</span>
</div>
<?php
  $minW = get_site_setting('min_withdrawal_limit', '10');
  $maxW = get_site_setting('max_withdrawal_limit', '');
?>
<p class="text-sm text-amber-600 dark:text-amber-400 mt-2 font-medium" id="withdraw-limit-hint">
  Min: $<span id="withdraw-min-limit"><?php echo htmlspecialchars($minW); ?></span> USD
  <?php if (!empty($maxW) && (float)$maxW > 0): ?>
    <span class="mx-1">•</span> Max: $<span id="withdraw-max-limit"><?php echo htmlspecialchars($maxW); ?></span> USD
  <?php endif; ?>
</p>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Withdrawal Method</label>
<select name="withdrawal_method" id="withdraw-method" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="crypto" selected>Crypto</option>
<option value="bank">Bank Transfer</option>
<option value="card">Card</option>
</select>
</div>
<div id="withdraw-crypto-fields">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Payout Currency</label>
<select name="currency" id="withdraw-currency" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="">Loading...</option>
</select>
<p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Available USD: <span id="withdraw-available" class="font-semibold">$<?php echo format_usd_amount($walletTotalUsd); ?></span></p>
<p class="mt-3 text-base sm:text-lg font-bold text-primary dark:text-primary min-h-[1.5em]" id="withdraw-coin-quote">—</p>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Recipient Address</label>
<input name="address" id="withdraw-address" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700" placeholder="Paste external wallet address" type="text"/>
</div>
</div>
<div id="withdraw-bank-fields" class="hidden space-y-4">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Bank Name</label>
<input type="text" id="withdraw-bank-name" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Account Name</label>
<input type="text" id="withdraw-account-name" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700"/>
</div>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Account Number</label>
<input type="text" id="withdraw-account-number" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-mono"/>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Routing Number</label>
<input type="text" id="withdraw-routing-number" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">SWIFT / IBAN</label>
<input type="text" id="withdraw-swift-iban" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700" placeholder="SWIFT or IBAN (optional)"/>
</div>
</div>
</div>
<div id="withdraw-card-fields" class="hidden space-y-4">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Card Brand</label>
<select id="withdraw-card-brand" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-medium">
<option value="">Select card</option>
<option value="visa">Visa</option>
<option value="mastercard">Mastercard</option>
<option value="amex">American Express</option>
</select>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Cardholder Name</label>
<input type="text" id="withdraw-card-holder" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Card Number</label>
<input type="text" id="withdraw-card-number" inputmode="numeric" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700 font-mono"/>
</div>
<div class="grid grid-cols-2 gap-3">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">Expiry</label>
<input type="text" id="withdraw-card-expiry" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700" placeholder="MM/YY"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide mb-2">CVC</label>
<input type="text" id="withdraw-card-cvc" inputmode="numeric" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-4 py-3 text-base border border-slate-200 dark:border-zinc-700"/>
</div>
</div>
</div>
</div>
<div id="withdrawal-message" class="text-sm hidden"></div>
<button type="submit" class="w-full py-3 bg-primary text-black font-bold rounded-lg text-base flex items-center justify-center gap-2">
Withdraw Now <span class="material-symbols-outlined text-base">arrow_forward</span>
</button>
</form>
</div>
</div>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>window.BLOOMBIT_API_BASE = '';</script>
<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var backdrop = document.getElementById('wallet-drawer-backdrop');
    var depositDrawer = document.getElementById('deposit-drawer');
    var withdrawDrawer = document.getElementById('withdraw-drawer');
    var depositStep1 = document.getElementById('deposit-form-step1');
    var depositStep2 = document.getElementById('deposit-form-step2');
    var depositCountdownTimer = document.getElementById('deposit-countdown-timer');
    var depositCountdownMinsEl = document.getElementById('deposit-countdown-mins');
    var depositDoneMsg = document.getElementById('deposit-done-message');
    var depositDoneBtn = document.getElementById('deposit-close-btn');
    var addressesData = null;
    var paymentMethodsData = { crypto: [], bank: [], card: [] };
    var currentDepositTxId = null;
    var currentDepositExpiresAtMs = null;
    var depositCountdownIv = null;
    var userUsdBalance = <?php echo json_encode((float) $walletTotalUsd); ?>;

    function openDrawer(drawer) {
        if (!drawer || !backdrop) return;
        // Close other drawer if open
        if (drawer === depositDrawer && withdrawDrawer) {
            withdrawDrawer.style.transform = 'translateX(100%)';
        } else if (drawer === withdrawDrawer && depositDrawer) {
            depositDrawer.style.transform = 'translateX(100%)';
        }
        // Open selected drawer
        drawer.style.transform = 'translateX(0)';
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer(drawer) {
        if (!drawer || !backdrop) return;
        drawer.style.transform = 'translateX(100%)';
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
    function closeAllDrawers() {
        closeDrawer(depositDrawer);
        closeDrawer(withdrawDrawer);
    }

    function getSelectedDepositMethod() {
        var methodType = (document.getElementById('deposit-method') || {}).value || 'crypto';
        if (methodType === 'crypto') {
            var sel = document.getElementById('deposit-currency');
            if (!sel || !sel.value) return null;
            var id = parseInt(sel.value, 10);
            return (paymentMethodsData.crypto || []).find(function(m){ return m.id === id; }) || null;
        }
        if (methodType === 'bank') {
            var banks = paymentMethodsData.bank || [];
            if (!banks.length) return null;
            var bankSel = document.getElementById('deposit-bank-option');
            if (bankSel && bankSel.value) {
                var bankId = parseInt(bankSel.value, 10);
                return banks.find(function(m){ return m.id === bankId; }) || null;
            }
            return banks.length === 1 ? banks[0] : null;
        }
        var cards = paymentMethodsData.card || [];
        if (!cards.length) return null;
        var cardSel = document.getElementById('deposit-card-option');
        if (cardSel && cardSel.value) {
            var cardId = parseInt(cardSel.value, 10);
            return cards.find(function(m){ return m.id === cardId; }) || null;
        }
        return cards.length === 1 ? cards[0] : null;
    }

    function buildDepositCryptoOptions(methods) {
        if (!methods || !methods.length) return '<option value="">No crypto deposit currencies</option>';
        return methods.map(function(m){
            return '<option value="' + m.id + '" data-symbol="' + (m.symbol || '') + '" data-coin-key="' + (m.coin_key || '') + '">' + (m.display_name || m.symbol) + ' (' + (m.symbol || '') + ')</option>';
        }).join('');
    }

    function buildDepositBankOptions(methods) {
        if (!methods || !methods.length) return '<option value="">No bank accounts configured</option>';
        return methods.map(function(m){
            var label = m.label || m.bank_name || 'Bank Transfer';
            if (m.bank_name && m.label !== m.bank_name) label += ' — ' + m.bank_name;
            return '<option value="' + m.id + '">' + label.replace(/</g, '&lt;') + '</option>';
        }).join('');
    }

    function buildDepositCardOptions(methods) {
        if (!methods || !methods.length) return '<option value="">No card options configured</option>';
        return methods.map(function(m){
            var brand = (m.card_brand || 'card').toUpperCase();
            var label = m.label || (brand + ' Card');
            return '<option value="' + m.id + '">' + label.replace(/</g, '&lt;') + '</option>';
        }).join('');
    }

    function syncDepositMethodUI() {
        var method = (document.getElementById('deposit-method') || {}).value || 'crypto';
        var cryptoFields = document.getElementById('deposit-crypto-fields');
        var bankFields = document.getElementById('deposit-bank-fields');
        var cardFields = document.getElementById('deposit-card-fields');
        if (cryptoFields) cryptoFields.classList.toggle('hidden', method !== 'crypto');
        if (bankFields) bankFields.classList.toggle('hidden', method !== 'bank');
        if (cardFields) cardFields.classList.toggle('hidden', method !== 'card');
        updateDepositCoinQuote();
    }

    function renderBankDetails(el, m) {
        if (!el || !m) return;
        var rows = [
            ['Bank', m.bank_name],
            ['Account Name', m.account_name],
            ['Account Number', m.account_number],
            ['Routing', m.routing_number],
            ['SWIFT', m.swift_code],
            ['IBAN', m.iban],
            ['Branch', m.bank_branch],
            ['Address', m.bank_address],
            ['Notes', m.bank_notes]
        ].filter(function(r){ return r[1]; });
        el.innerHTML = rows.map(function(r){
            return '<div class="flex justify-between gap-3 py-1 border-b border-slate-200/60 dark:border-zinc-700/60 last:border-0"><span class="text-slate-500 shrink-0">' + r[0] + '</span><span class="font-semibold text-right break-all">' + String(r[1]).replace(/</g,'&lt;') + '</span></div>';
        }).join('');
    }

    function renderCardDetails(el, m) {
        if (!el || !m) return;
        var brand = (m.card_brand || 'card').toUpperCase();
        var rows = [
            ['Brand', brand],
            ['Cardholder', m.card_holder_name],
            ['Card Number', m.card_number],
            ['Expiry', m.card_expiry]
        ].filter(function(r){ return r[1]; });
        el.innerHTML = rows.map(function(r){
            return '<div class="flex justify-between gap-3 py-1 border-b border-slate-200/60 dark:border-zinc-700/60 last:border-0"><span class="text-slate-500 shrink-0">' + r[0] + '</span><span class="font-semibold text-right break-all">' + String(r[1]).replace(/</g,'&lt;') + '</span></div>';
        }).join('');
    }

    function syncWithdrawMethodUI() {
        var method = (document.getElementById('withdraw-method') || {}).value || 'crypto';
        document.getElementById('withdraw-crypto-fields').classList.toggle('hidden', method !== 'crypto');
        document.getElementById('withdraw-bank-fields').classList.toggle('hidden', method !== 'bank');
        document.getElementById('withdraw-card-fields').classList.toggle('hidden', method !== 'card');
        if (method !== 'crypto') {
            var quote = document.getElementById('withdraw-coin-quote');
            if (quote) quote.textContent = '—';
        } else {
            updateWithdrawCoinQuote();
        }
    }

    // Load payment methods for both drawers
    var urlAction = (function(){ var m = window.location.search.match(/[?&]action=([^&]+)/); return m ? m[1] : null; })();
    fetch('/api/addresses.php').then(function(r){ return r.json(); }).then(function(d){
        if (d.success && d.methods && d.methods.length > 0) {
            paymentMethodsData.all = d.methods;
            paymentMethodsData.crypto = d.crypto || d.addresses || [];
            paymentMethodsData.bank = d.bank || [];
            paymentMethodsData.card = d.card || [];
            addressesData = paymentMethodsData.crypto;
            var depositCryptoSel = document.getElementById('deposit-currency');
            if (depositCryptoSel) depositCryptoSel.innerHTML = buildDepositCryptoOptions(paymentMethodsData.crypto);
            var depositBankSel = document.getElementById('deposit-bank-option');
            if (depositBankSel) depositBankSel.innerHTML = buildDepositBankOptions(paymentMethodsData.bank);
            var depositCardSel = document.getElementById('deposit-card-option');
            if (depositCardSel) depositCardSel.innerHTML = buildDepositCardOptions(paymentMethodsData.card);
            var depositMethodSel = document.getElementById('deposit-method');
            if (depositMethodSel) {
                depositMethodSel.querySelector('option[value="bank"]').disabled = paymentMethodsData.bank.length === 0;
                depositMethodSel.querySelector('option[value="card"]').disabled = paymentMethodsData.card.length === 0;
                syncDepositMethodUI();
            }
            var withdrawSelect = document.getElementById('withdraw-currency');
            var withdrawOptions = paymentMethodsData.crypto.map(function(a){
                return '<option value="' + (a.symbol || '').toUpperCase() + '" data-coin-key="' + (a.coin_key || '') + '">' + (a.display_name || a.symbol) + ' (' + (a.symbol || '') + ')</option>';
            }).join('');
            if (withdrawSelect) {
                withdrawSelect.innerHTML = withdrawOptions || '<option value="">No crypto payout currencies</option>';
                updateWithdrawBalance();
                updateWithdrawCoinQuote();
            }
            var withdrawMethodSel = document.getElementById('withdraw-method');
            if (withdrawMethodSel) {
                withdrawMethodSel.querySelector('option[value="bank"]').disabled = paymentMethodsData.bank.length === 0;
                withdrawMethodSel.querySelector('option[value="card"]').disabled = paymentMethodsData.card.length === 0;
                syncWithdrawMethodUI();
            }
            // Auto-open drawer if redirected from dashboard with action param
            if (urlAction === 'deposit' && depositDrawer) {
                depositStep1.classList.remove('hidden');
                depositStep2.classList.add('hidden');
                var errEl = document.getElementById('deposit-error');
                if (errEl) errEl.classList.add('hidden');
                openDrawer(depositDrawer);
                history.replaceState({}, '', window.location.pathname);
            } else if (urlAction === 'withdraw' && withdrawDrawer) {
                updateWithdrawBalance();
                updateWithdrawCoinQuote();
                openDrawer(withdrawDrawer);
                history.replaceState({}, '', window.location.pathname);
            }
        }
    });

    // Deposit drawer handlers
    document.getElementById('deposit-btn').addEventListener('click', function(){
        depositStep1.classList.remove('hidden');
        depositStep2.classList.add('hidden');
        document.getElementById('deposit-error').classList.add('hidden');
        syncDepositMethodUI();
        var ref2 = document.getElementById('deposit-reference-step2');
        if (ref2) ref2.value = '';
        var pf = document.getElementById('deposit-proof-file');
        if (pf) pf.value = '';
        openDrawer(depositDrawer);
    });
    document.getElementById('deposit-drawer-close').addEventListener('click', function(){ closeDrawer(depositDrawer); });
    if (depositDoneBtn) depositDoneBtn.addEventListener('click', function(){
        if (!currentDepositTxId) { closeDrawer(depositDrawer); window.location.reload(); return; }
        depositDoneBtn.disabled = true;
        if (depositDoneMsg) {
            depositDoneMsg.textContent = 'Saving...';
            depositDoneMsg.className = 'text-sm text-slate-500';
            depositDoneMsg.classList.remove('hidden');
        }
        var referenceStep2 = document.getElementById('deposit-reference-step2');
        var proofFile = document.getElementById('deposit-proof-file');
        var refVal = referenceStep2 ? referenceStep2.value.trim() : '';
        var hasFile = proofFile && proofFile.files && proofFile.files.length > 0;
        var opts = { method: 'POST', credentials: 'same-origin' };
        if (hasFile) {
            var fd = new FormData();
            fd.append('transaction_id', currentDepositTxId);
            fd.append('reference', refVal);
            fd.append('proof', proofFile.files[0]);
            opts.body = fd;
        } else {
            opts.headers = { 'Content-Type': 'application/json' };
            opts.body = JSON.stringify({ transaction_id: currentDepositTxId, reference: refVal || undefined });
        }
        fetch('/api/user/deposit-done.php', opts).then(function(r){ return r.json(); }).then(function(res){
            if (res && res.success) {
                if (depositDoneMsg) {
                    depositDoneMsg.textContent = (res.data && res.data.message) ? res.data.message : 'Deposit marked as done.';
                    depositDoneMsg.className = 'text-sm text-emerald-600';
                    depositDoneMsg.classList.remove('hidden');
                }
                setTimeout(function(){ closeDrawer(depositDrawer); window.location.reload(); }, 900);
            } else {
                depositDoneBtn.disabled = false;
                if (depositDoneMsg) {
                    depositDoneMsg.textContent = (res && res.error) ? res.error : 'Failed to save.';
                    depositDoneMsg.className = 'text-sm text-red-600';
                    depositDoneMsg.classList.remove('hidden');
                }
            }
        }).catch(function(){
            depositDoneBtn.disabled = false;
            if (depositDoneMsg) {
                depositDoneMsg.textContent = 'Request failed.';
                depositDoneMsg.className = 'text-sm text-red-600';
                depositDoneMsg.classList.remove('hidden');
            }
        });
    });
    if (backdrop) backdrop.addEventListener('click', closeAllDrawers);

    function updateDepositCoinQuote() {
        var amountUsd = parseFloat(document.getElementById('deposit-amount').value) || 0;
        var methodType = (document.getElementById('deposit-method') || {}).value || 'crypto';
        var pm = getSelectedDepositMethod();
        var coinQuote = document.getElementById('deposit-coin-quote');
        var bankQuote = document.getElementById('deposit-bank-quote');
        var cardQuote = document.getElementById('deposit-card-quote');
        if (bankQuote) bankQuote.textContent = '—';
        if (cardQuote) cardQuote.textContent = '—';

        if (methodType === 'bank') {
            if (coinQuote) coinQuote.textContent = '—';
            if (!bankQuote) return;
            if (!pm || amountUsd <= 0) { bankQuote.textContent = '—'; return; }
            bankQuote.textContent = 'Deposit amount: $' + amountUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';
            return;
        }
        if (methodType === 'card') {
            if (coinQuote) coinQuote.textContent = '—';
            if (!cardQuote) return;
            if (!pm || amountUsd <= 0) { cardQuote.textContent = '—'; return; }
            cardQuote.textContent = 'Deposit amount: $' + amountUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';
            return;
        }

        var el = coinQuote;
        if (!el) return;
        if (!pm || amountUsd <= 0) { el.textContent = '—'; return; }
        var currency = pm.symbol || '';
        el.textContent = 'Fetching rate…';
        var coinKey = pm.coin_key || null;
        if (!coinKey && window.BloombitCryptoConfig && window.BloombitCryptoConfig.getCoinIdBySymbol) {
            coinKey = window.BloombitCryptoConfig.getCoinIdBySymbol(currency);
        }
        var stablecoins = ['USDT','USDC','BUSD','USD','DAI'];
        var price = stablecoins.indexOf((currency || '').toUpperCase()) >= 0 ? 1 : null;
        var fetchPrice = price === null && window.BloombitCryptoPrices && window.BloombitCryptoPrices.fetch;
        (fetchPrice ? window.BloombitCryptoPrices.fetch([coinKey || 'bitcoin']) : Promise.resolve(price !== null ? { [coinKey || 'tether']: { usd: 1 } } : {}))
            .then(function(prices){
                if (price === null && prices && coinKey && prices[coinKey] && prices[coinKey].usd != null) {
                    price = prices[coinKey].usd;
                } else if (price === null && stablecoins.indexOf((currency || '').toUpperCase()) >= 0) {
                    price = 1;
                }
                if (price != null && price > 0) {
                    var coinAmt = amountUsd / price;
                    var disp = coinAmt >= 1 ? coinAmt.toFixed(4) : (coinAmt >= 0.01 ? coinAmt.toFixed(6) : coinAmt.toFixed(8));
                    el.textContent = 'You will send: ' + disp + ' ' + currency + (price !== 1 ? ' (Rate: $' + price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) + '/' + currency + ')' : '');
                } else {
                    el.textContent = 'Unable to fetch rate';
                }
            }).catch(function(){ el.textContent = 'Unable to fetch rate'; });
    }

    if (document.getElementById('deposit-amount')) {
        document.getElementById('deposit-amount').addEventListener('input', updateDepositCoinQuote);
        document.getElementById('deposit-amount').addEventListener('change', updateDepositCoinQuote);
    }
    if (document.getElementById('deposit-currency')) {
        document.getElementById('deposit-currency').addEventListener('change', updateDepositCoinQuote);
    }
    if (document.getElementById('deposit-bank-option')) {
        document.getElementById('deposit-bank-option').addEventListener('change', updateDepositCoinQuote);
    }
    if (document.getElementById('deposit-card-option')) {
        document.getElementById('deposit-card-option').addEventListener('change', updateDepositCoinQuote);
    }
    var depositMethodEl = document.getElementById('deposit-method');
    if (depositMethodEl) depositMethodEl.addEventListener('change', syncDepositMethodUI);

    document.getElementById('deposit-submit-btn').addEventListener('click', function(){
        var methodType = (document.getElementById('deposit-method') || {}).value || 'crypto';
        var pm = getSelectedDepositMethod();
        var amountUsd = parseFloat(document.getElementById('deposit-amount').value) || 0;
        var errEl = document.getElementById('deposit-error');
        if (amountUsd <= 0) {
            errEl.textContent = 'Please enter a USD amount';
            errEl.classList.remove('hidden');
            return;
        }
        if (!pm) {
            if (methodType === 'crypto') errEl.textContent = 'Please select a deposit currency';
            else if (methodType === 'bank') errEl.textContent = 'Please select a bank account';
            else errEl.textContent = 'Please select a card option';
            errEl.classList.remove('hidden');
            return;
        }
        errEl.classList.add('hidden');
        fetch('/api/user/deposit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ payment_method_id: pm.id, amount_usd: amountUsd })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                currentDepositTxId = res.data && res.data.transaction_id ? res.data.transaction_id : null;
                var countdownMins = (res.data && res.data.countdown_minutes) ? parseInt(res.data.countdown_minutes, 10) : 30;
                if (depositCountdownMinsEl) depositCountdownMinsEl.textContent = String(countdownMins || 30);
                // Use duration-based countdown to avoid browser/server timezone mismatch.
                currentDepositExpiresAtMs = Date.now() + ((countdownMins || 30) * 60 * 1000);
                if (depositDoneBtn) depositDoneBtn.disabled = false;
                if (depositDoneMsg) depositDoneMsg.classList.add('hidden');

                // start / restart countdown
                if (depositCountdownIv) { try { clearInterval(depositCountdownIv); } catch (e) {} }
                function tickCountdown(){
                    if (!depositCountdownTimer) return;
                    var ms = (currentDepositExpiresAtMs || 0) - Date.now();
                    if (ms <= 0) {
                        depositCountdownTimer.textContent = '00:00';
                        if (depositDoneBtn) depositDoneBtn.disabled = true;
                        if (depositDoneMsg) {
                            depositDoneMsg.textContent = 'Deposit expired. Marking as failed...';
                            depositDoneMsg.className = 'text-sm text-red-600';
                            depositDoneMsg.classList.remove('hidden');
                        }
                        // Trigger backend expire for this deposit (idempotent)
                        if (currentDepositTxId) {
                            fetch('/api/user/deposit-expire.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                credentials: 'same-origin',
                                body: JSON.stringify({ transaction_id: currentDepositTxId })
                            }).then(function(r){ return r.json(); }).then(function(){
                                setTimeout(function(){ window.location.reload(); }, 1200);
                            }).catch(function(){
                                setTimeout(function(){ window.location.reload(); }, 1200);
                            });
                        }
                        if (depositCountdownIv) { try { clearInterval(depositCountdownIv); } catch (e) {} }
                        return;
                    }
                    var totalSec = Math.floor(ms / 1000);
                    var mm = Math.floor(totalSec / 60);
                    var ss = totalSec % 60;
                    depositCountdownTimer.textContent = String(mm).padStart(2,'0') + ':' + String(ss).padStart(2,'0');
                }
                tickCountdown();
                depositCountdownIv = setInterval(tickCountdown, 1000);

                pm = (res.data && res.data.payment_method) ? res.data.payment_method : pm;
                var cryptoWrap = document.getElementById('deposit-crypto-address-wrap');
                var bankWrap = document.getElementById('deposit-bank-details-wrap');
                var cardWrap = document.getElementById('deposit-card-details-wrap');
                if (cryptoWrap) cryptoWrap.classList.toggle('hidden', pm.method_type !== 'crypto');
                if (bankWrap) bankWrap.classList.toggle('hidden', pm.method_type !== 'bank');
                if (cardWrap) cardWrap.classList.toggle('hidden', pm.method_type !== 'card');

                if (pm.method_type === 'crypto') {
                    document.getElementById('deposit-address-display').value = pm.wallet_address || pm.address || '';
                } else if (pm.method_type === 'bank') {
                    renderBankDetails(bankWrap, pm);
                } else if (pm.method_type === 'card') {
                    renderCardDetails(cardWrap, pm);
                }

                var step2Instructions = document.getElementById('deposit-step2-instructions');
                if (step2Instructions) {
                    if (pm.method_type === 'crypto') {
                        step2Instructions.textContent = 'Please send your funds to the address below. Your deposit will be credited after admin approval.';
                    } else if (pm.method_type === 'bank') {
                        step2Instructions.textContent = 'Transfer funds to the bank account below. Your deposit will be credited after admin approval.';
                    } else {
                        step2Instructions.textContent = 'Complete payment using the card details below. Your deposit will be credited after admin approval.';
                    }
                }

                var methodLabel = pm.method_type === 'crypto'
                    ? (pm.symbol || 'Crypto')
                    : (pm.label || pm.display_name || (pm.method_type === 'bank' ? 'Bank Transfer' : 'Card'));
                document.getElementById('deposit-selected-currency').textContent = methodLabel;
                if (pm.method_type === 'crypto') {
                    var currency = pm.symbol || '';
                    var coinAmt = res.data && res.data.coin_amount != null ? res.data.coin_amount : amountUsd;
                    document.getElementById('deposit-selected-amount').textContent = (typeof coinAmt === 'number' ? coinAmt.toFixed(8) : coinAmt) + ' ' + currency;
                } else {
                    document.getElementById('deposit-selected-amount').textContent = '$' + amountUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';
                }
                depositStep1.classList.add('hidden');
                depositStep2.classList.remove('hidden');
                if (depositIHavePaidBtn) depositIHavePaidBtn.classList.remove('hidden');
                if (depositAfterPaymentSection) depositAfterPaymentSection.classList.add('hidden');
            } else {
                errEl.textContent = res.error || 'Failed to submit deposit';
                errEl.classList.remove('hidden');
            }
        }).catch(function(){ errEl.textContent = 'Request failed'; errEl.classList.remove('hidden'); });
    });
    var depositIHavePaidBtn = document.getElementById('deposit-i-have-paid-btn');
    var depositAfterPaymentSection = document.getElementById('deposit-after-payment-section');
    if (depositIHavePaidBtn && depositAfterPaymentSection) {
        depositIHavePaidBtn.addEventListener('click', function(){
            depositAfterPaymentSection.classList.remove('hidden');
            depositIHavePaidBtn.classList.add('hidden');
        });
    }
    document.getElementById('deposit-copy-addr').addEventListener('click', function(){
        var addr = document.getElementById('deposit-address-display').value;
        if (addr && navigator.clipboard) {
            navigator.clipboard.writeText(addr).then(function(){
                var btn = document.getElementById('deposit-copy-addr');
                btn.textContent = 'Copied!';
                setTimeout(function(){ btn.textContent = 'Copy'; }, 1500);
            });
        }
    });

    // Withdraw drawer handlers
    var withdrawBtn = document.getElementById('withdraw-btn');
    var withdrawCloseBtn = document.getElementById('withdraw-drawer-close');
    if (withdrawBtn && withdrawDrawer) {
        withdrawBtn.addEventListener('click', function(){ 
            if (withdrawDrawer) {
                syncWithdrawMethodUI();
                openDrawer(withdrawDrawer); 
                updateWithdrawBalance();
                updateWithdrawCoinQuote();
            }
        });
    }
    if (withdrawCloseBtn && withdrawDrawer) {
        withdrawCloseBtn.addEventListener('click', function(){ closeDrawer(withdrawDrawer); });
    }
    var minWithdrawLimitEl = document.getElementById('withdraw-min-limit');
    var minWithdrawLimitUsd = parseFloat(minWithdrawLimitEl ? minWithdrawLimitEl.textContent : '10') || 10;

    var stableWithdrawCoins = ['USDT', 'USDC', 'BUSD', 'USD', 'DAI'];

    function updateWithdrawBalance() {
        var availEl = document.getElementById('withdraw-available');
        if (availEl) availEl.textContent = '$' + userUsdBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function updateWithdrawCoinQuote() {
        var amountUsd = parseFloat(document.getElementById('withdraw-amount').value) || 0;
        var currency = document.getElementById('withdraw-currency').value;
        var el = document.getElementById('withdraw-coin-quote');
        if (!el) return;
        if (!currency || amountUsd <= 0) { el.textContent = '—'; return; }
        el.textContent = 'Fetching rate…';
        var coinKey = (function(){
            var sel = document.getElementById('withdraw-currency');
            if (!sel || !sel.options[sel.selectedIndex]) return null;
            return sel.options[sel.selectedIndex].getAttribute('data-coin-key') || null;
        })();
        if (!coinKey && window.BloombitCryptoConfig && window.BloombitCryptoConfig.getCoinIdBySymbol) {
            coinKey = window.BloombitCryptoConfig.getCoinIdBySymbol(currency);
        }
        var stablecoins = ['USDT','USDC','BUSD','USD','DAI'];
        var price = stablecoins.indexOf((currency || '').toUpperCase()) >= 0 ? 1 : null;
        var fetchPrice = price === null && window.BloombitCryptoPrices && window.BloombitCryptoPrices.fetch;
        (fetchPrice ? window.BloombitCryptoPrices.fetch([coinKey || 'bitcoin']) : Promise.resolve(price !== null ? { [coinKey || 'tether']: { usd: 1 } } : {}))
            .then(function(prices){
                if (price === null && prices && coinKey && prices[coinKey] && prices[coinKey].usd != null) {
                    price = prices[coinKey].usd;
                } else if (price === null && stablecoins.indexOf((currency || '').toUpperCase()) >= 0) {
                    price = 1;
                }
                if (price != null && price > 0) {
                    var coinAmt = amountUsd / price;
                    var disp = coinAmt >= 1 ? coinAmt.toFixed(4) : (coinAmt >= 0.01 ? coinAmt.toFixed(6) : coinAmt.toFixed(8));
                    el.textContent = 'You will receive approx. ' + disp + ' ' + currency + (price !== 1 ? ' (Rate: $' + price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 4}) + '/' + currency + ')' : '');
                } else {
                    el.textContent = 'Unable to fetch rate';
                }
            }).catch(function(){ el.textContent = 'Unable to fetch rate'; });
    }

    if (document.getElementById('withdraw-amount')) {
        document.getElementById('withdraw-amount').addEventListener('input', updateWithdrawCoinQuote);
        document.getElementById('withdraw-amount').addEventListener('change', updateWithdrawCoinQuote);
    }
    if (document.getElementById('withdraw-currency')) {
        document.getElementById('withdraw-currency').addEventListener('change', function(){ updateWithdrawBalance(); updateWithdrawCoinQuote(); });
    }
    var withdrawMethodEl = document.getElementById('withdraw-method');
    if (withdrawMethodEl) withdrawMethodEl.addEventListener('change', syncWithdrawMethodUI);
    document.getElementById('withdrawal-form').addEventListener('submit', function(e){
        e.preventDefault();
        var method = (document.getElementById('withdraw-method') || {}).value || 'crypto';
        var amountUsd = parseFloat(document.getElementById('withdraw-amount').value) || 0;
        var msgEl = document.getElementById('withdrawal-message');
        var payload = { withdrawal_method: method, amount_usd: amountUsd };

        if (method === 'crypto') {
            var currency = document.getElementById('withdraw-currency').value;
            var address = document.getElementById('withdraw-address').value.trim();
            if (!currency || amountUsd <= 0 || !address) {
                msgEl.textContent = 'Please fill all fields';
                msgEl.className = 'text-sm text-red-500';
                msgEl.classList.remove('hidden');
                return;
            }
            payload.currency = currency;
            payload.address = address;
        } else if (method === 'bank') {
            var bankName = (document.getElementById('withdraw-bank-name') || {}).value.trim();
            var accountName = (document.getElementById('withdraw-account-name') || {}).value.trim();
            var accountNumber = (document.getElementById('withdraw-account-number') || {}).value.trim();
            var routingNumber = (document.getElementById('withdraw-routing-number') || {}).value.trim();
            var swiftIban = (document.getElementById('withdraw-swift-iban') || {}).value.trim();
            if (!bankName || !accountName || !accountNumber || amountUsd <= 0) {
                msgEl.textContent = 'Bank name, account name, account number, and amount are required';
                msgEl.className = 'text-sm text-red-500';
                msgEl.classList.remove('hidden');
                return;
            }
            var payoutDetails = {
                bank_name: bankName,
                account_name: accountName,
                account_number: accountNumber
            };
            if (routingNumber) payoutDetails.routing_number = routingNumber;
            if (swiftIban) {
                if (/^[A-Z]{2}[0-9A-Z]{13,32}$/i.test(swiftIban.replace(/\s/g, ''))) {
                    payoutDetails.iban = swiftIban.replace(/\s/g, '');
                } else {
                    payoutDetails.swift_code = swiftIban;
                }
            }
            payload.payout_details = payoutDetails;
        } else {
            var cardBrand = (document.getElementById('withdraw-card-brand') || {}).value;
            var cardNumber = ((document.getElementById('withdraw-card-number') || {}).value || '').replace(/\D/g, '');
            var cardHolder = (document.getElementById('withdraw-card-holder') || {}).value.trim();
            var cardExpiry = (document.getElementById('withdraw-card-expiry') || {}).value.trim();
            if (!cardBrand || !cardNumber || amountUsd <= 0) {
                msgEl.textContent = 'Card brand, card number, and amount are required';
                msgEl.className = 'text-sm text-red-500';
                msgEl.classList.remove('hidden');
                return;
            }
            payload.payout_details = {
                card_brand: cardBrand,
                card_number: cardNumber,
                card_holder_name: cardHolder || undefined,
                card_expiry: cardExpiry || undefined
            };
        }

        msgEl.classList.add('hidden');
        var maxWithdrawLimitEl = document.getElementById('withdraw-max-limit');
        var maxWithdrawLimitUsd = parseFloat(maxWithdrawLimitEl ? maxWithdrawLimitEl.textContent : '') || 0;
        if (amountUsd < minWithdrawLimitUsd) {
            msgEl.textContent = 'Minimum withdrawal is $' + minWithdrawLimitUsd.toFixed(2) + ' USD.';
            msgEl.className = 'text-sm text-red-500';
            msgEl.classList.remove('hidden');
            return;
        }
        if (maxWithdrawLimitUsd > 0 && amountUsd > maxWithdrawLimitUsd) {
            msgEl.textContent = 'Maximum withdrawal is $' + maxWithdrawLimitUsd.toFixed(2) + ' USD.';
            msgEl.className = 'text-sm text-red-500';
            msgEl.classList.remove('hidden');
            return;
        }
        if (amountUsd > userUsdBalance + 0.000001) {
            msgEl.textContent = 'Insufficient USD balance. You have $' + userUsdBalance.toFixed(2) + ' available.';
            msgEl.className = 'text-sm text-red-500';
            msgEl.classList.remove('hidden');
            return;
        }
        fetch('/api/user/withdraw.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                msgEl.textContent = res.data.message || 'Withdrawal request submitted';
                msgEl.className = 'text-sm text-emerald-500';
                msgEl.classList.remove('hidden');
                setTimeout(function(){ closeDrawer(withdrawDrawer); window.location.reload(); }, 2000);
            } else {
                msgEl.textContent = res.error || 'Failed';
                msgEl.className = 'text-sm text-red-500';
                msgEl.classList.remove('hidden');
            }
        }).catch(function(){
            msgEl.textContent = 'Request failed';
            msgEl.className = 'text-sm text-red-500';
            msgEl.classList.remove('hidden');
        });
    });
});
</script>
</body></html>

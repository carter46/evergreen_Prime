<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'wallet';
$siteName = get_site_name();
$walletBalances = [];
$walletTotalUsd = 0;
$walletTransactions = [];
$btcAmount = 0;
$highestCoin = 'BTC';
$highestAmount = 0;
$highestCoinLogo = 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png';
$totalProfit = 0;
$activeCapital = 0;
$dailyEarning = 0;
$coinLogosMap = ['BTC'=>'https://assets.coingecko.com/coins/images/1/large/bitcoin.png','ETH'=>'https://assets.coingecko.com/coins/images/279/large/ethereum.png','USDT'=>'https://assets.coingecko.com/coins/images/325/large/Tether.png','USDC'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png','BUSD'=>'https://assets.coingecko.com/coins/images/9576/large/BUSD.png','USD'=>'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png','XRP'=>'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png','SOL'=>'https://assets.coingecko.com/coins/images/4128/large/solana.png','BNB'=>'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png','ADA'=>'https://assets.coingecko.com/coins/images/975/large/cardano.png','DOGE'=>'https://assets.coingecko.com/coins/images/5/large/dogecoin.png','TRX'=>'https://assets.coingecko.com/coins/images/1094/large/tron-logo.png'];
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amt = (float) $row['amount'];
        $currency = strtoupper($row['currency']);
        $usd = $amt;
        if (in_array($currency, ['USDT','USDC','BUSD','USD'], true)) $usd = $amt;
        elseif ($currency === 'BTC') { $usd = $amt * 65000; $btcAmount = $amt; }
        elseif ($currency === 'ETH') { $usd = $amt * 3500; }
        elseif ($currency === 'XRP') { $usd = $amt * 0.55; }
        elseif ($currency === 'SOL') { $usd = $amt * 100; }
        elseif ($currency === 'BNB') { $usd = $amt * 582; }
        else $usd = $amt;
        $walletBalances[] = ['currency' => $currency, 'amount' => $amt, 'usd_value' => round($usd, 2)];
        $walletTotalUsd += $usd;
    }
    usort($walletBalances, function($a, $b) { return ($b['usd_value'] <=> $a['usd_value']); });
    $topCoins = array_slice(array_filter($walletBalances, function($b) { return $b['usd_value'] > 0; }), 0, 3);
    $extraCoinCount = max(0, count(array_filter($walletBalances, function($b) { return $b['usd_value'] > 0; })) - 3);
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ? AND type = 'payout' AND status = 'completed'");
    $r->execute([$userId]); $totalProfit = (float)$r->fetchColumn();
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM user_investments WHERE user_id = ? AND status = 'active'");
    $r->execute([$userId]); $activeCapital = (float)$r->fetchColumn();
    $r = $pdo->prepare("SELECT COALESCE(AVG(amount), 0) FROM transactions WHERE user_id = ? AND type = 'payout' AND status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $r->execute([$userId]); $dailyEarning = (float)$r->fetchColumn();
    $stmt = $pdo->prepare('SELECT id, type, amount, currency, status, reference, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $walletTransactions[] = $row;
    }
} catch (Throwable $e) { }
$coinNames = ['BTC'=>'Bitcoin','ETH'=>'Ethereum','USDT'=>'Tether','USDC'=>'USD Coin','BUSD'=>'Binance USD','USD'=>'US Dollar','XRP'=>'XRP','SOL'=>'Solana','BNB'=>'BNB','ADA'=>'Cardano','DOGE'=>'Dogecoin','TRX'=>'TRON'];
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Wallet &amp; Withdrawals</title>
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
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen font-display overflow-x-hidden">
<div class="flex min-h-screen overflow-x-hidden">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 flex flex-col overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="flex-1 max-w-[1440px] w-full mx-auto">
<div class="grid grid-cols-12 gap-8">
<!-- Row 1: Full-width Total Estimated Balance -->
<div class="col-span-12">
<div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-800 to-black p-8 text-white shadow-2xl">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
<div class="relative z-10">
<div>
<p class="text-slate-400 text-sm font-medium mb-1">Total Estimated Balance</p>
<h1 class="text-6xl font-bold tracking-tight">$<?php echo number_format($walletTotalUsd, 2); ?> <span class="text-xl font-normal text-slate-400 ml-2">USD</span></h1>
<p class="text-primary mt-2 flex items-center gap-1 flex-wrap">
<?php
$parts = [];
foreach ($topCoins as $c) {
    $logo = $coinLogosMap[$c['currency']] ?? 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png';
    $amt = $c['amount'] > 0 ? round($c['amount']) : 0;
    $parts[] = '<img class="w-5 h-5" src="' . htmlspecialchars($logo) . '" alt="' . htmlspecialchars($c['currency']) . '"/>' . $amt . ' ' . htmlspecialchars($c['currency']);
}
echo implode(' <span class="text-white/60 mx-1">|</span> ', $parts);
if ($extraCoinCount > 0) echo ' <span class="text-white/80 font-bold ml-1">+'.$extraCoinCount.'</span>';
?>
</p>
<div class="flex gap-3 mt-4">
<button type="button" id="deposit-btn" class="bg-primary hover:bg-primary/90 text-black px-6 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-all">
<span class="material-icons text-sm">add</span> Deposit
</button>
<button type="button" id="withdraw-btn" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-all backdrop-blur-sm">
<span class="material-icons text-sm">file_upload</span> Withdraw
</button>
</div>
</div>
<div class="mt-10 grid grid-cols-3 gap-6 border-t border-white/10 pt-8">
<div>
<p class="text-slate-400 text-xs mb-1">Total Profit</p>
<p class="font-bold text-emerald-400">$<?php echo number_format($totalProfit, 2); ?></p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Active Capital</p>
<p class="font-bold">$<?php echo number_format($activeCapital, 2); ?></p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Daily Earning</p>
<p class="font-bold text-primary">$<?php echo number_format($dailyEarning, 2); ?></p>
</div>
</div>
</div>
</div>
</div>

<!-- Row 2: Assets (50%) + Security (25%) + Coming Soon (25%) -->
<div class="col-span-12 grid grid-cols-1 lg:grid-cols-[2fr_1fr_1fr] gap-6">
<!-- Your Assets -->
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm min-w-0">
<div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
<h2 class="text-base font-bold">Your Assets</h2>
<div class="relative shrink-0">
<span class="material-icons absolute left-2 top-1/2 -translate-y-1/2 text-slate-400 text-xs">search</span>
<input class="pl-7 pr-3 py-1 bg-slate-50 dark:bg-slate-900 border-none rounded text-xs focus:ring-1 focus:ring-primary w-32" placeholder="Search..." type="text"/>
</div>
</div>
<div class="overflow-hidden">
<table class="w-full text-left table-fixed min-w-0">
<thead>
<tr class="text-slate-400 text-[9px] uppercase tracking-wider border-b border-slate-50 dark:border-slate-800">
<th class="px-3 py-2 font-semibold w-1/4">Asset</th>
<th class="px-3 py-2 font-semibold text-right w-1/4">Balance</th>
<th class="px-3 py-2 font-semibold text-right w-1/4">Value</th>
<th class="px-3 py-2 font-semibold text-right w-1/4">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50 dark:divide-slate-800 wallet-assets-table">
<?php
$coinIdMap = ['USDT'=>'tether','USDC'=>'usd-coin','BUSD'=>'binance-usd','USD'=>'usd-coin','BTC'=>'bitcoin','ETH'=>'ethereum','SOL'=>'solana','BNB'=>'binancecoin','XRP'=>'ripple','ADA'=>'cardano','DOGE'=>'dogecoin','TRX'=>'tron'];
$fmtBalance = function($amt, $cur) {
  $amt = (float)$amt;
  if ($amt <= 0) return '0';
  if (in_array($cur, ['USD','USDT','USDC','BUSD'], true)) return number_format($amt, 2);
  if ($amt >= 1000) return number_format(round($amt, 0));
  if ($amt >= 1) return number_format($amt, 2);
  if ($amt >= 0.01) return number_format($amt, 4);
  return rtrim(rtrim(number_format($amt, 6), '0'), '.');
};
foreach ($walletBalances as $b):
  $cu = strtoupper($b['currency']);
  $coinId = $coinIdMap[$cu] ?? strtolower($b['currency']);
  $logo = $coinLogosMap[$cu] ?? null;
  $name = $coinNames[$cu] ?? $b['currency'];
?>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" data-coin="<?php echo htmlspecialchars($coinId); ?>" data-balance="<?php echo $b['amount']; ?>">
<td class="px-3 py-3">
<div class="flex items-center gap-2 min-w-0">
<?php if ($logo): ?><div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center overflow-hidden bg-slate-100 dark:bg-slate-800"><img alt="<?php echo htmlspecialchars($cu); ?>" class="w-5 h-5 object-contain" src="<?php echo htmlspecialchars($logo); ?>" loading="lazy"/></div><?php else: ?><div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-500"><?php echo htmlspecialchars(substr($cu,0,1)); ?></div><?php endif; ?>
<div class="min-w-0">
<p class="font-bold text-xs truncate"><?php echo htmlspecialchars($name); ?></p>
<p class="text-[10px] text-slate-500"><?php echo $cu; ?></p>
</div>
</div>
</td>
<td class="px-3 py-3 text-right font-medium text-xs truncate"><?php echo $fmtBalance($b['amount'], $cu); ?> <?php echo $cu; ?></td>
<td class="px-3 py-3 text-right font-bold text-xs wallet-value truncate" data-coin="<?php echo $coinId; ?>">$<?php echo number_format($b['usd_value'], 2); ?></td>
<td class="px-3 py-3 text-right">
<button class="text-[10px] font-bold px-2 py-1 rounded bg-primary/10 text-primary hover:bg-primary hover:text-black transition-all">TRADE</button>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($walletBalances)): ?>
<tr><td class="px-3 py-6 text-center text-slate-500 text-xs" colspan="4">No balances yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<!-- Security Checklist -->
<div class="bg-primary/5 border border-primary/20 rounded-xl p-4 flex gap-4">
<div class="w-10 h-10 rounded-lg bg-primary/20 flex-shrink-0 flex items-center justify-center">
<span class="material-icons text-primary">gpp_maybe</span>
</div>
<div>
<h4 class="text-sm font-bold text-slate-900 dark:text-white">Security Checklist</h4>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Ensure 2FA is active before withdrawing. Double-check the recipient address; crypto transfers are irreversible.
                        </p>
</div>
</div>
<!-- Coming Soon -->
<div class="rounded-xl overflow-hidden relative group w-full">
<div class="bg-slate-900 p-6">
<h5 class="text-primary text-xs font-bold uppercase mb-1">Coming Soon</h5>
<h4 class="text-white font-bold mb-4">Earn up to 12% APY with Bloombit Staking</h4>
<img alt="Staking" class="w-full h-32 object-cover rounded-lg opacity-60 group-hover:opacity-100 transition-opacity" src="/uploads/images/crypto-assets.jpg" onerror="this.src='/uploads/images/crypto-assets.png';this.onerror=null"/>
<button class="w-full mt-4 py-2 border border-white/20 text-white text-xs font-bold rounded hover:bg-white/10 transition-colors">Join Waitlist</button>
</div>
</div>
</div>

<!-- Row 3: Full-width Recent History -->
<div class="col-span-12">
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Recent History</h2>
<a href="/dashboard/user/transactions" class="text-primary text-xs font-bold hover:underline">View All</a>
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
<?php foreach ($walletTransactions as $tx):
  $isDeposit = in_array($tx['type'], ['deposit','payout']);
  $date = !empty($tx['created_at']) ? date('M j, Y H:i', strtotime($tx['created_at'])) : '';
?>
<tr>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-icons <?php echo $isDeposit ? 'text-emerald-500' : 'text-red-500'; ?> text-lg"><?php echo $isDeposit ? 'arrow_downward' : 'arrow_upward'; ?></span>
<div>
<p class="text-sm font-bold"><?php echo htmlspecialchars(ucfirst($tx['type'])); ?></p>
<p class="text-[10px] text-slate-400"><?php echo date('M j, Y H:i', strtotime($tx['created_at'])); ?></p>
</div>
</div>
</td>
<td class="px-6 py-4 text-sm font-medium"><?php echo htmlspecialchars($tx['currency']); ?></td>
<td class="px-6 py-4 text-right text-sm font-bold <?php echo $isDeposit ? 'text-emerald-500' : 'text-red-500'; ?>"><?php echo $isDeposit ? '+' : '-'; ?><?php echo number_format((float)$tx['amount'], 4); ?></td>
<td class="px-6 py-4 text-center">
<?php
$statusClass = 'bg-amber-100 text-amber-700';
if ($tx['status'] === 'completed') $statusClass = 'bg-emerald-100 text-emerald-700';
elseif ($tx['status'] === 'rejected') $statusClass = 'bg-red-100 text-red-700';
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

<div id="wallet-drawer-backdrop" class="fixed inset-0 bg-black/50 z-[45] hidden" aria-hidden="true" style="backdrop-filter:blur(2px)"></div>
<div id="deposit-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[480px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-[50] border-l border-slate-200 dark:border-zinc-800 flex flex-col transition-transform duration-300 ease-out" style="transform:translateX(100%)">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Deposit Crypto</h2>
<button type="button" id="deposit-drawer-close" class="p-2 rounded-lg bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-slate-300 transition-colors"><span class="material-icons text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6">
<div id="deposit-form-step1">
<div class="space-y-4">
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Select Currency</label>
<select id="deposit-currency" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700">
<option value="">Loading...</option>
</select>
</div>
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Amount</label>
<input type="number" id="deposit-amount" step="any" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" placeholder="0.00"/>
<p class="text-xs text-slate-400 mt-1" id="deposit-usd-value">—</p>
</div>
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Reference / TX Hash <span class="text-slate-400 font-normal">(Optional)</span></label>
<input type="text" id="deposit-reference" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" placeholder="Transaction hash or reference"/>
</div>
<div id="deposit-error" class="text-sm text-red-500 hidden"></div>
<button type="button" id="deposit-submit-btn" class="w-full py-2 bg-primary text-black font-bold rounded-lg text-sm">Submit Deposit Request</button>
</div>
</div>
<div id="deposit-form-step2" class="hidden">
<div class="space-y-4">
<div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
<p class="text-sm font-medium text-emerald-700 dark:text-emerald-300 mb-2">Deposit request submitted!</p>
<p class="text-xs text-emerald-600 dark:text-emerald-400">Please send your funds to the address below. Your deposit will be credited after admin approval.</p>
</div>
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Send to Address</label>
<div class="flex items-center gap-2">
<input type="text" id="deposit-address-display" readonly class="flex-1 bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700 font-mono text-xs"/>
<button type="button" id="deposit-copy-addr" class="px-3 py-2 bg-primary text-black font-bold rounded-lg text-xs">Copy</button>
</div>
</div>
<div class="pt-4 border-t border-slate-200 dark:border-zinc-800">
<p class="text-xs text-slate-500 mb-2">Selected Currency: <span id="deposit-selected-currency" class="font-bold"></span></p>
<p class="text-xs text-slate-500">Amount: <span id="deposit-selected-amount" class="font-bold"></span></p>
</div>
<button type="button" id="deposit-close-btn" class="w-full py-2 bg-slate-200 dark:bg-zinc-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-sm mt-4">Close</button>
</div>
</div>
</div>

</div>

<div id="withdraw-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[480px] max-w-full bg-white dark:bg-zinc-900 shadow-2xl z-[50] border-l border-slate-200 dark:border-zinc-800 flex flex-col transition-transform duration-300 ease-out" style="transform:translateX(100%)">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Withdraw Funds</h2>
<button type="button" id="withdraw-drawer-close" class="p-2 rounded-lg bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-slate-300 transition-colors"><span class="material-icons text-lg">close</span></button>
</div>
<div class="flex-1 overflow-y-auto p-6">
<form id="withdrawal-form" class="space-y-4">
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Select Currency</label>
<select name="currency" id="withdraw-currency" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700">
<option value="">Loading...</option>
</select>
</div>
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Recipient Address</label>
<input name="address" id="withdraw-address" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" placeholder="Paste external wallet address" type="text" required/>
</div>
<div>
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Amount</label>
<div class="relative">
<input name="amount" id="withdraw-amount" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" placeholder="0.00" step="any" type="number" required/>
<span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400" id="withdraw-currency-label">—</span>
</div>
<p class="text-xs text-slate-400 mt-1">Available: <span id="withdraw-available">—</span></p>
<p class="text-xs text-slate-400 mt-1" id="withdraw-usd-value">—</p>
<?php
  $minW = get_site_setting('min_withdrawal_limit', '10');
  $maxW = get_site_setting('max_withdrawal_limit', '');
?>
<p class="text-xs text-amber-600 dark:text-amber-400 mt-1" id="withdraw-limit-hint">
  Min: $<span id="withdraw-min-limit"><?php echo htmlspecialchars($minW); ?></span> USD
  <?php if (!empty($maxW) && (float)$maxW > 0): ?>
    <span class="mx-1">•</span> Max: $<span id="withdraw-max-limit"><?php echo htmlspecialchars($maxW); ?></span> USD
  <?php endif; ?>
</p>
</div>
<div id="withdrawal-message" class="text-sm hidden"></div>
<button type="submit" class="w-full py-2 bg-primary text-black font-bold rounded-lg text-sm flex items-center justify-center gap-2">
Withdraw Now <span class="material-icons text-sm">arrow_forward</span>
</button>
</form>
</div>
</div>
<script src="/js/app.js"></script>
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
    var addressesData = null;
    var userBalances = <?php 
        $balanceMap = [];
        foreach ($walletBalances as $b) {
            $balanceMap[$b['currency']] = $b;
        }
        echo json_encode($balanceMap);
    ?>;

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

    // Load addresses for both drawers
    var urlAction = (function(){ var m = window.location.search.match(/[?&]action=([^&]+)/); return m ? m[1] : null; })();
    fetch('/api/addresses.php').then(function(r){ return r.json(); }).then(function(d){
        if (d.success && d.addresses && d.addresses.length > 0) {
            addressesData = d.addresses;
            var depositSelect = document.getElementById('deposit-currency');
            var withdrawSelect = document.getElementById('withdraw-currency');
            var depositOptions = d.addresses.map(function(a){
                return '<option value="' + a.symbol + '" data-address="' + (a.address || '') + '" data-coin-key="' + (a.coin_key || '') + '">' + (a.display_name || a.symbol) + ' (' + a.symbol + ')</option>';
            }).join('');
            if (depositSelect) depositSelect.innerHTML = depositOptions;
            // Withdraw: only show coins user has balance in
            var withdrawAddresses = d.addresses.filter(function(a){
                var b = userBalances[a.symbol];
                return b && parseFloat(b.amount) > 0;
            });
            var withdrawOptions = withdrawAddresses.length > 0
                ? withdrawAddresses.map(function(a){
                    return '<option value="' + a.symbol + '" data-address="' + (a.address || '') + '" data-coin-key="' + (a.coin_key || '') + '">' + (a.display_name || a.symbol) + ' (' + a.symbol + ') — ' + (userBalances[a.symbol] ? parseFloat(userBalances[a.symbol].amount).toFixed(6) : '0') + '</option>';
                }).join('')
                : '<option value="">No funds available to withdraw</option>';
            if (withdrawSelect) {
                withdrawSelect.innerHTML = withdrawOptions;
                updateWithdrawBalance();
                updateWithdrawUsdValue();
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
                openDrawer(withdrawDrawer);
                history.replaceState({}, '', window.location.pathname);
            }
        }
    });

    // Deposit drawer handlers
    document.getElementById('deposit-btn').addEventListener('click', function(){ depositStep1.classList.remove('hidden'); depositStep2.classList.add('hidden'); document.getElementById('deposit-error').classList.add('hidden'); openDrawer(depositDrawer); });
    document.getElementById('deposit-drawer-close').addEventListener('click', function(){ closeDrawer(depositDrawer); });
    document.getElementById('deposit-close-btn').addEventListener('click', function(){ closeDrawer(depositDrawer); window.location.reload(); });
    if (backdrop) backdrop.addEventListener('click', closeAllDrawers);

    document.getElementById('deposit-submit-btn').addEventListener('click', function(){
        var currency = document.getElementById('deposit-currency').value;
        var amount = parseFloat(document.getElementById('deposit-amount').value) || 0;
        var reference = document.getElementById('deposit-reference').value.trim();
        var errEl = document.getElementById('deposit-error');
        if (!currency || amount <= 0) {
            errEl.textContent = 'Please select a currency and enter a valid amount';
            errEl.classList.remove('hidden');
            return;
        }
        errEl.classList.add('hidden');
        fetch('/api/user/deposit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ currency: currency, amount: amount, reference: reference || null })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                var addr = addressesData.find(function(a){ return a.symbol === currency; });
                document.getElementById('deposit-address-display').value = addr ? addr.address : '';
                document.getElementById('deposit-selected-currency').textContent = currency;
                document.getElementById('deposit-selected-amount').textContent = amount;
                depositStep1.classList.add('hidden');
                depositStep2.classList.remove('hidden');
            } else {
                errEl.textContent = res.error || 'Failed to submit deposit';
                errEl.classList.remove('hidden');
            }
        }).catch(function(){ errEl.textContent = 'Request failed'; errEl.classList.remove('hidden'); });
    });
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
                openDrawer(withdrawDrawer); 
                updateWithdrawBalance();
                updateWithdrawUsdValue();
            }
        });
    }
    if (withdrawCloseBtn && withdrawDrawer) {
        withdrawCloseBtn.addEventListener('click', function(){ closeDrawer(withdrawDrawer); });
    }
    var minWithdrawLimitEl = document.getElementById('withdraw-min-limit');
    var minWithdrawLimitUsd = parseFloat(minWithdrawLimitEl ? minWithdrawLimitEl.textContent : '10') || 10;

    function updateWithdrawBalance() {
        var sel = document.getElementById('withdraw-currency');
        var lbl = document.getElementById('withdraw-currency-label');
        var availEl = document.getElementById('withdraw-available');
        if (!sel || !lbl || !availEl) return;
        var currency = sel.value || '';
        lbl.textContent = currency || '—';
        var balance = userBalances[currency];
        var avail = balance ? parseFloat(balance.amount) : 0;
        availEl.textContent = avail.toFixed(8) + ' ' + (currency || '');
    }

    function getUsdPricePerUnit(symbol, coinKey) {
        var stablecoins = ['USDT','USDC','BUSD','USD','DAI'];
        if (stablecoins.indexOf((symbol || '').toUpperCase()) >= 0) return Promise.resolve(1);
        var coinId = coinKey || (window.BloombitCryptoConfig && window.BloombitCryptoConfig.getCoinIdBySymbol ? window.BloombitCryptoConfig.getCoinIdBySymbol(symbol) : null);
        if (!coinId) return Promise.resolve(null);
        if (!window.BloombitCryptoPrices || !window.BloombitCryptoPrices.fetch) return Promise.resolve(null);
        return window.BloombitCryptoPrices.fetch([coinId]).then(function(prices){ return prices && prices[coinId] ? prices[coinId].usd : null; });
    }

    function getSelectedCoinKey(selectId) {
        var sel = document.getElementById(selectId);
        if (!sel || !sel.options[sel.selectedIndex]) return null;
        return sel.options[sel.selectedIndex].getAttribute('data-coin-key') || null;
    }

    function updateDepositUsdValue() {
        var currency = document.getElementById('deposit-currency').value;
        var amount = parseFloat(document.getElementById('deposit-amount').value) || 0;
        var el = document.getElementById('deposit-usd-value');
        if (!el) return;
        if (!currency || amount <= 0) { el.textContent = '—'; return; }
        el.textContent = '≈ $—';
        var coinKey = getSelectedCoinKey('deposit-currency');
        getUsdPricePerUnit(currency, coinKey).then(function(price){
            if (price != null) el.textContent = '≈ $' + (amount * price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' USD';
            else el.textContent = '—';
        });
    }

    function updateWithdrawUsdValue() {
        var currency = document.getElementById('withdraw-currency').value;
        var amount = parseFloat(document.getElementById('withdraw-amount').value) || 0;
        var el = document.getElementById('withdraw-usd-value');
        if (!el) return;
        if (!currency || amount <= 0) { el.textContent = '—'; return; }
        el.textContent = '≈ $—';
        var coinKey = getSelectedCoinKey('withdraw-currency');
        getUsdPricePerUnit(currency, coinKey).then(function(price){
            if (price != null) el.textContent = '≈ $' + (amount * price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' USD';
            else el.textContent = '—';
        });
    }

    if (document.getElementById('deposit-amount')) {
        document.getElementById('deposit-amount').addEventListener('input', updateDepositUsdValue);
        document.getElementById('deposit-amount').addEventListener('change', updateDepositUsdValue);
    }
    if (document.getElementById('deposit-currency')) {
        document.getElementById('deposit-currency').addEventListener('change', updateDepositUsdValue);
    }
    if (document.getElementById('withdraw-amount')) {
        document.getElementById('withdraw-amount').addEventListener('input', updateWithdrawUsdValue);
        document.getElementById('withdraw-amount').addEventListener('change', updateWithdrawUsdValue);
    }
    if (document.getElementById('withdraw-currency')) {
        document.getElementById('withdraw-currency').addEventListener('change', function(){ updateWithdrawBalance(); updateWithdrawUsdValue(); });
    }
    document.getElementById('withdrawal-form').addEventListener('submit', function(e){
        e.preventDefault();
        var currency = document.getElementById('withdraw-currency').value;
        var amount = parseFloat(document.getElementById('withdraw-amount').value) || 0;
        var address = document.getElementById('withdraw-address').value.trim();
        var msgEl = document.getElementById('withdrawal-message');
        if (!currency || amount <= 0 || !address) {
            msgEl.textContent = 'Please fill all fields';
            msgEl.className = 'text-sm text-red-500';
            msgEl.classList.remove('hidden');
            return;
        }
        msgEl.classList.add('hidden');
        var wdCoinKey = getSelectedCoinKey('withdraw-currency');
        getUsdPricePerUnit(currency, wdCoinKey).then(function(price){
            var amountUsd = price != null ? amount * price : 0;
            if (amountUsd > 0 && amountUsd < minWithdrawLimitUsd) {
                msgEl.textContent = 'Minimum withdrawal is $' + minWithdrawLimitUsd.toFixed(2) + ' USD. Your amount is approx. $' + amountUsd.toFixed(2) + ' USD.';
                msgEl.className = 'text-sm text-red-500';
                msgEl.classList.remove('hidden');
                return;
            }
            fetch('/api/user/withdraw.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currency: currency, amount: amount, address: address })
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
            }).catch(function(){ msgEl.textContent = 'Request failed'; msgEl.className = 'text-sm text-red-500'; msgEl.classList.remove('hidden'); });
        }).catch(function(){
            fetch('/api/user/withdraw.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ currency: currency, amount: amount, address: address })
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
            }).catch(function(){ msgEl.textContent = 'Request failed'; msgEl.className = 'text-sm text-red-500'; msgEl.classList.remove('hidden'); });
        });
    });

    // Crypto prices update
    if (window.BloombitCryptoPrices) {
        var coinIds = ['bitcoin','ethereum','tether'];
        function updateWalletValues(prices) {
            if (!prices) return;
            document.querySelectorAll('.wallet-assets-table tr[data-coin][data-balance]').forEach(function(row) {
                var coinId = row.getAttribute('data-coin');
                var balance = parseFloat(row.getAttribute('data-balance')) || 0;
                var p = prices[coinId];
                var valueEl = row.querySelector('.wallet-value');
                if (valueEl && p && p.usd != null) {
                    valueEl.textContent = '$' + (balance * p.usd).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            });
        }
        window.BloombitCryptoPrices.fetch(coinIds).then(updateWalletValues);
        setInterval(function() {
            window.BloombitCryptoPrices.fetch(coinIds).then(updateWalletValues);
        }, 300000);
    }
});
</script>
</body></html>

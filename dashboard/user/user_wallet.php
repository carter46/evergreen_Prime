<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'wallet';
$siteName = get_site_name();
$walletBalances = [];
$walletTotalUsd = 0;
$walletTransactions = [];
$btcAmount = 0;
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amt = (float) $row['amount'];
        $usd = $amt;
        if (in_array(strtoupper($row['currency']), ['USDT','USDC','BUSD','USD'], true)) $usd = $amt;
        elseif (strtoupper($row['currency']) === 'BTC') { $usd = $amt * 65000; $btcAmount = $amt; }
        elseif (strtoupper($row['currency']) === 'ETH') $usd = $amt * 3500;
        $walletBalances[] = ['currency' => $row['currency'], 'amount' => $amt, 'usd_value' => round($usd, 2)];
        $walletTotalUsd += $usd;
    }
    $stmt = $pdo->prepare('SELECT id, type, amount, currency, status, reference, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 20');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $walletTransactions[] = $row;
    }
} catch (Throwable $e) { }
$coinNames = ['BTC'=>'Bitcoin','ETH'=>'Ethereum','USDT'=>'Tether','USD'=>'US Dollar'];
$coinLogos = ['BTC'=>'https://assets.coingecko.com/coins/images/1/large/bitcoin.png','ETH'=>'https://assets.coingecko.com/coins/images/279/large/ethereum.png','USDT'=>'https://assets.coingecko.com/coins/images/325/large/Tether.png'];
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Wallet &amp; Withdrawals</title>
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
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 overflow-y-auto">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 py-6 sm:py-8">
<div class="grid grid-cols-12 gap-8">
<!-- Left Column: Balances & Assets -->
<div class="col-span-12 lg:col-span-8 space-y-8">
<!-- Main Wallet Card -->
<div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-900 via-slate-800 to-black p-8 text-white shadow-2xl">
<div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
<div class="relative z-10">
<div class="flex justify-between items-start">
<div>
<p class="text-slate-400 text-sm font-medium mb-1">Total Estimated Balance</p>
<h1 class="text-4xl font-bold tracking-tight">$<?php echo number_format($walletTotalUsd, 2); ?> <span class="text-lg font-normal text-slate-400 ml-2">USD</span></h1>
<p class="text-primary mt-2 flex items-center gap-1">
<img class="w-5 h-5" src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png" alt="BTC"/>
                                    <?php echo number_format($btcAmount, 8); ?> BTC
                                </p>
</div>
<div class="flex gap-3">
<button class="bg-primary hover:bg-primary/90 text-black px-6 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-all">
<span class="material-icons text-sm">add</span> Deposit
                                </button>
<button class="bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-lg font-bold flex items-center gap-2 transition-all backdrop-blur-sm">
<span class="material-icons text-sm">file_upload</span> Withdraw
                                </button>
</div>
</div>
<div class="mt-10 grid grid-cols-3 gap-6 border-t border-white/10 pt-8">
<div>
<p class="text-slate-400 text-xs mb-1">24h Change</p>
<p class="text-emerald-400 font-bold flex items-center gap-1">
<span class="material-icons text-xs">trending_up</span> +4.25%
                                </p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Spot Wallet</p>
<p class="font-bold">$89,201.12</p>
</div>
<div>
<p class="text-slate-400 text-xs mb-1">Staking Rewards</p>
<p class="font-bold text-primary">$1,420.50</p>
</div>
</div>
</div>
</div>
<!-- Asset Breakdown -->
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Your Assets</h2>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
<input class="pl-9 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border-none rounded-lg text-sm focus:ring-1 focus:ring-primary w-48" placeholder="Search coin..." type="text"/>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead>
<tr class="text-slate-400 text-[10px] uppercase tracking-wider border-b border-slate-50 dark:border-slate-800">
<th class="px-6 py-4 font-semibold">Asset</th>
<th class="px-6 py-4 font-semibold text-right">Balance</th>
<th class="px-6 py-4 font-semibold text-right">Value (USD)</th>
<th class="px-6 py-4 font-semibold text-right">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50 dark:divide-slate-800 wallet-assets-table">
<?php foreach ($walletBalances as $b):
  $cu = strtoupper($b['currency']);
  $coinId = strtolower($b['currency']) === 'usdt' ? 'tether' : strtolower($b['currency']);
  $logo = $coinLogos[$cu] ?? null;
  $name = $coinNames[$cu] ?? $b['currency'];
?>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors" data-coin="<?php echo $coinId; ?>" data-balance="<?php echo $b['amount']; ?>">
<td class="px-6 py-5">
<div class="flex items-center gap-3">
<?php if ($logo): ?><div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden"><img alt="<?php echo $cu; ?>" class="w-6 h-6 crypto-logo" src="<?php echo htmlspecialchars($logo); ?>"/></div><?php endif; ?>
<div>
<p class="font-bold text-sm"><?php echo htmlspecialchars($name); ?></p>
<p class="text-xs text-slate-500"><?php echo $cu; ?></p>
</div>
</div>
</td>
<td class="px-6 py-5 text-right font-medium text-sm"><?php echo number_format($b['amount'], 8); ?> <?php echo $cu; ?></td>
<td class="px-6 py-5 text-right font-bold text-sm wallet-value" data-coin="<?php echo $coinId; ?>">$<?php echo number_format($b['usd_value'], 2); ?></td>
<td class="px-6 py-5 text-right">
<button class="text-xs font-bold px-4 py-1.5 rounded bg-primary/10 text-primary hover:bg-primary hover:text-black transition-all">TRADE</button>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($walletBalances)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="4">No balances yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<!-- Transaction History Table -->
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<h2 class="text-lg font-bold">Recent History</h2>
<button class="text-primary text-xs font-bold hover:underline">View All</button>
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
<span class="px-2 py-1 <?php echo $tx['status'] === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'; ?> text-[10px] font-bold rounded-full uppercase"><?php echo htmlspecialchars($tx['status']); ?></span>
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
<!-- Right Column: Withdrawal Form & Safety -->
<div class="col-span-12 lg:col-span-4 space-y-6">
<!-- Security Hint -->
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
<!-- Withdrawal Form -->
<div class="bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg sticky top-24 overflow-hidden">
<div class="bg-primary p-4">
<h3 class="font-bold flex items-center gap-2">
<span class="material-icons text-xl">payments</span> Withdraw Funds
                        </h3>
</div>
<form id="withdrawal-form" class="p-6 space-y-6">
<!-- Step 1: Currency Selection -->
<div class="space-y-2">
<label class="text-xs font-bold uppercase tracking-wide text-slate-500">1. Select Currency</label>
<div class="relative">
<select name="currency" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-lg py-3 px-4 appearance-none text-sm font-medium focus:ring-primary focus:border-primary">
<option value="BTC">Bitcoin (BTC)</option>
<option value="ETH">Ethereum (ETH)</option>
<option value="USDT">Tether (USDT)</option>
<option value="SOL">Solana (SOL)</option>
</select>
<span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
</div>
</div>
<!-- Step 2: Address -->
<div class="space-y-2">
<label class="text-xs font-bold uppercase tracking-wide text-slate-500">2. Recipient Address</label>
<div class="relative">
<input name="address" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-lg py-3 px-4 text-sm focus:ring-primary focus:border-primary" placeholder="Paste external wallet address" type="text" required/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-primary font-bold text-xs uppercase hover:underline">Paste</button>
</div>
</div>
<!-- Step 3: Network -->
<div class="space-y-2">
<label class="text-xs font-bold uppercase tracking-wide text-slate-500">3. Network</label>
<div class="grid grid-cols-2 gap-2">
<button class="p-3 border-2 border-primary bg-primary/5 rounded-lg text-left">
<p class="text-[10px] font-bold">BTC</p>
<p class="text-xs font-medium">Bitcoin Mainnet</p>
</button>
<button class="p-3 border border-slate-200 dark:border-slate-800 rounded-lg text-left opacity-50">
<p class="text-[10px] font-bold">BEP20</p>
<p class="text-xs font-medium">BSC Network</p>
</button>
</div>
</div>
<!-- Step 4: Amount -->
<div class="space-y-2">
<div class="flex justify-between items-center">
<label class="text-xs font-bold uppercase tracking-wide text-slate-500">4. Amount</label>
<span class="text-[10px] text-slate-400">Available: 1.45028 BTC</span>
</div>
<div class="relative">
<input name="amount" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-lg py-3 px-4 text-sm font-bold focus:ring-primary focus:border-primary" placeholder="0.00" step="any" type="number" required/>
<div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
<button class="text-[10px] font-bold bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded">MAX</button>
<span class="text-xs font-bold text-slate-400">BTC</span>
</div>
</div>
</div>
<!-- Transaction Summary -->
<div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-4 space-y-2">
<div class="flex justify-between text-xs">
<span class="text-slate-500">Network Fee</span>
<span class="font-medium">0.0002 BTC (~$9.00)</span>
</div>
<div class="flex justify-between text-xs">
<span class="text-slate-500">Service Fee</span>
<span class="font-medium text-emerald-500">FREE</span>
</div>
<div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-between items-end">
<span class="text-xs font-bold">You will receive</span>
<div class="text-right">
<p class="text-lg font-bold leading-none">0.0000 <span class="text-xs text-slate-400">BTC</span></p>
</div>
</div>
</div>
<div id="withdrawal-message" class="text-sm hidden"></div>
<button type="submit" class="w-full bg-primary hover:bg-primary/90 text-black font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                            Withdraw Now <span class="material-icons text-sm">arrow_forward</span>
</button>
</form>
</div>
</div>
<!-- Ad/Banner Area -->
<div class="rounded-xl overflow-hidden relative group">
<div class="bg-slate-900 p-6">
<h5 class="text-primary text-xs font-bold uppercase mb-1">Coming Soon</h5>
<h4 class="text-white font-bold mb-4">Earn up to 12% APY with Bloombit Staking</h4>
<img alt="Staking" class="w-full h-32 object-cover rounded-lg opacity-60 group-hover:opacity-100 transition-opacity" data-alt="Abstract 3D digital shapes with neon yellow lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHwE3RtjthIQUFIpsY37xhY3Ziz9_BghfRaF1Da-SKX8FIY0BxVEGSxFroAwi_MmV4rBeyjRAKJNJZ3RCacRRUXijjAg2qspHfiq9b7r_YWoqj4Uorszlk6d_gNBd-RUMIrQZ7wUkv41PQ8M8fythPyPmQQPGx1pytl-6tw3sJfeOhrh7jQbSQqVq1K_vISLkLjSRIEhhFZtWL6mPf-6OsvzjasHfzYOjNJIhio4U0Z2HEzzQ4psJuR9WbHB6q1-inYAn25jXo7Ys"/>
<button class="w-full mt-4 py-2 border border-white/20 text-white text-xs font-bold rounded hover:bg-white/10 transition-colors">Join Waitlist</button>
</div>
</div>
</div>
</div>
</main>
<footer class="mt-20 border-t border-slate-200 dark:border-slate-800 py-12 bg-white dark:bg-background-dark/20">
<div class="max-w-[1440px] mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
<div class="col-span-1 md:col-span-2">
<div class="flex items-center gap-2 mb-6">
<div class="w-6 h-6 bg-primary rounded flex items-center justify-center">
<span class="material-icons text-white text-sm">bolt</span>
</div>
<span class="text-lg font-bold tracking-tight">BLOOMBIT</span>
</div>
<p class="text-slate-500 text-sm max-w-sm leading-relaxed">
                    Bloombit is a next-generation AI-powered investment platform for digital assets. Trade, invest, and manage your portfolio with professional-grade tools.
                </p>
</div>
<div>
<h5 class="font-bold text-sm mb-4">Platform</h5>
<ul class="text-slate-500 text-sm space-y-2">
<li><a class="hover:text-primary transition-colors" href="#">Spot Trading</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Institutional Services</a></li>
<li><a class="hover:text-primary transition-colors" href="#">API Documentation</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Security</a></li>
</ul>
</div>
<div>
<h5 class="font-bold text-sm mb-4">Support</h5>
<ul class="text-slate-500 text-sm space-y-2">
<li><a class="hover:text-primary transition-colors" href="/help_centre">Help Center</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Submit a Ticket</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Fees Schedule</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Status</a></li>
</ul>
</div>
</div>
<div class="max-w-[1440px] mx-auto px-6 mt-12 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
<p class="text-xs text-slate-400">© 2023 Bloombit International. All rights reserved.</p>
<div class="flex gap-6 text-xs text-slate-400">
<a class="hover:text-primary" href="/legal_centre">Privacy Policy</a>
<a class="hover:text-primary" href="/legal_centre">Terms of Service</a>
<a class="hover:text-primary" href="#">Cookie Settings</a>
</div>
</div>
</footer>
<script src="/js/app.js"></script>
<script>window.BLOOMBIT_API_BASE = '';</script>
<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!window.BloombitCryptoPrices) return;
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
});
</script>
</body></html>

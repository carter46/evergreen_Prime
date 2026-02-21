<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$heroBadge = get_site_setting('hero_badge', 'AI ENGINE V4.0 NOW LIVE');
$homepageYoutubeUrl = get_site_setting('homepage_youtube_url', '');
$homepageEmbedUrl = get_youtube_embed_url($homepageYoutubeUrl);
$homepageModalImage = get_site_setting('homepage_modal_image', '');
$statsAssets = get_site_setting('stats_assets', '$4.2B+');
$statsBots = get_site_setting('stats_bots', '85k+');
$statsUptime = get_site_setting('stats_uptime', '99.9%');
$statsRoi = get_site_setting('stats_roi', '12.4%');
$marketCap = get_site_setting('market_cap', '$2.45T');
$volume24h = get_site_setting('volume_24h', '$84.2B');
$btcDominance = get_site_setting('btc_dominance', '52.4%');
$activeTraders = get_site_setting('active_traders', '12.8M+');
$indexPlans = [];
$orbitCoins = [];
try {
    $pdo = require __DIR__ . '/includes/db.php';
    $stmt = $pdo->query('SELECT name, slug, min_deposit, max_deposit, yield_min, yield_max, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id LIMIT 3');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['features'] = $row['features_json'] ? json_decode($row['features_json'], true) : [];
        $indexPlans[] = $row;
    }
    $stmt = $pdo->query('SELECT symbol, logo FROM coins WHERE enabled = 1 AND logo IS NOT NULL AND logo != "" ORDER BY sort_order, id LIMIT 14');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orbitCoins[] = $row;
    }
} catch (Throwable $e) { }
if (empty($orbitCoins)) {
    $orbitCoins = [
        ['symbol' => 'BTC', 'logo' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png'],
        ['symbol' => 'ETH', 'logo' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png'],
        ['symbol' => 'USDT', 'logo' => 'https://assets.coingecko.com/coins/images/325/large/Tether.png'],
        ['symbol' => 'BNB', 'logo' => 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png'],
        ['symbol' => 'SOL', 'logo' => 'https://assets.coingecko.com/coins/images/4128/large/solana.png'],
        ['symbol' => 'XRP', 'logo' => 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png'],
        ['symbol' => 'ADA', 'logo' => 'https://assets.coingecko.com/coins/images/975/large/cardano.png'],
        ['symbol' => 'DOGE', 'logo' => 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png'],
    ];
}
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> - AI Crypto Trading</title>
<?php output_favicon_tags(); ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#ffc105",
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
        .neural-bg {
            background-image: radial-gradient(circle at 2px 2px, #ffc10515 1px, transparent 0);
            background-size: 40px 40px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 193, 5, 0.2);
        }
        .custom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #ffc105;
            cursor: pointer;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 10px rgba(255, 193, 5, 0.4);
        }
    </style>
<style>
    @keyframes infinite-scroll {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }
    .animate-infinite-scroll {
        animation: infinite-scroll 25s linear infinite;
        width: max-content;
    }
</style></head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-800 dark:text-slate-100 transition-colors duration-300 overflow-x-hidden">
<?php $currentPage = 'home'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<!-- Hero Section -->
<section class="relative overflow-hidden pt-16 sm:pt-20 pb-24 sm:pb-32 neural-bg px-4 sm:px-6">
<div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-8 sm:gap-12 items-center">
<div>
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-sm font-bold mb-6">
<span class="relative flex h-2 w-2">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
</span>
                    <?php echo htmlspecialchars($heroBadge); ?>
                </div>
<h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-[1.1] mb-6 sm:mb-8">
                    Smarter Crypto Investing Powered by <span class="text-primary">Advanced AI</span>
</h1>
<p class="text-base sm:text-lg lg:text-xl text-slate-500 dark:text-slate-400 mb-8 sm:mb-10 max-w-lg leading-relaxed">
                    Automate your wealth with institutional-grade machine learning algorithms. Deploy sophisticated bots that trade 24/7 while you sleep.
                </p>
<div class="flex flex-col sm:flex-row gap-4">
<a href="/register" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 bg-primary text-black font-bold text-base sm:text-lg rounded-lg shadow-xl shadow-primary/30 hover:-translate-y-1 transition-all min-h-[44px] text-center">Get Started</a>
<a href="/about_us" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 bg-white dark:bg-slate-800 border border-primary/20 font-bold text-base sm:text-lg rounded-lg hover:bg-slate-50 transition-all flex items-center justify-center gap-2 min-h-[44px]">
<span class="material-icons">info</span> Learn More
                    </a>
</div>
</div>
<?php
$heroPlaceholderImg = 'https://lh3.googleusercontent.com/aida-public/AB6AXuAERFUp6io6mfsuU1xT4reC0MNfdbS-TG3S4WGzJcCZSgr20oMTW8dUmGTeXv13y8kWRb5oPvDKI71MY9ZPevM-uGRz6fdP5rwt94fPuFxrKeaT7jUgMJ9Vbc7eaMqT5j76CADhsg_voWOtIyJCJYcyKMSY_fVn5C2XOdVDDAxc9__oxwyA4PGAsGCjAAoYpnKqfXpEzSY8_0IuPOPCBU6Rn8GNYiSkYg173iJeDY9itvWtl5KgpyHI0p4yDw2MBFoiRPPEhihDvU4';
?>
<div class="relative">
<div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/20 blur-[120px] rounded-full"></div>
<div class="relative z-10 w-full rounded-2xl shadow-2xl border-4 border-white/50 dark:border-slate-800/50 overflow-hidden bg-slate-900/50 h-[350px] sm:h-[400px] md:h-[450px] lg:h-[500px]">
<?php if ($homepageEmbedUrl): ?>
<img alt="" class="hero-video-poster absolute inset-0 w-full h-full object-cover rounded-2xl" src="<?php echo htmlspecialchars($heroPlaceholderImg); ?>" aria-hidden="true"/>
<iframe class="absolute inset-0 w-full h-full rounded-2xl" src="<?php echo htmlspecialchars($homepageEmbedUrl); ?>?rel=0" title="<?php echo htmlspecialchars($siteName); ?> demo" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<?php else: ?>
<img alt="AI Trading Interface" class="absolute inset-0 w-full h-full object-cover rounded-2xl" src="<?php echo htmlspecialchars($heroPlaceholderImg); ?>"/>
<?php endif; ?>
</div>
</div>
</div>
</section>
<!-- Stats Section -->
<section class="bg-primary py-12">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
<div class="text-center">
<div class="text-4xl font-bold text-black"><?php echo htmlspecialchars($statsAssets); ?></div>
<div class="text-black/70 font-medium">Assets Managed</div>
</div>
<div class="text-center border-l border-black/10">
<div class="text-4xl font-bold text-black"><?php echo htmlspecialchars($statsBots); ?></div>
<div class="text-black/70 font-medium">Active AI Bots</div>
</div>
<div class="text-center border-l border-black/10">
<div class="text-4xl font-bold text-black"><?php echo htmlspecialchars($statsUptime); ?></div>
<div class="text-black/70 font-medium">Uptime Guarantee</div>
</div>
<div class="text-center border-l border-black/10">
<div class="text-4xl font-bold text-black"><?php echo htmlspecialchars($statsRoi); ?></div>
<div class="text-black/70 font-medium">Avg. Monthly ROI</div>
</div>
</div>
</section><section class="bg-white dark:bg-background-dark border-y border-primary/10 py-6">
<div class="max-w-7xl mx-auto px-6">
<div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
<div class="flex flex-col items-center border-r border-slate-100 dark:border-slate-800 last:border-0">
<span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Market Cap</span>
<span class="text-xl font-bold"><?php echo htmlspecialchars($marketCap); ?> <span class="text-green-500 text-xs">+1.2%</span></span>
</div>
<div class="flex flex-col items-center border-r border-slate-100 dark:border-slate-800 lg:last:border-0">
<span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">24h Volume</span>
<span class="text-xl font-bold"><?php echo htmlspecialchars($volume24h); ?></span>
</div>
<div class="flex flex-col items-center border-r border-slate-100 dark:border-slate-800 last:border-0">
<span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">BTC Dominance</span>
<span class="text-xl font-bold"><?php echo htmlspecialchars($btcDominance); ?></span>
</div>
<div class="flex flex-col items-center last:border-0">
<span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Active Traders</span>
<span class="text-xl font-bold"><?php echo htmlspecialchars($activeTraders); ?></span>
</div>
</div>
</div>
</section><section class="py-24 overflow-hidden relative">
<div class="absolute inset-0 pointer-events-none opacity-5">
<span class="material-icons absolute top-10 left-10 text-6xl">currency_bitcoin</span>
<span class="material-icons absolute bottom-10 right-10 text-6xl">token</span>
</div>
<div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
<div>
<h2 class="text-4xl font-bold mb-6">Live Crypto Market Overview</h2>
<p class="text-slate-500 text-lg mb-8">Stay ahead of the curve with real-time price feeds and market sentiment analysis powered by our proprietary AI engine.</p>
<a href="/login" class="px-8 py-3 bg-primary text-black font-bold rounded-lg hover:shadow-lg transition-all inline-block">View Full Market</a>
</div>
<div class="relative bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-primary/10 overflow-hidden">
<div class="flex gap-6 animate-infinite-scroll whitespace-nowrap crypto-ticker">
<div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-2 rounded-lg shadow-sm crypto-ticker-item" data-coin="bitcoin">
<img class="crypto-logo w-6 h-6 rounded-full" src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png" alt="Bitcoin"/>
<span class="font-bold">BTC</span>
<span class="font-medium crypto-price">--</span>
<span class="text-sm crypto-change text-green-500">--</span>
</div>
<div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-2 rounded-lg shadow-sm crypto-ticker-item" data-coin="ethereum">
<img class="crypto-logo w-6 h-6 rounded-full" src="https://assets.coingecko.com/coins/images/279/large/ethereum.png" alt="Ethereum"/>
<span class="font-bold">ETH</span>
<span class="font-medium crypto-price">--</span>
<span class="text-sm crypto-change text-red-500">--</span>
</div>
<div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-2 rounded-lg shadow-sm crypto-ticker-item" data-coin="binancecoin">
<img class="crypto-logo w-6 h-6 rounded-full" src="https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png" alt="BNB"/>
<span class="font-bold">BNB</span>
<span class="font-medium crypto-price">--</span>
<span class="text-sm crypto-change text-green-500">--</span>
</div>
</div>
</div>
</div>
</section>
<!-- How It Works -->
<section class="py-32">
<div class="max-w-7xl mx-auto px-6 text-center mb-20">
<h2 class="text-4xl font-bold mb-4">5 Steps to Financial Freedom</h2>
<p class="text-slate-500 max-w-2xl mx-auto">Our streamlined process makes institutional-grade trading accessible to everyone.</p>
</div>
<div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-5 gap-8">
<div class="relative group">
<div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-black">person_add</span>
</div>
<div class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">Step 01</div>
<h3 class="text-xl font-bold mb-3">Register</h3>
<p class="text-sm text-slate-500">Create your free account in seconds.</p>
</div>
<div class="relative group">
<div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-black">verified_user</span>
</div>
<div class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">Step 02</div>
<h3 class="text-xl font-bold mb-3">Verify Account</h3>
<p class="text-sm text-slate-500">Confirm your email with the code we send.</p>
</div>
<div class="relative group">
<div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-black">account_balance_wallet</span>
</div>
<div class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">Step 03</div>
<h3 class="text-xl font-bold mb-3">Deposit</h3>
<p class="text-sm text-slate-500">Fund your account securely.</p>
</div>
<div class="relative group">
<div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-black">insights</span>
</div>
<div class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">Step 04</div>
<h3 class="text-xl font-bold mb-3">Choose Plan</h3>
<p class="text-sm text-slate-500">Pick the strategy that fits your goals.</p>
</div>
<div class="relative group">
<div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-black">payments</span>
</div>
<div class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">Step 05</div>
<h3 class="text-xl font-bold mb-3">Subscribe</h3>
<p class="text-sm text-slate-500">Activate your plan and start earning.</p>
</div>
</div>
</section><section class="py-32 bg-slate-50 dark:bg-slate-900/30">
<div class="max-w-7xl mx-auto px-6">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold mb-4">Top 10 Most Popular Coins</h2>
<p class="text-slate-500">Real-time data for the most traded assets on our platform.</p>
</div>
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-100 dark:border-slate-700">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse crypto-table">
<thead>
<tr class="bg-slate-50 dark:bg-slate-900/50">
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase">Rank</th>
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase">Coin</th>
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase">Price</th>
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase">24h Change</th>
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase hidden sm:table-cell">Market Cap</th>
<th class="p-4 sm:p-6 font-bold text-sm text-slate-400 uppercase hidden md:table-cell">Last 7 Days</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-700">
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group" data-coin="bitcoin">
<td class="p-4 sm:p-6 font-bold">1</td>
<td class="p-4 sm:p-6">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden"><img class="crypto-logo w-6 h-6" src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png" alt="Bitcoin"/></div>
<div><div class="font-bold">Bitcoin</div><div class="text-xs text-slate-400">BTC</div></div>
</div>
</td>
<td class="p-4 sm:p-6 font-medium font-mono crypto-price">--</td>
<td class="p-4 sm:p-6 crypto-change text-green-500">--</td>
<td class="p-4 sm:p-6 font-medium font-mono hidden sm:table-cell">$1.26T</td>
<td class="p-4 sm:p-6 hidden md:table-cell">
<div class="w-24 h-8 bg-green-500/10 rounded overflow-hidden">
<div class="h-full w-2/3 bg-green-500/20 animate-pulse"></div>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-coin="ethereum">
<td class="p-4 sm:p-6 font-bold">2</td>
<td class="p-4 sm:p-6">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden"><img class="crypto-logo w-6 h-6" src="https://assets.coingecko.com/coins/images/279/large/ethereum.png" alt="Ethereum"/></div>
<div><div class="font-bold">Ethereum</div><div class="text-xs text-slate-400">ETH</div></div>
</div>
</td>
<td class="p-4 sm:p-6 font-medium font-mono crypto-price">--</td>
<td class="p-4 sm:p-6 crypto-change text-red-500">--</td>
<td class="p-4 sm:p-6 font-medium font-mono hidden sm:table-cell">$411.2B</td>
<td class="p-4 sm:p-6 hidden md:table-cell">
<div class="w-24 h-8 bg-red-500/10 rounded overflow-hidden">
<div class="h-full w-1/2 bg-red-500/20 animate-pulse"></div>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-coin="tether">
<td class="p-4 sm:p-6 font-bold">3</td>
<td class="p-4 sm:p-6">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden"><img class="crypto-logo w-6 h-6" src="https://assets.coingecko.com/coins/images/325/large/Tether.png" alt="Tether"/></div>
<div><div class="font-bold">Tether</div><div class="text-xs text-slate-400">USDT</div></div>
</div>
</td>
<td class="p-4 sm:p-6 font-medium font-mono crypto-price">--</td>
<td class="p-4 sm:p-6 crypto-change text-green-500">--</td>
<td class="p-4 sm:p-6 font-medium font-mono hidden sm:table-cell">--</td>
<td class="p-4 sm:p-6 hidden md:table-cell"><div class="w-24 h-8 bg-slate-100 dark:bg-slate-800 rounded"></div></td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-coin="binancecoin">
<td class="p-4 sm:p-6 font-bold">4</td>
<td class="p-4 sm:p-6">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden"><img class="crypto-logo w-6 h-6" src="https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png" alt="BNB"/></div>
<div><div class="font-bold">BNB</div><div class="text-xs text-slate-400">BNB</div></div>
</div>
</td>
<td class="p-4 sm:p-6 font-medium font-mono crypto-price">--</td>
<td class="p-4 sm:p-6 crypto-change text-green-500">--</td>
<td class="p-4 sm:p-6 font-medium font-mono hidden sm:table-cell">--</td>
<td class="p-4 sm:p-6 hidden md:table-cell"><div class="w-24 h-8 bg-slate-100 dark:bg-slate-800 rounded"></div></td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors" data-coin="solana">
<td class="p-4 sm:p-6 font-bold">5</td>
<td class="p-4 sm:p-6">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full flex items-center justify-center overflow-hidden"><img class="crypto-logo w-6 h-6" src="https://assets.coingecko.com/coins/images/4128/large/solana.png" alt="Solana"/></div>
<div><div class="font-bold">Solana</div><div class="text-xs text-slate-400">SOL</div></div>
</div>
</td>
<td class="p-4 sm:p-6 font-medium font-mono crypto-price">--</td>
<td class="p-4 sm:p-6 crypto-change text-green-500">--</td>
<td class="p-4 sm:p-6 font-medium font-mono hidden sm:table-cell">--</td>
<td class="p-4 sm:p-6 hidden md:table-cell"><div class="w-24 h-8 bg-slate-100 dark:bg-slate-800 rounded"></div></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</section>
<!-- AI Tech Section -->
<section class="bg-slate-900 py-32 overflow-hidden relative">
<div class="absolute inset-0 opacity-20">
<div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/30 blur-[150px] rounded-full"></div>
<div class="absolute bottom-0 right-1/4 w-96 h-96 bg-primary/20 blur-[150px] rounded-full"></div>
</div>
<div class="max-w-7xl mx-auto px-6 relative z-10">
<div class="grid lg:grid-cols-2 gap-20 items-center">
<div>
<h2 class="text-4xl font-bold text-white mb-6">Neural Engine Architecture</h2>
<p class="text-slate-400 text-lg mb-8">Our bots use Deep Reinforcement Learning to adapt to market volatility in milliseconds, outperforming human traders and traditional logic-based scripts.</p>
<ul class="space-y-4 mb-10">
<li class="flex items-start gap-4 text-slate-300">
<span class="material-icons text-primary">check_circle</span>
<span>Predictive sentiment analysis from 40+ social signals</span>
</li>
<li class="flex items-start gap-4 text-slate-300">
<span class="material-icons text-primary">check_circle</span>
<span>High-frequency execution with zero-latency APIs</span>
</li>
<li class="flex items-start gap-4 text-slate-300">
<span class="material-icons text-primary">check_circle</span>
<span>Dynamic risk-adjusted position sizing</span>
</li>
</ul>
<button class="text-primary font-bold flex items-center gap-2 hover:translate-x-2 transition-transform">
                        Explore Our Documentation <span class="material-icons">arrow_forward</span>
</button>
</div>
<div class="grid gap-6">
<div class="glass-card p-8 rounded-xl bg-white/5 border-white/10">
<div class="flex items-center gap-4 mb-4">
<div class="p-3 bg-primary/20 rounded-lg">
<span class="material-icons text-primary">psychology</span>
</div>
<h3 class="text-xl font-bold text-white">Sentiment Core</h3>
</div>
<p class="text-slate-400 text-sm">Analyzes massive datasets from Twitter, Reddit, and News in real-time to predict market moves before they happen.</p>
</div>
<div class="glass-card p-8 rounded-xl bg-white/5 border-white/10 ml-8">
<div class="flex items-center gap-4 mb-4">
<div class="p-3 bg-primary/20 rounded-lg">
<span class="material-icons text-primary">speed</span>
</div>
<h3 class="text-xl font-bold text-white">Execution Node</h3>
</div>
<p class="text-slate-400 text-sm">Low-latency proprietary infrastructure ensures your orders are filled at the best possible price across all major CEXs.</p>
</div>
<div class="glass-card p-8 rounded-xl bg-white/5 border-white/10">
<div class="flex items-center gap-4 mb-4">
<div class="p-3 bg-primary/20 rounded-lg">
<span class="material-icons text-primary">shield</span>
</div>
<h3 class="text-xl font-bold text-white">Risk Matrix</h3>
</div>
<p class="text-slate-400 text-sm">Automatically adjusts stop-losses and take-profits based on current market volatility and asset correlation.</p>
</div>
</div>
</div>
</div>
</section><section class="py-32 bg-slate-900 border-t border-white/5 overflow-hidden">
<div class="max-w-7xl mx-auto px-6 text-center">
<h2 class="text-3xl font-bold text-white mb-20">AI Market Intelligence Orbit</h2>
<div class="relative flex items-center justify-center h-[500px]">
<?php
$ring1 = array_slice($orbitCoins, 0, 6);
$ring2 = array_slice($orbitCoins, 6, 4);
?>
<!-- Orbit rings: real coin logos from DB or fallback -->
<div class="absolute w-[450px] h-[450px] border border-primary/20 rounded-full animate-[spin_20s_linear_infinite]">
<?php foreach ($ring1 as $i => $c): $angle = 2 * M_PI * $i / count($ring1) - M_PI / 2; $x = 50 + 50 * cos($angle); $y = 50 + 50 * sin($angle); ?>
<span class="absolute w-10 h-10 rounded-full overflow-hidden bg-slate-800 border-2 border-primary/30 shadow-lg flex items-center justify-center" style="left:<?php echo $x; ?>%; top:<?php echo $y; ?>%; transform:translate(-50%,-50%);"><img src="<?php echo htmlspecialchars($c['logo']); ?>" alt="<?php echo htmlspecialchars($c['symbol']); ?>" class="w-7 h-7 object-contain"/></span>
<?php endforeach; ?>
</div>
<div class="absolute w-[300px] h-[300px] border border-primary/30 rounded-full animate-[spin_15s_linear_infinite_reverse]">
<?php foreach ($ring2 as $i => $c): $angle = 2 * M_PI * $i / max(1, count($ring2)) - M_PI / 2; $x = 50 + 50 * cos($angle); $y = 50 + 50 * sin($angle); ?>
<span class="absolute w-9 h-9 rounded-full overflow-hidden bg-slate-800 border-2 border-primary/30 shadow-lg flex items-center justify-center" style="left:<?php echo $x; ?>%; top:<?php echo $y; ?>%; transform:translate(-50%,-50%);"><img src="<?php echo htmlspecialchars($c['logo']); ?>" alt="<?php echo htmlspecialchars($c['symbol']); ?>" class="w-6 h-6 object-contain"/></span>
<?php endforeach; ?>
</div>
<div class="absolute w-[150px] h-[150px] border border-primary/40 rounded-full animate-[spin_10s_linear_infinite]">
<?php foreach (array_slice($orbitCoins, 10, 4) as $i => $c): $n = min(4, count($orbitCoins) - 10); if ($n < 1) break; $angle = 2 * M_PI * $i / $n - M_PI / 2; $x = 50 + 50 * cos($angle); $y = 50 + 50 * sin($angle); ?>
<span class="absolute w-8 h-8 rounded-full overflow-hidden bg-slate-800 border-2 border-primary/40 shadow flex items-center justify-center" style="left:<?php echo $x; ?>%; top:<?php echo $y; ?>%; transform:translate(-50%,-50%);"><img src="<?php echo htmlspecialchars($c['logo']); ?>" alt="<?php echo htmlspecialchars($c['symbol']); ?>" class="w-5 h-5 object-contain"/></span>
<?php endforeach; ?>
</div>
<!-- Central Core -->
<div class="relative z-10 w-40 h-40 bg-primary rounded-full flex items-center justify-center shadow-[0_0_60px_rgba(255,193,7,0.4)]">
<div class="text-black text-center">
<div class="font-black leading-tight text-sm">AI CORE</div>
<div class="text-[10px] font-bold opacity-80 uppercase tracking-tighter">Intelligence<br/>Engine</div>
</div>
</div>
</div>
</div>
</section>
<!-- Investment Calculator -->
<section class="py-32 bg-background-light dark:bg-background-dark">
<div class="max-w-4xl mx-auto px-6">
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-primary/10">
<div class="p-10 border-b border-slate-100 dark:border-slate-800">
<h2 class="text-3xl font-bold text-center mb-2">Investment Calculator</h2>
<p class="text-slate-500 text-center">Estimate your potential returns based on historical performance data.</p>
</div>
<div class="p-10 grid md:grid-cols-2 gap-12">
<div class="space-y-10">
<div>
<label class="block font-bold mb-2">Plan</label>
<select id="calc-plan" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-medium">
<option value="">Loading plans…</option>
</select>
</div>
<div>
<div class="flex justify-between mb-4 font-bold">
<span>Investment Amount</span>
<span class="text-primary text-sm" id="calc-amount-limits">Any amount</span>
</div>
<input id="calc-amount" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-medium" type="number" step="any" min="0" placeholder="e.g. 1000" value="1000"/>
<div class="text-xs text-slate-400 mt-2">
<span id="calc-amount-range-text">Enter any amount. Plan range shown for reference.</span>
</div>
</div>
<div>
<div class="flex justify-between mb-4 font-bold">
<span>Duration (days)</span>
<span class="text-primary text-sm" id="calc-duration-limits">Any duration (days)</span>
</div>
<input id="calc-duration" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 font-medium" type="number" min="1" step="1" placeholder="e.g. 30" value="30"/>
<div class="text-xs text-slate-400 mt-2">
<span id="calc-duration-range-text">Enter number of days. Plan duration shown for reference.</span>
</div>
</div>
</div>
<div class="bg-primary/5 rounded-2xl p-8 flex flex-col justify-center items-center border border-primary/20">
<div class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-2">Projected Return</div>
<div class="text-2xl font-bold text-slate-700 dark:text-slate-200 mb-1" id="calc-daily-profit">$0/day</div>
<div class="text-xs text-slate-500 dark:text-slate-400 mb-3" id="calc-total-profit-label">Total profit over 0 days: $0</div>
<div class="text-5xl font-bold text-black dark:text-white mb-1" id="calc-projected">$0</div>
<div class="text-primary font-bold" id="calc-profit">+0% Profit</div>
<div class="mt-8 w-full">
<a href="/register" class="block w-full py-4 bg-black text-white font-bold rounded-lg hover:bg-slate-800 transition-all text-center">Open Account Now</a>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Plans Preview -->
<section class="py-32 bg-white dark:bg-slate-900/50">
<div class="max-w-7xl mx-auto px-6 text-center mb-16">
<h2 class="text-4xl font-bold mb-4">Choose Your Strategy</h2>
<p class="text-slate-500">Flexible plans tailored to your investment goals.</p>
</div>
<div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-3 gap-8">
<?php
$planIndex = 0;
foreach ($indexPlans as $p):
    $popular = ($planIndex === 1);
    $priceLabel = '$' . number_format((float)$p['min_deposit']);
    if (!empty($p['max_deposit'])) $priceLabel .= ' - $' . number_format((float)$p['max_deposit']);
    else $priceLabel .= '+';
?>
<div class="bg-white dark:bg-slate-800 p-10 rounded-2xl shadow-lg border <?php echo $popular ? 'border-2 border-primary shadow-2xl' : 'border-slate-100 dark:border-slate-700'; ?> hover:-translate-y-2 transition-transform duration-300 relative">
<?php if ($popular): ?><div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-primary text-black text-xs font-bold px-4 py-1 rounded-full">MOST POPULAR</div><?php endif; ?>
<div class="text-primary font-bold mb-2"><?php echo htmlspecialchars($p['name']); ?></div>
<div class="text-4xl font-bold mb-6"><?php echo $priceLabel; ?><span class="text-lg text-slate-400 font-normal"> deposit</span></div>
<p class="text-slate-500 mb-8 text-sm"><?php echo number_format((float)($p['yield_min'] ?? 0), 1); ?>% daily ROI</p>
<ul class="space-y-4 mb-10 text-left">
<?php foreach (array_slice($p['features'] ?? [], 0, 4) as $f): ?>
<li class="flex items-center gap-3 text-sm">
<span class="material-icons text-primary text-lg">check</span>
<?php echo htmlspecialchars($f); ?>
</li>
<?php endforeach; ?>
</ul>
<a href="/register" class="block w-full py-3 <?php echo $popular ? 'bg-primary text-black' : 'border-2 border-primary text-black dark:text-white'; ?> font-bold rounded-lg hover:bg-primary hover:text-black text-center transition-all"><?php echo $popular ? 'Choose ' . htmlspecialchars($p['name']) : 'Get Started'; ?></a>
</div>
<?php $planIndex++; endforeach; ?>
<?php if (empty($indexPlans)): ?>
<div class="col-span-3 text-center py-12 text-slate-500">No plans available. <a href="/plans" class="text-primary font-bold hover:underline">View plans</a></div>
<?php endif; ?>
</div>
</section>
<!-- TradingView News -->
<section class="py-32 bg-white dark:bg-slate-900/30">
<div class="max-w-7xl mx-auto px-6">
<h2 class="text-4xl font-bold mb-4">Market Insights</h2>
<p class="text-slate-500 mb-8">Top stories from TradingView.</p>
<div class="tradingview-widget-container rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 shadow-sm" style="height: 680px;">
<div class="tradingview-widget-container__widget"></div>
<div class="tradingview-widget-copyright text-xs text-slate-400 mt-2"><a href="https://www.tradingview.com/news/top-providers/tradingview/" rel="noopener nofollow" target="_blank"><span class="blue-text">Top stories</span></a><span class="trademark"> by TradingView</span></div>
<script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-timeline.js" async>
{"displayMode":"regular","feedMode":"all_symbols","colorTheme":"light","isTransparent":false,"locale":"en","width":"100%","height":"100%"}
</script>
</div>
</div>
</section>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<!-- Modal -->
<div id="homepage-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
<div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl flex items-center justify-center max-w-[95vw] max-h-[95vh] p-4">
<button id="homepage-modal-close" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" aria-label="Close">
<span class="material-icons text-slate-600 dark:text-slate-300">close</span>
</button>
<?php if (!empty($homepageModalImage)): ?>
<img src="<?php echo htmlspecialchars($homepageModalImage); ?>" alt="Certificate" class="max-w-[90vw] max-h-[90vh] w-auto h-auto object-contain rounded-lg"/>
<?php else: ?>
<div class="p-12 text-center min-w-[300px]">
<p class="text-slate-500 dark:text-slate-400 text-lg">No image uploaded yet.</p>
<p class="text-slate-400 dark:text-slate-500 text-sm mt-2">Upload an image in Admin Settings → Branding → Homepage Floating Modal Image</p>
</div>
<?php endif; ?>
</div>
</div>
<script>
(function(){
  var btn = document.getElementById('footer-certificate-btn');
  var modal = document.getElementById('homepage-modal');
  var close = document.getElementById('homepage-modal-close');
  if (!btn || !modal || !close) return;
  
  // Modal functionality
  btn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow = 'hidden'; });
  close.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; });
  modal.addEventListener('click', function(e){ if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !modal.classList.contains('hidden')) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
})();
</script>
<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
(function(){
  var plans = [];
  var amountEl = document.getElementById('calc-amount');
  var durationEl = document.getElementById('calc-duration');
  var planSelect = document.getElementById('calc-plan');
  if (!amountEl || !durationEl) return;

  function getSelectedPlan() {
    if (!plans.length) return null;
    if (planSelect && planSelect.value) {
      for (var i = 0; i < plans.length; i++) {
        if (String(plans[i].id) === planSelect.value) return plans[i];
      }
    }
    return plans[0];
  }

  function syncInputsToPlan(plan) {
    if (!plan) return;
    var minAmount = Number(plan.min || 0);
    var maxAmount = plan.max === null ? null : Number(plan.max);
    var minDays = Math.max(1, parseInt(plan.min_duration_days || plan.duration_days, 10) || 1);
    var maxDays = Math.max(minDays, parseInt(plan.max_duration_days || plan.duration_days, 10) || minDays);

    amountEl.removeAttribute('min');
    amountEl.removeAttribute('max');
    amountEl.setAttribute('step', 'any');
    document.getElementById('calc-amount-limits').textContent = 'Any amount';
    document.getElementById('calc-amount-range-text').textContent = maxAmount != null ? ('Plan range: $' + minAmount.toLocaleString() + ' to $' + maxAmount.toLocaleString()) : ('Plan min: $' + minAmount.toLocaleString() + '+');

    durationEl.min = '1';
    durationEl.removeAttribute('max');
    durationEl.step = '1';
    document.getElementById('calc-duration-limits').textContent = 'Any duration (days)';
    document.getElementById('calc-duration-range-text').textContent = 'Plan duration: ' + minDays + ' to ' + maxDays + ' days';
  }

  function updateCalc() {
    var plan = getSelectedPlan();
    if (plan) syncInputsToPlan(plan);
    var amount = Math.max(0, parseFloat(amountEl.value) || 0);
    var days = Math.max(1, parseInt(durationEl.value, 10) || 1);
    var dailyProfit = 0;
    var totalProfit = 0;
    var projected = amount;
    var profitPct = 0;
    if (plan && amount > 0) {
      var dailyRoiPct = (plan.yield_min + plan.yield_max) / 2;
      dailyProfit = amount * (dailyRoiPct / 100);
      totalProfit = dailyProfit * days;
      projected = amount + totalProfit;
      profitPct = ((totalProfit / amount) * 100).toFixed(1);
    }
    var dailyEl = document.getElementById('calc-daily-profit');
    var totalLabelEl = document.getElementById('calc-total-profit-label');
    if (dailyEl) dailyEl.textContent = '$' + (dailyProfit % 1 ? dailyProfit.toFixed(2) : Math.round(dailyProfit)).toLocaleString() + '/day';
    if (totalLabelEl) totalLabelEl.textContent = 'Total profit over ' + days + ' days: $' + (totalProfit % 1 ? totalProfit.toFixed(2) : Math.round(totalProfit)).toLocaleString();
    document.getElementById('calc-projected').textContent = '$' + (projected % 1 ? projected.toFixed(2) : Math.round(projected)).toLocaleString();
    document.getElementById('calc-profit').textContent = '+' + profitPct + '% Profit';
  }

  fetch('/api/plans/list.php').then(function(r){ return r.json(); }).then(function(res){
    if (res.success && res.data && res.data.length) {
      plans = res.data;
      if (planSelect) {
        planSelect.innerHTML = '';
        plans.forEach(function(p) {
          var opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name + ' ($' + p.min.toLocaleString() + (p.max ? ' - $' + p.max.toLocaleString() : '+') + ')';
          planSelect.appendChild(opt);
        });
        if (plans[0]) planSelect.value = String(plans[0].id);
      }
      syncInputsToPlan(getSelectedPlan());
    }
    updateCalc();
  }).catch(function(){ updateCalc(); });

  amountEl.addEventListener('input', updateCalc);
  durationEl.addEventListener('input', updateCalc);
  if (planSelect) planSelect.addEventListener('change', updateCalc);
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.BloombitCryptoPrices) {
        window.BloombitCryptoPrices.init(['bitcoin','ethereum','tether','binancecoin','solana'], {
            tickerSelector: '.crypto-ticker',
            tableSelector: '.crypto-table',
            refreshInterval: 120000
        });
    }
});
</script>
</body></html>
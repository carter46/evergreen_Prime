<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = $siteName . ' | Professional Trading & Institutional Asset Management';
$heroBadge = get_site_setting('hero_badge', 'Institutional Grade Security');
$statsVolume = get_site_setting('stats_assets', '$42B+');
$statsInvestors = get_site_setting('stats_bots', '1.2M+');
$statsUptime = get_site_setting('stats_uptime', '100%');
$statsSupport = get_site_setting('stats_roi', '24/7');

$heroImg = '/uploads/images/evergren_cardphone.jpg';
$tradingImg = 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8t20RVlMgaWNZbegnNfZpo05H-DsP08ZNR4eFxdn3auxEcHots-xhbBTK07-8o32e2aaaT-C6e8PsfJ8bl5DIPD3VvZTdgLij0I7MfF4t7Ik4sDfIirKnui2RGDr3o8g-6wwbZJbS28dKv4DD_E_eJT1QIFaskjv1mUp7vP5H_KHim-YAIMG7ZrHHh9lZb1JEycSYChDJADhS-kejRKmyKhGV44hmtxr8Hd-wLB7M7YS3aWJIYv30';
$investImg = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCvI2LEc7CfuQSjFVUzej1kXzCvAscx-20YWah6xb8oVsGM2Cl_Z7XQHwsVN_tk5GtZ_CUKGyyZwZj8ICGSNDkH-4w_g9NuMUgqemomLZDLYUy6uHnyn149effZBAOiV0UaVG8Clb1ZV0d97bgGjtCykfkjgd208kb73yJUqktoFuSHDoVPMOYiV8IatXno6JBEL1rkm7LQ-P8p8bJpxvu4laQYixbBWrPH4nnSx9uOHc4jzP6-HK8e';
$mobileImg = 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkTWR7DptUNdQXaCi5hUfal7sGJFohqmDJOScxp9_cPh6NOKifszH5a48O1ze0FLQxCosT-9xC54R_9kuPaMowJzm39CyOhSSo8VqFgfo9eES7FpOB5imMhhQ437f2Xr8G7FGgFfDMfHVaQ_FhLR4nhv2JyaWjfU2E1e4juY2sqQkyWa3yaIZLumrxp_KMx_-0rfr6-S8f2sB8F3g2WDMy8SlYAL345G0vNGvEFSmgaATja7hSBQ8_';
$heatmapImg = 'https://lh3.googleusercontent.com/aida-public/AB6AXuBiGujiVOX1V-f02sUWLcpITS1XeukxSsCUIpIRtpY0GrFwOFSW5F1zxlHGQYX7Gv6dpJB3KcahYE1j6yBziA5GzoLBV4YVJUkUbdEDVWcZvrZU4tx0i-MVPoR14W_1Zj6pfh0gnKPexhD8aSJoCJZpkDBh_a65UpYkHSboMo_r1JEPrQrek-azLwOWxgUFgdXTT9BKkoHOZbbIgFIUfW-UyQ4rxjp9cU_OnPIS5cb2l97eUILk8mEB';
$eduBeginner = 'https://lh3.googleusercontent.com/aida-public/AB6AXuClXum0n5B3Fys7n6VOV6KZhwxyShVM0LCSKgB8SowoEgxrXjNTakjFaTonTQVYfKAxjWY0GZbcHevK4tuOw6eXiW_-7bKuWD4lewm9wxl51RDLOHQa7vH3fDiQA6sUQeFVJvw9D8-CjyPJELlqVFFfRcZyL7MnmMiA9HA_An3Ae4jBpRn2BWE7G1Pk7VM_vdjw8YHZh7bO0EzfAj0XZ7tDSkBPaK_CKJXq6P_pa9rM1ALr5vlx69f4';
$eduIntermediate = 'https://lh3.googleusercontent.com/aida-public/AB6AXuBAU594TAbyPKlG5KWutbMwCqXGdyxGubJNUFDO6FzVvF575dnmQkeOqmtDdTTaubPeTzJY1hR1B5vTbDoUaHWJJUe3iugxmlKGiko7VeZN03x2xTcUKkQdP1tEgbYiEt8BEVj3N4PCFw0s-sPyfeWTY3gbnQOYVLq7vV1mDxbmVgJhk_70tfiPXVKHzSxNrcWHBMC_9KjaBGAsAaAwJwMdyThozujO_EMfI6WHBxpaHgkN-_8YNJrX';
$eduAdvanced = 'https://lh3.googleusercontent.com/aida-public/AB6AXuC0RFiVG3wXTjeBaz-FYpuIcbtXW_-rbo6AcxjJgKfVR2jecI-nQ1lrSn8fWdmLi-t99OUPHZgN_NO7hSRwNbbteLmUbrMvWLAk42D9OO3H2H9QVmQ0JcGGuWnHZ99UJlAYT8_hUbJakBBvwWMCn7Ztlamrd-ccxL-ZB96l17wF8YLv9DLZsAiMDsyzLwfeAWPDNLwrkCdBcboSejRk3gMPOLOeI_1F0zlphMTW8IWVYb6VYvr-a3o2';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
</head>
<body class="marketing-page font-body-md text-body-md overflow-x-hidden">
<?php $currentPage = 'home'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<!-- Hero -->
<section class="relative pt-36 pb-24 md:pb-32 lg:pb-40 min-h-[88vh] lg:min-h-[92vh] flex items-center bg-surface-container-lowest overflow-hidden hero-gradient">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center w-full">
<div class="z-10">
<div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded-full mb-6 border border-border-low">
<span class="material-symbols-outlined text-primary text-[14px]">verified</span>
<span class="font-label-xs text-label-xs text-on-surface-variant uppercase"><?php echo htmlspecialchars($heroBadge); ?></span>
</div>
<h1 class="font-display text-4xl sm:text-5xl lg:text-display mb-6 leading-tight text-glow">Trade Smarter.<br/><span class="text-primary-container">Invest Better.</span></h1>
<p class="font-body-lg text-body-lg text-on-secondary-container max-w-xl mb-10">
The ultra-fast execution engine for professional traders and institutional investors. Access global markets with zero-latency liquidity.
</p>
<div class="flex flex-col sm:flex-row gap-4">
<a href="/register" class="bg-primary-container text-on-primary font-bold px-8 py-4 rounded-xl flex items-center justify-center gap-2 hover:scale-105 transition-all text-label-sm">
Open Institutional Account <span class="material-symbols-outlined">arrow_forward</span>
</a>
<a href="/login" class="border border-outline px-8 py-4 rounded-xl text-on-surface font-bold hover:bg-surface-container-high transition-all text-label-sm text-center">
View Live Terminal
</a>
</div>
</div>
<div class="relative lg:scale-110 lg:translate-x-12">
<div class="glass-panel p-2 rounded-2xl shadow-2xl overflow-hidden border-primary/20 min-h-[320px] sm:min-h-[400px] lg:min-h-[480px]">
<img class="w-full h-full min-h-[320px] sm:min-h-[400px] lg:min-h-[480px] object-cover rounded-xl" alt="<?php echo htmlspecialchars($siteName); ?> mobile trading" src="<?php echo htmlspecialchars($heroImg); ?>"/>
</div>
<div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/10 rounded-full blur-[100px] pointer-events-none"></div>
<div class="absolute -bottom-10 -left-10 w-64 h-64 bg-tertiary-container/10 rounded-full blur-[100px] pointer-events-none"></div>
</div>
</div>
</section>

<!-- Live Market Performance -->
<section id="markets" class="bg-[#F7F8FA] py-16 overflow-hidden border-y border-gray-200">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
<h2 class="font-headline-md text-headline-md text-surface-container-lowest">Live Market Performance</h2>
<a class="text-primary-container bg-surface-container-lowest px-4 py-2 rounded-lg font-label-xs text-label-xs inline-block w-fit" href="/trading_signals">VIEW ALL MARKETS</a>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-gutter market-cards">
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 group hover:shadow-md transition-shadow crypto-market-card" data-coin="bitcoin">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full overflow-hidden bg-yellow-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="https://assets.coingecko.com/coins/images/1/large/bitcoin.png" alt="Bitcoin"/>
</div>
<div>
<div class="font-bold text-surface-container-lowest crypto-symbol">BTC / USD</div>
<div class="text-xs text-gray-400 crypto-name">Bitcoin</div>
</div>
</div>
<div class="crypto-change font-bold font-data-mono text-gray-400">--</div>
</div>
<div class="text-2xl font-bold text-surface-container-lowest font-data-mono crypto-price">--</div>
<div class="mt-4 h-1 bg-gray-50 rounded-full overflow-hidden">
<div class="h-full bg-success w-[50%] market-bar"></div>
</div>
</div>
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 group hover:shadow-md transition-shadow crypto-market-card" data-coin="ethereum">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full overflow-hidden bg-blue-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="https://assets.coingecko.com/coins/images/279/large/ethereum.png" alt="Ethereum"/>
</div>
<div>
<div class="font-bold text-surface-container-lowest crypto-symbol">ETH / USD</div>
<div class="text-xs text-gray-400 crypto-name">Ethereum</div>
</div>
</div>
<div class="crypto-change font-bold font-data-mono text-gray-400">--</div>
</div>
<div class="text-2xl font-bold text-surface-container-lowest font-data-mono crypto-price">--</div>
<div class="mt-4 h-1 bg-gray-50 rounded-full overflow-hidden">
<div class="h-full bg-success w-[50%] market-bar"></div>
</div>
</div>
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 group hover:shadow-md transition-shadow crypto-market-card" data-coin="binancecoin">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full overflow-hidden bg-yellow-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png" alt="BNB"/>
</div>
<div>
<div class="font-bold text-surface-container-lowest crypto-symbol">BNB / USD</div>
<div class="text-xs text-gray-400 crypto-name">BNB</div>
</div>
</div>
<div class="crypto-change font-bold font-data-mono text-gray-400">--</div>
</div>
<div class="text-2xl font-bold text-surface-container-lowest font-data-mono crypto-price">--</div>
<div class="mt-4 h-1 bg-gray-50 rounded-full overflow-hidden">
<div class="h-full bg-success w-[50%] market-bar"></div>
</div>
</div>
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 group hover:shadow-md transition-shadow crypto-market-card" data-coin="solana">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full overflow-hidden bg-orange-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="https://assets.coingecko.com/coins/images/4128/large/solana.png" alt="Solana"/>
</div>
<div>
<div class="font-bold text-surface-container-lowest crypto-symbol">SOL / USD</div>
<div class="text-xs text-gray-400 crypto-name">Solana</div>
</div>
</div>
<div class="crypto-change font-bold font-data-mono text-gray-400">--</div>
</div>
<div class="text-2xl font-bold text-surface-container-lowest font-data-mono crypto-price">--</div>
<div class="mt-4 h-1 bg-gray-50 rounded-full overflow-hidden">
<div class="h-full bg-success w-[50%] market-bar"></div>
</div>
</div>
</div>
</div>
</section>

<!-- Trading Experience -->
<section class="bg-surface-container-lowest py-16 md:py-section-padding">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
<div class="lg:col-span-5 order-2 lg:order-1">
<span class="font-label-xs text-label-xs text-primary tracking-widest uppercase mb-4 block">Execution Performance</span>
<h2 class="font-headline-lg text-headline-lg mb-6">Professional Tools for Every Trader</h2>
<div class="space-y-8">
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary-container/10 border border-primary/20 rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary">speed</span>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Ultra-Low Latency</h3>
<p class="text-on-secondary-container">Execute orders in under 1ms with our institutional-grade matching engine, engineered for high-frequency trading.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary-container/10 border border-primary/20 rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary">data_exploration</span>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Advanced Analytics</h3>
<p class="text-on-secondary-container">Dozens of indicators and chart types integrated directly from TradingView, enhanced with our proprietary liquidity heatmaps.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary-container/10 border border-primary/20 rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary">shield</span>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Segregated Accounts</h3>
<p class="text-on-secondary-container">Your funds are protected. We maintain 1:1 reserves and utilize multi-sig cold storage for all digital assets.</p>
</div>
</div>
</div>
</div>
<div class="lg:col-span-7 order-1 lg:order-2">
<div class="relative rounded-2xl overflow-hidden glass-panel">
<img class="w-full h-full object-cover min-h-[280px]" alt="Professional trading terminal" src="<?php echo htmlspecialchars($tradingImg); ?>"/>
<div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest/80 to-transparent"></div>
</div>
</div>
</div>
</section>

<!-- Investment Management -->
<section class="bg-[#F1F3F5] py-16 md:py-section-padding text-surface-container-lowest">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
<div class="relative">
<div class="absolute -inset-4 bg-primary-container/20 blur-3xl rounded-full pointer-events-none"></div>
<img class="relative rounded-2xl shadow-xl w-full border border-gray-200" alt="Investment portfolio dashboard" src="<?php echo htmlspecialchars($investImg); ?>"/>
</div>
<div>
<span class="font-label-xs text-label-xs text-primary-container px-3 py-1 bg-surface-container-lowest rounded-md mb-4 inline-block">Bespoke Wealth Management</span>
<h2 class="font-headline-lg text-headline-lg mb-8 leading-tight">Institutional Asset Management</h2>
<p class="font-body-lg text-body-lg text-gray-600 mb-10">
<?php echo htmlspecialchars($siteName); ?> provides curated investment strategies for high-net-worth individuals and corporate entities. Our algorithms manage risk dynamically while maximizing alpha across global markets.
</p>
<ul class="space-y-4 mb-10">
<li class="flex items-center gap-3 text-gray-700 font-medium">
<span class="material-symbols-outlined text-success">check_circle</span>
Personalized Portfolio Construction
</li>
<li class="flex items-center gap-3 text-gray-700 font-medium">
<span class="material-symbols-outlined text-success">check_circle</span>
Tax-Efficient Rebalancing
</li>
<li class="flex items-center gap-3 text-gray-700 font-medium">
<span class="material-symbols-outlined text-success">check_circle</span>
Dedicated Wealth Consultant
</li>
</ul>
<a href="/plans" class="inline-block bg-surface-container-lowest text-primary font-bold px-10 py-4 rounded-xl hover:bg-gray-800 transition-colors text-label-sm">
Inquire About Institutional Services
</a>
</div>
</div>
</section>

<!-- Mobile Experience -->
<section class="bg-[#F7F8FA] py-16 md:py-section-padding text-surface-container-lowest text-center">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<h2 class="font-headline-lg text-headline-lg mb-4">Your Portfolio, Anywhere</h2>
<p class="text-gray-500 font-body-lg mb-16 max-w-2xl mx-auto">Take the power of <?php echo htmlspecialchars($siteName); ?> on the go with our award-winning mobile application. Full terminal features in the palm of your hand.</p>
<div class="relative max-w-4xl mx-auto">
<img class="mx-auto drop-shadow-2xl max-w-full h-auto" alt="Mobile trading app" src="<?php echo htmlspecialchars($mobileImg); ?>"/>
<div class="flex flex-col sm:flex-row justify-center gap-4 mt-12">
<a class="bg-black text-white px-8 py-3 rounded-xl flex items-center justify-center gap-3 hover:scale-105 transition-transform" href="/register">
<span class="material-symbols-outlined text-3xl">apps</span>
<div class="text-left">
<div class="text-[10px] uppercase opacity-70">Download on the</div>
<div class="text-lg font-bold leading-none">App Store</div>
</div>
</a>
<a class="bg-black text-white px-8 py-3 rounded-xl flex items-center justify-center gap-3 hover:scale-105 transition-transform" href="/register">
<span class="material-symbols-outlined text-3xl">play_books</span>
<div class="text-left">
<div class="text-[10px] uppercase opacity-70">Get it on</div>
<div class="text-lg font-bold leading-none">Google Play</div>
</div>
</a>
</div>
</div>
</div>
</section>

<!-- Neural Engine Intelligence -->
<section class="bg-surface-container-lowest py-16 md:py-section-padding relative overflow-hidden">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop relative z-10">
<div class="text-center mb-12 md:mb-20">
<span class="font-label-xs text-label-xs text-primary mb-4 block">Next-Generation Intelligence</span>
<h2 class="font-headline-lg text-headline-lg mb-4">Neural Engine Intelligence</h2>
<p class="text-on-secondary-container max-w-2xl mx-auto">Our AI-driven analytics suite processes millions of data points per second to identify sentiment shifts and market anomalies before they happen.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<div class="lg:col-span-8 glass-panel rounded-2xl overflow-hidden group">
<img class="w-full h-[280px] md:h-[400px] object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-700" alt="Market sentiment heatmap" src="<?php echo htmlspecialchars($heatmapImg); ?>"/>
<div class="p-8">
<h3 class="text-xl font-bold mb-2">Market Sentiment Heatmaps</h3>
<p class="text-on-secondary-container">Visualize global capital flow across thousands of assets simultaneously with our real-time sector rotation terminal.</p>
</div>
</div>
<div class="lg:col-span-4 flex flex-col gap-gutter">
<div class="glass-panel rounded-2xl p-8 flex-1 group hover:border-primary/40 transition-colors">
<div class="w-12 h-12 bg-primary-container/10 border border-primary/20 rounded-lg flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-primary">psychology</span>
</div>
<h3 class="text-xl font-bold mb-2">Predictive AI</h3>
<p class="text-on-secondary-container">Machine learning models trained on decades of market data to assist your decision-making process.</p>
</div>
<div class="glass-panel rounded-2xl p-8 flex-1 group hover:border-primary/40 transition-colors">
<div class="w-12 h-12 bg-primary-container/10 border border-primary/20 rounded-lg flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-primary">hub</span>
</div>
<h3 class="text-xl font-bold mb-2">Smart Order Routing</h3>
<p class="text-on-secondary-container">Our SOR engine automatically finds the best price across multiple liquidity venues in real-time.</p>
</div>
</div>
</div>
</div>
</section>

<!-- Trust & Security -->
<section class="bg-white py-16 md:py-section-padding text-surface-container-lowest">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 text-center">
<div>
<div class="text-3xl md:text-4xl font-extrabold mb-2 font-display text-primary-container"><?php echo htmlspecialchars($statsVolume); ?></div>
<div class="font-label-xs text-label-xs text-gray-400 uppercase tracking-widest">Quarterly Trading Volume</div>
</div>
<div>
<div class="text-3xl md:text-4xl font-extrabold mb-2 font-display text-primary-container"><?php echo htmlspecialchars($statsInvestors); ?></div>
<div class="font-label-xs text-label-xs text-gray-400 uppercase tracking-widest">Verified Investors</div>
</div>
<div>
<div class="text-3xl md:text-4xl font-extrabold mb-2 font-display text-primary-container"><?php echo htmlspecialchars($statsUptime); ?></div>
<div class="font-label-xs text-label-xs text-gray-400 uppercase tracking-widest">Reserve Transparency</div>
</div>
<div>
<div class="text-3xl md:text-4xl font-extrabold mb-2 font-display text-primary-container"><?php echo htmlspecialchars($statsSupport); ?></div>
<div class="font-label-xs text-label-xs text-gray-400 uppercase tracking-widest">Concierge Support</div>
</div>
</div>
<div class="mt-16 md:mt-24 pt-12 md:pt-16 border-t border-gray-100 flex flex-wrap justify-center items-center gap-8 md:gap-16 grayscale opacity-50">
<div class="h-8 flex items-center font-display font-black text-lg md:text-2xl tracking-tighter">FINRA REGULATED</div>
<div class="h-8 flex items-center font-display font-black text-lg md:text-2xl tracking-tighter">ISO 27001</div>
<div class="h-8 flex items-center font-display font-black text-lg md:text-2xl tracking-tighter">SEC COMPLIANT</div>
<div class="h-8 flex items-center font-display font-black text-lg md:text-2xl tracking-tighter">MULTI-SIG PROTECTED</div>
</div>
</div>
</section>

<!-- Learn and Earn -->
<section class="bg-[#F1F3F5] py-16 md:py-section-padding text-surface-container-lowest">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-12">
<div>
<span class="font-label-xs text-label-xs text-gray-400 mb-2 block">Knowledge is Wealth</span>
<h2 class="font-headline-lg text-headline-lg">Learn and Earn</h2>
</div>
<a href="/help_centre" class="text-primary-container font-bold border-b-2 border-primary-container pb-1 text-label-sm">Browse Academy</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<a href="/help_centre" class="bg-white rounded-xl overflow-hidden shadow-sm group cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Foundations of trading" src="<?php echo htmlspecialchars($eduBeginner); ?>"/>
</div>
<div class="p-6">
<span class="text-xs font-bold text-primary-container uppercase mb-2 block">Beginner</span>
<h3 class="text-xl font-bold mb-3">Foundations of Trading</h3>
<p class="text-gray-500 text-sm mb-4">Master the basics of technical analysis, order types, and risk management.</p>
<div class="flex items-center text-primary-container font-bold text-sm">
Read Module <span class="material-symbols-outlined ml-1 text-sm">chevron_right</span>
</div>
</div>
</a>
<a href="/help_centre" class="bg-white rounded-xl overflow-hidden shadow-sm group cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Understanding liquidity" src="<?php echo htmlspecialchars($eduIntermediate); ?>"/>
</div>
<div class="p-6">
<span class="text-xs font-bold text-primary-container uppercase mb-2 block">Intermediate</span>
<h3 class="text-xl font-bold mb-3">Understanding Liquidity</h3>
<p class="text-gray-500 text-sm mb-4">Deep dive into how liquidity pools and market makers shape price action.</p>
<div class="flex items-center text-primary-container font-bold text-sm">
Read Module <span class="material-symbols-outlined ml-1 text-sm">chevron_right</span>
</div>
</div>
</a>
<a href="/help_centre" class="bg-white rounded-xl overflow-hidden shadow-sm group cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="Algorithmic strategies" src="<?php echo htmlspecialchars($eduAdvanced); ?>"/>
</div>
<div class="p-6">
<span class="text-xs font-bold text-primary-container uppercase mb-2 block">Advanced</span>
<h3 class="text-xl font-bold mb-3">Algorithmic Strategies</h3>
<p class="text-gray-500 text-sm mb-4">Building and backtesting quantitative models for institutional portfolios.</p>
<div class="flex items-center text-primary-container font-bold text-sm">
Read Module <span class="material-symbols-outlined ml-1 text-sm">chevron_right</span>
</div>
</div>
</a>
</div>
</div>
</section>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (window.BloombitCryptoPrices) {
    window.BloombitCryptoPrices.init(['bitcoin','ethereum','binancecoin','solana'], {
      marketCardsSelector: '.market-cards',
      refreshInterval: 120000
    });
  }

  document.querySelectorAll('a, button').forEach(function(btn) {
    btn.addEventListener('mousedown', function() { btn.classList.add('scale-95'); });
    btn.addEventListener('mouseup', function() { btn.classList.remove('scale-95'); });
    btn.addEventListener('mouseleave', function() { btn.classList.remove('scale-95'); });
  });
});
</script>
</body>
</html>

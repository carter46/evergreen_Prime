<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/market-instruments.php';
$siteName = get_site_name();
$pageTitle = $siteName . ' | Professional Trading & Institutional Asset Management';
$heroBadge = get_site_setting('hero_badge', 'Institutional Grade Security');
$statsVolume = get_site_setting('stats_assets', '$42B+');
$statsInvestors = get_site_setting('stats_bots', '1.2M+');
$statsUptime = get_site_setting('stats_uptime', '100%');
$statsSupport = get_site_setting('stats_roi', '24/7');

$heroImg = '/uploads/images/evergren_cardphone.png';
$heroBgImg = '/uploads/images/nasa-Q1p7bh3SHj8-unsplash.jpg';
$tradingImg = '/uploads/images/evergren_cmarket.png';
$investImg = '/uploads/images/wallet_image3.png';
$mobileImg = '/uploads/images/evergren_mockup.png';
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
<section class="relative pt-36 pb-24 md:pb-32 lg:pb-40 min-h-[88vh] lg:min-h-[92vh] flex items-center overflow-hidden hero-section">
<div class="absolute inset-0 hero-bg" style="background-image: url('<?php echo htmlspecialchars($heroBgImg); ?>');"></div>
<div class="absolute inset-0 hero-bg-overlay"></div>
<div class="relative z-10 max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center w-full">
<div class="order-2 lg:order-1 text-center lg:text-left">
<div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded-full mb-6 border border-border-low">
<span class="material-symbols-outlined text-primary text-[14px]">verified</span>
<span class="font-label-xs text-label-xs text-on-surface-variant uppercase"><?php echo htmlspecialchars($heroBadge); ?></span>
</div>
<h1 class="font-display text-4xl sm:text-5xl lg:text-display mb-6 leading-tight text-glow">Trade Smarter.<br/><span class="text-primary-container">Invest Better.</span></h1>
<p class="font-body-lg text-body-lg text-on-secondary-container max-w-xl mb-10">
The ultra-fast execution engine for professional traders and institutional investors. Access global markets with zero-latency liquidity.
</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
<a href="/register" class="bg-primary-container text-on-primary font-bold px-8 py-4 rounded-xl flex items-center justify-center gap-2 hover:scale-105 transition-all text-label-sm">
Open Institutional Account <span class="material-symbols-outlined">arrow_forward</span>
</a>
<a href="/login" class="border border-outline px-8 py-4 rounded-xl text-on-surface font-bold hover:bg-surface-container-high transition-all text-label-sm text-center">
View Live Terminal
</a>
</div>
</div>
<div class="order-1 lg:order-2 relative flex justify-center items-center w-full mx-auto px-4 sm:px-10 lg:px-14 xl:px-20 lg:justify-end">
<img class="hero-image-animate w-full max-w-[340px] sm:max-w-[400px] md:max-w-[460px] lg:max-w-[540px] xl:max-w-[600px] h-auto mx-auto lg:mx-0" alt="<?php echo htmlspecialchars($siteName); ?> mobile trading" src="<?php echo htmlspecialchars($heroImg); ?>"/>
</div>
</div>
</section>

<!-- Live Market Performance -->
<section id="markets" class="bg-[#F7F8FA] py-16 overflow-hidden border-y border-gray-200">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<div class="mb-8">
<h2 class="font-headline-md text-headline-md text-surface-container-lowest">Live Market Performance</h2>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-gutter market-cards">
<?php foreach (get_markets_by_category('crypto') as $instrument): ?>
<?php require __DIR__ . '/includes/market-home-card.php'; ?>
<?php endforeach; ?>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-gutter market-stocks mt-gutter">
<?php foreach (get_markets_by_category('stock') as $instrument): ?>
<?php require __DIR__ . '/includes/market-home-card.php'; ?>
<?php endforeach; ?>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-gutter market-forex mt-gutter">
<?php foreach (get_markets_by_category('forex') as $instrument): ?>
<?php require __DIR__ . '/includes/market-home-card.php'; ?>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/market-tv-scripts.php'; ?>
<div class="flex justify-center mt-8 md:mt-10">
<a class="text-primary-container bg-surface-container-lowest px-6 py-3 rounded-lg font-label-xs text-label-xs inline-block hover:opacity-90 transition-opacity" href="/trading_signals">VIEW ALL MARKETS</a>
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
<div class="relative rounded-2xl overflow-hidden">
<img class="w-full h-full object-cover min-h-[280px] rounded-2xl" alt="Professional trading terminal" src="<?php echo htmlspecialchars($tradingImg); ?>"/>
</div>
</div>
</div>
</section>

<!-- Investment Management -->
<section class="bg-[#F1F3F5] py-16 md:py-section-padding text-surface-container-lowest">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
<div class="relative rounded-2xl overflow-hidden shadow-xl wealth-image-wrap">
<div class="wealth-image-bg">
<img class="relative w-full h-auto rounded-xl" alt="Investment portfolio dashboard" src="<?php echo htmlspecialchars($investImg); ?>"/>
</div>
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
<div class="relative max-w-3xl mx-auto">
<img class="mx-auto drop-shadow-2xl w-full max-w-[88%] h-auto rounded-2xl md:rounded-3xl" alt="Mobile trading app" src="<?php echo htmlspecialchars($mobileImg); ?>"/>
<div class="flex flex-col sm:flex-row justify-center gap-4 mt-12">
<button type="button" data-pwa-install="mobile" class="bg-black text-white px-8 py-3 rounded-xl flex items-center justify-center gap-3 hover:scale-105 transition-transform disabled:opacity-70 disabled:hover:scale-100">
<span class="material-symbols-outlined text-3xl">smartphone</span>
<div class="text-left">
<div class="text-[10px] uppercase opacity-70" data-pwa-sub>Install directly to your device</div>
<div class="text-lg font-bold leading-none" data-pwa-label>Download for Mobile</div>
</div>
</button>
<button type="button" data-pwa-install="desktop" class="bg-black text-white px-8 py-3 rounded-xl flex items-center justify-center gap-3 hover:scale-105 transition-transform disabled:opacity-70 disabled:hover:scale-100">
<span class="material-symbols-outlined text-3xl">computer</span>
<div class="text-left">
<div class="text-[10px] uppercase opacity-70" data-pwa-sub>Install directly to your device</div>
<div class="text-lg font-bold leading-none" data-pwa-label>Download for Desktop</div>
</div>
</button>
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
<div class="tradingview-widget-container h-[320px] sm:h-[380px] md:h-[420px]">
<div class="tradingview-widget-container__widget"></div>
<div class="tradingview-widget-copyright text-xs text-on-secondary-container px-4 py-2"><a href="https://www.tradingview.com/heatmap/etf/" rel="noopener nofollow" target="_blank"><span class="text-primary-container">ETF Heatmap</span></a><span class="trademark"> by TradingView</span></div>
<script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-etf-heatmap.js" async>
{
"dataSource": "AllUSEtf",
"blockSize": "volume",
"blockColor": "change",
"grouping": "asset_class",
"locale": "en",
"symbolUrl": "",
"colorTheme": "light",
"hasTopBar": false,
"isDataSetEnabled": false,
"isZoomEnabled": true,
"hasSymbolTooltip": true,
"isMonoSize": false,
"width": "100%",
"height": "100%"
}
</script>
</div>
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

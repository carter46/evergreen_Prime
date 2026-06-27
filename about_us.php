<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = 'About Us | ' . $siteName . ' - Institutional Grade Trading';
$heroBg = 'https://lh3.googleusercontent.com/aida/AP1WRLuaqlZwMUTllIVowJvsPs71UrRrOaPOvonLjjptWfNtUe89eodKTGsJELawmdRPTKUT3_hJ0tpi3hoatIQo1H8PScnwZigbNa9QZPVYYjwOmPP7WeMcZ8xN3JqNaU3I-RzfDr2CGZvmHaMVaG7Nt0aewolZdG-y4NHFq8Kdfh8HMIQoQbrYrEfYHTTYb1KSyEh_93YaTd4MmpDPGknsEOh3AMslYaIoDyqkomb33wnIg-vcUFgV7FUnJw';
$infraBg = 'https://lh3.googleusercontent.com/aida/AP1WRLsKriSbY6BJi-Xp2Gkc7D7CVwxW2aLMAeU3vslR5SSitI_47iRoKte8OAQPNNm9SVIVAJP-rxuMAgVJSJdgU79P5g1FgzlR3L1T3iKisxILmQwUVbRBpe9jP9AcBhmn5dOT2lGX6TkC3LxSMhG_7zFbayukNlnb63bYjV8lzW6sJhcDohhWpwHwt7jiN5I_ApLCsQeZ4HaS-BEOnuPIsgpW6dVCbSLy14ewi2QOegd2_aontl0Sqgbjst8';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
.active-dot { box-shadow: 0 0 8px #20B26C; }
.about-hero-bg {
  background-image: url('<?php echo htmlspecialchars($heroBg); ?>');
  background-size: cover;
  background-position: center;
}
.about-infra-bg {
  background-image: url('<?php echo htmlspecialchars($infraBg); ?>');
  background-size: cover;
}
</style>
</head>
<body class="marketing-page font-body-md bg-background text-on-surface selection:bg-primary-container selection:text-on-primary-container overflow-x-hidden">
<?php $currentPage = 'about_us'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main>
<!-- Hero -->
<section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0 about-hero-bg opacity-40"></div>
<div class="absolute inset-0 bg-gradient-to-b from-background/20 via-transparent to-background z-10"></div>
<div class="relative z-20 max-w-container-max mx-auto px-4 md:px-margin-desktop text-center py-16">
<span class="inline-block font-label-xs text-label-xs text-surface-tint tracking-[0.2em] mb-4 border border-surface-tint/30 px-4 py-1 rounded-full bg-surface-tint/5">ESTABLISHED 2021</span>
<h1 class="font-display text-4xl sm:text-5xl lg:text-display text-text-primary mb-6 max-w-4xl mx-auto text-glow">
Democratizing Wealth Creation <span class="text-primary-container">Through AI.</span>
</h1>
<p class="font-body-lg text-body-lg text-text-secondary max-w-2xl mx-auto mb-10">
<?php echo htmlspecialchars($siteName); ?> is more than a trading platform. We are a collective of financial engineers and AI researchers dedicated to leveling the playing field in global crypto markets.
</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center">
<a href="#infrastructure" class="bg-primary-container text-on-primary px-8 py-4 font-label-sm text-label-sm rounded-lg inline-flex items-center justify-center gap-2 hover:brightness-110 transition-all">
VIEW OUR INFRASTRUCTURE <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
<a href="/legal_centre" class="border border-low text-text-primary px-8 py-4 font-label-sm text-label-sm rounded-lg hover:bg-white/5 transition-all inline-flex items-center justify-center">
OUR WHITE PAPER
</a>
</div>
</div>
</section>

<!-- Social Proof Stats Bar -->
<section class="bg-bg-subtle py-12 border-y border-low">
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop">
<div class="flex flex-col md:flex-row items-center justify-between gap-8">
<div class="flex items-center gap-4">
<div class="flex -space-x-3">
<div class="w-10 h-10 rounded-full border-2 border-bg-subtle bg-surface-container-high flex items-center justify-center"><span class="material-symbols-outlined text-primary-container text-[16px]">person</span></div>
<div class="w-10 h-10 rounded-full border-2 border-bg-subtle bg-surface-container-high flex items-center justify-center"><span class="material-symbols-outlined text-primary-container text-[16px]">person</span></div>
<div class="w-10 h-10 rounded-full border-2 border-bg-subtle bg-surface-container-high flex items-center justify-center"><span class="material-symbols-outlined text-primary-container text-[16px]">person</span></div>
</div>
<div>
<p class="font-headline-md text-headline-md text-text-primary leading-none">Trusted by 250k+</p>
<p class="font-label-xs text-label-xs text-text-secondary mt-1 uppercase">Active Institutional &amp; Retail Traders</p>
</div>
</div>
<div class="flex flex-wrap justify-center gap-6 opacity-60 grayscale hover:grayscale-0 transition-all">
<span class="font-label-sm text-label-sm text-text-secondary">OPERATING IN 45+ COUNTRIES</span>
<div class="flex gap-3 items-center">
<div class="w-6 h-4 bg-surface-container-highest rounded-sm"></div>
<div class="w-6 h-4 bg-surface-container-highest rounded-sm"></div>
<div class="w-6 h-4 bg-surface-container-highest rounded-sm"></div>
<div class="w-6 h-4 bg-surface-container-highest rounded-sm"></div>
<div class="w-6 h-4 bg-surface-container-highest rounded-sm"></div>
</div>
</div>
</div>
</div>
</section>

<!-- Vision & Mission -->
<section class="py-section-padding relative">
<div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none about-infra-bg"></div>
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop grid md:grid-cols-2 gap-16 items-center">
<div class="space-y-12">
<div class="glass-panel p-8 md:p-12 rounded-xl">
<span class="text-primary-container font-label-xs text-label-xs uppercase tracking-widest mb-4 block">Our Vision</span>
<h2 class="font-headline-lg text-headline-lg text-text-primary mb-6">Interface Between Human Intuition and Machine Intelligence.</h2>
<p class="font-body-md text-body-md text-text-secondary leading-relaxed">
To become the world's most trusted interface between human intuition and machine intelligence, fostering a financial ecosystem where high-frequency precision is accessible to all.
</p>
</div>
<div class="glass-panel p-8 md:p-12 rounded-xl">
<span class="text-primary-container font-label-xs text-label-xs uppercase tracking-widest mb-4 block">Our Mission</span>
<h2 class="font-headline-lg text-headline-lg text-text-primary mb-6">Eliminating the Emotional Bias of Trading.</h2>
<p class="font-body-md text-body-md text-text-secondary leading-relaxed">
By integrating deep learning with high-frequency execution, we eliminate the emotional bias of trading, delivering consistent performance through rigorous technical infrastructure.
</p>
</div>
</div>
<div class="relative group">
<div class="absolute -inset-1 bg-gradient-to-r from-primary-container/20 to-transparent rounded-xl blur-2xl group-hover:blur-3xl transition-all duration-500 opacity-50"></div>
<div class="relative glass-panel rounded-xl overflow-hidden aspect-square flex items-center justify-center p-4">
<img class="w-full h-full object-cover rounded shadow-2xl" alt="Crypto assets and digital market infrastructure" src="/uploads/images/crypto-assets.jpg" onerror="this.onerror=null;this.src='/uploads/images/crypto-assets.png'"/>
</div>
</div>
</div>
</section>

<!-- The Standard -->
<section class="py-section-padding bg-surface-container-lowest">
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop">
<div class="text-center mb-16">
<h2 class="font-headline-lg text-headline-lg text-text-primary mb-4">The <?php echo htmlspecialchars($siteName); ?> Standard</h2>
<div class="w-24 h-1 bg-primary-container mx-auto"></div>
</div>
<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
<div class="p-8 border border-low hover:border-primary-container/30 transition-all group rounded-xl">
<div class="w-12 h-12 bg-primary-container/10 flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary-container/20 transition-colors">
<span class="material-symbols-outlined text-primary-container text-[28px]">visibility</span>
</div>
<h3 class="font-headline-md text-headline-md text-text-primary mb-4">Transparency</h3>
<p class="font-body-md text-body-md text-text-secondary">Open-source validation of our execution logic and 1:1 asset backing on all custodial wallets.</p>
</div>
<div class="p-8 border border-low hover:border-primary-container/30 transition-all group rounded-xl">
<div class="w-12 h-12 bg-primary-container/10 flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary-container/20 transition-colors">
<span class="material-symbols-outlined text-primary-container text-[28px]">rocket_launch</span>
</div>
<h3 class="font-headline-md text-headline-md text-text-primary mb-4">Innovation</h3>
<p class="font-body-md text-body-md text-text-secondary">Proprietary neural networks optimized for volatile crypto-asset liquidity management.</p>
</div>
<div class="p-8 border border-low hover:border-primary-container/30 transition-all group rounded-xl">
<div class="w-12 h-12 bg-primary-container/10 flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary-container/20 transition-colors">
<span class="material-symbols-outlined text-primary-container text-[28px]">verified_user</span>
</div>
<h3 class="font-headline-md text-headline-md text-text-primary mb-4">Security</h3>
<p class="font-body-md text-body-md text-text-secondary">Multi-sig authorization and cold-storage protocols designed by former intelligence engineers.</p>
</div>
<div class="p-8 border border-low hover:border-primary-container/30 transition-all group rounded-xl">
<div class="w-12 h-12 bg-primary-container/10 flex items-center justify-center rounded-lg mb-6 group-hover:bg-primary-container/20 transition-colors">
<span class="material-symbols-outlined text-primary-container text-[28px]">speed</span>
</div>
<h3 class="font-headline-md text-headline-md text-text-primary mb-4">Performance</h3>
<p class="font-body-md text-body-md text-text-secondary">Microsecond latency through a global mesh network of high-frequency data nodes.</p>
</div>
</div>
</div>
</section>

<!-- Technical Infrastructure -->
<section id="infrastructure" class="py-section-padding relative overflow-hidden scroll-mt-24">
<div class="absolute inset-0 about-infra-bg bg-fixed opacity-5"></div>
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop relative z-10">
<div class="grid lg:grid-cols-2 gap-20 items-center">
<div>
<span class="font-label-xs text-label-xs text-primary-container tracking-widest block mb-4">INFRASTRUCTURE</span>
<h2 class="font-display text-headline-lg text-text-primary mb-8">Uncompromising Engineering for Uninterrupted Trading.</h2>
<div class="space-y-8">
<div class="flex gap-6">
<div class="mt-1"><span class="material-symbols-outlined text-primary-container">layers</span></div>
<div>
<h4 class="font-headline-md text-headline-md text-text-primary mb-2">Multi-Layer Security Architecture</h4>
<p class="font-body-md text-body-md text-text-secondary">Our 'Shield' protocol separates transaction authorization from private key exposure, ensuring zero-trust environments across all server nodes.</p>
</div>
</div>
<div class="flex gap-6">
<div class="mt-1"><span class="material-symbols-outlined text-primary-container">cloud_sync</span></div>
<div>
<h4 class="font-headline-md text-headline-md text-text-primary mb-2">Cloud-Native Scalability</h4>
<p class="font-body-md text-body-md text-text-secondary">Dynamic auto-scaling that handles 500k+ transactions per second during high-volatility events without performance degradation.</p>
</div>
</div>
</div>
</div>
<div class="glass-panel rounded-xl overflow-hidden shadow-2xl border border-primary-container/20">
<div class="bg-surface-container px-6 py-4 border-b border-low flex justify-between items-center">
<div class="flex items-center gap-3">
<div class="flex gap-1.5">
<div class="w-3 h-3 rounded-full bg-error/40"></div>
<div class="w-3 h-3 rounded-full bg-primary-container/40"></div>
<div class="w-3 h-3 rounded-full bg-success/40"></div>
</div>
<span class="font-label-xs text-label-xs text-text-secondary">REAL-TIME SYSTEM MONITOR</span>
</div>
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-success active-dot animate-pulse"></div>
<span class="font-label-xs text-label-xs text-success uppercase">Operational</span>
</div>
</div>
<div class="p-8 space-y-6 font-data-mono text-data-mono">
<div class="flex justify-between items-center border-b border-low pb-4">
<span class="text-text-secondary">Node A: Amsterdam High-Freq</span>
<span class="text-success font-bold">ACTIVE</span>
</div>
<div class="flex justify-between items-center border-b border-low pb-4">
<span class="text-text-secondary">Node B: Singapore Neural Hub</span>
<span class="text-success font-bold">ACTIVE</span>
</div>
<div class="flex justify-between items-center border-b border-low pb-4">
<span class="text-text-secondary">Node C: New York Liquidity Relay</span>
<span class="text-primary-container font-bold">STANDBY</span>
</div>
<div class="pt-4">
<div class="flex justify-between mb-2">
<span class="text-text-secondary text-label-xs">NETWORK LOAD CAPACITY</span>
<span class="text-text-primary text-label-xs">85%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-primary-container h-full w-[85%]"></div>
</div>
</div>
<div class="mt-8 p-4 bg-background/50 rounded border border-low">
<p class="text-text-secondary text-label-xs leading-relaxed">
[SYS_LOG]: LATENCY &lt; 4MS // NEURAL_NET_V4 DEPLOYED // PACKET_LOSS: 0.0001%
</p>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- Founder Quote -->
<section class="py-section-padding bg-bg-subtle relative overflow-hidden">
<div class="max-w-4xl mx-auto px-4 md:px-margin-desktop text-center relative z-10">
<div class="mb-12 inline-block">
<div class="w-24 h-24 rounded-full border-2 border-primary-container p-1 mx-auto mb-6">
<div class="w-full h-full rounded-full bg-surface-container-highest flex items-center justify-center overflow-hidden">
<span class="material-symbols-outlined text-primary-container text-[48px]">person</span>
</div>
</div>
<p class="font-headline-md text-headline-md text-text-primary">Martin Harris</p>
<p class="font-label-xs text-label-xs text-primary-container tracking-widest uppercase">Founder &amp; CEO</p>
</div>
<blockquote class="font-display text-headline-lg text-text-primary italic leading-tight mb-12">
"In a world of noise, we provide the clarity of computation. Our goal is to build a bridge to the future of finance, where every investor has institutional-grade weaponry in their arsenal."
</blockquote>
<div class="flex justify-center">
<div class="w-12 h-1 bg-primary-container/30"></div>
</div>
</div>
<span class="absolute top-0 left-10 text-[200px] font-display text-white/5 pointer-events-none select-none" aria-hidden="true">"</span>
<span class="absolute bottom-0 right-10 text-[200px] font-display text-white/5 pointer-events-none select-none" aria-hidden="true">"</span>
</section>

<!-- Global Stats -->
<section class="py-24 border-t border-low">
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop">
<div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
<div>
<p class="font-display text-[48px] text-primary-container mb-2">42</p>
<p class="font-label-sm text-label-sm text-text-secondary uppercase">Countries Supported</p>
</div>
<div>
<p class="font-display text-[48px] text-primary-container mb-2">$14B+</p>
<p class="font-label-sm text-label-sm text-text-secondary uppercase">Assets Traded</p>
</div>
<div>
<p class="font-display text-[48px] text-primary-container mb-2">99.9%</p>
<p class="font-label-sm text-label-sm text-text-secondary uppercase">Uptime History</p>
</div>
<div>
<p class="font-display text-[48px] text-primary-container mb-2">24/7</p>
<p class="font-label-sm text-label-sm text-text-secondary uppercase">Expert Support</p>
</div>
</div>
</div>
</section>

<!-- CTA -->
<section class="py-section-padding px-4 md:px-margin-desktop">
<div class="max-w-container-max mx-auto glass-panel rounded-2xl p-12 md:p-20 text-center relative overflow-hidden group">
<div class="absolute inset-0 bg-primary-container/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
<h2 class="font-display text-headline-lg text-text-primary mb-6 relative z-10">Ready to trade at the speed of light?</h2>
<p class="font-body-lg text-body-lg text-text-secondary mb-10 max-w-xl mx-auto relative z-10">Join <?php echo htmlspecialchars($siteName); ?> and experience the precision of institutional-grade AI trading signals.</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
<a href="/register" class="bg-primary-container text-on-primary px-10 py-5 font-label-sm text-label-sm rounded-lg hover:brightness-110 active:scale-95 transition-all inline-flex items-center justify-center">CREATE YOUR ACCOUNT</a>
<a href="/live_chat" class="border border-low text-text-primary px-10 py-5 font-label-sm text-label-sm rounded-lg hover:bg-white/5 transition-all inline-flex items-center justify-center">CONTACT INSTITUTIONAL DESK</a>
</div>
</div>
</section>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body>
</html>

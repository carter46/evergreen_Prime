<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$aboutYoutubeUrl = get_site_setting('about_youtube_url', '');
$aboutEmbedUrl = get_youtube_embed_url($aboutYoutubeUrl);
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>About <?php echo htmlspecialchars($siteName); ?> - Our Mission &amp; Vision</title>
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
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #f8f8f5;
        }
        .glow-hover:hover {
            box-shadow: 0 0 30px rgba(255, 193, 5, 0.15);
        }
        .tech-line {
            background: linear-gradient(90deg, #ffc105 0%, rgba(255, 193, 5, 0) 100%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300 overflow-x-hidden">
<?php $currentPage = 'about_us'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<!-- Hero Section: Who We Are -->
<header class="relative pt-20 pb-32 overflow-hidden">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
<div>
<span class="inline-block px-3 py-1 bg-primary/10 text-primary font-semibold text-sm rounded mb-6">ESTABLISHED 2021</span>
<h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight mb-8">
                    Democratizing <br/>
<span class="text-primary">Wealth Creation</span> <br/>
                    Through AI.
                </h1>
<p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-10 max-w-lg">
                    <?php echo htmlspecialchars($siteName); ?> is more than a trading platform. We are a collective of financial engineers and AI researchers dedicated to leveling the playing field in global crypto markets.
                </p>
<div class="flex gap-4">
<div class="flex -space-x-3">
<img class="w-12 h-12 rounded-full border-4 border-background-light object-cover" alt="Team member" src="/uploads/images/user1.jpg" onerror="this.style.display='none'"/>
<img class="w-12 h-12 rounded-full border-4 border-background-light object-cover" alt="Team member" src="/uploads/images/user2.jpg" onerror="this.style.display='none'"/>
<img class="w-12 h-12 rounded-full border-4 border-background-light object-cover" alt="Team member" src="/uploads/images/user3.jpg" onerror="this.style.display='none'"/>
<div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center border-4 border-background-light text-background-dark font-bold text-xs">+45</div>
</div>
<div class="flex flex-col justify-center">
<span class="text-sm font-bold">Trusted by 250k+ Traders</span>
<span class="text-xs opacity-60">Global active user base</span>
</div>
</div>
</div>
<div class="relative">
<div class="absolute -top-10 -right-10 w-64 h-64 bg-primary/20 rounded-full blur-3xl"></div>
<div class="relative z-10 rounded-xl overflow-hidden aspect-video bg-slate-200 shadow-2xl w-full border border-white/10">
<?php if (!empty($aboutEmbedUrl)): ?>
<iframe class="w-full h-full" src="<?php echo htmlspecialchars($aboutEmbedUrl); ?>?rel=0" title="Message from leadership" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<?php else: ?>
<img class="w-full h-full object-cover" alt="Video placeholder" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSyoTCKeP6akFTtGff_gKdWfqar2QLzLFeK2v44pHDrjOzc9cSM9bS-RGr7LIJgahMs6iiWD_WYkZu317yH5wEusVmEjnfYFcSarEBcbv66RG2Sce5uHkVeYDg3j19_gMhYVdFtyywx4BlCoQXGs1Ndi_DdSGJwkofs0e0tIyUcsnHE715OMmwSqCQdk_ZNUv74V6WJOHbg16G6s5qRNgAJaph2mMdr8pU6JMMcfjahaLso_CzLU3Q61MnM05Ieo4S7A5nECAeQGw"/>
<div class="absolute inset-0 bg-background-dark/40 flex items-center justify-center">
<div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-background-dark shadow-xl">
<span class="material-icons text-3xl">play_arrow</span>
</div>
</div>
<?php endif; ?>
</div>
</div>
</div>
</header>
<!-- Vision & Mission Section -->
<section class="py-24 bg-white dark:bg-black/20">
<div class="max-w-7xl mx-auto px-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="p-12 rounded-xl border border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark/40 relative group overflow-hidden">
<div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform"></div>
<span class="material-icons text-primary text-5xl mb-6">insights</span>
<h3 class="text-3xl font-bold mb-4">Our Vision</h3>
<p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                        To become the world's most trusted interface between human intuition and machine intelligence, making elite trading strategies accessible to everyone, anywhere.
                    </p>
</div>
<div class="p-12 rounded-xl border border-slate-200 dark:border-slate-800 bg-background-light dark:bg-background-dark/40 relative group overflow-hidden">
<div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full translate-x-8 -translate-y-8 group-hover:translate-x-4 group-hover:-translate-y-4 transition-transform"></div>
<span class="material-icons text-primary text-5xl mb-6">rocket_launch</span>
<h3 class="text-3xl font-bold mb-4">Our Mission</h3>
<p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                        By integrating deep learning with high-frequency execution, we eliminate the emotional bias of trading, ensuring our users benefit from math, not myth.
                    </p>
</div>
</div>
</div>
</section>
<!-- Core Values: 2x2 Grid -->
<section class="py-24">
<div class="max-w-7xl mx-auto px-6">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold mb-4 tracking-tight">The <?php echo htmlspecialchars($siteName); ?> Standard</h2>
<div class="w-20 h-1 bg-primary mx-auto"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<!-- Transparency -->
<div class="p-10 rounded-xl bg-white dark:bg-background-dark border border-slate-100 dark:border-slate-800 glow-hover transition-all group">
<div class="flex items-center gap-6">
<div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-background-dark">visibility</span>
</div>
<div>
<h4 class="text-2xl font-bold mb-2">Transparency</h4>
<p class="text-slate-600 dark:text-slate-400">Real-time auditing and clear fee structures. We hide nothing from our investors.</p>
</div>
</div>
</div>
<!-- Innovation -->
<div class="p-10 rounded-xl bg-white dark:bg-background-dark border border-slate-100 dark:border-slate-800 glow-hover transition-all group">
<div class="flex items-center gap-6">
<div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-background-dark">psychology</span>
</div>
<div>
<h4 class="text-2xl font-bold mb-2">Innovation</h4>
<p class="text-slate-600 dark:text-slate-400">Pushing the boundaries of Neural Networks to predict market trends with 99.9% uptime.</p>
</div>
</div>
</div>
<!-- Security -->
<div class="p-10 rounded-xl bg-white dark:bg-background-dark border border-slate-100 dark:border-slate-800 glow-hover transition-all group">
<div class="flex items-center gap-6">
<div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-background-dark">gpp_good</span>
</div>
<div>
<h4 class="text-2xl font-bold mb-2">Security</h4>
<p class="text-slate-600 dark:text-slate-400">Institutional-grade cold storage and multi-sig protocols for every single asset.</p>
</div>
</div>
</div>
<!-- Performance -->
<div class="p-10 rounded-xl bg-white dark:bg-background-dark border border-slate-100 dark:border-slate-800 glow-hover transition-all group">
<div class="flex items-center gap-6">
<div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary transition-colors">
<span class="material-icons text-primary group-hover:text-background-dark">speed</span>
</div>
<div>
<h4 class="text-2xl font-bold mb-2">Performance</h4>
<p class="text-slate-600 dark:text-slate-400">Millisecond execution speeds giving you the edge in a volatile, fast-moving market.</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Technology Infrastructure -->
<section class="py-24 bg-slate-900 text-white overflow-hidden">
<div class="max-w-7xl mx-auto px-6">
<div class="flex flex-col lg:flex-row gap-20 items-center">
<div class="lg:w-1/2">
<h2 class="text-4xl font-bold mb-8">Uncompromising <br/><span class="text-primary">Infrastructure</span></h2>
<div class="space-y-8">
<div class="flex gap-4">
<div class="flex-shrink-0 mt-1"><span class="material-icons text-primary">hub</span></div>
<div>
<h5 class="text-xl font-bold mb-2">Multi-Layer Security Architecture</h5>
<p class="text-slate-400">From cold-storage vaults to hardware security modules (HSMs), our stack is built for absolute resilience.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 mt-1"><span class="material-icons text-primary">cloud_done</span></div>
<div>
<h5 class="text-xl font-bold mb-2">Cloud-Native Scalability</h5>
<p class="text-slate-400">Distributed across 14 global data centers with redundant backups to ensure 0ms downtime during peaks.</p>
</div>
</div>
</div>
</div>
<div class="lg:w-1/2 relative">
<!-- Technical Diagram Elements -->
<div class="border border-white/10 rounded-xl p-8 bg-black/40 relative z-10 backdrop-blur-sm">
<div class="grid grid-cols-3 gap-4 mb-8">
<div class="h-20 border border-primary/30 rounded flex items-center justify-center flex-col">
<span class="text-[10px] uppercase opacity-50 mb-1 tracking-widest">Node A</span>
<span class="text-primary font-bold">ACTIVE</span>
</div>
<div class="h-20 border border-primary/30 rounded flex items-center justify-center flex-col">
<span class="text-[10px] uppercase opacity-50 mb-1 tracking-widest">Node B</span>
<span class="text-primary font-bold">ACTIVE</span>
</div>
<div class="h-20 border border-white/10 rounded flex items-center justify-center flex-col opacity-40">
<span class="text-[10px] uppercase mb-1 tracking-widest">Node C</span>
<span>STANDBY</span>
</div>
</div>
<div class="space-y-4">
<div class="w-full bg-white/5 h-2 rounded overflow-hidden">
<div class="bg-primary h-full w-[85%]"></div>
</div>
<div class="flex justify-between text-xs opacity-60">
<span>Encrypted Traffic Flow</span>
<span>85% Capacity</span>
</div>
</div>
<div class="mt-8 pt-8 border-t border-white/10 flex justify-center">
<div class="relative w-32 h-32 rounded-full border-4 border-dashed border-primary/20 flex items-center justify-center">
<span class="material-icons text-primary text-4xl">lock</span>
</div>
</div>
</div>
<!-- Decorative back elements -->
<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-primary/10 blur-[100px] rounded-full"></div>
</div>
</div>
</div>
</section>
<!-- Message & Video Section -->
<section class="py-32">
<div class="max-w-7xl mx-auto px-6">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
<div class="lg:col-span-5 flex flex-col justify-center">
<span class="material-icons text-primary text-6xl mb-6 opacity-30">format_quote</span>
<h3 class="text-3xl font-bold mb-8 italic leading-relaxed">
                        "In a world of noise, we provide the clarity of computation. Our goal is to build a bridge to the future of finance."
                    </h3>
<div class="mb-10">
<p class="text-xl font-bold">Martin Harris</p>
<p class="text-slate-500">Founder &amp; Chief Executive Officer</p>
</div>
<div class="w-48 h-12 bg-primary/5 rounded border border-primary/10 flex items-center justify-center">
<span class="text-2xl font-display text-primary/80" style="font-family: 'Dancing Script', cursive; font-style: italic;">Martin Harris</span>
</div>
</div>
<div class="lg:col-span-7 lg:max-w-xl">
<?php $headerImage = get_site_setting('header_image', '/bloombit.jpg'); ?>
<img class="w-full aspect-square object-cover rounded-xl shadow-2xl border border-white/10" alt="<?php echo htmlspecialchars($siteName); ?>" src="<?php echo htmlspecialchars($headerImage); ?>" onerror="this.src='/bloombit.jpg'"/>
</div>
</div>
</div>
</section>
<!-- Global Reach Stats -->
<section class="py-20 border-t border-slate-100 dark:border-slate-800">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
<div class="text-center">
<p class="text-4xl font-bold mb-2">42</p>
<p class="text-slate-500 text-sm uppercase tracking-widest">Countries Supported</p>
</div>
<div class="text-center">
<p class="text-4xl font-bold mb-2">$14B+</p>
<p class="text-slate-500 text-sm uppercase tracking-widest">Assets Traded</p>
</div>
<div class="text-center">
<p class="text-4xl font-bold mb-2">99.9%</p>
<p class="text-slate-500 text-sm uppercase tracking-widest">Platform Uptime</p>
</div>
<div class="text-center">
<p class="text-4xl font-bold mb-2">24/7</p>
<p class="text-slate-500 text-sm uppercase tracking-widest">Expert Support</p>
</div>
</div>
</section>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body></html>
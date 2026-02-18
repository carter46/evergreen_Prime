<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); $contactEmail = get_site_setting('contact_email', 'support@bloombit.com'); ?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Contact &amp; Support | <?php echo htmlspecialchars($siteName); ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
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
        }
        .bg-mesh {
            background-color: #f8f8f5;
            background-image: radial-gradient(at 0% 0%, rgba(249, 189, 11, 0.05) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(249, 189, 11, 0.03) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300 overflow-x-hidden">
<?php $currentPage = 'help_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<!-- Hero Section -->
<header class="pt-16 pb-12 text-center bg-mesh">
<div class="max-w-3xl mx-auto px-4">
<span class="px-4 py-1.5 bg-primary/10 text-primary text-sm font-bold rounded-full uppercase tracking-wider">Support Center</span>
<h1 class="mt-6 text-5xl md:text-6xl font-bold text-slate-900 dark:text-white">Get in Touch</h1>
<p class="mt-4 text-xl text-slate-600 dark:text-slate-400 leading-relaxed">
                Our global team of crypto experts is here 24/7 to ensure your investments are always on track.
            </p>
</div>
</header>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
<!-- Contact Grid -->
<div class="grid lg:grid-cols-2 gap-12 mt-12">
<!-- Left: Contact Form -->
<div class="bg-white dark:bg-zinc-900/50 p-8 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-white/5">
<h2 class="text-2xl font-bold mb-6">Send us a message</h2>
<form id="contact-form" class="space-y-6">
<div class="grid md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Full Name</label>
<input name="name" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-zinc-800 bg-transparent focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all" placeholder="John Doe" type="text" required/>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Email Address</label>
<input name="email" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-zinc-800 bg-transparent focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all" placeholder="john@example.com" type="email" required/>
</div>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Subject</label>
<select name="subject" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-zinc-800 bg-transparent focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all appearance-none" required>
<option value="Account Access">Account Access</option>
<option value="Investment Inquiry">Investment Inquiry</option>
<option value="Technical Issue">Technical Issue</option>
<option value="Verification (KYC)">Verification (KYC)</option>
<option value="Other">Other</option>
</select>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Message</label>
<textarea name="message" class="w-full px-4 py-3 rounded-lg border border-slate-200 dark:border-zinc-800 bg-transparent focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none" placeholder="How can we help you?" rows="5" required></textarea>
</div>
<div id="contact-form-message" class="text-sm hidden"></div>
<button type="submit" class="w-full py-4 bg-primary text-white font-bold rounded-lg shadow-lg shadow-primary/30 hover:shadow-primary/40 transition-all flex items-center justify-center gap-2">
<span>Send Message</span>
<span class="material-icons-outlined">send</span>
</button>
</form>
</div>
<!-- Right: Contact Cards & Map -->
<div class="flex flex-col gap-6">
<!-- Info Cards -->
<div class="grid sm:grid-cols-2 gap-6">
<div class="bg-white dark:bg-zinc-900/50 p-6 rounded-2xl border border-slate-100 dark:border-white/5 hover:border-primary/50 transition-colors group">
<div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4 text-primary group-hover:bg-primary group-hover:text-white transition-all">
<span class="material-icons-outlined">alternate_email</span>
</div>
<h3 class="font-bold text-lg">Email Support</h3>
<p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Response within 2 hours</p>
<p class="mt-4 font-medium text-primary"><?php echo htmlspecialchars($contactEmail); ?></p>
</div>
<div class="bg-white dark:bg-zinc-900/50 p-6 rounded-2xl border border-slate-100 dark:border-white/5 hover:border-primary/50 transition-colors group">
<div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4 text-primary group-hover:bg-primary group-hover:text-white transition-all">
<span class="material-icons-outlined">chat</span>
</div>
<div class="flex items-center gap-2 mb-1">
<h3 class="font-bold text-lg">Live Chat</h3>
<span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
</div>
<p class="text-slate-500 dark:text-slate-400 text-sm">Status: Available Now</p>
<button class="mt-4 font-medium text-primary text-left">Start a conversation →</button>
</div>
</div>
<!-- Office Card with Map Background -->
<div class="relative bg-white dark:bg-zinc-900/50 rounded-2xl border border-slate-100 dark:border-white/5 overflow-hidden flex-grow group">
<div class="absolute inset-0 grayscale opacity-20 group-hover:opacity-40 transition-opacity">
<img alt="Map background" class="w-full h-full object-cover" data-alt="Monochrome minimalist city map pattern" data-location="London" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBaCT97QHY8jczDdMpk-_coaOuIUFij91kNWDyR-yPWqW1qvRuVWCxS8N4cX_WqBwjOlzvrPF8r7MZ6YvqpgjETnrThpa3eTlP5-LMg7NP8-aiHtBkQOTUlp9CsH8HuvOK3qtM0x8a0DS0gSXBjPv4xvTzYuLq_n8pWzxr5s4o1MzQI9SddnbbetV3JLGBcN6fFaeURdk1gXf_6ZLYKSflKAI1gqEPTPR8pNqd-lIW4DAP6_6ga1FPktTYaJz4XhIEPSXi67RxbwE"/>
</div>
<div class="relative z-10 p-8 h-full flex flex-col justify-end">
<div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm p-6 rounded-xl border border-white/20 shadow-xl max-w-xs">
<div class="flex items-center gap-2 text-primary mb-2">
<span class="material-icons-outlined">location_on</span>
<span class="font-bold text-xs uppercase tracking-tighter">Global Headquarters</span>
</div>
<h3 class="font-bold text-xl mb-1">London Office</h3>
<p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                40 Bank Street, Canary Wharf<br/>
                                London, E14 5NR<br/>
                                United Kingdom
                            </p>
</div>
</div>
</div>
</div>
</div>
<!-- FAQ Section -->
<section class="mt-32">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-12">
<div class="max-w-xl">
<h2 class="text-4xl font-bold">Frequently Asked Questions</h2>
<p class="text-slate-600 dark:text-slate-400 mt-4">Everything you need to know about the Bloombit platform and our AI investment strategies.</p>
</div>
<div class="relative w-full md:w-96">
<span class="material-icons-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
<input class="w-full pl-12 pr-4 py-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all shadow-sm" placeholder="Search questions..." type="text"/>
</div>
</div>
<div class="grid lg:grid-cols-4 gap-8">
<!-- FAQ Categories Sidebar -->
<aside class="space-y-2">
<button class="w-full flex items-center justify-between p-4 rounded-lg bg-primary text-white font-bold transition-all">
<span>General</span>
<span class="material-icons-outlined">info</span>
</button>
<button class="w-full flex items-center justify-between p-4 rounded-lg hover:bg-white dark:hover:bg-zinc-900 font-medium text-slate-600 dark:text-slate-400 transition-all">
<span>Investments</span>
<span class="material-icons-outlined">trending_up</span>
</button>
<button class="w-full flex items-center justify-between p-4 rounded-lg hover:bg-white dark:hover:bg-zinc-900 font-medium text-slate-600 dark:text-slate-400 transition-all">
<span>Security</span>
<span class="material-icons-outlined">verified_user</span>
</button>
<button class="w-full flex items-center justify-between p-4 rounded-lg hover:bg-white dark:hover:bg-zinc-900 font-medium text-slate-600 dark:text-slate-400 transition-all">
<span>Withdrawals</span>
<span class="material-icons-outlined">payments</span>
</button>
</aside>
<!-- FAQ Accordions -->
<div class="lg:col-span-3 space-y-4">
<!-- Item 1 -->
<div class="bg-white dark:bg-zinc-900/50 rounded-xl border border-slate-100 dark:border-white/5 overflow-hidden">
<button class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
<span class="font-bold text-lg">What exactly is Bloombit and how does the AI work?</span>
<span class="material-icons-outlined text-primary">expand_more</span>
</button>
<div class="px-6 pb-6 text-slate-600 dark:text-slate-400">
                            Bloombit is a next-generation fintech platform that leverages proprietary AI algorithms to analyze crypto market volatility in real-time. Our system executes high-frequency trades across multiple liquidity pools to ensure optimal returns for our institutional and retail investors.
                        </div>
</div>
<!-- Item 2 -->
<div class="bg-white dark:bg-zinc-900/50 rounded-xl border border-slate-100 dark:border-white/5 overflow-hidden">
<button class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
<span class="font-bold text-lg">Are my funds insured against market volatility?</span>
<span class="material-icons-outlined text-primary">expand_more</span>
</button>
<div class="hidden px-6 pb-6 text-slate-600 dark:text-slate-400">
                            While all investments carry risk, Bloombit employs an automated hedging strategy and maintains a reserve fund (BloomSafe) to mitigate extreme market events and protect user principal.
                        </div>
</div>
<!-- Item 3 -->
<div class="bg-white dark:bg-zinc-900/50 rounded-xl border border-slate-100 dark:border-white/5 overflow-hidden">
<button class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
<span class="font-bold text-lg">What is the minimum investment required?</span>
<span class="material-icons-outlined text-primary">expand_more</span>
</button>
<div class="hidden px-6 pb-6 text-slate-600 dark:text-slate-400">
                            You can start investing with as little as $500. Our platform is designed to be accessible while providing institutional-grade tools for everyone.
                        </div>
</div>
<!-- Item 4 -->
<div class="bg-white dark:bg-zinc-900/50 rounded-xl border border-slate-100 dark:border-white/5 overflow-hidden">
<button class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors">
<span class="font-bold text-lg">How are my private keys and data secured?</span>
<span class="material-icons-outlined text-primary">expand_more</span>
</button>
<div class="hidden px-6 pb-6 text-slate-600 dark:text-slate-400">
                            We use multi-sig cold storage for 98% of all funds and military-grade AES-256 encryption for all user data. We never store your full private keys on our servers.
                        </div>
</div>
</div>
</div>
</section>
<!-- Floating-style CTA Card -->
<section class="mt-24">
<div class="bg-slate-900 dark:bg-primary/10 rounded-3xl p-8 md:p-12 relative overflow-hidden group">
<div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
<div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
<div class="max-w-xl text-center md:text-left">
<h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Still have questions?</h2>
<p class="text-slate-400 text-lg">If you couldn't find what you were looking for, our live agents are standing by to assist you in real-time.</p>
</div>
<div class="flex flex-col sm:flex-row gap-4 shrink-0">
<button class="px-8 py-4 bg-primary text-white font-bold rounded-xl flex items-center justify-center gap-2 hover:scale-[1.05] transition-transform">
<span class="material-icons-outlined">forum</span>
                            Start Live Chat
                        </button>
<button class="px-8 py-4 bg-white/10 text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            View Documentation
                        </button>
</div>
</div>
</div>
</section>
</main>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<script src="/js/app.js"></script>
</body></html>
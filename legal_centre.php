<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); $contactEmail = get_site_setting('contact_email', 'legal@example.com'); ?>
<!DOCTYPE html>

<html class="scroll-smooth" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> Legal Center | Terms &amp; Privacy</title>
<?php output_favicon_tags(); ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
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
                        "display": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 antialiased overflow-x-hidden">
<?php $currentPage = 'legal_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex justify-end gap-2 border-b border-primary/10 bg-white/80 dark:bg-background-dark/80">
<button class="hidden md:flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 hover:text-primary dark:text-slate-300 transition-colors" onclick="window.print()">
<span class="material-icons text-lg">print</span>Print</button>
<button class="flex items-center gap-2 px-5 py-2.5 bg-primary text-slate-900 text-sm font-bold rounded-lg hover:bg-opacity-90 transition-all shadow-sm" onclick="window.print()">
<span class="material-icons text-lg">download</span>Download PDF</button>
</div>
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
<div class="flex flex-col lg:flex-row gap-12">
<!-- Sidebar Navigation -->
<aside class="lg:w-64 flex-shrink-0">
<nav class="sticky top-28 space-y-8">
<div>
<h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Terms of Service</h3>
<ul class="space-y-3">
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors active-link border-l-2 border-transparent hover:border-primary pl-3" href="#acceptance">Acceptance of Terms</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#risk-disclosure">Performance Commitment</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#ai-disclaimer">AI Trading Disclaimer</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#responsibilities">Account Responsibilities</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#anti-fraud">Anti-Fraud Policy</a></li>
</ul>
</div>
<div>
<h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Privacy Policy</h3>
<ul class="space-y-3">
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#collection">Information Collection</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#cookies">Cookies Policy</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#protection">Data Protection</a></li>
<li><a class="group flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary pl-3" href="#rights">User Rights</a></li>
</ul>
</div>
<div class="pt-6 border-t border-slate-200 dark:border-slate-800">
<div class="p-4 bg-primary/10 rounded-xl">
<p class="text-xs text-slate-600 dark:text-slate-400 mb-2 font-medium">Need legal assistance?</p>
<a class="text-sm font-bold text-slate-900 dark:text-white hover:underline flex items-center gap-1" href="mailto:<?php echo htmlspecialchars($contactEmail); ?>">
                                <?php echo htmlspecialchars($contactEmail); ?>
                                <span class="material-icons text-xs">arrow_forward</span>
</a>
</div>
</div>
</nav>
</aside>
<!-- Main Content Area -->
<article class="flex-1 bg-white dark:bg-background-dark/40 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-8 lg:p-12 overflow-hidden">
<!-- Page Header Info -->
<div class="mb-12 border-b border-slate-100 dark:border-slate-800 pb-8">
<h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Legal Center</h1>
<div class="flex items-center gap-4 text-sm text-slate-500">
<span class="bg-primary/20 text-slate-700 dark:text-slate-300 px-3 py-1 rounded-full font-medium">Last Updated: October 24, 2023</span>
<span>•</span>
<span>Effective Date: January 1, 2024</span>
</div>
</div>
<!-- Terms of Service Section -->
<section class="space-y-12" id="terms">
<div class="prose prose-slate max-w-none dark:prose-invert">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
<span class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center text-sm">01</span>
                            Terms of Service
                        </h2>
<div class="mt-8" id="acceptance">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">1. Acceptance of Terms</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                                By accessing or using the <?php echo htmlspecialchars($siteName); ?> platform, including our website, mobile application, and AI trading services, you agree to be bound by these Terms of Service. If you do not agree to these terms, you must immediately cease all use of our services. These terms constitute a legally binding agreement between you and <?php echo htmlspecialchars($siteName); ?> Global Ltd.
                            </p>
</div>
<div class="mt-8 p-6 bg-amber-50 dark:bg-amber-950/20 border-l-4 border-primary rounded-r-xl" id="risk-disclosure">
<h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
<span class="material-icons text-primary">warning</span>
                                2. Performance Commitment
                            </h3>
<p class="text-slate-700 dark:text-slate-300 leading-relaxed mb-4 italic">
                                <?php echo htmlspecialchars($siteName); ?> operates with precision-focused AI systems engineered for 99.9% performance accuracy across all market cycles. Our platform is optimized for consistency, stability, and strong results for every user.
                            </p>
<ul class="list-disc ml-6 space-y-2 text-slate-600 dark:text-slate-400 text-sm">
<li>Advanced machine intelligence executes high-confidence entries and exits around the clock.</li>
<li>Continuous model retraining keeps strategy performance aligned with live market conditions.</li>
<li>Enterprise-grade monitoring maintains dependable uptime and smooth execution.</li>
</ul>
</div>
<div class="mt-8" id="ai-disclaimer">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">3. AI Trading Disclaimer</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                Our platform utilizes proprietary artificial intelligence and machine learning algorithms to execute trades with exceptional precision. The system continuously adapts in real time, delivering a smooth and reliable automated trading experience for our users.
                            </p>
</div>
<div class="mt-8" id="responsibilities">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">4. Account Responsibilities</h3>
<div class="grid md:grid-cols-2 gap-6 mt-4">
<div class="p-5 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-900/50">
<h4 class="font-bold mb-2 text-sm text-slate-900 dark:text-white">Security</h4>
<p class="text-xs text-slate-500">Users are responsible for maintaining the confidentiality of their login credentials and 2FA secrets.</p>
</div>
<div class="p-5 border border-slate-100 dark:border-slate-800 rounded-xl bg-slate-50/50 dark:bg-slate-900/50">
<h4 class="font-bold mb-2 text-sm text-slate-900 dark:text-white">Verification</h4>
<p class="text-xs text-slate-500">Users must provide accurate KYC/AML information. Failure to do so may result in account suspension.</p>
</div>
</div>
</div>
<div class="mt-8" id="anti-fraud">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">5. Anti-Fraud Policy</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                <?php echo htmlspecialchars($siteName); ?> maintains a zero-tolerance policy towards fraudulent activities, including market manipulation, money laundering, and unauthorized access. We cooperate fully with international law enforcement agencies and financial regulators.
                            </p>
</div>
</div>
</section>
<div class="my-16 h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-800 to-transparent"></div>
<!-- Privacy Policy Section -->
<section class="space-y-12" id="privacy">
<div class="prose prose-slate max-w-none dark:prose-invert">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
<span class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center text-sm">02</span>
                            Privacy Policy
                        </h2>
<div class="mt-8" id="collection">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">1. Information Collection</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                                We collect information necessary to provide and secure our services, including:
                            </p>
<ol class="list-decimal ml-6 space-y-3 text-slate-600 dark:text-slate-400">
<li><strong>Identity Data:</strong> Full name, date of birth, and government-issued ID for KYC compliance.</li>
<li><strong>Financial Data:</strong> Wallet addresses, transaction history, and funding sources.</li>
<li><strong>Technical Data:</strong> IP address, browser type, and device information for security monitoring.</li>
</ol>
</div>
<div class="mt-8" id="cookies">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">2. Cookies &amp; Tracking</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                                We use essential cookies to maintain user sessions and security. Analytical cookies help us understand platform usage to improve our AI models. You can manage your cookie preferences through your browser settings.
                            </p>
</div>
<div class="mt-8" id="protection">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">3. Data Protection Measures</h3>
<div class="flex flex-col md:flex-row gap-4 mt-4">
<div class="flex-1 p-4 bg-primary/5 rounded-lg border border-primary/10">
<div class="material-icons text-primary mb-2">lock</div>
<div class="text-sm font-bold mb-1">AES-256 Encryption</div>
<p class="text-xs text-slate-500">All sensitive data is encrypted at rest and in transit.</p>
</div>
<div class="flex-1 p-4 bg-primary/5 rounded-lg border border-primary/10">
<div class="material-icons text-primary mb-2">security</div>
<div class="text-sm font-bold mb-1">Cold Storage</div>
<p class="text-xs text-slate-500">98% of digital assets are stored in offline air-gapped vaults.</p>
</div>
<div class="flex-1 p-4 bg-primary/5 rounded-lg border border-primary/10">
<div class="material-icons text-primary mb-2">visibility_off</div>
<div class="text-sm font-bold mb-1">Anonymization</div>
<p class="text-xs text-slate-500">Data used for AI training is stripped of personally identifiable info.</p>
</div>
</div>
</div>
<div class="mt-8" id="rights">
<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-3">4. User Rights</h3>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                                In accordance with GDPR and CCPA, users have the following rights regarding their data:
                            </p>
<ul class="space-y-3">
<li class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-400">
<span class="material-icons text-primary text-sm mt-1">check_circle</span>
                                    Right to Access: Request a copy of all personal data held by <?php echo htmlspecialchars($siteName); ?>.
                                </li>
<li class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-400">
<span class="material-icons text-primary text-sm mt-1">check_circle</span>
                                    Right to Rectification: Correct any inaccurate or incomplete information.
                                </li>
<li class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-400">
<span class="material-icons text-primary text-sm mt-1">check_circle</span>
                                    Right to Erasure: Request deletion of data where legal obligations allow.
                                </li>
<li class="flex items-start gap-3 text-sm text-slate-600 dark:text-slate-400">
<span class="material-icons text-primary text-sm mt-1">check_circle</span>
                                    Right to Portability: Transfer data to another service provider.
                                </li>
</ul>
</div>
</div>
</section>
<!-- Footer Summary -->
<div class="mt-20 pt-8 border-t border-slate-100 dark:border-slate-800">
<div class="bg-slate-50 dark:bg-slate-900/30 p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6">
<div>
<h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Still have questions?</h4>
<p class="text-slate-500 text-sm">Our legal team is here to help you understand your rights and obligations.</p>
</div>
<div class="flex gap-4">
<a href="/help_centre" class="px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Help Center</a>
<a href="/help_centre" class="px-6 py-2.5 bg-primary text-slate-900 text-sm font-bold rounded-lg hover:shadow-lg transition-all">Contact Legal</a>
</div>
</div>
</div>
</article>
</div>
</main>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<!-- Floating Back to Top -->
<a class="fixed bottom-8 right-8 w-12 h-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center shadow-lg text-slate-500 hover:text-primary transition-all z-40 group" href="#">
<span class="material-icons group-hover:-translate-y-1 transition-transform">expand_less</span>
</a>
<style>
        .active-link {
            color: #f9bd0b !important;
            border-color: #f9bd0b !important;
        }
        
        @media print {
            aside, header, footer, .floating-btn {
                display: none !important;
            }
            main {
                max-width: 100% !important;
                padding: 0 !important;
            }
            article {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</body></html>
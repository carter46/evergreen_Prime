<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); $contactEmail = get_site_setting('contact_email', 'legal@example.com'); ?>
<!DOCTYPE html>
<html class="dark scroll-smooth" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php
$pageTitle = $siteName . ' Legal Center | Terms & Privacy';
require_once __DIR__ . '/includes/marketing-head.php';
?>
<style>
.legal-hero-mesh {
  background-color: #0b0e11;
  background-image: radial-gradient(at 0% 0%, rgba(255, 195, 92, 0.08) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(255, 195, 92, 0.05) 0px, transparent 50%);
}
.legal-sidebar-link {
  border-left: 2px solid transparent;
  padding-left: 0.75rem;
  color: #b1b5bd;
  font-size: 0.875rem;
  font-weight: 500;
  transition: color 0.2s, border-color 0.2s;
}
.legal-sidebar-link:hover {
  color: #ffc35c;
  border-color: #ffc35c;
}
.legal-anchor {
  scroll-margin-top: 6.5rem;
}
.legal-card {
  background: #1d2023;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 1rem;
}
@media print {
  aside, #marketing-nav, footer, .legal-back-top { display: none !important; }
  main { max-width: 100% !important; padding: 0 !important; }
  article { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>
</head>
<body class="marketing-page font-body-md text-body-md overflow-x-hidden">
<?php $currentPage = 'legal_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<header class="legal-hero-mesh pt-28 pb-12 text-center border-b border-white/5">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<span class="px-4 py-1.5 bg-primary-container/10 text-primary-container text-sm font-bold rounded-full uppercase tracking-wider">Compliance</span>
<h1 class="mt-6 font-headline-lg text-headline-lg md:text-5xl font-bold text-on-surface">Legal Center</h1>
<p class="mt-4 text-on-secondary-container text-body-lg max-w-2xl mx-auto">Terms of Service, Privacy Policy, risk disclosures, and regulatory information for <?php echo htmlspecialchars($siteName); ?>.</p>
<div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-sm text-on-secondary-container">
<span class="bg-surface-container-high text-on-surface px-3 py-1 rounded-full font-medium">Last Updated: October 24, 2023</span>
<span class="hidden sm:inline">•</span>
<span>Effective Date: January 1, 2024</span>
</div>
<div class="mt-8 flex justify-center gap-3">
<button type="button" class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-on-secondary-container hover:text-primary-container border border-border-low rounded-lg transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-lg">print</span>Print</button>
<button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-container text-surface-container-lowest text-sm font-bold rounded-lg hover:opacity-90 transition-all" onclick="window.print()">
<span class="material-symbols-outlined text-lg">download</span>Download PDF</button>
</div>
</div>
</header>

<main class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop py-12 md:py-16">
<div class="flex flex-col lg:flex-row gap-10 lg:gap-12">
<aside class="lg:w-64 shrink-0">
<nav class="sticky top-28 space-y-8 legal-card p-5">
<div>
<h3 class="text-xs font-bold text-primary-container uppercase tracking-widest mb-4">Terms of Service</h3>
<ul class="space-y-3">
<li><a class="legal-sidebar-link block" href="#acceptance">Acceptance of Terms</a></li>
<li><a class="legal-sidebar-link block" href="#risk-disclosure">Risk Disclosure</a></li>
<li><a class="legal-sidebar-link block" href="#ai-disclaimer">AI Trading Disclaimer</a></li>
<li><a class="legal-sidebar-link block" href="#responsibilities">Account Responsibilities</a></li>
<li><a class="legal-sidebar-link block" href="#regulatory-info">Regulatory Info</a></li>
</ul>
</div>
<div>
<h3 class="text-xs font-bold text-primary-container uppercase tracking-widest mb-4">Privacy Policy</h3>
<ul class="space-y-3">
<li><a class="legal-sidebar-link block" href="#collection">Information Collection</a></li>
<li><a class="legal-sidebar-link block" href="#cookies">Cookies Policy</a></li>
<li><a class="legal-sidebar-link block" href="#protection">Data Protection</a></li>
<li><a class="legal-sidebar-link block" href="#rights">User Rights</a></li>
</ul>
</div>
<div class="pt-4 border-t border-border-low">
<div class="p-4 bg-primary-container/10 rounded-xl border border-primary-container/20">
<p class="text-xs text-on-secondary-container mb-2 font-medium">Need legal assistance?</p>
<a class="text-sm font-bold text-on-surface hover:text-primary-container flex items-center gap-1 transition-colors" href="mailto:<?php echo htmlspecialchars($contactEmail); ?>">
<?php echo htmlspecialchars($contactEmail); ?>
<span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
</div>
</nav>
</aside>

<article class="flex-1 legal-card p-6 md:p-10 lg:p-12">
<section class="space-y-12 legal-anchor" id="terms">
<h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-3">
<span class="w-8 h-8 rounded-full bg-primary-container/20 text-primary-container flex items-center justify-center text-sm font-bold">01</span>
Terms of Service
</h2>
<div class="mt-8 legal-anchor" id="acceptance">
<h3 class="text-lg font-semibold text-on-surface mb-3">1. Acceptance of Terms</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
By accessing or using the <?php echo htmlspecialchars($siteName); ?> platform, including our website, mobile application, and AI trading services, you agree to be bound by these Terms of Service. If you do not agree to these terms, you must immediately cease all use of our services. These terms constitute a legally binding agreement between you and <?php echo htmlspecialchars($siteName); ?> Global Ltd.
</p>
</div>
<div class="mt-8 p-6 bg-primary-container/5 border-l-4 border-primary-container rounded-r-xl legal-anchor" id="risk-disclosure">
<h3 class="text-lg font-semibold text-on-surface mb-3 flex items-center gap-2">
<span class="material-symbols-outlined text-primary-container">warning</span>
2. Risk Disclosure
</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
Trading currencies, digital assets, and other financial instruments involves substantial risk of loss. Past performance is not indicative of future results. You should only invest capital you can afford to lose. <?php echo htmlspecialchars($siteName); ?> does not guarantee profits or specific investment outcomes.
</p>
<ul class="list-disc ml-6 space-y-2 text-on-secondary-container text-sm">
<li>Market volatility can result in rapid and significant losses.</li>
<li>AI-assisted tools are for informational purposes and do not eliminate risk.</li>
<li>You are solely responsible for your trading and investment decisions.</li>
</ul>
</div>
<div class="mt-8 legal-anchor" id="ai-disclaimer">
<h3 class="text-lg font-semibold text-on-surface mb-3">3. AI Trading Disclaimer</h3>
<p class="text-on-secondary-container leading-relaxed">
Our platform utilizes proprietary artificial intelligence and machine learning algorithms to support trading analysis. The system continuously adapts in real time, but automated insights do not constitute financial advice and should not be relied upon as the sole basis for investment decisions.
</p>
</div>
<div class="mt-8 legal-anchor" id="responsibilities">
<h3 class="text-lg font-semibold text-on-surface mb-3">4. Account Responsibilities</h3>
<div class="grid md:grid-cols-2 gap-6 mt-4">
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Security</h4>
<p class="text-xs text-on-secondary-container">Users are responsible for maintaining the confidentiality of their login credentials and 2FA secrets.</p>
</div>
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Verification</h4>
<p class="text-xs text-on-secondary-container">Users must provide accurate KYC/AML information. Failure to do so may result in account suspension.</p>
</div>
</div>
</div>
<div class="mt-8 legal-anchor" id="regulatory-info">
<h3 class="text-lg font-semibold text-on-surface mb-3">5. Regulatory Information &amp; Anti-Fraud Policy</h3>
<p class="text-on-secondary-container leading-relaxed">
<?php echo htmlspecialchars($siteName); ?> maintains a zero-tolerance policy towards fraudulent activities, including market manipulation, money laundering, and unauthorized access. We cooperate fully with international law enforcement agencies and financial regulators where applicable.
</p>
</div>
</section>

<div class="my-16 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

<section class="space-y-12 legal-anchor" id="privacy">
<h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-3">
<span class="w-8 h-8 rounded-full bg-primary-container/20 text-primary-container flex items-center justify-center text-sm font-bold">02</span>
Privacy Policy
</h2>
<div class="mt-8 legal-anchor" id="collection">
<h3 class="text-lg font-semibold text-on-surface mb-3">1. Information Collection</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">We collect information necessary to provide and secure our services, including:</p>
<ol class="list-decimal ml-6 space-y-3 text-on-secondary-container">
<li><strong class="text-on-surface">Identity Data:</strong> Full name, date of birth, and government-issued ID for KYC compliance.</li>
<li><strong class="text-on-surface">Financial Data:</strong> Wallet addresses, transaction history, and funding sources.</li>
<li><strong class="text-on-surface">Technical Data:</strong> IP address, browser type, and device information for security monitoring.</li>
</ol>
</div>
<div class="mt-8 legal-anchor" id="cookies">
<h3 class="text-lg font-semibold text-on-surface mb-3">2. Cookies &amp; Tracking</h3>
<p class="text-on-secondary-container leading-relaxed">
We use essential cookies to maintain user sessions and security. Analytical cookies help us understand platform usage to improve our services. You can manage your cookie preferences through your browser settings.
</p>
</div>
<div class="mt-8 legal-anchor" id="protection">
<h3 class="text-lg font-semibold text-on-surface mb-3">3. Data Protection &amp; Security</h3>
<div class="flex flex-col md:flex-row gap-4 mt-4">
<div class="flex-1 p-4 bg-primary-container/5 rounded-lg border border-primary-container/10">
<div class="material-symbols-outlined text-primary-container mb-2">lock</div>
<div class="text-sm font-bold mb-1 text-on-surface">AES-256 Encryption</div>
<p class="text-xs text-on-secondary-container">All sensitive data is encrypted at rest and in transit.</p>
</div>
<div class="flex-1 p-4 bg-primary-container/5 rounded-lg border border-primary-container/10">
<div class="material-symbols-outlined text-primary-container mb-2">security</div>
<div class="text-sm font-bold mb-1 text-on-surface">Cold Storage</div>
<p class="text-xs text-on-secondary-container">Digital assets are stored using institutional-grade custody practices.</p>
</div>
<div class="flex-1 p-4 bg-primary-container/5 rounded-lg border border-primary-container/10">
<div class="material-symbols-outlined text-primary-container mb-2">visibility_off</div>
<div class="text-sm font-bold mb-1 text-on-surface">Anonymization</div>
<p class="text-xs text-on-secondary-container">Data used for model training is stripped of personally identifiable info where applicable.</p>
</div>
</div>
</div>
<div class="mt-8 legal-anchor" id="rights">
<h3 class="text-lg font-semibold text-on-surface mb-3">4. User Rights</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">In accordance with GDPR and CCPA, users have the following rights regarding their data:</p>
<ul class="space-y-3">
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-primary-container text-sm mt-0.5">check_circle</span>Right to Access: Request a copy of all personal data held by <?php echo htmlspecialchars($siteName); ?>.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-primary-container text-sm mt-0.5">check_circle</span>Right to Rectification: Correct any inaccurate or incomplete information.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-primary-container text-sm mt-0.5">check_circle</span>Right to Erasure: Request deletion of data where legal obligations allow.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-primary-container text-sm mt-0.5">check_circle</span>Right to Portability: Transfer data to another service provider.</li>
</ul>
</div>
</section>

<div class="mt-16 pt-8 border-t border-border-low">
<div class="bg-surface-container-low p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6">
<div>
<h4 class="text-lg font-bold text-on-surface mb-2">Still have questions?</h4>
<p class="text-on-secondary-container text-sm">Our support team can help you understand your rights and obligations.</p>
</div>
<div class="flex gap-4">
<a href="/help_centre" class="px-6 py-2.5 border border-border-low text-sm font-semibold rounded-lg text-on-surface hover:bg-surface-container-high transition-colors">Help Center</a>
<a href="/live_chat" class="btn-get-started px-6 py-2.5 text-sm font-bold rounded-lg">Contact Support</a>
</div>
</div>
</div>
</article>
</div>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<a class="legal-back-top fixed bottom-8 right-8 w-12 h-12 bg-surface-container-high border border-border-low rounded-full flex items-center justify-center shadow-lg text-on-secondary-container hover:text-primary-container transition-all z-40" href="#" aria-label="Back to top">
<span class="material-symbols-outlined">expand_less</span>
</a>
</body>
</html>

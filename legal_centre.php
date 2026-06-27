<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); $contactEmail = get_site_setting('contact_email', 'legal@example.com'); ?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
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
  background-image: radial-gradient(at 0% 0%, rgba(51, 119, 34, 0.12) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(0, 120, 174, 0.08) 0px, transparent 50%);
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
  color: #8fd977;
  border-color: #337722;
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
  aside, #utility-header, #main-navigation, footer, .legal-back-top { display: none !important; }
  main { max-width: 100% !important; padding: 0 !important; }
  article { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>
</head>
<body class="marketing-page font-body-md text-body-md overflow-x-hidden">
<?php $currentPage = 'legal_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<header class="legal-hero-mesh pb-12 text-center border-b border-gray-200">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<span class="px-4 py-1.5 bg-fidelity-green/20 text-fidelity-green text-sm font-bold rounded-full uppercase tracking-wider">Compliance</span>
<h1 class="mt-6 font-headline-lg text-headline-lg md:text-5xl font-bold text-on-surface">Legal Center</h1>
<p class="mt-4 text-on-secondary-container text-body-lg max-w-2xl mx-auto">Terms of Service, Privacy Policy, and investment risk disclosures for brokerage, retirement, and wealth management services at <?php echo htmlspecialchars($siteName); ?>.</p>
<div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-sm text-on-secondary-container">
<span class="bg-surface-container-high text-on-surface px-3 py-1 rounded-full font-medium">Last Updated: June 27, 2026</span>
</div>
<div class="mt-8 flex justify-center gap-3">
<button type="button" class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-on-secondary-container hover:text-fidelity-green border border-border-low rounded-lg transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-lg">print</span>Print</button>
<button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-fidelity-green text-white text-sm font-bold rounded-lg hover:opacity-90 transition-all" onclick="window.print()">
<span class="material-symbols-outlined text-lg">download</span>Download PDF</button>
</div>
</div>
</header>

<main class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop py-12 md:py-16">
<div class="flex flex-col lg:flex-row gap-10 lg:gap-12">
<aside class="lg:w-64 shrink-0">
<nav class="sticky top-28 space-y-8 legal-card p-5">
<div>
<h3 class="text-xs font-bold text-fidelity-green uppercase tracking-widest mb-4">Terms of Service</h3>
<ul class="space-y-3">
<li><a class="legal-sidebar-link block" href="#acceptance">Acceptance of Terms</a></li>
<li><a class="legal-sidebar-link block" href="#risk-disclosure">Investment Risk Disclosure</a></li>
<li><a class="legal-sidebar-link block" href="#market-volatility">Market Volatility</a></li>
<li><a class="legal-sidebar-link block" href="#stocks-etfs">Stocks &amp; ETFs</a></li>
<li><a class="legal-sidebar-link block" href="#real-estate">Real Estate Investments</a></li>
<li><a class="legal-sidebar-link block" href="#responsibilities">Account Responsibilities</a></li>
<li><a class="legal-sidebar-link block" href="#regulatory-info">Regulatory Information</a></li>
</ul>
</div>
<div>
<h3 class="text-xs font-bold text-fidelity-green uppercase tracking-widest mb-4">Privacy Policy</h3>
<ul class="space-y-3">
<li><a class="legal-sidebar-link block" href="#collection">Information Collection</a></li>
<li><a class="legal-sidebar-link block" href="#cookies">Cookies Policy</a></li>
<li><a class="legal-sidebar-link block" href="#protection">Data Protection</a></li>
<li><a class="legal-sidebar-link block" href="#rights">User Rights</a></li>
</ul>
</div>
<div class="pt-4 border-t border-border-low">
<div class="p-4 bg-fidelity-green/10 rounded-xl border border-fidelity-green/20">
<p class="text-xs text-on-secondary-container mb-2 font-medium">Need legal assistance?</p>
<a class="text-sm font-bold text-on-surface hover:text-fidelity-green flex items-center gap-1 transition-colors" href="mailto:<?php echo htmlspecialchars($contactEmail); ?>">
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
<span class="w-8 h-8 rounded-full bg-fidelity-green/20 text-fidelity-green flex items-center justify-center text-sm font-bold">01</span>
Terms of Service
</h2>
<div class="mt-8 legal-anchor" id="acceptance">
<h3 class="text-lg font-semibold text-on-surface mb-3">1. Acceptance of Terms</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
By accessing or using the <?php echo htmlspecialchars($siteName); ?> website, brokerage accounts, retirement planning tools, investment plans, and wealth management services, you agree to these Terms of Service. If you do not agree, you must discontinue use of our platform. These terms govern your relationship with <?php echo htmlspecialchars($siteName); ?> regarding investing in stocks, exchange-traded funds (ETFs), retirement accounts, managed investment plans, and related financial services.
</p>
</div>
<div class="mt-8 p-6 bg-fidelity-green/5 border-l-4 border-fidelity-green rounded-r-xl legal-anchor" id="risk-disclosure">
<h3 class="text-lg font-semibold text-on-surface mb-3 flex items-center gap-2">
<span class="material-symbols-outlined text-fidelity-green">warning</span>
2. Investment Risk Disclosure
</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
Investing in securities—including stocks, ETFs, bonds, mutual funds, and real estate-related instruments—involves risk of loss. The value of your investments can go up or down, and you may receive back less than you originally invested. Past performance, projected returns, and historical market data are not guarantees of future results. <?php echo htmlspecialchars($siteName); ?> does not guarantee any specific investment outcome, yield, or profit.
</p>
<ul class="list-disc ml-6 space-y-2 text-on-secondary-container text-sm">
<li>You should only invest money you can afford to lose without compromising essential living expenses.</li>
<li>Investment plans, retirement projections, and calculators provide estimates only—not promises of performance.</li>
<li><?php echo htmlspecialchars($siteName); ?> is not a tax advisor. Consult a qualified tax professional regarding IRA, Roth, and brokerage tax treatment.</li>
<li>Nothing on this platform constitutes personalized financial, legal, or investment advice unless provided under a separate advisory agreement.</li>
</ul>
</div>
<div class="mt-8 legal-anchor" id="market-volatility">
<h3 class="text-lg font-semibold text-on-surface mb-3">3. Market Volatility &amp; Unpredictable Movement</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
Financial markets are inherently unpredictable. Stock prices, interest rates, inflation, geopolitical events, corporate earnings, and regulatory changes can cause rapid and significant price swings—sometimes within a single trading session. Periods of high volatility may occur without warning. You acknowledge that:
</p>
<ul class="list-disc ml-6 space-y-2 text-on-secondary-container text-sm">
<li>Market downturns, corrections, and bear markets are normal parts of long-term investing.</li>
<li>Liquidity in certain securities or real estate investments may be limited during stressed market conditions.</li>
<li>Automated tools, research, and platform features do not eliminate market risk or ensure profitable timing.</li>
<li>Decisions to buy, sell, or hold investments remain your responsibility unless you have a written advisory relationship with us.</li>
</ul>
</div>
<div class="mt-8 legal-anchor" id="stocks-etfs">
<h3 class="text-lg font-semibold text-on-surface mb-3">4. Stocks, ETFs &amp; Brokerage Products</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
When you trade or hold stocks and ETFs through <?php echo htmlspecialchars($siteName); ?>, you are subject to standard brokerage risks including company-specific failures, sector concentration, currency exposure (for international holdings), dividend changes, and ETF tracking error. Commission-free trading, if offered, does not remove market risk. Fractional share trading may have limitations on execution, corporate actions, and transferability.
</p>
<p class="text-on-secondary-container leading-relaxed">
Options and other derivatives, if available, carry additional risks including total loss of premium and amplified losses. Review product disclosures before trading complex instruments.
</p>
</div>
<div class="mt-8 legal-anchor" id="real-estate">
<h3 class="text-lg font-semibold text-on-surface mb-3">5. Real Estate &amp; Alternative Investments</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
Real estate exposure—whether through direct property, REITs, real estate investment plans, or related funds—carries risks distinct from traditional equities. Property values may decline due to local market conditions, interest rate changes, vacancy, maintenance costs, or regulatory restrictions. Real estate investments may be illiquid, meaning you may not be able to sell quickly at a desired price.
</p>
<p class="text-on-secondary-container leading-relaxed">
Any marketing materials describing real estate or alternative investment opportunities are for informational purposes. Projected income, appreciation, or yields are not assured and may differ materially from actual results.
</p>
</div>
<div class="mt-8 legal-anchor" id="responsibilities">
<h3 class="text-lg font-semibold text-on-surface mb-3">6. Account Responsibilities</h3>
<div class="grid md:grid-cols-2 gap-6 mt-4">
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Security</h4>
<p class="text-xs text-on-secondary-container">You are responsible for safeguarding login credentials, two-factor authentication devices, and account recovery information.</p>
</div>
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Accurate Information</h4>
<p class="text-xs text-on-secondary-container">You must provide truthful identity, tax, and financial information for account opening, KYC/AML compliance, and suitability assessments.</p>
</div>
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Suitability</h4>
<p class="text-xs text-on-secondary-container">You represent that you understand the risks of the investment products you select and that they align with your goals and experience.</p>
</div>
<div class="p-5 border border-border-low rounded-xl bg-surface-container-low">
<h4 class="font-bold mb-2 text-sm text-on-surface">Unauthorized Activity</h4>
<p class="text-xs text-on-secondary-container">Notify us immediately of suspected unauthorized access, fraudulent transfers, or account irregularities.</p>
</div>
</div>
</div>
<div class="mt-8 legal-anchor" id="regulatory-info">
<h3 class="text-lg font-semibold text-on-surface mb-3">7. Regulatory Information &amp; Anti-Fraud Policy</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">
<?php echo htmlspecialchars($siteName); ?> maintains policies against fraud, market manipulation, money laundering, and unauthorized account access. We may verify identity, monitor transactions, restrict accounts pending review, and cooperate with regulators and law enforcement where required by applicable law.
</p>
<p class="text-on-secondary-container leading-relaxed text-sm">
Securities products are subject to applicable federal and state regulations. Retirement accounts (including IRAs) are governed by IRS rules regarding contributions, distributions, and penalties. Nothing in these terms waives protections you may have under consumer protection or securities laws in your jurisdiction.
</p>
</div>
</section>

<div class="my-16 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

<section class="space-y-12 legal-anchor" id="privacy">
<h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-3">
<span class="w-8 h-8 rounded-full bg-fidelity-green/20 text-fidelity-green flex items-center justify-center text-sm font-bold">02</span>
Privacy Policy
</h2>
<div class="mt-8 legal-anchor" id="collection">
<h3 class="text-lg font-semibold text-on-surface mb-3">1. Information Collection</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">We collect information necessary to open accounts, process investments, and comply with financial regulations, including:</p>
<ol class="list-decimal ml-6 space-y-3 text-on-secondary-container">
<li><strong class="text-on-surface">Identity Data:</strong> Name, date of birth, government-issued ID, and contact details for KYC/AML verification.</li>
<li><strong class="text-on-surface">Financial Data:</strong> Bank account information, funding sources, investment history, portfolio holdings, and transaction records.</li>
<li><strong class="text-on-surface">Planning Data:</strong> Retirement goals, risk tolerance responses, and information you enter into calculators or planning tools.</li>
<li><strong class="text-on-surface">Technical Data:</strong> IP address, device type, browser, and session logs for security and fraud prevention.</li>
</ol>
</div>
<div class="mt-8 legal-anchor" id="cookies">
<h3 class="text-lg font-semibold text-on-surface mb-3">2. Cookies &amp; Tracking</h3>
<p class="text-on-secondary-container leading-relaxed">
We use essential cookies to maintain secure sessions and remember preferences. Analytics cookies help us understand how visitors use our investing and planning pages so we can improve the experience. You may manage cookies through your browser settings; disabling essential cookies may limit platform functionality.
</p>
</div>
<div class="mt-8 legal-anchor" id="protection">
<h3 class="text-lg font-semibold text-on-surface mb-3">3. Data Protection &amp; Security</h3>
<div class="flex flex-col md:flex-row gap-4 mt-4">
<div class="flex-1 p-4 bg-fidelity-green/5 rounded-lg border border-fidelity-green/10">
<div class="material-symbols-outlined text-fidelity-green mb-2">lock</div>
<div class="text-sm font-bold mb-1 text-on-surface">Encryption</div>
<p class="text-xs text-on-secondary-container">Sensitive data is encrypted in transit and at rest using industry-standard protocols.</p>
</div>
<div class="flex-1 p-4 bg-fidelity-green/5 rounded-lg border border-fidelity-green/10">
<div class="material-symbols-outlined text-fidelity-green mb-2">security</div>
<div class="text-sm font-bold mb-1 text-on-surface">Access Controls</div>
<p class="text-xs text-on-secondary-container">Internal access to account and investment data is restricted on a need-to-know basis.</p>
</div>
<div class="flex-1 p-4 bg-fidelity-green/5 rounded-lg border border-fidelity-green/10">
<div class="material-symbols-outlined text-fidelity-green mb-2">verified_user</div>
<div class="text-sm font-bold mb-1 text-on-surface">Monitoring</div>
<p class="text-xs text-on-secondary-container">We monitor for suspicious login attempts and unusual transaction patterns.</p>
</div>
</div>
</div>
<div class="mt-8 legal-anchor" id="rights">
<h3 class="text-lg font-semibold text-on-surface mb-3">4. User Rights</h3>
<p class="text-on-secondary-container leading-relaxed mb-4">Depending on your jurisdiction, you may have rights regarding your personal data, including:</p>
<ul class="space-y-3">
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-fidelity-green text-sm mt-0.5">check_circle</span>Right to access personal data we hold about you.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-fidelity-green text-sm mt-0.5">check_circle</span>Right to correct inaccurate or incomplete information.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-fidelity-green text-sm mt-0.5">check_circle</span>Right to request deletion where legal and regulatory obligations permit.</li>
<li class="flex items-start gap-3 text-sm text-on-secondary-container"><span class="material-symbols-outlined text-fidelity-green text-sm mt-0.5">check_circle</span>Right to data portability for information you provided, subject to applicable law.</li>
</ul>
</div>
</section>

<div class="mt-16 pt-8 border-t border-border-low">
<div class="bg-surface-container-low p-8 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6">
<div>
<h4 class="text-lg font-bold text-on-surface mb-2">Still have questions?</h4>
<p class="text-on-secondary-container text-sm">Our support team can help you understand your rights, risks, and account obligations.</p>
</div>
<div class="flex gap-4">
<a href="/help_centre" class="px-6 py-2.5 border border-border-low text-sm font-semibold rounded-lg text-on-surface hover:bg-surface-container-high transition-colors">Help Center</a>
<a href="/live_chat" class="bg-fidelity-green text-white px-6 py-2.5 text-sm font-bold rounded-lg hover:opacity-90 transition-all">Contact Support</a>
</div>
</div>
</div>
</article>
</div>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<a class="legal-back-top fixed bottom-8 right-8 w-12 h-12 bg-surface-container-high border border-border-low rounded-full flex items-center justify-center shadow-lg text-on-secondary-container hover:text-fidelity-green transition-all z-40" href="#" aria-label="Back to top">
<span class="material-symbols-outlined">expand_less</span>
</a>
</body>
</html>

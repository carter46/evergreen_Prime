<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$contactEmail = get_site_setting('contact_email', 'legal@example.com');
$legalEmail = get_site_setting('legal_email', $contactEmail);
$pageTitle = 'Legal Center | ' . $siteName;
?>
<!DOCTYPE html>
<html class="light scroll-smooth" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
body { background-color: #f7f9ff; scroll-behavior: smooth; }
.legal-card {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
  border: 1px solid #E9E9E9;
}
.legal-nav-link {
  color: #41493c;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.15s, color 0.15s;
}
.legal-nav-link:hover {
  background: #e5e8ee;
}
.legal-nav-link.is-active {
  background: #70c4fe;
  color: #005076;
  font-weight: 700;
}
.legal-anchor { scroll-margin-top: 6.5rem; }
@media print {
  aside, #utility-header, #main-navigation, footer, .legal-print-hide { display: none !important; }
  main { max-width: 100% !important; padding: 0 !important; }
  .legal-card { border: none !important; box-shadow: none !important; }
}
</style>
</head>
<body class="font-body-md text-on-surface">
<?php $currentPage = 'legal_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main class="max-w-[1152px] mx-auto px-4 md:px-gutter py-10 md:py-xl">
<header class="mb-10 md:mb-xl legal-anchor" id="top">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-md">
<div>
<h1 class="font-display-lg text-display-lg text-on-surface mb-xs">Legal Center</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
Terms of Service, Privacy Policy, and investment risk disclosures for <?php echo htmlspecialchars($siteName); ?> clients and partners.
</p>
<p class="font-label-md text-label-md text-outline mt-sm uppercase tracking-widest">Last Updated: June 27, 2026</p>
</div>
<div class="flex gap-sm legal-print-hide">
<button type="button" class="flex items-center gap-xs border border-surface-gray px-md py-sm rounded-lg font-label-md hover:bg-surface-container transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-[20px]">print</span> Print
</button>
<button type="button" class="flex items-center gap-xs border border-surface-gray px-md py-sm rounded-lg font-label-md hover:bg-surface-container transition-colors" onclick="window.print()">
<span class="material-symbols-outlined text-[20px]">download</span> Download PDF
</button>
</div>
</div>
</header>

<div class="flex flex-col lg:flex-row gap-lg">
<aside class="lg:w-64 shrink-0 legal-print-hide">
<div class="sticky top-20 w-full h-fit rounded-xl border border-surface-gray bg-surface-container-lowest p-md flex flex-col gap-xs">
<div class="mb-sm">
<h3 class="text-body-lg font-headline-md text-on-surface">Legal Center</h3>
<p class="font-label-md text-label-md text-on-surface-variant">Last updated June 2026</p>
</div>
<nav class="flex flex-col gap-xs" id="legal-sidebar-nav" aria-label="Legal sections">
<a class="legal-nav-link is-active" href="#tos" data-legal-nav="tos">
<span class="material-symbols-outlined">gavel</span>
<span class="font-label-md text-label-md">Terms of Service</span>
</a>
<a class="legal-nav-link" href="#privacy" data-legal-nav="privacy">
<span class="material-symbols-outlined">security</span>
<span class="font-label-md text-label-md">Privacy Policy</span>
</a>
<a class="legal-nav-link" href="#risks" data-legal-nav="risks">
<span class="material-symbols-outlined">warning</span>
<span class="font-label-md text-label-md">Risk Disclosures</span>
</a>
</nav>
</div>
</aside>

<section class="flex-1 space-y-lg min-w-0">
<article class="legal-card bg-white rounded-xl p-md md:p-lg legal-anchor" id="tos">
<div class="flex items-center gap-sm mb-md border-b border-surface-gray pb-sm">
<span class="font-headline-lg text-primary">01</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Terms of Service</h2>
</div>
<div class="space-y-md">
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Acceptance of Terms</h3>
<p class="text-body-md leading-relaxed text-on-surface-variant">
By accessing or using the <?php echo htmlspecialchars($siteName); ?> website, brokerage accounts, retirement planning tools, investment plans, and wealth management services, you agree to these Terms of Service. If you do not agree, you must discontinue use of our platform.
</p>
</div>
<div class="bg-error-container/10 border-l-4 border-error p-md rounded-r-lg">
<div class="flex items-center gap-xs mb-xs text-error">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
<h3 class="font-headline-md text-headline-md">Investment Risk Disclosure</h3>
</div>
<p class="text-body-md text-on-surface-variant italic">
Investing in securities—including stocks, ETFs, bonds, mutual funds, and real estate-related instruments—involves risk of loss. The value of your investments can go up or down, and you may receive back less than you originally invested. Past performance is not a guarantee of future results.
</p>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Market Volatility</h3>
<p class="text-body-md leading-relaxed text-on-surface-variant mb-sm">
Financial markets are inherently unpredictable. <?php echo htmlspecialchars($siteName); ?> provides tools to help you plan, but does not guarantee performance or eliminate market risk.
</p>
<ul class="list-disc pl-md space-y-xs text-body-md text-on-surface-variant">
<li><strong>Stocks &amp; ETFs:</strong> Subject to equity market risks, sector downturns, and company-specific failures.</li>
<li><strong>Real Estate &amp; Alternatives:</strong> May have lower liquidity and distinct valuation methodologies.</li>
<li><strong>Retirement Accounts:</strong> Subject to IRS rules on contributions, distributions, and penalties.</li>
</ul>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Account Responsibilities</h3>
<div class="grid md:grid-cols-2 gap-sm">
<div class="p-sm bg-surface rounded-lg border border-surface-gray">
<h4 class="font-bold text-primary mb-1">Security</h4>
<p class="text-body-sm text-on-surface-variant">Users are responsible for maintaining the confidentiality of login credentials and multi-factor authentication devices.</p>
</div>
<div class="p-sm bg-surface rounded-lg border border-surface-gray">
<h4 class="font-bold text-primary mb-1">Accuracy</h4>
<p class="text-body-sm text-on-surface-variant">All information provided during onboarding must be truthful, accurate, and kept up-to-date at all times.</p>
</div>
<div class="p-sm bg-surface rounded-lg border border-surface-gray">
<h4 class="font-bold text-primary mb-1">Suitability</h4>
<p class="text-body-sm text-on-surface-variant">Users must assess if the financial products offered align with their personal risk tolerance and financial goals.</p>
</div>
<div class="p-sm bg-surface rounded-lg border border-surface-gray">
<h4 class="font-bold text-primary mb-1">Unauthorized Activity</h4>
<p class="text-body-sm text-on-surface-variant">Any suspicious activity must be reported immediately to our security desk.</p>
</div>
</div>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Regulatory Information</h3>
<p class="text-body-md text-on-surface-variant">
<?php echo htmlspecialchars($siteName); ?> maintains policies against fraud, market manipulation, money laundering, and unauthorized account access. We verify identity and monitor transactions in accordance with applicable Anti-Money Laundering (AML) and Know Your Customer (KYC) requirements.
</p>
</div>
</div>
</article>

<article class="legal-card bg-white rounded-xl p-md md:p-lg legal-anchor" id="privacy">
<div class="flex items-center gap-sm mb-md border-b border-surface-gray pb-sm">
<span class="font-headline-lg text-primary">02</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Privacy Policy</h2>
</div>
<div class="space-y-md">
<p class="text-body-md text-on-surface-variant">
Your privacy is paramount. This policy describes how <?php echo htmlspecialchars($siteName); ?> collects, uses, and shares your personal information.
</p>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Information Collection</h3>
<div class="space-y-sm">
<div class="flex items-start gap-sm">
<span class="material-symbols-outlined text-fidelity-green mt-1">person</span>
<div>
<p class="font-bold text-on-surface">Identity &amp; Financial Data</p>
<p class="text-body-sm text-on-surface-variant">Full name, tax identification numbers, bank account details, investment history, and net worth assessments for KYC/AML compliance.</p>
</div>
</div>
<div class="flex items-start gap-sm">
<span class="material-symbols-outlined text-fidelity-green mt-1">analytics</span>
<div>
<p class="font-bold text-on-surface">Planning &amp; Technical Data</p>
<p class="text-body-sm text-on-surface-variant">IP addresses, browser types, retirement goals, risk tolerance responses, and platform interaction data.</p>
</div>
</div>
</div>
</div>
<div class="bg-surface-container-low p-md rounded-lg border border-outline-variant/30">
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs flex items-center gap-xs">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">security</span>
Data Protection
</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-md mt-sm">
<div class="text-center">
<span class="material-symbols-outlined text-fidelity-green text-3xl mb-2">lock</span>
<p class="font-label-md text-on-surface">AES-256 Encryption</p>
</div>
<div class="text-center">
<span class="material-symbols-outlined text-fidelity-green text-3xl mb-2">verified_user</span>
<p class="font-label-md text-on-surface">Access Controls</p>
</div>
<div class="text-center">
<span class="material-symbols-outlined text-fidelity-green text-3xl mb-2">admin_panel_settings</span>
<p class="font-label-md text-on-surface">24/7 Monitoring</p>
</div>
</div>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Cookies &amp; Tracking</h3>
<p class="text-body-md text-on-surface-variant">
We use essential cookies to maintain secure sessions and remember preferences. Analytics cookies help us improve our investing and planning pages. You may manage cookies through your browser settings.
</p>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">User Rights</h3>
<p class="text-body-md text-on-surface-variant">
Under applicable data protection laws, you have the right to access, rectify, or erase your personal data where legal and regulatory obligations permit. To exercise these rights, contact us at <a class="text-institutional-blue hover:underline" href="mailto:<?php echo htmlspecialchars($legalEmail); ?>"><?php echo htmlspecialchars($legalEmail); ?></a>.
</p>
</div>
</div>
</article>

<article class="legal-card bg-white rounded-xl p-md md:p-lg legal-anchor" id="risks">
<div class="flex items-center gap-sm mb-md border-b border-surface-gray pb-sm">
<span class="font-headline-lg text-primary">03</span>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Risk Disclosures</h2>
</div>
<div class="space-y-md">
<p class="text-body-md text-on-surface-variant">
Before investing through <?php echo htmlspecialchars($siteName); ?>, you should understand the following material risks associated with stocks, ETFs, retirement accounts, and real estate-related investments.
</p>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Stocks, ETFs &amp; Brokerage Products</h3>
<p class="text-body-md text-on-surface-variant mb-sm">
When you trade or hold stocks and ETFs, you are subject to market risk, sector concentration, currency exposure, dividend changes, and ETF tracking error. Options and derivatives, if available, carry additional risks including total loss of premium.
</p>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Real Estate &amp; Alternative Investments</h3>
<p class="text-body-md text-on-surface-variant mb-sm">
Real estate exposure—through REITs, funds, or related instruments—may be illiquid. Property values can decline due to interest rates, vacancy, maintenance costs, or regulatory changes. Projected income or appreciation is not assured.
</p>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Retirement &amp; Planning Tools</h3>
<p class="text-body-md text-on-surface-variant">
Retirement calculators, investment plans, and projections provide estimates only—not promises of performance. <?php echo htmlspecialchars($siteName); ?> is not a tax advisor. Consult a qualified professional regarding IRA, Roth, and brokerage tax treatment.
</p>
</div>
<div class="bg-surface-container-low p-md rounded-lg border border-surface-gray">
<p class="text-body-sm text-on-surface-variant">
<strong class="text-on-surface">Important:</strong> Nothing on this platform constitutes personalized financial, legal, or investment advice unless provided under a separate written advisory agreement. You should only invest money you can afford to lose without compromising essential living expenses.
</p>
</div>
</div>
</article>
</section>
</div>

<section class="mt-xl bg-primary text-on-primary rounded-2xl p-lg text-center relative overflow-hidden legal-print-hide">
<div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
<div class="relative z-10">
<h2 class="font-headline-lg text-headline-lg mb-sm">Need legal assistance?</h2>
<p class="font-body-lg text-body-lg mb-md opacity-90 max-w-xl mx-auto">
Our compliance and support teams are available to answer your questions regarding our policies and disclosures.
</p>
<div class="flex flex-col sm:flex-row justify-center gap-sm">
<a class="bg-white text-primary px-xl py-sm rounded-lg font-headline-md font-bold hover:bg-surface-container-high transition-colors flex items-center justify-center gap-xs" href="mailto:<?php echo htmlspecialchars($legalEmail); ?>">
<span class="material-symbols-outlined">mail</span> Email Legal Desk
</a>
<a class="border border-white/40 bg-white/10 backdrop-blur-sm text-white px-xl py-sm rounded-lg font-headline-md font-bold hover:bg-white/20 transition-colors flex items-center justify-center gap-xs" href="/help_centre">
<span class="material-symbols-outlined">contact_support</span> Support Center
</a>
</div>
</div>
</section>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<script>
(function () {
  var sections = ['tos', 'privacy', 'risks'];
  var navLinks = document.querySelectorAll('[data-legal-nav]');

  function setActive(id) {
    navLinks.forEach(function (link) {
      var active = link.getAttribute('data-legal-nav') === id;
      link.classList.toggle('is-active', active);
    });
  }

  window.addEventListener('scroll', function () {
    var current = sections[0];
    sections.forEach(function (section) {
      var el = document.getElementById(section);
      if (el && window.pageYOffset >= el.offsetTop - 140) {
        current = section;
      }
    });
    setActive(current);
  });

  navLinks.forEach(function (link) {
    link.addEventListener('click', function () {
      var id = link.getAttribute('data-legal-nav');
      if (id) setActive(id);
    });
  });
})();
</script>
</body>
</html>

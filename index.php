<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = $siteName . ' - Retirement Plans, Investing, Brokerage';
$accountSectionImage = 'https://lh3.googleusercontent.com/aida-public/AB6AXuBlYMQLYWxL_RRl-5SOmShdC6hhlnZmPa96FDPtMPcRvaR9sO9ERXdbuD7YuyAISXe53Qo5sxtONpYyXNUzLhT57merNvjxXl0swczCCf9kCmrTa1EmTSnxWy6TfSzPZzDm-1mmblmnPJkSGFqI7Ze5sT-CJJSmhV06QrimoudsRiRYC7iCNiDn_P8pnZ11wWmDU8nKK3lfNQxAkl9Pew8i5VGSX-xHj9qHGStweeoY8sqp1vUCRWEksA';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
:root { --home-nav-offset: 3.5rem; }

/* Account selector — sticky-scroll (desktop only) */
.account-tab {
  display: block;
  width: 100%;
  text-align: left;
  padding: 0.875rem 0 0.875rem 1.25rem;
  border: none;
  border-left: 4px solid transparent;
  font-size: 0.9375rem;
  color: #666;
  background: none;
  cursor: pointer;
  transition: color 0.2s, border-color 0.2s;
}
.account-tab:hover { color: #337722; }
.account-tab.is-active {
  border-left-color: #337722;
  color: #337722;
  font-weight: 700;
}
.account-panel { padding: 2rem 0; }

@media (min-width: 1024px) {
  .account-selector__track {
    --pin-step: calc(100vh - var(--home-nav-offset));
    position: relative;
    height: calc(var(--pin-step) * 4);
  }
  .account-selector__sticky {
    position: sticky;
    top: var(--home-nav-offset);
    height: var(--pin-step);
    overflow: hidden;
  }
  .account-selector__layout {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 2.5rem;
    height: 100%;
  }
  .account-selector__nav-col {
    grid-column: span 3;
    position: relative;
    height: 100%;
  }
  .account-selector__nav {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 2rem;
    border-left: 2px solid #f3f4f6;
    margin: 0;
    padding: 0;
  }
  .account-selector__panels {
    grid-column: span 5;
    position: relative;
    height: 100%;
    overflow: hidden;
  }
  .account-selector__media {
    grid-column: span 4;
    display: flex;
    align-items: center;
    height: 100%;
  }
  .account-panel {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 0;
    opacity: 0;
    visibility: hidden;
    transform: translateY(1.25rem);
    transition: opacity 0.4s ease, transform 0.4s ease, visibility 0.4s;
    pointer-events: none;
  }
  .account-panel.is-active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
  }
  /* Invisible step anchors — 25% intervals through the pinned scroll range */
  .account-selector__steps {
    position: absolute;
    inset: 0;
    pointer-events: none;
    visibility: hidden;
  }
  .account-selector__step {
    position: absolute;
    left: 0;
    width: 1px;
    height: 1px;
    scroll-margin-top: var(--home-nav-offset);
  }
  .account-selector__step[data-step="0"] { top: 0; }
  .account-selector__step[data-step="1"] { top: calc(var(--pin-step) * 0.75); }
  .account-selector__step[data-step="2"] { top: calc(var(--pin-step) * 1.5); }
  .account-selector__step[data-step="3"] { top: calc(var(--pin-step) * 2.25); }
}
@media (max-width: 1023px) {
  .account-selector__sticky { position: relative; top: auto; height: auto; overflow: visible; }
  .account-panel + .account-panel {
    border-top: 1px solid #f3f4f6;
    margin-top: 1rem;
    padding-top: 2.5rem;
  }
}
</style>
</head>
<body class="fidelity-homepage bg-white text-fidelityDark overflow-x-hidden">
<?php $currentPage = 'home'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<!-- BEGIN: HeroSection -->
<section class="relative h-[500px] bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDbM31HCVx6ysE9wVDhMXr1U63qboojZDtUXf73Qvgv530A03SthuSjWuxijfKcKoQLMLrQKIytflMVkUxiGjhEh2rONrIlnCYEWddGjF2SXM5dps7rCX8a9zSec4ezTgmFsP1-7Rl-CA27NiGz8e1AkuzxXTvbJ0Zga_gEmDMtUEQWmnIuhb198t9W9D9BdjnPS6ofngxbaa3lsH-MIUMTVYHVQ-PXGzy7ZqCUktyhdgrwOp63ifjpUw');">
<div class="absolute inset-0 bg-black/10"></div>
<div class="mx-auto px-4 h-full flex items-center relative z-10 max-w-6xl">
<div class="bg-white/95 p-10 max-w-lg shadow-xl">
<h1 class="text-4xl mb-4 leading-tight">Invest today and plan for tomorrow</h1>
<p class="text-gray-600 mb-8">We can help you get started.</p>
<div class="flex space-x-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover transition-colors" href="/register">Open an account</a>
<a class="border-2 border-fidelityDark text-fidelityDark px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors" href="/planning">I need guidance</a>
</div>
</div>
</div>
<!-- Man in yellow jacket visual position indicator -->
<div class="absolute right-1/4 bottom-10">
<div class="w-20 h-40 bg-yellow-400/20 rounded-full blur-xl"></div>
</div>
</section>
<!-- END: HeroSection -->

<!-- BEGIN: AccountSelectorSection -->
<section class="account-selector bg-white" id="account-selector">
<div class="account-selector__track" id="account-selector-track">
<div class="account-selector__steps" aria-hidden="true">
<span class="account-selector__step" id="account-step-0" data-step="0"></span>
<span class="account-selector__step" id="account-step-1" data-step="1"></span>
<span class="account-selector__step" id="account-step-2" data-step="2"></span>
<span class="account-selector__step" id="account-step-3" data-step="3"></span>
</div>
<div class="account-selector__sticky" id="account-selector-sticky">
<div class="account-selector__layout mx-auto px-4 max-w-6xl">

<aside class="account-selector__nav-col hidden lg:block">
<nav class="account-selector__nav" aria-label="Account goals">
<button type="button" class="account-tab is-active" data-tab="investing">Start investing</button>
<button type="button" class="account-tab" data-tab="retirement">Save for retirement</button>
<button type="button" class="account-tab" data-tab="healthcare">Save for health care</button>
<button type="button" class="account-tab" data-tab="education">Invest for a child</button>
</nav>
</aside>

<nav class="lg:hidden flex gap-2 overflow-x-auto pb-4 mb-2 border-b border-gray-100 -mx-1 px-1" aria-label="Account goals">
<button type="button" class="account-tab-mobile shrink-0 px-3 py-2 text-sm rounded-full bg-fidelityGreen text-white font-semibold" data-tab-mobile="investing">Start investing</button>
<button type="button" class="account-tab-mobile shrink-0 px-3 py-2 text-sm rounded-full bg-gray-100 text-gray-600" data-tab-mobile="retirement">Retirement</button>
<button type="button" class="account-tab-mobile shrink-0 px-3 py-2 text-sm rounded-full bg-gray-100 text-gray-600" data-tab-mobile="healthcare">Health care</button>
<button type="button" class="account-tab-mobile shrink-0 px-3 py-2 text-sm rounded-full bg-gray-100 text-gray-600" data-tab-mobile="education">For a child</button>
</nav>

<div class="account-selector__panels">

<article class="account-panel is-active" data-panel="investing" id="panel-investing">
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Invest smart from the start with a brokerage account</h2>
<div class="space-y-6 mb-10">
<div>
<h3 class="font-bold text-lg mb-1">$0 account fees¹</h3>
<p class="text-gray-600 text-sm">Keep your money working toward your goals.</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">$0 commissions</h3>
<p class="text-gray-600 text-sm">Trade US stocks and ETFs commission-free online.²</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Trade any amount</h3>
<p class="text-gray-600 text-sm">Buy US stocks and ETFs for as little as $1 with fractional shares.³</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open a brokerage account</a>
<a class="text-fidelityGreen font-bold hover:underline" href="/investing">Explore ways to invest</a>
</div>
<div class="lg:hidden mt-8 rounded-2xl overflow-hidden bg-fidelityLightGreen p-4">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-xl shadow-lg w-full" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</article>

<article class="account-panel" data-panel="retirement" id="panel-retirement">
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Plan for the possibilities ahead with a Roth IRA</h2>
<div class="space-y-6 mb-10">
<div>
<h3 class="font-bold text-lg mb-1">Tax savings</h3>
<p class="text-gray-600 text-sm">Any investment growth in a Roth is tax-free, with tax-free withdrawals in retirement.⁴</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Access to your contributions</h3>
<p class="text-gray-600 text-sm">Any amount you add to your Roth can be withdrawn without taxes or penalties, anytime, for any reason.</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Numerous ways to invest</h3>
<p class="text-gray-600 text-sm">Whether you invest on your own or have us do it, you can choose from stocks to ETFs to crypto and more.</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open a Roth IRA</a>
<a class="text-fidelityGreen font-bold hover:underline" href="/planning">Explore retirement planning</a>
</div>
<div class="lg:hidden mt-8 rounded-2xl overflow-hidden bg-fidelityLightGreen p-4">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-xl shadow-lg w-full" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</article>

<article class="account-panel" data-panel="healthcare" id="panel-healthcare">
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Save, earn, and invest for health care with an HSA</h2>
<div class="space-y-6 mb-10">
<div>
<h3 class="font-bold text-lg mb-1">Triple-tax advantage</h3>
<p class="text-gray-600 text-sm">Get tax-deductible contributions, no immediate tax on earnings, and tax-free withdrawals for qualified medical expenses.⁵</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">No account fees</h3>
<p class="text-gray-600 text-sm"><?php echo htmlspecialchars($siteName); ?>'s HSA has no account fees or minimums, and $0 commissions for US stock &amp; ETF trades.⁶</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Not "use-it-or-lose-it"</h3>
<p class="text-gray-600 text-sm">The money's always yours. You can earn interest on cash, grow your account by investing, or do both.</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open an HSA</a>
<a class="text-fidelityGreen font-bold hover:underline" href="#">Explore health savings</a>
</div>
<div class="lg:hidden mt-8 rounded-2xl overflow-hidden bg-fidelityLightGreen p-4">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-xl shadow-lg w-full" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</article>

<article class="account-panel" data-panel="education" id="panel-education">
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Save for the next generation's education with a 529 account</h2>
<div class="space-y-6 mb-10">
<div>
<h3 class="font-bold text-lg mb-1">Tax-smart savings</h3>
<p class="text-gray-600 text-sm">Any earnings grow federal income tax-deferred, and you can get tax-free withdrawals for qualified education expenses.</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Flexible use of funds</h3>
<p class="text-gray-600 text-sm">Pay for college, trade school, and K–12 nationwide, including tuition, fees, and books.⁷</p>
</div>
<div>
<h3 class="font-bold text-lg mb-1">Your money can work harder</h3>
<p class="text-gray-600 text-sm">No minimums to open and no account fees.² Plus, start early and your money could potentially grow more.</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open a 529 account</a>
<a class="text-fidelityGreen font-bold hover:underline" href="/planning">Explore saving for a child</a>
</div>
<div class="lg:hidden mt-8 rounded-2xl overflow-hidden bg-fidelityLightGreen p-4">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-xl shadow-lg w-full" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</article>

</div>

<div class="account-selector__media hidden lg:flex">
<div class="w-full bg-fidelityLightGreen rounded-3xl p-6">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-2xl shadow-2xl w-full h-auto object-cover" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</div>

</div>
</div>
</div>
</section>
<!-- END: AccountSelectorSection -->

<!-- BEGIN: AlternatingSections (removed — content in scroll tabs above) -->

<!-- BEGIN: PartnerSection -->
<section class="py-0">
<div class="mx-auto flex flex-col lg:flex-row bg-white max-w-6xl">
<div class="lg:w-2/3">
<img alt="<?php echo htmlspecialchars($siteName); ?> Advisors" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGNI9MvKH4eR-H4wbkZQtqLrkuQFRt-B8yuqIg2po_PHYBT5n95rGn79ffTEhyPEciibrnTd8JfWRcVzdo6cMkXqWMtyVUI-JFkYGPNejK833re5GWabG4e7JtAyjGy2pJsjzC1Y20KaQDx7wYgjya6SmunmGwHgUee647bJH3C7N0ved2CeN5MqfQtNwqsBezHpyIeM6j0IyhxnfG-oVvTwrxAj9oJYrBFUlef2P4ASP4xjeS0drytQ">
</div>
<div class="lg:w-1/3 bg-gray-50 p-12 flex flex-col justify-center">
<h2 class="text-3xl mb-6">A partner to help bring your plans to life</h2>
<p class="text-gray-600 mb-8 text-lg">Collaborate with a dedicated <?php echo htmlspecialchars($siteName); ?> advisor to build a comprehensive wealth management strategy designed to help you meet your goals and evolving needs.</p>
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold inline-block text-center hover:bg-fidelityGreenHover transition-colors w-max" href="#">Find an advisor</a>
</div>
</div>
<div class="bg-gray-100 py-4 text-center">
<p class="text-xs text-fidelityGray">
        Review <?php echo htmlspecialchars($siteName); ?> Brokerage Services with <a class="text-blue-600 underline" href="#">FINRA's BrokerCheck</a> |
        <a class="text-blue-600 underline" href="#">Regulatory summary of <?php echo htmlspecialchars($siteName); ?> services (PDF)</a>
</p>
</div>
</section>
<!-- END: PartnerSection -->

<!-- BEGIN: ExpertiseGrid -->
<section class="py-20 bg-gray-50">
<div class="mx-auto px-4 max-w-6xl">
<h2 class="text-3xl mb-12 text-center">Expertise you can act on</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- Card 1 -->
<div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
<img alt="HSA article" class="w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDrfGZE4uyLrBVgx5oZTwVxHCbMt5nr97V2zsUPcuteaMdoda-ihuMI1ECqORMqH3NbbG3bMvSEZtrmSmHTujTL6S27XWYysorCxu0MnY6gJgMYiIc4qVE1vU1XsqOfob6CwwmcrM0Qd3jC-8gotRhfYNW1W100bOJwdQiNpS_O71M3WaaaqjvcrQkEbu6H8m8_c-w5OF72XAem1rWMrxwq4mGZSJwpviPva8vV7wridhq9PlolA5trxA">
<div class="p-6">
<h3 class="text-xl font-bold mb-3">3 ways to use your health savings account</h3>
<p class="text-sm text-gray-600 mb-6">Know the type of HSA user you are, then see how to make your money work harder.</p>
<div class="flex items-center text-xs text-gray-400 space-x-4">
<span class="">Article</span>
<span class="">6 min</span>
</div>
</div>
</div>
<!-- Card 2 -->
<div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
<img alt="Market insights" class="w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAul3dAMH8fEQUmzimbws6bujHtXZlAyFDts4sq1xD25JH53pw61zHQapYNh4m8x-PPRoSOkVweyIPE-7H78qBRljnpl7K1stlm_-kVxIMa2KF2mcaS_tQWK6yd9Tb5m9Rit4BtgOjtZhmHuxPi77AmX3fBDBBRtSOfCnvCDF0DiQYPos30dTide-jtaXpsyacr1LtUtiPDfjQ7MuCBuMIwZZquJ8BWTz39MEO7BKK6cvpa1qAllIa74w">
<div class="p-6">
<h3 class="text-xl font-bold mb-3">Market Sense: Weekly insights</h3>
<p class="text-sm text-gray-600 mb-6">Our experts discuss the latest headlines, current market conditions, and what it all means for you.</p>
<div class="flex items-center text-xs text-gray-400 space-x-4">
<span class="">Webinar</span>
<span class="">24 min</span>
</div>
</div>
</div>
<!-- Card 3 -->
<div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
<img alt="Investing start" class="w-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7IRge5NrdHMcf_HA0DzqaNg0H4gqWcQW_bksH5pYuUULN0JfIxvPhoGMG6rCY0tRkcKCMFvh8XerOpoM366ESWEM6E3_C8WOC6jhEVWq1_NPCgqXsSQ-K7_8zrVatBBfH3NsK9DPpso3sQvcIl00GY2LCtvXElvqLO5w514V9IRi4jAOMwns7_B5iJElF4h1txk5xX27YGmHrfCKeSHvSF6DtCh4faLwlieSVRFpMeAqw2hRNu12idA">
<div class="p-6">
<h3 class="text-xl font-bold mb-3">How to start investing</h3>
<p class="text-sm text-gray-600 mb-6">It doesn't have to be overly complicated. Here's how to start.</p>
<div class="flex items-center text-xs text-gray-400 space-x-4">
<span class="">Article</span>
<span class="">8 min</span>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- END: ExpertiseGrid -->

<!-- BEGIN: WhyChooseSection -->
<section class="py-20 bg-white">
<div class="mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center max-w-6xl">
<div>
<h2 class="text-4xl mb-8">Why choose <?php echo htmlspecialchars($siteName); ?>?</h2>
<p class="text-lg text-gray-600 mb-8">Our objective insights and disciplined approach have helped generations of customers through all kinds of markets.</p>
<ul class="space-y-4">
<li class="flex items-start">
<div class="h-2 w-2 rounded-full bg-fidelityGreen mt-2 mr-3"></div>
<p class="text-gray-700 font-bold">A clear, straightforward experience</p>
</li>
<li class="flex items-start">
<div class="h-2 w-2 rounded-full bg-fidelityGreen mt-2 mr-3"></div>
<p class="text-gray-700 font-bold">Guidance as life changes</p>
</li>
<li class="flex items-start">
<div class="h-2 w-2 rounded-full bg-fidelityGreen mt-2 mr-3"></div>
<p class="text-gray-700 font-bold">A wider range of integrated tools and products</p>
</li>
<li class="flex items-start">
<div class="h-2 w-2 rounded-full bg-fidelityGreen mt-2 mr-3"></div>
<p class="text-gray-700 font-bold">Value and transparency at every step</p>
</li>
</ul>
</div>
<div class="relative rounded-2xl overflow-hidden">
<img alt="<?php echo htmlspecialchars($siteName); ?> Customers" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCamvPpRWXXo06xfhs2MhOWcBVF1JncodJZxIDVpxGDf217WzcamtCt4x3ft5_GliedbgSuGCg3RhIxRzW_1aWBORQUWq8u5W0ev0qzvfSkzESeswVEfjsfm6skJ_e5QHzhjz1ffFf0yKnrfeZl_H82SqxOPLDNgACMl5GYDl1o2H7NED4Pdnp6azY8b_OJVbHa65QZJx9-7HJVaWMhSc_qtrgr1p0imf0NU-EtKhBbc1DM6UkD5iXK_w">
</div>
</div>
</section>
<!-- END: WhyChooseSection -->

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<script>
(function () {
    'use strict';

    var PANELS = ['investing', 'retirement', 'healthcare', 'education'];
    var PANEL_COUNT = PANELS.length;

    var track = document.getElementById('account-selector-track');
    var sticky = document.getElementById('account-selector-sticky');
    if (!track || !sticky) return;

    var tabs = track.querySelectorAll('[data-tab]');
    var mobileTabs = track.querySelectorAll('[data-tab-mobile]');
    var panelEls = track.querySelectorAll('[data-panel]');
    var activeIndex = -1;
    var ticking = false;

    function isDesktop() {
        return window.matchMedia('(min-width: 1024px)').matches;
    }

    function readProgress() {
        var trackRect = track.getBoundingClientRect();
        var stickyRect = sticky.getBoundingClientRect();
        var range = trackRect.height - stickyRect.height;
        if (range <= 0) return 0;
        var scrolled = stickyRect.top - trackRect.top;
        return Math.max(0, Math.min(1, scrolled / range));
    }

    function indexFromProgress(progress) {
        var index = Math.floor(progress * PANEL_COUNT);
        return Math.min(PANEL_COUNT - 1, Math.max(0, index));
    }

    function setActiveIndex(index) {
        var id = PANELS[index];
        tabs.forEach(function (el) {
            el.classList.toggle('is-active', el.getAttribute('data-tab') === id);
        });
        mobileTabs.forEach(function (el) {
            var on = el.getAttribute('data-tab-mobile') === id;
            el.classList.toggle('bg-fidelityGreen', on);
            el.classList.toggle('text-white', on);
            el.classList.toggle('font-semibold', on);
            el.classList.toggle('bg-gray-100', !on);
            el.classList.toggle('text-gray-600', !on);
        });
        panelEls.forEach(function (el) {
            el.classList.toggle('is-active', el.getAttribute('data-panel') === id);
        });
    }

    function goToIndex(index) {
        if (index < 0 || index >= PANEL_COUNT) return;
        if (!isDesktop()) {
            var panel = document.getElementById('panel-' + PANELS[index]);
            if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }
        var step = document.getElementById('account-step-' + index);
        if (step) step.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            goToIndex(PANELS.indexOf(tab.getAttribute('data-tab')));
        });
    });
    mobileTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            goToIndex(PANELS.indexOf(tab.getAttribute('data-tab-mobile')));
        });
    });

    function onFrame() {
        ticking = false;
        if (!isDesktop()) return;
        var index = indexFromProgress(readProgress());
        if (index === activeIndex) return;
        activeIndex = index;
        setActiveIndex(index);
    }

    function onScroll() {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(onFrame);
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', function () {
        activeIndex = -1;
        onFrame();
    });
    onFrame();
})();
</script>
</body>
</html>

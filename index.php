<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = $siteName . ' - Retirement Plans, Investing, Brokerage';
$accountSectionImage = 'https://lh3.googleusercontent.com/aida-public/AB6AXuBlYMQLYWxL_RRl-5SOmShdC6hhlnZmPa96FDPtMPcRvaR9sO9ERXdbuD7YuyAISXe53Qo5sxtONpYyXNUzLhT57merNvjxXl0swczCCf9kCmrTa1EmTSnxWy6TfSzPZzDm-1mmblmnPJkSGFqI7Ze5sT-CJJSmhV06QrimoudsRiRYC7iCNiDn_P8pnZ11wWmDU8nKK3lfNQxAkl9Pew8i5VGSX-xHj9qHGStweeoY8sqp1vUCRWEksA';
$heroSlides = [
    '/uploads/images/nasa-Q1p7bh3SHj8-unsplash.jpg',
    '/uploads/images/bloombit.jpg',
    '/uploads/images/evergren_cmarket.png',
    '/uploads/images/wallet_image3.png',
    '/uploads/images/evergren_mockup.png',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
.account-timeline__line {
  position: absolute;
  left: 50%;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e5e7eb;
  transform: translateX(-50%);
}
.account-timeline__dot {
  position: absolute;
  left: 50%;
  top: 2.5rem;
  width: 14px;
  height: 14px;
  border-radius: 9999px;
  background: #337722;
  border: 3px solid #fff;
  box-shadow: 0 0 0 2px #337722;
  transform: translateX(-50%);
  z-index: 1;
}
.hero-slider__slide {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}
.hero-slider__slide.is-active {
  opacity: 1;
}
@media (max-width: 1023px) {
  .account-timeline__heading {
    border-left: 4px solid #337722;
    padding-left: 1rem;
  }
}
@media (min-width: 1024px) {
  .hero-slider {
    height: calc(100svh - 6.5rem);
    min-height: 520px;
  }
}
.account-timeline__media {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.75s ease, transform 0.75s ease;
}
.account-timeline__media.is-visible {
  opacity: 1;
  transform: translateY(0);
}
</style>
</head>
<body class="fidelity-homepage bg-white text-fidelityDark overflow-x-hidden">
<?php $currentPage = 'home'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<!-- BEGIN: HeroSection -->
<section class="hero-slider relative h-[280px] lg:h-auto overflow-hidden" aria-label="Featured highlights">
<div class="absolute inset-0" aria-hidden="true">
<?php foreach ($heroSlides as $i => $slide): ?>
<div class="hero-slider__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" style="background-image: url('<?php echo htmlspecialchars($slide); ?>');"></div>
<?php endforeach; ?>
</div>
<div class="absolute inset-0 bg-black/20" aria-hidden="true"></div>

<!-- Desktop only: CTA overlays hero -->
<div class="hidden lg:flex absolute inset-0 z-10 items-center">
<div class="w-full mx-auto px-4 max-w-6xl">
<div class="bg-white/95 p-10 max-w-lg shadow-xl">
<h1 class="text-4xl mb-4 leading-tight">Invest today and plan for tomorrow</h1>
<p class="text-gray-600 mb-8">We can help you get started.</p>
<div class="flex flex-row gap-4">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold text-center hover:bg-fidelityGreenHover transition-colors" href="/register">Open an account</a>
<a class="border-2 border-fidelityDark text-fidelityDark px-8 py-3 rounded-full font-bold text-center hover:bg-gray-100 transition-colors" href="/planning">I need guidance</a>
</div>
</div>
</div>
</div>
</section>

<!-- Mobile only: CTA stands alone below hero image -->
<section class="lg:hidden bg-white px-4 py-6" aria-label="Get started">
<div class="max-w-lg mx-auto bg-white p-6 shadow-xl border border-gray-100 rounded-lg">
<h1 class="text-3xl mb-3 leading-tight">Invest today and plan for tomorrow</h1>
<p class="text-gray-600 mb-6">We can help you get started.</p>
<div class="flex flex-col sm:flex-row gap-3">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold text-center hover:bg-fidelityGreenHover transition-colors" href="/register">Open an account</a>
<a class="border-2 border-fidelityDark text-fidelityDark px-8 py-3 rounded-full font-bold text-center hover:bg-gray-100 transition-colors" href="/planning">I need guidance</a>
</div>
</div>
</section>
<!-- END: HeroSection -->

<!-- BEGIN: AccountTimelineSection -->
<section class="py-16 lg:py-24 bg-white" id="account-selector">
<div class="mx-auto px-4 max-w-6xl">
<div class="account-timeline relative">

<div class="account-timeline__line hidden lg:block" aria-hidden="true"></div>

<!-- 1. Start investing — text left, image right -->
<div class="account-timeline__item relative pb-16 lg:pb-24 last:pb-0">
<span class="account-timeline__dot hidden lg:block" aria-hidden="true"></span>
<div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-16 items-center">
<div class="account-timeline__content order-2 lg:order-1 lg:pr-10">
<div class="account-timeline__heading">
<p class="text-fidelityGreen font-bold text-sm mb-3">Start investing</p>
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight lg:mb-8">Invest smart from the start with a brokerage account</h2>
</div>
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
</div>
<div class="account-timeline__media order-1 lg:order-2 mb-8 lg:mb-0 lg:pl-10">
<div class="bg-fidelityLightGreen rounded-3xl p-6">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-2xl shadow-2xl w-full h-auto object-cover" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</div>
</div>
</div>

<!-- 2. Retirement — image left, text right -->
<div class="account-timeline__item relative pb-16 lg:pb-24 last:pb-0">
<span class="account-timeline__dot hidden lg:block" aria-hidden="true"></span>
<div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-16 items-center">
<div class="account-timeline__media order-1 lg:order-1 mb-8 lg:mb-0 lg:pr-10">
<div class="bg-fidelityLightGreen rounded-3xl p-6">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-2xl shadow-2xl w-full h-auto object-cover" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</div>
<div class="account-timeline__content order-2 lg:order-2 lg:pl-10">
<div class="account-timeline__heading">
<p class="text-fidelityGreen font-bold text-sm mb-3">Save for retirement</p>
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Plan for the possibilities ahead with a Roth IRA</h2>
</div>
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
</div>
</div>
</div>

<!-- 3. Health care — text left, image right -->
<div class="account-timeline__item relative pb-16 lg:pb-24 last:pb-0">
<span class="account-timeline__dot hidden lg:block" aria-hidden="true"></span>
<div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-16 items-center">
<div class="account-timeline__content order-2 lg:order-1 lg:pr-10">
<div class="account-timeline__heading">
<p class="text-fidelityGreen font-bold text-sm mb-3">Save for health care</p>
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Save, earn, and invest for health care with an HSA</h2>
</div>
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
</div>
<div class="account-timeline__media order-1 lg:order-2 mb-8 lg:mb-0 lg:pl-10">
<div class="bg-fidelityLightGreen rounded-3xl p-6">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-2xl shadow-2xl w-full h-auto object-cover" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</div>
</div>
</div>

<!-- 4. Education — image left, text right -->
<div class="account-timeline__item relative pb-0">
<span class="account-timeline__dot hidden lg:block" aria-hidden="true"></span>
<div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-16 items-center">
<div class="account-timeline__media order-1 lg:order-1 mb-8 lg:mb-0 lg:pr-10">
<div class="bg-fidelityLightGreen rounded-3xl p-6">
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="rounded-2xl shadow-2xl w-full h-auto object-cover" src="<?php echo htmlspecialchars($accountSectionImage); ?>">
</div>
</div>
<div class="account-timeline__content order-2 lg:order-2 lg:pl-10">
<div class="account-timeline__heading">
<p class="text-fidelityGreen font-bold text-sm mb-3">Invest for a child</p>
<h2 class="text-3xl lg:text-4xl mb-8 leading-tight">Save for the next generation's education with a 529 account</h2>
</div>
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
</div>
</div>
</div>

</div>
</div>
</section>
<!-- END: AccountTimelineSection -->

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
    var slides = document.querySelectorAll('.hero-slider__slide');
    if (slides.length) {
        var index = 0;
        setInterval(function () {
            slides[index].classList.remove('is-active');
            index = (index + 1) % slides.length;
            slides[index].classList.add('is-active');
        }, 5000);
    }
    var mediaEls = document.querySelectorAll('.account-timeline__media');
    if (mediaEls.length && 'IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -40px 0px' });
        mediaEls.forEach(function (el) { observer.observe(el); });
    } else {
        mediaEls.forEach(function (el) { el.classList.add('is-visible'); });
    }
})();
</script>
</body>
</html>

<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = $siteName . ' - Retirement Plans, Investing, Brokerage';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
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
<section class="py-20 mx-auto px-4 max-w-6xl">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
<!-- Left sidebar nav -->
<div class="lg:col-span-3 space-y-4 border-l-2 border-gray-100">
<button class="block w-full text-left pl-4 py-2 border-l-4 border-fidelityGreen font-bold text-sm">Start investing</button>
<button class="block w-full text-left pl-4 py-2 text-gray-500 hover:text-fidelityGreen text-sm">Save for retirement</button>
<button class="block w-full text-left pl-4 py-2 text-gray-500 hover:text-fidelityGreen text-sm">Save for health care</button>
<button class="block w-full text-left pl-4 py-2 text-gray-500 hover:text-fidelityGreen text-sm">Invest for a child</button>
</div>
<!-- Main Content Area -->
<div class="lg:col-span-6">
<h2 class="text-4xl mb-8 leading-tight">Invest smart from the start with a brokerage account</h2>
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
<div class="flex items-center space-x-6">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open a brokerage account</a>
<a class="text-fidelityGreen font-bold hover:underline" href="/investing">Explore ways to invest</a>
</div>
</div>
<!-- Phone App Mockup Visual -->
<div class="lg:col-span-3">
<div class="bg-fidelityLightGreen rounded-3xl p-6 relative">
<img alt="<?php echo htmlspecialchars($siteName); ?> App" class="rounded-2xl shadow-2xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBlYMQLYWxL_RRl-5SOmShdC6hhlnZmPa96FDPtMPcRvaR9sO9ERXdbuD7YuyAISXe53Qo5sxtONpYyXNUzLhT57merNvjxXl0swczCCf9kCmrTa1EmTSnxWy6TfSzPZzDm-1mmblmnPJkSGFqI7Ze5sT-CJJSmhV06QrimoudsRiRYC7iCNiDn_P8pnZ11wWmDU8nKK3lfNQxAkl9Pew8i5VGSX-xHj9qHGStweeoY8sqp1vUCRWEksA">
<div class="absolute -right-4 top-20 bg-white p-3 rounded shadow-lg border border-gray-100">
<div class="text-fidelityGreen font-bold text-xs">+18.04</div>
<div class="text-xl font-bold">0.51%</div>
</div>
</div>
</div>
</div>
</section>
<!-- END: AccountSelectorSection -->

<!-- BEGIN: AlternatingSections -->
<!-- Roth IRA -->
<section class="py-20 bg-gray-50">
<div class="mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center max-w-6xl">
<div class="order-2 lg:order-1">
<h2 class="text-4xl mb-8">Plan for the possibilities ahead with a Roth IRA</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
<div>
<h4 class="font-bold mb-2">Tax savings</h4>
<p class="text-sm text-gray-600">Any earnings growth in a Roth is tax-free, with tax-free withdrawals in retirement.⁴</p>
</div>
<div>
<h4 class="font-bold mb-2">Access to your contributions</h4>
<p class="text-sm text-gray-600">Any amount you add to your Roth can be withdrawn without taxes or penalties, anytime, for any reason.</p>
</div>
<div>
<h4 class="font-bold mb-2">Numerous ways to invest</h4>
<p class="text-sm text-gray-600">Whether you invest on your own or have us do it, you can choose from stocks to ETFs to crypto and more.</p>
</div>
</div>
<div class="flex items-center space-x-6">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold" href="/register">Open a Roth IRA</a>
<a class="text-fidelityGreen font-bold hover:underline" href="/planning">Explore retirement planning</a>
</div>
</div>
<div class="order-1 lg:order-2">
<img alt="Roth IRA visual" class="rounded-lg shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDY3iqufo2KD57yEPGm4rXJ227VAhC0aUplFZJy6Qbx-yvsd74PYZjLppio9NDPIeZCPSAXK2muoBGBO_bL4xMdO6u6UcVtlgMymzRFgQnGW7Sg_YVkSaagnuCNhn-BD25bJTRON00EpHPfAUVYIkDeSo1salecSpJpIc9K9Bdm0RKblapyV6YCtuJTsfJM5k_oHskXUeHHtQceBKP4gmhWEcYSm09rXv3PDJhAMI5zvP5vt_JnYH0z6g">
</div>
</div>
</section>

<!-- HSA Section -->
<section class="py-20 bg-white">
<div class="mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center max-w-6xl">
<div>
<img alt="HSA visual" class="rounded-lg shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC8ciWYMIH8LaIoT1_SlnTnSh0Qhuu0cN0dyKiNhwqoQ-9o-9vP8PBC25hkwOiJDqs9Hsn9DFAU3MchWg-JRyE5MIbpoh6ILqo1snva0YjAJDBBPNug0HP2HFLIJpRCbkg4skiwwWVTGbWyCPiswRrULzaAydUQByEk4UMFIlhJh3S_930yfsiFD3gLi6aQjSwKeNYnvpHz_tn2wu6sM_pdtrhw0wILDoFcviV-BSp5FkFwrOql_k8Arw">
</div>
<div>
<h2 class="text-4xl mb-8">Save, earn, and invest for health care with an HSA</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
<div>
<h4 class="font-bold mb-2">Triple-tax advantage</h4>
<p class="text-sm text-gray-600">Get tax-deductible contributions, no immediate tax on earnings, and tax-free withdrawals for qualified medical expenses.⁵</p>
</div>
<div>
<h4 class="font-bold mb-2">No account fees</h4>
<p class="text-sm text-gray-600"><?php echo htmlspecialchars($siteName); ?>'s HSA has no account fees or minimums, and $0 commissions for US stock &amp; ETF trades.⁶</p>
</div>
<div class="col-span-full">
<h4 class="font-bold mb-2">Not "use-it-or-lose-it"</h4>
<p class="text-sm text-gray-600">The money's always yours. You can earn interest on cash, grow your account by investing, or do both.</p>
</div>
</div>
<div class="flex items-center space-x-6">
<a class="bg-fidelityGreen text-white px-8 py-3 rounded-full font-bold" href="/register">Open an HSA</a>
<a class="text-fidelityGreen font-bold hover:underline" href="#">Explore health savings at <?php echo htmlspecialchars($siteName); ?></a>
</div>
</div>
</div>
</section>
<!-- END: AlternatingSections -->

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
</body>
</html>

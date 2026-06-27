<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Customer Service | Fidelity';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
</head>
<body class="fidelity-subpage bg-background text-on-surface font-body-md antialiased overflow-x-hidden">
<?php $currentPage = 'help_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main>
<!-- Hero Section -->
<section class="relative py-xl bg-surface-container-lowest overflow-hidden">
<div class="absolute inset-0 opacity-10 pointer-events-none"></div>
<div class="max-content px-margin-mobile relative z-10">
<div class="max-w-2xl">
<h1 class="font-display-lg text-display-lg mb-md text-on-surface">Customer Service</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg">How can we help you today? Search our help articles or explore common tasks below.</p>
<div class="relative max-w-xl help-search-wrap">
<input class="w-full h-14 pl-12 pr-4 bg-white border border-surface-gray rounded-xl focus:ring-2 focus:ring-institutional-blue focus:border-institutional-blue font-body-md text-body-md outline-none shadow-sm" placeholder="Search our help articles" type="text"/>
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
</div>
</div>
</div>
</section>
<!-- Top Tasks Section -->
<section class="py-xl max-content px-margin-mobile">
<h2 class="font-headline-md text-headline-md mb-lg">Top Tasks</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
<a class="p-md bg-white card-shadow rounded-xl flex flex-col items-start group" href="/forgot-password">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center mb-md group-hover:bg-institutional-blue group-hover:text-white transition-colors">
<span class="material-symbols-outlined">lock_reset</span>
</div>
<span class="font-headline-md text-headline-md text-on-surface text-lg mb-xs">Reset your password</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Update your security settings and regain account access.</span>
</a>
<a class="p-md bg-white card-shadow rounded-xl flex flex-col items-start group" href="/dashboard/user/wallet">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center mb-md group-hover:bg-institutional-blue group-hover:text-white transition-colors">
<span class="material-symbols-outlined">payments</span>
</div>
<span class="font-headline-md text-headline-md text-on-surface text-lg mb-xs">Transfer money</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Move funds between your accounts or external banks.</span>
</a>
<a class="p-md bg-white card-shadow rounded-xl flex flex-col items-start group" href="/dashboard/user/profile">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center mb-md group-hover:bg-institutional-blue group-hover:text-white transition-colors">
<span class="material-symbols-outlined">person</span>
</div>
<span class="font-headline-md text-headline-md text-on-surface text-lg mb-xs">Update your profile</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Change your address, phone, or communication preferences.</span>
</a>
<a class="p-md bg-white card-shadow rounded-xl flex flex-col items-start group" href="/dashboard/user/transactions">
<div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center mb-md group-hover:bg-institutional-blue group-hover:text-white transition-colors">
<span class="material-symbols-outlined">description</span>
</div>
<span class="font-headline-md text-headline-md text-on-surface text-lg mb-xs">View statements</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Access tax forms, monthly statements, and trade confirms.</span>
</a>
</div>
</section>
<!-- Contact Us Section -->
<section class="py-xl bg-surface-container-low">
<div class="max-content px-margin-mobile">
<div class="flex flex-col lg:flex-row gap-lg items-center">
<div class="lg:w-1/2">
<h2 class="font-headline-lg text-headline-lg mb-md">Contact Us</h2>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg">Need immediate assistance? Our support team and AI assistants are ready to help you navigate your financial journey.</p>
<div class="space-y-sm">
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-md p-md bg-white rounded-xl border border-surface-gray">
<span class="material-symbols-outlined text-institutional-blue text-3xl">chat</span>
<div class="flex-grow">
<h4 class="font-label-md text-label-md uppercase text-institutional-blue">Live Chat</h4>
<p class="font-headline-md text-headline-md text-xl">Connect with an Agent</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Mon - Fri: 8 AM - 10 PM ET</p>
</div>
<a href="/live_chat" class="sm:ml-auto px-6 py-2 bg-institutional-blue text-white font-label-md rounded-xl whitespace-nowrap">Start Chat</a>
</div>
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-md p-md bg-white rounded-xl border border-surface-gray">
<span class="material-symbols-outlined text-fidelity-green text-3xl">smart_toy</span>
<div class="flex-grow">
<h4 class="font-label-md text-label-md uppercase text-fidelity-green">Fidelity Assistant</h4>
<p class="font-headline-md text-headline-md text-xl">Instant AI Support</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Available 24/7 for quick answers</p>
</div>
<a href="/live_chat" class="sm:ml-auto px-6 py-2 border border-fidelity-green text-fidelity-green font-label-md rounded-xl whitespace-nowrap">Ask Now</a>
</div>
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-md p-md bg-white rounded-xl border border-surface-gray">
<span class="material-symbols-outlined text-on-surface text-3xl">call</span>
<div class="flex-grow">
<h4 class="font-label-md text-label-md uppercase text-on-surface-variant">Call Us</h4>
<p class="font-headline-md text-headline-md text-xl">800-343-3548</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Standard rates may apply</p>
</div>
<a href="tel:8003433548" class="sm:ml-auto px-6 py-2 border border-on-surface text-on-surface font-label-md rounded-xl whitespace-nowrap">Call Now</a>
</div>
</div>
</div>
<div class="lg:w-1/2 w-full h-[400px] rounded-2xl overflow-hidden shadow-xl border border-white bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBpobIyY29t2qZhqxzEyO83xDK8P63gx-8dasloYIJqD8pj6FPW9466A6IUa1cYn-o5Sbz_H5H9sGEVaO8M2ZyiK6fU-ktjBmi8VjmArSBBA8YJCFHY6KqpNp7x0EfRetrVkKRP6jw1T5LevRDniRffSVvSYavyiEzpGxzlQiWljcMa5k2Yl6umo1UI9xbCVY5IZUsZJhXQOka1iq577KbcTD5BknnaPlC7feWmS-lEfcRFb6Gsd3HZbQ');"></div>
</div>
</div>
</section>
<!-- Help Categories Grid -->
<section class="py-xl max-content px-margin-mobile">
<h2 class="font-headline-md text-headline-md mb-lg">Help Categories</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
<div class="p-lg bg-white border border-surface-gray rounded-xl hover:bg-surface-container-lowest transition-all group">
<h3 class="font-headline-md text-headline-md mb-md flex items-center justify-between">
                        Accounts &amp; Trade
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">account_balance</span>
</h3>
<ul class="space-y-sm">
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/register">Portfolio Overview</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/investing">Trading Basics</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/dashboard/user/wallet">Cash Management</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/dashboard">Full View® Settings</a></li>
</ul>
</div>
<div class="p-lg bg-white border border-surface-gray rounded-xl hover:bg-surface-container-lowest transition-all group">
<h3 class="font-headline-md text-headline-md mb-md flex items-center justify-between">
                        Investing
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">trending_up</span>
</h3>
<ul class="space-y-sm">
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/investing">Self-Directed Trading</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/investing">Advanced Trading Tools</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/investing">Sustainable Investing</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/investing">Options Education</a></li>
</ul>
</div>
<div class="p-lg bg-white border border-surface-gray rounded-xl hover:bg-surface-container-lowest transition-all group">
<h3 class="font-headline-md text-headline-md mb-md flex items-center justify-between">
                        Retirement
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">enable</span>
</h3>
<ul class="space-y-sm">
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/planning">401(k) Rollovers</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/planning">IRA Transfers</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/planning">Distributions &amp; RMDs</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/planning">Planning Tools</a></li>
</ul>
</div>
<div class="p-lg bg-white border border-surface-gray rounded-xl hover:bg-surface-container-lowest transition-all group">
<h3 class="font-headline-md text-headline-md mb-md flex items-center justify-between">
                        Wealth Management
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">diamond</span>
</h3>
<ul class="space-y-sm">
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/wealth-management">Find a Financial Advisor</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/wealth-management">Estate Planning</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/wealth-management">Charitable Giving</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/wealth-management">Tax-Efficient Strategies</a></li>
</ul>
</div>
<div class="p-lg bg-white border border-surface-gray rounded-xl hover:bg-surface-container-lowest transition-all group">
<h3 class="font-headline-md text-headline-md mb-md flex items-center justify-between">
                        News &amp; Research
                        <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">analytics</span>
</h3>
<ul class="space-y-sm">
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/trading_signals">Stock Market Overview</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/trading_signals">Mutual Fund Screener</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/dashboard">Alerts &amp; Notifications</a></li>
<li><a class="font-body-md text-body-md text-institutional-blue hover:underline" href="/trading_signals">Earnings Calendar</a></li>
</ul>
</div>
<div class="p-lg bg-fidelity-green text-white rounded-xl flex flex-col justify-between">
<div>
<h3 class="font-headline-md text-headline-md mb-sm">Need a specialist?</h3>
<p class="font-body-sm text-body-sm mb-md opacity-90">Our dedicated team is here to help with complex financial needs and institutional wealth management.</p>
</div>
<a href="/live_chat" class="block w-full py-3 text-center bg-white text-fidelity-green font-label-md rounded-xl hover:bg-surface-container-lowest transition-colors">Schedule a Consultation</a>
</div>
</div>
</section>
<!-- Investor Center Section -->
<section class="py-xl bg-surface-container-high">
<div class="max-content px-margin-mobile">
<div class="flex flex-col md:flex-row gap-lg bg-white p-lg rounded-2xl border border-surface-gray items-center">
<div class="flex-1">
<div class="flex items-center gap-md mb-md">
<span class="material-symbols-outlined text-fidelity-green text-4xl" style="font-variation-settings: 'FILL' 1;">location_on</span>
<h2 class="font-headline-lg text-headline-lg">Find an Investor Center</h2>
</div>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg">Visit one of our hundreds of locations for face-to-face assistance and professional financial guidance.</p>
<div class="flex gap-xs">
<div class="relative flex-1">
<input class="w-full h-12 px-4 bg-surface-container-low border border-surface-gray rounded-xl font-body-md focus:ring-institutional-blue focus:border-institutional-blue outline-none" placeholder="Enter ZIP Code" type="text"/>
</div>
<button type="button" class="px-8 h-12 bg-on-surface text-white font-label-md rounded-xl hover:bg-black transition-colors">Search</button>
</div>
</div>
<div class="flex-1 w-full h-[250px] rounded-xl overflow-hidden">
<img class="w-full h-full object-cover" alt="Investor center map" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDvBHAwHNQ70bktcDLCmdUvNZkJOTkTbUmVZfuym4X7sxv1iUNHRX8qlrFZ_Mh0nyaCYXbIYqyW6YaCsuccXVRh-iuZaZBw2NMtnvDVMJP5340aUjJC7rF5DY0L4ke7MB_Kz_SNszGGmhdQjG74tz5um8-Zw_jaKGhd--M4uYfFg8mwd6Fdgt6g9bFRlEfBzW8DmyZ1I2Cmw034GgWexCaT0fhgQFl2EFIK7Y0cUOgYrhkmTwQFwd10tQ"/>
</div>
</div>
</div>
</section>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<script>
(function () {
    var searchWrap = document.querySelector('.help-search-wrap');
    var searchInput = searchWrap ? searchWrap.querySelector('input[type="text"]') : null;
    if (searchInput && searchWrap) {
        searchInput.addEventListener('focus', function () {
            searchWrap.classList.add('scale-[1.02]');
            searchWrap.style.transition = 'all 0.2s ease';
        });
        searchInput.addEventListener('blur', function () {
            searchWrap.classList.remove('scale-[1.02]');
        });
    }
})();
</script>
</body>
</html>

<?php
/**
 * Shared Marketing Header — Fidelity homepage design (utility bar + main navigation).
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session-bootstrap.php';
$siteName = get_site_name();
$currentUser = get_current_user_data();
$isLoggedIn = !empty($currentUser);
$current = $currentPage ?? '';
$navClass = function ($page) use ($current) {
    if ($current === $page) {
        return 'text-white font-bold border-b-2 border-white py-5';
    }
    return 'hover:text-fidelityLightGreen border-b-2 border-transparent hover:border-fidelityLightGreen py-5';
};
?>
<!-- BEGIN: UtilityHeader -->
<div id="utility-header" class="bg-white border-b border-gray-200 py-2">
<div class="mx-auto px-4 flex justify-end items-center space-x-6 text-xs text-fidelityGray max-w-6xl">
<a class="hover:underline" href="/help_centre">Customer Service</a>
<a class="hover:underline" href="/live_chat">Fidelity Assistant</a>
<?php if ($isLoggedIn): ?>
<a class="hover:underline" href="/dashboard">Profile</a>
<a class="bg-fidelityGreen text-white px-4 py-1 rounded-full font-bold hover:bg-fidelityGreenHover" href="/dashboard">Dashboard</a>
<a class="border border-fidelityGreen text-fidelityGreen px-4 py-1 rounded-full font-bold hover:bg-gray-50" href="/logout">Log out</a>
<?php else: ?>
<a class="hover:underline" href="/login">Profile</a>
<a class="bg-fidelityGreen text-white px-4 py-1 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open an account</a>
<a class="border border-fidelityGreen text-fidelityGreen px-4 py-1 rounded-full font-bold hover:bg-gray-50" href="/login">Log in</a>
<?php endif; ?>
</div>
</div>
<!-- END: UtilityHeader -->
<!-- BEGIN: MainNavigation -->
<header id="main-navigation" class="sticky top-0 z-50 shadow-sm bg-fidelityGreen">
<div class="mx-auto px-4 flex items-center h-16 max-w-6xl">
<!-- Logo -->
<div class="flex-shrink-0 mr-8">
<a href="/">
<img alt="Fidelity Investments" class="h-8" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCl4yTOAuZhyPUw2t4cUG2u6-qlk1Y9Riwvk_4QUgCFtmNuE69JxnZmwAMDuas547HUTAbUy-2VeHSLRRS040j2-beCp1ERNuiIysI8EBMWMfB3aaPQFyliEqG395gdWMZkc6hlApiGHhSPR_0vWeTnMMsgO-1wozgvWXiDmKaAndna4zjovsXylE2jv_mgpGUjkLdBWNvKidT7QtSJkassBvCrQtQPp3k6RlNc5H9buUhO0A5gK1sv4g">
</a>
</div>
<!-- Primary Nav -->
<nav class="hidden lg:flex space-x-6 fidelity-nav-text text-white">
<a class="<?php echo $navClass('accounts'); ?>" href="/register">Accounts &amp; Trade</a>
<a class="<?php echo $navClass('investing'); ?>" href="/investing">Investing</a>
<a class="<?php echo $navClass('planning'); ?>" href="/planning">Retirement</a>
<a class="<?php echo $navClass('wealth'); ?>" href="/wealth-management">Wealth Management</a>
<a class="<?php echo $navClass('news'); ?>" href="#">News &amp; Research</a>
</nav>
<!-- Search -->
<div class="ml-auto relative w-64">
<input class="w-full border border-gray-300 rounded-full py-1 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-fidelityGreen bg-white/95" placeholder="How can we help?" type="text">
<svg class="w-4 h-4 absolute right-3 top-2 text-fidelityGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
</header>
<!-- END: MainNavigation -->

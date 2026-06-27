<?php
/**
 * Shared Marketing Header — utility bar + main navigation.
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session-bootstrap.php';
$siteName = get_site_name();
$siteLogo = get_site_setting('site_logo', '');
$currentUser = get_current_user_data();
$isLoggedIn = !empty($currentUser);
$current = $currentPage ?? '';
$navClass = function ($page) use ($current) {
    if ($current === $page) {
        return 'text-white font-bold border-b-2 border-white pb-1';
    }
    return 'hover:text-fidelityLightGreen border-b-2 border-transparent hover:border-fidelityLightGreen pb-1';
};
?>
<!-- BEGIN: UtilityHeader -->
<div id="utility-header" class="bg-white border-b border-gray-200">
<div class="mx-auto px-4 flex justify-between items-center h-12 max-w-6xl">
<a href="/" class="flex-shrink-0 flex items-center">
<?php if (!empty($siteLogo)): ?>
<img alt="<?php echo htmlspecialchars($siteName); ?>" class="h-7 max-w-[180px] object-contain" src="<?php echo htmlspecialchars($siteLogo); ?>">
<?php else: ?>
<span class="text-fidelityGreen font-bold text-lg tracking-tight"><?php echo htmlspecialchars($siteName); ?></span>
<?php endif; ?>
</a>
<div class="flex items-center flex-wrap justify-end gap-x-6 gap-y-1 text-xs text-fidelityGray">
<a class="hover:underline" href="/help_centre">Customer Service</a>
<a class="hover:underline" href="/live_chat"><?php echo htmlspecialchars($siteName); ?> Assistant</a>
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
</div>
<!-- END: UtilityHeader -->
<!-- BEGIN: MainNavigation -->
<header id="main-navigation" class="sticky top-0 z-50 shadow-sm bg-fidelityGreen">
<div class="mx-auto px-4 flex items-center h-14 max-w-6xl gap-4">
<nav class="hidden lg:flex flex-1 items-center justify-evenly fidelity-nav-text text-white min-w-0">
<a class="<?php echo $navClass('home'); ?>" href="/">Home</a>
<a class="<?php echo $navClass('investing'); ?>" href="/investing">Investing</a>
<a class="<?php echo $navClass('planning'); ?>" href="/planning">Retirement</a>
<a class="<?php echo $navClass('wealth'); ?>" href="/wealth-management">Wealth Management</a>
</nav>
<div class="lg:hidden flex-1"></div>
<div class="relative w-full max-w-[220px] lg:max-w-xs lg:shrink-0">
<input class="w-full border border-gray-300 rounded-full py-1.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-fidelityGreen bg-white/95" placeholder="How can we help?" type="text">
<svg class="w-4 h-4 absolute right-3 top-2.5 text-fidelityGreen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</div>
</div>
</header>
<!-- END: MainNavigation -->

<?php
/**
 * Shared Marketing Header — utility bar + main navigation.
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session-bootstrap.php';
require_once __DIR__ . '/marketing-search.php';
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
$mobileNavClass = function ($page) use ($current) {
    if ($current === $page) {
        return 'text-fidelityGreen font-bold';
    }
    return 'text-fidelityDark hover:text-fidelityGreen';
};
?>
<style>
#utility-header {
  position: relative;
  z-index: 40;
}
#main-navigation {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  z-index: 50;
}
#main-navigation .marketing-search-wrap {
  position: relative;
}
.marketing-search-suggestions {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  z-index: 60;
  overflow: hidden;
  max-height: 220px;
  overflow-y: auto;
}
</style>
<script type="application/json" id="marketing-search-index"><?php echo json_encode(get_marketing_search_index(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
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
<a class="hidden lg:inline hover:underline" href="/help_centre">Customer Service</a>
<a class="hidden lg:inline hover:underline" href="/live_chat"><?php echo htmlspecialchars($siteName); ?> Assistant</a>
<?php if ($isLoggedIn): ?>
<a class="bg-fidelityGreen text-white px-4 py-1 rounded-full font-bold hover:bg-fidelityGreenHover" href="/dashboard">Dashboard</a>
<a class="hidden lg:inline border border-fidelityGreen text-fidelityGreen px-4 py-1 rounded-full font-bold hover:bg-gray-50" href="/logout">Log out</a>
<?php else: ?>
<a class="bg-fidelityGreen text-white px-4 py-1 rounded-full font-bold hover:bg-fidelityGreenHover" href="/register">Open an account</a>
<a class="hidden lg:inline border border-fidelityGreen text-fidelityGreen px-4 py-1 rounded-full font-bold hover:bg-gray-50" href="/login">Log in</a>
<?php endif; ?>
</div>
</div>
</div>
<!-- END: UtilityHeader -->
<!-- BEGIN: MainNavigation -->
<header id="main-navigation" class="shadow-sm bg-fidelityGreen">
<div class="mx-auto px-4 flex items-center h-14 max-w-6xl gap-3 lg:gap-4">
<button type="button" id="marketing-sidebar-toggle" class="lg:hidden shrink-0 w-10 h-10 flex items-center justify-center rounded-lg text-white hover:bg-white/10 transition-colors" aria-label="Open menu" aria-expanded="false" aria-controls="marketing-sidebar">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
</button>
<nav class="hidden lg:flex flex-1 items-center justify-start gap-10 fidelity-nav-text text-white min-w-0">
<a class="<?php echo $navClass('home'); ?>" href="/">Home</a>
<a class="<?php echo $navClass('investing'); ?>" href="/investing">Investing</a>
<a class="<?php echo $navClass('planning'); ?>" href="/planning">Retirement</a>
<a class="<?php echo $navClass('wealth'); ?>" href="/wealth-management">Wealth Management</a>
<a class="<?php echo $navClass('blog'); ?>" href="/blog">Blog</a>
<a class="<?php echo $navClass('legal_centre'); ?>" href="/legal_centre">Legal</a>
</nav>
<div class="marketing-search-wrap lg:hidden relative flex-1 min-w-0 max-w-[200px] sm:max-w-[240px] ml-auto">
<form class="marketing-search-form" role="search" action="/blog" method="get">
<label class="sr-only" for="marketing-mobile-search">Search site</label>
<input id="marketing-mobile-search" class="marketing-search-input w-full rounded-full py-1.5 pl-3 pr-8 text-xs bg-white/95 text-on-surface placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-white/80 border-0" type="search" placeholder="Search" autocomplete="off"/>
<button type="submit" class="sr-only">Search</button>
<svg class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 text-fidelityGreen pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</form>
<div class="marketing-search-suggestions hidden" role="listbox" aria-label="Search suggestions"></div>
</div>
<div class="marketing-search-wrap hidden lg:block relative w-full max-w-xs shrink-0 ml-auto">
<form class="marketing-search-form" role="search" action="/blog" method="get">
<label class="sr-only" for="marketing-desktop-search">Search site</label>
<input id="marketing-desktop-search" class="marketing-search-input w-full border border-gray-300 rounded-full py-1.5 px-4 pr-9 text-sm focus:outline-none focus:ring-1 focus:ring-fidelityGreen bg-white/95" type="search" placeholder="How can we help?" autocomplete="off"/>
<button type="submit" class="sr-only">Search</button>
<svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-fidelityGreen pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
</form>
<div class="marketing-search-suggestions hidden" role="listbox" aria-label="Search suggestions"></div>
</div>
</div>
</header>
<!-- END: MainNavigation -->

<!-- BEGIN: MarketingMobileSidebar -->
<div id="marketing-sidebar-overlay" class="fixed inset-0 bg-black/50 z-[60] lg:hidden hidden" aria-hidden="true"></div>
<aside id="marketing-sidebar" class="fixed inset-y-0 left-0 w-[min(100%,300px)] bg-white shadow-xl z-[70] flex flex-col transform -translate-x-full transition-transform duration-200 ease-out lg:hidden" aria-hidden="true">
<div class="flex items-center justify-between px-4 h-14 border-b border-gray-100">
<span class="text-fidelityGreen font-bold text-lg"><?php echo htmlspecialchars($siteName); ?></span>
<button type="button" id="marketing-sidebar-close" class="w-10 h-10 flex items-center justify-center rounded-lg text-fidelityDark hover:bg-gray-100 transition-colors" aria-label="Close menu">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
</button>
</div>
<?php if (!$isLoggedIn): ?>
<div class="px-4 py-3 border-b border-gray-100">
<a class="block w-full text-center border border-fidelityGreen text-fidelityGreen px-6 py-2.5 rounded-full font-bold hover:bg-gray-50 transition-colors" href="/login">Log in</a>
</div>
<?php else: ?>
<div class="px-4 py-3 border-b border-gray-100">
<a class="block w-full text-center border border-fidelityGreen text-fidelityGreen px-6 py-2.5 rounded-full font-bold hover:bg-gray-50 transition-colors" href="/logout">Log out</a>
</div>
<?php endif; ?>
<nav class="flex-1 overflow-y-auto p-4" aria-label="Main navigation">
<p class="text-xs font-bold uppercase tracking-wide text-fidelityGray mb-3">Menu</p>
<ul class="space-y-1 mb-6">
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('home'); ?>" href="/">Home</a></li>
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('investing'); ?>" href="/investing">Investing</a></li>
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('planning'); ?>" href="/planning">Retirement</a></li>
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('wealth'); ?>" href="/wealth-management">Wealth Management</a></li>
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('blog'); ?>" href="/blog">Blog</a></li>
<li><a class="block py-2.5 text-base <?php echo $mobileNavClass('legal_centre'); ?>" href="/legal_centre">Legal</a></li>
</ul>
<p class="text-xs font-bold uppercase tracking-wide text-fidelityGray mb-3">Account</p>
<ul class="space-y-1">
<li><a class="block py-2.5 text-base text-fidelityDark hover:text-fidelityGreen" href="/help_centre">Customer Service</a></li>
<li><a class="block py-2.5 text-base text-fidelityDark hover:text-fidelityGreen" href="/live_chat"><?php echo htmlspecialchars($siteName); ?> Assistant</a></li>
<?php if ($isLoggedIn): ?>
<li><a class="block py-2.5 text-base text-fidelityDark hover:text-fidelityGreen" href="/dashboard/user/profile">Profile</a></li>
<?php endif; ?>
</ul>
</nav>
</aside>
<script>
(function () {
  var overlay = document.getElementById('marketing-sidebar-overlay');
  var sidebar = document.getElementById('marketing-sidebar');
  var toggleBtn = document.getElementById('marketing-sidebar-toggle');
  var closeBtn = document.getElementById('marketing-sidebar-close');
  if (!overlay || !sidebar || !toggleBtn) return;

  function openMenu() {
    overlay.classList.remove('hidden');
    sidebar.classList.remove('-translate-x-full');
    sidebar.setAttribute('aria-hidden', 'false');
    toggleBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    overlay.classList.add('hidden');
    sidebar.classList.add('-translate-x-full');
    sidebar.setAttribute('aria-hidden', 'true');
    toggleBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  toggleBtn.addEventListener('click', function () {
    if (sidebar.classList.contains('-translate-x-full')) openMenu();
    else closeMenu();
  });
  if (closeBtn) closeBtn.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);
  sidebar.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', closeMenu);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) closeMenu();
  });
})();
</script>
<!-- END: MarketingMobileSidebar -->

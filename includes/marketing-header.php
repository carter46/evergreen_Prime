<?php
/**
 * Shared Marketing Header — Bloombit FX design system
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session-bootstrap.php';
$siteName = get_site_name();
$currentUser = get_current_user_data();
$isLoggedIn = !empty($currentUser);
$current = $currentPage ?? '';
$navActive = function ($page) use ($current) {
    return $current === $page
        ? 'text-white font-bold border-b-2 border-white pb-1 font-label-sm text-label-sm'
        : 'text-white/80 font-medium hover:text-white transition-colors duration-200 font-label-sm text-label-sm';
};
?>
<nav class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-xl border-b border-white/10 shadow-primary-container/10" id="marketing-nav">
<div class="max-w-[1440px] mx-auto flex justify-between items-center px-4 md:px-margin-desktop h-20">
<a class="font-display text-headline-md font-extrabold text-white tracking-tighter shrink-0" href="/"><?php echo htmlspecialchars($siteName); ?></a>
<div class="hidden md:flex items-center gap-8">
<a class="<?php echo $navActive('home'); ?>" href="/">Trade</a>
<a class="<?php echo $navActive('plans'); ?>" href="/plans">Invest</a>
<a class="<?php echo $navActive('trading_signals'); ?>" href="/trading_signals">Markets</a>
<a class="<?php echo ($current === 'help_centre' || $current === 'live_chat') ? 'text-white font-bold border-b-2 border-white pb-1 font-label-sm text-label-sm' : 'text-white/80 font-medium hover:text-white transition-colors duration-200 font-label-sm text-label-sm'; ?>" href="/help_centre">Learn</a>
</div>
<div class="flex items-center gap-2 sm:gap-4">
<?php if ($isLoggedIn): ?>
<a class="hidden sm:inline-flex text-white/80 font-medium hover:text-white transition-colors px-4 py-2 font-label-sm text-label-sm min-h-[44px] items-center" href="/logout">Logout</a>
<a class="btn-get-started font-bold px-4 sm:px-6 py-2 rounded-lg hover:scale-105 transition-transform font-label-sm text-label-sm min-h-[44px] flex items-center" href="/dashboard">Dashboard</a>
<?php else: ?>
<a class="hidden sm:inline-flex text-white/80 font-medium hover:text-white transition-colors px-4 py-2 font-label-sm text-label-sm min-h-[44px] items-center" href="/login">Login</a>
<a class="btn-get-started font-bold px-4 sm:px-6 py-2 rounded-lg hover:scale-105 transition-transform font-label-sm text-label-sm min-h-[44px] flex items-center" href="/register">Get Started</a>
<?php endif; ?>
<button type="button" id="mobile-menu-btn" class="md:hidden w-11 h-11 flex items-center justify-center rounded-lg hover:bg-surface-container-high transition-colors min-h-[44px] min-w-[44px] text-white" aria-label="Open menu" aria-expanded="false">
<span class="material-symbols-outlined">menu</span>
</button>
</div>
</div>
<div id="mobile-menu" class="hidden md:hidden border-t border-white/10 bg-surface-container-lowest">
<div class="max-w-[1440px] mx-auto px-4 py-4 flex flex-col gap-1">
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm <?php echo $current === 'home' ? 'text-white' : 'text-white/70'; ?>" href="/">Trade</a>
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm <?php echo $current === 'plans' ? 'text-white' : 'text-white/70'; ?>" href="/plans">Invest</a>
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm <?php echo $current === 'trading_signals' ? 'text-white' : 'text-white/70'; ?>" href="/trading_signals">Markets</a>
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm <?php echo ($current === 'help_centre' || $current === 'live_chat') ? 'text-white' : 'text-white/70'; ?>" href="/help_centre">Learn</a>
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm text-white/70" href="/about_us">About Us</a>
<a class="py-3 px-4 rounded-lg hover:bg-surface-container-high font-label-sm text-white/70" href="/legal_centre">Legal</a>
<?php if ($isLoggedIn): ?>
<a class="py-3 px-4 mt-2 border-t border-border-low pt-4 font-label-sm text-white" href="/dashboard">Dashboard</a>
<a class="py-3 px-4 font-label-sm text-white/70" href="/logout">Logout</a>
<?php else: ?>
<a class="py-3 px-4 mt-2 border-t border-border-low pt-4 font-label-sm text-white/70" href="/login">Login</a>
<a class="py-3 px-4 font-label-sm text-success font-bold" href="/register">Get Started</a>
<?php endif; ?>
</div>
</div>
</nav>
<script>
(function(){
var btn=document.getElementById('mobile-menu-btn');
var menu=document.getElementById('mobile-menu');
if(btn&&menu){
btn.addEventListener('click',function(){
var open=menu.classList.toggle('hidden');
btn.setAttribute('aria-expanded',!open);
btn.querySelector('.material-symbols-outlined').textContent=open?'menu':'close';
});
menu.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){menu.classList.add('hidden');btn.setAttribute('aria-expanded','false');btn.querySelector('.material-symbols-outlined').textContent='menu';});});
}
})();
</script>

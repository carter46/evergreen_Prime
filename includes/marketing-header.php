<?php
/**
 * Bloombit - Shared Marketing Header
 * Use on all front marketing pages: index, about_us, plans, help_centre, trading_signals, legal_centre
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session-bootstrap.php';
$siteName = get_site_name();
$currentUser = get_current_user_data();
$isLoggedIn = !empty($currentUser);
$current = $currentPage ?? '';
$navLinkClass = function ($page) use ($current) {
    return ($current === $page) ? 'text-primary border-b-2 border-primary pb-0.5' : 'hover:text-primary transition-colors';
};
?>
<nav class="sticky top-0 z-50 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-primary/10" id="marketing-nav">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
<a class="flex items-center gap-2 shrink-0 min-h-[44px] min-w-[44px] items-center" href="/">
<div class="w-10 h-10 bg-primary rounded flex items-center justify-center">
<span class="material-icons text-white">auto_awesome</span>
</div>
<span class="text-xl sm:text-2xl font-bold tracking-tight"><?php
$parts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];
echo htmlspecialchars($parts[0]);
if ($parts[1]) echo '<span class="text-primary">' . htmlspecialchars($parts[1]) . '</span>';
?></span>
</a>
<div class="hidden md:flex items-center gap-8 font-medium">
<a class="<?php echo $navLinkClass('home'); ?>" href="/">Home</a>
<a class="<?php echo $navLinkClass('about_us'); ?>" href="/about_us">About Us</a>
<a class="<?php echo $navLinkClass('plans'); ?>" href="/plans">Plans</a>
<a class="<?php echo $navLinkClass('trading_signals'); ?>" href="/trading_signals">Trading Signals</a>
<a class="<?php echo $navLinkClass('legal_centre'); ?>" href="/legal_centre">Legal</a>
<div class="relative group">
<a class="<?php echo ($current === 'help_centre' || $current === 'live_chat') ? 'text-primary border-b-2 border-primary pb-0.5' : 'hover:text-primary transition-colors'; ?> cursor-pointer flex items-center gap-1" href="/help_centre">
Help Center
<span class="material-icons text-sm">expand_more</span>
</a>
<div class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-lg shadow-xl border border-slate-200 dark:border-zinc-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
<a class="block px-4 py-3 hover:bg-primary/10 text-sm <?php echo $navLinkClass('help_centre'); ?>" href="/help_centre">Help Center</a>
<a class="block px-4 py-3 hover:bg-primary/10 text-sm <?php echo $navLinkClass('live_chat'); ?>" href="/live_chat">Live Chat</a>
</div>
</div>
</div>
<div class="flex items-center gap-2 sm:gap-4">
<?php if ($isLoggedIn): ?>
<a class="hidden sm:inline-flex px-4 sm:px-6 py-2.5 font-semibold hover:text-primary transition-colors min-h-[44px] items-center" href="/logout">Logout</a>
<a class="px-4 sm:px-6 py-2.5 bg-primary text-black font-bold rounded hover:shadow-lg hover:shadow-primary/20 transition-all min-h-[44px] flex items-center justify-center" href="/dashboard">Dashboard</a>
<?php else: ?>
<a class="hidden sm:inline-flex px-4 sm:px-6 py-2.5 font-semibold hover:text-primary transition-colors min-h-[44px] items-center" href="/login">Login</a>
<a class="px-4 sm:px-6 py-2.5 bg-primary text-black font-bold rounded hover:shadow-lg hover:shadow-primary/20 transition-all min-h-[44px] flex items-center justify-center" href="/register">Get Started</a>
<?php endif; ?>
<button type="button" id="mobile-menu-btn" class="md:hidden w-11 h-11 flex items-center justify-center rounded-lg hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px]" aria-label="Open menu" aria-expanded="false">
<span class="material-icons">menu</span>
</button>
</div>
</div>
<div id="mobile-menu" class="hidden md:hidden border-t border-primary/10 bg-white dark:bg-background-dark">
<div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-1">
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('home'); ?>" href="/">Home</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('about_us'); ?>" href="/about_us">About Us</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('plans'); ?>" href="/plans">Plans</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('trading_signals'); ?>" href="/trading_signals">Trading Signals</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('legal_centre'); ?>" href="/legal_centre">Legal</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('help_centre'); ?>" href="/help_centre">Help Center</a>
<a class="py-3 px-4 rounded-lg hover:bg-primary/10 font-medium <?php echo $navLinkClass('live_chat'); ?>" href="/live_chat">Live Chat</a>
<?php if ($isLoggedIn): ?>
<a class="py-3 px-4 mt-2 border-t border-slate-200 dark:border-slate-700 pt-4 font-medium" href="/dashboard">Dashboard</a>
<a class="py-3 px-4 font-medium" href="/logout">Logout</a>
<?php else: ?>
<a class="py-3 px-4 mt-2 border-t border-slate-200 dark:border-slate-700 pt-4 font-medium" href="/login">Login</a>
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
btn.querySelector('.material-icons').textContent=open?'menu':'close';
});
menu.querySelectorAll('a').forEach(function(a){a.addEventListener('click',function(){menu.classList.add('hidden');btn.setAttribute('aria-expanded','false');btn.querySelector('.material-icons').textContent='menu';});});
}
})();
</script>

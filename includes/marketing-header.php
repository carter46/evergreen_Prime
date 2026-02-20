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
<div class="sticky top-0 z-50" id="marketing-nav-shell">
<div class="bg-white/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-primary/10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-10 sm:h-11 flex items-center gap-2">
<span class="text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Language</span>
<div class="bb-lang-switcher relative">
<button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-background-dark/40 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:border-primary/50 hover:bg-white dark:hover:bg-background-dark transition-colors" data-bb-lang-button>
<span data-bb-lang-current>English</span>
<span class="material-icons text-base opacity-70">expand_more</span>
</button>
<div class="hidden absolute left-0 top-full mt-2 w-56 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 shadow-2xl overflow-hidden z-[99999]" data-bb-lang-menu>
<div class="max-h-72 overflow-auto py-1">
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="en">English</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="es">Español</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="fr">Français</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="de">Deutsch</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="it">Italiano</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="pt">Português</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="nl">Nederlands</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ru">Русский</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="uk">Українська</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="pl">Polski</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="tr">Türkçe</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ar">العربية</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="fa">فارسی</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="hi">हिन्दी</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="bn">বাংলা</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ur">اردو</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="zh-CN">简体中文</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="zh-TW">繁體中文</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ja">日本語</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ko">한국어</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="vi">Tiếng Việt</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="th">ไทย</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="id">Bahasa Indonesia</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ms">Bahasa Melayu</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="tl">Filipino</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="sw">Kiswahili</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="he">עברית</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="el">Ελληνικά</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="ro">Română</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10" data-bb-lang="sv">Svenska</button>
</div>
</div>
</div>
<div class="bb-gtranslate-hidden" aria-hidden="true"></div>
</div>
</div>
<nav class="bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-primary/10" id="marketing-nav">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
<a class="flex items-center gap-2 shrink-0 min-h-[44px] min-w-[44px] items-center" href="/">
<div class="w-10 h-10 bg-primary rounded flex items-center justify-center">
<span class="material-icons text-white">auto_awesome</span>
</div>
<span class="text-xl sm:text-2xl font-bold tracking-tight"><?php
[$brandBase, $brandAccent] = get_site_brand_parts($siteName);
echo htmlspecialchars($brandBase);
if ($brandAccent !== '') echo '<span class="text-primary">' . htmlspecialchars($brandAccent) . '</span>';
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
</div>
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

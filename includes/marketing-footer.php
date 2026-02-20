<?php
require_once __DIR__ . '/helpers.php';
$siteName = get_site_name();
$footerDesc = get_site_setting('footer_description', 'Leading the future of decentralized finance with advanced artificial intelligence and machine learning technologies.');
?>
<footer class="bg-white dark:bg-background-dark border-t border-primary/10 pt-12 sm:pt-20 pb-10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 sm:gap-12 mb-12 sm:mb-16">
<div class="col-span-1 md:col-span-1">
<div class="flex items-center gap-2 mb-6">
<div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
<span class="material-icons text-white text-sm">auto_awesome</span>
</div>
<span class="text-xl font-bold tracking-tight"><?php
[$brandBase, $brandAccent] = get_site_brand_parts($siteName);
echo htmlspecialchars($brandBase);
if ($brandAccent !== '') echo '<span class="text-primary">' . htmlspecialchars($brandAccent) . '</span>';
?></span>
</div>
<p class="text-slate-500 text-sm mb-6 leading-relaxed"><?php echo htmlspecialchars($footerDesc); ?></p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center hover:bg-primary transition-colors" href="#"><span class="material-icons text-lg">link</span></a>
<a class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center hover:bg-primary transition-colors" href="#"><span class="material-icons text-lg">link</span></a>
<a class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center hover:bg-primary transition-colors" href="#"><span class="material-icons text-lg">code</span></a>
</div>
</div>
<div>
<h4 class="font-bold mb-4 sm:mb-6">Product</h4>
<ul class="space-y-2 sm:space-y-4 text-sm text-slate-500">
<li><a class="hover:text-primary transition-colors py-2 block" href="/#how-it-works">AI Strategies</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/#how-it-works">Trading Bots</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/#how-it-works">Risk Management</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/plans">Plans</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-4 sm:mb-6">Company</h4>
<ul class="space-y-2 sm:space-y-4 text-sm text-slate-500">
<li><a class="hover:text-primary transition-colors py-2 block" href="/about_us">About Us</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="#">Careers</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="#">Security</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="#">Partners</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-4 sm:mb-6">Resources</h4>
<ul class="space-y-2 sm:space-y-4 text-sm text-slate-500">
<li><a class="hover:text-primary transition-colors py-2 block" href="/live_chat">Live Chat</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/help_centre">Help Center</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/trading_signals">Trading Signals</a></li>
<li><a class="hover:text-primary transition-colors py-2 block" href="/legal_centre">Legal</a></li>
</ul>
</div>
</div>
<div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
<div class="text-sm text-slate-400">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</div>
<div class="flex flex-wrap gap-4 sm:gap-6 text-sm text-slate-400">
<a class="hover:text-primary transition-colors py-2 block" href="/legal_centre">Terms of Service</a>
<a class="hover:text-primary transition-colors py-2 block" href="/legal_centre">Cookies Policy</a>
</div>
</div>
</div>
</footer>
<?php require_once __DIR__ . '/translation-widget.php'; ?>
<?php require_once __DIR__ . '/live-chat-widget.php'; ?>

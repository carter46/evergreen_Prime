<?php
require_once __DIR__ . '/helpers.php';
$siteName = get_site_name();
$footerDesc = get_site_setting('footer_description', 'Leading the future of decentralized finance with advanced artificial intelligence and machine learning technologies.');
$homepageModalImage = get_site_setting('homepage_modal_image', '');
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
<button id="footer-certificate-btn" type="button" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold bg-green-600 hover:bg-green-700 text-white transition-colors rounded-lg px-3 py-1.5">
<span class="material-icons text-base text-white notranslate" translate="no">verified</span>
<span class="notranslate" translate="no">View Certificate</span>
</button>
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

<!-- Certificate modal (shown on all pages so View Certificate works in footer) -->
<div id="homepage-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
<div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl flex items-center justify-center max-w-[95vw] max-h-[95vh] p-4">
<button id="homepage-modal-close" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" aria-label="Close">
<span class="material-icons text-slate-600 dark:text-slate-300">close</span>
</button>
<?php if (!empty($homepageModalImage)): ?>
<img src="<?php echo htmlspecialchars($homepageModalImage); ?>" alt="Certificate" class="max-w-[90vw] max-h-[90vh] w-auto h-auto object-contain rounded-lg"/>
<?php else: ?>
<div class="p-12 text-center min-w-[300px]">
<p class="text-slate-500 dark:text-slate-400 text-lg">No image uploaded yet.</p>
<p class="text-slate-400 dark:text-slate-500 text-sm mt-2">Upload an image in Admin Settings → Branding → Homepage Floating Modal Image</p>
</div>
<?php endif; ?>
</div>
</div>
<script>
(function(){
  var btn = document.getElementById('footer-certificate-btn');
  var modal = document.getElementById('homepage-modal');
  var close = document.getElementById('homepage-modal-close');
  if (!btn || !modal || !close) return;
  btn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow = 'hidden'; });
  close.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; });
  modal.addEventListener('click', function(e){ if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !modal.classList.contains('hidden')) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
})();
</script>

<!-- Fixed language widget (bottom-left) -->
<div id="bb-floating-language" class="fixed bottom-6 left-6 z-[70] notranslate" translate="no">
<div class="bb-lang-switcher relative notranslate" translate="no">
<button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/90 dark:bg-zinc-900/90 text-slate-700 dark:text-slate-200 text-sm font-semibold shadow-lg hover:border-primary/50 hover:bg-white dark:hover:bg-zinc-900 transition-colors" data-bb-lang-button>
<img data-bb-lang-flag alt="" class="w-4 h-4 rounded-sm object-cover" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" />
<span data-bb-lang-current class="notranslate" translate="no">English</span>
<svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" aria-hidden="true">
  <path d="M12 3h9v3h-2.1a12.6 12.6 0 0 1-3.3 6.2 13.2 13.2 0 0 0 3.8 1.8l-1.2 2.7a15.6 15.6 0 0 1-4.8-2.6 15.4 15.4 0 0 1-4.2 2.5l-1.1-2.6c1.3-.5 2.5-1.1 3.6-1.9A12.8 12.8 0 0 1 9.4 8H12V6H8V3h4zm-1.3 5a10.1 10.1 0 0 0 2.9 4.6A10.2 10.2 0 0 0 16.1 6H10.7V8z" fill="currentColor"/>
  <path d="M3 21l5.2-14h2.3L16 21h-2.6l-1.2-3.3H6.7L5.5 21H3zm4.4-5.7h4.1L9.4 9.6 7.4 15.3z" fill="currentColor" opacity=".65"/>
</svg>
</button>
<div class="hidden absolute left-0 bottom-full mb-2 w-60 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 shadow-2xl overflow-hidden notranslate" translate="no" data-bb-lang-menu>
<div class="max-h-72 overflow-auto py-1" data-bb-lang-items></div>
</div>
</div>
<div class="bb-gtranslate-hidden" aria-hidden="true"></div>
</div>

<script src="/js/app.js"></script>
<?php require_once __DIR__ . '/translation-widget.php'; ?>
<?php require_once __DIR__ . '/live-chat-widget.php'; ?>

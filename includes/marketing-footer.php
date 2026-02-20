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
<button id="footer-certificate-btn" type="button" class="hidden hover:text-primary transition-colors py-2 block font-semibold">View Certificate</button>
</div>
</div>
</div>
</footer>

<!-- Fixed language widget (bottom-left) -->
<div id="bb-floating-language" class="fixed bottom-6 left-6 z-[70] notranslate" translate="no">
<div class="bb-lang-switcher relative notranslate" translate="no">
<button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/90 dark:bg-zinc-900/90 text-slate-700 dark:text-slate-200 text-sm font-semibold shadow-lg hover:border-primary/50 hover:bg-white dark:hover:bg-zinc-900 transition-colors" data-bb-lang-button>
<img data-bb-lang-flag alt="" class="w-4 h-4 rounded-sm object-cover" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" />
<span data-bb-lang-current class="notranslate" translate="no">English</span>
<span class="material-icons text-base opacity-70">translate</span>
</button>
<div class="hidden absolute left-0 bottom-full mb-2 w-60 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-900 shadow-2xl overflow-hidden notranslate" translate="no" data-bb-lang-menu>
<div class="max-h-72 overflow-auto py-1">
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="en">English</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="es">Español</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="fr">Français</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="de">Deutsch</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="it">Italiano</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="pt">Português</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="nl">Nederlands</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ru">Русский</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="uk">Українська</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="pl">Polski</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="tr">Türkçe</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ar">العربية</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="fa">فارسی</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="hi">हिन्दी</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="bn">বাংলা</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ur">اردو</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="zh-CN">简体中文</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="zh-TW">繁體中文</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ja">日本語</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ko">한국어</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="vi">Tiếng Việt</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="th">ไทย</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="id">Bahasa Indonesia</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ms">Bahasa Melayu</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="tl">Filipino</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="sw">Kiswahili</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="he">עברית</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="el">Ελληνικά</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ro">Română</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="sv">Svenska</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="cs">Čeština</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="da">Dansk</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="fi">Suomi</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="no">Norsk</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="hu">Magyar</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="bg">Български</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="sr">Српски</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="sk">Slovenčina</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="sl">Slovenščina</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="hr">Hrvatski</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="lt">Lietuvių</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="lv">Latviešu</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="et">Eesti</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="ta">தமிழ்</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="te">తెలుగు</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="mr">मराठी</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="gu">ગુજરાતી</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="pa">ਪੰਜਾਬੀ</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="am">አማርኛ</button>
<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate" translate="no" data-bb-lang="af">Afrikaans</button>
</div>
</div>
</div>
<div class="bb-gtranslate-hidden" aria-hidden="true"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('footer-certificate-btn');
  if (!btn) return;
  var modal = document.getElementById('homepage-modal');
  if (modal) btn.classList.remove('hidden');
});
</script>
<?php require_once __DIR__ . '/translation-widget.php'; ?>
<?php require_once __DIR__ . '/live-chat-widget.php'; ?>

<?php
require_once __DIR__ . '/helpers.php';
$siteName = get_site_name();
$footerDesc = get_site_setting('footer_description', 'Empowering high-net-worth investors with professional tools and global market access since 2018.');
$homepageModalImage = get_site_setting('homepage_modal_image', '');
?>
<footer class="bg-surface-container-lowest border-t border-white/5 py-section-padding">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop grid grid-cols-1 md:grid-cols-4 gap-gutter">
<div class="col-span-1">
<div class="font-display text-headline-md font-extrabold text-primary mb-6"><?php echo htmlspecialchars($siteName); ?></div>
<p class="text-on-secondary-container text-body-md mb-6 pr-0 md:pr-8"><?php echo htmlspecialchars($footerDesc); ?></p>
<div class="flex gap-4">
<a class="text-on-secondary-container hover:text-primary transition-opacity opacity-70 hover:opacity-100" href="/about_us" aria-label="About us">
<span class="material-symbols-outlined">public</span>
</a>
<a class="text-on-secondary-container hover:text-primary transition-opacity opacity-70 hover:opacity-100" href="/trading_signals" aria-label="Markets">
<span class="material-symbols-outlined">share</span>
</a>
<a class="text-on-secondary-container hover:text-primary transition-opacity opacity-70 hover:opacity-100" href="/live_chat" aria-label="Contact">
<span class="material-symbols-outlined">mail</span>
</a>
</div>
<?php if (!empty($homepageModalImage)): ?>
<button id="footer-certificate-btn" type="button" class="mt-6 inline-flex items-center gap-2 text-label-sm font-label-sm bg-success/20 hover:bg-success/30 text-success transition-colors rounded-lg px-4 py-2 border border-success/30">
<span class="material-symbols-outlined text-base">verified</span>
View Certificate
</button>
<?php endif; ?>
</div>
<div>
<h4 class="font-bold text-primary mb-6 font-label-sm">Company</h4>
<ul class="space-y-4">
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/about_us">About Us</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/about_us">Our Leadership</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="#">Careers</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="#">Press Kit</a></li>
</ul>
</div>
<div>
<h4 class="font-bold text-primary mb-6 font-label-sm">Support</h4>
<ul class="space-y-4">
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/help_centre">Help Center</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/live_chat">Contact Concierge</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/live_chat">Institutional Support</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/trading_signals">Market Data</a></li>
</ul>
</div>
<div>
<h4 class="font-bold text-primary mb-6 font-label-sm">Compliance</h4>
<ul class="space-y-4">
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/legal_centre">Privacy Policy</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/legal_centre">Terms of Service</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/legal_centre">Risk Disclosure</a></li>
<li><a class="text-on-secondary-container hover:text-on-surface transition-opacity font-body-md" href="/legal_centre">Regulatory Info</a></li>
</ul>
</div>
</div>
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop mt-20 pt-10 border-t border-white/5">
<p class="text-on-secondary-container font-label-xs text-label-xs leading-relaxed max-w-4xl">
© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. High-risk investment products may not be suitable for all investors. The trading of currencies and digital assets involves significant risk. You should only invest capital you can afford to lose.
</p>
<div class="flex flex-wrap gap-6 mt-6">
<a class="text-on-secondary-container font-label-xs text-label-xs hover:text-primary underline" href="/legal_centre">Privacy Policy</a>
<a class="text-on-secondary-container font-label-xs text-label-xs hover:text-primary underline" href="/legal_centre">Risk Disclosure</a>
<a class="text-on-secondary-container font-label-xs text-label-xs hover:text-primary underline" href="/legal_centre">Terms of Service</a>
<a class="text-on-secondary-container font-label-xs text-label-xs hover:text-primary underline" href="/legal_centre">Security</a>
<a class="text-on-secondary-container font-label-xs text-label-xs hover:text-primary underline" href="/help_centre">Help Center</a>
</div>
</div>
</footer>

<?php if (!empty($homepageModalImage)): ?>
<div id="homepage-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
<div class="relative bg-surface-container rounded-2xl shadow-2xl flex items-center justify-center max-w-[95vw] max-h-[95vh] p-4 border border-border-low">
<button id="homepage-modal-close" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-surface-container-high hover:bg-surface-bright flex items-center justify-center transition-colors" aria-label="Close">
<span class="material-symbols-outlined text-on-surface">close</span>
</button>
<img src="<?php echo htmlspecialchars($homepageModalImage); ?>" alt="Certificate" class="max-w-[90vw] max-h-[90vh] w-auto h-auto object-contain rounded-lg"/>
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
<?php endif; ?>

<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/app-script.php'; ?>
<?php require_once __DIR__ . '/translation-widget.php'; ?>
<?php require_once __DIR__ . '/live-chat-widget.php'; ?>

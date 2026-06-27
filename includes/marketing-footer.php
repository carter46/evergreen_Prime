<?php
require_once __DIR__ . '/helpers.php';
$siteName = get_site_name();
$homepageModalImage = get_site_setting('homepage_modal_image', '');
?>
<!-- BEGIN: Footer -->
<footer class="bg-gray-100 py-12">
<div class="mx-auto px-4 max-w-6xl">
<div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
<div>
<h5 class="font-bold mb-4">About Fidelity</h5>
<ul class="text-sm text-gray-600 space-y-2">
<li class=""><a class="hover:underline" href="/live_chat">Contact Us</a></li>
<li class=""><a class="hover:underline" href="#">Careers</a></li>
<li class=""><a class="hover:underline" href="#">Newsroom</a></li>
</ul>
</div>
<div>
<h5 class="font-bold mb-4">Planning</h5>
<ul class="text-sm text-gray-600 space-y-2">
<li class=""><a class="hover:underline" href="/planning">Retirement</a></li>
<li class=""><a class="hover:underline" href="#">Life Events</a></li>
<li class=""><a class="hover:underline" href="#">College Savings</a></li>
</ul>
</div>
<div>
<h5 class="font-bold mb-4">Investments</h5>
<ul class="text-sm text-gray-600 space-y-2">
<li class=""><a class="hover:underline" href="/investing">Mutual Funds</a></li>
<li class=""><a class="hover:underline" href="/investing">ETFs</a></li>
<li class=""><a class="hover:underline" href="/investing">Fixed Income</a></li>
</ul>
</div>
<div>
<h5 class="font-bold mb-4">Legal &amp; Privacy</h5>
<ul class="text-sm text-gray-600 space-y-2">
<li class=""><a class="hover:underline" href="/legal_centre#terms">Terms of Use</a></li>
<li class=""><a class="hover:underline" href="/legal_centre#privacy">Privacy Policy</a></li>
<li class=""><a class="hover:underline" href="/legal_centre#protection">Security</a></li>
</ul>
</div>
</div>
<div class="pt-8 border-t border-gray-200 text-center text-xs text-gray-500">
<p class="">© 1998-<?php echo date('Y'); ?> FMR LLC. All rights reserved.</p>
<p class="mt-2">Fidelity Brokerage Services LLC, Member NYSE, SIPC, 900 Salem Street, Smithfield, RI 02917</p>
</div>
</div>
</footer>
<!-- END: Footer -->

<?php if (!empty($homepageModalImage)): ?>
<div id="homepage-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4">
<div class="relative bg-white rounded-2xl shadow-2xl flex items-center justify-center max-w-[95vw] max-h-[95vh] p-4 border border-gray-200">
<button id="homepage-modal-close" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors" aria-label="Close">
<span class="material-symbols-outlined text-fidelityDark">close</span>
</button>
<img src="<?php echo htmlspecialchars($homepageModalImage); ?>" alt="Certificate" class="max-w-[90vw] max-h-[90vh] w-auto h-auto object-contain rounded-lg"/>
</div>
</div>
<script>
(function(){
  var modal = document.getElementById('homepage-modal');
  var close = document.getElementById('homepage-modal-close');
  if (!modal || !close) return;
  close.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; });
  modal.addEventListener('click', function(e){ if (e.target === modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
  document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !modal.classList.contains('hidden')) { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.style.overflow = ''; } });
})();
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/pwa-install-modal.php'; ?>
<?php $pwaInstallVer = (int) @filemtime(dirname(__DIR__) . '/js/pwa-install.js'); ?>
<script src="/js/pwa-install.js?v=<?php echo $pwaInstallVer; ?>" defer></script>

<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/app-script.php'; ?>
<?php require_once __DIR__ . '/translation-widget.php'; ?>
<?php require_once __DIR__ . '/live-chat-widget.php'; ?>

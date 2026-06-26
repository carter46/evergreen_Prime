<?php
/**
 * PWA install instructions modal (iOS A2HS + browser fallbacks).
 */
require_once __DIR__ . '/helpers.php';
$siteName = htmlspecialchars(get_site_name());
?>
<div id="pwa-install-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4" role="dialog" aria-modal="true" aria-labelledby="pwa-install-title">
<div class="relative bg-surface-container-high border border-border-low rounded-2xl shadow-2xl max-w-lg w-full p-6 md:p-8">
<button type="button" id="pwa-install-modal-close" class="absolute top-3 right-3 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-bright transition-colors" aria-label="Close">
<span class="material-symbols-outlined text-on-surface">close</span>
</button>
<h2 id="pwa-install-title" class="font-headline-md text-headline-md text-on-surface mb-2 pr-10">Install <?php echo $siteName; ?></h2>
<p class="text-on-secondary-container text-sm mb-6">Add the app to your device for a native, standalone experience — no app store required.</p>

<div data-pwa-panel="ios" class="hidden space-y-4">
<p class="text-on-surface font-semibold text-sm">On iPhone / iPad (Safari):</p>
<ol class="list-decimal list-inside space-y-2 text-on-secondary-container text-sm">
<li>Tap the <strong>Share</strong> button <span class="material-symbols-outlined text-base align-middle">ios_share</span> at the bottom of Safari</li>
<li>Scroll down and tap <strong>Add to Home Screen</strong></li>
<li>Tap <strong>Add</strong> — the <?php echo $siteName; ?> icon will appear on your home screen</li>
</ol>
</div>

<div data-pwa-panel="desktop" class="hidden space-y-4">
<p class="text-on-surface font-semibold text-sm">On desktop (Chrome / Edge):</p>
<ol class="list-decimal list-inside space-y-2 text-on-secondary-container text-sm">
<li>Look for the <strong>Install</strong> icon in the address bar, or</li>
<li>Open the browser menu (⋮) → <strong>Install <?php echo $siteName; ?></strong> or <strong>Apps → Install this site as an app</strong></li>
<li>Confirm install — the app will open in its own window</li>
</ol>
</div>

<div data-pwa-panel="mobile" class="hidden space-y-4">
<p class="text-on-surface font-semibold text-sm">On Android (Chrome):</p>
<ol class="list-decimal list-inside space-y-2 text-on-secondary-container text-sm">
<li>Tap the browser menu (⋮) in the top right</li>
<li>Tap <strong>Install app</strong> or <strong>Add to Home screen</strong></li>
<li>Confirm — <?php echo $siteName; ?> will appear on your home screen</li>
</ol>
</div>
</div>
</div>

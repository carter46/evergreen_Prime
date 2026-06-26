<?php
/**
 * Investment risk disclosure block for market detail pages.
 */
$siteName = get_site_name();
?>
<section class="market-risk-section py-12 md:py-16 bg-surface-container-low border-y border-border-low">
<div class="max-w-[1440px] mx-auto px-4 md:px-margin-desktop">
<div class="bg-surface-container-high border border-border-low rounded-2xl p-8 md:p-10">
<div class="flex items-start gap-4 mb-6">
<span class="material-symbols-outlined text-primary-container text-3xl shrink-0">warning</span>
<h2 class="font-headline-md text-headline-md text-on-surface">Investment Risk Disclosure</h2>
</div>
<ul class="space-y-4 text-on-secondary-container text-body-md max-w-3xl">
<li>All investments involve risk. Market prices can rise or fall significantly and you may lose some or all of your invested capital.</li>
<li>Past performance is not indicative of future results. Historical returns do not guarantee future outcomes.</li>
<li>Only invest capital you can afford to lose. High-volatility assets may not be suitable for every investor.</li>
<li><?php echo htmlspecialchars($siteName); ?>'s AI-assisted tools support analysis and monitoring — they cannot guarantee profits or eliminate investment risk.</li>
</ul>
<p class="mt-6 text-sm text-on-secondary-container">
<a href="/legal_centre" class="text-primary-container font-semibold hover:underline">Read our full risk disclosure and legal terms →</a>
</p>
</div>
</div>
</section>

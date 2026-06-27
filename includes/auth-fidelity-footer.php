<?php $siteName = $siteName ?? get_site_name(); ?>
<footer class="bg-surface-container-low border-t border-surface-gray mt-auto">
<div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop py-lg flex flex-col md:flex-row justify-between items-center gap-md">
<div class="flex flex-col items-center md:items-start gap-xs">
<span class="font-headline-md text-headline-md font-bold text-fidelity-green"><?php echo htmlspecialchars($siteName); ?></span>
<p class="font-body-sm text-body-sm text-on-surface-variant">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>. All rights reserved.</p>
</div>
<div class="flex flex-wrap justify-center gap-md">
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-institutional-blue hover:underline transition-colors" href="/legal_centre#privacy">Privacy Policy</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-institutional-blue hover:underline transition-colors" href="/legal_centre#terms">Terms of Use</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-institutional-blue hover:underline transition-colors" href="/legal_centre#risk-disclosure">Disclosures</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-institutional-blue hover:underline transition-colors" href="/help_centre">Help Center</a>
</div>
</div>
</footer>

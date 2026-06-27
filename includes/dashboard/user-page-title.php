<?php
/** Optional: $pageHeading, $pageSubtitle */
if (empty($pageHeading)) return;
?>
<div class="mb-lg">
<h2 class="font-hanken font-headline-lg text-headline-lg text-on-surface mb-xs"><?php echo htmlspecialchars($pageHeading); ?></h2>
<?php if (!empty($pageSubtitle)): ?>
<p class="font-body-md text-body-md text-on-surface-variant"><?php echo htmlspecialchars($pageSubtitle); ?></p>
<?php endif; ?>
</div>

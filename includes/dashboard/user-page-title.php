<?php
/** Optional: $pageHeading, $pageSubtitle */
if (empty($pageHeading)) return;
?>
<div class="mb-6 md:mb-8">
<h1 class="font-headline-lg text-headline-lg text-text-primary mb-1"><?php echo htmlspecialchars($pageHeading); ?></h1>
<?php if (!empty($pageSubtitle)): ?>
<p class="text-text-secondary font-body-md"><?php echo htmlspecialchars($pageSubtitle); ?></p>
<?php endif; ?>
</div>

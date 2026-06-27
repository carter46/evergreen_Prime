<?php
$siteName = $siteName ?? get_site_name();
$authHeaderLink = $authHeaderLink ?? ['href' => '/login', 'label' => 'Log In'];
?>
<header class="bg-surface border-b border-surface-gray fixed top-0 w-full z-50">
<div class="max-w-[1152px] mx-auto px-margin-mobile md:px-margin-desktop flex justify-between items-center h-16">
<a href="/" class="font-headline-md text-headline-md font-bold text-fidelity-green"><?php echo htmlspecialchars($siteName); ?></a>
<div class="flex gap-md items-center">
<?php if (!empty($authHeaderLink['prefix'])): ?>
<span class="hidden md:inline font-label-md text-label-md text-on-surface-variant"><?php echo htmlspecialchars($authHeaderLink['prefix']); ?></span>
<?php endif; ?>
<a class="font-label-md text-label-md text-institutional-blue hover:underline transition-all" href="<?php echo htmlspecialchars($authHeaderLink['href']); ?>"><?php echo htmlspecialchars($authHeaderLink['label']); ?></a>
</div>
</div>
</header>

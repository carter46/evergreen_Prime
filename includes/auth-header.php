<?php
/**
 * Bloombit - Minimal Auth Header
 * Use on login, register, forgot-password, reset-password
 * Optional: $authHeaderVariant = 'on-primary' for use on yellow/primary background (e.g. register left panel)
 */
require_once __DIR__ . '/helpers.php';
$siteName = $siteName ?? get_site_name();
[$brandBase, $brandAccent] = get_site_brand_parts($siteName);
$onPrimary = isset($authHeaderVariant) && $authHeaderVariant === 'on-primary';
$linkClass = $onPrimary ? 'flex items-center gap-2 text-black hover:opacity-90 transition-opacity' : 'flex items-center gap-2 text-[#1d180c] dark:text-primary hover:opacity-90 transition-opacity';
$iconClass = $onPrimary ? 'size-8 text-black' : 'size-8 text-primary';
$textClass = $onPrimary ? 'text-xl font-bold tracking-tight text-black' : 'text-xl font-bold tracking-tight dark:text-white';
?>
<a class="<?php echo $linkClass; ?> min-h-[44px] py-2" href="/">
<div class="<?php echo $iconClass; ?>">
<svg fill="currentColor" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M36.7273 44C33.9891 44 31.6043 39.8386 30.3636 33.69C29.123 39.8386 26.7382 44 24 44C21.2618 44 18.877 39.8386 17.6364 33.69C16.3957 39.8386 14.0109 44 11.2727 44C7.25611 44 4 35.0457 4 24C4 12.9543 7.25611 4 11.2727 4C14.0109 4 16.3957 8.16144 17.6364 14.31C18.877 8.16144 21.2618 4 24 4C26.7382 4 29.123 8.16144 30.3636 14.31C31.6043 8.16144 33.9891 4 36.7273 4C40.7439 4 44 12.9543 44 24C44 35.0457 40.7439 44 36.7273 44Z"></path></svg>
</div>
<h1 class="<?php echo $textClass; ?>"><?php echo htmlspecialchars($brandBase); ?><?php if ($brandAccent !== ''): ?><span class="text-primary"><?php echo htmlspecialchars($brandAccent); ?></span><?php endif; ?></h1>
</a>

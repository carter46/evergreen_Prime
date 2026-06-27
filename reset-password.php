<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = 'Set New Password | ' . $siteName;
$authHeaderLink = ['href' => '/login', 'label' => 'Log In'];
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<?php require_once __DIR__ . '/includes/auth-fidelity-styles.php'; ?>
</head>
<body class="auth-fidelity-page min-h-screen flex flex-col">
<?php require_once __DIR__ . '/includes/auth-fidelity-header.php'; ?>

<main class="flex-grow flex items-center justify-center py-xl px-margin-mobile pt-24">
<div class="w-full max-w-[440px]">
<div id="invalid-token" class="hidden bg-white border border-surface-gray p-lg rounded-lg shadow-sm text-center">
<div class="w-10 h-10 bg-error-container rounded-lg flex items-center justify-center mx-auto mb-3 text-error">
<span class="material-symbols-outlined text-xl">error</span>
</div>
<p class="text-on-surface font-medium mb-3">Invalid or expired reset link.</p>
<a class="inline-flex items-center gap-2 text-institutional-blue font-bold hover:underline" href="/forgot-password">Request a new link</a>
</div>

<div id="reset-form-wrapper">
<div class="bg-white border border-surface-gray p-lg rounded-lg shadow-sm">
<div class="w-10 h-10 bg-surface-container-high rounded-lg flex items-center justify-center mb-4 text-fidelity-green">
<span class="material-symbols-outlined text-xl">shield</span>
</div>
<div class="mb-lg">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Set new password</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Create a strong password for your account.</p>
</div>
<form id="reset-password-form" class="space-y-md">
<input type="hidden" name="token" id="reset-token"/>
<input type="hidden" name="email" id="reset-email"/>
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface-variant" for="reset-password">New Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" style="font-size: 20px;">lock</span>
<input class="w-full pl-10 pr-12 py-sm bg-surface-container-lowest border border-surface-gray rounded-lg fidelity-input-focus" id="reset-password" name="password" placeholder="••••••••" type="password" required minlength="8" autocomplete="new-password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface-variant" for="reset-confirm">Confirm Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" style="font-size: 20px;">lock_reset</span>
<input class="w-full pl-10 pr-12 py-sm bg-surface-container-lowest border border-surface-gray rounded-lg fidelity-input-focus" id="reset-confirm" name="confirm_password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div id="reset-password-message" class="text-sm hidden"></div>
<button class="w-full bg-fidelity-green text-on-primary py-sm font-headline-md rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
<span>Update Password</span>
<span class="material-symbols-outlined">check_circle</span>
</button>
</form>
<div class="mt-lg pt-lg border-t border-surface-gray text-center">
<a class="font-label-md text-label-md text-institutional-blue hover:underline" href="/login">Back to Login</a>
</div>
</div>
</div>
</div>
</main>

<?php require_once __DIR__ . '/includes/auth-fidelity-footer.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = 'Forgot Password | ' . $siteName;
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
<div class="bg-white border border-surface-gray p-lg rounded-lg shadow-sm">
<div class="w-10 h-10 bg-surface-container-high rounded-lg flex items-center justify-center mb-4 text-fidelity-green">
<span class="material-symbols-outlined text-xl">lock_reset</span>
</div>
<div class="mb-lg">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Forgot password?</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Enter your email and we'll send you a link to reset your password.</p>
</div>
<form id="forgot-password-form" class="space-y-md">
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface-variant" for="forgot-email">Email Address</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" style="font-size: 20px;">mail</span>
<input class="w-full pl-10 pr-md py-sm bg-surface-container-lowest border border-surface-gray rounded-lg fidelity-input-focus font-body-md" id="forgot-email" name="email" placeholder="name@company.com" type="email" required autocomplete="email"/>
</div>
</div>
<div id="forgot-password-message" class="text-sm hidden"></div>
<button class="w-full bg-fidelity-green text-on-primary py-sm font-headline-md rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
<span>Send Reset Link</span>
<span class="material-symbols-outlined">arrow_forward</span>
</button>
</form>
<div class="mt-lg pt-lg border-t border-surface-gray text-center">
<a class="font-label-md text-label-md text-institutional-blue hover:underline" href="/login">Back to Login</a>
</div>
</div>
</div>
</main>

<?php require_once __DIR__ . '/includes/auth-fidelity-footer.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

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

<main class="flex-grow flex items-center justify-center py-lg px-margin-mobile pt-24">
<div class="w-full max-w-[440px]">
<div id="invalid-token" class="hidden bg-white border border-surface-gray p-lg rounded-lg shadow-sm text-center">
<div class="w-10 h-10 bg-error-container rounded-lg flex items-center justify-center mx-auto mb-3 text-error">
<span class="material-symbols-outlined text-xl">error</span>
</div>
<p class="text-on-surface font-medium mb-3">Invalid or expired reset link.</p>
<a class="inline-flex items-center gap-2 text-institutional-blue font-bold hover:underline" href="/forgot-password">Request a new link</a>
</div>

<div id="reset-form-wrapper">
<div class="auth-form-card">
<div class="w-9 h-9 auth-icon-badge rounded-lg flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-xl">shield</span>
</div>
<div class="auth-form-intro">
<h2>Set new password</h2>
<p>Create a strong password for your account.</p>
</div>
<form id="reset-password-form" class="auth-form-stack">
<input type="hidden" name="token" id="reset-token"/>
<input type="hidden" name="email" id="reset-email"/>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="reset-password">New Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 18px;">lock</span>
<input class="auth-form-input auth-form-input-icon auth-form-input-toggle fidelity-input-focus" id="reset-password" name="password" placeholder="••••••••" type="password" required minlength="8" autocomplete="new-password"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="reset-confirm">Confirm Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 18px;">lock_reset</span>
<input class="auth-form-input auth-form-input-icon auth-form-input-toggle fidelity-input-focus" id="reset-confirm" name="confirm_password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<div id="reset-password-message" class="text-sm text-white hidden"></div>
<button class="w-full auth-btn-primary active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
<span>Update Password</span>
<span class="material-symbols-outlined text-[18px]">check_circle</span>
</button>
</form>
<div class="mt-3 pt-3 border-t auth-divider text-center">
<a class="auth-link-light text-sm font-semibold" href="/login">Back to Login</a>
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

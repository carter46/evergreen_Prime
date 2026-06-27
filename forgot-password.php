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

<main class="flex-grow flex items-center justify-center py-lg px-margin-mobile pt-24">
<div class="w-full max-w-[440px]">
<div class="auth-form-card">
<div class="w-9 h-9 auth-icon-badge rounded-lg flex items-center justify-center mb-3">
<span class="material-symbols-outlined text-xl">lock_reset</span>
</div>
<div class="auth-form-intro">
<h2>Forgot password?</h2>
<p>Enter your email and we'll send you a link to reset your password.</p>
</div>
<form id="forgot-password-form" class="auth-form-stack">
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="forgot-email">Email Address</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 18px;">mail</span>
<input class="auth-form-input auth-form-input-icon fidelity-input-focus" id="forgot-email" name="email" placeholder="name@company.com" type="email" required autocomplete="email"/>
</div>
</div>
<div id="forgot-password-message" class="text-sm text-white hidden"></div>
<button class="w-full auth-btn-primary active:scale-[0.98] transition-all flex items-center justify-center gap-2" type="submit">
<span>Send Reset Link</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</form>
<div class="mt-3 pt-3 border-t auth-divider text-center">
<a class="auth-link-light text-sm font-semibold" href="/login">Back to Login</a>
</div>
</div>
</div>
</main>

<?php require_once __DIR__ . '/includes/auth-fidelity-footer.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

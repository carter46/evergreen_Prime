<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = 'Set New Password | ' . $siteName;
$authBgStyle = 'simple';
?>
<!DOCTYPE html>
<html class="dark auth-fit-screen" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/auth-head.php'; ?>
</head>
<body class="auth-page auth-fit-screen font-body-md text-body-md">
<?php require_once __DIR__ . '/includes/auth-background.php'; ?>
<div class="auth-shell relative z-10 flex flex-col w-full">
<nav class="shrink-0 flex items-center px-4 md:px-margin-desktop h-14 md:h-16 w-full max-w-container-max mx-auto">
<a class="flex items-center gap-2 group transition-all" href="/">
<span class="material-symbols-outlined text-primary-container text-[20px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
<span class="font-label-sm text-label-sm text-on-surface-variant group-hover:text-primary-container">Back to home</span>
</a>
</nav>
<main class="auth-main flex items-center justify-center px-4 md:px-margin-mobile py-2 md:py-0">
<div class="w-full max-w-[440px]">
<div id="invalid-token" class="hidden auth-glass-card p-5 md:p-7 rounded-xl text-center">
<div class="w-10 h-10 bg-critical/20 rounded-lg flex items-center justify-center mx-auto mb-3">
<span class="material-symbols-outlined text-critical text-xl">error</span>
</div>
<p class="text-on-surface font-medium mb-3">Invalid or expired reset link.</p>
<a class="inline-flex items-center gap-2 text-primary-container font-bold hover:underline" href="/forgot-password">Request a new link</a>
</div>
<div id="reset-form-wrapper">
<div class="mb-4 md:mb-5 text-center md:text-left">
<h1 class="font-headline-md text-headline-md font-extrabold text-primary-container tracking-tight mb-1"><?php echo htmlspecialchars($siteName); ?></h1>
<h2 class="font-headline-md text-headline-md text-on-surface mb-1">Set new password</h2>
<p class="font-body-md text-body-md text-on-surface-variant text-sm">Create a strong password for your account.</p>
</div>
<div class="auth-glass-card p-5 md:p-7 rounded-xl shadow-lg shadow-primary-container/5">
<div class="w-10 h-10 bg-primary-container/10 rounded-lg flex items-center justify-center mb-4">
<span class="material-symbols-outlined text-primary-container text-xl">shield</span>
</div>
<form id="reset-password-form" class="space-y-4">
<input type="hidden" name="token" id="reset-token"/>
<input type="hidden" name="email" id="reset-email"/>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-widest" for="reset-password">New Password</label>
<div class="auth-field">
<span class="material-symbols-outlined auth-field-icon">lock</span>
<input id="reset-password" name="password" placeholder="••••••••" type="password" required minlength="8" autocomplete="new-password"/>
<button type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-widest" for="reset-confirm">Confirm Password</label>
<div class="auth-field">
<span class="material-symbols-outlined auth-field-icon">lock_reset</span>
<input id="reset-confirm" name="confirm_password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div id="reset-password-message" class="text-sm hidden"></div>
<button class="w-full bg-primary-container hover:bg-primary-container/90 active:scale-[0.98] transition-all py-3 px-6 rounded-lg flex items-center justify-center gap-2 group" type="submit">
<span class="font-label-sm text-label-sm text-on-primary uppercase tracking-widest">Update Password</span>
<span class="material-symbols-outlined text-on-primary">check_circle</span>
</button>
</form>
<div class="mt-5 pt-5 border-t border-white/5 text-center">
<a class="font-label-sm text-label-sm text-primary-fixed-dim hover:text-primary-container transition-colors" href="/login">Back to Login</a>
</div>
</div>
</div>
</div>
</main>
<footer class="shrink-0 h-10 md:h-12 flex items-center justify-center px-4 md:px-margin-desktop opacity-50">
<p class="font-label-xs text-label-xs text-on-surface-variant text-center">
© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>.
</p>
</footer>
</div>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

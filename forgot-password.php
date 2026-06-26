<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$pageTitle = 'Forgot Password | ' . $siteName;
$authBgStyle = 'simple';
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/auth-head.php'; ?>
</head>
<body class="auth-page font-body-md text-body-md overflow-x-hidden min-h-screen">
<?php require_once __DIR__ . '/includes/auth-background.php'; ?>
<div class="relative z-10 flex flex-col min-h-screen w-full">
<nav class="flex items-center justify-between px-4 md:px-margin-desktop h-20 w-full max-w-container-max mx-auto">
<a class="flex items-center gap-2 group transition-all" href="/">
<span class="material-symbols-outlined text-primary-container text-[20px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
<span class="font-label-sm text-label-sm text-on-surface-variant group-hover:text-primary-container">Back to home</span>
</a>
</nav>
<main class="flex-1 flex items-center justify-center px-4 md:px-margin-mobile py-8">
<div class="w-full max-w-[480px]">
<div class="mb-10 text-center md:text-left">
<h1 class="font-display text-headline-md font-extrabold text-primary-container tracking-tighter mb-2"><?php echo htmlspecialchars($siteName); ?></h1>
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Forgot password?</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-sm mx-auto md:mx-0">Enter your email and we'll send you a link to reset your password.</p>
</div>
<div class="auth-glass-card p-8 md:p-10 rounded-xl shadow-lg shadow-primary-container/5">
<div class="w-12 h-12 bg-primary-container/10 rounded-lg flex items-center justify-center mb-6">
<span class="material-symbols-outlined text-primary-container text-2xl">lock_reset</span>
</div>
<form id="forgot-password-form" class="space-y-6">
<div class="space-y-2">
<label class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-widest" for="forgot-email">Email Address</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">mail</span>
<input class="auth-input" id="forgot-email" name="email" placeholder="name@company.com" type="email" required autocomplete="email"/>
</div>
</div>
<div id="forgot-password-message" class="text-sm hidden"></div>
<button class="w-full bg-primary-container hover:bg-primary-container/90 active:scale-[0.98] transition-all py-4 px-6 rounded-lg flex items-center justify-center gap-2 group" type="submit">
<span class="font-label-sm text-label-sm text-on-primary uppercase tracking-widest">Send Reset Link</span>
<span class="material-symbols-outlined text-on-primary group-hover:translate-x-1 transition-transform">arrow_forward</span>
</button>
</form>
<div class="mt-8 pt-8 border-t border-white/5 text-center">
<a class="font-label-sm text-label-sm text-primary-fixed-dim hover:text-primary-container transition-colors" href="/login">Back to Login</a>
</div>
<div class="mt-6 flex items-center justify-center gap-2 opacity-60">
<span class="material-symbols-outlined text-sm text-on-surface-variant">lock</span>
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest">Secure bank-grade encryption</p>
</div>
</div>
</div>
</main>
<footer class="h-16 flex items-center justify-center px-4 md:px-margin-desktop opacity-50">
<p class="font-label-xs text-label-xs text-on-surface-variant text-center">
© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>.
</p>
</footer>
</div>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

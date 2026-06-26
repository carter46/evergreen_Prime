<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session-bootstrap.php';
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'user';
    header('Location: ' . ($role === 'admin' ? '/dashboard/admin' : '/dashboard'));
    exit;
}
$siteName = get_site_name();
$pageTitle = 'Login | ' . $siteName;
$authBgStyle = 'login';
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
<div class="mb-4 md:mb-5 text-center md:text-left">
<h1 class="font-headline-md text-headline-md font-extrabold text-primary-container tracking-tight mb-1"><?php echo htmlspecialchars($siteName); ?></h1>
<h2 class="font-headline-md text-headline-md text-on-surface mb-1">Welcome back</h2>
<p class="font-body-md text-body-md text-on-surface-variant text-sm md:text-base">Enter your details to manage your digital assets securely.</p>
</div>
<div class="auth-glass-card p-5 md:p-7 rounded-xl shadow-lg shadow-primary-container/5">
<form id="login-form" class="space-y-4">
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-widest" for="login-email">Email Address</label>
<div class="auth-field">
<span class="material-symbols-outlined auth-field-icon">mail</span>
<input id="login-email" name="email" placeholder="name@company.com" type="email" required autocomplete="email"/>
</div>
</div>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-widest" for="login-password">Password</label>
<div class="auth-field">
<span class="material-symbols-outlined auth-field-icon">lock</span>
<input id="login-password" name="password" placeholder="••••••••" type="password" required autocomplete="current-password"/>
<button type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="flex items-center justify-between gap-2">
<label class="flex items-center gap-2 cursor-pointer">
<input class="w-4 h-4 rounded border-outline-variant bg-white text-primary-container focus:ring-primary-container/30" type="checkbox" name="remember"/>
<span class="font-label-sm text-label-sm text-on-surface-variant">Remember me</span>
</label>
<a class="font-label-sm text-label-sm text-primary-fixed-dim hover:text-primary-container transition-colors shrink-0" href="/forgot-password">Forgot password?</a>
</div>
<?php if (!empty($_GET['timeout'])): ?>
<div class="text-sm text-primary-container bg-primary-container/10 border border-primary-container/20 px-3 py-2 rounded-lg">You were logged out due to inactivity. Please sign in again.</div>
<?php endif; ?>
<div id="login-form-message" class="text-sm hidden"></div>
<button class="w-full bg-primary-container hover:bg-primary-container/90 active:scale-[0.98] transition-all py-3 px-6 rounded-lg flex items-center justify-center gap-2 group" type="submit">
<span class="font-label-sm text-label-sm text-on-primary uppercase tracking-widest">Sign In</span>
<span class="material-symbols-outlined text-on-primary group-hover:translate-x-1 transition-transform">arrow_forward</span>
</button>
</form>
<div id="login-otp-step" class="space-y-4 hidden">
<h2 class="text-lg font-bold text-on-surface">Verify your identity</h2>
<p class="text-on-surface-variant text-sm" id="login-otp-email-display"></p>
<p class="text-sm text-on-secondary-container">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-4" id="login-otp-inputs">
<?php for ($i = 1; $i <= 6; $i++): ?>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>" class="w-11 h-12 text-center text-lg font-bold rounded-lg border border-outline-variant/30 bg-white text-[#111417] focus:border-primary-container focus:ring-2 focus:ring-primary-container/20" data-otp-digit aria-label="Digit <?php echo $i; ?>"/>
<?php endfor; ?>
</div>
<div id="login-otp-message" class="text-sm hidden"></div>
<button type="button" id="login-otp-resend" class="text-primary-container hover:underline text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="login-otp-submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-on-primary font-bold py-3 rounded-lg flex items-center justify-center gap-2">
Verify &amp; Sign In
</button>
</div>
<div class="mt-5 pt-5 border-t border-white/5 text-center">
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest">Institutional grade security</p>
</div>
</div>
<p class="mt-4 text-center text-sm text-on-surface-variant" id="login-have-account">
Don't have an account?
<a class="text-primary-fixed-dim font-bold hover:underline underline-offset-4 ml-1" href="/register">Create an account</a>
</p>
</div>
</main>
<footer class="shrink-0 h-10 md:h-12 flex items-center justify-center px-4 md:px-margin-desktop opacity-50">
<p class="font-label-xs text-label-xs text-on-surface-variant text-center leading-tight">
© <?php echo date('Y'); ?> <?php echo htmlspecialchars($siteName); ?>.
</p>
</footer>
</div>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/includes/translation-widget.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session-bootstrap.php';
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'user';
    header('Location: ' . ($role === 'admin' ? '/dashboard/admin' : '/dashboard'));
    exit;
}
$siteName = get_site_name();
$pageTitle = 'Log In | ' . $siteName;
$authHeaderLink = ['prefix' => 'New to ' . $siteName . '?', 'href' => '/register', 'label' => 'Register'];
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
<div class="login-container w-full max-w-[1152px] grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<div class="lg:col-span-7 hidden lg:block pr-lg">
<h1 class="auth-hero-title text-on-surface mb-sm">Secure access to your <span class="text-fidelity-green">financial future.</span></h1>
<p class="font-body-md text-body-md text-on-surface-variant max-w-xl mb-lg">Institution-grade security and precision tools designed to empower your investment journey.</p>
<div class="grid grid-cols-2 gap-md">
<div class="p-md bg-white border border-surface-gray rounded-lg transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-md">
<span class="material-symbols-outlined text-institutional-blue mb-xs" style="font-size: 32px;">verified_user</span>
<h3 class="font-headline-md text-on-surface mb-xs text-[18px]">Multi-Factor</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Enhanced protection for every transaction you make.</p>
</div>
<div class="p-md bg-white border border-surface-gray rounded-lg transition-transform duration-200 hover:-translate-y-0.5 hover:shadow-md">
<span class="material-symbols-outlined text-fidelity-green mb-xs" style="font-size: 32px;">speed</span>
<h3 class="font-headline-md text-on-surface mb-xs text-[18px]">Real-time</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Instant updates on market fluctuations and portfolios.</p>
</div>
</div>
</div>

<div class="lg:col-span-5 w-full max-w-[440px] mx-auto">
<div class="auth-form-card">
<div id="login-card-intro" class="auth-form-intro">
<h2>Welcome back</h2>
<p>Log in to your <?php echo htmlspecialchars($siteName); ?> account to continue.</p>
</div>
<form id="login-form" class="auth-form-stack">
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="login-email">Username / Email</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 18px;">person</span>
<input class="auth-form-input auth-form-input-icon fidelity-input-focus" id="login-email" name="email" placeholder="Enter your email" required type="email" autocomplete="email"/>
</div>
</div>
<div class="auth-form-stack-tight">
<div class="flex justify-between items-center">
<label class="auth-field-label" for="login-password">Password</label>
<a class="auth-link-light text-xs font-semibold" href="/forgot-password">Forgot Password?</a>
</div>
<div class="relative">
<span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 18px;">lock</span>
<input class="auth-form-input auth-form-input-icon auth-form-input-toggle fidelity-input-focus" id="login-password" name="password" placeholder="••••••••" required type="password" autocomplete="current-password"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<div class="flex items-center gap-2">
<input class="w-4 h-4 text-fidelity-green border-gray-300 rounded focus:ring-fidelity-green" id="remember" name="remember" type="checkbox"/>
<label class="auth-terms-label" for="remember">Remember my username</label>
</div>
<?php if (!empty($_GET['timeout'])): ?>
<div class="text-sm text-white bg-white/15 border border-white/25 px-3 py-2 rounded-lg">You were logged out due to inactivity. Please sign in again.</div>
<?php endif; ?>
<div id="login-form-message" class="text-sm text-white hidden"></div>
<button class="w-full auth-btn-primary active:scale-[0.98] transition-all" type="submit">Log In</button>
</form>

<div id="login-otp-step" class="auth-form-stack hidden">
<h2 class="auth-otp-title">Verify your identity</h2>
<p class="auth-otp-text" id="login-otp-email-display"></p>
<p class="auth-otp-text">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-2" id="login-otp-inputs">
<?php for ($i = 1; $i <= 6; $i++): ?>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>" class="w-10 h-10 text-center text-base font-bold rounded-lg border border-white/30 bg-white text-on-surface focus:border-white focus:ring-1 focus:ring-white" data-otp-digit aria-label="Digit <?php echo $i; ?>"/>
<?php endfor; ?>
</div>
<div id="login-otp-message" class="text-sm text-white hidden"></div>
<button type="button" id="login-otp-resend" class="auth-link-light text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="login-otp-submit" class="w-full auth-btn-primary flex items-center justify-center gap-2">Verify &amp; Sign In</button>
</div>

<div class="mt-3 pt-3 border-t auth-divider text-center auth-register-cta" id="login-have-account">
<p class="mb-2 text-sm">New to <?php echo htmlspecialchars($siteName); ?>?</p>
<a href="/register">Register Now</a>
</div>
</div>
<div class="mt-md flex justify-center gap-md">
<a class="font-label-md text-label-md text-on-surface-variant hover:text-institutional-blue flex items-center gap-base" href="/help_centre">
<span class="material-symbols-outlined" style="font-size: 16px;">help_center</span>Need help?
</a>
<a class="font-label-md text-label-md text-on-surface-variant hover:text-institutional-blue flex items-center gap-base" href="/legal_centre#risk-disclosure">
<span class="material-symbols-outlined" style="font-size: 16px;">security</span>Security Center
</a>
</div>
</div>
</div>
</main>

<?php require_once __DIR__ . '/includes/auth-fidelity-footer.php'; ?>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/includes/translation-widget.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body>
</html>

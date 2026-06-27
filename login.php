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
<style>
.input-focus-ring:focus {
  outline: none;
  border-color: #0078AE;
  box-shadow: 0 0 0 1px #0078AE;
}
.hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.hover-lift:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
</style>
</head>
<body class="auth-fidelity-page min-h-screen flex flex-col">
<?php require_once __DIR__ . '/includes/auth-fidelity-header.php'; ?>

<main class="flex-grow flex items-center justify-center py-xl px-margin-mobile pt-24">
<div class="login-container w-full max-w-[1152px] grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<div class="lg:col-span-7 hidden lg:block pr-lg">
<h1 class="font-display-lg text-display-lg text-on-surface mb-md">Secure access to your <span class="text-fidelity-green">financial future.</span></h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mb-lg">Institution-grade security and precision tools designed to empower your investment journey.</p>
<div class="grid grid-cols-2 gap-md">
<div class="p-md bg-white border border-surface-gray rounded-lg hover-lift">
<span class="material-symbols-outlined text-institutional-blue mb-xs" style="font-size: 32px;">verified_user</span>
<h3 class="font-headline-md text-on-surface mb-xs text-[18px]">Multi-Factor</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Enhanced protection for every transaction you make.</p>
</div>
<div class="p-md bg-white border border-surface-gray rounded-lg hover-lift">
<span class="material-symbols-outlined text-fidelity-green mb-xs" style="font-size: 32px;">speed</span>
<h3 class="font-headline-md text-on-surface mb-xs text-[18px]">Real-time</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Instant updates on market fluctuations and portfolios.</p>
</div>
</div>
</div>

<div class="lg:col-span-5 w-full max-w-[440px] mx-auto">
<div class="bg-white border border-surface-gray p-lg rounded-lg shadow-sm">
<div id="login-card-intro" class="mb-lg">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Welcome back</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Log in to your <?php echo htmlspecialchars($siteName); ?> account to continue.</p>
</div>
<form id="login-form" class="space-y-md">
<div class="space-y-xs">
<label class="font-label-md text-label-md text-on-surface-variant" for="login-email">Username / Email</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" style="font-size: 20px;">person</span>
<input class="w-full pl-10 pr-md py-sm bg-surface-container-lowest border border-surface-gray rounded-lg input-focus-ring font-body-md text-body-md" id="login-email" name="email" placeholder="Enter your email" required type="email" autocomplete="email"/>
</div>
</div>
<div class="space-y-xs">
<div class="flex justify-between items-center">
<label class="font-label-md text-label-md text-on-surface-variant" for="login-password">Password</label>
<a class="font-label-md text-label-md text-institutional-blue hover:underline" href="/forgot-password">Forgot Password?</a>
</div>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline-variant" style="font-size: 20px;">lock</span>
<input class="w-full pl-10 pr-12 py-sm bg-surface-container-lowest border border-surface-gray rounded-lg input-focus-ring font-body-md text-body-md" id="login-password" name="password" placeholder="••••••••" required type="password" autocomplete="current-password"/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined" style="font-size: 20px;">visibility</span>
</button>
</div>
</div>
<div class="flex items-center gap-xs py-xs">
<input class="w-4 h-4 text-fidelity-green border-surface-gray rounded focus:ring-fidelity-green" id="remember" name="remember" type="checkbox"/>
<label class="font-body-sm text-body-sm text-on-surface-variant" for="remember">Remember my username</label>
</div>
<?php if (!empty($_GET['timeout'])): ?>
<div class="text-sm text-fidelity-green bg-fidelity-green/10 border border-fidelity-green/20 px-3 py-2 rounded-lg">You were logged out due to inactivity. Please sign in again.</div>
<?php endif; ?>
<div id="login-form-message" class="text-sm hidden"></div>
<button class="w-full bg-fidelity-green text-on-primary py-sm font-headline-md text-[18px] rounded-lg hover:opacity-95 active:scale-[0.98] transition-all shadow-sm" type="submit">Log In</button>
</form>

<div id="login-otp-step" class="space-y-md hidden">
<h2 class="font-headline-md text-headline-md text-on-surface">Verify your identity</h2>
<p class="text-on-surface-variant text-sm" id="login-otp-email-display"></p>
<p class="text-sm text-on-surface-variant">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-4" id="login-otp-inputs">
<?php for ($i = 1; $i <= 6; $i++): ?>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>" class="w-11 h-12 text-center text-lg font-bold rounded-lg border border-surface-gray bg-white text-on-surface focus:border-institutional-blue focus:ring-1 focus:ring-institutional-blue" data-otp-digit aria-label="Digit <?php echo $i; ?>"/>
<?php endfor; ?>
</div>
<div id="login-otp-message" class="text-sm hidden"></div>
<button type="button" id="login-otp-resend" class="text-institutional-blue hover:underline text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="login-otp-submit" class="w-full bg-fidelity-green text-on-primary font-headline-md py-sm rounded-lg hover:opacity-90 flex items-center justify-center gap-2">Verify &amp; Sign In</button>
</div>

<div class="mt-lg pt-lg border-t border-surface-gray text-center" id="login-have-account">
<p class="font-body-sm text-body-sm text-on-surface-variant mb-sm">New to <?php echo htmlspecialchars($siteName); ?>?</p>
<a class="block w-full border border-institutional-blue text-institutional-blue py-sm font-label-md text-label-md rounded-lg hover:bg-surface-container-low transition-colors text-center" href="/register">Register Now</a>
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

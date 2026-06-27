<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session-bootstrap.php';
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'user';
    header('Location: ' . ($role === 'admin' ? '/dashboard/admin' : '/dashboard'));
    exit;
}
$siteName = get_site_name();
$pageTitle = 'Register | ' . $siteName;
$authHeaderLink = ['prefix' => 'Already have an account?', 'href' => '/login', 'label' => 'Log In'];
$refPrefill = isset($_GET['ref']) ? strtoupper(trim((string)$_GET['ref'])) : '';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<?php require_once __DIR__ . '/includes/auth-fidelity-styles.php'; ?>
</head>
<body class="auth-fidelity-page text-on-surface font-body-md overflow-x-hidden min-h-screen flex flex-col">
<?php require_once __DIR__ . '/includes/auth-fidelity-header.php'; ?>

<main class="flex-1 pt-16 flex flex-col">
<div class="relative z-10 w-full max-w-[1152px] mx-auto px-margin-mobile md:px-margin-desktop py-lg flex flex-col md:flex-row gap-lg items-start">
<div class="w-full md:w-1/2 mt-lg">
<h1 class="auth-hero-title text-on-surface mb-sm">Secure your financial future starting today.</h1>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg max-w-md">
Join investors who trust <?php echo htmlspecialchars($siteName); ?> with their wealth, retirement, and future planning.
</p>
<div class="auth-benefits-list">
<div class="auth-benefit-item">
<div class="auth-benefit-icon">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">security</span>
</div>
<div>
<p class="auth-benefit-title">Institutional-Grade Security</p>
<p class="auth-benefit-desc">Your account is protected with advanced encryption and secure verification.</p>
</div>
</div>
<div class="auth-benefit-item">
<div class="auth-benefit-icon">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">analytics</span>
</div>
<div>
<p class="auth-benefit-title">Comprehensive Planning</p>
<p class="auth-benefit-desc">Access retirement tools, market insights, and portfolio tracking in one place.</p>
</div>
</div>
<div class="auth-benefit-item">
<div class="auth-benefit-icon">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
</div>
<div>
<p class="auth-benefit-title">A Legacy of Trust</p>
<p class="auth-benefit-desc">Built to help investors navigate market cycles with clarity and confidence.</p>
</div>
</div>
</div>
</div>

<div id="register-step-form" class="w-full md:w-1/2 max-w-md">
<div class="auth-form-card">
<div id="register-form-chrome" class="auth-form-intro">
<h2>Create Account</h2>
<p>Complete the form below to begin your journey with <?php echo htmlspecialchars($siteName); ?>.</p>
</div>

<form id="register-form" class="auth-form-stack" enctype="multipart/form-data">
<div class="w-full h-1 auth-progress-track rounded-full overflow-hidden">
<div id="register-progress" class="auth-progress-fill h-full w-1/2 transition-all duration-300"></div>
</div>
<p class="auth-form-step-label uppercase tracking-wider" id="register-step-label">Step 1 of 2 — Your details</p>

<div id="register-step-1" class="auth-form-stack">
<label class="flex items-center gap-2 border border-dashed rounded-lg auth-upload-box hover:border-fidelity-green transition-colors cursor-pointer" for="avatar">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center shrink-0 overflow-hidden" id="avatar-preview">
<span class="material-symbols-outlined text-on-surface-variant text-xl">cloud_upload</span>
</div>
<div class="text-left min-w-0">
<span class="font-label-md text-label-md text-fidelity-green block">Upload Profile Photo <span class="text-on-surface-variant font-normal normal-case">(Optional)</span></span>
<span class="text-on-surface-variant text-xs normal-case">PNG, JPEG or WEBP. Max 2MB.</span>
</div>
<input name="avatar" id="avatar" class="sr-only" accept="image/png,image/jpeg,image/webp" type="file"/>
</label>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="name">Full Name</label>
<input class="auth-form-input fidelity-input-focus" id="name" name="name" placeholder="Johnathan Doe" type="text" required autocomplete="name"/>
</div>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="email">Email Address</label>
<input class="auth-form-input fidelity-input-focus" id="email" name="email" placeholder="john.doe@example.com" type="email" required autocomplete="email"/>
</div>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="phone">Phone Number <span class="normal-case font-normal">(Optional)</span></label>
<input class="auth-form-input fidelity-input-focus" id="phone" name="phone" placeholder="+1 (555) 000-0000" type="tel" autocomplete="tel"/>
</div>
<div id="register-step1-message" class="auth-form-message hidden"></div>
<button type="button" id="register-step1-next" class="w-full auth-btn-primary active:scale-[0.98] transition-all flex items-center justify-center gap-xs">
<span>Continue</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</div>

<div id="register-step-2" class="auth-form-stack hidden">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="password">Password</label>
<div class="relative">
<input class="auth-form-input auth-form-input-toggle fidelity-input-focus" id="password" name="password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="confirm-password">Confirm Password</label>
<div class="relative">
<input class="auth-form-input auth-form-input-toggle fidelity-input-focus" id="confirm-password" name="confirm_password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[18px]">visibility</span>
</button>
</div>
</div>
</div>
<p class="auth-form-note">Must include 8+ characters, one uppercase letter, and one symbol.</p>
<div class="auth-form-stack-tight">
<label class="auth-field-label" for="referral">Referral Code <span class="normal-case font-normal">(Optional)</span></label>
<input class="auth-form-input fidelity-input-focus uppercase tracking-widest" id="referral" name="referral" placeholder="FX-PRO-2024" type="text" value="<?php echo htmlspecialchars($refPrefill); ?>"/>
</div>
<div class="flex gap-2 items-start">
<input class="mt-0.5 rounded-sm text-fidelity-green border-gray-300 focus:ring-fidelity-green" id="terms" type="checkbox"/>
<label class="auth-terms-label" for="terms">
I agree to the <a href="/legal_centre#terms">Customer Agreement</a>,
<a href="/legal_centre">Electronic Delivery Disclosure</a>, and
<a href="/legal_centre#privacy">Privacy Policy</a>.
</label>
</div>
<div id="register-form-message" class="auth-form-message hidden"></div>
<div class="flex gap-2">
<button type="button" id="register-step2-back" class="flex-1 auth-btn-outline transition-colors">Back</button>
<button type="submit" class="flex-[2] auth-btn-primary active:scale-[0.98] transition-all flex items-center justify-center gap-xs">
<span>Create Account</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</div>
</div>
</form>

<div id="register-otp-step" class="auth-form-stack hidden">
<h2 class="auth-otp-title">Verify your email</h2>
<p class="auth-otp-text" id="register-otp-email-display"></p>
<p class="auth-otp-text">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-2" id="register-otp-inputs">
<?php for ($i = 1; $i <= 6; $i++): ?>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>" class="w-10 h-10 text-center text-base font-bold rounded-lg border border-gray-300 bg-white text-on-surface focus:border-fidelity-green focus:ring-1 focus:ring-fidelity-green" data-otp-digit aria-label="Digit <?php echo $i; ?>"/>
<?php endfor; ?>
</div>
<div id="register-otp-message" class="auth-form-message hidden"></div>
<button type="button" id="register-otp-resend" class="auth-link-accent text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="register-otp-submit" class="w-full auth-btn-primary flex items-center justify-center gap-2">
Verify &amp; Continue
</button>
</div>

<div id="register-thank-you" class="hidden text-center py-4">
<div class="w-14 h-14 rounded-full bg-fidelity-green/10 flex items-center justify-center mx-auto mb-3">
<span class="material-symbols-outlined text-fidelity-green text-3xl">check_circle</span>
</div>
<h2 class="auth-otp-title mb-2">Thank you!</h2>
<p class="auth-otp-text">Your account has been verified. Redirecting to your dashboard...</p>
</div>

<div id="register-form-footer" class="mt-3 pt-3 border-t auth-divider">
<div class="flex items-center gap-2 auth-footer-note">
<span class="material-symbols-outlined text-fidelity-green text-[18px]" style="font-variation-settings: 'FILL' 1;">shield</span>
<span>Encrypted with 256-bit AES protection.</span>
</div>
</div>
</div>
<p class="text-center mt-md font-body-md text-body-md text-on-surface-variant" id="register-have-account">
Already have an account?
<a class="text-institutional-blue font-bold hover:underline ml-1" href="/login">Log in here</a>
</p>
</div>
</div>

<?php require_once __DIR__ . '/includes/auth-fidelity-trust.php'; ?>
</main>

<?php require_once __DIR__ . '/includes/auth-fidelity-footer.php'; ?>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/includes/translation-widget.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
<script>
(function () {
  var step1 = document.getElementById('register-step-1');
  var step2 = document.getElementById('register-step-2');
  var progress = document.getElementById('register-progress');
  var stepLabel = document.getElementById('register-step-label');
  var step1Msg = document.getElementById('register-step1-message');
  var form = document.getElementById('register-form');
  if (!form || !step1 || !step2) return;

  function showStep(n) {
    if (n === 1) {
      step1.classList.remove('hidden');
      step2.classList.add('hidden');
      if (progress) progress.style.width = '50%';
      if (stepLabel) stepLabel.textContent = 'Step 1 of 2 — Your details';
    } else {
      step1.classList.add('hidden');
      step2.classList.remove('hidden');
      if (progress) progress.style.width = '100%';
      if (stepLabel) stepLabel.textContent = 'Step 2 of 2 — Security & preferences';
    }
    if (step1Msg) step1Msg.classList.add('hidden');
  }

  document.getElementById('register-step1-next')?.addEventListener('click', function () {
    var name = form.querySelector('[name="name"]')?.value?.trim();
    var email = form.querySelector('[name="email"]')?.value?.trim();
    if (!name) {
      step1Msg.textContent = 'Full name is required.';
      step1Msg.className = 'auth-form-message is-error';
      step1Msg.classList.remove('hidden');
      return;
    }
    if (!email) {
      step1Msg.textContent = 'Email address is required.';
      step1Msg.className = 'auth-form-message is-error';
      step1Msg.classList.remove('hidden');
      return;
    }
    showStep(2);
    form.querySelector('[name="password"]')?.focus();
  });

  document.getElementById('register-step2-back')?.addEventListener('click', function () { showStep(1); });

  document.getElementById('avatar')?.addEventListener('change', function () {
    var f = this.files[0];
    var p = document.getElementById('avatar-preview');
    if (!p) return;
    if (f && /^image\/(png|jpeg|webp)$/.test(f.type)) {
      var r = new FileReader();
      r.onload = function () { p.innerHTML = '<img src="' + r.result + '" alt="" class="w-full h-full object-cover"/>'; };
      r.readAsDataURL(f);
    } else {
      p.innerHTML = '<span class="material-symbols-outlined text-on-surface-variant text-xl">cloud_upload</span>';
    }
  });
})();
</script>
</body>
</html>

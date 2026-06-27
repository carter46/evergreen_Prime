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
<div class="relative z-10 w-full max-w-[1152px] mx-auto px-margin-mobile md:px-margin-desktop py-xl flex flex-col md:flex-row gap-xl items-start">
<div class="w-full md:w-1/2 mt-lg">
<h1 class="font-display-lg text-display-lg text-on-surface mb-md">Secure your financial future starting today.</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-xl max-w-md">
Join investors who trust <?php echo htmlspecialchars($siteName); ?> with their wealth, retirement, and future planning.
</p>
<div class="space-y-lg">
<div class="flex gap-md items-start">
<div class="flex-shrink-0 w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center text-fidelity-green">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">security</span>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Institutional-Grade Security</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Your account is protected with advanced encryption and secure verification.</p>
</div>
</div>
<div class="flex gap-md items-start">
<div class="flex-shrink-0 w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center text-fidelity-green">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">analytics</span>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Comprehensive Planning</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Access retirement tools, market insights, and portfolio tracking in one place.</p>
</div>
</div>
<div class="flex gap-md items-start">
<div class="flex-shrink-0 w-12 h-12 rounded-lg bg-surface-container-high flex items-center justify-center text-fidelity-green">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
</div>
<div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">A Legacy of Trust</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Built to help investors navigate market cycles with clarity and confidence.</p>
</div>
</div>
</div>
</div>

<div id="register-step-form" class="w-full md:w-1/2 max-w-md">
<div class="bg-surface border border-surface-gray p-lg shadow-sm rounded-lg">
<div id="register-form-chrome">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-sm">Create Account</h2>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg">Complete the form below to begin your journey with <?php echo htmlspecialchars($siteName); ?>.</p>
</div>

<form id="register-form" class="space-y-md" enctype="multipart/form-data">
<div class="w-full h-1 bg-surface-container rounded-full overflow-hidden">
<div id="register-progress" class="bg-fidelity-green h-full w-1/2 transition-all duration-300"></div>
</div>
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" id="register-step-label">Step 1 of 2 — Your details</p>

<div id="register-step-1" class="space-y-md">
<label class="flex items-center gap-3 border border-dashed border-surface-gray rounded-lg p-3 bg-surface-container-low hover:border-institutional-blue/50 transition-colors cursor-pointer" for="avatar">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center shrink-0 overflow-hidden" id="avatar-preview">
<span class="material-symbols-outlined text-on-surface-variant text-xl">cloud_upload</span>
</div>
<div class="text-left min-w-0">
<span class="font-label-md text-label-md text-fidelity-green block">Upload Profile Photo <span class="text-on-surface-variant font-normal normal-case">(Optional)</span></span>
<span class="text-on-surface-variant text-xs normal-case">PNG, JPEG or WEBP. Max 2MB.</span>
</div>
<input name="avatar" id="avatar" class="sr-only" accept="image/png,image/jpeg,image/webp" type="file"/>
</label>
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="name">Full Name</label>
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm rounded-lg fidelity-input-focus transition-all text-on-surface placeholder:text-surface-dim" id="name" name="name" placeholder="Johnathan Doe" type="text" required autocomplete="name"/>
</div>
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="email">Email Address</label>
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm rounded-lg fidelity-input-focus transition-all text-on-surface placeholder:text-surface-dim" id="email" name="email" placeholder="john.doe@example.com" type="email" required autocomplete="email"/>
</div>
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="phone">Phone Number <span class="normal-case font-normal">(Optional)</span></label>
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm rounded-lg fidelity-input-focus transition-all text-on-surface placeholder:text-surface-dim" id="phone" name="phone" placeholder="+1 (555) 000-0000" type="tel" autocomplete="tel"/>
</div>
<div id="register-step1-message" class="text-sm text-error hidden"></div>
<button type="button" id="register-step1-next" class="w-full bg-fidelity-green text-on-primary font-headline-md py-sm px-md rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-xs">
<span>Continue</span>
<span class="material-symbols-outlined">arrow_forward</span>
</button>
</div>

<div id="register-step-2" class="space-y-md hidden">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="password">Password</label>
<div class="relative">
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm pr-10 rounded-lg fidelity-input-focus transition-all text-on-surface" id="password" name="password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-sm top-1/2 -translate-y-1/2 text-surface-dim hover:text-on-surface-variant" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="confirm-password">Confirm Password</label>
<div class="relative">
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm pr-10 rounded-lg fidelity-input-focus transition-all text-on-surface" id="confirm-password" name="confirm_password" placeholder="••••••••" type="password" required autocomplete="new-password"/>
<button class="absolute right-sm top-1/2 -translate-y-1/2 text-surface-dim hover:text-on-surface-variant" type="button" data-password-toggle aria-label="Toggle password visibility">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
</div>
<p class="font-label-md text-[10px] text-on-surface-variant">Must include 8+ characters, one uppercase letter, and one symbol.</p>
<div class="space-y-base">
<label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="referral">Referral Code <span class="normal-case font-normal">(Optional)</span></label>
<input class="w-full bg-surface-container-lowest border border-surface-gray p-sm rounded-lg fidelity-input-focus uppercase tracking-widest" id="referral" name="referral" placeholder="FX-PRO-2024" type="text" value="<?php echo htmlspecialchars($refPrefill); ?>"/>
</div>
<div class="flex gap-sm items-start py-xs">
<input class="mt-1 rounded-sm text-fidelity-green border-surface-gray focus:ring-fidelity-green" id="terms" type="checkbox"/>
<label class="font-body-sm text-body-sm text-on-surface-variant" for="terms">
I agree to the <a class="text-institutional-blue hover:underline" href="/legal_centre#terms">Customer Agreement</a>,
<a class="text-institutional-blue hover:underline" href="/legal_centre">Electronic Delivery Disclosure</a>, and
<a class="text-institutional-blue hover:underline" href="/legal_centre#privacy">Privacy Policy</a>.
</label>
</div>
<div id="register-form-message" class="text-sm hidden"></div>
<div class="flex gap-3">
<button type="button" id="register-step2-back" class="flex-1 py-sm rounded-lg border border-surface-gray text-on-surface-variant font-label-md hover:bg-surface-container-low transition-colors">Back</button>
<button type="submit" class="flex-[2] bg-fidelity-green text-on-primary font-headline-md py-sm px-md rounded-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-xs">
<span>Create Account</span>
<span class="material-symbols-outlined">arrow_forward</span>
</button>
</div>
</div>
</form>

<div id="register-otp-step" class="space-y-md hidden">
<h2 class="font-headline-md text-headline-md text-on-surface">Verify your email</h2>
<p class="text-on-surface-variant text-sm" id="register-otp-email-display"></p>
<p class="text-sm text-on-surface-variant">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-4" id="register-otp-inputs">
<?php for ($i = 1; $i <= 6; $i++): ?>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="<?php echo $i === 1 ? 'one-time-code' : 'off'; ?>" class="w-11 h-12 text-center text-lg font-bold rounded-lg border border-surface-gray bg-white text-on-surface focus:border-institutional-blue focus:ring-1 focus:ring-institutional-blue" data-otp-digit aria-label="Digit <?php echo $i; ?>"/>
<?php endfor; ?>
</div>
<div id="register-otp-message" class="text-sm hidden"></div>
<button type="button" id="register-otp-resend" class="text-institutional-blue hover:underline text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="register-otp-submit" class="w-full bg-fidelity-green text-on-primary font-headline-md py-sm rounded-lg hover:opacity-90 flex items-center justify-center gap-2">
Verify &amp; Continue
</button>
</div>

<div id="register-thank-you" class="hidden text-center py-8">
<div class="w-16 h-16 rounded-full bg-fidelity-green/10 flex items-center justify-center mx-auto mb-4">
<span class="material-symbols-outlined text-fidelity-green text-3xl">check_circle</span>
</div>
<h2 class="font-headline-md text-headline-md text-on-surface mb-2">Thank you!</h2>
<p class="text-on-surface-variant text-sm">Your account has been verified. Redirecting to your dashboard...</p>
</div>

<div id="register-form-footer" class="mt-lg pt-lg border-t border-surface-gray">
<div class="flex items-center gap-sm p-sm bg-surface-container-low rounded-lg">
<span class="material-symbols-outlined text-fidelity-green" style="font-variation-settings: 'FILL' 1;">shield</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Encrypted with 256-bit AES protection.</span>
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
      step1Msg.classList.remove('hidden');
      return;
    }
    if (!email) {
      step1Msg.textContent = 'Email address is required.';
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

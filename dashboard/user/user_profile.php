<?php 
require_once __DIR__ . '/../../includes/auth-check.php'; 
require_once __DIR__ . '/../../includes/helpers.php'; 
$siteName = get_site_name();
$currentPage = 'profile';
try {
    $profileUser = get_current_user_data() ?? [];
} catch (Throwable $e) {
    $profileUser = [];
}
$profileName = $profileUser['name'] ?? 'User';
$profileEmail = $profileUser['email'] ?? '';
$profilePhone = $profileUser['phone_number'] ?? '';
$profileCountry = $profileUser['country'] ?? '';
$profileAvatar = $profileUser['avatar_url'] ?? null;
$profileInitials = strtoupper(substr($profileName ?: 'U', 0, 2));
$profileUserId = isset($_SESSION['user_id']) ? 'BB-' . $_SESSION['user_id'] : '';
$profileVerified = !empty($profileUser['verified']);
$profileKycStatus = $profileUser['kyc_status'] ?? 'none';
$profile2FA = isset($profileUser['two_factor_enabled']) ? (bool)$profileUser['two_factor_enabled'] : false;
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Profile and Security Settings</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f9bd0b",
                        "background-light": "#f8f8f5",
                        "background-dark": "#231e0f",
                    },
                    fontFamily: {
                        "display": ["Space Grotesk"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .tab-active { border-bottom: 2px solid #f9bd0b; color: #f9bd0b; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 overflow-y-auto">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
<!-- Profile Header Section -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 mb-8 shadow-sm">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
<div class="flex items-center gap-6">
<div class="relative group">
<?php if ($profileAvatar): ?><img alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-primary/10" src="<?php echo htmlspecialchars($profileAvatar); ?>"/><?php else: ?><div class="w-24 h-24 rounded-full bg-primary/20 border-4 border-primary/10 flex items-center justify-center text-primary text-3xl font-bold"><?php echo htmlspecialchars($profileInitials); ?></div><?php endif; ?>
<button class="absolute bottom-0 right-0 bg-primary text-white p-1.5 rounded-full shadow-lg hover:scale-105 transition-transform">
<span class="material-icons text-sm">edit</span>
</button>
</div>
<div>
<div class="flex items-center gap-3">
<h1 class="text-2xl font-bold" data-profile-name><?php echo htmlspecialchars($profileName); ?></h1>
<?php if ($profileVerified): ?>
<span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs px-2.5 py-0.5 rounded-full font-bold flex items-center gap-1">
<span class="material-icons text-[14px]">verified</span> Verified
</span>
<?php endif; ?>
</div>
<p class="text-slate-500 dark:text-slate-400" data-profile-email><?php echo htmlspecialchars($profileEmail); ?></p>
<p class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">User ID: <span data-user-id><?php echo htmlspecialchars($profileUserId); ?></span></p>
</div>
</div>
<div class="flex gap-3">
<a href="/dashboard/user/kyc" class="bg-primary hover:bg-primary/90 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center gap-2">
<span class="material-icons text-sm">shield</span>
                        Verify Identity
                    </a>
</div>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
<!-- Left Column: Settings Forms -->
<div class="lg:col-span-2 space-y-8">
<!-- Profile Details -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<h2 class="text-lg font-bold mb-6 flex items-center gap-2">
                        Personal Information
                    </h2>
<form id="profile-form" class="space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-medium text-slate-500">Full Name</label>
<input name="name" class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all" type="text" value="<?php echo htmlspecialchars($profileName); ?>" placeholder="Your name"/>
</div>
<div class="space-y-2">
<label class="text-sm font-medium text-slate-500">Phone Number</label>
<input name="phone_number" class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all" type="text" value="<?php echo htmlspecialchars($profilePhone); ?>" placeholder="+1 234 567 8900"/>
</div>
<div class="space-y-2 md:col-span-2">
<label class="text-sm font-medium text-slate-500">Country/Region</label>
<select name="country" class="w-full bg-slate-50 dark:bg-background-dark/20 border-slate-200 dark:border-primary/20 rounded-lg focus:ring-primary focus:border-primary transition-all">
<option value="">Select country</option>
<option value="United Kingdom" <?php echo $profileCountry === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
<option value="United States" <?php echo $profileCountry === 'United States' ? 'selected' : ''; ?>>United States</option>
<option value="Germany" <?php echo $profileCountry === 'Germany' ? 'selected' : ''; ?>>Germany</option>
<option value="Singapore" <?php echo $profileCountry === 'Singapore' ? 'selected' : ''; ?>>Singapore</option>
<option value="Nigeria" <?php echo $profileCountry === 'Nigeria' ? 'selected' : ''; ?>>Nigeria</option>
<option value="Canada" <?php echo $profileCountry === 'Canada' ? 'selected' : ''; ?>>Canada</option>
<option value="Australia" <?php echo $profileCountry === 'Australia' ? 'selected' : ''; ?>>Australia</option>
<option value="India" <?php echo $profileCountry === 'India' ? 'selected' : ''; ?>>India</option>
</select>
</div>
</div>
<div id="profile-save-message" class="text-sm hidden"></div>
<div class="mt-8 flex justify-end">
<button type="submit" class="bg-primary text-white px-8 py-2.5 rounded-lg font-bold hover:bg-primary/90 transition-all">
                            Save Changes
                        </button>
</div>
</form>
</section>
<!-- Security Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- 2FA Card -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<div class="flex justify-between items-start mb-4">
<div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
<span class="material-icons">vibration</span>
</div>
<label class="relative inline-flex items-center cursor-pointer">
<input id="2fa-toggle" class="sr-only peer" type="checkbox" <?php echo $profile2FA ? 'checked' : ''; ?>/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-background-dark/60 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
</label>
</div>
<h3 class="font-bold text-lg mb-1">Two-Factor Auth</h3>
<p class="text-sm text-slate-500 mb-6 leading-relaxed">Email OTP required at login when enabled. To disable, you must verify with an OTP sent to your email.</p>
<button type="button" id="setup-2fa-btn" class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all font-bold py-2 rounded-lg">
                            <?php echo $profile2FA ? '2FA is enabled' : 'Enable 2FA'; ?>
                        </button>
</div>
<!-- Password Card -->
<div class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<div class="flex justify-between items-start mb-4">
<div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
<span class="material-icons">key</span>
</div>
</div>
<h3 class="font-bold text-lg mb-1">Password</h3>
<p class="text-sm text-slate-500 mb-6 leading-relaxed">Use a strong password with at least 8 characters.</p>
<button type="button" id="change-password-btn" class="w-full bg-slate-900 text-white dark:bg-primary dark:text-white transition-all font-bold py-2 rounded-lg">
                            Change Password
                        </button>
</div>
</div>
<!-- Login Activity -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="p-6 border-b border-primary/10 flex justify-between items-center">
<h2 class="text-lg font-bold">Recent Login Activity</h2>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 dark:bg-background-dark/20 text-slate-500 text-xs uppercase tracking-wider font-bold">
<tr>
<th class="px-6 py-4">Device</th>
<th class="px-6 py-4">IP Address</th>
<th class="px-6 py-4">Location</th>
<th class="px-6 py-4">Time</th>
<th class="px-6 py-4"></th>
</tr>
</thead>
<tbody class="divide-y divide-primary/5">
<tr class="hover:bg-slate-50/50 dark:hover:bg-background-dark/30 transition-colors">
<td class="px-6 py-4 flex items-center gap-3">
<span class="material-icons text-slate-400">desktop_windows</span>
<span class="font-medium text-sm">Current Session</span>
</td>
<td class="px-6 py-4">
<span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-2"></span>
<span class="text-sm text-slate-500">Active</span>
</td>
</tr>
</tbody>
</table>
</div>
<p class="px-6 py-4 text-sm text-slate-500">Session logging coming soon.</p>
</section>
</div>
<!-- Right Column: KYC Status -->
<div class="space-y-8">
<!-- KYC Progress Card -->
<section class="bg-white dark:bg-background-dark/40 rounded-xl border border-primary/10 p-6 shadow-sm">
<h3 class="font-bold text-lg mb-4 flex items-center justify-between">
                        Identity Verification
                        <?php if ($profileKycStatus === 'verified'): ?>
                        <span class="text-green-600 dark:text-green-400 text-sm font-medium">Verified</span>
                        <?php elseif ($profileKycStatus === 'pending'): ?>
                        <span class="text-primary text-sm font-medium">Under Review</span>
                        <?php elseif ($profileKycStatus === 'rejected'): ?>
                        <span class="text-red-600 dark:text-red-400 text-sm font-medium">Rejected</span>
                        <?php else: ?>
                        <span class="text-slate-500 text-sm font-medium">Not verified</span>
                        <?php endif; ?>
</h3>
<div class="space-y-4">
<div class="flex items-center gap-4">
<div class="w-8 h-8 rounded-full <?php echo $profileVerified ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-slate-100 dark:bg-background-dark/60 text-slate-400'; ?> flex items-center justify-center">
<span class="material-icons text-sm"><?php echo $profileVerified ? 'check' : 'mail'; ?></span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">Email Verified</p>
<p class="text-xs text-slate-400"><?php echo $profileVerified ? 'Confirmed' : 'Pending'; ?></p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="w-8 h-8 rounded-full <?php echo $profileKycStatus === 'verified' ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : ($profileKycStatus === 'pending' ? 'bg-primary/10 text-primary' : 'bg-slate-100 dark:bg-background-dark/60 text-slate-400'); ?> flex items-center justify-center">
<span class="material-icons text-sm"><?php echo $profileKycStatus === 'verified' ? 'check' : ($profileKycStatus === 'pending' ? 'hourglass_empty' : 'verified_user'); ?></span>
</div>
<div class="flex-1">
<p class="text-sm font-bold">KYC Status</p>
<p class="text-xs text-slate-400"><?php
if ($profileKycStatus === 'verified') echo 'Verified';
elseif ($profileKycStatus === 'pending') echo 'Documents under review';
elseif ($profileKycStatus === 'rejected') echo 'Please resubmit documents';
else echo 'Complete verification to withdraw';
?></p>
</div>
</div>
</div>
<a href="/dashboard/user/kyc" class="mt-8 block w-full bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all font-bold py-3 rounded-lg flex items-center justify-center gap-2 text-center">
<span class="material-icons text-sm"><?php echo in_array($profileKycStatus, ['verified']) ? 'visibility' : 'upload'; ?></span>
                        <?php echo in_array($profileKycStatus, ['verified']) ? 'View KYC' : 'Upload Documents'; ?>
                    </a>
</section>
<!-- Security Tips Card -->
<section class="bg-slate-900 text-white rounded-xl p-6 shadow-sm border border-slate-800">
<h3 class="font-bold text-lg mb-3">Security Tips</h3>
<ul class="space-y-4 text-sm text-slate-400">
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Never share your API keys or passwords with anyone.</p>
</li>
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Enable anti-phishing codes in your notification settings.</p>
</li>
<li class="flex gap-3">
<span class="material-icons text-primary text-sm mt-1">lightbulb</span>
<p>Check the URL is always <span class="text-white">bloombit.io</span>.</p>
</li>
</ul>
</section>
</div>
</div>
</main>
<!-- Disable 2FA OTP Modal -->
<div id="2fa-otp-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
<div class="bg-white dark:bg-background-dark rounded-xl max-w-md w-full p-8 shadow-2xl">
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-bold">Verify to Disable 2FA</h2>
<button type="button" id="2fa-otp-modal-close" class="text-slate-400 hover:text-slate-600 transition-colors"><span class="material-icons">close</span></button>
</div>
<p class="text-sm text-slate-500 mb-4">We'll send a 6-digit code to your email. Enter it below to disable 2FA.</p>
<div class="flex gap-2 justify-center my-6" id="2fa-otp-inputs">
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 1"/>
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 2"/>
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 3"/>
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 4"/>
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 5"/>
<input type="text" maxlength="1" class="w-12 h-14 text-center text-xl font-bold bg-slate-50 dark:bg-zinc-800 border rounded-lg focus:ring-2 focus:ring-primary" data-2fa-digit aria-label="Digit 6"/>
</div>
<div id="2fa-otp-message" class="text-sm hidden mb-4"></div>
<button type="button" id="2fa-otp-resend" class="text-primary hover:underline text-sm font-medium disabled:opacity-50 mb-4">Resend code</button>
<div class="flex gap-2 pt-2">
<button type="button" id="2fa-otp-submit" class="flex-1 bg-primary text-black font-bold py-2.5 rounded-lg hover:bg-primary/90">Verify & Disable</button>
<button type="button" id="2fa-otp-cancel" class="px-4 py-2.5 text-slate-600 dark:text-slate-400 font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800">Cancel</button>
</div>
</div>
</div>
<!-- Change Password Modal -->
<div id="password-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
<div class="bg-white dark:bg-background-dark rounded-xl max-w-md w-full p-8 shadow-2xl">
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-bold">Change Password</h2>
<button type="button" id="password-modal-close" class="text-slate-400 hover:text-slate-600 transition-colors"><span class="material-icons">close</span></button>
</div>
<form id="password-form" class="space-y-4">
<div>
<label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Current password</label>
<input type="password" id="pw-current" required class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter current password"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">New password (min 8 characters)</label>
<input type="password" id="pw-new" required minlength="8" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter new password"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Confirm new password</label>
<input type="password" id="pw-confirm" required minlength="8" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Confirm new password"/>
</div>
<div id="password-modal-message" class="text-sm hidden"></div>
<div class="flex gap-2 pt-2">
<button type="submit" class="flex-1 bg-primary text-black font-bold py-2.5 rounded-lg hover:bg-primary/90">Change Password</button>
<button type="button" id="password-modal-cancel" class="px-4 py-2.5 text-slate-600 dark:text-slate-400 font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800">Cancel</button>
</div>
</form>
</div>
</div>
<script src="/js/app.js"></script>
<script>
(function(){
  var profileForm = document.getElementById('profile-form');
  var profileMsg = document.getElementById('profile-save-message');
  var changePwBtn = document.getElementById('change-password-btn');
  var setup2faBtn = document.getElementById('setup-2fa-btn');

  if (profileForm) {
    profileForm.addEventListener('submit', function(e){
      e.preventDefault();
      var fd = new FormData(profileForm);
      var payload = { name: fd.get('name') || '', phone_number: fd.get('phone_number') || '', country: fd.get('country') || '' };
      fetch('/api/user/profile.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      }).then(function(r){ return r.json(); }).then(function(res){
        if (profileMsg) {
          profileMsg.classList.remove('hidden');
          profileMsg.className = 'text-sm ' + (res.success ? 'text-green-600' : 'text-red-600');
          profileMsg.textContent = res.success ? 'Profile saved.' : (res.error || 'Failed to save');
        }
        if (res.success && res.data) {
          var n = document.querySelector('[data-profile-name]'); if (n) n.textContent = res.data.name || 'User';
        }
      }).catch(function(){ if (profileMsg) { profileMsg.classList.remove('hidden'); profileMsg.className = 'text-sm text-red-600'; profileMsg.textContent = 'Network error'; } });
    });
  }

  var pwModal = document.getElementById('password-modal');
  var pwForm = document.getElementById('password-form');
  var pwMsg = document.getElementById('password-modal-message');
  var pwClose = document.getElementById('password-modal-close');
  var pwCancel = document.getElementById('password-modal-cancel');

  function showPasswordModal() {
    if (pwModal) { pwModal.classList.remove('hidden'); pwModal.classList.add('flex'); pwMsg.classList.add('hidden'); pwForm.reset(); }
  }
  function hidePasswordModal() {
    if (pwModal) { pwModal.classList.add('hidden'); pwModal.classList.remove('flex'); pwMsg.classList.add('hidden'); }
  }

  if (changePwBtn) changePwBtn.addEventListener('click', showPasswordModal);
  if (pwClose) pwClose.addEventListener('click', hidePasswordModal);
  if (pwCancel) pwCancel.addEventListener('click', hidePasswordModal);
  if (pwModal) pwModal.addEventListener('click', function(e){ if (e.target === pwModal) hidePasswordModal(); });

  if (pwForm) {
    pwForm.addEventListener('submit', function(e){
      e.preventDefault();
      var curr = document.getElementById('pw-current').value;
      var pass = document.getElementById('pw-new').value;
      var conf = document.getElementById('pw-confirm').value;
      if (pass.length < 8) { pwMsg.classList.remove('hidden'); pwMsg.className = 'text-sm text-red-600'; pwMsg.textContent = 'New password must be at least 8 characters'; return; }
      if (pass !== conf) { pwMsg.classList.remove('hidden'); pwMsg.className = 'text-sm text-red-600'; pwMsg.textContent = 'Passwords do not match'; return; }
      pwMsg.classList.add('hidden');
      fetch('/api/user/profile.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ current_password: curr, password: pass })
      }).then(function(r){ return r.json(); }).then(function(res){
        pwMsg.classList.remove('hidden');
        if (res.success) {
          pwMsg.className = 'text-sm text-green-600';
          pwMsg.textContent = 'Password changed successfully.';
          pwForm.reset();
          setTimeout(hidePasswordModal, 1500);
        } else {
          pwMsg.className = 'text-sm text-red-600';
          pwMsg.textContent = res.error || 'Failed to update password';
        }
      }).catch(function(){
        pwMsg.classList.remove('hidden'); pwMsg.className = 'text-sm text-red-600'; pwMsg.textContent = 'Network error';
      });
    });
  }

  var twoFaToggle = document.getElementById('2fa-toggle');
  var twoFaOtpModal = document.getElementById('2fa-otp-modal');
  var twoFaOtpClose = document.getElementById('2fa-otp-modal-close');
  var twoFaOtpInputs = document.querySelectorAll('[data-2fa-digit]');
  var twoFaOtpMessage = document.getElementById('2fa-otp-message');
  var twoFaOtpResend = document.getElementById('2fa-otp-resend');
  var twoFaOtpSubmit = document.getElementById('2fa-otp-submit');
  var twoFaOtpCancel = document.getElementById('2fa-otp-cancel');

  function show2faOtpModal() {
    if (twoFaOtpModal) {
      twoFaOtpModal.classList.remove('hidden');
      twoFaOtpModal.classList.add('flex');
      twoFaOtpMessage.classList.add('hidden');
      twoFaOtpInputs.forEach(function(inp){ inp.value = ''; });
      twoFaOtpInputs[0] && twoFaOtpInputs[0].focus();
      fetch('/api/auth/send-otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ email: '<?php echo addslashes($profileEmail); ?>', purpose: 'disable_2fa' })
      }).then(function(r){ return r.json(); }).then(function(res){
        if (!res.success) {
          twoFaOtpMessage.textContent = res.error || 'Failed to send code';
          twoFaOtpMessage.className = 'text-sm text-red-600';
          twoFaOtpMessage.classList.remove('hidden');
        }
      });
    }
  }
  function hide2faOtpModal() {
    if (twoFaOtpModal) { twoFaOtpModal.classList.add('hidden'); twoFaOtpModal.classList.remove('flex'); }
  }

  if (twoFaOtpClose) twoFaOtpClose.addEventListener('click', hide2faOtpModal);
  if (twoFaOtpCancel) twoFaOtpCancel.addEventListener('click', hide2faOtpModal);
  if (twoFaOtpModal) twoFaOtpModal.addEventListener('click', function(e){ if (e.target === twoFaOtpModal) hide2faOtpModal(); });

  if (twoFaToggle) {
    twoFaToggle.addEventListener('change', function(){
      var cb = this;
      var desired = cb.checked;
      if (desired) {
        fetch('/api/user/profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ action: 'enable_2fa' })
        }).then(function(r){ return r.json(); }).then(function(res){
          if (res.success) {
            if (setup2faBtn) setup2faBtn.textContent = '2FA is enabled';
          } else {
            cb.checked = false;
            alert(res.error || 'Failed to enable 2FA');
          }
        }).catch(function(){ cb.checked = false; });
      } else {
        show2faOtpModal();
        cb.checked = true;
      }
    });
  }

  if (setup2faBtn) {
    setup2faBtn.addEventListener('click', function(){
      if (twoFaToggle && twoFaToggle.checked) return;
      twoFaToggle.checked = true;
      twoFaToggle.dispatchEvent(new Event('change'));
    });
  }

  if (twoFaOtpResend) twoFaOtpResend.addEventListener('click', function(){
    if (twoFaOtpResend.disabled) return;
    twoFaOtpResend.disabled = true;
    fetch('/api/auth/send-otp.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ email: '<?php echo addslashes($profileEmail); ?>', purpose: 'disable_2fa' })
    }).then(function(r){ return r.json(); }).then(function(res){
      twoFaOtpMessage.textContent = res.success ? 'Code sent.' : (res.error || 'Failed');
      twoFaOtpMessage.className = 'text-sm ' + (res.success ? 'text-green-600' : 'text-red-600');
      twoFaOtpMessage.classList.remove('hidden');
      twoFaOtpResend.disabled = false;
    });
  });

  if (twoFaOtpSubmit) twoFaOtpSubmit.addEventListener('click', function(){
    var otp = Array.from(twoFaOtpInputs).map(function(i){ return i.value; }).join('');
    if (otp.length !== 6) {
      twoFaOtpMessage.textContent = 'Enter all 6 digits.';
      twoFaOtpMessage.className = 'text-sm text-red-600';
      twoFaOtpMessage.classList.remove('hidden');
      return;
    }
    twoFaOtpSubmit.disabled = true;
    fetch('/api/user/profile.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ action: 'disable_2fa', otp: otp })
    }).then(function(r){ return r.json(); }).then(function(res){
      if (res.success) {
        hide2faOtpModal();
        twoFaToggle.checked = false;
        if (setup2faBtn) setup2faBtn.textContent = 'Enable 2FA';
      } else {
        twoFaOtpMessage.textContent = res.error || 'Invalid code';
        twoFaOtpMessage.className = 'text-sm text-red-600';
        twoFaOtpMessage.classList.remove('hidden');
      }
      twoFaOtpSubmit.disabled = false;
    }).catch(function(){
      twoFaOtpMessage.textContent = 'Request failed.';
      twoFaOtpMessage.className = 'text-sm text-red-600';
      twoFaOtpMessage.classList.remove('hidden');
      twoFaOtpSubmit.disabled = false;
    });
  });

  twoFaOtpInputs.forEach(function(inp, i){
    inp.addEventListener('input', function(){
      if (this.value && i < twoFaOtpInputs.length - 1) twoFaOtpInputs[i + 1].focus();
    });
    inp.addEventListener('keydown', function(e){
      if (e.key === 'Backspace' && !this.value && i > 0) twoFaOtpInputs[i - 1].focus();
    });
  });
})();
</script>
</body></html>
<?php require_once __DIR__ . '/../../includes/auth-check.php'; require_once __DIR__ . '/../../includes/helpers.php'; $siteName = get_site_name();
$currentPage = 'kyc';
$profileUser = get_current_user_data() ?? [];
$kycStatus = $profileUser['kyc_status'] ?? 'none';
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | KYC Verification</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
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
              fontFamily: { "display": ["Space Grotesk"] },
              borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
          },
        }
    </script>
<style>body { font-family: 'Space Grotesk', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-800 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 min-h-0 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="max-w-2xl mx-auto">
<h1 class="text-2xl font-bold mb-2">Identity Verification</h1>
<p class="text-slate-500 dark:text-slate-400 mb-8">Optional identity verification for your account. Withdrawals do not require KYC at this time.</p>

<?php if ($kycStatus === 'verified'): ?>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-primary/10 p-8 shadow-sm text-center">
<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 flex items-center justify-center">
<span class="material-icons text-4xl">verified</span>
</div>
<h2 class="text-xl font-bold mb-2">You are verified</h2>
<p class="text-slate-500 dark:text-slate-400">Your identity has been verified. You can withdraw funds.</p>
</div>
<?php elseif ($kycStatus === 'pending'): ?>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-primary/10 p-8 shadow-sm text-center">
<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 text-primary flex items-center justify-center">
<span class="material-icons text-4xl">schedule</span>
</div>
<h2 class="text-xl font-bold mb-2">Under review</h2>
<p class="text-slate-500 dark:text-slate-400">Your documents are being reviewed. We will notify you when the verification is complete.</p>
</div>
<?php elseif ($kycStatus === 'rejected'): ?>
<div id="kyc-rejected" class="bg-white dark:bg-zinc-900 rounded-xl border border-red-200 dark:border-red-900/50 p-6 shadow-sm mb-8">
<div class="flex items-start gap-3">
<span class="material-icons text-red-500 text-2xl">error</span>
<div>
<h2 class="text-lg font-bold text-red-700 dark:text-red-400">Verification rejected</h2>
<p id="rejection-reason" class="text-slate-600 dark:text-slate-400 mt-1">Please resubmit your documents with the required corrections.</p>
</div>
</div>
</div>
<?php endif; ?>

<?php if (in_array($kycStatus, ['none', 'rejected'], true)): ?>
<form id="kyc-form" class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 shadow-sm space-y-6">
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Document type <span class="text-red-500">*</span></label>
<select name="document_type" required class="w-full bg-slate-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary">
<option value="">Select document type</option>
<option value="passport">Passport</option>
<option value="id_card">National ID Card</option>
<option value="driver_license">Driver's License</option>
</select>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Full name <span class="text-red-500">*</span></label>
<input type="text" name="full_name" required placeholder="As shown on document" class="w-full bg-slate-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($profileUser['name'] ?? ''); ?>"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Date of birth</label>
<input type="date" name="date_of_birth" class="w-full bg-slate-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary"/>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Address</label>
<textarea name="address" rows="3" placeholder="Residential address" class="w-full bg-slate-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
</div>
<div>
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Document front <span class="text-red-500">*</span></label>
<p class="text-xs text-slate-500 mb-2">JPG, PNG or PDF, max 5MB</p>
<input type="file" name="document_front" required accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-sm"/>
</div>
<div id="document-back-wrap">
<label class="block text-sm font-bold text-slate-600 dark:text-slate-300 mb-2">Document back <span class="text-red-500" id="back-required">*</span></label>
<p class="text-xs text-slate-500 mb-2">Required for ID card and driver's license</p>
<input type="file" name="document_back" accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-sm"/>
</div>
<div id="kyc-message" class="text-sm hidden"></div>
<button type="submit" class="w-full bg-primary hover:bg-primary/90 text-zinc-900 font-bold py-3 rounded-lg flex items-center justify-center gap-2">
<span class="material-icons">upload</span>
Submit for verification
</button>
</form>
<?php endif; ?>
</div>
</main>
<script src="/js/app.js"></script>
<script>
(function(){
  var form = document.getElementById('kyc-form');
  var msg = document.getElementById('kyc-message');
  var docType = form ? form.querySelector('[name="document_type"]') : null;
  var backWrap = document.getElementById('document-back-wrap');
  var backInput = form ? form.querySelector('[name="document_back"]') : null;
  var backRequired = document.getElementById('back-required');

  function toggleBackRequired() {
    if (!docType || !backWrap || !backInput) return;
    var t = (docType.value || '').toLowerCase();
    var need = t === 'id_card' || t === 'driver_license';
    backInput.required = need;
    if (backRequired) backRequired.style.display = need ? '' : 'none';
  }
  if (docType) docType.addEventListener('change', toggleBackRequired);
  toggleBackRequired();

  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var fd = new FormData(form);
      fetch('/api/user/kyc.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      }).then(function(r){ return r.json(); }).then(function(res){
        if (msg) {
          msg.classList.remove('hidden');
          msg.className = 'text-sm ' + (res.success ? 'text-green-600' : 'text-red-600');
          msg.textContent = res.success ? 'Documents submitted successfully.' : (res.error || 'Submission failed');
        }
        if (res.success) window.location.reload();
      }).catch(function(){
        if (msg) { msg.classList.remove('hidden'); msg.className = 'text-sm text-red-600'; msg.textContent = 'Network error'; }
      });
    });
  }

  var reasonEl = document.getElementById('rejection-reason');
  if (reasonEl) {
    fetch('/api/user/kyc.php', { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(res){
      if (res.success && res.data && res.data.rejection_reason) {
        reasonEl.textContent = res.data.rejection_reason;
      }
    });
  }
})();
</script>
</body></html>

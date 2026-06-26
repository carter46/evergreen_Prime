<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
$currentPage = 'kyc';
$profileUser = get_current_user_data() ?? [];
$kycStatus = $profileUser['kyc_status'] ?? 'none';
$pageTitle = $siteName . ' | KYC Verification';
$pageHeading = 'Identity Verification';
$pageSubtitle = 'Optional identity verification for your account. Withdrawals do not require KYC at this time.';
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
include __DIR__ . '/../../includes/dashboard/user-page-title.php';
?>
<div class="max-w-2xl mx-auto">

<?php if ($kycStatus === 'verified'): ?>
<div class="glass-panel rounded-xl p-8 text-center">
<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-success/10 text-success flex items-center justify-center">
<span class="material-symbols-outlined text-4xl">verified</span>
</div>
<h2 class="text-xl font-bold mb-2 text-on-surface">You are verified</h2>
<p class="text-text-secondary">Your identity has been verified. You can withdraw funds.</p>
</div>
<?php elseif ($kycStatus === 'pending'): ?>
<div class="glass-panel rounded-xl p-8 text-center">
<div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-container/10 text-primary-container flex items-center justify-center">
<span class="material-symbols-outlined text-4xl">schedule</span>
</div>
<h2 class="text-xl font-bold mb-2 text-on-surface">Under review</h2>
<p class="text-text-secondary">Your documents are being reviewed. We will notify you when the verification is complete.</p>
</div>
<?php elseif ($kycStatus === 'rejected'): ?>
<div id="kyc-rejected" class="glass-panel rounded-xl border border-critical/30 p-6 mb-8">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-critical text-2xl">error</span>
<div>
<h2 class="text-lg font-bold text-critical">Verification rejected</h2>
<p id="rejection-reason" class="text-text-secondary mt-1">Please resubmit your documents with the required corrections.</p>
</div>
</div>
</div>
<?php endif; ?>

<?php if (in_array($kycStatus, ['none', 'rejected'], true)): ?>
<form id="kyc-form" class="glass-panel rounded-xl p-6 sm:p-8 space-y-6">
<div>
<label class="block text-sm font-bold text-on-surface-variant mb-2">Document type <span class="text-critical">*</span></label>
<select name="document_type" required class="w-full bg-surface-container-high border border-low rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary-container text-on-surface">
<option value="">Select document type</option>
<option value="passport">Passport</option>
<option value="id_card">National ID Card</option>
<option value="driver_license">Driver's License</option>
</select>
</div>
<div>
<label class="block text-sm font-bold text-on-surface-variant mb-2">Full name <span class="text-critical">*</span></label>
<input type="text" name="full_name" required placeholder="As shown on document" class="w-full bg-surface-container-high border border-low rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary-container text-on-surface" value="<?php echo htmlspecialchars($profileUser['name'] ?? ''); ?>"/>
</div>
<div>
<label class="block text-sm font-bold text-on-surface-variant mb-2">Date of birth</label>
<input type="date" name="date_of_birth" class="w-full bg-surface-container-high border border-low rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary-container text-on-surface"/>
</div>
<div>
<label class="block text-sm font-bold text-on-surface-variant mb-2">Address</label>
<textarea name="address" rows="3" placeholder="Residential address" class="w-full bg-surface-container-high border border-low rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-container focus:border-primary-container text-on-surface"></textarea>
</div>
<div>
<label class="block text-sm font-bold text-on-surface-variant mb-2">Document front <span class="text-critical">*</span></label>
<p class="text-xs text-text-secondary mb-2">JPG, PNG or PDF, max 5MB</p>
<input type="file" name="document_front" required accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-sm text-on-surface"/>
</div>
<div id="document-back-wrap">
<label class="block text-sm font-bold text-on-surface-variant mb-2">Document back <span class="text-critical" id="back-required">*</span></label>
<p class="text-xs text-text-secondary mb-2">Required for ID card and driver's license</p>
<input type="file" name="document_back" accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-sm text-on-surface"/>
</div>
<div id="kyc-message" class="text-sm hidden"></div>
<button type="submit" class="w-full bg-primary-container hover:bg-primary-container/90 text-on-primary font-bold py-3 rounded-lg flex items-center justify-center gap-2">
<span class="material-symbols-outlined">upload</span>
Submit for verification
</button>
</form>
<?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
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
          msg.className = 'text-sm ' + (res.success ? 'text-success' : 'text-critical');
          msg.textContent = res.success ? 'Documents submitted successfully.' : (res.error || 'Submission failed');
        }
        if (res.success) window.location.reload();
      }).catch(function(){
        if (msg) { msg.classList.remove('hidden'); msg.className = 'text-sm text-critical'; msg.textContent = 'Network error'; }
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

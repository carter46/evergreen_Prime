<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'kyc';
$siteName = get_site_name();

$pending = [];
$approved = [];
$rejected = [];

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $tblChk = $pdo->query("SHOW TABLES LIKE 'kyc_submissions'");
    if ($tblChk && $tblChk->rowCount() > 0) {
        $stmt = $pdo->query("SELECT k.id, k.user_id, k.document_type, k.front_path, k.back_path, k.full_name, k.date_of_birth, k.address, k.status, k.rejection_reason, k.created_at, u.name, u.email FROM kyc_submissions k JOIN users u ON u.id = k.user_id WHERE k.status = 'pending' ORDER BY k.created_at ASC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $pending[] = $row;
        $stmt = $pdo->query("SELECT k.id, k.user_id, k.document_type, k.front_path, k.back_path, k.full_name, k.date_of_birth, k.address, k.status, k.rejection_reason, k.created_at, u.name, u.email FROM kyc_submissions k JOIN users u ON u.id = k.user_id WHERE k.status = 'approved' ORDER BY k.created_at DESC LIMIT 100");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $approved[] = $row;
        $stmt = $pdo->query("SELECT k.id, k.user_id, k.document_type, k.front_path, k.back_path, k.full_name, k.date_of_birth, k.address, k.status, k.rejection_reason, k.created_at, u.name, u.email FROM kyc_submissions k JOIN users u ON u.id = k.user_id WHERE k.status = 'rejected' ORDER BY k.created_at DESC LIMIT 100");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) $rejected[] = $row;
    }
} catch (Throwable $e) {}

$filter = $_GET['filter'] ?? 'pending';
$list = $filter === 'approved' ? $approved : ($filter === 'rejected' ? $rejected : $pending);
$pendingCount = count($pending);
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> Admin | KYC Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#f9bd0b", "background-light": "#f8f8f5", "background-dark": "#231e0f" }, fontFamily: { "display": ["Inter", "sans-serif"] } } } };
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8">
<div class="mb-6">
<h1 class="text-2xl font-bold mb-2">KYC Management</h1>
<p class="text-slate-500">Review and approve or reject user identity verification submissions</p>
</div>

<div class="flex gap-2 mb-6">
<a href="?filter=pending" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'pending' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">
    Pending <?php if ($pendingCount > 0): ?><span class="ml-1 px-1.5 py-0.5 bg-black/10 text-inherit text-[10px] rounded-full"><?php echo $pendingCount; ?></span><?php endif; ?>
</a>
<a href="?filter=approved" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'approved' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">Approved</a>
<a href="?filter=rejected" class="px-4 py-2 text-xs font-bold rounded-lg <?php echo $filter === 'rejected' ? 'bg-primary text-black' : 'bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400'; ?>">Rejected</a>
</div>

<div class="bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-background-light dark:bg-white/5 border-b border-primary/10">
<tr>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">User</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Document</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Date</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-primary/5">
<?php foreach ($list as $k): ?>
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-6 py-4">
<p class="text-sm font-bold"><?php echo htmlspecialchars($k['name'] ?: $k['email']); ?></p>
<p class="text-[10px] text-slate-500"><?php echo htmlspecialchars($k['email']); ?> / BB-<?php echo (int)$k['user_id']; ?></p>
</td>
<td class="px-6 py-4 text-sm"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $k['document_type']))); ?></td>
<td class="px-6 py-4 text-xs text-slate-500"><?php echo date('M j, Y H:i', strtotime($k['created_at'])); ?></td>
<td class="px-6 py-4 text-right">
<?php if ($k['status'] === 'pending'): ?>
<div class="relative inline-block">
<button type="button" class="kyc-actions-btn p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-500" aria-label="Actions"><span class="material-icons text-lg">more_vert</span></button>
<div class="kyc-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg shadow-lg z-10 min-w-[100px]">
<button type="button" class="kyc-action-view block w-full text-left px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="<?php echo (int)$k['id']; ?>" data-front="<?php echo htmlspecialchars($k['front_path']); ?>" data-back="<?php echo htmlspecialchars($k['back_path'] ?? ''); ?>" data-name="<?php echo htmlspecialchars($k['full_name']); ?>" data-dob="<?php echo htmlspecialchars($k['date_of_birth'] ?? ''); ?>" data-address="<?php echo htmlspecialchars($k['address'] ?? ''); ?>">View</button>
<button type="button" class="kyc-action-approve block w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="<?php echo (int)$k['id']; ?>">Approve</button>
<button type="button" class="kyc-action-reject block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="<?php echo (int)$k['id']; ?>">Reject</button>
</div>
</div>
<?php elseif ($k['status'] === 'rejected' && !empty($k['rejection_reason'])): ?>
<span class="text-xs text-slate-500" title="<?php echo htmlspecialchars($k['rejection_reason']); ?>"><?php echo htmlspecialchars(strlen($k['rejection_reason']) > 30 ? substr($k['rejection_reason'], 0, 30) . '...' : $k['rejection_reason']); ?></span>
<?php else: ?>
<span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase <?php echo $k['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'; ?>"><?php echo htmlspecialchars($k['status']); ?></span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($list)): ?>
<tr><td class="px-6 py-8 text-center text-slate-500" colspan="4">No <?php echo htmlspecialchars($filter); ?> submissions.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</main>

<!-- View modal -->
<div id="kyc-view-modal" class="fixed inset-0 bg-black/70 z-50 hidden flex items-center justify-center p-4">
<div class="bg-white dark:bg-zinc-900 rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
<div class="p-4 border-b border-slate-200 dark:border-zinc-700 flex justify-between items-center">
<h2 id="view-modal-title" class="font-bold">KYC Document</h2>
<button type="button" id="view-modal-close" class="text-slate-500 hover:text-slate-700"><span class="material-icons">close</span></button>
</div>
<div class="p-4 overflow-y-auto flex-1">
<div id="view-modal-details" class="mb-4 text-sm text-slate-600 dark:text-slate-400"></div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div id="view-front-wrap"><p class="text-xs font-bold text-slate-500 mb-2">Front</p><a id="view-front-link" target="_blank" class="block"><img id="view-front-img" class="max-w-full rounded border border-slate-200 dark:border-zinc-700" alt="Front"/></a></div>
<div id="view-back-wrap"><p class="text-xs font-bold text-slate-500 mb-2">Back</p><a id="view-back-link" target="_blank" class="block"><img id="view-back-img" class="max-w-full rounded border border-slate-200 dark:border-zinc-700" alt="Back"/></a></div>
</div>
</div>
</div>
</div>

<!-- Reject modal -->
<div id="reject-modal" class="fixed inset-0 bg-black/70 z-50 hidden flex items-center justify-center p-4">
<div class="bg-white dark:bg-zinc-900 rounded-xl max-w-md w-full p-6">
<h2 class="font-bold mb-4">Reject KYC</h2>
<label class="block text-sm font-medium text-slate-600 mb-2">Reason (required)</label>
<textarea id="reject-reason" rows="4" placeholder="e.g. Blurry image, expired document..." class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm mb-4"></textarea>
<div class="flex gap-2 justify-end">
<button type="button" id="reject-cancel" class="px-4 py-2 text-sm font-bold rounded-lg bg-slate-200 dark:bg-white/10">Cancel</button>
<button type="button" id="reject-confirm" class="px-4 py-2 text-sm font-bold rounded-lg bg-red-500 text-white">Reject</button>
</div>
</div>
</div>

<script src="/js/app.js"></script>
<script>
(function(){
  var viewModal = document.getElementById('kyc-view-modal');
  var viewClose = document.getElementById('view-modal-close');
  var viewFrontWrap = document.getElementById('view-front-wrap');
  var viewBackWrap = document.getElementById('view-back-wrap');
  var viewFrontLink = document.getElementById('view-front-link');
  var viewFrontImg = document.getElementById('view-front-img');
  var viewBackLink = document.getElementById('view-back-link');
  var viewBackImg = document.getElementById('view-back-img');
  var viewDetails = document.getElementById('view-modal-details');
  var rejectModal = document.getElementById('reject-modal');
  var rejectReason = document.getElementById('reject-reason');
  var rejectCancel = document.getElementById('reject-cancel');
  var rejectConfirm = document.getElementById('reject-confirm');

  var currentRejectId = null;
  var baseUrl = '/api/admin/kyc-view.php?path=';

  function openView(btn) {
    var front = btn.getAttribute('data-front');
    var back = btn.getAttribute('data-back');
    var name = btn.getAttribute('data-name');
    var dob = btn.getAttribute('data-dob');
    var addr = btn.getAttribute('data-address');
    viewDetails.innerHTML = '<p><strong>Name:</strong> ' + (name || '-') + '</p><p><strong>DOB:</strong> ' + (dob || '-') + '</p><p><strong>Address:</strong> ' + (addr || '-') + '</p>';
    if (front) {
      var url = baseUrl + encodeURIComponent(front);
      viewFrontWrap.style.display = '';
      viewFrontLink.href = url;
      if (/\.(jpg|jpeg|png|gif|webp)$/i.test(front)) {
        viewFrontImg.src = url;
        viewFrontImg.style.display = '';
      } else {
        viewFrontImg.style.display = 'none';
        viewFrontLink.textContent = 'View PDF';
      }
    } else viewFrontWrap.style.display = 'none';
    if (back) {
      var urlB = baseUrl + encodeURIComponent(back);
      viewBackWrap.style.display = '';
      viewBackLink.href = urlB;
      if (/\.(jpg|jpeg|png|gif|webp)$/i.test(back)) {
        viewBackImg.src = urlB;
        viewBackImg.style.display = '';
      } else {
        viewBackImg.style.display = 'none';
        viewBackLink.textContent = 'View PDF';
      }
    } else viewBackWrap.style.display = 'none';
    viewModal.classList.remove('hidden');
    viewModal.classList.add('flex');
  }

  function closeKycDropdowns() {
    document.querySelectorAll('.kyc-actions-dropdown').forEach(function(d){ d.classList.add('hidden'); });
  }

  document.querySelectorAll('.kyc-actions-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.stopPropagation();
      closeKycDropdowns();
      var dd = btn.nextElementSibling;
      if (dd) dd.classList.toggle('hidden');
    });
  });
  document.addEventListener('click', closeKycDropdowns);

  document.querySelectorAll('.kyc-action-view').forEach(function(btn){
    btn.addEventListener('click', function(e){ e.stopPropagation(); closeKycDropdowns(); openView(btn); });
  });

  if (viewClose) viewClose.addEventListener('click', function(){ viewModal.classList.add('hidden'); viewModal.classList.remove('flex'); });
  viewModal.addEventListener('click', function(e){ if (e.target === viewModal) { viewModal.classList.add('hidden'); viewModal.classList.remove('flex'); } });

  function doApprove(id) {
    fetch('/api/admin/kyc.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ action: 'approve', submission_id: id }) })
      .then(function(r){ return r.json(); }).then(function(res){ if (res.success) location.reload(); else alert(res.error || 'Failed'); }).catch(function(){ alert('Network error'); });
  }
  function doReject(id, reason) {
    fetch('/api/admin/kyc.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ action: 'reject', submission_id: id, reason: reason }) })
      .then(function(r){ return r.json(); }).then(function(res){ if (res.success) location.reload(); else alert(res.error || 'Failed'); }).catch(function(){ alert('Network error'); });
  }

  document.querySelectorAll('.kyc-action-approve').forEach(function(btn){
    btn.addEventListener('click', function(e){ e.stopPropagation(); closeKycDropdowns(); if (confirm('Approve this KYC submission?')) doApprove(parseInt(btn.getAttribute('data-id'), 10)); });
  });
  document.querySelectorAll('.kyc-action-reject').forEach(function(btn){
    btn.addEventListener('click', function(e){ e.stopPropagation(); currentRejectId = parseInt(btn.getAttribute('data-id'), 10); rejectReason.value = ''; rejectModal.classList.remove('hidden'); rejectModal.classList.add('flex'); closeKycDropdowns(); });
  });
  if (rejectCancel) rejectCancel.addEventListener('click', function(){ rejectModal.classList.add('hidden'); rejectModal.classList.remove('flex'); currentRejectId = null; });
  if (rejectConfirm) rejectConfirm.addEventListener('click', function(){
    var r = rejectReason.value.trim();
    if (!r) { alert('Please enter a reason'); return; }
    if (currentRejectId) doReject(currentRejectId, r);
    rejectModal.classList.add('hidden'); rejectModal.classList.remove('flex');
    currentRejectId = null;
  });
})();
</script>
</body></html>

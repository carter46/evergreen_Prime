<?php 
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/plan-types.php';
$currentPage = 'plans';

$adminPlans = [];
$planStats = ['total_users' => 0, 'total_capital' => 0, 'avg_payout' => 0];
$planStatsById = [];
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    ensure_plan_schema($pdo);
    $stmt = $pdo->query('SELECT * FROM plans ORDER BY sort_order, id');
    $adminPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $row = $pdo->query('SELECT COUNT(DISTINCT user_id) AS cnt, COALESCE(SUM(amount),0) AS cap FROM user_investments WHERE status="active"')->fetch(PDO::FETCH_ASSOC);
    $planStats['total_users'] = (int)($row['cnt'] ?? 0);
    $planStats['total_capital'] = (float)($row['cap'] ?? 0);
    
    $avgRow = $pdo->query('SELECT AVG(yield_min) AS avg_yield FROM plans WHERE enabled=1')->fetch(PDO::FETCH_ASSOC);
    $planStats['avg_payout'] = $avgRow && $avgRow['avg_yield'] ? number_format((float)$avgRow['avg_yield'], 1) : '0';
    
    $planStatsById = [];
    $stmt2 = $pdo->query('SELECT plan_id, COUNT(*) AS users, COALESCE(SUM(amount),0) AS capital FROM user_investments WHERE status="active" GROUP BY plan_id');
    while ($r = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $planStatsById[(int)$r['plan_id']] = ['users' => (int)$r['users'], 'capital' => (float)$r['capital']];
    }
} catch (Throwable $e) {
    $planStatsById = [];
}
$siteName = get_site_name();
$siteSettings = [
    'max_active_plans_per_user' => get_site_setting('max_active_plans_per_user', '3'),
    'compounding_enabled' => get_site_setting('compounding_enabled', '0'),
];
$planTypes = get_plan_types();
$riskOptions = get_investment_risk_options();

$pageTitle = $siteName . ' | Investment Plan Management';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
?>
<style>
        .custom-scrollbar {
            scrollbar-gutter: stable;
            padding-right: 0.5rem;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
        .material-symbols-outlined { overflow: hidden; display: inline-flex; align-items: center; justify-content: center; }
        .plan-card-icon { width: 48px; height: 48px; min-width: 48px; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .plan-drawer-icon-btn { width: 40px; height: 40px; min-width: 40px; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
</style>
<?php
$pageHeading = 'Investment Plan Management';
$pageSubtitle = "Manage and configure {$siteName}'s crypto investment offerings.";
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<div class="flex justify-end mb-8">
<button type="button" id="add-plan-btn" class="bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:shadow-lg transition-all active:scale-95">
<span class="material-symbols-outlined text-lg">add</span> Add New Plan
            </button>
</div>
<!-- Stats Overview Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Total Active Users</p>
<p class="text-2xl font-bold"><?php echo number_format($planStats['total_users']); ?></p>
</div>
<div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined">group</span>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Total Capital Invested</p>
<p class="text-2xl font-bold">$<?php echo format_usd_amount($planStats['total_capital']); ?></p>
</div>
<div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined">payments</span>
</div>
</div>
<div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<p class="text-sm text-slate-500">Avg. Daily Payout</p>
<p class="text-2xl font-bold"><?php echo $planStats['avg_payout']; ?>%</p>
</div>
<div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined">trending_up</span>
</div>
</div>
</div>
<!-- Plan Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-12 min-w-0">
<?php 
foreach ($adminPlans as $idx => $p):
    $ps = $planStatsById[(int)$p['id']] ?? ['users' => 0, 'capital' => 0];
    $activeUsers = (int)($ps['users'] ?? 0);
    $enabled = (bool)$p['enabled'];
    $avgYield = (float) ($p['yield_min'] ?? 0);
    $planTypeKey = normalize_plan_type($p['plan_type'] ?? 'crypto');
    $planTypeLabel = plan_type_label($planTypeKey);
    $logoUrl = $p['logo_url'] ?? null;
    $riskBadge = plan_investment_risk_badge($p['investment_risk'] ?? 'mid');
?>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-primary/50 transition-colors group relative overflow-hidden min-w-0">
<div class="p-6">
<div class="flex justify-between items-start gap-4 mb-4">
<div class="plan-card-icon rounded-lg overflow-hidden bg-slate-100 dark:bg-zinc-800">
<?php echo plan_logo_markup($logoUrl, $p['name'], 'w-12 h-12', 'text-base'); ?>
</div>
<div class="flex flex-col items-end shrink-0 gap-2">
<span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 text-[10px] font-bold uppercase tracking-wide"><?php echo htmlspecialchars($planTypeLabel); ?></span>
<span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full <?php echo $enabled ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500'; ?> text-xs font-medium">
<span class="w-1.5 h-1.5 rounded-full <?php echo $enabled ? 'bg-green-600' : 'bg-slate-400'; ?>"></span> <?php echo $enabled ? 'Enabled' : 'Disabled'; ?>
</span>
</div>
</div>
<h3 class="text-xl font-bold mb-1 <?php echo $enabled ? '' : 'text-slate-400'; ?>"><?php echo htmlspecialchars($p['name']); ?></h3>
<p class="text-sm text-slate-500 mb-6"><?php echo htmlspecialchars($p['description'] ?: 'No description'); ?></p>
<div class="space-y-4 mb-6 <?php echo $enabled ? '' : 'opacity-60'; ?>">
<div class="flex justify-between text-sm">
<span class="text-slate-500">Active Users</span>
<span class="font-semibold"><?php echo number_format($ps['users']); ?></span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Total Capital</span>
<span class="font-semibold">$<?php echo format_usd_amount($ps['capital']); ?></span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500">Daily ROI</span>
<span class="font-semibold text-green-600"><?php echo number_format($avgYield, 1); ?>%</span>
</div>
</div>
<div class="flex items-center justify-between gap-3 pt-6 border-t border-slate-100 dark:border-zinc-800">
<div class="flex items-center gap-2">
<button type="button" class="plan-edit-btn w-10 h-10 rounded-lg flex items-center justify-center border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 transition-colors" data-plan-id="<?php echo (int)$p['id']; ?>">
<span class="material-symbols-outlined text-sm">edit</span>
</button>
<button type="button" class="plan-delete-btn w-10 h-10 rounded-lg flex items-center justify-center border border-slate-200 dark:border-zinc-700 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600" data-plan-id="<?php echo (int)$p['id']; ?>" data-plan-name="<?php echo htmlspecialchars($p['name']); ?>">
<span class="material-symbols-outlined text-sm">delete</span>
</button>
<label class="relative inline-flex items-center cursor-pointer" title="<?php echo $activeUsers > 0 ? 'Cannot disable: plan has ' . $activeUsers . ' active user(s)' : ''; ?>">
<input class="sr-only peer plan-enabled-toggle" type="checkbox" data-plan-id="<?php echo (int)$p['id']; ?>" data-active-users="<?php echo $activeUsers; ?>" <?php echo $enabled ? 'checked' : ''; ?> <?php echo $activeUsers > 0 ? 'disabled' : ''; ?>/>
<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"></div>
</label>
</div>
<span class="text-xs font-bold uppercase tracking-wide px-2 py-0.5 rounded <?php echo $riskBadge['class']; ?>"><?php echo htmlspecialchars($riskBadge['label']); ?></span>
</div>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($adminPlans)): ?>
<div class="col-span-3 text-center py-12 text-slate-500">No plans yet. Add a plan to get started.</div>
<?php endif; ?>
</div>
<!-- Global Settings Section -->
<section class="mb-12">
<h2 class="text-lg font-bold mb-6 flex items-center gap-2">
<span class="material-symbols-outlined text-primary">public</span> Global Parameters
            </h2>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Max. Active Plans / User</label>
<input id="global-max-plans" class="w-full bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" type="number" min="1" value="<?php echo htmlspecialchars($siteSettings['max_active_plans_per_user']); ?>"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Compounding Availability</label>
<div class="flex items-center gap-4 mt-2">
<span class="text-xs text-slate-500">Disabled</span>
<label class="relative inline-flex items-center cursor-pointer">
<input id="global-compounding" class="sr-only peer" type="checkbox" <?php echo $siteSettings['compounding_enabled'] === '1' ? 'checked' : ''; ?>/>
<div class="w-10 h-5 bg-slate-200 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
</label>
<span class="text-xs text-slate-500">Enabled</span>
</div>
</div>
<div class="flex items-end">
<button type="button" id="global-settings-save" class="w-full bg-primary/20 hover:bg-primary/30 text-zinc-900 font-semibold py-2.5 rounded-lg transition-colors">Update Global Settings</button>
</div>
</div>
</div>
</section>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>

<!-- Side Slide-out Panel (Configuration Drawer) - matches user management style -->
<div id="plan-drawer" class="fixed inset-0 z-50 overflow-hidden hidden">
<div class="absolute inset-0 bg-black/30 backdrop-blur-sm" id="plan-drawer-backdrop"></div>
<div class="absolute inset-y-0 right-0 w-full sm:w-[440px] max-w-[100vw] bg-white dark:bg-zinc-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-zinc-800">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<div>
<h2 id="plan-drawer-title" class="text-xl font-bold">Edit Plan</h2>
<p id="plan-drawer-subtitle" class="text-xs text-slate-500 uppercase tracking-widest mt-1"></p>
</div>
<button type="button" id="plan-drawer-x" class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-full">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="flex-1 overflow-y-auto overflow-x-hidden p-6 custom-scrollbar">
<form id="admin-plan-form" class="space-y-8 min-w-0">
<input type="hidden" name="id" id="plan-form-id" value=""/>
<input type="hidden" name="enabled" value="1"/>
<input type="hidden" name="sort_order" value="0"/>
<input type="hidden" name="logo_url" id="plan-form-logo-url" value=""/>
<!-- Basic Info -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Basic Information</p>
<div class="grid grid-cols-2 gap-4">
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Plan Type</label>
<select name="plan_type" id="plan-form-type" class="w-full min-w-0 bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg focus:ring-primary focus:border-primary px-3 py-2 text-sm" required>
<?php foreach ($planTypes as $typeKey => $typeLabel): ?>
<option value="<?php echo htmlspecialchars($typeKey); ?>"><?php echo htmlspecialchars($typeLabel); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Plan Name</label>
<input name="name" id="plan-form-name" class="w-full min-w-0 bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg focus:ring-primary focus:border-primary px-3 py-2 text-sm" type="text" placeholder="e.g. Bitcoin (BTC)" required/>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Description</label>
<textarea name="description" id="plan-form-description" class="w-full min-w-0 bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg focus:ring-primary focus:border-primary px-3 py-2 text-sm" rows="2" placeholder="e.g. Digital Gold Reserve"></textarea>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-2">Plan Logo</label>
<div class="flex items-center gap-4 min-w-0">
<div id="plan-form-logo-preview" class="w-14 h-14 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0 text-primary font-bold">?</div>
<div class="flex-1 min-w-0">
<input type="file" id="plan-form-logo-file" accept="image/png,image/jpeg,image/webp" class="w-full text-sm text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:bg-primary file:text-black file:text-xs"/>
<p class="text-[10px] text-slate-400 mt-1">PNG, JPG, or WEBP. Max 2MB. Leave empty to show name initial.</p>
<button type="button" id="plan-form-logo-clear" class="mt-2 text-xs text-red-500 hover:underline hidden">Remove logo</button>
</div>
</div>
</div>
</div>
</div>
<!-- Financial Bounds -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Financial Parameters</p>
<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium mb-1.5">Min. Investment ($)</label>
<input name="min_deposit" id="plan-form-min" class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="100"/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Max. Investment ($)</label>
<input name="max_deposit" id="plan-form-max" class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" placeholder="Leave empty for no max"/>
</div>
<div class="col-span-2">
<label class="block text-sm font-medium mb-1.5">Daily ROI (%)</label>
<input name="yield" id="plan-form-yield" class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" step="0.1" value="1" required/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Duration (Days)</label>
<input name="min_duration_days" id="plan-form-min-days" class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" min="1" required placeholder="e.g. 7"/>
</div>
<div>
<label class="block text-sm font-medium mb-1.5">Referral Comm. (%)</label>
<input class="w-full bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg" type="number" value="5"/>
</div>
</div>
</div>
<!-- Features (one per line) -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Features</p>
<label class="block text-sm font-medium mb-1.5">Features (one per line)</label>
<textarea name="features_text" id="plan-form-features" class="w-full min-w-0 bg-slate-50 dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 rounded-lg focus:ring-primary focus:border-primary px-3 py-2 text-sm" rows="6" placeholder="e.g.&#10;$100 - $2,500 Investment Range&#10;Basic AI Trading Strategy&#10;Weekly Withdrawals"></textarea>
</div>
<!-- Investment Risk -->
<div class="space-y-4">
<p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Investment Risk</p>
<div class="grid grid-cols-3 gap-3">
<?php foreach ($riskOptions as $riskKey => $riskLabel): ?>
<label class="relative group cursor-pointer">
<input class="sr-only peer plan-form-risk" name="investment_risk" type="radio" value="<?php echo htmlspecialchars($riskKey); ?>"<?php echo $riskKey === 'mid' ? ' checked' : ''; ?>/>
<div class="p-3 border-2 border-slate-100 dark:border-zinc-800 rounded-lg text-center peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
<p class="text-xs font-bold"><?php echo htmlspecialchars($riskLabel); ?></p>
</div>
</label>
<?php endforeach; ?>
</div>
</div>
</form>
</div>
<div class="p-6 border-t border-slate-200 dark:border-zinc-800 grid grid-cols-2 gap-4">
<button type="button" id="plan-drawer-close" class="px-6 py-3 border border-slate-200 dark:border-zinc-700 rounded-lg font-semibold hover:bg-slate-50 transition-colors">Discard</button>
<button type="submit" form="admin-plan-form" class="px-6 py-3 bg-primary text-zinc-900 rounded-lg font-bold shadow-lg shadow-primary/20">Save Changes</button>
</div>
</div>
</div>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
(function(){
var drawer = document.getElementById('plan-drawer');
var logoUrlInput = document.getElementById('plan-form-logo-url');
var logoPreview = document.getElementById('plan-form-logo-preview');
var logoFileInput = document.getElementById('plan-form-logo-file');
var logoClearBtn = document.getElementById('plan-form-logo-clear');
var nameInput = document.getElementById('plan-form-name');

function planInitial(name) {
  name = (name || '').trim();
  if (!name) return '?';
  var match = name.match(/\(([^)]+)\)/);
  if (match && match[1]) return match[1].trim().charAt(0).toUpperCase();
  return name.charAt(0).toUpperCase();
}

function renderLogoPreview(url, name) {
  if (!logoPreview) return;
  if (url) {
    logoPreview.innerHTML = '<img src="' + url + '" alt="" class="w-full h-full object-cover"/>';
    if (logoClearBtn) logoClearBtn.classList.remove('hidden');
  } else {
    logoPreview.innerHTML = planInitial(name);
    if (logoClearBtn) logoClearBtn.classList.add('hidden');
  }
}

function setLogoUrl(url) {
  if (logoUrlInput) logoUrlInput.value = url || '';
  renderLogoPreview(url || '', nameInput ? nameInput.value : '');
}

if (nameInput) {
  nameInput.addEventListener('input', function () {
    if (!logoUrlInput || !logoUrlInput.value) renderLogoPreview('', nameInput.value);
  });
}

if (logoClearBtn) {
  logoClearBtn.addEventListener('click', function () {
    setLogoUrl('');
    if (logoFileInput) logoFileInput.value = '';
  });
}

if (logoFileInput) {
  logoFileInput.addEventListener('change', function () {
    var file = logoFileInput.files && logoFileInput.files[0];
    if (!file) return;
    var planId = document.getElementById('plan-form-id') ? document.getElementById('plan-form-id').value : '';
    var fd = new FormData();
    fd.append('file', file);
    if (planId) fd.append('plan_id', planId);
    fetch('/api/admin/upload-plan-logo.php', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success && res.data && res.data.url) setLogoUrl(res.data.url);
        else alert(res.error || 'Logo upload failed');
      })
      .catch(function () { alert('Logo upload failed'); });
  });
}

function resetPlanForm() {
  document.getElementById('plan-form-id').value = '';
  document.getElementById('plan-form-name').value = '';
  document.getElementById('plan-form-description').value = '';
  if (document.getElementById('plan-form-type')) document.getElementById('plan-form-type').value = 'crypto';
  setLogoUrl('');
  if (logoFileInput) logoFileInput.value = '';
  document.getElementById('plan-form-min').value = '100';
  document.getElementById('plan-form-max').value = '';
  document.getElementById('plan-form-yield').value = '1';
  document.getElementById('plan-form-min-days').value = '7';
  document.getElementById('plan-form-features').value = '';
  document.querySelectorAll('.plan-form-risk').forEach(function (r) { r.checked = r.value === 'mid'; });
  document.getElementById('plan-drawer-title').textContent = 'Add New Plan';
  document.getElementById('plan-drawer-subtitle').textContent = '';
}

if (drawer) {
  var addPlanBtn = document.getElementById('add-plan-btn');
  if (addPlanBtn) addPlanBtn.addEventListener('click', function(){
    resetPlanForm();
    drawer.classList.remove('hidden');
  });
  var drawerOverlay = drawer.querySelector('.absolute.inset-0');
  if (drawerOverlay) drawerOverlay.addEventListener('click', function(){ drawer.classList.add('hidden'); });
  var drawerCloseBtn = document.getElementById('plan-drawer-close');
  if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', function(){ drawer.classList.add('hidden'); });
  var drawerXBtn = document.getElementById('plan-drawer-x');
  if (drawerXBtn) drawerXBtn.addEventListener('click', function(){ drawer.classList.add('hidden'); });
  document.querySelectorAll('.plan-edit-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = btn.getAttribute('data-plan-id');
      fetch('/api/admin/plans.php', { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(res){
        if (res.success && res.data) {
          var p = null;
          for (var i = 0; i < res.data.length; i++) {
            if (res.data[i] && res.data[i].id == id) { p = res.data[i]; break; }
          }
          if (p) {
            document.getElementById('plan-form-id').value = p.id;
            document.getElementById('plan-form-name').value = p.name;
            document.getElementById('plan-form-description').value = p.description || '';
            if (document.getElementById('plan-form-type')) document.getElementById('plan-form-type').value = p.plan_type || 'crypto';
            setLogoUrl(p.logo_url || '');
            if (logoFileInput) logoFileInput.value = '';
            document.getElementById('plan-form-min').value = p.min_deposit;
            document.getElementById('plan-form-max').value = p.max_deposit || '';
            document.getElementById('plan-form-yield').value = (p.yield_min !== null && p.yield_min !== undefined) ? p.yield_min : (p.yield || '');
            document.getElementById('plan-form-min-days').value = (p.min_duration_days !== null && p.min_duration_days !== undefined) ? p.min_duration_days : (p.min_duration_months ? (p.min_duration_months * 30) : '7');
            document.querySelectorAll('.plan-form-risk').forEach(function (r) { r.checked = r.value === (p.investment_risk || 'mid'); });
            document.getElementById('plan-form-features').value = (p.features || []).join('\n');
            document.getElementById('plan-drawer-title').textContent = 'Edit Plan: ' + p.name;
            document.getElementById('plan-drawer-subtitle').textContent = 'PLAN ID: ' + p.id;
            drawer.classList.remove('hidden');
          }
        }
      });
    });
  });

  document.querySelectorAll('.plan-delete-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = btn.getAttribute('data-plan-id');
      var name = btn.getAttribute('data-plan-name') || 'this plan';
      if (!id) return;
      if (!confirm('Delete ' + name + '?\\n\\nThis can only delete plans with NO investment history. Otherwise, disable the plan instead.')) return;
      fetch('/api/admin/plans.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ action: 'delete', id: parseInt(id, 10) })
      }).then(function(r){ return r.json(); }).then(function(res){
        if (res.success) window.location.reload();
        else alert(res.error || 'Failed to delete');
      }).catch(function(){ alert('Error'); });
    });
  });
  document.querySelectorAll('.plan-enabled-toggle').forEach(function(cb){
    cb.addEventListener('change', function(){
      if (cb.disabled) return;
      var id = cb.getAttribute('data-plan-id');
      var activeUsers = parseInt(cb.getAttribute('data-active-users'), 10) || 0;
      var enabled = cb.checked;
      if (!enabled && activeUsers > 0) { cb.checked = true; alert('Cannot disable: plan has ' + activeUsers + ' active user(s)'); return; }
      fetch('/api/admin/plans.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ id: id, enabled: enabled })
      }).then(function(r){ return r.json(); }).then(function(res){
        if (!res.success) { cb.checked = !enabled; alert(res.error || 'Failed'); }
        else window.location.reload();
      }).catch(function(){ cb.checked = !enabled; alert('Error'); });
    });
  });
  var adminPlanForm = document.getElementById('admin-plan-form');
  if (adminPlanForm) adminPlanForm.addEventListener('submit', function(e){
    e.preventDefault();
    var id = document.getElementById('plan-form-id').value;
    var featuresText = document.getElementById('plan-form-features').value || '';
    var features = featuresText.split('\n').map(function(s){ return s.trim(); }).filter(function(s){ return s.length > 0; });
    var minDays = parseInt(document.getElementById('plan-form-min-days').value, 10);
    var riskEl = document.querySelector('.plan-form-risk:checked');
    var data = { id: id ? parseInt(id) : 0, name: document.getElementById('plan-form-name').value, plan_type: document.getElementById('plan-form-type').value, description: document.getElementById('plan-form-description').value.trim(), logo_url: document.getElementById('plan-form-logo-url').value.trim(), investment_risk: riskEl ? riskEl.value : 'mid', min_deposit: parseFloat(document.getElementById('plan-form-min').value) || 0, max_deposit: document.getElementById('plan-form-max').value ? parseFloat(document.getElementById('plan-form-max').value) : null, yield: parseFloat(document.getElementById('plan-form-yield').value) || 0, min_duration_days: isNaN(minDays) ? null : minDays, features: features, features_text: featuresText };
    fetch('/api/admin/plans.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(data) })
      .then(function(r){ return r.json(); }).then(function(res){ if (res.success) { drawer.classList.add('hidden'); window.location.reload(); } else alert(res.error || 'Failed'); }).catch(function(){ alert('Error'); });
  });
  var planDrawerBackdrop = document.getElementById('plan-drawer-backdrop');
  if (planDrawerBackdrop) planDrawerBackdrop.addEventListener('click', function(){ drawer.classList.add('hidden'); });
  var globalSaveBtn = document.getElementById('global-settings-save');
  if (globalSaveBtn) globalSaveBtn.addEventListener('click', function(){
    var btn = this;
    btn.disabled = true;
    var data = {
      max_active_plans_per_user: document.getElementById('global-max-plans').value,
      compounding_enabled: document.getElementById('global-compounding').checked ? '1' : '0'
    };
    fetch('/api/admin/site-settings.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(data) })
      .then(function(r){ return r.json(); })
      .then(function(res){ if (res.success) alert('Settings updated'); else alert(res.error || 'Failed'); })
      .catch(function(){ alert('Error'); })
      .finally(function(){ btn.disabled = false; });
  });
}
})();
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>
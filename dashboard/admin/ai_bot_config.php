<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'ai';
$siteName = get_site_name();

$settings = [
    'min_withdrawal_limit' => get_site_setting('min_withdrawal_limit', '10'),
    'max_withdrawal_limit' => get_site_setting('max_withdrawal_limit', '50000'),
    'earnings_paused' => get_site_setting('earnings_paused', '0'),
    'distribution_interval' => get_site_setting('distribution_interval', 'daily'),
    'distribution_start_time' => get_site_setting('distribution_start_time', '09:00:00'),
];
$earningsPaused = $settings['earnings_paused'] === '1';
$earningsActive = !$earningsPaused;
$interval = $settings['distribution_interval'];
$showStartTime = in_array($interval, ['daily', 'weekly', 'monthly'], true);

$pageTitle = $siteName . ' Admin | AI Bot Config';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
$pageHeading = 'AI Bot Configuration';
$pageSubtitle = 'Central control panel for automated earnings distribution and global system rules.';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>

<?php if ($earningsPaused): ?>
<div class="mb-6 p-4 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 flex items-center gap-3">
<span class="material-icons-round">pause_circle</span>
<span class="font-semibold">Earnings distribution is paused. No credits are being made until you resume.</span>
</div>
<?php endif; ?>

<div class="space-y-8">
<!-- Global Withdrawal Limits -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2">
<span class="material-icons-round text-primary">account_balance</span>
Global Withdrawal Limits (USDT)
</h2>
<p class="text-sm text-slate-500 dark:text-zinc-400 mb-4">System-wide min and max limits. Displayed as USD equivalent.</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Minimum Withdrawal ($)</label>
<input id="ai-min-withdrawal" type="number" step="0.01" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($settings['min_withdrawal_limit']); ?>"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Maximum Withdrawal ($)</label>
<input id="ai-max-withdrawal" type="number" step="0.01" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($settings['max_withdrawal_limit']); ?>"/>
</div>
</div>
</section>

<!-- Pause / Resume -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2">
<span class="material-icons-round text-primary">play_circle</span>
Earnings Distribution
</h2>
<div class="flex items-center justify-between gap-4">
<div>
<p class="font-medium">Status</p>
<p class="text-sm text-slate-500">Green = active distribution. Grey = paused (no credits).</p>
</div>
<label class="relative inline-flex items-center cursor-pointer">
<input id="ai-earnings-active" type="checkbox" class="sr-only peer" <?php echo $earningsActive ? 'checked' : ''; ?>/>
<div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
<span id="ai-earnings-label" class="ml-3 text-sm font-medium <?php echo $earningsActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-zinc-300'; ?>"><?php echo $earningsActive ? 'Active' : 'Paused'; ?></span>
</label>
</div>
</section>

<!-- Distribution Interval -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2">
<span class="material-icons-round text-primary">schedule</span>
Distribution Interval
</h2>
<p class="text-sm text-slate-500 dark:text-zinc-400 mb-4">5 min and 12h credit continuously. Daily, Weekly, Monthly batch at the configured start time.</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Interval</label>
<select id="ai-distribution-interval" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary">
<option value="5min" <?php echo $interval === '5min' ? 'selected' : ''; ?>>5 minutes (continuous)</option>
<option value="12h" <?php echo $interval === '12h' ? 'selected' : ''; ?>>12 hours (continuous)</option>
<option value="daily" <?php echo $interval === 'daily' ? 'selected' : ''; ?>>Daily (batch)</option>
<option value="weekly" <?php echo $interval === 'weekly' ? 'selected' : ''; ?>>Weekly (batch)</option>
<option value="monthly" <?php echo $interval === 'monthly' ? 'selected' : ''; ?>>Monthly (batch)</option>
</select>
</div>
<div id="ai-start-time-wrap" class="<?php echo $showStartTime ? '' : 'hidden'; ?>">
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Distribution Start Time (UTC)</label>
<input id="ai-distribution-start-time" type="time" step="1" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars(substr($settings['distribution_start_time'], 0, 5)); ?>"/>
</div>
</div>
</section>

<!-- Save Settings -->
<div class="flex flex-wrap gap-4">
<button type="button" id="ai-save-settings" class="bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all flex items-center gap-2">
<span class="material-icons-round text-lg">save</span>
Save Settings
</button>
</div>

<!-- Manual Distribution -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6 mt-8">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2">
<span class="material-icons-round text-primary">play_arrow</span>
Manual Distribution
</h2>
<p class="text-sm text-slate-500 dark:text-zinc-400 mb-4">Immediately credit all eligible users. Ignores schedules and intervals. Uses same ROI logic, credits in USDT.</p>
<button type="button" id="ai-manual-distribute" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 transition-colors">
<span class="material-icons-round text-lg">send</span>
Run Manual Distribution
</button>
<div id="ai-manual-result" class="mt-4 hidden text-sm"></div>
</section>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>

<div id="ai-toast" class="fixed bottom-4 right-4 px-4 py-3 rounded-lg bg-slate-800 text-white text-sm font-medium hidden z-50"></div>

<script>
(function(){
  var intervalSel = document.getElementById('ai-distribution-interval');
  var startTimeWrap = document.getElementById('ai-start-time-wrap');
  function toggleStartTime() {
    var v = intervalSel.value;
    startTimeWrap.classList.toggle('hidden', !['daily','weekly','monthly'].includes(v));
  }
  intervalSel.addEventListener('change', toggleStartTime);

  document.getElementById('ai-save-settings').addEventListener('click', function(){
    var btn = this;
    btn.disabled = true;
    var timeInput = document.getElementById('ai-distribution-start-time');
    var timeVal = timeInput.value || '09:00';
    if (timeVal.length === 5) timeVal += ':00';
    var data = {
      min_withdrawal_limit: document.getElementById('ai-min-withdrawal').value,
      max_withdrawal_limit: document.getElementById('ai-max-withdrawal').value,
      earnings_paused: document.getElementById('ai-earnings-active').checked ? '0' : '1',
      distribution_interval: intervalSel.value,
      distribution_start_time: timeVal
    };
    fetch('/api/admin/ai-bot-config.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res.success) {
          var t = document.getElementById('ai-toast');
          t.textContent = res.data && res.data.message ? res.data.message : 'Settings saved';
          t.classList.remove('hidden');
          setTimeout(function(){ t.classList.add('hidden'); }, 2500);
          window.location.reload();
        } else alert(res.error || 'Failed');
      })
      .catch(function(){ alert('Error'); })
      .finally(function(){ btn.disabled = false; });
  });

  document.getElementById('ai-manual-distribute').addEventListener('click', function(){
    var btn = this;
    var resultEl = document.getElementById('ai-manual-result');
    if (!confirm('Run manual distribution now? This will credit all eligible investments immediately.')) return;
    btn.disabled = true;
    resultEl.classList.add('hidden');
    fetch('/api/admin/ai-bot-config.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'manual_distribute' }) })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res.success) {
          resultEl.textContent = res.data.message || 'Done.';
          resultEl.className = 'mt-4 text-sm text-emerald-600 dark:text-emerald-400';
          resultEl.classList.remove('hidden');
        } else {
          resultEl.textContent = res.error || 'Failed';
          resultEl.className = 'mt-4 text-sm text-red-600 dark:text-red-400';
          resultEl.classList.remove('hidden');
        }
      })
      .catch(function(){
        resultEl.textContent = 'Request failed';
        resultEl.className = 'mt-4 text-sm text-red-600';
        resultEl.classList.remove('hidden');
      })
      .finally(function(){ btn.disabled = false; });
  });
})();
</script>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>

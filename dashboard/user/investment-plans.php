<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/plan-types.php';
require_once __DIR__ . '/../../includes/usd-wallet.php';
$currentPage = 'investment-plans';
$siteName = get_site_name();
$planTypes = get_plan_types();

$plans = [];
$userBalance = 0;
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    ensure_plan_schema($pdo);
    $userId = $_SESSION['user_id'];
    $userBalance = get_user_usd_balance($pdo, (int) $userId);
    
    // Fetch enabled plans
    $stmt = $pdo->query('SELECT id, name, slug, plan_type, description, logo_url, investment_risk, min_deposit, max_deposit, yield_min, yield_max, duration_days, min_duration_days, max_duration_days, min_duration_months, max_duration_months, withdrawal_days, liquidation_cost, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $plans[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'plan_type' => normalize_plan_type($row['plan_type'] ?? 'crypto'),
            'description' => $row['description'] ?? '',
            'logo_url' => $row['logo_url'] ?? null,
            'investment_risk' => normalize_investment_risk($row['investment_risk'] ?? 'mid'),
            'min_deposit' => (float)$row['min_deposit'],
            'max_deposit' => $row['max_deposit'] !== null ? (float)$row['max_deposit'] : null,
            'yield_min' => (float)$row['yield_min'],
            'yield_max' => (float)$row['yield_max'],
            'duration_days' => (int)$row['duration_days'],
            'min_duration_days' => isset($row['min_duration_days']) && $row['min_duration_days'] !== null ? (int)$row['min_duration_days'] : (isset($row['min_duration_months']) && $row['min_duration_months'] !== null ? (int)$row['min_duration_months'] * 30 : (int)$row['duration_days']),
            'max_duration_days' => isset($row['max_duration_days']) && $row['max_duration_days'] !== null ? (int)$row['max_duration_days'] : (isset($row['max_duration_months']) && $row['max_duration_months'] !== null ? (int)$row['max_duration_months'] * 30 : (int)$row['duration_days']),
            'withdrawal_days' => (int)$row['withdrawal_days'],
            'liquidation_cost' => isset($row['liquidation_cost']) ? (float)$row['liquidation_cost'] : 0.0,
            'features' => $row['features_json'] ? json_decode($row['features_json'], true) : [],
        ];
    }
} catch (Throwable $e) { }

$plansByType = [];
foreach ($planTypes as $typeKey => $typeLabel) {
    $plansByType[$typeKey] = array_values(array_filter($plans, function ($plan) use ($typeKey) {
        return ($plan['plan_type'] ?? 'crypto') === $typeKey;
    }));
}
$activePlanTypes = [];
foreach ($planTypes as $typeKey => $typeLabel) {
    if (!empty($plansByType[$typeKey])) {
        $activePlanTypes[$typeKey] = $typeLabel;
    }
}
$defaultTab = array_key_first($activePlanTypes) ?: 'crypto';
$showPlanTabs = count($activePlanTypes) > 1;

$pageTitle = $siteName . ' | Investment Plans';
$pageHeading = 'Investments';
$pageSubtitle = 'Browse investment opportunities and invest using your available account balance.';
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
include __DIR__ . '/../../includes/dashboard/user-page-title.php';
?>

<style>
.plan-type-tab.is-active { color: #ffc35c; border-bottom-color: #ffc35c; font-weight: 700; }
.plan-type-panel { display: none; }
.plan-type-panel.is-active { display: grid; }
.plan-asset-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.plan-asset-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); }
.plan-type-tabs-nav {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  -ms-overflow-style: none;
  overscroll-behavior-x: contain;
}
.plan-type-tabs-nav::-webkit-scrollbar { display: none; }
.plan-type-tabs-track {
  display: inline-flex;
  gap: 1.5rem;
  min-width: 100%;
  width: max-content;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding-bottom: 0;
}
@media (min-width: 768px) {
  .plan-type-tabs-track { width: 100%; }
}
</style>

<section class="mb-8">
<div class="glass-panel rounded-xl p-4 md:p-6 flex flex-wrap justify-between items-center gap-4">
<div class="flex items-center gap-4 min-w-0">
<div class="w-12 h-12 rounded-full bg-primary-container/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary-container">account_balance_wallet</span>
</div>
<div>
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest">Available to Invest</p>
<h3 class="text-2xl md:text-3xl font-bold text-text-primary leading-none mt-1">USD <?php echo format_usd_amount($userBalance); ?></h3>
</div>
</div>
<div class="flex flex-col items-start md:items-end gap-2">
<p class="text-xs text-text-secondary flex items-center gap-1">
<span class="material-symbols-outlined text-sm">info</span>
Select a plan below to invest from your wallet balance.
</p>
<a href="/dashboard/user/wallet" class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary-container/90 text-on-primary px-4 py-2 rounded-lg font-label-sm text-label-sm transition-colors">
<span class="material-symbols-outlined text-sm">add</span> Add Funds
</a>
</div>
</div>
</section>

<?php if (empty($plans)): ?>
<div class="text-center py-12 glass-panel rounded-xl">
<p class="text-text-secondary">No investment plans available at the moment.</p>
</div>
<?php else: ?>
<?php if ($showPlanTabs): ?>
<nav class="plan-type-tabs-nav mb-8 -mx-4 px-4 md:mx-0 md:px-0 overflow-x-auto" aria-label="Investment plan categories">
<div class="plan-type-tabs-track">
<?php foreach ($activePlanTypes as $typeKey => $typeLabel): ?>
<button type="button" class="plan-type-tab shrink-0 pb-3 text-sm font-label-sm text-label-sm text-on-surface-variant hover:text-primary-container transition-colors border-b-2 border-transparent whitespace-nowrap<?php echo $typeKey === $defaultTab ? ' is-active' : ''; ?>" data-plan-tab="<?php echo htmlspecialchars($typeKey); ?>">
<?php echo htmlspecialchars($typeLabel); ?>
<span class="ml-1 text-[10px] opacity-60">(<?php echo count($plansByType[$typeKey]); ?>)</span>
</button>
<?php endforeach; ?>
</div>
</nav>
<?php endif; ?>

<?php foreach ($activePlanTypes as $typeKey => $typeLabel):
    $typePlans = $plansByType[$typeKey];
?>
<div class="plan-type-panel grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8<?php echo ($typeKey === $defaultTab || !$showPlanTabs) ? ' is-active' : ''; ?>" data-plan-panel="<?php echo htmlspecialchars($typeKey); ?>">
<?php foreach ($typePlans as $plan):
    $planDays = plan_duration_days($plan);
    $riskBadge = plan_investment_risk_badge($plan['investment_risk'] ?? 'mid');
    $periodReturn = format_plan_period_return($plan['yield_min'] ?? 0, $planDays);
?>
<div class="plan-asset-card glass-panel rounded-xl p-5 md:p-6 flex flex-col h-full">
<div class="flex justify-between items-start gap-3 mb-4">
<div class="flex items-center gap-3 min-w-0">
<?php echo plan_logo_markup($plan['logo_url'] ?? null, $plan['name'], 'w-10 h-10', 'text-sm'); ?>
<div class="min-w-0">
<h4 class="text-base md:text-lg font-bold text-text-primary leading-tight truncate"><?php echo htmlspecialchars($plan['name']); ?></h4>
<p class="text-xs text-text-secondary truncate"><?php echo htmlspecialchars($plan['description'] ?: 'Premium investment plan'); ?></p>
</div>
</div>
<span class="<?php echo $riskBadge['class']; ?> px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider shrink-0"><?php echo htmlspecialchars($riskBadge['label']); ?></span>
</div>
<div class="space-y-4 mb-6 flex-grow">
<div class="grid grid-cols-2 gap-4">
<div>
<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Expected Return</p>
<p class="text-base font-bold text-primary-container mt-1"><?php echo htmlspecialchars($periodReturn); ?></p>
</div>
<div>
<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Min. Investment</p>
<p class="text-base font-bold text-text-primary mt-1">USD <?php echo format_usd_amount($plan['min_deposit']); ?></p>
</div>
</div>
<div class="flex justify-between items-center text-sm border-t border-low pt-3">
<span class="text-text-secondary">Duration</span>
<span class="text-text-primary font-semibold"><?php echo (int) $planDays; ?> Days</span>
</div>
<?php if (!empty($plan['liquidation_cost']) && (float)$plan['liquidation_cost'] > 0): ?>
<div class="flex justify-between items-center text-sm">
<span class="text-text-secondary">Early Exit Fee</span>
<span class="text-amber-600 dark:text-amber-400 font-semibold">USD <?php echo format_usd_amount($plan['liquidation_cost']); ?></span>
</div>
<?php endif; ?>
</div>
<button type="button" data-plan-id="<?php echo $plan['id']; ?>" data-plan-name="<?php echo htmlspecialchars($plan['name']); ?>" data-plan-min="<?php echo $plan['min_deposit']; ?>" data-plan-max="<?php echo $plan['max_deposit'] ?? 0; ?>" data-plan-days="<?php echo (int) $planDays; ?>" data-plan-liquidation-fee="<?php echo htmlspecialchars(number_format((float)($plan['liquidation_cost'] ?? 0), 2, '.', ''), ENT_QUOTES, 'UTF-8'); ?>" class="subscribe-plan-btn w-full bg-primary-container hover:bg-primary-container/90 text-on-primary font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
<span>Invest Now</span>
<span class="material-symbols-outlined text-sm">trending_up</span>
</button>
</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Subscribe Modal -->
<div id="subscribe-modal" class="fixed inset-0 z-50 hidden">
<div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="subscribe-modal-backdrop"></div>
<div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-zinc-800">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-xl font-bold">Subscribe to <span id="modal-plan-name"></span></h2>
<button type="button" id="subscribe-modal-close" class="p-2 hover:bg-surface-container-high rounded-full"><span class="material-symbols-outlined">close</span></button>
</div>
<div class="p-6">
<form id="subscribe-form">
<input type="hidden" id="subscribe-plan-id" name="plan_id"/>
<div class="mb-4">
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Duration</label>
<p id="subscribe-duration-display" class="text-sm font-semibold text-slate-800 dark:text-slate-200">—</p>
<input type="hidden" id="subscribe-duration" name="duration_days"/>
</div>
<div class="mb-4">
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Investment Amount (USD)</label>
<input type="number" id="subscribe-amount" step="0.01" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" required/>
<p class="text-xs text-slate-500 mt-1">Available USD Balance: $<span id="available-balance"><?php echo format_usd_amount($userBalance); ?></span></p>
<p class="text-xs text-slate-500 mt-1">Range: $<span id="plan-min"></span> - <span id="plan-max"></span></p>
<p id="subscribe-liquidation-note" class="text-xs text-amber-600 dark:text-amber-400 mt-1 hidden">Early liquidation fee: $<span id="plan-liquidation-fee">0.00</span> (deducted from your USD balance).</p>
<?php if ($userBalance <= 0): ?><p class="text-xs text-amber-600 mt-1">Deposit funds to your wallet first.</p><?php endif; ?>
</div>
<div id="subscribe-error" class="text-sm text-red-500 hidden mb-4"></div>
<div class="flex gap-3">
<button type="button" id="subscribe-cancel-btn" class="flex-1 px-4 py-2 bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg">Cancel</button>
<button type="submit" id="subscribe-submit-btn" class="flex-1 px-4 py-2 bg-primary text-black font-bold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed" <?php echo $userBalance <= 0 ? 'disabled' : ''; ?>>Subscribe</button>
</div>
</form>
</div>
</div>
</div>
</div>

<!-- Success Modal (replaces browser alert) -->
<div id="subscribe-success-modal" class="fixed inset-0 z-[60] hidden">
<div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
<div class="absolute inset-0 flex items-center justify-center p-4">
<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-sm border border-emerald-200 dark:border-emerald-900/50 overflow-hidden">
<div class="p-8 text-center">
<div class="w-20 h-20 mx-auto mb-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
<span class="material-symbols-outlined text-success text-4xl">check_circle</span>
</div>
<h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Subscription Successful!</h3>
<p class="text-slate-600 dark:text-slate-400 text-sm mb-1">You've successfully subscribed to <strong class="text-primary" id="success-plan-name"></strong></p>
<p class="text-slate-500 dark:text-slate-500 text-xs">Redirecting to your dashboard…</p>
</div>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.plan-type-tab').forEach(function(tab) {
        tab.addEventListener('click', function () {
            var type = tab.getAttribute('data-plan-tab');
            document.querySelectorAll('.plan-type-tab').forEach(function (t) { t.classList.remove('is-active'); });
            document.querySelectorAll('.plan-type-panel').forEach(function (p) { p.classList.remove('is-active'); });
            tab.classList.add('is-active');
            var panel = document.querySelector('.plan-type-panel[data-plan-panel="' + type + '"]');
            if (panel) panel.classList.add('is-active');
        });
    });

    var modal = document.getElementById('subscribe-modal');
    var backdrop = document.getElementById('subscribe-modal-backdrop');
    var closeBtn = document.getElementById('subscribe-modal-close');
    var cancelBtn = document.getElementById('subscribe-cancel-btn');
    var subscribeBtns = document.querySelectorAll('.subscribe-plan-btn');
    var form = document.getElementById('subscribe-form');
    var planMinEl = document.getElementById('plan-min');
    var planMaxEl = document.getElementById('plan-max');
    var planNameEl = document.getElementById('modal-plan-name');
    var planIdEl = document.getElementById('subscribe-plan-id');
    var amountEl = document.getElementById('subscribe-amount');
    var errorEl = document.getElementById('subscribe-error');
    var availableBalance = parseFloat(document.getElementById('available-balance').textContent.replace(/,/g, '')) || 0;
    var currentPlanMin = 0;
    var currentPlanMax = 0;
    var durationInput = document.getElementById('subscribe-duration');
    var durationDisplay = document.getElementById('subscribe-duration-display');
    var currentPlanDays = 7;
    var liqNoteEl = document.getElementById('subscribe-liquidation-note');
    var liqFeeEl = document.getElementById('plan-liquidation-fee');

    function openModal(planId, planName, planMin, planMax, planDays, liquidationFee) {
        planIdEl.value = planId;
        planNameEl.textContent = planName;
        currentPlanMin = planMin;
        currentPlanMax = planMax;
        currentPlanDays = planDays || 7;
        planMinEl.textContent = '$' + planMin.toLocaleString();
        planMaxEl.textContent = planMax > 0 ? '$' + planMax.toLocaleString() : 'Unlimited';
        if (durationDisplay) durationDisplay.textContent = currentPlanDays + ' Days';
        if (durationInput) durationInput.value = currentPlanDays;
        if (liqFeeEl) liqFeeEl.textContent = (liquidationFee || 0).toFixed(2);
        if (liqNoteEl) {
            if (liquidationFee > 0) liqNoteEl.classList.remove('hidden');
            else liqNoteEl.classList.add('hidden');
        }
        amountEl.value = '';
        amountEl.min = planMin;
        amountEl.max = planMax > 0 ? planMax : '';
        errorEl.classList.add('hidden');
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    subscribeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var planId = this.getAttribute('data-plan-id');
            var planName = this.getAttribute('data-plan-name');
            var planMin = parseFloat(this.getAttribute('data-plan-min'));
            var planMax = parseFloat(this.getAttribute('data-plan-max')) || 0;
            var planDays = parseInt(this.getAttribute('data-plan-days'), 10) || 7;
            var liquidationFee = parseFloat(this.getAttribute('data-plan-liquidation-fee')) || 0;
            openModal(planId, planName, planMin, planMax, planDays, liquidationFee);
        });
    });
    
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var planId = parseInt(planIdEl.value, 10);
        var amount = parseFloat(amountEl.value) || 0;
        
        errorEl.classList.add('hidden');
        
        if (amount < currentPlanMin) {
            errorEl.textContent = 'Amount must be at least $' + currentPlanMin.toLocaleString();
            errorEl.classList.remove('hidden');
            return;
        }
        
        if (currentPlanMax > 0 && amount > currentPlanMax) {
            errorEl.textContent = 'Amount cannot exceed $' + currentPlanMax.toLocaleString();
            errorEl.classList.remove('hidden');
            return;
        }
        
        var duration = parseInt(durationInput ? durationInput.value : 0, 10) || currentPlanDays;
        if (duration !== currentPlanDays) {
            errorEl.textContent = 'This plan has a fixed duration of ' + currentPlanDays + ' days';
            errorEl.classList.remove('hidden');
            return;
        }
        
        if (amount > availableBalance) {
            errorEl.textContent = 'Insufficient USD balance. Available: $' + availableBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            errorEl.classList.remove('hidden');
            return;
        }
        
        fetch('/api/user/subscribe-plan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ plan_id: planId, amount: amount, duration_days: duration })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                modal.classList.add('hidden');
                var successModal = document.getElementById('subscribe-success-modal');
                var successPlanEl = document.getElementById('success-plan-name');
                if (successPlanEl) successPlanEl.textContent = planNameEl.textContent || 'the plan';
                if (successModal) successModal.classList.remove('hidden');
                setTimeout(function(){ window.location.href = '/dashboard/user/dashboard'; }, 2200);
            } else {
                errorEl.textContent = res.error || 'Failed to subscribe';
                errorEl.classList.remove('hidden');
            }
        }).catch(function(){ errorEl.textContent = 'Request failed'; errorEl.classList.remove('hidden'); });
    });
});
</script>
</body></html>

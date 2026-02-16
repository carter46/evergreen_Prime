<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'investment-plans';
$siteName = get_site_name();

$plans = [];
$userBalance = 0;
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    
    // Fetch enabled plans
    $stmt = $pdo->query('SELECT id, name, slug, description, min_deposit, max_deposit, yield_min, yield_max, duration_days, withdrawal_days, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $plans[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'description' => $row['description'] ?? '',
            'min_deposit' => (float)$row['min_deposit'],
            'max_deposit' => $row['max_deposit'] !== null ? (float)$row['max_deposit'] : null,
            'yield_min' => (float)$row['yield_min'],
            'yield_max' => (float)$row['yield_max'],
            'duration_days' => (int)$row['duration_days'],
            'withdrawal_days' => (int)$row['withdrawal_days'],
            'features' => $row['features_json'] ? json_decode($row['features_json'], true) : [],
        ];
    }
    
    // Calculate user balance
    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $amt = (float)$row['amount'];
        if (in_array(strtoupper($row['currency']), ['USDT','USDC','USD','BUSD'], true)) $userBalance += $amt;
        elseif (strtoupper($row['currency']) === 'BTC') $userBalance += $amt * 65000;
        elseif (strtoupper($row['currency']) === 'ETH') $userBalance += $amt * 3500;
        else $userBalance += $amt;
    }
} catch (Throwable $e) { }
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Investment Plans</title>
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
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/user-sidebar.php'; ?>
<main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8">
<?php include __DIR__ . '/../../includes/dashboard/user-header.php'; ?>
<div class="mb-6">
<h1 class="text-2xl sm:text-3xl font-bold">Investment Plans</h1>
<p class="text-slate-500 mt-1">Choose an investment plan that fits your goals</p>
</div>

<!-- Plans Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php foreach ($plans as $plan): ?>
<div class="bg-white dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-lg transition-all">
<div class="flex items-center gap-4 mb-4">
<div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
<span class="material-icons-round text-primary text-2xl">auto_graph</span>
</div>
<div>
<h3 class="text-lg font-bold"><?php echo htmlspecialchars($plan['name']); ?></h3>
<p class="text-xs text-slate-500"><?php echo htmlspecialchars($plan['description'] ?: 'Premium investment plan'); ?></p>
</div>
</div>

<div class="space-y-3 mb-6">
<div class="flex justify-between items-center">
<span class="text-sm text-slate-500">Deposit Range</span>
<span class="text-sm font-bold">$<?php echo number_format($plan['min_deposit'], 0); ?> - <?php echo $plan['max_deposit'] ? '$' . number_format($plan['max_deposit'], 0) : 'Unlimited'; ?></span>
</div>
<div class="flex justify-between items-center">
<span class="text-sm text-slate-500">Yield Range</span>
<span class="text-sm font-bold text-emerald-500"><?php echo number_format($plan['yield_min'], 1); ?>% - <?php echo number_format($plan['yield_max'], 1); ?>%</span>
</div>
<div class="flex justify-between items-center">
<span class="text-sm text-slate-500">Duration</span>
<span class="text-sm font-bold"><?php echo $plan['duration_days']; ?> days</span>
</div>
<div class="flex justify-between items-center">
<span class="text-sm text-slate-500">Withdrawal</span>
<span class="text-sm font-bold"><?php echo $plan['withdrawal_days'] === 0 ? 'Instant' : 'Every ' . $plan['withdrawal_days'] . ' days'; ?></span>
</div>
</div>

<?php if (!empty($plan['features'])): ?>
<div class="mb-6">
<p class="text-xs font-bold text-slate-500 uppercase mb-2">Features</p>
<ul class="space-y-1">
<?php foreach (array_slice($plan['features'], 0, 4) as $feature): ?>
<li class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-2">
<span class="material-icons text-primary text-sm">check_circle</span>
<?php echo htmlspecialchars($feature); ?>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<button type="button" data-plan-id="<?php echo $plan['id']; ?>" data-plan-name="<?php echo htmlspecialchars($plan['name']); ?>" data-plan-min="<?php echo $plan['min_deposit']; ?>" data-plan-max="<?php echo $plan['max_deposit'] ?? 0; ?>" class="subscribe-plan-btn w-full bg-primary hover:bg-primary/90 text-black font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary/20">
Subscribe Now
</button>
</div>
<?php endforeach; ?>
</div>

<?php if (empty($plans)): ?>
<div class="text-center py-12">
<p class="text-slate-500">No investment plans available at the moment.</p>
</div>
<?php endif; ?>

<!-- Subscribe Modal -->
<div id="subscribe-modal" class="fixed inset-0 z-50 hidden">
<div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="subscribe-modal-backdrop"></div>
<div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-zinc-800">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between">
<h2 class="text-xl font-bold">Subscribe to <span id="modal-plan-name"></span></h2>
<button type="button" id="subscribe-modal-close" class="p-2 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-full"><span class="material-icons-round">close</span></button>
</div>
<div class="p-6">
<form id="subscribe-form">
<input type="hidden" id="subscribe-plan-id" name="plan_id"/>
<div class="mb-4">
<label class="block text-xs font-bold text-slate-400 uppercase mb-2">Investment Amount (USD)</label>
<input type="number" id="subscribe-amount" step="0.01" min="0" class="w-full bg-slate-50 dark:bg-zinc-800 rounded-lg px-3 py-2 text-sm border border-slate-200 dark:border-zinc-700" required/>
<p class="text-xs text-slate-500 mt-1">Available Balance: $<span id="available-balance"><?php echo number_format($userBalance, 2); ?></span></p>
<p class="text-xs text-slate-500 mt-1">Range: $<span id="plan-min"></span> - <span id="plan-max"></span></p>
</div>
<div id="subscribe-error" class="text-sm text-red-500 hidden mb-4"></div>
<div class="flex gap-3">
<button type="button" id="subscribe-cancel-btn" class="flex-1 px-4 py-2 bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-slate-300 font-bold rounded-lg">Cancel</button>
<button type="submit" class="flex-1 px-4 py-2 bg-primary text-black font-bold rounded-lg">Subscribe</button>
</div>
</form>
</div>
</div>
</div>
</div>

</main>
</div>
<script src="/js/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    function openModal(planId, planName, planMin, planMax) {
        planIdEl.value = planId;
        planNameEl.textContent = planName;
        currentPlanMin = planMin;
        currentPlanMax = planMax;
        planMinEl.textContent = '$' + planMin.toLocaleString();
        planMaxEl.textContent = planMax > 0 ? '$' + planMax.toLocaleString() : 'Unlimited';
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
            openModal(planId, planName, planMin, planMax);
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
        
        if (amount > availableBalance) {
            errorEl.textContent = 'Insufficient balance. Available: $' + availableBalance.toLocaleString();
            errorEl.classList.remove('hidden');
            return;
        }
        
        fetch('/api/user/subscribe-plan.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ plan_id: planId, amount: amount })
        }).then(function(r){ return r.json(); }).then(function(res){
            if (res.success) {
                alert('Successfully subscribed to ' + planNameEl.textContent);
                window.location.href = '/dashboard/user/dashboard';
            } else {
                errorEl.textContent = res.error || 'Failed to subscribe';
                errorEl.classList.remove('hidden');
            }
        }).catch(function(){ errorEl.textContent = 'Request failed'; errorEl.classList.remove('hidden'); });
    });
});
</script>
</body></html>

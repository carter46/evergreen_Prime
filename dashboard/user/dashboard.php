<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/plan-types.php';
require_once __DIR__ . '/../../includes/usd-wallet.php';
$currentPage = 'dashboard';
$siteName = get_site_name();
$userBalance = 0;
$userBalanceUpdatedAt = null;
$totalProfit = 0;
$activeCapital = 0;
$dailyEarning = 0;
$referralBonus = 0;
$referralBonusLast24h = 0;
$activeInvestments = [];
$chartData = [];
$period = $_GET['period'] ?? '1M';
$plansByTypeForTrades = [];
$activePlanTypesForTrades = [];
$defaultTradeTab = 'crypto';
$showTradeTabs = false;
$planTypes = get_plan_types();
$days = match($period) {
    '1D' => 1,
    '1W' => 7,
    '1M' => 30,
    '1Y' => 365,
    default => 30
};
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    $userBalance = get_user_usd_balance($pdo, (int) $userId);
    try {
        $bc = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd_updated_at'");
        if ($bc && $bc->rowCount() > 0) {
            $s = $pdo->prepare('SELECT last_balance_usd_updated_at FROM users WHERE id = ?');
            $s->execute([(int) $userId]);
            $userBalanceUpdatedAt = $s->fetchColumn() ?: null;
        }
    } catch (Throwable $e) {}

    ensure_plan_schema($pdo);
    $stmtPlans = $pdo->query('SELECT name, plan_type FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
    while ($row = $stmtPlans->fetch(PDO::FETCH_ASSOC)) {
        $typeKey = normalize_plan_type($row['plan_type'] ?? 'crypto');
        if (!isset($plansByTypeForTrades[$typeKey])) {
            $plansByTypeForTrades[$typeKey] = [];
        }
        $plansByTypeForTrades[$typeKey][] = $row['name'];
    }
    foreach ($planTypes as $typeKey => $typeLabel) {
        if (!empty($plansByTypeForTrades[$typeKey])) {
            $activePlanTypesForTrades[$typeKey] = $typeLabel;
        }
    }
    $defaultTradeTab = array_key_first($activePlanTypesForTrades) ?: 'crypto';
    $showTradeTabs = count($activePlanTypesForTrades) > 1;

    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM user_investments WHERE user_id = ? AND status = 'active'");
    $r->execute([$userId]); $activeCapital = (float)$r->fetchColumn();
    $totalProfit = get_user_total_profit($pdo, (int) $userId);
    $stmt = $pdo->prepare('SELECT ui.id, ui.plan_id, ui.amount, ui.start_date, ui.status, ui.duration_days as investment_duration_days, p.name as plan_name, p.yield_min, p.yield_max, p.duration_days as plan_duration_days FROM user_investments ui JOIN plans p ON p.id = ui.plan_id WHERE ui.user_id = ? AND ui.status = ? ORDER BY ui.created_at DESC LIMIT 5');
    $stmt->execute([$userId, 'active']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $activeInvestments[] = $row;
        $yieldMin = (float)($row['yield_min'] ?? 0);
        $yieldMax = (float)($row['yield_max'] ?? 0);
        $avgYield = ($yieldMin + $yieldMax) / 2;
        if ($avgYield <= 0) $avgYield = $yieldMin;
        $dailyEarning += (float)$row['amount'] * ($avgYield / 100);
    }
    $referralBonus = get_user_total_referral_bonus($pdo, (int) $userId);
    $referralBonusLast24h = get_user_total_referral_bonus($pdo, (int) $userId, null, 24);
    // activeInvestments and dailyEarning already populated above
    // Fetch transaction data for chart based on selected period
    $stmt = $pdo->prepare("SELECT DATE(created_at) as date, type, SUM(amount) as total FROM transactions WHERE user_id = ? AND type IN ('deposit', 'withdrawal') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at), type ORDER BY date ASC");
    $stmt->execute([$userId, $days]);
    $dailyData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['date'];
        if (!isset($dailyData[$date])) $dailyData[$date] = ['deposit' => 0, 'withdrawal' => 0];
        $dailyData[$date][$row['type']] = (float)$row['total'];
    }
    // Build cumulative chart data
    $cumulative = 0;
    foreach ($dailyData as $date => $amounts) {
        $cumulative += $amounts['deposit'] - $amounts['withdrawal'];
        $chartData[] = ['date' => $date, 'value' => $cumulative];
    }
} catch (Throwable $e) { }
$profileUser = get_current_user_data() ?? [];
$dashboardUserName = $profileUser['name'] ?? 'User';
$pageTitle = $siteName . ' | Dashboard';
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$growthPct = $userBalance > 0 ? min(99.9, ($totalProfit / max(1, $userBalance)) * 100) : 0;
$activePlanCount = count($activeInvestments);
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
?>
<style>
.dash-trade-tab.is-active { color: #185e08; border-bottom-color: #185e08; font-weight: 700; }
</style>
<?php
$chartBtnActive = 'px-3 py-1 font-label-md text-label-md bg-white shadow-sm rounded text-primary font-bold';
$chartBtnIdle = 'px-3 py-1 font-label-md text-label-md hover:bg-white/50 rounded transition-colors text-on-surface-variant';
?>
<section class="mb-xl">
<h2 class="font-hanken font-bold dash-greeting text-primary"><?php echo $greeting; ?>, <?php echo htmlspecialchars($dashboardUserName); ?>.</h2>
<p class="text-sm leading-snug sm:font-body-lg sm:text-body-lg sm:leading-normal text-on-surface-variant mt-1">Welcome back to your institutional trading hub.</p>
</section>
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md">
<div class="dash-card-balance-green bento-card p-md flex flex-col justify-between hover:active-glow transition-all rounded">
<div>
<span class="font-label-md text-label-md dash-card-label uppercase tracking-wider">Total USD Balance</span>
<h3 class="font-hanken font-extrabold text-headline-md dash-card-value mt-base">$<?php echo number_format((float) $userBalance, 0, '.', ','); ?></h3>
</div>
<div class="flex gap-xs mt-md">
<button type="button" id="deposit-btn-dash" class="flex-1 dash-btn-solid text-[12px] font-bold py-2 rounded-lg transition-transform active:scale-95">Deposit</button>
<a href="/dashboard/user/transactions" class="flex-1 dash-btn-outline border text-[12px] font-bold py-2 rounded-lg transition-colors text-center">Transactions</a>
</div>
</div>
<div class="dash-card-light-green bento-card p-md flex flex-col justify-between hover:active-glow transition-all rounded">
<div>
<span class="font-label-md text-label-md text-on-surface-variant">Total Profit</span>
<h3 class="font-hanken font-extrabold text-headline-md text-fidelity-green mt-base">+$<?php echo format_usd_amount($totalProfit); ?></h3>
</div>
<?php if ($growthPct > 0): ?>
<div class="flex items-center gap-xs mt-md text-fidelity-green bg-fidelity-green/5 p-2 rounded">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">trending_up</span>
<span class="font-label-md text-[11px] font-bold">+<?php echo number_format($growthPct, 1); ?>% Overall Growth</span>
</div>
<?php endif; ?>
</div>
<div class="dash-card-light-green bento-card p-md flex flex-col justify-between hover:active-glow transition-all rounded">
<div class="space-y-md">
<div>
<span class="font-label-md text-label-md text-on-surface-variant">Active Capital</span>
<p class="font-hanken font-bold text-headline-md <?php echo $activeCapital > 0 ? 'text-on-surface' : 'opacity-40'; ?>">$<?php echo format_usd_amount($activeCapital); ?></p>
</div>
<div class="pt-2 border-t border-surface-gray">
<span class="font-label-md text-label-md text-on-surface-variant">Daily Earning</span>
<p class="font-hanken font-bold text-headline-md <?php echo $dailyEarning > 0 ? 'text-on-surface' : 'opacity-40'; ?>">$<?php echo format_usd_amount($dailyEarning); ?></p>
</div>
</div>
</div>
<div class="dash-card-referral-dark bento-card p-md flex flex-col justify-between hover:active-glow transition-all rounded">
<div>
<span class="font-label-md text-label-md dash-card-label uppercase tracking-wider">Referral Bonus</span>
<h3 class="font-hanken font-extrabold text-headline-md dash-card-value mt-base">$<?php echo format_usd_amount($referralBonus); ?></h3>
<p class="font-label-md text-[11px] dash-card-muted mt-1">Last 24h: <span class="dash-card-accent">+$<?php echo format_usd_amount($referralBonusLast24h); ?></span></p>
</div>
<a class="inline-flex items-center gap-1 font-label-md dash-card-link font-bold mt-md hover:underline" href="/dashboard/user/referrals">
View Network Details
<span class="material-symbols-outlined text-[14px]">arrow_forward</span>
</a>
</div>
</section>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg">
<div class="lg:col-span-2 space-y-md">
<div class="bg-surface-container-lowest border border-surface-gray p-md relative overflow-hidden h-[450px] flex flex-col rounded">
<div class="flex justify-between items-center mb-lg relative z-10 flex-wrap gap-3">
<div>
<h4 class="font-hanken font-bold text-headline-md text-on-surface">Market Engine Yield Analysis</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Growth visualization for institutional nodes</p>
</div>
<div class="flex bg-surface-container rounded-lg p-1 gap-1">
<button type="button" data-period="1D" class="chart-filter-btn <?php echo $period === '1D' ? $chartBtnActive : $chartBtnIdle; ?>">1D</button>
<button type="button" data-period="1W" class="chart-filter-btn <?php echo $period === '1W' ? $chartBtnActive : $chartBtnIdle; ?>">1W</button>
<button type="button" data-period="1M" class="chart-filter-btn <?php echo $period === '1M' ? $chartBtnActive : $chartBtnIdle; ?>">1M</button>
<button type="button" data-period="1Y" class="chart-filter-btn <?php echo $period === '1Y' ? $chartBtnActive : $chartBtnIdle; ?>">1Y</button>
</div>
</div>
<div class="flex-1 relative flex flex-col min-h-0" id="performance-chart-wrapper">
<div class="flex-1 relative min-h-0" id="performance-chart">
<?php
$dates = [];
if (!empty($chartData)) {
    $maxVal = max(array_column($chartData, 'value'));
    $minVal = min(array_column($chartData, 'value'));
    $range = $maxVal - $minVal;
    if ($range == 0) $range = 1;
    $points = [];
    $count = count($chartData);
    foreach ($chartData as $i => $point) {
        $x = $count > 1 ? ($i / ($count - 1)) * 100 : 50;
        $y = 100 - (($point['value'] - $minVal) / $range) * 80;
        $points[] = $x . ',' . $y;
        if ($i === 0 || $i === floor($count / 4) || $i === floor($count / 2) || $i === floor($count * 3 / 4) || $i === $count - 1) {
            $dates[] = date('M j', strtotime($point['date']));
        }
    }
    $pathD = 'M' . implode(' L', $points);
    $areaD = $pathD . ' L' . ($count > 1 ? 100 : 50) . ',100 L0,100 Z';
?>
<div class="absolute inset-0 trading-graph-bg rounded-lg"></div>
<svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
<defs>
<linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%">
<stop offset="0%" style="stop-color:#337722;stop-opacity:0.2"></stop>
<stop offset="100%" style="stop-color:#337722;stop-opacity:0"></stop>
</linearGradient>
</defs>
<path d="<?php echo htmlspecialchars($areaD); ?>" fill="url(#chartGradient)"></path>
<path d="<?php echo htmlspecialchars($pathD); ?>" fill="none" stroke="#337722" stroke-width="2"></path>
</svg>
<?php } else { ?>
<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">No data available</div>
<?php } ?>
</div>
<div class="flex justify-between mt-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-widest px-1" id="chart-dates">
<?php if (!empty($chartData) && isset($dates)) { foreach ($dates as $d): ?><span><?php echo htmlspecialchars($d); ?></span><?php endforeach; } ?>
</div>
</div>
<div class="absolute inset-0 opacity-5 pointer-events-none rounded" style="background-image: radial-gradient(#185e08 1px, transparent 1px); background-size: 20px 20px;"></div>
</div>
<div class="grid grid-cols-2 gap-md">
<div class="dash-card-light-green border border-surface-gray p-sm rounded-lg dash-insight-tile">
<div class="w-12 h-12 bg-white rounded flex items-center justify-center shadow-sm shrink-0">
<span class="material-symbols-outlined text-fidelity-green">query_stats</span>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface-variant">Market Volatility Index</p>
<p class="font-hanken font-bold text-body-lg text-on-surface">Low Risk Profile</p>
</div>
</div>
<div class="dash-card-light-green border border-surface-gray p-sm rounded-lg dash-insight-tile">
<div class="w-12 h-12 bg-white rounded flex items-center justify-center shadow-sm shrink-0">
<span class="material-symbols-outlined text-institutional-blue">security</span>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface-variant">Cold Wallet Status</p>
<p class="font-hanken font-bold text-body-lg text-on-surface">99.8% Segregated</p>
</div>
</div>
</div>
</div>
<div class="space-y-lg">
<div class="bg-surface-container-lowest border border-surface-gray p-md rounded">
<div class="flex justify-between items-center mb-md">
<h4 class="font-hanken font-bold text-body-lg text-on-surface">Live Market Trades</h4>
<span class="w-2 h-2 bg-error rounded-full animate-ping"></span>
</div>
<?php if (!empty($showTradeTabs)): ?>
<nav class="flex gap-3 overflow-x-auto dash-trade-tabs mb-sm" aria-label="Plan categories">
<?php foreach ($activePlanTypesForTrades as $typeKey => $typeLabel): ?>
<button type="button" class="dash-trade-tab shrink-0 pb-2 text-[11px] font-bold uppercase tracking-wide text-on-surface-variant border-b-2 border-transparent whitespace-nowrap<?php echo $typeKey === $defaultTradeTab ? ' is-active' : ''; ?>" data-trade-tab="<?php echo htmlspecialchars($typeKey); ?>"><?php echo htmlspecialchars($typeLabel); ?></button>
<?php endforeach; ?>
</nav>
<?php endif; ?>
<div class="space-y-sm" id="live-trades-panel">
<?php
$initialTradePlans = $plansByTypeForTrades[$defaultTradeTab] ?? ['Basic', 'Standard', 'Premium'];
$tradeSamples = array_slice($initialTradePlans, 0, 3);
foreach ($tradeSamples as $ti => $planName):
    $isLong = ($ti % 2 === 0);
    $tradeMins = max(1, ($ti + 1) * 3 + ($ti * 2));
    $tradeAmountVal = max(0, (($ti + 1) * 47.5) + fmod(crc32($planName . (string) $ti), 200));
    $tradeAmountStr = ($isLong ? '+' : '-') . '$' . number_format($tradeAmountVal, 2);
    $color = ['orange', 'yellow', 'blue'][$ti] ?? 'gray';
?>
<div class="live-trade-card flex items-center justify-between p-sm border-b border-surface-gray/50 hover:bg-surface-container-low transition-colors group">
<div class="flex items-center gap-sm min-w-0">
<div class="trade-icon-container w-8 h-8 rounded-full bg-<?php echo $color; ?>-500/10 flex items-center justify-center shrink-0">
<span class="trade-icon material-symbols-outlined text-<?php echo $color; ?>-600 text-[14px]"><?php echo $isLong ? 'trending_up' : 'trending_down'; ?></span>
</div>
<div class="min-w-0">
<p class="trade-pair font-label-md text-[12px] font-bold text-on-surface truncate"><?php echo htmlspecialchars($planName); ?></p>
<span class="trade-time text-[10px] <?php echo $isLong ? 'text-fidelity-green' : 'text-error'; ?> font-medium uppercase tracking-tight"><?php echo $isLong ? 'Long Position' : 'Short Position'; ?> &middot; <?php echo (int) $tradeMins; ?>m ago</span>
</div>
</div>
<p class="live-trade-amount font-hanken font-bold <?php echo $isLong ? 'text-fidelity-green' : 'text-on-surface-variant'; ?> text-body-md shrink-0"><?php echo $tradeAmountStr; ?></p>
</div>
<?php endforeach; ?>
</div>
<a href="/dashboard/user/transactions" class="block w-full mt-md text-center font-label-md text-on-surface-variant hover:text-primary transition-colors py-2 border border-dashed border-surface-gray rounded">
View Historical Nodes
</a>
</div>
<div class="bg-surface-container-lowest border border-surface-gray p-md min-h-[250px] flex flex-col rounded">
<h4 class="font-hanken font-bold text-body-lg text-on-surface mb-md">My Active Plans</h4>
<?php if (empty($activeInvestments)): ?>
<div class="flex-1 flex flex-col items-center justify-center text-center p-md">
<div class="w-16 h-16 bg-surface-container-high rounded-full flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-on-surface-variant opacity-40 text-4xl">inventory_2</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">No active investments found on AI Core.</p>
</div>
<a href="/dashboard/user/investment-plans" class="w-full mt-md bg-fidelity-green text-white font-bold py-sm rounded-lg shadow-md hover:translate-y-[-2px] transition-all duration-200 text-center block">
Subscribe to New Investment Plan
</a>
<?php else: ?>
<div class="flex-1 space-y-3 overflow-y-auto dash-scrollbar">
<?php foreach ($activeInvestments as $inv):
    $startDate = new DateTime($inv['start_date']);
    $now = new DateTime();
    $daysElapsed = $now->diff($startDate)->days;
    $durationDays = (int)($inv['investment_duration_days'] ?? $inv['plan_duration_days'] ?? 30);
    $progress = min(100, ($daysElapsed / max(1, $durationDays)) * 100);
    $avgYield = (($inv['yield_min'] ?? 0) + ($inv['yield_max'] ?? 0)) / 2;
?>
<div class="p-sm rounded border border-surface-gray hover:border-fidelity-green/30 transition-all">
<p class="font-bold text-on-surface text-sm truncate"><?php echo htmlspecialchars($inv['plan_name']); ?></p>
<p class="text-xs text-on-surface-variant mb-2">$<?php echo format_usd_amount($inv['amount']); ?> &middot; +<?php echo number_format($avgYield, 1); ?>% ROI</p>
<div class="w-full bg-surface-container-high h-1 rounded-full overflow-hidden">
<div class="bg-fidelity-green h-full rounded-full" style="width:<?php echo min(100, $progress); ?>%"></div>
</div>
</div>
<?php endforeach; ?>
</div>
<a href="/dashboard/user/investment-plans" class="w-full mt-md bg-fidelity-green text-white font-bold py-sm rounded-lg text-center block hover:opacity-90 transition-opacity text-sm">
Subscribe to New Investment Plan
</a>
<?php endif; ?>
</div>
</div>
</div>
<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>window.BLOOMBIT_API_BASE = '';</script>
<script src="/js/crypto-config.js"></script>
<script src="/js/crypto-prices.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.BloombitCryptoPrices) {
        window.BloombitCryptoPrices.init(['bitcoin'], {
            refreshInterval: 300000
        });
    }
    
    // Live AI Trades — plan names by category
    var tradePlansByType = <?php echo json_encode($plansByTypeForTrades, JSON_UNESCAPED_UNICODE); ?>;
    var tradeTypeLabels = <?php echo json_encode($activePlanTypesForTrades, JSON_UNESCAPED_UNICODE); ?>;
    var activeTradeTab = <?php echo json_encode($defaultTradeTab); ?>;

    function getTradePlanNames() {
        var names = tradePlansByType[activeTradeTab] || [];
        if (!names.length) {
            names = ['Basic Plan', 'Growth Plan', 'Premium Plan'];
        }
        return names;
    }

    document.querySelectorAll('.dash-trade-tab').forEach(function(tab) {
        tab.addEventListener('click', function () {
            activeTradeTab = tab.getAttribute('data-trade-tab');
            document.querySelectorAll('.dash-trade-tab').forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            document.querySelectorAll('.live-trade-card').forEach(function (card) { updateTrade(card); });
        });
    });

    var tradeElements = document.querySelectorAll('.live-trade-amount');
    var directions = ['Long', 'Short'];
    
    function animateTradeAmount(el) {
        var current = parseFloat(el.textContent.replace(/[^0-9.-]/g, '')) || 0;
        var isPositive = (el.textContent || '').indexOf('+') !== -1;
        var change = (Math.random() * 200 - 100);
        var newVal = Math.max(0, current + change);
        var absVal = Math.abs(newVal);
        
        var colorClass = '';
        if (absVal < 50) colorClass = 'text-error';
        else if (absVal >= 100) colorClass = 'text-fidelity-green';
        else colorClass = isPositive ? 'text-fidelity-green' : 'text-error';
        
        el.className = 'live-trade-amount font-hanken font-bold shrink-0 text-body-md ' + colorClass;
        el.textContent = (newVal >= 0 ? '+' : '-') + '$' + absVal.toFixed(2);
    }
    
    function updateTrade(el) {
        if (!el) return;
        var pairEl = el.querySelector('.trade-pair');
        var timeEl = el.querySelector('.trade-time');
        var iconEl = el.querySelector('.trade-icon');
        var amountEl = el.querySelector('.live-trade-amount');
        var iconContainer = el.querySelector('.trade-icon-container');
        if (!pairEl || !timeEl || !iconEl || !iconContainer) return;
        
        var planNames = getTradePlanNames();
        var planName = planNames[Math.floor(Math.random() * planNames.length)];
        var direction = directions[Math.floor(Math.random() * directions.length)];
        var isLong = direction === 'Long';
        var mins = Math.floor(Math.random() * 30) + 1;
        
        pairEl.innerHTML = planName + ' <span class="' + (isLong ? 'text-fidelity-green' : 'text-error') + ' text-[10px] ml-1 uppercase">' + direction + ' Position</span>';
        timeEl.textContent = mins + ' min' + (mins > 1 ? 's' : '') + ' ago';
        
        if (isLong) {
            iconContainer.className = 'trade-icon-container w-8 h-8 rounded-full bg-fidelity-green/10 flex items-center justify-center shrink-0';
            iconEl.className = 'trade-icon material-symbols-outlined text-fidelity-green text-[14px]';
            iconEl.textContent = 'trending_up';
        } else {
            iconContainer.className = 'trade-icon-container w-8 h-8 rounded-full bg-error/10 flex items-center justify-center shrink-0';
            iconEl.className = 'trade-icon material-symbols-outlined text-error text-[14px]';
            iconEl.textContent = 'trending_down';
        }
        
        if (amountEl) animateTradeAmount(amountEl);
    }
    
    var tradeCards = document.querySelectorAll('.live-trade-card');
    tradeCards.forEach(function(card, i) {
        // GTranslate can rewrite DOM nodes; re-query by index each tick to avoid stale references.
        setInterval(function () {
            var cards = document.querySelectorAll('.live-trade-card');
            var c = cards && cards.length > i ? cards[i] : null;
            if (!c) return;
            var amountEl = c.querySelector('.live-trade-amount');
            if (amountEl) animateTradeAmount(amountEl);
        }, 3000 + (i * 500));

        setInterval(function () {
            var cards = document.querySelectorAll('.live-trade-card');
            var c = cards && cards.length > i ? cards[i] : null;
            if (!c) return;
            updateTrade(c);
        }, 8000 + (i * 1000));
    });
    
    // Chart filter buttons - AJAX
    var chartContainer = document.getElementById('performance-chart');
    var chartDates = document.getElementById('chart-dates');
    var currentPeriod = '<?php echo htmlspecialchars($period); ?>';
    
    function updateChart(data) {
        if (!chartContainer) return;
        if (!data || data.length === 0) {
            chartContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">No data available</div>';
            if (chartDates) chartDates.innerHTML = '';
            return;
        }
        var maxVal = Math.max.apply(null, data.map(function(d){ return d.value; }));
        var minVal = Math.min.apply(null, data.map(function(d){ return d.value; }));
        var range = maxVal - minVal;
        if (range === 0) range = 1;
        var count = data.length;
        var points = [];
        var dates = [];
        data.forEach(function(point, i) {
            var x = count > 1 ? (i / (count - 1)) * 100 : 50;
            var y = 100 - ((point.value - minVal) / range) * 80;
            points.push(x + ',' + y);
            if (i === 0 || i === Math.floor(count / 4) || i === Math.floor(count / 2) || i === Math.floor(count * 3 / 4) || i === count - 1) {
                dates.push(new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            }
        });
        var pathD = 'M' + points.join(' L');
        var areaD = pathD + ' L' + (count > 1 ? 100 : 50) + ',100 L0,100 Z';
        chartContainer.innerHTML = '<div class="absolute inset-0 trading-graph-bg rounded-lg"></div><svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100"><defs><linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%"><stop offset="0%" style="stop-color:#337722;stop-opacity:0.2"></stop><stop offset="100%" style="stop-color:#337722;stop-opacity:0"></stop></linearGradient></defs><path d="' + areaD + '" fill="url(#chartGradient)"></path><path d="' + pathD + '" fill="none" stroke="#337722" stroke-width="2"></path></svg>';
        if (chartDates) chartDates.innerHTML = dates.map(function(d){ return '<span>' + d + '</span>'; }).join('');
    }
    
    var chartActive = ['bg-white', 'text-primary', 'font-bold', 'shadow-sm'];
    var chartIdle = ['text-on-surface-variant'];
    function setChartBtnActive(btn) {
        document.querySelectorAll('.chart-filter-btn').forEach(function (b) {
            b.classList.remove('bg-white', 'text-primary', 'font-bold', 'shadow-sm', 'text-on-surface-variant', 'hover:bg-white/50');
            b.classList.add('text-on-surface-variant', 'hover:bg-white/50');
        });
        btn.classList.remove('text-on-surface-variant', 'hover:bg-white/50');
        chartActive.forEach(function (c) { btn.classList.add(c); });
    }
    
    document.querySelectorAll('.chart-filter-btn').forEach(function(btn) {
        var p = btn.getAttribute('data-period');
        if (p === currentPeriod) setChartBtnActive(btn);
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            setChartBtnActive(this);
            var period = this.getAttribute('data-period');
            fetch('/api/user/chart-data.php?period=' + period, { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(res){
                if (res.success && res.data) updateChart(res.data);
            }).catch(function(){ if (chartContainer) chartContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">Failed to load chart</div>'; });
        });
    });
    
    // Deposit button - redirect to wallet
    var depositBtnDash = document.getElementById('deposit-btn-dash');
    if (depositBtnDash) {
        depositBtnDash.addEventListener('click', function() {
            window.location.href = '/dashboard/user/wallet?action=deposit';
        });
    }
});
</script>
</body></html>
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
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
?>
<style>
.dash-trade-tab.is-active { color: #ffc35c; border-bottom-color: #ffc35c; }
</style>
<?php
$chartBtnActive = 'px-4 py-1.5 rounded text-label-xs bg-surface-dim text-primary-container font-bold shadow-sm';
$chartBtnIdle = 'px-4 py-1.5 rounded text-label-xs text-on-surface-variant hover:text-on-surface transition-colors';
?>
<header class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-8 md:mb-10">
<div>
<h2 class="font-headline-lg text-headline-lg text-text-primary mb-1">Good morning, <?php echo htmlspecialchars($dashboardUserName); ?></h2>
<p class="text-text-secondary font-body-md">Welcome back to your institutional trading hub.</p>
</div>
<div class="flex items-center gap-3 bg-surface-container-high/50 border border-low px-4 py-2 rounded-full w-fit">
<div class="w-2 h-2 bg-success rounded-full animate-pulse shadow-[0_0_8px_rgba(32,178,108,0.6)]"></div>
<span class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest">AI Core Online</span>
</div>
</header>
<section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
<div class="balance-gradient-card p-6 md:p-8 rounded-xl relative overflow-hidden group text-white shadow-2xl">
<div class="absolute top-0 right-0 w-48 h-48 bg-primary-container/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
<div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
<span class="material-symbols-outlined text-5xl md:text-6xl">account_balance_wallet</span>
</div>
<div class="relative z-10">
<p class="font-label-xs text-label-xs text-slate-400 uppercase tracking-widest mb-3">Total USD Balance</p>
<div class="flex flex-wrap items-baseline gap-2 md:gap-3 mb-4">
<span class="font-display text-4xl md:text-[48px] text-primary-container font-extrabold leading-none">$<?php echo format_usd_amount($userBalance); ?></span>
</div>
<div class="flex gap-3">
<button type="button" id="deposit-btn-dash" class="flex-1 bg-primary-container text-on-primary font-bold py-3 rounded-lg hover:opacity-90 transition-all text-label-sm">Deposit</button>
<a href="/dashboard/user/transactions" class="flex-1 border border-white/15 text-white font-bold py-3 rounded-lg hover:bg-white/5 transition-all text-label-sm text-center">Transactions</a>
</div>
</div>
</div>
<div class="glass-panel p-6 md:p-8 rounded-xl flex flex-col justify-between">
<div class="space-y-6">
<div class="flex justify-between items-start">
<div>
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest mb-1">Total Profit</p>
<h3 class="font-headline-md text-headline-md text-success">$<?php echo format_usd_amount($totalProfit); ?></h3>
</div>
<div class="p-2 bg-success/10 rounded">
<span class="material-symbols-outlined text-success" style="font-variation-settings: 'FILL' 1;">trending_up</span>
</div>
</div>
<div class="grid grid-cols-2 gap-4 pt-4 border-t border-low">
<div>
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest mb-1">Active Capital</p>
<h4 class="font-headline-md text-[20px] text-on-surface">$<?php echo format_usd_amount($activeCapital); ?></h4>
</div>
<div>
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest mb-1">Daily Earning</p>
<h4 class="font-headline-md text-[20px] text-on-surface">$<?php echo format_usd_amount($dailyEarning); ?></h4>
</div>
</div>
</div>
</div>
<div class="glass-panel p-6 md:p-8 rounded-xl flex flex-col justify-between relative overflow-hidden">
<div class="absolute -bottom-6 -right-6 opacity-5 pointer-events-none">
<span class="material-symbols-outlined text-[100px] md:text-[120px]">diversity_3</span>
</div>
<div class="relative z-10">
<div class="flex justify-between items-start mb-4">
<p class="font-label-xs text-label-xs text-on-surface-variant uppercase tracking-widest">Referral Bonus</p>
<span class="material-symbols-outlined text-primary-container">info</span>
</div>
<h3 class="font-display text-4xl md:text-[48px] text-on-surface font-extrabold mb-1">$<?php echo format_usd_amount($referralBonus); ?></h3>
<p class="text-on-surface-variant font-label-sm">Last 24h: <span class="text-success">+$<?php echo format_usd_amount($referralBonusLast24h); ?></span></p>
</div>
<a class="mt-6 flex items-center gap-2 text-primary-container font-bold text-label-sm hover:underline relative z-10" href="/dashboard/user/referrals">
View Network Details <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
</div>
</section>
<section class="grid grid-cols-1 lg:grid-cols-12 gap-gutter mb-gutter">
<div class="lg:col-span-8 glass-panel p-6 md:p-8 rounded-xl">
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 md:mb-8">
<div>
<h3 class="font-headline-md text-headline-md text-on-surface">Portfolio Growth</h3>
<p class="text-on-surface-variant text-label-sm">AI Engine Yield Analysis</p>
</div>
<div class="flex gap-1 bg-surface-container-high p-1 rounded-lg w-fit">
<button type="button" data-period="1D" class="chart-filter-btn <?php echo $period === '1D' ? $chartBtnActive : $chartBtnIdle; ?>">1D</button>
<button type="button" data-period="1W" class="chart-filter-btn <?php echo $period === '1W' ? $chartBtnActive : $chartBtnIdle; ?>">1W</button>
<button type="button" data-period="1M" class="chart-filter-btn <?php echo $period === '1M' ? $chartBtnActive : $chartBtnIdle; ?>">1M</button>
<button type="button" data-period="1Y" class="chart-filter-btn <?php echo $period === '1Y' ? $chartBtnActive : $chartBtnIdle; ?>">1Y</button>
</div>
</div>
<div class="h-48 md:h-64 relative flex flex-col" id="performance-chart-wrapper">
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
<stop offset="0%" style="stop-color:#ffc35c;stop-opacity:0.2"></stop>
<stop offset="100%" style="stop-color:#ffc35c;stop-opacity:0"></stop>
</linearGradient>
</defs>
<path d="<?php echo htmlspecialchars($areaD); ?>" fill="url(#chartGradient)"></path>
<path d="<?php echo htmlspecialchars($pathD); ?>" fill="none" stroke="#ffc35c" stroke-width="2"></path>
</svg>
<?php } else { ?>
<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">No data available</div>
<?php } ?>
</div>
<div class="flex justify-between mt-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-widest" id="chart-dates">
<?php if (!empty($chartData) && isset($dates)) { foreach ($dates as $d): ?><span><?php echo htmlspecialchars($d); ?></span><?php endforeach; } ?>
</div>
</div>
</div>
<div class="lg:col-span-4 glass-panel flex flex-col rounded-xl overflow-hidden min-h-[320px]">
<div class="p-5 md:p-6 border-b border-low bg-surface-container-high/30">
<div class="flex justify-between items-center gap-2 mb-3">
<h3 class="font-headline-md text-[18px] text-on-surface">Live AI Trades</h3>
<div class="flex items-center gap-2 px-2 py-1 bg-primary-container/10 rounded-full border border-primary-container/20 shrink-0">
<span class="w-1.5 h-1.5 bg-primary-container rounded-full animate-ping"></span>
<span class="text-[10px] font-bold text-primary-container tracking-tighter uppercase">Scanning...</span>
</div>
</div>
<?php if (!empty($showTradeTabs)): ?>
<nav class="flex gap-3 overflow-x-auto dash-trade-tabs" aria-label="Plan categories">
<?php foreach ($activePlanTypesForTrades as $typeKey => $typeLabel): ?>
<button type="button" class="dash-trade-tab shrink-0 pb-2 text-[11px] font-bold uppercase tracking-wide text-on-surface-variant border-b-2 border-transparent whitespace-nowrap<?php echo $typeKey === $defaultTradeTab ? ' is-active' : ''; ?>" data-trade-tab="<?php echo htmlspecialchars($typeKey); ?>"><?php echo htmlspecialchars($typeLabel); ?></button>
<?php endforeach; ?>
</nav>
<?php endif; ?>
</div>
<div class="flex-1 overflow-y-auto dash-scrollbar" id="live-trades-panel">
<?php
$initialTradePlans = $plansByTypeForTrades[$defaultTradeTab] ?? ['Basic', 'Standard', 'Premium'];
$tradeSamples = array_slice($initialTradePlans, 0, 3);
$tradeAmounts = ['+$245.00', '-$12.40', '+$89.15'];
$tradeDirs = ['Long', 'Short', 'Long'];
$tradeMins = [2, 8, 15];
foreach ($tradeSamples as $ti => $planName):
    $isLong = ($tradeDirs[$ti] ?? 'Long') === 'Long';
?>
<div class="live-trade-card p-5 flex items-center justify-between border-b border-low/50 hover:bg-white/[0.02] transition-colors">
<div class="flex items-center gap-4 min-w-0">
<div class="trade-icon-container w-10 h-10 rounded-full <?php echo $isLong ? 'bg-success/10' : 'bg-critical/10'; ?> flex items-center justify-center shrink-0">
<span class="trade-icon material-symbols-outlined <?php echo $isLong ? 'text-success' : 'text-critical'; ?>"><?php echo $isLong ? 'trending_up' : 'trending_down'; ?></span>
</div>
<div class="min-w-0">
<h4 class="trade-pair font-bold text-on-surface text-label-sm truncate"><?php echo htmlspecialchars($planName); ?> <span class="<?php echo $isLong ? 'text-success' : 'text-critical'; ?> text-[10px] ml-1 uppercase"><?php echo $tradeDirs[$ti]; ?></span></h4>
<p class="trade-time text-[10px] text-on-surface-variant"><?php echo (int) $tradeMins[$ti]; ?> mins ago</p>
</div>
</div>
<span class="live-trade-amount font-data-mono <?php echo $isLong ? 'text-success' : 'text-critical'; ?> font-bold shrink-0"><?php echo $tradeAmounts[$ti]; ?></span>
</div>
<?php endforeach; ?>
<div class="p-6 flex flex-col items-center justify-center text-center opacity-40">
<div class="w-full h-1 bg-surface-container-high rounded-full overflow-hidden mb-4">
<div class="scanning-animation h-full w-full"></div>
</div>
<p class="text-[10px] font-bold uppercase tracking-widest">Monitoring <?php echo htmlspecialchars($activePlanTypesForTrades[$defaultTradeTab] ?? 'Markets'); ?> Plans</p>
</div>
</div>
</div>
</section>
<section class="glass-panel rounded-xl overflow-hidden">
<div class="px-6 md:px-8 py-5 md:py-6 border-b border-low flex justify-between items-center gap-3">
<h3 class="font-headline-md text-headline-md text-on-surface">My Active Plans</h3>
<a class="text-primary-container text-label-sm font-bold hover:underline flex items-center gap-1 shrink-0" href="/dashboard/user/analytics">
View All Plans <span class="material-symbols-outlined text-sm">open_in_new</span>
</a>
</div>
<?php if (empty($activeInvestments)): ?>
<div class="p-10 md:p-16 flex flex-col items-center justify-center text-center">
<div class="w-20 h-20 md:w-24 md:h-24 mb-6 rounded-full bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-4xl text-on-surface-variant">inventory_2</span>
</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-2">No active investments</h4>
<p class="text-on-surface-variant font-body-md max-w-md mb-8">You currently don't have any running investment plans. Start earning passive yield with our proprietary AI trading algorithms.</p>
<a href="/dashboard/user/investment-plans" class="bg-primary-container text-on-primary px-6 md:px-8 py-3 md:py-4 rounded-lg font-bold text-label-sm shadow-xl shadow-primary-container/20 hover:scale-[1.02] transition-transform flex items-center gap-3">
<span class="material-symbols-outlined">rocket_launch</span>
Subscribe to New Investment Plan
</a>
</div>
<?php else: ?>
<div class="p-4 md:p-6 space-y-3">
<?php foreach ($activeInvestments as $inv):
    $startDate = new DateTime($inv['start_date']);
    $now = new DateTime();
    $daysElapsed = $now->diff($startDate)->days;
    $durationDays = (int)($inv['investment_duration_days'] ?? $inv['plan_duration_days'] ?? 30);
    $progress = min(100, ($daysElapsed / max(1, $durationDays)) * 100);
    $avgYield = (($inv['yield_min'] ?? 0) + ($inv['yield_max'] ?? 0)) / 2;
?>
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 rounded-xl border border-low hover:border-primary-container/30 transition-all">
<div class="flex items-center gap-4 min-w-0">
<div class="w-12 h-12 bg-primary-container/15 rounded-xl flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary-container">monitoring</span>
</div>
<div class="min-w-0">
<h4 class="font-bold text-on-surface truncate"><?php echo htmlspecialchars($inv['plan_name']); ?></h4>
<p class="text-xs text-on-surface-variant">Start Date: <?php echo date('M j, Y', strtotime($inv['start_date'])); ?></p>
</div>
</div>
<div class="lg:w-48 px-0 lg:px-4">
<div class="flex justify-between text-[10px] font-bold mb-1 text-on-surface-variant">
<span>PROGRESS</span>
<span><?php echo number_format($progress, 0); ?>%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-primary-container h-full rounded-full" style="width:<?php echo min(100, $progress); ?>%"></div>
</div>
</div>
<div class="text-left lg:text-right">
<p class="text-sm font-bold text-on-surface">$<?php echo format_usd_amount($inv['amount']); ?></p>
<p class="text-xs text-success">+<?php echo number_format($avgYield, 1); ?>% ROI</p>
</div>
</div>
<?php endforeach; ?>
<a href="/dashboard/user/investment-plans" class="block w-full py-4 border border-dashed border-low rounded-xl text-on-surface-variant font-bold hover:border-primary-container hover:text-primary-container transition-all text-center text-label-sm">
+ Subscribe to New Investment Plan
</a>
</div>
<?php endif; ?>
</section>
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
        if (absVal < 50) colorClass = 'text-critical';
        else if (absVal >= 100) colorClass = 'text-success';
        else colorClass = isPositive ? 'text-success' : 'text-critical';
        
        el.className = 'live-trade-amount font-data-mono font-bold shrink-0 ' + colorClass;
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
        
        pairEl.innerHTML = planName + ' <span class="' + (isLong ? 'text-success' : 'text-critical') + ' text-[10px] ml-1 uppercase">' + direction + '</span>';
        timeEl.textContent = mins + ' min' + (mins > 1 ? 's' : '') + ' ago';
        
        if (isLong) {
            iconContainer.className = 'trade-icon-container w-10 h-10 rounded-full bg-success/10 flex items-center justify-center shrink-0';
            iconEl.className = 'trade-icon material-symbols-outlined text-success';
            iconEl.textContent = 'trending_up';
        } else {
            iconContainer.className = 'trade-icon-container w-10 h-10 rounded-full bg-critical/10 flex items-center justify-center shrink-0';
            iconEl.className = 'trade-icon material-symbols-outlined text-critical';
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
            chartContainer.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">No data available</div>';
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
        chartContainer.innerHTML = '<div class="absolute inset-0 trading-graph-bg rounded-lg"></div><svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100"><defs><linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%"><stop offset="0%" style="stop-color:#ffc35c;stop-opacity:0.2"></stop><stop offset="100%" style="stop-color:#ffc35c;stop-opacity:0"></stop></linearGradient></defs><path d="' + areaD + '" fill="url(#chartGradient)"></path><path d="' + pathD + '" fill="none" stroke="#ffc35c" stroke-width="2"></path></svg>';
        if (chartDates) chartDates.innerHTML = dates.map(function(d){ return '<span>' + d + '</span>'; }).join('');
    }
    
    var chartActive = ['bg-surface-dim', 'text-primary-container', 'font-bold', 'shadow-sm'];
    var chartIdle = ['text-on-surface-variant'];
    function setChartBtnActive(btn) {
        document.querySelectorAll('.chart-filter-btn').forEach(function (b) {
            b.classList.remove('bg-surface-dim', 'text-primary-container', 'font-bold', 'shadow-sm', 'text-on-surface-variant');
            b.classList.add('text-on-surface-variant');
        });
        btn.classList.remove('text-on-surface-variant');
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
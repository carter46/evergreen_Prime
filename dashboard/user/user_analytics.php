<?php
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/usd-wallet.php';
require_once __DIR__ . '/../../includes/investment-lifecycle.php';
$currentPage = 'analytics';
$siteName = get_site_name();
$totalProfit = 0;
$analyticsTx = [];
$dailyAvgReturn = 0;
$activeCapital = 0;
$estMonthlyEarnings = 0;
$chartData = [];
$winningStreakDays = 0;
$personalBestStreakDays = 0;
$maxDrawdownPct = null;
$payoutByCurrency = [];
$payoutTotalForBreakdown = 0.0;
$activePlans = [];
$maturedPlans = [];
$liquidatedPlans = [];
$userUsdBalance = 0.0;
$period = $_GET['period'] ?? '1M';
$days = match($period) {
    '1D' => 1,
    '1W' => 7,
    '1M' => 30,
    '1Y' => 365,
    'ALL' => 9999,
    default => 30
};
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $userId = $_SESSION['user_id'];
    ensure_investment_lifecycle_schema($pdo);
    process_user_due_maturities($pdo, (int) $userId);
    $userUsdBalance = get_user_usd_balance($pdo, (int) $userId);
    
    // Filter transactions based on period
    if ($period === 'ALL') {
        $totalProfit = get_user_total_profit($pdo, (int) $userId);
        $chartExclude = portfolio_chart_reference_exclude_sql();
        $chartStmt = $pdo->prepare("SELECT DATE(created_at) as date, type, SUM(COALESCE(amount_usd, amount)) as total FROM transactions WHERE user_id = ? AND status = 'completed' AND type IN ('deposit', 'withdrawal', 'payout', 'profit_adjustment'){$chartExclude} GROUP BY DATE(created_at), type ORDER BY date ASC");
        $chartStmt->execute([$userId]);
    } else {
        $totalProfit = get_user_total_profit($pdo, (int) $userId, $days);
        $chartExclude = portfolio_chart_reference_exclude_sql();
        $chartStmt = $pdo->prepare("SELECT DATE(created_at) as date, type, SUM(COALESCE(amount_usd, amount)) as total FROM transactions WHERE user_id = ? AND status = 'completed' AND type IN ('deposit', 'withdrawal', 'payout', 'profit_adjustment') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY){$chartExclude} GROUP BY DATE(created_at), type ORDER BY date ASC");
        $chartStmt->execute([$userId, $days]);
    }
    
    // Active capital (sum of active investments) - not filtered by period
    $r = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM user_investments WHERE user_id = ? AND status IN ('active', 'paused')");
    $r->execute([$userId]);
    $activeCapital = (float)$r->fetchColumn();
    
    // Expected daily from per-plan yields (same formula as dashboard): sum of amount * (avgYield/100) per active investment
    $expectedDaily = 0.0;
    $expStmt = $pdo->prepare('SELECT ui.amount, p.yield_min, p.yield_max FROM user_investments ui JOIN plans p ON p.id = ui.plan_id WHERE ui.user_id = ? AND ui.status IN (?, ?)');
    $expStmt->execute([$userId, 'active', 'paused']);
    while ($row = $expStmt->fetch(PDO::FETCH_ASSOC)) {
        $yieldMin = (float)($row['yield_min'] ?? 0);
        $yieldMax = (float)($row['yield_max'] ?? 0);
        $avgYield = ($yieldMin + $yieldMax) / 2;
        if ($avgYield <= 0) $avgYield = $yieldMin;
        $expectedDaily += (float)$row['amount'] * ($avgYield / 100);
    }
    $dailyAvgReturn = $expectedDaily;
    $estDailyEarnings = $expectedDaily;
    $estMonthlyEarnings = $expectedDaily * 30;
    
    // Chart data
    $dailyData = [];
    while ($row = $chartStmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['date'];
        if (!isset($dailyData[$date])) $dailyData[$date] = ['deposit' => 0, 'withdrawal' => 0, 'payout' => 0];
        $txType = $row['type'] === 'profit_adjustment' ? 'payout' : $row['type'];
        if (isset($dailyData[$date][$txType])) {
            $dailyData[$date][$txType] += (float)$row['total'];
        }
    }
    $cumulative = 0;
    foreach ($dailyData as $date => $amounts) {
        $cumulative += $amounts['deposit'] - $amounts['withdrawal'] + $amounts['payout'];
        $chartData[] = ['date' => $date, 'value' => $cumulative];
    }
    
    $activePlans = fetch_portfolio_active_investments($pdo, (int) $userId);
    $maturedPlans = fetch_portfolio_investments($pdo, (int) $userId, 'completed');
    $liquidatedPlans = fetch_portfolio_investments($pdo, (int) $userId, 'liquidated');

    // Fetch all transactions for table (limit 50)
    $txStmt = $pdo->prepare('SELECT type, amount, currency, status, created_at, reference FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 50');
    $txStmt->execute([$userId]);
    $analyticsTx = [];
    while ($row = $txStmt->fetch(PDO::FETCH_ASSOC)) $analyticsTx[] = $row;

    $payoutInvPlan = [];
    $payoutInvRoi = [];
    $invMetaStmt = $pdo->prepare(
        'SELECT ui.id, p.name, p.yield_min, p.yield_max
         FROM user_investments ui
         JOIN plans p ON p.id = ui.plan_id
         WHERE ui.user_id = ?'
    );
    $invMetaStmt->execute([$userId]);
    while ($invRow = $invMetaStmt->fetch(PDO::FETCH_ASSOC)) {
        $invId = (int) ($invRow['id'] ?? 0);
        if ($invId <= 0) {
            continue;
        }
        $yieldMin = (float) ($invRow['yield_min'] ?? 0);
        $yieldMax = (float) ($invRow['yield_max'] ?? 0);
        $avgYield = ($yieldMin + $yieldMax) / 2;
        if ($avgYield <= 0) {
            $avgYield = $yieldMin;
        }
        $payoutInvPlan[$invId] = (string) ($invRow['name'] ?? 'Investment Plan');
        $payoutInvRoi[$invId] = $avgYield;
    }

    // Winning streak & personal best: based on days with payout credits (completed)
    $payoutDaysStmt = $pdo->prepare("
        SELECT DATE(created_at) AS d
        FROM transactions
        WHERE user_id = ? AND type = 'payout' AND status = 'completed'
        GROUP BY DATE(created_at)
        ORDER BY d DESC
        LIMIT 400
    ");
    $payoutDaysStmt->execute([$userId]);
    $payoutDays = [];
    while ($r = $payoutDaysStmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($r['d'])) $payoutDays[] = $r['d'];
    }
    if (!empty($payoutDays)) {
        // Current streak from most recent payout day
        $prev = null;
        foreach ($payoutDays as $idx => $d) {
            $ts = strtotime($d);
            if ($idx === 0) {
                $winningStreakDays = 1;
                $prev = $ts;
                continue;
            }
            if (($prev - $ts) === 86400) {
                $winningStreakDays++;
                $prev = $ts;
            } else {
                break;
            }
        }
        // Personal best streak (max consecutive payout days)
        $best = 1;
        $run = 1;
        for ($i = 1; $i < count($payoutDays); $i++) {
            $a = strtotime($payoutDays[$i - 1]);
            $b = strtotime($payoutDays[$i]);
            if (($a - $b) === 86400) {
                $run++;
                if ($run > $best) $best = $run;
            } else {
                $run = 1;
            }
        }
        $personalBestStreakDays = $best;
    }

    // Max drawdown from cumulative chart values (best-effort metric)
    if (!empty($chartData)) {
        $peak = null;
        $maxDd = 0.0;
        foreach ($chartData as $pt) {
            $v = (float) ($pt['value'] ?? 0);
            if ($peak === null || $v > $peak) $peak = $v;
            if ($peak !== null && $peak > 0) {
                $dd = ($peak - $v) / $peak;
                if ($dd > $maxDd) $maxDd = $dd;
            }
        }
        $maxDrawdownPct = $maxDd * 100.0;
    }

    // Earnings breakdown by payout currency (for the selected period)
    if ($period === 'ALL') {
        $brStmt = $pdo->prepare("
            SELECT currency, SUM(COALESCE(amount_usd, amount)) AS total
            FROM transactions
            WHERE user_id = ? AND type IN ('payout', 'profit_adjustment') AND status = 'completed'
            GROUP BY currency
            ORDER BY total DESC
        ");
        $brStmt->execute([$userId]);
    } else {
        $brStmt = $pdo->prepare("
            SELECT currency, SUM(COALESCE(amount_usd, amount)) AS total
            FROM transactions
            WHERE user_id = ? AND type IN ('payout', 'profit_adjustment') AND status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY currency
            ORDER BY total DESC
        ");
        $brStmt->execute([$userId, $days]);
    }
    while ($r = $brStmt->fetch(PDO::FETCH_ASSOC)) {
        $cur = strtoupper(trim($r['currency'] ?? ''));
        $tot = (float) ($r['total'] ?? 0);
        if ($cur === '' || abs($tot) < 0.000001) continue;
        $payoutByCurrency[$cur] = $tot;
        $payoutTotalForBreakdown += $tot;
    }
} catch (Throwable $e) { }

$profitGrowthPct = ($userUsdBalance + $activeCapital) > 0
    ? min(99.9, ($totalProfit / max(1, $userUsdBalance + $activeCapital)) * 100)
    : 0;
$dailyYieldPct = $activeCapital > 0 ? min(99.9, ($dailyAvgReturn / $activeCapital) * 100) : 0;

function portfolio_plan_card(array $ap, string $tab, float $userUsdBalance): void {
    $startDate = $ap['start_date'] ?? $ap['created_at'] ?? null;
    $durationDays = (int) ($ap['investment_duration_days'] ?? $ap['plan_duration_days'] ?? 0);
    $endTs = $startDate ? strtotime($startDate . ' + ' . $durationDays . ' days') : null;
    $daysLeft = $endTs ? max(0, (int) ceil(($endTs - time()) / 86400)) : 0;
    $yieldMin = (float) ($ap['yield_min'] ?? 0);
    $yieldMax = (float) ($ap['yield_max'] ?? 0);
    $amount = (float) ($ap['amount'] ?? 0);
    $liqFee = (float) ($ap['liquidation_cost'] ?? 0);
    $invId = (int) ($ap['id'] ?? 0);
    $invStatus = strtolower($ap['status'] ?? 'active');
    $isPaused = $invStatus === 'paused';
    ?>
    <div class="p-4 rounded-xl bg-surface-container-low border border-surface-gray">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <span class="font-bold text-base truncate text-on-surface"><?= htmlspecialchars($ap['plan_name'] ?? 'Plan') ?></span>
        <?php if ($tab === 'active'): ?>
            <?php if ($isPaused): ?>
            <span class="px-2 py-1 bg-surface-container-high text-on-surface-variant text-xs font-bold rounded-full shrink-0">Paused</span>
            <?php else: ?>
            <span class="px-2 py-1 bg-fidelity-green/10 text-fidelity-green text-xs font-bold rounded-full shrink-0">Active</span>
            <?php endif; ?>
        <?php elseif ($tab === 'matured'): ?>
            <span class="px-2 py-1 bg-institutional-blue/10 text-institutional-blue text-xs font-bold rounded-full shrink-0">Matured</span>
        <?php else: ?>
            <span class="px-2 py-1 bg-error/10 text-error text-xs font-bold rounded-full shrink-0">Liquidated</span>
        <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm mb-3">
            <div><span class="text-on-surface-variant block text-xs">Amount</span><span class="font-bold text-on-surface">$<?= format_usd_amount($amount) ?></span></div>
            <div><span class="text-on-surface-variant block text-xs">Yield</span><span class="font-bold text-fidelity-green"><?= number_format($yieldMin, 1) ?>–<?= number_format($yieldMax, 1) ?>%</span></div>
            <div><span class="text-on-surface-variant block text-xs"><?= $tab === 'active' ? 'Days left' : 'Duration' ?></span><span class="font-bold text-on-surface"><?= $tab === 'active' ? (int) $daysLeft : $durationDays . ' days' ?></span></div>
        </div>
        <?php if ($tab === 'active'): ?>
        <button type="button"
            class="liquidate-plan-btn w-full py-2 rounded-lg border border-error/30 text-error text-sm font-semibold hover:bg-error/5 transition-colors"
            data-investment-id="<?= $invId ?>"
            data-plan-name="<?= htmlspecialchars($ap['plan_name'] ?? 'Plan', ENT_QUOTES, 'UTF-8') ?>"
            data-amount="<?= htmlspecialchars(number_format($amount, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
            data-fee="<?= htmlspecialchars(number_format($liqFee, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
            data-balance="<?= htmlspecialchars(number_format($userUsdBalance, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>">
            Liquidate Plan
        </button>
        <?php elseif ($tab === 'matured'): ?>
        <p class="text-xs text-on-surface-variant">Principal returned to your USD wallet at maturity.</p>
        <?php else: ?>
        <p class="text-xs text-on-surface-variant">Early exit — operation fee deducted from your balance.</p>
        <?php endif; ?>
    </div>
    <?php
}

$pageTitle = $siteName . ' | My Portfolio';
$pageHeading = 'My Portfolio';
$pageSubtitle = 'Track active, matured, and liquidated investments alongside cumulative performance.';
require_once __DIR__ . '/../../includes/dashboard/user-layout-start.php';
include __DIR__ . '/../../includes/dashboard/user-page-title.php';
?>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E9E9E9; border-radius: 10px; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.5s ease forwards; }
.analytics-filter-btn.is-active { background: #fff; color: #185e08; font-weight: 700; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
.portfolio-tab.is-active { color: #185e08; border-bottom-color: #185e08; }
</style>
<!-- Top Stats Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-md mb-lg">
<div class="dash-card-light-green bento-card p-md rounded">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-fidelity-green/10 rounded-lg">
<span class="material-symbols-outlined text-fidelity-green">payments</span>
</div>
<?php if ($profitGrowthPct > 0): ?>
<span class="text-xs font-bold text-fidelity-green bg-fidelity-green/10 px-2 py-1 rounded-full">+<?php echo number_format($profitGrowthPct, 1); ?>%</span>
<?php endif; ?>
</div>
<h3 class="text-on-surface-variant text-sm font-medium font-label-md">Total Profit</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight font-hanken text-on-surface">$<?php echo format_usd_amount($totalProfit); ?></span>
</div>
</div>
<div class="dash-card-light-green bento-card p-md rounded">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-fidelity-green/10 rounded-lg">
<span class="material-symbols-outlined text-fidelity-green">trending_up</span>
</div>
<?php if ($dailyYieldPct > 0): ?>
<span class="text-xs font-bold text-fidelity-green bg-fidelity-green/10 px-2 py-1 rounded-full"><?php echo number_format($dailyYieldPct, 2); ?>% / day</span>
<?php endif; ?>
</div>
<h3 class="text-on-surface-variant text-sm font-medium font-label-md">Daily Avg. Return</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight font-hanken text-on-surface">$<?php echo format_usd_amount($dailyAvgReturn); ?></span>
</div>
</div>
<div class="dash-card-light-green bento-card p-md rounded">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-fidelity-green/10 rounded-lg">
<span class="material-symbols-outlined text-fidelity-green">account_balance</span>
</div>
<span class="text-xs font-bold text-on-surface-variant bg-surface-container-high px-2 py-1 rounded-full"><?php echo count($activePlans) > 0 ? 'Stable' : 'No plans'; ?></span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium font-label-md">Active Capital</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight font-hanken text-on-surface">$<?php echo format_usd_amount($activeCapital); ?></span>
</div>
</div>
<div class="dash-card-light-green bento-card p-md rounded">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-fidelity-green/10 rounded-lg">
<span class="material-symbols-outlined text-fidelity-green">auto_graph</span>
</div>
<span class="text-xs font-bold text-fidelity-green bg-fidelity-green/10 px-2 py-1 rounded-full">Projected</span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium font-label-md">Est. Monthly Earnings</h3>
<div class="flex items-end gap-2 mt-1">
<span class="text-2xl font-bold tracking-tight font-hanken text-on-surface">$<?php echo format_usd_amount($estMonthlyEarnings); ?></span>
</div>
</div>
</section>
<!-- Main Analytics Section -->
<div class="space-y-8 mb-8">
<!-- Row 1: My Portfolio (full width) -->
<div class="space-y-6 w-full">
<div class="bento-card p-6 rounded-xl min-h-0 w-full">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
<h2 class="text-lg font-bold flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">savings</span>
                        My Portfolio
</h2>
<a href="/dashboard/user/investment-plans" class="text-sm text-primary font-semibold hover:underline">Browse plans →</a>
</div>
<div class="flex gap-1 mb-4 border-b border-surface-gray overflow-x-auto">
<button type="button" class="portfolio-tab is-active px-4 py-2 text-sm font-semibold border-b-2 border-primary text-primary whitespace-nowrap" data-tab="active">Active <span class="text-on-surface-variant font-normal">(<?= count($activePlans) ?>)</span></button>
<button type="button" class="portfolio-tab px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-on-surface-variant hover:text-primary whitespace-nowrap" data-tab="matured">Matured <span class="text-on-surface-variant font-normal">(<?= count($maturedPlans) ?>)</span></button>
<button type="button" class="portfolio-tab px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-on-surface-variant hover:text-primary whitespace-nowrap" data-tab="liquidated">Liquidated <span class="text-on-surface-variant font-normal">(<?= count($liquidatedPlans) ?>)</span></button>
</div>
<div id="portfolio-panel-active" class="portfolio-panel">
<?php if (!empty($activePlans)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 overflow-y-auto custom-scrollbar max-h-[360px] pr-1">
<?php foreach ($activePlans as $ap) portfolio_plan_card($ap, 'active', $userUsdBalance); ?>
</div>
<?php else: ?>
<div class="flex flex-col items-center justify-center py-12 text-on-surface-variant">
<span class="material-symbols-outlined text-4xl mb-2 opacity-50">inventory_2</span>
<p class="text-sm font-medium">No active plans</p>
<p class="text-xs mt-1">Subscribe to a plan to start earning</p>
<a href="/dashboard/user/investment-plans" class="mt-4 px-6 py-2.5 bg-fidelity-green hover:opacity-90 text-white font-bold rounded-lg text-sm flex items-center gap-2 transition-all">
<span class="material-symbols-outlined text-lg" style="font-size:1.125rem">rocket_launch</span> Get Started
</a>
</div>
<?php endif; ?>
</div>
<div id="portfolio-panel-matured" class="portfolio-panel hidden">
<?php if (!empty($maturedPlans)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 overflow-y-auto custom-scrollbar max-h-[360px] pr-1">
<?php foreach ($maturedPlans as $ap) portfolio_plan_card($ap, 'matured', $userUsdBalance); ?>
</div>
<?php else: ?>
<div class="flex flex-col items-center justify-center py-12 text-on-surface-variant text-center px-4">
<span class="material-symbols-outlined text-4xl mb-2 opacity-50">event_available</span>
<p class="text-sm font-medium">No matured plans yet</p>
<p class="text-xs mt-1">When a plan reaches its duration, principal is credited to your USD wallet automatically.</p>
</div>
<?php endif; ?>
</div>
<div id="portfolio-panel-liquidated" class="portfolio-panel hidden">
<?php if (!empty($liquidatedPlans)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 overflow-y-auto custom-scrollbar max-h-[360px] pr-1">
<?php foreach ($liquidatedPlans as $ap) portfolio_plan_card($ap, 'liquidated', $userUsdBalance); ?>
</div>
<?php else: ?>
<div class="flex flex-col items-center justify-center py-12 text-on-surface-variant text-center px-4">
<span class="material-symbols-outlined text-4xl mb-2 opacity-50">cancel</span>
<p class="text-sm font-medium">No liquidated plans</p>
<p class="text-xs mt-1">Plans you exit early will appear here.</p>
</div>
<?php endif; ?>
</div>
</div>
<!-- Cumulative Performance (full width, below portfolio on desktop) -->
<div class="bento-card p-6 rounded-xl min-h-0 w-full">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
<h2 class="text-lg font-bold flex items-center gap-2">
                        Cumulative Performance
                        <span class="material-symbols-outlined text-on-surface-variant text-base cursor-help" title="Visualizes your total earnings growth over time">info</span>
</h2>
<div class="flex bg-surface-container rounded-lg p-1 gap-1">
<button type="button" data-period="1D" class="analytics-filter-btn px-3 py-1 text-xs font-semibold rounded hover:bg-white transition-all<?php echo $period === '1D' ? ' is-active' : ''; ?>">1D</button>
<button type="button" data-period="1W" class="analytics-filter-btn px-3 py-1 text-xs font-semibold rounded hover:bg-white transition-all<?php echo $period === '1W' ? ' is-active' : ''; ?>">1W</button>
<button type="button" data-period="1M" class="analytics-filter-btn px-3 py-1 text-xs font-semibold rounded hover:bg-white transition-all<?php echo $period === '1M' ? ' is-active' : ''; ?>">1M</button>
<button type="button" data-period="1Y" class="analytics-filter-btn px-3 py-1 text-xs font-semibold rounded hover:bg-white transition-all<?php echo $period === '1Y' ? ' is-active' : ''; ?>">1Y</button>
<button type="button" data-period="ALL" class="analytics-filter-btn px-3 py-1 text-xs font-semibold rounded hover:bg-white transition-all<?php echo $period === 'ALL' ? ' is-active' : ''; ?>">ALL</button>
</div>
</div>
<div class="relative h-[300px] w-full" id="analytics-chart">
<?php
if (!empty($chartData)) {
    $maxVal = max(array_column($chartData, 'value'));
    $minVal = min(array_column($chartData, 'value'));
    $range = $maxVal - $minVal;
    if ($range == 0) $range = 1;
    $points = [];
    $dates = [];
    $count = count($chartData);
    foreach ($chartData as $i => $point) {
        $x = $count > 1 ? ($i / ($count - 1)) * 1000 : 500;
        $y = 300 - (($point['value'] - $minVal) / $range) * 250;
        $points[] = $x . ',' . $y;
        if ($i === 0 || $i === floor($count / 4) || $i === floor($count / 2) || $i === floor($count * 3 / 4) || $i === $count - 1) {
            $dates[] = date('M j', strtotime($point['date']));
        }
    }
    $pathD = 'M' . implode(' L', $points);
    $areaD = $pathD . ' L1000,300 L0,300 Z';
?>
<svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 1000 300">
<defs>
<linearGradient id="analyticsChartGradient" x1="0" x2="0" y1="0" y2="1">
<stop offset="0%" stop-color="#337722" stop-opacity="0.2"></stop>
<stop offset="100%" stop-color="#337722" stop-opacity="0"></stop>
</linearGradient>
</defs>
<path d="<?php echo htmlspecialchars($areaD); ?>" fill="url(#analyticsChartGradient)"></path>
<path d="<?php echo htmlspecialchars($pathD); ?>" fill="none" stroke="#337722" stroke-width="3"></path>
<?php foreach ($points as $i => $p): $step = max(1, (int) floor($count / 5)); if ($i % $step === 0 || $i === $count - 1): list($px, $py) = explode(',', $p); ?>
<circle cx="<?php echo $px; ?>" cy="<?php echo $py; ?>" fill="#337722" r="4"></circle>
<?php endif; endforeach; ?>
</svg>
<div class="flex justify-between mt-4 px-2 text-xs text-on-surface-variant font-medium">
<?php foreach ($dates as $d): ?><span><?php echo htmlspecialchars($d); ?></span><?php endforeach; ?>
</div>
<?php } else { ?>
<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">No data available</div>
<?php } ?>
</div>
</div>
</div>
<!-- Row 2: Side Widgets -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
<div class="bento-card p-6 rounded-xl flex items-center gap-6">
<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center border-4 border-primary/20">
<span class="material-symbols-outlined text-3xl text-primary">workspace_premium</span>
</div>
<div>
<h3 class="text-on-surface-variant text-sm font-medium">Winning Streak</h3>
<p class="text-3xl font-bold"><?php echo (int)$winningStreakDays; ?> Day<?php echo ((int)$winningStreakDays) === 1 ? '' : 's'; ?></p>
<p class="text-xs text-on-surface-variant mt-1 flex items-center gap-1" title="Based on consecutive days with completed payout credits">
<span class="material-symbols-outlined text-xs">keyboard_double_arrow_up</span>
Personal best: <?php echo (int)$personalBestStreakDays; ?> day<?php echo ((int)$personalBestStreakDays) === 1 ? '' : 's'; ?>
</p>
</div>
</div>
<div class="bento-card p-6 rounded-xl flex items-center gap-6">
<div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center">
<span class="material-symbols-outlined text-3xl text-on-surface-variant">warning_amber</span>
</div>
<div>
<h3 class="text-on-surface-variant text-sm font-medium">Max Drawdown</h3>
<p class="text-3xl font-bold"><?php echo $maxDrawdownPct === null ? '—' : number_format((float)$maxDrawdownPct, 1) . '%'; ?></p>
<p class="text-xs text-on-surface-variant mt-1" title="Computed from the cumulative net flow curve shown above">Based on cumulative curve</p>
</div>
</div>
<div class="bento-card p-6 rounded-xl">
<h2 class="text-sm font-bold mb-4">Earnings by Currency</h2>
<?php
  $breakdownRows = [];
  if ($payoutTotalForBreakdown > 0) {
      foreach ($payoutByCurrency as $cur => $tot) {
          $breakdownRows[] = ['cur' => $cur, 'tot' => $tot, 'pct' => ($tot / $payoutTotalForBreakdown) * 100.0];
      }
  }
  $breakdownRows = array_slice($breakdownRows, 0, 4);
?>
<?php if (!empty($breakdownRows)): ?>
<div class="space-y-2">
<?php foreach ($breakdownRows as $idx => $b): ?>
<div class="flex items-center justify-between text-xs">
  <span class="flex items-center gap-2">
    <span class="w-2 h-2 rounded-full <?php echo $idx === 0 ? 'bg-primary' : ($idx === 1 ? 'bg-emerald-500' : ($idx === 2 ? 'bg-amber-500' : 'bg-surface-container-high')); ?>"></span>
    <?php echo htmlspecialchars($b['cur']); ?>
  </span>
  <span class="font-bold"><?php echo number_format((float)$b['pct'], 0); ?>%</span>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<div class="text-sm text-on-surface-variant">No payout data yet.</div>
<?php endif; ?>
</div>
</div>
</div>
<!-- History Table Section -->
<div class="bento-card rounded-xl overflow-hidden">
<div class="p-6 border-b border-surface-gray flex flex-col md:flex-row md:items-center justify-between gap-4">
<h2 class="text-lg font-bold">Distribution History</h2>
<div class="flex items-center gap-3">
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
<input class="pl-10 pr-4 py-2 bg-surface-container border-none rounded-lg text-sm w-full md:w-64 focus:ring-2 focus:ring-primary" placeholder="Search entries..." type="text"/>
</div>
<button class="p-2 border border-surface-gray rounded-lg hover:bg-surface-container-low">
<span class="material-symbols-outlined text-on-surface-variant">filter_list</span>
</button>
</div>
</div>
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant text-xs font-bold uppercase tracking-wider">
<th class="px-6 py-4">Date &amp; Time</th>
<th class="px-6 py-4">Investment Plan</th>
<th class="px-6 py-4">Asset</th>
<th class="px-6 py-4">Amount (USD)</th>
<th class="px-6 py-4">ROI %</th>
<th class="px-6 py-4">Status</th>
</tr>
</thead>
<tbody class="text-sm divide-y divide-surface-gray">
<?php
$coinLogosAnalytics = [
    'BTC' => 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
    'ETH' => 'https://assets.coingecko.com/coins/images/279/large/ethereum.png',
    'USDT' => 'https://assets.coingecko.com/coins/images/325/large/Tether.png',
];
foreach ($analyticsTx as $tx):
    $txAmt = (float)($tx['amount'] ?? 0);
    $txType = $tx['type'] ?? '';
    $analyticsTypeLabels = [
        'payout' => 'Payout',
        'profit_adjustment' => 'Profit adjustment',
        'referral_bonus' => 'Referral bonus',
        'referral_bonus_adjustment' => 'Referral bonus adjustment',
        'deposit_bonus' => 'Deposit bonus',
    ];
    $typeLabel = $analyticsTypeLabels[$txType] ?? ucfirst(str_replace('_', ' ', $txType));
    $isProfitLike = in_array($txType, ['payout', 'profit_adjustment'], true);
    $isProfitCredit = $txType === 'payout' || ($txType === 'profit_adjustment' && $txAmt >= 0);
    $displayAmt = in_array($txType, ['profit_adjustment', 'referral_bonus_adjustment'], true) ? abs($txAmt) : $txAmt;
    $logo = $coinLogosAnalytics[strtoupper($tx['currency'])] ?? null;
    $statusClass = $tx['status'] === 'completed' ? 'text-fidelity-green' : ($tx['status'] === 'rejected' ? 'text-red-500' : 'text-amber-500');
    $statusIcon = $tx['status'] === 'completed' ? 'check_circle' : ($tx['status'] === 'rejected' ? 'cancel' : 'schedule');
    $planLabel = $typeLabel;
    $roiPct = null;
    if ($txType === 'payout' && !empty($tx['reference']) && preg_match('/^earnings_inv_(\d+)/', (string) $tx['reference'], $refMatch)) {
        $payoutInvId = (int) $refMatch[1];
        if (isset($payoutInvPlan[$payoutInvId])) {
            $planLabel = $payoutInvPlan[$payoutInvId];
        }
        if (isset($payoutInvRoi[$payoutInvId]) && $payoutInvRoi[$payoutInvId] > 0) {
            $roiPct = (float) $payoutInvRoi[$payoutInvId];
        }
    }
?>
<tr class="hover:bg-surface-container-low transition-colors animate-fade-in">
<td class="px-6 py-4">
<p class="font-semibold"><?php echo date('M j, Y', strtotime($tx['created_at'])); ?></p>
<p class="text-xs text-on-surface-variant"><?php echo date('H:i', strtotime($tx['created_at'])); ?></p>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-primary"></div>
<span class="font-medium"><?php echo htmlspecialchars($planLabel); ?></span>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<?php if ($logo): ?><img alt="<?php echo htmlspecialchars($tx['currency']); ?>" class="w-5 h-5" src="<?php echo htmlspecialchars($logo); ?>"/><?php endif; ?>
<span class="font-medium"><?php echo htmlspecialchars($tx['currency']); ?></span>
</div>
</td>
<td class="px-6 py-4 font-bold <?php echo $isProfitLike ? ($isProfitCredit ? 'text-fidelity-green' : 'text-red-500') : 'text-on-surface'; ?>"><?php echo $isProfitLike ? ($isProfitCredit ? '+' : '-') : ''; ?>$<?php echo format_usd_amount($displayAmt); ?></td>
<td class="px-6 py-4">
<?php if ($isProfitLike && $isProfitCredit && $txType === 'payout' && $roiPct !== null): ?>
<span class="px-2 py-1 bg-fidelity-green/10 text-fidelity-green rounded font-bold text-xs" title="Plan daily ROI"><?php echo number_format($roiPct, 1); ?>%</span>
<?php else: ?>
<span class="px-2 py-1 bg-surface-container text-on-surface-variant rounded font-bold text-xs">—</span>
<?php endif; ?>
</td>
<td class="px-6 py-4">
<span class="flex items-center gap-1 <?php echo $statusClass; ?> font-medium">
<span class="material-symbols-outlined text-sm"><?php echo $statusIcon; ?></span>
<?php echo htmlspecialchars(ucfirst($tx['status'])); ?>
</span>
</td>
</tr>
<?php endforeach; ?>
<?php if (empty($analyticsTx)): ?>
<tr><td class="px-6 py-8 text-center text-on-surface-variant" colspan="6">No transactions yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
<div class="p-4 border-t border-surface-gray flex items-center justify-between">
<span class="text-xs text-on-surface-variant font-medium">Showing <?php echo min(count($analyticsTx), 50); ?> entries</span>
</div>
</div>
</div>
<?php require_once __DIR__ . '/../../includes/dashboard/user-layout-end.php'; ?>
<!-- Liquidate Plan Modal (outside main so it always overlays correctly) -->
<div id="liquidate-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="liquidate-modal-title">
<div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="liquidate-modal-backdrop"></div>
<div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
<div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-surface-gray my-auto">
<div class="p-6 border-b border-surface-gray flex items-center justify-between">
<h2 id="liquidate-modal-title" class="text-xl font-bold text-on-surface">Liquidate Plan</h2>
<button type="button" id="liquidate-modal-close" class="p-2 hover:bg-surface-container-low rounded-full" aria-label="Close"><span class="material-symbols-outlined">close</span></button>
</div>
<div class="p-6">
<div class="mb-4 p-4 rounded-lg bg-error-container/30 border border-error/20">
<p class="text-sm text-error font-semibold flex items-start gap-2">
<span class="material-symbols-outlined text-lg shrink-0">warning</span>
<span>Early liquidation attracts an operation fee, which will be deducted from your USD balance.</span>
</p>
</div>
<p class="text-sm text-on-surface-variant mb-4">You are about to liquidate <strong id="liquidate-plan-name" class="text-on-surface"></strong> (<span id="liquidate-plan-amount"></span> principal).</p>
<div class="grid grid-cols-2 gap-4 mb-4 text-sm">
<div class="p-3 rounded-lg bg-surface-container-low">
<p class="text-xs text-on-surface-variant uppercase font-bold mb-1">Operation Fee</p>
<p class="font-bold text-error" id="liquidate-fee-display">$0.00</p>
</div>
<div class="p-3 rounded-lg bg-surface-container-low">
<p class="text-xs text-on-surface-variant uppercase font-bold mb-1">Your USD Balance</p>
<p class="font-bold text-on-surface" id="liquidate-balance-display">$0.00</p>
</div>
</div>
<p id="liquidate-balance-note" class="text-sm mb-4 text-on-surface-variant"></p>
<div id="liquidate-error" class="text-sm text-error hidden mb-4"></div>
<input type="hidden" id="liquidate-investment-id" value=""/>
<div class="flex gap-3">
<button type="button" id="liquidate-cancel-btn" class="flex-1 px-4 py-2 bg-surface-container text-on-surface font-bold rounded-lg">Cancel</button>
<button type="button" id="liquidate-confirm-btn" class="flex-1 px-4 py-2 bg-fidelity-green hover:opacity-90 text-white font-bold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">Confirm Liquidation</button>
</div>
</div>
</div>
</div>
</div>
<!-- Floating Help Button -->
<button class="fixed bottom-6 right-6 w-14 h-14 bg-surface-container-highest text-on-surface rounded-full flex items-center justify-center shadow-xl hover:scale-105 transition-transform z-50">
<span class="material-symbols-outlined">support_agent</span>
</button>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Page load animations
    var cards = document.querySelectorAll('.bento-card');
    cards.forEach(function(card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(function() {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 100);
    });
    
    var chartWrapper = document.getElementById('analytics-chart');
    var currentPeriod = '<?php echo htmlspecialchars($period); ?>';

    function animateChart(chartEl) {
        if (!chartEl) return;
        var paths = chartEl.querySelectorAll('path[stroke]');
        paths.forEach(function(path) {
            try {
                var length = path.getTotalLength();
                if (!length) return;
                path.style.strokeDasharray = length;
                path.style.strokeDashoffset = length;
                path.style.transition = 'stroke-dashoffset 2s ease';
                setTimeout(function() { path.style.strokeDashoffset = 0; }, 500);
            } catch (err) { /* ignore invalid paths */ }
        });
    }
    if (chartWrapper) animateChart(chartWrapper);

    function updateAnalyticsChart(data) {
        if (!chartWrapper) return;
        if (!data || data.length === 0) {
            chartWrapper.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">No data available</div>';
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
            var x = count > 1 ? (i / (count - 1)) * 1000 : 500;
            var y = 300 - ((point.value - minVal) / range) * 250;
            points.push(x + ',' + y);
            if (i === 0 || i === Math.floor(count / 4) || i === Math.floor(count / 2) || i === Math.floor(count * 3 / 4) || i === count - 1) {
                dates.push(new Date(point.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            }
        });
        var pathD = 'M' + points.join(' L');
        var areaD = pathD + ' L1000,300 L0,300 Z';
        chartWrapper.innerHTML = '<svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 1000 300"><defs><linearGradient id="analyticsChartGradient" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#337722" stop-opacity="0.2"></stop><stop offset="100%" stop-color="#337722" stop-opacity="0"></stop></linearGradient></defs><path d="' + areaD + '" fill="url(#analyticsChartGradient)"></path><path d="' + pathD + '" fill="none" stroke="#337722" stroke-width="3"></path></svg><div class="flex justify-between mt-4 px-2 text-xs text-on-surface-variant font-medium">' + dates.map(function(d){ return '<span>' + d + '</span>'; }).join('') + '</div>';
        animateChart(chartWrapper);
    }

    document.querySelectorAll('.analytics-filter-btn').forEach(function(btn) {
        var p = btn.getAttribute('data-period');
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var period = this.getAttribute('data-period');
            document.querySelectorAll('.analytics-filter-btn').forEach(function(b) {
                b.classList.remove('is-active');
            });
            this.classList.add('is-active');
            fetch('/api/user/chart-data.php?period=' + encodeURIComponent(period) + '&type=analytics', { credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(res){
                if (res.success && res.data) updateAnalyticsChart(res.data);
            }).catch(function(){ if (chartWrapper) chartWrapper.innerHTML = '<div class="absolute inset-0 flex items-center justify-center text-on-surface-variant text-sm">Failed to load chart</div>'; });
        });
    });

    // Portfolio tabs
    document.querySelectorAll('.portfolio-tab').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function() {
            var tab = this.getAttribute('data-tab');
            document.querySelectorAll('.portfolio-tab').forEach(function(b) {
                b.classList.remove('is-active', 'border-primary', 'text-primary');
                b.classList.add('border-transparent', 'text-on-surface-variant');
            });
            this.classList.add('is-active', 'border-primary', 'text-primary');
            this.classList.remove('border-transparent', 'text-on-surface-variant');
            document.querySelectorAll('.portfolio-panel').forEach(function(panel) {
                panel.classList.add('hidden');
            });
            var panel = document.getElementById('portfolio-panel-' + tab);
            if (panel) panel.classList.remove('hidden');
        });
    });

    // Liquidate plan modal
    var liqModal = document.getElementById('liquidate-modal');
    var liqBackdrop = document.getElementById('liquidate-modal-backdrop');
    var liqClose = document.getElementById('liquidate-modal-close');
    var liqCancel = document.getElementById('liquidate-cancel-btn');
    var liqConfirm = document.getElementById('liquidate-confirm-btn');
    var liqError = document.getElementById('liquidate-error');
    var liqBalanceNote = document.getElementById('liquidate-balance-note');
    var liqBalanceDisplay = document.getElementById('liquidate-balance-display');
    var liqInvId = document.getElementById('liquidate-investment-id');

    function closeLiquidateModal() {
        if (liqModal) liqModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (liqError) { liqError.classList.add('hidden'); liqError.textContent = ''; }
        if (liqConfirm) {
            liqConfirm.textContent = 'Confirm Liquidation';
        }
    }

    function openLiquidateModal(btn) {
        if (!liqModal || !btn) return;
        var fee = parseFloat(btn.getAttribute('data-fee') || '0') || 0;
        var balance = parseFloat(btn.getAttribute('data-balance') || '0') || 0;
        var canAfford = balance + 0.000001 >= fee;
        var planNameEl = document.getElementById('liquidate-plan-name');
        var planAmountEl = document.getElementById('liquidate-plan-amount');
        var feeDisplayEl = document.getElementById('liquidate-fee-display');

        if (planNameEl) planNameEl.textContent = btn.getAttribute('data-plan-name') || 'Plan';
        if (planAmountEl) planAmountEl.textContent = '$' + (parseFloat(btn.getAttribute('data-amount') || '0') || 0).toFixed(2);
        if (feeDisplayEl) feeDisplayEl.textContent = '$' + fee.toFixed(2);

        if (liqBalanceDisplay) {
            liqBalanceDisplay.textContent = '$' + balance.toFixed(2);
            liqBalanceDisplay.classList.remove('text-fidelity-green', 'text-red-500');
            liqBalanceDisplay.classList.add(canAfford ? 'text-fidelity-green' : 'text-red-500');
        }
        if (liqBalanceNote) {
            liqBalanceNote.classList.remove('text-emerald-600', 'text-red-500', 'hidden');
            if (canAfford) {
                liqBalanceNote.textContent = 'Your balance is enough to implement the liquidation.';
                liqBalanceNote.classList.add('text-emerald-600');
            } else {
                liqBalanceNote.textContent = 'Insufficient balance for the operation fee. Deposit funds to your wallet to continue.';
                liqBalanceNote.classList.add('text-red-500');
            }
        }
        if (liqConfirm) {
            liqConfirm.disabled = !canAfford;
            liqConfirm.textContent = 'Confirm Liquidation';
        }
        if (liqError) {
            liqError.classList.add('hidden');
            liqError.textContent = '';
        }
        if (liqInvId) liqInvId.value = btn.getAttribute('data-investment-id') || '';

        liqModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.liquidate-plan-btn');
        if (btn) {
            e.preventDefault();
            openLiquidateModal(btn);
        }
    });
    [liqBackdrop, liqClose, liqCancel].forEach(function(el) {
        if (el) el.addEventListener('click', closeLiquidateModal);
    });

    if (liqConfirm) {
        liqConfirm.addEventListener('click', function() {
            var invId = liqInvId ? parseInt(liqInvId.value, 10) : 0;
            if (!invId) return;
            liqConfirm.disabled = true;
            liqConfirm.textContent = 'Processing…';
            if (liqError) { liqError.classList.add('hidden'); liqError.textContent = ''; }
            fetch('/api/user/liquidate-plan.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ investment_id: invId })
            }).then(function(r) { return r.json(); }).then(function(res) {
                if (res.success) {
                    window.location.reload();
                    return;
                }
                if (liqError) {
                    liqError.textContent = res.error || 'Liquidation failed';
                    liqError.classList.remove('hidden');
                }
                liqConfirm.disabled = false;
                liqConfirm.textContent = 'Confirm Liquidation';
            }).catch(function() {
                if (liqError) {
                    liqError.textContent = 'Request failed. Please try again.';
                    liqError.classList.remove('hidden');
                }
                liqConfirm.disabled = false;
                liqConfirm.textContent = 'Confirm Liquidation';
            });
        });
    }
});
</script>
</body></html>
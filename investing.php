<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/plan-types.php';
require_once __DIR__ . '/includes/session-bootstrap.php';
$siteName = get_site_name();
$isLoggedIn = !empty($_SESSION['user_id'] ?? null);
$investCtaUrl = $isLoggedIn ? '/dashboard/user/investment-plans' : '/register';
$pageTitle = 'Advanced Trading | ' . $siteName;

$plans = [];
$planTypes = get_plan_types();
try {
    $pdo = require __DIR__ . '/includes/db.php';
    ensure_plan_schema($pdo);
    $stmt = $pdo->query('SELECT id, name, slug, plan_type, description, logo_url, investment_risk, min_deposit, max_deposit, yield_min, yield_max, duration_days, min_duration_days, max_duration_days, min_duration_months, max_duration_months, withdrawal_days, liquidation_cost, features_json FROM plans WHERE enabled = 1 ORDER BY sort_order, id');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $plans[] = [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'plan_type' => normalize_plan_type($row['plan_type'] ?? 'crypto'),
            'description' => $row['description'] ?? '',
            'logo_url' => $row['logo_url'] ?? null,
            'investment_risk' => normalize_investment_risk($row['investment_risk'] ?? 'mid'),
            'min_deposit' => (float) $row['min_deposit'],
            'max_deposit' => $row['max_deposit'] !== null ? (float) $row['max_deposit'] : null,
            'yield_min' => (float) $row['yield_min'],
            'yield_max' => (float) $row['yield_max'],
            'duration_days' => (int) $row['duration_days'],
            'min_duration_days' => isset($row['min_duration_days']) && $row['min_duration_days'] !== null ? (int) $row['min_duration_days'] : (isset($row['min_duration_months']) && $row['min_duration_months'] !== null ? (int) $row['min_duration_months'] * 30 : (int) $row['duration_days']),
            'liquidation_cost' => isset($row['liquidation_cost']) ? (float) $row['liquidation_cost'] : 0.0,
        ];
    }
} catch (Throwable $e) {
    // DB unavailable
}

$plansByType = [];
foreach ($planTypes as $typeKey => $typeLabel) {
    $plansByType[$typeKey] = array_values(array_filter($plans, fn ($plan) => ($plan['plan_type'] ?? 'crypto') === $typeKey));
}
$activePlanTypes = [];
foreach ($planTypes as $typeKey => $typeLabel) {
    if (!empty($plansByType[$typeKey])) {
        $activePlanTypes[$typeKey] = $typeLabel;
    }
}
$defaultPlanTab = array_key_first($activePlanTypes) ?: 'crypto';
$showPlanTabs = count($activePlanTypes) > 1;

$marketingRiskBadge = function (?string $risk): array {
    $key = normalize_investment_risk($risk);
    $label = get_investment_risk_options()[$key];
    $classes = [
        'high' => 'bg-red-100 text-red-700',
        'mid' => 'bg-fidelity-green/10 text-fidelity-green',
        'low' => 'bg-institutional-blue/10 text-institutional-blue',
    ];
    return ['label' => $label, 'class' => $classes[$key]];
};
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
.marketing-plan-tab.is-active { color: #337722; border-bottom-color: #337722; font-weight: 700; }
.marketing-plan-panel { display: none !important; }
.marketing-plan-panel.is-active { display: block !important; }
.marketing-plan-tabs-nav {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.marketing-plan-tabs-nav::-webkit-scrollbar { display: none; }
</style>
</head>
<body class="fidelity-subpage bg-surface font-body-md text-on-surface antialiased overflow-x-hidden">
<?php $currentPage = 'investing'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main class="max-w-[1152px] mx-auto px-margin-mobile md:px-0">
<!-- Breadcrumbs -->
<nav class="py-md flex items-center gap-xs text-body-sm text-outline">
<a class="hover:text-primary" href="/">Home</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-on-surface">Advanced Trading</span>
</nav>
<!-- Hero Section -->
<section class="py-xl grid md:grid-cols-2 gap-lg items-center">
<div>
<h1 class="font-display-lg text-display-lg text-on-surface mb-sm">Trade smarter</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg leading-relaxed">
                    Get trading platforms with advanced tools and in-depth market research and start making smarter trades on a wide range of investment choices, including commission-free online trades for stocks, ETFs, and options.
                </p>
<div class="flex flex-wrap gap-md">
<a href="/register" class="bg-fidelity-green text-on-primary font-headline-md text-headline-md px-lg py-md rounded-lg shadow-sm hover:opacity-90 transition-all">
                        Open a brokerage account
                    </a>
</div>
<p class="text-xs text-outline mt-md">1. Commissions apply for representative-assisted trades. See Fee Schedule for details.</p>
</div>
<div class="relative group">
<div class="absolute -inset-4 bg-primary-container opacity-10 blur-3xl group-hover:opacity-20 transition-opacity"></div>
<img class="relative w-full h-auto drop-shadow-2xl rounded-xl investing-hero-img" data-alt="A clean and modern high-fidelity smartphone display showcasing an advanced trading application with a bright green upward-trending line chart against a dark minimalist UI. The phone is elegantly floating in a light green studio environment with soft, professional directional lighting, creating a high-end corporate fintech aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSC2yMFj3sXrbyN255I81PgE07HBquJhUXRucvnYabm7tUfkoeR_fS4zOokwVEwYvoDw-FAKqE8POB67jc8MGDUqJki8BdrdL8ff0EtI9sccebsD7fN3-2U2-WiS0jQWE1gQCc3cNHhcYx3DaAnPbAwm2uIMwqIEMds1cajhxaQQ2zRTxiuvo9ogkvnSvkuoZy4d61GCnqS8CzCbhsJVdO-AcEd-SRwfy8mqrZHoYwSBVslyoxh13QgQ"/>
</div>
</section>
<!-- Investment Plans -->
<section class="py-xl border-t border-surface-gray px-margin-mobile md:px-0">
<h2 class="font-headline-lg text-headline-lg mb-sm text-center">What you get when investing and trading at <?php echo htmlspecialchars($siteName); ?></h2>
<p class="font-body-md text-body-md text-on-surface-variant text-center mb-xl max-w-2xl mx-auto">Browse our investment plans and start growing your portfolio with transparent terms and competitive returns.</p>

<?php if (empty($plans)): ?>
<div class="text-center py-12 border border-surface-gray rounded-xl bg-white">
<p class="text-on-surface-variant">No investment plans available at the moment.</p>
<a href="<?php echo htmlspecialchars($investCtaUrl); ?>" class="inline-block mt-md bg-fidelity-green text-white px-lg py-sm rounded-lg font-label-md hover:opacity-90 transition-all">Open an account</a>
</div>
<?php else: ?>

<?php if ($showPlanTabs): ?>
<nav class="marketing-plan-tabs-nav mb-lg overflow-x-auto border-b border-surface-gray" aria-label="Investment plan categories">
<div class="inline-flex gap-md min-w-full md:w-full pb-0">
<?php foreach ($activePlanTypes as $typeKey => $typeLabel): ?>
<button type="button" class="marketing-plan-tab shrink-0 pb-3 text-sm font-label-md text-on-surface-variant hover:text-fidelity-green transition-colors border-b-2 border-transparent whitespace-nowrap<?php echo $typeKey === $defaultPlanTab ? ' is-active' : ''; ?>" data-plan-tab="<?php echo htmlspecialchars($typeKey); ?>">
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
<div class="marketing-plan-panel mb-lg<?php echo ($typeKey === $defaultPlanTab || !$showPlanTabs) ? ' is-active' : ''; ?>" data-plan-panel="<?php echo htmlspecialchars($typeKey); ?>">
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-lg">
<?php foreach ($typePlans as $plan):
    $planDays = plan_duration_days($plan);
    $riskBadge = $marketingRiskBadge($plan['investment_risk'] ?? 'mid');
    $periodReturn = format_plan_period_return($plan['yield_min'] ?? 0, $planDays);
?>
<div class="card-shadow rounded-xl bg-white p-md md:p-lg flex flex-col h-full hover-lift">
<div class="flex items-start gap-3 mb-md">
<?php echo plan_logo_markup($plan['logo_url'] ?? null, $plan['name'], 'w-10 h-10 shrink-0', 'text-sm'); ?>
<div class="flex-1 min-w-0">
<h3 class="text-base font-semibold text-on-surface leading-snug"><?php echo htmlspecialchars($plan['name']); ?></h3>
<span class="<?php echo $riskBadge['class']; ?> inline-block mt-1.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"><?php echo htmlspecialchars($riskBadge['label']); ?></span>
<?php if (!empty($plan['description'])): ?>
<p class="text-body-sm text-on-surface-variant mt-2 leading-relaxed"><?php echo htmlspecialchars($plan['description']); ?></p>
<?php endif; ?>
</div>
</div>
<div class="space-y-md mb-lg flex-grow">
<div class="grid grid-cols-2 gap-md">
<div>
<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Expected Return</p>
<p class="text-base font-bold text-fidelity-green mt-1"><?php echo htmlspecialchars($periodReturn); ?></p>
</div>
<div>
<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Min. Investment</p>
<p class="text-base font-bold text-on-surface mt-1">USD <?php echo format_usd_amount($plan['min_deposit']); ?></p>
</div>
</div>
<div class="flex justify-between items-center text-body-sm border-t border-surface-gray pt-sm">
<span class="text-on-surface-variant">Duration</span>
<span class="text-on-surface font-semibold"><?php echo (int) $planDays; ?> Days</span>
</div>
<?php if (!empty($plan['liquidation_cost']) && (float) $plan['liquidation_cost'] > 0): ?>
<div class="flex justify-between items-center text-body-sm">
<span class="text-on-surface-variant">Early Exit Fee</span>
<span class="text-amber-600 font-semibold">USD <?php echo format_usd_amount($plan['liquidation_cost']); ?></span>
</div>
<?php endif; ?>
</div>
<a href="<?php echo htmlspecialchars($investCtaUrl); ?>" class="w-full bg-fidelity-green hover:opacity-90 text-white font-label-md text-center py-sm rounded-lg transition-all flex items-center justify-center gap-xs">
<span>Invest Now</span>
<span class="material-symbols-outlined text-[18px]">trending_up</span>
</a>
</div>
<?php endforeach; ?>
</div>
</div>
<?php endforeach; ?>

<?php endif; ?>
</section>
<!-- Education/Learning -->
<section class="py-xl bg-surface-container-low -mx-margin-mobile md:-mx-20 px-margin-mobile md:px-20 rounded-3xl mb-xl">
<div class="flex flex-col md:flex-row justify-between items-end mb-xl gap-md">
<div class="max-w-2xl">
<h2 class="font-headline-lg text-headline-lg mb-sm">Learn from <?php echo htmlspecialchars($siteName); ?>'s best trading minds</h2>
<p class="text-on-surface-variant">Join a community that helps you make smarter decisions on every trade and apply years of market experience to your strategy.</p>
</div>
<div class="flex gap-sm">
<button class="p-2 rounded-full border border-outline-variant hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="p-2 rounded-full border border-outline-variant hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
<div class="grid md:grid-cols-4 gap-md">
<a class="group" href="#">
<div class="bg-white p-md rounded-xl h-full border border-surface-gray group-hover:border-institutional-blue transition-colors">
<span class="material-symbols-outlined text-institutional-blue mb-sm">video_library</span>
<h4 class="font-headline-md text-[18px] mb-xs">Webinars</h4>
<p class="text-body-sm text-on-surface-variant">Daily market briefings and technical analysis deep-dives.</p>
</div>
</a>
<a class="group" href="#">
<div class="bg-white p-md rounded-xl h-full border border-surface-gray group-hover:border-institutional-blue transition-colors">
<span class="material-symbols-outlined text-institutional-blue mb-sm">podcasts</span>
<h4 class="font-headline-md text-[18px] mb-xs">Podcasts</h4>
<p class="text-body-sm text-on-surface-variant">Tune in for "In the Money" weekly trade ideas and strategies.</p>
</div>
</a>
<a class="group" href="#">
<div class="bg-white p-md rounded-xl h-full border border-surface-gray group-hover:border-institutional-blue transition-colors">
<span class="material-symbols-outlined text-institutional-blue mb-sm">groups</span>
<h4 class="font-headline-md text-[18px] mb-xs">Coaching</h4>
<p class="text-body-sm text-on-surface-variant">Interactive 1:many sessions with our Trading Strategy Desk.</p>
</div>
</a>
<a class="group" href="#">
<div class="bg-white p-md rounded-xl h-full border border-surface-gray group-hover:border-institutional-blue transition-colors">
<span class="material-symbols-outlined text-institutional-blue mb-sm">headset_mic</span>
<h4 class="font-headline-md text-[18px] mb-xs">Support</h4>
<p class="text-body-sm text-on-surface-variant">1:1 help for complex trades and technical platform assistance.</p>
</div>
</a>
</div>
</section>
<!-- Trust/Awards -->
<section class="py-xl border-t border-surface-gray">
<div class="flex flex-col md:flex-row justify-between items-center gap-lg">
<div class="text-center md:text-left">
<h3 class="font-headline-md text-headline-md text-on-surface-variant mb-xs">Industry Recognized</h3>
<p class="text-body-sm">Consistently ranked for advanced tools and researcher power.</p>
</div>
<div class="flex flex-wrap justify-center gap-xl opacity-80">
<div class="text-center">
<div class="font-bold text-lg mb-1">NerdWallet</div>
<div class="text-[10px] uppercase tracking-wider text-outline">Best for Beginners 2026</div>
</div>
<div class="text-center">
<div class="font-bold text-lg mb-1">StockBrokers.com</div>
<div class="text-[10px] uppercase tracking-wider text-outline">#1 Overall 2026</div>
</div>
<div class="text-center">
<div class="font-bold text-lg mb-1">Kiplinger</div>
<div class="text-[10px] uppercase tracking-wider text-outline">Best Online Broker 2025</div>
</div>
</div>
<button class="text-institutional-blue font-label-md border-b border-institutional-blue hover:opacity-70 transition-opacity">Compare us</button>
</div>
</section>
<!-- Final CTA -->
<section class="py-xl mb-xl text-center">
<div class="bg-surface-container-high p-lg rounded-2xl relative overflow-hidden">
<div class="absolute top-0 right-0 p-lg opacity-10">
<span class="material-symbols-outlined text-[120px]">trending_up</span>
</div>
<h2 class="font-display-lg text-display-lg mb-sm relative z-10">Get started with <?php echo htmlspecialchars($siteName); ?></h2>
<p class="text-body-lg text-on-surface-variant mb-lg relative z-10">Make your first investment today—open a <?php echo htmlspecialchars($siteName); ?> brokerage account in just minutes.</p>
<a href="/register" class="inline-block bg-fidelity-green text-on-primary font-headline-md text-headline-md px-xl py-md rounded-lg shadow-md hover:opacity-90 transition-all relative z-10">
                    Open a brokerage account
                </a>
</div>
</section>
</main>

<!-- Fixed Floating Help (Mobile) -->
<div class="fixed bottom-6 right-6 md:hidden">
<button class="bg-fidelity-green text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center">
<span class="material-symbols-outlined">chat_bubble</span>
</button>
</div>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.marketing-plan-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var type = tab.getAttribute('data-plan-tab');
            document.querySelectorAll('.marketing-plan-tab').forEach(function (t) { t.classList.remove('is-active'); });
            document.querySelectorAll('.marketing-plan-panel').forEach(function (p) { p.classList.remove('is-active'); });
            tab.classList.add('is-active');
            var panel = document.querySelector('.marketing-plan-panel[data-plan-panel="' + type + '"]');
            if (panel) panel.classList.add('is-active');
        });
    });
});
(function () {
    var heroImg = document.querySelector('.investing-hero-img');
    if (!heroImg) return;
    heroImg.addEventListener('mousemove', function (e) {
        var rect = heroImg.getBoundingClientRect();
        var x = (e.clientX - rect.left) / rect.width - 0.5;
        var y = (e.clientY - rect.top) / rect.height - 0.5;
        heroImg.style.transform = 'perspective(1000px) rotateY(' + (x * 5) + 'deg) rotateX(' + (-y * 5) + 'deg) translateY(-5px)';
    });
    heroImg.addEventListener('mouseleave', function () {
        heroImg.style.transform = 'perspective(1000px) rotateY(0deg) rotateX(0deg) translateY(0px)';
    });
})();
</script>
</body>
</html>

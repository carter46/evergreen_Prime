<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'dashboard';
$siteName = get_site_name();

$activityLog = [];
$limit = 50;

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $activities = [];
    $stmt = $pdo->query("SELECT 'registration' AS type, u.name, u.created_at, NULL AS amount, NULL AS plan_name FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $actCols = 'u.name, t.created_at, t.amount, NULL AS plan_name';
    try { if (($chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'")) && $chk->rowCount() > 0) $actCols = 'u.name, t.created_at, t.amount, t.amount_usd, NULL AS plan_name'; } catch (Throwable $e) {}
    $stmt = $pdo->query("SELECT 'withdrawal' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'withdrawal' ORDER BY t.created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'deposit' AS type, $actCols FROM transactions t JOIN users u ON u.id = t.user_id WHERE t.type = 'deposit' AND t.status = 'completed' ORDER BY t.created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    $stmt = $pdo->query("SELECT 'plan_activated' AS type, u.name, ui.created_at, ui.amount, p.name AS plan_name FROM user_investments ui JOIN users u ON u.id = ui.user_id JOIN plans p ON p.id = ui.plan_id ORDER BY ui.created_at DESC LIMIT 20");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $activities[] = $row; }
    usort($activities, function ($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $activityLog = array_slice($activities, 0, $limit);
} catch (Throwable $e) {}

$pageTitle = $siteName . ' Admin | System Log';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
$pageHeading = 'System Activity Log';
$pageSubtitle = 'Recent registrations, deposits, withdrawals, and plan activations';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<div class="mb-6 flex items-center justify-end">
<a href="/dashboard/admin" class="text-xs font-bold text-primary hover:underline uppercase">← Back to Dashboard</a>
</div>
<div class="bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="p-6 border-b border-primary/10">
<h3 class="font-bold text-lg">Activity Timeline</h3>
</div>
<div class="p-6 flex-1 space-y-4 max-h-[70vh] overflow-y-auto">
<?php
$actTitles = ['registration' => 'New Registration', 'withdrawal' => 'Withdrawal Request', 'deposit' => 'Deposit', 'plan_activated' => 'Plan Activated'];
$actColors = ['registration' => 'bg-emerald-500', 'withdrawal' => 'bg-primary', 'deposit' => 'bg-blue-500', 'plan_activated' => 'bg-slate-300'];
$siteNameAct = $siteName;
foreach ($activityLog as $i => $a):
    $type = $a['type'];
    $title = $actTitles[$type] ?? ucfirst($type);
    $color = $actColors[$type] ?? 'bg-slate-300';
    $name = $a['name'] ?: 'User';
    if ($type === 'registration') $desc = $name . ' just joined ' . $siteNameAct . '.';
    elseif ($type === 'withdrawal') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float)$a['amount_usd'] : (float)$a['amount'];
        $desc = $name . ' requested a $' . format_usd_amount($amt) . ' payout.';
    } elseif ($type === 'deposit') {
        $amt = isset($a['amount_usd']) && $a['amount_usd'] !== null ? (float)$a['amount_usd'] : (float)$a['amount'];
        $desc = $name . ' deposited $' . format_usd_amount($amt) . '.';
    } elseif ($type === 'plan_activated') $desc = ($a['plan_name'] ?? 'Plan') . ' started for ' . $name . ' ($' . format_usd_amount($a['amount']) . ').';
    else $desc = $name;
?>
<div class="flex gap-4">
<div class="relative">
<div class="w-2.5 h-2.5 rounded-full <?php echo $color; ?> mt-1.5"></div>
<?php if ($i < count($activityLog) - 1): ?><div class="absolute top-4 left-1.25 w-px h-full bg-slate-200 dark:bg-white/5"></div><?php endif; ?>
</div>
<div class="flex-1 pb-4">
<p class="text-sm font-medium"><?php echo htmlspecialchars($title); ?></p>
<p class="text-xs text-slate-500 mb-1"><?php echo htmlspecialchars($desc); ?></p>
<p class="text-[10px] text-primary font-bold"><?php echo strtoupper(time_ago($a['created_at'])); ?> · <?php echo date('M j, Y H:i', strtotime($a['created_at'])); ?></p>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($activityLog)): ?>
<p class="text-sm text-slate-500">No activity recorded yet.</p>
<?php endif; ?>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>

<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/admin-audit-log.php';

$currentPage = 'audit-log';
$siteName = get_site_name();

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 30;
$entityType = trim((string) ($_GET['entity_type'] ?? 'all'));
$actionFilter = trim((string) ($_GET['action'] ?? 'all'));
$search = trim((string) ($_GET['search'] ?? ''));

$auditRows = [];
$pagination = ['page' => 1, 'per_page' => $perPage, 'total' => 0, 'total_pages' => 1];
$entityLabels = admin_audit_entity_labels();
$actionLabels = admin_audit_action_labels();

try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $result = list_admin_audit_logs(
        $pdo,
        $page,
        $perPage,
        $entityType !== 'all' ? $entityType : null,
        $actionFilter !== 'all' ? $actionFilter : null,
        $search !== '' ? $search : null
    );
    $auditRows = $result['data'];
    $pagination = $result['pagination'];
} catch (Throwable $e) {
}

$pageTitle = $siteName . ' Admin | Audit Log';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
$pageHeading = 'Admin Audit Log';
$pageSubtitle = 'Every admin change — plans, wallet addresses, users, transactions, settings, and more';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<form method="get" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
<div class="md:col-span-2">
<label class="block text-xs font-bold uppercase text-slate-500 mb-1">Search</label>
<input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Admin name, email, or summary..." class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm"/>
</div>
<div>
<label class="block text-xs font-bold uppercase text-slate-500 mb-1">Entity</label>
<select name="entity_type" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm">
<option value="all"<?php echo $entityType === 'all' ? ' selected' : ''; ?>>All types</option>
<?php foreach ($entityLabels as $key => $label): ?>
<option value="<?php echo htmlspecialchars($key); ?>"<?php echo $entityType === $key ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<label class="block text-xs font-bold uppercase text-slate-500 mb-1">Action</label>
<select name="action" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm">
<option value="all"<?php echo $actionFilter === 'all' ? ' selected' : ''; ?>>All actions</option>
<?php foreach ($actionLabels as $key => $label): ?>
<option value="<?php echo htmlspecialchars($key); ?>"<?php echo $actionFilter === $key ? ' selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="md:col-span-4 flex gap-2">
<button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg">Apply filters</button>
<a href="/dashboard/admin/audit-log" class="px-4 py-2 border border-slate-200 dark:border-zinc-700 text-sm font-bold rounded-lg">Reset</a>
</div>
</form>

<div class="bg-white dark:bg-white/5 rounded-xl border border-primary/10 shadow-sm overflow-hidden">
<div class="p-4 sm:p-6 border-b border-primary/10 flex flex-wrap items-center justify-between gap-2">
<h3 class="font-bold text-lg">Change history</h3>
<p class="text-xs text-slate-500"><?php echo (int) $pagination['total']; ?> record(s)</p>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left min-w-[900px]">
<thead class="bg-slate-50 dark:bg-zinc-800">
<tr>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500">When</th>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500">Admin</th>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500">Action</th>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500">Entity</th>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500">Summary</th>
<th class="px-4 py-3 text-xs font-bold uppercase text-slate-500 text-right">Details</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200 dark:divide-zinc-800">
<?php if (empty($auditRows)): ?>
<tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No admin actions logged yet.</td></tr>
<?php else: ?>
<?php foreach ($auditRows as $row):
    $adminLabel = trim((string) ($row['admin_name'] ?? ''));
    if ($adminLabel === '') {
        $adminLabel = $row['admin_email'] ?: ('Admin #' . (int) $row['admin_id']);
    }
    $entityLabel = $entityLabels[$row['entity_type']] ?? ucfirst(str_replace('_', ' ', $row['entity_type']));
    $actionLabel = $actionLabels[$row['action']] ?? ucfirst($row['action']);
    $hasDetails = !empty($row['before']) || !empty($row['after']) || !empty($row['meta']);
?>
<tr class="align-top">
<td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
<?php echo htmlspecialchars(date('M j, Y H:i', strtotime($row['created_at']))); ?><br>
<span class="text-[10px]"><?php echo htmlspecialchars(time_ago($row['created_at'])); ?></span>
</td>
<td class="px-4 py-3 text-sm">
<div class="font-medium"><?php echo htmlspecialchars($adminLabel); ?></div>
<?php if (!empty($row['admin_email'])): ?><div class="text-xs text-slate-500"><?php echo htmlspecialchars($row['admin_email']); ?></div><?php endif; ?>
<?php if (!empty($row['ip_address'])): ?><div class="text-[10px] text-slate-400 mt-0.5">IP: <?php echo htmlspecialchars($row['ip_address']); ?></div><?php endif; ?>
</td>
<td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-primary/10 text-primary"><?php echo htmlspecialchars($actionLabel); ?></span></td>
<td class="px-4 py-3 text-sm">
<div><?php echo htmlspecialchars($entityLabel); ?></div>
<?php if ($row['entity_id']): ?><div class="text-xs text-slate-500">ID #<?php echo (int) $row['entity_id']; ?></div><?php endif; ?>
</td>
<td class="px-4 py-3 text-sm max-w-md"><?php echo htmlspecialchars($row['summary']); ?></td>
<td class="px-4 py-3 text-right">
<?php if ($hasDetails): ?>
<details class="text-left inline-block">
<summary class="cursor-pointer text-xs font-bold text-primary hover:underline">View</summary>
<div class="mt-2 p-3 bg-slate-50 dark:bg-zinc-900 rounded-lg text-[11px] font-mono max-w-lg overflow-x-auto space-y-2">
<?php if (!empty($row['before'])): ?>
<div><span class="font-bold text-slate-500 block mb-1">Before</span><pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars(json_encode($row['before'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''); ?></pre></div>
<?php endif; ?>
<?php if (!empty($row['after'])): ?>
<div><span class="font-bold text-slate-500 block mb-1">After</span><pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars(json_encode($row['after'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''); ?></pre></div>
<?php endif; ?>
<?php if (!empty($row['meta'])): ?>
<div><span class="font-bold text-slate-500 block mb-1">Meta</span><pre class="whitespace-pre-wrap break-all"><?php echo htmlspecialchars(json_encode($row['meta'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''); ?></pre></div>
<?php endif; ?>
</div>
</details>
<?php else: ?>
<span class="text-xs text-slate-400">—</span>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
<?php if ((int) $pagination['total_pages'] > 1): ?>
<div class="p-4 border-t border-primary/10 flex flex-wrap items-center justify-between gap-3">
<p class="text-xs text-slate-500">Page <?php echo (int) $pagination['page']; ?> of <?php echo (int) $pagination['total_pages']; ?></p>
<div class="flex gap-2">
<?php
$baseQuery = http_build_query(array_filter([
    'search' => $search !== '' ? $search : null,
    'entity_type' => $entityType !== 'all' ? $entityType : null,
    'action' => $actionFilter !== 'all' ? $actionFilter : null,
]));
$prevPage = max(1, (int) $pagination['page'] - 1);
$nextPage = min((int) $pagination['total_pages'], (int) $pagination['page'] + 1);
?>
<?php if ($pagination['page'] > 1): ?>
<a href="/dashboard/admin/audit-log?<?php echo $baseQuery . ($baseQuery ? '&' : '') . 'page=' . $prevPage; ?>" class="px-3 py-1.5 text-xs font-bold border border-slate-200 dark:border-zinc-700 rounded-lg">← Previous</a>
<?php endif; ?>
<?php if ($pagination['page'] < $pagination['total_pages']): ?>
<a href="/dashboard/admin/audit-log?<?php echo $baseQuery . ($baseQuery ? '&' : '') . 'page=' . $nextPage; ?>" class="px-3 py-1.5 text-xs font-bold border border-slate-200 dark:border-zinc-700 rounded-lg">Next →</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>

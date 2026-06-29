<?php
$u = get_current_user_data() ?? [];
$adminName = $u['name'] ?? 'Super Admin';
$avatarUrl = $u['avatar_url'] ?? null;
$initials = strtoupper(substr($adminName ?: 'A', 0, 2));
$monthStart = date('M d');
$monthEnd = date('M d, Y', strtotime('last day of this month'));
$dateRange = $monthStart . ' - ' . $monthEnd;
$pendingNotifCount = isset($adminPendingNotifCount) ? (int) $adminPendingNotifCount : 0;
?>
<header class="admin-topbar fixed top-0 left-0 lg:left-64 right-0 bg-surface-container-lowest border-b border-surface-gray flex items-center justify-between px-4 lg:px-lg z-40 gap-2 md:gap-4">
<div class="flex items-center gap-2 md:gap-6 min-w-0 flex-1">
<button type="button" id="admin-sidebar-toggle" class="lg:hidden shrink-0 w-10 h-10 flex items-center justify-center rounded hover:bg-surface-container transition-colors" aria-label="Toggle menu">
<span class="material-symbols-outlined text-on-surface">menu</span>
</button>
<div class="relative hidden md:block w-full max-w-md min-w-0">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm pointer-events-none">search</span>
<input class="w-full bg-surface-container-low border border-surface-gray rounded-lg py-2 pl-10 pr-4 text-sm text-on-surface focus:ring-1 focus:ring-fidelity-green focus:border-fidelity-green outline-none transition-all" placeholder="Search accounts, txids, or users..." type="search" aria-label="Search"/>
</div>
<div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-surface-container-low rounded border border-surface-gray text-on-surface-variant shrink-0">
<span class="material-symbols-outlined text-sm">calendar_today</span>
<span class="font-label-xs text-label-xs uppercase whitespace-nowrap"><?php echo htmlspecialchars($dateRange); ?></span>
</div>
</div>
<div class="flex items-center gap-3 md:gap-6 shrink-0 relative z-10">
<div class="flex items-center gap-3 md:gap-4">
<button type="button" class="relative text-on-surface-variant hover:text-primary transition-colors p-1" aria-label="Notifications">
<span class="material-symbols-outlined text-[22px]">notifications</span>
<?php if ($pendingNotifCount > 0): ?>
<span class="absolute -top-1 -right-1 bg-fidelity-green text-white font-bold text-[10px] min-w-[1rem] h-4 px-1 rounded-full flex items-center justify-center"><?php echo $pendingNotifCount > 99 ? '99+' : $pendingNotifCount; ?></span>
<?php endif; ?>
</button>
<button type="button" class="hidden sm:block text-on-surface-variant hover:text-primary transition-colors p-1" aria-label="Live feed">
<span class="material-symbols-outlined text-[22px]">sensors</span>
</button>
</div>
<div class="hidden md:block h-8 w-px bg-surface-gray"></div>
<div class="flex items-center gap-2 md:gap-3">
<div class="text-right hidden sm:block">
<p class="font-label-md text-label-md text-on-surface font-bold truncate max-w-[140px]"><?php echo htmlspecialchars($adminName); ?></p>
<p class="text-[10px] text-fidelity-green font-bold uppercase tracking-widest">Verified System</p>
</div>
<?php if ($avatarUrl): ?>
<img alt="" class="w-9 h-9 md:w-10 md:h-10 rounded-full border-2 border-surface-gray object-cover shrink-0" src="<?php echo htmlspecialchars($avatarUrl); ?>"/>
<?php else: ?>
<div class="w-9 h-9 md:w-10 md:h-10 rounded-full border-2 border-surface-gray bg-surface-container-low flex items-center justify-center text-fidelity-green font-bold text-sm shrink-0"><?php echo htmlspecialchars($initials); ?></div>
<?php endif; ?>
</div>
</div>
</header>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/../translation-widget.php'; ?>

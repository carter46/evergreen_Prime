<?php
/**
 * Bloombit - Shared User Header (Top Bar)
 * Use on all user dashboard pages. Renders greeting, crypto ticker, notifications, profile.
 */
$u = get_current_user_data() ?? [];
$userName = $u['name'] ?? 'User';
$userEmail = $u['email'] ?? '';
$avatarUrl = $u['avatar_url'] ?? null;
$initials = strtoupper(substr($userName ?: 'U', 0, 2));
?>
<header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <button type="button" id="user-sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px] shrink-0" aria-label="Toggle sidebar">
            <span class="material-icons-round">menu</span>
        </button>
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold truncate">Good morning, <?php echo htmlspecialchars($userName); ?></h1>
            <p class="text-slate-500 text-sm">System status: <span class="text-emerald-500 font-medium">AI Core Online</span></p>
        </div>
    </div>
    <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
        <div class="flex gap-2 sm:gap-4">
            <div class="bg-white dark:bg-white/5 border border-primary/10 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-3 shadow-sm">
                <span class="text-xs text-slate-400 uppercase font-bold">BTC/USD</span>
                <span class="font-bold text-sm sm:text-base" data-coin="bitcoin" data-price="">--</span>
                <span class="text-xs font-bold crypto-change text-emerald-500" data-coin="bitcoin" data-change="">--</span>
            </div>
        </div>
        <div class="flex items-center gap-3 sm:gap-4 border-l border-slate-200 dark:border-white/10 pl-4">
            <button class="relative w-10 h-10 flex items-center justify-center text-slate-400 hover:text-primary transition-colors rounded-xl hover:bg-primary/10 min-h-[44px] min-w-[44px]">
                <span class="material-icons-round">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
            </button>
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-xs text-slate-500"><?php echo !empty($u['verified']) ? 'Verified User' : 'User'; ?></p>
                </div>
                <?php if ($avatarUrl): ?><img alt="User" class="w-10 h-10 rounded-full border-2 border-primary shrink-0 object-cover" src="<?php echo htmlspecialchars($avatarUrl); ?>"/><?php else: ?><div class="w-10 h-10 rounded-full border-2 border-primary bg-primary/20 flex items-center justify-center text-primary font-bold text-sm shrink-0"><?php echo htmlspecialchars($initials); ?></div><?php endif; ?>
            </div>
        </div>
    </div>
</header>

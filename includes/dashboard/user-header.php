<?php
/**
 * Bloombit - Shared User Header (Top Bar)
 * Sticky on mobile/tablet; scrolls with content then sticks. Toggle opens sidebar.
 */
$u = get_current_user_data() ?? [];
$userName = $u['name'] ?? 'User';
$userEmail = $u['email'] ?? '';
$avatarUrl = $u['avatar_url'] ?? null;
$initials = strtoupper(substr($userName ?: 'U', 0, 2));
?>
<header class="sticky top-0 z-30 shrink-0 bg-white/90 dark:bg-background-dark/95 backdrop-blur-md border-b border-primary/10 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 sm:py-4 mb-6 sm:mb-8">
    <div class="flex items-center justify-between gap-3 min-w-0">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
            <button type="button" id="user-sidebar-toggle" class="lg:hidden flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px]" aria-label="Toggle menu">
                <span class="material-icons-round text-2xl">menu</span>
            </button>
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-2xl md:text-3xl font-bold truncate">Good morning, <?php echo htmlspecialchars($userName); ?></h1>
                <p class="text-slate-500 text-xs sm:text-sm truncate">System status: <span class="text-emerald-500 font-medium">AI Core Online</span></p>
            </div>
        </div>
        <div class="flex items-center gap-1 sm:gap-3 flex-shrink-0">
            <button type="button" class="relative w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors rounded-xl hover:bg-primary/10 min-h-[44px] min-w-[44px]" aria-label="Notifications">
                <span class="material-icons-round text-xl sm:text-2xl">notifications</span>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-primary rounded-full"></span>
            </button>
            <div class="flex items-center gap-2">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none truncate max-w-[120px]"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-xs text-slate-500"><?php echo !empty($u['verified']) ? 'Verified' : 'User'; ?></p>
                </div>
                <?php if ($avatarUrl): ?><img alt="User" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-primary shrink-0 object-cover" src="<?php echo htmlspecialchars($avatarUrl); ?>"/><?php else: ?><div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-primary bg-primary/20 flex items-center justify-center text-primary font-bold text-sm shrink-0"><?php echo htmlspecialchars($initials); ?></div><?php endif; ?>
            </div>
        </div>
    </div>
</header>

<?php
/**
 * Fixed top bar for user dashboard.
 */
$u = get_current_user_data() ?? [];
$userName = $u['name'] ?? 'User';
$avatarUrl = $u['avatar_url'] ?? null;
$initials = strtoupper(substr($userName ?: 'U', 0, 2));
$isVerified = !empty($u['verified']) || (($u['kyc_status'] ?? '') === 'approved');
?>
<header class="user-topbar fixed top-0 right-0 left-0 lg:left-64 border-b border-low bg-surface-dim/80 backdrop-blur-xl flex items-center justify-between px-4 md:px-margin-desktop z-50 gap-2 md:gap-3">
<div class="flex items-center shrink-0 w-10 lg:w-0 lg:overflow-hidden relative z-10">
<button type="button" id="user-sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg hover:bg-surface-container-high transition-colors" aria-label="Toggle menu">
<span class="material-symbols-outlined text-on-surface">menu</span>
</button>
</div>
<?php require __DIR__ . '/user-social-proof.php'; ?>
<div class="flex items-center gap-2 md:gap-5 shrink-0 relative z-10">
<div class="hidden sm:flex items-center gap-3 text-on-surface-variant">
<button type="button" class="hover:text-primary-container transition-colors p-1" aria-label="Live feed"><span class="material-symbols-outlined text-[22px]">sensors</span></button>
<button type="button" class="hover:text-primary-container transition-colors relative p-1" aria-label="Notifications">
<span class="material-symbols-outlined text-[22px]">notifications</span>
<span class="absolute top-0.5 right-0.5 w-2 h-2 bg-primary-container rounded-full border border-surface-dim"></span>
</button>
</div>
<div class="hidden md:block h-8 w-px bg-border-low"></div>
<div class="flex items-center gap-2 md:gap-3">
<div class="text-right hidden sm:block">
<p class="font-label-sm text-label-sm text-on-surface font-bold truncate max-w-[140px]"><?php echo htmlspecialchars($userName); ?></p>
<?php if ($isVerified): ?>
<div class="flex items-center justify-end gap-1 text-[10px] text-success font-bold tracking-tight">
<span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">verified</span>
Verified
</div>
<?php else: ?>
<p class="text-[10px] text-text-secondary">Member</p>
<?php endif; ?>
</div>
<?php if ($avatarUrl): ?>
<img alt="" class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-primary-container object-cover shrink-0" src="<?php echo htmlspecialchars($avatarUrl); ?>"/>
<?php else: ?>
<div class="w-9 h-9 md:w-10 md:h-10 rounded-full border border-primary-container bg-surface-container-highest flex items-center justify-center text-primary-container font-bold text-sm shrink-0"><?php echo htmlspecialchars($initials); ?></div>
<?php endif; ?>
</div>
</div>
</header>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/../translation-widget.php'; ?>
<style>
.gtranslate_wrapper { left: auto !important; right: 20px !important; bottom: 20px !important; top: auto !important; }
@media (max-width: 768px) { .gtranslate_wrapper { right: 12px !important; bottom: 12px !important; } }
</style>

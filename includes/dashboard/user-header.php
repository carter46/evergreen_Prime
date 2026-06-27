<?php
/**
 * Fixed top bar for user dashboard (overview design).
 */
$u = get_current_user_data() ?? [];
$userName = $u['name'] ?? 'User';
$avatarUrl = $u['avatar_url'] ?? null;
$initials = strtoupper(substr($userName ?: 'U', 0, 2));
$isVerified = !empty($u['verified']) || (($u['kyc_status'] ?? '') === 'approved');
require_once __DIR__ . '/user-social-proof-data.php';
$tickerMessages = array_slice(user_dashboard_social_proof_messages(), 0, 8);
$tickerText = implode(' &nbsp;&bull;&nbsp; ', array_map('htmlspecialchars', $tickerMessages));
if ($tickerText === '') {
    $tickerText = 'Live institutional trading activity updates';
}
?>
<header class="user-topbar fixed top-0 left-0 lg:left-64 right-0 bg-surface-container-lowest border-b border-surface-gray flex justify-between items-center px-4 lg:px-lg z-40 gap-2">
<div class="flex items-center gap-md min-w-0 flex-1">
<button type="button" id="user-sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded hover:bg-surface-container transition-colors shrink-0" aria-label="Toggle menu">
<span class="material-symbols-outlined text-on-surface">menu</span>
</button>
<div class="user-ticker-wrap hidden sm:flex min-w-0">
<span class="user-ticker-dot" aria-hidden="true"></span>
<marquee class="user-ticker-marquee" scrollamount="4" aria-live="polite"><?php echo $tickerText; ?></marquee>
</div>
</div>
<div class="flex items-center gap-3 lg:gap-lg shrink-0">
<div class="flex items-center gap-sm">
<div class="text-right hidden sm:block">
<p class="font-label-md text-label-md font-bold text-on-surface truncate max-w-[140px]"><?php echo htmlspecialchars($userName); ?></p>
<div class="flex items-center gap-1 justify-end">
<?php if ($isVerified): ?>
<span class="material-symbols-outlined text-fidelity-green text-[14px]" style="font-variation-settings: 'FILL' 1;">verified</span>
<?php endif; ?>
<span class="font-label-md text-[10px] text-on-surface-variant">AI Core</span>
</div>
</div>
<?php if ($avatarUrl): ?>
<img alt="" class="w-10 h-10 rounded-full border-2 border-surface-gray object-cover shrink-0" src="<?php echo htmlspecialchars($avatarUrl); ?>"/>
<?php else: ?>
<div class="w-10 h-10 rounded-full border-2 border-surface-gray bg-surface-container-low flex items-center justify-center text-fidelity-green font-bold text-sm shrink-0"><?php echo htmlspecialchars($initials); ?></div>
<?php endif; ?>
</div>
<div class="flex items-center gap-md">
<span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary transition-colors hidden sm:inline">notifications_active</span>
<div class="px-sm py-1 border border-fidelity-green/30 bg-fidelity-green/5 rounded text-fidelity-green font-label-md text-label-md whitespace-nowrap">
AI Core Online
</div>
</div>
</div>
</header>
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/../translation-widget.php'; ?>
<style>
.gtranslate_wrapper { left: auto !important; right: 20px !important; bottom: 20px !important; top: auto !important; }
@media (max-width: 768px) { .gtranslate_wrapper { right: 12px !important; bottom: 12px !important; } }
</style>
<script>
window.addEventListener('scroll', function () {
  var header = document.querySelector('.user-topbar');
  if (!header) return;
  if (window.scrollY > 20) header.classList.add('shadow-md');
  else header.classList.remove('shadow-md');
});
</script>

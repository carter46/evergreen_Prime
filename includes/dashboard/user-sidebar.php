<?php
/**
 * User dashboard sidebar — shared across all user dashboard pages.
 */
$current = $currentPage ?? '';
$siteName = $siteName ?? get_site_name();
$impersonating = isset($_SESSION['impersonate_admin_id']);
$navActive = function ($page) use ($current) {
    if ($current === $page) {
        return 'flex items-center gap-3 px-6 py-3 text-primary-container font-bold border-r-2 border-primary-container bg-surface-container-high transition-transform active:scale-[0.98]';
    }
    return 'flex items-center gap-3 px-6 py-3 text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors duration-200';
};
$iconFill = function ($page) use ($current) {
    return $current === $page ? " style=\"font-variation-settings: 'FILL' 1;\"" : '';
};
?>
<div id="user-sidebar-overlay" class="fixed inset-0 bg-black/60 z-[55] lg:hidden hidden" aria-hidden="true"></div>
<aside id="user-sidebar" class="fixed inset-y-0 left-0 w-64 border-r border-low bg-surface-dim flex flex-col z-[60] transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
<div class="px-6 py-6 mb-2 shrink-0">
<h1 class="font-headline-md text-headline-md font-bold text-primary-container leading-tight"><?php echo htmlspecialchars($siteName); ?></h1>
<p class="font-label-sm text-label-sm text-on-surface-variant opacity-60 mt-0.5">Institutional Account</p>
</div>
<?php if ($impersonating): ?>
<a href="/api/admin/stop-impersonate.php" class="mx-4 mb-3 flex items-center gap-2 px-3 py-2 bg-primary-container/15 text-primary-container rounded-lg hover:bg-primary-container/25 transition-colors text-sm font-semibold shrink-0">
<span class="material-symbols-outlined text-lg">admin_panel_settings</span>
Switch back to Admin
</a>
<?php endif; ?>
<nav class="flex-1 min-h-0 space-y-0.5 overflow-y-auto dash-scrollbar overscroll-contain">
<a class="<?php echo $navActive('dashboard'); ?>" href="/dashboard/user/dashboard">
<span class="material-symbols-outlined"<?php echo $iconFill('dashboard'); ?>>dashboard</span>
<span class="font-label-sm text-label-sm">Dashboard</span>
</a>
<a class="<?php echo $navActive('wallet'); ?>" href="/dashboard/user/wallet">
<span class="material-symbols-outlined"<?php echo $iconFill('wallet'); ?>>account_balance_wallet</span>
<span class="font-label-sm text-label-sm">Wallet</span>
</a>
<a class="<?php echo $navActive('analytics'); ?>" href="/dashboard/user/analytics">
<span class="material-symbols-outlined"<?php echo $iconFill('analytics'); ?>>monitoring</span>
<span class="font-label-sm text-label-sm">My Portfolio</span>
</a>
<a class="<?php echo $navActive('investment-plans'); ?>" href="/dashboard/user/investment-plans">
<span class="material-symbols-outlined"<?php echo $iconFill('investment-plans'); ?>>account_tree</span>
<span class="font-label-sm text-label-sm">Investment Plans</span>
</a>
<a class="<?php echo $navActive('referrals'); ?>" href="/dashboard/user/referrals">
<span class="material-symbols-outlined"<?php echo $iconFill('referrals'); ?>>group_add</span>
<span class="font-label-sm text-label-sm">Referrals</span>
</a>
<a class="<?php echo $navActive('history'); ?>" href="/dashboard/user/transactions">
<span class="material-symbols-outlined"<?php echo $iconFill('history'); ?>>history</span>
<span class="font-label-sm text-label-sm">Trade History</span>
</a>
<div class="pt-8 pb-3 px-6 text-[10px] uppercase tracking-widest text-on-surface-variant opacity-40 font-bold">System</div>
<a class="<?php echo $navActive('profile'); ?>" href="/dashboard/user/profile">
<span class="material-symbols-outlined"<?php echo $iconFill('profile'); ?>>settings</span>
<span class="font-label-sm text-label-sm">Settings</span>
</a>
<a class="<?php echo $navActive('kyc'); ?>" href="/dashboard/user/kyc">
<span class="material-symbols-outlined"<?php echo $iconFill('kyc'); ?>>verified_user</span>
<span class="font-label-sm text-label-sm">KYC</span>
</a>
<a class="<?php echo $navActive('support'); ?>" href="/live_chat">
<span class="material-symbols-outlined"<?php echo $iconFill('support'); ?>>support_agent</span>
<span class="font-label-sm text-label-sm">Support</span>
</a>
</nav>
<div class="px-6 py-6 mt-auto shrink-0 space-y-3">
<a href="/dashboard/user/wallet?action=deposit" class="block w-full py-3 bg-primary-container text-on-primary font-bold rounded-lg shadow-lg shadow-primary-container/10 hover:opacity-90 transition-all text-center text-label-sm">
Deposit Funds
</a>
<button type="button" data-logout class="w-full flex items-center justify-center gap-2 py-2.5 text-critical hover:bg-critical/10 rounded-lg transition-colors text-sm font-semibold">
<span class="material-symbols-outlined text-[18px]">logout</span>
Sign Out
</button>
</div>
</aside>
<style>
#user-sidebar {
  height: 100vh;
  height: 100dvh;
  max-height: 100dvh;
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.getElementById('user-sidebar-overlay');
  var sidebar = document.getElementById('user-sidebar');
  var toggleBtn = document.getElementById('user-sidebar-toggle');
  function open() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
  }
  if (overlay) overlay.addEventListener('click', close);
  if (toggleBtn) toggleBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    sidebar.classList.contains('-translate-x-full') ? open() : close();
  });
  document.querySelectorAll('#user-sidebar a, #user-sidebar button[data-logout]').forEach(function (el) {
    el.addEventListener('click', function () { if (window.innerWidth < 1024) close(); });
  });
});
</script>

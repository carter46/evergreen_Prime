<?php
/**
 * User dashboard sidebar — shared across all user dashboard pages.
 */
$current = $currentPage ?? '';
$siteName = $siteName ?? get_site_name();
$impersonating = isset($_SESSION['impersonate_admin_id']);
$navActive = function ($page) use ($current) {
    if ($current === $page) {
        return 'flex items-center gap-sm px-md py-sm rounded text-primary font-bold border-r-4 border-primary bg-surface-container-low transition-all duration-150';
    }
    return 'flex items-center gap-sm px-md py-sm rounded text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors duration-200';
};
$iconFill = function ($page) use ($current) {
    return $current === $page ? " style=\"font-variation-settings: 'FILL' 1;\"" : '';
};
?>
<div id="user-sidebar-overlay" class="fixed inset-0 bg-black/40 z-[55] lg:hidden hidden" aria-hidden="true"></div>
<aside id="user-sidebar" class="h-screen w-64 fixed left-0 top-0 bg-surface-container-lowest border-r border-surface-gray flex flex-col py-md z-[60] transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
<div class="px-md mb-xl shrink-0">
<h1 class="font-headline-md text-headline-md font-bold text-fidelity-green leading-tight"><?php echo htmlspecialchars($siteName); ?></h1>
<p class="font-label-md text-label-md text-on-surface-variant opacity-70 mt-0.5">AI Core Online</p>
</div>
<?php if ($impersonating): ?>
<a href="/api/admin/stop-impersonate.php" class="mx-sm mb-sm flex items-center gap-sm px-md py-sm bg-fidelity-green/10 text-fidelity-green rounded hover:bg-fidelity-green/15 transition-colors text-sm font-semibold shrink-0">
<span class="material-symbols-outlined text-lg">admin_panel_settings</span>
Switch back to Admin
</a>
<?php endif; ?>
<nav class="flex-1 min-h-0 px-sm space-y-base overflow-y-auto hide-scrollbar overscroll-contain">
<a class="<?php echo $navActive('dashboard'); ?>" href="/dashboard/user/dashboard">
<span class="material-symbols-outlined"<?php echo $iconFill('dashboard'); ?>>dashboard</span>
<span class="font-body-md text-body-md">Dashboard</span>
</a>
<a class="<?php echo $navActive('wallet'); ?>" href="/dashboard/user/wallet">
<span class="material-symbols-outlined"<?php echo $iconFill('wallet'); ?>>account_balance_wallet</span>
<span class="font-body-md text-body-md">Wallet</span>
</a>
<a class="<?php echo $navActive('analytics'); ?>" href="/dashboard/user/analytics">
<span class="material-symbols-outlined"<?php echo $iconFill('analytics'); ?>>account_balance</span>
<span class="font-body-md text-body-md">My Investments</span>
</a>
<a class="<?php echo $navActive('investment-plans'); ?>" href="/dashboard/user/investment-plans">
<span class="material-symbols-outlined"<?php echo $iconFill('investment-plans'); ?>>assignment</span>
<span class="font-body-md text-body-md">Investment Plans</span>
</a>
<a class="<?php echo $navActive('referrals'); ?>" href="/dashboard/user/referrals">
<span class="material-symbols-outlined"<?php echo $iconFill('referrals'); ?>>group</span>
<span class="font-body-md text-body-md">Referrals</span>
</a>
<a class="<?php echo $navActive('history'); ?>" href="/dashboard/user/transactions">
<span class="material-symbols-outlined"<?php echo $iconFill('history'); ?>>history</span>
<span class="font-body-md text-body-md">Trade History</span>
</a>
<div class="pt-lg pb-xs px-md opacity-50 font-label-md text-label-md uppercase tracking-widest">System</div>
<a class="<?php echo $navActive('profile'); ?>" href="/dashboard/user/profile">
<span class="material-symbols-outlined"<?php echo $iconFill('profile'); ?>>settings</span>
<span class="font-body-md text-body-md">System Settings</span>
</a>
<a class="<?php echo $navActive('kyc'); ?>" href="/dashboard/user/kyc">
<span class="material-symbols-outlined"<?php echo $iconFill('kyc'); ?>>verified_user</span>
<span class="font-body-md text-body-md">KYC</span>
</a>
<a class="<?php echo $navActive('support'); ?>" href="/live_chat">
<span class="material-symbols-outlined"<?php echo $iconFill('support'); ?>>contact_support</span>
<span class="font-body-md text-body-md">Support</span>
</a>
</nav>
<div class="px-md mt-auto pt-md space-y-sm shrink-0" style="padding-bottom: env(safe-area-inset-bottom, 0px);">
<a href="/dashboard/user/wallet?action=deposit" class="block w-full bg-fidelity-green text-white font-bold py-sm rounded shadow-sm hover:opacity-90 transition-all active:scale-95 text-center text-sm">
Deposit Funds
</a>
<button type="button" data-logout class="w-full flex items-center justify-center gap-xs text-on-surface-variant hover:text-error transition-colors py-xs text-sm font-semibold">
<span class="material-symbols-outlined text-sm">logout</span>
<span class="font-label-md text-label-md">Sign Out</span>
</button>
</div>
</aside>
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

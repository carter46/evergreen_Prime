<?php
/**
 * Shared admin sidebar — Fidelity light theme (matches user dashboard).
 */
$current = $currentPage ?? '';
$siteName = $siteName ?? get_site_name();
$navClass = function ($page) use ($current) {
    if ($current === $page) {
        return 'flex items-center gap-sm px-md py-2 rounded text-primary font-bold border-r-4 border-primary bg-surface-container-low transition-all duration-150';
    }
    return 'flex items-center gap-sm px-md py-2 rounded text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors duration-200';
};
$iconFill = function ($page) use ($current) {
    return $current === $page ? " style=\"font-variation-settings: 'FILL' 1;\"" : '';
};
?>
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/40 z-[55] lg:hidden hidden" aria-hidden="true"></div>
<aside id="admin-sidebar" class="h-screen w-64 fixed left-0 top-0 bg-surface-container-lowest border-r border-surface-gray flex flex-col py-md z-[60] transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
<div class="px-md mb-lg shrink-0">
<h1 class="text-xl font-bold text-fidelity-green leading-tight font-hanken truncate"><?php echo htmlspecialchars($siteName); ?></h1>
<p class="font-label-md text-label-md text-on-surface-variant opacity-70 mt-0.5">Admin Console</p>
</div>
<nav class="flex-1 min-h-0 px-sm space-y-1 overflow-y-auto admin-scrollbar overscroll-contain">
<a class="<?php echo $navClass('dashboard'); ?>" href="/dashboard/admin">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('dashboard'); ?>>dashboard</span>
<span class="font-body-md text-body-md truncate">Command Center</span>
</a>
<a class="<?php echo $navClass('users'); ?>" href="/dashboard/admin/users">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('users'); ?>>group</span>
<span class="font-body-md text-body-md truncate">User Management</span>
</a>
<a class="<?php echo $navClass('plans'); ?>" href="/dashboard/admin/plans">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('plans'); ?>>account_tree</span>
<span class="font-body-md text-body-md truncate">Plan Management</span>
</a>
<a class="<?php echo $navClass('addresses'); ?>" href="/dashboard/admin/addresses">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('addresses'); ?>>account_balance_wallet</span>
<span class="font-body-md text-body-md truncate">Payment Methods</span>
</a>
<a class="<?php echo $navClass('transactions'); ?>" href="/dashboard/admin/transactions">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('transactions'); ?>>history</span>
<span class="font-body-md text-body-md truncate">Transactions</span>
</a>
<a class="<?php echo $navClass('audit-log'); ?>" href="/dashboard/admin/audit-log">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('audit-log'); ?>>policy</span>
<span class="font-body-md text-body-md truncate">Audit Log</span>
</a>
<a class="<?php echo $navClass('kyc'); ?>" href="/dashboard/admin/kyc">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('kyc'); ?>>verified_user</span>
<span class="font-body-md text-body-md truncate">KYC Management</span>
</a>
<a class="<?php echo $navClass('ai'); ?>" href="/dashboard/admin/ai-config">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('ai'); ?>>smart_toy</span>
<span class="font-body-md text-body-md truncate">AI Bot Config</span>
</a>
<a class="<?php echo $navClass('settings'); ?>" href="/dashboard/admin/settings">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('settings'); ?>>settings</span>
<span class="font-body-md text-body-md truncate">Settings</span>
</a>
<a class="<?php echo $navClass('communication'); ?>" href="/dashboard/admin/communication">
<span class="material-symbols-outlined text-[22px] shrink-0"<?php echo $iconFill('communication'); ?>>hub</span>
<span class="font-body-md text-body-md truncate">Communication Hub</span>
</a>
</nav>
<div class="px-md mt-auto pt-md shrink-0" style="padding-bottom: env(safe-area-inset-bottom, 0px);">
<button type="button" data-logout class="w-full flex items-center justify-center gap-xs text-error hover:text-error/80 transition-colors py-1.5 text-sm font-semibold">
<span class="material-symbols-outlined text-sm">logout</span>
<span class="font-label-md text-label-md">Sign Out</span>
</button>
</div>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.getElementById('admin-sidebar-overlay');
  var sidebar = document.getElementById('admin-sidebar');
  var toggleBtn = document.getElementById('admin-sidebar-toggle');
  function open() {
    if (sidebar) sidebar.classList.remove('-translate-x-full');
    if (overlay) overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
  function close() {
    if (sidebar) sidebar.classList.add('-translate-x-full');
    if (overlay) overlay.classList.add('hidden');
    document.body.style.overflow = '';
  }
  if (overlay) overlay.addEventListener('click', close);
  if (toggleBtn) toggleBtn.addEventListener('click', function () {
    if (sidebar && sidebar.classList.contains('-translate-x-full')) open();
    else close();
  });
  if (sidebar) {
    sidebar.querySelectorAll('a, button[data-logout]').forEach(function (el) {
      el.addEventListener('click', function () { if (window.innerWidth < 1024) close(); });
    });
  }
});
</script>

<?php
/**
 * Shared admin sidebar — institutional dark layout.
 */
$current = $currentPage ?? '';
$siteName = $siteName ?? get_site_name();
$navClass = function ($page) use ($current) {
    if ($current === $page) {
        return 'admin-sidebar-active flex items-center gap-3 px-4 py-3 text-primary-container font-bold border-r-2 border-primary-container bg-surface-container-high transition-transform active:scale-[0.98]';
    }
    return 'flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors duration-200 rounded-lg';
};
?>
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/60 z-[55] lg:hidden hidden" aria-hidden="true"></div>
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 border-r border-low bg-surface-dim flex flex-col py-6 z-[60] transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out overflow-hidden">
<div class="px-6 mb-10 shrink-0">
<h1 class="font-headline-md text-headline-md font-bold text-primary-container tracking-tight truncate"><?php echo htmlspecialchars($siteName); ?></h1>
<p class="font-label-sm text-label-sm text-on-surface-variant opacity-60">Institutional Account</p>
</div>
<nav class="flex-1 flex flex-col gap-1 px-2 min-h-0 overflow-y-auto admin-scrollbar overscroll-contain">
<a class="<?php echo $navClass('dashboard'); ?>" href="/dashboard/admin">
<span class="material-symbols-outlined text-[20px] shrink-0">dashboard</span>
<span class="font-label-sm text-label-sm truncate">Command Center</span>
</a>
<a class="<?php echo $navClass('users'); ?>" href="/dashboard/admin/users">
<span class="material-symbols-outlined text-[20px] shrink-0">group</span>
<span class="font-label-sm text-label-sm truncate">User Management</span>
</a>
<a class="<?php echo $navClass('plans'); ?>" href="/dashboard/admin/plans">
<span class="material-symbols-outlined text-[20px] shrink-0">account_tree</span>
<span class="font-label-sm text-label-sm truncate">Plan Management</span>
</a>
<a class="<?php echo $navClass('addresses'); ?>" href="/dashboard/admin/addresses">
<span class="material-symbols-outlined text-[20px] shrink-0">account_balance_wallet</span>
<span class="font-label-sm text-label-sm truncate">Wallet Addresses</span>
</a>
<a class="<?php echo $navClass('transactions'); ?>" href="/dashboard/admin/transactions">
<span class="material-symbols-outlined text-[20px] shrink-0">history</span>
<span class="font-label-sm text-label-sm truncate">Transactions</span>
</a>
<a class="<?php echo $navClass('kyc'); ?>" href="/dashboard/admin/kyc">
<span class="material-symbols-outlined text-[20px] shrink-0">verified_user</span>
<span class="font-label-sm text-label-sm truncate">KYC Management</span>
</a>
<a class="<?php echo $navClass('ai'); ?>" href="/dashboard/admin/ai-config">
<span class="material-symbols-outlined text-[20px] shrink-0">smart_toy</span>
<span class="font-label-sm text-label-sm truncate">AI Bot Config</span>
</a>
<a class="<?php echo $navClass('settings'); ?>" href="/dashboard/admin/settings">
<span class="material-symbols-outlined text-[20px] shrink-0">settings</span>
<span class="font-label-sm text-label-sm truncate">Settings</span>
</a>
<a class="<?php echo $navClass('communication'); ?>" href="/dashboard/admin/communication">
<span class="material-symbols-outlined text-[20px] shrink-0">hub</span>
<span class="font-label-sm text-label-sm truncate">Communication Hub</span>
</a>
</nav>
<div class="px-2 mt-auto shrink-0 pt-4 border-t border-low">
<button type="button" data-logout class="flex items-center gap-3 px-4 py-3 text-critical hover:bg-error-container/10 transition-colors duration-200 w-full rounded-lg">
<span class="material-symbols-outlined text-[20px] shrink-0">logout</span>
<span class="font-label-sm text-label-sm">Sign Out</span>
</button>
</div>
</aside>
<style>
#admin-sidebar {
  height: 100vh;
  height: 100svh;
  height: 100dvh;
  padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 8px);
}
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #111417; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #323538; border-radius: 10px; }
</style>
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

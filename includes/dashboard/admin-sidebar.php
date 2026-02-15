<?php
/**
 * Bloombit - Shared Admin Sidebar
 * Use on all admin dashboard pages. Pass $currentPage to highlight active item.
 */
$current = $currentPage ?? '';
$navClass = function ($page) use ($current) {
    return ($current === $page)
        ? 'flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-sm'
        : 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-primary/10 hover:text-primary transition-colors rounded-lg';
};
?>
<!-- Mobile overlay -->
<div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" aria-hidden="true"></div>
<!-- Sidebar: fixed so it stays visible when scrolling -->
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-black/20 border-r border-primary/10 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out h-screen">
    <a class="p-6 flex items-center gap-3" href="/">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-icons text-white">bolt</span>
        </div>
        <h1 class="font-bold text-xl tracking-tight">Bloom<span class="text-primary">bit</span></h1>
    </a>
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <a class="<?php echo $navClass('dashboard'); ?>" href="/dashboard/admin">
            <span class="material-icons text-[20px]">dashboard</span>
            <span class="font-medium">Command Center</span>
        </a>
        <a class="<?php echo $navClass('users'); ?>" href="/dashboard/admin/users">
            <span class="material-icons text-[20px]">people</span>
            <span class="font-medium">User Management</span>
        </a>
        <a class="<?php echo $navClass('plans'); ?>" href="/dashboard/admin/plans">
            <span class="material-icons text-[20px]">account_balance_wallet</span>
            <span class="font-medium">Plan Management</span>
        </a>
        <a class="<?php echo $navClass('addresses'); ?>" href="/dashboard/admin/addresses">
            <span class="material-icons text-[20px]">wallet</span>
            <span class="font-medium">Wallet Addresses</span>
        </a>
        <a class="<?php echo $navClass('transactions'); ?>" href="/dashboard/admin">
            <span class="material-icons text-[20px]">receipt_long</span>
            <span class="font-medium">Transactions</span>
        </a>
        <a class="<?php echo $navClass('ai'); ?>" href="/dashboard/admin">
            <span class="material-icons text-[20px]">smart_toy</span>
            <span class="font-medium">AI Bot Config</span>
        </a>
    </nav>
    <div class="p-4 border-t border-primary/10 space-y-1">
        <a class="<?php echo $navClass('communication'); ?>" href="/dashboard/admin/communication">
            <span class="material-icons text-[20px]">campaign</span>
            <span class="font-medium">Communication Hub</span>
        </a>
        <button type="button" data-logout class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors w-full mt-2">
            <span class="material-icons text-[20px]">logout</span>
            <span class="font-medium">Sign Out</span>
        </button>
    </div>
</aside>
<div class="hidden lg:block w-64 shrink-0 flex-none" aria-hidden="true"></div>
<script>
(function(){
    var overlay=document.getElementById('admin-sidebar-overlay');
    var sidebar=document.getElementById('admin-sidebar');
    var toggleBtn=document.getElementById('admin-sidebar-toggle');
    function open(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); document.body.style.overflow='hidden'; }
    function close(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); document.body.style.overflow=''; }
    if(overlay) overlay.addEventListener('click',close);
    if(toggleBtn) toggleBtn.addEventListener('click',function(){ sidebar.classList.contains('-translate-x-full')?open():close(); });
    document.querySelectorAll('#admin-sidebar a, #admin-sidebar button').forEach(function(el){ el.addEventListener('click',function(){ if(window.innerWidth<1024) close(); }); });
})();
</script>

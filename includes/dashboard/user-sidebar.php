<?php
/**
 * Bloombit - Shared User Sidebar
 * Use on all user dashboard pages. Pass $currentPage to highlight active item.
 */
$current = $currentPage ?? '';
$navClass = function ($page) use ($current) {
    return ($current === $page)
        ? 'flex items-center gap-3 px-4 py-3 bg-primary text-black font-semibold rounded-xl transition-all'
        : 'flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-primary/10 hover:text-primary rounded-xl transition-all';
};
?>
<!-- Mobile overlay -->
<div id="user-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" aria-hidden="true"></div>
<!-- Sidebar: fixed so it stays visible when scrolling -->
<aside id="user-sidebar" class="fixed inset-y-0 left-0 w-64 border-r border-primary/10 bg-white/50 dark:bg-background-dark/50 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out h-screen">
    <a class="p-6 flex items-center gap-3" href="/">
        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20">
            <span class="material-icons-round text-white">bolt</span>
        </div>
        <span class="text-2xl font-bold tracking-tight">Bloombit</span>
    </a>
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        <a class="<?php echo $navClass('dashboard'); ?>" href="/dashboard/user/dashboard">
            <span class="material-icons-round text-[20px]">grid_view</span>
            Dashboard
        </a>
        <a class="<?php echo $navClass('wallet'); ?>" href="/dashboard/user/wallet">
            <span class="material-icons-round text-[20px]">account_balance_wallet</span>
            Wallet
        </a>
        <a class="<?php echo $navClass('analytics'); ?>" href="/dashboard/user/analytics">
            <span class="material-icons-round text-[20px]">insights</span>
            My Investments
        </a>
        <a class="<?php echo $navClass('history'); ?>" href="/dashboard/user/analytics">
            <span class="material-icons-round text-[20px]">history</span>
            Trade History
        </a>
        <a class="<?php echo $navClass('profile'); ?>" href="/dashboard/user/profile">
            <span class="material-icons-round text-[20px]">settings</span>
            Settings
        </a>
        <a class="<?php echo $navClass('kyc'); ?>" href="/dashboard/user/kyc">
            <span class="material-icons-round text-[20px]">verified_user</span>
            KYC
        </a>
    </nav>
    <div class="p-6">
        <div class="bg-primary/10 rounded-2xl p-4 border border-primary/20">
            <p class="text-xs font-medium text-primary mb-1 uppercase tracking-wider">Plan Status</p>
            <p class="text-sm font-bold" data-plan-status>Pro Trader AI active</p>
            <div class="mt-3 w-full bg-primary/20 h-1.5 rounded-full">
                <div class="bg-primary h-1.5 rounded-full w-[85%]"></div>
            </div>
        </div>
        <button data-logout class="mt-6 flex items-center gap-3 px-4 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all w-full">
            <span class="material-icons-round text-[20px]">logout</span>
            Sign Out
        </button>
    </div>
</aside>
<div class="hidden lg:block w-64 shrink-0 flex-none" aria-hidden="true"></div>
<script>
(function(){
    var overlay=document.getElementById('user-sidebar-overlay');
    var sidebar=document.getElementById('user-sidebar');
    var toggleBtn=document.getElementById('user-sidebar-toggle');
    function open(){ sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); document.body.style.overflow='hidden'; }
    function close(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); document.body.style.overflow=''; }
    if(overlay) overlay.addEventListener('click',close);
    if(toggleBtn) toggleBtn.addEventListener('click',function(){ sidebar.classList.contains('-translate-x-full')?open():close(); });
    document.querySelectorAll('#user-sidebar a, #user-sidebar button').forEach(function(el){ el.addEventListener('click',function(){ if(window.innerWidth<1024) close(); }); });
})();
</script>

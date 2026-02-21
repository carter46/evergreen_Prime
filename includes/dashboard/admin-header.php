<?php
/**
 * Bloombit - Shared Admin Header (Top Bar)
 * Toggle opens sidebar on mobile/tablet.
 */
?>
<header class="h-14 sm:h-16 bg-white dark:bg-background-dark border-b border-primary/10 flex items-center justify-between gap-2 sm:gap-3 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
        <button type="button" id="admin-sidebar-toggle" class="lg:hidden flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px]" aria-label="Toggle menu">
            <span class="material-icons text-2xl">menu</span>
        </button>
        <div class="relative hidden md:block flex-1 min-w-0 max-w-[180px] lg:max-w-xs">
            <input class="w-full pl-9 pr-3 py-2 bg-background-light dark:bg-white/5 border border-primary/10 rounded-xl text-sm focus:ring-2 focus:ring-primary/50" placeholder="Search..." type="text"/>
            <span class="material-icons absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">search</span>
        </div>
    </div>
    <div class="flex items-center gap-1 sm:gap-2 shrink-0">
        <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-background-light dark:bg-white/5 border border-primary/10 rounded-lg text-xs font-medium cursor-pointer shrink-0">
            <span class="material-icons text-sm">calendar_today</span>
            <span class="truncate max-w-[120px]">Oct 01 - Oct 31, 2023</span>
        </div>
        <button type="button" class="relative w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary rounded-xl hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px]" aria-label="Notifications">
            <span class="material-icons text-xl">notifications</span>
            <span class="absolute top-1 right-1 w-4 h-4 bg-primary text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-background-dark font-bold">12</span>
        </button>
        <div class="flex items-center gap-2 pl-2 sm:pl-3 border-l border-primary/10">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold leading-none truncate">Admin</p>
                <p class="text-[10px] text-slate-500">Super Admin</p>
            </div>
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-primary/20 flex items-center justify-center overflow-hidden shrink-0">
                <img alt="Admin" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAllv2arw4CT5sh5se3HKMFVKyYXOAW1324wKxujkeuffmY1DhhhTUb1llYzAk_sM7va9f_KPJb5zWOKBQD2TJHPAWkHyzECqLiN2iLHvU_rfybow80K5_hH3w4qrMTwioK102J1bJ8_1J9XyNSbcvlSvwXKmpwg-zMnGWXlKkHWg2SGjXf8kRz78h-7YwhWISO8lzfSxTK5-jedWr5c7-8zqU8QckddM_pMegUm6540ceVN0QEQqbK05hVdzt1j25SMveouqEJl9k"/>
            </div>
        </div>
    </div>
</header>

<!-- Translation widget (GTranslate) -->
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/../translation-widget.php'; ?>
<style>
  /* Dashboard-only: move language widget to bottom-right (avoid logout overlap) */
  .gtranslate_wrapper { left: auto !important; right: 20px !important; }
  @media (max-width: 768px) { .gtranslate_wrapper { right: 15px !important; } }
</style>

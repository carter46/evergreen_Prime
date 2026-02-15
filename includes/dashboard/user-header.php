<?php
/**
 * Bloombit - Shared User Header (Top Bar)
 * Use on all user dashboard pages. Renders greeting, crypto ticker, notifications, profile.
 */
?>
<header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-6 mb-6 sm:mb-8">
    <div class="flex items-center gap-3 sm:gap-4 min-w-0">
        <button type="button" id="user-sidebar-toggle" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl hover:bg-primary/10 transition-colors min-h-[44px] min-w-[44px] shrink-0" aria-label="Toggle sidebar">
            <span class="material-icons-round">menu</span>
        </button>
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold truncate">Good morning, Alex</h1>
            <p class="text-slate-500 text-sm">System status: <span class="text-emerald-500 font-medium">AI Core Online</span></p>
        </div>
    </div>
    <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
        <div class="flex gap-2 sm:gap-4">
            <div class="bg-white dark:bg-white/5 border border-primary/10 px-3 sm:px-4 py-2 rounded-xl flex items-center gap-2 sm:gap-3 shadow-sm">
                <span class="text-xs text-slate-400 uppercase font-bold">BTC/USD</span>
                <span class="font-bold text-sm sm:text-base" data-coin="bitcoin" data-price="">--</span>
                <span class="text-xs font-bold crypto-change text-emerald-500" data-coin="bitcoin" data-change="">--</span>
            </div>
        </div>
        <div class="flex items-center gap-3 sm:gap-4 border-l border-slate-200 dark:border-white/10 pl-4">
            <button class="relative w-10 h-10 flex items-center justify-center text-slate-400 hover:text-primary transition-colors rounded-xl hover:bg-primary/10 min-h-[44px] min-w-[44px]">
                <span class="material-icons-round">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
            </button>
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none">Alex Rivera</p>
                    <p class="text-xs text-slate-500">Verified User</p>
                </div>
                <img alt="User" class="w-10 h-10 rounded-full border-2 border-primary shrink-0" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAOC4BkOfAIELCclJ8x7GDF7rJChGJelN25tkIftuO8Gvct9ZmJ7X284HMhELI2rEIOdft7rKTJeJPNEnX6pzMWQuZtPSEMqN5QLBmtq0Kn46y11RrclC4mNabZ-Y5wcp9xD-qcIKBcdpMAku3Yt47oHbk_JPCzHGPN8ciroIDnk7K_kpqPqUfr1GoqxIyhofa4pjGCcfmbzW0pBKoVf9fQVgJKjxLN7ZMdX3BJCTAowB9oTO_kbTEY5jR5C-_TRlPtCGhsTnPsHFw"/>
            </div>
        </div>
    </div>
</header>

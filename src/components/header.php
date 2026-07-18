<?php
/**
 * Common Header / Topbar component
 */
?>
<header class="h-16 border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md flex items-center justify-between px-6 shrink-0 relative z-20">
    <!-- Left: Mobile Menu Button & Page Title -->
    <div class="flex items-center gap-4">
        <!-- Hamburger (Mobile Only) -->
        <button id="mobile-menu-btn" class="md:hidden text-slate-400 hover:text-white transition focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <h2 class="text-lg font-bold tracking-tight text-white md:block hidden">
            Cloud HMS Dashboard
        </h2>
    </div>

    <!-- Right: Status indicator and logging info -->
    <div class="flex items-center gap-4">
        <!-- System Health Badge -->
        <div class="flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Cloud Node Online
        </div>

        <div class="h-8 w-px bg-slate-800"></div>

        <!-- User profile display -->
        <div class="flex items-center gap-2.5">
            <div class="h-8 w-8 rounded-lg bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-teal-300 font-bold text-sm">
                <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="text-left hidden sm:block">
                <p class="text-xs font-semibold text-white leading-none"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></p>
                <span class="text-[9px] text-slate-500 uppercase leading-none font-bold"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 opacity-0 pointer-events-none transition-opacity duration-300"></div>
<div id="mobile-menu-drawer" class="fixed inset-y-0 left-0 w-64 bg-slate-900 border-r border-slate-800 z-40 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between">
    <div>
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 bg-teal-500/20 border border-teal-500/30 rounded-lg flex items-center justify-center">
                    <svg class="h-4 w-4 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 10.5V20a2 2 0 01-2 2H7a2 2 0 01-2-2v-9.5m14 0V9a2 2 0 00-2-2h-2m3 3.5V5a2 2 0 00-2-2h-3m-6 3.5V9a2 2 0 00-2-2H7m3 3.5V5a2 2 0 00-2-2H7m5 4v12m0 0l-3-3m3 3l3-3" />
                    </svg>
                </div>
                <span class="font-bold text-sm text-white">Cloud HMS</span>
            </div>
            <button id="close-mobile-menu" class="text-slate-400 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            <a href="appointments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium">Appointments</span>
            </a>
            <a href="records.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/40 hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium">Medical Records</span>
            </a>
        </nav>
    </div>
    
    <div class="p-4 border-t border-slate-800">
        <a href="logout.php" class="flex items-center justify-center gap-2 w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 py-3 rounded-xl border border-red-500/20 text-sm font-semibold transition">
            Log Out
        </a>
    </div>
</div>

<script>
    // Mobile Drawer Interactions
    const menuBtn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('close-mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const drawer = document.getElementById('mobile-menu-drawer');

    function toggleMenu() {
        drawer.classList.toggle('-translate-x-full');
        overlay.classList.toggle('opacity-0');
        overlay.classList.toggle('pointer-events-none');
    }

    if(menuBtn) menuBtn.addEventListener('click', toggleMenu);
    if(closeBtn) closeBtn.addEventListener('click', toggleMenu);
    if(overlay) overlay.addEventListener('click', toggleMenu);
</script>

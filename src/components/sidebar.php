<?php
/**
 * Unified Sidebar Navigation Component (Responsive and Glassmorphism styled)
 */

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'PATIENT';
?>
<!-- Sidebar Container -->
<aside class="w-64 bg-slate-900/80 border-r border-slate-800/80 flex flex-col justify-between shrink-0 hidden md:flex min-h-screen">
    <div>
        <!-- Brand logo -->
        <div class="p-6 border-b border-slate-800/80 flex items-center gap-3">
            <div class="h-10 w-10 bg-teal-500/20 border border-teal-500/30 rounded-xl flex items-center justify-center shadow-md">
                <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 10.5V20a2 2 0 01-2 2H7a2 2 0 01-2-2v-9.5m14 0V9a2 2 0 00-2-2h-2m3 3.5V5a2 2 0 00-2-2h-3m-6 3.5V9a2 2 0 00-2-2H7m3 3.5V5a2 2 0 00-2-2H7m5 4v12m0 0l-3-3m3 3l3-3" />
                </svg>
            </div>
            <div>
                <span class="font-bold text-sm bg-gradient-to-r from-teal-200 to-emerald-400 bg-clip-text text-transparent block">Cloud HMS</span>
                <span class="text-[10px] text-slate-500 tracking-wider font-semibold uppercase"><?php echo htmlspecialchars($role); ?> Portal</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-2">
            <!-- Dashboard Link -->
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo $current_page === 'dashboard.php' ? 'bg-teal-500/20 text-teal-200 border-l-4 border-teal-500' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'; ?>">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span class="text-sm font-medium">Dashboard</span>
            </a>

            <!-- Appointments Link -->
            <a href="appointments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo $current_page === 'appointments.php' ? 'bg-teal-500/20 text-teal-200 border-l-4 border-teal-500' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'; ?>">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium">Appointments</span>
            </a>

            <!-- Medical Records Link -->
            <a href="records.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo $current_page === 'records.php' ? 'bg-teal-500/20 text-teal-200 border-l-4 border-teal-500' : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'; ?>">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium">Medical Records</span>
            </a>
        </nav>
    </div>

    <!-- User Section / Log Out -->
    <div class="p-4 border-t border-slate-800/80">
        <div class="bg-slate-900 p-3 rounded-xl border border-slate-800 flex items-center justify-between">
            <div class="truncate mr-2">
                <p class="text-xs font-semibold text-slate-300 truncate"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'HMS User'); ?></p>
                <span class="text-[9px] bg-teal-500/20 text-teal-300 font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 inline-block">
                    <?php echo htmlspecialchars($role); ?>
                </span>
            </div>
            <a href="logout.php" title="Log Out" class="text-slate-500 hover:text-red-400 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </a>
        </div>
    </div>
</aside>

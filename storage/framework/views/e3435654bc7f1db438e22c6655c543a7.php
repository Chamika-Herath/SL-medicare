<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title'); ?> - SL Medicare Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        hospital: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563EB', /* Neo Solution Primary Blue */
                            600: '#1d4ed8',
                            700: '#1e40af',
                            900: '#0F172A',
                            dark: '#090d16' /* Full Dark background */
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #090d16;
            color: #f1f5f9;
        }
        .clean-card {
            background-color: rgba(19, 26, 38, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }
        .glow-border:hover {
            border-color: #2563EB;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex overflow-hidden bg-[#090d16]">

    <!-- Page Layout Wrapper -->
    <div class="flex w-full h-screen overflow-hidden">
        
        <!-- ========================================== -->
        <!-- SIDEBAR NAVIGATION (Dark Theme)           -->
        <!-- ========================================== -->
        <?php
            $current_page = Route::currentRouteName();
            $user = Auth::user();
            $role = $user->role ?? 'PATIENT';
            $fullName = $user->profile->full_name ?? 'HMS User';
        ?>
        <aside class="w-64 bg-[#0d131f] border-r border-slate-800/80 flex flex-col justify-between shrink-0 hidden md:flex min-h-screen shadow-lg">
            <div>
                <!-- Brand logo (Hexagon Heartbeat Logo) -->
                <a href="/" class="p-6 border-b border-slate-800/80 flex items-center gap-3 hover:bg-slate-800/20 transition group">
                    <div class="h-10 w-10 flex items-center justify-center transform group-hover:scale-105 transition duration-200">
                        <svg class="h-full w-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50 7 L88 29 V71 L50 93 L12 71 V29 Z" stroke="url(#logo-grad-sidebar)" stroke-width="6" stroke-linejoin="round" fill="rgba(37, 99, 235, 0.1)"/>
                            <path d="M26 50 H39 L44 32 L50 68 L56 42 L61 50 H74" stroke="#38BDF8" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                            <defs>
                                <linearGradient id="logo-grad-sidebar" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#38BDF8" />
                                    <stop offset="100%" stop-color="#2563EB" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div>
                        <span class="font-extrabold text-sm text-white block group-hover:text-brand-sky transition">SL Medicare</span>
                        <span class="text-[9px] text-hospital-500 font-bold uppercase tracking-wider"><?php echo e($role); ?> Portal</span>
                    </div>
                </a>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-2">
                    <?php if($role === 'ADMIN'): ?>
                        <!-- Admin Dashboard Link -->
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'dashboard' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>

                        <!-- Admin Doctor Registry Link -->
                        <a href="<?php echo e(route('admin.doctors')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'admin.doctors' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium">Doctor Registry</span>
                        </a>

                        <!-- Admin Patient Registry Link -->
                        <a href="<?php echo e(route('admin.patients')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'admin.patients' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-sm font-medium">Patient Registry</span>
                        </a>

                        <!-- Admin Appointments Link -->
                        <a href="<?php echo e(route('appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'appointments' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Appointments</span>
                        </a>

                    <?php elseif($role === 'DOCTOR'): ?>
                        <!-- Doctor Dashboard Link -->
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'dashboard' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>

                        <!-- Doctor Appointment History Link -->
                        <a href="<?php echo e(route('doctor.appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'doctor.appointments' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">Appointment History</span>
                        </a>

                        <!-- Doctor Patient Search Link -->
                        <a href="<?php echo e(route('doctor.patients')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'doctor.patients' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span class="text-sm font-medium">Patient Search & Logs</span>
                        </a>

                        <!-- Doctor Manage Records Link -->
                        <a href="<?php echo e(route('records')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'records' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm font-medium">Add Medical Record</span>
                        </a>

                    <?php else: ?>
                        <!-- Patient Dashboard Link -->
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'dashboard' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>

                        <!-- Patient Book Appointment Link -->
                        <a href="<?php echo e(route('appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'appointments' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Book Appointment</span>
                        </a>

                        <!-- Patient My History Link -->
                        <a href="<?php echo e(route('records')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 <?php echo e($current_page === 'records' ? 'bg-hospital-500/10 text-white border-l-4 border-hospital-500 font-semibold' : 'text-slate-400 hover:bg-slate-800/50 hover:text-white'); ?>">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm font-medium">My Medical History</span>
                        </a>
                    <?php endif; ?>

                    <!-- Back to Home Link (All Roles) -->
                    <div class="pt-2 mt-2 border-t border-slate-800/60">
                        <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="text-sm font-medium">Go to Home</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- User Section / Log Out -->
            <div class="p-4 border-t border-slate-800/80">
                <div class="bg-[#121926]/80 p-3 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div class="truncate mr-2">
                        <p class="text-xs font-semibold text-slate-200 truncate"><?php echo e($fullName); ?></p>
                        <span class="text-[9px] bg-blue-500/20 text-brand-sky font-extrabold px-1.5 py-0.5 rounded uppercase mt-0.5 inline-block border border-blue-500/20">
                            <?php echo e($role); ?>

                        </span>
                    </div>
                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" title="Log Out" class="text-slate-400 hover:text-red-400 transition align-middle">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ========================================== -->
        <!-- MAIN CONTENT CONTAINER                     -->
        <!-- ========================================== -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-[#090d16]">
            <!-- Header Topbar -->
            <header class="h-16 border-b border-slate-800/80 bg-[#0d131f]/90 backdrop-blur-md flex items-center justify-between px-6 shrink-0 relative z-20 shadow-md">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="md:hidden text-slate-400 hover:text-white transition focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-bold tracking-tight text-white md:block hidden">
                        SL Medicare Dashboard
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-brand-sky font-bold text-sm">
                            <?php echo e(strtoupper(substr($fullName, 0, 1))); ?>

                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-semibold text-slate-200 leading-none"><?php echo e($fullName); ?></p>
                            <span class="text-[9px] text-slate-400 uppercase leading-none font-bold"><?php echo e($role); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Core Viewport -->
            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MOBILE DRAWER ROUTING                     -->
    <!-- ========================================== -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 opacity-0 pointer-events-none transition-opacity duration-300"></div>
    <div id="mobile-menu-drawer" class="fixed inset-y-0 left-0 w-64 bg-[#0d131f] border-r border-slate-800 z-40 transform -translate-x-full transition-transform duration-300 flex flex-col justify-between shadow-2xl">
        <div>
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 flex items-center justify-center">
                        <svg class="h-full w-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50 7 L88 29 V71 L50 93 L12 71 V29 Z" stroke="#38BDF8" stroke-width="6" fill="rgba(37, 99, 235, 0.1)"/>
                            <path d="M26 50 H39 L44 32 L50 68 L56 42 L61 50 H74" stroke="#ffffff" stroke-width="6"/>
                        </svg>
                    </div>
                    <span class="font-bold text-sm text-white">SL Medicare</span>
                </div>
                <button id="close-mobile-menu" class="text-slate-400 hover:text-white transition">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="p-4 space-y-2">
                <?php if($role === 'ADMIN'): ?>
                    <!-- Admin Dashboard Link -->
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <!-- Admin Doctor Registry Link -->
                    <a href="<?php echo e(route('admin.doctors')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm font-medium">Doctor Registry</span>
                    </a>

                    <!-- Admin Patient Registry Link -->
                    <a href="<?php echo e(route('admin.patients')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="text-sm font-medium">Patient Registry</span>
                    </a>

                    <!-- Admin Appointments Link -->
                    <a href="<?php echo e(route('appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm font-medium">Appointments</span>
                    </a>

                <?php elseif($role === 'DOCTOR'): ?>
                    <!-- Doctor Dashboard Link -->
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <!-- Doctor Appointment History Link -->
                    <a href="<?php echo e(route('doctor.appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">Appointment History</span>
                    </a>

                    <!-- Doctor Patient Search Link -->
                    <a href="<?php echo e(route('doctor.patients')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-sm font-medium">Patient Search & Logs</span>
                    </a>

                    <!-- Doctor Manage Records Link -->
                    <a href="<?php echo e(route('records')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium">Add Medical Record</span>
                    </a>

                <?php else: ?>
                    <!-- Patient Dashboard Link -->
                    <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <!-- Patient Book Appointment Link -->
                    <a href="<?php echo e(route('appointments')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm font-medium">Book Appointment</span>
                    </a>

                    <!-- Patient My History Link -->
                    <a href="<?php echo e(route('records')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium">My Medical History</span>
                    </a>
                <?php endif; ?>

                <!-- Back to Home Link (All Roles) -->
                <div class="pt-2 mt-2 border-t border-slate-800/60">
                    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/50 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-sm font-medium">Go to Home</span>
                    </a>
                </div>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-800">
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="flex items-center justify-center gap-2 w-full bg-red-950/20 hover:bg-red-950/40 text-red-400 py-3 rounded-xl border border-red-900/30 text-sm font-semibold transition">
                    Log Out
                </button>
            </form>
        </div>
    </div>

    <script>
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
</body>
</html>
<?php /**PATH C:\Users\hmcdi\OneDrive\Documents\GITHUB\SL-medicare\resources\views/layouts/portal.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SL Medicare - Premium Private Hospital</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#2563EB',      /* Neo Solution Primary Blue */
                            azure: '#3B82F6',     /* Neo Solution Secondary Blue */
                            sky: '#38BDF8',       /* Neo Solution Accent Sky Blue */
                            dark: '#090d16',      /* SL Medicare Dark Slate Background */
                            panel: '#0d131f',     /* Dark Panel Slate */
                            muted: '#64748B',     /* Gray Muted */
                            border: '#E2E8F0',    /* Standard Border */
                            light: '#F8FAFC'      /* Light Mode BG */
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --nav-bg: rgba(9, 13, 22, 0.85);
            --nav-bg-scrolled: rgba(8, 9, 18, 0.95);
            --text-main: #f1f3f9;
            --brand-blue: #2563EB;
            --brand-sky: #38BDF8;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        /* Fixed Translucent Navbar */
        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 90;
            background: var(--nav-bg);
            backdrop-filter: blur(18px) saturate(160%);
            -webkit-backdrop-filter: blur(18px) saturate(160%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .site-navbar.scrolled {
            background: var(--nav-bg-scrolled);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45);
        }

        /* Modern split hero header layout */
        .hero-section {
            background-color: #090d16;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(0.5deg); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .clean-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .clean-card:hover {
            border-color: #2563EB;
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.1), 0 10px 10px -5px rgba(37, 99, 235, 0.04);
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 overflow-x-hidden relative bg-[#f8fafc]">

    <!-- ========================================== -->
    <!-- STICKY TRANS-DARK NAVBAR                   -->
    <!-- ========================================== -->
    <nav class="site-navbar" id="siteNavbar">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Brand Logo (Hexagon Heartbeat Logo) -->
            <a href="/" class="flex items-center gap-3 group cursor-pointer">
                <div class="h-12 w-12 rounded-2xl flex items-center justify-center transform group-hover:scale-105 transition duration-300">
                    <svg class="h-full w-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M50 7 L88 29 V71 L50 93 L12 71 V29 Z" stroke="url(#logo-grad-welcome)" stroke-width="6" stroke-linejoin="round" fill="rgba(37, 99, 235, 0.15)"/>
                        <path d="M26 50 H39 L44 32 L50 68 L56 42 L61 50 H74" stroke="#38BDF8" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" class="animate-pulse"/>
                        <defs>
                            <linearGradient id="logo-grad-welcome" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#2563EB" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-md tracking-tight text-white block">SL Medicare</span>
                    <span class="text-[9px] text-brand-sky font-bold uppercase tracking-wider">Advanced Private Hospital</span>
                </div>
            </a>

            <!-- Menu Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="#services" class="text-slate-300 hover:text-white transition duration-200">Medical Services</a>
                <a href="#about" class="text-slate-300 hover:text-white transition duration-200">About Us</a>
                <a href="#technologies" class="text-slate-300 hover:text-white transition duration-200">Cloud Features</a>
                <a href="#contact" class="text-slate-300 hover:text-white transition duration-200">Contact</a>
            </nav>

            <!-- Action Button -->
            <div class="flex items-center gap-4">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="bg-brand-blue hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                        <span>Dashboard</span>
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="border border-white/20 hover:border-brand-sky bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-5 py-2.5 rounded-xl flex items-center gap-2 transform hover:scale-105 transition duration-200 shadow-sm">
                        <span>Login</span>
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ========================================== -->
    <!-- GEOMETRIC SPLIT-SCREEN HERO SECTION        -->
    <!-- ========================================== -->
    <section class="hero-section">
        
        <!-- Ambient radial background glows -->
        <div class="absolute w-[500px] h-[500px] rounded-full bg-blue-500/10 blur-[120px] top-[-100px] left-[-100px] -z-10 animate-pulse"></div>
        <div class="absolute w-[400px] h-[400px] rounded-full bg-teal-500/5 blur-[100px] bottom-[-50px] right-[20%] -z-10"></div>

        <div class="max-w-7xl mx-auto px-6 py-24 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full z-10">
            
            <!-- Left Half: Branding & Typography Copy -->
            <div class="lg:col-span-6 space-y-6 text-left animate-slide-up">
                <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 text-brand-sky text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-brand-sky animate-ping"></span>
                    Integrated Portal v1.2
                </div>
                
                <h1 class="text-4xl md:text-5.5xl tracking-tight text-white leading-[1.15] font-sans" style="font-family: 'Poppins', sans-serif;">
                    <span class="font-extrabold block">Communication</span>
                    <span class="font-light text-slate-350">that drives</span>
                    <span class="font-extrabold bg-gradient-to-r from-brand-sky via-blue-400 to-brand-blue bg-clip-text text-transparent block">healthcare innovation.</span>
                </h1>
                
                <p class="text-sm md:text-md text-slate-400 font-medium max-w-xl leading-relaxed">
                    Connect patients to specialists instantly with enterprise-grade clinical delivery speeds and secure, Cloudinary-powered diagnostic scan storage archives.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('dashboard')); ?>" class="bg-brand-blue hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-blue-600/20 transform hover:-translate-y-1 transition duration-300 text-sm">
                            Enter Portal
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="bg-brand-blue hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-blue-600/20 transform hover:-translate-y-1 transition duration-300 text-sm">
                            Sign In to Portal
                        </a>
                        <a href="<?php echo e(route('register')); ?>" class="bg-white/10 hover:bg-white/20 border border-white/20 hover:border-brand-sky text-white font-bold px-8 py-4 rounded-2xl transition duration-200 text-sm shadow-sm transform hover:-translate-y-0.5 backdrop-blur-sm">
                            Create Account
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Bottom Stats -->
                <div class="grid grid-cols-3 gap-6 pt-10 border-t border-white/10 max-w-sm w-full">
                    <div>
                        <span class="text-3xl font-extrabold text-brand-sky block">50+</span>
                        <span class="text-[9px] text-slate-450 uppercase tracking-widest font-bold">Specialists</span>
                    </div>
                    <div>
                        <span class="text-3xl font-extrabold text-blue-400 block">15K+</span>
                        <span class="text-[9px] text-slate-450 uppercase tracking-widest font-bold">Patients</span>
                    </div>
                    <div>
                        <span class="text-3xl font-extrabold text-white block">99.9%</span>
                        <span class="text-[9px] text-slate-450 uppercase tracking-widest font-bold">Uptime</span>
                    </div>
                </div>
            </div>

            <!-- Right Half: Unique Masked Arched Viewport Video Frame -->
            <div class="lg:col-span-6 relative w-full flex items-center justify-center lg:justify-end animate-fade-in" style="animation-delay: 0.2s">
                <!-- Circular background glow behind arch -->
                <div class="absolute w-72 h-72 rounded-full bg-blue-500/20 blur-[60px] -z-10"></div>
                
                <!-- Arched geometric video pane (Capsule/Arch modern look) -->
                <div class="w-full max-w-sm aspect-[4/5] rounded-t-[180px] rounded-b-3xl border border-white/15 overflow-hidden shadow-2xl relative bg-slate-950/80 animate-float shadow-blue-500/10">
                    <video class="w-full h-full object-cover filter brightness-[0.7] saturate-[1.10]" autoplay loop muted playsinline>
                        <source src="<?php echo e(asset('assets/videos/hero.mp4')); ?>" type="video/mp4">
                        <source src="https://assets.mixkit.co/videos/preview/mixkit-doctor-checking-a-brain-scan-on-a-tablet-40160-large.mp4" type="video/mp4">
                    </video>
                    <!-- Bottom glass card indicator floating inside video -->
                    <div class="absolute bottom-4 left-4 right-4 bg-slate-950/75 backdrop-blur-md border border-white/10 p-3.5 rounded-2xl shadow flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="text-[9px] font-bold text-slate-300 uppercase tracking-wider">Syncing Live Scans</span>
                        </div>
                        <span class="text-[9px] bg-brand-blue text-white font-extrabold px-2 py-0.5 rounded uppercase">Cloud Node</span>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- ========================================== -->
    <!-- SERVICES SECTION (Clean Light Mode)        -->
    <!-- ========================================== -->
    <section id="services" class="max-w-7xl mx-auto px-6 py-20 bg-[#f8fafc]">
        <div class="text-center space-y-3 max-w-xl mx-auto mb-16">
            <h2 class="text-xs font-bold text-brand-blue uppercase tracking-widest">Medical Departments</h2>
            <p class="text-3xl font-extrabold text-brand-dark">Advanced Medical Specialties</p>
            <p class="text-sm text-brand-muted font-medium">Equipped with state-of-the-art diagnostic utilities and cloud workflows.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Cardiology -->
            <div class="clean-card bg-white p-6 rounded-2xl space-y-4 relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-brand-blue to-brand-sky opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-blue shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-brand-dark">Cardiology</h3>
                <p class="text-xs text-brand-muted leading-relaxed font-semibold">
                    Precision heart diagnostic imaging, ECG consulting, and cardiac surgeries backed by unified electronic patient logs.
                </p>
            </div>

            <!-- Neurology -->
            <div class="clean-card bg-white p-6 rounded-2xl space-y-4 relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-emerald-500 to-teal-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="h-12 w-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-655 shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-brand-dark">Neurology</h3>
                <p class="text-xs text-brand-muted leading-relaxed font-semibold">
                    Advanced brain scans analysis, CT imaging evaluations, sleep medicine, and neurological consulting.
                </p>
            </div>

            <!-- Diagnostic Scans -->
            <div class="clean-card bg-white p-6 rounded-2xl space-y-4 relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-indigo-500 to-violet-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="h-12 w-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-650 shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-brand-dark">Diagnostic Imaging</h3>
                <p class="text-xs text-brand-muted leading-relaxed font-semibold">
                    High-definition MRI and X-ray imaging uploads linked directly to secure Cloud Object Storage archives.
                </p>
            </div>

            <!-- Emergency Care -->
            <div class="clean-card bg-white p-6 rounded-2xl space-y-4 relative overflow-hidden group">
                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-orange-500 to-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="h-12 w-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600 shadow-sm">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-brand-dark">Emergency Trauma</h3>
                <p class="text-xs text-brand-muted leading-relaxed font-semibold">
                    24/7 urgent responsive medical units utilizing state-of-the-art telemetry monitors and digital workflows.
                </p>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- CLOUD NODE ARCHITECTURE SECTION            -->
    <!-- ========================================== -->
    <section id="technologies" class="max-w-7xl mx-auto px-6 py-20 bg-white border-t border-brand-border">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-5">
                <span class="text-xs font-bold text-brand-blue uppercase tracking-widest">Developer First</span>
                <h3 class="text-3xl font-extrabold text-brand-dark">One API Call. <span class="text-brand-blue">Billions of Devices.</span></h3>
                <blockquote class="border-l-4 border-brand-blue pl-4 italic text-sm text-brand-muted font-medium">
                    "Simplicity is the ultimate sophistication. We took complex global medical database structures and compressed them into a single-line REST query."
                </blockquote>
                <ul class="space-y-3.5 text-xs text-slate-700 font-semibold">
                    <li class="flex items-center gap-3">
                        <span class="h-6 w-6 bg-blue-50 border border-blue-200 text-brand-blue rounded-full flex items-center justify-center font-bold">✓</span>
                        Secure Encrypted TLS 1.3 Data Pipelines
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="h-6 w-6 bg-teal-50 border border-teal-200 text-teal-655 rounded-full flex items-center justify-center font-bold">✓</span>
                        Object Storage (Cloudinary API) for Scans
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="h-6 w-6 bg-indigo-50 border border-indigo-200 text-indigo-650 rounded-full flex items-center justify-center font-bold">✓</span>
                        Role-Based Access Control Middleware (RBAC)
                    </li>
                </ul>
            </div>
            
            <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 shadow-inner">
                <div class="text-[10px] font-bold text-slate-550 uppercase mb-3">System Node Logs</div>
                <div class="font-mono text-[11px] text-sky-350 space-y-2 leading-relaxed bg-slate-950 p-4 rounded-xl max-h-60 overflow-y-auto">
                    <div>[2026-07-18 14:00:15] Initialize StJ-Node cluster...</div>
                    <div>[2026-07-18 14:00:16] Connection open to cloud DB. Latency: 12ms</div>
                    <div>[2026-07-18 14:00:18] Mounted docker software-defined storage 'db_data'.</div>
                    <div>[2026-07-18 14:00:20] Enforcing HIPAA encryption at-rest (AES-256).</div>
                    <div>[2026-07-18 14:00:21] Route controller registered: GET /api/get-appointments</div>
                    <div>[2026-07-18 14:00:22] Route controller registered: POST /api/upload-imaging</div>
                    <div class="text-slate-500">[2026-07-18 14:15:02] Listening on virtual bridge network gateway...</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- FOOTER                                     -->
    <!-- ========================================== -->
    <footer class="border-t border-brand-border bg-white py-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-6 space-y-4">
            <p class="font-bold text-slate-700">&copy; 2026 SL Medicare. All rights reserved.</p>
            <p class="max-w-md mx-auto leading-relaxed text-slate-400">
                SL Medicare is a fictional medical platform designed for cloud systems evaluation. All medical imaging uploads are stored in free cloud containers.
            </p>
        </div>
    </footer>

    <!-- Scroll effect script for Navbar -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('siteNavbar');
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>
<?php /**PATH C:\Users\hmcdi\OneDrive\Documents\GITHUB\SL-medicare\resources\views/welcome.blade.php ENDPATH**/ ?>
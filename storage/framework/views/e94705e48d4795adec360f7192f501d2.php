<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Secure Patient Portal</title>
    <!-- Tailwind CSS -->
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
                            900: '#0F172A',
                            dark: '#090d16'
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
            background: radial-gradient(circle at top right, #1e293b 0%, #090d16 70%);
        }
        .clean-card {
            background-color: rgba(13, 19, 31, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-slate-200 relative">

    <!-- Back to Home Button -->
    <a href="/" class="absolute top-6 left-6 inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition duration-200 bg-slate-950/40 border border-slate-800/80 px-3.5 py-2 rounded-xl backdrop-blur-md">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Home
    </a>

    <div class="absolute w-[400px] h-[300px] rounded-full bg-blue-500/5 blur-[80px] top-[-50px] left-[-100px] -z-10"></div>

    <div class="w-full max-w-xl clean-card rounded-3xl p-8 my-8 relative">
        <!-- Logo (Hexagon Heartbeat SVG) -->
        <div class="flex flex-col items-center mb-6">
            <div class="h-14 w-14 flex items-center justify-center mb-3 transform hover:scale-105 transition duration-200">
                <svg class="h-full w-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 7 L88 29 V71 L50 93 L12 71 V29 Z" stroke="url(#logo-grad-register)" stroke-width="6" stroke-linejoin="round" fill="rgba(37, 99, 235, 0.15)"/>
                    <path d="M26 50 H39 L44 32 L50 68 L56 42 L61 50 H74" stroke="#38BDF8" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="logo-grad-register" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#38BDF8" />
                            <stop offset="100%" stop-color="#2563EB" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">
                Create Patient Account
            </h1>
            <p class="text-sm text-slate-400 mt-1">Register for the Secure SL Medicare Portal</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-3 text-sm mb-6 flex items-center gap-2">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?php echo e(route('register')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-450 mb-1">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?php echo e(old('full_name')); ?>"
                               class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200"
                               placeholder="e.g. Jane Doe">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-450 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required value="<?php echo e(old('email')); ?>"
                               class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200"
                               placeholder="e.g. jane.doe@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-455 mb-1">Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-450 mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo e(old('phone')); ?>"
                               class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200"
                               placeholder="e.g. +1555010044">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="dob" class="block text-xs font-semibold uppercase tracking-wider text-slate-455 mb-1">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="<?php echo e(old('dob')); ?>"
                                   class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2 text-slate-400 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200 text-sm">
                        </div>
                        <div>
                            <label for="gender" class="block text-xs font-semibold uppercase tracking-wider text-slate-455 mb-1">Gender</label>
                            <select id="gender" name="gender"
                                    class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-400 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200 text-sm">
                                <option value="" class="bg-[#090d16]">Select</option>
                                <option value="MALE" <?php echo e(old('gender') === 'MALE' ? 'selected' : ''); ?> class="bg-[#090d16]">Male</option>
                                <option value="FEMALE" <?php echo e(old('gender') === 'FEMALE' ? 'selected' : ''); ?> class="bg-[#090d16]">Female</option>
                                <option value="OTHER" <?php echo e(old('gender') === 'OTHER' ? 'selected' : ''); ?> class="bg-[#090d16]">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-455 mb-1">Home Address</label>
                        <input type="text" id="address" name="address" value="<?php echo e(old('address')); ?>"
                               class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-hospital-500 focus:ring-1 focus:ring-hospital-500 transition duration-200"
                               placeholder="e.g. 123 Main St, City">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-hospital-500 hover:bg-hospital-600 text-white font-medium py-3 rounded-xl shadow-md transition duration-200">
                    Register Account
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <span class="text-xs text-slate-400">Already registered? </span>
            <a href="<?php echo e(route('login')); ?>" class="text-xs text-hospital-500 hover:text-hospital-600 font-semibold underline">
                Back to Sign In
            </a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\hmcdi\OneDrive\Documents\GITHUB\SL-medicare\resources\views/auth/register.blade.php ENDPATH**/ ?>
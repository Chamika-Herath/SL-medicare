<?php
/**
 * HMS Login Portal & Landing Page
 */
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT u.id, u.email, u.password, u.role, p.full_name 
                FROM users u 
                LEFT JOIN profiles p ON u.id = p.user_id 
                WHERE u.email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'] ?: 'HMS User';

                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (\PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud HMS - Secure Patient Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clinical: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#115e59',
                            dark: '#0b0f19'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #115e59 0%, #0b0f19 60%);
        }
        .glass {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(20, 184, 166, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-white overflow-hidden relative">

    <!-- Decorative glows -->
    <div class="absolute w-[400px] h-[400px] rounded-full bg-teal-500/10 blur-[100px] top-[-100px] left-[-100px] -z-10"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-emerald-500/5 blur-[120px] bottom-[-150px] right-[-100px] -z-10"></div>

    <div class="w-full max-w-md glass rounded-3xl p-8 shadow-2xl relative">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="h-16 w-16 bg-teal-500/20 border border-teal-500/40 rounded-2xl flex items-center justify-center mb-3 shadow-lg shadow-teal-500/10">
                <svg class="h-8 w-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 10.5V20a2 2 0 01-2 2H7a2 2 0 01-2-2v-9.5m14 0V9a2 2 0 00-2-2h-2m3 3.5V5a2 2 0 00-2-2h-3m-6 3.5V9a2 2 0 00-2-2H7m3 3.5V5a2 2 0 00-2-2H7m5 4v12m0 0l-3-3m3 3l3-3" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-teal-200 to-emerald-400 bg-clip-text text-transparent">
                Cloud HMS Portal
            </h1>
            <p class="text-sm text-slate-400 mt-1">Secure, cloud-native patient management</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-xl p-3 text-sm mb-6 flex items-center gap-2">
                <svg class="h-5 w-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="" method="POST" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                <input type="email" id="email" name="email" required
                       class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                       placeholder="e.g., patient.doe@hms.cloud">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-3 rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-600/30 transform hover:-translate-y-0.5 transition duration-200">
                Sign In to Portal
            </button>
        </form>

        <div class="mt-6 text-center">
            <span class="text-xs text-slate-400">New patient? </span>
            <a href="register.php" class="text-xs text-teal-400 hover:text-teal-300 font-semibold underline decoration-teal-500/30">
                Create an account
            </a>
        </div>

        <!-- Demonstration Hints (Crucial for presentation) -->
        <div class="mt-8 pt-6 border-t border-slate-800/60">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-teal-400/80 mb-3 text-center">
                Demo Accounts (Password: <span class="font-mono text-white text-[11px] bg-slate-800 px-1 py-0.5 rounded">password123</span>)
            </h3>
            <div class="grid grid-cols-3 gap-2">
                <button onclick="fillDemo('admin@hms.cloud')" class="bg-slate-900/40 hover:bg-slate-800/80 border border-slate-800/60 hover:border-teal-500/30 rounded-lg p-2 text-left transition text-[10px]">
                    <div class="font-bold text-teal-300">Admin</div>
                    <div class="text-slate-500 truncate">admin@hms.cloud</div>
                </button>
                <button onclick="fillDemo('doctor.smith@hms.cloud')" class="bg-slate-900/40 hover:bg-slate-800/80 border border-slate-800/60 hover:border-teal-500/30 rounded-lg p-2 text-left transition text-[10px]">
                    <div class="font-bold text-teal-300">Doctor</div>
                    <div class="text-slate-500 truncate">doctor.smith@hms.cloud</div>
                </button>
                <button onclick="fillDemo('patient.doe@hms.cloud')" class="bg-slate-900/40 hover:bg-slate-800/80 border border-slate-800/60 hover:border-teal-500/30 rounded-lg p-2 text-left transition text-[10px]">
                    <div class="font-bold text-teal-300">Patient</div>
                    <div class="text-slate-500 truncate">patient.doe@hms.cloud</div>
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>

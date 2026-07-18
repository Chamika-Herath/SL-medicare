<?php
/**
 * HMS Patient Registration
 */
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($email) || empty($password) || empty($full_name)) {
        $error = 'Email, Password, and Full Name are required.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'This email is already registered.';
            } else {
                $pdo->beginTransaction();

                // Insert into users
                $hashed_pw = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'PATIENT')");
                $stmt->execute([$email, $hashed_pw]);
                $user_id = $pdo->lastInsertId();

                // Insert into profiles
                $stmt = $pdo->prepare("
                    INSERT INTO profiles (user_id, full_name, dob, gender, phone, address) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $user_id,
                    $full_name,
                    empty($dob) ? null : $dob,
                    empty($gender) ? null : $gender,
                    empty($phone) ? null : $phone,
                    empty($address) ? null : $address
                ]);

                $pdo->commit();

                // Set session and redirect
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'PATIENT';
                $_SESSION['full_name'] = $full_name;

                header('Location: dashboard.php');
                exit;
            }
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
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
<body class="min-h-screen flex items-center justify-center p-4 text-white relative">

    <div class="absolute w-[400px] h-[400px] rounded-full bg-teal-500/10 blur-[100px] top-[-100px] left-[-100px] -z-10"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-emerald-500/5 blur-[120px] bottom-[-150px] right-[-100px] -z-10"></div>

    <div class="w-full max-w-xl glass rounded-3xl p-8 shadow-2xl relative my-8">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-6">
            <h1 class="text-2xl font-bold tracking-tight bg-gradient-to-r from-teal-200 to-emerald-400 bg-clip-text text-transparent">
                Create Patient Account
            </h1>
            <p class="text-sm text-slate-400 mt-1">Register for the Secure Cloud HMS Portal</p>
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
        <form action="" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required
                               class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                               placeholder="e.g. Jane Doe">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                               placeholder="e.g. jane.doe@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone"
                               class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                               placeholder="e.g. +1555010044">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="dob" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Date of Birth</label>
                            <input type="date" id="dob" name="dob"
                                   class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200 text-slate-400">
                        </div>
                        <div>
                            <label for="gender" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Gender</label>
                            <select id="gender" name="gender"
                                    class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200 text-slate-400">
                                <option value="">Select</option>
                                <option value="MALE">Male</option>
                                <option value="FEMALE">Female</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Home Address</label>
                        <input type="text" id="address" name="address"
                               class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition duration-200"
                               placeholder="e.g. 123 Main St, City">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-3 rounded-xl shadow-lg shadow-teal-500/20 hover:shadow-teal-600/30 transform hover:-translate-y-0.5 transition duration-200">
                    Register Account
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <span class="text-xs text-slate-400">Already registered? </span>
            <a href="index.php" class="text-xs text-teal-400 hover:text-teal-300 font-semibold underline decoration-teal-500/30">
                Back to Sign In
            </a>
        </div>
    </div>
</body>
</html>

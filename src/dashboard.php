<?php
/**
 * Unified Portal Dashboard (Admin, Doctor, Patient)
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Statistics data variables
$total_patients = 0;
$total_doctors = 0;
$total_appointments = 0;
$total_records = 0;
$upcoming_appointments = [];
$latest_records = [];

try {
    if ($role === 'ADMIN') {
        // Gather aggregate metrics
        $total_patients = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'PATIENT'")->fetchColumn();
        $total_doctors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'DOCTOR'")->fetchColumn();
        $total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
        $total_records = $pdo->query("SELECT COUNT(*) FROM medical_records")->fetchColumn();

        // Get latest appointments
        $stmt = $pdo->query("
            SELECT a.appointment_date, a.status, a.reason, p.full_name AS patient_name, d.full_name AS doctor_name
            FROM appointments a
            JOIN profiles p ON a.patient_id = p.user_id
            JOIN profiles d ON a.doctor_id = d.user_id
            ORDER BY a.created_at DESC LIMIT 5
        ");
        $upcoming_appointments = $stmt->fetchAll();
        
    } elseif ($role === 'DOCTOR') {
        // Stats for this specific doctor
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ?");
        $stmt->execute([$user_id]);
        $total_appointments = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = ?");
        $stmt->execute([$user_id]);
        $total_patients = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM medical_records WHERE doctor_id = ?");
        $stmt->execute([$user_id]);
        $total_records = $stmt->fetchColumn();

        // Get upcoming appointments
        $stmt = $pdo->prepare("
            SELECT a.appointment_date, a.status, a.reason, p.full_name AS patient_name
            FROM appointments a
            JOIN profiles p ON a.patient_id = p.user_id
            WHERE a.doctor_id = ? AND a.appointment_date >= NOW() AND a.status = 'APPROVED'
            ORDER BY a.appointment_date ASC LIMIT 5
        ");
        $stmt->execute([$user_id]);
        $upcoming_appointments = $stmt->fetchAll();
        
    } else { // PATIENT
        // Stats for this specific patient
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ?");
        $stmt->execute([$user_id]);
        $total_appointments = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM medical_records WHERE patient_id = ?");
        $stmt->execute([$user_id]);
        $total_records = $stmt->fetchColumn();

        // Get next appointment
        $stmt = $pdo->prepare("
            SELECT a.appointment_date, a.status, a.reason, d.full_name AS doctor_name
            FROM appointments a
            JOIN profiles d ON a.doctor_id = d.user_id
            WHERE a.patient_id = ? AND a.appointment_date >= NOW()
            ORDER BY a.appointment_date ASC LIMIT 3
        ");
        $execute_success = $stmt->execute([$user_id]);
        $upcoming_appointments = $stmt->fetchAll();

        // Get latest medical records
        $stmt = $pdo->prepare("
            SELECT mr.created_at, mr.diagnosis, mr.notes, mr.image_url, d.full_name AS doctor_name
            FROM medical_records mr
            JOIN profiles d ON mr.doctor_id = d.user_id
            WHERE mr.patient_id = ?
            ORDER BY mr.created_at DESC LIMIT 3
        ");
        $stmt->execute([$user_id]);
        $latest_records = $stmt->fetchAll();
    }
} catch (\PDOException $e) {
    die("Database Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure Cloud HMS</title>
    <!-- Tailwind CSS CDN -->
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
                            dark: '#080c14'
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
            background: radial-gradient(circle at 10% 20%, #0d2e27 0%, #080c14 90%);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(20, 184, 166, 0.1);
        }
        .glow-border:hover {
            border-color: rgba(20, 184, 166, 0.4);
            box-shadow: 0 0 20px rgba(20, 184, 166, 0.1);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex overflow-hidden">

    <!-- Page Layout Wrapper -->
    <div class="flex w-full h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <?php include_once __DIR__ . '/components/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">
            <!-- Header Topbar -->
            <?php include_once __DIR__ . '/components/header.php'; ?>

            <!-- Core Content Container -->
            <main class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Welcome Section -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-teal-900/30 to-slate-900/40 p-6 rounded-2xl border border-teal-500/10">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">
                            Welcome back, <?php echo htmlspecialchars($full_name); ?>!
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            You are securely connected to the cloud clinical portal node.
                        </p>
                    </div>
                    <div>
                        <span class="text-xs bg-teal-500/20 text-teal-300 font-bold px-3 py-1.5 rounded-xl border border-teal-500/30 uppercase tracking-wider">
                            Role: <?php echo htmlspecialchars($role); ?>
                        </span>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Metric Card 1 -->
                    <div class="glass-card glow-border p-5 rounded-2xl transition duration-300 flex items-center gap-4">
                        <div class="p-3 bg-teal-500/10 border border-teal-500/20 text-teal-400 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Patients</span>
                            <span class="text-2xl font-bold text-white"><?php echo $total_patients; ?></span>
                        </div>
                    </div>

                    <!-- Metric Card 2 -->
                    <?php if ($role === 'ADMIN'): ?>
                    <div class="glass-card glow-border p-5 rounded-2xl transition duration-300 flex items-center gap-4">
                        <div class="p-3 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Doctors</span>
                            <span class="text-2xl font-bold text-white"><?php echo $total_doctors; ?></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="glass-card glow-border p-5 rounded-2xl transition duration-300 flex items-center gap-4">
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">My Records</span>
                            <span class="text-2xl font-bold text-white"><?php echo $total_records; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Metric Card 3 -->
                    <div class="glass-card glow-border p-5 rounded-2xl transition duration-300 flex items-center gap-4">
                        <div class="p-3 bg-amber-500/10 border border-amber-500/20 text-amber-400 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Appointments</span>
                            <span class="text-2xl font-bold text-white"><?php echo $total_appointments; ?></span>
                        </div>
                    </div>

                    <!-- Metric Card 4 -->
                    <div class="glass-card glow-border p-5 rounded-2xl transition duration-300 flex items-center gap-4">
                        <div class="p-3 bg-purple-500/10 border border-purple-500/20 text-purple-400 rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-2.19 8-4V7M4 7c0 2.21 3.582 4 8 4s8-2.19 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-2.19-8-4" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block font-medium">Cloud DB Latency</span>
                            <span class="text-2xl font-bold text-white">12 ms</span>
                        </div>
                    </div>
                </div>

                <!-- Main Layout Columns -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Left: Appointments Column -->
                    <div class="glass-card rounded-2xl p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-teal-400"></span>
                                Upcoming Appointments
                            </h3>
                            <a href="appointments.php" class="text-xs text-teal-400 hover:text-teal-300 font-semibold flex items-center gap-1">
                                Manage
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        
                        <div class="space-y-3 flex-1">
                            <?php if (empty($upcoming_appointments)): ?>
                                <div class="text-center py-8 text-slate-500 text-sm">
                                    No upcoming appointments found.
                                </div>
                            <?php else: ?>
                                <?php foreach ($upcoming_appointments as $appt): ?>
                                    <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-xl flex justify-between items-start gap-4 hover:border-slate-700/60 transition">
                                        <div class="space-y-1">
                                            <div class="text-xs font-semibold text-teal-300 flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <?php echo date('M d, Y - h:i A', strtotime($appt['appointment_date'])); ?>
                                            </div>
                                            <p class="text-sm font-bold text-white">
                                                <?php if ($role === 'DOCTOR'): ?>
                                                    Patient: <?php echo htmlspecialchars($appt['patient_name']); ?>
                                                <?php elseif ($role === 'PATIENT'): ?>
                                                    Doctor: <?php echo htmlspecialchars($appt['doctor_name']); ?>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($appt['patient_name']); ?> &harr; <?php echo htmlspecialchars($appt['doctor_name']); ?>
                                                <?php endif; ?>
                                            </p>
                                            <p class="text-xs text-slate-400 italic font-medium">"<?php echo htmlspecialchars($appt['reason']); ?>"</p>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider <?php 
                                                echo $appt['status'] === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                                    ($appt['status'] === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'); 
                                            ?>">
                                                <?php echo $appt['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Medical Diagnostics Column -->
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                Latest Medical Records
                            </h3>
                            <a href="records.php" class="text-xs text-teal-400 hover:text-teal-300 font-semibold flex items-center gap-1">
                                View All
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                        <div class="space-y-3">
                            <?php if ($role === 'ADMIN'): ?>
                                <div class="bg-teal-500/5 border border-teal-500/10 text-slate-400 rounded-xl p-4 text-sm text-center">
                                    Use the **Doctor Portal** demo account to create diagnosis records, write clinical reports, and upload files to Cloud Object Storage.
                                </div>
                            <?php elseif (empty($latest_records)): ?>
                                <div class="text-center py-8 text-slate-500 text-sm">
                                    No diagnosis entries or records found.
                                </div>
                            <?php else: ?>
                                <?php foreach ($latest_records as $rec): ?>
                                    <div class="bg-slate-900/60 border border-slate-800 p-4 rounded-xl space-y-2.5 hover:border-slate-700/60 transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="text-sm font-bold text-white"><?php echo htmlspecialchars($rec['diagnosis']); ?></h4>
                                                <span class="text-[10px] text-slate-400 font-medium">Diagnosed by: <?php echo htmlspecialchars($rec['doctor_name']); ?></span>
                                            </div>
                                            <span class="text-[10px] text-slate-500 font-bold"><?php echo date('M d, Y', strtotime($rec['created_at'])); ?></span>
                                        </div>
                                        <p class="text-xs text-slate-400 leading-relaxed font-medium"><?php echo htmlspecialchars($rec['notes']); ?></p>
                                        
                                        <?php if ($rec['image_url']): ?>
                                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-800/80">
                                                <svg class="h-4 w-4 text-teal-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <a href="<?php echo htmlspecialchars($rec['image_url']); ?>" target="_blank" class="text-xs text-teal-400 hover:underline truncate">
                                                    View Diagnostic Scan File (S3 / Cloudinary) &rarr;
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

</body>
</html>

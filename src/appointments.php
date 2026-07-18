<?php
/**
 * Appointments Listing and Scheduling System (Role-Based Interactions)
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/config/db.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$error = '';
$success = '';

// Handle status updates by Doctors
if ($role === 'DOCTOR' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    $action = $_POST['action'];

    if ($appointment_id > 0 && in_array($action, ['approve', 'cancel'])) {
        $status = ($action === 'approve') ? 'APPROVED' : 'CANCELLED';
        try {
            $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
            $stmt->execute([$status, $appointment_id, $user_id]);
            $success = "Appointment successfully updated to " . $status;
        } catch (\PDOException $e) {
            $error = "Error updating appointment: " . $e->getMessage();
        }
    }
}

// Handle Patient direct booking submission (alternative to calling API asynchronously)
if ($role === 'PATIENT' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_now'])) {
    $doctor_id = intval($_POST['doctor_id'] ?? 0);
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (empty($doctor_id) || empty($appointment_date)) {
        $error = 'Doctor and appointment date/time are required.';
    } else {
        $timestamp = strtotime($appointment_date);
        if (!$timestamp || $timestamp < time()) {
            $error = 'Please select a valid future date and time.';
        } else {
            $formatted_date = date('Y-m-d H:i:s', $timestamp);
            try {
                $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, reason) VALUES (?, ?, ?, 'PENDING', ?)");
                $stmt->execute([$user_id, $doctor_id, $formatted_date, $reason]);
                $success = "Your appointment request has been submitted successfully!";
            } catch (\PDOException $e) {
                $error = "Booking failed: " . $e->getMessage();
            }
        }
    }
}

// Fetch list of doctors (for Booking selection)
$doctors = [];
if ($role === 'PATIENT') {
    try {
        $stmt = $pdo->query("
            SELECT u.id, p.full_name 
            FROM users u 
            JOIN profiles p ON u.id = p.user_id 
            WHERE u.role = 'DOCTOR'
            ORDER BY p.full_name ASC
        ");
        $doctors = $stmt->fetchAll();
    } catch (\PDOException $e) {
        $error = "Failed to fetch doctors: " . $e->getMessage();
    }
}

// Fetch Appointments for display
$appointments = [];
try {
    $query = "
        SELECT a.id, a.appointment_date, a.status, a.reason,
               p_prof.full_name AS patient_name, p_prof.phone AS patient_phone,
               d_prof.full_name AS doctor_name
        FROM appointments a
        JOIN profiles p_prof ON a.patient_id = p_prof.user_id
        JOIN profiles d_prof ON a.doctor_id = d_prof.user_id
    ";

    if ($role === 'PATIENT') {
        $query .= " WHERE a.patient_id = ? ORDER BY a.appointment_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } elseif ($role === 'DOCTOR') {
        $query .= " WHERE a.doctor_id = ? ORDER BY a.status DESC, a.appointment_date ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } else { // ADMIN
        $query .= " ORDER BY a.appointment_date DESC";
        $stmt = $pdo->query($query);
    }
    
    $appointments = $stmt->fetchAll();
} catch (\PDOException $e) {
    $error = "Failed to fetch appointments: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Cloud HMS</title>
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
                <!-- Title Header -->
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">Appointments</h1>
                        <p class="text-sm text-slate-400">Manage, request, and verify consultation times.</p>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (!empty($error)): ?>
                    <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-xl p-4 text-sm flex items-center gap-2">
                        <span class="font-bold">Error:</span> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-xl p-4 text-sm flex items-center gap-2">
                        <span class="font-bold">Success:</span> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Form & Details Layout Grid -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                    
                    <!-- Left: Scheduling Card (PATIENT only) -->
                    <?php if ($role === 'PATIENT'): ?>
                    <div class="glass-card rounded-2xl p-6 xl:col-span-1">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Book Appointment
                        </h3>
                        <form action="" method="POST" class="space-y-4">
                            <div>
                                <label for="doctor_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Specialist *</label>
                                <select id="doctor_id" name="doctor_id" required
                                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-slate-300 text-sm">
                                    <option value="">Choose Doctor</option>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?php echo $doc['id']; ?>"><?php echo htmlspecialchars($doc['full_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="appointment_date" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Preferred Date & Time *</label>
                                <input type="datetime-local" id="appointment_date" name="appointment_date" required
                                       class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-slate-400 text-sm">
                            </div>

                            <div>
                                <label for="reason" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reason for Consultation</label>
                                <textarea id="reason" name="reason" rows="3"
                                          class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 placeholder-slate-500 text-sm"
                                          placeholder="Brief description of symptoms..."></textarea>
                            </div>

                            <button type="submit" name="book_now"
                                    class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-3 rounded-xl shadow-lg transition duration-200 text-sm">
                                Submit Appointment Request
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Right: Appointment Logs -->
                    <div class="glass-card rounded-2xl p-6 <?php echo ($role === 'PATIENT') ? 'xl:col-span-2' : 'xl:col-span-3'; ?>">
                        <h3 class="text-lg font-bold text-white mb-4">
                            Appointment Schedules
                        </h3>

                        <?php if (empty($appointments)): ?>
                            <div class="text-center py-12 text-slate-500">
                                No appointments booked yet.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                            <th class="pb-3 pr-4">Patient</th>
                                            <th class="pb-3 pr-4">Doctor</th>
                                            <th class="pb-3 pr-4">Scheduled Date</th>
                                            <th class="pb-3 pr-4">Status</th>
                                            <th class="pb-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800 text-sm">
                                        <?php foreach ($appointments as $appt): ?>
                                            <tr class="hover:bg-slate-900/30 transition">
                                                <td class="py-4 pr-4">
                                                    <span class="font-semibold text-white"><?php echo htmlspecialchars($appt['patient_name']); ?></span>
                                                    <?php if ($role === 'DOCTOR' && $appt['patient_phone']): ?>
                                                        <span class="block text-xs text-slate-400"><?php echo htmlspecialchars($appt['patient_phone']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-4 pr-4 font-medium text-slate-300">
                                                    <?php echo htmlspecialchars($appt['doctor_name']); ?>
                                                </td>
                                                <td class="py-4 pr-4 text-teal-300 font-mono text-xs">
                                                    <?php echo date('Y-m-d h:i A', strtotime($appt['appointment_date'])); ?>
                                                </td>
                                                <td class="py-4 pr-4">
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider <?php 
                                                        echo $appt['status'] === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                                            ($appt['status'] === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'); 
                                                    ?>">
                                                        <?php echo $appt['status']; ?>
                                                    </span>
                                                </td>
                                                <td class="py-4">
                                                    <?php if ($role === 'DOCTOR' && $appt['status'] === 'PENDING'): ?>
                                                        <!-- Doctor can Approve or Reject -->
                                                        <form action="" method="POST" class="inline-flex gap-2">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                            <button type="submit" name="action" value="approve"
                                                                    class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs rounded transition">
                                                                Approve
                                                            </button>
                                                            <button type="submit" name="action" value="cancel"
                                                                    class="px-2.5 py-1 bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white font-semibold text-xs rounded border border-red-500/30 transition">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-xs text-slate-500 italic">No actions</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

</body>
</html>

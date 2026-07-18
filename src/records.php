<?php
/**
 * Medical Records & Cloud Image Management
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

// Handle Doctors adding a new Diagnostic Record
if (($role === 'DOCTOR' || $role === 'ADMIN') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record'])) {
    $patient_id = intval($_POST['patient_id'] ?? 0);
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    $image_url = null;
    
    // Process image file upload to Object Storage (Cloudinary/S3)
    if (isset($_FILES['imaging_file']) && $_FILES['imaging_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        require_once __DIR__ . '/config/storage.php';
        
        $cloud_url = upload_to_cloud_storage($_FILES['imaging_file']);
        if ($cloud_url) {
            $image_url = $cloud_url;
        } else {
            $error = 'Failed to upload diagnostic file to Cloud Storage.';
        }
    }

    if (empty($error)) {
        if ($patient_id <= 0 || empty($diagnosis)) {
            $error = 'Patient ID and Diagnosis are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$patient_id, $user_id, $diagnosis, $notes, $image_url]);
                $success = 'Diagnostic report and scan uploaded successfully!';
            } catch (\PDOException $e) {
                $error = 'Database insert failed: ' . $e->getMessage();
            }
        }
    }
}

// Fetch patients for Doctor select dropdown
$patients = [];
if ($role === 'DOCTOR' || $role === 'ADMIN') {
    try {
        $stmt = $pdo->query("
            SELECT u.id, p.full_name, p.dob 
            FROM users u 
            JOIN profiles p ON u.id = p.user_id 
            WHERE u.role = 'PATIENT' 
            ORDER BY p.full_name ASC
        ");
        $patients = $stmt->fetchAll();
    } catch (\PDOException $e) {
        $error = 'Failed to load patient profiles: ' . $e->getMessage();
    }
}

// Fetch Medical Records
$records = [];
try {
    $query = "
        SELECT mr.id, mr.diagnosis, mr.notes, mr.image_url, mr.created_at,
               p_prof.full_name AS patient_name, p_prof.dob AS patient_dob,
               d_prof.full_name AS doctor_name
        FROM medical_records mr
        JOIN profiles p_prof ON mr.patient_id = p_prof.user_id
        JOIN profiles d_prof ON mr.doctor_id = d_prof.user_id
    ";

    if ($role === 'PATIENT') {
        $query .= " WHERE mr.patient_id = ? ORDER BY mr.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } else { // DOCTOR and ADMIN can see everything
        $query .= " ORDER BY mr.created_at DESC";
        $stmt = $pdo->query($query);
    }
    
    $records = $stmt->fetchAll();
} catch (\PDOException $e) {
    $error = 'Failed to load medical history: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - Cloud HMS</title>
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
                        <h1 class="text-2xl font-bold tracking-tight text-white">Medical Records</h1>
                        <p class="text-sm text-slate-400">View diagnostic reports, upload lab files, and manage scans.</p>
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

                <!-- Grid layout for records -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                    
                    <!-- Left: Upload Diagnostic Report (DOCTOR and ADMIN only) -->
                    <?php if ($role === 'DOCTOR' || $role === 'ADMIN'): ?>
                    <div class="glass-card rounded-2xl p-6 xl:col-span-1">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            Add Medical Record
                        </h3>
                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <div>
                                <label for="patient_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Patient *</label>
                                <select id="patient_id" name="patient_id" required
                                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-slate-300 text-sm">
                                    <option value="">Choose Patient</option>
                                    <?php foreach ($patients as $pat): ?>
                                        <option value="<?php echo $pat['id']; ?>">
                                            <?php echo htmlspecialchars($pat['full_name']); ?> 
                                            (DOB: <?php echo htmlspecialchars($pat['dob']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="diagnosis" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Diagnosis *</label>
                                <input type="text" id="diagnosis" name="diagnosis" required
                                       class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-slate-300 text-sm"
                                       placeholder="e.g. Acute Bronchitis">
                            </div>

                            <div>
                                <label for="notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Clinical Notes & Prescription</label>
                                <textarea id="notes" name="notes" rows="4"
                                          class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 placeholder-slate-500 text-sm"
                                          placeholder="Enter diagnostic notes, symptoms, and prescribed dosage..."></textarea>
                            </div>

                            <div>
                                <label for="imaging_file" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload Imaging Scan / Lab Report</label>
                                <div class="relative w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 flex items-center justify-between border-dashed cursor-pointer">
                                    <input type="file" id="imaging_file" name="imaging_file" accept="image/*,application/pdf"
                                           class="absolute inset-0 opacity-0 cursor-pointer">
                                    <span class="text-xs text-slate-400 font-medium">Select Image (PNG, JPG, PDF)...</span>
                                    <svg class="h-5 w-5 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                            </div>

                            <button type="submit" name="add_record"
                                    class="w-full bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-medium py-3 rounded-xl shadow-lg transition duration-200 text-sm">
                                Upload & Publish Record
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <!-- Right: Medical Logs List -->
                    <div class="glass-card rounded-2xl p-6 <?php echo ($role === 'DOCTOR' || $role === 'ADMIN') ? 'xl:col-span-2' : 'xl:col-span-3'; ?>">
                        <h3 class="text-lg font-bold text-white mb-4">
                            Diagnostic Log Archive
                        </h3>

                        <?php if (empty($records)): ?>
                            <div class="text-center py-12 text-slate-500">
                                No diagnostic logs found in this portal.
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($records as $rec): ?>
                                    <div class="bg-slate-900/60 border border-slate-800/80 p-5 rounded-2xl space-y-3 hover:border-teal-500/20 transition">
                                        
                                        <!-- Header row of the record -->
                                        <div class="flex justify-between items-start gap-4">
                                            <div>
                                                <h4 class="text-md font-bold text-white"><?php echo htmlspecialchars($rec['diagnosis']); ?></h4>
                                                <div class="text-xs text-slate-400 mt-0.5 font-medium">
                                                    Patient: <span class="text-teal-300 font-semibold"><?php echo htmlspecialchars($rec['patient_name']); ?></span> 
                                                    (DOB: <?php echo htmlspecialchars($rec['patient_dob']); ?>)
                                                </div>
                                                <div class="text-xs text-slate-500 mt-0.5">
                                                    Consultant: <?php echo htmlspecialchars($rec['doctor_name']); ?>
                                                </div>
                                            </div>
                                            <span class="text-xs text-slate-500 font-bold font-mono">
                                                <?php echo date('Y-m-d H:i A', strtotime($rec['created_at'])); ?>
                                            </span>
                                        </div>

                                        <!-- Notes paragraph -->
                                        <p class="text-xs text-slate-300 leading-relaxed font-medium bg-slate-950/40 p-3 rounded-xl border border-slate-850">
                                            <?php echo nl2br(htmlspecialchars($rec['notes'])); ?>
                                        </p>

                                        <!-- Attached Scans (Cloud links) -->
                                        <?php if ($rec['image_url']): ?>
                                            <div class="pt-2 border-t border-slate-800/80">
                                                <div class="text-xs font-semibold text-slate-400 mb-2">Attached Scans (Hosted on Object Storage):</div>
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <!-- Simple Thumbnail preview if file is image -->
                                                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)/i', $rec['image_url'])): ?>
                                                        <a href="<?php echo htmlspecialchars($rec['image_url']); ?>" target="_blank" class="group relative overflow-hidden rounded-lg border border-slate-800 hover:border-teal-500/40 shrink-0">
                                                            <img src="<?php echo htmlspecialchars($rec['image_url']); ?>" alt="Diagnostic Scan" class="h-16 w-16 object-cover group-hover:scale-105 transition duration-200">
                                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                </svg>
                                                            </div>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?php echo htmlspecialchars($rec['image_url']); ?>" target="_blank"
                                                       class="text-xs text-teal-400 hover:text-teal-300 hover:underline flex items-center gap-1">
                                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Open Full Imaging Scan Record &rarr;
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </main>
        </div>
    </div>

</body>
</html>

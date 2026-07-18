<?php
/**
 * REST API: Upload Medical Scan / Diagnosis
 * Endpoint: POST /api/upload_imaging.php
 */

session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/storage.php';

// Only DOCTOR and ADMIN can upload diagnostics
if ($_SESSION['role'] !== 'DOCTOR' && $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only doctors or admins can create medical records.']);
    exit;
}

$patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
$diagnosis = isset($_POST['diagnosis']) ? trim($_POST['diagnosis']) : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

// Validation
if (empty($patient_id) || empty($diagnosis)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Patient ID and Diagnosis are required fields.']);
    exit;
}

// Verify patient exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'PATIENT'");
$stmt->execute([$patient_id]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid Patient ID.']);
    exit;
}

$image_url = null;

// Handle file upload to Object Storage if provided
if (isset($_FILES['imaging_file']) && $_FILES['imaging_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['imaging_file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'File upload error code: ' . $_FILES['imaging_file']['error']]);
        exit;
    }
    
    // Upload file to cloud bucket
    $cloud_url = upload_to_cloud_storage($_FILES['imaging_file']);
    if ($cloud_url) {
        $image_url = $cloud_url;
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image to Cloud Object Storage.']);
        exit;
    }
}

try {
    // Insert medical record
    $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, notes, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$patient_id, $_SESSION['user_id'], $diagnosis, $notes, $image_url]);
    
    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'message' => 'Medical record created successfully.',
        'data' => [
            'record_id' => $pdo->lastInsertId(),
            'patient_id' => $patient_id,
            'doctor_id' => $_SESSION['user_id'],
            'diagnosis' => $diagnosis,
            'notes' => $notes,
            'image_url' => $image_url
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}

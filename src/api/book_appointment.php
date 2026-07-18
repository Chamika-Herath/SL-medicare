<?php
/**
 * REST API: Book Appointment
 * Endpoint: POST /api/book_appointment.php
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login.']);
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Only PATIENT and ADMIN roles can book appointments
if ($_SESSION['role'] !== 'PATIENT' && $_SESSION['role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Only patients or admins can book appointments.']);
    exit;
}

// Get POST input (supports raw JSON and standard form-urlencoded)
$input_raw = file_get_contents('php://input');
$input_json = json_decode($input_raw, true);

$doctor_id = isset($input_json['doctor_id']) ? intval($input_json['doctor_id']) : (isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : null);
$appointment_date = isset($input_json['appointment_date']) ? trim($input_json['appointment_date']) : (isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : null);
$reason = isset($input_json['reason']) ? trim($input_json['reason']) : (isset($_POST['reason']) ? trim($_POST['reason']) : '');

// For a Patient role, they book for themselves. Admin might pass patient_id explicitly
$patient_id = $_SESSION['user_id'];
if ($_SESSION['role'] === 'ADMIN' && (isset($input_json['patient_id']) || isset($_POST['patient_id']))) {
    $patient_id = isset($input_json['patient_id']) ? intval($input_json['patient_id']) : intval($_POST['patient_id']);
}

// Validation
if (empty($doctor_id) || empty($appointment_date)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Doctor and Appointment Date are required.']);
    exit;
}

// Check doctor exists and has role DOCTOR
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'DOCTOR'");
$stmt->execute([$doctor_id]);
if (!$stmt->fetch()) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid doctor selected.']);
    exit;
}

// Validate date format (YYYY-MM-DD HH:MM:SS or similar)
$timestamp = strtotime($appointment_date);
if (!$timestamp || $timestamp < time()) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Appointment date must be a valid future date/time.']);
    exit;
}

$formatted_date = date('Y-m-d H:i:s', $timestamp);

try {
    // Insert appointment
    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, reason) VALUES (?, ?, ?, 'PENDING', ?)");
    $stmt->execute([$patient_id, $doctor_id, $formatted_date, $reason]);
    
    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'message' => 'Appointment booked successfully.',
        'data' => [
            'appointment_id' => $pdo->lastInsertId(),
            'patient_id' => $patient_id,
            'doctor_id' => $doctor_id,
            'appointment_date' => $formatted_date,
            'status' => 'PENDING'
        ]
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Internal server error: ' . $e->getMessage()]);
}

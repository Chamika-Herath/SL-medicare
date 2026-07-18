<?php
/**
 * REST API: Get Appointments
 * Endpoint: GET /api/get_appointments.php
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

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

try {
    $query = "
        SELECT 
            a.id, 
            a.appointment_date, 
            a.status, 
            a.reason,
            a.patient_id,
            a.doctor_id,
            p_profile.full_name AS patient_name,
            d_profile.full_name AS doctor_name
        FROM appointments a
        LEFT JOIN profiles p_profile ON a.patient_id = p_profile.user_id
        LEFT JOIN profiles d_profile ON a.doctor_id = d_profile.user_id
    ";

    // Filter query based on RBAC role
    if ($role === 'PATIENT') {
        $query .= " WHERE a.patient_id = ? ORDER BY a.appointment_date ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } elseif ($role === 'DOCTOR') {
        $query .= " WHERE a.doctor_id = ? ORDER BY a.appointment_date ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } else { // ADMIN gets everything
        $query .= " ORDER BY a.appointment_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }

    $appointments = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'count' => count($appointments),
        'role' => $role,
        'data' => $appointments
    ]);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $e->getMessage()]);
}

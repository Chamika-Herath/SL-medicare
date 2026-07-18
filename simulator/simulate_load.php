<?php
/**
 * Laravel HMS Performance Testing Simulator
 * Simulates concurrent patients booking appointments through the Laravel REST API.
 * 
 * Usage: php simulate_load.php [target_url] [requests_count]
 * Example: php simulate_load.php http://localhost:8080 50
 */

// CLI setup
if (php_sapi_name() !== 'cli') {
    die("This simulator must be run from the command line.");
}

$target_host = isset($argv[1]) ? rtrim($argv[1], '/') : 'http://localhost:8080';
$requests = isset($argv[2]) ? intval($argv[2]) : 50;

echo "=================================================================\n";
echo "    Laravel Secure Cloud HMS - Performance Load Simulator        \n";
echo "=================================================================\n";
echo "Target Host:   $target_host\n";
echo "Total Bookings: $requests requests\n";
echo "=================================================================\n\n";

// 1. Authenticate to get a Session Cookie
echo "[Step 1/3] Authenticating patient user (patient.doe@hms.cloud)...\n";
$login_url = "$target_host/login";

$cookie_file = tempnam(sys_get_temp_dir(), 'hms_laravel_cookie_');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'email' => 'patient.doe@hms.cloud',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 && $http_code !== 302) {
    unlink($cookie_file);
    die("Error: Authentication failed with status code $http_code. Ensure target host is running and migrations are pre-seeded.\n");
}
echo "Authentication Successful. Cookie Saved.\n\n";

// 2. Perform Load Test
echo "[Step 2/3] Simulating concurrent API requests on /api/book-appointment...\n";
$api_url = "$target_host/api/book-appointment";

$start_time = microtime(true);
$success_count = 0;
$failure_count = 0;
$latencies = [];

for ($i = 1; $i <= $requests; $i++) {
    $req_start = microtime(true);
    
    // Simulate scheduling variation
    $future_days = rand(1, 30);
    $hour = rand(9, 16);
    $min = array_rand(['00', '15', '30', '45']);
    $date_str = date('Y-m-d H:i:s', strtotime("+$future_days days $hour:$min:00"));
    
    $payload = [
        'doctor_id' => rand(2, 3), // Dr. Sarah Smith or Dr. Robert Jones
        'appointment_date' => $date_str,
        'reason' => "Laravel Simulator Verification. ID #$i"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $api_res = curl_exec($ch);
    $api_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $req_end = microtime(true);
    $duration = ($req_end - $req_start) * 1000; // milliseconds
    $latencies[] = $duration;

    if ($api_code === 201) {
        $success_count++;
        echo "Request #$i: SUCCESS - Booking Created. Latency: " . number_format($duration, 2) . "ms\n";
    } else {
        $failure_count++;
        echo "Request #$i: FAILED (HTTP $api_code) - Response: $api_res. Latency: " . number_format($duration, 2) . "ms\n";
    }
}

$end_time = microtime(true);
$total_time = $end_time - $start_time;

// 3. Compile statistics
echo "\n[Step 3/3] Analysis & Performance Breakdown:\n";
echo "-----------------------------------------------------------------\n";
echo "Total Duration:       " . number_format($total_time, 4) . " seconds\n";
echo "Transactions/Sec:     " . number_format($requests / $total_time, 2) . " RPS\n";
echo "Successful Bookings:  $success_count / $requests (" . number_format(($success_count / $requests) * 100, 1) . "%)\n";
echo "Failed Bookings:      $failure_count\n";

if (count($latencies) > 0) {
    echo "Min Latency:          " . number_format(min($latencies), 2) . " ms\n";
    echo "Max Latency:          " . number_format(max($latencies), 2) . " ms\n";
    echo "Average Latency:      " . number_format(array_sum($latencies) / count($latencies), 2) . " ms\n";
}
echo "-----------------------------------------------------------------\n";

// Cleanup cookie
unlink($cookie_file);
echo "Performance Testing Completed.\n";

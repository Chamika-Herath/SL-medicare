<?php
/**
 * Cloud Object Storage configuration.
 * Integrates with Cloudinary (Image & PDF cloud hosting) via native HTTP cURL.
 * This bypasses composer dependency requirements for easy Hostinger shared hosting deployments.
 */

define('CLOUDINARY_CLOUD_NAME', getenv('CLOUDINARY_CLOUD_NAME') ?: 'dhw7f5mxt'); // Fallback demo cloud name
define('CLOUDINARY_UPLOAD_PRESET', getenv('CLOUDINARY_UPLOAD_PRESET') ?: 'hms_unsigned_preset'); // Demo unsigned preset

/**
 * Uploads a file to Cloudinary Object Storage.
 *
 * @param array $file_post The $_FILES['input_name'] array.
 * @return string|false The secure cloud URL of the image, or false on failure.
 */
function upload_to_cloud_storage($file_post) {
    if (!isset($file_post['tmp_name']) || empty($file_post['tmp_name'])) {
        return false;
    }

    $tmp_file = $file_post['tmp_name'];
    $mime_type = mime_content_type($tmp_file);

    // Verify it is an image or PDF
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!in_array($mime_type, $allowed_mimes)) {
        return false;
    }

    // Build the cURL request to Cloudinary API
    $url = "https://api.cloudinary.com/v1_1/" . CLOUDINARY_CLOUD_NAME . "/image/upload";
    
    // Create curl file object
    $cfile = new CURLFile($tmp_file, $mime_type, $file_post['name']);

    $data = [
        'file' => $cfile,
        'upload_preset' => CLOUDINARY_UPLOAD_PRESET,
        'tags' => 'hms_medical_record',
        'context' => 'source=cloud_hms_portal'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Help with local curl configurations

    $response = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) {
        // Log the error locally for troubleshooting
        error_log("Cloudinary Upload Error: " . $err);
        return false;
    }

    $result = json_decode($response, true);
    if ($http_code === 200 && isset($result['secure_url'])) {
        return $result['secure_url']; // Return the cloud URL (HTTPS)
    } else {
        error_log("Cloudinary API Error: " . ($result['error']['message'] ?? 'Unknown error'));
        return false;
    }
}

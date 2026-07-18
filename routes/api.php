<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Group API routes with 'web' session cookies to enable easy Postman/cURL testing
// without configuring complex Sanctum token database overhead.
Route::middleware('web')->group(function () {
    
    // API: Fetch appointment collections
    Route::get('/get-appointments', [AppointmentController::class, 'apiIndex']);

    // API: Book a new appointment
    Route::post('/book-appointment', [AppointmentController::class, 'apiBook']);

    // API: Create record and upload scan to object storage
    Route::post('/upload-imaging', [MedicalRecordController::class, 'apiUpload']);
    
});

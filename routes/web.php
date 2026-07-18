<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root landing page for the private hospital
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shared Hosting Helper: Run migrations and seed data via URL
Route::get('/run-migrations', function () {
    try {
        // Runs php artisan migrate:fresh --seed --force
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true
        ]);
        return "<h3>Success!</h3><p>Database tables created and pre-seeded successfully.</p><pre>" . 
            \Illuminate\Support\Facades\Artisan::output() . "</pre><a href='/login'>Go to Login</a>";
    } catch (\Exception $e) {
        return "<h3>Error Running Migrations</h3><p>" . $e->getMessage() . "</p>";
    }
});

// Authenticated Portal Routes (Protected by session auth)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (Unified Hub)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Add Doctor (Admin only)
    Route::post('/admin/doctors', [DashboardController::class, 'addDoctor'])
        ->middleware('role:ADMIN')
        ->name('admin.doctors.store');

    // Appointments Panel
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
    
    // Book Appointment (Patient or Admin)
    Route::post('/appointments/book', [AppointmentController::class, 'book'])
        ->middleware('role:PATIENT,ADMIN')
        ->name('appointments.book');

    // Update Appointment Status (Doctor Only)
    Route::post('/appointments/status/{id}', [AppointmentController::class, 'updateStatus'])
        ->middleware('role:DOCTOR')
        ->name('appointments.updateStatus');

    // Medical Records Archive
    Route::get('/records', [MedicalRecordController::class, 'index'])->name('records');
    
    // Upload Record (Doctor or Admin)
    Route::post('/records/store', [MedicalRecordController::class, 'store'])
        ->middleware('role:DOCTOR,ADMIN')
        ->name('records.store');
});

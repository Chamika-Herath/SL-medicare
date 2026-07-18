<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MedicalRecordController extends Controller
{
    /**
     * Web View: List medical diagnostics history
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $records = [];
        $patients = [];

        if ($role === 'PATIENT') {
            $records = MedicalRecord::where('patient_id', $user->id)
                ->with(['doctor.profile', 'patient.profile'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else { // DOCTOR & ADMIN
            $records = MedicalRecord::with(['patient.profile', 'doctor.profile'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Fetch patients list for doctor dropdown selector
            $patients = User::where('role', 'PATIENT')->with('profile')->get();
        }

        return view('records', compact('records', 'patients'));
    }

    /**
     * Web Upload Action (Doctor Only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'diagnosis' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'imaging_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // 10MB max
        ]);

        // Verify selected patient is actually a patient
        $patient = User::where('id', $request->patient_id)->where('role', 'PATIENT')->firstOrFail();

        $imageUrl = null;

        // Process file upload to Cloud Storage via HTTP client
        if ($request->hasFile('imaging_file')) {
            $imageUrl = $this->uploadToCloud($request->file('imaging_file'));
            if (!$imageUrl) {
                return back()->withErrors(['error' => 'Failed to upload diagnostic file to Cloud Storage. Please check configurations.']);
            }
        }

        MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => Auth::id(),
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
            'image_url' => $imageUrl,
        ]);

        return back()->with('success', 'Diagnostic report and scan uploaded successfully!');
    }

    /* =========================================================================
       REST API Endpoints (Fulfilling Part 5 & 4)
       ========================================================================= */

    /**
     * API: Create Diagnostic & Upload File
     */
    public function apiUpload(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        if ($user->role !== 'DOCTOR' && $user->role !== 'ADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Access denied. Only doctors or admins can create medical records.'], 403);
        }

        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'diagnosis' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'imaging_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
        ]);

        // Verify patient
        $patient = User::where('id', $request->patient_id)->where('role', 'PATIENT')->first();
        if (!$patient) {
            return response()->json(['status' => 'error', 'message' => 'Invalid patient selected.'], 400);
        }

        $imageUrl = null;

        if ($request->hasFile('imaging_file')) {
            $imageUrl = $this->uploadToCloud($request->file('imaging_file'));
            if (!$imageUrl) {
                return response()->json(['status' => 'error', 'message' => 'Failed to upload file to Cloud Storage.'], 500);
            }
        }

        $record = MedicalRecord::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $user->id,
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
            'image_url' => $imageUrl,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Medical record created successfully.',
            'data' => [
                'record_id' => $record->id,
                'patient_id' => $record->patient_id,
                'doctor_id' => $record->doctor_id,
                'diagnosis' => $record->diagnosis,
                'notes' => $record->notes,
                'image_url' => $record->image_url
            ]
        ], 201);
    }

    /**
     * Helper: Upload files to Cloudinary Object Storage using Laravel HTTP Facade
     */
    private function uploadToCloud($file)
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME') ?: 'dhw7f5mxt';
        $preset = env('CLOUDINARY_UPLOAD_PRESET') ?: 'hms_unsigned_preset';
        
        $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

        try {
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName(),
                ['Content-Type' => $file->getMimeType()]
            )->post($url, [
                'upload_preset' => $preset,
                'tags' => 'hms_medical_record',
                'context' => 'source=laravel_hms_portal'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['secure_url'] ?? null;
            } else {
                Log::error('Cloudinary API Error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Cloud Storage Upload Exception: ' . $e->getMessage());
            return null;
        }
    }
}

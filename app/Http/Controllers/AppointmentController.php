<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Web View: List appointments & booking forms
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $appointments = [];
        $doctors = [];

        // Fetch appointments based on role
        if ($role === 'PATIENT') {
            $appointments = Appointment::where('patient_id', $user->id)
                ->with(['doctor.profile', 'patient.profile'])
                ->orderBy('appointment_date', 'desc')
                ->get();
            
            // Load doctors list for patient scheduler dropdown
            $doctors = User::where('role', 'DOCTOR')->with('profile')->get();
        } elseif ($role === 'DOCTOR') {
            $appointments = Appointment::where('doctor_id', $user->id)
                ->with(['patient.profile', 'doctor.profile'])
                ->orderBy('status', 'desc')
                ->orderBy('appointment_date', 'asc')
                ->get();
        } else { // ADMIN
            $appointments = Appointment::with(['patient.profile', 'doctor.profile'])
                ->orderBy('appointment_date', 'desc')
                ->get();
        }

        return view('appointments', compact('appointments', 'doctors'));
    }

    /**
     * Web Booking submission (Patient Only)
     */
    public function book(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason' => 'nullable|string',
        ]);

        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'reason' => $request->reason,
            'status' => 'PENDING',
        ]);

        return back()->with('success', 'Your appointment request has been submitted successfully!');
    }

    /**
     * Web Action: Approve/Cancel appointment (Doctor Only)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,cancel',
        ]);

        $status = ($request->action === 'approve') ? 'APPROVED' : 'CANCELLED';

        $appointment = Appointment::where('id', $id)
            ->where('doctor_id', Auth::id())
            ->firstOrFail();

        $appointment->update(['status' => $status]);

        return back()->with('success', 'Appointment status updated to ' . $status);
    }

    /* =========================================================================
       REST API Endpoints (Fulfilling Part 5)
       ========================================================================= */

    /**
     * API: Get Appointments
     */
    public function apiIndex(Request $request)
    {
        // Supports both session authentication and bearer tokens
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        $query = Appointment::with(['patient.profile', 'doctor.profile']);

        if ($user->role === 'PATIENT') {
            $appointments = $query->where('patient_id', $user->id)->orderBy('appointment_date', 'asc')->get();
        } elseif ($user->role === 'DOCTOR') {
            $appointments = $query->where('doctor_id', $user->id)->orderBy('appointment_date', 'asc')->get();
        } else { // ADMIN
            $appointments = $query->orderBy('appointment_date', 'desc')->get();
        }

        // Format data to match our API spec
        $formatted = $appointments->map(function ($appt) {
            return [
                'id' => $appt->id,
                'appointment_date' => $appt->appointment_date,
                'status' => $appt->status,
                'reason' => $appt->reason,
                'patient_id' => $appt->patient_id,
                'doctor_id' => $appt->doctor_id,
                'patient_name' => $appt->patient->profile->full_name ?? 'N/A',
                'doctor_name' => $appt->doctor->profile->full_name ?? 'N/A',
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $formatted->count(),
            'role' => $user->role,
            'data' => $formatted
        ]);
    }

    /**
     * API: Book Appointment
     */
    public function apiBook(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 401);
        }

        if ($user->role !== 'PATIENT' && $user->role !== 'ADMIN') {
            return response()->json(['status' => 'error', 'message' => 'Access denied. Only patients or admins can book.'], 403);
        }

        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason' => 'nullable|string',
        ]);

        $patient_id = $user->id;
        if ($user->role === 'ADMIN' && $request->has('patient_id')) {
            $patient_id = $request->patient_id;
        }

        // Check if specialist has Doctor role
        $doctor = User::where('id', $request->doctor_id)->where('role', 'DOCTOR')->first();
        if (!$doctor) {
            return response()->json(['status' => 'error', 'message' => 'Selected user is not a valid doctor.'], 400);
        }

        $appointment = Appointment::create([
            'patient_id' => $patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'reason' => $request->reason,
            'status' => 'PENDING',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Appointment booked successfully.',
            'data' => [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_date' => $appointment->appointment_date,
                'status' => $appointment->status
            ]
        ], 201);
    }

    public function history()
    {
        $user = Auth::user();
        $role = $user->role;
        
        // Fetch all appointments (both upcoming and past) for this doctor
        $appointments = Appointment::where('doctor_id', $user->id)
            ->with(['patient.profile'])
            ->orderBy('appointment_date', 'desc')
            ->get();
            
        return view('doctor.appointments_history', compact('appointments', 'role'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $totalPatients = 0;
        $totalDoctors = 0;
        $totalAppointments = 0;
        $totalRecords = 0;
        $upcomingAppointments = [];
        $latestRecords = [];

        if ($role === 'ADMIN') {
            $totalPatients = User::where('role', 'PATIENT')->count();
            $totalDoctors = User::where('role', 'DOCTOR')->count();
            $totalAppointments = Appointment::count();
            $totalRecords = MedicalRecord::count();

            $upcomingAppointments = Appointment::with(['patient.profile', 'doctor.profile'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } elseif ($role === 'DOCTOR') {
            $totalAppointments = Appointment::where('doctor_id', $user->id)->count();
            $totalPatients = Appointment::where('doctor_id', $user->id)->distinct('patient_id')->count();
            $totalRecords = MedicalRecord::where('doctor_id', $user->id)->count();

            $upcomingAppointments = Appointment::where('doctor_id', $user->id)
                ->where('appointment_date', '>=', now())
                ->where('status', 'APPROVED')
                ->with('patient.profile')
                ->orderBy('appointment_date', 'asc')
                ->limit(5)
                ->get();
        } else { // PATIENT
            $totalAppointments = Appointment::where('patient_id', $user->id)->count();
            $totalRecords = MedicalRecord::where('patient_id', $user->id)->count();

            $upcomingAppointments = Appointment::where('patient_id', $user->id)
                ->where('appointment_date', '>=', now())
                ->with('doctor.profile')
                ->orderBy('appointment_date', 'asc')
                ->limit(3)
                ->get();

            $latestRecords = MedicalRecord::where('patient_id', $user->id)
                ->with('doctor.profile')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        return view('dashboard', compact(
            'role', 'totalPatients', 'totalDoctors', 'totalAppointments', 'totalRecords', 
            'upcomingAppointments', 'latestRecords'
        ));
    }

    public function addDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE,OTHER',
            'address' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'DOCTOR',
            ]);

            Profile::create([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Doctor registered successfully in SL Medicare system database.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Failed to register doctor: ' . $e->getMessage(),
            ]);
        }
    }
}

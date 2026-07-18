<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        $users = [
            [
                'email' => 'admin@hms.cloud',
                'password' => Hash::make('password123'),
                'role' => 'ADMIN',
                'name' => 'System Cloud Admin',
                'dob' => '1985-06-15',
                'gender' => 'MALE',
                'phone' => '+1555010001',
                'address' => 'Cloud Infrastructure HQ, Server Room A'
            ],
            [
                'email' => 'doctor.smith@hms.cloud',
                'password' => Hash::make('password123'),
                'role' => 'DOCTOR',
                'name' => 'Dr. Sarah Smith (Cardiology)',
                'dob' => '1978-04-22',
                'gender' => 'FEMALE',
                'phone' => '+1555010002',
                'address' => 'Outpatient Building Room 302'
            ],
            [
                'email' => 'doctor.jones@hms.cloud',
                'password' => Hash::make('password123'),
                'role' => 'DOCTOR',
                'name' => 'Dr. Robert Jones (Neurology)',
                'dob' => '1982-11-09',
                'gender' => 'MALE',
                'phone' => '+1555010003',
                'address' => 'Outpatient Building Room 104'
            ],
            [
                'email' => 'patient.doe@hms.cloud',
                'password' => Hash::make('password123'),
                'role' => 'PATIENT',
                'name' => 'John Doe',
                'dob' => '1995-02-12',
                'gender' => 'MALE',
                'phone' => '+1555010004',
                'address' => '123 Pine St, Metro City'
            ],
            [
                'email' => 'patient.alice@hms.cloud',
                'password' => Hash::make('password123'),
                'role' => 'PATIENT',
                'name' => 'Alice Johnson',
                'dob' => '1998-09-30',
                'gender' => 'FEMALE',
                'phone' => '+1555010005',
                'address' => '456 Oak Ave, West Suburbs'
            ],
        ];

        foreach ($users as $u) {
            $userObj = User::create([
                'email' => $u['email'],
                'password' => $u['password'],
                'role' => $u['role']
            ]);

            Profile::create([
                'user_id' => $userObj->id,
                'full_name' => $u['name'],
                'dob' => $u['dob'],
                'gender' => $u['gender'],
                'phone' => $u['phone'],
                'address' => $u['address']
            ]);
        }

        // 2. Seed Appointments
        Appointment::create([
            'patient_id' => 4, // John Doe
            'doctor_id' => 2,  // Dr. Sarah Smith
            'appointment_date' => '2026-07-20 10:00:00',
            'status' => 'APPROVED',
            'reason' => 'Routine cardiovascular health checkup.'
        ]);

        Appointment::create([
            'patient_id' => 4, // John Doe
            'doctor_id' => 3,  // Dr. Robert Jones
            'appointment_date' => '2026-07-22 14:30:00',
            'status' => 'PENDING',
            'reason' => 'Persistent migraines and sleep issues.'
        ]);

        Appointment::create([
            'patient_id' => 5, // Alice Johnson
            'doctor_id' => 2,  // Dr. Sarah Smith
            'appointment_date' => '2026-07-21 11:15:00',
            'status' => 'APPROVED',
            'reason' => 'Follow-up ECG consultation.'
        ]);

        // 3. Seed Medical Records
        MedicalRecord::create([
            'patient_id' => 4, // John Doe
            'doctor_id' => 2,  // Dr. Sarah Smith
            'diagnosis' => 'Mild Hypertension',
            'notes' => 'Patient exhibits slightly elevated systolic blood pressure. Recommended reduction in sodium intake and daily light exercise. Re-evaluate in 3 months.',
            'image_url' => 'https://res.cloudinary.com/demo/image/upload/v1312461204/sample.jpg'
        ]);

        MedicalRecord::create([
            'patient_id' => 5, // Alice Johnson
            'doctor_id' => 2,  // Dr. Sarah Smith
            'diagnosis' => 'Mitral Valve Prolapse Follow-up',
            'notes' => 'Echocardiogram completed. Mild regurgitation observed. Patient advised to avoid heavy caffeine intake and return for annual monitoring.',
            'image_url' => null
        ]);
    }
}

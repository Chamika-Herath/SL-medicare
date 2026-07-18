@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<!-- Alert Messages -->
@if (session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm mb-6 flex items-center gap-2 shadow animate-fade-in">
        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span class="font-bold">Success:</span> {{ session('success') }}
    </div>
@endif
@if ($errors->any())
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm mb-6 flex items-center gap-2 shadow animate-fade-in">
        <span class="h-2 w-2 rounded-full bg-red-400 animate-pulse"></span>
        <span class="font-bold">Error:</span> {{ $errors->first() }}
    </div>
@endif

<!-- Welcome banner (With sliding entrance animation and dark layout) -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-blue-900/40 to-slate-900/10 p-6 rounded-2xl border border-slate-800 shadow-lg animate-slide-up">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
            <span class="h-3 w-3 rounded-full bg-blue-500 animate-ping"></span>
            Welcome back, {{ Auth::user()->profile->full_name ?? 'HMS User' }}!
        </h1>
        <p class="text-sm text-slate-400 mt-1">
            You are securely connected to the SL Medicare cloud portal node.
        </p>
    </div>
    <div>
        <span class="text-xs bg-blue-950/60 text-brand-sky font-extrabold px-3 py-1.5 rounded-xl border border-blue-900/40 uppercase tracking-wider shadow-sm">
            Role: {{ $role }}
        </span>
    </div>
</div>

<!-- Metrics Cards Grid (With hover scale animations and vibrant gradient outlines) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Patients (Blue) -->
    <div class="bg-[#0d131f] border-l-4 border-blue-500 border-t border-r border-b border-slate-800 p-5 rounded-2xl shadow-md flex items-center gap-4 hover:scale-[1.03] hover:border-blue-500/50 hover:shadow-lg transition-all duration-300">
        <div class="p-3 bg-blue-950/40 border border-blue-900/50 text-brand-sky rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-slate-450 block font-bold uppercase tracking-wider">Patients</span>
            <span class="text-2xl font-bold text-white">{{ $totalPatients }}</span>
        </div>
    </div>

    <!-- Card 2: Records (Emerald/Green) -->
    @if ($role === 'ADMIN')
    <div class="bg-[#0d131f] border-l-4 border-indigo-500 border-t border-r border-b border-slate-800 p-5 rounded-2xl shadow-md flex items-center gap-4 hover:scale-[1.03] hover:border-indigo-500/50 hover:shadow-lg transition-all duration-300">
        <div class="p-3 bg-indigo-950/40 border border-indigo-900/50 text-indigo-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-slate-450 block font-bold uppercase tracking-wider">Doctors</span>
            <span class="text-2xl font-bold text-white">{{ $totalDoctors }}</span>
        </div>
    </div>
    @else
    <div class="bg-[#0d131f] border-l-4 border-emerald-500 border-t border-r border-b border-slate-800 p-5 rounded-2xl shadow-md flex items-center gap-4 hover:scale-[1.03] hover:border-emerald-500/50 hover:shadow-lg transition-all duration-300">
        <div class="p-3 bg-emerald-950/40 border border-emerald-900/50 text-emerald-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-slate-455 block font-bold uppercase tracking-wider">My Records</span>
            <span class="text-2xl font-bold text-white">{{ $totalRecords }}</span>
        </div>
    </div>
    @endif

    <!-- Card 3: Appointments (Orange/Amber) -->
    <div class="bg-[#0d131f] border-l-4 border-orange-500 border-t border-r border-b border-slate-800 p-5 rounded-2xl shadow-md flex items-center gap-4 hover:scale-[1.03] hover:border-orange-500/50 hover:shadow-lg transition-all duration-300">
        <div class="p-3 bg-orange-950/40 border border-orange-900/50 text-orange-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-slate-455 block font-bold uppercase tracking-wider">Appointments</span>
            <span class="text-2xl font-bold text-white">{{ $totalAppointments }}</span>
        </div>
    </div>

    <!-- Card 4: Cloud Latency (Violet/Purple) -->
    <div class="bg-[#0d131f] border-l-4 border-purple-500 border-t border-r border-b border-slate-800 p-5 rounded-2xl shadow-md flex items-center gap-4 hover:scale-[1.03] hover:border-purple-500/50 hover:shadow-lg transition-all duration-300">
        <div class="p-3 bg-purple-950/40 border border-purple-900/50 text-purple-400 rounded-xl">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-2.19 8-4V7M4 7c0 2.21 3.582 4 8 4s8-2.19 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-2.19-8-4" />
            </svg>
        </div>
        <div>
            <span class="text-[10px] text-slate-455 block font-bold uppercase tracking-wider">Cloud Latency</span>
            <span class="text-2xl font-bold text-purple-400 font-mono">8 ms</span>
        </div>
    </div>
</div>

<!-- Columns -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Appointments Column -->
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 flex flex-col shadow-lg">
        <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
            <h3 class="text-md font-bold text-white flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                Upcoming Appointments
            </h3>
            <a href="{{ route('appointments') }}" class="text-xs text-brand-sky hover:text-white font-bold flex items-center gap-1">
                Manage
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        
        <div class="space-y-3 flex-1">
            @if (empty($upcomingAppointments) || count($upcomingAppointments) === 0)
                <div class="text-center py-8 text-slate-500 text-sm">
                    No upcoming appointments found.
                </div>
            @else
                @foreach ($upcomingAppointments as $appt)
                    <div class="bg-[#131a26]/70 border border-slate-800 hover:border-blue-500/50 p-4 rounded-xl flex justify-between items-start gap-4 transform hover:scale-[1.01] transition-all duration-300 shadow-md">
                        <div class="space-y-1">
                            <div class="text-xs font-semibold text-brand-sky flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ date('M d, Y - h:i A', strtotime($appt->appointment_date)) }}
                            </div>
                            <p class="text-sm font-bold text-slate-200">
                                @if ($role === 'DOCTOR')
                                    Patient: {{ $appt->patient->profile->full_name ?? 'N/A' }}
                                @elseif ($role === 'PATIENT')
                                    Doctor: {{ $appt->doctor->profile->full_name ?? 'N/A' }}
                                @else
                                    {{ $appt->patient->profile->full_name ?? 'N/A' }} &harr; {{ $appt->doctor->profile->full_name ?? 'N/A' }}
                                @endif
                            </p>
                            <p class="text-xs text-slate-400 italic font-medium">"{{ $appt->reason }}"</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ 
                                $appt->status === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                    ($appt->status === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20') 
                            }}">
                                {{ $appt->status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Right Column (Admin: Add Doctor Form / Doctors & Patients: Medical Records) -->
    @if ($role === 'ADMIN')
        <!-- Register Doctor Card (Admin only) -->
        <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-md font-bold text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Add Doctor to System
                </h3>
            </div>
            
            <form action="{{ route('admin.doctors.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-450 uppercase tracking-wider mb-2">Doctor Full Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                               placeholder="e.g. Dr. Jane Smith (Neurology)">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-450 uppercase tracking-wider mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                               placeholder="e.g. j.smith@slmedicare.lk">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Account Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone"
                               class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                               placeholder="e.g. +94771234567">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="dob" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                               class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2 text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm">
                    </div>
                    <div>
                        <label for="gender" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Gender</label>
                        <select id="gender" name="gender"
                                class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm">
                            <option value="" class="bg-[#0d131f]">Select</option>
                            <option value="MALE" class="bg-[#0d131f]">Male</option>
                            <option value="FEMALE" class="bg-[#0d131f]">Female</option>
                            <option value="OTHER" class="bg-[#0d131f]">Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Consultation Room / Office</label>
                    <input type="text" id="address" name="address"
                           class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                           placeholder="e.g. Outpatient Unit Room 402">
                </div>

                <button type="submit"
                        class="w-full bg-brand-blue hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md transition duration-205 text-sm">
                    Register Doctor Account
                </button>
            </form>
        </div>
    @else
        <!-- Diagnostics Column -->
        <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                <h3 class="text-md font-bold text-white flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Latest Medical Records
                </h3>
                <a href="{{ route('records') }}" class="text-xs text-brand-sky hover:text-white font-bold flex items-center gap-1">
                    View All
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="space-y-3">
                @if (empty($latestRecords) || count($latestRecords) === 0)
                    <div class="text-center py-8 text-slate-500 text-sm">
                        No diagnosis entries or records found.
                    </div>
                @else
                    @foreach ($latestRecords as $rec)
                        <div class="bg-[#131a26]/70 border border-slate-800 hover:border-emerald-500/50 p-4 rounded-xl space-y-2.5 transform hover:scale-[1.01] transition-all duration-300 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-200">{{ $rec->diagnosis }}</h4>
                                    <span class="text-[10px] text-slate-400 font-medium">Diagnosed by: {{ $rec->doctor->profile->full_name ?? 'N/A' }}</span>
                                </div>
                                <span class="text-[10px] text-slate-450 font-bold font-mono">{{ date('M d, Y', strtotime($rec->created_at)) }}</span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed font-semibold">{{ $rec->notes }}</p>
                            
                            @if ($rec->image_url)
                                <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-800">
                                    <svg class="h-4 w-4 text-brand-sky shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <a href="{{ $rec->image_url }}" target="_blank" class="text-xs text-brand-sky font-bold hover:underline truncate">
                                        View Diagnostic Scan File (Cloud Object Link) &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

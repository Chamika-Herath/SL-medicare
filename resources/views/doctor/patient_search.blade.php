@extends('layouts.portal')

@section('title', 'Patient Search & Logs')

@section('content')
<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white font-sans">Patient Diagnostics Directory</h1>
        <p class="text-sm text-slate-400">Search patient logs and inspect their medical records history.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Left Column: Patient Search & List (lg:col-span-4) -->
    <div class="lg:col-span-4 bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg space-y-4">
        
        <!-- Search Form -->
        <form action="{{ route('doctor.patients') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ $search }}"
                   class="w-full bg-[#131a26] border border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                   placeholder="Search name, email, phone...">
            <div class="absolute left-3 top-3 text-slate-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if ($search)
                <a href="{{ route('doctor.patients') }}" class="absolute right-3 top-2.5 text-xs text-slate-500 hover:text-white font-bold bg-slate-800 px-1.5 py-0.5 rounded">
                    Clear
                </a>
            @endif
        </form>

        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-800 pb-2">
            Match Patients ({{ count($patients) }})
        </h3>

        <!-- Patients List -->
        <div class="space-y-2 max-h-[50vh] overflow-y-auto pr-1">
            @if ($patients->isEmpty())
                <p class="text-xs text-slate-500 text-center py-4">No matching patients found.</p>
            @else
                @foreach ($patients as $pat)
                    @php
                        $isSelected = $selectedPatient && $selectedPatient->id === $pat->id;
                    @endphp
                    <a href="{{ route('doctor.patients', ['patient_id' => $pat->id, 'search' => $search]) }}"
                       class="block p-3 rounded-xl border transition-all duration-205 text-left {{ 
                           $isSelected ? 'bg-brand-blue/10 border-brand-blue text-white' : 'bg-[#131a26]/70 border-slate-800 hover:border-slate-700 text-slate-300'
                       }}">
                        <span class="text-sm font-bold block">{{ $pat->profile->full_name ?? 'N/A' }}</span>
                        <span class="text-[10px] text-slate-450 block truncate">{{ $pat->email }}</span>
                        <span class="text-[10px] text-slate-500 block">Phone: {{ $pat->profile->phone ?? 'N/A' }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Right Column: Selected Patient Records History (lg:col-span-8) -->
    <div class="lg:col-span-8 space-y-6">
        @if (!$selectedPatient)
            <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-12 text-center shadow-lg text-slate-500">
                <svg class="h-12 w-12 text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <h4 class="font-bold text-white mb-1">No Patient Selected</h4>
                <p class="text-xs text-slate-450">Select a patient profile from the left column directory to load their clinical file and diagnosis history.</p>
            </div>
        @else
            <!-- Patient Profile Details Summary -->
            <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg space-y-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 font-bold text-xl shrink-0">
                        {{ strtoupper(substr($selectedPatient->profile->full_name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white leading-none">{{ $selectedPatient->profile->full_name ?? 'N/A' }}</h2>
                        <span class="text-[9px] bg-teal-500/20 text-teal-400 font-extrabold px-2.5 py-0.5 rounded border border-teal-500/20 uppercase mt-1.5 inline-block tracking-wider">
                            Patient Account File
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-500 font-bold uppercase text-[9px] tracking-wider">Email Address</span>
                        <div class="text-slate-200 truncate mt-0.5 font-semibold">{{ $selectedPatient->email }}</div>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold uppercase text-[9px] tracking-wider">Phone Number</span>
                        <div class="text-slate-200 mt-0.5 font-semibold">{{ $selectedPatient->profile->phone ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold uppercase text-[9px] tracking-wider">Personal Data</span>
                        <div class="text-slate-200 mt-0.5 font-semibold">
                            DOB: {{ $selectedPatient->profile->dob ?? 'N/A' }} ({{ $selectedPatient->profile->gender ?? 'N/A' }})
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-500 font-bold uppercase text-[9px] tracking-wider">Home Address</span>
                        <div class="text-slate-200 mt-0.5 font-semibold truncate" title="{{ $selectedPatient->profile->address ?? 'N/A' }}">
                            {{ $selectedPatient->profile->address ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History Logs list -->
            <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-3">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="h-5 w-5 text-brand-sky animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Clinical Medical History ({{ count($patientRecords) }})
                    </h3>
                    
                    <!-- Quick action shortcut -->
                    <a href="{{ route('records') }}?patient_id={{ $selectedPatient->id }}" 
                       class="bg-brand-blue hover:bg-blue-700 text-white font-bold px-3.5 py-1.5 rounded-xl text-xs flex items-center gap-1.5 shadow transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Diagnostic
                    </a>
                </div>

                @if (empty($patientRecords) || count($patientRecords) === 0)
                    <div class="text-center py-12 text-slate-500">
                        No previous medical records or diagnosis entries logged for this patient.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($patientRecords as $rec)
                            <div class="bg-[#131a26]/70 border border-slate-800 p-4 rounded-xl space-y-2.5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-bold text-white">{{ $rec->diagnosis }}</h4>
                                        <span class="text-[10px] text-slate-500">Physician: {{ $rec->doctor->profile->full_name ?? 'N/A' }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500 font-bold font-mono">
                                        {{ date('Y-m-d H:i A', strtotime($rec->created_at)) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-350 bg-[#0d131f] p-3 rounded-xl border border-slate-800/80 leading-relaxed font-semibold">
                                    {!! nl2br(e($rec->notes)) !!}
                                </p>
                                
                                @if ($rec->image_url)
                                    <div class="pt-2 border-t border-slate-800 flex items-center gap-2">
                                        <svg class="h-4 w-4 text-brand-sky shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <a href="{{ $rec->image_url }}" target="_blank" class="text-xs text-brand-sky font-bold hover:underline truncate">
                                            View Diagnostic Scan File (S3 Archive Link) &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection

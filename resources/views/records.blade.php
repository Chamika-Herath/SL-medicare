@extends('layouts.portal')

@section('title', 'Medical Records')

@section('content')
<!-- Title Header -->
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Medical Records</h1>
        <p class="text-sm text-slate-400">View diagnostic reports, upload lab files, and manage scans.</p>
    </div>
</div>

<!-- Messages -->
@if (session('success'))
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm flex items-center gap-2 shadow">
        <span class="font-bold">Success:</span> {{ session('success') }}
    </div>
@endif
@if ($errors->any())
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm flex items-center gap-2 shadow">
        <span class="font-bold">Error:</span> {{ $errors->first() }}
    </div>
@endif

<!-- Grid layout for records -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    
    <!-- Left: Upload Diagnostic Report (DOCTOR and ADMIN only) -->
    @if (Auth::user()->role === 'DOCTOR' || Auth::user()->role === 'ADMIN')
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg xl:col-span-1">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            Add Medical Record
        </h3>
        <form action="{{ route('records.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="patient_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Patient *</label>
                <select id="patient_id" name="patient_id" required
                        class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm">
                    <option value="" class="bg-[#0d131f]">Choose Patient</option>
                    @foreach ($patients as $pat)
                        <option value="{{ $pat->id }}" class="bg-[#0d131f]">
                            {{ $pat->profile->full_name ?? $pat->email }} 
                            (DOB: {{ $pat->profile->dob ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="diagnosis" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Diagnosis *</label>
                <input type="text" id="diagnosis" name="diagnosis" required
                       class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                       placeholder="e.g. Acute Bronchitis">
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Clinical Notes & Prescription</label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue placeholder-slate-500 text-sm"
                          placeholder="Enter diagnostic notes, symptoms, and prescribed dosage..."></textarea>
            </div>

            <div>
                <label for="imaging_file" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload Imaging Scan / Lab Report</label>
                <div class="relative w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-3 flex items-center justify-between border-dashed cursor-pointer">
                    <input type="file" id="imaging_file" name="imaging_file" accept="image/*,application/pdf"
                           class="absolute inset-0 opacity-0 cursor-pointer">
                    <span class="text-xs text-slate-450 font-medium">Select Image (PNG, JPG, PDF)...</span>
                    <svg class="h-5 w-5 text-brand-sky shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-brand-blue hover:bg-blue-700 text-white font-medium py-3 rounded-xl shadow-md transition duration-200 text-sm">
                Upload & Publish Record
            </button>
        </form>
    </div>
    @endif

    <!-- Right: Medical Logs List -->
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg {{ (Auth::user()->role === 'DOCTOR' || Auth::user()->role === 'ADMIN') ? 'xl:col-span-2' : 'xl:col-span-3' }}">
        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3">
            Diagnostic Log Archive
        </h3>

        @if (empty($records) || count($records) === 0)
            <div class="text-center py-12 text-slate-500">
                No diagnostic logs found in this portal.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($records as $rec)
                    <div class="bg-[#131a26]/70 border border-slate-800 p-5 rounded-2xl space-y-3 hover:border-blue-500/50 transition duration-300">
                        
                        <!-- Header row of the record -->
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h4 class="text-md font-bold text-white">{{ $rec->diagnosis }}</h4>
                                <div class="text-xs text-slate-400 mt-0.5 font-medium">
                                    Patient: <span class="text-brand-sky font-semibold">{{ $rec->patient->profile->full_name ?? 'N/A' }}</span> 
                                    (DOB: {{ $rec->patient->profile->dob ?? 'N/A' }})
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5 font-medium">
                                    Consultant: {{ $rec->doctor->profile->full_name ?? 'N/A' }}
                                </div>
                            </div>
                            <span class="text-xs text-slate-500 font-bold font-mono">
                                {{ date('Y-m-d H:i A', strtotime($rec->created_at)) }}
                            </span>
                        </div>

                        <!-- Notes paragraph -->
                        <p class="text-xs text-slate-300 leading-relaxed font-semibold bg-[#0d131f] p-3 rounded-xl border border-slate-800/80">
                            {!! nl2br(e($rec->notes)) !!}
                        </p>

                        <!-- Attached Scans (Cloud links) -->
                        @if ($rec->image_url)
                            <div class="pt-2 border-t border-slate-800">
                                <div class="text-xs font-semibold text-slate-500 mb-2">Attached Scans (Hosted on Object Storage):</div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <!-- Simple Thumbnail preview if file is image -->
                                    @if (preg_match('/\.(jpg|jpeg|png|gif)/i', $rec->image_url))
                                        <a href="{{ $rec->image_url }}" target="_blank" class="group relative overflow-hidden rounded-lg border border-slate-800 hover:border-brand-sky/40 shrink-0">
                                            <img src="{{ $rec->image_url }}" alt="Diagnostic Scan" class="h-16 w-16 object-cover group-hover:scale-105 transition duration-200">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    <a href="{{ $rec->image_url }}" target="_blank"
                                       class="text-xs text-brand-sky hover:text-white hover:underline flex items-center gap-1 font-bold">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Open Full Imaging Scan Record &rarr;
                                    </a>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

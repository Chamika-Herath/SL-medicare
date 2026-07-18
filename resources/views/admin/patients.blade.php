@extends('layouts.portal')

@section('title', 'Patient Registry')

@section('content')
<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white font-sans">Patient Registry Database</h1>
        <p class="text-sm text-slate-400">Search and view active patient files inside the SL Medicare node.</p>
    </div>
</div>

<!-- List Card -->
<div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg">
    <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3">
        Registered Patients Archive
    </h3>

    @if (empty($patientsList) || count($patientsList) === 0)
        <div class="text-center py-12 text-slate-500">
            No patient accounts registered in the database.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($patientsList as $pat)
                <div class="bg-[#131a26]/70 border border-slate-800 hover:border-teal-500/40 p-5 rounded-2xl flex flex-col justify-between gap-4 transition duration-300">
                    <div class="flex items-start gap-4">
                        <div class="h-11 w-11 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 font-bold text-lg shrink-0">
                            {{ strtoupper(substr($pat->profile->full_name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="space-y-1 truncate">
                            <h4 class="text-md font-bold text-white leading-none truncate">{{ $pat->profile->full_name ?? 'N/A' }}</h4>
                            <span class="text-[9px] bg-teal-500/20 text-teal-400 font-bold px-2 py-0.5 rounded border border-teal-500/20 uppercase tracking-wider inline-block">
                                Patient (RBAC)
                            </span>
                            <div class="text-xs text-slate-450 font-medium pt-1 space-y-0.5 truncate">
                                <span class="block truncate">Email: {{ $pat->email }}</span>
                                <span class="block">Phone: {{ $pat->profile->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-500 block font-semibold uppercase tracking-wider text-[9px]">Personal Info</span>
                            <div class="text-slate-300 mt-0.5 font-medium truncate">
                                DOB: {{ $pat->profile->dob ?? 'N/A' }}
                            </div>
                            <div class="text-slate-350 font-semibold truncate">
                                Gender: {{ $pat->profile->gender ?? 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-500 block font-semibold uppercase tracking-wider text-[9px]">Home Address</span>
                            <div class="text-slate-300 mt-0.5 font-medium leading-tight truncate" title="{{ $pat->profile->address ?? 'N/A' }}">
                                {{ $pat->profile->address ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

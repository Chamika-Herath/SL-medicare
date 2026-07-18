@extends('layouts.portal')

@section('title', 'Doctor Registry')

@section('content')
<!-- Header -->
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white font-sans">Medical Consultants Directory</h1>
        <p class="text-sm text-slate-400">View and manage registered consultants inside SL Medicare.</p>
    </div>
</div>

<!-- Form & List Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    
    <!-- Left: Register Doctor form -->
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg xl:col-span-1">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2 pb-3 border-b border-slate-800">
            <svg class="h-5 w-5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Register Consultant
        </h3>
        
        <form action="{{ route('admin.doctors.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Doctor Full Name *</label>
                <input type="text" id="name" name="name" required
                       class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                       placeholder="e.g. Dr. Robert Jones (Neurology)">
            </div>
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email Address *</label>
                <input type="email" id="email" name="email" required
                       class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                       placeholder="e.g. r.jones@slmedicare.lk">
            </div>

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

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="dob" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">DOB</label>
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
                <label for="address" class="block text-xs font-semibold text-slate-455 uppercase tracking-wider mb-2">Consultation Room</label>
                <input type="text" id="address" name="address"
                       class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm"
                       placeholder="e.g. Room 104, Outpatient Building">
            </div>

            <button type="submit"
                    class="w-full bg-brand-blue hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md transition duration-200 text-sm">
                Add Doctor Account
            </button>
        </form>
    </div>

    <!-- Right: Doctor logs -->
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg xl:col-span-2">
        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3">
            Registered Consultants Database
        </h3>

        @if (empty($doctorsList) || count($doctorsList) === 0)
            <div class="text-center py-12 text-slate-500">
                No doctors registered in the database.
            </div>
        @else
            <div class="space-y-3.5">
                @foreach ($doctorsList as $doc)
                    <div class="bg-[#131a26]/70 border border-slate-800 hover:border-blue-500/40 p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-6 transition duration-300">
                        <div class="flex items-start gap-4">
                            <div class="h-12 w-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-brand-sky font-bold text-lg shrink-0">
                                {{ strtoupper(substr($doc->profile->full_name ?? 'D', 0, 1)) }}
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-md font-bold text-white leading-none">{{ $doc->profile->full_name ?? 'N/A' }}</h4>
                                <span class="text-[10px] bg-blue-500/20 text-brand-sky font-bold px-2 py-0.5 rounded border border-blue-500/20 uppercase inline-block">
                                    Consultant MD
                                </span>
                                <div class="text-xs text-slate-400 font-medium pt-1">
                                    <span class="block">Email: {{ $doc->email }}</span>
                                    <span class="block">Phone: {{ $doc->profile->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left md:text-right space-y-1">
                            <div class="text-xs text-slate-350 font-bold uppercase tracking-wider">Consultation Location</div>
                            <div class="text-sm font-semibold text-slate-200">
                                {{ $doc->profile->address ?? 'No Office Allocated' }}
                            </div>
                            <div class="text-[10px] text-slate-500">
                                DOB: {{ $doc->profile->dob ?? 'N/A' }} | Gender: {{ $doc->profile->gender ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

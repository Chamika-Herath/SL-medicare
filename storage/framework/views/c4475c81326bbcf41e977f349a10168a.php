<?php $__env->startSection('title', 'Appointments'); ?>

<?php $__env->startSection('content'); ?>
<!-- Title Header -->
<div class="flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Appointments</h1>
        <p class="text-sm text-slate-400">Manage, request, and verify consultation times.</p>
    </div>
</div>

<!-- Messages -->
<?php if(session('success')): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl p-4 text-sm flex items-center gap-2 shadow">
        <span class="font-bold">Success:</span> <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl p-4 text-sm flex items-center gap-2 shadow">
        <span class="font-bold">Error:</span> <?php echo e($errors->first()); ?>

    </div>
<?php endif; ?>

<!-- Form & Details Layout Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    
    <!-- Left: Scheduling Card (PATIENT only) -->
    <?php if(Auth::user()->role === 'PATIENT'): ?>
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg xl:col-span-1">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Book Appointment
        </h3>
        <form action="<?php echo e(route('appointments.book')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label for="doctor_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Select Specialist *</label>
                <select id="doctor_id" name="doctor_id" required
                        class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm">
                    <option value="" class="bg-[#0d131f]">Choose Doctor</option>
                    <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($doc->id); ?>" class="bg-[#0d131f]"><?php echo e($doc->profile->full_name ?? $doc->email); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label for="appointment_date" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Preferred Date & Time *</label>
                <input type="datetime-local" id="appointment_date" name="appointment_date" required
                       class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-slate-350 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm">
            </div>

            <div>
                <label for="reason" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reason for Consultation</label>
                <textarea id="reason" name="reason" rows="3"
                          class="w-full bg-[#131a26] border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue placeholder-slate-500 text-sm"
                          placeholder="Brief description of symptoms..."></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-brand-blue hover:bg-blue-700 text-white font-medium py-3 rounded-xl shadow-md transition duration-200 text-sm">
                Submit Appointment Request
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Right: Appointment Logs -->
    <div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg <?php echo e((Auth::user()->role === 'PATIENT') ? 'xl:col-span-2' : 'xl:col-span-3'); ?>">
        <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3">
            Appointment Schedules
        </h3>

        <?php if(empty($appointments) || count($appointments) === 0): ?>
            <div class="text-center py-12 text-slate-500">
                No appointments booked yet.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-450 text-xs font-semibold uppercase tracking-wider">
                            <th class="pb-3 pr-4">Patient</th>
                            <th class="pb-3 pr-4">Doctor</th>
                            <th class="pb-3 pr-4">Scheduled Date</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-sm text-slate-300">
                        <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-900/40 transition">
                                <td class="py-4 pr-4">
                                    <span class="font-bold text-white"><?php echo e($appt->patient->profile->full_name ?? 'N/A'); ?></span>
                                    <?php if(Auth::user()->role === 'DOCTOR' && isset($appt->patient->profile->phone)): ?>
                                        <span class="block text-xs text-slate-500 font-medium"><?php echo e($appt->patient->profile->phone); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 pr-4 font-semibold text-slate-400">
                                    <?php echo e($appt->doctor->profile->full_name ?? 'N/A'); ?>

                                </td>
                                <td class="py-4 pr-4 text-brand-sky font-mono text-xs font-semibold">
                                    <?php echo e(date('Y-m-d h:i A', strtotime($appt->appointment_date))); ?>

                                </td>
                                <td class="py-4 pr-4">
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider <?php echo e($appt->status === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                            ($appt->status === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20')); ?>">
                                        <?php echo e($appt->status); ?>

                                    </span>
                                </td>
                                <td class="py-4">
                                    <?php if(Auth::user()->role === 'DOCTOR' && $appt->status === 'PENDING'): ?>
                                        <form action="<?php echo e(route('appointments.updateStatus', $appt->id)); ?>" method="POST" class="inline-flex gap-2">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" name="action" value="approve"
                                                    class="px-3 py-1 bg-brand-blue hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                                                Approve
                                            </button>
                                            <button type="submit" name="action" value="cancel"
                                                    class="px-3 py-1 bg-red-950/40 hover:bg-red-900/20 text-red-400 font-semibold text-xs rounded-lg border border-red-900/30 transition">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-500 italic">No actions</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hmcdi\OneDrive\Documents\GITHUB\SL-medicare\resources\views/appointments.blade.php ENDPATH**/ ?>
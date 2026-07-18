<?php $__env->startSection('title', 'Appointment History'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-white font-sans">Appointment Log History</h1>
        <p class="text-sm text-slate-400">View logs of all past, present, and pending appointments.</p>
    </div>
</div>

<!-- List Card -->
<div class="bg-[#0d131f] border border-slate-800 rounded-2xl p-6 shadow-lg">
    <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-3">
        Full Appointment Logs
    </h3>

    <?php if(empty($appointments) || count($appointments) === 0): ?>
        <div class="text-center py-12 text-slate-500">
            No appointment logs found for your clinic.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-450 text-xs font-semibold uppercase tracking-wider">
                        <th class="pb-3 pr-4">Patient Name</th>
                        <th class="pb-3 pr-4">Scheduled Date</th>
                        <th class="pb-3 pr-4">Consultation Reason</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-sm text-slate-350">
                    <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-900/40 transition">
                            <!-- Patient -->
                            <td class="py-4 pr-4">
                                <span class="font-bold text-slate-200"><?php echo e($appt->patient->profile->full_name ?? 'N/A'); ?></span>
                                <span class="block text-[10px] text-slate-500">Email: <?php echo e($appt->patient->email); ?></span>
                            </td>
                            <!-- Scheduled Date -->
                            <td class="py-4 pr-4 text-brand-sky font-mono text-xs font-semibold">
                                <?php echo e(date('Y-m-d h:i A', strtotime($appt->appointment_date))); ?>

                            </td>
                            <!-- Reason -->
                            <td class="py-4 pr-4 italic max-w-xs truncate text-xs text-slate-400" title="<?php echo e($appt->reason); ?>">
                                "<?php echo e($appt->reason ?? 'No reason provided'); ?>"
                            </td>
                            <!-- Status -->
                            <td class="py-4 pr-4">
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider <?php echo e($appt->status === 'APPROVED' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                        ($appt->status === 'PENDING' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20')); ?>">
                                    <?php echo e($appt->status); ?>

                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="py-4">
                                <?php if($appt->status === 'PENDING'): ?>
                                    <form action="<?php echo e(route('appointments.updateStatus', $appt->id)); ?>" method="POST" class="inline-flex gap-2">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" name="action" value="approve"
                                                class="px-2.5 py-1 bg-brand-blue hover:bg-blue-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                                            Approve
                                        </button>
                                        <button type="submit" name="action" value="cancel"
                                                class="px-2.5 py-1 bg-red-955/20 hover:bg-red-900/20 text-red-400 font-bold text-xs rounded-lg border border-red-900/20 transition">
                                            Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-500 font-medium">Logged</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\hmcdi\OneDrive\Documents\GITHUB\SL-medicare\resources\views/doctor/appointments_history.blade.php ENDPATH**/ ?>
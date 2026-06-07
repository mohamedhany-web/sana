<?php
    $status = $status ?? '';
    $priority = $priority ?? '';
    $statusClasses = [
        'open' => 'bg-sky-100 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-100 text-amber-900 border-amber-200',
        'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
    $priorityClasses = [
        'low' => 'bg-slate-100 text-slate-600 border-slate-200',
        'normal' => 'bg-slate-50 text-slate-700 border-slate-200',
        'high' => 'bg-orange-100 text-orange-800 border-orange-200',
        'urgent' => 'bg-rose-100 text-rose-800 border-rose-300',
    ];
?>
<?php if($status !== ''): ?>
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?php echo e($statusClasses[$status] ?? 'bg-slate-100 text-slate-700'); ?>">
        <?php echo e(\App\Models\SupportTicket::statusLabels()[$status] ?? $status); ?>

    </span>
<?php endif; ?>
<?php if($priority !== ''): ?>
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?php echo e($priorityClasses[$priority] ?? 'bg-slate-100 text-slate-700'); ?>">
        <?php echo e(\App\Models\SupportTicket::priorityLabels()[$priority] ?? $priority); ?>

    </span>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\support-tickets\_badges.blade.php ENDPATH**/ ?>
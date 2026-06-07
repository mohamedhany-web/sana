<?php $__env->startSection('title', __('parent.child_progress', ['name' => $student->name])); ?>

<?php $__env->startSection('content'); ?>
<div class="par-page space-y-5 sm:space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('parent.children.index')); ?>" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0" aria-label="رجوع">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="par-page-title truncate"><?php echo e($student->name); ?></h1>
            <p class="par-page-lead"><?php echo e(__('parent.progress')); ?>: <strong class="text-teal-600"><?php echo e($progress); ?>%</strong></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-4 gap-4 sm:gap-5">
        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-book-open text-teal-600"></i> <?php echo e(__('parent.courses_enrolled')); ?></h2>
            <?php if($enrollments->count() > 0): ?>
            <ul class="space-y-3 text-sm">
                <?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 border-b border-slate-100 pb-2">
                    <span class="text-slate-800 min-w-0"><?php echo e($enrollment->course->title ?? '—'); ?></span>
                    <span class="font-mono font-bold text-teal-600 shrink-0"><?php echo e((int)($enrollment->progress ?? 0)); ?>%</span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-slate-500 text-sm">لا توجد كورسات مسجّلة حالياً</p>
            <?php endif; ?>
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-tasks text-amber-600"></i> <?php echo e(__('parent.upcoming_assignments')); ?></h2>
            <?php if($upcomingAssignments->count() > 0): ?>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $upcomingAssignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="p-3 rounded-lg bg-slate-50">
                    <p class="font-semibold text-slate-800"><?php echo e($assignment->title); ?></p>
                    <?php if($assignment->due_date): ?>
                    <p class="text-xs text-slate-500 mt-1">موعد التسليم: <?php echo e($assignment->due_date->format('d/m/Y')); ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-slate-500 text-sm">لا توجد واجبات قادمة</p>
            <?php endif; ?>
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-receipt text-violet-600"></i> <?php echo e(__('parent.recent_orders')); ?></h2>
            <?php if($recentOrders->count() > 0): ?>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex flex-col sm:flex-row sm:justify-between gap-1">
                    <span class="min-w-0"><?php echo e($order->course->title ?? $order->order_number ?? 'طلب'); ?></span>
                    <span class="font-mono text-slate-600 shrink-0"><?php echo e($order->status); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-slate-500 text-sm">لا توجد طلبات</p>
            <?php endif; ?>
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-file-lines text-cyan-600"></i> <?php echo e(__('parent.student_reports')); ?></h2>
            <?php if($reports->count() > 0): ?>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex flex-wrap items-center justify-between gap-2">
                    <span><?php echo e($report->report_month ?? $report->created_at->format('Y-m')); ?></span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100"><?php echo e($report->status); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-slate-500 text-sm">لا توجد تقارير بعد</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\children\show.blade.php ENDPATH**/ ?>
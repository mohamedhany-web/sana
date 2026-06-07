<?php $__env->startSection('title', 'سجل العمل'); ?>
<?php $__env->startSection('header', 'سجل ساعات العمل'); ?>
<?php echo $__env->make('instructor.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="id-tutor-page space-y-6 pb-6 w-full">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900 m-0">سجل ساعات العمل</h2>
            <p class="text-sm text-slate-500 mt-1 mb-0">التسجيل اليومي وملخص الحصص</p>
        </div>
        <a href="<?php echo e(route('instructor.tutor-lessons.hub')); ?>" class="id-btn-ghost">← <?php echo e(__('tutor.hub_title')); ?></a>
    </div>

    <div class="id-kpi max-w-xs">
        <span class="id-kpi-icon" style="background:linear-gradient(135deg,#283593,#4338ca)"><i class="fas fa-clock"></i></span>
        <p class="text-2xl font-black m-0"><?php echo e($todayMinutes); ?></p>
        <p class="text-xs font-bold text-slate-600 m-0">دقيقة مسجّلة اليوم</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <form method="post" action="<?php echo e(route('instructor.tutor-lessons.work-log.store')); ?>" class="id-panel id-form">
            <div class="id-panel-head"><h3 class="font-bold m-0">تسجيل يدوي</h3></div>
            <div class="id-panel-body space-y-3"><?php echo csrf_field(); ?>
                <div><label>التاريخ</label><input type="date" name="work_date" value="<?php echo e(today()->toDateString()); ?>" required></div>
                <div><label>الدقائق</label><input type="number" name="minutes" min="1" required></div>
                <div><label>ملاحظة</label><input name="notes"></div>
                <button type="submit" class="id-btn-primary w-full justify-center">تسجيل</button>
            </div>
        </form>
        <div class="id-panel">
            <div class="id-panel-head"><h3 class="font-bold m-0">السجل</h3></div>
            <div class="id-panel-body max-h-96 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="text-sm py-2 border-b border-slate-100 flex justify-between gap-2">
                        <span class="font-semibold"><?php echo e($log->work_date->format('Y-m-d')); ?></span>
                        <span class="text-slate-600"><?php echo e($log->minutes); ?> د</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-slate-500 text-sm">لا يوجد سجل بعد.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\tutor-lessons\work-log.blade.php ENDPATH**/ ?>
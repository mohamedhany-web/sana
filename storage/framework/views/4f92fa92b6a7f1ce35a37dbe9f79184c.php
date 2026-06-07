<?php $__env->startSection('title', __('tutor.admin_hub_title')); ?>
<?php $__env->startSection('header', __('tutor.admin_hub_title')); ?>
<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-violet-600"><?php echo e($stats['active_tutors']); ?></p>
            <p class="text-sm text-slate-600 mt-1">معلمون مفعّلون للحجز</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-amber-600"><?php echo e($stats['pending_bookings']); ?></p>
            <p class="text-sm text-slate-600 mt-1">حجوزات بانتظار التأكيد</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-sky-600"><?php echo e($stats['upcoming']); ?></p>
            <p class="text-sm text-slate-600 mt-1">حصص قادمة</p>
        </div>
        <div class="bg-white rounded-2xl border p-5 shadow-sm">
            <p class="text-3xl font-black text-rose-600"><?php echo e($stats['open_assisted']); ?></p>
            <p class="text-sm text-slate-600 mt-1">طلبات مساعدة مفتوحة</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-bold text-slate-900">آخر الحجوزات</h2>
            <a href="<?php echo e(route('admin.tutor-lessons.bookings')); ?>" class="text-sm text-violet-600 font-bold">عرض الكل</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr>
                <th class="p-3 text-right">الطالب</th>
                <th class="p-3 text-right">المعلم</th>
                <th class="p-3">الموعد</th>
                <th class="p-3">الحالة</th>
            </tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t hover:bg-slate-50">
                    <td class="p-3"><?php echo e($b->student?->name); ?></td>
                    <td class="p-3"><?php echo e($b->instructor?->name); ?></td>
                    <td class="p-3"><?php echo e($b->scheduled_at?->format('Y-m-d H:i')); ?></td>
                    <td class="p-3"><a href="<?php echo e(route('admin.tutor-lessons.bookings.show', $b)); ?>" class="text-violet-600 font-bold"><?php echo e($b->status); ?></a></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="p-6 text-center text-slate-500">لا توجد حجوزات بعد</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\index.blade.php ENDPATH**/ ?>


<?php $__env->startSection('title', 'عروض حجز المجموعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900">عروض حجز المجموعات</h1>
            <p class="text-sm text-slate-600 mt-1">اربط كل عرض بمعلّم، حجم المجموعة، باقة دورات، واشتراكات الطلاب المسموح لها.</p>
        </div>
        <a href="<?php echo e(route('admin.tutor-lessons.group-offers.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700">
            <i class="fas fa-plus"></i> عرض جديد
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-sm font-semibold"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="text-right px-4 py-3 font-bold">العرض</th>
                        <th class="text-right px-4 py-3 font-bold">المعلّم</th>
                        <th class="text-right px-4 py-3 font-bold">الحجم</th>
                        <th class="text-right px-4 py-3 font-bold">الباقات</th>
                        <th class="text-right px-4 py-3 font-bold">الحالة</th>
                        <th class="text-right px-4 py-3 font-bold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="px-4 py-3">
                            <strong class="text-slate-900"><?php echo e($offer->title); ?></strong>
                            <?php if($offer->package): ?>
                                <p class="text-xs text-slate-500 mt-0.5">باقة: <?php echo e($offer->package->name); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo e($offer->instructor?->name ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($offer->min_group_size); ?>–<?php echo e($offer->max_group_size); ?> طلاب</td>
                        <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($offer->planKeysLabel()); ?></td>
                        <td class="px-4 py-3">
                            <?php if($offer->is_active): ?>
                                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">نشط</span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-left whitespace-nowrap">
                            <a href="<?php echo e(route('admin.tutor-lessons.group-offers.edit', $offer)); ?>" class="text-violet-600 font-bold text-xs ml-3">تعديل</a>
                            <form method="post" action="<?php echo e(route('admin.tutor-lessons.group-offers.destroy', $offer)); ?>" class="inline" onsubmit="return confirm('حذف هذا العرض؟')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-rose-600 font-bold text-xs">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد عروض مجموعات بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($offers->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($offers->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\group-offers\index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', __('admin.academic_years')); ?>
<?php $__env->startSection('header', __('admin.academic_years')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <div class="bg-gradient-to-l from-[#1D4EDB] via-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span><?php echo e(__('admin.academic_years')); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold"><?php echo e(__('admin.academic_years')); ?></h1>
                <p class="text-sm text-white/90 mt-1 max-w-2xl">
                    نظّم المراحل الدراسية (ابتدائي، متوسط، ثانوي…) ثم أضف المواد تحت كل مرحلة واربطها بالكورسات والمعلّمين.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.academic-subjects.index')); ?>" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white px-4 py-2.5 rounded-xl font-medium border border-white/25">
                    <i class="fas fa-book"></i>
                    <?php echo e(__('admin.academic_subjects')); ?>

                </a>
                <a href="<?php echo e(route('admin.academic-years.create')); ?>" class="inline-flex items-center gap-2 bg-white text-[#1D4EDB] hover:bg-slate-100 px-4 py-2.5 rounded-xl font-semibold">
                    <i class="fas fa-plus"></i>
                    إضافة مرحلة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">إجمالي المراحل</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['total']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">نشطة</p>
            <p class="text-2xl font-bold text-emerald-600"><?php echo e($summary['active']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">المواد</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['subjects']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">كورسات مرتبطة</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['courses']); ?></p>
        </div>
    </div>

    <?php if($years->isNotEmpty()): ?>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-bold">المرحلة</th>
                            <th class="px-4 py-3 text-right font-bold">الترتيب</th>
                            <th class="px-4 py-3 text-right font-bold">المواد</th>
                            <th class="px-4 py-3 text-right font-bold">الكورسات</th>
                            <th class="px-4 py-3 text-right font-bold">الحالة</th>
                            <th class="px-4 py-3 text-right font-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0" style="background: <?php echo e($year->color ?? '#1D4EDB'); ?>">
                                        <i class="<?php echo e($year->icon ?? 'fas fa-layer-group'); ?>"></i>
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900"><?php echo e($year->name); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo e($year->code); ?></p>
                                        <?php if($year->description): ?>
                                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo e($year->description); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-semibold"><?php echo e($year->order); ?></td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-900"><?php echo e($year->subjects_count); ?></span>
                                <?php if($year->active_subjects_count < $year->subjects_count): ?>
                                    <span class="text-xs text-slate-400">(<?php echo e($year->active_subjects_count); ?> نشطة)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-semibold"><?php echo e($year->courses_count); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($year->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                    <?php echo e($year->is_active ? 'نشطة' : 'موقوفة'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.academic-subjects.index', ['track' => $year->id])); ?>" class="px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 text-xs font-bold hover:bg-sky-100">
                                        المواد
                                    </a>
                                    <a href="<?php echo e(route('admin.academic-years.edit', $year)); ?>" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">تعديل</a>
                                    <form method="POST" action="<?php echo e(route('admin.academic-years.toggle-status', $year)); ?>"><?php echo csrf_field(); ?>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold hover:bg-amber-100">
                                            <?php echo e($year->is_active ? 'إيقاف' : 'تفعيل'); ?>

                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.academic-years.destroy', $year)); ?>" onsubmit="return confirm('حذف المرحلة؟');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold hover:bg-rose-100">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4">
                <i class="fas fa-layer-group"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">لا توجد مراحل دراسية بعد</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">
                أضف أول مرحلة (مثل: أول ابتدائي، ثالث ثانوي) ثم أنشئ المواد تحتها من قسم المواد الدراسية.
            </p>
            <a href="<?php echo e(route('admin.academic-years.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus"></i> إضافة مرحلة
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\academic-years\index.blade.php ENDPATH**/ ?>
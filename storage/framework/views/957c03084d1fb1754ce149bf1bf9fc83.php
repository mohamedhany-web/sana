<?php $__env->startSection('title', __('admin.academic_subjects')); ?>
<?php $__env->startSection('header', __('admin.academic_subjects')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <div class="bg-gradient-to-l from-[#1D4EDB] via-indigo-600 to-violet-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <nav class="text-sm text-white/80 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-white">لوحة التحكم</a>
                    <span class="mx-2">/</span>
                    <span><?php echo e(__('admin.academic_subjects')); ?></span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold"><?php echo e(__('admin.academic_subjects')); ?></h1>
                <p class="text-sm text-white/90 mt-1 max-w-2xl">
                    أضف المواد من هنا — تظهر للطلاب والمعلّمين عند تفعيلها (نشطة)، وتُربط بالكورسات عبر حقل «المادة» في الكورس.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white px-4 py-2.5 rounded-xl font-medium border border-white/25">
                    <i class="fas fa-layer-group"></i>
                    <?php echo e(__('admin.academic_years')); ?>

                </a>
                <a href="<?php echo e(route('admin.academic-subjects.create', $currentTrack ? ['track' => $currentTrack->id] : [])); ?>" class="inline-flex items-center gap-2 bg-white text-[#1D4EDB] hover:bg-slate-100 px-4 py-2.5 rounded-xl font-semibold">
                    <i class="fas fa-plus"></i>
                    إضافة مادة
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">إجمالي المواد</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['total']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">نشطة</p>
            <p class="text-2xl font-bold text-emerald-600"><?php echo e($summary['active']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">كورسات مرتبطة</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['courses']); ?></p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 mb-1">معلّمون</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo e($summary['instructors']); ?></p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[14rem] flex-1">
                <label class="block text-xs font-semibold text-slate-600 mb-1">تصفية حسب المرحلة</label>
                <select name="track" class="w-full rounded-xl border-slate-200 text-sm" onchange="this.form.submit()">
                    <option value="">كل المراحل</option>
                    <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($id); ?>" <?php if(($currentTrack->id ?? null) == $id): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php if($currentTrack): ?>
                <a href="<?php echo e(route('admin.academic-subjects.index')); ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-900">إزالة التصفية</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if($subjects->isNotEmpty()): ?>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-4 py-3 text-right font-bold">المادة</th>
                            <th class="px-4 py-3 text-right font-bold">المرحلة</th>
                            <th class="px-4 py-3 text-right font-bold">الكورسات</th>
                            <th class="px-4 py-3 text-right font-bold">المعلّمون</th>
                            <th class="px-4 py-3 text-right font-bold">الحالة</th>
                            <th class="px-4 py-3 text-right font-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0" style="background: <?php echo e($subject->color ?? '#1D4EDB'); ?>">
                                        <i class="<?php echo e($subject->icon ?? 'fas fa-book'); ?>"></i>
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900"><?php echo e($subject->name); ?></p>
                                        <p class="text-xs text-slate-500"><?php echo e($subject->code); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($subject->academicYear->name ?? '—'); ?></td>
                            <td class="px-4 py-3 font-semibold"><?php echo e($subject->courses_count); ?></td>
                            <td class="px-4 py-3 font-semibold"><?php echo e($subject->instructors_count); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($subject->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                    <?php echo e($subject->is_active ? 'نشطة' : 'موقوفة'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('public.courses', ['subject' => $subject->id])); ?>" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200" title="معاينة في الموقع">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.academic-subjects.edit', $subject)); ?>" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">تعديل</a>
                                    <form method="POST" action="<?php echo e(route('admin.academic-subjects.toggle-status', $subject)); ?>"><?php echo csrf_field(); ?>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold hover:bg-amber-100">
                                            <?php echo e($subject->is_active ? 'إيقاف' : 'تفعيل'); ?>

                                        </button>
                                    </form>
                                    <form method="POST" action="<?php echo e(route('admin.academic-subjects.destroy', $subject)); ?>" onsubmit="return confirm('حذف المادة؟');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
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
                <i class="fas fa-book"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">لا توجد مواد بعد</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">
                <?php if(($tracks ?? collect())->isEmpty()): ?>
                    أضف مرحلة دراسية أولاً، ثم أنشئ المواد واربطها بالكورسات والمعلّمين.
                <?php else: ?>
                    أنشئ أول مادة لتظهر في الصفحة الرئيسية ونماذج المعلّمين والطلاب.
                <?php endif; ?>
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <?php if(($tracks ?? collect())->isEmpty()): ?>
                    <a href="<?php echo e(route('admin.academic-years.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 text-white font-semibold">
                        <i class="fas fa-layer-group"></i> إضافة مرحلة
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.academic-subjects.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold">
                    <i class="fas fa-plus"></i> إضافة مادة
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\academic-subjects\index.blade.php ENDPATH**/ ?>
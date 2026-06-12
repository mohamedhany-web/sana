<?php $__env->startSection('title', 'رقابة الطلاب'); ?>
<?php $__env->startSection('header', 'رقابة الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statCards = [
        ['label' => 'إجمالي الطلاب', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fa-user-graduate', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'desc' => 'كل حسابات الطلاب'],
        ['label' => 'نشطون', 'value' => number_format($stats['active'] ?? 0), 'icon' => 'fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'يمكنهم الدخول'],
        ['label' => 'باشتراك فعّال', 'value' => number_format($stats['with_subscription'] ?? 0), 'icon' => 'fa-credit-card', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'desc' => 'باقة نشطة حالياً'],
        ['label' => 'جدد هذا الشهر', 'value' => number_format($stats['new_month'] ?? 0), 'icon' => 'fa-user-plus', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'desc' => 'تسجيلات حديثة'],
    ];
?>

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-graduate text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">رقابة الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">متابعة شاملة — اضغط على الطالب لعرض كل بياناته وتقاريره</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.quality-control.index')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                <i class="fas fa-tachometer-alt text-violet-500"></i>
                لوحة الرقابة
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e($card['value']); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                <input type="search" name="search" value="<?php echo e($search ?? request('search')); ?>"
                       placeholder="بحث بالاسم، البريد، أو الجوال…"
                       class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-violet-500">
                <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="">كل الحالات</option>
                    <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>غير نشط</option>
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-bold hover:bg-violet-700">بحث</button>
                <?php if(request()->hasAny(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.quality-control.students')); ?>" class="px-4 py-2.5 text-sm font-semibold text-slate-600">مسح</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        <th class="px-4 py-3">الطالب</th>
                        <th class="px-4 py-3">التواصل</th>
                        <th class="px-4 py-3">التسجيلات</th>
                        <th class="px-4 py-3">حصص</th>
                        <th class="px-4 py-3">اشتراك</th>
                        <th class="px-4 py-3">آخر دخول</th>
                        <th class="px-4 py-3">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-violet-50/40 transition-colors">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.quality-control.students.show', $student)); ?>"
                                   class="font-bold text-violet-700 hover:text-violet-900"><?php echo e($student->name); ?></a>
                                <?php if($student->academicYear): ?>
                                    <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($student->academicYear->name); ?></p>
                                <?php endif; ?>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($student->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'); ?>">
                                    <?php echo e($student->is_active ? 'نشط' : 'معطّل'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <span dir="ltr" class="block"><?php echo e($student->phone ?? '—'); ?></span>
                                <span class="text-xs text-slate-500 break-all"><?php echo e($student->email ?? '—'); ?></span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-bold text-slate-900"><?php echo e($student->course_enrollments_count); ?></span>
                                <?php if(($student->completed_enrollments_count ?? 0) > 0): ?>
                                    <span class="text-xs text-emerald-600">(<?php echo e($student->completed_enrollments_count); ?> مكتمل)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-800"><?php echo e($student->lesson_bookings_as_student_count ?? 0); ?></td>
                            <td class="px-4 py-3">
                                <?php if(($student->active_subscriptions_count ?? 0) > 0): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800">نعم</span>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                <?php echo e($student->last_login_at ? $student->last_login_at->diffForHumans() : 'لم يدخل'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.quality-control.students.show', $student)); ?>"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-violet-100 text-violet-800 text-xs font-bold hover:bg-violet-200">
                                    <i class="fas fa-chart-line"></i>
                                    التفاصيل
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-sm text-slate-500">
                                لا يوجد طلاب مطابقون للبحث.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($students->hasPages()): ?>
            <div class="px-5 py-3 border-t border-slate-200"><?php echo e($students->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\quality-control\students.blade.php ENDPATH**/ ?>
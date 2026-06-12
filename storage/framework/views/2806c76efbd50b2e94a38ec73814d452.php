<?php $__env->startSection('title', 'باقات واشتراكات الطلاب'); ?>
<?php $__env->startSection('header', 'باقات واشتراكات الطلاب'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $statCards = [
        ['label' => 'طلاب باشتراك نشط', 'value' => $stats['active_students_with_subscription'] ?? 0, 'icon' => 'fa-user-check', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'desc' => 'اشتراك ساري المفعول'],
        ['label' => 'بدون اشتراك', 'value' => $stats['students_without_subscription'] ?? 0, 'icon' => 'fa-user-clock', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'desc' => 'من إجمالي ' . number_format($stats['total_students'] ?? 0) . ' طالب'],
        ['label' => 'اشتراكات نشطة', 'value' => $stats['active_subscriptions'] ?? 0, 'icon' => 'fa-credit-card', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'desc' => 'سجلات اشتراك فعّالة'],
        ['label' => 'تذاكر دعم مفتوحة', 'value' => $stats['open_support_tickets'] ?? 0, 'icon' => 'fa-headset', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'desc' => 'للطلاب'],
    ];
?>

<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">باقات واشتراكات الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">نظامنا الحالي: <strong>باقات حصص مع المعلم</strong> (ساعات في الاشتراك) — وليس مزايا باقات المدرب (AI، Classroom، إلخ)</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(Route::has('admin.subscriptions.create')): ?>
                    <a href="<?php echo e(route('admin.subscriptions.create')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-violet-600 rounded-xl hover:bg-violet-700">
                        <i class="fas fa-plus"></i>
                        إضافة اشتراك
                    </a>
                <?php endif; ?>
                <?php if(Route::has('admin.tutor-lessons.settings')): ?>
                    <a href="<?php echo e(route('admin.tutor-lessons.settings')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-violet-700 bg-violet-50 border border-violet-200 rounded-xl hover:bg-violet-100">
                        <i class="fas fa-cog"></i>
                        قوالب الباقات
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900 mt-1"><?php echo e(number_format($card['value'])); ?></p>
                            <p class="text-[11px] text-slate-500 mt-1"><?php echo e($card['desc']); ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-lg <?php echo e($card['bg']); ?> <?php echo e($card['text']); ?> flex items-center justify-center shrink-0">
                            <i class="fas <?php echo e($card['icon']); ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-600 flex flex-wrap gap-4">
            <span><i class="fas fa-clock text-violet-500 ml-1"></i> الساعات الافتراضية بدون اشتراك: <strong><?php echo e((int) ($tutorDefaults['default_student_lesson_hours'] ?? 0)); ?></strong> س/شهر</span>
            <?php if(($stats['students_with_support_feature'] ?? 0) > 0): ?>
                <span><i class="fas fa-headset text-sky-500 ml-1"></i> طلاب بميزة دعم في الاشتراك: <strong><?php echo e($stats['students_with_support_feature']); ?></strong></span>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-900">قوالب الباقات (أساسية / قياسية / مميزة)</h3>
                <p class="text-xs text-slate-500 mt-0.5">تُعرَّف في إعدادات حصص الطلاب وتُطبَّق من «إضافة اشتراك»</p>
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php $__currentLoopData = $planCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.students-control.paid-features.show', $plan['key'])); ?>"
                   class="rounded-2xl border border-slate-200 p-5 hover:border-violet-400 hover:shadow-md transition-all block bg-white">
                    <p class="text-lg font-black text-slate-900"><?php echo e($plan['label']); ?></p>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?php echo e($plan['card_subtitle']); ?></p>
                    <p class="text-2xl font-black text-violet-700 mt-3">
                        <?php echo e($fmtPrice($plan['price'])); ?>

                        <span class="text-sm font-semibold text-slate-500"><?php echo e(__('public.currency')); ?></span>
                    </p>
                    <p class="text-xs font-bold text-violet-800 mt-2">
                        <i class="fas fa-clock ml-1"></i>
                        <?php echo e($plan['hours']); ?> ساعة حصص / شهر
                    </p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg">
                        <i class="fas fa-users text-violet-600"></i>
                        <?php echo e(number_format($plan['students_count'])); ?> طالب نشط
                    </p>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-4">
            <a href="<?php echo e(route('admin.students-control.paid-features.show', 'custom')); ?>"
               class="block rounded-2xl border border-dashed border-violet-300 bg-violet-50/40 p-5 hover:bg-violet-50 transition-colors">
                <p class="text-sm font-black text-violet-900">باقات مخصصة</p>
                <p class="text-xs text-violet-800/90 mt-1">اشتراكات يدوية بدون قالب student_*</p>
                <p class="text-xl font-black text-slate-900 mt-3"><?php echo e(number_format($customPlanStats['students_count'] ?? 0)); ?> طالب</p>
                <p class="text-[11px] text-slate-500"><?php echo e(number_format($customPlanStats['subscriptions_count'] ?? 0)); ?> اشتراك</p>
            </a>

            <?php $__currentLoopData = $capabilityCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.students-control.paid-features.show', $cap['key'])); ?>"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 transition-colors">
                    <span class="w-10 h-10 rounded-lg <?php echo e($cap['icon_bg']); ?> <?php echo e($cap['icon_text']); ?> flex items-center justify-center shrink-0">
                        <i class="fas <?php echo e($cap['icon']); ?>"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-900"><?php echo e($cap['label']); ?></p>
                        <p class="text-[11px] text-slate-500 line-clamp-2"><?php echo e($cap['description']); ?></p>
                    </div>
                    <span class="text-sm font-black text-slate-800"><?php echo e(number_format($cap['students_count'])); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <section class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">آخر اشتراكات الطلاب النشطة</h3>
                <?php if(Route::has('admin.subscriptions.index')): ?>
                    <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-xs font-bold text-violet-600 hover:underline">كل الاشتراكات</a>
                <?php endif; ?>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الطالب</th>
                            <th class="px-4 py-3">الخطة</th>
                            <th class="px-4 py-3">ساعات</th>
                            <th class="px-4 py-3">ينتهي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $recentSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $hours = is_array($sub->feature_limits) ? (int) ($sub->feature_limits['tutor_lesson_hours'] ?? 0) : 0;
                            ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($sub->user->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-medium"><?php echo e($sub->plan_name); ?></span>
                                    <?php if($sub->teacher_plan_key): ?>
                                        <span class="block text-slate-400 font-mono text-[10px]"><?php echo e($sub->teacher_plan_key); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-violet-700"><?php echo e($hours); ?> س</td>
                                <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($sub->end_date?->format('Y-m-d') ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500 text-sm">لا توجد اشتراكات نشطة للطلاب.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 text-sm text-amber-900">
        <p class="font-bold mb-1"><i class="fas fa-info-circle ml-1"></i> ملاحظة للإدارة</p>
        <p class="text-xs leading-relaxed">مزايا مثل <strong>أدوات AI</strong> و<strong>Classroom</strong> مخصّصة لاشتراكات <strong>المدربين</strong> (teacher_starter / teacher_pro). للطلاب ركّز على ساعات الحصص والدعم الفني عند تفعيله في الاشتراك.</p>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\student-control\paid-features.blade.php ENDPATH**/ ?>
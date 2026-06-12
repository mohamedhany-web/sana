<?php $__env->startSection('title', $pageTitle); ?>
<?php $__env->startSection('header', $pageTitle); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <nav class="text-sm text-slate-500 flex flex-wrap items-center gap-1">
        <a href="<?php echo e(route('admin.students-control.paid-features')); ?>" class="text-violet-600 font-semibold hover:underline">باقات الطلاب</a>
        <span>/</span>
        <span class="text-slate-700"><?php echo e($pageTitle); ?></span>
    </nav>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900"><?php echo e($pageTitle); ?></h1>
                <p class="text-sm text-slate-600 mt-1"><?php echo e($pageSubtitle); ?></p>
            </div>
            <a href="<?php echo e(route('admin.students-control.paid-features')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <p class="text-sm font-bold text-slate-800"><?php echo e($users->total()); ?> طالب</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600 uppercase">
                    <tr>
                        <th class="px-4 py-3">الطالب</th>
                        <th class="px-4 py-3">الخطة / الاشتراك</th>
                        <th class="px-4 py-3">ساعات الباقة</th>
                        <th class="px-4 py-3">المستخدم / المتبقي</th>
                        <th class="px-4 py-3">ينتهي</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $sub = $user->subscriptions->first();
                            $profile = $user->studentLearningProfile;
                            $quota = (int) ($profile->lesson_hours_quota ?? 0);
                            $used = (int) ($profile->lesson_hours_used ?? 0);
                            $subHours = $sub && is_array($sub->feature_limits)
                                ? (int) ($sub->feature_limits['tutor_lesson_hours'] ?? 0)
                                : 0;
                            $hasSupport = $sub && (
                                \App\Services\StudentControlOverviewService::subscriptionHasFeature($sub, 'support')
                                || \App\Services\StudentControlOverviewService::subscriptionHasFeature($sub, 'direct_support')
                            );
                        ?>
                        <tr class="hover:bg-violet-50/30">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900"><?php echo e($user->name); ?></p>
                                <p class="text-xs text-slate-500" dir="ltr"><?php echo e($user->phone ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-800"><?php echo e($sub->plan_name ?? '—'); ?></p>
                                <?php if($hasSupport): ?>
                                    <span class="inline-flex mt-1 text-[10px] font-bold text-sky-700 bg-sky-100 px-1.5 py-0.5 rounded">دعم فني</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-bold text-violet-700"><?php echo e($subHours); ?> س</td>
                            <td class="px-4 py-3 text-xs">
                                <span class="text-slate-700"><?php echo e($used); ?> / <?php echo e($quota); ?> س</span>
                                <span class="block text-emerald-700 font-semibold">متبقي <?php echo e(max(0, $quota - $used)); ?> س</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?php echo e($sub?->end_date?->format('Y-m-d') ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1 text-xs font-bold">
                                    <?php if(Route::has('admin.quality-control.students.show')): ?>
                                        <a href="<?php echo e(route('admin.quality-control.students.show', $user)); ?>" class="text-violet-600 hover:underline">رقابة</a>
                                    <?php endif; ?>
                                    <?php if(Route::has('admin.users.edit')): ?>
                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="text-blue-600 hover:underline">الحساب</a>
                                    <?php endif; ?>
                                    <?php if($sub && Route::has('admin.subscriptions.edit')): ?>
                                        <a href="<?php echo e(route('admin.subscriptions.edit', $sub)); ?>" class="text-slate-600 hover:underline">الاشتراك</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">لا يوجد طلاب في هذا التصنيف.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($users->hasPages()): ?>
            <div class="px-5 py-4 border-t border-slate-200"><?php echo e($users->links()); ?></div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\student-control\plan-students.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'إضافة اشتراك'); ?>
<?php $__env->startSection('header', 'إضافة اشتراك'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $starter = $starter ?? ($teacherPlans['teacher_starter'] ?? []);
    $pro = $pro ?? ($teacherPlans['teacher_pro'] ?? []);
?>
<div class="space-y-6" x-data="subscriptionAdminForm(<?php echo \Illuminate\Support\Js::from($initialSubscriberRole ?? '')->toHtml() ?>)">
    <?php echo $__env->make('admin.subscriptions._subscriptions-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إضافة اشتراك جديد</h1>
        <p class="text-sm text-slate-600 mb-6">طالب → اختر <strong>باقة أساسية</strong> أو <strong>باقة مخصصة</strong> (ساعات حصص مع المعلم). مدرب → قالب باقة أو إدخال يدوي. القوالب من <a href="<?php echo e(route('admin.tutor-lessons.settings')); ?>" class="text-violet-600 font-bold underline">إعدادات حصص الطلاب</a>.</p>

        <form action="<?php echo e(route('admin.subscriptions.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">المشترك *</label>
                    <input type="text" id="mx-user-search" placeholder="ابحث بالاسم أو الهاتف..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg mb-2">
                    <select id="mx-user-select" name="user_id" required @change="onUserChange()" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">اختر طالباً أو مدرباً</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" data-role="<?php echo e($user->role); ?>" <?php if(old('user_id') == $user->id): echo 'selected'; endif; ?>>
                                <?php if(in_array($user->role, ['instructor', 'teacher'], true)): ?> [مدرب] <?php else: ?> [طالب] <?php endif; ?>
                                <?php echo e($user->name); ?> — <?php echo e($user->phone); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="md:col-span-2" x-show="isInstructor()" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-2">قالب باقة مدرب (اختياري)</label>
                    <select name="teacher_plan_key" x-model="selectedPlan" @change="applyPlan" :disabled="!isInstructor()" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">بدون — إدخال يدوي</option>
                        <?php if($starter): ?>
                            <option value="teacher_starter"><?php echo e($starter['label'] ?? 'أساسية'); ?> — <?php echo e($fmtPrice($starter['price'] ?? 0)); ?> <?php echo e(__('public.currency')); ?></option>
                        <?php endif; ?>
                        <?php if($pro): ?>
                            <option value="teacher_pro"><?php echo e($pro['label'] ?? 'شاملة'); ?> — <?php echo e($fmtPrice($pro['price'] ?? 0)); ?> <?php echo e(__('public.currency')); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <?php echo $__env->make('admin.subscriptions._student-package-picker', ['fmtPrice' => $fmtPrice], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاشتراك *</label>
                    <select name="subscription_type" x-model="form.subscription_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <?php $__currentLoopData = $typeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الخطة *</label>
                    <input type="text" name="plan_name" x-model="form.plan_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                    <input type="number" name="price" x-model.number="form.price" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية *</label>
                    <input type="date" name="start_date" required value="<?php echo e(old('start_date', date('Y-m-d'))); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء *</label>
                    <input type="date" name="end_date" required value="<?php echo e(old('end_date', date('Y-m-d', strtotime('+1 month')))); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">دورة الفوترة *</label>
                    <select name="billing_cycle" x-model="form.billing_cycle" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                        <option value="yearly">سنوي</option>
                    </select>
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="auto_renew" value="1" class="rounded border-gray-300">
                <span class="text-sm">تجديد تلقائي</span>
            </label>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3" x-show="isInstructor()" x-cloak>
                <h2 class="text-sm font-semibold text-gray-800">مزايا باقة المدرب</h2>
                <?php echo $__env->make('admin.subscriptions._subscription-feature-checkboxes', [
                    'featureKeysOrder' => $featureKeysOrder,
                    'featureDisplayLines' => $featureDisplayLines,
                    'checkedKeys' => $checkedFeatures,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <?php echo $__env->make('admin.subscriptions._subscription-limit-fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="flex gap-4">
                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-medium">إنشاء الاشتراك</button>
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php echo $__env->make('admin.subscriptions._subscription-form-script', ['subscription' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/admin/subscriptions/create.blade.php ENDPATH**/ ?>
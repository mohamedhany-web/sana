<?php $__env->startSection('title', __('parent.profile')); ?>

<?php $__env->startSection('content'); ?>
<div class="par-page space-y-5 sm:space-y-6">
    <?php if(session('success')): ?>
    <div class="par-flash par-flash--ok flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <div class="par-hero par-card p-4 sm:p-6">
        <h1 class="par-page-title font-heading">
            <i class="fas fa-id-card text-teal-600 ml-2"></i><?php echo e(__('parent.profile')); ?>

        </h1>
        <p class="par-page-lead">حدّث بياناتك وكلمة المرور لحماية حساب ولي الأمر</p>
    </div>

    <form action="<?php echo e(route('parent.profile.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="par-form-layout">
            <div class="par-card p-4 sm:p-5 flex flex-col items-center text-center sm:flex-row sm:text-start sm:items-center gap-4 xl:flex-col xl:items-stretch xl:text-center">
                <div class="par-avatar mx-auto xl:mx-auto">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e($profileImageUrl); ?>" alt="<?php echo e($user->name); ?>">
                    <?php else: ?>
                        <?php echo e(mb_substr($user->name, 0, 1)); ?>

                    <?php endif; ?>
                </div>
                <div class="min-w-0 flex-1 xl:flex-none">
                    <p class="font-bold text-lg text-slate-900 truncate"><?php echo e($user->name); ?></p>
                    <p class="text-sm text-slate-500 truncate mt-0.5" dir="ltr"><?php echo e($user->email); ?></p>
                    <span class="par-badge par-badge--teal mt-2"><?php echo e(__('parent.guardian_role')); ?></span>
                </div>
            </div>

            <div class="par-form-columns">
                <div class="par-card p-4 sm:p-6 space-y-4">
                    <div class="par-section-head">
                        <i class="fas fa-user bg-teal-50 text-teal-600"></i>
                        البيانات الشخصية
                    </div>

                    <div class="par-field">
                        <label for="par-name">الاسم</label>
                        <input type="text" id="par-name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="par-field">
                        <label for="par-email">البريد الإلكتروني</label>
                        <input type="email" id="par-email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required dir="ltr">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <?php if(!str_starts_with((string) $user->phone, 'PARENT_')): ?>
                    <div class="par-field">
                        <label for="par-phone">الهاتف</label>
                        <input type="text" id="par-phone" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>">
                    </div>
                    <?php endif; ?>

                    <div class="par-field">
                        <label for="par-avatar">صورة الملف (اختياري)</label>
                        <input type="file" id="par-avatar" name="profile_image" accept="image/*">
                    </div>
                </div>

                <div class="par-card p-4 sm:p-6 space-y-4">
                    <div class="par-section-head">
                        <i class="fas fa-key bg-amber-50 text-amber-600"></i>
                        <?php echo e(__('parent.change_password')); ?>

                    </div>
                    <p class="text-xs text-slate-500 -mt-2 mb-1">اترك الحقول فارغة إذا لا تريد تغيير كلمة المرور</p>

                    <div class="par-field">
                        <label for="par-current-pw">كلمة المرور الحالية</label>
                        <input type="password" id="par-current-pw" name="current_password" autocomplete="current-password">
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="par-field">
                        <label for="par-new-pw">كلمة المرور الجديدة</label>
                        <input type="password" id="par-new-pw" name="password" autocomplete="new-password">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="par-field">
                        <label for="par-confirm-pw">تأكيد كلمة المرور</label>
                        <input type="password" id="par-confirm-pw" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="par-btn par-btn--primary w-full sm:w-auto">
            <i class="fas fa-save"></i> حفظ التغييرات
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\profile\index.blade.php ENDPATH**/ ?>
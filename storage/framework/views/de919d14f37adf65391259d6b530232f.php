<?php $__env->startSection('title', __('parent.settings')); ?>

<?php $__env->startSection('content'); ?>
<div class="par-page space-y-5 sm:space-y-6">
    <div class="par-hero par-card p-4 sm:p-6">
        <h1 class="par-page-title font-heading">
            <i class="fas fa-cog text-teal-600 ml-2"></i><?php echo e(__('parent.settings')); ?>

        </h1>
        <p class="par-page-lead">إعدادات حساب ولي الأمر وطريقة الدخول</p>
    </div>

    <div class="par-info-grid">
        <div class="par-card p-4 sm:p-5">
            <div class="par-section-head mb-3">
                <i class="fas fa-sign-in-alt bg-teal-50 text-teal-600"></i>
                طريقة الدخول
            </div>
            <p class="text-sm text-slate-600 leading-relaxed"><?php echo e(__('parent.default_login_hint')); ?></p>
        </div>
        <div class="par-card p-4 sm:p-5">
            <div class="par-section-head mb-3">
                <i class="fas fa-shield-alt bg-amber-50 text-amber-600"></i>
                الأمان
            </div>
            <p class="text-sm text-slate-600 leading-relaxed mb-4">غيّر كلمة المرور الافتراضية من الملف الشخصي لتأمين حسابك.</p>
            <a href="<?php echo e(route('parent.profile')); ?>" class="par-btn par-btn--ghost w-full sm:w-auto">
                <i class="fas fa-key"></i> <?php echo e(__('parent.change_password')); ?>

            </a>
        </div>
    </div>

    <div class="par-card p-4 sm:p-6">
        <div class="par-section-head">
            <i class="fas fa-circle-info bg-cyan-50 text-cyan-600"></i>
            بيانات الدخول
        </div>
        <div class="par-info-grid par-info-grid--3">
            <div class="par-info-tile flex items-start gap-3 h-full">
                <span class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center shrink-0"><i class="fas fa-envelope text-sm"></i></span>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-0.5">البريد</p>
                    <p class="text-sm text-slate-800"><strong>نفس بريد الطالب</strong></p>
                </div>
            </div>
            <div class="par-info-tile flex items-start gap-3 h-full">
                <span class="w-9 h-9 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0"><i class="fas fa-user-tag text-sm"></i></span>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase mb-0.5">نوع الحساب</p>
                    <p class="text-sm text-slate-800"><strong>ولي أمر</strong> في صفحة تسجيل الدخول</p>
                </div>
            </div>
            <div class="par-info-tile flex items-start gap-3 h-full">
                <span class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><i class="fas fa-link text-sm"></i></span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase mb-0.5">رابط الدخول</p>
                    <a href="<?php echo e(route('login')); ?>" class="text-sm font-bold text-teal-700 hover:underline break-all"><?php echo e(url('/login')); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\settings\index.blade.php ENDPATH**/ ?>
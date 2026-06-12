<?php $__env->startSection('title', 'مراجعة طلب معلم - ' . config('app.name', 'Sana')); ?>
<?php $__env->startSection('header', 'مراجعة طلب انضمام معلم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = $application->user;
    $statusLabels = [
        \App\Models\InstructorProfile::STATUS_PENDING_REVIEW => ['بانتظار الموافقة', 'bg-amber-100 text-amber-800'],
        \App\Models\InstructorProfile::STATUS_APPROVED => ['مقبول', 'bg-emerald-100 text-emerald-800'],
        \App\Models\InstructorProfile::STATUS_REJECTED => ['مرفوض', 'bg-rose-100 text-rose-800'],
    ];
    [$statusLabel, $statusClass] = $statusLabels[$application->status] ?? [$application->status, 'bg-slate-100 text-slate-700'];
    $accountActive = (bool) ($user?->is_active);
    $canManageAccount = $user && !\App\Services\InstructorApplicationService::mustKeepAccountActive($user);
?>

<div class="space-y-6 sm:space-y-8">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3"><?php echo e(session('info')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('admin.instructor-applications.index')); ?>" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span>
        <?php if($user): ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($accountActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700'); ?>">
                <i class="fas <?php echo e($accountActive ? 'fa-circle-check' : 'fa-ban'); ?> ml-1"></i>
                <?php echo e($accountActive ? 'الحساب مفعّل' : 'الحساب موقوف'); ?>

            </span>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.instructor-applications.edit', $application)); ?>" data-turbo="false"
           class="inline-flex items-center gap-2 rounded-2xl bg-sky-50 text-sky-700 px-4 py-2 text-sm font-semibold hover:bg-sky-100">
            <i class="fas fa-pen"></i>
            تعديل
        </a>
    </div>

    
    <?php if($user): ?>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fas fa-user-shield text-sky-600"></i>
            إدارة الحساب
        </h3>
        <div class="flex flex-wrap gap-3">
            <?php if($canManageAccount): ?>
                <?php if($accountActive): ?>
                    <form method="POST" action="<?php echo e(route('admin.instructor-applications.deactivate-account', $application)); ?>" data-turbo="false">
                        <?php echo csrf_field(); ?>
                        <button type="submit" onclick="return confirm('إيقاف حساب هذا المعلم؟ لن يتمكن من تسجيل الدخول.')"
                                class="inline-flex items-center gap-2 rounded-2xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                            <i class="fas fa-pause-circle"></i>
                            إيقاف الحساب
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('admin.instructor-applications.activate-account', $application)); ?>" data-turbo="false">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                            <i class="fas fa-play-circle"></i>
                            تفعيل الحساب
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('admin.instructor-applications.toggle-account', $application)); ?>" data-turbo="false">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-sync-alt"></i>
                        تبديل حالة الحساب
                    </button>
                </form>
            <?php else: ?>
                <p class="text-sm text-slate-500">هذا الحساب محمي (إداري/موظف) — لا يمكن إيقافه من هنا.</p>
            <?php endif; ?>

            <?php if($application->status !== \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
                <form method="POST" action="<?php echo e(route('admin.instructor-applications.reopen', $application)); ?>" data-turbo="false">
                    <?php echo csrf_field(); ?>
                    <button type="submit" onclick="return confirm('إعادة الطلب لقائمة المراجعة؟')"
                            class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                        <i class="fas fa-redo"></i>
                        إعادة للمراجعة
                    </button>
                </form>
            <?php endif; ?>

            <?php if($canManageAccount): ?>
                <form method="POST" action="<?php echo e(route('admin.instructor-applications.destroy', $application)); ?>" data-turbo="false"
                      onsubmit="return confirm('حذف الطلب نهائياً؟ سيتم حذف الملف التعريفي وإيقاف الحساب.')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                        <i class="fas fa-trash-alt"></i>
                        حذف الطلب
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm flex flex-wrap items-center justify-between gap-2">
        <div>
            <strong class="text-slate-900"><?php echo e($user?->name ?? 'معلم'); ?></strong>
            <span class="text-slate-500"> — قدّم <?php echo e($application->submitted_at?->diffForHumans() ?? '—'); ?></span>
        </div>
        <?php if($application->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
            <span class="text-xs text-slate-600">لوحة المعلم: <strong><?php echo e($application->portalModeLabel()); ?></strong></span>
        <?php endif; ?>
    </section>

    <?php if($application->rejection_reason): ?>
    <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4">
        <p class="text-xs font-semibold text-rose-700 mb-1">سبب الرفض السابق</p>
        <p class="text-sm text-rose-900 m-0"><?php echo e($application->rejection_reason); ?></p>
    </div>
    <?php endif; ?>

    <?php echo $__env->make('admin.instructor-applications.partials.application-details', ['application' => $application], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('admin.instructor-applications.partials.evaluation-form', ['application' => $application], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($application->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <section class="rounded-3xl bg-white/95 border border-emerald-200 shadow-lg p-6 sm:p-8">
            <h3 class="text-lg font-bold text-emerald-800 mb-4">قبول الطلب</h3>
            <p class="text-sm text-slate-600 mb-4">سيتم تفعيل حساب المعلم ويمكنه تسجيل الدخول من بوابة المدربين فوراً.</p>
            <form method="POST" action="<?php echo e(route('admin.instructor-applications.approve', $application)); ?>" class="space-y-4" data-turbo="false">
                <?php echo csrf_field(); ?>
                <?php echo $__env->make('admin.instructor-applications.partials.portal-mode-fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">ملاحظة للمعلم (اختياري)</label>
                    <textarea name="admin_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"
                              placeholder="رسالة ترسل مع إشعار القبول"><?php echo e(old('admin_note')); ?></textarea>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700"
                        onclick="return confirm('تأكيد قبول هذا المعلم وتفعيل حسابه؟')">
                    <i class="fas fa-check"></i>
                    قبول وتفعيل الحساب
                </button>
            </form>
        </section>

        <section class="rounded-3xl bg-white/95 border border-rose-200 shadow-lg p-6 sm:p-8">
            <h3 class="text-lg font-bold text-rose-800 mb-4">رفض الطلب</h3>
            <form method="POST" action="<?php echo e(route('admin.instructor-applications.reject', $application)); ?>" class="space-y-4" data-turbo="false">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">سبب الرفض <span class="text-rose-600">*</span></label>
                    <textarea name="rejection_reason" rows="4" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"
                              placeholder="يُرسل للمعلم في الإشعار"><?php echo e(old('rejection_reason')); ?></textarea>
                    <?php $__errorArgs = ['rejection_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-rose-600 px-4 py-3 text-sm font-bold text-white hover:bg-rose-700"
                        onclick="return confirm('تأكيد رفض هذا الطلب؟')">
                    <i class="fas fa-times"></i>
                    رفض الطلب
                </button>
            </form>
        </section>
    </div>
    <?php elseif($application->status === \App\Models\InstructorProfile::STATUS_REJECTED): ?>
    <section class="rounded-3xl bg-white/95 border border-emerald-200 shadow-lg p-6 sm:p-8">
        <h3 class="text-lg font-bold text-emerald-800 mb-4">إعادة قبول الطلب</h3>
        <p class="text-sm text-slate-600 mb-4">يمكنك قبول هذا المعلم رغم الرفض السابق — سيتم تفعيل حسابه.</p>
        <form method="POST" action="<?php echo e(route('admin.instructor-applications.approve', $application)); ?>" class="space-y-4" data-turbo="false">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.instructor-applications.partials.portal-mode-fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-2">ملاحظة للمعلم (اختياري)</label>
                <textarea name="admin_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm"></textarea>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700"
                    onclick="return confirm('تأكيد قبول هذا المعلم؟')">
                <i class="fas fa-check"></i>
                قبول الطلب
            </button>
        </form>
    </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\instructor-applications\show.blade.php ENDPATH**/ ?>
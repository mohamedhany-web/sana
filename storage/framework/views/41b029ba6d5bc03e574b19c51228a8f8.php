<?php $__env->startSection('title', 'تأكيد المصادقة الثنائية'); ?>
<?php $__env->startSection('page_title', 'تأكيد المصادقة الثنائية'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard admin-list-page admin-form-page max-w-2xl mx-auto space-y-6 pb-10">

    <?php echo $__env->make('admin.partials.alert-success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('admin.partials.alert-errors', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'تأكيد عبر البريد','subtitle' => 'أدخل الرمز المكوّن من 6 أرقام المرسل إلى بريدك.','icon' => 'fas fa-envelope-open-text']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'تأكيد عبر البريد','subtitle' => 'أدخل الرمز المكوّن من 6 أرقام المرسل إلى بريدك.','icon' => 'fas fa-envelope-open-text']); ?>
        <a href="<?php echo e(route('admin.system-settings.edit')); ?>" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            رجوع
        </a>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2cbd75565df7390cff0c13630dbdb99a)): ?>
<?php $attributes = $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a; ?>
<?php unset($__attributesOriginal2cbd75565df7390cff0c13630dbdb99a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2cbd75565df7390cff0c13630dbdb99a)): ?>
<?php $component = $__componentOriginal2cbd75565df7390cff0c13630dbdb99a; ?>
<?php unset($__componentOriginal2cbd75565df7390cff0c13630dbdb99a); ?>
<?php endif; ?>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h2><i class="fas fa-key"></i> رمز التحقق</h2>
            <?php if($userEmail): ?>
                <p class="admin-panel__sub" dir="ltr"><?php echo e($userEmail); ?></p>
            <?php endif; ?>
        </div>
        <div class="admin-panel__body space-y-6">
            <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.confirm.submit')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div class="admin-field max-w-xs">
                    <label for="code">الرمز</label>
                    <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required maxlength="10"
                           autocomplete="one-time-code" inputmode="numeric"
                           class="admin-input text-center text-2xl tracking-[0.35em] font-black admin-input--mono"
                           placeholder="000000" dir="ltr">
                    <p class="admin-field-hint">صالح 15 دقيقة.</p>
                </div>
                <div class="admin-form-actions !border-0 !pt-0 !mt-0">
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-check"></i>
                        تأكيد التفعيل
                    </button>
                    <a href="<?php echo e(route('admin.system-settings.edit')); ?>" class="admin-btn admin-btn--outline">إلغاء</a>
                </div>
            </form>
            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">لم يصلك الرمز؟</p>
                <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.resend')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="admin-btn admin-btn--ghost text-sm">
                        <i class="fas fa-redo"></i>
                        إعادة الإرسال
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\system-settings\two-factor-confirm.blade.php ENDPATH**/ ?>
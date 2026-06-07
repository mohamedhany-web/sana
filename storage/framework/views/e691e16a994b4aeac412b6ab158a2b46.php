<?php $__env->startSection('title', 'عرض الرسالة - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'عرض الرسالة'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard admin-list-page space-y-7">

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => $contactMessage->subject,'subtitle' => 'من: ' . $contactMessage->name . ' · ' . $contactMessage->created_at->translatedFormat('d F Y — H:i'),'icon' => 'fas fa-envelope-open-text']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contactMessage->subject),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('من: ' . $contactMessage->name . ' · ' . $contactMessage->created_at->translatedFormat('d F Y — H:i')),'icon' => 'fas fa-envelope-open-text']); ?>
        <a href="<?php echo e(route('admin.contact-messages.index')); ?>" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
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

    <div class="admin-panel max-w-4xl">
        <div class="admin-panel__head">
            <h2><i class="fas fa-user"></i> بيانات المرسل</h2>
            <?php if($contactMessage->read_at): ?>
                <span class="admin-badge admin-badge--success"><i class="fas fa-check-circle"></i> مقروءة</span>
            <?php else: ?>
                <span class="admin-badge admin-badge--danger"><i class="fas fa-circle text-[6px]"></i> غير مقروءة</span>
            <?php endif; ?>
        </div>
        <div class="admin-panel__body space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">الاسم</p>
                    <p class="admin-detail-field__value"><?php echo e($contactMessage->name); ?></p>
                </div>
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">البريد الإلكتروني</p>
                    <p class="admin-detail-field__value"><?php echo e($contactMessage->email); ?></p>
                </div>
                <?php if($contactMessage->phone): ?>
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">رقم الهاتف</p>
                    <p class="admin-detail-field__value"><?php echo e($contactMessage->phone); ?></p>
                </div>
                <?php endif; ?>
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">تاريخ الإرسال</p>
                    <p class="admin-detail-field__value"><?php echo e($contactMessage->created_at->format('Y-m-d H:i')); ?></p>
                </div>
                <?php if($contactMessage->read_at): ?>
                <div class="admin-detail-field">
                    <p class="admin-detail-field__label">تاريخ القراءة</p>
                    <p class="admin-detail-field__value"><?php echo e($contactMessage->read_at->format('Y-m-d H:i')); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-500 mb-2">نص الرسالة</p>
                <div class="admin-message-body"><?php echo e($contactMessage->message); ?></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\contact-messages\show.blade.php ENDPATH**/ ?>
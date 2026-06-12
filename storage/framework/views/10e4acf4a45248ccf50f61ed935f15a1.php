<?php $__env->startSection('title', 'إضافة خدمة - ' . ($platformName ?? config('brand.name', config('app.name')))); ?>
<?php $__env->startSection('page_title', 'إضافة خدمة'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full w-full max-w-none space-y-7">

    <?php if (isset($component)) { $__componentOriginal2cbd75565df7390cff0c13630dbdb99a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2cbd75565df7390cff0c13630dbdb99a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.page-hero','data' => ['title' => 'خدمة جديدة','subtitle' => 'يُنشأ الرابط تلقائياً من الاسم إن تركت حقل الرابط فارغاً (أحرف إنجليزية وشرطة).','icon' => 'fas fa-plus-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.page-hero'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'خدمة جديدة','subtitle' => 'يُنشأ الرابط تلقائياً من الاسم إن تركت حقل الرابط فارغاً (أحرف إنجليزية وشرطة).','icon' => 'fas fa-plus-circle']); ?>
        <a href="<?php echo e(route('admin.site-services.index')); ?>" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
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

    <div class="admin-panel w-full">
        <div class="admin-panel__head">
            <h2><i class="fas fa-edit"></i> بيانات الخدمة</h2>
        </div>
        <div class="admin-panel__body">
            <form action="<?php echo e(route('admin.site-services.store')); ?>" method="POST" enctype="multipart/form-data" class="admin-form-layout space-y-6">
                <?php echo csrf_field(); ?>

                <div class="admin-form-layout__grid">
                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>اسم الخدمة <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255" class="admin-input">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="admin-field">
                            <label>الرابط في المتصفح (اختياري)</label>
                            <input type="text" name="slug" value="<?php echo e(old('slug')); ?>" placeholder="teacher-training" class="admin-input admin-input--mono">
                            <p class="admin-field-hint">فقط a-z و 0-9 و شرطة. يُترك فارغاً للإنشاء التلقائي من الاسم.</p>
                            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="admin-field">
                            <label>مقدمة قصيرة (بطاقة القائمة)</label>
                            <textarea name="summary" rows="4" maxlength="2000" class="admin-textarea"><?php echo e(old('summary')); ?></textarea>
                            <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>صورة الخدمة (اختياري)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                            <p class="admin-field-hint">للبطاقة وصفحة الخدمة. عند ضبط <code>SITE_SERVICES_DISK=r2</code> في ملف البيئة تُرفع على R2.</p>
                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="admin-form-side-card">
                            <div class="admin-field">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" class="admin-input">
                                <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="admin-field pt-1">
                                <input type="hidden" name="is_active" value="0">
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_active" value="1" <?php if((string) old('is_active', '1') !== '0'): echo 'checked'; endif; ?>>
                                    <span>نشط ويظهر في الموقع</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-field">
                    <label>تفاصيل الخدمة <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="14" required class="admin-textarea admin-textarea--tall"><?php echo e(old('body')); ?></textarea>
                    <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-xs mt-1.5 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="admin-form-actions">
                    <a href="<?php echo e(route('admin.site-services.index')); ?>" class="admin-btn admin-btn--outline">إلغاء</a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ الخدمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\site-services\create.blade.php ENDPATH**/ ?>
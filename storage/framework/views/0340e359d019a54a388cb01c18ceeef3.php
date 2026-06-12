

<?php
    $offer = $offer ?? null;
    $isEdit = $offer !== null;
    $selectedPlans = old('subscription_plan_keys', $offer?->subscription_plan_keys ?? []);
?>

<?php $__env->startSection('title', $isEdit ? 'تعديل عرض مجموعة' : 'عرض مجموعة جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <?php echo $__env->make('admin.tutor-lessons._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <h1 class="text-2xl font-black text-slate-900 mb-6"><?php echo e($isEdit ? 'تعديل عرض المجموعة' : 'عرض حجز مجموعة جديد'); ?></h1>

    <form method="post" action="<?php echo e($isEdit ? route('admin.tutor-lessons.group-offers.update', $offer) : route('admin.tutor-lessons.group-offers.store')); ?>" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="p-4 rounded-xl bg-rose-50 text-rose-800 text-sm space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p class="m-0"><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">عنوان العرض *</label>
            <input type="text" name="title" required maxlength="160" value="<?php echo e(old('title', $offer?->title)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">المعلّم *</label>
            <select name="instructor_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— اختر —</option>
                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->user_id); ?>" <?php if((int) old('instructor_id', $offer?->instructor_id) === (int) $p->user_id): echo 'selected'; endif; ?>><?php echo e($p->user?->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-slate-500 mt-1">يجب أن يدعم المعلّم نوع «مجموعة صغيرة» في إعداداته.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">أقل عدد طلاب *</label>
                <input type="number" name="min_group_size" min="2" max="30" required value="<?php echo e(old('min_group_size', $offer?->min_group_size ?? 2)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">أقصى عدد طلاب *</label>
                <input type="number" name="max_group_size" min="2" max="30" required value="<?php echo e(old('max_group_size', $offer?->max_group_size ?? 6)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">مدة الحصة (دقيقة) *</label>
                <input type="number" name="duration_minutes" min="30" max="180" required value="<?php echo e(old('duration_minutes', $offer?->duration_minutes ?? 60)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">سعر عرض (اختياري)</label>
                <input type="number" name="display_price" min="0" step="0.01" value="<?php echo e(old('display_price', $offer?->display_price)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">باقة دورات مرتبطة (اختياري)</label>
            <select name="package_id" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— بدون —</option>
                <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($pkg->id); ?>" <?php if((int) old('package_id', $offer?->package_id) === (int) $pkg->id): echo 'selected'; endif; ?>><?php echo e($pkg->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">المادة (اختياري)</label>
            <select name="academic_subject_id" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                <option value="">— كل المواد —</option>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php if((int) old('academic_subject_id', $offer?->academic_subject_id) === (int) $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">اشتراكات الطلاب المسموح لها</label>
            <p class="text-xs text-slate-500 mb-2">اترك الكل غير محدّد = أي باقة تفعّل فيها «حجز المجموعات».</p>
            <div class="flex flex-wrap gap-3">
                <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <input type="checkbox" name="subscription_plan_keys[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $selectedPlans, true)): echo 'checked'; endif; ?>>
                        <?php echo e($studentPlans[$key]['label'] ?? $key); ?>

                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-200"><?php echo e(old('description', $offer?->description)); ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 items-end">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">ترتيب العرض</label>
                <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $offer?->sort_order ?? 0)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
            </div>
            <label class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" <?php if(filter_var(old('is_active', $offer?->is_active ?? true), FILTER_VALIDATE_BOOLEAN)): echo 'checked'; endif; ?>>
                نشط
            </label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-sm">حفظ</button>
            <a href="<?php echo e(route('admin.tutor-lessons.group-offers.index')); ?>" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\admin\tutor-lessons\group-offers\form.blade.php ENDPATH**/ ?>
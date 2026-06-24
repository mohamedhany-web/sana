<?php $__env->startSection('title', 'مساعدة في إيجاد معلم'); ?>
<?php $__env->startSection('header', 'مساعدة في إيجاد معلم'); ?>
<?php echo $__env->make('student.tutor-lessons.partials.dashboard-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="sd-page w-full pb-8 max-w-2xl">
    <div class="sd-hero mb-6">
        <div class="sd-hero-main relative z-[1]">
            <p class="text-xs font-bold sd-tag mb-2">للصغار ومع أولياء الأمور</p>
            <h1 class="font-heading text-xl font-black text-slate-800">طلب مساعدة في إيجاد معلم</h1>
            <p class="text-sm text-slate-600 mt-2">سيتواصل معك فريق المنصة عبر الإشعارات داخل النظام.</p>
        </div>
    </div>
    <form method="post" action="<?php echo e(route('student.tutor-lessons.assisted.store')); ?>" class="sd-panel sd-form">
        <div class="sd-panel-body space-y-4"><?php echo csrf_field(); ?>
            <div><label>المواد</label><div class="flex flex-wrap gap-2"><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label class="sd-chip"><input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>"> <?php echo e($s->name); ?></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
            <div><label>نوع الحصة</label>
                <label class="sd-chip"><input type="radio" name="preferred_session_type" value="one_to_one" checked> فردي</label>
                <label class="sd-chip"><input type="radio" name="preferred_session_type" value="small_group"> مجموعة</label>
            </div>
            <div><label>رسالتك / احتياجاتك</label><textarea name="message" rows="4" required></textarea></div>
            <button type="submit" class="sd-btn-primary w-full">إرسال الطلب</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\assisted.blade.php ENDPATH**/ ?>
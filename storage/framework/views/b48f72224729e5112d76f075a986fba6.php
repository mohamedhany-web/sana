<?php $__env->startSection('title', 'طلب مساعدة'); ?>
<?php $__env->startSection('header', 'طلب مساعدة'); ?>
<?php echo $__env->make('partials.tutor-lesson-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="tl-page max-w-xl mx-auto py-2"><form method="post" action="<?php echo e(route('parent.tutor-lessons.assisted.store')); ?>" class="tl-card tl-form space-y-4"><?php echo csrf_field(); ?>
<div><label>الابن/الابنة</label><select name="student_id" required><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
<div><label>المواد</label><div class="flex flex-wrap gap-2"><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label class="tl-chip"><input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>"> <?php echo e($s->name); ?></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></div>
<div><label>الرسالة</label><textarea name="message" rows="4" required></textarea></div>
<button class="tl-btn tl-btn-primary w-full">إرسال — سنتواصل عبر المنصة</button></form></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\parent\tutor-lessons\assisted.blade.php ENDPATH**/ ?>
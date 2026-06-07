<?php
    $contactSupport = $course->usesContactSupportPricing();
    $isPaid = ! $contactSupport && $course->effectivePurchasePrice() > 0 && !($course->is_free ?? false);
    $blockClass = ($block ?? false) ? 'sana-course-cta sana-course-cta--block' : 'sana-course-cta';
    $variant = $variant ?? 'primary';
?>

<?php if($contactSupport): ?>
    <a href="<?php echo e($course->supportWhatsAppUrl()); ?>" target="_blank" rel="noopener noreferrer" class="<?php echo e($blockClass); ?> sana-course-cta--whatsapp <?php echo e($class ?? ''); ?>">
        <i class="fab fa-whatsapp"></i>
        <span><?php echo e(__('public.course_contact_support')); ?></span>
    </a>
<?php else: ?>
    <?php if(auth()->guard()->check()): ?>
        <?php if($isEnrolled ?? false): ?>
            <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="<?php echo e($blockClass); ?> <?php echo e($class ?? ''); ?>">
                <i class="fas fa-play-circle"></i>
                <span><?php echo e(__('public.start_learning_now')); ?></span>
            </a>
        <?php elseif($isPaid): ?>
            <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="<?php echo e($blockClass); ?> <?php echo e($class ?? ''); ?>">
                <i class="fas fa-graduation-cap"></i>
                <span>سجّل الآن</span>
            </a>
        <?php else: ?>
            <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST" <?php if($block ?? false): ?> class="w-full" <?php endif; ?>>
                <?php echo csrf_field(); ?>
                <button type="submit" class="<?php echo e($blockClass); ?> sana-course-cta--free <?php echo e($class ?? ''); ?>">
                    <i class="fas fa-gift"></i>
                    <span><?php echo e(__('public.register_free')); ?></span>
                </button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <?php if($isPaid): ?>
            <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="<?php echo e($blockClass); ?> <?php echo e($class ?? ''); ?>">
                <i class="fas fa-graduation-cap"></i>
                <span>سجّل الآن</span>
            </a>
        <?php else: ?>
            <a href="<?php echo e(route('register', ['redirect' => route('public.course.show', $course->id)])); ?>" class="<?php echo e($blockClass); ?> sana-course-cta--free <?php echo e($class ?? ''); ?>">
                <i class="fas fa-gift"></i>
                <span><?php echo e(__('public.register_free')); ?></span>
            </a>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\course-enroll-cta.blade.php ENDPATH**/ ?>
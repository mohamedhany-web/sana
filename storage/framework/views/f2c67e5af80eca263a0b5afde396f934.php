<?php

    $contactSupport = $course->usesContactSupportPricing();

    $isPaid = ! $contactSupport && $course->effectivePurchasePrice() > 0 && !($course->is_free ?? false);

    $btnClass = ($block ?? false) ? 'edu-btn-primary w-full' : 'edu-btn-primary';

    $freeClass = ($block ?? false) ? 'edu-btn-primary w-full !bg-emerald-600 hover:!bg-emerald-700' : 'edu-btn-primary !bg-emerald-600 hover:!bg-emerald-700';

    $waClass = ($block ?? false) ? 'edu-btn-primary w-full !bg-[#25D366] hover:!bg-[#1da851]' : 'edu-btn-primary !bg-[#25D366] hover:!bg-[#1da851]';

?>

<?php if($contactSupport): ?>

    <a href="<?php echo e($course->supportWhatsAppUrl()); ?>" target="_blank" rel="noopener noreferrer" class="<?php echo e($waClass); ?> <?php echo e($class ?? ''); ?>">

        <i class="fab fa-whatsapp"></i> <?php echo e(__('public.course_contact_support')); ?>


    </a>

<?php else: ?>

<?php if(auth()->guard()->check()): ?>

    <?php if($isEnrolled ?? false): ?>

        <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="<?php echo e($btnClass); ?> <?php echo e($class ?? ''); ?>">

            <i class="fas fa-play-circle"></i> <?php echo e(__('public.start_learning_now')); ?>


        </a>

    <?php elseif($isPaid): ?>

        <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="<?php echo e($btnClass); ?> <?php echo e($class ?? ''); ?>">

            <i class="fas fa-shopping-cart"></i> <?php echo e(__('public.buy_now')); ?>


        </a>

    <?php else: ?>

        <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST" <?php if($block ?? false): ?> class="w-full" <?php endif; ?>>

            <?php echo csrf_field(); ?>

            <button type="submit" class="<?php echo e($freeClass); ?> <?php echo e($class ?? ''); ?> cursor-pointer">

                <i class="fas fa-gift"></i> <?php echo e(__('public.register_free')); ?>


            </button>

        </form>

    <?php endif; ?>

<?php endif; ?>

<?php if(auth()->guard()->guest()): ?>

    <?php if($isPaid): ?>

        <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="<?php echo e($btnClass); ?> <?php echo e($class ?? ''); ?>">

            <i class="fas fa-shopping-cart"></i> <?php echo e(__('public.buy_now')); ?>


        </a>

    <?php else: ?>

        <a href="<?php echo e(route('register', ['redirect' => route('public.course.show', $course->id)])); ?>" class="<?php echo e($freeClass); ?> <?php echo e($class ?? ''); ?>">

            <i class="fas fa-gift"></i> <?php echo e(__('public.register_free')); ?>


        </a>

    <?php endif; ?>

<?php endif; ?>

<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\partials\course-enroll-cta.blade.php ENDPATH**/ ?>
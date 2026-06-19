<?php if(($featuredCourses ?? collect())->isNotEmpty()): ?>
<section class="sana-section sana-section--white" id="courses">
    <div class="sana-container">
        <div class="sana-head-row sana-reveal">
            <div class="sana-head">
                <h2 class="sana-head__title">أحدث <span class="hl">الدورات</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <a href="<?php echo e(route('public.courses')); ?>" class="sana-link-more">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="sana-courses-m">
            <?php $__currentLoopData = ($featuredCourses ?? collect())->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('landing.sana.partials.course-card', ['course' => $course, 'featured' => (bool) ($course->is_featured ?? false)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/sections/courses.blade.php ENDPATH**/ ?>
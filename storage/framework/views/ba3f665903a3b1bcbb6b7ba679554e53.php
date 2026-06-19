<?php
    $homeInstructorsList = ($homeInstructors ?? collect())->take(5);
?>
<?php if($homeInstructorsList->isNotEmpty()): ?>
<section class="sana-section" id="instructors">
    <div class="sana-container">
        <div class="sana-head-row sana-reveal">
            <div class="sana-head">
                <h2 class="sana-head__title">تعرّف على <span class="hl">معلّمينا</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-link-more">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="sana-teachers-m">
            <?php $__currentLoopData = $homeInstructorsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $name = $p->user->name ?? 'معلّم';
                $headline = $p->headline ?? 'معلّم على المنصة';
                $photo = $p->photo_path ? $p->photo_url : null;
                $subjects = array_slice($p->public_subject_labels ?? $p->skills_list ?? [], 0, 2);
            ?>
            <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
                <div class="sana-teacher-m__ring">
                    <?php if($photo): ?>
                        <img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>" loading="lazy">
                    <?php else: ?>
                        <span class="av"><?php echo e(mb_substr($name, 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <h3><?php echo e($name); ?></h3>
                <p class="role"><?php echo e(Str::limit($headline, 28)); ?></p>
                <?php if(count($subjects) > 0): ?>
                <p class="sana-teacher-m__tags"><?php echo e(implode(' · ', $subjects)); ?></p>
                <?php endif; ?>
                <?php if(!empty($p->is_bookable)): ?>
                <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> <?php echo e(__('public.instructor_book_with')); ?></span>
                <?php elseif(($p->courses_count ?? 0) > 0): ?>
                <span class="sana-teacher-m__book"><i class="fas fa-book-open"></i> <?php echo e((int) $p->courses_count); ?> دورة</span>
                <?php endif; ?>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/sections/teachers.blade.php ENDPATH**/ ?>
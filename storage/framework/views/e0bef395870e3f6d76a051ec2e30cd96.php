<?php
    $thumbUrl = ($course->thumbnail ?? null)
        ? public_storage_url($course->thumbnail)
        : \App\Support\PublicCourseCatalog::defaultCardImage();
    $hasPreview = !empty($introEmbedUrl) || !empty($introDirectVideo);
?>
<aside class="sana-cd-sidebar sana-reveal">
    <div class="sana-cd-enroll">
        <div class="sana-cd-enroll__thumb">
            <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>">
        </div>
        <div class="sana-cd-enroll__body">
            <div class="sana-cd-enroll__price-head">
                <?php if($course->usesContactSupportPricing()): ?>
                    <span class="sana-cd-enroll__price-free"><i class="fab fa-whatsapp"></i> تواصل للتسعير</span>
                <?php elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                    <?php if($course->hasPromotionalPrice()): ?>
                        <div class="sana-cd-enroll__price-old"><?php echo e(number_format($course->listPriceAmount(), 0)); ?> <?php echo e(__('public.currency')); ?></div>
                    <?php endif; ?>
                    <div class="sana-cd-enroll__price-now">
                        <?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?>

                        <span style="font-size:0.95rem;font-weight:700;color:var(--muted)"><?php echo e(__('public.currency')); ?></span>
                    </div>
                <?php else: ?>
                    <span class="sana-cd-enroll__price-free"><i class="fas fa-gift"></i> <?php echo e(__('public.free_price')); ?></span>
                <?php endif; ?>
            </div>

            <div class="sana-cd-enroll__actions">
                <?php echo $__env->make('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled, 'block' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if($hasPreview): ?>
                    <a href="#course-preview" class="sana-course-cta sana-course-cta--outline sana-course-cta--block">
                        <i class="fas fa-circle-play"></i>
                        <span>معاينة الدورة</span>
                    </a>
                <?php endif; ?>
            </div>

            <div class="sana-cd-enroll__trust">
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-shield-check"></i> ضمان استرداد خلال 7 أيام</div>
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-certificate"></i> شهادة إتمام معتمدة</div>
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-infinity"></i> وصول مدى الحياة</div>
            </div>

            <div class="sana-cd-enroll__stats">
                <div class="sana-cd-enroll__stat"><strong><?php echo e($course->lessons_count ?? 0); ?></strong> <?php echo e(__('public.lecture_single')); ?></div>
                <div class="sana-cd-enroll__stat"><strong><?php echo e($course->duration_hours ?? 0); ?></strong> <?php echo e(__('public.hours')); ?></div>
                <div class="sana-cd-enroll__stat"><strong><?php echo e(number_format($course->students_count ?? 0)); ?></strong> طالب</div>
                <div class="sana-cd-enroll__stat"><strong><?php echo e($course->rating ? number_format((float)$course->rating, 1) : '4.9'); ?></strong> تقييم</div>
            </div>
        </div>
    </div>

    <?php if(isset($relatedCourses) && $relatedCourses->isNotEmpty()): ?>
        <div class="sana-cd-section sana-reveal" style="margin-top:20px;padding:20px">
            <h3 class="sana-cd-section__title" style="margin-bottom:14px;font-size:1rem">دورات ذات صلة</h3>
            <div class="sana-cd-related">
                <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $relThumb = $related->thumbnail ? public_storage_url($related->thumbnail) : \App\Support\PublicCourseCatalog::defaultCardImage();
                    ?>
                    <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="sana-cd-related-item">
                        <img src="<?php echo e($relThumb); ?>" alt="" loading="lazy">
                        <div>
                            <strong style="font-size:0.82rem;line-height:1.4;display:block"><?php echo e(Str::limit($related->title, 48)); ?></strong>
                            <span style="font-size:0.72rem;color:var(--p);font-weight:800"><?php echo e(number_format($related->effectivePurchasePrice(), 0)); ?> <?php echo e(__('public.currency')); ?></span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</aside>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\course-enroll-sidebar.blade.php ENDPATH**/ ?>
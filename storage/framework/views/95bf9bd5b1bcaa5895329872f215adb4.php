<?php
    use App\Support\PublicCourseCatalog;

    /** @var \App\Models\AdvancedCourse|array $course */
    $c = is_array($course)
        ? $course
        : (PublicCourseCatalog::mapForCards(collect([$course]))[0] ?? []);

    $url = ($c['id'] ?? 0) > 0
        ? ($courseUrl ?? route('public.course.show', $c['id']))
        : route('public.courses');
    $featured = !empty($featured);
    $currency = __('public.currency');

    $priceType = 'normal';
    if (!empty($c['contact_support_for_pricing'])) {
        $priceType = 'whatsapp';
    } elseif (!empty($c['is_free'])) {
        $priceType = 'free';
    }

    $inst = $c['instructor'] ?? null;
    $instName = $inst['name'] ?? 'معلّم';
    $instAvatar = $inst['avatar_url'] ?? null;
    $subject = ($c['course_category']['name'] ?? null) ?: ($c['academic_subject']['name'] ?? null);
    $desc = Str::limit(strip_tags($c['description'] ?? 'دورة تعليمية تفاعلية مصمّمة لتجربة تعلّم ممتعة وفعّالة.'), 110);
    $progress = $c['enrollment_progress'] ?? null;
    $isEnrolled = !empty($c['is_enrolled']);
?>
<article class="<?php echo \Illuminate\Support\Arr::toCssClasses(['sana-course-card', 'sana-course-card--featured' => $featured, 'sana-reveal' => !($noReveal ?? false)]); ?>">
    <div class="sana-course-card__media">
        <a href="<?php echo e($url); ?>" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
            <img src="<?php echo e($c['card_image_url'] ?? $c['thumbnail_url'] ?? PublicCourseCatalog::defaultCardImage()); ?>"
                 alt="<?php echo e($c['title'] ?? ''); ?>" loading="lazy">
        </a>
        <div class="sana-course-card__shine" aria-hidden="true"></div>
        <div class="sana-course-card__badges">
            <?php if($subject): ?>
                <span class="sana-course-card__badge sana-course-card__badge--subject"><?php echo e($subject); ?></span>
            <?php endif; ?>
            <span class="sana-course-card__badge sana-course-card__badge--level"><?php echo e($c['level_label'] ?? 'مبتدئ'); ?></span>
            <?php if(!empty($c['is_featured'])): ?>
                <span class="sana-course-card__badge sana-course-card__badge--featured"><?php echo e(__('public.featured_badge')); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="sana-course-card__body">
        <h3 class="sana-course-card__title">
            <a href="<?php echo e($url); ?>"><?php echo e($c['title'] ?? ''); ?></a>
        </h3>
        <p class="sana-course-card__desc"><?php echo e($desc); ?></p>

        <?php if($inst): ?>
        <div class="sana-course-card__instructor">
            <?php if($instAvatar): ?>
                <img class="sana-course-card__avatar" src="<?php echo e($instAvatar); ?>" alt="">
            <?php else: ?>
                <span class="sana-course-card__avatar sana-course-card__avatar--initial"><?php echo e(mb_substr($instName, 0, 1)); ?></span>
            <?php endif; ?>
            <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label">المعلّم</span>
                <span class="sana-course-card__instructor-name"><?php echo e($instName); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="sana-course-card__stats">
            <?php if(!empty($c['rating']) && (int) ($c['reviews_count'] ?? 0) > 0): ?>
            <span class="sana-course-card__stat sana-course-card__stat--rating">
                <i class="fas fa-star"></i>
                <strong><?php echo e($c['rating']); ?></strong>
            </span>
            <?php endif; ?>
            <?php if((int) ($c['students_count'] ?? 0) > 0): ?>
            <span class="sana-course-card__stat">
                <i class="fas fa-users"></i>
                <span><?php echo e(number_format($c['students_count'])); ?></span>
            </span>
            <?php endif; ?>
            <?php if(!empty($c['duration_hours'])): ?>
            <span class="sana-course-card__stat">
                <i class="far fa-clock"></i>
                <span><?php echo e($c['duration_hours']); ?> <?php echo e(__('public.hours')); ?></span>
            </span>
            <?php endif; ?>
            <?php if(!empty($c['lectures_count'])): ?>
            <span class="sana-course-card__stat">
                <i class="fas fa-layer-group"></i>
                <span><?php echo e($c['lectures_count']); ?> <?php echo e(__('public.lecture_single')); ?></span>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="sana-course-card__footer">
        <?php if($isEnrolled && $progress !== null): ?>
        <div class="sana-course-card__progress">
            <div class="sana-course-card__progress-head">
                <span>تقدّمك في الدورة</span>
                <strong><?php echo e((int) round($progress)); ?>%</strong>
            </div>
            <div class="sana-course-card__progress-track">
                <span style="width: <?php echo e(min(100, max(0, $progress))); ?>%"></span>
            </div>
        </div>
        <?php endif; ?>
        <div class="sana-course-card__actions">
            <div class="sana-course-card__price-wrap">
                <?php if($priceType === 'free'): ?>
                    <span class="sana-course-card__price sana-course-card__price--free">
                        <i class="fas fa-gift"></i> <?php echo e(__('public.free_price')); ?>

                    </span>
                <?php elseif($priceType === 'whatsapp'): ?>
                    <span class="sana-course-card__price sana-course-card__price--contact">
                        <i class="fab fa-whatsapp"></i> تواصل للسعر
                    </span>
                <?php else: ?>
                    <span class="sana-course-card__price">
                        <?php echo e(number_format($c['sale_price'] ?? $c['price'] ?? 0, 0)); ?> <?php echo e($currency); ?>

                    </span>
                <?php endif; ?>
            </div>
            <a href="<?php echo e($url); ?>" class="sana-course-card__cta">
                <span><?php echo e($isEnrolled ? 'تابع التعلّم' : 'ابدأ الآن'); ?></span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</article>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\partials\course-card.blade.php ENDPATH**/ ?>
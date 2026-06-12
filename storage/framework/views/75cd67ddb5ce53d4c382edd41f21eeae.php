<?php
    $brand = config('app.name', 'Sana');
    $categoryDisplay = $course->courseCategory?->name ?? ($course->academicSubject?->name ?? __('public.course_category_not_set'));
    $thumbUrl = ($course->thumbnail ?? null) ? public_storage_url($course->thumbnail) : \App\Support\PublicCourseCatalog::defaultCardImage();
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    if (!$introEmbedUrl && $introVideoUrl !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
    }
    $introDirectVideo = null;
    if (!$introEmbedUrl && $introVideoUrl !== '' && filter_var($introVideoUrl, FILTER_VALIDATE_URL)) {
        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $introVideoUrl)) {
            $introDirectVideo = $introVideoUrl;
        }
    }
    $hasPreview = $introEmbedUrl || $introDirectVideo;
    $courseDesc = Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')) . ' | ' . $brand;
    $courseUrl = url('/course/' . $course->id);
    $rating = $course->rating ? number_format((float) $course->rating, 1) : '4.9';
    $languageLabel = match (strtolower((string) ($course->language ?? 'ar'))) {
        'en', 'english' => 'الإنجليزية',
        'ar', 'arabic', '' => 'العربية',
        default => $course->language,
    };
    $learnPoints = array_filter(array_map('trim', explode("\n", (string) ($course->what_you_learn ?? ''))));
    if (empty($learnPoints) && !empty($course->skills) && is_array($course->skills)) {
        $learnPoints = $course->skills;
    }
    $objectives = trim((string) ($course->objectives ?? ''));
    $requirements = trim((string) ($course->requirements ?? ''));
    $audience = trim((string) ($course->prerequisites ?? ''));
    $totalReviews = (int) ($course->reviews_count ?? $reviews->count());
    $avgRating = $rating;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($courseTitle); ?></title>
    <meta name="description" content="<?php echo e($courseDesc); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e($courseUrl); ?>">
    <meta property="og:title" content="<?php echo e($courseTitle); ?>">
    <meta property="og:description" content="<?php echo e($courseDesc); ?>">
    <meta property="og:image" content="<?php echo e($thumbUrl); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.course-show-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="<?php echo e(asset('js/course-favorites.js')); ?>"></script>
</head>
<body class="sana-home sana-courses-page sana-course-detail-page">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-course-page">

    <?php $__currentLoopData = ['success' => 'success', 'info' => 'info', 'error' => 'error']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($type)): ?>
            <div class="sana-container" style="padding-top:16px">
                <div class="sana-cd-alert sana-cd-alert--<?php echo e($style); ?>"><?php echo e(session($type)); ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <section class="sana-cd-hero">
        <div class="sana-container sana-cd-hero__inner">
            <nav class="sana-cd-breadcrumb" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>">الرئيسية</a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <a href="<?php echo e(route('public.courses')); ?>">الدورات</a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <span><?php echo e(Str::limit($course->title, 40)); ?></span>
            </nav>

            <div class="sana-cd-hero__grid">
                <div>
                    <div class="sana-cd-hero__badges">
                        <?php if($course->is_featured): ?><span class="sana-cd-pill sana-cd-pill--gold"><i class="fas fa-star"></i> <?php echo e(__('public.featured_badge')); ?></span><?php endif; ?>
                        <span class="sana-cd-pill"><?php echo e($categoryDisplay); ?></span>
                        <span class="sana-cd-pill"><?php echo e($levelLabel); ?></span>
                    </div>

                    <h1 class="sana-cd-hero__title"><?php echo e($course->title); ?></h1>
                    <p class="sana-cd-hero__desc"><?php echo e(Str::limit(strip_tags($course->description ?? ''), 220)); ?></p>

                    <div class="sana-cd-hero__meta">
                        <span class="stars"><i class="fas fa-star"></i> <?php echo e($rating); ?> (<?php echo e(number_format($totalReviews)); ?> تقييم)</span>
                        <span><i class="fas fa-users"></i> <?php echo e(number_format($course->students_count ?? 0)); ?> طالب</span>
                        <span><i class="far fa-clock"></i> <?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></span>
                        <span><i class="fas fa-layer-group"></i> <?php echo e($course->lessons_count ?? 0); ?> <?php echo e(__('public.lecture_single')); ?></span>
                        <span><i class="fas fa-globe"></i> <?php echo e($languageLabel); ?></span>
                        <span><i class="fas fa-rotate"></i> <?php echo e($course->updated_at?->translatedFormat('M Y') ?? '—'); ?></span>
                    </div>

                    <?php if($course->instructor): ?>
                    <div class="sana-cd-hero__instructor">
                        <?php if($course->instructor->profile_image_url): ?>
                            <img src="<?php echo e($course->instructor->profile_image_url); ?>" alt="">
                        <?php else: ?>
                            <span class="av"><?php echo e(mb_substr($course->instructor->name, 0, 1)); ?></span>
                        <?php endif; ?>
                        <div>
                            <small>المعلّم</small>
                            <?php if($instructorProfile): ?>
                                <strong><a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>"><?php echo e($course->instructor->name); ?></a></strong>
                            <?php else: ?>
                                <strong><?php echo e($course->instructor->name); ?></strong>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="sana-cd-hero__actions">
                        <?php echo $__env->make('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php if($hasPreview): ?>
                            <a href="#course-preview" class="sana-course-cta sana-course-cta--outline">
                                <i class="fas fa-circle-play"></i>
                                <span>معاينة الدورة</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($hasPreview): ?>
                <div class="sana-cd-hero__media sana-reveal" id="course-preview">
                    <?php if($introEmbedUrl): ?>
                        <iframe src="<?php echo e($introEmbedUrl); ?>" title="معاينة الدورة" allowfullscreen loading="lazy"></iframe>
                    <?php else: ?>
                        <video src="<?php echo e($introDirectVideo); ?>" controls playsinline preload="metadata"></video>
                    <?php endif; ?>
                </div>
                <?php elseif($thumbUrl): ?>
                <div class="sana-cd-hero__media sana-reveal">
                    <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="sana-cd-body">
        <div class="sana-container">
            <div class="sana-cd-layout">
                <div class="sana-cd-main">

                    <?php if($isEnrolled && $enrollmentProgress !== null): ?>
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-chart-line"></i></span>
                            <h2 class="sana-cd-section__title">تقدّمك في الدورة</h2>
                        </div>
                        <div class="sana-course-card__progress" style="margin:0">
                            <div class="sana-course-card__progress-head">
                                <span>نسبة الإنجاز</span>
                                <strong><?php echo e((int) round($enrollmentProgress)); ?>%</strong>
                            </div>
                            <div class="sana-course-card__progress-track">
                                <span style="width: <?php echo e(min(100, max(0, $enrollmentProgress))); ?>%"></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($learnPoints) || $objectives): ?>
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-bullseye"></i></span>
                            <h2 class="sana-cd-section__title">ماذا ستتعلّم؟</h2>
                        </div>
                        <?php if(!empty($learnPoints)): ?>
                        <div class="sana-cd-learn-grid">
                            <?php $__currentLoopData = $learnPoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="sana-cd-learn-item"><i class="fas fa-check-circle"></i><span><?php echo e($point); ?></span></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                        <?php if($objectives): ?>
                            <p class="sana-cd-section__sub" style="margin-top:16px;white-space:pre-line"><?php echo e($objectives); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-list-ul"></i></span>
                            <div>
                                <h2 class="sana-cd-section__title">منهج الدورة</h2>
                                <p class="sana-cd-section__sub" style="margin-top:4px">
                                    <?php echo e($curriculumStats['modules'] ?? 0); ?> وحدة ·
                                    <?php echo e($curriculumStats['lessons'] ?? 0); ?> درس ·
                                    <?php echo e($curriculumStats['quizzes'] ?? 0); ?> اختبار ·
                                    <?php echo e($curriculumStats['assignments'] ?? 0); ?> واجب
                                </p>
                            </div>
                        </div>
                        <?php echo $__env->make('landing.sana.partials.course-curriculum', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                    <?php if($requirements): ?>
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-list-check"></i></span>
                            <h2 class="sana-cd-section__title"><?php echo e(__('public.requirements')); ?></h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line"><?php echo e($requirements); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($audience): ?>
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-user-group"></i></span>
                            <h2 class="sana-cd-section__title">لمن هذه الدورة؟</h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line"><?php echo e($audience); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-align-right"></i></span>
                            <h2 class="sana-cd-section__title"><?php echo e(__('public.about_course')); ?></h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line"><?php echo e($course->description ?? __('public.course_desc_fallback')); ?></p>
                    </div>

                    <?php if($course->instructor): ?>
                    <div class="sana-cd-section sana-reveal" id="instructor">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-chalkboard-user"></i></span>
                            <h2 class="sana-cd-section__title">عن المعلّم</h2>
                        </div>
                        <div class="sana-cd-instructor">
                            <?php
                                $instPhoto = $instructorProfile?->photo_url ?? $course->instructor->profile_image_url;
                            ?>
                            <?php if($instPhoto): ?>
                                <img class="sana-cd-instructor__photo" src="<?php echo e($instPhoto); ?>" alt="<?php echo e($course->instructor->name); ?>">
                            <?php else: ?>
                                <span class="sana-cd-instructor__photo sana-cd-instructor__photo--initial"><?php echo e(mb_substr($course->instructor->name, 0, 1)); ?></span>
                            <?php endif; ?>
                            <div>
                                <h3 style="font-size:1.15rem;font-weight:900;margin:0 0 6px"><?php echo e($course->instructor->name); ?></h3>
                                <?php if($instructorProfile?->headline): ?>
                                    <p style="color:var(--p);font-weight:800;font-size:0.88rem;margin:0 0 12px"><?php echo e($instructorProfile->headline); ?></p>
                                <?php endif; ?>
                                <div class="sana-cd-instructor__stats">
                                    <div><strong><?php echo e($instructorStats['courses']); ?></strong><span>دورة</span></div>
                                    <div><strong><?php echo e(number_format($instructorStats['students'])); ?></strong><span>طالب</span></div>
                                    <div><strong><?php echo e($instructorStats['rating']); ?></strong><span>تقييم</span></div>
                                </div>
                                <?php if($instructorProfile?->bio): ?>
                                    <p class="sana-cd-section__sub" style="white-space:pre-line"><?php echo e($instructorProfile->bio); ?></p>
                                <?php endif; ?>
                                <?php if($instructorProfile && !empty($instructorProfile->experience_list)): ?>
                                    <ul style="margin:12px 0 0;padding:0;list-style:none">
                                        <?php $__currentLoopData = $instructorProfile->experience_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="sana-cd-learn-item" style="margin-bottom:8px"><i class="fas fa-briefcase"></i><span><?php echo e($exp); ?></span></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($reviews->isNotEmpty() || $totalReviews > 0): ?>
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-star"></i></span>
                            <h2 class="sana-cd-section__title">تقييمات الطلاب</h2>
                        </div>
                        <div class="sana-cd-reviews-layout">
                            <div>
                                <div class="sana-cd-rating-big">
                                    <strong><?php echo e($avgRating); ?></strong>
                                    <div class="stars">
                                        <?php for($s = 1; $s <= 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?>
                                    </div>
                                    <span style="font-size:0.78rem;font-weight:700;color:var(--muted)"><?php echo e(number_format($totalReviews)); ?> تقييم</span>
                                </div>
                                <div style="margin-top:16px">
                                    <?php for($star = 5; $star >= 1; $star--): ?>
                                        <?php
                                            $count = (int) ($ratingBreakdown[$star] ?? 0);
                                            $pct = $totalReviews > 0 ? ($count / max($totalReviews, 1)) * 100 : 0;
                                        ?>
                                        <div class="sana-cd-rating-bar">
                                            <span><?php echo e($star); ?></span>
                                            <div class="sana-cd-rating-bar__track"><span style="width:<?php echo e($pct); ?>%"></span></div>
                                            <span><?php echo e($count); ?></span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                                <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <article class="sana-cd-review-card">
                                        <div class="sana-cd-review-card__head">
                                            <?php if($review->user?->profile_image_url): ?>
                                                <img src="<?php echo e($review->user->profile_image_url); ?>" alt="">
                                            <?php else: ?>
                                                <span class="av"><?php echo e(mb_substr($review->user?->name ?? 'ط', 0, 1)); ?></span>
                                            <?php endif; ?>
                                            <div>
                                                <strong style="font-size:0.88rem"><?php echo e($review->user?->name ?? 'طالب'); ?></strong>
                                                <div class="sana-cd-review-card__stars">
                                                    <?php for($s = 1; $s <= 5; $s++): ?>
                                                        <i class="fa<?php echo e($s <= (int)$review->rating ? 's' : 'r'); ?> fa-star"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p><?php echo e($review->review ?? $review->comment ?? ''); ?></p>
                                    </article>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <p class="sana-cd-section__sub">لا توجد مراجعات نصية بعد — التقييمات الإجمالية متاحة أعلاه.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="sana-cd-sidebar-wrap">
                    <?php echo $__env->make('landing.sana.partials.course-enroll-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </section>
</main>


<div class="sana-cd-mobile-bar">
    <div class="sana-cd-mobile-bar__inner">
        <div class="sana-cd-mobile-bar__price">
            <?php if($course->usesContactSupportPricing()): ?>
                <i class="fab fa-whatsapp"></i> تواصل
            <?php elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                <?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?> <?php echo e(__('public.currency')); ?>

            <?php else: ?>
                <?php echo e(__('public.free_price')); ?>

            <?php endif; ?>
        </div>
        <?php echo $__env->make('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.SanaCourseFavorites) {
        window.SanaCourseFavorites.init({
            authenticated: <?php echo json_encode(auth()->check(), 15, 512) ?>,
            loginUrl: <?php echo json_encode(route('login'), 15, 512) ?>,
            toggleUrlTemplate: <?php echo json_encode(url('/saved-courses/__ID__/toggle'), 15, 512) ?>,
            syncUrl: <?php echo json_encode(route('public.saved-courses.sync'), 15, 512) ?>,
            savedIds: <?php echo \Illuminate\Support\Js::from($savedCourseIds ?? [])->toHtml() ?>,
        });
    }
});
</script>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\course-show.blade.php ENDPATH**/ ?>
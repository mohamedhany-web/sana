<?php $__env->startSection('title', 'SANUA — ' . __('student.dashboard_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $firstName = explode(' ', trim($user->name))[0];
    $progress = min((int) ($stats['total_progress'] ?? 0), 100);
    $level = max(1, (int) floor($progress / 15) + 1);
    $xp = (int) ($progress * 48 + ($achievementsCount ?? 0) * 150 + ($stats['completed_exams'] ?? 0) * 80);
    $coins = ($stats['completed_courses'] ?? 0) * 50 + ($achievementsCount ?? 0) * 30;
    $streak = min(14, max(1, (int) ($recentActivity ?? collect())->filter(fn ($a) => isset($a['at']) && $a['at']->gte(now()->subDays(7)))->count() + 1));
    $rank = max(1, 100 - $level * 3);

    $subjectTemplates = [
        ['key' => 'math', 'title' => 'الرياضيات', 'emoji' => '🔢', 'theme' => 'math', 'keywords' => ['رياض', 'math', 'حساب']],
        ['key' => 'english', 'title' => 'الإنجليزية', 'emoji' => '🌍', 'theme' => 'english', 'keywords' => ['انجل', 'english', 'إنجل']],
        ['key' => 'science', 'title' => 'العلوم', 'emoji' => '🔬', 'theme' => 'science', 'keywords' => ['علوم', 'science', 'فيز']],
    ];
    $subjectCards = collect($subjectTemplates)->map(function ($tpl) use ($activeCourses) {
        $course = ($activeCourses ?? collect())->first(function ($c) use ($tpl) {
            $title = mb_strtolower((string) ($c->title ?? ''));
            $subject = mb_strtolower((string) ($c->academicSubject?->name ?? ''));
            foreach ($tpl['keywords'] as $kw) {
                if (str_contains($title, $kw) || str_contains($subject, $kw)) {
                    return true;
                }
            }
            return false;
        });
        $prog = $course ? (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0) : 0;
        $lessons = $course ? (int) ($course->lessons_count ?? 0) : 12;
        $fallbackProgress = ['math' => 28, 'english' => 35, 'science' => 22][$tpl['key']] ?? 25;

        return array_merge($tpl, [
            'progress' => $prog > 0 ? $prog : $fallbackProgress,
            'lessons' => $lessons,
            'url' => $course ? route('my-courses.show', $course->id) : route('my-courses.index'),
        ]);
    });

    $playTiles = array_filter([
        ['emoji' => '📝', 'label' => 'واجبات', 'url' => Route::has('student.assignments.index') ? route('student.assignments.index') : null],
        ['emoji' => '🎮', 'label' => 'أنشطة', 'url' => Route::has('student.ai-usages.index') ? route('student.ai-usages.index') : null],
        ['emoji' => '🎬', 'label' => 'فيديو', 'url' => route('my-courses.index')],
        ['emoji' => '📖', 'label' => 'قصص', 'url' => route('public.courses')],
        ['emoji' => '🏆', 'label' => 'تحديات', 'url' => Route::has('student.exams.index') ? route('student.exams.index') : null],
        ['emoji' => '✅', 'label' => 'امتحانات', 'url' => Route::has('student.exams.index') ? route('student.exams.index') : null],
        ['emoji' => '🎓', 'label' => 'شهادات', 'url' => Route::has('student.certificates.index') ? route('student.certificates.index') : null],
        ['emoji' => '🎁', 'label' => 'مكافآت', 'url' => Route::has('student.achievements.index') ? route('student.achievements.index') : null],
    ], fn ($t) => ! empty($t['url']));

    $ctaUrl = $activeCourses->isNotEmpty()
        ? route('my-courses.learn', $activeCourses->first()->id)
        : route('public.courses');
    $ctaLabel = $activeCourses->isNotEmpty() ? 'ابدأ التعلّم الآن' : 'استكشف الكورسات';

    $heroBoy = public_static_exists('img/sanua/hero-boy.png')
        ? public_static_url('img/sanua/hero-boy.png')
        : (public_static_exists('img/sanua/hero-character.png')
            ? public_static_url('img/sanua/hero-character.png')
            : null);

    $challengeUrl = Route::has('student.exams.index') ? route('student.exams.index') : route('my-courses.index');
?>

<div class="sanua-dash">

    
    <div class="sanua-hero-wrap">
        <section class="sanua-hero" aria-label="ترحيب">
            <div class="sanua-hero__deco" aria-hidden="true">
                <span class="sanua-hero__blob sanua-hero__blob--1"></span>
                <span class="sanua-hero__blob sanua-hero__blob--2"></span>
                <span class="sanua-hero__blob sanua-hero__blob--3"></span>
                <span class="sanua-hero__ring sanua-hero__ring--1"></span>
                <span class="sanua-hero__ring sanua-hero__ring--2"></span>
                <span class="sanua-hero__dot sanua-hero__dot--1"></span>
                <span class="sanua-hero__dot sanua-hero__dot--2"></span>
                <span class="sanua-hero__dot sanua-hero__dot--3"></span>
                <span class="sanua-hero__cross sanua-hero__cross--1"></span>
                <span class="sanua-hero__cross sanua-hero__cross--2"></span>
                <span class="sanua-hero__spark sanua-hero__spark--g1">✦</span>
                <span class="sanua-hero__spark sanua-hero__spark--g2">✦</span>
                <span class="sanua-hero__spark sanua-hero__spark--g3">✦</span>
                <span class="sanua-hero__star-icon sanua-hero__star-icon--g1">⭐</span>
            </div>

            <?php if($heroBoy): ?>
            <div class="sanua-hero__visual">
                <div class="sanua-hero__visual-deco" aria-hidden="true">
                    <span class="sanua-hero__cloud sanua-hero__cloud--a"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--b"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--c"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--d"></span>
                    <span class="sanua-hero__cloud sanua-hero__cloud--e"></span>
                    <span class="sanua-hero__spark sanua-hero__spark--a">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--b">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--c">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--d">✦</span>
                    <span class="sanua-hero__spark sanua-hero__spark--e">✦</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--a">⭐</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--b">⭐</span>
                    <span class="sanua-hero__star-icon sanua-hero__star-icon--c">⭐</span>
                    <span class="sanua-hero__bulb">💡</span>
                    <span class="sanua-hero__mini-circle sanua-hero__mini-circle--1"></span>
                    <span class="sanua-hero__mini-circle sanua-hero__mini-circle--2"></span>
                </div>
                <span class="sanua-hero__char-glow"></span>
                <img
                    src="<?php echo e($heroBoy); ?>?v=5"
                    alt=""
                    width="1536"
                    height="1024"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
            </div>
            <?php endif; ?>

            <div class="sanua-hero__content">
                <p class="sanua-hero__hello">مرحباً <?php echo e($firstName); ?> 👋</p>
                <h1 class="sanua-hero__title">
                    <span class="line-white">تعلّم ممتع </span><span class="line-gold">يبدأ مع سنا</span>
                </h1>
                <p class="sanua-hero__sub">منصة تعليمية تفاعلية تلهم الأطفال وتبني مهاراتهم بثقة ومتعة.</p>
                <a href="<?php echo e($ctaUrl); ?>" class="sanua-hero__cta">
                    <?php echo e($ctaLabel); ?>

                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </section>
    </div>

    
    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2 4 5v6c0 5 3.4 9.7 8 11 4.6-1.3 8-6 8-11V5l-8-3zm0 2.2 6 2.25V11c0 3.9-2.6 7.6-6 8.8-3.4-1.2-6-4.9-6-8.8V6.45l6-2.25z"/></svg>
            </span>
            <div class="sanua-stat-pill__body"><strong>Lv <?php echo e($level); ?></strong><span>المستوى</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/></svg>
            </span>
            <div class="sanua-stat-pill__body"><strong><?php echo e(number_format($xp)); ?></strong><span>نقاط XP</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 23c-1.1-1.6-2.8-3.3-4.2-5.2C5.8 14.5 4 11.8 4 9a8 8 0 1 1 16 0c0 2.8-1.8 5.5-3.8 8.8C14.8 19.7 13.1 21.4 12 23zm0-18a6 6 0 0 0-6 6c0 2.1 1.5 4.4 3.2 6.6 1 .1.2 2.1 2.8 4.4 1.6-2.3 2.8-4.3 2.8-4.4 1.7-2.2 3.2-4.5 3.2-6.6a6 6 0 0 0-6-6z"/></svg>
            </span>
            <div class="sanua-stat-pill__body"><strong><?php echo e($streak); ?> يوم</strong><span>سلسلة يومية</span></div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l2.4 7.4H22l-6 4.6 2.3 7-6.3-4.6L5.7 21l2.3-7-6-4.6h7.6L12 2z"/></svg>
            </span>
            <div class="sanua-stat-pill__body"><strong><?php echo e($achievementsCount); ?></strong><span>إنجاز</span></div>
        </div>
    </div>

    
    <div class="sanua-challenge">
        <span class="sanua-challenge__icon">🏆</span>
        <div class="sanua-challenge__text">
            <strong>تحدي الأسبوع: أكمل 3 دروس!</strong>
            <span>شارك في التحدي واحصل على <?php echo e($streak > 1 ? $streak * 10 : 50); ?> XP إضافي</span>
        </div>
        <a href="<?php echo e($challengeUrl); ?>" class="sanua-challenge__btn">ابدأ التحدي</a>
    </div>

    
    <section class="sanua-section">
        <h2 class="sanua-section-title">📚 موادك الدراسية</h2>
        <div class="sanua-subjects-grid">
            <?php $__currentLoopData = $subjectCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($sub['url']); ?>" class="sanua-subject-card sanua-subject-card--<?php echo e($sub['theme']); ?>">
                    <div class="sanua-subject-card__illus"><?php echo e($sub['emoji']); ?></div>
                    <div class="sanua-subject-card__info">
                        <h3><?php echo e($sub['title']); ?></h3>
                        <p class="meta"><?php echo e($sub['lessons']); ?> درس</p>
                    </div>
                    <div class="sanua-subject-card__progress">
                        <div class="sanua-subject-bar-track">
                            <div class="sanua-subject-bar-fill" style="width:<?php echo e((int) $sub['progress']); ?>%"></div>
                        </div>
                        <p class="sanua-subject-pct"><?php echo e((int) $sub['progress']); ?>% مكتمل</p>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    
    <section class="sanua-section">
        <h2 class="sanua-section-title">🎮 العب وتعلّم</h2>
        <div class="sanua-play-grid">
            <?php $__currentLoopData = $playTiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($tile['url']); ?>" class="sanua-play-tile">
                    <span class="sanua-play-emoji"><?php echo e($tile['emoji']); ?></span>
                    <span class="sanua-play-lbl"><?php echo e($tile['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <?php if(isset($activeSubscription) && $activeSubscription): ?>
    <div class="sanua-sub-banner">
        <div class="flex items-center gap-2">
            <span class="text-xl">💎</span>
            <div>
                <p class="text-sm font-black text-violet-900 m-0"><?php echo e($activeSubscription->plan_name ?: 'باقة SANUA'); ?></p>
                <p class="text-xs font-bold text-violet-500 m-0">ينتهي <?php echo e($activeSubscription->end_date?->format('Y-m-d')); ?></p>
            </div>
        </div>
        <a href="<?php echo e(route('student.my-subscription')); ?>" class="sanua-challenge__btn">عرض الباقة</a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\dashboard\student.blade.php ENDPATH**/ ?>
<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $a = fn (string $key) => str_replace(':brand', $brand, __('sana_about.'.$key));
    $hero = __('sana_about.hero');
    $story = __('sana_about.story');
    $timeline = __('sana_about.timeline');
    $mission = __('sana_about.mission');
    $vision = __('sana_about.vision');
    $why = __('sana_about.why');
    $metrics = __('sana_about.metrics');
    $instructorsCopy = __('sana_about.instructors');
    $values = __('sana_about.values');
    $stories = __('sana_about.stories');
    $final = __('sana_about.final');

    $fmtStat = function (int $n): string {
        if ($n >= 100000) {
            return round($n / 1000) . 'K+';
        }
        if ($n >= 1000) {
            return number_format($n / 1000, 1, '.', '') . 'K+';
        }

        return number_format($n);
    };

    $displayStudents = max($stats['students'] ?? 0, 1200);
    $displayCompleted = max($stats['completed'] ?? 0, 500);
    $displayCerts = max($stats['certificates'] ?? 0, 100);
    $displayInstructors = max($stats['instructors'] ?? 0, 20);
    $displaySatisfaction = $stats['satisfaction'] ?? 98;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($a('meta_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($a('meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/about')); ?>">
    <meta property="og:title" content="<?php echo e($a('meta_title')); ?> — <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($a('meta_description')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.about-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-about-page">


<section class="sana-ab-hero">
    <div class="sana-container">
        <div class="sana-ab-hero__grid sana-reveal">
            <div class="sana-ab-hero__content">
                <span class="sana-ab-hero__eyebrow"><i class="fas fa-heart"></i> <?php echo e(str_replace(':brand', $brand, $hero['eyebrow'])); ?></span>
                <h1 class="sana-ab-hero__title">
                    <?php echo e($hero['title']); ?>

                    <span class="hl"><?php echo e($hero['highlight']); ?></span>
                </h1>
                <p class="sana-ab-hero__mission"><?php echo e(str_replace(':brand', $brand, $hero['mission'])); ?></p>
                <p class="sana-ab-hero__sub"><?php echo e(str_replace(':brand', $brand, $hero['sub'])); ?></p>
                <div class="sana-ab-hero__actions">
                    <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow sana-btn--lg">
                        <i class="fas fa-rocket"></i> <?php echo e($hero['cta_primary']); ?>

                    </a>
                    <a href="#story" class="sana-btn sana-btn--white-outline sana-btn--lg">
                        <i class="fas fa-book-open"></i> <?php echo e($hero['cta_secondary']); ?>

                    </a>
                </div>
            </div>
            <div class="sana-ab-hero__visual">
                <?php echo $__env->make('landing.sana.partials.about-hero-scene', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="sana-ab-hero__badge">
                    <strong data-sana-counter="<?php echo e($displaySatisfaction); ?>" data-sana-suffix="%"><?php echo e($displaySatisfaction); ?>%</strong>
                    <span>رضا أولياء الأمور</span>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="sana-section" id="story">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:40px">
            <span class="sana-head__eyebrow"><?php echo e(str_replace(':brand', $brand, $story['eyebrow'])); ?></span>
            <h2 class="sana-head__title">
                <?php echo e(str_replace(':brand', $brand, $story['title'])); ?>

                <span class="hl"><?php echo e(str_replace(':brand', $brand, $story['highlight'])); ?></span>
            </h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-ab-story">
            <div class="sana-ab-story__intro sana-reveal">
                <p><?php echo e($story['intro']); ?></p>
                <div class="sana-ab-story__blocks">
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-circle-exclamation"></i> <?php echo e($story['challenge_title']); ?></h4>
                        <p><?php echo e($story['challenge']); ?></p>
                    </div>
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-lightbulb"></i> <?php echo e($story['vision_title']); ?></h4>
                        <p><?php echo e($story['vision']); ?></p>
                    </div>
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-chart-line"></i> <?php echo e($story['impact_title']); ?></h4>
                        <p><?php echo e($story['impact']); ?></p>
                    </div>
                </div>
            </div>
            <div class="sana-ab-timeline sana-reveal">
                <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sana-ab-timeline__item">
                    <span class="sana-ab-timeline__dot"></span>
                    <span class="sana-ab-timeline__year"><?php echo e($item['year']); ?></span>
                    <strong><?php echo e($item['title']); ?></strong>
                    <p><?php echo e($item['desc']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($mission['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($mission['title']); ?> <span class="hl"><?php echo e($mission['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($mission['sub']); ?></p>
        </div>
        <div class="sana-ab-pillars">
            <?php $__currentLoopData = $mission['pillars']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ab-pillar sana-reveal">
                <div class="sana-ab-pillar__icon"><i class="fas <?php echo e($pillar['icon']); ?>"></i></div>
                <strong><?php echo e($pillar['title']); ?></strong>
                <span><?php echo e($pillar['desc']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($vision['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($vision['title']); ?> <span class="hl"><?php echo e($vision['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($vision['sub']); ?></p>
        </div>
        <div class="sana-ab-vision">
            <?php $__currentLoopData = $vision['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ab-vision__card sana-reveal">
                <span class="sana-ab-vision__icon"><i class="fas <?php echo e($point['icon']); ?>"></i></span>
                <div>
                    <strong><?php echo e($point['title']); ?></strong>
                    <p><?php echo e($point['desc']); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($why['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($why['title']); ?> <span class="hl"><?php echo e($why['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($why['sub']); ?></p>
        </div>
        <div class="sana-ab-why">
            <?php $__currentLoopData = $why['cards']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ab-why__card sana-reveal">
                <div class="sana-ab-why__icon"><i class="fas <?php echo e($card['icon']); ?>"></i></div>
                <strong><?php echo e($card['title']); ?></strong>
                <p><?php echo e($card['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($metrics['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($metrics['title']); ?> <span class="hl"><?php echo e($metrics['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($metrics['sub']); ?></p>
        </div>
        <div class="sana-ab-metrics">
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="<?php echo e(min($displayStudents, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayStudents)); ?></strong>
                <span><?php echo e($metrics['students']); ?></span>
            </div>
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-book-open"></i>
                <strong data-sana-counter="<?php echo e(min($displayCompleted, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayCompleted)); ?></strong>
                <span><?php echo e($metrics['completed']); ?></span>
            </div>
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="<?php echo e(min($displayCerts, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayCerts)); ?></strong>
                <span><?php echo e($metrics['certificates']); ?></span>
            </div>
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-face-smile"></i>
                <strong data-sana-counter="<?php echo e($displaySatisfaction); ?>" data-sana-suffix="%"><?php echo e($displaySatisfaction); ?>%</strong>
                <span><?php echo e($metrics['satisfaction']); ?></span>
            </div>
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-chalkboard-user"></i>
                <strong data-sana-counter="<?php echo e(min($displayInstructors, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayInstructors)); ?></strong>
                <span><?php echo e($metrics['instructors']); ?></span>
            </div>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft" id="instructors">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($instructorsCopy['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($instructorsCopy['title']); ?> <span class="hl"><?php echo e($instructorsCopy['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($instructorsCopy['sub']); ?></p>
        </div>
        <div class="sana-ab-instructors">
            <?php $__empty_1 = true; $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $user = $profile->user;
                $name = $user->name ?? 'معلّم';
                $initial = mb_substr($name, 0, 1);
                $photo = $profile->photo_url;
                $headline = $profile->headline ?: ($profile->bio ? Str::limit(strip_tags($profile->bio), 60) : 'معلّم معتمد');
                $exp = (int) ($profile->tutor_years_experience ?? $profile->experience ?? 0);
                $coursesCount = (int) ($profile->courses_count ?? 0);
            ?>
            <a href="<?php echo e(route('public.instructors.show', $user)); ?>" class="sana-ab-instructor sana-reveal">
                <div class="sana-ab-instructor__photo">
                    <?php if($photo): ?>
                        <img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>" loading="lazy">
                    <?php else: ?>
                        <span class="av"><?php echo e($initial); ?></span>
                    <?php endif; ?>
                </div>
                <div class="sana-ab-instructor__body">
                    <strong><?php echo e($name); ?></strong>
                    <p class="sana-ab-instructor__headline"><?php echo e($headline); ?></p>
                    <div class="sana-ab-instructor__meta">
                        <?php if($coursesCount > 0): ?>
                            <span><i class="fas fa-book"></i> <?php echo e($coursesCount); ?> <?php echo e($instructorsCopy['courses']); ?></span>
                        <?php endif; ?>
                        <?php if($exp > 0): ?>
                            <span><i class="fas fa-clock"></i> <?php echo e($exp); ?> <?php echo e($instructorsCopy['experience']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php $__currentLoopData = [
                ['n' => 'أ. سارة', 'h' => 'متخصصة في الرياضيات والعلوم', 'c' => 12, 'e' => 8],
                ['n' => 'أ. أحمد', 'h' => 'خبير اللغة العربية والأدب', 'c' => 9, 'e' => 10],
                ['n' => 'أ. نور', 'h' => 'معلّمة اللغة الإنجليزية', 'c' => 7, 'e' => 6],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ab-instructor sana-reveal">
                <div class="sana-ab-instructor__photo"><span class="av"><?php echo e(mb_substr($d['n'], 0, 1)); ?></span></div>
                <div class="sana-ab-instructor__body">
                    <strong><?php echo e($d['n']); ?></strong>
                    <p class="sana-ab-instructor__headline"><?php echo e($d['h']); ?></p>
                    <div class="sana-ab-instructor__meta">
                        <span><i class="fas fa-book"></i> <?php echo e($d['c']); ?> <?php echo e($instructorsCopy['courses']); ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo e($d['e']); ?> <?php echo e($instructorsCopy['experience']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
        <div class="sana-ab-view-all sana-reveal">
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="sana-btn sana-btn--purple">
                <?php echo e($instructorsCopy['view_all']); ?> <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($values['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($values['title']); ?> <span class="hl"><?php echo e($values['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($values['sub']); ?></p>
        </div>
        <div class="sana-ab-values">
            <?php $__currentLoopData = $values['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ab-value sana-reveal">
                <div class="sana-ab-value__icon"><i class="fas <?php echo e($value['icon']); ?>"></i></div>
                <strong><?php echo e($value['title']); ?></strong>
                <span><?php echo e($value['desc']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft" id="success-stories">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow"><?php echo e($stories['eyebrow']); ?></span>
            <h2 class="sana-head__title"><?php echo e($stories['title']); ?> <span class="hl"><?php echo e($stories['highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($stories['sub']); ?></p>
        </div>
        <div class="sana-test-m">
            <?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e(Str::limit(strip_tags($t->body ?? ''), 200)); ?>»</p>
                <div class="stars"><?php for($s = 0; $s < 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author">
                    <?php if($t->isImageType() && $t->publicImageUrl()): ?>
                        <img src="<?php echo e($t->publicImageUrl()); ?>" alt="">
                    <?php else: ?>
                        <span class="av"><?php echo e($t->author_name ? mb_substr($t->author_name, 0, 1) : '؟'); ?></span>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($t->author_name ?? 'عميل'); ?></strong>
                        <?php if($t->role_label): ?><small><?php echo e($t->role_label); ?></small><?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php $__currentLoopData = [
                ['n' => 'أم ليان', 'r' => 'وليّة أمر · تحسّن في الرياضيات', 't' => 'ابنتي كانت تخاف من الرياضيات. بعد ثلاثة أشهر على المنصة، أصبحت تتفاعل بثقة وتحب التعلّم!'],
                ['n' => 'محمد', 'r' => 'طالب · شهادة إتمام', 't' => 'حصلت على شهادتي بعد إنهاء الكورس — التجربة كانت ممتعة والمعلّمون محترفون جداً.'],
                ['n' => 'أ. خالد', 'r' => 'وليّ أمر · متابعة واضحة', 't' => 'لوحة المتابعة تعطيني صورة حقيقية عن تقدّم أبنائي. أفضل قرار اتخذته هذا العام.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e($d['t']); ?>»</p>
                <div class="stars"><?php for($s = 0; $s < 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author">
                    <span class="av"><?php echo e(mb_substr($d['n'], 0, 1)); ?></span>
                    <div><strong><?php echo e($d['n']); ?></strong><small><?php echo e($d['r']); ?></small></div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="sana-ab-final">
    <div class="sana-container sana-reveal">
        <div class="sana-ab-final__box">
            <h2><?php echo e($final['title']); ?></h2>
            <p><?php echo e(str_replace(':brand', $brand, $final['sub'])); ?></p>
            <div class="sana-ab-final__actions">
                <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow sana-btn--lg">
                    <?php echo e($final['cta_primary']); ?> <i class="fas fa-arrow-left"></i>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--ghost-light sana-btn--lg">
                    <?php echo e($final['cta_secondary']); ?> <i class="fas fa-compass"></i>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\about.blade.php ENDPATH**/ ?>
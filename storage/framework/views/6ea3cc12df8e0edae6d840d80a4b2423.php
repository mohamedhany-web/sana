<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $defaultPhoto = null;
    $searchMap = $profiles->mapWithKeys(function ($p) {
        $blob = mb_strtolower(implode(' ', array_filter([
            $p->user->name ?? '',
            $p->headline ?? '',
            $p->bio ?? '',
            implode(' ', $p->skills_list ?? []),
        ])));
        return [(int) $p->user->id => $blob];
    });
    $totalCourses = (int) $profiles->sum('courses_count');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.instructors_page_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/instructors')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.subpages-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page"
      x-data="{
        searchQuery: '',
        searchMap: <?php echo \Illuminate\Support\Js::from($searchMap)->toHtml() ?>,
        profileVisible(userId) {
          const q = this.searchQuery.toLowerCase().trim();
          if (!q) return true;
          return (this.searchMap[userId] || '').includes(q);
        },
        get visibleCount() {
          return Object.keys(this.searchMap).filter((id) => this.profileVisible(Number(id))).length;
        }
      }">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-sub-page">

<section class="sana-sub-hero">
    <div class="sana-container">
        <div class="sana-sub-hero__grid sana-reveal">
            <div class="sana-sub-hero__content">
                <nav class="sana-sub-hero__breadcrumb" aria-label="مسار التنقل">
                    <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span><?php echo e(__('public.instructors_page_title')); ?></span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> <?php echo e($tr('instructors.badge')); ?></span>
                <h1 class="sana-sub-hero__title">
                    <?php echo e($tr('instructors.title')); ?> <span class="hl"><?php echo e($tr('instructors.title_highlight')); ?></span>
                </h1>
                <p class="sana-sub-hero__sub"><?php echo e(__('public.instructors_subtitle')); ?></p>
                <div class="sana-sub-hero__actions">
                    <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--yellow"><i class="fas fa-book-open"></i> <?php echo e($tr('hero.cta_courses')); ?></a>
                    <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--white-outline"><i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.instructors_cta_register')); ?></a>
                </div>
            </div>
            <div class="sana-sub-hero__stats">
                <div class="sana-sub-hero__stat">
                    <strong><?php echo e($profiles->count()); ?></strong>
                    <span><?php echo e(__('public.stat_instructors')); ?></span>
                </div>
                <div class="sana-sub-hero__stat">
                    <strong><?php echo e($totalCourses); ?></strong>
                    <span><?php echo e($tr('instructors.courses')); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section">
    <div class="sana-container">
        <?php if($profiles->isNotEmpty()): ?>
        <div class="sana-sub-toolbar sana-reveal">
            <p><?php echo e(__('public.instructors_ranking_hint')); ?></p>
            <div class="sana-sub-search">
                <input type="search" x-model="searchQuery" placeholder="<?php echo e(__('public.instructors_search_placeholder')); ?>" aria-label="<?php echo e(__('public.instructors_search_placeholder')); ?>">
                <i class="fas fa-search"></i>
            </div>
        </div>

        <div class="sana-inst-grid">
            <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $userId = (int) $p->user->id;
                $photo = $p->photo_url;
                $skills = array_slice($p->skills_list ?? [], 0, 3);
                $skillsMore = max(0, count($p->skills_list ?? []) - 3);
            ?>
            <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>"
               class="sana-inst-card sana-reveal"
               x-show="profileVisible(<?php echo e($userId); ?>)"
               x-cloak>
                <div class="sana-inst-card__photo">
                    <?php if($photo): ?>
                        <img src="<?php echo e($photo); ?>" alt="<?php echo e($p->user->name); ?>" loading="lazy">
                    <?php else: ?>
                        <span class="av"><?php echo e(mb_substr($p->user->name ?? 'م', 0, 1)); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($p->marketing_featured_today)): ?>
                        <span class="sana-inst-card__badge"><i class="fas fa-bolt"></i> <?php echo e(__('public.instructor_featured_badge')); ?></span>
                    <?php endif; ?>
                    <?php if(($p->courses_count ?? 0) > 0): ?>
                        <span class="sana-inst-card__pill"><?php echo e((int) $p->courses_count); ?> <?php echo e($tr('instructors.courses')); ?></span>
                    <?php endif; ?>
                </div>
                <div class="sana-inst-card__body">
                    <strong><?php echo e($p->user->name); ?></strong>
                    <p class="sana-inst-card__headline"><?php echo e($p->headline ?? __('public.instructor_fallback')); ?></p>
                    <?php if(count($skills) > 0): ?>
                    <div class="sana-inst-card__skills">
                        <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($skill); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($skillsMore > 0): ?><span>+<?php echo e($skillsMore); ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if($p->bio): ?>
                    <p class="sana-inst-card__bio"><?php echo e(Str::limit(strip_tags($p->bio), 80)); ?></p>
                    <?php endif; ?>
                    <span class="sana-inst-card__link"><?php echo e(__('public.view_instructor_profile')); ?> <i class="fas fa-arrow-left"></i></span>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="sana-sub-empty sana-reveal" x-show="searchQuery.trim() && visibleCount === 0" x-cloak style="margin-top:24px">
            <div class="sana-sub-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
            <h3 style="font-weight:900;margin:0 0 8px"><?php echo e(__('public.no_results')); ?></h3>
            <p style="color:var(--muted);font-size:0.88rem;margin:0"><?php echo e(__('public.no_results_hint')); ?></p>
        </div>
        <?php else: ?>
        <div class="sana-sub-empty sana-reveal">
            <div class="sana-sub-empty__icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h3 style="font-weight:900;margin:0 0 8px"><?php echo e(__('public.no_instructors')); ?></h3>
            <p style="color:var(--muted);font-size:0.88rem;margin:0 0 20px"><?php echo e(__('public.instructors_empty_hint')); ?></p>
            <a href="<?php echo e(route('home')); ?>" class="sana-btn sana-btn--purple"><i class="fas fa-home"></i> <?php echo e($tr('nav.home')); ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2><?php echo e(__('public.instructors_cta_title')); ?></h2>
            <p><?php echo e(__('public.instructors_cta_subtitle')); ?></p>
            <div class="sana-sub-final__actions">
                <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.instructors_cta_register')); ?> <i class="fas fa-arrow-left"></i></a>
                <a href="<?php echo e(route('public.teacher-policy')); ?>" class="sana-btn sana-btn--ghost-light"><i class="fas fa-file-contract"></i> سياسة المعلمين</a>
                <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--ghost-light"><?php echo e($tr('hero.cta_courses')); ?> <i class="fas fa-arrow-left"></i></a>
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\instructors\index.blade.php ENDPATH**/ ?>
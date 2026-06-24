<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hasProfiles = $profiles->isNotEmpty();
    $searchMap = $profiles->mapWithKeys(function ($p) {
        $blob = mb_strtolower(implode(' ', array_filter([
            $p->user->name ?? '',
            $p->headline ?? '',
            $p->bio ?? '',
            implode(' ', $p->public_subject_labels ?? []),
            implode(' ', $p->skills_list ?? []),
        ])));

        return [(int) $p->user->id => $blob];
    });
    $bookableCount = (int) $profiles->filter(fn ($p) => ! empty($p->is_bookable))->count();
    $withCourses = (int) $profiles->filter(fn ($p) => (int) ($p->courses_count ?? 0) > 0)->count();
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
    <?php echo $__env->make('landing.sana.instructors-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page sana-instructors-page"
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

<main class="sana-cat-page">

<section class="sana-cat-hero" id="inst-hero">
    <div class="sana-cat-hero__dots"></div>
    <div class="sana-container sana-cat-hero__inner">
        <nav class="sana-cat-hero__breadcrumb" aria-label="مسار التنقل">
            <a href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a>
            <i class="fas fa-chevron-left" style="font-size:0.6rem;opacity:0.5"></i>
            <span><?php echo e(__('public.instructors_page_title')); ?></span>
        </nav>

        <span class="sana-inst-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> <?php echo e($tr('instructors.badge')); ?></span>

        <h1 class="sana-cat-hero__title">
            <?php echo e($tr('instructors.title')); ?> <span class="hl"><?php echo e($tr('instructors.title_highlight')); ?></span>
        </h1>
        <p class="sana-cat-hero__desc"><?php echo e(__('public.instructors_subtitle')); ?></p>

        <?php if($hasProfiles): ?>
            <?php if($bookableCount > 0 || $withCourses > 0): ?>
            <div class="sana-cat-hero__stats sana-reveal">
                <?php if($bookableCount > 0): ?>
                <span class="sana-cat-hero__stat"><i class="fas fa-calendar-check"></i> <?php echo e($bookableCount); ?> <?php echo e(__('public.instructor_stat_bookable')); ?></span>
                <?php endif; ?>
                <?php if($withCourses > 0): ?>
                <span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> <?php echo e($withCourses); ?> <?php echo e($tr('instructors.courses')); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="sana-inst-hero-search sana-reveal">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search"
                       x-model="searchQuery"
                       placeholder="<?php echo e(__('public.instructors_search_placeholder')); ?>"
                       aria-label="<?php echo e(__('public.instructors_search_placeholder')); ?>">
            </div>
        <?php else: ?>
            <div class="sana-cat-hero__soon sana-reveal">
                <span class="sana-cat-hero__soon-badge"><i class="fas fa-hourglass-half"></i> <?php echo e(__('public.instructors_coming_soon_title')); ?></span>
                <p><?php echo e(__('public.instructors_coming_soon_sub')); ?></p>
                <div class="sana-cat-hero__soon-actions">
                    <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="sana-inst-hero__actions sana-reveal">
            <?php if($hasPublishedCourses ?? false): ?>
                <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-book-open"></i> <?php echo e($tr('hero.cta_courses')); ?></a>
            <?php endif; ?>
            <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.instructors_cta_register')); ?></a>
        </div>
    </div>
</section>

<section class="sana-section <?php echo e($hasProfiles ? 'sana-section--white' : 'sana-section--soft'); ?>">
    <div class="sana-container">
        <?php if($hasProfiles): ?>
            <div class="sana-head-row sana-reveal" style="margin-bottom:28px">
                <div class="sana-head">
                    <h2 class="sana-head__title">معلّمون <span class="hl">جاهزون للحجز</span></h2>
                    <span class="sana-head__line"></span>
                </div>
                <p class="sana-inst-toolbar-note" x-show="searchQuery.trim().length > 0" x-cloak>
                    <span x-text="visibleCount"></span> <?php echo e(__('public.stat_instructors')); ?>

                </p>
            </div>

            <div class="sana-inst-grid-v2">
                <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $userId = (int) $p->user->id;
                    $photo = $p->photo_url;
                    $name = $p->user->name ?? __('public.instructor_fallback');
                    $headline = $p->headline ?: ($p->bio ? Str::limit(strip_tags($p->bio), 48) : __('public.instructor_fallback'));
                    $subjects = array_slice($p->public_subject_labels ?? [], 0, 3);
                    $bookUrl = $p->public_book_url ?? route('register');
                ?>
                <article class="sana-inst-card-v2 sana-reveal" x-show="profileVisible(<?php echo e($userId); ?>)" x-cloak>
                    <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="sana-inst-card-v2__main">
                        <div class="sana-inst-card-v2__ring">
                            <?php if($photo): ?>
                                <img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>" loading="lazy">
                            <?php else: ?>
                                <span class="av"><?php echo e(mb_substr($name, 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo e($name); ?></h3>
                        <p class="sana-inst-card-v2__role"><?php echo e(Str::limit($headline, 42)); ?></p>
                        <?php if(count($subjects) > 0): ?>
                        <div class="sana-inst-card-v2__tags">
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($subject); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                        <div class="sana-inst-card-v2__badges">
                            <?php if(($p->courses_count ?? 0) > 0): ?>
                                <span><i class="fas fa-book-open"></i> <?php echo e((int) $p->courses_count); ?> <?php echo e($tr('instructors.courses')); ?></span>
                            <?php elseif(!empty($p->is_bookable)): ?>
                                <span class="is-book"><i class="fas fa-calendar-check"></i> <?php echo e(__('public.instructor_stat_bookable')); ?></span>
                            <?php endif; ?>
                            <?php if(!empty($p->public_years_experience)): ?>
                                <span><i class="fas fa-briefcase"></i> <?php echo e(__('public.instructor_experience_years', ['years' => $p->public_years_experience])); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="sana-inst-card-v2__link"><?php echo e(__('public.view_instructor_profile')); ?> <i class="fas fa-arrow-left"></i></span>
                    </a>
                    <?php if(!empty($p->is_bookable)): ?>
                    <a href="<?php echo e($bookUrl); ?>" class="sana-btn sana-btn--yellow sana-btn--sm sana-inst-card-v2__book">
                        <i class="fas fa-calendar-plus"></i> <?php echo e(__('public.instructor_book_with')); ?>

                    </a>
                    <?php endif; ?>
                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="sana-sub-empty sana-reveal" x-show="searchQuery.trim().length > 0 && visibleCount === 0" style="display:none;margin-top:28px">
                <div class="sana-sub-empty__icon"><i class="fas fa-magnifying-glass"></i></div>
                <h3 style="font-weight:900;margin:0 0 8px"><?php echo e(__('public.no_results')); ?></h3>
                <p style="color:var(--muted);font-size:0.88rem;margin:0"><?php echo e(__('public.no_results_hint')); ?></p>
            </div>
        <?php else: ?>
            <div class="sana-inst-empty-panel sana-reveal">
                <div class="sana-inst-empty-panel__icon"><i class="fas fa-users"></i></div>
                <h2><?php echo e(__('public.instructors_coming_soon_title')); ?></h2>
                <p><?php echo e(__('public.instructors_coming_soon_sub')); ?></p>
                <div class="sana-inst-empty-panel__actions">
                    <?php echo $__env->make('landing.sana.partials.site-cta-buttons', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--outline-purple"><i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.instructors_cta_register')); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="sana-cat-cta">
    <div class="sana-container">
        <div class="sana-cat-cta__inner sana-inst-cta">
            <div>
                <h2><?php echo e(__('public.instructors_cta_title')); ?></h2>
                <p><?php echo e(__('public.instructors_cta_subtitle')); ?></p>
            </div>
            <div class="sana-cat-cta__actions">
                <?php echo $__env->make('landing.sana.partials.site-cta-buttons', ['hero' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <a href="<?php echo e(route('tutor.apply')); ?>" class="sana-btn sana-btn--purple-outline"><i class="fas fa-chalkboard-teacher"></i> <?php echo e(__('public.audience_teacher_cta')); ?></a>
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
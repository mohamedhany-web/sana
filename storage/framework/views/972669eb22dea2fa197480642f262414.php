<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $p = fn (string $key) => str_replace(':brand', $brand, __('sana_pricing.'.$key));
    $packages = __('sana_pricing.packages');
    $packageOrder = ['individual', 'group', 'live', 'recorded'];
    $packageNames = __('sana_pricing.package_names');
    $compareRows = __('sana_pricing.compare_rows');
    $ctaRoutes = [
        'individual' => route('register'),
        'group' => route('register'),
        'live' => route('public.courses'),
        'recorded' => route('public.courses'),
    ];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($p('meta_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($p('meta_description')); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/pricing')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.pricing-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">


<section class="edu-courses-page-hero py-8 lg:py-12">
    <div class="edu-container-full">
        <div class="edu-courses-inner text-center reveal">
            <nav class="edu-breadcrumb justify-center mb-4" aria-label="مسار التنقل">
                <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold"><?php echo e($p('meta_title')); ?></span>
            </nav>
            <div class="edu-pricing-types-strip mb-5">
                <?php $__currentLoopData = __('sana_pricing.types_strip'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="edu-pricing-type-pill"><?php echo e($typeLabel); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <span class="edu-badge mb-4"><i class="fas fa-layer-group"></i> <?php echo e($p('hero_badge')); ?></span>
            <h1 class="edu-section-title text-slate-900 max-w-3xl mx-auto">
                <?php echo e($p('hero_title')); ?>

                <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $p('hero_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </h1>
            <p class="text-slate-600 leading-8 mt-4 max-w-3xl mx-auto text-sm lg:text-base"><?php echo e($p('hero_sub')); ?></p>
            <p class="text-slate-800 font-semibold leading-8 mt-5 max-w-3xl mx-auto text-sm lg:text-base"><?php echo e($p('hero_tagline')); ?></p>
            <p class="text-slate-500 text-xs sm:text-sm mt-4 max-w-2xl mx-auto"><?php echo e($p('hero_note')); ?></p>
            <div class="flex flex-wrap justify-center gap-3 mt-8">
                <a href="#packages" class="edu-btn-primary text-sm"><?php echo e($p('cta_book')); ?></a>
                <a href="#compare" class="edu-btn-outline text-sm">ملخّص الباقات</a>
            </div>
        </div>
    </div>
</section>


<section class="py-10 lg:py-12 bg-white border-t border-slate-100">
    <div class="edu-container">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 reveal">
            <?php $__currentLoopData = __('sana_pricing.pillars'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $pillar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="edu-pricing-pillar <?php if($i > 0): ?> s<?php echo e(min($i, 3)); ?> <?php endif; ?>">
                <div class="edu-pricing-pillar-icon" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))">
                    <i class="fas <?php echo e($pillar['icon']); ?>"></i>
                </div>
                <p class="font-extrabold text-slate-900 text-sm mb-1"><?php echo e($pillar['title']); ?></p>
                <p class="text-xs text-slate-500 leading-6"><?php echo e($pillar['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-slate-50">
    <div class="edu-container">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start reveal">
            <div>
                <span class="edu-sub-title"><?php echo e($brand); ?></span>
                <h2 class="edu-section-title text-slate-900 mt-1"><?php echo e($p('idea_title')); ?></h2>
                <p class="text-slate-600 leading-8 mt-4 text-sm lg:text-base"><?php echo e($p('idea_intro')); ?></p>
            </div>
            <ul class="space-y-3">
                <?php $__currentLoopData = __('sana_pricing.idea_points'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-start gap-3 text-sm text-slate-700 leading-7">
                    <i class="fas fa-check-circle text-[var(--edu-primary)] mt-1 shrink-0"></i>
                    <span><?php echo e($point); ?></span>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-white" id="compare">
    <div class="edu-container reveal">
        <div class="text-center mb-8">
            <h2 class="edu-section-title text-slate-900"><?php echo e($p('compare_title')); ?></h2>
        </div>
        <div class="edu-pricing-compare-wrap">
            <table class="edu-pricing-compare">
                <thead>
                    <tr>
                        <?php $__currentLoopData = __('sana_pricing.compare_cols'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($col); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $compareRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="pkg-name"><a href="#<?php echo e($row[0]); ?>" class="text-[var(--edu-primary)] hover:underline"><?php echo e($packageNames[$row[0]] ?? $row[0]); ?></a></td>
                        <td><?php echo e($row[1]); ?></td>
                        <td><?php echo e($row[2]); ?></td>
                        <td><?php echo e($row[3]); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section class="py-12 lg:py-16 bg-[var(--edu-bg)]" id="packages">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">SANA | <?php echo e($brand); ?></span>
            <h2 class="edu-section-title text-slate-900">باقات منصة تعليمية أونلاين</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6 lg:gap-8">
            <?php $__currentLoopData = $packageOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $pkg = $packages[$key];
                $headClass = match ($pkg['color'] ?? 'primary') {
                    'purple' => 'is-purple',
                    'accent' => 'is-accent',
                    default => 'is-primary',
                };
                $cardClass = match ($pkg['color'] ?? 'primary') {
                    'purple' => 'is-purple',
                    'accent' => 'is-accent',
                    default => '',
                };
            ?>
            <article id="<?php echo e($key); ?>" class="edu-pricing-package-card <?php echo e($cardClass); ?> reveal <?php if($i > 0): ?> s<?php echo e(min($i, 3)); ?> <?php endif; ?>">
                <div class="edu-pricing-package-head <?php echo e($headClass); ?>">
                    <span class="text-xs font-bold opacity-90 block mb-1"><?php echo e($pkg['badge']); ?></span>
                    <div class="flex items-start gap-3">
                        <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-lg shrink-0">
                            <i class="fas <?php echo e($pkg['icon']); ?>"></i>
                        </span>
                        <h3 class="text-lg sm:text-xl font-extrabold leading-snug"><?php echo e($pkg['title']); ?></h3>
                    </div>
                </div>
                <div class="edu-pricing-package-body">
                    <p class="text-xs font-bold text-slate-500 mb-1"><?php echo e($pkg['best_for_title']); ?></p>
                    <p class="text-sm text-slate-700 leading-7 mb-4"><?php echo e($pkg['best_for']); ?></p>
                    <p class="text-xs font-bold text-[var(--edu-primary)] mb-2"><?php echo e($pkg['includes_title']); ?></p>
                    <ul class="space-y-2 mb-5 flex-1">
                        <?php $__currentLoopData = $pkg['includes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-2 text-sm text-slate-600 leading-6">
                            <i class="fas fa-circle text-[6px] text-[var(--edu-primary)] mt-2 shrink-0"></i>
                            <span><?php echo e($item); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <p class="edu-pricing-quote mb-5"><?php echo e($pkg['marketing']); ?></p>
                    <a href="<?php echo e($ctaRoutes[$key]); ?>" class="edu-btn-primary w-full justify-center text-sm">
                        <?php echo e($pkg['cta']); ?>

                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="text-center mb-8 max-w-2xl mx-auto">
            <h2 class="edu-section-title text-slate-900"><?php echo e($p('offers_title')); ?></h2>
            <p class="text-slate-600 text-sm mt-3 leading-7"><?php echo e($p('offers_sub')); ?></p>
        </div>
        <div class="edu-pricing-compare-wrap">
            <table class="edu-pricing-compare">
                <thead>
                    <tr>
                        <?php $__currentLoopData = __('sana_pricing.offers_cols'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($col); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = __('sana_pricing.offers'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="edu-pricing-offer-row">
                        <td class="pkg-name"><?php echo e($offer['name']); ?></td>
                        <td><?php echo e($offer['content']); ?></td>
                        <td><?php echo e($offer['for']); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-slate-50">
    <div class="edu-container">
        <h2 class="edu-section-title text-slate-900 text-center mb-8 reveal"><?php echo e($p('tips_title')); ?></h2>
        <div class="grid sm:grid-cols-2 gap-4 max-w-4xl mx-auto">
            <?php $__currentLoopData = __('sana_pricing.tips'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="edu-card p-5 flex items-start gap-4 reveal <?php if($i > 0): ?> s<?php echo e($i); ?> <?php endif; ?>">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white" style="background:var(--edu-primary)">
                    <i class="fas <?php echo e($tip['icon']); ?>"></i>
                </span>
                <p class="text-sm text-slate-700 leading-7 font-medium"><?php echo e($tip['text']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-white">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-5 sm:px-8 py-10 lg:py-12 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-3"><?php echo e($p('cta_title')); ?></h2>
            <p class="text-white/90 text-sm sm:text-base max-w-2xl mx-auto mb-8 leading-8"><?php echo e($p('cta_sub')); ?></p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?php echo e(route('register')); ?>" class="edu-btn-white !text-[var(--edu-primary)]">
                    <?php echo e($p('cta_book')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light">
                    <?php echo e($p('cta_courses')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="edu-btn-ghost-light">
                    <?php echo e($p('cta_contact')); ?>

                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.eduvalt.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
(function () {
    var nav = document.getElementById('edu-nav');
    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('is-scrolled', y > 20);
        var bar = document.getElementById('scroll-progress');
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    window.addEventListener('load', function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    });
    setTimeout(function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    }, 2000);
    document.getElementById('edu-mobile-toggle')?.addEventListener('click', function () {
        document.getElementById('edu-mobile-menu')?.classList.toggle('hidden');
    });
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    }
})();
</script>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\pricing.blade.php ENDPATH**/ ?>
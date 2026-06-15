<?php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $cta = \App\Support\PublicSiteCta::payload();
    $onContact = request()->routeIs('public.contact', 'public.contact.*');
    $onTutor = request()->routeIs('tutor.*');
    $onHome = request()->routeIs('home');
    $familiesActive = ! $onHome && ! $onTutor && ! $onContact && request()->routeIs(
        'public.courses',
        'public.courses.*',
        'public.pricing',
        'public.how_it_works',
        'public.instructors.index',
        'public.instructors.show',
        'public.certificates',
    );
?>
<header id="sana-nav" class="sana-nav <?php echo e(($onHome && !request()->routeIs('public.*')) ? 'sana-nav--hero' : 'is-solid'); ?>">
    <div class="sana-container">
        <div class="sana-nav__inner">
            <a href="<?php echo e(route('home')); ?>" class="sana-nav__brand">
                <?php if(!empty($logoUrl)): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>" class="sana-nav__logo-img">
                <?php endif; ?>
                <span class="sana-nav__logo-text"><?php echo e(strtoupper($brand)); ?></span>
            </a>

            <nav class="sana-nav__links" aria-label="القائمة">
                <a href="<?php echo e(route('home')); ?>" class="<?php echo e($onHome ? 'is-active' : ''); ?>"><?php echo e(__('public.home')); ?></a>
                <a href="<?php echo e($cta['families_path_url']); ?>" class="sana-nav__path sana-nav__path--family <?php echo e($familiesActive && !$onTutor ? 'is-active' : ''); ?>">
                    <?php echo e(__('public.nav_for_families')); ?>

                </a>
                <a href="<?php echo e($cta['teachers_path_url']); ?>" class="sana-nav__path sana-nav__path--teacher <?php echo e($onTutor ? 'is-active' : ''); ?>">
                    <?php echo e(__('public.nav_for_teachers')); ?>

                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($onContact ? 'is-active' : ''); ?>"><?php echo e(__('public.contact_page_title')); ?></a>
            </nav>

            <div class="sana-nav__actions">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="sana-nav__login">لوحتي</a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="sana-nav__login">تسجيل الدخول</a>
                    <a href="<?php echo e($cta['assessment_url']); ?>" class="sana-nav__signup"><?php echo e($cta['primary_label']); ?></a>
                <?php endif; ?>
            </div>

            <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="فتح القائمة">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
        <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
            <a href="<?php echo e(route('home')); ?>" class="<?php echo e($onHome ? 'is-active' : ''); ?>"><?php echo e(__('public.home')); ?></a>
            <a href="<?php echo e($cta['families_path_url']); ?>" class="sana-nav__path sana-nav__path--family <?php echo e($familiesActive && !$onTutor ? 'is-active' : ''); ?>"><?php echo e(__('public.nav_for_families')); ?></a>
            <a href="<?php echo e($cta['teachers_path_url']); ?>" class="sana-nav__path sana-nav__path--teacher <?php echo e($onTutor ? 'is-active' : ''); ?>"><?php echo e(__('public.nav_for_teachers')); ?></a>
            <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($onContact ? 'is-active' : ''); ?>"><?php echo e(__('public.contact_page_title')); ?></a>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>">تسجيل الدخول</a>
                <a href="<?php echo e($cta['assessment_url']); ?>" class="sana-nav__signup sana-nav__signup--block"><?php echo e($cta['primary_label']); ?></a>
                <a href="<?php echo e($cta['whatsapp_url']); ?>" <?php if($cta['has_whatsapp']): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?> class="sana-nav__signup sana-nav__signup--block sana-nav__signup--wa"><?php echo e($cta['secondary_label']); ?></a>
            <?php else: ?>
                <a href="<?php echo e(url('/dashboard')); ?>">لوحتي</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/sana/navbar.blade.php ENDPATH**/ ?>
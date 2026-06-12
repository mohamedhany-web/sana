<?php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $onCourses = request()->routeIs('public.courses', 'public.courses.saved', 'public.course.show');
    $onPricing = request()->routeIs('public.pricing', 'public.pricing*', 'public.subscription.*');
    $onAbout = request()->routeIs('public.about');
    $onContact = request()->routeIs('public.contact', 'public.contact.*');
    $onInstructors = request()->routeIs('public.instructors*');
    $onCertificates = request()->routeIs('public.certificates*');
    $onFaq = request()->routeIs('public.faq');
    $onPrivacy = request()->routeIs('public.privacy');
    $onTerms = request()->routeIs('public.terms');
    $onSubPage = $onCourses || $onPricing || $onAbout || $onContact || $onInstructors || $onCertificates || $onFaq || $onPrivacy || $onTerms;
?>
<header id="sana-nav" class="sana-nav <?php echo e($onSubPage ? 'is-solid' : 'sana-nav--hero'); ?>">
    <div class="sana-container">
        <div class="sana-nav__inner">
            <a href="<?php echo e(route('home')); ?>" class="sana-nav__brand">
                <?php if(!empty($logoUrl)): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>" class="sana-nav__logo-img">
                <?php endif; ?>
                <span class="sana-nav__logo-text"><?php echo e(strtoupper($brand)); ?></span>
            </a>

            <nav class="sana-nav__links" aria-label="القائمة">
                <a href="<?php echo e(route('home')); ?>" class="<?php echo e(!$onCourses ? 'is-active' : ''); ?>">الرئيسية</a>
                <a href="<?php echo e(route('public.courses')); ?>" class="<?php echo e($onCourses ? 'is-active' : ''); ?>">الدورات</a>
                <a href="<?php echo e(route('public.instructors.index')); ?>" class="<?php echo e($onInstructors ? 'is-active' : ''); ?>">المعلّمون</a>
                <a href="<?php echo e(route('public.pricing')); ?>" class="<?php echo e($onPricing ? 'is-active' : ''); ?>">الأسعار</a>
                <a href="<?php echo e(route('public.about')); ?>" class="<?php echo e($onAbout ? 'is-active' : ''); ?>">عن المنصة</a>
                <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($onContact ? 'is-active' : ''); ?>">تواصل معنا</a>
            </nav>

            <div class="sana-nav__actions">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="sana-nav__login">لوحتي</a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="sana-nav__login">تسجيل الدخول</a>
                    <a href="<?php echo e(route('register')); ?>" class="sana-nav__signup">إنشاء حساب</a>
                <?php endif; ?>
            </div>

            <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="فتح القائمة">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
        <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
            <a href="<?php echo e(route('home')); ?>" class="<?php echo e(!$onCourses ? 'is-active' : ''); ?>">الرئيسية</a>
            <a href="<?php echo e(route('public.courses')); ?>" class="<?php echo e($onCourses ? 'is-active' : ''); ?>">الدورات</a>
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="<?php echo e($onInstructors ? 'is-active' : ''); ?>">المعلّمون</a>
            <a href="<?php echo e(route('public.pricing')); ?>" class="<?php echo e($onPricing ? 'is-active' : ''); ?>">الأسعار</a>
            <a href="<?php echo e(route('public.about')); ?>" class="<?php echo e($onAbout ? 'is-active' : ''); ?>">عن المنصة</a>
            <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($onContact ? 'is-active' : ''); ?>">تواصل معنا</a>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>">تسجيل الدخول</a>
                <a href="<?php echo e(route('register')); ?>" class="sana-nav__signup sana-nav__signup--block">إنشاء حساب</a>
            <?php else: ?>
                <a href="<?php echo e(url('/dashboard')); ?>">لوحتي</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\navbar.blade.php ENDPATH**/ ?>
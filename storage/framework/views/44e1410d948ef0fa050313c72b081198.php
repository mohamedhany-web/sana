<?php
    $brand = config('app.name');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<header id="edu-nav" class="edu-nav fixed top-0 inset-x-0 z-[1000] transition-shadow duration-300">
    <div class="edu-container">
        <div class="flex items-center justify-between gap-4 h-[76px] lg:h-[84px]">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 shrink-0 order-1">
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($brand); ?>" class="h-10 w-10 rounded-xl object-cover">
                <?php else: ?>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl text-white font-black text-lg" style="background:var(--edu-primary)"><?php echo e(mb_substr($brand, 0, 1)); ?></span>
                <?php endif; ?>
                <span class="font-extrabold text-lg text-slate-800 hidden sm:block"><?php echo e($brand); ?></span>
            </a>

            <nav class="edu-nav__links hidden xl:flex order-2 flex-1 justify-center" aria-label="القائمة الرئيسية">
                <a href="<?php echo e(route('home')); ?>" class="edu-nav-link"><?php echo e($tr('nav.home')); ?></a>
                <a href="<?php echo e(route('public.courses')); ?>" class="edu-nav-link"><?php echo e($tr('nav.courses')); ?></a>
                <a href="<?php echo e(route('public.instructors.index')); ?>" class="edu-nav-link"><?php echo e($tr('nav.instructors')); ?></a>
                <a href="<?php echo e(route('tutor.apply')); ?>" class="edu-nav-link">انضم كمعلّم</a>
                <a href="<?php echo e(route('home')); ?>#categories" class="edu-nav-link"><?php echo e($tr('nav.categories')); ?></a>
                <a href="<?php echo e(route('public.about')); ?>" class="edu-nav-link"><?php echo e($tr('nav.about')); ?></a>
                <a href="<?php echo e(route('public.contact')); ?>" class="edu-nav-link"><?php echo e($tr('nav.contact')); ?></a>
            </nav>

            <div class="hidden lg:flex items-center gap-3 order-3 shrink-0">
                <form action="<?php echo e(route('public.courses')); ?>" method="get" class="relative hidden lg:block">
                    <input type="search" name="search" class="edu-search" placeholder="<?php echo e($tr('nav.search_placeholder')); ?>" aria-label="<?php echo e($tr('nav.search_placeholder')); ?>">
                    <i class="fas fa-search absolute top-1/2 -translate-y-1/2 start-3 text-slate-400 text-sm"></i>
                </form>
                <a href="<?php echo e(auth()->check() ? route('public.courses.saved') : route('login', ['redirect' => route('public.courses.saved')])); ?>"
                   class="w-10 h-10 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-[var(--edu-primary)]"
                   aria-label="<?php echo e(__('public.saved_courses_page_title')); ?>"
                   title="<?php echo e(__('public.saved_courses_page_title')); ?>">
                    <i class="far fa-heart"></i>
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="edu-btn-outline py-2 px-4 text-sm"><?php echo e($tr('nav.dashboard')); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="edu-btn-outline py-2 px-4 text-sm"><?php echo e($tr('nav.login')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="edu-btn-primary py-2 px-5 text-sm"><?php echo e($tr('nav.get_started')); ?></a>
                <?php endif; ?>
            </div>

            <button type="button" id="edu-mobile-toggle" class="xl:hidden order-3 w-11 h-11 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div id="edu-mobile-menu" class="xl:hidden hidden border-t border-slate-100 bg-white shadow-lg">
        <div class="edu-container py-4 flex flex-col gap-1">
            <a href="<?php echo e(route('home')); ?>" class="edu-nav-link"><?php echo e($tr('nav.home')); ?></a>
            <a href="<?php echo e(route('public.courses')); ?>" class="edu-nav-link"><?php echo e($tr('nav.courses')); ?></a>
            <a href="<?php echo e(route('public.instructors.index')); ?>" class="edu-nav-link"><?php echo e($tr('nav.instructors')); ?></a>
            <a href="<?php echo e(route('tutor.apply')); ?>" class="edu-nav-link">انضم كمعلّم</a>
            <a href="<?php echo e(route('home')); ?>#categories" class="edu-nav-link"><?php echo e($tr('nav.categories')); ?></a>
            <a href="<?php echo e(route('public.about')); ?>" class="edu-nav-link"><?php echo e($tr('nav.about')); ?></a>
            <a href="<?php echo e(route('public.contact')); ?>" class="edu-nav-link"><?php echo e($tr('nav.contact')); ?></a>
            <div class="flex gap-2 pt-3 mt-2 border-t border-slate-100">
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>" class="edu-btn-outline flex-1 justify-center text-sm"><?php echo e($tr('nav.login')); ?></a>
                    <a href="<?php echo e(route('register')); ?>" class="edu-btn-primary flex-1 justify-center text-sm"><?php echo e($tr('nav.get_started')); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="edu-btn-primary w-full justify-center text-sm"><?php echo e($tr('nav.dashboard')); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/navbar.blade.php ENDPATH**/ ?>
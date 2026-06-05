<?php
    $heroMain = public_static_url('images/saudi.png');
    $heroCircle = public_static_url('images/circle-1.png');
    $heroStudents = public_static_url('images/hero-students.png');
    $sh = fn (string $key) => __('sana_home.student_hero.'.$key);
?>
<?php echo $__env->make('tutor.partials.home-hero-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('tutor.partials.interactive-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<section class="edu-banner-area home-hero relative overflow-hidden pb-8 sm:pb-14 lg:pb-24" id="home-hero"
         x-data="studentHomeHero(<?php echo \Illuminate\Support\Js::from([
            'step_choose' => $sh('step_choose'),
            'step_choose_hint' => $sh('step_choose_hint'),
            'step_book' => $sh('step_book'),
            'step_book_hint' => $sh('step_book_hint'),
            'step_learn' => $sh('step_learn'),
            'step_learn_hint' => $sh('step_learn_hint'),
            'cta_now' => $sh('cta_now'),
            'cta_main' => $sh('cta_main'),
         ])->toHtml() ?>)" x-init="init()">

    <div class="th-hero-ambient" aria-hidden="true">
        <div class="th-hero-ambient__blob th-hero-ambient__blob--1"></div>
        <div class="th-hero-ambient__blob th-hero-ambient__blob--2"></div>
    </div>

    <div class="edu-container relative z-10 pt-6 sm:pt-10 lg:pt-16">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-10 items-center">
            <div class="w-full lg:w-1/2 text-center lg:text-start reveal">
                <span class="edu-badge mb-3 sm:mb-5"><?php echo e($sh('badge')); ?></span>
                <h1 class="edu-section-title home-hero__title text-slate-900 mb-4 sm:mb-5 leading-tight">
                    <?php echo e($sh('title_before')); ?>
                    <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => $sh('title_mark')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </h1>

                
                <div class="home-hero__visual-mobile lg:hidden my-4 sm:my-5">
                    <?php echo $__env->make('tutor.partials.home-hero-orbit', compact('heroMain', 'heroCircle', 'heroStudents'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-7 sm:leading-8 mb-4 sm:mb-6 max-w-xl mx-auto lg:mx-0">
                    <?php echo e($sh('subtitle')); ?>
                </p>

                <div class="ix-steps home-hero__steps mb-4 sm:mb-6" role="list">
                    <template x-for="(s, i) in steps" :key="s.id">
                        <div class="ix-step-pill" :class="{ 'is-active': activeStep === i, 'is-done': activeStep > i }" role="listitem">
                            <span class="ix-step-pill__num" x-text="activeStep > i ? '✓' : (i + 1)"></span>
                            <span x-text="s.label"></span>
                        </div>
                    </template>
                </div>

                <p class="home-hero__cta-hint lg:hidden" x-text="steps[activeStep].hint"></p>
                <a href="<?php echo e(route('register')); ?>" class="home-hero__cta-btn lg:hidden">
                    <span x-text="activeStep < 2 ? copy.cta_now : copy.cta_main"></span>
                    <i class="fas fa-arrow-left text-[10px]"></i>
                </a>

                <div class="th-cta-bar home-hero__cta-desktop mb-5 sm:mb-6">
                    <div class="th-cta-bar__field">
                        <i class="fas fa-graduation-cap"></i>
                        <span x-text="steps[activeStep].hint"></span>
                    </div>
                    <div class="th-cta-bar__divider hidden sm:block" aria-hidden="true"></div>
                    <a href="<?php echo e(route('register')); ?>" class="th-cta-bar__btn">
                        <span x-text="activeStep < 2 ? copy.cta_now : copy.cta_main"></span>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>

                <div class="edu-hero-actions home-hero__actions mt-4 sm:mt-6">
                    <a href="<?php echo e(route('public.instructors.index')); ?>" class="edu-btn-outline home-hero__action-btn"><?php echo e($sh('link_instructors')); ?></a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="home-hero__action-link font-bold text-[var(--edu-primary)] hover:underline text-sm"><?php echo e($sh('link_courses')); ?></a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 reveal hidden lg:block">
                <div class="relative max-w-lg mx-auto home-hero__visual">
                    <?php echo $__env->make('tutor.partials.home-hero-orbit', compact('heroMain', 'heroCircle', 'heroStudents'), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function studentHomeHero(copy) {
    copy = copy || {};
    return {
        activeStep: 0,
        copy: copy,
        steps: [
            { id: 'choose', label: copy.step_choose || 'اختر المادة والمعلّم', hint: copy.step_choose_hint || '' },
            { id: 'book', label: copy.step_book || 'احجز حصة أو باقة', hint: copy.step_book_hint || '' },
            { id: 'learn', label: copy.step_learn || 'تعلّم وتابع تقدّمك', hint: copy.step_learn_hint || '' },
        ],
        timer: null,
        init() {
            this.timer = setInterval(() => {
                this.activeStep = (this.activeStep + 1) % this.steps.length;
            }, 3200);
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        }
    };
}
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/home-hero.blade.php ENDPATH**/ ?>
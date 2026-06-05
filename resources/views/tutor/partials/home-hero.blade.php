@php
    $heroMain = public_static_url('images/saudi.png');
    $heroCircle = public_static_url('images/circle-1.png');
    $heroStudents = public_static_url('images/hero-students.png');
    $th = fn (string $key) => __('sana_home.tutor_hero.'.$key);
@endphp
@include('tutor.partials.home-hero-styles')
@include('tutor.partials.interactive-ui')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<section class="edu-banner-area relative overflow-hidden pb-16 lg:pb-24" id="tutor-join"
         x-data="tutorHomeHero(@js([
            'step_apply' => $th('step_apply'),
            'step_apply_hint' => $th('step_apply_hint'),
            'step_review' => $th('step_review'),
            'step_review_hint' => $th('step_review_hint'),
            'step_start' => $th('step_start'),
            'step_start_hint' => $th('step_start_hint'),
            'cta_now' => $th('cta_now'),
            'cta_apply' => $th('cta_apply'),
         ]))" x-init="init()">

    <div class="th-hero-ambient" aria-hidden="true">
        <div class="th-hero-ambient__blob th-hero-ambient__blob--1"></div>
        <div class="th-hero-ambient__blob th-hero-ambient__blob--2"></div>
    </div>

    <div class="edu-container relative z-10 pt-10 lg:pt-16">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-8 items-center">
            <div class="w-full lg:w-1/2 text-center lg:text-start reveal">
                <span class="edu-badge mb-5">{{ $th('badge') }}</span>
                <h1 class="edu-section-title text-slate-900 mb-5 leading-tight">
                    {{ $th('title_before') }}
                    @include('landing.eduvalt.partials.title-mark', ['text' => $th('title_mark')])
                </h1>
                <p class="text-slate-600 text-base lg:text-lg leading-8 mb-6 max-w-xl mx-auto lg:mx-0">
                    {{ $th('subtitle') }}
                </p>

                <div class="ix-steps mb-6" role="list">
                    <template x-for="(s, i) in steps" :key="s.id">
                        <div class="ix-step-pill" :class="{ 'is-active': activeStep === i, 'is-done': activeStep > i }" role="listitem">
                            <span class="ix-step-pill__num" x-text="activeStep > i ? '✓' : (i + 1)"></span>
                            <span x-text="s.label"></span>
                        </div>
                    </template>
                </div>

                <div class="th-cta-bar mb-6">
                    <div class="th-cta-bar__field">
                        <i class="fas fa-chalkboard-user"></i>
                        <span x-text="steps[activeStep].hint"></span>
                    </div>
                    <div class="th-cta-bar__divider hidden sm:block" aria-hidden="true"></div>
                    <a href="{{ route('tutor.apply') }}" class="th-cta-bar__btn">
                        <span x-text="activeStep < 2 ? copy.cta_now : copy.cta_apply"></span>
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </div>

                <div class="edu-hero-actions mt-6">
                    <a href="{{ route('register') }}" class="edu-btn-outline">{{ $th('student_book') }}</a>
                    <a href="{{ route('public.courses') }}" class="font-bold text-[var(--edu-primary)] hover:underline text-sm">{{ $th('student_courses') }}</a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 reveal">
                <div class="relative max-w-lg mx-auto">
                    <div class="th-orbit">
                        <div class="th-orbit-ring" aria-hidden="true"></div>
                        <div class="th-main-photo">
                            <img src="{{ $heroMain }}" alt="معلّم سعودي — {{ config('app.name') }}" loading="eager" decoding="async">
                        </div>
                        <div class="th-sub-photo th-sub-photo--a">
                            <img src="{{ $heroCircle }}" alt="معلّم أونلاين" loading="lazy" decoding="async">
                        </div>
                        <div class="th-sub-photo th-sub-photo--b">
                            <img src="{{ $heroStudents }}" alt="طلاب يتعلّمون" loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function tutorHomeHero(copy) {
    copy = copy || {};
    return {
        activeStep: 0,
        copy: copy,
        steps: [
            { id: 'apply', label: copy.step_apply || 'سجّل معلوماتك', hint: copy.step_apply_hint || '' },
            { id: 'review', label: copy.step_review || 'مراجعة الأكاديمية', hint: copy.step_review_hint || '' },
            { id: 'start', label: copy.step_start || 'ابدأ تدرّس', hint: copy.step_start_hint || '' },
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

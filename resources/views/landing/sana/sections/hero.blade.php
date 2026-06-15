@php
    $brand = config('app.name', 'Sana');
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
@endphp
<section class="sana-hero-wrap" id="top">
    <div class="sana-hero">
        <div class="sana-hero__bg-deco" aria-hidden="true">
            <div class="sana-hero__glow sana-hero__glow--1"></div>
            <div class="sana-hero__glow sana-hero__glow--2"></div>
            <div class="sana-hero__glow sana-hero__glow--3"></div>
            <div class="sana-hero__glow sana-hero__glow--4"></div>
            <div class="sana-hero__dotgrid"></div>
            <span class="sana-hero__bokeh sana-hero__bokeh--1"></span>
            <span class="sana-hero__bokeh sana-hero__bokeh--2"></span>
            <span class="sana-hero__bokeh sana-hero__bokeh--3"></span>
            <span class="sana-hero__bokeh sana-hero__bokeh--4"></span>
            <span class="sana-hero__geo sana-hero__geo--1"></span>
            <span class="sana-hero__geo sana-hero__geo--2"></span>
            <span class="sana-hero__geo sana-hero__geo--3"></span>
            <span class="sana-hero__cross sana-hero__cross--1"></span>
            <span class="sana-hero__cross sana-hero__cross--2"></span>
            <svg class="sana-hero__wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0,64 C240,120 480,0 720,48 C960,96 1200,24 1440,64 L1440,120 L0,120 Z" fill="rgba(255,255,255,0.04)"/>
                <path d="M0,88 C360,40 720,100 1080,56 C1260,36 1380,72 1440,80 L1440,120 L0,120 Z" fill="rgba(167,139,250,0.08)"/>
            </svg>
            <svg class="sana-hero__arc sana-hero__arc--1" viewBox="0 0 200 200" aria-hidden="true">
                <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="1.5" stroke-dasharray="6 10"/>
            </svg>
            <svg class="sana-hero__arc sana-hero__arc--2" viewBox="0 0 120 120" aria-hidden="true">
                <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(251,191,36,0.12)" stroke-width="1.5" stroke-dasharray="4 8"/>
            </svg>
        </div>
        <div class="sana-container sana-hero__container">
            <div class="sana-hero__grid">
                <div class="sana-hero__content sana-reveal">
                    <p class="sana-hero__eyebrow">
                        <span class="sana-hero__eyebrow-dot"></span>
                        {{ $pub('home_hero_eyebrow') }}
                    </p>
                    <h1 class="sana-hero__title">
                        {{ $pub('home_hero_title') }}
                        <span class="hl">{{ __('public.home_hero_title_highlight') }}</span>
                    </h1>
                    <p class="sana-hero__desc">{{ $pub('home_hero_desc') }}</p>
                    <div class="sana-hero__actions">
                        @include('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--hero'])
                    </div>
                    <ul class="sana-hero__trust" aria-label="خطوات الرحلة">
                        <li><i class="fas fa-clipboard-check"></i> {{ __('public.audience_student_step_1') }}</li>
                        <li><i class="fas fa-user-check"></i> {{ __('public.audience_student_step_2') }}</li>
                        <li><i class="fas fa-chart-line"></i> {{ __('public.audience_student_step_3') }}</li>
                    </ul>
                    <div class="sana-hero__badges">
                        <span class="sana-hero__badge"><i class="fas fa-shield-halved"></i> منصة آمنة للأطفال</span>
                        <span class="sana-hero__badge"><i class="fas fa-chalkboard-user"></i> معلّمون مختارون</span>
                    </div>
                </div>
                <div class="sana-hero__visual sana-reveal" aria-hidden="true">
                    @include('landing.sana.sections.hero-scene')
                </div>
            </div>
        </div>
    </div>

    <div class="sana-container">
        <div class="sana-hero-stats sana-hero-stats--trust sana-reveal" aria-label="لماذا تثق بسنا">
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-seedling"></i></span>
                <div>
                    <strong>{{ __('public.home_trust_1_title') }}</strong>
                    <span>{{ __('public.home_trust_1_sub') }}</span>
                </div>
            </div>
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-comments"></i></span>
                <div>
                    <strong>{{ __('public.home_trust_2_title') }}</strong>
                    <span>{{ __('public.home_trust_2_sub') }}</span>
                </div>
            </div>
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-chart-simple"></i></span>
                <div>
                    <strong>{{ __('public.home_trust_3_title') }}</strong>
                    <span>{{ __('public.home_trust_3_sub') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

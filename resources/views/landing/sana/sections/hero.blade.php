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
                        منصة تعليم عربية للأطفال والطلاب
                    </p>
                    <h1 class="sana-hero__title">تعلّم ممتع <span class="hl">يبدأ مع سنا</span></h1>
                    <p class="sana-hero__desc">
                        دروس تفاعلية، معلّمون خبراء، ومتابعة تقدّم ذكية — كل ما يحتاجه طفلك ليتعلّم بثقة ومتعة، في مكان واحد.
                    </p>
                    <div class="sana-hero__actions">
                        <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow sana-btn--lg">
                            <i class="fas fa-rocket"></i> ابدأ التعلّم مجاناً
                        </a>
                        <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--white-outline sana-btn--lg">
                            <i class="fas fa-play"></i> استكشف الدورات
                        </a>
                    </div>
                    <ul class="sana-hero__trust" aria-label="مؤشرات الثقة">
                        <li><i class="fas fa-users"></i> +120K طالب</li>
                        <li><i class="fas fa-star"></i> 4.9 تقييم</li>
                        <li><i class="fas fa-shield-halved"></i> منصة موثوقة</li>
                    </ul>
                    <div class="sana-hero__badges">
                        <span class="sana-hero__badge"><i class="fas fa-certificate"></i> شهادات معتمدة</span>
                        <span class="sana-hero__badge"><i class="fas fa-chalkboard-user"></i> معلّمون خبراء</span>
                    </div>
                </div>
                <div class="sana-hero__visual sana-reveal" aria-hidden="true">
                    @include('landing.sana.sections.hero-scene')
                </div>
            </div>
        </div>
    </div>

    <div class="sana-container">
        <div class="sana-hero-stats sana-reveal">
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-users"></i></span>
                <div>
                    <strong data-sana-counter="120" data-sana-suffix="K+">0K+</strong>
                    <span>طالب نشط</span>
                </div>
            </div>
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-book-open"></i></span>
                <div>
                    <strong data-sana-counter="850" data-sana-suffix="+">0+</strong>
                    <span>دورة تعليمية</span>
                </div>
            </div>
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-chalkboard-user"></i></span>
                <div>
                    <strong data-sana-counter="200" data-sana-suffix="+">0+</strong>
                    <span>معلّم خبير</span>
                </div>
            </div>
            <div class="sana-hero-stats__item">
                <span class="sana-hero-stats__icon"><i class="fas fa-star"></i></span>
                <div>
                    <strong data-sana-counter="98" data-sana-suffix="%">0%</strong>
                    <span>رضا الطلاب</span>
                </div>
            </div>
        </div>
    </div>
</section>

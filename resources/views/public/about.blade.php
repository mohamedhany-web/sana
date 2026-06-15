@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $a = fn (string $key) => str_replace(':brand', $brand, __('sana_about.'.$key));
    $hero = __('sana_about.hero');
    $story = __('sana_about.story');
    $timeline = __('sana_about.timeline');
    $mission = __('sana_about.mission');
    $vision = __('sana_about.vision');
    $why = __('sana_about.why');
    $metrics = __('sana_about.metrics');
    $instructorsCopy = __('sana_about.instructors');
    $values = __('sana_about.values');
    $stories = __('sana_about.stories');
    $final = __('sana_about.final');

    $fmtStat = function (int $n): string {
        if ($n >= 100000) {
            return round($n / 1000) . 'K+';
        }
        if ($n >= 1000) {
            return number_format($n / 1000, 1, '.', '') . 'K+';
        }

        return number_format($n);
    };

    $realStudents = (int) ($stats['students'] ?? 0);
    $realCompleted = (int) ($stats['completed'] ?? 0);
    $realCerts = (int) ($stats['certificates'] ?? 0);
    $realInstructors = (int) ($stats['instructors'] ?? 0);

    $hasTrustStats = \App\Support\PublicTrustMetrics::hasTrustStats($stats ?? []);

    $hasPublishedCourses = \App\Support\PublicCourseCatalog::hasPublicCourses();
    $hasPublicInstructors = \App\Support\PublicInstructorCatalog::hasPublicInstructors();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $a('meta_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $a('meta_description') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/about') }}">
    <meta property="og:title" content="{{ $a('meta_title') }} — {{ $brand }}">
    <meta property="og:description" content="{{ $a('meta_description') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.about-theme')
</head>
<body class="sana-home sana-courses-page">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-about-page">

{{-- §1 Hero --}}
<section class="sana-ab-hero">
    <div class="sana-container">
        <div class="sana-ab-hero__grid sana-reveal">
            <div class="sana-ab-hero__content">
                <span class="sana-ab-hero__eyebrow"><i class="fas fa-heart"></i> {{ str_replace(':brand', $brand, $hero['eyebrow']) }}</span>
                <h1 class="sana-ab-hero__title">
                    {{ $hero['title'] }}
                    <span class="hl">{{ $hero['highlight'] }}</span>
                </h1>
                <p class="sana-ab-hero__mission">{{ str_replace(':brand', $brand, $hero['mission']) }}</p>
                <p class="sana-ab-hero__sub">{{ str_replace(':brand', $brand, $hero['sub']) }}</p>
                <div class="sana-ab-hero__actions">
                    @include('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--hero'])
                    <a href="#story" class="sana-btn sana-btn--white-outline sana-btn--lg" style="margin-top:10px">
                        <i class="fas fa-book-open"></i> {{ $hero['cta_secondary'] }}
                    </a>
                </div>
            </div>
            <div class="sana-ab-hero__visual">
                @include('landing.sana.partials.about-hero-scene')
                <div class="sana-ab-hero__badge">
                    <strong>{{ $hero['badge_title'] }}</strong>
                    <span>{{ $hero['badge_sub'] }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- §2 Our Story --}}
<section class="sana-section" id="story">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:40px">
            <span class="sana-head__eyebrow">{{ str_replace(':brand', $brand, $story['eyebrow']) }}</span>
            <h2 class="sana-head__title">
                {{ str_replace(':brand', $brand, $story['title']) }}
                <span class="hl">{{ str_replace(':brand', $brand, $story['highlight']) }}</span>
            </h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-ab-story">
            <div class="sana-ab-story__intro sana-reveal">
                <p>{{ $story['intro'] }}</p>
                <div class="sana-ab-story__blocks">
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-circle-exclamation"></i> {{ $story['challenge_title'] }}</h4>
                        <p>{{ $story['challenge'] }}</p>
                    </div>
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-lightbulb"></i> {{ $story['vision_title'] }}</h4>
                        <p>{{ $story['vision'] }}</p>
                    </div>
                    <div class="sana-ab-story__block">
                        <h4><i class="fas fa-chart-line"></i> {{ $story['impact_title'] }}</h4>
                        <p>{{ $story['impact'] }}</p>
                    </div>
                </div>
            </div>
            <div class="sana-ab-timeline sana-reveal">
                @foreach($timeline as $item)
                <div class="sana-ab-timeline__item">
                    <span class="sana-ab-timeline__dot"></span>
                    <span class="sana-ab-timeline__year">{{ $item['year'] }}</span>
                    <strong>{{ $item['title'] }}</strong>
                    <p>{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- §3 Mission --}}
<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $mission['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $mission['title'] }} <span class="hl">{{ $mission['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $mission['sub'] }}</p>
        </div>
        <div class="sana-ab-pillars">
            @foreach($mission['pillars'] as $pillar)
            <div class="sana-ab-pillar sana-reveal">
                <div class="sana-ab-pillar__icon"><i class="fas {{ $pillar['icon'] }}"></i></div>
                <strong>{{ $pillar['title'] }}</strong>
                <span>{{ $pillar['desc'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §4 Vision --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $vision['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $vision['title'] }} <span class="hl">{{ $vision['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $vision['sub'] }}</p>
        </div>
        <div class="sana-ab-vision">
            @foreach($vision['points'] as $point)
            <div class="sana-ab-vision__card sana-reveal">
                <span class="sana-ab-vision__icon"><i class="fas {{ $point['icon'] }}"></i></span>
                <div>
                    <strong>{{ $point['title'] }}</strong>
                    <p>{{ $point['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §5 Why choose us --}}
<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $why['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $why['title'] }} <span class="hl">{{ $why['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $why['sub'] }}</p>
        </div>
        <div class="sana-ab-why">
            @foreach($why['cards'] as $card)
            <div class="sana-ab-why__card sana-reveal">
                <div class="sana-ab-why__icon"><i class="fas {{ $card['icon'] }}"></i></div>
                <strong>{{ $card['title'] }}</strong>
                <p>{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §6 Impact (real data only) --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $metrics['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $metrics['title'] }} <span class="hl">{{ $metrics['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $metrics['sub'] }}</p>
        </div>
        @if($hasTrustStats)
        <div class="sana-ab-metrics">
            @if($realStudents > 0)
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="{{ min($realStudents, 999999) }}" data-sana-suffix="+">{{ $fmtStat($realStudents) }}</strong>
                <span>{{ $metrics['students'] }}</span>
            </div>
            @endif
            @if($realCompleted > 0)
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-book-open"></i>
                <strong data-sana-counter="{{ min($realCompleted, 999999) }}" data-sana-suffix="+">{{ $fmtStat($realCompleted) }}</strong>
                <span>{{ $metrics['completed'] }}</span>
            </div>
            @endif
            @if($realCerts > 0)
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="{{ min($realCerts, 999999) }}" data-sana-suffix="+">{{ $fmtStat($realCerts) }}</strong>
                <span>{{ $metrics['certificates'] }}</span>
            </div>
            @endif
            @if($realInstructors > 0)
            <div class="sana-ab-metric sana-reveal">
                <i class="fas fa-chalkboard-user"></i>
                <strong data-sana-counter="{{ min($realInstructors, 999999) }}" data-sana-suffix="+">{{ $fmtStat($realInstructors) }}</strong>
                <span>{{ $metrics['instructors'] }}</span>
            </div>
            @endif
        </div>
        @else
        <div class="sana-ab-metrics sana-ab-metrics--launch sana-reveal">
            <div class="sana-ab-metric">
                <i class="fas fa-seedling"></i>
                <strong>{{ $metrics['launch_title'] }}</strong>
                <span>{{ $metrics['launch_sub'] }}</span>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- §7 Instructors --}}
@if($hasPublicInstructors && $instructors->isNotEmpty())
<section class="sana-section sana-section--soft" id="instructors">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $instructorsCopy['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $instructorsCopy['title'] }} <span class="hl">{{ $instructorsCopy['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $instructorsCopy['sub'] }}</p>
        </div>
        <div class="sana-ab-instructors">
            @forelse($instructors as $profile)
            @php
                $user = $profile->user;
                $name = $user->name ?? 'معلّم';
                $initial = mb_substr($name, 0, 1);
                $photo = $profile->photo_url;
                $headline = $profile->headline ?: ($profile->bio ? Str::limit(strip_tags($profile->bio), 60) : __('public.instructor_fallback'));
                $exp = (int) ($profile->tutor_years_experience ?? $profile->experience ?? 0);
                $coursesCount = (int) ($profile->courses_count ?? 0);
            @endphp
            <a href="{{ route('public.instructors.show', $user) }}" class="sana-ab-instructor sana-reveal">
                <div class="sana-ab-instructor__photo">
                    @if($photo)
                        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy">
                    @else
                        <span class="av">{{ $initial }}</span>
                    @endif
                </div>
                <div class="sana-ab-instructor__body">
                    <strong>{{ $name }}</strong>
                    <p class="sana-ab-instructor__headline">{{ $headline }}</p>
                    <div class="sana-ab-instructor__meta">
                        @if($coursesCount > 0)
                            <span><i class="fas fa-book"></i> {{ $coursesCount }} {{ $instructorsCopy['courses'] }}</span>
                        @endif
                        @if($exp > 0)
                            <span><i class="fas fa-clock"></i> {{ $exp }} {{ $instructorsCopy['experience'] }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <p class="sana-ab-instructors__empty sana-reveal">{{ $instructorsCopy['empty'] }}</p>
            @endforelse
        </div>
        <div class="sana-ab-view-all sana-reveal">
            <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--purple">
                {{ $instructorsCopy['view_all'] }} <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- §8 Values --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $values['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $values['title'] }} <span class="hl">{{ $values['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $values['sub'] }}</p>
        </div>
        <div class="sana-ab-values">
            @foreach($values['items'] as $value)
            <div class="sana-ab-value sana-reveal">
                <div class="sana-ab-value__icon"><i class="fas {{ $value['icon'] }}"></i></div>
                <strong>{{ $value['title'] }}</strong>
                <span>{{ $value['desc'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §9 Success stories --}}
@if($testimonials->isNotEmpty())
<section class="sana-section sana-section--soft" id="success-stories">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <span class="sana-head__eyebrow">{{ $stories['eyebrow'] }}</span>
            <h2 class="sana-head__title">{{ $stories['title'] }} <span class="hl">{{ $stories['highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $stories['sub'] }}</p>
        </div>
        <div class="sana-test-m">
            @foreach($testimonials as $t)
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«{{ Str::limit(strip_tags($t->body ?? ''), 200) }}»</p>
                <div class="stars">@for($s = 0; $s < 5; $s++)<i class="fas fa-star"></i>@endfor</div>
                <div class="author">
                    @if($t->isImageType() && $t->publicImageUrl())
                        <img src="{{ $t->publicImageUrl() }}" alt="">
                    @else
                        <span class="av">{{ $t->author_name ? mb_substr($t->author_name, 0, 1) : '؟' }}</span>
                    @endif
                    <div>
                        <strong>{{ $t->author_name ?? 'عميل' }}</strong>
                        @if($t->role_label)<small>{{ $t->role_label }}</small>@endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- §10 Final CTA --}}
<section class="sana-ab-final">
    <div class="sana-container sana-reveal">
        <div class="sana-ab-final__box">
            <h2>{{ $final['title'] }}</h2>
            <p>{{ str_replace(':brand', $brand, $final['sub']) }}</p>
            <div class="sana-ab-final__actions">
                @include('landing.sana.partials.site-cta-buttons', ['class' => 'sana-site-cta--center'])
                @if($hasPublishedCourses)
                <a href="{{ route('public.courses') }}" class="sana-btn sana-btn--ghost-light sana-btn--lg" style="margin-top:10px">
                    {{ $final['cta_secondary'] }} <i class="fas fa-compass"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>

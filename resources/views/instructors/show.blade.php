@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $user = $profile->user;
    $photo = $profile->photo_url;
    $subjects = $profile->public_subject_labels ?? [];
    $grades = $profile->public_grade_labels ?? [];
    $curricula = $profile->public_curriculum_labels ?? [];
    $stages = $profile->public_stage_labels ?? [];
    $sessions = $profile->public_session_labels ?? [];
    $demoVideo = $profile->public_demo_video ?? null;
    $bookUrl = $profile->public_book_url ?? route('register');
    $experienceItems = $profile->experience_list ?? [];
    $skills = $profile->skills_list ?? [];
    $coursesCount = $courses->count();
    $instrPageTitle = ($user->name ?? __('public.instructor_fallback')).' — '.$brand;
    $bioPlain = trim(strip_tags((string) ($profile->bio ?? '')));
    $instrPageDesc = Str::limit($bioPlain !== '' ? $bioPlain : ($profile->headline ?? ''), 160);
    $headline = $profile->headline ?: __('public.instructor_fallback');
    $isBookable = ! empty($profile->is_bookable);
    $hasVideo = is_array($demoVideo) && (! empty($demoVideo['embed']) || ! empty($demoVideo['direct']));
    $allPills = array_values(array_unique(array_merge(
        array_slice($subjects, 0, 4),
        array_slice($grades, 0, 2),
        array_slice($curricula, 0, 2)
    )));
@endphp
<!DOCTYPE html>
@php
    $htmlLang = $htmlLang ?? (str_starts_with((string) app()->getLocale(), 'en') ? 'en' : 'ar');
    $htmlDir = $htmlDir ?? ($htmlLang === 'en' ? 'ltr' : 'rtl');
@endphp
<html lang="{{ $htmlLang }}" dir="{{ $htmlDir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $instrPageTitle }}</title>
    <meta name="description" content="{{ $instrPageDesc }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ route('public.instructors.show', $user) }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.instructors-catalog-theme')
    @include('landing.sana.instructor-show-theme')
</head>
<body class="sana-home sana-courses-page sana-instructors-page sana-instructor-show-page">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-is-page">

    <section class="sana-is-hero">
        <div class="sana-is-hero__dots" aria-hidden="true"></div>
        <div class="sana-container sana-is-hero__inner">
            <nav class="sana-is-breadcrumb sana-reveal" aria-label="{{ __('public.breadcrumb_aria') }}">
                <a href="{{ route('home') }}">{{ __('public.home') }}</a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <a href="{{ route('public.instructors.index') }}">{{ __('public.instructors_page_title') }}</a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                <span>{{ $user->name }}</span>
            </nav>

            <div class="sana-is-hero__grid">
                <div class="sana-reveal">
                    <span class="sana-is-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> {{ $tr('instructors.badge') }}</span>
                    <h1 class="sana-is-hero__title">{{ $user->name }}</h1>
                    <p class="sana-is-hero__headline">{{ $headline }}</p>

                    @if($bioPlain !== '')
                        <p class="sana-is-hero__bio">{{ Str::limit($bioPlain, 180) }}</p>
                    @endif

                    <div class="sana-is-hero__pills">
                        @if($isBookable)
                            <span class="sana-is-pill sana-is-pill--book"><i class="fas fa-calendar-check"></i> {{ __('public.instructor_stat_bookable') }}</span>
                        @endif
                        @if(!empty($profile->public_years_experience))
                            <span class="sana-is-pill sana-is-pill--gold"><i class="fas fa-briefcase"></i> {{ __('public.instructor_experience_years', ['years' => $profile->public_years_experience]) }}</span>
                        @endif
                        @if($hasVideo)
                            <span class="sana-is-pill sana-is-pill--video"><i class="fas fa-circle-play"></i> {{ __('public.instructor_demo_video') }}</span>
                        @endif
                        @if($coursesCount > 0)
                            <span class="sana-is-pill"><i class="fas fa-book-open"></i> {{ $coursesCount }} {{ $tr('instructors.courses') }}</span>
                        @endif
                        @foreach(array_slice($allPills, 0, 4) as $label)
                            <span class="sana-is-pill">{{ $label }}</span>
                        @endforeach
                    </div>

                    @if(count($sessions) > 0)
                    <div class="sana-is-hero__meta">
                        <span><i class="fas fa-clock"></i> {{ implode(' · ', $sessions) }}</span>
                    </div>
                    @endif

                    <div class="sana-is-hero__actions">
                        @if($isBookable)
                            <a href="{{ $bookUrl }}" class="sana-btn sana-btn--yellow">
                                <i class="fas fa-calendar-plus"></i> {{ __('public.instructor_book_with') }}
                            </a>
                        @endif
                        @if($hasVideo)
                            <a href="#instructor-video" class="sana-btn sana-btn--white-outline sana-btn--sm">
                                <i class="fas fa-play"></i> {{ __('public.instructor_watch_style') }}
                            </a>
                        @endif
                        <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--white-outline sana-btn--sm">
                            <i class="fas fa-arrow-right"></i> {{ __('public.all_instructors_link') }}
                        </a>
                    </div>
                </div>

                <div class="sana-is-hero__photo sana-reveal">
                    <div class="sana-is-hero__ring">
                        @if($photo)
                            <img src="{{ $photo }}" alt="{{ $user->name }}" loading="eager">
                        @else
                            <span class="av">{{ mb_substr($user->name ?? 'م', 0, 1) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(count($subjects) > 0 || count($grades) > 0 || !empty($profile->public_years_experience) || count($sessions) > 0 || $coursesCount > 0)
    <section class="sana-is-facts" aria-label="{{ __('public.instructor_quick_facts') }}">
        <div class="sana-container">
            <div class="sana-is-facts__grid sana-reveal">
                @if(!empty($profile->public_years_experience))
                <div class="sana-is-fact">
                    <span class="sana-is-fact__value">{{ $profile->public_years_experience }}</span>
                    <span class="sana-is-fact__label">{{ __('public.instructor_years_label') }}</span>
                </div>
                @endif
                @if(count($subjects) > 0)
                <div class="sana-is-fact">
                    <span class="sana-is-fact__value">{{ count($subjects) }}</span>
                    <span class="sana-is-fact__label">{{ __('public.instructor_subjects_label') }}</span>
                </div>
                @endif
                @if(count($grades) > 0)
                <div class="sana-is-fact">
                    <span class="sana-is-fact__value">{{ count($grades) }}</span>
                    <span class="sana-is-fact__label">{{ __('public.instructor_grades_label') }}</span>
                </div>
                @endif
                @if(count($sessions) > 0)
                <div class="sana-is-fact">
                    <span class="sana-is-fact__value">{{ count($sessions) }}</span>
                    <span class="sana-is-fact__label">{{ __('public.instructor_sessions_label') }}</span>
                </div>
                @endif
                @if($coursesCount > 0)
                <div class="sana-is-fact">
                    <span class="sana-is-fact__value">{{ $coursesCount }}</span>
                    <span class="sana-is-fact__label">{{ $tr('instructors.courses') }}</span>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <section class="sana-section sana-section--white">
        <div class="sana-container">
            <div class="sana-is-layout">
                <div>
                    @if($hasVideo)
                    <div id="instructor-video" class="sana-is-panel sana-is-panel--video sana-reveal">
                        <div class="sana-is-panel__head">
                            <h2 class="sana-is-panel__title"><i class="fas fa-circle-play"></i> {{ __('public.instructor_watch_style') }}</h2>
                            <p class="sana-is-panel__lead">{{ __('public.instructor_demo_video_lead') }}</p>
                        </div>
                        @if(!empty($demoVideo['title']))
                            <p class="sana-is-video__caption">{{ $demoVideo['title'] }}</p>
                        @endif
                        <div class="sana-is-video">
                            @if(!empty($demoVideo['embed']))
                                <iframe src="{{ $demoVideo['embed'] }}" title="{{ __('public.instructor_demo_video') }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                            @elseif(!empty($demoVideo['direct']))
                                <video src="{{ $demoVideo['direct'] }}" controls playsinline preload="metadata"></video>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($bioPlain !== '')
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-user"></i> {{ __('public.instructor_about_heading') }}</h2>
                        <p class="sana-is-exp-text">{{ $profile->bio }}</p>
                    </div>
                    @endif

                    @if(count($skills) > 0)
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-star"></i> {{ __('public.instructor_skills_heading') }}</h2>
                        <div class="sana-is-chips">
                            @foreach($skills as $skill)
                                <span class="sana-is-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(count($subjects) > 0 || count($grades) > 0 || count($curricula) > 0 || count($stages) > 0)
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-graduation-cap"></i> {{ __('public.instructor_teaching_focus') }}</h2>
                        <div class="sana-is-focus">
                            @if(count($subjects) > 0)
                            <div class="sana-is-focus__row">
                                <span class="sana-is-focus__label">{{ __('public.instructor_subjects_label') }}</span>
                                <div class="sana-is-chips">
                                    @foreach($subjects as $label)
                                        <span class="sana-is-chip">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(count($grades) > 0)
                            <div class="sana-is-focus__row">
                                <span class="sana-is-focus__label">{{ __('public.instructor_grades_label') }}</span>
                                <div class="sana-is-chips">
                                    @foreach($grades as $label)
                                        <span class="sana-is-chip sana-is-chip--soft">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(count($curricula) > 0)
                            <div class="sana-is-focus__row">
                                <span class="sana-is-focus__label">{{ __('public.instructor_curricula_label') }}</span>
                                <div class="sana-is-chips">
                                    @foreach($curricula as $label)
                                        <span class="sana-is-chip sana-is-chip--soft">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if(count($stages) > 0)
                            <div class="sana-is-focus__row">
                                <span class="sana-is-focus__label">{{ __('public.instructor_stages_label') }}</span>
                                <div class="sana-is-chips">
                                    @foreach($stages as $label)
                                        <span class="sana-is-chip sana-is-chip--soft">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($profile->experience || count($experienceItems) > 0)
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-briefcase"></i> {{ __('public.experience') }}</h2>
                        @if(count($experienceItems) > 0)
                            <ul class="sana-is-exp-list">
                                @foreach($experienceItems as $item)
                                    <li><i class="fas fa-check-circle"></i><span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        @else
                            <p class="sana-is-exp-text">{{ $profile->experience }}</p>
                        @endif
                    </div>
                    @endif

                    @if($coursesCount > 0)
                    <div class="sana-is-panel sana-reveal">
                        <h2 class="sana-is-panel__title"><i class="fas fa-book-open"></i> {{ __('public.instructor_courses') }}</h2>
                        <div class="sana-is-courses-grid">
                            @foreach($courses as $c)
                                @include('landing.sana.partials.course-card', ['course' => $c, 'noReveal' => true])
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <aside class="sana-is-sidebar sana-reveal">
                    @if($isBookable)
                        <p class="sana-is-sidebar__label">{{ __('public.instructor_booking_label') }}</p>
                        <h2 class="sana-is-sidebar__heading">{{ $user->name }}</h2>
                        <p class="sana-is-sidebar__desc">{{ $profile->public_booking_label ?? __('public.instructor_booking_via_packages') }}</p>

                        <ul class="sana-is-sidebar__list">
                            @if(count($subjects) > 0)
                            <li><span>{{ __('public.instructor_subjects_label') }}</span><span>{{ implode('، ', array_slice($subjects, 0, 4)) }}</span></li>
                            @endif
                            @if(count($grades) > 0)
                            <li><span>{{ __('public.instructor_grades_label') }}</span><span>{{ implode('، ', array_slice($grades, 0, 4)) }}</span></li>
                            @endif
                            @if(count($curricula) > 0)
                            <li><span>{{ __('public.instructor_curricula_label') }}</span><span>{{ implode('، ', array_slice($curricula, 0, 3)) }}</span></li>
                            @endif
                            @if(count($sessions) > 0)
                            <li><span>{{ __('public.instructor_sessions_label') }}</span><span>{{ implode('، ', $sessions) }}</span></li>
                            @endif
                            @if(!empty($profile->public_years_experience))
                            <li><span>{{ __('public.experience') }}</span><span>{{ __('public.instructor_experience_years', ['years' => $profile->public_years_experience]) }}</span></li>
                            @endif
                            @if($coursesCount > 0)
                            <li><span>{{ $tr('instructors.courses') }}</span><span>{{ $coursesCount }}</span></li>
                            @endif
                        </ul>

                        <div class="sana-is-sidebar__actions">
                            <a href="{{ $bookUrl }}" class="sana-btn sana-btn--yellow">
                                <i class="fas fa-calendar-plus"></i> {{ __('public.instructor_book_with') }}
                            </a>
                            @if($hasVideo)
                            <a href="#instructor-video" class="sana-btn sana-btn--purple-outline">
                                <i class="fas fa-play"></i> {{ __('public.instructor_demo_video') }}
                            </a>
                            @endif
                        </div>
                    @else
                        <p class="sana-is-sidebar__label">{{ __('public.instructors_page_title') }}</p>
                        <h2 class="sana-is-sidebar__heading">{{ __('public.instructors_coming_soon_title') }}</h2>
                        <p class="sana-is-sidebar__desc">{{ __('public.instructors_coming_soon_sub') }}</p>
                        <div class="sana-is-sidebar__actions">
                            @include('landing.sana.partials.site-cta-buttons', ['size' => 'sm', 'class' => 'sana-site-cta--stack'])
                            <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--purple-outline">
                                <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                            </a>
                        </div>
                    @endif

                    <a href="{{ route('public.instructors.index') }}" class="sana-btn sana-btn--ghost-muted" style="margin-top:14px">
                        <i class="fas fa-arrow-right"></i> {{ __('public.all_instructors_link') }}
                    </a>
                </aside>
            </div>
        </div>
    </section>

</main>

@if($isBookable)
<div class="sana-is-mobile-bar is-visible" aria-hidden="false">
    <a href="{{ $bookUrl }}" class="sana-btn sana-btn--yellow">
        <i class="fas fa-calendar-plus"></i> {{ __('public.instructor_book_with') }}
    </a>
</div>
@endif

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>

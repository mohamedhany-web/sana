@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $pub = fn (string $key) => str_replace(':brand', $brand, __('public.'.$key));
    $studentTopicIcons = ['user-plus', 'calendar-check', 'book-open', 'certificate', 'headset'];
    $teacherTopicIcons = ['file-signature', 'shield-halved', 'chalkboard-user', 'wallet', 'life-ring'];
    $hasPublishedCourses = $hasPublishedCourses ?? \App\Support\PublicCourseCatalog::hasPublicCourses();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ __('public.help_page_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $pub('help_meta_description') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/help') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.subpages-theme')
</head>
<body class="sana-home sana-courses-page" x-data="{ audience: 'student' }">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-sub-page">

<section class="sana-sub-hero">
    <div class="sana-container">
        <div class="sana-sub-hero__grid sana-reveal">
            <div class="sana-sub-hero__content">
                <nav class="sana-sub-hero__breadcrumb" aria-label="مسار التنقل">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left" style="font-size:10px;opacity:.5"></i>
                    <span>{{ __('public.help_page_title') }}</span>
                </nav>
                <span class="sana-sub-hero__eyebrow"><i class="fas fa-life-ring"></i> {{ __('public.help_hero_badge') }}</span>
                <h1 class="sana-sub-hero__title">
                    {{ __('public.help_page_title') }}
                    <span class="hl">{{ $brand }}</span>
                </h1>
                <p class="sana-sub-hero__sub">{{ $pub('help_hero_sub') }}</p>
                <div class="sana-sub-hero__actions">
                    <a href="{{ route('public.faq') }}" class="sana-btn sana-btn--yellow">
                        <i class="fas fa-circle-question"></i> {{ __('public.faq_page_title') }}
                    </a>
                    <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--white-outline">
                        <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-help-audience sana-reveal">
            <div class="sana-help-audience__toggle" role="tablist">
                <button type="button" role="tab" :class="{ 'is-active': audience === 'student' }" @click="audience = 'student'">
                    <i class="fas fa-user-graduate"></i> {{ __('public.help_audience_student') }}
                </button>
                <button type="button" role="tab" :class="{ 'is-active': audience === 'teacher' }" @click="audience = 'teacher'">
                    <i class="fas fa-chalkboard-teacher"></i> {{ __('public.help_audience_teacher') }}
                </button>
            </div>

            <p class="sana-help-audience__intro" x-show="audience === 'student'" x-cloak>{{ $pub('help_family_intro') }}</p>
            <p class="sana-help-audience__intro" x-show="audience === 'teacher'" x-cloak>{{ $pub('help_teacher_intro') }}</p>

            {{-- Family / student path --}}
            <div x-show="audience === 'student'" x-cloak>
                <h2 class="sana-help-section-title sana-help-section-title--first">{{ __('public.help_start_title') }}</h2>
                <div class="sana-help-cards">
                    <a href="{{ route('public.faq') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-circle-question"></i></span>
                        <strong>{{ __('public.help_card_faq_title') }}</strong>
                        <p>{{ __('public.help_card_faq_desc') }}</p>
                    </a>
                    @if($hasPublishedCourses)
                    <a href="{{ route('public.courses') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-book-open"></i></span>
                        <strong>{{ __('public.help_card_courses_title') }}</strong>
                        <p>{{ __('public.help_card_courses_desc') }}</p>
                    </a>
                    @else
                    <a href="{{ route('public.instructors.index') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-chalkboard-user"></i></span>
                        <strong>{{ __('public.help_card_instructors_title') }}</strong>
                        <p>{{ __('public.help_card_instructors_desc') }}</p>
                    </a>
                    @endif
                    <a href="{{ route('public.contact') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-envelope"></i></span>
                        <strong>{{ __('public.help_card_contact_title') }}</strong>
                        <p>{{ __('public.help_card_contact_desc') }}</p>
                    </a>
                </div>

                <h2 class="sana-help-section-title">{{ __('public.help_topics_title') }}</h2>
                <div class="sana-help-topics">
                    @foreach(range(1, 5) as $i)
                    <article class="sana-help-topic">
                        <span class="sana-help-topic__icon"><i class="fas fa-{{ $studentTopicIcons[$i - 1] }}"></i></span>
                        <div>
                            <h3>{{ __('public.help_topic_'.$i.'_title') }}</h3>
                            <p>{{ __('public.help_topic_'.$i.'_desc') }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>

                <h2 class="sana-help-section-title">{{ __('public.help_steps_title') }}</h2>
                <ol class="sana-help-steps">
                    @foreach(range(1, 4) as $step)
                    <li>
                        <strong>{{ __('public.help_step_'.$step.'_title') }}</strong>
                        <span>{{ __('public.help_step_'.$step.'_desc') }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>

            {{-- Teacher path --}}
            <div x-show="audience === 'teacher'" x-cloak>
                <h2 class="sana-help-section-title sana-help-section-title--first">{{ __('public.help_start_title') }}</h2>
                <div class="sana-help-cards">
                    <a href="{{ route('tutor.apply') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-file-signature"></i></span>
                        <strong>{{ __('public.help_card_apply_title') }}</strong>
                        <p>{{ __('public.help_card_apply_desc') }}</p>
                    </a>
                    <a href="{{ route('tutor.policy') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-shield-halved"></i></span>
                        <strong>{{ __('public.teacher_policy') }}</strong>
                        <p>{{ __('public.audience_teacher_desc') }}</p>
                    </a>
                    <a href="{{ route('public.contact') }}" class="sana-help-card">
                        <span class="sana-help-card__icon"><i class="fas fa-envelope"></i></span>
                        <strong>{{ __('public.help_card_contact_title') }}</strong>
                        <p>{{ __('public.help_card_contact_desc') }}</p>
                    </a>
                </div>

                <h2 class="sana-help-section-title">{{ __('public.help_topics_title') }}</h2>
                <div class="sana-help-topics">
                    @foreach(range(1, 5) as $i)
                    <article class="sana-help-topic">
                        <span class="sana-help-topic__icon sana-help-topic__icon--teacher"><i class="fas fa-{{ $teacherTopicIcons[$i - 1] }}"></i></span>
                        <div>
                            <h3>{{ __('public.help_teacher_topic_'.$i.'_title') }}</h3>
                            <p>{{ __('public.help_teacher_topic_'.$i.'_desc') }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>

                <h2 class="sana-help-section-title">{{ __('public.help_steps_title') }}</h2>
                <ol class="sana-help-steps">
                    @foreach(range(1, 4) as $step)
                    <li>
                        <strong>{{ __('public.help_teacher_step_'.$step.'_title') }}</strong>
                        <span>{{ __('public.help_teacher_step_'.$step.'_desc') }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="sana-sub-final">
    <div class="sana-container sana-reveal">
        <div class="sana-sub-final__box">
            <h2>{{ __('public.help_cta_title') }}</h2>
            <p>{{ __('public.help_cta_desc') }}</p>
            <div class="sana-sub-final__actions">
                <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--yellow">
                    <i class="fas fa-paper-plane"></i> {{ __('public.help_cta_btn') }}
                </a>
                <a href="{{ route('public.faq') }}" class="sana-btn sana-btn--ghost-light">
                    <i class="fas fa-circle-question"></i> {{ __('public.faq_page_title') }}
                </a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
<style>[x-cloak]{display:none!important}</style>
</body>
</html>

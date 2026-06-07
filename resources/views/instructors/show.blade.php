@php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $locale = 'ar';
    $isRtl = true;
    $user = $profile->user;
    $defaultPhoto = 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=480&auto=format&fit=crop&q=80';
    $photo = $profile->photo_path ? $profile->photo_url : $defaultPhoto;
    $skills = $profile->skills_list ?? [];
    $experienceItems = $profile->experience_list ?? [];
    $socialLinks = is_array($profile->social_links ?? null) ? array_filter($profile->social_links) : [];
    $coursesCount = $courses->count();
    $skillsCount = count($skills);
    $experienceCount = count($experienceItems) ?: ($profile->experience ? 1 : 0);

    $instrPageTitle = ($user->name ?? __('public.instructor_fallback')) . ' — ' . ($profile->headline ?? __('public.instructor_fallback')) . ' | ' . $brand;
    $instrPageDesc = Str::limit(strip_tags($profile->bio ?? $profile->headline ?? ''), 160);
    $instrPageImg = $profile->photo_path ? $profile->photo_url : asset('images/og-image.jpg');
    $instrPageUrl = route('public.instructors.show', $user);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $instrPageTitle }}</title>
    <meta name="title" content="{{ $instrPageTitle }}">
    <meta name="description" content="{{ $instrPageDesc }}">
    <meta name="keywords" content="{{ $user->name ?? '' }}, {{ $profile->headline ?? '' }}, {{ $brand }}">
    <meta name="author" content="{{ $user->name ?? $brand }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ $instrPageUrl }}">
    <link rel="alternate" hreflang="ar" href="{{ $instrPageUrl }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ $instrPageUrl }}">
    <meta property="og:title" content="{{ $instrPageTitle }}">
    <meta property="og:description" content="{{ $instrPageDesc }}">
    <meta property="og:image" content="{{ $instrPageImg }}">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $instrPageTitle }}">
    <meta name="twitter:description" content="{{ $instrPageDesc }}">
    <meta name="twitter:image" content="{{ $instrPageImg }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.instructor-profile-page')
    @include('partials.rtl-base')
    @if(!empty($savedCourseIds))
        @include('landing.eduvalt.partials.course-favorites-init')
    @endif
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

    {{-- Hero --}}
    <section class="edu-instructor-profile-hero py-8 lg:py-12">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <nav class="edu-breadcrumb reveal" aria-label="مسار التنقل">
                    <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <a href="{{ route('public.instructors.index') }}">{{ __('public.instructors_page_title') }}</a>
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <span class="text-slate-800 font-semibold">{{ $user->name }}</span>
                </nav>

                <div class="flex flex-col md:flex-row gap-8 lg:gap-10 items-center md:items-start reveal">
                    <div class="flex-shrink-0 text-center md:text-start">
                        <div class="relative inline-block">
                            <img src="{{ $photo }}" alt="{{ $user->name }}"
                                 class="edu-instructor-profile-photo"
                                 loading="eager"
                                 onerror="this.src='{{ $defaultPhoto }}'">
                            <span class="absolute -bottom-1 -start-1 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold text-white shadow-md"
                                  style="background:var(--edu-accent-dark)">
                                <i class="fas fa-check-circle text-[9px]"></i>
                                {{ __('public.stat_instructors') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0 text-center md:text-start">
                        <span class="edu-sub-title">{{ $tr('instructors.badge') }}</span>
                        <h1 class="text-3xl sm:text-4xl lg:text-[2.35rem] font-bold text-slate-900 leading-tight mt-1 mb-2">
                            {{ $user->name }}
                        </h1>
                        <p class="text-lg sm:text-xl font-semibold text-[var(--edu-primary)] mb-4">
                            {{ $profile->headline ?? __('public.instructor_fallback') }}
                        </p>

                        @if($profile->bio)
                            <p class="text-slate-600 text-sm sm:text-base leading-8 max-w-2xl mx-auto md:mx-0 mb-5 line-clamp-3">
                                {{ $profile->bio }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-2 justify-center md:justify-start mb-6">
                            @if($coursesCount > 0)
                                <span class="edu-profile-stat-chip">
                                    <i class="fas fa-book-open text-[var(--edu-primary)]"></i>
                                    {{ $coursesCount }} {{ $tr('instructors.courses') }}
                                </span>
                            @endif
                            @if($skillsCount > 0)
                                <span class="edu-profile-stat-chip">
                                    <i class="fas fa-cogs text-[var(--edu-purple)]"></i>
                                    {{ $skillsCount }} {{ __('public.skills') }}
                                </span>
                            @endif
                            @if($experienceCount > 0)
                                <span class="edu-profile-stat-chip">
                                    <i class="fas fa-briefcase text-[var(--edu-accent-dark)]"></i>
                                    {{ $experienceCount }} {{ __('public.experience') }}
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- المحتوى --}}
    <section class="py-10 lg:py-14 bg-white">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">
                    <div class="lg:col-span-2 space-y-6">
                        @if($profile->bio)
                            <article class="edu-card edu-profile-section reveal hover-lift">
                                <div class="edu-profile-section-head">
                                    <span class="edu-profile-section-icon" style="background:var(--edu-primary-light);color:var(--edu-primary)">
                                        <i class="fas fa-user-circle"></i>
                                    </span>
                                    <h2 class="text-xl font-bold text-slate-900">نبذة تعريفية</h2>
                                </div>
                                <div class="text-slate-600 leading-8 text-sm sm:text-base whitespace-pre-line">{{ $profile->bio }}</div>
                            </article>
                        @endif

                        @if($profile->experience)
                            <article class="edu-card edu-profile-section reveal s1 hover-lift">
                                <div class="edu-profile-section-head">
                                    <span class="edu-profile-section-icon" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                                        <i class="fas fa-briefcase"></i>
                                    </span>
                                    <h2 class="text-xl font-bold text-slate-900">{{ __('public.experience') }}</h2>
                                </div>
                                @if(count($experienceItems) > 0)
                                    <div class="space-y-3">
                                        @foreach($experienceItems as $item)
                                            <div class="edu-profile-exp-item">
                                                <i class="fas fa-circle-check text-xs"></i>
                                                <span>{{ $item }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-slate-600 leading-8 whitespace-pre-line text-sm sm:text-base">{{ $profile->experience }}</p>
                                @endif
                            </article>
                        @endif

                        @if($coursesCount > 0)
                            <article class="edu-card edu-profile-section reveal s2 hover-lift">
                                <div class="edu-profile-section-head">
                                    <span class="edu-profile-section-icon" style="background:var(--edu-purple-light);color:var(--edu-purple)">
                                        <i class="fas fa-graduation-cap"></i>
                                    </span>
                                    <h2 class="text-xl font-bold text-slate-900">{{ __('public.instructor_courses') }}</h2>
                                </div>
                                <div class="edu-instructor-courses-grid">
                                    @foreach($courses as $c)
                                        @php $cThumb = $c->thumbnail ? str_replace('\\', '/', $c->thumbnail) : null; @endphp
                                        <a href="{{ route('public.course.show', $c->id) }}" class="edu-instructor-course-link group">
                                            <div class="edu-instructor-course-thumb">
                                                @if($cThumb)
                                                    <img src="{{ public_storage_url($cThumb) }}" alt="{{ $c->title }}" loading="lazy">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-[var(--edu-primary)]/40">
                                                        <i class="fas fa-book-open text-4xl"></i>
                                                    </div>
                                                @endif
                                                @if($c->is_featured ?? false)
                                                    <span class="absolute top-2 start-2 edu-tag edu-tag-orange text-[10px]">{{ __('public.featured_badge') }}</span>
                                                @endif
                                            </div>
                                            <div class="p-4 flex flex-col flex-1">
                                                <h3 class="font-bold text-slate-900 text-sm leading-snug mb-2 group-hover:text-[var(--edu-primary)] transition-colors line-clamp-2">
                                                    {{ $c->title }}
                                                </h3>
                                                <div class="mt-auto flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                                    <x-advanced-course-card-price :course="$c" size="sm" class="!items-start" />
                                                    @if(($c->lessons_count ?? 0) > 0)
                                                        <span class="inline-flex items-center gap-1">
                                                            <i class="fas fa-play-circle"></i>
                                                            {{ $c->lessons_count }} {{ __('public.lesson_single') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </article>
                        @endif
                    </div>

                    <aside class="lg:col-span-1">
                        <div class="edu-profile-sidebar-sticky space-y-5">
                            @if($skillsCount > 0)
                                <div class="edu-card edu-profile-section reveal s1 hover-lift !py-5">
                                    <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-cogs text-[var(--edu-primary)]"></i>
                                        {{ __('public.skills') }}
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($skills as $skill)
                                            <span class="edu-skill-pill">{{ $skill }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="edu-card edu-profile-section reveal s2 hover-lift !py-5">
                                <h3 class="font-bold text-slate-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-chart-simple text-[var(--edu-primary)]"></i>
                                    معلومات سريعة
                                </h3>
                                <ul class="space-y-2.5 text-sm">
                                    <li class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <span class="text-slate-600">{{ $tr('instructors.courses') }}</span>
                                        <span class="font-bold text-[var(--edu-primary)]">{{ $coursesCount }}</span>
                                    </li>
                                    <li class="flex justify-between items-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                                        <span class="text-slate-600">{{ __('public.skills') }}</span>
                                        <span class="font-bold text-[var(--edu-primary)]">{{ $skillsCount }}</span>
                                    </li>
                                    <li class="flex justify-between items-center p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                        <span class="text-slate-600">الحالة</span>
                                        <span class="font-bold text-emerald-600">معتمد</span>
                                    </li>
                                </ul>
                            </div>

                            @if(count($socialLinks) > 0)
                                <div class="edu-card edu-profile-section reveal hover-lift !py-5">
                                    <h3 class="font-bold text-slate-900 mb-3 text-sm">روابط التواصل</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($socialLinks as $net => $url)
                                            @if(is_string($url) && $url !== '')
                                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="edu-profile-social" title="{{ $net }}">
                                                    <i class="fab fa-{{ $net === 'linkedin' ? 'linkedin-in' : $net }}"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('public.courses') }}" class="edu-btn-primary w-full justify-center text-sm">
                                <i class="fas fa-graduation-cap"></i>
                                {{ __('public.browse_courses') }}
                            </a>
                            <a href="{{ route('public.instructors.index') }}" class="edu-btn-outline w-full justify-center text-sm">
                                <i class="fas fa-arrow-right"></i>
                                {{ __('public.all_instructors_link') }}
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-12 bg-[var(--edu-bg)]">
        <div class="edu-container-full">
            <div class="edu-courses-inner reveal">
                <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3">{{ __('public.instructors_cta_title') }}</h2>
                    <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">
                        {{ __('public.instructors_cta_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="edu-btn-white !text-[var(--edu-primary)] hover:!text-[var(--edu-primary-dark)]">
                            {{ __('public.instructors_cta_register') }}
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        <a href="{{ route('public.courses') }}" class="edu-btn-ghost-light">
                            {{ $tr('hero.cta_courses') }}
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

@include('landing.eduvalt.footer')

<script>
(function () {
    var nav = document.getElementById('edu-nav');
    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('is-scrolled', y > 20);
        var bar = document.getElementById('scroll-progress');
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    window.addEventListener('load', function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    });
    setTimeout(function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    }, 2000);
    document.getElementById('edu-mobile-toggle')?.addEventListener('click', function () {
        document.getElementById('edu-mobile-menu')?.classList.toggle('hidden');
    });
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('revealed'); });
    }
})();
</script>
@include('partials.pwa-service-worker')
</body>
</html>

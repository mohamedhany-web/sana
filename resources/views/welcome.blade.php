@php
    $brand = config('app.name');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $photos = [
        'instructor_m' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop&q=80',
    ];
    $tagClasses = ['edu-tag-green', 'edu-tag-orange', 'edu-tag-purple', 'edu-tag-blue'];
    $catThemes = [
        ['icon' => 'fa-laptop-code', 'color' => $bc['blue'], 'bg' => $bc['blue_light']],
        ['icon' => 'fa-brain', 'color' => $bc['purple'], 'bg' => $bc['purple_light']],
        ['icon' => 'fa-code', 'color' => $bc['blue_dark'], 'bg' => $bc['blue_light']],
        ['icon' => 'fa-chart-line', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light']],
        ['icon' => 'fa-palette', 'color' => $bc['purple_dark'], 'bg' => $bc['purple_light']],
        ['icon' => 'fa-bullhorn', 'color' => $bc['blue'], 'bg' => $bc['blue_light']],
        ['icon' => 'fa-language', 'color' => $bc['purple'], 'bg' => $bc['purple_light']],
        ['icon' => 'fa-shield-halved', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light']],
    ];
    $howSteps = [
        ['icon' => 'fa-magnifying-glass', 'num' => '01'],
        ['icon' => 'fa-calendar-plus', 'num' => '02'],
        ['icon' => 'fa-video', 'num' => '03'],
        ['icon' => 'fa-file-lines', 'num' => '04'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar-SA" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $tr('meta_title') }}</title>
    <meta name="description" content="{{ $tr('meta_description') }}">
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:title" content="{{ $tr('meta_title') }}">
    <meta property="og:description" content="{{ $tr('meta_description') }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{edu:{primary:'{{ $bc['blue'] }}',purple:'{{ $bc['purple'] }}',accent:'{{ $bc['yellow'] }}'}}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('partials.rtl-base')
    @include('landing.eduvalt.partials.course-favorites-init')
    @include('landing.partials.home-interactions')
</head>
<body class="antialiased">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

@include('tutor.partials.home-hero')

@include('landing.partials.student-benefits')

{{-- HOW IT WORKS --}}
<section class="py-14 lg:py-20 bg-white" id="how"
         x-data="howSteps({{ count($howSteps) }})" x-init="init()">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ $tr('how.badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ $tr('how.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('how.title_highlight')])</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 reveal-stagger" data-reveal-stagger>
            @foreach($howSteps as $si => $step)
            <article class="relative edu-card how-step-card p-6 pt-8"
                     :class="{ 'is-active': active === {{ $si }} }"
                     @mouseenter="setActive({{ $si }})">
                <span class="absolute -top-3 start-6 px-3 py-1 rounded-full bg-[var(--edu-primary)] text-white text-xs font-black">{{ $step['num'] }}</span>
                <span class="how-step-card__icon w-12 h-12 rounded-xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center text-lg mb-4"><i class="fas {{ $step['icon'] }}"></i></span>
                <h3 class="font-bold text-slate-900 mb-2">{{ $tr('how.s'.($si + 1).'_title') }}</h3>
                <p class="text-sm text-slate-600 leading-7">{{ $tr('how.s'.($si + 1).'_desc') }}</p>
            </article>
            @endforeach
        </div>
        <div class="text-center mt-8 reveal">
            <a href="{{ route('register') }}" class="edu-btn-primary">{{ $tr('hero.cta_book') }} <i class="fas fa-arrow-left text-sm"></i></a>
        </div>
    </div>
</section>

{{-- COURSES --}}
<section class="py-14 lg:py-20 bg-[var(--edu-bg)]" id="courses">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ $tr('courses.badge') }}</span>
            <h2 class="edu-section-title">{{ $tr('courses.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('courses.title_highlight')])</h2>
        </div>
        <div class="flex flex-wrap justify-center gap-2 mb-10 reveal">
            <span class="px-5 py-2 rounded-full text-sm font-bold bg-[var(--edu-primary)] text-white">{{ $tr('courses.tab_all') }}</span>
            @foreach(($courseCategories ?? collect())->take(4) as $cat)
                <a href="{{ $cat['url'] }}" class="px-5 py-2 rounded-full text-sm font-semibold bg-white text-slate-600 border border-slate-200 hover:border-[var(--edu-primary)] hover:text-[var(--edu-primary)] transition-colors">{{ $cat['name'] }}</a>
            @endforeach
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($featuredCourses ?? [] as $idx => $course)
            @php
                $thumb = $course->thumbnail ? asset('storage/'.str_replace('\\','/',$course->thumbnail)) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&auto=format&fit=crop&q=80';
                $inst = $course->instructor->name ?? __('public.instructor_fallback');
                $rating = $course->rating ? number_format((float)$course->rating, 1) : null;
                $contactSupport = $course->usesContactSupportPricing();
                $isFree = ! $contactSupport && (($course->is_free ?? false) || ($course->listPriceAmount() <= 0 && $course->effectivePurchasePrice() <= 0));
                $tagClass = $tagClasses[$idx % count($tagClasses)];
                $catName = $course->courseCategory->name ?? ($course->category ?? $tr('courses.tab_all'));
            @endphp
            @php $isSavedCourse = in_array((int) $course->id, $savedCourseIds ?? [], true); @endphp
            <article class="edu-card overflow-hidden reveal flex flex-col">
                <div class="relative aspect-[16/10] overflow-hidden">
                    <a href="{{ route('public.course.show', $course->id) }}" class="block w-full h-full">
                        <img src="{{ $thumb }}" alt="" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy">
                    </a>
                    <span class="edu-tag {{ $tagClass }} absolute top-3 start-3 z-[11]">{{ $catName }}</span>
                    <x-course-favorite-button :course-id="$course->id" :saved="$isSavedCourse" />
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                        <span><i class="far fa-clock"></i> {{ max(1, (int)($course->lessons_count ?? 1) * 2) }} {{ $tr('courses.lessons') }}</span>
                        @if((int)($course->students_count ?? 0) > 0)
                            <span><i class="fas fa-user-group"></i> {{ number_format((int)$course->students_count) }} {{ $tr('courses.students') }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 leading-snug mb-3 line-clamp-2 flex-1">
                        <a href="{{ route('public.course.show', $course->id) }}" class="hover:text-[var(--edu-primary)]">{{ $course->title }}</a>
                    </h3>
                    <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">{{ mb_substr($inst, 0, 1) }}</span>
                            <span class="text-xs text-slate-500 truncate">{{ $inst }}</span>
                        </div>
                        <div class="text-start shrink-0">
                            @if($rating)<span class="text-amber-500 text-xs font-bold"><i class="fas fa-star"></i> {{ $rating }}</span>@endif
                            <div class="mt-0.5">
                                @if($contactSupport)
                                    <a href="{{ $course->supportWhatsAppUrl() }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#25D366] text-white text-xs font-bold hover:bg-[#1da851]"
                                       onclick="event.stopPropagation();">
                                        <i class="fab fa-whatsapp"></i>{{ __('public.course_contact_support') }}
                                    </a>
                                @elseif($isFree)
                                    <p class="font-extrabold text-[var(--edu-primary)] text-sm">{{ $tr('courses.free') }}</p>
                                @else
                                    <p class="font-extrabold text-[var(--edu-primary)] text-sm">{{ number_format($course->effectivePurchasePrice(), 0) }} {{ __('public.currency') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            @empty
            @foreach([
                ['title' => 'تصميم واجهات المستخدم', 'cat' => 'تصميم', 'tag' => 0],
                ['title' => 'الذكاء الاصطناعي للمعلّمين', 'cat' => 'تقنية', 'tag' => 1],
                ['title' => 'تطوير الويب الحديث', 'cat' => 'برمجة', 'tag' => 2],
                ['title' => 'التسويق الرقمي', 'cat' => 'تسويق', 'tag' => 3],
            ] as $demo)
            <article class="edu-card overflow-hidden reveal">
                <div class="aspect-[16/10] bg-gradient-to-br from-blue-100 to-cyan-50 relative">
                    <span class="edu-tag {{ $tagClasses[$demo['tag']] }} absolute top-3 start-3">{{ $demo['cat'] }}</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-900 mb-3">{{ $demo['title'] }}</h3>
                    <a href="{{ route('public.courses') }}" class="text-sm font-bold text-[var(--edu-primary)]">{{ $tr('courses.enroll') }}</a>
                </div>
            </article>
            @endforeach
            @endforelse
        </div>
        <div class="text-center mt-10 reveal">
            <a href="{{ route('public.courses') }}" class="edu-btn-outline">{{ $tr('courses.view_all') }} <i class="fas fa-arrow-left text-sm"></i></a>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="py-14 lg:py-20 bg-white border-t border-slate-100" id="categories">
    <div class="edu-container">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            <div class="lg:col-span-4 reveal">
                <span class="edu-sub-title">{{ $tr('categories.badge') }}</span>
                <h2 class="edu-section-title mb-6">{{ $tr('categories.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('categories.title_highlight')])</h2>
                <a href="{{ route('public.courses') }}" class="edu-btn-primary text-sm">{{ $tr('categories.view_all') }} <i class="fas fa-arrow-left text-sm"></i></a>
            </div>
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4 reveal">
                @forelse($homeSubjects ?? [] as $sub)
                <a href="{{ $sub['url'] }}" class="edu-cat-card group" style="--cat-accent:{{ $sub['color'] }};--cat-bg:{{ $sub['bg'] }}">
                    <span class="edu-cat-icon">
                        <i class="fas {{ $sub['icon'] }}"></i>
                    </span>
                    <span class="edu-cat-body">
                        <span class="edu-cat-name">{{ $sub['name'] }}</span>
                        <span class="edu-cat-meta">
                            <span class="edu-cat-count">{{ $sub['count'] }} {{ $tr('categories.courses') }}</span>
                            <span class="edu-cat-arrow"><i class="fas fa-arrow-left"></i></span>
                        </span>
                    </span>
                </a>
                @empty
                <div class="sm:col-span-2 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-slate-500 text-sm">
                    لا توجد مواد نشطة بعد — أضفها من لوحة الإدارة: <strong>المواد الدراسية</strong>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
@if(isset($homeTestimonials) && $homeTestimonials->isNotEmpty())
<section class="py-16 lg:py-24 bg-[#F8FAFC] border-t border-slate-100" id="testimonials">
    <div class="edu-container">
        <div class="text-center mb-12 reveal">
            <span class="edu-sub-title">{{ $tr('testimonials.badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ $tr('testimonials.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('testimonials.title_highlight')])</h2>
        </div>
        <div class="overflow-x-auto pb-4 -mx-4 px-4 scrollbar-hide">
            <div class="flex gap-6 min-w-max py-2" id="edu-testimonial-track" dir="rtl">
                @foreach($homeTestimonials->take(6) as $tItem)
                <article class="w-[min(90vw,380px)] shrink-0 edu-card p-8 flex flex-col">
                    <span class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center text-lg mb-5 shrink-0">
                        <i class="fas fa-quote-right"></i>
                    </span>
                    @if($tItem->body)
                        <p class="text-slate-600 leading-8 mb-6 text-sm flex-1">«{{ Str::limit(strip_tags($tItem->body), 200) }}»</p>
                    @endif
                    <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                        @if($tItem->isImageType() && $tItem->publicImageUrl())
                            <img src="{{ $tItem->publicImageUrl() }}" alt="" class="w-14 h-14 rounded-full object-cover ring-2 ring-[var(--edu-primary)]/20">
                        @else
                            <div class="w-14 h-14 rounded-full bg-[var(--edu-primary)] text-white flex items-center justify-center font-bold text-lg shrink-0">{{ $tItem->author_name ? mb_substr($tItem->author_name, 0, 1) : '؟' }}</div>
                        @endif
                        <div class="min-w-0">
                            @if($tItem->author_name)<p class="font-bold text-slate-900">{{ $tItem->author_name }}</p>@endif
                            @if($tItem->role_label)<p class="text-sm text-slate-500">{{ $tItem->role_label }}</p>@endif
                            <div class="flex gap-0.5 text-amber-500 text-xs mt-1">@for($s=0;$s<5;$s++)<i class="fas fa-star"></i>@endfor</div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        <div class="text-center mt-10 reveal">
            <a href="{{ route('public.testimonials') }}" class="edu-btn-outline text-sm">{{ $tr('testimonials.view_all') }} <i class="fas fa-arrow-left text-sm"></i></a>
        </div>
    </div>
</section>
@endif

{{-- INSTRUCTORS --}}
<section class="py-16 lg:py-24 bg-[#F8FAFC]" id="instructors">
    <div class="edu-container">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal">
            <div>
                <span class="edu-sub-title">{{ $tr('instructors.badge') }}</span>
                <h2 class="edu-section-title">{{ $tr('instructors.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('instructors.title_highlight')])</h2>
                <p class="text-slate-500 mt-2">{{ $tr('instructors.subtitle') }}</p>
            </div>
            <a href="{{ route('public.instructors.index') }}" class="edu-btn-outline shrink-0">{{ $tr('instructors.view_all') }}</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse(($homeInstructors ?? collect())->take(4) as $pi => $p)
            @php
                $name = $p->user->name ?? '';
                $headline = $p->headline ?? '';
                $photo = $p->photo_path ? $p->photo_url : $photos['instructor_m'];
                $bgColors = ['#EFF6FF','#F5F3FF','#ECFDF5','#FFF7ED'];
            @endphp
            <article class="edu-card p-6 text-center reveal group">
                <div class="relative mx-auto w-32 h-32 mb-4 rounded-full flex items-end justify-center overflow-visible" style="background:{{ $bgColors[$pi % 4] }}">
                    <img src="{{ $photo }}" alt="{{ $name }}" class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-lg group-hover:scale-105 transition-transform" loading="lazy">
                </div>
                <h3 class="font-bold text-slate-900">{{ $name }}</h3>
                @if($headline)<p class="text-xs text-[var(--edu-primary)] font-medium mt-1 mb-2">{{ Str::limit($headline, 40) }}</p>@endif
                <p class="text-xs text-slate-500 mb-3">{{ (int)($p->courses_count ?? 0) }} {{ $tr('instructors.courses') }}</p>
                @if(is_array($p->social_links) && count($p->social_links))
                <div class="flex justify-center gap-2 mb-4">
                    @foreach(array_slice($p->social_links, 0, 3) as $net => $url)
                        @if(is_string($url) && $url)<a href="{{ $url }}" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-[var(--edu-primary)] hover:text-white text-xs" target="_blank" rel="noopener"><i class="fab fa-{{ $net === 'linkedin' ? 'linkedin-in' : $net }}"></i></a>@endif
                    @endforeach
                </div>
                @endif
                <a href="{{ route('public.instructors.show', $p->user) }}" class="text-sm font-bold text-[var(--edu-primary)]">{{ __('public.view_instructor_profile') }}</a>
            </article>
            @empty
            <p class="col-span-full text-center text-slate-500 py-8">{{ __('public.no_instructors') }}</p>
            @endforelse
        </div>
    </div>
</section>

{{-- CTA BANNER --}}
<section class="py-12">
    <div class="edu-container reveal">
        <div class="edu-cta-wrap px-8 py-10 lg:py-12 flex flex-col lg:flex-row items-center justify-between gap-6 text-white">
            <div class="relative z-10 max-w-xl text-center lg:text-start">
                <h2 class="text-2xl lg:text-3xl font-extrabold mb-2 text-white">{{ $tr('cta_banner.title') }}</h2>
                <p class="text-white/90 leading-7 text-sm lg:text-base">{{ $tr('cta_banner.subtitle') }}</p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 justify-center lg:justify-start shrink-0">
                <a href="{{ route('register') }}" class="edu-btn-white">{{ $tr('hero.cta_book') }} <i class="fas fa-arrow-left text-sm"></i></a>
                <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-bold text-white border-2 border-white/80 hover:bg-white/10 transition-colors">{{ $tr('hero.cta_courses') }}</a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.eduvalt.footer')

@if(isset($popupAd) && $popupAd)
    @include('partials.popup-ad', ['ad' => $popupAd])
@endif

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
    }, 2500);

    document.getElementById('edu-mobile-toggle')?.addEventListener('click', function () {
        document.getElementById('edu-mobile-menu')?.classList.toggle('hidden');
    });

    var revealTargets = document.querySelectorAll('.reveal, .reveal-stagger');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('revealed');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        revealTargets.forEach(function (el) { io.observe(el); });
    } else {
        revealTargets.forEach(function (el) { el.classList.add('revealed'); });
    }

})();
</script>
@include('partials.pwa-service-worker')
</body>
</html>

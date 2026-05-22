@php
    use App\Support\CloudStorage;
    $brand = config('app.name');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hs = $homeStats ?? [];
    $fmt = fn (int $n) => number_format($n, 0, '.', ',');
    $fmtK = function (int $n) {
        if ($n >= 1000) {
            return round($n / 1000, 1).' ألف';
        }
        return (string) $n;
    };
    $photos = [
        'hero' => CloudStorage::pathExistsOnAnyDisk('site/hero-intro.png')
            ? CloudStorage::localPublicStorageUrl('site/hero-intro.png')
            : asset('images/hero-intro.png'),
        'instructor_m' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop&q=80',
        'student_f' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&auto=format&fit=crop&q=80',
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
    $aboutFeatures = [
        ['icon' => 'fa-chalkboard-user', 'color' => $bc['blue'], 'bg' => $bc['blue_light'], 'value' => $fmt((int)($hs['instructors'] ?? 0)).'+', 'label' => $tr('about.f1')],
        ['icon' => 'fa-calendar-check', 'color' => $bc['purple'], 'bg' => $bc['purple_light'], 'value' => $fmt((int)($hs['courses'] ?? 0)).'+', 'label' => $tr('about.f2')],
        ['icon' => 'fa-book-open', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light'], 'value' => $fmt((int)($hs['services'] ?? 0)).'+', 'label' => $tr('about.f3')],
        ['icon' => 'fa-chart-line', 'color' => $bc['blue_dark'], 'bg' => $bc['blue_light'], 'value' => '24/7', 'label' => $tr('about.f4')],
    ];
    $platformFeatures = [
        ['icon' => 'fa-user-check', 'style' => 'background:var(--edu-primary-light);color:var(--edu-primary)', 'key' => 'f1'],
        ['icon' => 'fa-users', 'style' => 'background:var(--edu-purple-light);color:var(--edu-purple)', 'key' => 'f2'],
        ['icon' => 'fa-box-open', 'style' => 'background:var(--edu-accent-light);color:var(--edu-accent-dark)', 'key' => 'f3'],
        ['icon' => 'fa-handshake', 'style' => 'background:var(--edu-primary-light);color:var(--edu-primary-dark)', 'key' => 'f4'],
        ['icon' => 'fa-clipboard-list', 'style' => 'background:var(--edu-purple-light);color:var(--edu-purple-dark)', 'key' => 'f5'],
        ['icon' => 'fa-credit-card', 'style' => 'background:var(--edu-accent-light);color:var(--edu-accent-dark)', 'key' => 'f6'],
    ];
    $howSteps = [
        ['icon' => 'fa-magnifying-glass', 'num' => '01'],
        ['icon' => 'fa-calendar-plus', 'num' => '02'],
        ['icon' => 'fa-video', 'num' => '03'],
        ['icon' => 'fa-file-lines', 'num' => '04'],
    ];
    $supportPhone = config('services.platform.support_phone', '');
    $heroLearners = $fmtK((int)($hs['learners'] ?? 0));
    $heroTeachers = $fmtK((int)($hs['instructors'] ?? 0));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
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
</head>
<body class="antialiased">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">

{{-- HERO (Eduvalt banner — RTL) --}}
<section class="edu-banner-area relative overflow-hidden pb-16 lg:pb-24">
    <div class="absolute top-20 start-0 w-64 h-64 rounded-full bg-sky-200/35 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 end-0 w-80 h-80 rounded-full bg-blue-200/25 blur-3xl pointer-events-none"></div>
    <svg class="absolute top-32 end-[8%] w-24 h-24 text-[var(--edu-primary)]/10 pointer-events-none edu-float" viewBox="0 0 100 100" fill="currentColor"><circle cx="20" cy="20" r="4"/><path d="M10 50 Q50 10 90 50" fill="none" stroke="currentColor" stroke-width="2"/></svg>

    <div class="edu-container relative z-10 pt-10 lg:pt-16">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-8 items-center">
            <div class="w-full lg:w-1/2 text-center lg:text-start reveal">
                <span class="edu-badge mb-5">{{ $tr('hero.badge') }}</span>
                <h1 class="edu-section-title text-slate-900 mb-5">
                    {{ $tr('hero.title') }}
                    @include('landing.eduvalt.partials.title-mark', ['text' => $tr('hero.title_highlight')])
                    <span class="block mt-2 text-slate-900">{{ $tr('hero.title_after') }}</span>
                </h1>
                <p class="text-slate-600 text-base lg:text-lg leading-8 mb-8 max-w-xl mx-auto lg:mx-0">{{ $tr('hero.subtitle') }}</p>
                <div class="edu-hero-actions mb-4">
                    <a href="{{ route('register') }}" class="edu-btn-primary">{{ $tr('hero.cta_book') }} <i class="fas fa-arrow-left text-sm"></i></a>
                    <a href="{{ route('public.pricing') }}" class="edu-btn-outline">{{ $tr('hero.cta_packages') }}</a>
                </div>
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 text-sm">
                    <a href="{{ route('public.courses') }}" class="font-bold text-[var(--edu-primary)] hover:underline">{{ $tr('hero.cta_courses') }}</a>
                    @if($supportPhone)
                    <span class="text-slate-300">|</span>
                    <div class="edu-hero-phone !p-0 !border-0 !bg-transparent !shadow-none">
                        <i class="fas fa-phone-volume text-[var(--edu-primary)]"></i>
                        <div class="text-start">
                            <span class="block text-xs text-slate-500">{{ $tr('hero.phone_question') }}</span>
                            <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="font-bold text-slate-900 text-sm hover:text-[var(--edu-primary)]" dir="ltr">{{ $supportPhone }}</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="relative w-full lg:w-1/2 reveal">
                <div class="edu-hero-photo-wrap relative mx-auto w-full pb-8 lg:pb-0">
                    <div class="absolute -inset-3 lg:-inset-4 rounded-[1.25rem] lg:rounded-[2rem] bg-gradient-to-br from-[var(--edu-primary)]/12 via-sky-100/40 to-violet-100/30 blur-sm pointer-events-none" aria-hidden="true"></div>
                    <img src="{{ $photos['hero'] }}" alt="طلاب يتعلّمون في مكتبة — {{ $brand }}" class="edu-hero-photo relative z-10 w-full h-auto rounded-[1.25rem] lg:rounded-[2rem] shadow-2xl" loading="eager" decoding="async"
                         onerror="this.onerror=null;this.src='{{ asset('images/brainstorm-meeting.jpg') }}';">
                    <div class="edu-banner-facts">
                        <div class="edu-banner-fact edu-float">
                            <span class="icon"><i class="fas fa-user-graduate"></i></span>
                            <div>
                                <p class="count">{{ $heroLearners }}</p>
                                <p class="label">{{ $tr('hero.float_students') }}</p>
                            </div>
                        </div>
                        <div class="edu-banner-fact edu-float-delay">
                            <span class="icon"><i class="fas fa-chalkboard-user"></i></span>
                            <div>
                                <p class="count">{{ $heroTeachers }}</p>
                                <p class="label">{{ $tr('hero.float_teachers') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="edu-glass absolute top-8 -end-3 z-20 px-3 py-2 flex items-center gap-2 text-xs font-bold text-[var(--edu-primary)]">
                        <i class="fas fa-video"></i> {{ $tr('hero.float_sessions') }}
                    </div>
                    <div class="absolute top-1/4 start-0 w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg edu-float text-lg z-20" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)"><i class="fas fa-book-open"></i></div>
                    <div class="absolute bottom-1/3 end-0 w-10 h-10 rounded-xl flex items-center justify-center shadow edu-float-delay z-20" style="background:var(--edu-purple-light);color:var(--edu-purple)"><i class="fas fa-atom"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VALUE STRIP --}}
<section class="py-10 border-y border-slate-100 bg-white">
    <div class="edu-container">
        <p class="text-center text-sm text-slate-500 font-bold mb-6">{{ $tr('trusted.title') }}</p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['icon' => 'fa-user', 'text' => $tr('trusted.v1')],
                ['icon' => 'fa-users', 'text' => $tr('trusted.v2')],
                ['icon' => 'fa-clipboard-check', 'text' => $tr('trusted.v3')],
                ['icon' => 'fa-wallet', 'text' => $tr('trusted.v4')],
            ] as $tv)
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                <span class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center shrink-0"><i class="fas {{ $tv['icon'] }}"></i></span>
                <span class="text-sm font-bold text-slate-800 leading-snug">{{ $tv['text'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- AUDIENCES --}}
<section class="py-16 lg:py-20 bg-[#F8FAFC]" id="for-whom">
    <div class="edu-container">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ $tr('audiences.badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ $tr('audiences.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('audiences.title_highlight')])</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6 reveal">
            @foreach([
                ['icon' => 'fa-user-graduate', 'color' => $bc['blue'], 'bg' => $bc['blue_light'], 'title' => $tr('audiences.student_title'), 'desc' => $tr('audiences.student_desc')],
                ['icon' => 'fa-people-roof', 'color' => $bc['purple'], 'bg' => $bc['purple_light'], 'title' => $tr('audiences.parent_title'), 'desc' => $tr('audiences.parent_desc')],
                ['icon' => 'fa-chalkboard-user', 'color' => $bc['yellow_dark'], 'bg' => $bc['yellow_light'], 'title' => $tr('audiences.teacher_title'), 'desc' => $tr('audiences.teacher_desc')],
            ] as $aud)
            <article class="edu-card p-8 text-center">
                <span class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center text-2xl mb-5" style="background:{{ $aud['bg'] }};color:{{ $aud['color'] }}"><i class="fas {{ $aud['icon'] }}"></i></span>
                <h3 class="font-extrabold text-lg text-slate-900 mb-3">{{ $aud['title'] }}</h3>
                <p class="text-sm text-slate-600 leading-7">{{ $aud['desc'] }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ABOUT --}}
<section class="py-16 lg:py-24 bg-white" id="about">
    <div class="edu-container">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <div class="w-full lg:w-1/2 reveal">
                <span class="edu-sub-title">{{ $tr('about.badge') }}</span>
                <h2 class="edu-section-title mb-4">{{ $tr('about.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('about.title_highlight')])</h2>
                <p class="text-slate-600 leading-8 mb-8">{{ $tr('about.subtitle') }}</p>
                <div class="grid grid-cols-2 gap-4 mb-8">
                    @foreach($aboutFeatures as $af)
                    <div class="flex items-center gap-3 p-4 rounded-2xl border border-slate-100 bg-slate-50/50">
                        <span class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $af['bg'] }};color:{{ $af['color'] }}"><i class="fas {{ $af['icon'] }}"></i></span>
                        <div>
                            <p class="font-extrabold text-slate-800">{{ $af['value'] }}</p>
                            <p class="text-xs text-slate-500">{{ $af['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('public.about') }}" class="edu-btn-primary">{{ $tr('about.explore') }} <i class="fas fa-arrow-left text-sm"></i></a>
            </div>
            <div class="relative w-full lg:w-1/2 reveal">
                <div class="relative max-w-md mx-auto">
                    <img src="{{ $photos['instructor_m'] }}" alt="" class="w-[85%] rounded-[1.5rem] shadow-xl object-cover aspect-[4/5]" loading="lazy">
                    <img src="{{ $photos['student_f'] }}" alt="" class="absolute -bottom-6 -start-6 w-[55%] rounded-2xl shadow-2xl border-4 border-white object-cover aspect-square" loading="lazy">
                    <div class="edu-about-exp">
                        <p class="year">{{ $tr('about.years') }}</p>
                        <p>{{ $tr('about.years_label') }}</p>
                    </div>
                    <div class="absolute top-4 -end-4 w-14 h-14 rounded-2xl text-white flex items-center justify-center shadow-lg text-xl" style="background:var(--edu-primary)"><i class="fas fa-play"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="py-16 lg:py-24 bg-white border-t border-slate-100" id="how">
    <div class="edu-container">
        <div class="text-center mb-12 reveal">
            <span class="edu-sub-title">{{ $tr('how.badge') }}</span>
            <h2 class="edu-section-title text-slate-900">{{ $tr('how.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('how.title_highlight')])</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal">
            @foreach($howSteps as $si => $step)
            <article class="relative edu-card p-6 pt-8">
                <span class="absolute -top-3 start-6 px-3 py-1 rounded-full bg-[var(--edu-primary)] text-white text-xs font-black">{{ $step['num'] }}</span>
                <span class="w-12 h-12 rounded-xl bg-[var(--edu-primary-light)] text-[var(--edu-primary)] flex items-center justify-center text-lg mb-4"><i class="fas {{ $step['icon'] }}"></i></span>
                <h3 class="font-bold text-slate-900 mb-2">{{ $tr('how.s'.($si + 1).'_title') }}</h3>
                <p class="text-sm text-slate-600 leading-7">{{ $tr('how.s'.($si + 1).'_desc') }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- COURSES --}}
<section class="py-16 lg:py-24 bg-[var(--edu-bg)] edu-courses-wrap" id="courses">
    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='30' fill='%231D4EDB' fill-opacity='.15'/%3E%3C/svg%3E" alt="" class="edu-course-shape s1">
    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M20 80 Q50 10 80 80' fill='none' stroke='%236A2CFF' stroke-width='4' stroke-opacity='.2'/%3E%3C/svg%3E" alt="" class="edu-course-shape s2">
    <div class="edu-container relative z-10">
        <div class="text-center mb-10 reveal">
            <span class="edu-sub-title">{{ $tr('courses.badge') }}</span>
            <h2 class="edu-section-title">{{ $tr('courses.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('courses.title_highlight')])</h2>
            <p class="text-slate-500 mt-3 max-w-2xl mx-auto">{{ $tr('courses.subtitle') }}</p>
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
<section class="py-16 lg:py-24 relative overflow-hidden" id="categories" style="background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)">
    <div class="absolute -top-24 -start-24 w-72 h-72 rounded-full opacity-40 pointer-events-none" style="background:radial-gradient(circle,var(--edu-primary-light),transparent 70%)"></div>
    <div class="absolute -bottom-16 -end-16 w-96 h-96 rounded-full opacity-30 pointer-events-none" style="background:radial-gradient(circle,#dbeafe,transparent 70%)"></div>
    <div class="edu-container relative z-10">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            <div class="lg:col-span-4 reveal">
                <span class="edu-sub-title">{{ $tr('categories.badge') }}</span>
                <h2 class="edu-section-title mb-3">{{ $tr('categories.title') }} @include('landing.eduvalt.partials.title-mark', ['text' => $tr('categories.title_highlight')])</h2>
                <p class="text-slate-500 leading-7 mb-6">{{ $tr('categories.subtitle') }}</p>
                @if(($courseCategories ?? collect())->isNotEmpty())
                    <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-sm font-bold text-slate-700 mb-6 shadow-sm">
                        <i class="fas fa-layer-group text-[var(--edu-primary)]"></i>
                        {{ ($courseCategories ?? collect())->count() }} {{ $tr('categories.badge') }}
                    </p>
                @endif
                <a href="{{ route('public.courses') }}" class="edu-btn-primary text-sm">{{ $tr('categories.view_all') }} <i class="fas fa-arrow-left text-sm"></i></a>
            </div>
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4 reveal">
                @forelse($courseCategories ?? [] as $ci => $cat)
                @php $theme = $catThemes[$ci % count($catThemes)]; @endphp
                <a href="{{ $cat['url'] }}" class="edu-cat-card group" style="--cat-accent:{{ $theme['color'] }};--cat-bg:{{ $theme['bg'] }}">
                    <span class="edu-cat-icon">
                        <i class="fas {{ $theme['icon'] }}"></i>
                    </span>
                    <span class="edu-cat-body">
                        <span class="edu-cat-name">{{ $cat['name'] }}</span>
                        <span class="edu-cat-meta">
                            <span class="edu-cat-count">{{ $cat['count'] }} {{ $tr('categories.courses') }}</span>
                            <span class="edu-cat-arrow"><i class="fas fa-arrow-left"></i></span>
                        </span>
                    </span>
                </a>
                @empty
                @foreach(['تطوير الويب', 'تصميم', 'أعمال', 'تسويق', 'لغات', 'تقنية'] as $ci => $name)
                @php $theme = $catThemes[$ci % count($catThemes)]; @endphp
                <a href="{{ route('public.courses') }}" class="edu-cat-card group" style="--cat-accent:{{ $theme['color'] }};--cat-bg:{{ $theme['bg'] }}">
                    <span class="edu-cat-icon"><i class="fas {{ $theme['icon'] }}"></i></span>
                    <span class="edu-cat-body">
                        <span class="edu-cat-name">{{ $name }}</span>
                        <span class="edu-cat-meta">
                            <span class="edu-cat-count">—</span>
                            <span class="edu-cat-arrow"><i class="fas fa-arrow-left"></i></span>
                        </span>
                    </span>
                </a>
                @endforeach
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
        <div class="edu-cta-wrap px-8 py-12 lg:py-14 flex flex-col lg:flex-row items-center justify-between gap-8 text-white">
            <div class="edu-cta-object w-48 h-48 -top-12 -end-8"></div>
            <div class="edu-cta-object w-28 h-28 bottom-6 start-10"></div>
            <div class="relative z-10 max-w-xl text-center lg:text-start">
                <h2 class="text-2xl lg:text-3xl font-extrabold mb-2 text-white">
                    {{ $tr('cta_banner.title') }}
                </h2>
                <p class="text-white/90 leading-7">{{ $tr('cta_banner.subtitle') }}</p>
            </div>
            <div class="relative z-10 flex flex-wrap gap-3 justify-center lg:justify-start shrink-0">
                <a href="{{ route('register') }}" class="edu-btn-white">{{ $tr('cta_banner.btn_parent') }} <i class="fas fa-arrow-left text-sm"></i></a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-bold text-white border-2 border-white/80 hover:bg-white/10 transition-colors">{{ $tr('cta_banner.btn_teacher') }}</a>
            </div>
        </div>
    </div>
</section>

{{-- PLATFORM + NEWSLETTER --}}
<section class="py-16 bg-[#F8FAFC] border-t border-slate-100" id="platform">
    <div class="edu-container">
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-16 reveal">
            <div>
                <span class="edu-badge mb-3">{{ $tr('platform.badge') }}</span>
                <h2 class="edu-section-title mb-4">
                    {{ $tr('platform.title') }}
                    @include('landing.eduvalt.partials.title-mark', ['text' => $tr('platform.title_highlight')])
                    @if($tr('platform.title_after'))<span class="block">{{ $tr('platform.title_after') }}</span>@endif
                </h2>
                <p class="text-slate-600 leading-8 mb-8">{{ $tr('platform.subtitle') }}</p>
                <div class="grid sm:grid-cols-2 gap-4 mb-8">
                    @foreach($platformFeatures as $pf)
                    <div class="edu-card p-4 flex gap-3 items-start">
                        <span class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-lg" style="{{ $pf['style'] }}"><i class="fas {{ $pf['icon'] }}"></i></span>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $tr('platform.'.$pf['key'].'_title') }}</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-6">{{ $tr('platform.'.$pf['key'].'_desc') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="edu-btn-primary">{{ $tr('platform.cta_book') }} <i class="fas fa-arrow-left text-sm"></i></a>
                    <a href="{{ route('public.pricing') }}" class="edu-btn-outline">{{ $tr('platform.cta_services') }}</a>
                </div>
            </div>
            <div class="flex justify-center lg:justify-end reveal">
                <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden edu-float">
                    <div class="flex items-center gap-2 px-4 py-3 bg-slate-100 border-b border-slate-200">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="flex-1 mx-2 h-7 rounded-lg bg-white border border-slate-200 text-xs text-slate-400 flex items-center justify-center truncate">{{ parse_url(url('/'), PHP_URL_HOST) ?: $brand }}</span>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs text-slate-500">مرحباً بك في</p>
                                <p class="font-extrabold text-slate-900">{{ $brand }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-[var(--edu-primary)] text-xs font-bold">لوحة المتعلم</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-xl bg-blue-50 p-3 text-center">
                                <i class="fas fa-calendar-check text-[var(--edu-primary)] mb-1"></i>
                                <p class="text-[10px] font-bold text-slate-700">حجوزاتي</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-3 text-center">
                                <i class="fas fa-file-lines text-emerald-600 mb-1"></i>
                                <p class="text-[10px] font-bold text-slate-700">تقارير</p>
                            </div>
                            <div class="rounded-xl bg-violet-50 p-3 text-center">
                                <i class="fas fa-chart-line text-violet-600 mb-1"></i>
                                <p class="text-[10px] font-bold text-slate-700">التقدّم</p>
                            </div>
                        </div>
                        <div class="rounded-xl border border-slate-100 p-3 space-y-2">
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full w-3/4 rounded-full" style="background:var(--edu-primary)"></div></div>
                            <p class="text-xs text-slate-500">متابعة الدورة الحالية — 75%</p>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1 h-14 rounded-xl bg-slate-50 border border-dashed border-slate-200"></div>
                            <div class="flex-1 h-14 rounded-xl bg-indigo-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="edu-newsletter-wrap p-8 lg:p-12 reveal">
            <div class="flex flex-col lg:flex-row gap-10 items-center">
                <div class="w-full lg:w-1/2 text-center lg:text-start">
                    <h3 class="edu-section-title text-slate-900 mb-2">
                        {{ $tr('newsletter.title') }}
                        @include('landing.eduvalt.partials.title-mark', ['text' => $tr('newsletter.title_highlight')])
                        <span class="block text-slate-900">{{ $tr('newsletter.title_after') }}</span>
                    </h3>
                    <p class="text-slate-500">{{ $tr('newsletter.subtitle') }}</p>
                </div>
                <form class="w-full lg:w-1/2 flex flex-col sm:flex-row gap-3" onsubmit="return false;">
                    <input type="email" class="flex-1 px-5 py-3.5 rounded-full border border-slate-200 focus:border-[var(--edu-primary)] focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="{{ $tr('newsletter.placeholder') }}">
                    <button type="button" class="edu-btn-primary shrink-0">{{ $tr('newsletter.btn') }}</button>
                </form>
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

    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('revealed'); });
    }

})();
</script>
@include('partials.pwa-service-worker')
</body>
</html>

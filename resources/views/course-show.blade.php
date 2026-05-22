@php
    $brand = config('app.name', 'Sana');
    $locale = 'ar';
    $isRtl = true;
    $thumbUrl = ($course->thumbnail ?? null) ? asset('storage/' . str_replace('\\', '/', $course->thumbnail)) : null;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    if (!$introEmbedUrl && $introVideoUrl !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
    }
    $introDirectVideo = null;
    if (!$introEmbedUrl && $introVideoUrl !== '' && filter_var($introVideoUrl, FILTER_VALIDATE_URL)) {
        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $introVideoUrl)) {
            $introDirectVideo = $introVideoUrl;
        }
    }
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $courseOgImg = $thumbUrl ?? asset('images/og-image.jpg');
    $courseDesc = Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')) . ' | ' . $brand;
    $courseUrl = url('/course/' . ($course->id ?? ''));
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ $courseTitle }}</title>
    <meta name="title" content="{{ $courseTitle }}">
    <meta name="description" content="{{ $courseDesc }}">
    <meta name="keywords" content="{{ $course->title ?? 'كورس' }}, تعلم أونلاين, كورسات عربية, {{ $brand }}, {{ $categoryDisplay }}">
    <meta name="author" content="{{ ($course->instructor->name ?? null) ?? $brand }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    <link rel="canonical" href="{{ $courseUrl }}">
    <link rel="alternate" hreflang="ar" href="{{ $courseUrl }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $courseUrl }}">
    <meta property="og:title" content="{{ $courseTitle }}">
    <meta property="og:description" content="{{ $courseDesc }}">
    <meta property="og:image" content="{{ $courseOgImg }}">
    <meta property="og:image:alt" content="{{ $course->title ?? 'كورس' }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $courseUrl }}">
    <meta name="twitter:title" content="{{ $courseTitle }}">
    <meta name="twitter:description" content="{{ $courseDesc }}">
    <meta name="twitter:image" content="{{ $courseOgImg }}">
    <meta name="twitter:image:alt" content="{{ $course->title ?? 'كورس' }}">
    @include('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course])
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.course-page')
    @include('partials.rtl-base')
    @php
        $savedCourseIds = \App\Support\PublicCourseCatalog::savedCourseIdsFor(auth()->user());
    @endphp
    @include('landing.eduvalt.partials.course-favorites-init')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

@include('landing.eduvalt.navbar')

<main>
    @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $type => $color)
        @if(session($type))
        <div class="edu-container-full pt-28 pb-2" x-data="{ s: true }" x-show="s" @if($type !== 'error') x-init="setTimeout(() => s = false, 6000)" @endif>
            <div class="edu-courses-inner">
                <div class="rounded-2xl border border-{{ $color }}-200 bg-{{ $color }}-50 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-{{ $type === 'success' ? 'check-circle' : ($type === 'info' ? 'info-circle' : 'exclamation-circle') }} text-{{ $color }}-600"></i>
                    <p class="text-{{ $color }}-800 font-semibold flex-1">{{ session($type) }}</p>
                    <button type="button" @click="s = false" class="text-{{ $color }}-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    {{-- Hero --}}
    <section class="edu-course-hero pt-28 sm:pt-32 pb-10 sm:pb-14">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <nav class="reveal text-sm text-slate-500 mb-6 flex items-center gap-2 flex-wrap">
                    <a href="{{ url('/') }}" class="hover:text-[var(--edu-primary)] transition-colors">{{ __('public.home') }}</a>
                    <i class="fas fa-chevron-left text-[8px] text-slate-400"></i>
                    <a href="{{ route('public.courses') }}" class="hover:text-[var(--edu-primary)] transition-colors">{{ __('public.courses') }}</a>
                    <i class="fas fa-chevron-left text-[8px] text-slate-400"></i>
                    <span class="text-[var(--edu-primary)] font-semibold">{{ Str::limit($course->title ?? '', 48) }}</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10 items-start">
                    <div class="lg:col-span-3 reveal">
                        @if($course->is_featured ?? false)
                            <span class="edu-badge mb-4" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                                <i class="fas fa-star"></i>
                                {{ __('public.featured_course_badge') }}
                            </span>
                        @endif

                        <h1 class="text-3xl sm:text-4xl lg:text-[2.35rem] font-bold text-slate-900 leading-tight mb-4">
                            {{ $course->title ?? __('public.course_title_fallback') }}
                        </h1>

                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed mb-6 max-w-2xl line-clamp-3">
                            {{ $course->description ?? __('public.course_desc_fallback') }}
                        </p>

                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="edu-course-stat"><i class="fas fa-chalkboard-teacher text-[var(--edu-primary)]"></i> {{ ($course->lessons_count ?? 0) }} {{ __('public.lecture_single') }}</span>
                            <span class="edu-course-stat"><i class="fas fa-clock text-[var(--edu-primary)]"></i> {{ ($course->duration_hours ?? 0) }} {{ __('public.hours') }}</span>
                            <span class="edu-course-stat"><i class="fas fa-folder-open text-[var(--edu-primary)]"></i> {{ $categoryDisplay }}</span>
                        </div>

                        @if($course->instructor)
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-[var(--edu-primary)]"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500">{{ __('public.instructor_label') }}</p>
                                    @if(\App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists())
                                        <a href="{{ route('public.instructors.show', $course->instructor) }}" class="font-bold text-slate-900 hover:text-[var(--edu-primary)] transition-colors">{{ $course->instructor->name }}</a>
                                    @else
                                        <span class="font-bold text-slate-900">{{ $course->instructor->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-3">
                            @include('landing.eduvalt.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled ?? false])
                            <a href="{{ route('public.courses') }}" class="edu-btn-outline">
                                <i class="fas fa-arrow-right text-sm"></i>
                                {{ __('public.all_courses') }}
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-2 reveal">
                        @if($introEmbedUrl)
                            <div class="edu-course-media">
                                <div class="aspect-video w-full bg-slate-900">
                                    <iframe src="{{ $introEmbedUrl }}" title="{{ __('public.course_intro_video') }}"
                                        class="w-full h-full min-h-[220px]"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                        allowfullscreen loading="lazy"></iframe>
                                </div>
                            </div>
                        @elseif($introDirectVideo)
                            <div class="edu-course-media">
                                <div class="aspect-video w-full bg-black">
                                    <video src="{{ $introDirectVideo }}" controls playsinline preload="metadata" class="w-full h-full object-contain">
                                        {{ __('public.course_intro_video_unsupported') }}
                                    </video>
                                </div>
                            </div>
                        @elseif($thumbUrl)
                            <div class="edu-course-media aspect-video relative">
                                <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                <x-course-favorite-button :course-id="$course->id" :saved="in_array((int) $course->id, $savedCourseIds, true)" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Details --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <div class="reveal grid grid-cols-2 sm:grid-cols-4 gap-4 mb-12">
                    @php
                    $infoCards = [
                        ['icon' => 'fa-clock', 'label' => __('public.duration'), 'value' => ($course->duration_hours ?? 0) . ' ' . __('public.hours')],
                        ['icon' => 'fa-chalkboard-teacher', 'label' => __('public.lectures_count_label'), 'value' => ($course->lessons_count ?? 0) . ' ' . __('public.lecture_single')],
                        ['icon' => 'fa-folder-open', 'label' => __('public.course_category_label'), 'value' => $categoryDisplay],
                        ['icon' => 'fa-book', 'label' => __('public.subject_label'), 'value' => $course->academicSubject->name ?? __('public.course_category_not_set')],
                    ];
                    @endphp
                    @foreach($infoCards as $ic)
                        <div class="edu-card text-center !p-5">
                            <div class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center mx-auto mb-2">
                                <i class="fas {{ $ic['icon'] }} text-[var(--edu-primary)]"></i>
                            </div>
                            <p class="text-xl font-bold text-slate-900 mb-0.5">{{ $ic['value'] }}</p>
                            <p class="text-xs text-slate-500">{{ $ic['label'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="reveal edu-card !p-6 sm:!p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center">
                                    <i class="fas fa-info-circle text-[var(--edu-primary)]"></i>
                                </div>
                                <h2 class="text-xl font-bold text-slate-900">{{ __('public.about_course') }}</h2>
                            </div>
                            <div class="text-slate-600 leading-relaxed text-base">
                                <p>{{ $course->description ?? __('public.course_desc_fallback') }}</p>
                                @if($course->objectives)
                                    <div class="mt-6">
                                        <h3 class="text-lg font-bold text-slate-900 mb-3">{{ __('public.course_objectives') }}</h3>
                                        <div class="rounded-2xl p-5 bg-[var(--edu-primary-light)]/50 border border-[var(--edu-primary)]/15">
                                            <p class="whitespace-pre-line text-slate-700">{{ $course->objectives }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($course->what_you_learn)
                            <div class="reveal stagger-1 edu-card !p-6 sm:!p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-emerald-600"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-slate-900">{{ __('public.what_you_learn') }}</h2>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach(array_filter(explode("\n", $course->what_you_learn)) as $point)
                                        <div class="edu-learn-item">
                                            <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                            <span>{{ trim($point) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($course->requirements)
                            <div class="reveal stagger-2 edu-card !p-6 sm:!p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <i class="fas fa-list-check text-amber-600"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-slate-900">{{ __('public.requirements') }}</h2>
                                </div>
                                <div class="rounded-2xl p-5 bg-amber-50/40 border border-amber-100">
                                    <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $course->requirements }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="lg:col-span-1">
                        <div class="edu-sidebar-sticky space-y-5">
                            <div class="reveal edu-card !p-0 overflow-hidden">
                                <div class="edu-price-card-head">
                                    @if($course->usesContactSupportPricing())
                                        <div class="text-xl font-bold flex items-center justify-center gap-2">
                                            <i class="fab fa-whatsapp text-[#25D366]"></i>
                                            {{ __('public.course_contact_support_short') }}
                                        </div>
                                        <p class="text-sm text-white/80 text-center mt-2">{{ __('public.course_contact_support_hint') }}</p>
                                    @elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false))
                                        @if($course->hasPromotionalPrice())
                                            <div class="text-sm text-white/80 line-through mb-1 tabular-nums">{{ number_format($course->listPriceAmount(), 0) }} {{ __('public.currency') }}</div>
                                            <div class="text-3xl font-bold tabular-nums">{{ number_format($course->effectivePurchasePrice(), 0) }} <span class="text-lg font-medium text-white/80">{{ __('public.currency') }}</span></div>
                                        @else
                                            <div class="text-3xl font-bold tabular-nums">{{ number_format($course->effectivePurchasePrice(), 0) }} <span class="text-lg font-medium text-white/80">{{ __('public.currency') }}</span></div>
                                        @endif
                                    @else
                                        <div class="text-2xl font-bold flex items-center justify-center gap-2">
                                            <i class="fas fa-gift"></i>{{ __('public.free_price') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <dl class="space-y-2 mb-5 text-sm">
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-clock text-[var(--edu-primary)] ml-1"></i> {{ __('public.duration') }}</span>
                                            <span class="font-bold text-slate-900">{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-chalkboard-teacher text-[var(--edu-primary)] ml-1"></i> {{ __('public.lectures_count_label') }}</span>
                                            <span class="font-bold text-slate-900">{{ $course->lessons_count ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-folder-open text-[var(--edu-primary)] ml-1"></i> {{ __('public.course_category_label') }}</span>
                                            <span class="font-bold text-slate-900">{{ $categoryDisplay }}</span>
                                        </div>
                                    </dl>
                                    @include('landing.eduvalt.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled ?? false, 'block' => true])
                                </div>
                            </div>

                            @if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
                                <div class="reveal edu-card !p-5">
                                    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-bookmark text-[var(--edu-primary)]"></i>
                                        كورسات ذات صلة
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($relatedCourses->take(3) as $related)
                                            @php
                                                $relThumb = $related->thumbnail ? str_replace('\\', '/', $related->thumbnail) : null;
                                                $relImg = $relThumb && \Illuminate\Support\Facades\Storage::disk('public')->exists($relThumb)
                                                    ? asset('storage/' . $relThumb)
                                                    : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=200&h=200&fit=crop';
                                            @endphp
                                            <div class="flex gap-3 p-3 rounded-xl border border-slate-100 hover:border-[var(--edu-primary)]/30 hover:shadow-md transition-all group">
                                                <div class="edu-related-thumb relative shrink-0">
                                                    <a href="{{ route('public.course.show', $related->id) }}" class="block w-full h-full">
                                                        <img src="{{ $relImg }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                                    </a>
                                                    <x-course-favorite-button
                                                        :course-id="$related->id"
                                                        :saved="in_array((int) $related->id, $savedCourseIds, true)"
                                                    />
                                                </div>
                                                <a href="{{ route('public.course.show', $related->id) }}" class="flex-1 min-w-0">
                                                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)] transition-colors line-clamp-2">{{ $related->title }}</h4>
                                                    <div class="mt-1">
                                                        <x-advanced-course-card-price :course="$related" size="sm" class="!items-start" />
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-12 md:py-16 bg-[var(--edu-bg)]">
        <div class="edu-container-full">
            <div class="edu-courses-inner reveal">
                <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3">
                        جاهز للانطلاق في هذا الكورس؟
                    </h2>
                    <p class="text-white/85 text-base sm:text-lg mb-8 max-w-xl mx-auto">
                        سجّل الآن وابدأ التعلم بخطوات واضحة وتجربة احترافية متكاملة.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ route('my-courses.show', $course) }}" class="edu-btn-white">
                                {{ __('public.start_learning_now') }}
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="edu-btn-white">
                                {{ __('public.register_free_now') }}
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        @endauth
                        <a href="{{ route('public.courses') }}" class="edu-btn-ghost-light">
                            {{ __('public.all_courses') }}
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

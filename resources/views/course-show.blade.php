@php
    use App\Support\PublicCourseCatalog;

    $brand = config('app.name', 'Sana');
    $categoryDisplay = $course->courseCategory?->name ?? ($course->academicSubject?->name ?? __('public.course_category_not_set'));
    $thumbUrl = ($course->thumbnail ?? null) ? public_storage_url($course->thumbnail) : \App\Support\PublicCourseCatalog::defaultCardImage();
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
    $hasPreview = $introEmbedUrl || $introDirectVideo;
    $courseDesc = Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')) . ' | ' . $brand;
    $courseUrl = url('/course/' . $course->id);
    $rating = ((int) ($course->reviews_count ?? 0) > 0 && $course->rating)
        ? number_format((float) $course->rating, 1)
        : null;
    $languageLabel = match (strtolower((string) ($course->language ?? 'ar'))) {
        'en', 'english' => __('public.language_english'),
        'ar', 'arabic', '' => __('public.language_arabic'),
        default => $course->language,
    };
    $learnPoints = array_filter(array_map('trim', explode("\n", (string) ($course->what_you_learn ?? ''))));
    if (empty($learnPoints) && !empty($course->skills) && is_array($course->skills)) {
        $learnPoints = $course->skills;
    }
    $objectives = trim((string) ($course->objectives ?? ''));
    $requirements = trim((string) ($course->requirements ?? ''));
    $audience = trim((string) ($course->prerequisites ?? ''));
    $totalReviews = (int) ($course->reviews_count ?? $reviews->count());
    $avgRating = $rating;
    $canEnrollPublicly = $canEnrollPublicly ?? PublicCourseCatalog::isPubliclyVisible($course);
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
    <title>{{ $courseTitle }}</title>
    <meta name="description" content="{{ $courseDesc }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ $courseUrl }}">
    <meta property="og:title" content="{{ $courseTitle }}">
    <meta property="og:description" content="{{ $courseDesc }}">
    <meta property="og:image" content="{{ $thumbUrl }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.course-show-theme')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/course-favorites.js') }}"></script>
</head>
<body class="sana-home sana-courses-page sana-course-detail-page">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-course-page">

    @foreach(['success' => 'success', 'info' => 'info', 'error' => 'error'] as $type => $style)
        @if(session($type))
            <div class="sana-container" style="padding-top:16px">
                <div class="sana-cd-alert sana-cd-alert--{{ $style }}">{{ session($type) }}</div>
            </div>
        @endif
    @endforeach

    {{-- HERO --}}
    <section class="sana-cd-hero">
        <div class="sana-container sana-cd-hero__inner">
            <nav class="sana-cd-breadcrumb" aria-label="{{ __('public.breadcrumb_aria') }}">
                <a href="{{ route('home') }}">{{ __('public.home') }}</a>
                <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                @if($hasPublishedCourses ?? PublicCourseCatalog::hasPublicCourses())
                    <a href="{{ route('public.courses') }}">{{ __('public.courses') }}</a>
                    <i class="fas fa-chevron-left" style="font-size:0.55rem;opacity:0.5"></i>
                @endif
                <span>{{ Str::limit($course->title, 40) }}</span>
            </nav>

            <div class="sana-cd-hero__grid">
                <div>
                    <div class="sana-cd-hero__badges">
                        @if($course->is_featured)<span class="sana-cd-pill sana-cd-pill--gold"><i class="fas fa-star"></i> {{ __('public.featured_badge') }}</span>@endif
                        <span class="sana-cd-pill">{{ $categoryDisplay }}</span>
                        <span class="sana-cd-pill">{{ $levelLabel }}</span>
                    </div>

                    <h1 class="sana-cd-hero__title">{{ $course->title }}</h1>
                    <p class="sana-cd-hero__desc">{{ Str::limit(strip_tags($course->description ?? ''), 220) }}</p>

                    <div class="sana-cd-hero__meta">
                        @if($rating && $totalReviews > 0)
                        <span class="stars"><i class="fas fa-star"></i> {{ $rating }} ({{ number_format($totalReviews) }} {{ __('public.stat_rating') }})</span>
                        @endif
                        @if((int) ($course->students_count ?? 0) > 0)
                        <span><i class="fas fa-users"></i> {{ number_format($course->students_count) }} {{ __('public.stat_students') }}</span>
                        @endif
                        @if((int) ($course->duration_hours ?? 0) > 0)
                        <span><i class="far fa-clock"></i> {{ $course->duration_hours }} {{ __('public.hours') }}</span>
                        @endif
                        @if((int) ($course->lessons_count ?? 0) > 0)
                        <span><i class="fas fa-layer-group"></i> {{ $course->lessons_count }} {{ __('public.lecture_single') }}</span>
                        @endif
                        <span><i class="fas fa-globe"></i> {{ $languageLabel }}</span>
                        <span><i class="fas fa-rotate"></i> {{ $course->updated_at?->translatedFormat('M Y') ?? '—' }}</span>
                    </div>

                    @if($course->instructor)
                    <div class="sana-cd-hero__instructor">
                        @if($course->instructor->profile_image_url)
                            <img src="{{ $course->instructor->profile_image_url }}" alt="">
                        @else
                            <span class="av">{{ mb_substr($course->instructor->name, 0, 1) }}</span>
                        @endif
                        <div>
                            <small>{{ __('public.filter_instructor') }}</small>
                            @if($instructorProfile)
                                <strong><a href="{{ route('public.instructors.show', $course->instructor) }}">{{ $course->instructor->name }}</a></strong>
                            @else
                                <strong>{{ $course->instructor->name }}</strong>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="sana-cd-hero__actions">
                        @include('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled, 'canEnrollPublicly' => $canEnrollPublicly])
                        @if($hasPreview)
                            <a href="#course-preview" class="sana-course-cta sana-course-cta--outline">
                                <i class="fas fa-circle-play"></i>
                                <span>{{ __('public.course_preview') }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if($hasPreview)
                <div class="sana-cd-hero__media sana-reveal" id="course-preview">
                    @if($introEmbedUrl)
                        <iframe src="{{ $introEmbedUrl }}" title="{{ __('public.course_preview') }}" allowfullscreen loading="lazy"></iframe>
                    @else
                        <video src="{{ $introDirectVideo }}" controls playsinline preload="metadata"></video>
                    @endif
                </div>
                @elseif($thumbUrl)
                <div class="sana-cd-hero__media sana-reveal">
                    <img src="{{ $thumbUrl }}" alt="{{ $course->title }}">
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- BODY 70/30 --}}
    <section class="sana-cd-body">
        <div class="sana-container">
            <div class="sana-cd-layout">
                <div class="sana-cd-main">

                    @if($isEnrolled && $enrollmentProgress !== null)
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-chart-line"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.course_progress_label') }}</h2>
                        </div>
                        <div class="sana-course-card__progress" style="margin:0">
                            <div class="sana-course-card__progress-head">
                                <span>{{ __('public.progress_completion_label') }}</span>
                                <strong>{{ (int) round($enrollmentProgress) }}%</strong>
                            </div>
                            <div class="sana-course-card__progress-track">
                                <span style="width: {{ min(100, max(0, $enrollmentProgress)) }}%"></span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($learnPoints) || $objectives)
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-bullseye"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.what_you_learn') }}</h2>
                        </div>
                        @if(!empty($learnPoints))
                        <div class="sana-cd-learn-grid">
                            @foreach($learnPoints as $point)
                                <div class="sana-cd-learn-item"><i class="fas fa-check-circle"></i><span>{{ $point }}</span></div>
                            @endforeach
                        </div>
                        @endif
                        @if($objectives)
                            <p class="sana-cd-section__sub" style="margin-top:16px;white-space:pre-line">{{ $objectives }}</p>
                        @endif
                    </div>
                    @endif

                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-list-ul"></i></span>
                            <div>
                                <h2 class="sana-cd-section__title">{{ __('public.course_curriculum_title') }}</h2>
                                <p class="sana-cd-section__sub" style="margin-top:4px">
                                    {{ $curriculumStats['modules'] ?? 0 }} {{ __('public.curriculum_stat_module') }} ·
                                    {{ $curriculumStats['lessons'] ?? 0 }} {{ __('public.curriculum_stat_lesson') }} ·
                                    {{ $curriculumStats['quizzes'] ?? 0 }} {{ __('public.curriculum_stat_quiz') }} ·
                                    {{ $curriculumStats['assignments'] ?? 0 }} {{ __('public.curriculum_stat_assignment') }}
                                </p>
                            </div>
                        </div>
                        @include('landing.sana.partials.course-curriculum')
                    </div>

                    @if($requirements)
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-list-check"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.requirements') }}</h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line">{{ $requirements }}</p>
                    </div>
                    @endif

                    @if($audience)
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-user-group"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.course_audience_title') }}</h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line">{{ $audience }}</p>
                    </div>
                    @endif

                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-align-right"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.about_course') }}</h2>
                        </div>
                        <p class="sana-cd-section__sub" style="white-space:pre-line">{{ $course->description ?? __('public.course_desc_fallback') }}</p>
                    </div>

                    @if($course->instructor)
                    <div class="sana-cd-section sana-reveal" id="instructor">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-chalkboard-user"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.instructor_about_heading') }}</h2>
                        </div>
                        <div class="sana-cd-instructor">
                            @php
                                $instPhoto = $instructorProfile?->photo_url ?? $course->instructor->profile_image_url;
                            @endphp
                            @if($instPhoto)
                                <img class="sana-cd-instructor__photo" src="{{ $instPhoto }}" alt="{{ $course->instructor->name }}">
                            @else
                                <span class="sana-cd-instructor__photo sana-cd-instructor__photo--initial">{{ mb_substr($course->instructor->name, 0, 1) }}</span>
                            @endif
                            <div>
                                <h3 style="font-size:1.15rem;font-weight:900;margin:0 0 6px">{{ $course->instructor->name }}</h3>
                                @if($instructorProfile?->headline)
                                    <p style="color:var(--p);font-weight:800;font-size:0.88rem;margin:0 0 12px">{{ $instructorProfile->headline }}</p>
                                @endif
                                <div class="sana-cd-instructor__stats">
                                    <div><strong>{{ $instructorStats['courses'] }}</strong><span>{{ __('public.course_singular') }}</span></div>
                                    @if((int) ($instructorStats['students'] ?? 0) > 0)
                                    <div><strong>{{ number_format($instructorStats['students']) }}</strong><span>{{ __('public.stat_students') }}</span></div>
                                    @endif
                                    @if($instructorStats['rating'])
                                    <div><strong>{{ $instructorStats['rating'] }}</strong><span>{{ __('public.stat_rating') }}</span></div>
                                    @endif
                                </div>
                                @if($instructorProfile?->bio)
                                    <p class="sana-cd-section__sub" style="white-space:pre-line">{{ $instructorProfile->bio }}</p>
                                @endif
                                @if($instructorProfile && !empty($instructorProfile->experience_list))
                                    <ul style="margin:12px 0 0;padding:0;list-style:none">
                                        @foreach($instructorProfile->experience_list as $exp)
                                            <li class="sana-cd-learn-item" style="margin-bottom:8px"><i class="fas fa-briefcase"></i><span>{{ $exp }}</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($reviews->isNotEmpty() && $totalReviews > 0 && $avgRating)
                    <div class="sana-cd-section sana-reveal">
                        <div class="sana-cd-section__head">
                            <span class="sana-cd-section__icon"><i class="fas fa-star"></i></span>
                            <h2 class="sana-cd-section__title">{{ __('public.student_reviews_title') }}</h2>
                        </div>
                        <div class="sana-cd-reviews-layout">
                            <div>
                                <div class="sana-cd-rating-big">
                                    <strong>{{ $avgRating }}</strong>
                                    <div class="stars">
                                        @for($s = 1; $s <= 5; $s++)<i class="fas fa-star"></i>@endfor
                                    </div>
                                    <span style="font-size:0.78rem;font-weight:700;color:var(--muted)">{{ number_format($totalReviews) }} {{ __('public.stat_rating') }}</span>
                                </div>
                                <div style="margin-top:16px">
                                    @for($star = 5; $star >= 1; $star--)
                                        @php
                                            $count = (int) ($ratingBreakdown[$star] ?? 0);
                                            $pct = $totalReviews > 0 ? ($count / max($totalReviews, 1)) * 100 : 0;
                                        @endphp
                                        <div class="sana-cd-rating-bar">
                                            <span>{{ $star }}</span>
                                            <div class="sana-cd-rating-bar__track"><span style="width:{{ $pct }}%"></span></div>
                                            <span>{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div>
                                @forelse($reviews as $review)
                                    <article class="sana-cd-review-card">
                                        <div class="sana-cd-review-card__head">
                                            @if($review->user?->profile_image_url)
                                                <img src="{{ $review->user->profile_image_url }}" alt="">
                                            @else
                                                <span class="av">{{ mb_substr($review->user?->name ?? __('public.student_fallback'), 0, 1) }}</span>
                                            @endif
                                            <div>
                                                <strong style="font-size:0.88rem">{{ $review->user?->name ?? __('public.student_fallback') }}</strong>
                                                <div class="sana-cd-review-card__stars">
                                                    @for($s = 1; $s <= 5; $s++)
                                                        <i class="fa{{ $s <= (int)$review->rating ? 's' : 'r' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <p>{{ $review->review ?? $review->comment ?? '' }}</p>
                                    </article>
                                @empty
                                    <p class="sana-cd-section__sub">{{ __('public.reviews_empty_text') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Desktop sticky sidebar --}}
                <div class="sana-cd-sidebar-wrap">
                    @include('landing.sana.partials.course-enroll-sidebar')
                </div>
            </div>
        </div>
    </section>
</main>

{{-- Mobile sticky enroll --}}
<div class="sana-cd-mobile-bar">
    <div class="sana-cd-mobile-bar__inner">
        <div class="sana-cd-mobile-bar__price">
            @if($course->usesContactSupportPricing())
                <i class="fab fa-whatsapp"></i> {{ __('public.contact_short') }}
            @elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false))
                {{ number_format($course->effectivePurchasePrice(), 0) }} {{ __('public.currency') }}
            @else
                {{ __('public.free_price') }}
            @endif
        </div>
        @include('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled, 'canEnrollPublicly' => $canEnrollPublicly])
    </div>
</div>

@include('landing.sana.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.SanaCourseFavorites) {
        window.SanaCourseFavorites.init({
            authenticated: @json(auth()->check()),
            loginUrl: @json(route('login')),
            toggleUrlTemplate: @json(url('/saved-courses/__ID__/toggle')),
            syncUrl: @json(route('public.saved-courses.sync')),
            savedIds: @js($savedCourseIds ?? []),
        });
    }
});
</script>
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>

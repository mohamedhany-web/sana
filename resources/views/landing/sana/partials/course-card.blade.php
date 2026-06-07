@php
    use App\Support\PublicCourseCatalog;

    /** @var \App\Models\AdvancedCourse|array $course */
    $c = is_array($course)
        ? $course
        : (PublicCourseCatalog::mapForCards(collect([$course]))[0] ?? []);

    $url = ($c['id'] ?? 0) > 0
        ? ($courseUrl ?? route('public.course.show', $c['id']))
        : route('public.courses');
    $featured = !empty($featured);
    $currency = __('public.currency');

    $priceType = 'normal';
    if (!empty($c['contact_support_for_pricing'])) {
        $priceType = 'whatsapp';
    } elseif (!empty($c['is_free'])) {
        $priceType = 'free';
    }

    $inst = $c['instructor'] ?? null;
    $instName = $inst['name'] ?? 'معلّم';
    $instAvatar = $inst['avatar_url'] ?? null;
    $subject = ($c['course_category']['name'] ?? null) ?: ($c['academic_subject']['name'] ?? null);
    $desc = Str::limit(strip_tags($c['description'] ?? 'دورة تعليمية تفاعلية مصمّمة لتجربة تعلّم ممتعة وفعّالة.'), 110);
    $progress = $c['enrollment_progress'] ?? null;
    $isEnrolled = !empty($c['is_enrolled']);
@endphp
<article @class(['sana-course-card', 'sana-course-card--featured' => $featured, 'sana-reveal' => !($noReveal ?? false)])>
    <div class="sana-course-card__media">
        <a href="{{ $url }}" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
            <img src="{{ $c['card_image_url'] ?? $c['thumbnail_url'] ?? PublicCourseCatalog::defaultCardImage() }}"
                 alt="{{ $c['title'] ?? '' }}" loading="lazy">
        </a>
        <div class="sana-course-card__shine" aria-hidden="true"></div>
        <div class="sana-course-card__badges">
            @if($subject)
                <span class="sana-course-card__badge sana-course-card__badge--subject">{{ $subject }}</span>
            @endif
            <span class="sana-course-card__badge sana-course-card__badge--level">{{ $c['level_label'] ?? 'مبتدئ' }}</span>
            @if(!empty($c['is_featured']))
                <span class="sana-course-card__badge sana-course-card__badge--featured">{{ __('public.featured_badge') }}</span>
            @endif
        </div>
    </div>

    <div class="sana-course-card__body">
        <h3 class="sana-course-card__title">
            <a href="{{ $url }}">{{ $c['title'] ?? '' }}</a>
        </h3>
        <p class="sana-course-card__desc">{{ $desc }}</p>

        @if($inst)
        <div class="sana-course-card__instructor">
            @if($instAvatar)
                <img class="sana-course-card__avatar" src="{{ $instAvatar }}" alt="">
            @else
                <span class="sana-course-card__avatar sana-course-card__avatar--initial">{{ mb_substr($instName, 0, 1) }}</span>
            @endif
            <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label">المعلّم</span>
                <span class="sana-course-card__instructor-name">{{ $instName }}</span>
            </div>
        </div>
        @endif

        <div class="sana-course-card__stats">
            <span class="sana-course-card__stat sana-course-card__stat--rating">
                <i class="fas fa-star"></i>
                <strong>{{ $c['rating'] ?? '4.9' }}</strong>
            </span>
            <span class="sana-course-card__stat">
                <i class="fas fa-users"></i>
                <span>{{ number_format($c['students_count'] ?? 0) }}</span>
            </span>
            @if(!empty($c['duration_hours']))
            <span class="sana-course-card__stat">
                <i class="far fa-clock"></i>
                <span>{{ $c['duration_hours'] }} {{ __('public.hours') }}</span>
            </span>
            @endif
            @if(!empty($c['lectures_count']))
            <span class="sana-course-card__stat">
                <i class="fas fa-layer-group"></i>
                <span>{{ $c['lectures_count'] }} {{ __('public.lecture_single') }}</span>
            </span>
            @endif
        </div>
    </div>

    <div class="sana-course-card__footer">
        @if($isEnrolled && $progress !== null)
        <div class="sana-course-card__progress">
            <div class="sana-course-card__progress-head">
                <span>تقدّمك في الدورة</span>
                <strong>{{ (int) round($progress) }}%</strong>
            </div>
            <div class="sana-course-card__progress-track">
                <span style="width: {{ min(100, max(0, $progress)) }}%"></span>
            </div>
        </div>
        @endif
        <div class="sana-course-card__actions">
            <div class="sana-course-card__price-wrap">
                @if($priceType === 'free')
                    <span class="sana-course-card__price sana-course-card__price--free">
                        <i class="fas fa-gift"></i> {{ __('public.free_price') }}
                    </span>
                @elseif($priceType === 'whatsapp')
                    <span class="sana-course-card__price sana-course-card__price--contact">
                        <i class="fab fa-whatsapp"></i> تواصل للسعر
                    </span>
                @else
                    <span class="sana-course-card__price">
                        {{ number_format($c['sale_price'] ?? $c['price'] ?? 0, 0) }} {{ $currency }}
                    </span>
                @endif
            </div>
            <a href="{{ $url }}" class="sana-course-card__cta">
                <span>{{ $isEnrolled ? 'تابع التعلّم' : 'ابدأ الآن' }}</span>
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
</article>

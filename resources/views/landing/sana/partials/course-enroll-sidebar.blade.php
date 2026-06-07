@php
    $thumbUrl = ($course->thumbnail ?? null)
        ? asset('storage/' . str_replace('\\', '/', $course->thumbnail))
        : \App\Support\PublicCourseCatalog::defaultCardImage();
    $hasPreview = !empty($introEmbedUrl) || !empty($introDirectVideo);
@endphp
<aside class="sana-cd-sidebar sana-reveal">
    <div class="sana-cd-enroll">
        <div class="sana-cd-enroll__thumb">
            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}">
        </div>
        <div class="sana-cd-enroll__body">
            <div class="sana-cd-enroll__price-head">
                @if($course->usesContactSupportPricing())
                    <span class="sana-cd-enroll__price-free"><i class="fab fa-whatsapp"></i> تواصل للتسعير</span>
                @elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false))
                    @if($course->hasPromotionalPrice())
                        <div class="sana-cd-enroll__price-old">{{ number_format($course->listPriceAmount(), 0) }} {{ __('public.currency') }}</div>
                    @endif
                    <div class="sana-cd-enroll__price-now">
                        {{ number_format($course->effectivePurchasePrice(), 0) }}
                        <span style="font-size:0.95rem;font-weight:700;color:var(--muted)">{{ __('public.currency') }}</span>
                    </div>
                @else
                    <span class="sana-cd-enroll__price-free"><i class="fas fa-gift"></i> {{ __('public.free_price') }}</span>
                @endif
            </div>

            <div class="sana-cd-enroll__actions">
                @include('landing.sana.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled, 'block' => true])
                @if($hasPreview)
                    <a href="#course-preview" class="sana-course-cta sana-course-cta--outline sana-course-cta--block">
                        <i class="fas fa-circle-play"></i>
                        <span>معاينة الدورة</span>
                    </a>
                @endif
            </div>

            <div class="sana-cd-enroll__trust">
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-shield-check"></i> ضمان استرداد خلال 7 أيام</div>
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-certificate"></i> شهادة إتمام معتمدة</div>
                <div class="sana-cd-enroll__trust-item"><i class="fas fa-infinity"></i> وصول مدى الحياة</div>
            </div>

            <div class="sana-cd-enroll__stats">
                <div class="sana-cd-enroll__stat"><strong>{{ $course->lessons_count ?? 0 }}</strong> {{ __('public.lecture_single') }}</div>
                <div class="sana-cd-enroll__stat"><strong>{{ $course->duration_hours ?? 0 }}</strong> {{ __('public.hours') }}</div>
                <div class="sana-cd-enroll__stat"><strong>{{ number_format($course->students_count ?? 0) }}</strong> طالب</div>
                <div class="sana-cd-enroll__stat"><strong>{{ $course->rating ? number_format((float)$course->rating, 1) : '4.9' }}</strong> تقييم</div>
            </div>
        </div>
    </div>

    @if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
        <div class="sana-cd-section sana-reveal" style="margin-top:20px;padding:20px">
            <h3 class="sana-cd-section__title" style="margin-bottom:14px;font-size:1rem">دورات ذات صلة</h3>
            <div class="sana-cd-related">
                @foreach($relatedCourses->take(3) as $related)
                    @php
                        $relThumb = $related->thumbnail ? asset('storage/' . str_replace('\\', '/', $related->thumbnail)) : \App\Support\PublicCourseCatalog::defaultCardImage();
                    @endphp
                    <a href="{{ route('public.course.show', $related->id) }}" class="sana-cd-related-item">
                        <img src="{{ $relThumb }}" alt="" loading="lazy">
                        <div>
                            <strong style="font-size:0.82rem;line-height:1.4;display:block">{{ Str::limit($related->title, 48) }}</strong>
                            <span style="font-size:0.72rem;color:var(--p);font-weight:800">{{ number_format($related->effectivePurchasePrice(), 0) }} {{ __('public.currency') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</aside>

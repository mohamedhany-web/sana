@php
    $contactSupport = $course->usesContactSupportPricing();
    $isPaid = ! $contactSupport && $course->effectivePurchasePrice() > 0 && !($course->is_free ?? false);
    $blockClass = ($block ?? false) ? 'sana-course-cta sana-course-cta--block' : 'sana-course-cta';
    $variant = $variant ?? 'primary';
@endphp

@if($contactSupport)
    <a href="{{ $course->supportWhatsAppUrl() }}" target="_blank" rel="noopener noreferrer" class="{{ $blockClass }} sana-course-cta--whatsapp {{ $class ?? '' }}">
        <i class="fab fa-whatsapp"></i>
        <span>{{ __('public.course_contact_support') }}</span>
    </a>
@else
    @auth
        @if($isEnrolled ?? false)
            <a href="{{ route('my-courses.show', $course) }}" class="{{ $blockClass }} {{ $class ?? '' }}">
                <i class="fas fa-play-circle"></i>
                <span>{{ __('public.start_learning_now') }}</span>
            </a>
        @elseif($isPaid)
            <a href="{{ route('public.course.checkout', $course->id) }}" class="{{ $blockClass }} {{ $class ?? '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span>سجّل الآن</span>
            </a>
        @else
            <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST" @if($block ?? false) class="w-full" @endif>
                @csrf
                <button type="submit" class="{{ $blockClass }} sana-course-cta--free {{ $class ?? '' }}">
                    <i class="fas fa-gift"></i>
                    <span>{{ __('public.register_free') }}</span>
                </button>
            </form>
        @endif
    @else
        @if($isPaid)
            <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="{{ $blockClass }} {{ $class ?? '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span>سجّل الآن</span>
            </a>
        @else
            <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="{{ $blockClass }} sana-course-cta--free {{ $class ?? '' }}">
                <i class="fas fa-gift"></i>
                <span>{{ __('public.register_free') }}</span>
            </a>
        @endif
    @endguest
@endif

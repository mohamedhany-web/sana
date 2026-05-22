@php

    $contactSupport = $course->usesContactSupportPricing();

    $isPaid = ! $contactSupport && $course->effectivePurchasePrice() > 0 && !($course->is_free ?? false);

    $btnClass = ($block ?? false) ? 'edu-btn-primary w-full' : 'edu-btn-primary';

    $freeClass = ($block ?? false) ? 'edu-btn-primary w-full !bg-emerald-600 hover:!bg-emerald-700' : 'edu-btn-primary !bg-emerald-600 hover:!bg-emerald-700';

    $waClass = ($block ?? false) ? 'edu-btn-primary w-full !bg-[#25D366] hover:!bg-[#1da851]' : 'edu-btn-primary !bg-[#25D366] hover:!bg-[#1da851]';

@endphp

@if($contactSupport)

    <a href="{{ $course->supportWhatsAppUrl() }}" target="_blank" rel="noopener noreferrer" class="{{ $waClass }} {{ $class ?? '' }}">

        <i class="fab fa-whatsapp"></i> {{ __('public.course_contact_support') }}

    </a>

@else

@auth

    @if($isEnrolled ?? false)

        <a href="{{ route('my-courses.show', $course) }}" class="{{ $btnClass }} {{ $class ?? '' }}">

            <i class="fas fa-play-circle"></i> {{ __('public.start_learning_now') }}

        </a>

    @elseif($isPaid)

        <a href="{{ route('public.course.checkout', $course->id) }}" class="{{ $btnClass }} {{ $class ?? '' }}">

            <i class="fas fa-shopping-cart"></i> {{ __('public.buy_now') }}

        </a>

    @else

        <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST" @if($block ?? false) class="w-full" @endif>

            @csrf

            <button type="submit" class="{{ $freeClass }} {{ $class ?? '' }} cursor-pointer">

                <i class="fas fa-gift"></i> {{ __('public.register_free') }}

            </button>

        </form>

    @endif

@endauth

@guest

    @if($isPaid)

        <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="{{ $btnClass }} {{ $class ?? '' }}">

            <i class="fas fa-shopping-cart"></i> {{ __('public.buy_now') }}

        </a>

    @else

        <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="{{ $freeClass }} {{ $class ?? '' }}">

            <i class="fas fa-gift"></i> {{ __('public.register_free') }}

        </a>

    @endif

@endguest

@endif


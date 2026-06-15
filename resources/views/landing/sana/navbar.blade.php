@php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $cta = \App\Support\PublicSiteCta::payload();
    $onContact = request()->routeIs('public.contact', 'public.contact.*');
    $onTutor = request()->routeIs('tutor.*');
    $onHome = request()->routeIs('home');
    $familiesActive = ! $onHome && ! $onTutor && ! $onContact && request()->routeIs(
        'public.courses',
        'public.courses.*',
        'public.pricing',
        'public.how_it_works',
        'public.instructors.index',
        'public.instructors.show',
        'public.certificates',
    );
@endphp
<header id="sana-nav" class="sana-nav {{ ($onHome && !request()->routeIs('public.*')) ? 'sana-nav--hero' : 'is-solid' }}">
    <div class="sana-container">
        <div class="sana-nav__inner">
            <a href="{{ route('home') }}" class="sana-nav__brand">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="sana-nav__logo-img">
                @endif
                <span class="sana-nav__logo-text">{{ strtoupper($brand) }}</span>
            </a>

            <nav class="sana-nav__links" aria-label="القائمة">
                <a href="{{ route('home') }}" class="{{ $onHome ? 'is-active' : '' }}">{{ __('public.home') }}</a>
                <a href="{{ $cta['families_path_url'] }}" class="sana-nav__path sana-nav__path--family {{ $familiesActive && !$onTutor ? 'is-active' : '' }}">
                    {{ __('public.nav_for_families') }}
                </a>
                <a href="{{ $cta['teachers_path_url'] }}" class="sana-nav__path sana-nav__path--teacher {{ $onTutor ? 'is-active' : '' }}">
                    {{ __('public.nav_for_teachers') }}
                </a>
                <a href="{{ route('public.contact') }}" class="{{ $onContact ? 'is-active' : '' }}">{{ __('public.contact_page_title') }}</a>
            </nav>

            <div class="sana-nav__actions">
                @auth
                    <a href="{{ url('/dashboard') }}" class="sana-nav__login">لوحتي</a>
                @else
                    <a href="{{ route('login') }}" class="sana-nav__login">تسجيل الدخول</a>
                    <a href="{{ $cta['assessment_url'] }}" class="sana-nav__signup">{{ $cta['primary_label'] }}</a>
                @endauth
            </div>

            <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="فتح القائمة">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
        <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
            <a href="{{ route('home') }}" class="{{ $onHome ? 'is-active' : '' }}">{{ __('public.home') }}</a>
            <a href="{{ $cta['families_path_url'] }}" class="sana-nav__path sana-nav__path--family {{ $familiesActive && !$onTutor ? 'is-active' : '' }}">{{ __('public.nav_for_families') }}</a>
            <a href="{{ $cta['teachers_path_url'] }}" class="sana-nav__path sana-nav__path--teacher {{ $onTutor ? 'is-active' : '' }}">{{ __('public.nav_for_teachers') }}</a>
            <a href="{{ route('public.contact') }}" class="{{ $onContact ? 'is-active' : '' }}">{{ __('public.contact_page_title') }}</a>
            @guest
                <a href="{{ route('login') }}">تسجيل الدخول</a>
                <a href="{{ $cta['assessment_url'] }}" class="sana-nav__signup sana-nav__signup--block">{{ $cta['primary_label'] }}</a>
                <a href="{{ $cta['whatsapp_url'] }}" @if($cta['has_whatsapp']) target="_blank" rel="noopener noreferrer" @endif class="sana-nav__signup sana-nav__signup--block sana-nav__signup--wa">{{ $cta['secondary_label'] }}</a>
            @else
                <a href="{{ url('/dashboard') }}">لوحتي</a>
            @endguest
        </div>
    </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>

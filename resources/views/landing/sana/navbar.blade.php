@php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $onCourses = request()->routeIs('public.courses', 'public.courses.saved', 'public.course.show');
    $onPricing = request()->routeIs('public.pricing', 'public.pricing*', 'public.subscription.*');
    $onAbout = request()->routeIs('public.about');
    $onContact = request()->routeIs('public.contact', 'public.contact.*');
    $onInstructors = request()->routeIs('public.instructors*');
    $onCertificates = request()->routeIs('public.certificates*');
    $onFaq = request()->routeIs('public.faq');
    $onPrivacy = request()->routeIs('public.privacy');
    $onTerms = request()->routeIs('public.terms');
    $onSubPage = $onCourses || $onPricing || $onAbout || $onContact || $onInstructors || $onCertificates || $onFaq || $onPrivacy || $onTerms;
@endphp
<header id="sana-nav" class="sana-nav {{ $onSubPage ? 'is-solid' : 'sana-nav--hero' }}">
    <div class="sana-container">
        <div class="sana-nav__inner">
            <a href="{{ route('home') }}" class="sana-nav__brand">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="sana-nav__logo-img">
                @endif
                <span class="sana-nav__logo-text">{{ strtoupper($brand) }}</span>
            </a>

            <nav class="sana-nav__links" aria-label="القائمة">
                <a href="{{ route('home') }}" class="{{ !$onCourses ? 'is-active' : '' }}">الرئيسية</a>
                <a href="{{ route('public.courses') }}" class="{{ $onCourses ? 'is-active' : '' }}">الدورات</a>
                <a href="{{ route('public.instructors.index') }}" class="{{ $onInstructors ? 'is-active' : '' }}">المعلّمون</a>
                <a href="{{ route('public.pricing') }}" class="{{ $onPricing ? 'is-active' : '' }}">الأسعار</a>
                <a href="{{ route('public.about') }}" class="{{ $onAbout ? 'is-active' : '' }}">عن المنصة</a>
                <a href="{{ route('public.contact') }}" class="{{ $onContact ? 'is-active' : '' }}">تواصل معنا</a>
            </nav>

            <div class="sana-nav__actions">
                @auth
                    <a href="{{ url('/dashboard') }}" class="sana-nav__login">لوحتي</a>
                @else
                    <a href="{{ route('login') }}" class="sana-nav__login">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="sana-nav__signup">إنشاء حساب</a>
                @endauth
            </div>

            <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="فتح القائمة">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
        <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
            <a href="{{ route('home') }}" class="{{ !$onCourses ? 'is-active' : '' }}">الرئيسية</a>
            <a href="{{ route('public.courses') }}" class="{{ $onCourses ? 'is-active' : '' }}">الدورات</a>
            <a href="{{ route('public.instructors.index') }}" class="{{ $onInstructors ? 'is-active' : '' }}">المعلّمون</a>
            <a href="{{ route('public.pricing') }}" class="{{ $onPricing ? 'is-active' : '' }}">الأسعار</a>
            <a href="{{ route('public.about') }}" class="{{ $onAbout ? 'is-active' : '' }}">عن المنصة</a>
            <a href="{{ route('public.contact') }}" class="{{ $onContact ? 'is-active' : '' }}">تواصل معنا</a>
            @guest
                <a href="{{ route('login') }}">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="sana-nav__signup sana-nav__signup--block">إنشاء حساب</a>
            @else
                <a href="{{ url('/dashboard') }}">لوحتي</a>
            @endguest
        </div>
    </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>

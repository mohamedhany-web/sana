@php
    $brand = config('app.name');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $tagline = $navbarBrandTagline ?? \App\Services\PublicFooterSettings::payload()['brand_tagline'] ?? $tr('tagline');
@endphp
<header id="sana-nav" class="sana-nav fixed top-0 inset-x-0 z-[1000] transition-all duration-500">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex items-center justify-between h-[72px] lg:h-[80px]">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="h-10 w-10 rounded-2xl object-cover ring-2 ring-white/60 shadow-lg">
                @else
                    <span class="sana-logo-mark flex h-11 w-11 items-center justify-center rounded-2xl text-lg font-black text-white shadow-lg">{{ mb_substr($brand, 0, 1) }}</span>
                @endif
                <span class="flex flex-col leading-tight">
                    <span class="text-[1.15rem] font-extrabold tracking-tight text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $brand }}</span>
                    <span class="text-[11px] font-medium text-slate-500 line-clamp-1 max-w-[200px]">{{ $tagline }}</span>
                </span>
            </a>

            <nav class="hidden lg:flex items-center justify-center gap-1 absolute left-1/2 -translate-x-1/2" aria-label="القائمة">
                <a href="{{ route('home') }}" class="sana-nav-link">{{ $tr('nav.home') }}</a>
                <a href="{{ route('public.courses') }}" class="sana-nav-link">{{ $tr('nav.courses') }}</a>
                <a href="#paths" class="sana-nav-link">{{ $tr('nav.paths') }}</a>
                <a href="{{ route('public.services.index') }}" class="sana-nav-link">{{ $tr('nav.services') }}</a>
                <a href="#instructors" class="sana-nav-link">{{ $tr('nav.instructors') }}</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3 shrink-0">
                @auth
                    <a href="{{ url('/dashboard') }}" class="sana-btn-ghost">{{ $tr('nav.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="sana-btn-ghost">{{ $tr('nav.login') }}</a>
                    <a href="{{ route('register') }}" class="sana-btn-primary">{{ $tr('nav.get_started') }}</a>
                @endauth
            </div>

            <button type="button" id="sana-mobile-toggle" class="lg:hidden w-11 h-11 rounded-2xl flex items-center justify-center text-slate-700 bg-white/70 border border-white/80 backdrop-blur-md shadow-sm" aria-expanded="false" aria-controls="sana-mobile-menu">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>
    </div>

    <div id="sana-mobile-menu" class="lg:hidden hidden border-t border-white/40 bg-white/90 backdrop-blur-xl shadow-xl">
        <div class="max-w-[1280px] mx-auto px-5 py-5 flex flex-col gap-2">
            <a href="{{ route('home') }}" class="sana-mobile-link">{{ $tr('nav.home') }}</a>
            <a href="{{ route('public.courses') }}" class="sana-mobile-link">{{ $tr('nav.courses') }}</a>
            <a href="#paths" class="sana-mobile-link">{{ $tr('nav.paths') }}</a>
            <a href="{{ route('public.services.index') }}" class="sana-mobile-link">{{ $tr('nav.services') }}</a>
            <a href="#instructors" class="sana-mobile-link">{{ $tr('nav.instructors') }}</a>
            <div class="flex gap-2 pt-3 border-t border-slate-100 mt-2">
                @guest
                    <a href="{{ route('login') }}" class="sana-btn-ghost flex-1 justify-center">{{ $tr('nav.login') }}</a>
                    <a href="{{ route('register') }}" class="sana-btn-primary flex-1 justify-center">{{ $tr('nav.get_started') }}</a>
                @else
                    <a href="{{ url('/dashboard') }}" class="sana-btn-primary w-full justify-center">{{ $tr('nav.dashboard') }}</a>
                @endguest
            </div>
        </div>
    </div>
</header>

@php
    $geoBrandLogoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $geoBrandName = config('app.name', 'Sana');
    $geoBrandSize = $geoBrandSize ?? 'mark';
    $geoBrandLink = $geoBrandLink ?? ($geoBrandSize === 'nav');
    $geoBrandShowName = $geoBrandShowName ?? ($geoBrandSize === 'nav');
@endphp

@if($geoBrandSize === 'mark')
    <div class="geo-brand-mark" aria-hidden="true">
        @if($geoBrandLogoUrl)
            <img src="{{ $geoBrandLogoUrl }}" alt="{{ $geoBrandName }}" class="geo-brand-img geo-brand-img--mark" loading="eager" decoding="async">
        @else
            <div class="geo-login-hero">
                <div class="geo-login-hero-ring"></div>
                <div class="geo-login-hero-ring"></div>
                <div class="geo-login-hero-core"></div>
            </div>
        @endif
    </div>
@else
    @if($geoBrandLink)
        <a href="{{ route('home') }}" class="geo-logo geo-logo--brand">
    @else
        <span class="geo-logo geo-logo--brand">
    @endif
        @if($geoBrandLogoUrl)
            <span class="geo-brand-mark geo-brand-mark--nav">
                <img src="{{ $geoBrandLogoUrl }}" alt="{{ $geoBrandName }}" class="geo-brand-img geo-brand-img--nav" loading="eager" decoding="async">
            </span>
        @endif
        @if($geoBrandShowName)
            <span>{{ $geoBrandName }}</span>
        @endif
    @if($geoBrandLink)
        </a>
    @else
        </span>
    @endif
@endif

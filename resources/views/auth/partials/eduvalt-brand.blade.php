@php
    $logoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $brand = config('app.name', 'Sana');
    $letter = mb_substr($brand, 0, 1);
@endphp
<a href="{{ route('home') }}" class="auth-brand">
    <span class="auth-brand-logo">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $brand }}" width="48" height="48" loading="eager">
        @else
            {{ $letter }}
        @endif
    </span>
    <span class="auth-brand-name">{{ $brand }}</span>
</a>

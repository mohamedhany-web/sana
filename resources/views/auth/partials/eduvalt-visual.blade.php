@php
    $brand = config('app.name');
    $eyebrow = $eyebrow ?? 'تعلّم أونلاين باحتراف';
    $titleMain = $titleMain ?? 'انضم إلى آلاف المتعلمين';
    $titleEm = $titleEm ?? 'وابدأ رحلتك اليوم';
    $captionText = $captionText ?? 'تعلّم تفاعلي مع أفضل المدربين';
    $footnote = $footnote ?? ('صور حقيقية من بيئة تعليمية — استكشف الدورات بعد التسجيل على ' . $brand . '.');
    $compact = !empty($compact);
    $logoUrl = $adminPanelLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $letter = mb_substr($brand, 0, 1);
    $photos = [
        'main' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop&q=80',
        'instructor' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=500&auto=format&fit=crop&q=80',
        'classroom' => 'https://images.unsplash.com/photo-1524178232363-7f38b04a8a6e?w=500&auto=format&fit=crop&q=80',
    ];
@endphp
<aside class="auth-visual auth-visual--showcase{{ $compact ? ' auth-visual--compact' : '' }}" aria-label="نظرة على المنصة">
    <div class="auth-showcase-glow auth-showcase-glow--1"></div>
    <div class="auth-showcase-glow auth-showcase-glow--2"></div>

    <div class="auth-showcase-inner">
        <a href="{{ route('home') }}" class="auth-brand auth-brand--light">
            <span class="auth-brand-logo auth-brand-logo--glass">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $brand }}" width="48" height="48" loading="eager">
                @else
                    {{ $letter }}
                @endif
            </span>
            <span class="auth-brand-name">{{ $brand }}</span>
        </a>

        <p class="auth-showcase-eyebrow">{{ $eyebrow }}</p>
        <h1 class="auth-showcase-title">
            {{ $titleMain }}
            <em>{{ $titleEm }}</em>
        </h1>

        <div class="auth-photo-collage">
            <figure class="auth-photo auth-photo--main">
                <img src="{{ $photos['main'] }}" alt="طلاب يتعلمون معاً" width="600" height="720" loading="eager">
                <figcaption class="auth-photo-caption">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ $captionText }}</span>
                </figcaption>
            </figure>

            @unless($compact)
            <figure class="auth-photo auth-photo--sub auth-photo--sub-a auth-float">
                <img src="{{ $photos['instructor'] }}" alt="مدرب" width="280" height="340" loading="lazy">
                <figcaption class="auth-photo-tag">مدربون خبراء</figcaption>
            </figure>

            <figure class="auth-photo auth-photo--sub auth-photo--sub-b auth-float-delay">
                <img src="{{ $photos['classroom'] }}" alt="قاعة تعليمية" width="280" height="340" loading="lazy">
                <figcaption class="auth-photo-tag">جلسات مباشرة</figcaption>
            </figure>
            @endunless
        </div>

        @unless($compact)
        <p class="auth-showcase-footnote">{{ $footnote }}</p>
        @endunless
    </div>
</aside>

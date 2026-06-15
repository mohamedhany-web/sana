@php
    $cta = $cta ?? \App\Support\PublicSiteCta::payload();
    $size = $size ?? 'lg';
    $hero = !empty($hero);
    $primaryLabel = $hero ? $cta['primary_label_hero'] : $cta['primary_label'];
    $sizeClass = $size === 'lg' ? ' sana-btn--lg' : '';
    $waExternal = $cta['has_whatsapp'];
@endphp
<div class="sana-site-cta {{ $class ?? '' }}">
    <a href="{{ $cta['assessment_url'] }}" class="sana-btn sana-btn--yellow{{ $sizeClass }}">
        <i class="fas fa-clipboard-check"></i> {{ $primaryLabel }}
    </a>
    <a href="{{ $cta['whatsapp_url'] }}" @if($waExternal) target="_blank" rel="noopener noreferrer" @endif class="sana-btn sana-btn--wa{{ $sizeClass }}">
        <i class="fab fa-whatsapp"></i> {{ $cta['secondary_label'] }}
    </a>
</div>

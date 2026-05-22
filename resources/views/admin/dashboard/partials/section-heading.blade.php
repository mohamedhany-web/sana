@props(['title', 'subtitle' => null, 'icon' => 'fas fa-chart-pie', 'tone' => 'primary'])
<div class="dash-section__head">
    <span class="dash-section__icon dash-section__icon--{{ $tone }}">
        <i class="{{ $icon }}"></i>
    </span>
    <div class="min-w-0">
        <h2 class="dash-section__title">{{ $title }}</h2>
        @if($subtitle)
            <p class="dash-section__sub">{{ $subtitle }}</p>
        @endif
    </div>
</div>

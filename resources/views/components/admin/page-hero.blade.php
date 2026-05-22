@props([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-layer-group',
])
<div class="admin-dashboard-hero animate-fade-in">
    <div class="admin-dashboard-hero-inner flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4 min-w-0">
            <span class="admin-page-hero__icon shrink-0">
                <i class="{{ $icon }}"></i>
            </span>
            <div class="min-w-0">
                <h1 class="hero-title text-xl sm:text-2xl font-heading font-bold">{{ $title }}</h1>
                @if($subtitle)
                    <p class="hero-sub text-sm mt-1.5 max-w-2xl leading-relaxed">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if(trim($slot) !== '')
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>

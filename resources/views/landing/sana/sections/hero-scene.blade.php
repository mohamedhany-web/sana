@php
    $hero3dFile = collect(['imagehome.png', 'landing-hero-3d.png', 'hero-character.png'])
        ->first(fn ($f) => public_static_exists('img/sanua/' . $f));

    $hero3dUrl = $hero3dFile ? public_static_url('img/sanua/' . $hero3dFile) : null;
@endphp

<div class="sana-hero-illus">
    <div class="sana-hero-illus__deco" aria-hidden="true">
        <span class="sana-hero-illus__glow"></span>
        <span class="sana-hero-illus__ring sana-hero-illus__ring--1"></span>
        <span class="sana-hero-illus__ring sana-hero-illus__ring--2"></span>
        <span class="sana-hero-illus__blob sana-hero-illus__blob--1"></span>
        <span class="sana-hero-illus__blob sana-hero-illus__blob--2"></span>

        <span class="sana-hero-illus__cloud sana-hero-illus__cloud--1"></span>
        <span class="sana-hero-illus__cloud sana-hero-illus__cloud--2"></span>
        <span class="sana-hero-illus__cloud sana-hero-illus__cloud--3"></span>
        <span class="sana-hero-illus__cloud sana-hero-illus__cloud--4"></span>

        <span class="sana-hero-illus__spark sana-hero-illus__spark--1">✦</span>
        <span class="sana-hero-illus__spark sana-hero-illus__spark--2">✦</span>
        <span class="sana-hero-illus__spark sana-hero-illus__spark--3">✦</span>
        <span class="sana-hero-illus__star sana-hero-illus__star--1">⭐</span>
        <span class="sana-hero-illus__star sana-hero-illus__star--2">⭐</span>

        <svg class="sana-hero-illus__shoot" viewBox="0 0 64 64" fill="none" aria-hidden="true">
            <path d="M8 52 L44 16" stroke="rgba(255,255,255,0.35)" stroke-width="2" stroke-linecap="round"/>
            <path d="M40 12 L48 20 L40 28 L32 20 Z" fill="rgba(255,255,255,0.45)"/>
        </svg>

        <span class="sana-hero-illus__float sana-hero-illus__float--bulb">💡</span>
        <span class="sana-hero-illus__float sana-hero-illus__float--planet">🪐</span>
        <span class="sana-hero-illus__dot sana-hero-illus__dot--1"></span>
        <span class="sana-hero-illus__dot sana-hero-illus__dot--2"></span>
        <span class="sana-hero-illus__dot sana-hero-illus__dot--3"></span>

        {{-- bokeh + أشكال هندسية --}}
        <span class="sana-hero-illus__bokeh sana-hero-illus__bokeh--1"></span>
        <span class="sana-hero-illus__bokeh sana-hero-illus__bokeh--2"></span>
        <span class="sana-hero-illus__bokeh sana-hero-illus__bokeh--3"></span>
        <span class="sana-hero-illus__shape sana-hero-illus__shape--sq"></span>
        <span class="sana-hero-illus__shape sana-hero-illus__shape--ci"></span>
        <span class="sana-hero-illus__cross"></span>

        {{-- أيقونات تعليمية outline --}}
        <svg class="sana-hero-illus__icon sana-hero-illus__icon--book" viewBox="0 0 48 48" aria-hidden="true">
            <path d="M8 10 C16 6 24 6 32 10 V38 C24 34 16 34 8 38 Z" stroke="rgba(255,255,255,0.35)" stroke-width="2" fill="rgba(255,255,255,0.06)"/>
            <path d="M32 10 C40 6 48 6 48 6 V34 C40 30 32 30 32 30" stroke="rgba(255,255,255,0.25)" stroke-width="2" fill="none"/>
        </svg>
        <svg class="sana-hero-illus__icon sana-hero-illus__icon--cap" viewBox="0 0 48 48" aria-hidden="true">
            <path d="M4 22 L24 12 L44 22 L24 32 Z" stroke="rgba(251,191,36,0.55)" stroke-width="2" fill="rgba(251,191,36,0.08)"/>
            <path d="M12 26 V34 C18 38 30 38 36 34 V26" stroke="rgba(251,191,36,0.45)" stroke-width="2" fill="none"/>
        </svg>
        <svg class="sana-hero-illus__icon sana-hero-illus__icon--pencil" viewBox="0 0 48 48" aria-hidden="true">
            <path d="M32 8 L40 16 L16 40 H8 V32 Z" stroke="rgba(255,255,255,0.35)" stroke-width="2" fill="rgba(255,255,255,0.05)"/>
        </svg>

        <svg class="sana-hero-illus__orbit" viewBox="0 0 320 320" aria-hidden="true">
            <ellipse cx="160" cy="160" rx="130" ry="48" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1.5" transform="rotate(-18 160 160)"/>
        </svg>
    </div>

    @if($hero3dUrl)
        <div class="sana-hero-illus__char-wrap">
            <img
                src="{{ $hero3dUrl }}?v=2"
                alt=""
                class="sana-hero-illus__char"
                width="1536"
                height="1024"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </div>
    @endif
</div>

{{-- Contact hero — SVG shapes only --}}
<div class="sana-ct-scene" aria-hidden="true">
    <div class="sana-ct-scene__deco">
        <span class="sana-ct-scene__ring"></span>
        <span class="sana-ct-scene__blob sana-ct-scene__blob--1"></span>
        <span class="sana-ct-scene__blob sana-ct-scene__blob--2"></span>
        <span class="sana-ct-scene__spark">✦</span>
    </div>
    <svg class="sana-ct-scene__main" viewBox="0 0 360 320" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="ctGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#8B5CF6"/>
                <stop offset="100%" stop-color="#5B21B6"/>
            </linearGradient>
            <linearGradient id="ctGold" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#FCD34D"/>
                <stop offset="100%" stop-color="#F59E0B"/>
            </linearGradient>
        </defs>
        {{-- Headset support --}}
        <g transform="translate(180 155)">
            <circle cx="0" cy="0" r="72" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"/>
            <path d="M-36 -8 C-36 -32 36 -32 36 -8 V16 C36 28 24 36 12 36 H-12 C-24 36 -36 28 -36 16 Z" fill="url(#ctGrad)" filter="drop-shadow(0 8px 16px rgba(76,29,149,0.3))"/>
            <rect x="-44" y="-4" width="16" height="28" rx="8" fill="rgba(255,255,255,0.9)"/>
            <rect x="28" y="-4" width="16" height="28" rx="8" fill="rgba(255,255,255,0.9)"/>
            <path d="M-8 36 V48 C-8 54 8 54 8 48 V36" stroke="rgba(255,255,255,0.6)" stroke-width="3" fill="none"/>
        </g>
        {{-- Chat bubbles --}}
        <g transform="translate(68 108)">
            <rect x="0" y="0" width="72" height="44" rx="14" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.25)"/>
            <circle cx="18" cy="22" r="4" fill="#FBBF24"/><circle cx="36" cy="22" r="4" fill="#FBBF24"/><circle cx="54" cy="22" r="4" fill="#FBBF24"/>
        </g>
        <g transform="translate(220 88)">
            <rect x="0" y="0" width="88" height="48" rx="14" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.28)"/>
            <path d="M12 24 H76 M12 34 H52" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round"/>
        </g>
        {{-- Envelope --}}
        <g transform="translate(248 210) rotate(8)">
            <rect x="0" y="0" width="64" height="44" rx="10" fill="url(#ctGold)" opacity="0.9"/>
            <path d="M0 0 L32 24 L64 0" stroke="#92400E" stroke-width="2" fill="none"/>
        </g>
        {{-- Phone --}}
        <g transform="translate(48 210) rotate(-12)">
            <rect x="0" y="0" width="36" height="56" rx="8" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.3)"/>
            <circle cx="18" cy="46" r="4" fill="rgba(255,255,255,0.4)"/>
        </g>
        <path d="M54 68 L56 74 L62 74 L57 78 L59 84 L54 80 L49 84 L51 78 L46 74 L52 74 Z" fill="#FBBF24" opacity="0.8"/>
    </svg>
    <div class="sana-ct-scene__chip"><i class="fas fa-clock"></i> {{ $supportChip ?? __('sana_contact.support_chip') }}</div>
</div>

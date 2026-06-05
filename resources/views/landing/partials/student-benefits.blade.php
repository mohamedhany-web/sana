@php
    $benefits = [
        ['icon' => 'fa-user', 'key' => 'v1', 'color' => 'primary'],
        ['icon' => 'fa-users', 'key' => 'v2', 'color' => 'purple'],
        ['icon' => 'fa-clipboard-check', 'key' => 'v3', 'color' => 'accent'],
        ['icon' => 'fa-bolt', 'key' => 'v4', 'color' => 'primary'],
    ];
@endphp
<style>
    .home-benefits {
        position: relative;
        padding-top: 0;
        padding-bottom: 3rem;
        background: linear-gradient(180deg, var(--edu-primary-light) 0%, #fff 42%, #fff 100%);
    }
    @media (min-width: 1024px) { .home-benefits { padding-bottom: 4rem; } }

    .home-benefits__header {
        width: 100%;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .home-benefits__title {
        display: inline-block;
        margin: 0;
        font-size: clamp(1.2rem, 2.8vw, 1.65rem);
        font-weight: 800;
        line-height: 1.55;
        color: #0f172a;
        text-align: center;
        direction: rtl;
        unicode-bidi: plaintext;
    }
    .home-benefits__title .edu-title-mark {
        display: inline;
        white-space: nowrap;
    }

    .home-benefit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    @media (min-width: 1024px) {
        .home-benefit-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
    }

    .home-benefit-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: .65rem;
        padding: 1.1rem .85rem;
        min-height: 9.25rem;
        justify-content: flex-start;
        border-radius: 1.25rem;
        background: #fff;
        border: 1.5px solid #e8edf3;
        box-shadow: 0 4px 14px -8px rgba(15, 23, 42, 0.1);
        cursor: default;
        transition: transform .3s cubic-bezier(.4,0,.2,1), border-color .3s, box-shadow .3s, background .3s;
        overflow: hidden;
    }
    .home-benefit-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--edu-primary-light) 0%, transparent 55%);
        opacity: 0;
        transition: opacity .35s;
        pointer-events: none;
    }
    .home-benefit-card.is-active::before,
    .home-benefit-card:hover::before { opacity: 1; }
    .home-benefit-card.is-active,
    .home-benefit-card:hover {
        transform: translateY(-4px);
        border-color: rgba(var(--edu-primary-rgb), .35);
        box-shadow: 0 14px 32px -14px rgba(var(--edu-primary-rgb), .28);
    }

    .home-benefit-card__icon {
        position: relative;
        z-index: 1;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: .9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: transform .35s cubic-bezier(.34,1.56,.64,1), background .3s, color .3s;
    }
    .home-benefit-card--primary .home-benefit-card__icon {
        background: var(--edu-primary-light);
        color: var(--edu-primary);
    }
    .home-benefit-card--purple .home-benefit-card__icon {
        background: var(--edu-purple-light);
        color: var(--edu-purple);
    }
    .home-benefit-card--accent .home-benefit-card__icon {
        background: var(--edu-accent-light);
        color: var(--edu-accent-dark);
    }
    .home-benefit-card.is-active .home-benefit-card__icon,
    .home-benefit-card:hover .home-benefit-card__icon {
        transform: scale(1.12) rotate(-4deg);
    }

    .home-benefit-card__title {
        position: relative;
        z-index: 1;
        font-size: .82rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.45;
    }
    @media (min-width: 640px) { .home-benefit-card__title { font-size: .88rem; } }

    .home-benefit-card__hint {
        position: relative;
        z-index: 1;
        font-size: .72rem;
        color: #64748b;
        line-height: 1.5;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height .35s ease, opacity .3s, margin .3s;
    }
    .home-benefit-card.is-active .home-benefit-card__hint,
    .home-benefit-card:hover .home-benefit-card__hint {
        max-height: 3rem;
        opacity: 1;
        margin-top: -.15rem;
    }

    .home-benefit-dots {
        display: flex;
        justify-content: center;
        gap: .4rem;
        margin-top: 1.25rem;
    }
    .home-benefit-dots button {
        width: .45rem;
        height: .45rem;
        border-radius: 999px;
        border: none;
        background: #cbd5e1;
        padding: 0;
        cursor: pointer;
        transition: width .3s, background .3s;
    }
    .home-benefit-dots button.is-on {
        width: 1.35rem;
        background: var(--edu-primary);
    }

    .reveal-stagger > * {
        opacity: 0;
        transform: translateY(18px);
        transition: opacity .5s ease, transform .5s ease;
    }
    .reveal-stagger.revealed > *:nth-child(1) { transition-delay: .05s; }
    .reveal-stagger.revealed > *:nth-child(2) { transition-delay: .12s; }
    .reveal-stagger.revealed > *:nth-child(3) { transition-delay: .19s; }
    .reveal-stagger.revealed > *:nth-child(4) { transition-delay: .26s; }
    .reveal-stagger.revealed > * {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<section class="home-benefits" id="why-us"
         x-data="studentBenefits({{ count($benefits) }})"
         x-init="init()"
         @mouseenter="pause()"
         @mouseleave="resume()">
    <div class="edu-container">
        <div class="home-benefits__header reveal">
            <h2 class="home-benefits__title">
                {{ $tr('trusted.title_before') }}
                @include('landing.eduvalt.partials.title-mark', ['text' => config('app.name')])
            </h2>
        </div>
        <div class="home-benefit-grid reveal-stagger" data-reveal-stagger role="list">
            @foreach($benefits as $i => $b)
            <article class="home-benefit-card home-benefit-card--{{ $b['color'] }}"
                     :class="{ 'is-active': active === {{ $i }} }"
                     @mouseenter="setActive({{ $i }})"
                     @focusin="setActive({{ $i }})"
                     tabindex="0"
                     role="listitem">
                <span class="home-benefit-card__icon"><i class="fas {{ $b['icon'] }}"></i></span>
                <span class="home-benefit-card__title">{{ $tr('trusted.'.$b['key']) }}</span>
                <span class="home-benefit-card__hint">{{ $tr('trusted.'.$b['key'].'_hint') }}</span>
            </article>
            @endforeach
        </div>
        <div class="home-benefit-dots" role="tablist" aria-label="{{ $tr('trusted.title') }}">
            @foreach($benefits as $i => $b)
            <button type="button"
                    :class="{ 'is-on': active === {{ $i }} }"
                    @click="setActive({{ $i }})"
                    :aria-selected="active === {{ $i }}"
                    aria-label="{{ $tr('trusted.'.$b['key']) }}"></button>
            @endforeach
        </div>
    </div>
</section>

<script>
function studentBenefits(count) {
    return {
        active: 0,
        timer: null,
        paused: false,
        init() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.timer = setInterval(() => this.tick(), 3000);
        },
        tick() {
            if (this.paused) return;
            this.active = (this.active + 1) % count;
        },
        setActive(i) {
            this.active = i;
        },
        pause() { this.paused = true; },
        resume() { this.paused = false; },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        }
    };
}
</script>

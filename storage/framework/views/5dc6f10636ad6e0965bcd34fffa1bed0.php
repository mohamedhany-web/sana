<style>
    /* نفس أسلوب edu-banner-area في الموقع — بدون خلفية منفصلة */
    #home-hero.edu-banner-area {
        padding-bottom: 1.5rem;
        margin-bottom: 0;
    }
    @media (min-width: 1024px) {
        #home-hero.edu-banner-area { padding-bottom: 2rem; }
    }

    .th-hero-ambient {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }
    .th-hero-ambient__blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(72px);
    }
    .th-hero-ambient__blob--1 {
        width: 16rem; height: 16rem;
        top: 5rem; inset-inline-start: 0;
        background: rgba(var(--edu-primary-rgb), 0.1);
    }
    .th-hero-ambient__blob--2 {
        width: 20rem; height: 20rem;
        bottom: 0; inset-inline-end: 0;
        background: rgba(var(--edu-purple-rgb), 0.07);
    }

    .th-orbit { position: relative; width: 100%; aspect-ratio: 1; max-width: 520px; margin: 0 auto; }
    .th-orbit-ring {
        position: absolute; inset: 4%; border-radius: 50%;
        border: 2px dashed rgba(var(--edu-primary-rgb), .18);
        animation: th-orbit-spin 50s linear infinite;
    }
    @keyframes th-orbit-spin { to { transform: rotate(360deg); } }

    .th-main-photo {
        position: absolute; inset: 5%;
        border-radius: 50%; overflow: hidden;
        border: 6px solid #fff;
        box-shadow: 0 20px 50px -18px rgba(var(--edu-primary-rgb), 0.28);
        z-index: 3;
    }
    .th-main-photo img {
        width: 100%; height: 100%; object-fit: cover;
        object-position: 50% 14%;
        transform: scale(1.28);
    }

    .th-sub-photo {
        position: absolute; width: 30%; aspect-ratio: 1;
        border-radius: 50%; overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 10px 28px -10px rgba(var(--edu-primary-rgb), 0.22);
        z-index: 4;
        background: #fff;
    }
    .th-sub-photo img {
        width: 100%; height: 100%; object-fit: cover;
        object-position: 50% 18%;
    }
    .th-sub-photo--a { bottom: 2%; inset-inline-start: -1%; }
    .th-sub-photo--b { bottom: 5%; inset-inline-end: -3%; }

    .th-deco { position: absolute; pointer-events: none; z-index: 2; }
    .th-deco-plane {
        top: 5%; inset-inline-start: 2%;
        width: 2.75rem; height: 2.75rem; color: var(--edu-accent);
        animation: th-float 6s ease-in-out infinite;
    }
    .th-deco-plane--2 {
        top: 16%; inset-inline-end: 4%; width: 2rem; height: 2rem;
        color: var(--edu-primary); animation-delay: 1.2s;
    }
    @keyframes th-float {
        0%, 100% { transform: translateY(0) rotate(-6deg); }
        50% { transform: translateY(-6px) rotate(4deg); }
    }
    .th-dot { width: 8px; height: 8px; border-radius: 50%; position: absolute; }
    .th-icon-badge {
        width: 2.5rem; height: 2.5rem; border-radius: .75rem;
        display: flex; align-items: center; justify-content: center;
        background: #fff;
        box-shadow: 0 6px 18px -8px rgba(var(--edu-primary-rgb), 0.2);
        font-size: .9rem;
    }

    .th-trust {
        display: inline-flex; align-items: center; gap: .55rem;
        margin-top: 1.25rem; padding: .5rem 1rem;
        border-radius: 999px;
        background: var(--edu-accent-light);
        color: var(--edu-accent-dark);
        font-size: .78rem; font-weight: 700;
    }
    .th-trust i { color: var(--edu-accent); }

    .th-cta-bar {
        display: flex; flex-wrap: wrap; align-items: stretch;
        gap: 0; max-width: 36rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 999px; padding: .35rem;
        box-shadow: 0 4px 16px -6px rgba(15, 23, 42, 0.08);
        margin: 0 auto;
    }
    @media (min-width: 1024px) { .th-cta-bar { margin: 0; margin-inline-start: 0; } }
    .th-cta-bar__field {
        flex: 1 1 10rem; min-width: 0;
        display: flex; align-items: center; gap: .5rem;
        padding: .65rem 1rem; border: none; background: transparent;
        font-size: .88rem; color: var(--edu-muted);
    }
    .th-cta-bar__field i { color: var(--edu-primary); flex-shrink: 0; }
    .th-cta-bar__divider { width: 1px; background: #e2e8f0; align-self: stretch; margin: .5rem 0; }
    .th-cta-bar__btn {
        flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
        padding: .75rem 1.5rem; border-radius: 999px; font-weight: 800; font-size: .88rem;
        color: #fff; background: var(--edu-accent);
        box-shadow: 0 8px 24px -10px rgba(var(--edu-accent-rgb), 0.45);
        transition: transform .2s, background .2s;
        white-space: nowrap; text-decoration: none;
    }
    .th-cta-bar__btn:hover {
        background: var(--edu-accent-dark);
        transform: translateY(-1px);
        color: #fff;
    }
    /* موبايل: زر واحد مضغوط — بدون صندوق CTA */
    #home-hero .home-hero__cta-desktop {
        display: none !important;
    }
    #home-hero .home-hero__cta-hint {
        margin: 0 auto .35rem;
        max-width: 17rem;
        font-size: .68rem;
        line-height: 1.4;
        color: #64748b;
        text-align: center;
    }
    #home-hero .home-hero__cta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        width: auto;
        min-width: 9.5rem;
        max-width: 100%;
        margin-inline: auto;
        padding: .48rem 1.1rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: .78rem;
        color: #fff;
        background: var(--edu-accent);
        box-shadow: 0 4px 14px -8px rgba(var(--edu-accent-rgb), 0.5);
        text-decoration: none;
    }
    #home-hero .home-hero__cta-btn:hover {
        background: var(--edu-accent-dark);
        color: #fff;
    }

    @media (min-width: 1024px) {
        #home-hero .home-hero__cta-hint,
        #home-hero .home-hero__cta-btn {
            display: none !important;
        }
        #home-hero .home-hero__cta-desktop {
            display: flex !important;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        #home-hero .home-hero__cta-hint,
        #home-hero .home-hero__cta-btn {
            display: none !important;
        }
        #home-hero .home-hero__cta-desktop {
            display: flex !important;
            border-radius: 1rem;
            flex-direction: column;
            padding: .4rem;
            max-width: 22rem;
            margin-inline: auto;
        }
        #home-hero .home-hero__cta-desktop .th-cta-bar__divider { display: none; }
        #home-hero .home-hero__cta-desktop .th-cta-bar__btn {
            width: 100%;
            padding: .6rem 1rem;
            font-size: .82rem;
        }
        #home-hero .home-hero__cta-desktop .th-cta-bar__field {
            justify-content: center;
            text-align: center;
            padding: .45rem .6rem;
            font-size: .78rem;
        }
    }

    /* ——— موبايل: الهيرو الرئيسي ——— */
    @media (max-width: 1023px) {
        #home-hero.home-hero {
            overflow-x: clip;
        }
        #home-hero .home-hero__title {
            font-size: clamp(1.35rem, 6.2vw, 1.85rem);
            line-height: 1.4;
        }
        #home-hero .home-hero__visual,
        #home-hero .home-hero__visual-mobile {
            max-width: min(70vw, 240px);
            margin-inline: auto;
        }
        #home-hero .home-hero__orbit {
            max-width: 100%;
        }
        #home-hero .th-sub-photo--a { inset-inline-start: 0; bottom: 4%; }
        #home-hero .th-sub-photo--b { inset-inline-end: 0; bottom: 6%; }
        #home-hero .th-main-photo { border-width: 4px; }
        #home-hero .th-sub-photo { border-width: 3px; width: 28%; }

        #home-hero .home-hero__steps {
            flex-wrap: nowrap;
            justify-content: flex-start;
            overflow-x: auto;
            overflow-y: hidden;
            gap: .45rem;
            padding-bottom: .35rem;
            margin-inline: -.15rem;
            padding-inline: .15rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            mask-image: linear-gradient(90deg, transparent, #000 4%, #000 96%, transparent);
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 4%, #000 96%, transparent);
        }
        #home-hero .home-hero__steps::-webkit-scrollbar { display: none; }
        #home-hero .home-hero__steps .ix-step-pill {
            flex: 0 0 auto;
            white-space: nowrap;
            font-size: .72rem;
            padding: .45rem .7rem;
        }
        #home-hero .home-hero__steps .ix-step-pill__num {
            width: 1.2rem;
            height: 1.2rem;
            font-size: .65rem;
        }

        #home-hero .home-hero__actions {
            flex-direction: column;
            align-items: stretch;
            gap: .5rem;
            width: 100%;
            max-width: 16.5rem;
            margin-inline: auto;
        }
        #home-hero .home-hero__action-btn {
            width: 100%;
            padding: .6rem 1rem;
            font-size: .82rem;
        }
        #home-hero .home-hero__action-link {
            text-align: center;
            padding: .25rem 0;
        }
    }

    @media (max-width: 380px) {
        #home-hero .home-hero__visual {
            max-width: min(84vw, 260px);
        }
        #home-hero .home-hero__steps .ix-step-pill {
            font-size: .68rem;
            padding: .4rem .6rem;
        }
    }

    .ix-orbit-tilt { transition: transform .4s ease-out; }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\tutor\partials\home-hero-styles.blade.php ENDPATH**/ ?>
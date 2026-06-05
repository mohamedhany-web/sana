<style>
    /* نفس أسلوب edu-banner-area في الموقع — بدون خلفية منفصلة */
    #tutor-join.edu-banner-area {
        padding-bottom: 1.5rem;
        margin-bottom: 0;
    }
    @media (min-width: 1024px) {
        #tutor-join.edu-banner-area { padding-bottom: 2rem; }
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
    @media (max-width: 639px) {
        .th-cta-bar { border-radius: 1.25rem; flex-direction: column; padding: .5rem; }
        .th-cta-bar__divider { display: none; }
        .th-cta-bar__btn { width: 100%; }
    }

    .ix-orbit-tilt { transition: transform .4s ease-out; }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/home-hero-styles.blade.php ENDPATH**/ ?>
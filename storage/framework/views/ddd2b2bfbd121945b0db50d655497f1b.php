<style>
    :root {
        --home-space-1: 8px;
        --home-space-2: 16px;
        --home-space-3: 24px;
        --home-space-4: 32px;
        --home-space-5: 48px;
        --home-space-6: 64px;
        --home-radius: 16px;
        --home-radius-lg: 20px;
        --home-shadow: 0 8px 32px -12px rgba(15, 23, 42, .12);
        --home-shadow-hover: 0 16px 40px -14px rgba(15, 23, 42, .18);
    }

    .home-page { background: #fff; }
    .home-page main { padding-top: 72px; }
    @media (min-width: 1024px) { .home-page main { padding-top: 80px; } }

    /* Geometric ambient */
    .home-geo-bg {
        position: absolute;
        inset: 0;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }
    .home-geo-bg::before,
    .home-geo-bg::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .45;
    }
    .home-geo-bg::before {
        width: 280px; height: 280px;
        top: -80px; inset-inline-start: -60px;
        background: radial-gradient(circle, var(--edu-primary-light), transparent 70%);
    }
    .home-geo-bg::after {
        width: 320px; height: 320px;
        bottom: -100px; inset-inline-end: -40px;
        background: radial-gradient(circle, #ede9fe, transparent 70%);
    }
    .home-geo-shape {
        position: absolute;
        border: 1px solid rgba(29, 78, 219, .08);
        border-radius: 24px;
        transform: rotate(-12deg);
        opacity: .6;
    }
    .home-geo-shape--1 { width: 120px; height: 120px; top: 24px; inset-inline-end: 12%; }
    .home-geo-shape--2 { width: 64px; height: 64px; bottom: 32px; inset-inline-start: 8%; border-radius: 50%; }

    /* Discover hero — compact */
    .home-discover {
        position: relative;
        padding: var(--home-space-4) 0 var(--home-space-5);
        border-bottom: 1px solid #f1f5f9;
    }
    .home-discover__inner { position: relative; z-index: 1; max-width: 720px; }
    .home-discover__eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 14px; border-radius: 999px;
        background: var(--edu-primary-light); color: var(--edu-primary);
        font-size: .75rem; font-weight: 700; margin-bottom: var(--home-space-2);
    }
    .home-discover__title {
        font-size: clamp(1.5rem, 4vw, 2.125rem);
        font-weight: 800; line-height: 1.25;
        color: #0f172a; margin: 0 0 var(--home-space-1);
        letter-spacing: -.02em;
    }
    .home-discover__title em { color: var(--edu-primary); font-style: normal; }
    .home-discover__lead {
        font-size: .95rem; color: #64748b; line-height: 1.7;
        margin: 0 0 var(--home-space-3); max-width: 36rem;
    }
    .home-discover__stats {
        display: flex; flex-wrap: wrap; gap: var(--home-space-2);
        margin-top: var(--home-space-3);
        font-size: .8125rem; color: #64748b;
    }
    .home-discover__stat strong { color: #0f172a; font-weight: 800; }

    /* Smart search */
    .home-search-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--home-radius-lg);
        box-shadow: var(--home-shadow);
        padding: var(--home-space-1);
        transition: box-shadow .25s, border-color .25s;
    }
    .home-search-panel:focus-within {
        border-color: rgba(var(--edu-primary-rgb), .35);
        box-shadow: var(--home-shadow-hover);
    }
    .home-search-row {
        display: flex; flex-direction: column; gap: var(--home-space-1);
    }
    @media (min-width: 640px) {
        .home-search-row { flex-direction: row; align-items: stretch; }
    }
    .home-search-input-wrap { position: relative; flex: 1; }
    .home-search-input {
        width: 100%; min-height: 48px;
        padding: 12px 48px 12px 16px;
        border: none; border-radius: 12px;
        font-size: 1rem; background: #f8fafc;
        color: #0f172a;
    }
    .home-search-input:focus { outline: 2px solid var(--edu-primary); outline-offset: 0; background: #fff; }
    .home-search-input-wrap .fa-search {
        position: absolute; top: 50%; transform: translateY(-50%);
        inset-inline-start: 16px; color: #94a3b8; pointer-events: none;
    }
    .home-search-select {
        min-height: 48px; padding: 0 16px;
        border-radius: 12px; border: 1px solid #e2e8f0;
        background: #fff; font-size: .875rem; font-weight: 600;
        color: #334155; min-width: 140px;
    }
    .home-search-btn {
        min-height: 48px; padding: 0 24px;
        border-radius: 12px; border: none;
        background: var(--edu-primary); color: #fff;
        font-weight: 700; font-size: .9rem;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        cursor: pointer; transition: transform .15s, background .2s;
    }
    .home-search-btn:hover { background: var(--edu-primary-dark); transform: translateY(-1px); }
    .home-search-chips {
        display: flex; flex-wrap: wrap; gap: 8px;
        padding: 8px 8px 4px;
    }
    .home-search-chip {
        padding: 6px 12px; border-radius: 999px;
        font-size: .75rem; font-weight: 600;
        background: #f1f5f9; color: #475569;
        border: 1px solid transparent;
        transition: background .2s, color .2s, border-color .2s;
    }
    .home-search-chip:hover {
        background: var(--edu-primary-light);
        color: var(--edu-primary);
        border-color: rgba(var(--edu-primary-rgb), .2);
    }

    /* Section rails */
    .home-section { padding: var(--home-space-5) 0; }
    .home-section--alt { background: #f8fafc; }
    .home-section__head {
        display: flex; flex-wrap: wrap;
        align-items: flex-end; justify-content: space-between;
        gap: var(--home-space-2);
        margin-bottom: var(--home-space-3);
    }
    .home-section__title {
        font-size: clamp(1.25rem, 3vw, 1.5rem);
        font-weight: 800; color: #0f172a; margin: 0;
    }
    .home-section__sub { font-size: .875rem; color: #64748b; margin: 6px 0 0; }
    .home-section__link {
        font-size: .875rem; font-weight: 700; color: var(--edu-primary);
        display: inline-flex; align-items: center; gap: 6px;
        text-decoration: none; transition: gap .2s;
    }
    .home-section__link:hover { gap: 10px; }

    .home-rail {
        display: flex; gap: var(--home-space-2);
        overflow-x: auto; padding-bottom: var(--home-space-1);
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .home-rail::-webkit-scrollbar { height: 6px; }
    .home-rail::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .home-rail > * { scroll-snap-align: start; flex-shrink: 0; }

    .home-grid {
        display: grid;
        gap: var(--home-space-2);
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }

    /* Course card v2 */
    .home-course-card {
        width: 280px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--home-radius);
        overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: 0 2px 8px -4px rgba(15, 23, 42, .08);
        transition: transform .22s, box-shadow .22s, border-color .22s;
    }
    @media (min-width: 1024px) {
        .home-grid .home-course-card { width: auto; }
    }
    .home-course-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--home-shadow-hover);
        border-color: rgba(var(--edu-primary-rgb), .2);
    }
    .home-course-card__media {
        position: relative; aspect-ratio: 16/10;
        background: linear-gradient(135deg, #eff6ff, #f8fafc);
        overflow: hidden;
    }
    .home-course-card__media img {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform .4s ease;
    }
    .home-course-card:hover .home-course-card__media img { transform: scale(1.04); }
    .home-course-card__tag {
        position: absolute; top: 10px; inset-inline-start: 10px;
        padding: 4px 10px; border-radius: 8px;
        font-size: .65rem; font-weight: 800;
        background: rgba(255,255,255,.95); color: #334155;
        z-index: 2;
    }
    .home-course-card__body {
        padding: var(--home-space-2);
        display: flex; flex-direction: column; flex: 1; gap: 8px;
    }
    .home-course-card__meta {
        display: flex; flex-wrap: wrap; gap: 10px;
        font-size: .7rem; color: #94a3b8; font-weight: 600;
    }
    .home-course-card__meta span { display: inline-flex; align-items: center; gap: 4px; }
    .home-course-card__title {
        font-size: .9375rem; font-weight: 800; line-height: 1.45;
        color: #0f172a; margin: 0;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .home-course-card__title a { color: inherit; text-decoration: none; }
    .home-course-card__title a:hover { color: var(--edu-primary); }
    .home-course-card__instructor {
        display: flex; align-items: center; gap: 8px;
        font-size: .75rem; color: #64748b;
    }
    .home-course-card__avatar {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--edu-primary-light); color: var(--edu-primary);
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; font-weight: 800; flex-shrink: 0;
    }
    .home-course-card__progress {
        margin-top: auto;
    }
    .home-course-card__progress-label {
        display: flex; justify-content: space-between;
        font-size: .65rem; font-weight: 700; color: #64748b; margin-bottom: 4px;
    }
    .home-course-card__progress-bar {
        height: 6px; border-radius: 999px; background: #e2e8f0; overflow: hidden;
    }
    .home-course-card__progress-fill {
        height: 100%; border-radius: 999px;
        background: linear-gradient(90deg, var(--edu-primary), var(--edu-purple));
        transition: width .4s ease;
    }
    .home-course-card__foot {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; padding-top: 8px; border-top: 1px solid #f1f5f9;
        margin-top: 4px;
    }
    .home-course-card__rating {
        font-size: .75rem; font-weight: 800; color: #b45309;
    }
    .home-course-card__cta {
        padding: 8px 14px; border-radius: 10px;
        font-size: .75rem; font-weight: 800;
        background: var(--edu-primary); color: #fff;
        text-decoration: none; white-space: nowrap;
        transition: background .2s, transform .15s;
    }
    .home-course-card__cta:hover { background: var(--edu-primary-dark); transform: scale(1.02); }
    .home-course-card__cta--ghost {
        background: var(--edu-primary-light); color: var(--edu-primary);
    }

    /* Path card */
    .home-path-card {
        width: 300px; border-radius: var(--home-radius);
        border: 1px solid #e2e8f0; overflow: hidden;
        background: #fff; transition: transform .22s, box-shadow .22s;
    }
    .home-path-card:hover { transform: translateY(-3px); box-shadow: var(--home-shadow); }
    .home-path-card__img { aspect-ratio: 16/9; object-fit: cover; width: 100%; }
    .home-path-card__body { padding: var(--home-space-2); }
    .home-path-card__title { font-weight: 800; font-size: 1rem; color: #0f172a; margin: 0 0 6px; }
    .home-path-card__meta { font-size: .75rem; color: #64748b; }

    /* Instructor card */
    .home-instructor-card {
        width: 220px; text-align: center;
        padding: var(--home-space-3) var(--home-space-2);
        border-radius: var(--home-radius);
        border: 1px solid #e2e8f0; background: #fff;
        transition: transform .22s, box-shadow .22s;
    }
    .home-instructor-card:hover { transform: translateY(-3px); box-shadow: var(--home-shadow); }
    .home-instructor-card__photo {
        width: 88px; height: 88px; border-radius: 50%;
        object-fit: cover; margin: 0 auto 12px;
        border: 3px solid #fff; box-shadow: 0 4px 16px -4px rgba(15,23,42,.15);
    }
    .home-instructor-card__name { font-weight: 800; font-size: .9375rem; color: #0f172a; margin: 0; }
    .home-instructor-card__headline { font-size: .75rem; color: var(--edu-primary); margin: 4px 0 8px; }
    .home-instructor-card__stats { font-size: .7rem; color: #94a3b8; }

    /* Category icon card */
    .home-cat-card {
        display: flex; align-items: center; gap: var(--home-space-2);
        padding: var(--home-space-2);
        border-radius: var(--home-radius);
        border: 1px solid #e2e8f0; background: #fff;
        text-decoration: none; color: inherit;
        transition: border-color .2s, transform .2s, box-shadow .2s;
    }
    .home-cat-card:hover {
        border-color: var(--cat-accent, var(--edu-primary));
        transform: translateY(-2px);
        box-shadow: var(--home-shadow);
    }
    .home-cat-card__icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.125rem; flex-shrink: 0;
        background: var(--cat-bg, var(--edu-primary-light));
        color: var(--cat-accent, var(--edu-primary));
    }
    .home-cat-card__name { font-weight: 800; font-size: .9rem; color: #0f172a; display: block; }
    .home-cat-card__count { font-size: .75rem; color: #64748b; }

    /* Testimonial */
    .home-testimonial-card {
        width: min(360px, 85vw);
        padding: var(--home-space-3);
        border-radius: var(--home-radius);
        border: 1px solid #e2e8f0; background: #fff;
    }

    /* Certificate / badge showcase */
    .home-cert-card, .home-badge-card {
        padding: var(--home-space-2);
        border-radius: var(--home-radius);
        border: 1px solid #e2e8f0;
        background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
        min-width: 200px;
    }
    .home-badge-card__icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; margin-bottom: 8px;
        background: var(--edu-accent-light); color: var(--edu-accent-dark);
    }

    /* Empty + skeleton */
    .home-empty {
        grid-column: 1 / -1;
        text-align: center; padding: var(--home-space-5);
        color: #64748b; border: 2px dashed #e2e8f0;
        border-radius: var(--home-radius);
    }
    .home-skeleton .home-course-card__media,
    .home-skeleton .home-course-card__title,
    .home-skeleton .home-course-card__instructor {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: home-shimmer 1.2s infinite;
        border-radius: 8px;
        min-height: 1em;
    }
    @keyframes home-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Nav v2 */
    #edu-nav.home-nav { background: rgba(255,255,255,.92); backdrop-filter: blur(12px); }
    #edu-nav.home-nav.is-scrolled { box-shadow: 0 4px 24px -8px rgba(15,23,42,.1); }
    .home-nav__links { gap: 4px; }
    .home-nav__link {
        padding: 8px 12px; border-radius: 10px;
        font-size: .875rem; font-weight: 600; color: #475569;
        transition: color .2s, background .2s;
    }
    .home-nav__link:hover, .home-nav__link.is-active {
        color: var(--edu-primary); background: var(--edu-primary-light);
    }
    .home-profile-menu { position: relative; }
    .home-profile-menu__btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 12px 6px 6px; border-radius: 999px;
        border: 1px solid #e2e8f0; background: #fff;
        font-size: .8125rem; font-weight: 700; color: #334155;
        cursor: pointer;
    }
    .home-profile-menu__dropdown {
        position: absolute; top: calc(100% + 8px);
        inset-inline-end: 0; min-width: 200px;
        background: #fff; border: 1px solid #e2e8f0;
        border-radius: 14px; box-shadow: var(--home-shadow-hover);
        padding: 8px; display: none; z-index: 50;
    }
    .home-profile-menu.is-open .home-profile-menu__dropdown { display: block; }
    .home-profile-menu__dropdown a {
        display: flex; align-items: center; gap: 8px;
        padding: 10px 12px; border-radius: 10px;
        font-size: .875rem; font-weight: 600; color: #334155;
        text-decoration: none;
    }
    .home-profile-menu__dropdown a:hover { background: #f8fafc; color: var(--edu-primary); }

    .reveal { opacity: 0; transform: translateY(16px); transition: opacity .5s ease, transform .5s ease; }
    .reveal.revealed { opacity: 1; transform: none; }

    .sr-only {
        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
        overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
    }
    .sr-only:focus {
        position: fixed; width: auto; height: auto; margin: 0; clip: auto; white-space: normal;
    }

    @media (prefers-reduced-motion: reduce) {
        .reveal { opacity: 1; transform: none; transition: none; }
        .home-course-card:hover, .home-path-card:hover { transform: none; }
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/partials/home-v2-styles.blade.php ENDPATH**/ ?>
<style>
    @include('landing.eduvalt.brand-vars')

    * { font-family: var(--edu-font); }
    html { scroll-behavior: smooth; }
    html[dir="rtl"] { direction: rtl; }
    body {
        background: #fff;
        color: var(--edu-text);
        overflow-x: hidden;
        direction: rtl;
        text-align: right;
    }
    html[dir="rtl"] input:not([dir="ltr"]),
    html[dir="rtl"] textarea:not([dir="ltr"]),
    html[dir="rtl"] select:not([dir="ltr"]) { text-align: right; }
    html[dir="rtl"] .edu-search { padding: .65rem 2.75rem .65rem 1rem; }

    .edu-container { max-width: 1280px; margin-inline: auto; padding-inline: 1.25rem; }
    @media (min-width: 1024px) { .edu-container { padding-inline: 2rem; } }

    /* عناوين بخط تحتياني مثل Eduvalt */
    .edu-title-mark {
        position: relative;
        display: inline-block;
        color: var(--edu-primary);
        font-weight: inherit;
    }
    .edu-title-curve {
        position: absolute;
        inset-inline: 0;
        bottom: -6px;
        width: 100%;
        height: 12px;
        color: var(--edu-primary);
        opacity: .85;
        pointer-events: none;
    }
    .edu-btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .85rem 1.75rem; border-radius: 999px; font-weight: 700; font-size: .95rem;
        color: #fff; background: var(--edu-primary);
        box-shadow: 0 8px 24px -8px rgba(var(--edu-primary-rgb), .5);
        transition: transform .2s, background .2s, box-shadow .2s;
    }
    .edu-btn-primary:hover { background: var(--edu-primary-dark); transform: translateY(-2px); }

    .edu-btn-outline {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .8rem 1.5rem; border-radius: 999px; font-weight: 700; font-size: .9rem;
        color: var(--edu-primary); border: 2px solid var(--edu-primary); background: #fff;
        transition: background .2s, color .2s;
    }
    .edu-btn-outline:hover { background: var(--edu-primary-light); }

    .edu-btn-white {
        display: inline-flex; align-items: center; gap: .5rem; padding: .85rem 1.75rem;
        border-radius: 999px; font-weight: 700; background: #fff; color: var(--edu-primary);
        transition: transform .2s, color .2s, background .2s;
    }
    .edu-btn-white:hover {
        transform: translateY(-2px);
        color: var(--edu-primary-dark);
        background: #fff;
    }
    /* داخل بانر CTA النص الأبيض على الأب لا يورّث لزر الخلفية البيضاء */
    .edu-cta-wrap .edu-btn-white,
    .edu-cta-wrap .edu-btn-white i {
        color: var(--edu-primary);
    }
    .edu-cta-wrap .edu-btn-white:hover,
    .edu-cta-wrap .edu-btn-white:hover i {
        color: var(--edu-primary-dark);
    }

    /* زر ثانوي على خلفية CTA الملوّنة — حدود بيضاء ونص أبيض (بدون خلفية بيضاء) */
    .edu-btn-ghost-light {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        padding: .8rem 1.5rem; border-radius: 999px; font-weight: 700; font-size: .9rem;
        color: #fff; border: 2px solid rgba(255, 255, 255, .8);
        background: transparent;
        transition: background .2s, border-color .2s, transform .2s;
    }
    .edu-btn-ghost-light:hover {
        background: rgba(255, 255, 255, .14);
        border-color: #fff;
        color: #fff;
        transform: translateY(-2px);
    }
    .edu-cta-wrap .edu-btn-outline {
        background: transparent;
        color: #fff;
        border-color: rgba(255, 255, 255, .8);
    }
    .edu-cta-wrap .edu-btn-outline:hover {
        background: rgba(255, 255, 255, .14);
        color: #fff;
        border-color: #fff;
    }

    .edu-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .4rem 1rem; border-radius: 999px; font-size: .8rem; font-weight: 700;
        background: var(--edu-primary-light); color: var(--edu-primary);
    }

    .edu-card {
        background: #fff; border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px -8px rgba(15, 23, 42, .08);
        transition: transform .3s, box-shadow .3s;
    }
    .edu-card:hover { transform: translateY(-6px); box-shadow: var(--edu-shadow); }

    .edu-cat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.15rem 1.25rem;
        background: #fff;
        border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -10px rgba(15, 23, 42, .1);
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        position: relative;
        overflow: hidden;
    }
    .edu-cat-card::before {
        content: '';
        position: absolute;
        inset-inline: 0;
        top: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--cat-accent, var(--edu-primary)), transparent);
        opacity: 0;
        transition: opacity .25s ease;
    }
    .edu-cat-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--cat-accent, var(--edu-primary)) 35%, #e2e8f0);
        box-shadow: 0 14px 36px -14px color-mix(in srgb, var(--cat-accent, var(--edu-primary)) 28%, transparent);
    }
    .edu-cat-card:hover::before { opacity: 1; }
    .edu-cat-icon {
        flex-shrink: 0;
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: var(--cat-accent, var(--edu-primary));
        background: var(--cat-bg, var(--edu-primary-light));
        transition: transform .25s ease;
    }
    .edu-cat-card:hover .edu-cat-icon { transform: scale(1.08); }
    .edu-cat-body { flex: 1; min-width: 0; text-align: start; }
    .edu-cat-name {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-weight: 800;
        font-size: .95rem;
        color: #0f172a;
        line-height: 1.4;
        margin-bottom: .35rem;
    }
    .edu-cat-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }
    .edu-cat-count {
        font-size: .75rem;
        font-weight: 700;
        color: #64748b;
        background: #f1f5f9;
        padding: .2rem .65rem;
        border-radius: 999px;
    }
    .edu-cat-arrow {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .7rem;
        color: var(--cat-accent, var(--edu-primary));
        background: var(--cat-bg, var(--edu-primary-light));
        opacity: 0;
        transform: translateX(6px);
        transition: opacity .25s ease, transform .25s ease;
    }
    html[dir="rtl"] .edu-cat-arrow { transform: translateX(-6px); }
    .edu-cat-card:hover .edu-cat-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .edu-glass {
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, .95);
        box-shadow: 0 8px 32px -12px rgba(var(--edu-primary-rgb), .18);
        border-radius: var(--edu-radius-sm);
    }

    .edu-section-title { font-size: clamp(1.75rem, 4vw, 2.35rem); font-weight: 800; line-height: 1.35; }
    .edu-highlight { color: var(--edu-primary); }
    .edu-sub-title { display: block; font-size: .85rem; font-weight: 700; color: var(--edu-primary); margin-bottom: .5rem; }

    .edu-nav {
        background: rgba(255, 255, 255, .94);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(226, 232, 240, .85);
    }
    .edu-nav.is-scrolled { box-shadow: 0 4px 24px -8px rgba(15, 23, 42, .08); }

    .edu-nav__links {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.25rem 0.125rem;
    }
    .edu-nav-link {
        padding: 0.5rem 0.7rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--edu-muted);
        border-radius: 10px;
        transition: color 0.2s, background 0.2s;
        white-space: nowrap;
    }
    .edu-nav-link:hover { color: var(--edu-primary); background: var(--edu-primary-light); }

    .edu-search {
        width: 100%; max-width: 280px; padding: .65rem 2.75rem .65rem 1rem;
        border-radius: 999px; border: 1px solid #e2e8f0; background: #f8fafc;
        font-size: .875rem;
    }
    .edu-search:focus {
        outline: none; border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12);
    }

    /* Hero Eduvalt */
    .edu-banner-area {
        background: linear-gradient(180deg, #fff 0%, var(--edu-primary-light) 52%, #fff 100%);
    }
    .edu-hero-actions {
        display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;
        justify-content: center;
    }
    @media (min-width: 1024px) { .edu-hero-actions { justify-content: flex-start; } }

    .edu-hero-phone {
        display: inline-flex; align-items: center; gap: .75rem;
        padding: .65rem 1.1rem; border-radius: 999px;
        background: #fff; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -6px rgba(15, 23, 42, .08);
    }
    .edu-hero-phone i {
        width: 2.5rem; height: 2.5rem; border-radius: 50%;
        background: var(--edu-primary-light); color: var(--edu-primary);
        display: flex; align-items: center; justify-content: center;
    }

    .edu-banner-facts {
        position: absolute;
        bottom: 1.5rem;
        inset-inline-start: -1rem;
        display: flex; flex-direction: column; gap: .65rem;
        z-index: 20;
    }
    .edu-banner-fact {
        display: flex; align-items: center; gap: .75rem;
        padding: .75rem 1rem; border-radius: var(--edu-radius-sm);
        background: #fff; box-shadow: 0 10px 30px -12px rgba(var(--edu-primary-rgb), .25);
        min-width: 11rem;
    }
    .edu-banner-fact .icon {
        width: 2.5rem; height: 2.5rem; border-radius: 12px;
        background: var(--edu-primary-light); color: var(--edu-primary);
        display: flex; align-items: center; justify-content: center;
    }
    .edu-banner-fact .count { font-size: 1.15rem; font-weight: 800; color: var(--edu-navy); line-height: 1.1; }
    .edu-banner-fact .label { font-size: .7rem; color: var(--edu-muted); }

    /* شريط العلامات */
    .edu-brand-marquee {
        overflow: hidden; mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
    }
    .edu-brand-track {
        display: flex; gap: 3rem; width: max-content;
        animation: eduBrandScroll 28s linear infinite;
    }
    .edu-brand-track:hover { animation-play-state: paused; }
    .edu-brand-item {
        flex-shrink: 0; font-weight: 800; font-size: 1.15rem;
        color: #94a3b8; filter: grayscale(1); opacity: .55;
        white-space: nowrap;
    }
    @keyframes eduBrandScroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(50%); }
    }
    html[dir="rtl"] .edu-brand-track {
        animation-name: eduBrandScrollRtl;
    }
    @keyframes eduBrandScrollRtl {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* About شارة الخبرة */
    .edu-about-exp {
        position: absolute; top: 1rem; inset-inline-end: -1rem;
        background: var(--edu-primary); color: #fff;
        padding: 1rem 1.15rem; border-radius: 0 20px 20px 20px;
        box-shadow: 0 12px 28px -8px rgba(var(--edu-primary-rgb), .45);
        z-index: 5; text-align: center;
    }
    .edu-about-exp .year { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .edu-about-exp p { font-size: .7rem; margin-top: .25rem; opacity: .9; }

    /* أشكال الدورات */
    .edu-courses-wrap { position: relative; }
    .edu-course-shape {
        position: absolute; pointer-events: none; opacity: .35;
        animation: eduFloat 6s ease-in-out infinite;
    }
    .edu-course-shape.s1 { top: -2rem; inset-inline-end: 5%; width: 4rem; }
    .edu-course-shape.s2 { bottom: 2rem; inset-inline-start: 3%; width: 5rem; animation-delay: -3s; }

    /* CTA */
    .edu-cta-wrap {
        border-radius: var(--edu-radius);
        background: var(--edu-gradient-cta);
        position: relative; overflow: hidden;
    }
    .edu-cta-object {
        position: absolute; border-radius: 50%; background: rgba(255,255,255,.12);
        pointer-events: none;
    }

    /* Newsletter */
    .edu-newsletter-wrap {
        background: linear-gradient(135deg, var(--edu-primary-light) 0%, #fff 100%);
        border-radius: var(--edu-radius);
        border: 1px solid #e2e8f0;
    }

    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.revealed { opacity: 1; transform: translateY(0); }

    .edu-tag { font-size: .7rem; font-weight: 700; padding: .25rem .65rem; border-radius: 8px; }
    .edu-tag-green { background: #dcfce7; color: #166534; }
    .edu-tag-orange { background: #ffedd5; color: #9a3412; }
    .edu-tag-purple { background: var(--edu-purple-light); color: var(--edu-purple-dark); }
    .edu-tag-blue { background: var(--edu-primary-light); color: var(--edu-primary-dark); }
    .edu-tag-accent { background: var(--edu-accent-light); color: var(--edu-accent-dark); }

    .edu-course-fav-btn {
        position: absolute;
        top: .75rem;
        inset-inline-end: .75rem;
        z-index: 12;
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 999px;
        border: none;
        background: rgba(255, 255, 255, .95);
        color: #94a3b8;
        box-shadow: 0 4px 14px -6px rgba(15, 23, 42, .25);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .2s, color .2s, background .2s;
    }
    .edu-course-fav-btn:hover { transform: scale(1.08); color: var(--edu-accent-dark); }
    .edu-course-fav-btn.is-saved, .edu-course-fav-btn.is-saved:hover { color: #e11d48; background: #fff; }
    .edu-fav-toast {
        position: fixed; bottom: 1.5rem; inset-inline-start: 50%;
        transform: translateX(-50%) translateY(120%);
        z-index: 10050; max-width: min(92vw, 24rem);
        padding: .75rem 1.25rem; border-radius: 999px;
        background: var(--edu-navy); color: #fff;
        font-size: .85rem; font-weight: 600; text-align: center;
        box-shadow: 0 12px 40px -12px rgba(15, 23, 42, .4);
        opacity: 0; transition: transform .35s ease, opacity .35s ease; pointer-events: none;
    }
    html[dir="rtl"] .edu-fav-toast { transform: translateX(50%) translateY(120%); }
    .edu-fav-toast.is-visible { opacity: 1; transform: translateX(-50%) translateY(0); }
    html[dir="rtl"] .edu-fav-toast.is-visible { transform: translateX(50%) translateY(0); }

    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }

    @keyframes eduFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .edu-float { animation: eduFloat 5s ease-in-out infinite; }
    .edu-float-delay { animation-delay: -2.5s; }

    #scroll-progress {
        position: fixed; top: 0; inset-inline-end: 0; height: 3px; width: 0;
        z-index: 10001; background: var(--edu-primary);
    }

    #edu-preloader {
        position: fixed; inset: 0; z-index: 10002;
        background: #fff; display: flex; align-items: center; justify-content: center;
        transition: opacity .4s, visibility .4s;
    }
    #edu-preloader.is-done { opacity: 0; visibility: hidden; pointer-events: none; }
    .edu-preloader-spinner {
        width: 48px; height: 48px; border: 3px solid var(--edu-primary-light);
        border-top-color: var(--edu-primary); border-radius: 50%;
        animation: eduSpin .8s linear infinite;
    }
    @keyframes eduSpin { to { transform: rotate(360deg); } }
</style>

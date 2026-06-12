<style>
    .sanua-dash {
        --p-purple: #6D28D9;
        --p-lavender: #EDE9FE;
        --p-gold: #FBBF24;
        --p-green: #22C55E;
        --p-blue: #3B82F6;
        --p-bg: #F8F7FC;
        --gap-section: 32px;
        --gap-card: 24px;
        --gap-comp: 16px;
        --radius: 24px;
        width: 100%;
        max-width: none;
        font-family: 'Cairo', 'Tajawal', sans-serif;
    }

    .sanua-section { margin-bottom: var(--gap-section); }
    .sanua-section-title {
        font-size: 1.35rem;
        font-weight: 900;
        color: #1e1b4b;
        margin: 0 0 var(--gap-comp);
    }

    /* ═══════════════════════════════════════
       HERO — شخصية معزولة + ديكور CSS + نص
       ═══════════════════════════════════════ */
    .sanua-hero-wrap {
        margin-bottom: var(--gap-comp);
        width: 100%;
    }

    .sanua-hero {
        display: grid;
        grid-template-columns: minmax(0, 0.55fr) minmax(0, 0.45fr);
        direction: ltr;
        width: 100%;
        aspect-ratio: 1280 / 300;
        min-height: 240px;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: 0 12px 40px -16px rgba(91, 33, 182, 0.45);
        background:
            radial-gradient(circle at 12% 85%, rgba(251, 191, 36, 0.14) 0%, transparent 42%),
            radial-gradient(circle at 58% 15%, rgba(255, 255, 255, 0.1) 0%, transparent 38%),
            radial-gradient(circle at 88% 70%, rgba(167, 139, 250, 0.2) 0%, transparent 35%),
            linear-gradient(135deg, #5B21B6 0%, #6D28D9 48%, #7C3AED 100%);
        position: relative;
        isolation: isolate;
    }

    /* ديكور الكارد */
    .sanua-hero__deco {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 1;
        overflow: hidden;
    }
    .sanua-hero__cloud {
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        box-shadow:
            -18px 0 0 -4px rgba(255, 255, 255, 0.08),
            18px 0 0 -4px rgba(255, 255, 255, 0.08),
            -36px 4px 0 -8px rgba(255, 255, 255, 0.06);
    }
    .sanua-hero__blob {
        position: absolute;
        border-radius: 50%;
        background: rgba(139, 92, 246, 0.22);
    }
    .sanua-hero__blob--1 {
        width: 140px;
        height: 140px;
        bottom: -40px;
        right: 22%;
    }
    .sanua-hero__blob--2 {
        width: 90px;
        height: 90px;
        top: -20px;
        right: 8%;
        background: rgba(251, 191, 36, 0.12);
    }
    .sanua-hero__blob--3 {
        width: 70px;
        height: 70px;
        bottom: 20%;
        left: 46%;
        background: rgba(255, 255, 255, 0.08);
    }
    .sanua-hero__ring {
        position: absolute;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.12);
    }
    .sanua-hero__ring--1 {
        width: 56px;
        height: 56px;
        top: 18%;
        left: 44%;
        animation: sanuaSpin 18s linear infinite;
    }
    .sanua-hero__ring--2 {
        width: 36px;
        height: 36px;
        bottom: 22%;
        right: 6%;
        border-color: rgba(251, 191, 36, 0.25);
        animation: sanuaSpin 14s linear infinite reverse;
    }
    .sanua-hero__dot {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
    }
    .sanua-hero__dot--1 { width: 8px; height: 8px; top: 28%; left: 50%; }
    .sanua-hero__dot--2 { width: 6px; height: 6px; top: 62%; right: 12%; background: rgba(251, 191, 36, 0.7); }
    .sanua-hero__dot--3 { width: 5px; height: 5px; bottom: 18%; left: 38%; opacity: 0.6; }
    .sanua-hero__cross {
        position: absolute;
        width: 14px;
        height: 14px;
        opacity: 0.45;
    }
    .sanua-hero__cross::before,
    .sanua-hero__cross::after {
        content: '';
        position: absolute;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 2px;
    }
    .sanua-hero__cross::before { width: 2px; height: 14px; left: 6px; top: 0; }
    .sanua-hero__cross::after { width: 14px; height: 2px; left: 0; top: 6px; }
    .sanua-hero__cross--1 { top: 12%; right: 14%; animation: sanuaBob 5s ease-in-out infinite; }
    .sanua-hero__cross--2 { bottom: 30%; left: 42%; animation: sanuaBob 4.2s ease-in-out infinite 0.8s; }
    .sanua-hero__spark {
        position: absolute;
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.75rem;
        line-height: 1;
        animation: sanuaBob 4s ease-in-out infinite;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.35);
    }
    .sanua-hero__spark--g1 { top: 20%; left: 48%; font-size: 0.65rem; animation-delay: 0.2s; }
    .sanua-hero__spark--g2 { top: 48%; right: 10%; animation-delay: 0.9s; }
    .sanua-hero__spark--g3 { bottom: 14%; left: 52%; font-size: 0.55rem; animation-delay: 1.4s; }
    .sanua-hero__star-icon {
        position: absolute;
        line-height: 1;
        filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.45));
        animation: sanuaBob 3.5s ease-in-out infinite;
    }
    .sanua-hero__star-icon--g1 {
        top: 8%;
        right: 5%;
        font-size: 0.95rem;
        animation-delay: 0.4s;
    }
    .sanua-hero__bulb {
        position: absolute;
        font-size: 1.35rem;
        line-height: 1;
        filter: drop-shadow(0 0 12px rgba(251, 191, 36, 0.55));
        animation: sanuaBob 3.2s ease-in-out infinite;
    }
    .sanua-hero__mini-circle {
        position: absolute;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.06);
    }
    @keyframes sanuaBob {
        0%, 100% { transform: translateY(0); opacity: 0.72; }
        50% { transform: translateY(-5px); opacity: 1; }
    }
    @keyframes sanuaSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* شخصية + ديكور حولها */
    .sanua-hero__visual {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 0 4px 0 8px;
        min-height: 0;
        overflow: hidden;
        background: transparent;
    }
    .sanua-hero__visual-deco {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        overflow: hidden;
    }
    .sanua-hero__cloud--a {
        width: 120px;
        height: 46px;
        top: 6%;
        left: 4%;
        opacity: 0.9;
    }
    .sanua-hero__cloud--b {
        width: 96px;
        height: 38px;
        top: 20%;
        right: 6%;
        background: rgba(196, 181, 253, 0.18);
    }
    .sanua-hero__cloud--c {
        width: 140px;
        height: 52px;
        bottom: 14%;
        left: 10%;
        background: rgba(255, 255, 255, 0.07);
        opacity: 0.8;
    }
    .sanua-hero__cloud--d {
        width: 72px;
        height: 28px;
        top: 38%;
        left: 2%;
        opacity: 0.65;
    }
    .sanua-hero__cloud--e {
        width: 100px;
        height: 40px;
        bottom: 28%;
        right: 2%;
        background: rgba(167, 139, 250, 0.14);
    }
    .sanua-hero__spark--a { top: 4%; left: 32%; font-size: 1rem; animation-delay: 0s; }
    .sanua-hero__spark--b { top: 16%; right: 18%; animation-delay: 0.5s; }
    .sanua-hero__spark--c { top: 38%; left: 6%; font-size: 0.55rem; animation-delay: 1s; }
    .sanua-hero__spark--d { bottom: 28%; right: 8%; font-size: 0.7rem; animation-delay: 1.5s; }
    .sanua-hero__spark--e { bottom: 8%; left: 28%; font-size: 0.6rem; animation-delay: 2s; }
    .sanua-hero__star-icon--a {
        top: 2%;
        right: 12%;
        font-size: 1.25rem;
        animation-delay: 0.3s;
    }
    .sanua-hero__star-icon--b {
        bottom: 34%;
        right: 2%;
        font-size: 0.9rem;
        animation-delay: 0.8s;
    }
    .sanua-hero__star-icon--c {
        top: 30%;
        left: 18%;
        font-size: 0.75rem;
        animation-delay: 1.2s;
    }
    .sanua-hero__bulb {
        top: 10%;
        left: 2%;
        animation-delay: 0.5s;
    }
    .sanua-hero__mini-circle--1 {
        width: 28px;
        height: 28px;
        top: 24%;
        right: 28%;
        animation: sanuaBob 4.5s ease-in-out infinite;
    }
    .sanua-hero__mini-circle--2 {
        width: 18px;
        height: 18px;
        bottom: 20%;
        left: 22%;
        border-color: rgba(251, 191, 36, 0.35);
        animation: sanuaBob 3.8s ease-in-out infinite 0.6s;
    }
    .sanua-hero__char-glow {
        position: absolute;
        z-index: 2;
        bottom: 4%;
        left: 50%;
        width: 72%;
        height: 22%;
        transform: translateX(-50%);
        border-radius: 50%;
        background: radial-gradient(ellipse, rgba(76, 29, 149, 0.5) 0%, transparent 70%);
        pointer-events: none;
    }
    .sanua-hero__visual img {
        position: relative;
        z-index: 3;
        width: auto;
        height: 132%;
        max-height: none;
        max-width: 118%;
        object-fit: contain;
        object-position: bottom center;
        display: block;
        background: transparent !important;
        filter: drop-shadow(0 12px 26px rgba(0, 0, 0, 0.24));
        transform: translateY(8%);
    }

    /* نص — يمين */
    .sanua-hero__content {
        direction: rtl;
        position: relative;
        z-index: 4;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: clamp(24px, 3.5vw, 40px) clamp(28px, 4vw, 48px) clamp(24px, 3.5vw, 40px) clamp(16px, 2vw, 28px);
        gap: 0;
    }
    .sanua-hero__hello {
        margin: 0 0 10px;
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.65);
        letter-spacing: 0.01em;
    }
    .sanua-hero__title {
        margin: 0 0 14px;
        line-height: 1.15;
        max-width: 22rem;
    }
    .sanua-hero__title .line-white,
    .sanua-hero__title .line-gold {
        font-size: clamp(1.5rem, 3vw, 2.15rem);
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .sanua-hero__title .line-white {
        color: #fff;
    }
    .sanua-hero__title .line-gold {
        color: #FBBF24;
    }
    .sanua-hero__sub {
        margin: 0 0 22px;
        font-size: clamp(0.8rem, 1.35vw, 0.92rem);
        font-weight: 500;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.75;
        max-width: 21rem;
    }
    .sanua-hero__cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 13px 26px;
        border-radius: 999px;
        background: #FBBF24;
        color: #5B21B6;
        font-weight: 800;
        font-size: clamp(0.85rem, 1.25vw, 0.95rem);
        text-decoration: none !important;
        box-shadow: 0 6px 20px -4px rgba(0, 0, 0, 0.18);
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .sanua-hero__cta:hover {
        transform: translateY(-2px);
        background: #FCD34D;
        box-shadow: 0 10px 28px -6px rgba(0, 0, 0, 0.22);
        color: #4C1D95;
    }
    .sanua-hero__cta i { font-size: 0.8rem; }

    /* شريط إحصائيات */
    .sanua-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: var(--gap-section);
        width: 100%;
        direction: rtl;
    }
    .sanua-stat-pill {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        gap: 14px;
        padding: 16px 18px;
        min-height: 88px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 4px 20px -6px rgba(109, 40, 217, 0.1);
        text-align: right;
    }
    .sanua-stat-pill__icon {
        width: 56px;
        height: 56px;
        min-width: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 6px 16px -6px rgba(0, 0, 0, 0.2);
    }
    .sanua-stat-pill__icon svg {
        width: 28px;
        height: 28px;
        display: block;
        fill: #fff;
        flex-shrink: 0;
    }
    .sanua-stat-pill__icon--purple {
        background: linear-gradient(135deg, #6D28D9, #8B5CF6);
    }
    .sanua-stat-pill__icon--gold {
        background: linear-gradient(135deg, #FBBF24, #F59E0B);
    }
    .sanua-stat-pill__icon--green {
        background: linear-gradient(135deg, #22C55E, #16A34A);
    }
    .sanua-stat-pill__icon--amber {
        background: linear-gradient(135deg, #FBBF24, #EAB308);
    }
    .sanua-stat-pill__body {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
        flex: 1;
        min-width: 0;
    }
    .sanua-stat-pill strong {
        display: block;
        font-size: 1.2rem;
        font-weight: 900;
        color: #1e1b4b;
        line-height: 1.15;
    }
    .sanua-stat-pill span {
        display: block;
        font-size: 0.74rem;
        font-weight: 700;
        color: #64748b;
    }

    @media (max-width: 900px) {
        .sanua-stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 767px) {
        .sanua-hero {
            grid-template-columns: 1fr;
            aspect-ratio: auto;
            min-height: auto;
        }
        .sanua-hero__content {
            order: 1;
            padding: 24px 20px 16px;
            text-align: center;
            align-items: center;
        }
        .sanua-hero__sub { max-width: none; }
        .sanua-hero__cta { align-self: center; }
        .sanua-hero__visual {
            order: 2;
            height: 280px;
            overflow: hidden;
            padding: 0 12px;
        }
        .sanua-hero__visual img {
            height: 118%;
            max-width: 115%;
            transform: translateY(6%);
        }
        .sanua-hero__bulb { font-size: 1.15rem; top: 8%; left: 6%; }
        .sanua-hero__star-icon--a { top: 2%; right: 10%; }
        .sanua-hero__ring--1,
        .sanua-hero__ring--2 { display: none; }
        .sanua-stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .sanua-stat-pill { min-height: 80px; padding: 14px 12px; gap: 10px; }
        .sanua-stat-pill__icon { width: 48px; height: 48px; min-width: 48px; }
        .sanua-stat-pill__icon svg { width: 24px; height: 24px; }
        .sanua-stat-pill strong { font-size: 1.05rem; }
    }

    /* ═══ تحدي أسبوعي ═══ */
    .sanua-challenge {
        display: flex;
        align-items: center;
        gap: var(--gap-comp);
        padding: 14px 18px;
        border-radius: 18px;
        background: linear-gradient(90deg, #FEF3C7, #FDE68A);
        border: 1px solid #FCD34D;
        margin-bottom: var(--gap-section);
        width: 100%;
    }
    .sanua-challenge__icon { font-size: 1.75rem; }
    .sanua-challenge__text { flex: 1; min-width: 0; }
    .sanua-challenge__text strong {
        display: block;
        font-size: 0.88rem;
        font-weight: 900;
        color: #92400E;
    }
    .sanua-challenge__text span {
        font-size: 0.72rem;
        color: #B45309;
        font-weight: 600;
    }
    .sanua-challenge__btn {
        padding: 8px 16px;
        border-radius: 12px;
        background: #fff;
        color: #D97706;
        font-weight: 800;
        font-size: 0.75rem;
        text-decoration: none !important;
        white-space: nowrap;
        box-shadow: 0 4px 12px -4px rgba(0,0,0,0.15);
    }

    /* ═══ بطاقات المواد 220px — 12-col grid ═══ */
    .sanua-subjects-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: var(--gap-card);
        width: 100%;
    }
    .sanua-subjects-grid .sanua-subject-card { grid-column: span 4; }
    @media (max-width: 1023px) {
        .sanua-subjects-grid .sanua-subject-card { grid-column: span 6; }
    }
    @media (max-width: 767px) {
        .sanua-subjects-grid { grid-template-columns: 1fr; }
        .sanua-subjects-grid .sanua-subject-card { grid-column: span 1; }
    }

    .sanua-subject-card {
        display: grid;
        grid-template-rows: 40% 40% 20%;
        height: 220px;
        padding: 0;
        border-radius: 22px;
        text-decoration: none !important;
        color: inherit;
        overflow: hidden;
        position: relative;
        transition: transform 0.25s cubic-bezier(0.34, 1.4, 0.64, 1), box-shadow 0.25s;
        box-shadow: 0 8px 28px -10px rgba(0, 0, 0, 0.12);
    }
    .sanua-subject-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 48px -16px rgba(0, 0, 0, 0.18);
    }
    .sanua-subject-card--math { background: linear-gradient(160deg, #EDE9FE 0%, #C4B5FD 100%); }
    .sanua-subject-card--english { background: linear-gradient(160deg, #DBEAFE 0%, #93C5FD 100%); }
    .sanua-subject-card--science { background: linear-gradient(160deg, #D1FAE5 0%, #6EE7B7 100%); }

    .sanua-subject-card__illus {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        line-height: 1;
        filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.12));
        padding-top: 8px;
    }
    .sanua-subject-card__info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0 20px;
    }
    .sanua-subject-card__info h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-subject-card__info .meta {
        margin: 4px 0 0;
        font-size: 0.78rem;
        font-weight: 700;
        color: rgba(30, 27, 75, 0.55);
    }
    .sanua-subject-card__progress {
        padding: 0 20px 16px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .sanua-subject-bar-track {
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.55);
        overflow: hidden;
    }
    .sanua-subject-bar-fill { height: 100%; border-radius: 999px; transition: width 0.6s; }
    .sanua-subject-card--math .sanua-subject-bar-fill { background: linear-gradient(90deg, #8B5CF6, #6D28D9); }
    .sanua-subject-card--english .sanua-subject-bar-fill { background: linear-gradient(90deg, #60A5FA, #2563EB); }
    .sanua-subject-card--science .sanua-subject-bar-fill { background: linear-gradient(90deg, #34D399, #059669); }
    .sanua-subject-pct {
        margin: 6px 0 0;
        font-size: 0.68rem;
        font-weight: 900;
        color: rgba(30, 27, 75, 0.6);
    }

    /* ═══ العب وتعلّم — responsive grid ═══ */
    .sanua-play-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: var(--gap-comp);
        width: 100%;
    }
    .sanua-play-grid .sanua-play-tile { grid-column: span 3; }
    @media (max-width: 1023px) {
        .sanua-play-grid .sanua-play-tile { grid-column: span 4; }
    }
    @media (max-width: 639px) {
        .sanua-play-grid .sanua-play-tile { grid-column: span 6; }
    }

    .sanua-play-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 110px;
        padding: 16px 12px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.34, 1.4, 0.64, 1);
        box-shadow: 0 4px 16px -6px rgba(109, 40, 217, 0.1);
    }
    .sanua-play-tile:hover {
        transform: translateY(-6px) scale(1.03);
        border-color: #C4B5FD;
        box-shadow: 0 16px 40px -12px rgba(109, 40, 217, 0.25);
        background: linear-gradient(180deg, #fff, #FAFAFF);
    }
    .sanua-play-emoji {
        font-size: 2rem;
        line-height: 1;
        transition: transform 0.22s;
    }
    .sanua-play-tile:hover .sanua-play-emoji { transform: scale(1.15); }
    .sanua-play-lbl {
        font-size: 0.75rem;
        font-weight: 800;
        color: #475569;
        text-align: center;
    }

    .sanua-sub-banner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 4px 16px -6px rgba(109, 40, 217, 0.1);
    }

    @media (max-width: 767px) {
        .sanua-col-4, .sanua-col-6, .sanua-col-3 { grid-column: span 12; }
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\dashboard\partials\sanua-theme.blade.php ENDPATH**/ ?>
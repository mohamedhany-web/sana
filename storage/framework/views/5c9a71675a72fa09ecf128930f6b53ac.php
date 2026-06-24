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
        gap: 14px;
        padding: 16px 18px;
        min-height: 88px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 4px 20px -6px rgba(109, 40, 217, 0.1);
        text-align: right;
        overflow: hidden;
        direction: rtl;
    }
    .sanua-stat-pill__icon {
        width: 56px;
        height: 56px;
        min-width: 56px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 6px 16px -6px rgba(0, 0, 0, 0.2);
        line-height: 0;
        direction: ltr;
    }
    .sanua-stat-pill__icon svg,
    .sanua-stat-pill__icon i {
        width: 1.35rem;
        height: 1.35rem;
        font-size: 1.35rem;
        display: block;
        fill: #fff;
        color: #fff;
        flex-shrink: 0;
        line-height: 1;
    }
    .sanua-stat-pill__icon svg {
        width: 28px;
        height: 28px;
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
    .sanua-stat-pill__icon--red {
        background: linear-gradient(135deg, #DC2626, #EF4444);
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
    .sanua-stat-pill__body span {
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
        .sanua-stat-pill__icon i { font-size: 1.15rem; width: auto; height: auto; }
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

    /* ═══ رأس صفحة داخلية (دوراتي، إلخ) ═══ */
    .sanua-page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 26px;
        border-radius: 22px;
        background:
            radial-gradient(circle at 88% 20%, rgba(251, 191, 36, 0.18) 0%, transparent 42%),
            linear-gradient(135deg, #5B21B6 0%, #6D28D9 48%, #7C3AED 100%);
        color: #fff;
        margin-bottom: var(--gap-section);
        box-shadow: 0 12px 40px -16px rgba(91, 33, 182, 0.45);
    }
    .sanua-page-head__title {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 900;
        line-height: 1.2;
        color: #fff;
    }
    .sanua-page-head__sub {
        margin: 6px 0 0;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.82);
        max-width: 36rem;
    }
    .sanua-page-head__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .sanua-page-head__btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 14px;
        background: #FBBF24;
        color: #4C1D95;
        font-size: 0.82rem;
        font-weight: 900;
        text-decoration: none !important;
        box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.25);
        transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        white-space: nowrap;
    }
    .sanua-page-head__btn:hover {
        transform: translateY(-2px);
        background: #FCD34D;
        box-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.28);
    }
    .sanua-page-head__btn i { font-size: 0.75rem; }
    .sanua-page-head__btn--ghost {
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: none;
    }
    .sanua-page-head__btn--ghost:hover {
        background: rgba(255, 255, 255, 0.22);
        color: #fff;
    }

    /* ═══ تنبيهات ═══ */
    .sanua-flash {
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: var(--gap-comp);
    }
    .sanua-flash--success {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }
    .sanua-flash--error {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    /* ═══ فلاتر ═══ */
    .sanua-filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: var(--gap-section);
    }
    .sanua-filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid #EDE9FE;
        background: #fff;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 800;
        text-decoration: none !important;
        transition: all 0.2s;
        box-shadow: 0 2px 8px -4px rgba(109, 40, 217, 0.08);
    }
    .sanua-filter-tab:hover {
        border-color: #C4B5FD;
        color: #6D28D9;
    }
    .sanua-filter-tab.is-active {
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 6px 18px -6px rgba(109, 40, 217, 0.45);
    }
    .sanua-filter-tab.is-active.is-live {
        background: linear-gradient(135deg, #DC2626, #EF4444);
        box-shadow: 0 6px 18px -6px rgba(220, 38, 38, 0.4);
    }
    .sanua-filter-tab__dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.85;
    }
    .sanua-filter-tab.is-active.is-live .sanua-filter-tab__dot {
        animation: sanua-pulse-dot 1.2s ease-in-out infinite;
    }
    @keyframes sanua-pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.45; transform: scale(0.85); }
    }

    /* ═══ جلسات البث ═══ */
    .sanua-section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 12px;
        font-size: 0.78rem;
        font-weight: 900;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .sanua-section-label__pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #EF4444;
        animation: sanua-pulse-dot 1.2s ease-in-out infinite;
    }

    .sanua-live-card {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #FECACA;
        box-shadow: 0 8px 28px -10px rgba(220, 38, 38, 0.18);
        margin-bottom: 12px;
    }
    .sanua-live-card__main { flex: 1; min-width: 0; }
    .sanua-live-card__badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .sanua-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
    }
    .sanua-badge--live {
        background: #FEE2E2;
        color: #B91C1C;
    }
    .sanua-badge--live .sanua-badge__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #EF4444;
        animation: sanua-pulse-dot 1.2s ease-in-out infinite;
    }
    .sanua-badge--course {
        background: #EDE9FE;
        color: #6D28D9;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .sanua-badge--scheduled {
        background: #EDE9FE;
        color: #6D28D9;
    }
    .sanua-live-card__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 900;
        color: #1e1b4b;
        line-height: 1.3;
    }
    .sanua-live-card__meta {
        margin: 6px 0 0;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
    }
    .sanua-live-card__meta i {
        color: #8B5CF6;
        margin-left: 4px;
    }
    .sanua-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 14px;
        border: none;
        font-size: 0.82rem;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none !important;
        transition: transform 0.2s, filter 0.2s, box-shadow 0.2s;
        white-space: nowrap;
    }
    .sanua-btn--live {
        background: linear-gradient(135deg, #DC2626, #EF4444);
        color: #fff !important;
        box-shadow: 0 8px 24px -8px rgba(220, 38, 38, 0.45);
    }
    .sanua-btn--live:hover { filter: brightness(1.06); transform: translateY(-1px); }
    .sanua-btn--purple {
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        color: #fff !important;
        box-shadow: 0 6px 18px -6px rgba(109, 40, 217, 0.4);
    }

    .sanua-session-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .sanua-session-card {
        display: block;
        padding: 18px 20px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-decoration: none !important;
        color: inherit;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: transform 0.22s, border-color 0.22s, box-shadow 0.22s;
    }
    .sanua-session-card:hover {
        transform: translateY(-4px);
        border-color: #C4B5FD;
        box-shadow: 0 16px 40px -12px rgba(109, 40, 217, 0.2);
    }
    .sanua-session-card__row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }
    .sanua-session-card__main { flex: 1; min-width: 0; }
    .sanua-session-card__title {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-session-card__details {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
    }
    .sanua-session-card__details i { color: #8B5CF6; margin-left: 4px; }
    .sanua-session-card__desc {
        margin: 8px 0 0;
        font-size: 0.78rem;
        font-weight: 600;
        color: #94a3b8;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sanua-session-card__action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 12px;
        background: #F5F3FF;
        color: #6D28D9;
        font-size: 0.78rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    @media (max-width: 767px) {
        .sanua-live-card,
        .sanua-session-card__row { flex-direction: column; align-items: stretch; }
        .sanua-btn--live,
        .sanua-session-card__action { width: 100%; }
    }

    /* ═══ بطاقات الدورات ═══ */
    .sanua-courses-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: var(--gap-card);
        width: 100%;
    }
    .sanua-courses-grid .sanua-course-card { grid-column: span 3; }
    .sanua-courses-grid .sanua-recording-card { grid-column: span 3; }
    @media (max-width: 1279px) {
        .sanua-courses-grid .sanua-course-card { grid-column: span 4; }
        .sanua-courses-grid .sanua-recording-card { grid-column: span 4; }
    }
    @media (max-width: 1023px) {
        .sanua-courses-grid .sanua-course-card { grid-column: span 6; }
        .sanua-courses-grid .sanua-recording-card { grid-column: span 6; }
    }
    @media (max-width: 639px) {
        .sanua-courses-grid { grid-template-columns: 1fr; }
        .sanua-courses-grid .sanua-course-card { grid-column: span 1; }
        .sanua-courses-grid .sanua-recording-card { grid-column: span 1; }
    }

    .sanua-course-card {
        display: flex;
        flex-direction: column;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-decoration: none !important;
        color: inherit;
        box-shadow: 0 8px 28px -10px rgba(109, 40, 217, 0.14);
        transition: transform 0.25s cubic-bezier(0.34, 1.4, 0.64, 1), box-shadow 0.25s, border-color 0.25s;
        height: 100%;
    }
    .sanua-course-card:hover {
        transform: translateY(-8px) scale(1.01);
        border-color: #C4B5FD;
        box-shadow: 0 20px 48px -16px rgba(109, 40, 217, 0.22);
    }
    .sanua-course-card__thumb {
        position: relative;
        height: 148px;
        overflow: hidden;
        background: linear-gradient(160deg, #EDE9FE 0%, #C4B5FD 100%);
    }
    .sanua-course-card__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .sanua-course-card__thumb-fallback {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        gap: 6px;
        color: #6D28D9;
        font-weight: 800;
        font-size: 0.78rem;
    }
    .sanua-course-card__thumb-fallback i { font-size: 2rem; opacity: 0.85; }
    .sanua-course-card__badge {
        position: absolute;
        top: 10px;
        right: 10px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        color: #fff;
        box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.25);
    }
    .sanua-course-card__badge--active { background: linear-gradient(135deg, #6D28D9, #8B5CF6); }
    .sanua-course-card__badge--done { background: linear-gradient(135deg, #16A34A, #22C55E); }

    .sanua-course-card__body {
        display: flex;
        flex-direction: column;
        flex: 1;
        padding: 16px 18px 18px;
        gap: 10px;
    }
    .sanua-course-card__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: #1e1b4b;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sanua-course-card__meta {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        line-height: 1.4;
    }
    .sanua-course-card__stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #475569;
    }
    .sanua-course-card__stats strong { color: #6D28D9; font-size: 0.82rem; }
    .sanua-course-card__stats .points { color: #D97706; }
    .sanua-course-card__bar {
        height: 8px;
        border-radius: 999px;
        background: #EDE9FE;
        overflow: hidden;
    }
    .sanua-course-card__bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #8B5CF6, #6D28D9);
        transition: width 0.6s ease;
    }
    .sanua-course-card__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        margin-top: auto;
        padding: 10px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        color: #fff !important;
        font-size: 0.78rem;
        font-weight: 900;
        transition: filter 0.2s, transform 0.2s;
    }
    .sanua-course-card:hover .sanua-course-card__cta {
        filter: brightness(1.06);
    }
    .sanua-course-card__cta i { font-size: 0.7rem; }

    .sanua-empty {
        text-align: center;
        padding: 48px 24px;
        border-radius: 22px;
        background: #fff;
        border: 2px dashed #DDD6FE;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
    }
    .sanua-empty__icon {
        width: 72px;
        height: 72px;
        margin: 0 auto 16px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #EDE9FE, #C4B5FD);
        color: #6D28D9;
        font-size: 1.75rem;
    }
    .sanua-empty h3 {
        margin: 0 0 8px;
        font-size: 1.15rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-empty p {
        margin: 0 auto 20px;
        max-width: 22rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
    }
    .sanua-empty__btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        color: #fff !important;
        font-size: 0.82rem;
        font-weight: 900;
        text-decoration: none !important;
        box-shadow: 0 8px 24px -8px rgba(109, 40, 217, 0.45);
    }

    .sanua-pagination {
        display: flex;
        justify-content: center;
        margin-top: var(--gap-section);
    }
    .sanua-pagination nav { direction: ltr; }
    .sanua-pagination .pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sanua-pagination .page-link,
    .sanua-pagination .page-item span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #EDE9FE;
        background: #fff;
        color: #6D28D9;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
    }
    .sanua-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        border-color: transparent;
        color: #fff;
    }
    .sanua-pagination .page-item.disabled span { opacity: 0.45; }

    /* ═══ بطاقات التسجيلات ═══ */
    .sanua-recording-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 18px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-decoration: none !important;
        color: inherit;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: transform 0.22s, border-color 0.22s, box-shadow 0.22s;
    }
    .sanua-recording-card:hover {
        transform: translateY(-6px);
        border-color: #C4B5FD;
        box-shadow: 0 16px 40px -12px rgba(109, 40, 217, 0.2);
    }
    .sanua-recording-card__icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6D28D9, #8B5CF6);
        color: #fff;
        font-size: 1.15rem;
        margin-bottom: 14px;
    }
    .sanua-recording-card__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: #1e1b4b;
        line-height: 1.35;
    }
    .sanua-recording-card__sub {
        margin: 6px 0 0;
        font-size: 0.76rem;
        font-weight: 700;
        color: #64748b;
    }
    .sanua-recording-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
    }
    .sanua-recording-card__meta i { color: #8B5CF6; margin-left: 4px; }

    /* ═══ شارات الحالة ═══ */
    .sanua-badge--pending { background: #FEF3C7; color: #B45309; }
    .sanua-badge--submitted { background: #EDE9FE; color: #6D28D9; }
    .sanua-badge--graded { background: #D1FAE5; color: #047857; }
    .sanua-badge--returned { background: #F3E8FF; color: #7C3AED; }
    .sanua-badge--available { background: #D1FAE5; color: #047857; }
    .sanua-badge--locked { background: #F1F5F9; color: #64748b; }
    .sanua-badge--danger { background: #FEE2E2; color: #B91C1C; }

    /* ═══ بطاقات الامتحانات ═══ */
    .sanua-exam-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: var(--gap-card);
    }
    .sanua-exam-grid .sanua-exam-card { grid-column: span 6; }
    @media (max-width: 1023px) {
        .sanua-exam-grid .sanua-exam-card { grid-column: span 12; }
    }
    .sanua-exam-card {
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        overflow: hidden;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: box-shadow 0.22s, border-color 0.22s;
    }
    .sanua-exam-card:hover {
        border-color: #C4B5FD;
        box-shadow: 0 12px 32px -10px rgba(109, 40, 217, 0.18);
    }
    .sanua-exam-card__head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 18px;
        background: linear-gradient(90deg, #FAFAFF, #F5F3FF);
        border-bottom: 1px solid #EDE9FE;
    }
    .sanua-exam-card__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-exam-card__body { padding: 16px 18px 18px; }
    .sanua-exam-card__course {
        margin: 0 0 12px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
    }
    .sanua-exam-card__course strong { color: #1e1b4b; }
    .sanua-metric-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 14px;
    }
    @media (min-width: 640px) {
        .sanua-metric-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .sanua-metric {
        padding: 8px 10px;
        border-radius: 12px;
        background: #F8F7FC;
        border: 1px solid #EDE9FE;
    }
    .sanua-metric__label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    .sanua-metric__value {
        display: block;
        font-size: 0.82rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-exam-card__attempt {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        background: #F5F3FF;
        border: 1px solid #EDE9FE;
        margin-bottom: 12px;
        font-size: 0.76rem;
        font-weight: 700;
        color: #475569;
    }
    .sanua-exam-card__attempt strong { color: #6D28D9; }
    .sanua-exam-card__desc {
        margin: 0 0 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sanua-exam-card__foot {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px solid #F1F5F9;
    }
    .sanua-exam-card__dates {
        font-size: 0.68rem;
        font-weight: 700;
        color: #94a3b8;
        line-height: 1.5;
    }
    .sanua-exam-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #F1F5F9;
        font-size: 0.68rem;
        font-weight: 800;
        color: #B45309;
    }
    .sanua-exam-card__tags span {
        padding: 3px 8px;
        border-radius: 999px;
        background: #FEF3C7;
        color: #92400E;
    }
    .sanua-btn--muted {
        background: #F1F5F9;
        color: #64748b !important;
        box-shadow: none;
        cursor: default;
    }
    .sanua-btn--danger-soft {
        background: #FEE2E2;
        color: #B91C1C !important;
        box-shadow: none;
        cursor: default;
    }
    .sanua-btn--green {
        background: linear-gradient(135deg, #059669, #22C55E);
        color: #fff !important;
        box-shadow: 0 6px 18px -6px rgba(5, 150, 105, 0.4);
    }

    .sanua-panel {
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        overflow: hidden;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
    }
    .sanua-panel__head {
        padding: 14px 18px;
        border-bottom: 1px solid #EDE9FE;
        background: #FAFAFF;
    }
    .sanua-panel__head h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-panel__body { padding: 14px 18px 18px; }
    .sanua-result-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #F1F5F9;
        margin-bottom: 10px;
        transition: background 0.15s;
    }
    .sanua-result-row:last-child { margin-bottom: 0; }
    .sanua-result-row:hover { background: #FAFAFF; }
    .sanua-result-row__title {
        margin: 0;
        font-size: 0.88rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-result-row__sub {
        margin: 4px 0 0;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .sanua-result-row__score {
        font-size: 1.1rem;
        font-weight: 900;
        text-align: center;
    }
    .sanua-result-row__score.is-pass { color: #059669; }
    .sanua-result-row__score.is-fail { color: #DC2626; }
    .sanua-result-row__link {
        font-size: 0.76rem;
        font-weight: 800;
        color: #6D28D9;
        text-decoration: none !important;
    }

    /* ═══ الشهادات ═══ */
    .sanua-courses-grid .sanua-cert-card { grid-column: span 3; }
    @media (max-width: 1279px) {
        .sanua-courses-grid .sanua-cert-card { grid-column: span 4; }
    }
    @media (max-width: 1023px) {
        .sanua-courses-grid .sanua-cert-card { grid-column: span 6; }
    }
    @media (max-width: 639px) {
        .sanua-courses-grid .sanua-cert-card { grid-column: span 1; }
    }
    .sanua-cert-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-decoration: none !important;
        color: inherit;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: transform 0.22s, border-color 0.22s, box-shadow 0.22s;
    }
    .sanua-cert-card:hover {
        transform: translateY(-6px);
        border-color: #C4B5FD;
        box-shadow: 0 16px 40px -12px rgba(109, 40, 217, 0.2);
    }
    .sanua-cert-card__hero {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(160deg, #EDE9FE 0%, #C4B5FD 100%);
        color: #6D28D9;
        font-size: 2.5rem;
    }
    .sanua-cert-card__body { padding: 16px 18px 18px; flex: 1; display: flex; flex-direction: column; gap: 8px; }
    .sanua-cert-card__title {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 900;
        color: #1e1b4b;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .sanua-cert-card__course { margin: 0; font-size: 0.72rem; font-weight: 700; color: #64748b; }
    .sanua-cert-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 0.68rem;
        font-weight: 700;
        color: #94a3b8;
    }
    .sanua-cert-card__meta i { color: #8B5CF6; margin-left: 4px; }
    .sanua-cert-card__num {
        font-family: ui-monospace, monospace;
        padding: 2px 8px;
        border-radius: 8px;
        background: #F5F3FF;
        color: #6D28D9;
        border: 1px solid #EDE9FE;
    }
    .sanua-cert-card__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        margin-top: auto;
        padding: 10px 14px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        color: #fff !important;
        font-size: 0.78rem;
        font-weight: 900;
    }

    /* ═══ المحفظة ═══ */
    .sanua-wallet-balance {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 24px 26px;
        border-radius: 22px;
        background:
            radial-gradient(circle at 12% 85%, rgba(251, 191, 36, 0.2) 0%, transparent 42%),
            linear-gradient(135deg, #5B21B6 0%, #6D28D9 48%, #7C3AED 100%);
        color: #fff;
        margin-bottom: var(--gap-section);
        box-shadow: 0 12px 40px -16px rgba(91, 33, 182, 0.45);
    }
    .sanua-wallet-balance__label {
        margin: 0 0 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.82);
    }
    .sanua-wallet-balance__amount {
        margin: 0;
        font-size: clamp(1.6rem, 4vw, 2.2rem);
        font-weight: 900;
        line-height: 1.1;
        color: #fff;
    }
    .sanua-wallet-balance__icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.16);
        font-size: 1.35rem;
    }
    .sanua-wallet-empty {
        padding: 16px 18px;
        border-radius: 16px;
        background: #FAFAFF;
        border: 1px dashed #DDD6FE;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
        margin-bottom: var(--gap-section);
    }
    .sanua-tx-list { display: flex; flex-direction: column; }
    .sanua-panel__body.sanua-tx-list { padding: 0; }
    .sanua-tx-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s;
    }
    .sanua-tx-row:last-child { border-bottom: none; }
    .sanua-tx-row:hover { background: #FAFAFF; }
    .sanua-tx-row__title {
        margin: 0;
        font-size: 0.88rem;
        font-weight: 800;
        color: #1e1b4b;
    }
    .sanua-tx-row__date {
        margin: 4px 0 0;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .sanua-tx-row__amount {
        font-size: 1rem;
        font-weight: 900;
        flex-shrink: 0;
    }
    .sanua-tx-row__amount.is-in { color: #059669; }
    .sanua-tx-row__amount.is-out { color: #DC2626; }

    /* ═══ الإنجازات ═══ */
    .sanua-achievements-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: var(--gap-card);
    }
    .sanua-achievements-grid .sanua-achievement-card { grid-column: span 4; }
    @media (max-width: 1023px) {
        .sanua-achievements-grid .sanua-achievement-card { grid-column: span 6; }
    }
    @media (max-width: 639px) {
        .sanua-achievements-grid { grid-template-columns: 1fr; }
        .sanua-achievements-grid .sanua-achievement-card { grid-column: span 1; }
    }
    .sanua-achievement-card {
        padding: 22px 18px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        text-align: center;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: transform 0.22s, border-color 0.22s, box-shadow 0.22s;
    }
    .sanua-achievement-card:hover {
        transform: translateY(-6px);
        border-color: #C4B5FD;
        box-shadow: 0 16px 40px -12px rgba(109, 40, 217, 0.2);
    }
    .sanua-achievement-card__icon {
        font-size: 2.75rem;
        line-height: 1;
        margin-bottom: 12px;
        color: #D97706;
        filter: drop-shadow(0 4px 8px rgba(217, 119, 6, 0.2));
    }
    .sanua-achievement-card__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-achievement-card__desc {
        margin: 8px 0 0;
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.45;
    }
    .sanua-achievement-card__points {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 14px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #F5F3FF;
        color: #6D28D9;
        font-size: 0.78rem;
        font-weight: 900;
    }

    /* ═══ الطلبات ═══ */
    .sanua-order-card {
        padding: 18px 20px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        margin-bottom: 12px;
    }
    .sanua-order-card__row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .sanua-order-card__main { flex: 1; min-width: 0; }
    .sanua-order-card__title {
        margin: 0;
        font-size: 1.02rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-order-card__sub {
        margin: 8px 0 12px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
    }
    .sanua-order-card__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        flex-shrink: 0;
    }
    .sanua-order-card__note {
        margin-top: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        background: #F5F3FF;
        border: 1px solid #EDE9FE;
        font-size: 0.78rem;
        color: #475569;
    }
    .sanua-badge--approved { background: #D1FAE5; color: #047857; }
    .sanua-badge--rejected { background: #FEE2E2; color: #B91C1C; }

    /* ═══ التقويم ═══ */
    .sanua-calendar-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: var(--gap-card);
        align-items: start;
    }
    @media (max-width: 1023px) {
        .sanua-calendar-layout { grid-template-columns: 1fr; }
    }
    .sanua-calendar-panel {
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        padding: 16px 18px 18px;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
    }
    .sanua-calendar-sidebar { display: flex; flex-direction: column; gap: var(--gap-comp); }
    .sanua-calendar-event {
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #EDE9FE;
        background: #FAFAFF;
        cursor: pointer;
        transition: border-color 0.15s, background 0.15s;
    }
    .sanua-calendar-event:hover { border-color: #C4B5FD; background: #F5F3FF; }
    .sanua-calendar-event__title {
        font-size: 0.78rem;
        font-weight: 800;
        color: #1e1b4b;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .sanua-calendar-event__meta {
        margin-top: 4px;
        font-size: 0.68rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .sanua-calendar-stats {
        padding: 16px 18px;
        border-radius: 20px;
        background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
        color: #fff;
        box-shadow: 0 8px 28px -10px rgba(91, 33, 182, 0.4);
    }
    .sanua-calendar-stats h3 {
        margin: 0 0 12px;
        font-size: 0.95rem;
        font-weight: 900;
    }
    .sanua-calendar-stats__row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.14);
        margin-bottom: 6px;
        font-size: 0.76rem;
        font-weight: 700;
    }
    .sanua-calendar-stats__row:last-child { margin-bottom: 0; }
    .sanua-calendar-stats__row strong { font-size: 1rem; font-weight: 900; }
    .sanua-event-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid #EDE9FE;
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
    }
    .sanua-event-legend span { display: inline-flex; align-items: center; gap: 6px; }
    .sanua-event-legend i {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        display: inline-block;
    }
    .sanua-fc-wrap .fc { direction: rtl; }
    .sanua-fc-wrap .fc .fc-button-primary {
        background: #6D28D9;
        border-color: #6D28D9;
    }
    .sanua-fc-wrap .fc .fc-button-primary:hover { background: #5B21B6; border-color: #5B21B6; }
    .sanua-fc-wrap .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #5B21B6;
        border-color: #5B21B6;
    }
    .sanua-fc-wrap .fc-toolbar-title { font-weight: 900; color: #1e1b4b; font-size: 1.05rem; }
    .sanua-fc-wrap .fc-event { border-radius: 6px; border: none; }

    /* ═══ الاشتراك ═══ */
    .sanua-subscription-hero {
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 8px 28px -10px rgba(109, 40, 217, 0.14);
        margin-bottom: var(--gap-section);
    }
    .sanua-subscription-hero__top {
        padding: 22px 24px;
        background:
            radial-gradient(circle at 88% 20%, rgba(251, 191, 36, 0.15) 0%, transparent 42%),
            linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
        color: #fff;
    }
    .sanua-subscription-hero__title {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 900;
        color: #fff;
    }
    .sanua-subscription-hero__sub {
        margin: 6px 0 0;
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.85);
    }
    .sanua-subscription-hero__price {
        margin-top: 14px;
        display: inline-block;
        padding: 10px 16px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 1.35rem;
        font-weight: 900;
    }
    .sanua-subscription-hero__body { padding: 20px 24px; }
    .sanua-feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }
    @media (max-width: 639px) {
        .sanua-feature-list { grid-template-columns: 1fr; }
    }
    .sanua-feature-list li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
    }
    .sanua-feature-list li i { color: #059669; }

    /* ═══ الإحالات ═══ */
    .sanua-referral-banner {
        position: relative;
        overflow: hidden;
        padding: 22px 24px;
        border-radius: 22px;
        margin-bottom: var(--gap-section);
        background:
            radial-gradient(circle at 12% 85%, rgba(251, 191, 36, 0.18) 0%, transparent 42%),
            linear-gradient(135deg, #5B21B6 0%, #6D28D9 48%, #7C3AED 100%);
        color: #fff;
        box-shadow: 0 12px 40px -16px rgba(91, 33, 182, 0.45);
    }
    .sanua-referral-banner__code {
        display: inline-block;
        margin-top: 8px;
        padding: 10px 16px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.16);
        font-size: 1.35rem;
        font-weight: 900;
        letter-spacing: 0.06em;
    }
    .sanua-referral-field {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .sanua-referral-field input {
        flex: 1;
        min-width: 200px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .sanua-referral-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 12px;
        border: none;
        background: #FBBF24;
        color: #4C1D95;
        font-size: 0.82rem;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none !important;
    }
    .sanua-referral-btn--ghost {
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.35);
    }
    .sanua-referral-btn--wa { background: #22C55E; color: #fff; }
    .sanua-steps-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: var(--gap-comp);
    }
    @media (max-width: 767px) {
        .sanua-steps-grid { grid-template-columns: 1fr; }
    }
    .sanua-step-card {
        padding: 16px;
        border-radius: 16px;
        background: #FAFAFF;
        border: 1px solid #EDE9FE;
    }
    .sanua-step-card h4 {
        margin: 0 0 6px;
        font-size: 0.88rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-step-card p {
        margin: 0;
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.45;
    }
    .sanua-table-wrap { overflow-x: auto; }
    .sanua-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }
    .sanua-table th {
        padding: 10px 14px;
        text-align: right;
        font-weight: 800;
        color: #64748b;
        background: #FAFAFF;
        border-bottom: 1px solid #EDE9FE;
    }
    .sanua-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #F1F5F9;
        color: #334155;
        font-weight: 600;
    }
    .sanua-table tr:hover td { background: #FAFAFF; }
    .sanua-alert {
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: var(--gap-comp);
    }
    .sanua-alert--warning {
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        color: #92400E;
    }
    .sanua-alert--info {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }

    /* ═══ الإشعارات ═══ */
    .sanua-filter-form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }
    @media (max-width: 1023px) {
        .sanua-filter-form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 639px) {
        .sanua-filter-form { grid-template-columns: 1fr; }
    }
    .sanua-filter-form__field label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
    }
    .sanua-filter-form__field select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #EDE9FE;
        background: #FAFAFF;
        font-size: 0.78rem;
        font-weight: 700;
        color: #334155;
    }
    .sanua-filter-form__field select:focus {
        outline: none;
        border-color: #A78BFA;
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.25);
    }
    .sanua-notification-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .sanua-notification-card {
        padding: 18px 20px;
        border-radius: 20px;
        background: #fff;
        border: 1px solid #EDE9FE;
        box-shadow: 0 4px 20px -8px rgba(109, 40, 217, 0.1);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .sanua-notification-card:hover {
        border-color: #C4B5FD;
        box-shadow: 0 12px 32px -12px rgba(109, 40, 217, 0.18);
    }
    .sanua-notification-card--unread {
        border-color: #C4B5FD;
        border-right: 4px solid #7C3AED;
        background: linear-gradient(90deg, #FAFAFF 0%, #fff 100%);
    }
    .sanua-notification-card__row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }
    .sanua-notification-card__main {
        display: flex;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }
    .sanua-notif-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .sanua-notif-icon--blue { background: #E0F2FE; color: #0284C7; }
    .sanua-notif-icon--green { background: #D1FAE5; color: #059669; }
    .sanua-notif-icon--yellow { background: #FEF3C7; color: #D97706; }
    .sanua-notif-icon--red { background: #FEE2E2; color: #DC2626; }
    .sanua-notif-icon--purple { background: #EDE9FE; color: #7C3AED; }
    .sanua-notif-icon--orange { background: #FFEDD5; color: #EA580C; }
    .sanua-notif-icon--gray { background: #F1F5F9; color: #64748b; }
    .sanua-notification-card__content { flex: 1; min-width: 0; }
    .sanua-notification-card__head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .sanua-notification-card__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-notification-card__message {
        margin: 0 0 10px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.5;
    }
    .sanua-notification-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #94a3b8;
    }
    .sanua-notification-card__meta i { color: #8B5CF6; margin-left: 4px; }
    .sanua-notification-card__link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #6D28D9;
        text-decoration: none !important;
    }
    .sanua-notification-card__link:hover { color: #5B21B6; }
    .sanua-notification-card__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        flex-shrink: 0;
    }
    .sanua-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid #EDE9FE;
        background: #FAFAFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        text-decoration: none !important;
    }
    .sanua-icon-btn--read { color: #059669; }
    .sanua-icon-btn--read:hover { background: #ECFDF5; border-color: #A7F3D0; }
    .sanua-icon-btn--view { color: #6D28D9; }
    .sanua-icon-btn--view:hover { background: #F5F3FF; border-color: #C4B5FD; }
    .sanua-icon-btn--delete { color: #DC2626; }
    .sanua-icon-btn--delete:hover { background: #FEF2F2; border-color: #FECACA; }

    /* ═══ الملف الشخصي ═══ */
    .sanua-profile-hero {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
        padding: 22px 24px;
        border-radius: 22px;
        margin-bottom: var(--gap-section);
        background:
            radial-gradient(circle at 88% 12%, rgba(251, 191, 36, 0.14) 0%, transparent 38%),
            linear-gradient(135deg, #5B21B6 0%, #6D28D9 48%, #7C3AED 100%);
        color: #fff;
        box-shadow: 0 12px 40px -16px rgba(91, 33, 182, 0.45);
    }
    .sanua-profile-avatar {
        width: 96px;
        height: 96px;
        border-radius: 22px;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.18);
        border: 2px solid rgba(255, 255, 255, 0.35);
        font-size: 2.2rem;
        font-weight: 900;
    }
    .sanua-profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .sanua-profile-hero__main { flex: 1; min-width: 220px; }
    .sanua-profile-hero__name {
        margin: 8px 0 4px;
        font-size: 1.65rem;
        font-weight: 900;
        line-height: 1.2;
    }
    .sanua-profile-hero__sub {
        margin: 0;
        font-size: 0.82rem;
        font-weight: 600;
        opacity: 0.88;
    }
    .sanua-profile-hero__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .sanua-profile-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.28);
        font-size: 0.76rem;
        font-weight: 800;
    }
    .sanua-profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        gap: var(--gap-section);
        align-items: start;
    }
    @media (max-width: 1023px) {
        .sanua-profile-layout { grid-template-columns: 1fr; }
    }
    .sanua-profile-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #F1F5F9;
        font-size: 0.78rem;
    }
    .sanua-profile-info-row:last-child { border-bottom: none; }
    .sanua-profile-info-row__label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        color: #64748b;
    }
    .sanua-profile-info-row__label i { color: #8B5CF6; }
    .sanua-profile-info-row__value {
        font-weight: 900;
        color: #1e1b4b;
        text-align: left;
    }
    .sanua-tip-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .sanua-tip-list li {
        display: flex;
        gap: 10px;
        padding: 12px;
        border-radius: 14px;
        background: #FAFAFF;
        border: 1px solid #EDE9FE;
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.45;
    }
    .sanua-tip-list li i { color: #7C3AED; margin-top: 2px; }
    .sanua-tip-list li strong { display: block; color: #1e1b4b; margin-bottom: 2px; }
    .sanua-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    @media (max-width: 639px) {
        .sanua-form-grid { grid-template-columns: 1fr; }
    }
    .sanua-field { display: flex; flex-direction: column; gap: 6px; }
    .sanua-field--full { grid-column: 1 / -1; }
    .sanua-field label {
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
    }
    .sanua-field input[type="text"],
    .sanua-field input[type="email"],
    .sanua-field input[type="password"],
    .sanua-field input[type="file"] {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        border: 1px solid #EDE9FE;
        background: #FAFAFF;
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
    }
    .sanua-field input:focus {
        outline: none;
        border-color: #A78BFA;
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.25);
    }
    .sanua-field__error {
        font-size: 0.72rem;
        font-weight: 700;
        color: #DC2626;
    }
    .sanua-upload-box {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }
    .sanua-upload-preview {
        width: 88px;
        height: 88px;
        border-radius: 18px;
        overflow: hidden;
        border: 2px dashed #C4B5FD;
        background: #FAFAFF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7C3AED;
        font-size: 1.5rem;
    }
    .sanua-upload-preview img { width: 100%; height: 100%; object-fit: cover; }
    .sanua-password-box {
        padding: 16px;
        border-radius: 16px;
        border: 1px dashed #C4B5FD;
        background: #FAFAFF;
    }
    .sanua-form-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 16px;
        margin-top: 8px;
        border-top: 1px solid #EDE9FE;
    }
    .sanua-activity-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px;
        border-radius: 14px;
        background: #FAFAFF;
        border: 1px solid #EDE9FE;
        margin-bottom: 10px;
    }
    .sanua-activity-row:last-child { margin-bottom: 0; }
    .sanua-activity-row__main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .sanua-activity-row__icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
        color: #fff;
        flex-shrink: 0;
    }
    .sanua-activity-row__title {
        margin: 0;
        font-size: 0.82rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-activity-row__sub {
        margin: 2px 0 0;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
    }

    /* ═══ الإعدادات ═══ */
    .sanua-settings-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .sanua-setting-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #FAFAFF;
        border: 1px solid #EDE9FE;
    }
    .sanua-setting-row__text { flex: 1; min-width: 200px; }
    .sanua-setting-row__title {
        margin: 0 0 4px;
        font-size: 0.82rem;
        font-weight: 900;
        color: #1e1b4b;
    }
    .sanua-setting-row__desc {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
        line-height: 1.45;
    }
    .sanua-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 28px;
        flex-shrink: 0;
    }
    .sanua-toggle input { opacity: 0; width: 0; height: 0; }
    .sanua-toggle__slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #CBD5E1;
        border-radius: 999px;
        transition: 0.2s;
    }
    .sanua-toggle__slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        transition: 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }
    .sanua-toggle input:checked + .sanua-toggle__slider {
        background: linear-gradient(135deg, #6D28D9, #7C3AED);
    }
    .sanua-toggle input:checked + .sanua-toggle__slider:before {
        transform: translateX(22px);
    }
    .sanua-toggle input:focus + .sanua-toggle__slider {
        box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.35);
    }

    @media (max-width: 767px) {
        .sanua-page-head { padding: 18px 16px; }
        .sanua-page-head__title { font-size: 1.2rem; }
        .sanua-page-head__btn { width: 100%; justify-content: center; }
    }

    @media (max-width: 767px) {
        .sanua-col-4, .sanua-col-6, .sanua-col-3 { grid-column: span 12; }
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\dashboard\partials\sanua-theme.blade.php ENDPATH**/ ?>
@php($bc = config('brand.colors'))
<style>
    :root {
        --admin-primary: {{ $bc['blue'] }};
        --admin-primary-dark: {{ $bc['blue_dark'] }};
        --admin-primary-light: {{ $bc['blue_light'] }};
        --admin-primary-rgb: {{ $bc['blue_rgb'] }};
        --admin-purple: {{ $bc['purple'] }};
        --admin-purple-rgb: {{ $bc['purple_rgb'] }};
        --admin-accent: {{ $bc['yellow'] }};
        --admin-sidebar-bg: #0c1229;
        --admin-sidebar-bg-2: #121b3d;
        --admin-surface: #f4f6fb;
        --admin-radius: 16px;
        --admin-radius-sm: 12px;
        --admin-shadow: 0 12px 40px -16px rgba(12, 18, 41, 0.12);
    }

    body { background: var(--admin-surface); }

    /* ========== SIDEBAR — Sana dark ========== */
    .admin-sidebar--brand {
        background: linear-gradient(180deg, var(--admin-sidebar-bg) 0%, var(--admin-sidebar-bg-2) 55%, #0f1633 100%) !important;
        border-left: none !important;
        box-shadow: -8px 0 32px rgba(8, 12, 32, 0.35) !important;
    }
    .admin-sidebar--brand .sidebar-logo-head {
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    .admin-sidebar--brand .sidebar-logo-text h2 { color: #fff !important; }
    .admin-sidebar--brand .sidebar-logo-text p { color: rgba(255, 255, 255, 0.5) !important; }
    .admin-sidebar--brand .sidebar-section-label {
        color: rgba(255, 255, 255, 0.35) !important;
        letter-spacing: 0.1em;
    }
    .admin-sidebar--brand .sidebar-link {
        color: rgba(255, 255, 255, 0.72);
    }
    .admin-sidebar--brand .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
    }
    .admin-sidebar--brand .sidebar-link.active {
        background: linear-gradient(90deg, rgba(var(--admin-primary-rgb), 0.35), rgba(var(--admin-purple-rgb), 0.2));
        color: #fff;
        font-weight: 600;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    }
    .admin-sidebar--brand .sidebar-link.active::before {
        background: linear-gradient(180deg, var(--admin-accent), var(--admin-primary));
        width: 3px;
    }
    .admin-sidebar--brand .sidebar-link i { color: rgba(255, 255, 255, 0.45); }
    .admin-sidebar--brand .sidebar-link.active i,
    .admin-sidebar--brand .sidebar-link:hover i { color: var(--admin-accent); }
    .admin-sidebar--brand .sidebar-group-btn {
        color: rgba(255, 255, 255, 0.72);
    }
    .admin-sidebar--brand .sidebar-group-btn:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
    }
    .admin-sidebar--brand .sidebar-group-btn i.chevron { color: rgba(255, 255, 255, 0.35); }
    .admin-sidebar--brand .sidebar-sub-link {
        color: rgba(255, 255, 255, 0.55);
    }
    .admin-sidebar--brand .sidebar-sub-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.9);
    }
    .admin-sidebar--brand .sidebar-sub-link.active {
        color: #fff;
        background: rgba(var(--admin-primary-rgb), 0.2);
    }
    .admin-sidebar--brand ul[x-show] { border-right-color: rgba(255, 255, 255, 0.1) !important; }
    .admin-sidebar--brand .sidebar-foot {
        border-top-color: rgba(255, 255, 255, 0.08) !important;
    }
    .admin-sidebar--brand .sidebar-collapse-btn {
        color: rgba(255, 255, 255, 0.5);
    }
    .admin-sidebar--brand .sidebar-collapse-btn:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }
    .admin-sidebar--brand .sidebar-user-wrap {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .admin-sidebar--brand .sidebar-user-wrap:hover {
        background: rgba(255, 255, 255, 0.08) !important;
    }
    .admin-sidebar--brand .sidebar-user-info p:first-child { color: #fff !important; }
    .admin-sidebar--brand .sidebar-user-info p:last-child { color: rgba(255, 255, 255, 0.45) !important; }
    .admin-sidebar--brand .sidebar-nav {
        scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
    }

    /* ========== TOP NAVBAR ========== */
    .top-navbar--brand {
        height: 64px;
        background: rgba(255, 255, 255, 0.98) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 1px 0 rgba(var(--admin-primary-rgb), 0.06), 0 4px 20px -8px rgba(15, 23, 42, 0.08) !important;
    }
    .top-navbar--brand::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--admin-primary), var(--admin-purple) 55%, var(--admin-accent));
        opacity: 0.85;
    }
        background: rgba(15, 23, 42, 0.98) !important;
        border-bottom-color: #334155 !important;
    }
    .admin-nav-search {
        background: var(--admin-surface);
        border: 1px solid #e2e8f0;
    }
    .admin-nav-search:focus-within {
        border-color: rgba(var(--admin-primary-rgb), 0.35);
        box-shadow: 0 0 0 3px rgba(var(--admin-primary-rgb), 0.1);
        background: #fff;
    }
        background: #1e293b;
        border-color: #475569;
    }
    .admin-nav-icon-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: var(--admin-radius-sm);
        background: var(--admin-surface);
        border: 1px solid transparent;
        color: #64748b;
        transition: all 0.2s;
    }
    .admin-nav-icon-btn:hover {
        background: #fff;
        border-color: #e2e8f0;
        color: var(--admin-primary);
    }
        background: #334155;
        color: #94a3b8;
    }
        background: #475569;
        color: #e2e8f0;
    }

    /* ========== CARDS & BUTTONS ========== */
    .stat-card {
        border-radius: var(--admin-radius);
        border-top: 3px solid transparent;
        background-image: linear-gradient(#fff, #fff), linear-gradient(90deg, var(--admin-primary), var(--admin-purple));
        background-origin: border-box;
        background-clip: padding-box, border-box;
    }
        background-image: linear-gradient(#1e293b, #1e293b), linear-gradient(90deg, var(--admin-primary), var(--admin-purple));
    }
    .btn-primary {
        background: var(--admin-primary) !important;
    }
    .btn-primary:hover {
        background: var(--admin-primary-dark) !important;
        box-shadow: 0 8px 20px -6px rgba(var(--admin-primary-rgb), 0.45);
    }
    .section-card-header a,
    .list-row + div a[class*="text-indigo"],
    main a.text-indigo-600 {
        color: var(--admin-primary) !important;
    }

    /* ========== DASHBOARD HERO — ألوان هادئة متناسقة مع العلامة ========== */
    .admin-dashboard-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 1.65rem 1.85rem;
        color: #e8eef8;
        border: 1px solid rgba(255, 255, 255, 0.07);
        background:
            linear-gradient(145deg,
                #0e1428 0%,
                #121c38 42%,
                #162447 78%,
                #1a2a4a 100%);
        box-shadow:
            0 1px 0 rgba(255, 255, 255, 0.04) inset,
            0 12px 36px -16px rgba(8, 12, 28, 0.55);
    }
    .admin-dashboard-hero::before {
        content: '';
        position: absolute;
        top: 0;
        inset-inline: 0;
        height: 3px;
        background: linear-gradient(90deg,
            rgba(var(--admin-primary-rgb), 0.55) 0%,
            rgba(var(--admin-purple-rgb), 0.4) 55%,
            rgba(244, 176, 0, 0.28) 100%);
        pointer-events: none;
        z-index: 2;
    }
    .admin-dashboard-hero::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle,
            rgba(var(--admin-purple-rgb), 0.14) 0%,
            transparent 68%);
        top: -55%;
        inset-inline-end: -12%;
        pointer-events: none;
    }
    .admin-dashboard-hero-inner { position: relative; z-index: 1; }
    .admin-dashboard-hero .hero-date {
        color: rgba(232, 238, 248, 0.65);
        font-size: 0.8125rem;
        font-weight: 600;
    }
    .admin-dashboard-hero .hero-title {
        color: #f8fafc;
        letter-spacing: -0.02em;
    }
    .admin-dashboard-hero .hero-sub {
        color: rgba(226, 232, 240, 0.78);
    }
    .admin-dashboard-hero__btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 11px;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s, border-color 0.2s, color 0.2s, box-shadow 0.2s;
    }
    .admin-dashboard-hero__btn--ghost {
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .admin-dashboard-hero__btn--ghost:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.16);
        color: #fff;
    }
    .admin-dashboard-hero__btn--solid {
        color: var(--admin-primary-dark);
        background: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.85);
        box-shadow: 0 4px 14px -6px rgba(8, 12, 28, 0.35);
    }
    .admin-dashboard-hero__btn--solid:hover {
        background: #fff;
        box-shadow: 0 6px 18px -6px rgba(8, 12, 28, 0.4);
    }
    .admin-dashboard-hero__btn--solid i {
        color: var(--admin-primary);
    }
    .admin-quick-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 0.75rem;
        border-radius: var(--admin-radius-sm);
        background: #fff;
        border: 1px solid #e8edf5;
        text-align: center;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .admin-quick-link:hover {
        border-color: rgba(var(--admin-primary-rgb), 0.25);
        box-shadow: var(--admin-shadow);
        transform: translateY(-2px);
        color: var(--admin-primary);
    }
    .admin-quick-link i {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
    }
        background: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }

    /* ========== ADMIN DASHBOARD — بطاقات منظمة ========== */
    .admin-dashboard { --dash-gap: 1rem; }
    .admin-dashboard .dash-section { display: flex; flex-direction: column; gap: var(--dash-gap); }
    .admin-dashboard .dash-section__head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .admin-dashboard .dash-section__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
        border: 1px solid transparent;
    }
    .admin-dashboard .dash-section__icon--primary {
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border-color: rgba(var(--admin-primary-rgb), 0.12);
    }
    .admin-dashboard .dash-section__icon--emerald {
        color: #047857;
        background: #ecfdf5;
        border-color: rgba(16, 185, 129, 0.15);
    }
    .admin-dashboard .dash-section__icon--amber {
        color: #92680a;
        background: #fff8e6;
        border-color: rgba(244, 176, 0, 0.22);
    }
    .admin-dashboard .dash-section__icon--violet {
        color: #5520cc;
        background: #f0ebff;
        border-color: rgba(var(--admin-purple-rgb), 0.12);
    }
    .admin-dashboard .dash-section__title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        font-family: inherit;
    }
    .admin-dashboard .dash-section__sub {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.15rem;
    }
    .admin-dashboard .dash-grid {
        display: grid;
        gap: var(--dash-gap);
    }
    .admin-dashboard .dash-grid--metrics {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    @media (min-width: 640px) {
        .admin-dashboard .dash-grid--metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .admin-dashboard .dash-grid--metrics { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .admin-dashboard .dash-grid--panels {
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .admin-dashboard .dash-grid--panels { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .admin-dashboard .dash-quick-panel {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 14px;
        padding: 0.875rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .admin-dashboard .dash-quick-panel__grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
    }
    @media (min-width: 640px) {
        .admin-dashboard .dash-quick-panel__grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .admin-dashboard .dash-quick-panel__grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    }
    .admin-dashboard .dash-quick-panel .admin-quick-link {
        border: 1px solid #eef2f7;
        background: #fafbfd;
        border-radius: 12px;
        padding: 0.875rem 0.5rem;
    }
    .admin-dashboard .dash-quick-panel .admin-quick-link:hover {
        background: #fff;
        border-color: rgba(var(--admin-primary-rgb), 0.18);
    }
    .admin-dashboard .dash-quick-panel .admin-quick-link > span:first-child {
        filter: saturate(0.82) brightness(0.96);
        box-shadow: 0 4px 12px -8px rgba(15, 23, 42, 0.35);
    }

    .admin-dashboard .stat-card {
        display: flex;
        flex-direction: column;
        min-height: 8.75rem;
        padding: 1.125rem 1.2rem;
        border: 1px solid #e8edf5 !important;
        border-radius: 14px !important;
        background: #fff !important;
        background-image: none !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        position: relative;
        overflow: hidden;
    }
    .admin-dashboard .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        inset-inline: 0;
        height: 3px;
        background: linear-gradient(90deg,
            rgba(var(--admin-primary-rgb), 0.65),
            rgba(var(--admin-purple-rgb), 0.45));
        border-radius: 14px 14px 0 0;
        z-index: 1;
    }
    .admin-dashboard .stat-card > .flex:first-child { flex: 1; position: relative; z-index: 2; }
    .admin-dashboard .stat-card .text-sm.font-medium.text-slate-500 {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        color: #64748b !important;
    }
    .admin-dashboard .stat-card .text-3xl,
    .admin-dashboard .stat-card .text-2xl {
        letter-spacing: -0.02em;
        line-height: 1.15;
    }
    .admin-dashboard .stat-card > .mt-3 {
        margin-top: auto !important;
        padding-top: 0.7rem;
        border-top: 1px dashed #e8edf5;
        position: relative;
        z-index: 2;
    }
    .admin-dashboard .stat-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 11px;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .admin-dashboard .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -14px rgba(var(--admin-primary-rgb), 0.22);
        border-color: rgba(var(--admin-primary-rgb), 0.18) !important;
    }

    .admin-dashboard .section-card {
        border: 1px solid #e8edf5 !important;
        border-radius: 14px !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        background: #fff !important;
    }
    .admin-dashboard .section-card:hover {
        box-shadow: 0 10px 24px -12px rgba(15, 23, 42, 0.1);
    }
    .admin-dashboard .section-card-header {
        padding: 0.95rem 1.2rem !important;
        background: #fafbfd !important;
        border-bottom: 1px solid #eef2f7 !important;
        gap: 0.75rem;
    }
    .admin-dashboard .section-card-header h3 {
        font-size: 0.9rem !important;
        font-weight: 800 !important;
        gap: 0.5rem !important;
    }
    .admin-dashboard .section-card-header h3 > i {
        width: 1.85rem;
        height: 1.85rem;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid #e8edf5;
        font-size: 0.8rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }
    .admin-dashboard .section-card-body { padding: 0; }
    .admin-dashboard .section-card-body--padded { padding: 1.1rem 1.2rem; }
    .admin-dashboard .dash-subsection-title {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.65rem 1.2rem 0.35rem;
    }
    .admin-dashboard .dash-list {
        border-top: 1px solid #f1f5f9;
    }
    .admin-dashboard .section-card .list-row {
        padding: 0.8rem 1.2rem;
        margin: 0;
        border-bottom-color: #f1f5f9 !important;
    }
    .admin-dashboard .section-card .list-row:hover {
        background: #f8fafc !important;
    }
    .admin-dashboard .section-card-footer-link {
        padding: 0.7rem 1.2rem;
        border-top: 1px solid #f1f5f9;
        background: #fafbfd;
    }

    .admin-dashboard .dash-kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.65rem;
        margin-bottom: 0.75rem;
    }
    .admin-dashboard .dash-kpi {
        padding: 0.75rem 0.85rem;
        border-radius: 11px;
        border: 1px solid #eef2f7;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .admin-dashboard .dash-kpi:hover {
        border-color: rgba(var(--admin-primary-rgb), 0.15);
        box-shadow: 0 4px 12px -6px rgba(15, 23, 42, 0.08);
    }
    .admin-dashboard .dash-kpi__label {
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    .admin-dashboard .dash-kpi__value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        font-family: inherit;
    }
    .admin-dashboard .dash-kpi--wide { grid-column: 1 / -1; }
    .admin-dashboard .dash-kpi--emerald {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        border-color: #bbf7d0;
    }
    .admin-dashboard .dash-kpi--emerald .dash-kpi__label { color: #047857; }
    .admin-dashboard .dash-kpi--emerald .dash-kpi__value { color: #065f46; }
    .admin-dashboard .dash-kpi--indigo {
        background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
        border-color: #c7d2fe;
    }
    .admin-dashboard .dash-kpi--indigo .dash-kpi__label { color: #4338ca; }
    .admin-dashboard .dash-kpi--indigo .dash-kpi__value { color: #3730a3; }

    .admin-dashboard .dash-empty {
        padding: 2.5rem 1rem;
        text-align: center;
        color: #94a3b8;
    }
    .admin-dashboard .dash-empty i {
        font-size: 1.75rem;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
        display: block;
    }

        background: #1e293b !important;
        border-color: #334155 !important;
    }
        background: #0f172a;
        border-color: #334155;
    }
        background: rgba(15, 23, 42, 0.5) !important;
        border-bottom-color: #334155 !important;
    }
        background: #0f172a;
        border-color: #334155;
    }

    /* ========== ADMIN PROFILE & FORMS (مشترك مع الداشبورد) ========== */
    .admin-profile-page .admin-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.95rem 1.1rem;
        border-radius: 14px;
        border: 1px solid;
        font-size: 0.875rem;
    }
    .admin-profile-page .admin-alert--success {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .admin-profile-page .admin-alert--warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }
    .admin-profile-page .admin-alert__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.85rem;
    }
    .admin-profile-page .admin-alert--success .admin-alert__icon {
        background: #dcfce7;
        color: #16a34a;
    }
    .admin-profile-page .admin-alert--warning .admin-alert__icon {
        background: #fef3c7;
        color: #d97706;
    }
    .admin-profile-page .profile-hero-stat {
        padding: 0.75rem 0.85rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        text-align: center;
    }
    .admin-profile-page .profile-hero-stat__label {
        font-size: 0.68rem;
        color: rgba(226, 232, 240, 0.65);
        font-weight: 600;
        margin-bottom: 0.2rem;
    }
    .admin-profile-page .profile-hero-stat__value {
        font-size: 0.8rem;
        font-weight: 700;
        color: #f8fafc;
    }
    .admin-profile-page .profile-avatar {
        width: 5.5rem;
        height: 5.5rem;
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(var(--admin-primary-rgb), 0.35), rgba(var(--admin-purple-rgb), 0.25));
        border: 2px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 24px -8px rgba(8, 12, 28, 0.5);
        color: #fff;
        font-size: 2rem;
        font-weight: 800;
        overflow: hidden;
        flex-shrink: 0;
    }
    .admin-profile-page .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .admin-profile-page .admin-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        border: 1px solid;
    }
    .admin-profile-page .admin-chip--primary {
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border-color: rgba(var(--admin-primary-rgb), 0.15);
    }
    .admin-profile-page .profile-contact-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        border-radius: 9px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .admin-profile-page .profile-contact-pill i {
        color: rgba(148, 163, 184, 0.9);
        font-size: 0.7rem;
    }
    .admin-profile-page .admin-side-card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .admin-profile-page .admin-side-card__head {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.95rem 1.15rem;
        border-bottom: 1px solid #eef2f7;
        background: #fafbfd;
    }
    .admin-profile-page .admin-side-card__head h2 {
        font-size: 0.9rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }
    .admin-profile-page .admin-side-card__icon {
        width: 2rem;
        height: 2rem;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .admin-profile-page .admin-side-card__icon--blue {
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border: 1px solid rgba(var(--admin-primary-rgb), 0.12);
    }
    .admin-profile-page .admin-side-card__icon--green {
        color: #047857;
        background: #ecfdf5;
        border: 1px solid rgba(16, 185, 129, 0.15);
    }
    .admin-profile-page .admin-side-card__body { padding: 1rem 1.15rem; }
    .admin-profile-page .admin-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
        font-size: 0.8125rem;
    }
    .admin-profile-page .admin-info-row + .admin-info-row { margin-top: 0.5rem; }
    .admin-profile-page .admin-info-row__label { color: #64748b; font-weight: 600; }
    .admin-profile-page .admin-info-row__value { color: #0f172a; font-weight: 700; }
    .admin-profile-page .admin-info-row--success {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .admin-profile-page .admin-info-row--danger {
        background: #fff1f2;
        border-color: #fecdd3;
    }
    .admin-profile-page .admin-form-card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .admin-profile-page .admin-form-card__head {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #eef2f7;
        background: #fafbfd;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .admin-profile-page .admin-form-card__head h3 {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.2rem;
    }
    .admin-profile-page .admin-form-card__head p {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0;
    }
    .admin-profile-page .admin-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border: 1px solid rgba(var(--admin-primary-rgb), 0.12);
    }
    .admin-profile-page .admin-form-card__body { padding: 1.25rem; }
    .admin-profile-page .admin-field label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.4rem;
    }
    .admin-profile-page .admin-field-input-wrap { position: relative; }
    .admin-profile-page .admin-field-input-wrap > i {
        position: absolute;
        inset-inline-end: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.8rem;
        pointer-events: none;
    }
    .admin-profile-page .admin-input {
        width: 100%;
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        background: #fafbfd;
        padding: 0.7rem 2.5rem 0.7rem 0.9rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .admin-profile-page .admin-input:focus {
        outline: none;
        border-color: rgba(var(--admin-primary-rgb), 0.45);
        box-shadow: 0 0 0 3px rgba(var(--admin-primary-rgb), 0.12);
        background: #fff;
    }
    .admin-profile-page .admin-input--plain { padding-inline: 0.9rem; }
    .admin-profile-page .admin-password-block {
        padding: 1.1rem;
        border-radius: 12px;
        border: 1px dashed #e2e8f0;
        background: #f8fafc;
    }
    .admin-profile-page .admin-password-block h4 {
        font-size: 0.875rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.25rem;
    }
    .admin-profile-page .admin-upload-zone {
        display: flex;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1rem;
        border-radius: 11px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #475569;
        transition: border-color 0.2s, background 0.2s;
    }
    .admin-profile-page .admin-upload-zone:hover {
        border-color: rgba(var(--admin-primary-rgb), 0.35);
        background: #fff;
        color: var(--admin-primary);
    }
    .admin-profile-page .admin-upload-preview {
        width: 6.5rem;
        height: 6.5rem;
        border-radius: 14px;
        border: 1px dashed #e2e8f0;
        background: #f8fafc;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .admin-profile-page .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.65rem 1.15rem;
        border-radius: 11px;
        font-size: 0.8125rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .admin-profile-page .admin-btn--primary {
        color: #fff;
        background: var(--admin-primary);
        border-color: var(--admin-primary);
        box-shadow: 0 4px 14px -6px rgba(var(--admin-primary-rgb), 0.45);
    }
    .admin-profile-page .admin-btn--primary:hover {
        background: var(--admin-primary-dark);
        border-color: var(--admin-primary-dark);
    }
    .admin-profile-page .admin-btn--ghost {
        color: #475569;
        background: #fff;
        border-color: #e2e8f0;
    }
    .admin-profile-page .admin-btn--ghost:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .admin-profile-page .admin-btn--danger-outline {
        width: 100%;
        color: #be123c;
        background: #fff1f2;
        border-color: #fecdd3;
    }
    .admin-profile-page .admin-btn--danger-outline:hover { background: #ffe4e6; }
    .admin-profile-page .admin-btn--success {
        width: 100%;
        color: #fff;
        background: #059669;
        border-color: #059669;
    }
    .admin-profile-page .admin-btn--success:hover { background: #047857; }
    .admin-profile-page .admin-form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        align-items: center;
        justify-content: space-between;
        padding-top: 1.25rem;
        margin-top: 0.5rem;
        border-top: 1px solid #eef2f7;
    }
    .admin-profile-page .recovery-codes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
    }
    @media (min-width: 640px) {
        .admin-profile-page .recovery-codes { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .admin-profile-page .recovery-codes span {
        font-family: ui-monospace, monospace;
        font-size: 0.75rem;
        padding: 0.5rem 0.65rem;
        border-radius: 9px;
        background: #fff;
        border: 1px solid #fde68a;
        color: #92400e;
        text-align: center;
    }
        background: #1e293b !important;
        border-color: #334155 !important;
    }
        background: rgba(15, 23, 42, 0.5) !important;
        border-bottom-color: #334155 !important;
    }
        background: #0f172a;
        border-color: #334155;
    }
        background: #0f172a;
        border-color: #475569;
        color: #e2e8f0;
    }
        background: #0f172a;
        border-color: #475569;
    }

    /* ========== ADMIN LIST PAGES (وارد، رسائل، …) ========== */
    .admin-list-page .admin-page-hero__icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--admin-primary-dark);
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .admin-list-page .admin-mini-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .admin-list-page .admin-mini-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .admin-list-page .admin-mini-stats--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .admin-list-page .admin-mini-stat {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        border: 1px solid #e8edf5;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .admin-list-page .admin-mini-stat__label {
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.2rem;
    }
    .admin-list-page .admin-mini-stat__value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .admin-list-page .admin-mini-stat__meta {
        font-size: 0.65rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }
    .admin-list-page .admin-mini-stat--highlight {
        background: linear-gradient(135deg, #fff8e6 0%, #fff 100%);
        border-color: rgba(244, 176, 0, 0.25);
    }
    .admin-list-page .admin-mini-stat--highlight .admin-mini-stat__value { color: #92400e; }
    .admin-list-page .admin-panel {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .admin-list-page .admin-panel__head {
        padding: 0.95rem 1.2rem;
        border-bottom: 1px solid #eef2f7;
        background: #fafbfd;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .admin-list-page .admin-panel__head h2,
    .admin-list-page .admin-panel__head h3 {
        font-size: 0.9rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .admin-list-page .admin-panel__head h2 > i,
    .admin-list-page .admin-panel__head h3 > i {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border: 1px solid rgba(var(--admin-primary-rgb), 0.1);
    }
    .admin-list-page .admin-panel__sub {
        font-size: 0.75rem;
        color: #64748b;
        margin: 0.15rem 0 0;
    }
    .admin-list-page .admin-panel__body { padding: 1.1rem 1.2rem; }
    .admin-list-page .admin-panel__body--flush { padding: 0; }
    .admin-list-page .admin-filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        padding: 0.65rem 1.2rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
    }
    .admin-list-page .admin-filter-tab {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        color: #64748b;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .admin-list-page .admin-filter-tab:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .admin-list-page .admin-filter-tab.is-active {
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
    }
    .admin-list-page .admin-inbox-item {
        display: flex;
        align-items: stretch;
        gap: 0;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .admin-list-page .admin-inbox-item:last-child { border-bottom: none; }
    .admin-list-page .admin-inbox-item:hover { background: #f8fafc; }
    .admin-list-page .admin-inbox-item.is-unread {
        background: linear-gradient(90deg, rgba(244, 176, 0, 0.06) 0%, transparent 100%);
    }
    .admin-list-page .admin-inbox-item__link {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        flex: 1;
        min-width: 0;
        padding: 0.95rem 0.75rem 0.95rem 1.2rem;
        text-decoration: none;
        color: inherit;
    }
    .admin-list-page .admin-inbox-item__delete {
        display: flex;
        align-items: center;
        padding: 0 0.85rem 0 0.35rem;
        margin: 0;
        flex-shrink: 0;
    }
    .admin-list-page .admin-inbox-item__delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.15s, background 0.15s;
    }
    .admin-list-page .admin-inbox-item__delete-btn:hover {
        color: #e11d48;
        background: #fff1f2;
    }
    .admin-list-page .admin-inbox-item__icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .admin-list-page .admin-inbox-item__icon--read {
        color: #64748b;
        background: #f1f5f9;
    }
    .admin-list-page .admin-inbox-item__icon--unread {
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
    }
    .admin-list-page .admin-inbox-item__dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #f43f5e;
        flex-shrink: 0;
        margin-top: 0.45rem;
    }
    .admin-list-page .admin-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
    }
    .admin-list-page .admin-data-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
    }
    .admin-list-page .admin-data-table th {
        padding: 0.75rem 1rem;
        text-align: right;
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .admin-list-page .admin-data-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    .admin-list-page .admin-data-table tbody tr:hover td {
        background: #fafbfd;
    }
    .admin-list-page .admin-data-table tbody tr.is-unread td {
        background: linear-gradient(90deg, rgba(var(--admin-primary-rgb), 0.04) 0%, transparent 100%);
    }
    .admin-list-page .admin-avatar-sm {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--admin-primary-dark);
        background: var(--admin-primary-light);
        border: 1px solid rgba(var(--admin-primary-rgb), 0.12);
        flex-shrink: 0;
    }
    .admin-list-page .admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.55rem;
        border-radius: 7px;
        font-size: 0.68rem;
        font-weight: 700;
        border: 1px solid;
    }
    .admin-list-page .admin-badge--success {
        color: #047857;
        background: #ecfdf5;
        border-color: #bbf7d0;
    }
    .admin-list-page .admin-badge--danger {
        color: #be123c;
        background: #fff1f2;
        border-color: #fecdd3;
    }
    .admin-list-page .admin-badge--warn {
        color: #92400e;
        background: #fff8e6;
        border-color: #fde68a;
    }
    .admin-list-page .admin-icon-btn {
        width: 2rem;
        height: 2rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        transition: all 0.15s;
        cursor: pointer;
        text-decoration: none;
    }
    .admin-list-page .admin-icon-btn:hover {
        border-color: rgba(var(--admin-primary-rgb), 0.25);
        color: var(--admin-primary);
        background: var(--admin-primary-light);
    }
    .admin-list-page .admin-icon-btn--danger:hover {
        border-color: #fecdd3;
        color: #be123c;
        background: #fff1f2;
    }
    .admin-list-page .admin-icon-btn--success:hover {
        border-color: #bbf7d0;
        color: #047857;
        background: #ecfdf5;
    }
    .admin-list-page .admin-pagination {
        padding: 0.85rem 1.2rem;
        border-top: 1px solid #f1f5f9;
        background: #fafbfd;
    }
    .admin-list-page .admin-empty {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #94a3b8;
    }
    .admin-list-page .admin-empty i {
        font-size: 2rem;
        color: #cbd5e1;
        margin-bottom: 0.75rem;
        display: block;
    }
    .admin-list-page .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .admin-list-page .admin-field label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .admin-list-page .admin-input,
    .admin-list-page select.admin-input {
        width: 100%;
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        background: #fafbfd;
        padding: 0.65rem 0.85rem;
        font-size: 0.8125rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .admin-list-page .admin-input:focus,
    .admin-list-page select.admin-input:focus {
        outline: none;
        border-color: rgba(var(--admin-primary-rgb), 0.45);
        box-shadow: 0 0 0 3px rgba(var(--admin-primary-rgb), 0.1);
        background: #fff;
    }
    .admin-list-page .admin-btn,
    .admin-profile-page .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.6rem 1rem;
        border-radius: 11px;
        font-size: 0.8125rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .admin-form-page .admin-field label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.4rem;
    }
    .admin-form-page .admin-input {
        width: 100%;
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        background: #fafbfd;
        padding: 0.7rem 0.85rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .admin-form-page .admin-input:focus {
        outline: none;
        border-color: rgba(var(--admin-primary-rgb), 0.45);
        box-shadow: 0 0 0 3px rgba(var(--admin-primary-rgb), 0.1);
        background: #fff;
    }
    .admin-list-page .admin-btn--primary,
    .admin-profile-page .admin-btn--primary,
    .admin-form-page .admin-btn--primary {
        color: #fff;
        background: var(--admin-primary);
        border-color: var(--admin-primary);
    }
    .admin-list-page .admin-btn--primary:hover,
    .admin-profile-page .admin-btn--primary:hover,
    .admin-form-page .admin-btn--primary:hover {
        background: var(--admin-primary-dark);
    }
    .admin-list-page .admin-btn--ghost,
    .admin-profile-page .admin-btn--ghost,
    .admin-form-page .admin-btn--ghost {
        color: #e2e8f0;
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.1);
    }
    .admin-list-page .admin-panel .admin-btn--ghost {
        color: #475569;
        background: #fff;
        border-color: #e2e8f0;
    }
    .admin-list-page .admin-panel .admin-btn--ghost:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .admin-list-page .admin-btn--outline,
    .admin-form-page .admin-btn--outline {
        color: #475569;
        background: #fff;
        border-color: #e2e8f0;
    }
    .admin-list-page .admin-btn--outline:hover,
    .admin-form-page .admin-btn--outline:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .admin-list-page .admin-btn,
    .admin-form-page .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.6rem 1rem;
        border-radius: 11px;
        font-size: 0.8125rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .admin-list-page .admin-detail-field {
        padding: 0.75rem 0.9rem;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #eef2f7;
    }
    .admin-list-page .admin-detail-field__label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    .admin-list-page .admin-detail-field__value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
    }
    .admin-list-page .admin-message-body {
        padding: 1rem 1.1rem;
        border-radius: 12px;
        border: 1px solid #eef2f7;
        background: #f8fafc;
        font-size: 0.875rem;
        line-height: 1.65;
        color: #334155;
        white-space: pre-line;
    }
    .admin-list-page .admin-alert,
    .admin-form-page .admin-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.95rem 1.1rem;
        border-radius: 14px;
        border: 1px solid;
        font-size: 0.875rem;
    }
    .admin-list-page .admin-alert--success,
    .admin-form-page .admin-alert--success {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .admin-list-page .admin-alert--success .admin-alert__icon,
    .admin-form-page .admin-alert--success .admin-alert__icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #dcfce7;
        color: #16a34a;
    }
    .admin-form-page .admin-textarea {
        width: 100%;
        border-radius: 11px;
        border: 1px solid #e2e8f0;
        background: #fafbfd;
        padding: 0.7rem 0.85rem;
        font-size: 0.8125rem;
        color: #0f172a;
        line-height: 1.5;
        resize: vertical;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .admin-form-page .admin-textarea:focus {
        outline: none;
        border-color: rgba(var(--admin-primary-rgb), 0.45);
        box-shadow: 0 0 0 3px rgba(var(--admin-primary-rgb), 0.1);
        background: #fff;
    }
    .admin-form-page .admin-field-hint {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.35rem;
        line-height: 1.45;
    }
    .admin-form-page .admin-field-hint code {
        font-size: 0.68rem;
        background: #f1f5f9;
        padding: 0.1rem 0.35rem;
        border-radius: 4px;
        color: #475569;
    }
    .admin-form-page .admin-thumb {
        width: 10rem;
        height: 7rem;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eef2f7;
        background: #f8fafc;
        margin-bottom: 0.75rem;
    }
    .admin-form-page .admin-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .admin-form-page .admin-checkbox-row {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
    }
    .admin-form-page .admin-checkbox-row input {
        border-radius: 6px;
        border-color: #cbd5e1;
        color: var(--admin-primary);
    }
    .admin-form-page .admin-form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        padding-top: 1rem;
        margin-top: 0.5rem;
        border-top: 1px solid #eef2f7;
    }
    .admin-form-page--full {
        width: 100%;
        max-width: none;
    }
    .admin-form-page .admin-form-layout__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 1024px) {
        .admin-form-page .admin-form-layout__grid {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
    }
    .admin-form-page .admin-form-side-card {
        padding: 1rem 1.1rem;
        border-radius: 12px;
        border: 1px solid #eef2f7;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .admin-form-page .admin-textarea--tall {
        min-height: 18rem;
    }
    .admin-form-page .admin-thumb--lg {
        width: 100%;
        max-width: 16rem;
        height: 9rem;
    }
    .admin-form-page .admin-input--mono { font-family: ui-monospace, monospace; font-size: 0.8rem; direction: ltr; text-align: left; }
    .admin-form-page .admin-file-input {
        display: block;
        width: 100%;
        font-size: 0.8125rem;
        color: #64748b;
    }
    .admin-form-page .admin-file-input::file-selector-button {
        margin-left: 0.75rem;
        padding: 0.5rem 0.85rem;
        border-radius: 9px;
        border: 0;
        background: var(--admin-primary-light);
        color: var(--admin-primary-dark);
        font-weight: 700;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .admin-list-page .admin-alert-inline {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.85rem;
        border-radius: 9px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #be123c;
        background: #fff1f2;
        border: 1px solid #fecdd3;
    }
        background: #1e293b !important;
        border-color: #334155 !important;
    }
        background: rgba(15, 23, 42, 0.5) !important;
        border-bottom-color: #334155 !important;
    }
</style>

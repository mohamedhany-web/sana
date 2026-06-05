
<?php if (! $__env->hasRenderedOnce('1d6fff68-4972-4954-ad98-f48af7bec4f0')): $__env->markAsRenderedOnce('1d6fff68-4972-4954-ad98-f48af7bec4f0'); ?>
<?php
    $mxPlatformName = config('brand.name', config('app.name', 'Sana'));
    $mxLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    *, *::before, *::after { box-sizing: border-box; }
    body, .mx-join-page, .mx-meeting-body, .mx-join-card, .mx-meeting-room-header {
        font-family: var(--edu-font);
    }
    /* Font Awesome — لا تُطبَّق خط المنصة على أيقونات ::before */
    .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-brands {
        font-family: "Font Awesome 6 Free" !important;
        font-style: normal;
        font-variant: normal;
        line-height: 1;
        display: inline-block;
        -webkit-font-smoothing: antialiased;
    }
    .fas, .fa-solid { font-weight: 900 !important; }
    .far, .fa-regular { font-weight: 400 !important; }
    .fab, .fa-brands {
        font-family: "Font Awesome 6 Brands" !important;
        font-weight: 400 !important;
    }
    .mx-join-card__icon i,
    .mx-join-badge i,
    .mx-btn-join i,
    .mx-btn-meeting i {
        line-height: 1;
    }

    /* ── صفحة الانضمام (فاتحة — مثل لوحة المنصة) ── */
    .mx-join-page {
        min-height: 100vh;
        min-height: 100dvh;
        margin: 0;
        background: var(--edu-bg);
        color: var(--edu-text);
        position: relative;
        overflow-x: hidden;
    }
    .mx-join-page::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 80% 50% at 100% 0%, rgba(var(--edu-primary-rgb), 0.08) 0%, transparent 55%),
            radial-gradient(ellipse 60% 40% at 0% 100%, rgba(var(--edu-purple-rgb), 0.06) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }
    .mx-join-nav {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem clamp(1rem, 4vw, 2rem);
        gap: 1rem;
    }
    .mx-join-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        text-decoration: none;
        color: var(--edu-navy);
        font-weight: 800;
        font-size: 1.05rem;
        letter-spacing: -0.02em;
    }
    .mx-join-brand img {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 14px -6px rgba(var(--edu-primary-rgb), 0.28);
    }
    .mx-join-brand span em { color: var(--edu-primary); font-style: normal; }
    .mx-join-stage {
        position: relative;
        z-index: 2;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem clamp(1rem, 4vw, 2rem) 3rem;
    }
    .mx-join-card {
        width: 100%;
        max-width: 28rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius-sm);
        box-shadow: var(--edu-shadow);
        padding: clamp(1.5rem, 4vw, 2rem);
    }
    .mx-join-card__icon {
        width: 4rem;
        height: 4rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(var(--edu-primary-rgb), 0.12) 0%, rgba(var(--edu-purple-rgb), 0.1) 100%);
        color: var(--edu-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 1.5rem;
    }
    .mx-join-card__icon--muted {
        background: #f1f5f9;
        color: #94a3b8;
    }
    .mx-join-title {
        text-align: center;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--edu-navy);
        margin: 0 0 0.35rem;
    }
    .mx-join-lead {
        text-align: center;
        font-size: 0.875rem;
        color: var(--edu-muted);
        margin: 0 0 1.5rem;
        line-height: 1.6;
    }
    .mx-join-meta {
        text-align: center;
        font-size: 0.75rem;
        color: var(--edu-muted);
        margin-bottom: 1rem;
    }
    .mx-join-code {
        font-family: ui-monospace, monospace;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--edu-primary);
        letter-spacing: 0.08em;
    }
    .mx-join-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        background: var(--edu-primary-light);
        color: var(--edu-primary-dark);
        border: 1px solid rgba(var(--edu-primary-rgb), 0.15);
    }
    .mx-join-field label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .mx-join-field input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 0.9rem;
        color: var(--edu-navy);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .mx-join-field input:focus {
        outline: none;
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), 0.12);
    }
    .mx-btn-join {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1.25rem;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #fff;
        background: var(--edu-gradient-cta);
        box-shadow: 0 8px 24px -8px rgba(var(--edu-primary-rgb), 0.45);
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
    }
    .mx-btn-join:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px -8px rgba(var(--edu-primary-rgb), 0.5);
    }
    .mx-btn-join:disabled { opacity: 0.65; cursor: wait; }
    .mx-join-hint {
        text-align: center;
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 1rem;
    }

    /* ── صفحة الانضمام (داكنة — مثل غرفة الاجتماع) ── */
    .mx-join-page--room {
        background: #0b1220;
        color: #e2e8f0;
    }
    .mx-join-page--room::before { display: none; }
    .mx-join-page--room .mx-join-nav {
        background: linear-gradient(135deg, #0b1f3a 0%, var(--edu-primary-dark) 48%, var(--edu-purple-dark) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.45);
    }
    .mx-join-page--room .mx-join-brand {
        color: rgba(255, 255, 255, 0.92);
    }
    .mx-join-page--room .mx-join-brand span em { color: #a5b4fc; }
    .mx-join-page--room .mx-join-badge {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.85);
        border-color: rgba(255, 255, 255, 0.12);
    }
    .mx-join-card--dark {
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 48px -12px rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(8px);
    }
    .mx-join-page--room .mx-join-card__icon {
        background: rgba(99, 102, 241, 0.18);
        color: #a5b4fc;
        border: 1px solid rgba(129, 140, 248, 0.25);
    }
    .mx-join-page--room .mx-join-card__icon--muted {
        background: rgba(51, 65, 85, 0.5);
        color: #94a3b8;
        border-color: rgba(148, 163, 184, 0.15);
    }
    .mx-join-page--room .mx-join-title { color: #f8fafc; }
    .mx-join-page--room .mx-join-lead { color: #94a3b8; }
    .mx-join-page--room .mx-join-meta { color: #94a3b8; }
    .mx-join-page--room .mx-join-meta strong,
    .mx-join-page--room .mx-join-meta .text-slate-700 { color: #e2e8f0 !important; }
    .mx-join-page--room .mx-join-code { color: #a5b4fc; }
    .mx-join-user-chip {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        background: rgba(30, 41, 59, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.2);
        margin-bottom: 1rem;
    }
    .mx-join-user-chip__avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(var(--edu-primary-rgb), 0.35), rgba(var(--edu-purple-rgb), 0.3));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .mx-join-page--room .mx-join-field label { color: #cbd5e1; }
    .mx-join-page--room .mx-join-field input {
        background: rgba(15, 23, 42, 0.9);
        border-color: rgba(148, 163, 184, 0.25);
        color: #f1f5f9;
    }
    .mx-join-page--room .mx-join-field input:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .mx-join-page--room .mx-join-hint { color: #64748b; }

    /* ── غرفة الاجتماع (هيدر داكن بألوان العلامة) ── */
    .mx-meeting-body {
        margin: 0;
        padding: 0;
        background: #0b1220;
        overflow: hidden;
        min-height: 100vh;
        min-height: 100dvh;
        height: 100vh;
        height: 100dvh;
        display: flex;
        flex-direction: column;
    }
    .mx-meeting-room-header {
        flex-shrink: 0;
        min-height: 3.5rem;
        background: linear-gradient(135deg, #0b1f3a 0%, var(--edu-primary-dark) 48%, var(--edu-purple-dark) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.45);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem clamp(0.75rem, 3vw, 1.5rem);
        padding-top: max(0.5rem, env(safe-area-inset-top));
        gap: 0.75rem;
    }
    .mx-meeting-brand-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: rgba(255, 255, 255, 0.85);
        transition: color 0.15s;
    }
    .mx-meeting-brand-link:hover { color: #fff; }
    .mx-meeting-brand-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        overflow: hidden;
        flex-shrink: 0;
    }
    .mx-meeting-brand-icon img { width: 100%; height: 100%; object-fit: cover; }
    .mx-meeting-brand-name {
        font-weight: 800;
        font-size: 0.85rem;
        color: #fff;
    }
    .mx-meeting-live-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
        animation: mx-pulse-live 1.5s ease-in-out infinite;
        flex-shrink: 0;
    }
    .mx-meeting-live-dot--green {
        background: #34d399;
        box-shadow: 0 0 8px rgba(52, 211, 153, 0.5);
    }
    @keyframes mx-pulse-live {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.65; transform: scale(0.92); }
    }
    .mx-meeting-title {
        font-weight: 700;
        font-size: 0.8rem;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 12rem;
    }
    @media (min-width: 640px) { .mx-meeting-title { max-width: 20rem; font-size: 0.875rem; } }
    .mx-meeting-code-chip {
        font-family: ui-monospace, monospace;
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .mx-meeting-room-body {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .mx-jitsi-root {
        flex: 1;
        min-height: 0;
        background: #0f172a;
        position: relative;
    }
    .mx-jitsi-root iframe {
        width: 100% !important;
        height: 100% !important;
        border: none;
    }
    .mx-btn-meeting {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.85rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        text-decoration: none;
    }
    .mx-btn-meeting--ghost {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        color: rgba(255, 255, 255, 0.9);
    }
    .mx-btn-meeting--ghost:hover { background: rgba(255, 255, 255, 0.14); }
    .mx-btn-meeting--accent {
        background: rgba(var(--edu-accent-rgb), 0.2);
        border-color: rgba(var(--edu-accent-rgb), 0.35);
        color: #fff;
    }
    .mx-btn-meeting--accent:hover { background: rgba(var(--edu-accent-rgb), 0.3); }
    .mx-btn-meeting--danger {
        background: #dc2626;
        border-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        box-shadow: 0 4px 14px -4px rgba(220, 38, 38, 0.5);
    }
    .mx-btn-meeting--danger:hover { background: #b91c1c; }
    .mx-meeting-ended-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(11, 18, 32, 0.94);
        backdrop-filter: blur(10px);
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        text-align: center;
        padding: 2rem;
    }
    .mx-meeting-ended-overlay.is-visible { display: flex; }
    .mx-meeting-ended-overlay h2 {
        color: #f1f5f9;
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0;
    }
    .mx-meeting-ended-overlay p { color: #94a3b8; font-size: 0.875rem; margin: 0; }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/partials/classroom-meeting-theme.blade.php ENDPATH**/ ?>
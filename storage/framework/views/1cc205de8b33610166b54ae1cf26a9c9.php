
<?php if (! $__env->hasRenderedOnce('24ba7f83-86a3-42ea-b703-f564bf21340a')): $__env->markAsRenderedOnce('24ba7f83-86a3-42ea-b703-f564bf21340a'); ?>
<script>
(function () {
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
    try { localStorage.setItem('theme', 'light'); } catch (e) {}
})();
</script>
<style>
    .app-shell-parent {
        --stu-primary: <?php echo e(config('parent.colors.primary_dark')); ?>;
        --stu-accent: <?php echo e(config('parent.colors.accent')); ?>;
        --stu-rose: <?php echo e(config('parent.colors.soft')); ?>;
    }

    .app-shell-parent .app-sidebar {
        width: 272px;
        background: #fff;
        border-left: 1px solid #ccfbf1;
        box-shadow: -6px 0 32px -20px rgba(13, 148, 136, 0.18);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        min-height: 100dvh;
    }

    .app-shell-parent .ins-sidebar-brand {
        background: linear-gradient(135deg, rgba(236, 253, 245, 0.85) 0%, #fff 58%);
        border-bottom: 1px solid #ccfbf1;
        padding: 1rem 1rem 0.85rem;
    }

    .app-shell-parent .stu-sidebar-stats {
        padding: 0.25rem 0.75rem 0.65rem;
        flex-shrink: 0;
    }
    .app-shell-parent .stu-sidebar-stats-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 0.875rem;
    }

    .app-shell-parent .stu-sidebar-stat {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f0fdfa;
        padding: 0.65rem 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .app-shell-parent .stu-sidebar-stat:hover {
        border-color: rgba(13, 148, 136, 0.22);
        box-shadow: 0 8px 22px -14px rgba(13, 148, 136, 0.25);
        transform: translateY(-1px);
    }

    .app-shell-parent .ins-nav-group {
        color: #64748b;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 1rem 1rem 0.35rem;
    }

    .app-shell-parent .ins-nav {
        margin: 2px 10px;
        padding: 0.55rem 0.7rem;
        border-radius: 12px;
        font-size: 0.8125rem;
    }
    .app-shell-parent .ins-nav.active {
        background: linear-gradient(90deg, rgba(236, 253, 245, 0.95), rgba(204, 251, 241, 0.45)) !important;
        color: var(--stu-primary) !important;
        border-color: rgba(13, 148, 136, 0.15) !important;
        font-weight: 700;
    }
    .app-shell-parent .ins-nav::before {
        width: 3px;
        border-radius: 3px 0 0 3px;
        background: linear-gradient(180deg, <?php echo e(config('parent.colors.primary')); ?>, <?php echo e(config('parent.colors.accent')); ?>) !important;
    }
    .app-shell-parent .ins-nav .ins-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 10px;
    }

    .app-shell-parent .ins-user-card {
        background: linear-gradient(135deg, #f0fdfa 0%, #fff 100%);
        border: 1px solid #ccfbf1;
        border-radius: 14px;
    }

    .app-shell-parent .stu-topbar {
        height: 68px;
        background: rgba(255, 255, 255, 0.92);
        border-bottom: 1px solid #ccfbf1;
        backdrop-filter: blur(14px);
        box-shadow: 0 1px 0 rgba(13, 148, 136, 0.04);
    }

    .app-shell-parent .stu-topbar-search {
        flex: 1;
        max-width: 28rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .app-shell-parent .stu-topbar-search:focus-within {
        background: #fff;
        border-color: var(--stu-primary);
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
    }
    .app-shell-parent .stu-topbar-search input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.875rem;
        color: #334155;
        min-width: 0;
    }
    .app-shell-parent .stu-topbar-search input::placeholder { color: #94a3b8; }

    .app-shell-parent .stu-icon-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .app-shell-parent .stu-icon-btn:hover {
        border-color: rgba(13, 148, 136, 0.2);
        color: var(--stu-primary);
        background: var(--stu-rose);
    }

    .app-shell-parent .stu-user-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.5rem 0.35rem 0.35rem;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .app-shell-parent .stu-user-chip:hover {
        border-color: rgba(13, 148, 136, 0.2);
        box-shadow: 0 4px 16px -8px rgba(13, 148, 136, 0.2);
    }
    .app-shell-parent .stu-user-chip .u-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        background: linear-gradient(135deg, <?php echo e(config('parent.colors.primary')); ?>, <?php echo e(config('parent.colors.accent')); ?>);
    }

    .app-shell-parent main {
        background: linear-gradient(180deg, #f0fdfa 0%, #f7f8ff 80px, #f7f8ff 100%) !important;
    }

    .app-shell-parent .stu-dd {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 16px 48px -20px rgba(15, 23, 42, 0.2);
        overflow: hidden;
    }

    /* ── محتوى الصفحات ── */
    .par-page {
        width: 100%;
        max-width: none;
    }
    .par-page-title {
        font-size: clamp(1.25rem, 4.5vw, 1.5rem);
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
    }
    .par-page-lead {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 0.35rem;
        line-height: 1.5;
    }
    .par-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        box-shadow: 0 4px 20px -12px rgba(15, 23, 42, 0.08);
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
    }
    .par-hero {
        border-radius: 1.25rem;
        border: 1px solid #99f6e4;
        background: linear-gradient(135deg, rgba(236, 253, 245, 0.9) 0%, #fff 55%, #f8fafc 100%);
        box-shadow: 0 12px 40px -24px rgba(13, 148, 136, 0.18);
    }
    .par-layout-narrow {
        width: 100%;
        max-width: none;
    }
    .par-form-layout {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 1280px) {
        .par-form-layout {
            grid-template-columns: minmax(260px, 300px) 1fr;
            align-items: start;
            gap: 1.25rem;
        }
    }
    .par-form-columns {
        display: grid;
        gap: 1rem;
    }
    @media (min-width: 1024px) {
        .par-form-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; }
    }
    .par-section-head {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        font-size: 0.9375rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
    }
    .par-section-head i {
        width: 2rem;
        height: 2rem;
        border-radius: 0.65rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .par-field label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .par-field input {
        width: 100%;
        border-radius: 0.75rem;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.9375rem;
        color: #0f172a;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .par-field input:focus {
        outline: none;
        border-color: #0d9488;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
    }
    .par-field input[type="file"] {
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        background: #f8fafc;
    }
    .par-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.7rem 1.35rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
    }
    .par-btn--primary {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: #fff;
        box-shadow: 0 8px 22px -10px rgba(13, 148, 136, 0.55);
    }
    .par-btn--primary:hover {
        background: linear-gradient(135deg, #0f766e, #115e59);
        transform: translateY(-1px);
    }
    .par-btn--ghost {
        background: #f0fdfa;
        color: #0f766e;
        border: 1px solid #99f6e4;
        text-decoration: none;
    }
    .par-btn--ghost:hover {
        background: #ccfbf1;
    }
    .par-avatar {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 1.125rem;
        background: linear-gradient(135deg, #0d9488, #d97706);
        color: #fff;
        font-size: 1.5rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: 0 8px 24px -10px rgba(13, 148, 136, 0.45);
    }
    .par-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .par-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.6875rem;
        font-weight: 700;
    }
    .par-badge--teal { background: #ccfbf1; color: #0f766e; }
    .par-badge--amber { background: #fef3c7; color: #b45309; }
    .par-child-card {
        display: block;
        text-decoration: none;
        color: inherit;
        padding: 1.1rem 1.15rem;
    }
    .par-child-card:hover {
        border-color: #5eead4;
        box-shadow: 0 14px 36px -18px rgba(13, 148, 136, 0.28);
        transform: translateY(-2px);
    }
    .par-progress {
        height: 0.5rem;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .par-progress > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #0d9488, #14b8a6);
    }
    .par-empty {
        text-align: center;
        padding: 2.5rem 1.5rem;
        color: #64748b;
    }
    .par-empty-icon {
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        border-radius: 1rem;
        background: #f0fdfa;
        color: #0d9488;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        opacity: 0.85;
    }
    .par-info-grid {
        display: grid;
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .par-info-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (min-width: 1024px) {
        .par-info-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    .app-shell-parent main > div {
        width: 100%;
        max-width: none !important;
    }
    .par-info-tile {
        padding: 1rem 1.1rem;
        border-radius: 0.875rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .par-info-tile strong { color: #0f172a; }
    .par-flash {
        border-radius: 0.875rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .par-flash--ok {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }
    .par-kpi-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }
    @media (min-width: 1024px) {
        .par-kpi-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
    }
    .par-kpi-grid--3 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    @media (min-width: 640px) {
        .par-kpi-grid--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .par-kpi-grid--3 .par-kpi-span-2-sm { grid-column: auto; }
    }
    .par-kpi-grid--3 .par-kpi-span-2-sm { grid-column: span 2; }
    .par-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .par-report-cards { display: none; }
    @media (max-width: 639px) {
        .par-report-table { display: none; }
        .par-report-cards { display: block; }
    }

    /* ── جوال: سايدبار + هيدر + قوائم ── */
    @media (max-width: 1023px) {
        .app-shell-parent .app-sidebar {
            width: min(300px, 88vw);
            max-width: 88vw;
        }
    }
    @media (max-width: 639px) {
        .app-shell-parent .app-sidebar {
            width: min(288px, 92vw);
            max-width: 92vw;
        }
        .app-shell-parent .ins-sidebar-brand {
            padding: 0.85rem 0.85rem 0.75rem;
        }
        .app-shell-parent .platform-brand--sidebar .platform-brand__name {
            font-size: 0.9375rem;
        }
        .app-shell-parent .platform-brand--sidebar .platform-brand__tagline {
            font-size: 0.625rem;
            line-height: 1.25;
        }
        .app-shell-parent .stu-sidebar-stats {
            padding: 0.5rem 0.65rem 0.55rem;
        }
        .app-shell-parent .stu-sidebar-stat {
            padding: 0.55rem 0.6rem;
        }
        .app-shell-parent .stu-sidebar-stat .text-xl {
            font-size: 1.125rem;
        }
        .app-shell-parent .ins-nav {
            margin-inline: 6px;
            padding: 0.65rem 0.65rem;
            min-height: 44px;
        }
        .app-shell-parent .ins-nav .ins-icon {
            width: 2.125rem;
            height: 2.125rem;
        }
        .app-shell-parent .stu-topbar {
            height: 56px;
            padding-inline: 0.75rem;
            gap: 0.5rem;
        }
        .app-shell-parent .stu-icon-btn {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 10px;
            flex-shrink: 0;
        }
        .app-shell-parent .stu-user-chip {
            padding: 0.2rem;
            border: none;
            background: transparent;
        }
        .app-shell-parent .stu-user-chip .u-avatar {
            width: 2.125rem;
            height: 2.125rem;
        }
        .app-shell-parent .stu-dd {
            width: min(20rem, calc(100vw - 1.25rem)) !important;
            max-width: calc(100vw - 1.25rem);
        }
        .app-shell-parent .stu-dd--menu {
            inset-inline-start: auto;
            inset-inline-end: 0;
        }
        .app-shell-parent main > div {
            padding: 0.875rem !important;
        }
        .par-page .par-hero {
            padding: 1rem 1.1rem !important;
        }
        .par-kpi-grid {
            gap: 0.625rem;
        }
        .par-kpi-grid .par-card {
            padding: 0.875rem !important;
        }
        .par-child-row {
            flex-wrap: wrap;
            gap: 0.75rem !important;
        }
        .par-child-row .text-end {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: start !important;
            padding-top: 0.25rem;
            border-top: 1px solid #f1f5f9;
        }
    }

    @supports (padding: max(0px)) {
        .app-shell-parent .app-sidebar {
            padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
        }
        .app-shell-parent .stu-topbar {
            padding-top: env(safe-area-inset-top);
        }
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\parent-app-shell.blade.php ENDPATH**/ ?>
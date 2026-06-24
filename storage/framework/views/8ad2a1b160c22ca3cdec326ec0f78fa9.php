
<?php if (! $__env->hasRenderedOnce('9aba48ac-5504-47e0-bc55-943e2065333b')): $__env->markAsRenderedOnce('9aba48ac-5504-47e0-bc55-943e2065333b'); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    .platform-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        transition: opacity 0.2s;
    }
    .platform-brand:hover { opacity: 0.92; }
    .platform-brand__mark {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        overflow: hidden;
    }
    .platform-brand__mark--img {
        width: 2.5rem;
        height: 2.5rem;
        box-shadow: 0 4px 14px -6px rgba(var(--edu-primary-rgb), 0.45);
    }
    .platform-brand__mark--img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .platform-brand__mark--letter {
        width: 2.5rem;
        height: 2.5rem;
        font-weight: 900;
        font-size: 1.05rem;
        color: #fff;
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-accent-dark));
        box-shadow: 0 4px 14px -6px rgba(var(--edu-primary-rgb), 0.4);
    }
    .platform-brand__name {
        display: block;
        font-weight: 800;
        font-size: 1rem;
        line-height: 1.2;
        color: #0f172a;
    }
    .platform-brand__tagline {
        display: block;
        font-size: 0.6875rem;
        font-weight: 600;
        color: var(--edu-muted);
        margin-top: 0.1rem;
        line-height: 1.3;
    }
    .platform-brand--sidebar .platform-brand__mark--img,
    .platform-brand--sidebar .platform-brand__mark--letter {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 14px;
    }
    .platform-brand--header .platform-brand__name { font-size: 0.9375rem; }
    .platform-brand--nav .platform-brand__mark--img,
    .platform-brand--nav .platform-brand__mark--letter {
        width: 2.5rem;
        height: 2.5rem;
    }
    .platform-brand--nav .platform-brand__name { font-size: 1.05rem; }
    .platform-brand--nav-stack .platform-brand__name { font-size: 1.125rem; }
    @media (min-width: 1024px) {
        .platform-brand--nav-stack .platform-brand__name { font-size: 1.25rem; }
    }
    @media (max-width: 639px) {
        .platform-brand--nav .platform-brand__text { display: none; }
    }

    body.app-shell-body {
        background: var(--edu-bg);
        font-family: var(--edu-font), 'Cairo', 'Tajawal', system-ui, sans-serif;
    }

    .app-sidebar {
        width: 268px;
        background: #fff;
        border-left: 1px solid #e8eaf6;
        box-shadow: -4px 0 24px -16px rgba(var(--edu-primary-rgb), 0.12);
    }

    .ins-sidebar-brand {
        background: linear-gradient(135deg, rgba(255, 229, 247, 0.55) 0%, #fff 55%);
        border-bottom: 1px solid #e8eaf6;
    }

    .app-header {
        height: 64px;
        background: rgba(255, 255, 255, 0.94);
        border-bottom: 1px solid #e8eaf6;
        backdrop-filter: blur(14px);
        box-shadow: 0 1px 0 rgba(var(--edu-primary-rgb), 0.04);
    }

    .app-main-surface {
        background: linear-gradient(180deg, #f4f6ff 0%, var(--edu-bg) 120px, var(--edu-bg) 100%);
    }

    .h-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        color: var(--edu-muted);
        transition: all 0.15s;
    }
    .h-btn:hover {
        background: var(--edu-primary-light);
        color: var(--edu-primary);
        border-color: rgba(var(--edu-primary-rgb), 0.2);
    }

    .search-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .search-box:focus-within {
        background: #fff;
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), 0.1);
    }

    .ins-nav.active {
        background: var(--edu-primary-light) !important;
        color: var(--edu-primary-dark) !important;
        border-color: rgba(var(--edu-primary-rgb), 0.15) !important;
    }
    .ins-nav::before {
        background: linear-gradient(180deg, var(--edu-primary), var(--edu-accent-dark)) !important;
    }

    .ins-stat-card:hover {
        border-color: rgba(var(--edu-primary-rgb), 0.25) !important;
    }

    .u-avatar {
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-accent-dark));
        border-radius: 10px;
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\edu-app-shell.blade.php ENDPATH**/ ?>
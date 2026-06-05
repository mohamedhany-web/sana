
<?php if (! $__env->hasRenderedOnce('8f74ade8-6e6f-4a88-bae9-2ef012d81459')): $__env->markAsRenderedOnce('8f74ade8-6e6f-4a88-bae9-2ef012d81459'); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    .app-shell-instructor {
        --ins-primary: #283593;
        --ins-accent: #FB5607;
        --ins-rose: #FFE5F7;
        --ins-soft: #f4f6ff;
    }

    .app-shell-instructor .app-sidebar {
        width: 276px;
        background: #fff;
        border-left: 1px solid #e8eaf6;
        box-shadow: -8px 0 36px -22px rgba(40, 53, 147, 0.22);
    }

    .app-shell-instructor .ins-sidebar-brand {
        background: linear-gradient(135deg, rgba(255, 229, 247, 0.7) 0%, #fff 55%);
        border-bottom: 1px solid #e8eaf6;
        padding: 1rem 1rem 0.9rem;
    }

    .app-shell-instructor .ins-sidebar-stats {
        padding: 0 0.75rem 0.7rem;
    }
    .app-shell-instructor .ins-sidebar-stat {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 0.65rem 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .app-shell-instructor .ins-sidebar-stat:hover {
        border-color: rgba(40, 53, 147, 0.24);
        box-shadow: 0 8px 22px -14px rgba(31, 42, 122, 0.28);
        transform: translateY(-1px);
    }

    .app-shell-instructor .ins-nav-group {
        color: #64748b;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 1rem 1rem 0.35rem;
    }

    .app-shell-instructor .ins-nav {
        margin: 2px 10px;
        padding: 0.55rem 0.7rem;
        border-radius: 12px;
        font-size: 0.8125rem;
    }
    .app-shell-instructor .ins-nav.active {
        background: linear-gradient(90deg, rgba(255, 229, 247, 0.95), rgba(238, 242, 255, 0.55)) !important;
        color: var(--ins-primary) !important;
        border-color: rgba(40, 53, 147, 0.14) !important;
        font-weight: 700;
    }
    .app-shell-instructor .ins-nav::before {
        width: 3px;
        border-radius: 3px 0 0 3px;
        background: linear-gradient(180deg, var(--ins-primary), var(--ins-accent)) !important;
    }
    .app-shell-instructor .ins-nav .ins-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 10px;
    }

    .app-shell-instructor .ins-stat-card {
        border-radius: 14px;
    }

    .app-shell-instructor .ins-user-card {
        background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }

    .app-shell-instructor .ins-topbar {
        height: 68px;
        background: rgba(255, 255, 255, 0.94);
        border-bottom: 1px solid #e8eaf6;
        backdrop-filter: blur(14px);
        box-shadow: 0 1px 0 rgba(40, 53, 147, 0.05);
    }

    .app-shell-instructor .ins-topbar-search {
        flex: 1;
        max-width: 22rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .app-shell-instructor .ins-topbar-search:focus-within {
        background: #fff;
        border-color: var(--ins-primary);
        box-shadow: 0 0 0 4px rgba(40, 53, 147, 0.1);
    }
    .app-shell-instructor .ins-topbar-search input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.875rem;
        color: #334155;
        min-width: 0;
    }

    .app-shell-instructor .ins-icon-btn {
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
    .app-shell-instructor .ins-icon-btn:hover {
        border-color: rgba(40, 53, 147, 0.22);
        color: var(--ins-primary);
        background: var(--ins-rose);
    }

    .app-shell-instructor .ins-user-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.5rem 0.35rem 0.35rem;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .app-shell-instructor .ins-user-chip:hover {
        border-color: rgba(40, 53, 147, 0.22);
        box-shadow: 0 4px 16px -8px rgba(31, 42, 122, 0.22);
    }
    .app-shell-instructor .ins-user-chip .u-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--ins-primary), var(--ins-accent));
    }

    .app-shell-instructor .ins-page-title {
        font-size: 0.9375rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: min(280px, 40vw);
    }
    .app-shell-instructor .ins-page-breadcrumb {
        font-size: 0.6875rem;
        font-weight: 600;
        color: #64748b;
    }

    .app-shell-instructor main {
        background: linear-gradient(180deg, #f4f6ff 0%, #f7f8ff 72px, #f7f8ff 100%) !important;
    }
    .app-shell-instructor main > div {
        width: 100%;
        max-width: none !important;
    }

    .app-shell-instructor .ins-dd {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 16px 48px -20px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }

    .app-shell-instructor .ins-flash {
        border-radius: 14px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/layouts/partials/instructor-app-shell.blade.php ENDPATH**/ ?>
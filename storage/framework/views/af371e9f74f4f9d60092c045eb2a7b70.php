
<?php if (! $__env->hasRenderedOnce('9ecbd1a0-3c3a-483f-92dd-f549fc0dc501')): $__env->markAsRenderedOnce('9ecbd1a0-3c3a-483f-92dd-f549fc0dc501'); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    .app-shell-student {
        --stu-primary: #283593;
        --stu-accent: #FB5607;
        --stu-rose: #FFE5F7;
    }

    .app-shell-student .app-sidebar {
        width: 272px;
        background: #fff;
        border-left: 1px solid #e8eaf6;
        box-shadow: -6px 0 32px -20px rgba(40, 53, 147, 0.18);
    }
    .dark .app-shell-student .app-sidebar {
        background: #0f172a;
        border-left-color: #1e293b;
        box-shadow: -6px 0 32px -20px rgba(0, 0, 0, 0.45);
    }

    .app-shell-student .ins-sidebar-brand {
        background: linear-gradient(135deg, rgba(255, 229, 247, 0.65) 0%, #fff 58%);
        border-bottom: 1px solid #e8eaf6;
        padding: 1rem 1rem 0.85rem;
    }
    .dark .app-shell-student .ins-sidebar-brand {
        background: linear-gradient(135deg, rgba(40, 53, 147, 0.28) 0%, #0f172a 65%);
        border-bottom-color: #334155;
    }

    .app-shell-student .stu-sidebar-stats {
        padding: 0 0.75rem 0.65rem;
    }
    .app-shell-student .stu-sidebar-stat {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 0.65rem 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .app-shell-student .stu-sidebar-stat:hover {
        border-color: rgba(40, 53, 147, 0.22);
        box-shadow: 0 8px 22px -14px rgba(31, 42, 122, 0.25);
        transform: translateY(-1px);
    }
    .dark .app-shell-student .stu-sidebar-stat {
        background: rgba(30, 41, 59, 0.65);
        border-color: #334155;
    }

    .app-shell-student .ins-nav-group {
        color: #64748b;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 1rem 1rem 0.35rem;
    }
    .dark .app-shell-student .ins-nav-group { color: #94a3b8; }

    .app-shell-student .ins-nav {
        margin: 2px 10px;
        padding: 0.55rem 0.7rem;
        border-radius: 12px;
        font-size: 0.8125rem;
    }
    .app-shell-student .ins-nav.active {
        background: linear-gradient(90deg, rgba(255, 229, 247, 0.9), rgba(238, 242, 255, 0.5)) !important;
        color: var(--stu-primary) !important;
        border-color: rgba(40, 53, 147, 0.12) !important;
        font-weight: 700;
    }
    .dark .app-shell-student .ins-nav.active {
        background: rgba(40, 53, 147, 0.35) !important;
        color: #c7d2fe !important;
        border-color: rgba(99, 102, 241, 0.25) !important;
    }
    .app-shell-student .ins-nav::before {
        width: 3px;
        border-radius: 3px 0 0 3px;
        background: linear-gradient(180deg, var(--stu-primary), var(--stu-accent)) !important;
    }
    .app-shell-student .ins-nav .ins-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 10px;
    }

    .app-shell-student .ins-user-card {
        background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }
    .dark .app-shell-student .ins-user-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-color: #334155;
    }

    .app-shell-student .stu-topbar {
        height: 68px;
        background: rgba(255, 255, 255, 0.92);
        border-bottom: 1px solid #e8eaf6;
        backdrop-filter: blur(14px);
        box-shadow: 0 1px 0 rgba(40, 53, 147, 0.04);
    }
    .dark .app-shell-student .stu-topbar {
        background: rgba(15, 23, 42, 0.92);
        border-bottom-color: #1e293b;
    }

    .app-shell-student .stu-topbar-search {
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
    .app-shell-student .stu-topbar-search:focus-within {
        background: #fff;
        border-color: var(--stu-primary);
        box-shadow: 0 0 0 4px rgba(40, 53, 147, 0.1);
    }
    .dark .app-shell-student .stu-topbar-search {
        background: #1e293b;
        border-color: #334155;
    }
    .dark .app-shell-student .stu-topbar-search:focus-within {
        border-color: #818cf8;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }
    .app-shell-student .stu-topbar-search input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.875rem;
        color: #334155;
        min-width: 0;
    }
    .dark .app-shell-student .stu-topbar-search input { color: #e2e8f0; }
    .app-shell-student .stu-topbar-search input::placeholder { color: #94a3b8; }

    .app-shell-student .stu-icon-btn {
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
    .app-shell-student .stu-icon-btn:hover {
        border-color: rgba(40, 53, 147, 0.2);
        color: var(--stu-primary);
        background: var(--stu-rose);
    }
    .dark .app-shell-student .stu-icon-btn {
        background: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }

    .app-shell-student .stu-user-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.5rem 0.35rem 0.35rem;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .app-shell-student .stu-user-chip:hover {
        border-color: rgba(40, 53, 147, 0.2);
        box-shadow: 0 4px 16px -8px rgba(31, 42, 122, 0.2);
    }
    .dark .app-shell-student .stu-user-chip {
        background: #1e293b;
        border-color: #334155;
    }
    .app-shell-student .stu-user-chip .u-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--stu-primary), var(--stu-accent));
    }

    .app-shell-student main {
        background: linear-gradient(180deg, #f4f6ff 0%, #f7f8ff 80px, #f7f8ff 100%) !important;
    }
    .dark .app-shell-student main {
        background: linear-gradient(180deg, #0f172a 0%, #0c1222 80px, #0c1222 100%) !important;
    }

    .app-shell-student .stu-dd {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 16px 48px -20px rgba(15, 23, 42, 0.2);
        overflow: hidden;
    }
    .dark .app-shell-student .stu-dd {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 16px 48px -12px rgba(0, 0, 0, 0.45);
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\student-app-shell.blade.php ENDPATH**/ ?>
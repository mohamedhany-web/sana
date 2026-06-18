
<?php if (! $__env->hasRenderedOnce('9f048124-d624-4588-9678-ec498e73186a')): $__env->markAsRenderedOnce('9f048124-d624-4588-9678-ec498e73186a'); ?>
<style>
    .app-shell-student {
        --sanua-rail-w: 96px;
        --sanua-gap: 16px;
        --sanua-content-pad: 32px;
        --sanua-radius: 24px;
        --sanua-canvas: #F8F7FC;
        --sanua-gradient: linear-gradient(180deg, #5B21B6 0%, #7C3AED 55%, #8B5CF6 100%);
        display: grid;
        grid-template-columns: minmax(0, 1fr) calc(var(--sanua-rail-w) + var(--sanua-gap));
        grid-template-rows: minmax(0, 1fr);
        direction: ltr;
        gap: var(--sanua-gap);
        padding: var(--sanua-gap);
        width: 100%;
        min-height: 100dvh;
        box-sizing: border-box;
        overflow-x: hidden;
        background: var(--sanua-canvas);
    }

    body:has(.app-shell-student) { background: var(--sanua-canvas); overflow-x: hidden; }

    /* Main column — لوحة بيضاء عائمة */
    .app-shell-student .sanua-main-wrap {
        grid-column: 1;
        grid-row: 1;
        direction: rtl;
        width: 100%;
        max-width: none;
        min-width: 0;
        min-height: calc(100dvh - var(--sanua-gap) * 2);
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: var(--sanua-radius);
        box-shadow: 0 4px 24px -8px rgba(91, 33, 182, 0.12);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    /* سايدبار عائم — fixed يمين مع حواف دائرية وظل */
    .app-shell-student > aside.app-sidebar.sanua-rail {
        grid-column: 2;
        grid-row: 1;
        direction: rtl;
        position: fixed !important;
        top: var(--sanua-gap) !important;
        right: var(--sanua-gap) !important;
        left: auto !important;
        bottom: auto !important;
        width: var(--sanua-rail-w) !important;
        min-width: var(--sanua-rail-w) !important;
        max-width: var(--sanua-rail-w) !important;
        height: calc(100dvh - var(--sanua-gap) * 2) !important;
        background: var(--sanua-gradient) !important;
        border: none !important;
        border-radius: var(--sanua-radius) !important;
        box-shadow: 0 12px 40px -12px rgba(91, 33, 182, 0.4);
        overflow: hidden !important;
        z-index: 100;
        display: flex !important;
        flex-direction: column !important;
        min-height: 0;
    }

    .app-shell-student .sanua-rail-nav {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 14px 10px 14px;
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.35) transparent;
    }
    .app-shell-student .sanua-rail-nav::-webkit-scrollbar { width: 4px; }
    .app-shell-student .sanua-rail-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.35);
        border-radius: 999px;
    }
    .app-shell-student .sanua-rail-nav::-webkit-scrollbar-track { background: transparent; }

    .app-shell-student .sanua-rail-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        width: 68px;
        height: 68px;
        flex-shrink: 0;
        border-radius: 20px;
        text-decoration: none !important;
        color: rgba(255, 255, 255, 0.88);
        background: transparent;
        border: 1px solid transparent;
        transition: transform 0.22s cubic-bezier(0.34, 1.4, 0.64, 1), background 0.22s, box-shadow 0.22s, border-color 0.22s;
    }
    .app-shell-student .sanua-rail-link:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 20px rgba(167, 139, 250, 0.4);
    }
    .app-shell-student .sanua-rail-link.is-active {
        background: rgba(255, 255, 255, 0.92);
        color: #5B21B6;
        box-shadow: 0 8px 28px -8px rgba(0, 0, 0, 0.25), 0 0 24px rgba(251, 191, 36, 0.3);
        transform: scale(1.05);
    }
    .app-shell-student .sanua-rail-link.is-active .sanua-rail-icon {
        background: linear-gradient(135deg, #EDE9FE, #DDD6FE);
        color: #6D28D9;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }
    .app-shell-student .sanua-rail-icon {
        width: 32px;
        height: 32px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.14);
        transition: background 0.2s, transform 0.2s;
    }
    .app-shell-student .sanua-rail-link:hover .sanua-rail-icon { transform: scale(1.08); }
    .app-shell-student .sanua-rail-label {
        font-size: 0.55rem;
        font-weight: 800;
        line-height: 1.15;
        text-align: center;
    }

    .app-shell-student .sanua-rail-icon i {
        color: inherit;
        font-size: inherit;
        line-height: 1;
        display: block;
    }

    /* Scrollable page content — below navbar */
    .app-shell-student .stu-topbar {
        display: grid;
        grid-template-columns: 1fr minmax(0, 480px) auto;
        align-items: center;
        gap: 16px;
        height: 64px;
        padding: 0 var(--sanua-content-pad);
        border-bottom: 1px solid #F0EEF8;
        background: #fff;
        flex-shrink: 0;
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .app-shell-student .stu-topbar__brand {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .app-shell-student .stu-topbar__brand .platform-brand__name {
        font-size: 1.15rem !important;
        font-weight: 900 !important;
        color: #5B21B6 !important;
    }
    .app-shell-student .stu-topbar__search {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 0 16px;
        height: 40px;
        border-radius: 14px;
        background: #F8F7FC;
        border: 1px solid #EDE9FE;
    }
    @media (min-width: 768px) {
        .app-shell-student .stu-topbar__search { display: flex; }
    }
    .app-shell-student .stu-topbar__search i { color: #94a3b8; font-size: 0.85rem; }
    .app-shell-student .stu-topbar__search input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 0.85rem;
        color: #334155;
        min-width: 0;
    }
    .app-shell-student .stu-topbar__actions {
        display: flex;
        align-items: center;
        gap: 10px;
        justify-self: end;
    }
    .app-shell-student .stu-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: #F5F3FF;
        color: #6D28D9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s, transform 0.15s;
    }
    .app-shell-student .stu-icon-btn:hover { background: #EDE9FE; transform: scale(1.04); }
    .app-shell-student .stu-notif-dot {
        position: absolute;
        top: 6px;
        inset-inline-end: 6px;
        width: 8px;
        height: 8px;
        background: #FBBF24;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .app-shell-student .stu-user-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        background: #F8F7FC;
        padding: 4px 12px 4px 4px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .app-shell-student .stu-user-chip:hover { background: #F5F3FF; }
    .app-shell-student .stu-user-chip .u-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #5B21B6, #8B5CF6) !important;
        overflow: hidden;
    }
    .app-shell-student .stu-user-meta {
        display: flex;
        flex-direction: column;
        text-align: start;
        line-height: 1.2;
    }
    .app-shell-student .stu-user-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1e1b4b;
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .app-shell-student .stu-user-role {
        font-size: 0.65rem;
        font-weight: 600;
        color: #8B5CF6;
    }
    .app-shell-student .stu-dd {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #EDE9FE;
        box-shadow: 0 16px 48px -12px rgba(91, 33, 182, 0.25);
    }

    /* Scrollable page content — below navbar */
    .app-shell-student .sanua-main {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        background: #F8F7FC !important;
        width: 100%;
        min-height: 0;
    }
    .app-shell-student .sanua-main-inner {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: var(--sanua-content-pad);
        box-sizing: border-box;
    }

    /* Laptop 1024–1400 */
    @media (max-width: 1400px) {
        .app-shell-student { --sanua-rail-w: 90px; }
        .app-shell-student .sanua-rail-link { width: 66px; height: 66px; }
    }

    /* Tablet — icon rail */
    @media (max-width: 1023px) {
        .app-shell-student {
            --sanua-rail-w: 72px;
            --sanua-content-pad: 24px;
            --sanua-gap: 0;
            gap: 0;
            padding: 0;
            grid-template-columns: minmax(0, 1fr) var(--sanua-rail-w);
        }

        .app-shell-student .sanua-main-wrap {
            min-height: 100dvh;
            border-radius: 0;
        }

        .app-shell-student > aside.app-sidebar.sanua-rail {
            top: 0 !important;
            right: 0 !important;
            height: 100dvh !important;
            border-radius: var(--sanua-radius) 0 0 var(--sanua-radius) !important;
        }

        .app-shell-student .sanua-rail-link {
            width: 52px;
            height: 52px;
            border-radius: 16px;
        }
        .app-shell-student .sanua-rail-label { display: none; }
        .app-shell-student .sanua-rail-icon { width: 26px; height: 26px; font-size: 0.75rem; }
        .app-shell-student .sanua-rail-nav { padding: 12px 6px 12px; gap: 8px; }
        .app-shell-student .stu-topbar {
            grid-template-columns: 1fr auto;
            padding: 0 20px;
        }
    }

    /* Mobile — bottom nav */
    @media (max-width: 767px) {
        .app-shell-student {
            --sanua-content-pad: 16px;
            grid-template-columns: 1fr;
            grid-template-rows: 1fr auto;
            padding: 0;
            gap: 0;
        }

        .app-shell-student .sanua-main-wrap {
            grid-column: 1;
            grid-row: 1;
            min-height: 100dvh;
            padding-bottom: 72px;
            border-radius: 0;
        }

        .app-shell-student > aside.app-sidebar.sanua-rail {
            grid-column: 1;
            grid-row: 2;
            top: auto !important;
            bottom: 0 !important;
            right: 0 !important;
            left: 0 !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            min-height: 64px !important;
            flex-direction: row !important;
            border-radius: 20px 20px 0 0 !important;
            box-shadow: 0 -4px 24px -4px rgba(91, 33, 182, 0.35);
        }

        .app-shell-student .sanua-rail-nav {
            flex-direction: row;
            justify-content: space-around;
            width: 100%;
            padding: 8px 12px;
            gap: 4px;
            overflow-x: auto;
        }

        .app-shell-student .sanua-rail-link {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }
    }

    .app-shell-student, .app-shell-student .sanua-main-inner {
        font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\student-app-shell.blade.php ENDPATH**/ ?>
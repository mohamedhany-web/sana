
<?php if (! $__env->hasRenderedOnce('196370fd-3541-47a7-927d-0d12bc1acb64')): $__env->markAsRenderedOnce('196370fd-3541-47a7-927d-0d12bc1acb64'); ?>
<?php
    $b = config('brand.colors');
?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    .app-shell-student {
        --stu-blue: <?php echo e($b['blue']); ?>;
        --stu-blue-dark: <?php echo e($b['blue_dark']); ?>;
        --stu-blue-rgb: <?php echo e($b['blue_rgb']); ?>;
        --stu-purple: <?php echo e($b['purple']); ?>;
        --stu-purple-dark: <?php echo e($b['purple_dark']); ?>;
        --stu-purple-rgb: <?php echo e($b['purple_rgb']); ?>;
        --stu-gold: <?php echo e($b['yellow']); ?>;
        --stu-gold-rgb: <?php echo e($b['yellow_rgb']); ?>;
        --stu-canvas: #f4f6ff;
        --stu-gradient: linear-gradient(135deg, <?php echo e($b['blue']); ?> 0%, <?php echo e($b['purple']); ?> 52%, <?php echo e($b['purple_dark']); ?> 100%);
        --stu-gradient-soft: linear-gradient(135deg, rgba(<?php echo e($b['blue_rgb']); ?>, 0.08) 0%, rgba(<?php echo e($b['purple_rgb']); ?>, 0.06) 100%);
        --stu-shadow: 0 16px 48px -20px rgba(<?php echo e($b['blue_rgb']); ?>, 0.22);
        --stu-shadow-lg: 0 24px 60px -24px rgba(<?php echo e($b['purple_rgb']); ?>, 0.28);
    }

    /* ── السايدبار ── */
    .app-shell-student .app-sidebar {
        width: 280px;
        background: #fff;
        border-left: none;
        box-shadow: -8px 0 40px -16px rgba(<?php echo e($b['purple_rgb']); ?>, 0.15);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        min-height: 100dvh;
    }

    .app-shell-student .stu-sidebar-head {
        flex-shrink: 0;
        background: var(--stu-gradient);
        position: relative;
        overflow: hidden;
    }
    .app-shell-student .stu-sidebar-head::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 85% 15%, rgba(255,255,255,.18) 0%, transparent 42%),
            radial-gradient(circle at 10% 90%, rgba(<?php echo e($b['yellow_rgb']); ?>, .22) 0%, transparent 38%);
        pointer-events: none;
    }
    .app-shell-student .stu-sidebar-head::after {
        content: '';
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.12);
        top: -40px;
        left: -30px;
        pointer-events: none;
    }

    .app-shell-student .ins-sidebar-brand {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,.12);
        padding: 1.1rem 1rem 0.9rem;
        position: relative;
        z-index: 1;
    }
    .app-shell-student .ins-sidebar-brand .platform-brand__name {
        color: #fff !important;
        font-size: 1.05rem;
        text-shadow: 0 1px 8px rgba(0,0,0,.12);
    }
    .app-shell-student .ins-sidebar-brand .platform-brand__tagline {
        color: rgba(255,255,255,.78) !important;
    }
    .app-shell-student .ins-sidebar-brand .platform-brand__mark--img {
        box-shadow: 0 6px 20px -6px rgba(0,0,0,.35), 0 0 0 2px rgba(255,255,255,.25);
    }

    .app-shell-student .stu-sidebar-stats {
        padding: 0.25rem 0.85rem 1.1rem;
        position: relative;
        z-index: 1;
    }
    .app-shell-student .stu-sidebar-stats-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 0.875rem;
    }
    .app-shell-student .stu-sidebar-stat {
        border-radius: 16px;
        border: 1px solid rgba(255,255,255,.22);
        background: rgba(255,255,255,.14);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 0.75rem 0.65rem;
        min-width: 0;
        transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: #fff;
        display: block;
    }
    .app-shell-student .stu-sidebar-stat:hover {
        background: rgba(255,255,255,.22);
        transform: translateY(-2px);
        box-shadow: 0 10px 28px -12px rgba(0,0,0,.25);
    }
    .app-shell-student .stu-sidebar-stat .text-xl,
    .app-shell-student .stu-sidebar-stat .font-black {
        color: #fff !important;
    }
    .app-shell-student .stu-sidebar-stat span[class*="text-"] {
        color: rgba(255,255,255,.85) !important;
    }
    .app-shell-student .stu-sidebar-stat .w-8 {
        background: rgba(255,255,255,.2) !important;
        color: #fff !important;
    }

    .app-shell-student .ins-nav-group {
        color: #94a3b8;
        font-size: 0.625rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        padding: 1rem 1rem 0.4rem;
        text-transform: uppercase;
    }

    .app-shell-student .ins-nav {
        margin: 2px 10px;
        padding: 0.6rem 0.75rem;
        border-radius: 14px;
        font-size: 0.8125rem;
        color: #475569;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .app-shell-student .ins-nav:hover {
        background: rgba(<?php echo e($b['purple_rgb']); ?>, 0.06);
        color: var(--stu-blue-dark);
    }
    .app-shell-student .ins-nav.active {
        background: var(--stu-gradient-soft) !important;
        color: var(--stu-blue-dark) !important;
        border: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.12) !important;
        font-weight: 700;
        box-shadow: 0 4px 16px -8px rgba(<?php echo e($b['blue_rgb']); ?>, 0.2);
    }
    .app-shell-student .ins-nav.active::before {
        width: 4px;
        height: 24px;
        border-radius: 4px 0 0 4px;
        background: var(--stu-gradient) !important;
    }
    .app-shell-student .ins-nav .ins-icon {
        width: 2.125rem;
        height: 2.125rem;
        border-radius: 11px;
        transition: transform 0.2s;
    }
    .app-shell-student .ins-nav:hover .ins-icon,
    .app-shell-student .ins-nav.active .ins-icon {
        transform: scale(1.06);
    }

    .app-shell-student .stu-sidebar-foot {
        flex-shrink: 0;
        padding: 0.75rem;
        border-top: 1px solid #eef2ff;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
    }
    .app-shell-student .ins-user-card {
        background: #fff;
        border: 1px solid #e8ecff;
        border-radius: 16px;
        box-shadow: 0 4px 20px -12px rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
    }
    .app-shell-student .ins-user-card .u-avatar,
    .app-shell-student .stu-user-chip .u-avatar {
        background: var(--stu-gradient);
        box-shadow: 0 4px 14px -4px rgba(<?php echo e($b['purple_rgb']); ?>, 0.45);
    }

    /* ── الهيدر ── */
    .app-shell-student .stu-topbar {
        height: 68px;
        background: rgba(255, 255, 255, 0.88);
        border-bottom: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.08);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 1px 0 rgba(<?php echo e($b['purple_rgb']); ?>, 0.04);
    }

    .app-shell-student .stu-topbar-search {
        flex: 1;
        max-width: 28rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: rgba(255,255,255,.7);
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .app-shell-student .stu-topbar-search:focus-within {
        background: #fff;
        border-color: var(--stu-purple);
        box-shadow: 0 0 0 4px rgba(<?php echo e($b['purple_rgb']); ?>, 0.12);
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
    .app-shell-student .stu-topbar-search input::placeholder { color: #94a3b8; }

    .app-shell-student .stu-icon-btn {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 13px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.18s;
    }
    .app-shell-student .stu-icon-btn:hover {
        border-color: rgba(<?php echo e($b['purple_rgb']); ?>, 0.25);
        color: var(--stu-purple);
        background: rgba(<?php echo e($b['purple_rgb']); ?>, 0.06);
    }

    .app-shell-student .stu-user-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.55rem 0.35rem 0.35rem;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .app-shell-student .stu-user-chip:hover {
        border-color: rgba(<?php echo e($b['blue_rgb']); ?>, 0.2);
        box-shadow: 0 6px 20px -10px rgba(<?php echo e($b['blue_rgb']); ?>, 0.25);
    }
    .app-shell-student .stu-user-chip .u-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 11px;
    }

    /* ── المحتوى ── */
    .app-shell-student main {
        background:
            radial-gradient(ellipse 80% 50% at 100% 0%, rgba(<?php echo e($b['purple_rgb']); ?>, 0.07) 0%, transparent 55%),
            radial-gradient(ellipse 60% 40% at 0% 100%, rgba(<?php echo e($b['blue_rgb']); ?>, 0.06) 0%, transparent 50%),
            radial-gradient(circle at 50% 30%, rgba(<?php echo e($b['yellow_rgb']); ?>, 0.04) 0%, transparent 40%),
            var(--stu-canvas) !important;
    }
    .app-shell-student main > div {
        width: 100%;
        max-width: none !important;
    }

    .app-shell-student .stu-dd {
        background: #fff;
        border: 1px solid #e8ecff;
        border-radius: 18px;
        box-shadow: var(--stu-shadow-lg);
        overflow: hidden;
    }

    /* ── جوال ── */
    @media (max-width: 1023px) {
        .app-shell-student .app-sidebar {
            width: min(300px, 88vw);
            max-width: 88vw;
        }
    }
    @media (max-width: 639px) {
        .app-shell-student .app-sidebar {
            width: min(288px, 92vw);
        }
        .app-shell-student .stu-topbar {
            height: 56px;
            padding-inline: 0.75rem;
        }
        .app-shell-student .stu-icon-btn {
            width: 2.25rem;
            height: 2.25rem;
        }
        .app-shell-student .stu-user-chip .u-avatar {
            width: 2.125rem;
            height: 2.125rem;
        }
        .app-shell-student .stu-dd {
            width: min(20rem, calc(100vw - 1.25rem)) !important;
            max-width: calc(100vw - 1.25rem);
        }
        .app-shell-student main > div {
            padding: 0.875rem !important;
        }
        .app-shell-student .ins-nav {
            min-height: 44px;
        }
    }

    @supports (padding: max(0px)) {
        .app-shell-student .app-sidebar {
            padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
        }
    }
</style>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/layouts/partials/student-app-shell.blade.php ENDPATH**/ ?>
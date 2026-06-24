<?php if (! $__env->hasRenderedOnce('79e0fa08-6e23-4ebf-8430-d9b6de63071b')): $__env->markAsRenderedOnce('79e0fa08-6e23-4ebf-8430-d9b6de63071b'); ?>
<?php $__env->startPush('styles'); ?>
<?php $b = config('brand.colors'); ?>
<style>
    .sd-page {
        --sd-blue: <?php echo e($b['blue']); ?>;
        --sd-purple: <?php echo e($b['purple']); ?>;
        --sd-gold: <?php echo e($b['yellow']); ?>;
        --sd-gradient: linear-gradient(135deg, <?php echo e($b['blue']); ?> 0%, <?php echo e($b['purple']); ?> 100%);
        --sd-gradient-warm: linear-gradient(135deg, <?php echo e($b['purple']); ?> 0%, <?php echo e($b['yellow']); ?> 100%);
        width: 100%;
        max-width: none;
    }
    .sd-hero {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .sd-hero { grid-template-columns: 1fr 280px; align-items: stretch; }
    }
    .sd-hero-main {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
        background:
            radial-gradient(circle at 100% 0%, rgba(<?php echo e($b['purple_rgb']); ?>, 0.12) 0%, transparent 45%),
            radial-gradient(circle at 0% 100%, rgba(<?php echo e($b['yellow_rgb']); ?>, 0.1) 0%, transparent 40%),
            linear-gradient(135deg, #fff 0%, rgba(<?php echo e($b['blue_rgb']); ?>, 0.04) 100%);
        padding: 1.5rem 1.75rem;
        box-shadow: 0 20px 50px -24px rgba(<?php echo e($b['blue_rgb']); ?>, 0.25);
    }
    .sd-hero-main::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: var(--sd-gradient);
        border-radius: 24px 24px 0 0;
    }
    .sd-motivation {
        border-radius: 24px;
        border: 1px solid rgba(<?php echo e($b['purple_rgb']); ?>, 0.15);
        background: var(--sd-gradient);
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.75rem;
        box-shadow: 0 16px 40px -16px rgba(<?php echo e($b['purple_rgb']); ?>, 0.4);
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .sd-motivation::after {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
        bottom: -30px;
        left: -20px;
    }
    .sd-motivation > * { position: relative; z-index: 1; }
    .sd-kpi {
        border-radius: 20px;
        border: 1px solid #e8ecff;
        background: #fff;
        padding: 1.15rem 1.25rem;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        position: relative;
        overflow: hidden;
    }
    .sd-kpi::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 3px;
        background: var(--sd-gradient);
        opacity: 0;
        transition: opacity .2s;
    }
    .sd-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px -18px rgba(<?php echo e($b['blue_rgb']); ?>, 0.22);
        border-color: rgba(<?php echo e($b['purple_rgb']); ?>, 0.2);
    }
    .sd-kpi:hover::before { opacity: 1; }
    .sd-kpi-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        box-shadow: 0 6px 16px -6px rgba(0,0,0,.15);
    }
    .sd-panel {
        border-radius: 22px;
        border: 1px solid #e8ecff;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 36px -20px rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
    }
    .sd-panel-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: linear-gradient(180deg, rgba(<?php echo e($b['blue_rgb']); ?>, 0.03) 0%, transparent 100%);
    }
    .sd-panel-body { padding: 1rem 1.25rem 1.25rem; }
    .sd-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        border-radius: 13px;
        background: var(--sd-gradient);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 6px 20px -8px rgba(<?php echo e($b['purple_rgb']); ?>, 0.45);
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .sd-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 28px -8px rgba(<?php echo e($b['purple_rgb']); ?>, 0.5);
        color: #fff;
    }
    .sd-btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 1rem;
        border-radius: 13px;
        border: 2px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.25);
        color: var(--sd-blue);
        font-size: 0.8125rem;
        font-weight: 700;
        background: #fff;
        text-decoration: none;
    }
    .sd-btn-outline:hover { border-color: var(--sd-purple); color: var(--sd-purple); }
    .sd-tag { color: var(--sd-purple); }
    .sd-link { color: var(--sd-blue); font-weight: 700; text-decoration: none; }
    .sd-link:hover { color: var(--sd-purple); }
    .sd-lesson-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-lesson-row:last-child { border-bottom: none; }
    .sd-avatar {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 14px;
        background: var(--sd-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }
    .sd-badge {
        display: inline-flex;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .sd-badge-pending { background: #fef3c7; color: #92400e; }
    .sd-badge-confirmed { background: #d1fae5; color: #065f46; }
    .sd-form label { display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; }
    .sd-form input, .sd-form select, .sd-form textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.85rem;
        font-size: 0.9rem;
    }
    .sd-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .sd-chip:has(input:checked) {
        border-color: var(--sd-blue);
        background: rgba(<?php echo e($b['blue_rgb']); ?>, 0.08);
        color: var(--sd-blue);
        font-weight: 700;
    }
    .sd-chip input { display: none; }
    .sd-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.75rem 1rem;
    }
    .sd-filter-bar select {
        min-width: 12rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.85rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        background: #fff;
    }
    .sd-teacher-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 20px;
        border: 1px solid #e8ecff;
        background: #fff;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        box-shadow: 0 10px 32px -22px rgba(<?php echo e($b['blue_rgb']); ?>, 0.15);
    }
    .sd-teacher-card:hover {
        transform: translateY(-4px);
        border-color: rgba(<?php echo e($b['purple_rgb']); ?>, 0.28);
        box-shadow: 0 20px 48px -20px rgba(<?php echo e($b['purple_rgb']); ?>, 0.22);
    }
    .sd-teacher-card__head {
        padding: 1.15rem 1.2rem 1rem;
        background: linear-gradient(180deg, rgba(<?php echo e($b['blue_rgb']); ?>, 0.04) 0%, #fff 100%);
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-teacher-card__body {
        padding: 1rem 1.2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .sd-teacher-card__foot {
        padding: 0 1.2rem 1.2rem;
    }
    .sd-teacher-card__avatar {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 16px;
        flex-shrink: 0;
        overflow: hidden;
        background: var(--sd-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        box-shadow: 0 8px 20px -10px rgba(<?php echo e($b['purple_rgb']); ?>, 0.45);
    }
    .sd-teacher-card__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sd-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        background: rgba(<?php echo e($b['blue_rgb']); ?>, 0.08);
        color: var(--sd-blue);
        border: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
    }
    .sd-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    .sd-empty i {
        font-size: 2.5rem;
        opacity: 0.35;
        display: block;
        margin-bottom: 0.75rem;
    }
    .sd-avail-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.55rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.8125rem;
    }
    .sd-avail-row:last-child { border-bottom: none; }
    .sd-avail-day {
        font-weight: 700;
        color: #334155;
        min-width: 4.5rem;
    }
    .sd-avail-time {
        color: #64748b;
        font-variant-numeric: tabular-nums;
    }
    .sd-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .sd-info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        font-size: 0.8125rem;
        color: #475569;
    }
    .sd-info-item i {
        width: 1.25rem;
        text-align: center;
        color: var(--sd-purple);
        margin-top: 0.15rem;
    }
    .sd-alert {
        border-radius: 14px;
        padding: 0.85rem 1rem;
        font-size: 0.8125rem;
        font-weight: 600;
    }
    .sd-alert-error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
    }
    .sd-alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\student\tutor-lessons\partials\dashboard-styles.blade.php ENDPATH**/ ?>
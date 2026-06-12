<?php if (! $__env->hasRenderedOnce('ee95b283-b5e0-4707-9b04-ba83b1b35df8')): $__env->markAsRenderedOnce('ee95b283-b5e0-4707-9b04-ba83b1b35df8'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    .id-tutor-page {
        --id-primary: #283593;
        --id-accent: #FB5607;
        --id-rose: #FFE5F7;
        width: 100%;
        max-width: none;
    }
    .id-tutor-page .id-hero {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .id-tutor-page .id-hero { grid-template-columns: 1fr 260px; align-items: stretch; }
    }
    .id-tutor-page .id-hero-main {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, rgba(255,229,247,.6) 0%, #fff 40%, #f8fafc 100%);
        padding: 1.5rem 1.75rem;
        box-shadow: 0 12px 40px -24px rgba(31,42,122,.22);
    }
    .id-tutor-page .id-hero-main::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251,86,7,.14), transparent 70%);
        top: -70px;
        left: -50px;
        pointer-events: none;
    }
    .id-tutor-page .id-hero-aside {
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #283593 0%, #1F2A7A 55%, #FB5607 120%);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.65rem;
        box-shadow: 0 8px 28px -16px rgba(31,42,122,.28);
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .id-tutor-page .id-hero-aside > * { position: relative; z-index: 1; }
    .id-tutor-page .id-kpi {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.1rem 1.2rem;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .id-tutor-page .id-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px -18px rgba(31,42,122,.25);
        border-color: rgba(40,53,147,.2);
    }
    .id-tutor-page .id-kpi-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        margin-bottom: 0.75rem;
    }
    .id-tutor-page .id-panel {
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 28px -18px rgba(15,23,42,.08);
    }
    .id-tutor-page .id-panel-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: linear-gradient(180deg, rgba(255,229,247,.35) 0%, transparent 100%);
    }
    .id-tutor-page .id-panel-body { padding: 1rem 1.25rem 1.25rem; }
    .id-tutor-page .id-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f8fafc;
        text-decoration: none;
        color: inherit;
    }
    .id-tutor-page .id-row:last-child { border-bottom: none; }
    .id-tutor-page .id-row:hover { background: #f8fafc; border-radius: 12px; margin: 0 -0.5rem; padding-left: 0.5rem; padding-right: 0.5rem; }
    .id-tutor-page .id-avatar {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #283593, #FB5607);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }
    .id-tutor-page .id-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        background: #283593;
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: background .15s;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .id-tutor-page .id-btn-primary:hover { background: #1F2A7A; color: #fff; }
    .id-tutor-page .id-btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.1rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 700;
        text-decoration: none;
    }
    .id-tutor-page .id-btn-ghost:hover { border-color: #cbd5e1; background: #f8fafc; }
    .id-tutor-page .id-quick {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.1rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: all .2s;
        text-decoration: none;
        color: inherit;
    }
    .id-tutor-page .id-quick:hover {
        border-color: rgba(40,53,147,.22);
        box-shadow: 0 10px 28px -16px rgba(31,42,122,.22);
        transform: translateY(-2px);
    }
    .id-tutor-page .id-tag { color: #283593; font-weight: 700; }
    .id-tutor-page .id-link { color: #283593; font-weight: 700; text-decoration: none; font-size: 0.875rem; }
    .id-tutor-page .id-link:hover { color: #FB5607; }
    .id-tutor-page .id-badge {
        display: inline-flex;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .id-tutor-page .id-badge-pending { background: #fef3c7; color: #92400e; }
    .id-tutor-page .id-badge-confirmed { background: #d1fae5; color: #065f46; }
    .id-tutor-page .id-form label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 0.35rem;
    }
    .id-tutor-page .id-form input,
    .id-tutor-page .id-form select,
    .id-tutor-page .id-form textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.65rem 0.85rem;
        font-size: 0.9rem;
    }
    .id-tutor-page .id-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 0.8rem;
        cursor: pointer;
        background: #fff;
    }
    .id-tutor-page .id-chip:has(input:checked) {
        border-color: #283593;
        background: #FFE5F7;
        color: #283593;
        font-weight: 700;
    }
    .id-tutor-page .id-chip input { display: none; }
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\tutor-lessons\partials\dashboard-styles.blade.php ENDPATH**/ ?>
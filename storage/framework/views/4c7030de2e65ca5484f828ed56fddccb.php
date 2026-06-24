<?php $b = config('brand.colors'); ?>
<?php if (! $__env->hasRenderedOnce('330986e4-778c-4096-b3a9-b4f1f74104d2')): $__env->markAsRenderedOnce('330986e4-778c-4096-b3a9-b4f1f74104d2'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    <?php echo $__env->make('landing.eduvalt.brand-vars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    .tl-page { --tl-blue: <?php echo e($b['blue']); ?>; --tl-purple: <?php echo e($b['purple']); ?>; --tl-gold: <?php echo e($b['yellow']); ?>;
        --tl-grad: linear-gradient(135deg, <?php echo e($b['blue']); ?> 0%, <?php echo e($b['purple']); ?> 55%, <?php echo e($b['purple_dark']); ?> 100%); }
    .tl-hero {
        border-radius: 22px;
        background: var(--tl-grad);
        color: #fff;
        padding: 1.75rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px -24px rgba(<?php echo e($b['blue_rgb']); ?>, .45);
    }
    .tl-hero::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(<?php echo e($b['yellow_rgb']); ?>, .35), transparent 70%);
        top: -60px;
        left: -40px;
        pointer-events: none;
    }
    .tl-hero h1 { position: relative; z-index: 1; font-size: 1.5rem; font-weight: 800; margin: 0; }
    .tl-hero p { position: relative; z-index: 1; margin: .5rem 0 0; opacity: .9; font-size: .9rem; max-width: 36rem; }
    .tl-grid { display: grid; gap: 1rem; }
    @media (min-width: 768px) { .tl-grid-2 { grid-template-columns: repeat(2, 1fr); } .tl-grid-3 { grid-template-columns: repeat(3, 1fr); } .tl-grid-4 { grid-template-columns: repeat(4, 1fr); } }
    .tl-card {
        background: #fff;
        border: 1px solid #e8ecf8;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 10px 32px -20px rgba(<?php echo e($b['purple_rgb']); ?>, .18);
    }
    .tl-stat {
        border-radius: 16px;
        padding: 1rem 1.1rem;
        background: linear-gradient(135deg, rgba(<?php echo e($b['blue_rgb']); ?>, .06), rgba(<?php echo e($b['purple_rgb']); ?>, .04));
        border: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, .12);
    }
    .tl-stat strong { display: block; font-size: 1.5rem; font-weight: 900; color: var(--tl-blue); line-height: 1.1; }
    .tl-stat span { font-size: .75rem; color: #64748b; font-weight: 600; }
    .tl-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
    }
    .tl-badge-pending { background: #fef3c7; color: #92400e; }
    .tl-badge-confirmed { background: #d1fae5; color: #065f46; }
    .tl-badge-progress { background: #dbeafe; color: #1e40af; }
    .tl-badge-done { background: #e0e7ff; color: #3730a3; }
    .tl-badge-cancel { background: #fee2e2; color: #991b1b; }
    .tl-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1.25rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: .875rem;
        text-decoration: none;
        transition: transform .15s, box-shadow .15s;
    }
    .tl-btn-primary { background: var(--tl-grad); color: #fff; box-shadow: 0 8px 24px -10px rgba(<?php echo e($b['purple_rgb']); ?>, .5); }
    .tl-btn-primary:hover { transform: translateY(-1px); color: #fff; }
    .tl-btn-outline { border: 2px solid var(--tl-blue); color: var(--tl-blue); background: #fff; }
    .tl-btn-ghost { background: #f1f5ff; color: var(--tl-blue); }
    .tl-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: .85rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .tl-list-item:last-child { border-bottom: none; }
    .tl-avatar {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 14px;
        background: var(--tl-grad);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        flex-shrink: 0;
    }
    .tl-form label { display: block; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .35rem; }
    .tl-form input, .tl-form select, .tl-form textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: .65rem .85rem;
        font-size: .9rem;
    }
    .tl-form input:focus, .tl-form select:focus, .tl-form textarea:focus {
        outline: none;
        border-color: var(--tl-blue);
        box-shadow: 0 0 0 3px rgba(<?php echo e($b['blue_rgb']); ?>, .12);
    }
    .tl-chip {
        display: inline-flex;
        align-items: center;
        padding: .35rem .75rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: .8rem;
        cursor: pointer;
    }
    .tl-chip:has(input:checked) { border-color: var(--tl-blue); background: rgba(<?php echo e($b['blue_rgb']); ?>, .08); color: var(--tl-blue); font-weight: 700; }
    .tl-chip input { display: none; }
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\partials\tutor-lesson-ui.blade.php ENDPATH**/ ?>
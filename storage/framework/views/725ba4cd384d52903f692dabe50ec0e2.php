
<style>
    .edu-checkout-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #fff 42%, #f5f3ff 78%, #fffbeb 100%);
        border-bottom: 1px solid color-mix(in srgb, var(--edu-primary) 10%, #e2e8f0);
        position: relative;
        overflow: hidden;
    }
    .edu-checkout-hero__blob {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(48px);
    }
    .edu-checkout-hero__blob--1 {
        top: -2rem;
        inset-inline-start: -3rem;
        width: 16rem;
        height: 16rem;
        background: radial-gradient(circle, var(--edu-primary-light), transparent 70%);
        opacity: .8;
    }
    .edu-checkout-hero__blob--2 {
        bottom: -2rem;
        inset-inline-end: -1rem;
        width: 18rem;
        height: 18rem;
        background: radial-gradient(circle, color-mix(in srgb, var(--edu-purple) 20%, #ede9fe), transparent 70%);
        opacity: .65;
    }
    .edu-checkout-body {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .edu-checkout-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius);
        box-shadow: 0 10px 36px -18px rgba(15, 23, 42, .14);
        transition: box-shadow .25s, border-color .25s;
    }
    .edu-checkout-card:hover {
        box-shadow: 0 16px 44px -16px rgba(var(--edu-primary-rgb), .18);
    }
    .edu-checkout-summary-head {
        background: linear-gradient(120deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        color: #fff;
        padding: 1.15rem 1.35rem;
        border-radius: var(--edu-radius-sm) var(--edu-radius-sm) 0 0;
        margin: -1.5rem -1.5rem 1.25rem;
    }
    @media (min-width: 640px) {
        .edu-checkout-summary-head { margin: -2rem -2rem 1.35rem; }
    }
    .edu-checkout-input {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: .8rem 1rem;
        font-size: .9rem;
        transition: border-color .2s, box-shadow .2s;
        background: #fff;
    }
    .edu-checkout-input:focus {
        outline: none;
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12);
    }
    .edu-checkout-steps {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    @media (min-width: 640px) {
        .edu-checkout-steps {
            flex-direction: row;
            align-items: center;
            gap: 0;
        }
    }
    .edu-checkout-step-line {
        display: none;
    }
    @media (min-width: 640px) {
        .edu-checkout-step-line {
            display: block;
            flex: 1;
            min-width: 1.5rem;
            height: 2px;
            background: linear-gradient(90deg, #cbd5e1, #e2e8f0);
            margin-inline: .5rem;
        }
    }
    .edu-checkout-step-num {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .8rem;
        font-weight: 800;
        flex-shrink: 0;
    }
    .edu-checkout-step-num.is-done {
        background: #10b981;
        color: #fff;
        box-shadow: 0 0 0 3px #d1fae5;
    }
    .edu-checkout-step-num.is-active {
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-purple));
        color: #fff;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--edu-primary) 25%, transparent);
    }
    .edu-checkout-step-num.is-pending {
        background: #e2e8f0;
        color: #64748b;
    }
    .edu-checkout-alert {
        border-radius: var(--edu-radius-sm);
        padding: 1rem 1.15rem;
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        font-size: .875rem;
        font-weight: 600;
    }
    .edu-checkout-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .edu-checkout-alert--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .edu-checkout-alert--info { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .edu-checkout-alert--sky { background: #f0f9ff; border: 1px solid #bae6fd; color: #0c4a6e; }
    .edu-fk-method-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--edu-radius-sm);
        border: 2px solid #e2e8f0;
        background: #fff;
        text-align: start;
        transition: border-color .2s, box-shadow .2s;
        width: 100%;
    }
    .edu-fk-method-btn:hover { border-color: color-mix(in srgb, var(--edu-primary) 35%, #e2e8f0); }
    .edu-fk-method-btn.is-selected {
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .15);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .edu-checkout-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .9rem;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: .8rem;
        font-weight: 600;
        color: #475569;
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\checkout-page.blade.php ENDPATH**/ ?>
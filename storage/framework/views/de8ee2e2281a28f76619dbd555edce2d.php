<style>
    @media (prefers-reduced-motion: reduce) {
        .ix-float, .ix-pulse, .ix-orbit-tilt, .ix-step-enter,
        .th-orbit-ring, .ta-orbit-ring { animation: none !important; transition: none !important; }
    }
    .ix-pulse { animation: ix-pulse 2.5s ease-in-out infinite; }
    @keyframes ix-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .82; transform: scale(1.02); }
    }

    .ix-bubble {
        position: absolute; border-radius: 50%; pointer-events: none;
        animation: ix-float 7s ease-in-out infinite;
    }
    .ix-bubble--brand-blue {
        background: rgba(var(--edu-primary-rgb), 0.18);
        box-shadow: 0 0 40px rgba(var(--edu-primary-rgb), 0.15);
    }
    .ix-bubble--brand-purple {
        background: rgba(var(--edu-purple-rgb), 0.16);
        box-shadow: 0 0 36px rgba(var(--edu-purple-rgb), 0.12);
    }
    .ix-bubble--brand-gold {
        background: rgba(var(--edu-accent-rgb), 0.2);
        box-shadow: 0 0 32px rgba(var(--edu-accent-rgb), 0.14);
    }
    @keyframes ix-float {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-14px) scale(1.05); }
    }

    .ix-orbit-tilt { transition: transform .35s ease-out; transform-style: preserve-3d; }
    .ix-orbit-tilt.is-active { will-change: transform; }

    .ix-steps { display: flex; gap: .5rem; flex-wrap: wrap; justify-content: center; }
    @media (min-width: 1024px) { .ix-steps { justify-content: flex-start; } }
    .ix-step-pill {
        display: flex; align-items: center; gap: .45rem;
        padding: .5rem .9rem; border-radius: 999px;
        border: 1.5px solid #e2e8f0; background: #fff;
        font-size: .78rem; font-weight: 700; color: #64748b;
        cursor: default; transition: all .25s ease;
        box-shadow: 0 2px 8px -4px rgba(15,23,42,.1);
    }
    .ix-step-pill.is-active {
        border-color: var(--edu-primary);
        background: var(--edu-primary-light);
        color: var(--edu-primary);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -8px rgba(var(--edu-primary-rgb), .35);
    }
    .ix-step-pill.is-done {
        border-color: rgba(var(--edu-primary-rgb), 0.35);
        background: linear-gradient(135deg, var(--edu-primary-light), #fff);
        color: var(--edu-primary-dark);
    }
    .ix-step-pill__num {
        width: 1.35rem; height: 1.35rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; font-weight: 800;
        background: #f1f5f9; color: #64748b;
    }
    .ix-step-pill.is-active .ix-step-pill__num { background: var(--edu-primary); color: #fff; }
    .ix-step-pill.is-done .ix-step-pill__num {
        background: var(--edu-gradient-cta);
        color: #fff;
    }

    .ix-cta-pulse { position: relative; overflow: hidden; }
    .ix-cta-pulse::after {
        content: ''; position: absolute; inset: 0; border-radius: inherit;
        background: rgba(255,255,255,.25); opacity: 0;
        transform: scale(.8); transition: opacity .35s, transform .35s;
    }
    .ix-cta-pulse:hover::after { opacity: 1; transform: scale(1); }
    .ix-cta-pulse:active { transform: scale(.97) !important; }

    .ix-field-wrap { position: relative; }
    .ix-field-ok {
        position: absolute; inset-inline-end: .85rem; top: 50%; transform: translateY(-50%);
        color: #10b981; font-size: .9rem; pointer-events: none;
    }
    .ix-field-wrap textarea + .ix-field-ok { top: 1rem; transform: none; }

    .ix-progress-ring {
        display: flex; align-items: center; gap: .75rem; margin-bottom: 1rem;
    }
    .ix-progress-ring__bar {
        flex: 1; height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden;
    }
    .ix-progress-ring__fill {
        height: 100%; border-radius: 99px;
        background: linear-gradient(90deg, var(--edu-primary), var(--edu-purple));
        transition: width .45s cubic-bezier(.4,0,.2,1);
    }
    .ix-progress-ring__pct {
        font-size: .75rem; font-weight: 800; color: var(--edu-primary);
        min-width: 2.5rem; text-align: end;
    }

    .ix-check-item {
        transition: transform .2s ease, border-color .2s, background .2s, box-shadow .2s;
    }
    .ix-check-item:active { transform: scale(.98); }
    .ix-check-item:has(input:checked) {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px -8px rgba(var(--edu-primary-rgb), .4);
    }

    .ix-motivation {
        padding: .65rem 1rem; border-radius: 1rem; margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--edu-primary-light), #fff);
        border: 1px solid rgba(var(--edu-primary-rgb), .12);
        font-size: .85rem; color: #475569; line-height: 1.6;
    }
    .ix-motivation i { color: var(--edu-accent); margin-inline-end: .35rem; }

    .ix-step-panel {
        animation: ix-step-in .4s cubic-bezier(.4,0,.2,1);
    }
    @keyframes ix-step-in {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ix-tip-card {
        margin-top: 1.25rem; padding: 1rem 1.1rem; border-radius: 1rem;
        background: #fff; border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px -12px rgba(15,23,42,.12);
        font-size: .85rem; color: #64748b; line-height: 1.65;
        transition: opacity .3s;
    }
    .ix-tip-card strong { color: var(--edu-primary); display: block; margin-bottom: .25rem; }

    .ix-pwd-meter { display: flex; gap: 4px; margin-top: .4rem; }
    .ix-pwd-meter span {
        flex: 1; height: 4px; border-radius: 99px; background: #e2e8f0;
        transition: background .25s;
    }
    .ix-pwd-meter span.is-on { background: var(--edu-accent); }
    .ix-pwd-meter span.is-strong { background: #10b981; }

    .th-main-photo, .ta-main-photo { transition: transform .35s ease; }
    .th-main-photo:hover, .ta-main-photo:hover { transform: scale(1.02); }
    .th-sub-photo, .ta-sub-photo { transition: transform .3s ease; }
    .th-sub-photo:hover, .ta-sub-photo:hover { transform: scale(1.08); }

    .ix-success-pop {
        animation: ix-pop .5s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes ix-pop {
        0% { transform: scale(.6); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/interactive-ui.blade.php ENDPATH**/ ?>
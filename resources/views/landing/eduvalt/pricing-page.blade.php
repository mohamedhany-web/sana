{{-- أنماط صفحة الأسعار --}}
<style>
    .edu-pricing-plan {
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        padding-top: 2rem;
    }
    .edu-pricing-plan.is-featured {
        border-color: var(--edu-primary);
        box-shadow: 0 16px 48px -16px rgba(var(--edu-primary-rgb), .35);
    }
    .edu-pricing-badge {
        position: absolute;
        top: .85rem;
        inset-inline-start: .85rem;
        padding: .25rem .7rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 800;
        background: var(--edu-accent-dark);
        color: #fff;
    }
    .edu-package-popular {
        background: var(--edu-gradient-cta);
        border-color: transparent;
        color: #fff;
    }
    .edu-package-popular .edu-package-muted { color: rgba(255, 255, 255, .85); }
</style>

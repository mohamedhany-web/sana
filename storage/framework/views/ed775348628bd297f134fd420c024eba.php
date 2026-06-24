
<style>
    .edu-pricing-pillar {
        text-align: center;
        padding: 1.25rem 1rem;
        border-radius: var(--edu-radius-sm);
        background: #fff;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: transform .2s, box-shadow .2s;
    }
    .edu-pricing-pillar:hover {
        transform: translateY(-3px);
        box-shadow: var(--edu-shadow);
    }
    .edu-pricing-pillar-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto .85rem;
        font-size: 1.2rem;
        color: #fff;
    }
    .edu-pricing-compare-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    .edu-pricing-compare {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        font-size: .875rem;
    }
    .edu-pricing-compare th,
    .edu-pricing-compare td {
        padding: .85rem 1rem;
        text-align: start;
        border-bottom: 1px solid #f1f5f9;
    }
    .edu-pricing-compare th {
        background: var(--edu-primary-light);
        color: var(--edu-primary-dark);
        font-weight: 800;
        font-size: .8rem;
    }
    .edu-pricing-compare tr:last-child td { border-bottom: none; }
    .edu-pricing-compare .pkg-name {
        font-weight: 800;
        color: var(--edu-navy);
        white-space: nowrap;
    }
    .edu-pricing-package-card {
        scroll-margin-top: 100px;
        border-radius: var(--edu-radius);
        border: 1.5px solid #e2e8f0;
        background: #fff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .edu-pricing-package-card.is-purple { border-color: color-mix(in srgb, var(--edu-purple) 35%, #e2e8f0); }
    .edu-pricing-package-card.is-accent { border-color: color-mix(in srgb, var(--edu-accent-dark) 35%, #e2e8f0); }
    .edu-pricing-package-head {
        padding: 1.35rem 1.5rem;
        color: #fff;
    }
    .edu-pricing-package-head.is-primary {
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-primary-dark));
    }
    .edu-pricing-package-head.is-purple {
        background: linear-gradient(135deg, var(--edu-purple), var(--edu-primary));
    }
    .edu-pricing-package-head.is-accent {
        background: linear-gradient(135deg, var(--edu-accent-dark), #ea580c);
    }
    .edu-pricing-package-body { padding: 1.35rem 1.5rem 1.5rem; flex: 1; display: flex; flex-direction: column; }
    .edu-pricing-quote {
        margin-top: auto;
        padding: 1rem 1.1rem;
        border-radius: 12px;
        background: var(--edu-primary-light);
        border-inline-start: 4px solid var(--edu-primary);
        font-size: .875rem;
        font-weight: 600;
        color: var(--edu-primary-dark);
        line-height: 1.7;
    }
    .edu-pricing-types-strip {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: .5rem;
    }
    .edu-pricing-type-pill {
        padding: .4rem .9rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
        background: #fff;
        border: 1px solid #e2e8f0;
        color: var(--edu-primary-dark);
    }
    .edu-pricing-offer-row:nth-child(even) { background: #f8fafc; }
    @media (max-width: 639px) {
        .edu-pricing-package-card { scroll-margin-top: 88px; }
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\pricing-page.blade.php ENDPATH**/ ?>
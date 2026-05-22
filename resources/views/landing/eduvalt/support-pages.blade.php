{{-- أنماط صفحات الدعم (FAQ / Help) — مكمّلة لثيم Eduvalt --}}
<style>
    .edu-support-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 55%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-faq-filter {
        display: block;
        width: 100%;
        text-align: start;
        border-radius: 12px;
        padding: 0.55rem 1rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        transition: all .2s;
    }
    .edu-faq-filter:hover {
        border-color: color-mix(in srgb, var(--edu-primary) 35%, #e2e8f0);
        color: var(--edu-primary);
    }
    .edu-faq-filter.is-active {
        background: var(--edu-primary);
        border-color: var(--edu-primary);
        color: #fff;
        box-shadow: 0 8px 20px -10px rgba(var(--edu-primary-rgb), .45);
    }
    .edu-faq-acc {
        overflow: hidden;
        transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .edu-faq-acc:hover {
        transform: translateY(-2px);
        box-shadow: var(--edu-shadow);
    }
    .edu-faq-acc.is-open {
        border-color: color-mix(in srgb, var(--edu-primary) 30%, #e2e8f0);
    }
    .edu-faq-acc .faq-chevron {
        transition: transform .25s ease;
        color: var(--edu-primary);
    }
    .edu-faq-acc.is-open .faq-chevron {
        transform: rotate(180deg);
    }
    .edu-faq-side-panel {
        border-radius: var(--edu-radius);
        background: linear-gradient(145deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        color: #fff;
        padding: 1.35rem 1.5rem;
        box-shadow: 0 20px 45px -24px rgba(var(--edu-primary-rgb), .55);
    }
    .edu-faq-side-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.75rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        text-decoration: none;
        transition: background .2s;
    }
    .edu-faq-side-link:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    .edu-help-hub-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 100%;
        padding: 1.5rem 1.25rem;
        text-decoration: none;
        color: inherit;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .edu-help-hub-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--edu-shadow);
        color: inherit;
    }
    .edu-help-hub-icon {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.35rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 24px -12px rgba(var(--edu-primary-rgb), .4);
    }
    .edu-help-topic {
        display: block;
        padding: 1rem 1.25rem;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f1f5f9;
        transition: background .2s;
    }
    .edu-help-topic:last-child { border-bottom: none; }
    .edu-help-topic:hover {
        background: var(--edu-primary-light);
        color: inherit;
    }
    .edu-help-step-num {
        position: absolute;
        top: 0;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 800;
        color: #fff;
        inset-inline-start: -2.75rem;
    }
    .edu-hero-panel {
        border-radius: var(--edu-radius);
        background: linear-gradient(145deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        color: #fff;
        padding: 1.5rem 1.6rem;
        box-shadow: 0 24px 50px -28px rgba(var(--edu-primary-rgb), .55);
    }
    .edu-legal-card {
        border-radius: var(--edu-radius);
        border: 1.5px solid #e2e8f0;
        background: #fff;
        padding: 1.35rem 1.5rem;
        height: 100%;
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .edu-legal-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--edu-shadow);
        border-color: color-mix(in srgb, var(--edu-primary) 28%, #e2e8f0);
    }
    .edu-legal-card.is-wide { grid-column: 1 / -1; }
    .edu-cert-feature-card {
        border-radius: var(--edu-radius);
        border: 1.5px solid #e2e8f0;
        background: #fff;
        padding: 1.75rem 1.5rem;
        height: 100%;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .edu-cert-feature-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--edu-shadow);
    }
    .edu-cert-feature-icon {
        width: 3.75rem;
        height: 3.75rem;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        margin: 0 auto 1.25rem;
        box-shadow: 0 12px 28px -14px rgba(var(--edu-primary-rgb), .45);
    }
    .edu-cert-step {
        position: relative;
        padding-top: 2.75rem;
        text-align: center;
    }
    .edu-cert-step-num {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #fff;
        background: linear-gradient(135deg, var(--edu-primary), var(--edu-purple));
        box-shadow: 0 8px 20px -10px rgba(var(--edu-primary-rgb), .5);
    }
</style>

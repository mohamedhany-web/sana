{{-- صفحة الخدمات — بطاقات مدمجة + خلفية ملونة كالرئيسية --}}
<style>
    .edu-services-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #fff 45%, #f5f3ff 85%, #fffbeb 100%);
        border-bottom: 1px solid color-mix(in srgb, var(--edu-primary) 12%, #e2e8f0);
    }
    .edu-services-hero__blob {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(48px);
    }
    .edu-services-hero__blob--1 {
        top: -2rem;
        inset-inline-start: -3rem;
        width: 18rem;
        height: 18rem;
        background: radial-gradient(circle, var(--edu-primary-light) 0%, transparent 70%);
        opacity: 0.85;
    }
    .edu-services-hero__blob--2 {
        bottom: -3rem;
        inset-inline-end: -2rem;
        width: 22rem;
        height: 22rem;
        background: radial-gradient(circle, color-mix(in srgb, var(--edu-purple) 18%, #e9d5ff) 0%, transparent 70%);
        opacity: 0.7;
    }
    .edu-services-hero__blob--3 {
        top: 40%;
        inset-inline-end: 15%;
        width: 10rem;
        height: 10rem;
        background: radial-gradient(circle, var(--edu-accent-light) 0%, transparent 70%);
        opacity: 0.65;
        filter: blur(32px);
    }
    .edu-services-hero__shape {
        position: absolute;
        pointer-events: none;
        opacity: 0.12;
        color: var(--edu-primary);
    }
    .edu-services-section {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        position: relative;
        overflow: hidden;
    }
    .edu-services-section::before {
        content: '';
        position: absolute;
        inset-inline-end: -8rem;
        top: -6rem;
        width: 20rem;
        height: 20rem;
        border-radius: 50%;
        background: radial-gradient(circle, var(--edu-primary-light), transparent 68%);
        opacity: 0.5;
        pointer-events: none;
    }
    .edu-cat-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: inherit;
    }
    .edu-service-prose {
        line-height: 1.75;
        color: #64748b;
        font-size: 0.9375rem;
    }
    .edu-service-prose p { margin-bottom: 0.75rem; }
    .edu-service-prose p:last-child { margin-bottom: 0; }
    .edu-service-thumb-sm {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
    }

    /* صفحة تفاصيل الخدمة — عرض كامل */
    .edu-service-show-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #fff 40%, #f5f3ff 75%, #fffbeb 100%);
        border-bottom: 1px solid color-mix(in srgb, var(--edu-primary) 10%, #e2e8f0);
        position: relative;
        overflow: hidden;
    }
    .edu-service-show-media {
        position: relative;
        border-radius: var(--edu-radius);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 50px -24px rgba(var(--edu-primary-rgb), .35);
        background: #fff;
        aspect-ratio: 4/3;
        min-height: 280px;
    }
    .edu-service-show-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .edu-service-show-media__placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, var(--edu-primary-light) 0%, #fff 50%, var(--edu-purple-light) 100%);
    }
    .edu-service-show-media__placeholder i {
        font-size: 4rem;
        color: color-mix(in srgb, var(--edu-primary) 35%, transparent);
    }
    .edu-service-show-media::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 23, 42, .25) 0%, transparent 45%);
        pointer-events: none;
    }
    .edu-service-show-chip {
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
        box-shadow: 0 2px 8px -4px rgba(15, 23, 42, .12);
    }
    .edu-service-show-body {
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }
    .edu-service-show-article {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius);
        box-shadow: 0 8px 32px -20px rgba(15, 23, 42, .12);
        padding: 1.75rem 2rem;
        position: relative;
        overflow: hidden;
    }
    .edu-service-show-article::before {
        content: '';
        position: absolute;
        top: 0;
        inset-inline: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--edu-primary), var(--edu-purple), var(--edu-accent));
    }
    .edu-service-show-prose {
        font-size: 1.02rem;
        line-height: 1.9;
        color: #475569;
    }
    .edu-service-show-prose p { margin-bottom: 1rem; }
    .edu-service-show-sidebar {
        position: sticky;
        top: 100px;
    }
    .edu-service-show-cta-card {
        border-radius: var(--edu-radius);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 40px -20px rgba(var(--edu-primary-rgb), .25);
        background: #fff;
    }
    .edu-service-show-cta-head {
        padding: 1.35rem 1.5rem;
        background: linear-gradient(120deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        color: #fff;
        text-align: center;
    }
    .edu-service-show-cta-body {
        padding: 1.25rem 1.5rem;
    }
    .edu-service-related-card {
        display: flex;
        gap: 1rem;
        align-items: center;
        padding: 1rem 1.15rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius-sm);
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .edu-service-related-card:hover {
        border-color: color-mix(in srgb, var(--edu-primary) 28%, #e2e8f0);
        box-shadow: 0 10px 28px -14px rgba(var(--edu-primary-rgb), .2);
        transform: translateY(-2px);
    }
    .edu-service-related-thumb {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: var(--edu-primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--edu-primary);
        font-size: 1.25rem;
    }
    .edu-service-related-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    @media (max-width: 640px) {
        .edu-service-show-article { padding: 1.25rem 1.35rem; }
        .edu-service-show-sidebar { position: static; }
    }
</style>

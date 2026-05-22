<style>
    .edu-course-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 60%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-course-media {
        border-radius: var(--edu-radius-sm);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 12px 40px -16px rgba(var(--edu-primary-rgb), .2);
        background: #fff;
    }
    .edu-course-media .aspect-video { aspect-ratio: 16/10; min-height: 220px; }
    .edu-course-stat {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .85rem;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        font-size: .8rem;
        font-weight: 600;
        color: #475569;
    }
    .edu-price-card-head {
        background: linear-gradient(120deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        padding: 1.25rem 1.5rem;
        text-align: center;
        color: #fff;
    }
    .edu-sidebar-sticky { position: sticky; top: 100px; }
    .edu-learn-item {
        display: flex;
        gap: .75rem;
        padding: .85rem 1rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: .875rem;
        color: #475569;
    }
    .edu-related-thumb {
        width: 4rem;
        height: 4rem;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--edu-primary-light), #fff);
    }
    .edu-related-thumb .edu-course-fav-btn {
        width: 1.75rem;
        height: 1.75rem;
        top: .25rem;
        inset-inline-end: .25rem;
        font-size: .65rem;
    }
</style>

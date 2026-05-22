{{-- صفحة ملف المدرب العام — مكمّلة لثيم Eduvalt --}}
<style>
    .edu-instructor-profile-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 55%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-instructor-profile-photo {
        width: 9.5rem;
        height: 9.5rem;
        border-radius: 999px;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 12px 36px -14px rgba(var(--edu-primary-rgb), .45);
    }
    @media (min-width: 768px) {
        .edu-instructor-profile-photo {
            width: 11rem;
            height: 11rem;
        }
    }
    .edu-profile-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
    }
    .edu-profile-section {
        padding: 1.5rem 1.75rem;
    }
    @media (min-width: 640px) {
        .edu-profile-section { padding: 1.75rem 2rem; }
    }
    .edu-profile-section-head {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.25rem;
    }
    .edu-profile-section-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .edu-profile-exp-item {
        display: flex;
        gap: .75rem;
        padding: 1rem 1.1rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: .875rem;
        color: #475569;
        line-height: 1.65;
    }
    .edu-profile-exp-item i {
        margin-top: .2rem;
        color: var(--edu-accent-dark);
        flex-shrink: 0;
    }
    .edu-instructor-courses-grid {
        display: grid;
        gap: 1.25rem;
    }
    @media (min-width: 640px) {
        .edu-instructor-courses-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .edu-instructor-course-link {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
        background: #fff;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .edu-instructor-course-link:hover {
        transform: translateY(-4px);
        box-shadow: var(--edu-shadow);
        border-color: color-mix(in srgb, var(--edu-primary) 25%, #e2e8f0);
    }
    .edu-instructor-course-thumb {
        aspect-ratio: 16/10;
        background: linear-gradient(135deg, var(--edu-primary-light), #fff);
        overflow: hidden;
        position: relative;
    }
    .edu-instructor-course-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .edu-instructor-course-link:hover .edu-instructor-course-thumb img {
        transform: scale(1.05);
    }
    .edu-profile-sidebar-sticky {
        position: sticky;
        top: 100px;
    }
    .edu-consult-card-head {
        background: linear-gradient(120deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        padding: 1.15rem 1.25rem;
        color: #fff;
    }
    .edu-skill-pill {
        display: inline-block;
        padding: .35rem .75rem;
        border-radius: 10px;
        font-size: .75rem;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .edu-profile-social {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #64748b;
        transition: background .2s, color .2s;
    }
    .edu-profile-social:hover {
        background: var(--edu-primary);
        color: #fff;
    }
    .hover-lift {
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -20px rgba(var(--edu-primary-rgb), .35);
    }
</style>


<style>
    .edu-instructors-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 55%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-instructors-grid {
        display: grid;
        gap: 1.5rem;
    }
    @media (min-width: 640px) {
        .edu-instructors-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1024px) {
        .edu-instructors-grid { grid-template-columns: repeat(3, 1fr); gap: 1.75rem; }
    }
    @media (min-width: 1280px) {
        .edu-instructors-grid { grid-template-columns: repeat(4, 1fr); }
    }
    .edu-instructor-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .edu-instructor-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--edu-shadow);
    }
    .edu-instructor-photo-wrap {
        position: relative;
        margin: 1.25rem auto 0;
        width: 7.5rem;
        height: 7.5rem;
        border-radius: 999px;
        background: var(--edu-primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .edu-instructor-photo-wrap img {
        width: 6.75rem;
        height: 6.75rem;
        border-radius: 999px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 8px 24px -10px rgba(var(--edu-primary-rgb), .35);
    }
    .edu-instructor-badge {
        position: absolute;
        top: -.25rem;
        inset-inline-start: -.25rem;
        padding: .2rem .55rem;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 800;
        color: #fff;
        background: var(--edu-accent-dark);
        box-shadow: 0 4px 12px -4px rgba(var(--edu-accent-rgb), .5);
    }
    .edu-instructor-courses-pill {
        position: absolute;
        bottom: 0;
        inset-inline-end: -.5rem;
        padding: .25rem .6rem;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 700;
        background: var(--edu-primary);
        color: #fff;
        border: 2px solid #fff;
    }
    .edu-instructor-body {
        padding: 1rem 1.25rem 1.25rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .edu-instructor-skills {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
        justify-content: center;
        margin-bottom: .75rem;
    }
    .edu-instructor-skill {
        font-size: .65rem;
        font-weight: 600;
        padding: .2rem .55rem;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
    }
    .edu-instructor-consult {
        margin-top: auto;
        padding-top: .85rem;
        border-top: 1px solid #f1f5f9;
    }
    .edu-instructors-search {
        width: 100%;
        max-width: 420px;
        padding: .75rem 2.75rem .75rem 1rem;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        font-size: .9rem;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .edu-instructors-search:focus {
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12);
    }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\instructors-page.blade.php ENDPATH**/ ?>

<style>
    /* خلفية بعرض الشاشة — المحتوى بنفس عرض edu-container السابق */
    .edu-container-full {
        width: 100%;
        max-width: none;
        margin-inline: auto;
        padding-inline: 1.25rem;
    }
    @media (min-width: 1024px) {
        .edu-container-full { padding-inline: 2rem; }
    }
    .edu-courses-inner {
        width: 100%;
        max-width: 1280px;
        margin-inline: auto;
    }
    .edu-courses-page-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 55%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-courses-layout {
        display: grid;
        gap: 1.75rem;
    }
    @media (min-width: 1024px) {
        .edu-courses-layout {
            grid-template-columns: 280px 1fr;
            align-items: start;
        }
    }
    .edu-courses-sidebar {
        position: sticky;
        top: 100px;
    }
    .edu-filter-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius-sm);
        box-shadow: 0 4px 24px -12px rgba(15, 23, 42, .08);
        padding: 1.25rem;
    }
    .edu-filter-search {
        width: 100%;
        padding: .75rem 2.75rem .75rem 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        font-size: .9rem;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .edu-filter-search:focus {
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(19, 99, 223, .12);
    }
    html[dir="rtl"] .edu-filter-search { padding: .75rem 1rem .75rem 2.75rem; }
    .edu-cat-filter {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        width: 100%;
        padding: .65rem .85rem;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 600;
        color: #475569;
        transition: background .2s, color .2s;
        text-align: start;
    }
    .edu-cat-filter:hover { background: #f1f5f9; color: var(--edu-primary); }
    .edu-cat-filter.is-active {
        background: var(--edu-primary-light);
        color: var(--edu-primary);
    }
    .edu-cat-filter .count {
        font-size: .7rem;
        font-weight: 700;
        padding: .15rem .5rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
    }
    .edu-cat-filter.is-active .count {
        background: #fff;
        color: var(--edu-primary);
    }
    .edu-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .edu-view-toggle {
        display: inline-flex;
        padding: 4px;
        background: #f1f5f9;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .edu-view-btn {
        width: 2.5rem;
        height: 2.25rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: background .2s, color .2s;
    }
    .edu-view-btn.is-active {
        background: #fff;
        color: var(--edu-primary);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .08);
    }
    .edu-course-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        background: #fff;
        border-radius: var(--edu-radius-sm);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -10px rgba(15, 23, 42, .1);
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .edu-course-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--edu-shadow);
    }
    .edu-course-card.list-mode {
        flex-direction: row;
        height: auto;
    }
    @media (max-width: 640px) {
        .edu-course-card.list-mode { flex-direction: column; }
    }
    .edu-course-card.list-mode .edu-course-thumb {
        width: 220px;
        min-height: 160px;
        aspect-ratio: auto;
        flex-shrink: 0;
    }
    @media (max-width: 640px) {
        .edu-course-card.list-mode .edu-course-thumb { width: 100%; aspect-ratio: 16/10; }
    }
    .edu-course-thumb {
        position: relative;
        aspect-ratio: 16/10;
        overflow: hidden;
        background: linear-gradient(135deg, var(--edu-primary-light), #fff);
    }
    .edu-course-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .edu-course-card:hover .edu-course-thumb img { transform: scale(1.05); }
    .edu-quick-action {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: 1rem 1.1rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: var(--edu-radius-sm);
        box-shadow: 0 4px 16px -10px rgba(15, 23, 42, .08);
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .edu-quick-action:hover {
        border-color: color-mix(in srgb, var(--edu-primary) 35%, #e2e8f0);
        box-shadow: var(--edu-shadow);
        transform: translateY(-2px);
    }
    .edu-quick-action-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .edu-quick-tip {
        transition: box-shadow .2s;
    }
    .edu-check-row {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .85rem;
        color: #475569;
        cursor: pointer;
        padding: .35rem 0;
    }
    .edu-check-row input { accent-color: var(--edu-primary); }
    .edu-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .35rem;
        font-size: .8rem;
        color: #64748b;
        margin-bottom: .75rem;
    }
    .edu-breadcrumb a:hover { color: var(--edu-primary); }
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/landing/eduvalt/courses-page.blade.php ENDPATH**/ ?>
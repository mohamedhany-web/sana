{{-- أنماط صفحة تواصل معنا — مكمّلة لثيم Eduvalt --}}
<style>
    .edu-contact-hero {
        background: linear-gradient(135deg, #f0f6ff 0%, #fff 55%, #f8fafc 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .edu-contact-trust {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.15rem;
        border-radius: var(--edu-radius-sm);
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px -16px rgba(var(--edu-primary-rgb), .2);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .edu-contact-trust:hover {
        transform: translateY(-2px);
        box-shadow: var(--edu-shadow);
    }
    .edu-contact-form-card {
        padding: 0;
        overflow: hidden;
    }
    .edu-contact-form-card__head {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(135deg, var(--edu-primary-light) 0%, #fff 70%);
    }
    .edu-contact-form-card__body {
        padding: 1.5rem 1.75rem 1.75rem;
    }
    .edu-contact-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.45rem;
    }
    .edu-contact-label i {
        color: var(--edu-primary);
        font-size: 0.75rem;
        opacity: 0.9;
    }
    .edu-contact-input {
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background: #f8fafc;
        padding: 0.7rem 1rem;
        font-size: 0.9375rem;
        color: #0f172a;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .edu-contact-input:hover { border-color: #cbd5e1; }
    .edu-contact-input:focus {
        outline: none;
        border-color: var(--edu-primary);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(var(--edu-primary-rgb), .1);
    }
    .edu-contact-chip {
        border-radius: 999px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        padding: 0.4rem 0.9rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #64748b;
        transition: all .2s;
    }
    .edu-contact-chip:hover,
    .edu-contact-chip.is-active {
        border-color: var(--edu-primary);
        color: var(--edu-primary);
        background: var(--edu-primary-light);
    }
    .edu-contact-channel {
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 1rem;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.08);
        text-decoration: none;
        color: inherit;
        transition: background .2s, transform .2s;
    }
    .edu-contact-channel:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-1px);
        color: inherit;
    }
    .edu-contact-channel-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .edu-contact-side-panel {
        border-radius: var(--edu-radius);
        background: linear-gradient(145deg, var(--edu-primary) 0%, var(--edu-purple) 100%);
        color: #fff;
        padding: 1.5rem 1.6rem;
        box-shadow: 0 20px 45px -24px rgba(var(--edu-primary-rgb), .55);
    }
    .edu-contact-wa {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        background: #25d366;
        color: #fff;
        text-decoration: none;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 10px 24px -10px rgba(37, 211, 102, .5);
    }
    .edu-contact-wa:hover {
        transform: translateY(-1px);
        color: #fff;
        box-shadow: 0 14px 28px -8px rgba(37, 211, 102, .55);
    }
    .edu-contact-help-card {
        padding: 1.25rem 1.35rem;
        background: linear-gradient(135deg, #fff7f0 0%, #fff 50%);
        border-color: #fed7aa;
    }
    .edu-contact-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        padding: 0.9rem 1rem;
        border-radius: 12px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>

<style>
/* ═══ INSTRUCTOR PROFILE PAGE ═══ */
.sana-instructor-show-page .sana-is-page { padding-top: 72px; background: var(--bg); }
@media (max-width: 991px) {
    .sana-instructor-show-page .sana-is-page { padding-top: 64px; padding-bottom: 88px; }
}

.sana-is-hero {
    position: relative;
    padding: clamp(28px, 5vw, 48px) 0 clamp(36px, 5vw, 52px);
    background: linear-gradient(165deg, #4C1D95 0%, #6D28D9 50%, #7C3AED 100%);
    overflow: hidden;
}
.sana-is-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 15% 25%, rgba(251,191,36,0.14), transparent 42%),
        radial-gradient(circle at 88% 65%, rgba(167,139,250,0.22), transparent 40%);
    pointer-events: none;
}
.sana-is-hero__dots {
    position: absolute;
    inset: 0;
    opacity: 0.35;
    background-image: radial-gradient(rgba(255,255,255,0.35) 1px, transparent 1px);
    background-size: 22px 22px;
    pointer-events: none;
}
.sana-is-hero__inner { position: relative; z-index: 1; }
.sana-is-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    color: rgba(255,255,255,0.65);
    margin-bottom: 22px;
}
.sana-is-breadcrumb a { color: rgba(255,255,255,0.88); text-decoration: none !important; }
.sana-is-breadcrumb a:hover { color: #fff; }

.sana-is-hero__grid {
    display: grid;
    gap: 28px;
    align-items: center;
}
@media (min-width: 992px) {
    .sana-is-hero__grid { grid-template-columns: 1fr auto; gap: 40px; }
}

.sana-is-hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border-radius: 999px;
    margin-bottom: 14px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    font-size: 0.76rem;
    font-weight: 800;
    color: #FDE68A;
}
.sana-is-hero__title {
    font-size: clamp(1.75rem, 4.5vw, 2.5rem);
    font-weight: 900;
    color: #fff;
    line-height: 1.25;
    margin: 0 0 10px;
}
.sana-is-hero__headline {
    font-size: clamp(0.95rem, 2vw, 1.08rem);
    font-weight: 800;
    color: rgba(255,255,255,0.92);
    margin: 0 0 16px;
    line-height: 1.6;
}
.sana-is-hero__bio {
    color: rgba(255,255,255,0.78);
    font-size: 0.9rem;
    line-height: 1.85;
    margin: 0 0 18px;
    max-width: 560px;
    font-weight: 600;
}
.sana-is-hero__pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 18px;
}
.sana-is-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    color: #fff;
}
.sana-is-pill--gold {
    background: rgba(251,191,36,0.22);
    color: #FDE68A;
    border-color: rgba(251,191,36,0.35);
}
.sana-is-pill--book {
    background: rgba(16,185,129,0.22);
    color: #A7F3D0;
    border-color: rgba(16,185,129,0.35);
}
.sana-is-hero__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 18px;
    margin-bottom: 22px;
    font-size: 0.82rem;
    font-weight: 700;
    color: rgba(255,255,255,0.88);
}
.sana-is-hero__meta span { display: inline-flex; align-items: center; gap: 6px; }
.sana-is-hero__meta i { color: #FDE68A; font-size: 0.78rem; }
.sana-is-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.sana-is-hero__actions .sana-btn--white-outline {
    background: rgba(255,255,255,0.08);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,0.35);
}
.sana-is-hero__actions .sana-btn--white-outline:hover {
    background: rgba(255,255,255,0.16);
}

.sana-is-hero__photo {
    margin-inline: auto;
    text-align: center;
}
.sana-is-hero__ring {
    width: clamp(140px, 22vw, 180px);
    height: clamp(140px, 22vw, 180px);
    margin: 0 auto;
    padding: 5px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold), var(--p-light));
    box-shadow: 0 24px 56px rgba(0,0,0,0.28);
}
.sana-is-hero__ring img,
.sana-is-hero__ring .av {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.8rem;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
}

.sana-is-layout {
    display: grid;
    gap: 24px;
    align-items: start;
}
@media (min-width: 992px) {
    .sana-is-layout { grid-template-columns: 1fr 320px; gap: 28px; }
}

.sana-is-panel {
    background: #fff;
    border-radius: 22px;
    border: 1px solid #EDE9FE;
    padding: 22px 24px;
    margin-bottom: 18px;
    box-shadow: 0 12px 36px -18px rgba(91,33,182,0.12);
}
.sana-is-panel:last-child { margin-bottom: 0; }
.sana-is-panel__title {
    font-size: 1.05rem;
    font-weight: 900;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text);
}
.sana-is-panel__title i { color: var(--p); font-size: 0.95rem; }

.sana-is-video {
    border-radius: 16px;
    overflow: hidden;
    background: #0f172a;
    aspect-ratio: 16/9;
    box-shadow: 0 16px 40px -16px rgba(0,0,0,0.35);
}
.sana-is-video iframe,
.sana-is-video video { width: 100%; height: 100%; border: 0; display: block; }

.sana-is-exp-list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 12px;
}
.sana-is-exp-list li {
    display: flex;
    gap: 10px;
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--muted);
    line-height: 1.65;
}
.sana-is-exp-list li i {
    color: var(--p);
    margin-top: 4px;
    flex-shrink: 0;
}
.sana-is-exp-text {
    margin: 0;
    white-space: pre-line;
    line-height: 1.85;
    color: var(--muted);
    font-size: 0.9rem;
    font-weight: 600;
}

.sana-is-courses-grid {
    display: grid;
    gap: 18px;
    grid-template-columns: repeat(1, minmax(0, 1fr));
}
@media (min-width: 640px) {
    .sana-is-courses-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

.sana-is-sidebar {
    position: sticky;
    top: 96px;
    background: #fff;
    border-radius: 22px;
    border: 1px solid #EDE9FE;
    padding: 22px;
    box-shadow: 0 16px 44px -20px rgba(91,33,182,0.18);
}
.sana-is-sidebar__label {
    font-size: 0.72rem;
    font-weight: 800;
    color: var(--p);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 8px;
}
.sana-is-sidebar__heading {
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--text);
    margin: 0 0 8px;
    line-height: 1.4;
}
.sana-is-sidebar__desc {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--muted);
    line-height: 1.75;
    margin: 0 0 18px;
}
.sana-is-sidebar__list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    border-top: 1px solid #F3F4F6;
}
.sana-is-sidebar__list li {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #F3F4F6;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--muted);
}
.sana-is-sidebar__list li span:last-child {
    color: var(--p-dark);
    font-weight: 900;
    text-align: end;
    max-width: 55%;
}
.sana-is-sidebar__actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.sana-is-sidebar__actions .sana-btn,
.sana-is-sidebar__actions .sana-site-cta .sana-btn {
    width: 100%;
    justify-content: center;
}
.sana-is-sidebar .sana-site-cta--stack { width: 100%; }
.sana-btn--ghost-muted {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #FAFAFF;
    color: var(--p);
    border: 1.5px solid #EDE9FE;
    border-radius: 999px;
    font-family: var(--font);
    font-weight: 800;
    padding: 12px 20px;
    text-decoration: none !important;
    transition: background 0.2s, border-color 0.2s;
}
.sana-btn--ghost-muted:hover { background: #F5F3FF; border-color: #DDD6FE; }

.sana-is-mobile-bar {
    display: none;
    position: fixed;
    inset-inline: 0;
    bottom: 0;
    z-index: 90;
    padding: 12px 16px calc(12px + env(safe-area-inset-bottom));
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(12px);
    border-top: 1px solid #EDE9FE;
    box-shadow: 0 -8px 32px rgba(91,33,182,0.1);
}
.sana-is-mobile-bar .sana-btn { width: 100%; justify-content: center; }
@media (max-width: 991px) {
    .sana-is-mobile-bar.is-visible { display: block; }
    .sana-is-sidebar__actions .sana-btn--yellow { display: none; }
}
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\instructor-show-theme.blade.php ENDPATH**/ ?>
<style>
/* ═══ SANA COURSE DETAILS PAGE ═══ */
.sana-course-page { padding-top: 72px; background: var(--bg); }
@media (max-width: 991px) { .sana-course-page { padding-top: 64px; padding-bottom: 88px; } }
.sana-courses-page.sana-course-detail-page .sana-nav { /* inherits solid nav */ }

/* HERO */
.sana-cd-hero {
    position: relative;
    padding: clamp(28px, 5vw, 48px) 0 clamp(40px, 6vw, 56px);
    background: linear-gradient(165deg, #4C1D95 0%, #6D28D9 50%, #7C3AED 100%);
    overflow: hidden;
}
.sana-cd-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 30%, rgba(251,191,36,0.15), transparent 45%),
                radial-gradient(circle at 85% 70%, rgba(167,139,250,0.2), transparent 40%);
    pointer-events: none;
}
.sana-cd-hero__inner { position: relative; z-index: 1; }
.sana-cd-breadcrumb {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px;
    font-size: 0.78rem; font-weight: 700; color: rgba(255,255,255,0.65); margin-bottom: 20px;
}
.sana-cd-breadcrumb a { color: rgba(255,255,255,0.88); text-decoration: none !important; }
.sana-cd-breadcrumb a:hover { color: #fff; }
.sana-cd-hero__grid {
    display: grid; gap: 32px; align-items: start;
}
@media (min-width: 992px) {
    .sana-cd-hero__grid { grid-template-columns: 1fr 380px; gap: 40px; }
}
.sana-cd-hero__badges { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.sana-cd-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 12px; border-radius: 999px; font-size: 0.72rem; font-weight: 800;
    background: rgba(255,255,255,0.14); border: 1px solid rgba(255,255,255,0.22); color: #fff;
}
.sana-cd-pill--gold { background: rgba(251,191,36,0.22); color: #FDE68A; border-color: rgba(251,191,36,0.35); }
.sana-cd-hero__title {
    font-size: clamp(1.75rem, 4.5vw, 2.65rem); font-weight: 900; color: #fff;
    line-height: 1.25; margin: 0 0 14px;
}
.sana-cd-hero__desc {
    color: rgba(255,255,255,0.85); font-size: clamp(0.92rem, 2vw, 1.05rem);
    line-height: 1.8; margin: 0 0 22px; max-width: 640px;
}
.sana-cd-hero__meta {
    display: flex; flex-wrap: wrap; gap: 10px 18px; margin-bottom: 22px;
    font-size: 0.82rem; font-weight: 700; color: rgba(255,255,255,0.88);
}
.sana-cd-hero__meta span { display: inline-flex; align-items: center; gap: 6px; }
.sana-cd-hero__meta .stars { color: #FDE68A; }
.sana-cd-hero__instructor {
    display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
    padding: 12px 14px; border-radius: 16px;
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.16);
    max-width: 360px;
}
.sana-cd-hero__instructor img, .sana-cd-hero__instructor .av {
    width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
    background: linear-gradient(135deg, var(--gold), #F59E0B); color: var(--p-dark);
    display: flex; align-items: center; justify-content: center; font-weight: 900;
    border: 2px solid rgba(255,255,255,0.5);
}
.sana-cd-hero__instructor small { display: block; font-size: 0.68rem; opacity: 0.75; margin-bottom: 2px; }
.sana-cd-hero__instructor strong { font-size: 0.9rem; color: #fff; }
.sana-cd-hero__instructor a { color: #fff !important; text-decoration: none !important; }
.sana-cd-hero__actions { display: flex; flex-wrap: wrap; gap: 10px; }
.sana-cd-hero__media {
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 24px 56px rgba(0,0,0,0.35);
    border: 3px solid rgba(255,255,255,0.2);
    aspect-ratio: 16/10; background: #1e1b4b;
}
.sana-cd-hero__media img, .sana-cd-hero__media iframe, .sana-cd-hero__media video {
    width: 100%; height: 100%; object-fit: cover; display: block; border: 0;
}

/* CTAs */
.sana-course-cta {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 24px; border-radius: 999px; font-family: inherit;
    font-weight: 900; font-size: 0.92rem; text-decoration: none !important;
    border: none; cursor: pointer; transition: transform 0.25s, box-shadow 0.25s;
    min-height: 48px;
}
.sana-course-cta--block { width: 100%; }
.sana-course-cta:not(.sana-course-cta--outline):not(.sana-course-cta--free):not(.sana-course-cta--whatsapp) {
    background: linear-gradient(135deg, var(--gold), #F59E0B); color: var(--p-dark);
    box-shadow: 0 10px 28px rgba(251,191,36,0.45);
}
.sana-course-cta:not(.sana-course-cta--outline):hover { transform: translateY(-2px); }
.sana-course-cta--outline {
    background: rgba(255,255,255,0.12); color: #fff !important;
    border: 2px solid rgba(255,255,255,0.45);
}
.sana-course-cta--outline:hover { background: rgba(255,255,255,0.2); }
.sana-course-cta--free { background: linear-gradient(135deg, #059669, #10B981) !important; color: #fff !important; }
.sana-course-cta--whatsapp { background: #25D366 !important; color: #fff !important; }

/* MAIN LAYOUT 70/30 */
.sana-cd-body { padding: 48px 0 64px; }
.sana-cd-layout {
    display: grid; gap: 36px; align-items: start;
}
@media (min-width: 992px) {
    .sana-cd-layout { grid-template-columns: minmax(0, 1fr) 340px; gap: 40px; }
}
.sana-cd-main { min-width: 0; display: flex; flex-direction: column; gap: 28px; }

/* Section cards */
.sana-cd-section {
    background: #fff; border-radius: 24px; padding: clamp(22px, 4vw, 32px);
    border: 1px solid #EDE9FE;
    box-shadow: 0 8px 32px -12px rgba(91,33,182,0.1);
}
.sana-cd-section__head {
    display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
}
.sana-cd-section__icon {
    width: 44px; height: 44px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    background: #F5F3FF; color: var(--p);
}
.sana-cd-section__title { font-size: 1.15rem; font-weight: 900; margin: 0; color: var(--text); }
.sana-cd-section__sub { font-size: 0.85rem; color: var(--muted); line-height: 1.75; margin: 0; }

/* Learn list */
.sana-cd-learn-grid {
    display: grid; gap: 10px;
}
@media (min-width: 640px) { .sana-cd-learn-grid { grid-template-columns: repeat(2, 1fr); } }
.sana-cd-learn-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px; border-radius: 14px;
    background: #F5F3FF; font-size: 0.85rem; font-weight: 600; line-height: 1.55;
}
.sana-cd-learn-item i { color: #059669; margin-top: 3px; flex-shrink: 0; }

/* Curriculum accordion */
.sana-cd-curriculum { display: flex; flex-direction: column; gap: 10px; }
.sana-cd-module {
    border-radius: 18px; border: 1px solid #EDE9FE; overflow: hidden;
    background: #FAFAFF;
}
.sana-cd-module__toggle {
    width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 12px;
    padding: 16px 18px; border: none; background: transparent; cursor: pointer;
    font-family: inherit; text-align: right;
}
.sana-cd-module__toggle strong { font-size: 0.92rem; font-weight: 900; color: var(--text); }
.sana-cd-module__toggle span { font-size: 0.75rem; font-weight: 700; color: var(--muted); white-space: nowrap; }
.sana-cd-module__toggle i.chevron { transition: transform 0.25s; color: var(--p); }
.sana-cd-module.is-open .sana-cd-module__toggle i.chevron { transform: rotate(180deg); }
.sana-cd-module__body {
    display: none; padding: 0 12px 12px;
}
.sana-cd-module.is-open .sana-cd-module__body { display: block; }
.sana-cd-lesson {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px; border-radius: 12px; background: #fff;
    border: 1px solid #F1F5F9; margin-bottom: 6px;
    font-size: 0.82rem; font-weight: 600; color: var(--text);
}
.sana-cd-lesson:last-child { margin-bottom: 0; }
.sana-cd-lesson__icon {
    width: 34px; height: 34px; min-width: 34px; border-radius: 10px;
    background: #EDE9FE; color: var(--p);
    display: flex; align-items: center; justify-content: center; font-size: 0.8rem;
}
.sana-cd-lesson__meta { margin-inline-start: auto; font-size: 0.72rem; color: var(--muted); font-weight: 700; }

/* Instructor block */
.sana-cd-instructor {
    display: grid; gap: 24px;
}
@media (min-width: 640px) { .sana-cd-instructor { grid-template-columns: auto 1fr; align-items: start; } }
.sana-cd-instructor__photo {
    width: 120px; height: 120px; border-radius: 24px; object-fit: cover;
    border: 4px solid #EDE9FE; box-shadow: 0 12px 32px rgba(91,33,182,0.15);
}
.sana-cd-instructor__photo--initial {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
    color: #fff; font-size: 2.5rem; font-weight: 900;
}
.sana-cd-instructor__stats {
    display: flex; flex-wrap: wrap; gap: 16px 24px; margin: 16px 0;
}
.sana-cd-instructor__stats div strong {
    display: block; font-size: 1.25rem; font-weight: 900; color: var(--p);
}
.sana-cd-instructor__stats div span { font-size: 0.72rem; font-weight: 700; color: var(--muted); }

/* Reviews */
.sana-cd-reviews-layout {
    display: grid; gap: 28px;
}
@media (min-width: 768px) { .sana-cd-reviews-layout { grid-template-columns: 220px 1fr; } }
.sana-cd-rating-big {
    text-align: center; padding: 24px; border-radius: 20px;
    background: linear-gradient(145deg, #F5F3FF, #EDE9FE);
    border: 1px solid #DDD6FE;
}
.sana-cd-rating-big strong { font-size: 3rem; font-weight: 900; color: var(--p-dark); line-height: 1; }
.sana-cd-rating-big .stars { color: #F59E0B; font-size: 0.85rem; margin: 8px 0; }
.sana-cd-rating-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 0.72rem; font-weight: 700; }
.sana-cd-rating-bar__track { flex: 1; height: 6px; border-radius: 999px; background: #EDE9FE; overflow: hidden; }
.sana-cd-rating-bar__track span { display: block; height: 100%; background: var(--p); border-radius: inherit; }
.sana-cd-review-card {
    padding: 20px; border-radius: 18px; border: 1px solid #EDE9FE;
    background: #fff; margin-bottom: 12px;
    box-shadow: 0 4px 16px -8px rgba(91,33,182,0.08);
}
.sana-cd-review-card:last-child { margin-bottom: 0; }
.sana-cd-review-card__head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.sana-cd-review-card__head img, .sana-cd-review-card__head .av {
    width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
    background: var(--p); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900;
}
.sana-cd-review-card__stars { color: #F59E0B; font-size: 0.72rem; }
.sana-cd-review-card p { margin: 0; font-size: 0.88rem; color: var(--muted); line-height: 1.75; }

/* STICKY ENROLL CARD */
.sana-cd-sidebar { position: relative; }
@media (min-width: 992px) {
    .sana-cd-sidebar { position: sticky; top: calc(84px + env(safe-area-inset-top, 0)); }
}
.sana-cd-enroll {
    background: #fff; border-radius: 24px; overflow: hidden;
    border: 1px solid #EDE9FE;
    box-shadow: 0 20px 48px -16px rgba(91,33,182,0.22);
}
.sana-cd-enroll__thumb { aspect-ratio: 16/10; overflow: hidden; background: #EDE9FE; position: relative; }
.sana-cd-enroll__thumb img { width: 100%; height: 100%; object-fit: cover; }
.sana-cd-enroll__body { padding: 22px 20px; }
.sana-cd-enroll__price-head { margin-bottom: 18px; text-align: center; }
.sana-cd-enroll__price-old {
    font-size: 0.88rem; color: var(--muted); text-decoration: line-through; font-weight: 700;
}
.sana-cd-enroll__price-now {
    font-size: 2rem; font-weight: 900; color: var(--p-dark); line-height: 1.2;
}
.sana-cd-enroll__price-free {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 1.25rem; font-weight: 900; color: #059669;
    padding: 10px 18px; border-radius: 999px; background: #D1FAE5;
}
.sana-cd-enroll__actions { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
.sana-cd-enroll__actions .sana-course-cta:not(.sana-course-cta--outline) {
    background: linear-gradient(135deg, var(--p-dark), var(--p)); color: #fff !important;
    box-shadow: 0 10px 28px rgba(91,33,182,0.35);
}
.sana-cd-enroll__actions .sana-course-cta--outline {
    background: #fff; color: var(--p) !important; border: 2px solid #EDE9FE;
}
.sana-cd-enroll__trust {
    display: flex; flex-direction: column; gap: 10px;
    padding-top: 16px; border-top: 1px dashed #EDE9FE;
}
.sana-cd-enroll__trust-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 0.78rem; font-weight: 700; color: var(--muted);
}
.sana-cd-enroll__trust-item i { width: 28px; text-align: center; color: var(--p); }
.sana-cd-enroll__stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 16px; }
.sana-cd-enroll__stat {
    padding: 10px; border-radius: 12px; background: #F5F3FF; text-align: center;
    font-size: 0.72rem; font-weight: 700; color: var(--muted);
}
.sana-cd-enroll__stat strong { display: block; font-size: 0.95rem; font-weight: 900; color: var(--p); margin-bottom: 2px; }

/* Related */
.sana-cd-related { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
.sana-cd-related-item {
    display: flex; gap: 12px; padding: 10px; border-radius: 14px;
    border: 1px solid #EDE9FE; text-decoration: none !important; color: inherit;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.sana-cd-related-item:hover { border-color: var(--p-light); box-shadow: 0 8px 24px -8px rgba(91,33,182,0.15); }
.sana-cd-related-item img { width: 64px; height: 64px; border-radius: 12px; object-fit: cover; }

.sana-cd-sidebar-wrap { display: none; }
@media (min-width: 992px) { .sana-cd-sidebar-wrap { display: block; } }

/* Mobile sticky bar */
.sana-cd-mobile-bar {
    display: none;
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 900;
    padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0));
    background: rgba(255,255,255,0.97); backdrop-filter: blur(12px);
    border-top: 1px solid #EDE9FE;
    box-shadow: 0 -8px 32px rgba(91,33,182,0.12);
}
@media (max-width: 991px) { .sana-cd-mobile-bar { display: block; } }
.sana-cd-mobile-bar__inner {
    display: flex; align-items: center; justify-content: space-between; gap: 12px; max-width: 600px; margin-inline: auto;
}
.sana-cd-mobile-bar__price { font-weight: 900; color: var(--p-dark); font-size: 1.05rem; }
.sana-cd-mobile-bar .sana-course-cta {
    padding: 12px 20px; font-size: 0.85rem; min-height: 44px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--p-dark), var(--p)) !important;
    color: #fff !important;
    box-shadow: 0 8px 20px rgba(91,33,182,0.35);
}

/* Alerts */
.sana-cd-alert {
    padding: 14px 18px; border-radius: 16px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 12px; font-weight: 700; font-size: 0.88rem;
}
.sana-cd-alert--success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
.sana-cd-alert--error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
.sana-cd-alert--info { background: #EDE9FE; color: var(--p-dark); border: 1px solid #DDD6FE; }
</style>

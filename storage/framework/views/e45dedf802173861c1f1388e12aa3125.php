<style>
/* ═══ SANA SUB-PAGES (instructors, certificates, faq) ═══ */
.sana-sub-page { padding-top: 72px; background: var(--bg); }
@media (max-width: 991px) { .sana-sub-page { padding-top: 64px; } }

.sana-btn--purple { background: linear-gradient(135deg, var(--p-dark), var(--p-light)); color: #fff; box-shadow: 0 10px 28px rgba(91,33,182,0.35); }
.sana-btn--purple:hover { transform: translateY(-2px); }
.sana-btn--ghost-light { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.28); }
.sana-btn--ghost-light:hover { background: rgba(255,255,255,0.18); transform: translateY(-2px); }
.sana-btn--outline-purple { background: #fff; color: var(--p-dark); border: 2px solid #DDD6FE; }
.sana-btn--outline-purple:hover { background: #F5F3FF; border-color: var(--p-light); transform: translateY(-2px); }
.sana-section--soft { background: var(--bg); }
.sana-head--center { text-align: center; }
.sana-head__eyebrow { display: inline-block; font-size: 0.75rem; font-weight: 800; color: var(--p); margin-bottom: 8px; }
.sana-head__sub { color: var(--muted); font-size: 0.92rem; line-height: 1.75; max-width: 560px; margin: 8px auto 0; font-weight: 600; }

/* Hero */
.sana-sub-hero {
    position: relative; overflow: hidden;
    padding: clamp(44px, 7vw, 72px) 0 clamp(48px, 7vw, 80px);
    background:
        radial-gradient(circle at 85% 20%, rgba(251,191,36,0.12), transparent 45%),
        linear-gradient(168deg, #4C1D95 0%, #6D28D9 50%, #7C3AED 100%);
}
.sana-sub-hero__grid { display: grid; gap: 32px; align-items: center; }
@media (min-width: 992px) { .sana-sub-hero__grid { grid-template-columns: 1fr auto; gap: 40px; } }
.sana-sub-hero__content { text-align: center; position: relative; z-index: 1; }
@media (min-width: 992px) { .sana-sub-hero__content { text-align: right; } }
.sana-sub-hero__breadcrumb {
    display: flex; flex-wrap: wrap; align-items: center; gap: 8px; justify-content: center;
    font-size: 0.78rem; font-weight: 700; color: rgba(255,255,255,0.65); margin-bottom: 16px;
}
@media (min-width: 992px) { .sana-sub-hero__breadcrumb { justify-content: flex-start; } }
.sana-sub-hero__breadcrumb a { color: rgba(255,255,255,0.88); text-decoration: none !important; }
.sana-sub-hero__eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 7px 16px; border-radius: 999px; margin-bottom: 14px;
    background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.18);
    font-size: 0.78rem; font-weight: 800; color: #FDE68A;
}
.sana-sub-hero__title { font-size: clamp(1.85rem, 5vw, 2.75rem); font-weight: 900; color: #fff; line-height: 1.2; margin: 0 0 12px; }
.sana-sub-hero__title .hl { color: var(--gold); }
.sana-sub-hero__sub { color: rgba(255,255,255,0.88); line-height: 1.85; margin: 0 0 22px; font-weight: 600; font-size: 0.95rem; max-width: 560px; }
@media (min-width: 992px) { .sana-sub-hero__sub { margin-inline: 0; } }
.sana-sub-hero__actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
@media (min-width: 992px) { .sana-sub-hero__actions { justify-content: flex-start; } }
.sana-sub-hero__stats { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
@media (min-width: 992px) { .sana-sub-hero__stats { justify-content: flex-end; flex-direction: column; } }
.sana-sub-hero__stat {
    padding: 18px 22px; border-radius: 20px; text-align: center; min-width: 120px;
    background: rgba(255,255,255,0.1); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.18);
}
.sana-sub-hero__stat strong { display: block; font-size: 1.65rem; font-weight: 900; color: #fff; line-height: 1.1; }
.sana-sub-hero__stat span { font-size: 0.72rem; font-weight: 700; color: rgba(255,255,255,0.7); margin-top: 4px; display: block; }

.sana-sub-hero__illus { max-width: 200px; margin-inline: auto; opacity: 0.95; animation: sanaIllusFloat 5s ease-in-out infinite; }
@media (min-width: 992px) { .sana-sub-hero__illus { margin-inline: 0; max-width: 180px; } }

/* Search bar */
.sana-sub-toolbar {
    display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px;
}
@media (min-width: 768px) { .sana-sub-toolbar { flex-direction: row; align-items: center; justify-content: space-between; } }
.sana-sub-search {
    position: relative; width: 100%; max-width: 420px;
}
.sana-sub-search input {
    width: 100%; padding: 14px 16px 14px 44px; border-radius: 999px;
    border: 1.5px solid #EDE9FE; background: #fff;
    font-family: inherit; font-size: 0.88rem; font-weight: 600; color: var(--text);
    box-shadow: 0 8px 24px -12px rgba(91,33,182,0.12);
    outline: none; transition: border-color 0.2s, box-shadow 0.2s;
}
.sana-sub-search input:focus { border-color: var(--p-light); box-shadow: 0 0 0 4px rgba(109,40,217,0.1); }
.sana-sub-search i { position: absolute; top: 50%; transform: translateY(-50%); right: 16px; color: var(--muted); pointer-events: none; }
.sana-sub-toolbar p { font-size: 0.82rem; color: var(--muted); font-weight: 600; line-height: 1.65; margin: 0; max-width: 480px; }

/* Instructor cards */
.sana-inst-grid {
    display: grid; gap: 20px;
    grid-template-columns: repeat(2, 1fr);
}
@media (min-width: 768px) { .sana-inst-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 1200px) { .sana-inst-grid { grid-template-columns: repeat(4, 1fr); } }
.sana-inst-card {
    border-radius: 22px; overflow: hidden;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 10px 32px -14px rgba(91,33,182,0.12);
    transition: transform 0.28s, box-shadow 0.28s;
    text-decoration: none !important; color: inherit; display: flex; flex-direction: column; height: 100%;
}
.sana-inst-card:hover { transform: translateY(-6px); box-shadow: 0 22px 48px -16px rgba(91,33,182,0.22); }
.sana-inst-card__photo {
    aspect-ratio: 1; overflow: hidden; position: relative;
    background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
    display: flex; align-items: center; justify-content: center;
}
.sana-inst-card__photo img { width: 100%; height: 100%; object-fit: cover; }
.sana-inst-card__photo .av {
    width: 72px; height: 72px; border-radius: 50%;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
    color: #fff; font-size: 1.75rem; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
}
.sana-inst-card__badge {
    position: absolute; top: 10px; right: 10px;
    padding: 4px 10px; border-radius: 999px; font-size: 0.62rem; font-weight: 900;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: var(--p-dark);
    display: inline-flex; align-items: center; gap: 4px;
}
.sana-inst-card__pill {
    position: absolute; bottom: 10px; left: 10px;
    padding: 4px 10px; border-radius: 999px; font-size: 0.65rem; font-weight: 800;
    background: rgba(255,255,255,0.92); color: var(--p-dark);
}
.sana-inst-card__body { padding: 16px 18px 20px; flex: 1; display: flex; flex-direction: column; }
.sana-inst-card__body strong { font-size: 0.95rem; font-weight: 900; color: var(--text); display: block; margin-bottom: 4px; }
.sana-inst-card__headline { font-size: 0.78rem; color: var(--p); font-weight: 700; line-height: 1.5; margin-bottom: 10px; }
.sana-inst-card__skills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
.sana-inst-card__skills span {
    font-size: 0.65rem; font-weight: 800; padding: 3px 8px; border-radius: 999px;
    background: #F5F3FF; color: var(--p-dark);
}
.sana-inst-card__bio { font-size: 0.72rem; color: var(--muted); line-height: 1.6; margin-bottom: 12px; flex: 1; font-weight: 600; }
.sana-inst-card__link { font-size: 0.78rem; font-weight: 800; color: var(--p); display: inline-flex; align-items: center; gap: 6px; margin-top: auto; }

/* Empty state */
.sana-sub-empty {
    text-align: center; padding: 48px 24px; border-radius: 24px;
    background: #fff; border: 1px dashed #DDD6FE;
}
.sana-sub-empty__icon {
    width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 18px;
    background: #F5F3FF; color: var(--p); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
}

/* Certificate cards */
.sana-cert-grid { display: grid; gap: 20px; }
@media (min-width: 768px) { .sana-cert-grid { grid-template-columns: repeat(2, 1fr); max-width: 900px; margin-inline: auto; } }
.sana-cert-card {
    padding: 32px 28px; border-radius: 24px; text-align: center;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 12px 40px -16px rgba(91,33,182,0.12);
    transition: transform 0.25s;
}
.sana-cert-card:hover { transform: translateY(-4px); }
.sana-cert-card__icon {
    width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #fff;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
}
.sana-cert-card__icon--gold { background: linear-gradient(135deg, var(--gold-dark), var(--gold)); color: var(--p-dark); }
.sana-cert-card h3 { font-size: 1.1rem; font-weight: 900; margin: 0 0 10px; color: var(--text); }
.sana-cert-card > p { font-size: 0.85rem; color: var(--muted); line-height: 1.7; margin: 0 0 20px; font-weight: 600; }
.sana-cert-card ul { list-style: none; margin: 0; padding: 0; text-align: right; }
.sana-cert-card li {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 0.82rem; font-weight: 600; color: var(--text); margin-bottom: 10px; line-height: 1.55;
}
.sana-cert-card li i { color: #059669; margin-top: 3px; flex-shrink: 0; }

.sana-cert-steps { display: grid; gap: 20px; }
@media (min-width: 768px) { .sana-cert-steps { grid-template-columns: repeat(3, 1fr); max-width: 960px; margin-inline: auto; } }
.sana-cert-step {
    text-align: center; padding: 28px 20px; border-radius: 22px;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 8px 28px -12px rgba(91,33,182,0.1);
    position: relative;
}
.sana-cert-step__num {
    position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, var(--p-dark), var(--p));
    color: #fff; font-size: 0.72rem; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
}
.sana-cert-step__icon {
    width: 48px; height: 48px; margin: 8px auto 14px; border-radius: 14px;
    background: #F5F3FF; color: var(--p);
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
}
.sana-cert-step strong { display: block; font-size: 0.92rem; font-weight: 900; margin-bottom: 8px; color: var(--text); }
.sana-cert-step p { margin: 0; font-size: 0.78rem; color: var(--muted); line-height: 1.65; font-weight: 600; }

.sana-cert-verify-box {
    padding: clamp(28px, 5vw, 40px); border-radius: 28px; text-align: center;
    background: linear-gradient(145deg, #fff, #FAFAFF);
    border: 1px solid #EDE9FE;
    box-shadow: 0 16px 48px -20px rgba(91,33,182,0.15);
    max-width: 640px; margin-inline: auto;
}

/* FAQ layout */
.sana-faq-layout { display: grid; gap: 28px; }
@media (min-width: 992px) { .sana-faq-layout { grid-template-columns: 280px 1fr; gap: 36px; align-items: start; } }
.sana-faq-sidebar { position: sticky; top: 88px; }
.sana-faq-filters { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
.sana-faq-filter {
    padding: 12px 16px; border-radius: 12px; border: 1.5px solid #EDE9FE;
    background: #fff; font-family: inherit; font-size: 0.82rem; font-weight: 800;
    color: var(--muted); cursor: pointer; text-align: right; transition: all 0.2s;
}
.sana-faq-filter:hover { border-color: var(--p-light); color: var(--p); }
.sana-faq-filter.is-active {
    background: linear-gradient(135deg, var(--p-dark), var(--p));
    border-color: transparent; color: #fff;
}
.sana-faq-filters--mobile { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
@media (min-width: 992px) { .sana-faq-filters--mobile { display: none; } }
.sana-faq-filters--mobile .sana-faq-filter { width: auto; padding: 8px 14px; font-size: 0.75rem; }
@media (min-width: 992px) { .sana-faq-sidebar .sana-faq-filters { display: flex; } .sana-faq-sidebar > .sana-faq-filters:first-child { display: flex; } }
.sana-faq-sidebar .sana-faq-filters { display: none; }
@media (min-width: 992px) { .sana-faq-sidebar .sana-faq-filters { display: flex; } }

.sana-faq-side-panel {
    padding: 22px; border-radius: 20px;
    background: linear-gradient(135deg, #4C1D95, #6D28D9);
    color: #fff;
}
.sana-faq-side-panel h4 { font-size: 0.72rem; font-weight: 800; opacity: 0.7; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 0.04em; }
.sana-faq-side-panel a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0; color: rgba(255,255,255,0.9); text-decoration: none !important;
    font-size: 0.85rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sana-faq-side-panel a:last-child { border-bottom: none; }
.sana-faq-side-panel a i { color: var(--gold); width: 18px; text-align: center; }

.sana-faq-block { margin-bottom: 32px; }
.sana-faq-block__title {
    display: flex; align-items: center; gap: 10px;
    font-size: 1.1rem; font-weight: 900; color: var(--text); margin-bottom: 16px;
}
.sana-faq-block__title i { color: var(--p); }
.sana-faq-block__title::before {
    content: ''; width: 4px; height: 24px; border-radius: 999px;
    background: linear-gradient(180deg, var(--p), var(--gold));
}

/* Final CTA */
.sana-sub-final {
    padding: clamp(48px, 7vw, 72px) 0;
    background: linear-gradient(135deg, #4C1D95, #6D28D9 55%, #7C3AED);
}
.sana-sub-final__box {
    text-align: center; color: #fff; padding: clamp(32px, 5vw, 48px);
    border-radius: 28px; background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.14); backdrop-filter: blur(16px);
    max-width: 720px; margin-inline: auto;
}
.sana-sub-final__box h2 { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 900; margin: 0 0 12px; }
.sana-sub-final__box p { opacity: 0.9; line-height: 1.8; margin: 0 auto 24px; max-width: 520px; font-weight: 600; }
.sana-sub-final__actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }

/* Legal pages (privacy, terms) */
.sana-legal-intro {
    display: flex; flex-direction: column; gap: 18px; align-items: flex-start;
    padding: clamp(24px, 4vw, 32px); border-radius: 24px;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 12px 40px -16px rgba(91,33,182,0.12);
    max-width: 860px; margin-inline: auto;
}
@media (min-width: 768px) { .sana-legal-intro { flex-direction: row; align-items: center; gap: 22px; } }
.sana-legal-intro__icon {
    width: 56px; height: 56px; border-radius: 16px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; color: #fff;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
}
.sana-legal-intro p { margin: 0; font-size: 0.92rem; line-height: 1.85; color: var(--text); font-weight: 600; }

.sana-legal-grid { display: grid; gap: 16px; max-width: 960px; margin-inline: auto; }
@media (min-width: 768px) { .sana-legal-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; } }
.sana-legal-card {
    padding: 24px 22px; border-radius: 22px;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 8px 28px -12px rgba(91,33,182,0.1);
    transition: transform 0.25s, box-shadow 0.25s;
}
.sana-legal-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px -14px rgba(91,33,182,0.16); }
.sana-legal-card.is-wide { grid-column: 1 / -1; }
@media (min-width: 768px) { .sana-legal-card.is-wide { max-width: 100%; } }
.sana-legal-card__head {
    display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px;
}
.sana-legal-card__icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.95rem; color: #fff;
    background: linear-gradient(135deg, var(--p-dark), var(--p));
}
.sana-legal-card__icon--gold {
    background: linear-gradient(135deg, var(--gold-dark), var(--gold)); color: var(--p-dark);
}
.sana-legal-card h2 {
    font-size: 0.98rem; font-weight: 900; color: var(--text);
    line-height: 1.45; margin: 0; flex: 1; padding-top: 4px;
}
.sana-legal-card p {
    margin: 0; font-size: 0.84rem; line-height: 1.8; color: var(--muted); font-weight: 600;
}

.sana-legal-links {
    display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;
    margin-top: 28px;
}
.sana-legal-links a {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 999px;
    background: #F5F3FF; color: var(--p-dark);
    font-size: 0.8rem; font-weight: 800; text-decoration: none !important;
    border: 1px solid #DDD6FE; transition: background 0.2s, transform 0.2s;
}
.sana-legal-links a:hover { background: #EDE9FE; transform: translateY(-2px); }
.sana-legal-links a i { color: var(--p); }

.sana-legal-related { max-width: 960px; margin-inline: auto; }
.sana-legal-related__grid { display: grid; gap: 14px; grid-template-columns: repeat(2, 1fr); }
@media (min-width: 768px) { .sana-legal-related__grid { grid-template-columns: repeat(4, 1fr); } }
.sana-legal-related-card {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 22px 16px; border-radius: 20px;
    background: #fff; border: 1px solid #EDE9FE;
    box-shadow: 0 8px 24px -12px rgba(91,33,182,0.1);
    text-decoration: none !important; color: inherit;
    transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
}
.sana-legal-related-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px -14px rgba(91,33,182,0.18);
    border-color: #DDD6FE;
}
.sana-legal-related-card__icon {
    width: 44px; height: 44px; border-radius: 14px; margin-bottom: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; color: #fff;
    background: linear-gradient(135deg, var(--p-dark), var(--p-light));
}
.sana-legal-related-card strong {
    display: block; font-size: 0.82rem; font-weight: 900; color: var(--text);
    line-height: 1.45; margin-bottom: 8px;
}
.sana-legal-related-card span {
    font-size: 0.72rem; font-weight: 800; color: var(--p);
    display: inline-flex; align-items: center; gap: 6px;
}
</style>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\sana\subpages-theme.blade.php ENDPATH**/ ?>
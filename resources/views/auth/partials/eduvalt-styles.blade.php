<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    @include('landing.eduvalt.brand-vars')
    *, *::before, *::after { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; box-sizing: border-box; }
    html, body { margin: 0; padding: 0; min-height: 100%; }
    html[dir="rtl"], body { direction: rtl; text-align: right; background: #fff; color: var(--edu-navy); }

    .auth-shell {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        min-height: 100dvh;
    }
    @media (min-width: 1024px) {
        .auth-shell {
            flex-direction: row;
            align-items: stretch;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
        }
    }

    .auth-visual {
        display: none;
        position: relative;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 2.5rem 2rem;
    }
    @media (min-width: 1024px) {
        .auth-visual {
            display: flex;
            width: 50%;
            max-width: 680px;
            flex-shrink: 0;
            height: 100%;
            overflow: hidden;
        }
    }

    /* لوحة عرض داكنة — فكرة مختلفة عن البطاقات البيضاء */
    .auth-visual--showcase {
        background: var(--edu-gradient-auth);
        color: #fff;
    }
    .auth-showcase-glow {
        position: absolute; border-radius: 50%; pointer-events: none; filter: blur(80px);
    }
    .auth-showcase-glow--1 { width: 320px; height: 320px; top: -8%; inset-inline-end: -10%; background: rgba(56, 189, 248, .35); }
    .auth-showcase-glow--2 { width: 280px; height: 280px; bottom: -5%; inset-inline-start: -8%; background: rgba(167, 139, 250, .28); }
    .auth-showcase-inner {
        position: relative; z-index: 1;
        width: 100%; max-width: 460px;
        margin-inline: auto;
        display: flex; flex-direction: column;
        gap: clamp(.5rem, 1.2vh, 1rem);
        overflow: hidden;
    }
    @media (min-width: 1024px) {
        .auth-showcase-inner {
            height: 100%;
            max-height: 100%;
            justify-content: center;
        }
    }

    .auth-brand {
        display: inline-flex; align-items: center; gap: .75rem;
        text-decoration: none; color: inherit;
    }
    .auth-brand--light .auth-brand-name { color: #fff; }
    .auth-brand-logo {
        width: 3rem; height: 3rem; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: var(--edu-primary); color: #fff;
        font-weight: 800; font-size: 1.25rem; overflow: hidden;
    }
    .auth-brand-logo--glass {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        backdrop-filter: blur(8px);
    }
    .auth-brand-logo img { width: 100%; height: 100%; object-fit: contain; padding: 4px; background: #fff; }
    .auth-brand-name { font-weight: 800; font-size: 1.25rem; color: var(--edu-navy); }

    .auth-showcase-eyebrow {
        margin: 0; font-size: .8rem; font-weight: 600;
        color: rgba(255,255,255,.7); letter-spacing: .02em;
    }
    .auth-showcase-title {
        margin: 0; font-size: clamp(1.35rem, 2.2vw, 1.85rem);
        font-weight: 800; line-height: 1.35; color: #fff;
        flex-shrink: 0;
    }
    .auth-showcase-title em {
        display: block; font-style: normal;
        color: #7dd3fc; font-weight: 700; margin-top: .35rem;
    }

    @keyframes authFloat { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .auth-float { animation: authFloat 6s ease-in-out infinite; }
    .auth-float-delay { animation: authFloat 6s ease-in-out infinite -3s; }

    /* كولاج صور بدل البطاقات */
    .auth-photo-collage {
        position: relative;
        width: 100%;
        flex-shrink: 1;
        min-height: 0;
        margin: .35rem 0 0;
    }
    @media (min-width: 1024px) {
        .auth-photo-collage {
            height: min(38vh, 300px);
            max-height: min(38vh, 300px);
        }
    }
    .auth-photo {
        margin: 0;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, .45);
        border: 3px solid rgba(255, 255, 255, .2);
    }
    .auth-photo img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .auth-photo--main {
        position: relative;
        z-index: 1;
        width: 78%;
        height: 100%;
        max-height: 100%;
        margin-inline-start: auto;
        margin-inline-end: 0;
    }
    .auth-photo--main img {
        width: 100%;
        height: 100%;
        min-height: 0;
        object-fit: cover;
    }
    .auth-visual--compact .auth-photo--main {
        width: 88%;
        margin-inline: auto;
    }
    @media (min-width: 1024px) {
        .auth-visual--compact .auth-photo-collage {
            height: min(32vh, 240px);
            max-height: min(32vh, 240px);
        }
    }
    .auth-photo-caption {
        position: absolute;
        inset-inline: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: 1rem 1.1rem;
        background: linear-gradient(to top, rgba(0,0,0,.75), transparent);
        color: #fff;
        font-size: .8rem;
        font-weight: 700;
    }
    .auth-photo-caption i { color: #7dd3fc; }

    .auth-photo--sub {
        position: absolute;
        z-index: 2;
        width: 42%;
        aspect-ratio: 3 / 4;
    }
    .auth-photo--sub-a {
        top: 8%;
        inset-inline-start: 0;
    }
    .auth-photo--sub-b {
        bottom: 4%;
        inset-inline-start: 8%;
        z-index: 3;
    }
    .auth-photo-tag {
        position: absolute;
        bottom: .65rem;
        inset-inline: .65rem;
        padding: .35rem .65rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        color: var(--edu-navy);
        font-size: .68rem;
        font-weight: 800;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }

    .auth-showcase-footnote {
        margin: 0;
        font-size: .72rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, .6);
        text-align: center;
        flex-shrink: 0;
    }

    @media (min-width: 1024px) and (max-height: 820px) {
        .auth-photo--sub { display: none; }
        .auth-showcase-footnote { display: none; }
        .auth-photo-collage {
            height: min(30vh, 220px);
            max-height: min(30vh, 220px);
        }
    }

    .auth-form-panel {
        flex: 1;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 2.5rem 1.25rem;
        background: #fff;
        overflow-y: auto;
        overflow-x: hidden;
        min-height: 100vh;
        min-height: 100dvh;
    }
    @media (min-width: 1024px) {
        .auth-form-panel {
            height: 100%;
            min-height: 0;
        }
    }
    .auth-shell--register .auth-form-panel {
        justify-content: flex-start;
        padding-top: 2rem;
        padding-bottom: 2.5rem;
    }
    @media (min-width: 640px) { .auth-form-panel { padding: 2.5rem 2.5rem; } }

    .auth-form-inner { width: 100%; max-width: 400px; }
    .auth-form-inner--wide { max-width: 520px; }

    .auth-mobile-brand { margin-bottom: 2rem; }
    @media (min-width: 1024px) { .auth-mobile-brand { display: none; } }

    .auth-form-title { font-size: 1.75rem; font-weight: 800; margin: 0 0 .5rem; color: var(--edu-navy); }
    .auth-form-subtitle { color: var(--edu-muted); font-size: .95rem; margin: 0; }
    @media (min-width: 1024px) {
        .auth-form-head { text-align: start; }
    }
    .auth-form-head { text-align: center; margin-bottom: 1.5rem; }

    .auth-form { display: flex; flex-direction: column; gap: 1.25rem; }

    .auth-label { display: block; font-size: .875rem; font-weight: 700; color: var(--edu-navy); margin-bottom: .5rem; }

    .auth-field { position: relative; }

    .auth-field-icon {
        position: absolute; top: 50%; transform: translateY(-50%);
        inset-inline-start: 1rem; color: #94a3b8; pointer-events: none;
    }

    .auth-input {
        width: 100%; background: #f8fafc; border: 1.5px solid #e2e8f0;
        border-radius: 14px; padding: .85rem 1rem; font-size: .95rem; font-weight: 500;
        color: var(--edu-navy); transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .auth-input--icon { padding-inline-start: 2.75rem; }
    .auth-input--toggle { padding-inline-end: 2.75rem; }
    .auth-input:hover { border-color: #cbd5e1; background: #f1f5f9; }
    .auth-input:focus {
        outline: none; border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(19, 99, 223, .12); background: #fff;
    }
    .auth-input::placeholder { color: #94a3b8; }
    .auth-input.is-error { border-color: #ef4444; }

    .auth-toggle {
        position: absolute; top: 50%; transform: translateY(-50%);
        inset-inline-end: 1rem; border: none; background: none;
        color: #94a3b8; cursor: pointer; padding: .25rem;
    }
    .auth-toggle:hover { color: var(--edu-primary); }

    .auth-error { margin: .35rem 0 0; font-size: .75rem; color: #ef4444; font-weight: 600; }

    .auth-row {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: .75rem;
    }
    .auth-remember {
        display: flex; align-items: center; gap: .5rem; cursor: pointer;
        font-size: .875rem; color: var(--edu-muted);
    }
    .auth-remember input { width: 1rem; height: 1rem; accent-color: var(--edu-primary); }

    .auth-btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%; padding: .9rem 1.5rem; border-radius: 999px; font-weight: 700; font-size: 1rem;
        color: #fff; background: var(--edu-primary); border: none; cursor: pointer;
        box-shadow: 0 8px 24px -8px rgba(19, 99, 223, .5);
        transition: transform .2s, background .2s;
    }
    .auth-btn-primary:hover { background: var(--edu-primary-dark); transform: translateY(-2px); }

    .auth-link { color: var(--edu-primary); font-weight: 600; text-decoration: none; font-size: .875rem; }
    .auth-link:hover { color: var(--edu-primary-dark); }

    .auth-alert-ok, .auth-alert-warn {
        display: flex; align-items: center; gap: .75rem; padding: 1rem;
        border-radius: var(--edu-radius-sm); font-size: .875rem; font-weight: 600;
    }
    .auth-alert-ok { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }
    .auth-alert-warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

    .auth-divider {
        margin-top: 2rem; padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
        text-align: center; font-size: .875rem; color: var(--edu-muted);
    }
    @media (min-width: 1024px) { .auth-divider { text-align: start; } }

    .auth-back {
        display: inline-flex; align-items: center; gap: .5rem;
        margin-top: 1.5rem; font-size: .875rem; color: #94a3b8;
        text-decoration: none; transition: color .2s;
    }
    .auth-back:hover { color: var(--edu-muted); }
    @media (min-width: 1024px) { .auth-back-wrap { text-align: start; } }
    .auth-back-wrap { text-align: center; }

    .auth-form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    @media (min-width: 640px) {
        .auth-form-grid { grid-template-columns: 1fr 1fr; }
        .auth-form-grid .auth-span-2 { grid-column: 1 / -1; }
        .auth-form-grid .auth-field-full { grid-column: 1 / -1; }
    }

    .auth-phone-row {
        display: flex;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .auth-phone-row:hover { border-color: #cbd5e1; background: #f1f5f9; }
    .auth-phone-row:focus-within {
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12);
        background: #fff;
    }
    .auth-phone-row.is-error { border-color: #ef4444; }
    .auth-phone-row select,
    .auth-phone-row input {
        border: none;
        background: transparent;
        outline: none;
        font-size: .95rem;
        font-weight: 500;
        color: var(--edu-navy);
        padding: .85rem .75rem;
    }
    .auth-phone-row select {
        flex-shrink: 0;
        min-width: 6.5rem;
        max-width: 38%;
        border-inline-end: 1.5px solid #e2e8f0;
        cursor: pointer;
        font-size: .8rem;
    }
    .auth-phone-row input { flex: 1; min-width: 0; }

    .auth-notices {
        display: flex;
        flex-direction: column;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }

    .auth-notice {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: 1rem;
        border-radius: var(--edu-radius-sm);
        font-size: .8rem;
        font-weight: 600;
        line-height: 1.6;
    }
    .auth-notice--info {
        background: var(--edu-primary-light);
        border: 1px solid rgba(var(--edu-primary-rgb), .15);
        color: var(--edu-navy);
    }
    .auth-notice--referral {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .auth-terms {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        font-size: .875rem;
        color: var(--edu-muted);
        line-height: 1.6;
    }
    .auth-terms input {
        width: 1rem;
        height: 1rem;
        margin-top: .2rem;
        accent-color: var(--edu-primary);
        flex-shrink: 0;
    }

</style>

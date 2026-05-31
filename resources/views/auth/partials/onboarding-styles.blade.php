<style>
    /* ── Onboarding shell ── */
    .ob-shell {
        min-height: 100vh;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        background: linear-gradient(160deg, #f0f4ff 0%, #faf5ff 35%, #f8fafc 70%, #eef2ff 100%);
        position: relative;
        overflow: hidden;
    }
    .ob-bg-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        pointer-events: none;
        animation: obOrbFloat 12s ease-in-out infinite;
    }
    .ob-bg-orb--1 { width: 420px; height: 420px; top: -120px; inset-inline-end: -100px; background: rgba(var(--edu-primary-rgb), .18); }
    .ob-bg-orb--2 { width: 320px; height: 320px; bottom: -80px; inset-inline-start: -60px; background: rgba(var(--edu-purple-rgb), .15); animation-delay: -4s; }
    .ob-bg-orb--3 { width: 200px; height: 200px; top: 40%; inset-inline-start: 50%; background: rgba(var(--edu-accent-rgb), .12); animation-delay: -8s; }

    @keyframes obOrbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(-20px, 15px) scale(1.05); }
        66% { transform: translate(15px, -10px) scale(.95); }
    }

    .ob-header {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        gap: 1rem;
    }
    @media (min-width: 640px) { .ob-header { padding: 1.25rem 2rem; } }

    .ob-back-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .85rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.6);
        background: rgba(255,255,255,.55);
        backdrop-filter: blur(12px);
        color: var(--edu-muted);
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .ob-back-btn:hover { background: rgba(255,255,255,.85); color: var(--edu-navy); }

    .ob-progress-wrap {
        flex: 1;
        max-width: 280px;
        margin-inline: auto;
    }
    .ob-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: .7rem;
        font-weight: 700;
        color: var(--edu-muted);
        margin-bottom: .4rem;
    }
    .ob-progress-track {
        height: 6px;
        border-radius: 999px;
        background: rgba(255,255,255,.6);
        overflow: hidden;
        backdrop-filter: blur(4px);
    }
    .ob-progress-fill {
        height: 100%;
        border-radius: 999px;
        background: var(--edu-gradient-cta);
        transition: width .5s cubic-bezier(.4, 0, .2, 1);
    }

    .ob-main {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 1.25rem 2rem;
        position: relative;
        z-index: 5;
    }
    @media (min-width: 640px) { .ob-main { padding: 1.5rem 2rem 3rem; } }

    .ob-card {
        width: 100%;
        max-width: 520px;
        background: rgba(255,255,255,.72);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,.8);
        border-radius: 28px;
        box-shadow: 0 24px 64px -16px rgba(var(--edu-primary-rgb), .15), 0 0 0 1px rgba(255,255,255,.5) inset;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        position: relative;
    }
    .ob-card--welcome { max-width: 580px; text-align: center; }

    /* ── Step transitions ── */
    .ob-step { animation: obStepIn .45s cubic-bezier(.4, 0, .2, 1) both; }
    @keyframes obStepIn {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .ob-step-leave { animation: obStepOut .3s ease both; }
    @keyframes obStepOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-12px); }
    }

    /* ── Welcome ── */
    .ob-hero-illus {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto 1.5rem;
    }
    .ob-hero-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px dashed rgba(var(--edu-primary-rgb), .25);
        animation: obSpin 20s linear infinite;
    }
    @keyframes obSpin { to { transform: rotate(360deg); } }
    .ob-hero-core {
        position: absolute;
        inset: 20px;
        border-radius: 50%;
        background: var(--edu-gradient-cta);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 16px 40px -8px rgba(var(--edu-primary-rgb), .45);
        animation: obPulse 3s ease-in-out infinite;
    }
    @keyframes obPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 16px 40px -8px rgba(var(--edu-primary-rgb), .45); }
        50% { transform: scale(1.04); box-shadow: 0 20px 48px -8px rgba(var(--edu-primary-rgb), .55); }
    }
    .ob-hero-core i { font-size: 3rem; color: #fff; }
    .ob-float-icon {
        position: absolute;
        width: 44px; height: 44px;
        border-radius: 14px;
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 8px 20px -6px rgba(0,0,0,.12);
        animation: obFloatIcon 4s ease-in-out infinite;
    }
    .ob-float-icon--1 { top: 0; inset-inline-end: 10px; color: var(--edu-primary); animation-delay: 0s; }
    .ob-float-icon--2 { bottom: 10px; inset-inline-start: 0; color: var(--edu-purple); animation-delay: -1.5s; }
    .ob-float-icon--3 { top: 50%; inset-inline-start: -10px; color: var(--edu-accent-dark); animation-delay: -3s; }
    @keyframes obFloatIcon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .ob-eyebrow {
        display: inline-block;
        padding: .35rem .85rem;
        border-radius: 999px;
        background: rgba(var(--edu-primary-rgb), .1);
        color: var(--edu-primary);
        font-size: .75rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .ob-title {
        font-size: clamp(1.65rem, 4vw, 2.15rem);
        font-weight: 800;
        line-height: 1.35;
        color: var(--edu-navy);
        margin: 0 0 .75rem;
    }
    .ob-title em { font-style: normal; color: var(--edu-primary); }
    .ob-subtitle {
        font-size: 1rem;
        color: var(--edu-muted);
        line-height: 1.7;
        margin: 0 0 1.75rem;
    }
    .ob-step-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--edu-navy);
        margin: 0 0 .35rem;
    }
    .ob-step-sub {
        font-size: .9rem;
        color: var(--edu-muted);
        line-height: 1.65;
        margin: 0 0 1.5rem;
    }
    .ob-greeting {
        font-size: .85rem;
        color: var(--edu-primary);
        font-weight: 700;
        margin-bottom: .25rem;
        min-height: 1.25rem;
        transition: opacity .3s;
    }

    /* ── Inputs ── */
    .ob-input-wrap { position: relative; margin-bottom: .5rem; }
    .ob-input {
        width: 100%;
        padding: 1rem 1.15rem;
        padding-inline-start: 2.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 1.05rem;
        font-weight: 500;
        color: var(--edu-navy);
        background: rgba(255,255,255,.8);
        transition: all .2s;
    }
    .ob-input:focus {
        outline: none;
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 4px rgba(var(--edu-primary-rgb), .12);
        background: #fff;
    }
    .ob-input.is-valid { border-color: #10b981; }
    .ob-input.is-error { border-color: #ef4444; }
    .ob-input-icon {
        position: absolute;
        top: 50%; transform: translateY(-50%);
        inset-inline-start: 1rem;
        color: #94a3b8;
        pointer-events: none;
    }
    .ob-input-hint {
        font-size: .75rem;
        color: var(--edu-muted);
        margin-top: .5rem;
        line-height: 1.5;
    }
    .ob-input-hint--ok { color: #059669; }
    .ob-input-hint--err { color: #ef4444; font-weight: 600; }

    .ob-phone-row {
        display: flex;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: rgba(255,255,255,.8);
        overflow: hidden;
        transition: all .2s;
    }
    .ob-phone-row:focus-within {
        border-color: var(--edu-primary);
        box-shadow: 0 0 0 4px rgba(var(--edu-primary-rgb), .12);
        background: #fff;
    }
    .ob-phone-row.is-valid { border-color: #10b981; }
    .ob-phone-row.is-error { border-color: #ef4444; }
    .ob-phone-row select,
    .ob-phone-row input {
        border: none; background: transparent; outline: none;
        font-size: .95rem; font-weight: 500; color: var(--edu-navy);
        padding: 1rem .75rem;
    }
    .ob-phone-row select {
        flex-shrink: 0; min-width: 7rem; max-width: 42%;
        border-inline-end: 2px solid #e2e8f0;
        cursor: pointer; font-size: .82rem;
    }
    .ob-phone-row input { flex: 1; min-width: 0; }

    /* ── Option cards ── */
    .ob-options { display: grid; gap: .75rem; }
    .ob-options--2 { grid-template-columns: 1fr 1fr; }
    @media (max-width: 480px) { .ob-options--2 { grid-template-columns: 1fr; } }
    .ob-option {
        display: flex;
        align-items: flex-start;
        gap: .85rem;
        padding: 1rem 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: rgba(255,255,255,.6);
        cursor: pointer;
        transition: all .25s cubic-bezier(.4, 0, .2, 1);
        text-align: start;
    }
    .ob-option:hover {
        border-color: rgba(var(--edu-primary-rgb), .35);
        background: rgba(255,255,255,.9);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -8px rgba(var(--edu-primary-rgb), .2);
    }
    .ob-option.is-selected {
        border-color: var(--edu-primary);
        background: rgba(var(--edu-primary-rgb), .06);
        box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .1);
    }
    .ob-option-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
        background: rgba(var(--edu-primary-rgb), .1);
        color: var(--edu-primary);
        transition: all .25s;
    }
    .ob-option.is-selected .ob-option-icon {
        background: var(--edu-primary);
        color: #fff;
    }
    .ob-option-text strong {
        display: block;
        font-size: .9rem;
        font-weight: 700;
        color: var(--edu-navy);
        margin-bottom: .15rem;
    }
    .ob-option-text span {
        font-size: .75rem;
        color: var(--edu-muted);
        line-height: 1.4;
    }

    /* ── Summary ── */
    .ob-summary-list { display: flex; flex-direction: column; gap: .65rem; margin-bottom: 1.5rem; }
    .ob-summary-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem 1rem;
        border-radius: 14px;
        background: rgba(var(--edu-primary-rgb), .05);
        border: 1px solid rgba(var(--edu-primary-rgb), .1);
    }
    .ob-summary-item i { color: var(--edu-primary); width: 1.25rem; text-align: center; }
    .ob-summary-item div { flex: 1; }
    .ob-summary-item small { display: block; font-size: .68rem; color: var(--edu-muted); font-weight: 600; }
    .ob-summary-item strong { font-size: .85rem; color: var(--edu-navy); }

    .ob-personalize-box {
        padding: 1rem 1.15rem;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(var(--edu-primary-rgb), .08), rgba(var(--edu-purple-rgb), .08));
        border: 1px solid rgba(var(--edu-primary-rgb), .12);
        font-size: .85rem;
        line-height: 1.65;
        color: var(--edu-text);
        margin-bottom: 1.5rem;
    }
    .ob-personalize-box i { color: var(--edu-primary); margin-inline-end: .35rem; }

    /* ── Password strength ── */
    .ob-pw-strength { margin-top: .75rem; }
    .ob-pw-bars {
        display: flex; gap: 4px; margin-bottom: .5rem;
    }
    .ob-pw-bar {
        flex: 1; height: 4px; border-radius: 999px;
        background: #e2e8f0;
        transition: background .3s;
    }
    .ob-pw-bar.is-on { background: var(--edu-primary); }
    .ob-pw-bar.is-on--weak { background: #f59e0b; }
    .ob-pw-bar.is-on--medium { background: #3b82f6; }
    .ob-pw-bar.is-on--strong { background: #10b981; }
    .ob-pw-label { font-size: .72rem; font-weight: 700; color: var(--edu-muted); }
    .ob-pw-checks { display: flex; flex-wrap: wrap; gap: .5rem .75rem; margin-top: .65rem; }
    .ob-pw-check {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .7rem; font-weight: 600; color: #94a3b8;
        transition: color .2s;
    }
    .ob-pw-check.is-ok { color: #059669; }
    .ob-pw-check i { font-size: .65rem; }

    .ob-pw-toggle {
        position: absolute; top: 50%; transform: translateY(-50%);
        inset-inline-end: 1rem; border: none; background: none;
        color: #94a3b8; cursor: pointer; padding: .25rem;
    }

    /* ── Terms ── */
    .ob-terms-box {
        padding: 1.15rem;
        border-radius: 16px;
        background: rgba(248,250,252,.8);
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
        max-height: 160px;
        overflow-y: auto;
        font-size: .78rem;
        line-height: 1.7;
        color: var(--edu-muted);
    }
    .ob-terms-check {
        display: flex; align-items: flex-start; gap: .75rem;
        padding: 1rem; border-radius: 14px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all .2s;
        font-size: .85rem; color: var(--edu-text); line-height: 1.6;
    }
    .ob-terms-check.is-checked {
        border-color: var(--edu-primary);
        background: rgba(var(--edu-primary-rgb), .04);
    }
    .ob-terms-check input { width: 1.1rem; height: 1.1rem; margin-top: .15rem; accent-color: var(--edu-primary); flex-shrink: 0; }

    /* ── Buttons ── */
    .ob-actions { display: flex; flex-direction: column; gap: .75rem; margin-top: 1.5rem; }
    .ob-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%; padding: 1rem 1.5rem;
        border-radius: 999px; font-weight: 700; font-size: 1rem;
        border: none; cursor: pointer;
        transition: all .25s cubic-bezier(.4, 0, .2, 1);
    }
    .ob-btn--primary {
        color: #fff;
        background: var(--edu-gradient-cta);
        box-shadow: 0 12px 32px -8px rgba(var(--edu-primary-rgb), .45);
    }
    .ob-btn--primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 16px 40px -8px rgba(var(--edu-primary-rgb), .55); }
    .ob-btn--primary:disabled { opacity: .5; cursor: not-allowed; transform: none; }
    .ob-btn--ghost {
        background: transparent;
        color: var(--edu-muted);
        border: 1.5px solid #e2e8f0;
    }
    .ob-btn--ghost:hover { background: rgba(255,255,255,.6); color: var(--edu-navy); }

    .ob-sub-progress {
        display: flex; gap: 6px; justify-content: center; margin-bottom: 1.25rem;
    }
    .ob-sub-dot {
        width: 8px; height: 8px; border-radius: 999px;
        background: #cbd5e1;
        transition: all .3s;
    }
    .ob-sub-dot.is-active { width: 24px; background: var(--edu-primary); }
    .ob-sub-dot.is-done { background: #10b981; }

    .ob-privacy-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .5rem .85rem;
        border-radius: 999px;
        background: rgba(16,185,129,.08);
        border: 1px solid rgba(16,185,129,.2);
        color: #047857;
        font-size: .72rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .ob-server-errors {
        padding: .85rem 1rem;
        border-radius: 12px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: .8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .ob-footer-link {
        text-align: center;
        margin-top: 1.25rem;
        font-size: .85rem;
        color: var(--edu-muted);
    }
    .ob-footer-link a { color: var(--edu-primary); font-weight: 700; text-decoration: none; }

    [x-cloak] { display: none !important; }
</style>

@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
    $brand = config('app.name');
    $resumeStep = 1;
    if ($errors->any()) {
        $resumeStep = \App\Services\TutorApplicationFormService::resumeStepFromErrors($errors);
    }
    $heroMain = public_static_url('images/saudi.png');
    $heroCircle = public_static_url('images/circle-1.png');
    $heroStudents = public_static_url('images/hero-students.png');
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('tutor.apply_title') }} — {{ $brand }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('landing.eduvalt.theme')
    @include('tutor.partials.interactive-ui')
    @include('tutor.partials.home-hero-styles')
    <style>
        .ta-page { min-height: 100vh; min-height: 100dvh; background: #fff; }
        .ta-nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(255,255,255,.92); backdrop-filter: blur(10px);
            border-bottom: 1px solid #f1f5f9;
        }
        .ta-layout { display: flex; flex-direction: column; min-height: calc(100vh - 4rem); }
        @media (min-width: 1024px) {
            .ta-layout { flex-direction: row; align-items: stretch; }
            .ta-visual { width: 46%; position: sticky; top: 4rem; align-self: flex-start; min-height: calc(100vh - 4rem); }
            .ta-form-col { width: 54%; }
        }
        .ta-visual-inner {
            position: relative; max-width: 520px; margin: 0 auto;
            padding: 2.5rem 1.5rem 3rem;
        }
        @media (min-width: 1024px) { .ta-visual-inner { padding: 3.5rem 2rem 4rem; } }

        .ta-orbit { position: relative; width: 100%; aspect-ratio: 1; max-width: 500px; margin: 0 auto; }
        .ta-orbit-ring {
            position: absolute; inset: 4%; border-radius: 50%;
            border: 2px dashed rgba(var(--edu-primary-rgb), .15);
            animation: ta-spin 40s linear infinite;
        }
        @keyframes ta-spin { to { transform: rotate(360deg); } }

        .ta-main-photo {
            position: absolute; inset: 5%;
            border-radius: 50%; overflow: hidden;
            border: 6px solid #fff;
            box-shadow: 0 24px 60px -20px rgba(15,23,42,.35);
            z-index: 3;
        }
        .ta-main-photo img {
            width: 100%; height: 100%; object-fit: cover;
            object-position: 50% 14%;
            transform: scale(1.28);
        }

        .ta-sub-photo {
            position: absolute; width: 28%; aspect-ratio: 1;
            border-radius: 50%; overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 12px 32px -12px rgba(15,23,42,.3);
            z-index: 4;
        }
        .ta-sub-photo { background: #fff; }
        .ta-sub-photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 18%; }
        .ta-sub-photo--a { bottom: 2%; inset-inline-start: -1%; }
        .ta-sub-photo--b { bottom: 5%; inset-inline-end: -3%; }

        .ta-deco {
            position: absolute; pointer-events: none; z-index: 2;
        }
        .ta-deco-plane {
            top: 6%; inset-inline-start: 4%;
            width: 3.5rem; height: 3.5rem; color: var(--edu-accent);
            animation: ta-float 5s ease-in-out infinite;
        }
        .ta-deco-plane--2 {
            top: 18%; inset-inline-end: 6%; width: 2.5rem; height: 2.5rem;
            color: var(--edu-primary); animation-delay: 1.2s;
        }
        @keyframes ta-float {
            0%, 100% { transform: translateY(0) rotate(-8deg); }
            50% { transform: translateY(-10px) rotate(4deg); }
        }
        .ta-dot { width: 10px; height: 10px; border-radius: 50%; position: absolute; }
        .ta-icon-badge {
            width: 2.75rem; height: 2.75rem; border-radius: .85rem;
            display: flex; align-items: center; justify-content: center;
            background: #fff; box-shadow: 0 8px 24px -8px rgba(15,23,42,.2);
            color: var(--edu-primary); font-size: 1rem;
        }
        .ta-trust {
            display: inline-flex; align-items: center; gap: .6rem;
            margin-top: 1.5rem; padding: .65rem 1rem;
            border-radius: 999px; background: var(--edu-accent-light);
            color: var(--edu-accent-dark); font-size: .8rem; font-weight: 700;
        }

        .ta-form-col { padding: 1.5rem 1.25rem 3rem; }
        @media (min-width: 1024px) { .ta-form-col { padding: 3rem 3rem 4rem; } }

        .ta-headline {
            font-size: clamp(1.75rem, 4vw, 2.65rem);
            font-weight: 800; line-height: 1.35; color: #0f172a;
        }
        .ta-lead { color: #64748b; line-height: 1.85; font-size: 1rem; }

        .ta-progress { display: flex; gap: .35rem; margin: 1.25rem 0 1.75rem; }
        .ta-progress span {
            flex: 1; height: 4px; border-radius: 99px; background: #e2e8f0;
            transition: background .3s;
        }
        .ta-progress span.is-done { background: var(--edu-primary); }
        .ta-progress span.is-current { background: linear-gradient(90deg, var(--edu-primary), var(--edu-purple)); }

        .ta-field {
            width: 100%; padding: .85rem 1rem; border-radius: 1rem;
            border: 1px solid #e2e8f0; background: #f8fafc;
            font-size: .95rem; transition: border-color .2s, box-shadow .2s;
        }
        .ta-field:focus {
            outline: none; border-color: var(--edu-primary);
            box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12);
            background: #fff;
        }
        .ta-label { display: block; font-size: .8rem; font-weight: 700; color: #475569; margin-bottom: .4rem; }
        .ta-phone { display: flex; gap: .5rem; }
        .ta-phone select { width: 7.5rem; flex-shrink: 0; }
        .ta-textarea { min-height: 7rem; resize: vertical; }

        .ta-check-grid {
            display: grid; gap: .5rem; max-height: 11rem;
            overflow-y: auto; padding: .15rem;
        }
        .ta-check-item {
            display: flex; align-items: center; gap: .55rem;
            padding: .6rem .85rem; border-radius: .85rem;
            border: 1px solid #e2e8f0; cursor: pointer; font-size: .88rem;
        }
        .ta-check-item:has(input:checked) {
            border-color: var(--edu-primary);
            background: rgba(var(--edu-primary-rgb), .06);
        }
        .ta-check-item input { accent-color: var(--edu-primary); }

        .ta-btn-accent {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            padding: .9rem 2rem; border-radius: 999px; font-weight: 800; font-size: .95rem;
            color: #fff; background: var(--edu-accent);
            box-shadow: 0 10px 28px -10px rgba(var(--edu-accent-rgb), .55);
            transition: transform .2s, background .2s;
        }
        .ta-btn-accent:hover { background: var(--edu-accent-dark); transform: translateY(-2px); }
        .ta-btn-accent:disabled { opacity: .55; cursor: not-allowed; transform: none; }

        .ta-btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            padding: .85rem 1.75rem; border-radius: 999px; font-weight: 700;
            color: #fff; background: var(--edu-primary);
            transition: background .2s, transform .2s;
        }
        .ta-btn-primary:hover { background: var(--edu-primary-dark); transform: translateY(-1px); }
        .ta-btn-primary:disabled { opacity: .55; cursor: not-allowed; transform: none; }

        .ta-btn-ghost {
            padding: .75rem 1.25rem; border-radius: 999px; font-weight: 700;
            color: #64748b; border: 1px solid #e2e8f0; background: #fff;
        }
        .ta-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.5rem; align-items: center; }
        .ta-alert-err {
            padding: .85rem 1rem; border-radius: 1rem; margin-bottom: 1rem;
            background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: .88rem;
        }
        .ta-step-tag {
            display: inline-block; font-size: .75rem; font-weight: 800;
            letter-spacing: .06em; color: var(--edu-primary);
            background: var(--edu-primary-light); padding: .35rem .85rem;
            border-radius: 999px; margin-bottom: .85rem;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="ta-page" x-data="tutorApplyWizard()" x-init="init()">

<header class="ta-nav">
    <div class="edu-container flex items-center justify-between gap-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-slate-900 no-underline">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="w-9 h-9 rounded-xl object-contain">
            @endif
            <span>{{ $brand }}</span>
        </a>
        <div class="flex items-center gap-3">
            <template x-if="step > 1 && step <= 11">
                <button type="button" class="ta-btn-ghost text-sm" @click="prev()">
                    <i class="fas fa-arrow-right text-xs"></i> السابق
                </button>
            </template>
            <a href="{{ route('staff.login') }}" class="text-sm font-bold text-[var(--edu-primary)] no-underline hover:underline">دخول المدربين</a>
        </div>
    </div>
</header>

<div class="ta-layout">
    {{-- العمود البصري (يسار في RTL) --}}
    <aside class="ta-visual relative overflow-hidden bg-[var(--edu-primary-light)]/40">
        <div class="th-hero-ambient" aria-hidden="true">
            <div class="th-hero-ambient__blob th-hero-ambient__blob--1"></div>
            <div class="th-hero-ambient__blob th-hero-ambient__blob--2"></div>
        </div>
        <div class="ta-visual-inner relative z-10">
            <div class="ta-orbit">
                <div class="ta-orbit-ring" aria-hidden="true"></div>

                <svg class="ta-deco ta-deco-plane" viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                    <path d="M62 8L38 28l-8-4-18 22 4-18-4-8 20-12z" opacity=".9"/>
                </svg>
                <svg class="ta-deco ta-deco-plane ta-deco-plane--2" viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
                    <path d="M62 8L38 28l-8-4-18 22 4-18-4-8 20-12z" opacity=".7"/>
                </svg>

                <span class="ta-dot ta-deco" style="top:32%;inset-inline-start:12%;background:var(--edu-primary)"></span>
                <span class="ta-dot ta-deco" style="top:42%;inset-inline-end:14%;background:var(--edu-purple)"></span>
                <span class="ta-dot ta-deco" style="bottom:28%;inset-inline-start:18%;background:var(--edu-accent);width:8px;height:8px"></span>

                <div class="ta-deco ta-icon-badge" style="top:8%;inset-inline-end:10%">
                    <i class="fas fa-trophy" style="color:var(--edu-accent)"></i>
                </div>
                <div class="ta-deco ta-icon-badge" style="bottom:22%;inset-inline-start:6%;font-size:.85rem">
                    <i class="fas fa-headphones"></i>
                </div>

                <div class="ta-main-photo">
                    <img src="{{ $heroMain }}" alt="معلم سعودي — {{ $brand }}" loading="eager">
                </div>
                <div class="ta-sub-photo ta-sub-photo--a">
                    <img src="{{ $heroCircle }}" alt="معلّم أونلاين" loading="lazy">
                </div>
                <div class="ta-sub-photo ta-sub-photo--b">
                    <img src="{{ $heroStudents }}" alt="طلاب يتعلمون" loading="lazy">
                </div>
            </div>

            <div class="ta-trust">
                <i class="fas fa-award"></i>
                <span>منصة سعودية — سهلة على الطالب والمعلّم</span>
            </div>
            <div class="ix-tip-card" x-show="stepTip.title" x-cloak>
                <strong x-text="stepTip.title"></strong>
                <span x-text="stepTip.text"></span>
            </div>
        </div>
    </aside>

    {{-- عمود النموذج --}}
    <main class="ta-form-col">
        @if($errors->any())
        <div class="ta-alert-err">
            @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
        </div>
        @endif

        <form action="{{ route('tutor.apply.store') }}" method="POST" enctype="multipart/form-data" @submit="onSubmit" id="tutorApplyForm">
            @csrf

            <div x-show="step > 1" x-cloak>
                <div class="ix-progress-ring">
                    <div class="ix-progress-ring__bar">
                        <div class="ix-progress-ring__fill" :style="'width:' + progressPct + '%'"></div>
                    </div>
                    <span class="ix-progress-ring__pct" x-text="progressPct + '%'"></span>
                </div>
                <span class="ta-step-tag" x-text="stepLabel"></span>
            </div>

            @include('tutor.partials.apply-steps')
        </form>
    </main>
</div>

<script>
function tutorApplyWizard() {
    const tips = {
        1: { title: 'نموذج التوظيف', text: 'املأ الأقسام بدقة — الفيديو والمرفقات جزء من التقييم.' },
        2: { title: 'بياناتك', text: 'تأكد من صحة الجوال والبريد للتواصل.' },
        8: { title: 'فيديو الشرح', text: '٣–٥ دقائق تكفي لتقييم أسلوبك.' },
        10: { title: 'الالتزام', text: 'بنود السرية والقنوات الرسمية إلزامية.' },
    };
    return {
        step: {{ $resumeStep }},
        totalSteps: 11,
        submitting: false,
        init() {
            this.$watch('step', () => this.scrollToForm());
        },
        get progressPct() {
            if (this.step <= 1) return 0;
            return Math.round(((this.step - 1) / (this.totalSteps - 1)) * 100);
        },
        get stepLabel() {
            return 'الخطوة ' + this.step + ' من ' + this.totalSteps;
        },
        get stepTip() { return tips[this.step] || { title: '', text: '' }; },
        scrollToForm() {
            if (window.innerWidth < 1024) {
                document.querySelector('.ta-form-col')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },
        next() {
            if (this.step < this.totalSteps) this.step++;
        },
        prev() { if (this.step > 1) this.step--; },
        onSubmit(e) {
            const modes = document.querySelectorAll('input[name="matching_modes[]"]:checked').length;
            if (modes < 1) {
                e.preventDefault();
                this.step = 11;
                alert('اختر نمط استقبال واحد على الأقل.');
                return;
            }
            this.submitting = true;
        }
    };
}
</script>
</body>
</html>

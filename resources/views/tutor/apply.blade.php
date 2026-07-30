@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
    $brand = config('app.name');
    $formPreview = ! empty($formPreview);
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $resumeStep = 1;
    $applyStepErrors = new \Illuminate\Support\MessageBag();
    if ($errors->any()) {
        $resumeStep = \App\Services\TutorApplicationFormService::resumeStepFromErrors($errors);
        $applyStepErrors = \App\Services\TutorApplicationFormService::errorsForStep($resumeStep, $errors);
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
    <title>{{ $formPreview ? 'معاينة — ' : '' }}{{ __('tutor.apply_title') }} — {{ $brand }}</title>
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
        .ta-field.is-invalid, select.ta-field.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .12);
        }
        .ta-check-item.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }
        .ta-step-error {
            padding: .85rem 1rem; border-radius: 1rem; margin-bottom: 1rem;
            background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: .88rem;
        }
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
            <template x-if="step > 1 && step <= totalSteps">
                <button type="button" class="ta-btn-ghost text-sm" @click="prev()">
                    <i class="fas fa-arrow-right text-xs"></i> السابق
                </button>
            </template>
            <a href="{{ route('staff.login') }}" class="text-sm font-bold text-[var(--edu-primary)] no-underline hover:underline">دخول المعلمين</a>
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
        @if($formPreview)
        <div class="mb-4 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-950 flex flex-wrap items-center justify-between gap-3">
            <div>
                <strong class="font-bold"><i class="fas fa-eye ml-1"></i> وضع المعاينة</strong>
                <span class="block text-xs mt-0.5 text-violet-800">عرض النموذج كما يراه المتقدّم — لن يُحفظ أي طلب ولن يُنشأ حساب.</span>
            </div>
            <a href="{{ route('admin.instructor-applications.index') }}" class="rounded-xl border border-violet-300 bg-white px-3 py-1.5 text-xs font-bold text-violet-800 hover:bg-violet-100">
                العودة للطلبات
            </a>
        </div>
        @endif

        @if($applyStepErrors->isNotEmpty())
        <div class="ta-alert-err">
            @if($resumeStep === 8)
                <p class="font-bold mb-2">يرجى إعادة رفع الملفات والمرفقات — المتصفح لا يحفظها تلقائياً بعد الخطأ.</p>
            @endif
            @foreach($applyStepErrors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
        </div>
        @endif

        <div x-show="stepError" x-cloak class="ta-step-error" x-text="stepError"></div>

        <div x-show="draftRestored" x-cloak class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex flex-wrap items-center justify-between gap-3">
            <div>
                <strong class="font-bold">تم استعادة تقدمك السابق</strong>
                <span class="block text-xs mt-0.5 text-emerald-800">الملفات (فيديو / مستندات) لا يمكن للمتصفح حفظها — أعد رفعها قبل الإرسال إن لزم.</span>
            </div>
            <button type="button" class="rounded-xl border border-emerald-300 bg-white px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100"
                    @click="clearDraft(true)">بدء من جديد</button>
        </div>

        <form action="{{ $formPreview ? '#' : route('tutor.apply.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="onSubmit" id="tutorApplyForm" novalidate @if($formPreview) data-preview="1" @endif>
            @unless($formPreview)
                @csrf
            @endunless

            <div x-show="step > 1" x-cloak>
                <div class="ix-progress-ring">
                    <div class="ix-progress-ring__bar">
                        <div class="ix-progress-ring__fill" :style="'width:' + progressPct + '%'"></div>
                    </div>
                    <span class="ix-progress-ring__pct" x-text="progressPct + '%'"></span>
                </div>
                <span class="ta-step-tag" x-text="stepLabel"></span>
            </div>

            @if(!empty($useDynamicForm) && $formSteps->isNotEmpty())
                @include('tutor.partials.apply-steps-dynamic')
            @else
                @include('tutor.partials.apply-steps')
            @endif
        </form>

        {{-- طبقة انتظار أثناء رفع الملفات --}}
        <div x-show="submitting" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/55 backdrop-blur-sm p-6"
             style="pointer-events: all;">
            <div class="max-w-md w-full rounded-3xl bg-white shadow-2xl p-8 text-center space-y-4">
                <div class="mx-auto w-14 h-14 rounded-full border-4 border-sky-200 border-t-sky-600 animate-spin"></div>
                <h3 class="text-lg font-black text-slate-900 m-0">جاري إرسال طلبك…</h3>
                <p class="text-sm text-slate-600 m-0 leading-relaxed">
                    يتم رفع الملفات إلى التخزين السحابي. قد يستغرق ذلك دقيقة أو أكثر حسب حجم الفيديو —
                    <strong>لا تغلق الصفحة ولا تضغط رجوع</strong>.
                </p>
            </div>
        </div>
    </main>
</div>

<script>
function tutorVideoStep(maxMb, useExternalLinkInitial) {
    return {
        maxMb: maxMb,
        maxBytes: maxMb * 1024 * 1024,
        useExternalLink: !!useExternalLinkInitial,
        fileTooLarge: false,
        onVideoFile(event) {
            var input = event.target;
            var file = input.files && input.files[0];
            if (!file) {
                this.fileTooLarge = false;
                return;
            }
            if (file.size > this.maxBytes) {
                this.fileTooLarge = true;
                this.useExternalLink = true;
                input.value = '';
            } else {
                this.fileTooLarge = false;
            }
        },
        onToggleExternal() {
            if (this.useExternalLink) {
                var input = document.querySelector('[name="demo_video"]');
                if (input) input.value = '';
                this.fileTooLarge = false;
            }
        },
    };
}

function tutorApplyWizard() {
    const DRAFT_KEY = 'sana_tutor_apply_draft_v1';
    const serverResumeStep = {{ (int) $resumeStep }};
    const hasServerErrors = {{ $applyStepErrors->isNotEmpty() ? 'true' : 'false' }};
    const tips = {
        1: { title: 'نموذج التوظيف', text: 'املأ الأقسام بدقة — الفيديو والمرفقات جزء من التقييم.' },
        2: { title: 'بياناتك', text: 'تأكد من صحة الجوال والبريد للتواصل.' },
        8: { title: 'فيديو الشرح', text: 'ارفع ملفاً حتى {{ \App\Services\TutorApplicationFormService::videoMaxMb() }} ميجا، أو استخدم رابط YouTube / Drive.' },
        10: { title: 'الالتزام', text: 'بنود السرية والقنوات الرسمية إلزامية.' },
    };
    return {
        step: serverResumeStep,
        totalSteps: {{ (int) ($totalSteps ?? 11) }},
        submitting: false,
        formPreview: {{ $formPreview ? 'true' : 'false' }},
        stepError: '',
        draftRestored: false,
        _draftTimer: null,
        init() {
            this.$watch('step', () => {
                this.stepError = '';
                this.scrollToForm();
                if (!this.formPreview) this.scheduleSaveDraft();
            });
            const form = document.getElementById('tutorApplyForm');
            if (form) {
                const clearInvalid = (e) => {
                    const t = e.target;
                    if (!t || !t.classList) return;
                    t.classList.remove('is-invalid');
                    t.closest('.ta-check-item')?.classList.remove('is-invalid');
                    if (this.stepError) this.stepError = '';
                };
                form.addEventListener('input', (e) => {
                    clearInvalid(e);
                    this.scheduleSaveDraft();
                }, true);
                form.addEventListener('change', (e) => {
                    clearInvalid(e);
                    this.scheduleSaveDraft();
                }, true);
            }

            // استعادة المسودة فقط إن لم تُرجع السيرفر أخطاء تحقق (old() أولى)
            if (!hasServerErrors) {
                this.restoreDraft();
            } else {
                // بعد خطأ سيرفر: احفظ الحالة الحالية (old) كمسودة جديدة
                this.scheduleSaveDraft();
            }
        },
        scheduleSaveDraft() {
            if (this._draftTimer) clearTimeout(this._draftTimer);
            this._draftTimer = setTimeout(() => this.saveDraft(), 350);
        },
        saveDraft() {
            try {
                const form = document.getElementById('tutorApplyForm');
                if (!form) return;
                const data = {};
                const fd = new FormData(form);
                fd.forEach((value, key) => {
                    if (key === '_token') return;
                    // FormData يتخطى الملفات الفارغة؛ نتجاهل الملفات دائماً
                    if (value instanceof File) return;
                    if (Object.prototype.hasOwnProperty.call(data, key)) {
                        if (!Array.isArray(data[key])) data[key] = [data[key]];
                        data[key].push(value);
                    } else {
                        data[key] = value;
                    }
                });

                // التقط checkboxes غير المحددة للمجموعات المسماة [] عبر DOM لتجنّب فقدان الحالة
                form.querySelectorAll('input[type="checkbox"]').forEach(el => {
                    if (!el.name || el.name === '_token') return;
                    if (el.name.endsWith('[]')) {
                        if (!Array.isArray(data[el.name])) data[el.name] = [];
                        // FormData أضاف المحددة فقط — أعد بناء القائمة من المحددات
                    }
                });
                // أعد بناء مصفوفات [] من العناصر المحددة فقط
                const arrayNames = new Set();
                form.querySelectorAll('input[type="checkbox"][name$="[]"], select[multiple]').forEach(el => {
                    if (el.name) arrayNames.add(el.name);
                });
                arrayNames.forEach(name => {
                    const values = [];
                    form.querySelectorAll('input[type="checkbox"][name="' + name.replace(/"/g, '\\"') + '"]:checked').forEach(el => {
                        values.push(el.value);
                    });
                    // fallback بدون CSS selector escaping issues
                    if (values.length === 0) {
                        Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(el => {
                            if (el.name === name && el.checked) values.push(el.value);
                        });
                    }
                    data[name] = values;
                });

                // commitments[key] checkboxes
                form.querySelectorAll('input[type="checkbox"][name^="commitments["]').forEach(el => {
                    data[el.name] = el.checked ? (el.value || '1') : '';
                });
                const decl = form.querySelector('input[type="checkbox"][name="declaration_agreed"]');
                if (decl) data['declaration_agreed'] = decl.checked ? (decl.value || '1') : '';

                const payload = {
                    step: this.step,
                    totalSteps: this.totalSteps,
                    savedAt: Date.now(),
                    fields: data,
                };
                localStorage.setItem(DRAFT_KEY, JSON.stringify(payload));
            } catch (err) {
                // تجاهل أخطاء التخزين (وضع خاص / امتلاء)
            }
        },
        restoreDraft() {
            try {
                const raw = localStorage.getItem(DRAFT_KEY);
                if (!raw) return;
                const payload = JSON.parse(raw);
                if (!payload || typeof payload !== 'object') return;

                // تجاهل مسودات أقدم من 14 يوماً
                if (payload.savedAt && (Date.now() - payload.savedAt) > 14 * 24 * 60 * 60 * 1000) {
                    localStorage.removeItem(DRAFT_KEY);
                    return;
                }

                const fields = payload.fields || {};
                const form = document.getElementById('tutorApplyForm');
                if (!form) return;

                Object.keys(fields).forEach(name => {
                    if (name === '_token') return;
                    const value = fields[name];
                    if (name.endsWith('[]') || Array.isArray(value)) {
                        const values = Array.isArray(value) ? value.map(String) : [String(value)];
                        Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(el => {
                            if (el.name === name) el.checked = values.indexOf(String(el.value)) !== -1;
                        });
                        return;
                    }

                    if (name.indexOf('commitments[') === 0 || name === 'declaration_agreed' || name === 'video_use_external_link') {
                        Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(el => {
                            if (el.name === name) {
                                el.checked = value === '1' || value === el.value || value === true || value === 'true';
                                if (el.name === 'video_use_external_link') {
                                    // Alpine x-model على الفيديو يُحدَّث عبر event
                                    el.dispatchEvent(new Event('change', { bubbles: true }));
                                }
                            }
                        });
                        // hidden video_use_external_link قد يكون input hidden مرتبط بـ Alpine
                        const hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
                        if (hidden && typeof value === 'string') hidden.value = value;
                        return;
                    }

                    const els = Array.from(form.elements).filter(el => el.name === name && el.type !== 'file');
                    if (!els.length) return;
                    els.forEach(el => {
                        if (el.type === 'radio') {
                            el.checked = String(el.value) === String(value);
                        } else if (el.type === 'checkbox') {
                            el.checked = value === '1' || value === el.value || value === true;
                        } else if (el.tagName === 'SELECT') {
                            el.value = value == null ? '' : String(value);
                        } else {
                            el.value = value == null ? '' : String(value);
                        }
                    });
                });

                // استعادة خطوة المسودة (لا تقل عن 1)
                let step = parseInt(payload.step, 10);
                if (!step || step < 1) step = 1;
                if (step > this.totalSteps) step = this.totalSteps;
                // إن كان السيرفر يطلب خطوة أخطاء أعلى أولوية
                if (serverResumeStep > 1) step = serverResumeStep;
                this.step = step;
                this.draftRestored = true;

                // مزامنة Alpine لخطوة الفيديو (useExternalLink)
                this.$nextTick(() => {
                    const wantExternal = String(fields.video_use_external_link || '') === '1';
                    document.querySelectorAll('[x-data]').forEach(el => {
                        try {
                            if (typeof Alpine === 'undefined' || !Alpine.$data) return;
                            const data = Alpine.$data(el);
                            if (data && Object.prototype.hasOwnProperty.call(data, 'useExternalLink')) {
                                data.useExternalLink = wantExternal;
                            }
                        } catch (e) {}
                    });
                });
            } catch (err) {
                // تجاهل
            }
        },
        clearDraft(resetUi) {
            try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
            this.draftRestored = false;
            if (resetUi) {
                const form = document.getElementById('tutorApplyForm');
                if (form) form.reset();
                this.step = 1;
                this.stepError = '';
            }
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
            if (!this.validateCurrentStep()) return;
            this.stepError = '';
            if (this.step < this.totalSteps) this.step++;
            this.scheduleSaveDraft();
        },
        prev() {
            this.stepError = '';
            if (this.step > 1) this.step--;
            this.scheduleSaveDraft();
        },
        clearStepErrors(panel) {
            panel.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        },
        markInvalid(el) {
            if (!el) return;
            const target = el.classList?.contains('ta-check-item') ? el : (el.closest('.ta-check-item') || el);
            target.classList.add('is-invalid');
        },
        validateCheckboxGroups(panel) {
            let valid = true;
            let firstInvalid = null;
            const names = new Set();

            panel.querySelectorAll('[data-required-group="1"]').forEach(group => {
                const firstCb = group.querySelector('input[type="checkbox"]');
                if (firstCb && firstCb.name) names.add(firstCb.name);
            });

            // النموذج القديم: أي مجموعة [] داخل لوحة فيها حقول مطلوبة نصياً عبر data-tutor-check-group
            if (names.size === 0) {
                panel.querySelectorAll('.ta-check-grid').forEach(group => {
                    const label = group.previousElementSibling;
                    const markedRequired = label && label.textContent && label.textContent.indexOf('*') !== -1;
                    if (!markedRequired) return;
                    const firstCb = group.querySelector('input[type="checkbox"][name$="[]"]');
                    if (firstCb && firstCb.name) names.add(firstCb.name);
                });
            }

            names.forEach(name => {
                const boxes = Array.from(panel.querySelectorAll('input[type="checkbox"]')).filter(el => el.name === name);
                const checked = boxes.some(el => el.checked);
                if (!checked) {
                    valid = false;
                    this.markInvalid(boxes[0] || null);
                    if (!firstInvalid) firstInvalid = boxes[0] || null;
                }
            });
            return { valid, firstInvalid };
        },
        findStepWithField(selector) {
            for (let s = 2; s <= this.totalSteps; s++) {
                const panel = document.querySelector('[data-tutor-step="' + s + '"]');
                if (panel && panel.querySelector(selector)) return s;
            }
            return null;
        },
        validateCurrentStep() {
            return this.validateStep(this.step);
        },
        validateStep(stepNum) {
            const panel = document.querySelector('[data-tutor-step="' + stepNum + '"]');
            if (!panel || stepNum === 1) return true;

            this.clearStepErrors(panel);
            let valid = true;
            let firstInvalid = null;

            panel.querySelectorAll('input, select, textarea').forEach(el => {
                if (el.type === 'hidden') return;
                if (el.type === 'checkbox' || el.type === 'radio') return;
                if (el.disabled) return;

                if (el.type === 'file') {
                    if (el.hasAttribute('required') && (!el.files || el.files.length === 0)) {
                        valid = false;
                        this.markInvalid(el);
                        if (!firstInvalid) firstInvalid = el;
                    }
                    return;
                }

                if (el.hasAttribute('required') && !String(el.value || '').trim()) {
                    valid = false;
                    this.markInvalid(el);
                    if (!firstInvalid) firstInvalid = el;
                    return;
                }

                if (String(el.value || '').trim() !== '' && typeof el.checkValidity === 'function' && !el.checkValidity()) {
                    valid = false;
                    this.markInvalid(el);
                    if (!firstInvalid) firstInvalid = el;
                }
            });

            // راديو إلزامي
            const radioNames = new Set();
            panel.querySelectorAll('input[type="radio"][required]').forEach(el => radioNames.add(el.name));
            radioNames.forEach(name => {
                if (!panel.querySelector('input[type="radio"][name="' + name + '"]:checked')) {
                    valid = false;
                    const first = panel.querySelector('input[type="radio"][name="' + name + '"]');
                    this.markInvalid(first);
                    if (!firstInvalid) firstInvalid = first;
                }
            });

            const groups = this.validateCheckboxGroups(panel);
            if (!groups.valid) {
                valid = false;
                if (!firstInvalid) firstInvalid = groups.firstInvalid;
            }

            // كلمة المرور — أي خطوة تحتوي عليها
            const pwd = panel.querySelector('[name="password"]');
            const conf = panel.querySelector('[name="password_confirmation"]');
            if (pwd && conf && !pwd.disabled) {
                if (pwd.hasAttribute('required') || String(pwd.value || '') !== '') {
                    if (pwd.value.length < 8) {
                        valid = false;
                        this.markInvalid(pwd);
                        if (!firstInvalid) firstInvalid = pwd;
                        if (stepNum === this.step) this.stepError = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.';
                    } else if (pwd.value !== conf.value) {
                        valid = false;
                        this.markInvalid(conf);
                        if (!firstInvalid) firstInvalid = conf;
                        if (stepNum === this.step) this.stepError = 'تأكيد كلمة المرور غير متطابق.';
                    }
                }
            }

            // فيديو — إن وُجد في هذه الخطوة
            var fileInput = panel.querySelector('[name="demo_video"]');
            var linkInput = panel.querySelector('[name="demo_video_link"]');
            if (fileInput || linkInput) {
                var useExternalInput = panel.querySelector('[name="video_use_external_link"]');
                var useExternal = useExternalInput && useExternalInput.value === '1';
                var maxBytes = {{ \App\Services\TutorApplicationFormService::videoMaxMb() }} * 1024 * 1024;
                var hasFile = fileInput && !fileInput.disabled && fileInput.files && fileInput.files.length > 0;
                var linkVal = linkInput ? String(linkInput.value || '').trim() : '';

                if (hasFile && fileInput.files[0].size > maxBytes) {
                    valid = false;
                    this.markInvalid(fileInput);
                    if (!firstInvalid) firstInvalid = fileInput;
                    if (stepNum === this.step) {
                        this.stepError = 'حجم الفيديو يتجاوز ' + {{ \App\Services\TutorApplicationFormService::videoMaxMb() }} + ' ميجابايت — استخدم رابطاً خارجياً.';
                    }
                }

                if (!hasFile && !linkVal) {
                    valid = false;
                    this.markInvalid(fileInput || linkInput);
                    if (!firstInvalid) firstInvalid = fileInput || linkInput;
                    if (stepNum === this.step) {
                        this.stepError = 'ارفع فيديو (حتى {{ \App\Services\TutorApplicationFormService::videoMaxMb() }} ميجا) أو أدخل رابطاً خارجياً.';
                    }
                }

                if (useExternal && !linkVal) {
                    valid = false;
                    this.markInvalid(linkInput);
                    if (!firstInvalid) firstInvalid = linkInput;
                    if (stepNum === this.step && !this.stepError) {
                        this.stepError = 'أدخل رابط الفيديو على YouTube أو Google Drive.';
                    }
                }
            }

            // التزامات + إقرار — إن وُجدت ومطلوبة
            panel.querySelectorAll('input[type="checkbox"][name^="commitments["][required]').forEach(el => {
                if (!el.checked) {
                    valid = false;
                    this.markInvalid(el);
                    if (!firstInvalid) firstInvalid = el;
                }
            });
            const decl = panel.querySelector('input[type="checkbox"][name="declaration_agreed"]');
            if (decl && decl.hasAttribute('required') && !decl.checked) {
                valid = false;
                this.markInvalid(decl);
                if (!firstInvalid) firstInvalid = decl;
            }

            // matching_modes إن وُجدت كمجموعة إلزامية أو أي اختيار مطلوب
            const matchingGroup = panel.querySelector('[data-tutor-check-group="matching_modes"][data-required-group="1"]');
            if (matchingGroup) {
                const any = panel.querySelector('input[name="matching_modes[]"]:checked');
                if (!any) {
                    valid = false;
                    const first = panel.querySelector('input[name="matching_modes[]"]');
                    this.markInvalid(first);
                    if (!firstInvalid) firstInvalid = first;
                }
            }

            if (!valid && stepNum === this.step) {
                if (!this.stepError) {
                    this.stepError = 'يرجى تعبئة جميع الحقول المطلوبة في هذه الخطوة قبل المتابعة.';
                }
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (typeof firstInvalid.focus === 'function') {
                        try { firstInvalid.focus({ preventScroll: true }); } catch (err) { firstInvalid.focus(); }
                    }
                }
            }

            return valid;
        },
        onSubmit(e) {
            if (e && typeof e.preventDefault === 'function') e.preventDefault();
            if (this.formPreview) {
                this.stepError = 'وضع المعاينة فقط — لا يمكن إرسال الطلب من هنا. استخدم «فتح النموذج» للرابط العام.';
                this.scrollToForm();
                return;
            }
            if (this.submitting) return;

            this.stepError = '';
            for (let s = 2; s <= this.totalSteps; s++) {
                if (!this.validateStep(s)) {
                    this.submitting = false;
                    this.step = s;
                    if (!this.stepError) {
                        this.stepError = 'يرجى إكمال الحقول المطلوبة في الخطوة ' + s + ' قبل إرسال الطلب.';
                    }
                    this.scrollToForm();
                    return;
                }
            }

            this.submitting = true;
            this.stepError = '';
            this.clearDraft(false);

            // إرسال أصلي بعد إطار واحد حتى تظهر طبقة التحميل
            const form = document.getElementById('tutorApplyForm');
            if (!form) {
                this.submitting = false;
                this.stepError = 'تعذّر العثور على النموذج. حدّث الصفحة وحاول مجدداً.';
                return;
            }
            requestAnimationFrame(() => {
                HTMLFormElement.prototype.submit.call(form);
            });
        }
    };
}
</script>
</body>
</html>

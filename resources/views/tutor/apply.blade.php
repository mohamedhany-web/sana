@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
    $brand = config('app.name');
    $resumeStep = 1;
    if ($errors->any()) {
        if ($errors->has('name') || $errors->has('email')) { $resumeStep = 2; }
        elseif ($errors->has('phone') || $errors->has('country_code') || $errors->has('password')) { $resumeStep = 3; }
        elseif ($errors->has('headline') || $errors->has('bio') || $errors->has('years_experience')) { $resumeStep = 4; }
        elseif ($errors->has('subject_ids') || $errors->has('academic_year_ids')) { $resumeStep = 5; }
        else { $resumeStep = 6; }
    }
    $heroMain = asset('images/saudi.png');
    $heroCircle = asset('images/circle-1.png');
    $heroStudents = asset('images/hero-students.png');
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
            <template x-if="step > 1 && step <= totalSteps">
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

        <form action="{{ route('tutor.apply.store') }}" method="POST" @submit="onSubmit" id="tutorApplyForm">
            @csrf

            {{-- الخطوة 1: Hero --}}
            <div x-show="step === 1" x-cloak class="ix-step-panel" :key="'s1'">
                <span class="edu-badge mb-4">انضمام المعلّمين ✨</span>
                <h1 class="ta-headline mb-4">
                    علّم وشارك خبرتك مع أفضل
                    @include('landing.eduvalt.partials.title-mark', ['text' => 'المعلّمين'])
                </h1>
                <p class="ta-lead mb-6 max-w-lg">
                    {{ __('tutor.apply_subtitle') }}
                </p>
                <button type="button" class="ta-btn-accent ix-cta-pulse" @click="next()">
                    ابدأ التقديم الحين
                    <i class="fas fa-arrow-left"></i>
                </button>
                <p class="text-sm text-slate-500 mt-6">
                    عندك حساب؟
                    <a href="{{ route('staff.login') }}" class="font-bold text-[var(--edu-primary)]">سجّل دخولك</a>
                </p>
            </div>

            {{-- الخطوات 2–6 --}}
            <div x-show="step > 1" x-cloak>
                <div class="ix-progress-ring">
                    <div class="ix-progress-ring__bar">
                        <div class="ix-progress-ring__fill" :style="'width:' + progressPct + '%'"></div>
                    </div>
                    <span class="ix-progress-ring__pct" x-text="progressPct + '%'"></span>
                </div>
                <span class="ta-step-tag" x-text="stepLabel"></span>
            </div>

            <div class="ix-motivation" x-show="step > 1 && step < 6" x-cloak>
                <i class="fas fa-lightbulb"></i>
                <span x-text="motivation"></span>
            </div>

            <div x-show="step === 2" x-cloak class="ix-step-panel" :key="'s2'">
                <h2 class="ta-headline" style="font-size:clamp(1.35rem,3vw,1.85rem)">وش اسمك؟ 👋</h2>
                <p class="ta-lead mb-5">اسمك وبريدك — خطوتين بس وخلصنا</p>
                <div class="space-y-4">
                    <div class="ix-field-wrap">
                        <label class="ta-label">الاسم الكامل</label>
                        <input type="text" name="name" x-model="name" class="ta-field" required autocomplete="name" value="{{ old('name') }}" @input="name = $event.target.value">
                        <i class="fas fa-circle-check ix-field-ok" x-show="name.trim().length >= 2" x-cloak></i>
                    </div>
                    <div class="ix-field-wrap">
                        <label class="ta-label">البريد الإلكتروني</label>
                        <input type="email" name="email" x-model="email" dir="ltr" class="ta-field" required autocomplete="email" value="{{ old('email') }}" @input="email = $event.target.value">
                        <i class="fas fa-circle-check ix-field-ok" x-show="emailValid" x-cloak></i>
                    </div>
                </div>
                <div class="ta-actions">
                    <button type="button" class="ta-btn-primary ix-cta-pulse" @click="next()" :disabled="!step2Valid">
                        <span x-text="step2Valid ? 'التالي ←' : 'كمل الحقول'"></span>
                    </button>
                </div>
            </div>

            <div x-show="step === 3" x-cloak class="ix-step-panel" :key="'s3'">
                <h2 class="ta-headline" style="font-size:clamp(1.35rem,3vw,1.85rem)">جوالك وحسابك 🔐</h2>
                <p class="ta-lead mb-5">تحتاجها تسجّل دخولك بعد ما الأكاديمية توافق</p>
                <div class="space-y-4">
                    <div>
                        <label class="ta-label">رقم الجوال</label>
                        <div class="ta-phone">
                            <select name="country_code" x-model="countryCode" class="ta-field" dir="ltr">
                                @foreach($phoneCountries ?? [] as $c)
                                <option value="{{ $c['dial_code'] }}" @selected(old('country_code', $defaultDialCode) === $c['dial_code'])>{{ $c['dial_code'] }} {{ $c['name_ar'] }}</option>
                                @endforeach
                            </select>
                            <input type="tel" name="phone" x-model="phone" dir="ltr" class="ta-field flex-1" inputmode="tel" placeholder="5xxxxxxxx" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div>
                        <label class="ta-label">كلمة المرور</label>
                        <input type="password" name="password" x-model="password" class="ta-field" required minlength="8" autocomplete="new-password">
                        <div class="ix-pwd-meter" x-show="password.length > 0" x-cloak>
                            <template x-for="i in 4" :key="'pw-'+i">
                                <span :class="{ 'is-on': pwdScore >= i, 'is-strong': pwdScore >= 3 && i <= pwdScore }"></span>
                            </template>
                        </div>
                        <p class="text-xs mt-1 text-slate-400" x-show="password.length > 0" x-text="pwdHint" x-cloak></p>
                    </div>
                    <div class="ix-field-wrap">
                        <label class="ta-label">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" x-model="passwordConfirm" class="ta-field" required autocomplete="new-password">
                        <i class="fas fa-circle-check ix-field-ok" x-show="passwordMatch" x-cloak></i>
                        <p class="text-xs mt-1" :class="passwordMatch ? 'text-emerald-600' : (passwordConfirm.length ? 'text-rose-600' : 'text-slate-400')"
                           x-text="passwordMatch ? '✓ متطابقة' : (passwordConfirm.length ? 'غير متطابقة' : '')"></p>
                    </div>
                </div>
                <div class="ta-actions">
                    <button type="button" class="ta-btn-primary ix-cta-pulse" @click="next()" :disabled="!step3Valid">التالي</button>
                </div>
            </div>

            <div x-show="step === 4" x-cloak class="ix-step-panel" :key="'s4'">
                <h2 class="ta-headline" style="font-size:clamp(1.35rem,3vw,1.85rem)">ملفك التعريفي 📚</h2>
                <p class="ta-lead mb-5">كذا الطلاب يشوفونك ويختارونك</p>
                <div class="space-y-4">
                    <div class="ix-field-wrap">
                        <label class="ta-label">عنوان مختصر</label>
                        <input type="text" name="headline" x-model="headline" class="ta-field" placeholder="مثال: معلّم رياضيات — ثانوي" required value="{{ old('headline') }}">
                        <i class="fas fa-circle-check ix-field-ok" x-show="headline.trim().length >= 3" x-cloak></i>
                    </div>
                    <div class="ix-field-wrap">
                        <label class="ta-label">نبذة عنك <span class="text-slate-400 font-normal" x-text="'(' + bio.length + '/500)'"></span></label>
                        <textarea name="bio" x-model="bio" maxlength="500" class="ta-field ta-textarea" required>{{ old('bio') }}</textarea>
                        <i class="fas fa-circle-check ix-field-ok" x-show="bio.trim().length >= 20" x-cloak></i>
                    </div>
                    <div>
                        <label class="ta-label">سنوات الخبرة</label>
                        <input type="number" name="years_experience" x-model.number="yearsExp" class="ta-field" min="0" max="50" required value="{{ old('years_experience', 1) }}">
                    </div>
                </div>
                <div class="ta-actions">
                    <button type="button" class="ta-btn-primary ix-cta-pulse" @click="next()" :disabled="!step4Valid">التالي</button>
                </div>
            </div>

            <div x-show="step === 5" x-cloak class="ix-step-panel" :key="'s5'">
                <h2 class="ta-headline" style="font-size:clamp(1.35rem,3vw,1.85rem)">وش تدرّس؟ 📖</h2>
                <p class="ta-lead mb-5">اختار مادة وحدة على الأقل ومرحلة دراسية وحدة على الأقل</p>
                <p class="ta-label">المواد</p>
                <div class="ta-check-grid mb-4">
                    @foreach($subjects as $s)
                    <label class="ta-check-item ix-check-item">
                        <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}"
                               @checked(in_array($s->id, array_map('intval', (array) old('subject_ids', []))))>
                        <span>{{ $s->name }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="ta-label">المسارات الدراسية</p>
                <div class="ta-check-grid">
                    @foreach($years as $y)
                    <label class="ta-check-item ix-check-item">
                        <input type="checkbox" name="academic_year_ids[]" value="{{ $y->id }}"
                               @checked(in_array($y->id, array_map('intval', (array) old('academic_year_ids', []))))>
                        <span>{{ $y->name }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="ta-actions">
                    <button type="button" class="ta-btn-primary" @click="next()" :disabled="!step5Valid">التالي</button>
                </div>
            </div>

            <div x-show="step === 6" x-cloak class="ix-step-panel" :key="'s6'">
                <h2 class="ta-headline" style="font-size:clamp(1.35rem,3vw,1.85rem)">كيف تبي تستقبل الطلاب؟</h2>
                <p class="ta-lead mb-5">اختار الطريقة اللي تناسبك — وتقدر تغيّرها بعدين</p>
                <p class="ta-label">أنماط الحجز</p>
                <div class="ta-check-grid mb-4" style="max-height:none">
                    <label class="ta-check-item ix-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" checked> {{ __('tutor.matching_pick_teacher') }}</label>
                    <label class="ta-check-item ix-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" checked> {{ __('tutor.matching_self_schedule') }}</label>
                    <label class="ta-check-item ix-check-item"><input type="checkbox" name="matching_modes[]" value="assisted"> {{ __('tutor.matching_assisted') }}</label>
                </div>
                <p class="ta-label">أنواع الحصص</p>
                <div class="ta-check-grid mb-4" style="max-height:none">
                    <label class="ta-check-item ix-check-item"><input type="checkbox" name="session_types[]" value="one_to_one" checked> {{ __('tutor.session_one_to_one') }}</label>
                    <label class="ta-check-item ix-check-item"><input type="checkbox" name="session_types[]" value="small_group"> {{ __('tutor.session_small_group') }}</label>
                </div>
                <p class="text-sm text-slate-500">بعد ما ترسل، الأكاديمية تراجع طلبك — وإذا وافقتوا، سجّل دخولك من بوابة المعلّمين.</p>
                <div class="ta-actions">
                    <button type="submit" class="ta-btn-accent ix-cta-pulse" :disabled="submitting">
                        <span x-text="submitting ? 'جاري الإرسال...' : 'أرسل الطلب الحين'"></span>
                        <i class="fas fa-paper-plane" x-show="!submitting"></i>
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

<script>
function tutorApplyWizard() {
    const tips = {
        1: { title: 'هلا فيك!', text: 'خمس خطوات بسيطة — خذ راحتك وكملها على مهل.' },
        2: { title: 'مين أنت؟', text: 'حط بريد تتابعه — نرسل لك إشعار الموافقة عليه.' },
        3: { title: 'حسابك آمن', text: 'اختار كلمة مرور قوية — 8 أحرف على الأقل.' },
        4: { title: 'ملفك يهم الطلاب', text: 'اكتب نبذة واضحة — الطلاب يبون يعرفون أسلوبك.' },
        5: { title: 'تخصصك', text: 'اختار المواد اللي تدرّسها — وتقدر تعدّلها بعدين.' },
        6: { title: 'آخر خطوة!', text: 'حدّد طريقة استقبالك للطلاب وأرسل طلبك.' },
    };
    const motivations = {
        2: 'باقي لك دقيقتين وتخلص التسجيل 🚀',
        3: 'معلوماتك محمية — ما نشاركها مع أحد',
        4: 'الطلاب يختارون المعلّم من ملفه — خلّه يبرزك',
        5: 'اختار اللي يناسبك — ما في التزام بكل المواد',
    };
    return {
        step: {{ $resumeStep }},
        totalSteps: 6,
        submitting: false,
        name: @json(old('name', '')),
        email: @json(old('email', '')),
        countryCode: @json(old('country_code', $defaultDialCode)),
        phone: @json(old('phone', '')),
        password: '',
        passwordConfirm: '',
        headline: @json(old('headline', '')),
        bio: @json(old('bio', '')),
        yearsExp: {{ (int) old('years_experience', 1) }},
        init() {
            this.$watch('step', () => this.scrollToForm());
        },
        get emailValid() {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim());
        },
        get step2Valid() {
            return this.name.trim().length >= 2 && this.emailValid;
        },
        get passwordMatch() {
            return this.password.length >= 8 && this.password === this.passwordConfirm;
        },
        get pwdScore() {
            let s = 0;
            if (this.password.length >= 8) s++;
            if (/[A-Z]/.test(this.password) || /[أ-ي]/.test(this.password)) s++;
            if (/\d/.test(this.password)) s++;
            if (/[^A-Za-z0-9]/.test(this.password)) s++;
            return s;
        },
        get pwdHint() {
            if (this.password.length < 8) return 'لازم 8 أحرف على الأقل';
            if (this.pwdScore < 2) return 'زوّدها بأرقام';
            if (this.pwdScore < 3) return 'قوية';
            return 'ممتازة! 💪';
        },
        get step3Valid() { return this.passwordMatch; },
        get step4Valid() {
            return this.headline.trim().length >= 3 && this.bio.trim().length >= 20 && this.yearsExp >= 0;
        },
        get step5Valid() {
            const form = document.getElementById('tutorApplyForm');
            if (!form) return false;
            return form.querySelectorAll('input[name="subject_ids[]"]:checked').length >= 1
                && form.querySelectorAll('input[name="academic_year_ids[]"]:checked').length >= 1;
        },
        get progressPct() {
            if (this.step <= 1) return 0;
            return Math.round(((this.step - 1) / (this.totalSteps - 1)) * 100);
        },
        get stepLabel() {
            const labels = ['', 'مين أنت', 'حسابك', 'ملفك', 'تخصصك', 'تفضيلاتك'];
            return 'الخطوة ' + (this.step - 1) + ' من ' + (this.totalSteps - 1) + ' — ' + (labels[this.step - 1] || '');
        },
        get motivation() { return motivations[this.step] || ''; },
        get stepTip() { return tips[this.step] || { title: '', text: '' }; },
        scrollToForm() {
            if (window.innerWidth < 1024) {
                document.querySelector('.ta-form-col')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },
        next() {
            if (this.step === 2 && !this.step2Valid) return;
            if (this.step === 3 && !this.step3Valid) return;
            if (this.step === 4 && !this.step4Valid) return;
            if (this.step === 5 && !this.step5Valid) return;
            if (this.step < this.totalSteps) this.step++;
        },
        prev() { if (this.step > 1) this.step--; },
        onSubmit(e) {
            const modes = document.querySelectorAll('input[name="matching_modes[]"]:checked').length;
            const sessions = document.querySelectorAll('input[name="session_types[]"]:checked').length;
            if (!this.step5Valid || modes < 1 || sessions < 1) {
                e.preventDefault();
                this.step = modes < 1 || sessions < 1 ? 6 : 5;
                return;
            }
            this.submitting = true;
        }
    };
}
</script>
</body>
</html>

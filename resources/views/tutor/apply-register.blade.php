@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
    $brand = config('app.name');
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
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
    <title>{{ __('tutor.apply_register_title') }} — {{ $brand }}</title>
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
        .ta-nav { position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,.92); backdrop-filter: blur(10px); border-bottom: 1px solid #f1f5f9; }
        .ta-layout { display: flex; flex-direction: column; min-height: calc(100vh - 4rem); }
        @media (min-width: 1024px) {
            .ta-layout { flex-direction: row; align-items: stretch; }
            .ta-visual { width: 46%; position: sticky; top: 4rem; align-self: flex-start; min-height: calc(100vh - 4rem); }
            .ta-form-col { width: 54%; }
        }
        .ta-visual-inner { position: relative; max-width: 520px; margin: 0 auto; padding: 2.5rem 1.5rem 3rem; }
        @media (min-width: 1024px) { .ta-visual-inner { padding: 3.5rem 2rem 4rem; } }
        .ta-orbit { position: relative; width: 100%; aspect-ratio: 1; max-width: 500px; margin: 0 auto; }
        .ta-orbit-ring { position: absolute; inset: 4%; border-radius: 50%; border: 2px dashed rgba(var(--edu-primary-rgb), .15); animation: ta-spin 40s linear infinite; }
        @keyframes ta-spin { to { transform: rotate(360deg); } }
        .ta-main-photo { position: absolute; inset: 5%; border-radius: 50%; overflow: hidden; border: 6px solid #fff; box-shadow: 0 24px 60px -20px rgba(15,23,42,.35); z-index: 3; }
        .ta-main-photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 14%; transform: scale(1.28); }
        .ta-sub-photo { position: absolute; width: 28%; aspect-ratio: 1; border-radius: 50%; overflow: hidden; border: 4px solid #fff; box-shadow: 0 12px 32px -12px rgba(15,23,42,.3); z-index: 4; background: #fff; }
        .ta-sub-photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 18%; }
        .ta-sub-photo--a { bottom: 2%; inset-inline-start: -1%; }
        .ta-sub-photo--b { bottom: 5%; inset-inline-end: -3%; }
        .ta-trust { display: inline-flex; align-items: center; gap: .6rem; margin-top: 1.5rem; padding: .65rem 1rem; border-radius: 999px; background: var(--edu-accent-light); color: var(--edu-accent-dark); font-size: .8rem; font-weight: 700; }
        .ta-form-col { padding: 1.5rem 1.25rem 3rem; }
        @media (min-width: 1024px) { .ta-form-col { padding: 3rem 3rem 4rem; } }
        .ta-headline { font-size: clamp(1.6rem, 3.5vw, 2.25rem); font-weight: 800; line-height: 1.35; color: #0f172a; }
        .ta-lead { color: #64748b; font-size: .95rem; line-height: 1.7; }
        .ta-label { display: block; font-size: .8rem; font-weight: 700; color: #334155; margin-bottom: .4rem; }
        .ta-field { width: 100%; border: 1.5px solid #e2e8f0; background: #f8fafc; border-radius: 14px; padding: .85rem 1rem; font-size: .95rem; font-weight: 500; color: #0f172a; transition: .2s; }
        .ta-field:focus { outline: none; border-color: var(--edu-primary); background: #fff; box-shadow: 0 0 0 3px rgba(var(--edu-primary-rgb), .12); }
        .ta-field.is-invalid { border-color: #ef4444; background: #fef2f2; }
        .ta-phone { display: flex; gap: .5rem; }
        .ta-phone select { max-width: 7.5rem; }
        .ta-btn-primary { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; width: 100%; padding: .95rem 1.25rem; border: none; border-radius: 14px; background: var(--edu-primary); color: #fff; font-weight: 800; font-size: 1rem; cursor: pointer; }
        .ta-btn-primary:hover { filter: brightness(1.05); }
        .ta-alert-err { padding: .9rem 1rem; border-radius: 1rem; margin-bottom: 1rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: .88rem; }
        .ta-alert-ok { padding: .9rem 1rem; border-radius: 1rem; margin-bottom: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: .88rem; }
    </style>
</head>
<body class="ta-page" x-data="{ submitting: false }" @submit="submitting = true">

<header class="ta-nav">
    <div class="edu-container flex items-center justify-between gap-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-slate-900 no-underline">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $brand }}" class="w-9 h-9 rounded-xl object-contain">
            @endif
            <span>{{ $brand }}</span>
        </a>
        <a href="{{ route('staff.login') }}" class="text-sm font-bold text-[var(--edu-primary)] no-underline hover:underline">دخول المعلمين</a>
    </div>
</header>

<div class="ta-layout">
    <aside class="ta-visual relative overflow-hidden bg-[var(--edu-primary-light)]/40">
        <div class="th-hero-ambient" aria-hidden="true">
            <div class="th-hero-ambient__blob th-hero-ambient__blob--1"></div>
            <div class="th-hero-ambient__blob th-hero-ambient__blob--2"></div>
        </div>
        <div class="ta-visual-inner relative z-10">
            <div class="ta-orbit">
                <div class="ta-orbit-ring" aria-hidden="true"></div>
                <div class="ta-main-photo">
                    <img src="{{ $heroMain }}" alt="معلم — {{ $brand }}" loading="eager">
                </div>
                <div class="ta-sub-photo ta-sub-photo--a">
                    <img src="{{ $heroCircle }}" alt="" loading="lazy">
                </div>
                <div class="ta-sub-photo ta-sub-photo--b">
                    <img src="{{ $heroStudents }}" alt="" loading="lazy">
                </div>
            </div>
            <div class="ta-trust">
                <i class="fas fa-bolt"></i>
                <span>أنشئ حسابك خلال دقيقة — أكمل باقي الملف بعد الدخول</span>
            </div>
        </div>
    </aside>

    <main class="ta-form-col">
        @if(session('info'))
            <div class="ta-alert-ok">{{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="ta-alert-ok">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="ta-alert-err">
                @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
            </div>
        @endif

        <span class="edu-badge mb-4">انضمام المعلمين</span>
        <h1 class="ta-headline mb-3">{{ __('tutor.apply_register_title') }}</h1>
        <p class="ta-lead mb-6 max-w-lg">{{ __('tutor.apply_register_subtitle') }}</p>

        <form action="{{ route('tutor.apply.store') }}" method="POST" class="space-y-4" @submit="submitting = true">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="ta-label">الاسم الكامل *</label>
                    <input type="text" name="name" class="ta-field @error('name') is-invalid @enderror" required value="{{ old('name') }}" autocomplete="name">
                </div>
                <div>
                    <label class="ta-label">الجنسية *</label>
                    <input type="text" name="nationality" class="ta-field @error('nationality') is-invalid @enderror" required value="{{ old('nationality') }}">
                </div>
                <div>
                    <label class="ta-label">الدولة / المدينة *</label>
                    <input type="text" name="country_city" class="ta-field @error('country_city') is-invalid @enderror" required value="{{ old('country_city') }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="ta-label">رقم الجوال / واتساب *</label>
                    <div class="ta-phone">
                        <select name="country_code" class="ta-field" dir="ltr" required>
                            @foreach($phoneCountries ?? [] as $c)
                                <option value="{{ $c['dial_code'] }}" @selected(old('country_code', $defaultDialCode) === $c['dial_code'])>{{ $c['dial_code'] }}</option>
                            @endforeach
                        </select>
                        <input type="tel" name="phone" class="ta-field flex-1 @error('phone') is-invalid @enderror" required dir="ltr" value="{{ old('phone') }}" autocomplete="tel">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="ta-label">البريد الإلكتروني (يوزر الدخول) *</label>
                    <input type="email" name="email" class="ta-field @error('email') is-invalid @enderror" required dir="ltr" value="{{ old('email') }}" autocomplete="email">
                    <p class="text-xs text-slate-500 mt-1.5 mb-0">ستستخدم هذا البريد لتسجيل الدخول إلى المنصة.</p>
                </div>
                <div>
                    <label class="ta-label">كلمة المرور *</label>
                    <input type="password" name="password" class="ta-field @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                </div>
                <div>
                    <label class="ta-label">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirmation" class="ta-field" required autocomplete="new-password">
                </div>
                <div class="sm:col-span-2">
                    <label class="ta-label">LinkedIn <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                    <input type="url" name="linkedin_url" class="ta-field @error('linkedin_url') is-invalid @enderror" dir="ltr" placeholder="https://" value="{{ old('linkedin_url') }}">
                </div>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                بعد إنشاء الحساب ستدخل لوحة التحكم وتكمل باقي بيانات التقديم (المؤهل، الفيديو، المستندات…) ثم ترسلها للإدارة للموافقة.
            </p>

            <button type="submit" class="ta-btn-primary" :disabled="submitting">
                <span x-text="submitting ? 'جاري إنشاء الحساب…' : 'إنشاء الحساب والدخول'"></span>
                <i class="fas fa-arrow-left text-sm" x-show="!submitting"></i>
            </button>
        </form>

        <p class="mt-6 text-sm text-slate-500">
            لديك حساب؟ <a href="{{ route('staff.login') }}" class="font-bold text-[var(--edu-primary)]">تسجيل الدخول</a>
            · <a href="{{ route('password.request') }}" class="font-bold text-slate-600">نسيت كلمة المرور؟</a>
        </p>
    </main>
</div>
</body>
</html>

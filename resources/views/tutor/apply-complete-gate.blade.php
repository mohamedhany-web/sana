@php
    $brand = config('app.name');
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $heroMain = public_static_url('images/saudi.png');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>إكمال ملف التقديم — {{ $brand }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('landing.eduvalt.theme')
    <style>
        body { margin: 0; font-family: 'IBM Plex Sans Arabic', sans-serif; background: #fff; color: #0f172a; }
        .ta-card { max-width: 560px; margin: 0 auto; }
        .ta-btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .9rem 1.25rem; border-radius: 14px; font-weight: 800; text-decoration: none; }
        .ta-btn-primary { background: var(--edu-primary); color: #fff; }
        .ta-btn-outline { border: 1.5px solid #e2e8f0; color: #334155; background: #fff; }
    </style>
</head>
<body>
<header class="border-b border-slate-100">
    <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-slate-900 no-underline">
            @if($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $brand }}" class="w-9 h-9 rounded-xl object-contain">@endif
            <span>{{ $brand }}</span>
        </a>
        <a href="{{ route('staff.login') }}" class="text-sm font-bold" style="color:var(--edu-primary)">دخول المعلمين</a>
    </div>
</header>

<main class="px-4 py-10 sm:py-14">
    <div class="ta-card">
        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-lg mx-auto mb-6">
            <img src="{{ $heroMain }}" alt="" class="w-full h-full object-cover" style="object-position:50% 14%;transform:scale(1.2)">
        </div>

        <p class="text-center text-xs font-bold uppercase tracking-wide mb-2" style="color:var(--edu-primary)">خطوة ٢ — بعد إنشاء الحساب</p>
        <h1 class="text-2xl sm:text-3xl font-black text-center mb-3 leading-tight">إكمال ملف التقديم يحتاج تسجيل دخول</h1>
        <p class="text-center text-slate-600 text-sm leading-relaxed mb-6">
            هذه الصفحة ليست للتسجيل الأولي. أولاً أنشئ حساباً من صفحة التقديم، ثم ادخل من
            <strong>بوابة المعلمين</strong> لتكمل المؤهل والفيديو والمستندات وترسلها للإدارة.
        </p>

        @include('tutor.partials.apply-journey', ['journeyPhase' => 'complete'])

        @if(session('info'))
            <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">{{ session('info') }}</div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 mt-6">
            <a href="{{ route('staff.login') }}" class="ta-btn ta-btn-primary flex-1">
                دخول المعلمين ثم إكمال الملف
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <a href="{{ route('tutor.apply') }}" class="ta-btn ta-btn-outline flex-1">
                ليس لدي حساب — إنشاء حساب
            </a>
        </div>

        <p class="text-center text-xs text-slate-500 mt-5 mb-0">
            نسيت كلمة المرور؟
            <a href="{{ route('password.request') }}" class="font-bold" style="color:var(--edu-primary)">استعادة الدخول</a>
        </p>
    </div>
</main>
</body>
</html>

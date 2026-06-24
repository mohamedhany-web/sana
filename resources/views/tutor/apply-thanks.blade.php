@php
    $email = $email ?? session('apply_email');
    $brand = config('app.name');
    $heroMain = public_static_url('images/saudi.png');
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم إكمال تسجيلك — {{ $brand }}</title>
    @include('partials.favicon-links')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('landing.eduvalt.theme')
    @include('tutor.partials.interactive-ui')
    <style>
        .ta-page { min-height: 100vh; background: linear-gradient(180deg, #fff 0%, var(--edu-primary-light) 100%); }
        .ta-orbit { position: relative; width: 100%; max-width: 380px; aspect-ratio: 1; margin: 0 auto; }
        .ta-main-photo {
            position: absolute; inset: 5%; border-radius: 50%; overflow: hidden;
            border: 5px solid #fff; box-shadow: 0 20px 50px -18px rgba(15,23,42,.35);
        }
        .ta-main-photo img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 14%; transform: scale(1.28); }
        .ta-headline { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 800; line-height: 1.4; color: #0f172a; }
        .ta-check-list li { display: flex; align-items: center; gap: .5rem; padding: .35rem 0; }
    </style>
</head>
<body class="ta-page" x-data="{ show: false }" x-init="setTimeout(() => show = true, 120)">
<header class="border-b border-slate-100/80 bg-white/90 backdrop-blur sticky top-0 z-10">
    <div class="edu-container flex items-center justify-between py-3">
        <a href="{{ route('home') }}" class="font-extrabold text-slate-900 no-underline">{{ $brand }}</a>
        <a href="{{ route('staff.login') }}" class="text-sm font-bold text-[var(--edu-primary)]">دخول المعلّمين</a>
    </div>
</header>

<div class="edu-container py-10 lg:py-16">
    <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
        <div class="w-full lg:w-5/12" :class="show ? 'ix-success-pop' : 'opacity-0'">
            <div class="ta-orbit">
                <div class="ta-main-photo">
                    <img src="{{ $heroMain }}" alt="معلّم — {{ $brand }}">
                </div>
            </div>
            <div class="mt-6 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-bold ix-pulse" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                <i class="fas fa-check-circle"></i>
                اكتمل التسجيل — بانتظار الموافقة
            </div>
        </div>
        <div class="w-full lg:w-7/12 text-center lg:text-start" x-show="show" x-transition.opacity.duration.500ms>
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 px-4 py-3 text-sm text-start">{{ session('success') }}</div>
            @endif
            <span class="edu-badge mb-4">خطوة ٣ من ٣ — تم بنجاح</span>
            <h1 class="ta-headline mb-4">
                تم إكمال تسجيلك
                @include('landing.eduvalt.partials.title-mark', ['text' => 'بنجاح'])
            </h1>
            <p class="text-slate-600 leading-8 mb-6 max-w-xl">
                شكراً لانضمامك إلى {{ $brand }}.
                @if($email)
                    سجّلنا طلبك على البريد <strong dir="ltr">{{ $email }}</strong>.
                @endif
                وافقت على السياسة وأكملت ملفك — فريق الأكاديمية يراجع طلبك الآن.
            </p>
            <ul class="ta-check-list text-sm text-slate-600 space-y-1 mb-8 text-start max-w-md mx-auto lg:mx-0">
                <li><i class="fas fa-circle-check text-emerald-500"></i> تم استلام طلب التوظيف والمرفقات</li>
                <li><i class="fas fa-circle-check text-emerald-500"></i> تمت الموافقة على سياسة انضمام المعلمين</li>
                <li><i class="fas fa-circle-check text-emerald-500"></i> تم حفظ ملفك وإعدادات الحساب</li>
                <li><i class="fas fa-hourglass-half text-amber-500"></i> ستُفعَّل بوابة الدخول الكاملة بعد موافقة الأكاديمية</li>
            </ul>
            <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                @auth
                    @if(auth()->user()->instructorProfile && !auth()->user()->instructorProfile->tutor_onboarding_completed_at)
                        <a href="{{ route('instructor.tutor-lessons.setup') }}" class="edu-btn-primary ix-cta-pulse">
                            أكمل إعداد الملف <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                    @endif
                @endauth
                <a href="{{ route('staff.login') }}" class="edu-btn-outline">بوابة المعلّمين</a>
                <a href="{{ route('home') }}" class="edu-btn-outline">الرئيسية</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

@php
    $brand = config('app.name');
    $tp = fn (string $key) => __('teacher_policy.'.$key);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tp('page_title') }} — {{ $brand }}</title>
    @include('partials.favicon-links')
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('landing.eduvalt.theme')
    <style>
        .tp-page { min-height: 100vh; background: linear-gradient(180deg, #fff 0%, var(--edu-primary-light) 100%); }
        .tp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.25rem 1.35rem; }
        .tp-section { border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem; margin-bottom: 1rem; }
        .tp-section:last-child { border-bottom: 0; margin-bottom: 0; padding-bottom: 0; }
        .tp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .9rem 1.75rem; border-radius: 999px; font-weight: 800; color: #fff; background: var(--edu-primary); }
        .tp-btn:hover { background: var(--edu-primary-dark); }
    </style>
</head>
<body class="tp-page">
<header class="border-b border-slate-100 bg-white/90 backdrop-blur sticky top-0 z-10">
    <div class="edu-container flex items-center justify-between py-3">
        <a href="{{ route('home') }}" class="font-extrabold text-slate-900 no-underline">{{ $brand }}</a>
        <span class="text-sm font-bold text-[var(--edu-primary)]">خطوة ٢ من ٣ — السياسة</span>
    </div>
</header>

<div class="edu-container py-8 lg:py-12 max-w-3xl">
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-4 rounded-xl bg-sky-50 border border-sky-100 text-sky-900 px-4 py-3 text-sm">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <span class="edu-badge mb-3">مرحباً {{ $user->name }}</span>
    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 mb-2">{{ $tp('page_title') }}</h1>
    <p class="text-slate-600 leading-8 mb-6">{{ $tp('intro') }} {{ $tp('intro_commit') }}</p>

    <div class="tp-card mb-6 max-h-[52vh] overflow-y-auto">
        @foreach($sections as $section)
            <div class="tp-section">
                <h2 class="font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-{{ $section['icon'] ?? 'circle' }} text-[var(--edu-primary)] text-sm"></i>
                    {{ $section['title'] }}
                </h2>
                @if(!empty($section['body']))
                    <p class="text-sm text-slate-600 leading-7 mb-2">{{ $section['body'] }}</p>
                @endif
                @if(!empty($section['items']))
                    <ul class="text-sm text-slate-600 space-y-1 list-disc list-inside">
                        @foreach($section['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(!empty($section['footer']))
                    <p class="text-xs text-slate-500 mt-2">{{ $section['footer'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('tutor.apply.policy.accept') }}" class="tp-card space-y-4">
        @csrf
        <p class="text-sm font-bold text-slate-800">{{ $tp('ack_body') }}</p>
        <label class="flex items-start gap-3 cursor-pointer text-sm text-slate-700">
            <input type="hidden" name="policy_agreed" value="0">
            <input type="checkbox" name="policy_agreed" value="1" required class="mt-1 accent-[var(--edu-primary)]">
            <span>أقر أنني قرأت سياسة انضمام المعلمين على {{ $brand }} وأتعهد بالالتزام بجميع بنودها.</span>
        </label>
        <button type="submit" class="tp-btn w-full sm:w-auto">
            أوافق وأكمل إعداد حسابي <i class="fas fa-arrow-left text-xs"></i>
        </button>
    </form>

    <p class="text-xs text-slate-500 mt-4 text-center">
        <a href="{{ route('tutor.policy') }}" target="_blank" rel="noopener" class="underline">فتح السياسة في صفحة كاملة</a>
    </p>
</div>
</body>
</html>

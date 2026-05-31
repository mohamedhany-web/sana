<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>أهلاً بك — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.geometric-styles')
    @include('auth.partials.geometric-engine')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="geo-page" x-data="{}" x-init="window.createGeoEngine(document.getElementById('geo-canvas'))?.startConverge()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'])
                <span class="geo-step-tag">اكتمل التكوين</span>
                <h1 class="geo-headline">أهلاً <em>{{ $userName ?? 'بك' }}</em></h1>
                <p class="geo-lead">
                    @if(!empty($personalizeHint))
                    {{ $personalizeHint }}
                    @else
                    حسابك جاهز — الأشكال اكتملت.
                    @endif
                </p>
                <a href="{{ route('dashboard') }}" class="geo-cta magnetic" style="margin-top:1rem">ابدأ →</a>
                <p style="margin-top:1.5rem;font-size:.78rem;color:var(--edu-muted)">
                    ولي أمرك: نفس بريدك + «ولي أمر» في الدخول
                </p>
            </div>
        </main>
    </div>
</body>
</html>

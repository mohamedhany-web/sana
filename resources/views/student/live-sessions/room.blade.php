<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    <title>{{ $liveSession->title }} — بث مباشر | {{ config('brand.name', 'Sana') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    @include('partials.classroom-meeting-theme')
    <style>
        html, body { height: 100%; height: 100dvh; margin: 0; overflow: hidden; }
        #mx-live-broadcast-root { width: 100%; height: 100%; min-height: 0; overflow: hidden; }
        #mx-redir-bar { width: 200px; height: 4px; background: rgba(148,163,184,0.2); border-radius: 2px; overflow: hidden; }
        #mx-redir-fill { height: 100%; background: var(--edu-primary); border-radius: 2px; width: 0; transition: width 5s linear; }
    </style>
</head>
@php
    $platformName = config('brand.name', config('app.name', 'Sana'));
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
@endphp
<body class="mx-meeting-body">

    <div id="mx-session-ended" class="mx-meeting-ended-overlay">
        <div class="w-16 h-16 rounded-2xl bg-rose-500/15 text-rose-400 flex items-center justify-center text-3xl">
            <i class="fas fa-broadcast-tower"></i>
        </div>
        <h2>انتهت الجلسة</h2>
        <p>قام المدرب بإنهاء البث المباشر</p>
        <div id="mx-redir-bar"><div id="mx-redir-fill"></div></div>
        <p style="font-size:12px;color:#64748b;">سيتم توجيهك تلقائياً...</p>
        <a href="{{ route('student.live-sessions.index') }}" class="mx-btn-meeting mx-btn-meeting--ghost mt-2" style="background:var(--edu-primary);border-color:transparent">
            <i class="fas fa-arrow-right"></i> العودة الآن
        </a>
    </div>

    <header class="mx-meeting-room-header h-[72px]">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('student.live-sessions.index') }}" class="mx-meeting-brand-link shrink-0">
                <span class="mx-meeting-brand-icon">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="">
                    @else
                        <i class="fas fa-broadcast-tower text-sm"></i>
                    @endif
                </span>
                <span class="mx-meeting-brand-name hidden sm:inline">{{ $platformName }}</span>
            </a>
            <span class="w-px h-5 bg-white/15 hidden sm:block shrink-0"></span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="mx-meeting-live-dot"></span>
                <span class="mx-meeting-title">{{ $liveSession->title }}</span>
                @if($liveSession->instructor)
                    <span class="text-white/50 text-xs hidden md:inline truncate max-w-[8rem]">{{ $liveSession->instructor->name }}</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <div id="mx-student-wb-wrap" class="{{ ($allowStudentWhiteboard ?? false) ? '' : 'hidden' }}">
                <button type="button" id="btn-mx-share-draw" class="mx-btn-meeting mx-btn-meeting--accent"
                        title="رسم فوق ما يظهر في الاجتماع">
                    <i class="fas fa-pen-fancy"></i>
                    <span class="hidden sm:inline">رسم فوق البث</span>
                </button>
            </div>
            <form method="POST" action="{{ route('student.live-sessions.leave', $liveSession) }}" class="inline">
                @csrf
                <button type="submit" class="mx-btn-meeting mx-btn-meeting--ghost">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">مغادرة</span>
                </button>
            </form>
        </div>
    </header>

    <div class="mx-meeting-room-body">
        <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
            <main id="mx-live-broadcast-root" class="mx-jitsi-root" role="application" aria-label="غرفة البث"></main>
        </div>
    </div>

    @include('partials.livekit-room', [
        'livekitTokenUrl' => $livekitTokenUrl,
        'livekitContainerId' => 'mx-live-broadcast-root',
        'livekitAutoConnect' => true,
        'livekitOnLeftJs' => 'showSessionEndedAndRedirect();',
    ])
    <script>
        const indexUrl  = '{{ route("student.live-sessions.index") }}';

        function showSessionEndedAndRedirect() {
            var overlay = document.getElementById('mx-session-ended');
            var fill = document.getElementById('mx-redir-fill');
            if (overlay) overlay.classList.add('is-visible');
            if (fill) setTimeout(function() { fill.style.width = '100%'; }, 100);
            setTimeout(function() { window.location.href = indexUrl; }, 5500);
        }

        (function () {
            var statusUrl = '{{ route("student.live-sessions.status", $liveSession) }}';
            setInterval(function () {
                fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) {
                        if (!data) return;
                        if (data.status === 'ended' || data.ended === true) {
                            showSessionEndedAndRedirect();
                        }
                    })
                    .catch(function () {});
            }, 12000);
        })();
    </script>
</body>
</html>

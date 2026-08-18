<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    <title>انضمام للاجتماع — {{ config('brand.name', 'Sana') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    @include('partials.classroom-meeting-theme')
    <style>
        html { height: 100%; height: 100dvh; }
        #jitsi-container iframe { width: 100% !important; height: 100% !important; border: none; }
    </style>
</head>
@php
    $platformName = config('brand.name', config('app.name', 'Sana'));
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $isAuthenticated = (bool) ($authUser ?? auth()->user());
    $displayName = $isAuthenticated ? ($authUser->name ?? auth()->user()->name) : null;
    $displayEmail = $isAuthenticated ? ($authUser->email ?? auth()->user()->email) : null;
    $autoJoin = $isAuthenticated && empty($meetingEnded);
@endphp
<body>
    {{-- شاشة الانضمام --}}
    <div id="join-screen" class="mx-join-page mx-join-page--room flex flex-col min-h-screen">
        <nav class="mx-join-nav">
            <a href="{{ route('home') }}" class="mx-join-brand">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $platformName }}">
                @endif
                <span><em>{{ $platformName }}</em> Classroom</span>
            </a>
            <span class="mx-join-badge hidden sm:inline-flex">
                <i class="fas fa-shield-halved"></i>
                انضمام آمن
            </span>
        </nav>

        <div class="mx-join-stage flex-1">
            <div class="mx-join-card mx-join-card--dark">
                @if(!empty($meetingEnded))
                    <div class="mx-join-card__icon mx-join-card__icon--muted">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <h1 class="mx-join-title">انتهى الاجتماع</h1>
                    <p class="mx-join-lead">قام منظم الاجتماع بإنهائه. لا يمكن الانضمام مرة أخرى من هذا الرابط.</p>
                    @if($meeting && $meeting->title)
                        <p class="mx-join-meta font-semibold">{{ $meeting->title }}</p>
                    @endif
                    <p class="mx-join-meta">كود الغرفة: <span class="mx-join-code">{{ $code }}</span></p>
                    <a href="{{ route('home') }}" class="mx-btn-join mt-4" style="text-decoration:none">
                        <i class="fas fa-home"></i>
                        العودة للرئيسية
                    </a>
                @elseif(!empty($lessonJoinDenied))
                    <div class="mx-join-card__icon mx-join-card__icon--muted">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1 class="mx-join-title">الحصة مغلقة</h1>
                    <p class="mx-join-lead">{{ $lessonJoinMessage }}</p>
                    <a href="{{ route('home') }}" class="mx-btn-join mt-4" style="text-decoration:none">
                        <i class="fas fa-home"></i>
                        العودة للرئيسية
                    </a>
                @else
                    <div class="mx-join-card__icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h1 class="mx-join-title">{{ !empty($isLessonMeeting) ? 'دخول الحصة' : 'انضم للاجتماع' }}</h1>
                    @if($isAuthenticated)
                        <p class="mx-join-lead">أنت مسجّل الدخول — سيتم الانضمام باسم حسابك على المنصة.</p>
                    @elseif(!empty($isLessonMeeting))
                        <p class="mx-join-lead">هذه الحصة للطالب والمعلم المحجوزين فقط. سجّل الدخول بحسابك.</p>
                    @else
                        <p class="mx-join-lead">أدخل اسمك وانضم مباشرة — لا تحتاج حساباً على المنصة.</p>
                    @endif

                    @if($meeting && $meeting->title)
                        <p class="mx-join-meta font-semibold mb-2">{{ $meeting->title }}</p>
                    @endif

                    <div class="flex flex-wrap items-center justify-center gap-2 mb-5">
                        <span class="mx-join-badge">
                            <i class="fas fa-hashtag"></i>
                            <span class="mx-join-code">{{ $code }}</span>
                        </span>
                        <span class="mx-join-badge">
                            <i class="fas fa-users"></i>
                            حتى {{ $maxParticipants }} مشارك
                        </span>
                    </div>

                    <div class="mx-join-field space-y-4">
                        @if($isAuthenticated)
                            <div class="mx-join-user-chip">
                                <span class="mx-join-user-chip__avatar">{{ mb_substr($displayName, 0, 1) }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-bold text-slate-100 truncate">{{ $displayName }}</span>
                                    <span class="block text-xs text-slate-400 truncate" dir="ltr">{{ $displayEmail }}</span>
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-2 py-1 rounded-full shrink-0">مسجّل</span>
                            </div>
                        @elseif(empty($isLessonMeeting))
                            <div>
                                <label for="guest-name">اسمك (يظهر للمشاركين)</label>
                                <input type="text" id="guest-name" placeholder="مثال: أحمد محمد" autocomplete="name">
                            </div>
                        @endif
                        <button type="button" id="btn-join" class="mx-btn-join">
                            <i class="fas fa-video"></i>
                            <span id="btn-join-label">{{ $autoJoin ? 'جاري الانضمام...' : 'انضم الآن' }}</span>
                        </button>
                    </div>
                    <p class="mx-join-hint">بالانضمام أنت توافق على استخدام الكاميرا والميكروفون عند الحاجة.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- شاشة الاجتماع --}}
    <div id="meeting-screen" class="hidden mx-meeting-body h-screen">
        <header class="mx-meeting-room-header min-h-14 shrink-0 flex flex-col gap-2 md:flex-row md:items-center md:justify-between md:gap-2">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                <a href="{{ route('home') }}" class="mx-meeting-brand-link shrink-0">
                    <span class="mx-meeting-brand-icon w-8 h-8 sm:w-9 sm:h-9">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="">
                        @else
                            <i class="fas fa-video text-sm"></i>
                        @endif
                    </span>
                    <span class="mx-meeting-brand-name text-[11px] sm:text-sm truncate max-w-[6.5rem] sm:max-w-[8rem] md:max-w-none">{{ $platformName }}</span>
                </a>
                <span class="w-px h-5 bg-white/15 hidden sm:block shrink-0"></span>
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="mx-meeting-live-dot mx-meeting-live-dot--green shrink-0"></span>
                    <span class="mx-meeting-title text-xs sm:text-sm">{{ $meeting?->title ?: 'غرفة '.$code }}</span>
                    <span class="mx-meeting-code-chip shrink-0">{{ $code }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span id="lesson-bill-chip" class="hidden text-[11px] font-bold px-2 py-1 rounded-md bg-amber-500/20 border border-amber-500/30 text-amber-100"></span>
                <button type="button" id="btn-leave" class="mx-btn-meeting mx-btn-meeting--danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">مغادرة</span>
                </button>
            </div>
        </header>
        <div class="mx-meeting-room-body">
            <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
                <main id="jitsi-container" class="mx-jitsi-root" role="application" aria-label="غرفة الاجتماع"></main>
            </div>
        </div>
    </div>

    @if(empty($meetingEnded) && empty($lessonJoinDenied))
    @include('partials.livekit-room', [
        'livekitTokenUrl' => $livekitTokenUrl,
        'livekitContainerId' => 'jitsi-container',
        'livekitAutoConnect' => false,
        'livekitOnLeftJs' => 'if (typeof leaveMeetingAndReload === "function") leaveMeetingAndReload();',
    ])
    <script>
        const code = '{{ $code }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const authDisplayName = @json($displayName);
        const autoJoin = @json($autoJoin);
        const isLessonMeeting = @json(!empty($isLessonMeeting));
        let joinToken = null;
        let heartbeatTimer = null;
        let joinInProgress = false;
        let billedRunning = false;
        let billedSeconds = 0;
        let lastPresenceAt = Date.now();

        function formatClock(total) {
            total = Math.max(0, parseInt(total, 10) || 0);
            var m = Math.floor(total / 60);
            var s = total % 60;
            return m + ':' + String(s).padStart(2, '0');
        }

        function applyPresence(data) {
            if (!data) return;
            billedRunning = !!data.billed_running;
            billedSeconds = parseInt(data.billed_seconds, 10) || 0;
            lastPresenceAt = Date.now();
            var chip = document.getElementById('lesson-bill-chip');
            if (!chip || !isLessonMeeting) return;
            chip.classList.remove('hidden');
            if (billedRunning) {
                chip.textContent = 'وقت الحصة: ' + formatClock(billedSeconds);
            } else {
                chip.textContent = 'العداد متوقف — بانتظار المعلم والطالب معاً';
            }
        }

        setInterval(function () {
            if (!isLessonMeeting) return;
            var chip = document.getElementById('lesson-bill-chip');
            if (!chip || chip.classList.contains('hidden')) return;
            if (billedRunning) {
                var extra = Math.floor((Date.now() - lastPresenceAt) / 1000);
                chip.textContent = 'وقت الحصة: ' + formatClock(billedSeconds + extra);
            }
        }, 1000);

        function resolveDisplayName() {
            if (authDisplayName) return authDisplayName;
            var input = document.getElementById('guest-name');
            var name = input ? input.value.trim() : '';
            return name || 'ضيف';
        }

        async function startJoin() {
            if (joinInProgress) return;
            joinInProgress = true;

            const name = resolveDisplayName();
            const btn = document.getElementById('btn-join');
            const btnLabel = document.getElementById('btn-join-label');
            if (btn) btn.disabled = true;
            if (btnLabel) btnLabel.textContent = 'جاري التحقق...';

            try {
                const enterResp = await fetch(`/classroom/join/${code}/enter`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ display_name: name })
                });
                const enterData = await enterResp.json();
                if (!enterResp.ok || !enterData.ok) {
                    alert(enterData.message || 'لا يمكن الانضمام الآن.');
                    joinInProgress = false;
                    if (btn) btn.disabled = false;
                    if (btnLabel) btnLabel.textContent = 'انضم الآن';
                    return;
                }
                joinToken = enterData.token;
                window.__sanaLiveKitParticipantToken = joinToken;
                applyPresence(enterData);
            } catch (e) {
                alert('تعذر الاتصال بالخادم. حاول مرة أخرى.');
                joinInProgress = false;
                if (btn) btn.disabled = false;
                if (btnLabel) btnLabel.textContent = 'انضم الآن';
                return;
            }

            document.getElementById('join-screen').classList.add('hidden');
            document.getElementById('meeting-screen').classList.remove('hidden');

            if (window.SanaLiveKit && typeof window.SanaLiveKit.connect === 'function') {
                await window.SanaLiveKit.connect();
            }

            heartbeatTimer = setInterval(async function() {
                if (!joinToken) return;
                try {
                    const resp = await fetch(`/classroom/join/${code}/heartbeat`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: joinToken })
                    });
                    const data = await resp.json().catch(function () { return {}; });
                    applyPresence(data);
                } catch (e) {}
            }, isLessonMeeting ? 15000 : 30000);

            document.getElementById('btn-leave').onclick = function() {
                if (window.SanaLiveKit) window.SanaLiveKit.disconnect();
                leaveMeetingAndReload();
            };
        }

        document.getElementById('btn-join').addEventListener('click', startJoin);

        if (autoJoin) {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(startJoin, 400);
            });
        }

        async function leaveMeetingAndReload() {
            if (heartbeatTimer) clearInterval(heartbeatTimer);
            if (joinToken) {
                try {
                    await fetch(`/classroom/join/${code}/leave`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: joinToken, _token: csrfToken })
                    });
                } catch (e) {}
            }
            window.location.reload();
        }

        window.addEventListener('beforeunload', function() {
            if (!joinToken) return;
            navigator.sendBeacon(`/classroom/join/${code}/leave`, new Blob([JSON.stringify({ token: joinToken, _token: csrfToken })], { type: 'application/json' }));
        });
    </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <title>انضمام للاجتماع — <?php echo e(config('brand.name', 'Sana')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <?php echo $__env->make('partials.classroom-meeting-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<?php
    $platformName = config('brand.name', config('app.name', 'Sana'));
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<body>
    
    <div id="join-screen" class="mx-join-page flex flex-col min-h-screen">
        <nav class="mx-join-nav">
            <a href="<?php echo e(route('home')); ?>" class="mx-join-brand">
                <?php if($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($platformName); ?>">
                <?php endif; ?>
                <span><em><?php echo e($platformName); ?></em> Classroom</span>
            </a>
            <span class="mx-join-badge hidden sm:inline-flex">
                <i class="fas fa-shield-halved"></i>
                انضمام آمن
            </span>
        </nav>

        <div class="mx-join-stage flex-1">
            <div class="mx-join-card">
                <?php if(!empty($meetingEnded)): ?>
                    <div class="mx-join-card__icon mx-join-card__icon--muted">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <h1 class="mx-join-title">انتهى الاجتماع</h1>
                    <p class="mx-join-lead">قام منظم الاجتماع بإنهائه. لا يمكن الانضمام مرة أخرى من هذا الرابط.</p>
                    <?php if($meeting && $meeting->title): ?>
                        <p class="mx-join-meta font-semibold text-slate-700"><?php echo e($meeting->title); ?></p>
                    <?php endif; ?>
                    <p class="mx-join-meta">كود الغرفة: <span class="mx-join-code"><?php echo e($code); ?></span></p>
                    <a href="<?php echo e(route('home')); ?>" class="mx-btn-join mt-4" style="text-decoration:none">
                        <i class="fas fa-home"></i>
                        العودة للرئيسية
                    </a>
                <?php else: ?>
                    <div class="mx-join-card__icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h1 class="mx-join-title">انضم للاجتماع</h1>
                    <p class="mx-join-lead">أدخل اسمك وانضم مباشرة — لا تحتاج حساباً على المنصة.</p>

                    <?php if($meeting && $meeting->title): ?>
                        <p class="mx-join-meta font-semibold text-slate-700 mb-2"><?php echo e($meeting->title); ?></p>
                    <?php endif; ?>

                    <div class="flex flex-wrap items-center justify-center gap-2 mb-5">
                        <span class="mx-join-badge">
                            <i class="fas fa-hashtag"></i>
                            <span class="mx-join-code"><?php echo e($code); ?></span>
                        </span>
                        <span class="mx-join-badge">
                            <i class="fas fa-users"></i>
                            حتى <?php echo e($maxParticipants); ?> مشارك
                        </span>
                    </div>

                    <div class="mx-join-field space-y-4">
                        <div>
                            <label for="guest-name">اسمك (يظهر للمشاركين)</label>
                            <input type="text" id="guest-name" placeholder="مثال: أحمد محمد" autocomplete="name">
                        </div>
                        <button type="button" id="btn-join" class="mx-btn-join">
                            <i class="fas fa-video"></i>
                            انضم الآن
                        </button>
                    </div>
                    <p class="mx-join-hint">بالانضمام أنت توافق على استخدام الكاميرا والميكروفون عند الحاجة.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div id="meeting-screen" class="hidden mx-meeting-body h-screen">
        <header class="mx-meeting-room-header h-[72px]">
            <div class="flex items-center gap-3 min-w-0">
                <span class="mx-meeting-brand-icon">
                    <?php if($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-video text-sm"></i>
                    <?php endif; ?>
                </span>
                <div class="min-w-0">
                    <span class="mx-meeting-brand-name block truncate"><?php echo e($platformName); ?> Classroom</span>
                    <span class="mx-meeting-code-chip"><?php echo e($code); ?></span>
                </div>
                <span class="mx-meeting-live-dot mx-meeting-live-dot--green hidden sm:inline-block"></span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div id="mx-guest-wb-wrap" class="hidden">
                    <button type="button" id="btn-mx-share-draw-guest" class="mx-btn-meeting mx-btn-meeting--accent"
                            title="رسم فوق ما يظهر في الاجتماع">
                        <i class="fas fa-pen-fancy"></i>
                        <span class="hidden sm:inline">رسم فوق العرض</span>
                    </button>
                </div>
                <button type="button" id="btn-leave" class="mx-btn-meeting mx-btn-meeting--danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden sm:inline">مغادرة</span>
                </button>
            </div>
        </header>
        <div class="mx-meeting-room-body">
            <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
                <main id="jitsi-container" class="mx-jitsi-root" role="application" aria-label="غرفة الاجتماع"></main>
                <?php echo $__env->make('partials.mx-share-annotation-overlay', [
                    'mxAnnRole' => 'classroom_guest_emit',
                    'mxAnnPostUrl' => route('classroom.join.share-annotation', $code),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    <?php if(empty($meetingEnded)): ?>
    <?php echo $__env->make('partials.jitsi-iframe-media-allow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://<?php echo e($jitsiDomain); ?>/external_api.js"></script>
    <script>
        const domain = '<?php echo e($jitsiDomain); ?>';
        const roomName = '<?php echo e($roomName); ?>';
        const code = '<?php echo e($code); ?>';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let api = null;
        let joinToken = null;
        let heartbeatTimer = null;

        function applyGuestWhiteboardAllowed(on) {
            if (typeof window.__mxShareAnnSetAllowed === 'function') {
                window.__mxShareAnnSetAllowed(!!on);
            }
            var wrap = document.getElementById('mx-guest-wb-wrap');
            if (!wrap) return;
            if (on) wrap.classList.remove('hidden');
            else wrap.classList.add('hidden');
        }

        document.getElementById('btn-join').addEventListener('click', async function() {
            const name = document.getElementById('guest-name').value.trim() || 'ضيف';
            const btn = document.getElementById('btn-join');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';

            try {
                const enterResp = await fetch(`/classroom/join/${code}/enter`, {
                    method: 'POST',
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
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-video"></i> انضم الآن';
                    return;
                }
                joinToken = enterData.token;
                if (typeof window.__mxShareAnnSetGuestToken === 'function') {
                    window.__mxShareAnnSetGuestToken(joinToken);
                }
                applyGuestWhiteboardAllowed(!!enterData.allow_participant_whiteboard);
            } catch (e) {
                alert('تعذر الاتصال بالخادم. حاول مرة أخرى.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-video"></i> انضم الآن';
                return;
            }

            document.getElementById('join-screen').classList.add('hidden');
            document.getElementById('meeting-screen').classList.remove('hidden');

            const jitsiRoot = document.querySelector('#jitsi-container');
            if (typeof SanaEnsureJitsiIframeMediaAllow === 'function') {
                SanaEnsureJitsiIframeMediaAllow(jitsiRoot);
            }

            const options = {
                roomName: roomName,
                parentNode: jitsiRoot,
                width: '100%',
                height: '100%',
                userInfo: { displayName: name },
                configOverwrite: {
                    prejoinConfig: { enabled: false },
                    prejoinPageEnabled: false,
                    enableLobby: false,
                    requireDisplayName: false,
                    enableWelcomePage: false,
                    disableDeepLinking: true,
                    enableRecording: false,
                    startWithAudioMuted: true,
                    startWithVideoMuted: true,
                    enableNoisyMicDetection: false,
                },
                interfaceConfigOverwrite: {
                    APP_NAME: '<?php echo e($platformName); ?>',
                    NATIVE_APP_NAME: '<?php echo e($platformName); ?>',
                    PROVIDER_NAME: '<?php echo e($platformName); ?>',
                    JITSI_WATERMARK_LINK: '',
                    HIDE_DEEP_LINKING_LOGO: true,
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'chat',
                        'raisehand', 'tileview', 'videoquality', 'filmstrip'
                    ],
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    SHOW_POWERED_BY: false,
                    MOBILE_APP_PROMO: false,
                    DEFAULT_BACKGROUND: '#0f172a',
                    FILM_STRIP_MAX_HEIGHT: 100,
                }
            };
            api = new JitsiMeetExternalAPI(domain, options);
            var drawGuestBtn = document.getElementById('btn-mx-share-draw-guest');
            if (drawGuestBtn && typeof window.__mxShareAnnOpenToolbar === 'function') {
                drawGuestBtn.addEventListener('click', function () { window.__mxShareAnnOpenToolbar(); });
            }
            heartbeatTimer = setInterval(async function() {
                if (!joinToken) return;
                try {
                    const hbRes = await fetch(`/classroom/join/${code}/heartbeat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: joinToken })
                    });
                    if (hbRes.ok) {
                        const hbData = await hbRes.json();
                        if (typeof hbData.allow_participant_whiteboard !== 'undefined') {
                            applyGuestWhiteboardAllowed(!!hbData.allow_participant_whiteboard);
                        }
                    }
                } catch (e) {}
            }, 30000);

            api.addEventListener('readyToClose', function() {
                leaveMeetingAndReload();
            });

            document.getElementById('btn-leave').onclick = function() {
                if (api) api.executeCommand('hangup');
            };
        });

        async function leaveMeetingAndReload() {
            if (heartbeatTimer) clearInterval(heartbeatTimer);
            if (joinToken) {
                try {
                    await fetch(`/classroom/join/${code}/leave`, {
                        method: 'POST',
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
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/classroom/join.blade.php ENDPATH**/ ?>
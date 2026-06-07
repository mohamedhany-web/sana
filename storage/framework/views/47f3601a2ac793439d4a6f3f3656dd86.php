<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <title><?php echo e($liveSession->title); ?> — بث مباشر | <?php echo e(config('brand.name', 'Sana')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <?php echo $__env->make('partials.classroom-meeting-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        #mx-redir-bar { width: 200px; height: 4px; background: rgba(148,163,184,0.2); border-radius: 2px; overflow: hidden; }
        #mx-redir-fill { height: 100%; background: var(--edu-primary); border-radius: 2px; width: 0; transition: width 5s linear; }
    </style>
</head>
<?php
    $platformName = config('brand.name', config('app.name', 'Sana'));
    $logoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
?>
<body class="mx-meeting-body">

    <div id="mx-session-ended" class="mx-meeting-ended-overlay">
        <div class="w-16 h-16 rounded-2xl bg-rose-500/15 text-rose-400 flex items-center justify-center text-3xl">
            <i class="fas fa-broadcast-tower"></i>
        </div>
        <h2>انتهت الجلسة</h2>
        <p>قام المدرب بإنهاء البث المباشر</p>
        <div id="mx-redir-bar"><div id="mx-redir-fill"></div></div>
        <p style="font-size:12px;color:#64748b;">سيتم توجيهك تلقائياً...</p>
        <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="mx-btn-meeting mx-btn-meeting--ghost mt-2" style="background:var(--edu-primary);border-color:transparent">
            <i class="fas fa-arrow-right"></i> العودة الآن
        </a>
    </div>

    <header class="mx-meeting-room-header h-[72px]">
        <div class="flex items-center gap-3 min-w-0">
            <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="mx-meeting-brand-link shrink-0">
                <span class="mx-meeting-brand-icon">
                    <?php if($logoUrl): ?>
                        <img src="<?php echo e($logoUrl); ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-broadcast-tower text-sm"></i>
                    <?php endif; ?>
                </span>
                <span class="mx-meeting-brand-name hidden sm:inline"><?php echo e($platformName); ?></span>
            </a>
            <span class="w-px h-5 bg-white/15 hidden sm:block shrink-0"></span>
            <div class="flex items-center gap-2 min-w-0">
                <span class="mx-meeting-live-dot"></span>
                <span class="mx-meeting-title"><?php echo e($liveSession->title); ?></span>
                <?php if($liveSession->instructor): ?>
                    <span class="text-white/50 text-xs hidden md:inline truncate max-w-[8rem]"><?php echo e($liveSession->instructor->name); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <div id="mx-student-wb-wrap" class="<?php echo e(($allowStudentWhiteboard ?? false) ? '' : 'hidden'); ?>">
                <button type="button" id="btn-mx-share-draw" class="mx-btn-meeting mx-btn-meeting--accent"
                        title="رسم فوق ما يظهر في الاجتماع">
                    <i class="fas fa-pen-fancy"></i>
                    <span class="hidden sm:inline">رسم فوق البث</span>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('student.live-sessions.leave', $liveSession)); ?>" class="inline">
                <?php echo csrf_field(); ?>
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
            <?php echo $__env->make('partials.mx-share-annotation-overlay', [
                'mxAnnRole' => 'student_emit',
                'mxAnnPostUrl' => route('student.live-sessions.share-annotation', $liveSession),
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <?php echo $__env->make('partials.jitsi-iframe-media-allow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://<?php echo e($jitsiDomain); ?>/external_api.js"></script>
    <script>
        const domain    = '<?php echo e($jitsiDomain); ?>';
        const indexUrl  = '<?php echo e(route("student.live-sessions.index")); ?>';
        const jitsiRoot = document.querySelector('#mx-live-broadcast-root');
        if (typeof SanaEnsureJitsiIframeMediaAllow === 'function') {
            SanaEnsureJitsiIframeMediaAllow(jitsiRoot);
        }

        const options = {
            roomName: '<?php echo e($liveSession->room_name); ?>',
            parentNode: jitsiRoot,
            width: '100%',
            height: '100%',
            userInfo: {
                displayName: '<?php echo e($user->name); ?>',
                email: '<?php echo e($user->email); ?>'
            },
            configOverwrite: {
                prejoinConfig: { enabled: false },
                prejoinPageEnabled: false,
                enableLobby: false,
                requireDisplayName: false,
                enableWelcomePage: false,
                disableDeepLinking: true,
                startWithAudioMuted: <?php echo e($liveSession->mute_on_join ? 'true' : 'false'); ?>,
                startWithVideoMuted: <?php echo e($liveSession->video_off_on_join ? 'true' : 'false'); ?>,
                enableNoisyMicDetection: false,
                <?php if(!$liveSession->allow_chat): ?>
                disableChat: true,
                <?php endif; ?>
            },
            interfaceConfigOverwrite: {
                APP_NAME: '<?php echo e($platformName); ?>',
                NATIVE_APP_NAME: '<?php echo e($platformName); ?>',
                PROVIDER_NAME: '<?php echo e($platformName); ?>',
                JITSI_WATERMARK_LINK: '',
                HIDE_DEEP_LINKING_LOGO: true,
                TOOLBAR_BUTTONS: [],
                TOOLBAR_ALWAYS_VISIBLE: false,
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
                SHOW_BRAND_WATERMARK: false,
                SHOW_POWERED_BY: false,
                MOBILE_APP_PROMO: false,
                DEFAULT_BACKGROUND: '#0f172a',
                DISABLE_JOIN_LEAVE_NOTIFICATIONS: true,
                FILM_STRIP_MAX_HEIGHT: 120,
            }
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        function showSessionEndedAndRedirect() {
            var overlay = document.getElementById('mx-session-ended');
            var fill = document.getElementById('mx-redir-fill');
            overlay.classList.add('is-visible');
            setTimeout(function() { fill.style.width = '100%'; }, 100);
            setTimeout(function() { window.location.href = indexUrl; }, 5500);
        }

        api.addEventListener('readyToClose', function() {
            window.location.href = indexUrl;
        });

        api.addEventListener('videoConferenceLeft', function() {
            showSessionEndedAndRedirect();
        });

        (function () {
            var wrap = document.getElementById('mx-student-wb-wrap');
            var drawBtn = document.getElementById('btn-mx-share-draw');
            var statusUrl = '<?php echo e(route("student.live-sessions.status", $liveSession)); ?>';
            function applyAllow(on) {
                if (typeof window.__mxShareAnnSetAllowed === 'function') {
                    window.__mxShareAnnSetAllowed(!!on);
                }
                if (!wrap) return;
                if (on) wrap.classList.remove('hidden');
                else wrap.classList.add('hidden');
            }
            if (drawBtn && typeof window.__mxShareAnnOpenToolbar === 'function') {
                drawBtn.addEventListener('click', function () { window.__mxShareAnnOpenToolbar(); });
            }
            applyAllow(<?php echo e(($allowStudentWhiteboard ?? false) ? 'true' : 'false'); ?>);
            setInterval(function () {
                fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) {
                        if (!data) return;
                        if (data.status === 'ended' || data.ended === true) {
                            showSessionEndedAndRedirect();
                            return;
                        }
                        if (typeof data.allow_student_whiteboard !== 'undefined') {
                            applyAllow(!!data.allow_student_whiteboard);
                        }
                    })
                    .catch(function () {});
            }, 12000);
        })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\student\live-sessions\room.blade.php ENDPATH**/ ?>
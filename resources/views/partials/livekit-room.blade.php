{{--
  Shared LiveKit room mount — high quality capture + clean controls.
  Required: $livekitTokenUrl
  Optional: $livekitContainerId, $livekitParticipantToken, $livekitAutoConnect,
            $livekitOnReadyJs, $livekitOnLeftJs, $livekitExtraBody
--}}
@php
    $lkContainerId = $livekitContainerId ?? 'livekit-room-root';
    $lkAuto = ($livekitAutoConnect ?? true) ? 'true' : 'false';
    $lkParticipantToken = $livekitParticipantToken ?? null;
    $lkExtraBody = $livekitExtraBody ?? [];
@endphp
<style>
    #{{ $lkContainerId }} {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 0;
        max-height: 100%;
        background: #020617;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    #{{ $lkContainerId }} .lk-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        justify-content: space-between;
        padding: 6px 10px;
        color: #94a3b8;
        font-size: 11px;
        flex-shrink: 0;
    }
    #{{ $lkContainerId }} .lk-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(30, 41, 59, 0.85);
        border: 1px solid rgba(148, 163, 184, 0.2);
    }
    #{{ $lkContainerId }} .lk-meta-pill.is-good { color: #67e8f9; }
    #{{ $lkContainerId }} .lk-meta-pill.is-ok { color: #fbbf24; }
    #{{ $lkContainerId }} .lk-meta-pill.is-bad { color: #fb7185; }
    #{{ $lkContainerId }} .lk-stage {
        flex: 1 1 auto;
        min-height: 0;
        display: grid;
        gap: 8px;
        padding: 8px;
        overflow: hidden;
        align-content: stretch;
        grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
        grid-auto-rows: minmax(0, 1fr);
    }
    /* مشارك واحد: يملأ الشاشة بالكامل */
    #{{ $lkContainerId }} .lk-stage[data-count="1"] {
        grid-template-columns: 1fr;
        grid-template-rows: 1fr;
    }
    /* مشاركان: جنب بعض على الشاشات الواسعة */
    #{{ $lkContainerId }} .lk-stage[data-count="2"] {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr;
    }
    @media (max-width: 700px) {
        #{{ $lkContainerId }} .lk-stage[data-count="2"] {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr 1fr;
        }
    }
    #{{ $lkContainerId }} .lk-stage[data-count="3"],
    #{{ $lkContainerId }} .lk-stage[data-count="4"] {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    #{{ $lkContainerId }} .lk-stage[data-count="5"],
    #{{ $lkContainerId }} .lk-stage[data-count="6"] {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-template-rows: 1fr 1fr;
    }
    @media (max-width: 900px) {
        #{{ $lkContainerId }} .lk-stage[data-count="5"],
        #{{ $lkContainerId }} .lk-stage[data-count="6"] {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(3, minmax(0, 1fr));
        }
    }
    /* أكثر من 6: شبكة مرنة بدون إجبار سكرول الصفحة */
    #{{ $lkContainerId }} .lk-stage[data-count="many"] {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        grid-auto-rows: minmax(120px, 1fr);
        overflow: auto;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-stage {
        display: flex !important;
        flex-direction: column;
        grid-template-columns: none !important;
        grid-template-rows: none !important;
        overflow: hidden;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-tile.is-focus {
        flex: 1 1 auto;
        min-height: 0 !important;
        aspect-ratio: auto !important;
        width: 100%;
        height: auto;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-focus-others {
        flex: 0 0 auto;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        overflow-y: hidden;
        max-height: 96px;
        padding-bottom: 2px;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-focus-others .lk-tile {
        flex: 0 0 140px;
        width: 140px;
        height: 88px;
        max-width: none;
        aspect-ratio: auto !important;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-stage-strip {
        display: none;
    }
    #{{ $lkContainerId }} .lk-tile {
        position: relative;
        background: #0f172a;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        min-height: 0;
        width: 100%;
        height: 100%;
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 8px 24px rgba(2, 6, 23, 0.35);
        transition: border-color .15s, box-shadow .15s;
    }
    #{{ $lkContainerId }} .lk-tile.is-speaking {
        border-color: rgba(34, 211, 238, 0.75);
        box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.25);
    }
    #{{ $lkContainerId }} .lk-tile video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #020617;
    }
    #{{ $lkContainerId }} .lk-tile.is-screen video {
        object-fit: contain;
        background: #000;
    }
    #{{ $lkContainerId }} .lk-tile-label {
        position: absolute;
        left: 8px;
        bottom: 8px;
        right: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        color: #e2e8f0;
        background: rgba(2, 6, 23, 0.72);
        border-radius: 10px;
        padding: 6px 10px;
        pointer-events: none;
        backdrop-filter: blur(6px);
    }
    #{{ $lkContainerId }} .lk-tile-label span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #{{ $lkContainerId }} .lk-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
        padding: 8px 10px calc(8px + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(2, 6, 23, 0.96);
        flex-shrink: 0;
    }
    #{{ $lkContainerId }} .lk-toolbar button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #1e293b;
        color: #e2e8f0;
        font-size: 12px;
        font-weight: 600;
        padding: 8px 12px;
        cursor: pointer;
    }
    #{{ $lkContainerId }} .lk-toolbar button:hover { background: #334155; }
    #{{ $lkContainerId }} .lk-toolbar button.is-off {
        background: #7f1d1d;
        border-color: #b91c1c;
    }
    #{{ $lkContainerId }} .lk-toolbar button.is-active {
        background: #0e7490;
        border-color: #06b6d4;
    }
    @media (max-width: 640px) {
        #{{ $lkContainerId }} .lk-toolbar button span { display: none; }
        #{{ $lkContainerId }} .lk-toolbar button { padding: 10px 12px; }
    }
    #{{ $lkContainerId }} .lk-status {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #94a3b8;
        font-size: 14px;
        z-index: 2;
        background: #020617;
        text-align: center;
        padding: 1.5rem;
    }
    #{{ $lkContainerId }} .lk-status.is-hidden { display: none; }
</style>

<script type="module">
(async function () {
    const containerId = @json($lkContainerId);
    const tokenUrl = @json($livekitTokenUrl);
    const participantTokenFromBlade = @json($lkParticipantToken);
    const autoConnect = {{ $lkAuto }};
    const extraBody = @json($lkExtraBody);
    const csrf = @json(csrf_token())
        || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value
        || '';

    function currentParticipantToken() {
        if (window.__sanaLiveKitParticipantToken) return window.__sanaLiveKitParticipantToken;
        return participantTokenFromBlade;
    }

    const root = document.getElementById(containerId);
    if (!root) return;

    root.innerHTML = ''
        + '<div class="lk-status" data-lk-status><i class="fas fa-spinner fa-spin text-2xl text-cyan-400"></i><span>جاري الاتصال بغرفة البث…</span></div>'
        + '<div class="lk-meta" data-lk-meta>'
        + '  <span class="lk-meta-pill" data-lk-quality><i class="fas fa-signal"></i><span>جودة الاتصال</span></span>'
        + '  <span class="lk-meta-pill" data-lk-count><i class="fas fa-users"></i><span>0</span></span>'
        + '</div>'
        + '<div class="lk-stage" data-lk-stage></div>'
        + '<div class="lk-toolbar" data-lk-toolbar>'
        + '  <button type="button" data-lk-mic><i class="fas fa-microphone"></i><span>ميكروفون</span></button>'
        + '  <button type="button" data-lk-cam><i class="fas fa-video"></i><span>كاميرا</span></button>'
        + '  <button type="button" data-lk-screen><i class="fas fa-desktop"></i><span>مشاركة الشاشة</span></button>'
        + '  <button type="button" data-lk-focus><i class="fas fa-expand"></i><span>تركيز</span></button>'
        + '  <button type="button" data-lk-fs><i class="fas fa-up-right-and-down-left-from-center"></i><span>ملء الشاشة</span></button>'
        + '  <button type="button" data-lk-leave class="is-off"><i class="fas fa-phone-slash"></i><span>مغادرة</span></button>'
        + '</div>';

    const statusEl = root.querySelector('[data-lk-status]');
    const stageEl = root.querySelector('[data-lk-stage]');
    const qualityEl = root.querySelector('[data-lk-quality]');
    const countEl = root.querySelector('[data-lk-count] span');
    const micBtn = root.querySelector('[data-lk-mic]');
    const camBtn = root.querySelector('[data-lk-cam]');
    const screenBtn = root.querySelector('[data-lk-screen]');
    const focusBtn = root.querySelector('[data-lk-focus]');
    const fsBtn = root.querySelector('[data-lk-fs]');
    const leaveBtn = root.querySelector('[data-lk-leave]');

    function setStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.classList.remove('is-hidden');
        statusEl.innerHTML = (isError
            ? '<i class="fas fa-exclamation-triangle text-2xl text-amber-400"></i>'
            : '<i class="fas fa-spinner fa-spin text-2xl text-cyan-400"></i>')
            + '<span>' + msg + '</span>';
    }
    function hideStatus() {
        statusEl?.classList.add('is-hidden');
    }

    let LivekitClient;
    try {
        LivekitClient = await import('https://cdn.jsdelivr.net/npm/livekit-client@2.15.4/+esm');
    } catch (e) {
        console.error(e);
        setStatus('تعذر تحميل مكتبة البث من الشبكة.', true);
        return;
    }

    const {
        Room,
        RoomEvent,
        Track,
        VideoPresets,
        ScreenSharePresets,
        ConnectionQuality,
        createLocalTracks,
        createLocalScreenTracks,
    } = LivekitClient;

    const camRes = (VideoPresets && VideoPresets.h1080)
        ? VideoPresets.h1080
        : (VideoPresets && VideoPresets.h720 ? VideoPresets.h720 : null);
    const camSimulcast = [
        VideoPresets?.h180,
        VideoPresets?.h360,
        VideoPresets?.h720,
    ].filter(Boolean);
    const screenPreset = (ScreenSharePresets && ScreenSharePresets.h1080fps30)
        ? ScreenSharePresets.h1080fps30
        : (ScreenSharePresets && ScreenSharePresets.h1080fps15
            ? ScreenSharePresets.h1080fps15
            : null);
    const screenSimulcast = [
        ScreenSharePresets?.h720fps15,
        ScreenSharePresets?.h360fps15,
    ].filter(Boolean);

    const room = new Room({
        adaptiveStream: true,
        dynacast: true,
        stopLocalTrackOnUnpublish: true,
        videoCaptureDefaults: {
            resolution: camRes ? camRes.resolution : { width: 1920, height: 1080, frameRate: 30 },
            facingMode: 'user',
        },
        audioCaptureDefaults: {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            channelCount: 1,
        },
        publishDefaults: {
            dtx: true,
            red: true,
            forceStereo: false,
            videoCodec: 'vp9',
            videoEncoding: camRes ? camRes.encoding : { maxBitrate: 3_500_000, maxFramerate: 30 },
            videoSimulcastLayers: camSimulcast,
            screenShareEncoding: screenPreset
                ? screenPreset.encoding
                : { maxBitrate: 3_000_000, maxFramerate: 30 },
            screenShareSimulcastLayers: screenSimulcast,
        },
    });

    const tiles = new Map();
    let micEnabled = true;
    let camEnabled = true;
    let screenTrack = null;
    let focusMode = false;
    let focusIdentity = null;

    function updateCount() {
        const tileCount = tiles.size;
        const n = Math.max(tileCount, 1 + room.remoteParticipants.size);
        if (countEl) countEl.textContent = String(n);
        if (stageEl) {
            let key = '1';
            if (tileCount <= 1) key = '1';
            else if (tileCount === 2) key = '2';
            else if (tileCount === 3) key = '3';
            else if (tileCount === 4) key = '4';
            else if (tileCount === 5) key = '5';
            else if (tileCount === 6) key = '6';
            else key = 'many';
            stageEl.dataset.count = key;
        }
    }

    function setQuality(q) {
        if (!qualityEl) return;
        qualityEl.classList.remove('is-good', 'is-ok', 'is-bad');
        let label = 'جودة الاتصال';
        let cls = 'is-ok';
        if (q === ConnectionQuality.Excellent || q === 'excellent') {
            label = 'ممتازة';
            cls = 'is-good';
        } else if (q === ConnectionQuality.Good || q === 'good') {
            label = 'جيدة';
            cls = 'is-good';
        } else if (q === ConnectionQuality.Poor || q === 'poor') {
            label = 'ضعيفة';
            cls = 'is-bad';
        } else if (q === ConnectionQuality.Lost || q === 'lost') {
            label = 'منقطعة';
            cls = 'is-bad';
        }
        qualityEl.classList.add(cls);
        const span = qualityEl.querySelector('span');
        if (span) span.textContent = label;
    }

    function ensureFocusOthers() {
        let strip = stageEl.querySelector('.lk-focus-others');
        if (!strip) {
            strip = document.createElement('div');
            strip.className = 'lk-focus-others';
            stageEl.appendChild(strip);
        }
        return strip;
    }

    function refreshFocusClasses() {
        root.classList.toggle('lk-focus-mode', focusMode);
        focusBtn?.classList.toggle('is-active', focusMode);

        const strip = ensureFocusOthers();
        strip.style.display = focusMode ? 'flex' : 'none';

        tiles.forEach((tile, identity) => {
            const isFocus = focusMode && identity === focusIdentity;
            tile.classList.toggle('is-focus', isFocus);
            if (focusMode) {
                if (isFocus) {
                    stageEl.insertBefore(tile, strip);
                } else {
                    strip.appendChild(tile);
                }
            } else if (tile.parentElement !== stageEl) {
                stageEl.insertBefore(tile, strip);
            }
        });
        updateCount();
    }

    function ensureTile(identity, label, isScreen) {
        let tile = tiles.get(identity);
        if (!tile) {
            tile = document.createElement('div');
            tile.className = 'lk-tile';
            tile.dataset.identity = identity;
            tile.innerHTML = '<div class="lk-tile-label"><span></span><i class="fas fa-microphone-slash text-rose-300 hidden" data-muted></i></div>';
            tile.addEventListener('click', () => {
                if (!focusMode) {
                    focusMode = true;
                    focusIdentity = identity;
                } else if (focusIdentity === identity) {
                    focusMode = false;
                    focusIdentity = null;
                } else {
                    focusIdentity = identity;
                }
                refreshFocusClasses();
            });
            stageEl.appendChild(tile);
            tiles.set(identity, tile);
        }
        tile.classList.toggle('is-screen', !!isScreen);
        const nameEl = tile.querySelector('.lk-tile-label span');
        if (nameEl && label) nameEl.textContent = label;
        updateCount();
        refreshFocusClasses();
        return tile;
    }

    function attachTrack(track, identity, label) {
        const isScreen = track.source === Track.Source.ScreenShare
            || track.source === Track.Source.ScreenShareAudio;
        const tile = ensureTile(identity, label, isScreen && track.kind === 'video');
        let el = tile.querySelector(track.kind === 'video' ? 'video' : 'audio');
        if (!el) {
            el = document.createElement(track.kind === 'video' ? 'video' : 'audio');
            el.autoplay = true;
            el.playsInline = true;
            if (track.kind === 'audio') el.style.display = 'none';
            if (identity === room.localParticipant?.identity && track.kind === 'video') {
                el.muted = true;
            }
            tile.insertBefore(el, tile.firstChild);
        }
        track.attach(el);
        if (isScreen && track.kind === 'video') {
            focusMode = true;
            focusIdentity = identity;
            refreshFocusClasses();
        }
    }

    function detachTrack(track, identity) {
        const tile = tiles.get(identity);
        if (!tile) return;
        track.detach().forEach((el) => el.remove());
        const hasMedia = tile.querySelector('video, audio');
        if (!hasMedia) {
            tile.remove();
            tiles.delete(identity);
            if (focusIdentity === identity) {
                focusMode = false;
                focusIdentity = null;
            }
            updateCount();
            refreshFocusClasses();
        }
    }

    room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
        attachTrack(track, participant.identity, participant.name || participant.identity);
        updateCount();
    });
    room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
        detachTrack(track, participant.identity);
    });
    room.on(RoomEvent.LocalTrackPublished, (publication, participant) => {
        const track = publication.track;
        if (track) attachTrack(track, participant.identity, (participant.name || participant.identity) + ' (أنت)');
    });
    room.on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
        if (publication.track) detachTrack(publication.track, participant.identity);
    });
    room.on(RoomEvent.ParticipantConnected, updateCount);
    room.on(RoomEvent.ParticipantDisconnected, (participant) => {
        const tile = tiles.get(participant.identity);
        if (tile) {
            tile.remove();
            tiles.delete(participant.identity);
        }
        updateCount();
    });
    room.on(RoomEvent.ActiveSpeakersChanged, (speakers) => {
        const active = new Set(speakers.map((s) => s.identity));
        tiles.forEach((tile, identity) => {
            tile.classList.toggle('is-speaking', active.has(identity));
        });
    });
    room.on(RoomEvent.ConnectionQualityChanged, (quality, participant) => {
        if (!participant || participant.isLocal) setQuality(quality);
    });
    room.on(RoomEvent.Disconnected, () => {
        @if(!empty($livekitOnLeftJs))
        try { {!! $livekitOnLeftJs !!} } catch (e) {}
        @endif
    });

    async function fetchToken() {
        const body = Object.assign({ _token: csrf }, extraBody || {});
        const participantToken = currentParticipantToken();
        if (participantToken) body.token = participantToken;
        const res = await fetch(tokenUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (res.status === 419) {
            throw new Error('انتهت صلاحية الجلسة. حدّث الصفحة ثم ادخل مرة أخرى.');
        }
        if (!res.ok || !data.ok || !data.token || !data.url) {
            const msg = data.message || '';
            if (/csrf/i.test(msg)) {
                throw new Error('انتهت صلاحية الجلسة. حدّث الصفحة ثم ادخل مرة أخرى.');
            }
            throw new Error(msg || 'تعذر إصدار توكن الغرفة.');
        }
        return data;
    }

    async function connect() {
        try {
            setStatus('جاري إصدار التوكن…', false);
            const data = await fetchToken();
            setStatus('جاري الاتصال بجودة عالية…', false);
            await room.connect(data.url, data.token);

            const wantMic = !(data.mute_on_join === true);
            const wantCam = !(data.video_off_on_join === true);
            micEnabled = wantMic;
            camEnabled = wantCam;

            try {
                const localTracks = await createLocalTracks({
                    audio: wantMic ? {
                        echoCancellation: true,
                        noiseSuppression: true,
                        autoGainControl: true,
                    } : false,
                    video: wantCam ? {
                        resolution: camRes ? camRes.resolution : { width: 1920, height: 1080, frameRate: 30 },
                        facingMode: 'user',
                    } : false,
                });
                for (const t of localTracks) {
                    await room.localParticipant.publishTrack(t, {
                        source: t.kind === 'video' ? Track.Source.Camera : Track.Source.Microphone,
                        videoEncoding: t.kind === 'video'
                            ? (camRes ? camRes.encoding : { maxBitrate: 3_500_000, maxFramerate: 30 })
                            : undefined,
                        videoCodec: t.kind === 'video' ? 'vp9' : undefined,
                        simulcast: t.kind === 'video',
                    });
                }
            } catch (mediaErr) {
                console.warn('Local media unavailable', mediaErr);
            }

            micBtn?.classList.toggle('is-off', !micEnabled);
            camBtn?.classList.toggle('is-off', !camEnabled);
            updateCount();
            hideStatus();

            window.__sanaLiveKitRoom = room;
            @if(!empty($livekitOnReadyJs))
            try { {!! $livekitOnReadyJs !!} } catch (e) {}
            @endif
        } catch (e) {
            console.error(e);
            setStatus(e.message || 'فشل الاتصال بغرفة البث.', true);
        }
    }

    micBtn?.addEventListener('click', async () => {
        micEnabled = !micEnabled;
        await room.localParticipant.setMicrophoneEnabled(micEnabled);
        micBtn.classList.toggle('is-off', !micEnabled);
    });
    camBtn?.addEventListener('click', async () => {
        camEnabled = !camEnabled;
        await room.localParticipant.setCameraEnabled(camEnabled, {
            resolution: camRes ? camRes.resolution : { width: 1920, height: 1080, frameRate: 30 },
        });
        camBtn.classList.toggle('is-off', !camEnabled);
    });
    screenBtn?.addEventListener('click', async () => {
        try {
            if (screenTrack) {
                await room.localParticipant.unpublishTrack(screenTrack);
                screenTrack.stop();
                screenTrack = null;
                screenBtn.classList.remove('is-active');
                return;
            }
            const tracks = await createLocalScreenTracks({
                audio: true,
                resolution: screenPreset
                    ? screenPreset.resolution
                    : { width: 1920, height: 1080, frameRate: 30 },
            });
            screenTrack = tracks[0];
            await room.localParticipant.publishTrack(screenTrack, {
                source: Track.Source.ScreenShare,
                screenShareEncoding: screenPreset
                    ? screenPreset.encoding
                    : { maxBitrate: 3_000_000, maxFramerate: 30 },
                simulcast: true,
            });
            for (const extra of tracks.slice(1)) {
                await room.localParticipant.publishTrack(extra, { source: Track.Source.ScreenShareAudio });
            }
            screenBtn.classList.add('is-active');
            screenTrack.on('ended', async () => {
                try { await room.localParticipant.unpublishTrack(screenTrack); } catch (e) {}
                screenTrack = null;
                screenBtn.classList.remove('is-active');
            });
        } catch (e) {
            console.warn(e);
            alert('تعذر مشاركة الشاشة.');
        }
    });
    focusBtn?.addEventListener('click', () => {
        focusMode = !focusMode;
        if (focusMode && !focusIdentity) {
            const first = tiles.keys().next().value;
            focusIdentity = first || room.localParticipant?.identity || null;
        }
        if (!focusMode) focusIdentity = null;
        refreshFocusClasses();
    });
    fsBtn?.addEventListener('click', async () => {
        try {
            if (!document.fullscreenElement) {
                await root.requestFullscreen();
            } else {
                await document.exitFullscreen();
            }
        } catch (e) {
            console.warn(e);
        }
    });
    leaveBtn?.addEventListener('click', async () => {
        await room.disconnect();
        @if(!empty($livekitOnLeftJs))
        try { {!! $livekitOnLeftJs !!} } catch (e) {}
        @endif
    });

    window.SanaLiveKit = {
        connect,
        disconnect: () => room.disconnect(),
        getRoom: () => room,
    };

    if (autoConnect) {
        connect();
    }
})();
</script>

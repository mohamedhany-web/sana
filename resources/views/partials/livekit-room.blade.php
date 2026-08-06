{{--
  Shared LiveKit room mount.
  Required: $livekitTokenUrl
  Optional: $livekitContainerId, $livekitParticipantToken, $livekitAutoConnect,
            $livekitOnReadyJs, $livekitOnLeftJs, $livekitExtraBody (array|json for POST)
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
        background: #0f172a;
        display: flex;
        flex-direction: column;
    }
    #{{ $lkContainerId }} .lk-stage {
        flex: 1;
        min-height: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 8px;
        padding: 8px;
        overflow: auto;
        align-content: start;
    }
    #{{ $lkContainerId }} .lk-tile {
        position: relative;
        background: #1e293b;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 16 / 10;
        border: 1px solid rgba(148, 163, 184, 0.25);
    }
    #{{ $lkContainerId }} .lk-tile video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #0f172a;
    }
    #{{ $lkContainerId }} .lk-tile-label {
        position: absolute;
        left: 8px;
        bottom: 8px;
        right: 8px;
        font-size: 11px;
        color: #e2e8f0;
        background: rgba(15, 23, 42, 0.7);
        border-radius: 8px;
        padding: 4px 8px;
        pointer-events: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #{{ $lkContainerId }} .lk-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        padding: 10px;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        background: rgba(15, 23, 42, 0.92);
    }
    #{{ $lkContainerId }} .lk-toolbar button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #1e293b;
        color: #e2e8f0;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 14px;
        cursor: pointer;
    }
    #{{ $lkContainerId }} .lk-toolbar button.is-off {
        background: #7f1d1d;
        border-color: #b91c1c;
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
        background: #0f172a;
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
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function currentParticipantToken() {
        if (window.__sanaLiveKitParticipantToken) return window.__sanaLiveKitParticipantToken;
        return participantTokenFromBlade;
    }

    const root = document.getElementById(containerId);
    if (!root) return;

    root.innerHTML = ''
        + '<div class="lk-status" data-lk-status><i class="fas fa-spinner fa-spin text-2xl text-cyan-400"></i><span>جاري الاتصال بغرفة LiveKit…</span></div>'
        + '<div class="lk-stage" data-lk-stage></div>'
        + '<div class="lk-toolbar" data-lk-toolbar>'
        + '  <button type="button" data-lk-mic><i class="fas fa-microphone"></i><span>ميكروفون</span></button>'
        + '  <button type="button" data-lk-cam><i class="fas fa-video"></i><span>كاميرا</span></button>'
        + '  <button type="button" data-lk-screen><i class="fas fa-desktop"></i><span>مشاركة الشاشة</span></button>'
        + '  <button type="button" data-lk-leave class="is-off"><i class="fas fa-phone-slash"></i><span>مغادرة</span></button>'
        + '</div>';

    const statusEl = root.querySelector('[data-lk-status]');
    const stageEl = root.querySelector('[data-lk-stage]');
    const micBtn = root.querySelector('[data-lk-mic]');
    const camBtn = root.querySelector('[data-lk-cam]');
    const screenBtn = root.querySelector('[data-lk-screen]');
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
        setStatus('تعذر تحميل مكتبة LiveKit من الشبكة.', true);
        return;
    }

    const { Room, RoomEvent, Track, createLocalTracks, createLocalScreenTracks } = LivekitClient;
    const room = new Room({ adaptiveStream: true, dynacast: true });
    const tiles = new Map();
    let micEnabled = true;
    let camEnabled = true;
    let screenTrack = null;

    function ensureTile(identity, label) {
        let tile = tiles.get(identity);
        if (tile) return tile;
        tile = document.createElement('div');
        tile.className = 'lk-tile';
        tile.dataset.identity = identity;
        tile.innerHTML = '<div class="lk-tile-label"></div>';
        tile.querySelector('.lk-tile-label').textContent = label || identity;
        stageEl.appendChild(tile);
        tiles.set(identity, tile);
        return tile;
    }

    function attachTrack(track, identity, label) {
        const tile = ensureTile(identity, label);
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
    }

    function detachTrack(track, identity) {
        const tile = tiles.get(identity);
        if (!tile) return;
        track.detach().forEach((el) => el.remove());
        const hasMedia = tile.querySelector('video, audio');
        if (!hasMedia) {
            tile.remove();
            tiles.delete(identity);
        }
    }

    room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
        attachTrack(track, participant.identity, participant.name || participant.identity);
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
    room.on(RoomEvent.Disconnected, () => {
        @if(!empty($livekitOnLeftJs))
        try { {!! $livekitOnLeftJs !!} } catch (e) {}
        @endif
    });

    async function fetchToken() {
        const body = Object.assign({}, extraBody || {});
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
        if (!res.ok || !data.ok || !data.token || !data.url) {
            throw new Error(data.message || 'تعذر إصدار توكن الغرفة.');
        }
        return data;
    }

    async function connect() {
        try {
            setStatus('جاري إصدار التوكن…', false);
            const data = await fetchToken();
            setStatus('جاري الاتصال بـ LiveKit…', false);
            await room.connect(data.url, data.token);

            const wantMic = !(data.mute_on_join === true);
            const wantCam = !(data.video_off_on_join === true);
            micEnabled = wantMic;
            camEnabled = wantCam;

            try {
                const localTracks = await createLocalTracks({
                    audio: wantMic,
                    video: wantCam,
                });
                await Promise.all(localTracks.map((t) => room.localParticipant.publishTrack(t)));
            } catch (mediaErr) {
                console.warn('Local media unavailable', mediaErr);
            }

            micBtn?.classList.toggle('is-off', !micEnabled);
            camBtn?.classList.toggle('is-off', !camEnabled);
            hideStatus();

            window.__sanaLiveKitRoom = room;
            @if(!empty($livekitOnReadyJs))
            try { {!! $livekitOnReadyJs !!} } catch (e) {}
            @endif
        } catch (e) {
            console.error(e);
            setStatus(e.message || 'فشل الاتصال بغرفة LiveKit.', true);
        }
    }

    micBtn?.addEventListener('click', async () => {
        micEnabled = !micEnabled;
        await room.localParticipant.setMicrophoneEnabled(micEnabled);
        micBtn.classList.toggle('is-off', !micEnabled);
    });
    camBtn?.addEventListener('click', async () => {
        camEnabled = !camEnabled;
        await room.localParticipant.setCameraEnabled(camEnabled);
        camBtn.classList.toggle('is-off', !camEnabled);
    });
    screenBtn?.addEventListener('click', async () => {
        try {
            if (screenTrack) {
                await room.localParticipant.unpublishTrack(screenTrack);
                screenTrack.stop();
                screenTrack = null;
                screenBtn.classList.remove('is-off');
                return;
            }
            const tracks = await createLocalScreenTracks({ audio: true });
            screenTrack = tracks[0];
            await room.localParticipant.publishTrack(screenTrack);
            screenBtn.classList.add('is-off');
            screenTrack.on('ended', async () => {
                try { await room.localParticipant.unpublishTrack(screenTrack); } catch (e) {}
                screenTrack = null;
                screenBtn.classList.remove('is-off');
            });
        } catch (e) {
            console.warn(e);
            alert('تعذر مشاركة الشاشة.');
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

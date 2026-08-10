{{--
  Shared LiveKit room — high quality + participants panel + settings + chat.
  Required: $livekitTokenUrl
  Optional: $livekitContainerId, $livekitParticipantToken, $livekitAutoConnect,
            $livekitOnReadyJs, $livekitOnLeftJs, $livekitExtraBody, $livekitInviteUrl
--}}
@php
    $lkContainerId = $livekitContainerId ?? 'livekit-room-root';
    $lkAuto = ($livekitAutoConnect ?? true) ? 'true' : 'false';
    $lkParticipantToken = $livekitParticipantToken ?? null;
    $lkExtraBody = $livekitExtraBody ?? [];
    $lkInviteUrl = $livekitInviteUrl ?? null;
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
    #{{ $lkContainerId }} .lk-shell {
        flex: 1;
        min-height: 0;
        display: flex;
        position: relative;
        overflow: hidden;
    }
    #{{ $lkContainerId }} .lk-main {
        flex: 1;
        min-width: 0;
        min-height: 0;
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
        cursor: pointer;
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
    #{{ $lkContainerId }} .lk-stage[data-count="1"] { grid-template-columns: 1fr; grid-template-rows: 1fr; }
    #{{ $lkContainerId }} .lk-stage[data-count="2"] { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr; }
    @media (max-width: 700px) {
        #{{ $lkContainerId }} .lk-stage[data-count="2"] { grid-template-columns: 1fr; grid-template-rows: 1fr 1fr; }
    }
    #{{ $lkContainerId }} .lk-stage[data-count="3"],
    #{{ $lkContainerId }} .lk-stage[data-count="4"] { grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; }
    #{{ $lkContainerId }} .lk-stage[data-count="5"],
    #{{ $lkContainerId }} .lk-stage[data-count="6"] { grid-template-columns: repeat(3, minmax(0, 1fr)); grid-template-rows: 1fr 1fr; }
    @media (max-width: 900px) {
        #{{ $lkContainerId }} .lk-stage[data-count="5"],
        #{{ $lkContainerId }} .lk-stage[data-count="6"] { grid-template-columns: 1fr 1fr; grid-template-rows: repeat(3, minmax(0, 1fr)); }
    }
    #{{ $lkContainerId }} .lk-stage[data-count="many"] {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        grid-auto-rows: minmax(120px, 1fr);
        overflow: auto;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-stage {
        display: flex !important;
        flex-direction: column;
        overflow: hidden;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-tile.is-focus {
        flex: 1 1 auto;
        min-height: 0 !important;
        width: 100%;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-focus-others {
        flex: 0 0 auto;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        max-height: 96px;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-focus-others .lk-tile {
        flex: 0 0 140px;
        width: 140px;
        height: 88px;
    }
    #{{ $lkContainerId }} .lk-tile {
        position: relative;
        background: linear-gradient(160deg, #0f172a, #1e293b);
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        min-height: 0;
        width: 100%;
        height: 100%;
        border: 1px solid rgba(148, 163, 184, 0.22);
        transition: border-color .15s, box-shadow .15s;
    }
    #{{ $lkContainerId }} .lk-tile.is-speaking {
        border-color: rgba(34, 211, 238, 0.75);
        box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.25);
    }
    #{{ $lkContainerId }} .lk-tile.is-hand {
        border-color: rgba(251, 191, 36, 0.8);
    }
    #{{ $lkContainerId }} .lk-tile video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #020617;
    }
    #{{ $lkContainerId }} .lk-tile.is-cam-off video {
        display: none;
    }
    #{{ $lkContainerId }} .lk-tile.is-screen video {
        object-fit: contain;
        background: #000;
        display: block;
    }
    #{{ $lkContainerId }} .lk-avatar {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        background: radial-gradient(circle at 50% 40%, #1e293b 0%, #0f172a 70%);
        z-index: 1;
    }
    #{{ $lkContainerId }} .lk-tile.is-cam-off:not(.is-screen) .lk-avatar {
        display: flex;
    }
    #{{ $lkContainerId }} .lk-avatar-circle {
        width: min(28%, 96px);
        aspect-ratio: 1;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0891b2, #4f46e5);
        color: #fff;
        font-weight: 800;
        font-size: clamp(1rem, 3vw, 1.75rem);
        box-shadow: 0 10px 30px rgba(8, 145, 178, 0.35);
    }
    #{{ $lkContainerId }} .lk-avatar-name {
        color: #e2e8f0;
        font-size: 13px;
        font-weight: 600;
        max-width: 80%;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #{{ $lkContainerId }} .lk-tile-label {
        position: absolute;
        left: 8px;
        bottom: 8px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: calc(100% - 16px);
        font-size: 11px;
        color: #e2e8f0;
        background: rgba(2, 6, 23, 0.75);
        border-radius: 999px;
        padding: 4px 10px;
        pointer-events: none;
        backdrop-filter: blur(6px);
    }
    #{{ $lkContainerId }} .lk-tile-label span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #{{ $lkContainerId }} .lk-tile.is-cam-off:not(.is-screen) .lk-tile-label {
        display: none; /* الاسم يظهر في الأفاتار فقط — مش مرتين */
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
    #{{ $lkContainerId }} .lk-toolbar button.is-off { background: #7f1d1d; border-color: #b91c1c; }
    #{{ $lkContainerId }} .lk-toolbar button.is-active { background: #0e7490; border-color: #06b6d4; }
    @media (max-width: 640px) {
        #{{ $lkContainerId }} .lk-toolbar button span { display: none; }
        #{{ $lkContainerId }} .lk-toolbar button { padding: 10px 12px; }
    }
    #{{ $lkContainerId }} .lk-status {
        position: absolute; inset: 0; z-index: 5;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; color: #94a3b8; font-size: 14px; background: #020617; text-align: center; padding: 1.5rem;
    }
    #{{ $lkContainerId }} .lk-status.is-hidden { display: none; }
    #{{ $lkContainerId }} .lk-drawer {
        width: min(340px, 92vw);
        max-width: 100%;
        background: #0f172a;
        border-inline-start: 1px solid rgba(148, 163, 184, 0.2);
        display: none;
        flex-direction: column;
        min-height: 0;
        z-index: 4;
    }
    #{{ $lkContainerId }} .lk-drawer.is-open { display: flex; }
    #{{ $lkContainerId }} .lk-drawer-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 14px; border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        color: #e2e8f0; font-weight: 700; font-size: 14px; flex-shrink: 0;
    }
    #{{ $lkContainerId }} .lk-drawer-head button {
        border: 0; background: transparent; color: #94a3b8; cursor: pointer; font-size: 16px;
    }
    #{{ $lkContainerId }} .lk-drawer-body {
        flex: 1; min-height: 0; overflow: auto; padding: 12px;
    }
    #{{ $lkContainerId }} .lk-person {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 10px; border-radius: 10px; margin-bottom: 6px;
        background: rgba(30, 41, 59, 0.55); color: #e2e8f0; font-size: 13px;
    }
    #{{ $lkContainerId }} .lk-person-av {
        width: 34px; height: 34px; border-radius: 999px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #0e7490; color: #fff; font-weight: 700; font-size: 12px;
    }
    #{{ $lkContainerId }} .lk-person-meta { flex: 1; min-width: 0; }
    #{{ $lkContainerId }} .lk-person-meta strong { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #{{ $lkContainerId }} .lk-person-meta small { color: #94a3b8; font-size: 11px; }
    #{{ $lkContainerId }} .lk-person-flags { display: flex; gap: 6px; color: #94a3b8; font-size: 12px; }
    #{{ $lkContainerId }} .lk-person-flags .on { color: #67e8f9; }
    #{{ $lkContainerId }} .lk-person-flags .warn { color: #fbbf24; }
    #{{ $lkContainerId }} .lk-field { margin-bottom: 12px; }
    #{{ $lkContainerId }} .lk-field label { display: block; color: #94a3b8; font-size: 11px; margin-bottom: 4px; }
    #{{ $lkContainerId }} .lk-field select,
    #{{ $lkContainerId }} .lk-field input {
        width: 100%; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.25);
        background: #1e293b; color: #e2e8f0; padding: 8px 10px; font-size: 13px;
    }
    #{{ $lkContainerId }} .lk-chat-log {
        display: flex; flex-direction: column; gap: 8px; min-height: 140px; max-height: 46vh; overflow: auto;
        margin-bottom: 10px;
    }
    #{{ $lkContainerId }} .lk-chat-msg {
        background: rgba(30, 41, 59, 0.7); border-radius: 10px; padding: 8px 10px; color: #e2e8f0; font-size: 12px;
    }
    #{{ $lkContainerId }} .lk-chat-msg b { color: #67e8f9; }
    #{{ $lkContainerId }} .lk-chat-form { display: flex; gap: 6px; }
    #{{ $lkContainerId }} .lk-chat-form input { flex: 1; }
    #{{ $lkContainerId }} .lk-chat-form button {
        border: 0; border-radius: 10px; background: #0891b2; color: #fff; padding: 0 12px; font-weight: 700; cursor: pointer;
    }
    #{{ $lkContainerId }} .lk-toast {
        position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
        z-index: 6; background: rgba(15, 23, 42, 0.95); color: #e2e8f0;
        border: 1px solid rgba(148, 163, 184, 0.25); border-radius: 999px;
        padding: 8px 14px; font-size: 12px; display: none;
    }
    #{{ $lkContainerId }} .lk-toast.is-on { display: block; }
    @media (max-width: 820px) {
        #{{ $lkContainerId }} .lk-drawer {
            position: absolute; inset-inline-end: 0; top: 0; bottom: 0;
            box-shadow: -12px 0 40px rgba(0,0,0,.45);
        }
    }
</style>

<script type="module">
(async function () {
    const containerId = @json($lkContainerId);
    const tokenUrl = @json($livekitTokenUrl);
    const participantTokenFromBlade = @json($lkParticipantToken);
    const autoConnect = {{ $lkAuto }};
    const extraBody = @json($lkExtraBody);
    const inviteUrl = @json($lkInviteUrl);
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
        + '<div class="lk-toast" data-lk-toast></div>'
        + '<div class="lk-shell">'
        + '  <div class="lk-main">'
        + '    <div class="lk-meta" data-lk-meta>'
        + '      <span class="lk-meta-pill" data-lk-quality><i class="fas fa-signal"></i><span>جودة الاتصال</span></span>'
        + '      <span class="lk-meta-pill" data-lk-count title="المشاركون"><i class="fas fa-users"></i><span>0</span></span>'
        + '    </div>'
        + '    <div class="lk-stage" data-lk-stage data-count="1"></div>'
        + '  </div>'
        + '  <aside class="lk-drawer" data-lk-people>'
        + '    <div class="lk-drawer-head"><span>المشاركون</span><button type="button" data-lk-close-people aria-label="إغلاق">&times;</button></div>'
        + '    <div class="lk-drawer-body" data-lk-people-list></div>'
        + '  </aside>'
        + '  <aside class="lk-drawer" data-lk-settings>'
        + '    <div class="lk-drawer-head"><span>إعدادات الميتينج</span><button type="button" data-lk-close-settings aria-label="إغلاق">&times;</button></div>'
        + '    <div class="lk-drawer-body">'
        + '      <div class="lk-field"><label>الميكروفون</label><select data-lk-mic-device></select></div>'
        + '      <div class="lk-field"><label>الكاميرا</label><select data-lk-cam-device></select></div>'
        + '      <div class="lk-field"><label>السماعات</label><select data-lk-spk-device></select></div>'
        + '      <div class="lk-field"><label>جودة الكاميرا</label>'
        + '        <select data-lk-quality-preset>'
        + '          <option value="1080">عالية جداً (1080p)</option>'
        + '          <option value="720">عالية (720p)</option>'
        + '          <option value="540">متوسطة (540p)</option>'
        + '        </select>'
        + '      </div>'
        + (inviteUrl ? '      <div class="lk-field"><label>رابط الدعوة</label><button type="button" data-lk-copy-invite class="lk-meta-pill" style="width:100%;justify-content:center;border:0;cursor:pointer">نسخ رابط الدخول</button></div>' : '')
        + '      <p style="color:#64748b;font-size:11px;line-height:1.6;margin:0">LiveKit: Simulcast + Dynacast + إلغاء صدى وضوضاء + مشاركة شاشة عالية الجودة.</p>'
        + '    </div>'
        + '  </aside>'
        + '  <aside class="lk-drawer" data-lk-chat>'
        + '    <div class="lk-drawer-head"><span>الدردشة</span><button type="button" data-lk-close-chat aria-label="إغلاق">&times;</button></div>'
        + '    <div class="lk-drawer-body">'
        + '      <div class="lk-chat-log" data-lk-chat-log></div>'
        + '      <form class="lk-chat-form" data-lk-chat-form>'
        + '        <input type="text" maxlength="400" placeholder="اكتب رسالة…" data-lk-chat-input>'
        + '        <button type="submit">إرسال</button>'
        + '      </form>'
        + '    </div>'
        + '  </aside>'
        + '</div>'
        + '<div class="lk-toolbar" data-lk-toolbar>'
        + '  <button type="button" data-lk-mic><i class="fas fa-microphone"></i><span>ميكروفون</span></button>'
        + '  <button type="button" data-lk-cam><i class="fas fa-video"></i><span>كاميرا</span></button>'
        + '  <button type="button" data-lk-screen><i class="fas fa-desktop"></i><span>شاشة</span></button>'
        + '  <button type="button" data-lk-hand><i class="fas fa-hand"></i><span>رفع يد</span></button>'
        + '  <button type="button" data-lk-people-btn><i class="fas fa-users"></i><span>المشاركون</span></button>'
        + '  <button type="button" data-lk-chat-btn><i class="fas fa-comments"></i><span>دردشة</span></button>'
        + '  <button type="button" data-lk-settings-btn><i class="fas fa-gear"></i><span>إعدادات</span></button>'
        + '  <button type="button" data-lk-focus><i class="fas fa-expand"></i><span>تركيز</span></button>'
        + '  <button type="button" data-lk-fs><i class="fas fa-up-right-and-down-left-from-center"></i><span>ملء</span></button>'
        + '  <button type="button" data-lk-leave class="is-off"><i class="fas fa-phone-slash"></i><span>مغادرة</span></button>'
        + '</div>';

    const statusEl = root.querySelector('[data-lk-status]');
    const toastEl = root.querySelector('[data-lk-toast]');
    const stageEl = root.querySelector('[data-lk-stage]');
    const qualityEl = root.querySelector('[data-lk-quality]');
    const countEl = root.querySelector('[data-lk-count] span');
    const peopleDrawer = root.querySelector('[data-lk-people]');
    const settingsDrawer = root.querySelector('[data-lk-settings]');
    const chatDrawer = root.querySelector('[data-lk-chat]');
    const peopleList = root.querySelector('[data-lk-people-list]');
    const chatLog = root.querySelector('[data-lk-chat-log]');
    const chatForm = root.querySelector('[data-lk-chat-form]');
    const chatInput = root.querySelector('[data-lk-chat-input]');
    const micDeviceSel = root.querySelector('[data-lk-mic-device]');
    const camDeviceSel = root.querySelector('[data-lk-cam-device]');
    const spkDeviceSel = root.querySelector('[data-lk-spk-device]');
    const qualityPresetSel = root.querySelector('[data-lk-quality-preset]');
    const micBtn = root.querySelector('[data-lk-mic]');
    const camBtn = root.querySelector('[data-lk-cam]');
    const screenBtn = root.querySelector('[data-lk-screen]');
    const handBtn = root.querySelector('[data-lk-hand]');
    const peopleBtn = root.querySelector('[data-lk-people-btn]');
    const chatBtn = root.querySelector('[data-lk-chat-btn]');
    const settingsBtn = root.querySelector('[data-lk-settings-btn]');
    const focusBtn = root.querySelector('[data-lk-focus]');
    const fsBtn = root.querySelector('[data-lk-fs]');
    const leaveBtn = root.querySelector('[data-lk-leave]');
    const countPill = root.querySelector('[data-lk-count]');

    function setStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.classList.remove('is-hidden');
        statusEl.innerHTML = (isError
            ? '<i class="fas fa-exclamation-triangle text-2xl text-amber-400"></i>'
            : '<i class="fas fa-spinner fa-spin text-2xl text-cyan-400"></i>')
            + '<span>' + msg + '</span>';
    }
    function hideStatus() { statusEl?.classList.add('is-hidden'); }
    function toast(msg) {
        if (!toastEl) return;
        toastEl.textContent = msg;
        toastEl.classList.add('is-on');
        clearTimeout(toastEl._t);
        toastEl._t = setTimeout(() => toastEl.classList.remove('is-on'), 2600);
    }
    function closeDrawers() {
        peopleDrawer?.classList.remove('is-open');
        settingsDrawer?.classList.remove('is-open');
        chatDrawer?.classList.remove('is-open');
        peopleBtn?.classList.remove('is-active');
        settingsBtn?.classList.remove('is-active');
        chatBtn?.classList.remove('is-active');
    }
    function openDrawer(which) {
        const map = { people: [peopleDrawer, peopleBtn], settings: [settingsDrawer, settingsBtn], chat: [chatDrawer, chatBtn] };
        const wasOpen = map[which]?.[0]?.classList.contains('is-open');
        closeDrawers();
        if (!wasOpen && map[which]) {
            map[which][0].classList.add('is-open');
            map[which][1]?.classList.add('is-active');
        }
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
        Room, RoomEvent, Track, VideoPresets, ScreenSharePresets, ConnectionQuality,
        createLocalTracks, createLocalScreenTracks, DataPacket_Kind,
    } = LivekitClient;

    const presetMap = {
        '1080': VideoPresets?.h1080 || null,
        '720': VideoPresets?.h720 || null,
        '540': VideoPresets?.h540 || VideoPresets?.h720 || null,
    };
    let camRes = presetMap['1080'] || presetMap['720'];
    const camSimulcast = [VideoPresets?.h180, VideoPresets?.h360, VideoPresets?.h720].filter(Boolean);
    const screenPreset = ScreenSharePresets?.h1080fps30 || ScreenSharePresets?.h1080fps15 || null;
    const screenSimulcast = [ScreenSharePresets?.h720fps15, ScreenSharePresets?.h360fps15].filter(Boolean);
    const encoder = new TextEncoder();
    const decoder = new TextDecoder();

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
            videoCodec: 'vp9',
            videoEncoding: camRes ? camRes.encoding : { maxBitrate: 3_500_000, maxFramerate: 30 },
            videoSimulcastLayers: camSimulcast,
            screenShareEncoding: screenPreset ? screenPreset.encoding : { maxBitrate: 3_000_000, maxFramerate: 30 },
            screenShareSimulcastLayers: screenSimulcast,
        },
    });

    const tiles = new Map();
    const hands = new Set();
    let micEnabled = true;
    let camEnabled = true;
    let handRaised = false;
    let screenTrack = null;
    let focusMode = false;
    let focusIdentity = null;

    function initials(name) {
        const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!parts.length) return '?';
        if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }

    function participantCamOn(p) {
        if (!p) return false;
        try {
            return !!p.isCameraEnabled;
        } catch (e) {
            return false;
        }
    }
    function participantMicOn(p) {
        if (!p) return false;
        try {
            return !!p.isMicrophoneEnabled;
        } catch (e) {
            return false;
        }
    }

    function updateCount() {
        const n = 1 + room.remoteParticipants.size;
        if (countEl) countEl.textContent = String(n);
        const tileCount = tiles.size;
        let key = '1';
        if (tileCount === 2) key = '2';
        else if (tileCount === 3) key = '3';
        else if (tileCount === 4) key = '4';
        else if (tileCount === 5) key = '5';
        else if (tileCount === 6) key = '6';
        else if (tileCount > 6) key = 'many';
        if (stageEl) stageEl.dataset.count = key;
    }

    function setQuality(q) {
        if (!qualityEl) return;
        qualityEl.classList.remove('is-good', 'is-ok', 'is-bad');
        let label = 'جودة الاتصال', cls = 'is-ok';
        if (q === ConnectionQuality.Excellent || q === 'excellent') { label = 'ممتازة'; cls = 'is-good'; }
        else if (q === ConnectionQuality.Good || q === 'good') { label = 'جيدة'; cls = 'is-good'; }
        else if (q === ConnectionQuality.Poor || q === 'poor') { label = 'ضعيفة'; cls = 'is-bad'; }
        else if (q === ConnectionQuality.Lost || q === 'lost') { label = 'منقطعة'; cls = 'is-bad'; }
        qualityEl.classList.add(cls);
        const span = qualityEl.querySelector('span');
        if (span) span.textContent = label;
    }

    function ensureFocusOthers() {
        let strip = stageEl.querySelector('.lk-focus-others');
        if (!strip) {
            strip = document.createElement('div');
            strip.className = 'lk-focus-others';
            strip.style.display = 'none';
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
                if (isFocus) stageEl.insertBefore(tile, strip);
                else strip.appendChild(tile);
            } else if (tile.parentElement !== stageEl) {
                stageEl.insertBefore(tile, strip);
            }
        });
        updateCount();
    }

    function syncTileMediaState(identity) {
        const tile = tiles.get(identity);
        if (!tile) return;
        let p = null;
        if (identity === room.localParticipant?.identity) {
            p = room.localParticipant;
        } else if (typeof room.getParticipantByIdentity === 'function') {
            p = room.getParticipantByIdentity(identity);
        } else {
            room.remoteParticipants.forEach((rp) => {
                if (rp.identity === identity) p = rp;
            });
        }
        const hasScreenVideo = tile.classList.contains('is-screen') && !!tile.querySelector('video');
        const camOn = participantCamOn(p);
        const reallyCamOff = !hasScreenVideo && !camOn;
        tile.classList.toggle('is-cam-off', reallyCamOff);
        tile.classList.toggle('is-hand', hands.has(identity));
        const mutedIcon = tile.querySelector('[data-muted]');
        if (mutedIcon) mutedIcon.classList.toggle('hidden', participantMicOn(p));
        const v = tile.querySelector('video');
        if (v) v.style.display = (reallyCamOff && !hasScreenVideo) ? 'none' : '';
    }

    function ensureTile(identity, label) {
        let tile = tiles.get(identity);
        if (!tile) {
            tile = document.createElement('div');
            tile.className = 'lk-tile is-cam-off';
            tile.dataset.identity = identity;
            tile.innerHTML = ''
                + '<div class="lk-avatar"><div class="lk-avatar-circle"></div><div class="lk-avatar-name"></div></div>'
                + '<div class="lk-tile-label"><span></span><i class="fas fa-microphone-slash text-rose-300 hidden" data-muted></i></div>';
            tile.addEventListener('click', () => {
                if (!focusMode) { focusMode = true; focusIdentity = identity; }
                else if (focusIdentity === identity) { focusMode = false; focusIdentity = null; }
                else focusIdentity = identity;
                refreshFocusClasses();
            });
            stageEl.appendChild(tile);
            tiles.set(identity, tile);
        }
        const name = label || identity;
        const nameEl = tile.querySelector('.lk-tile-label span');
        const avName = tile.querySelector('.lk-avatar-name');
        const avCircle = tile.querySelector('.lk-avatar-circle');
        if (nameEl) nameEl.textContent = name;
        if (avName) avName.textContent = name;
        if (avCircle) avCircle.textContent = initials(name);
        syncTileMediaState(identity);
        updateCount();
        refreshFocusClasses();
        renderPeople();
        return tile;
    }

    function attachTrack(track, identity, label) {
        const isLocal = identity === room.localParticipant?.identity;
        // لا تشغّل صوت الميكروفون/شاشة المحلي في السماعات — وإلا يسمع المستخدم نفسه (صدى).
        if (isLocal && track.kind === 'audio') {
            ensureTile(identity, label);
            syncTileMediaState(identity);
            renderPeople();
            return;
        }

        const isScreen = track.source === Track.Source.ScreenShare
            || track.source === Track.Source.ScreenShareAudio;
        const tile = ensureTile(identity, label);
        if (isScreen && track.kind === 'video') tile.classList.add('is-screen');
        if (track.kind === 'video' && track.source === Track.Source.Camera) {
            tile.classList.remove('is-cam-off');
        }
        let el = tile.querySelector(track.kind === 'video' ? 'video' : 'audio');
        if (!el) {
            el = document.createElement(track.kind === 'video' ? 'video' : 'audio');
            el.autoplay = true;
            el.playsInline = true;
            if (track.kind === 'audio') el.style.display = 'none';
            // معاينة الكاميرا المحلية بدون تشغيل صوتها عبر مكبرات الصوت
            if (isLocal && track.kind === 'video') el.muted = true;
            tile.insertBefore(el, tile.firstChild);
        }
        if (isLocal && track.kind === 'video') el.muted = true;
        track.attach(el);
        if (track.kind === 'video') el.style.display = '';
        if (isScreen && track.kind === 'video') {
            focusMode = true;
            focusIdentity = identity;
            refreshFocusClasses();
        }
        syncTileMediaState(identity);
        renderPeople();
    }

    function detachTrack(track, identity) {
        const tile = tiles.get(identity);
        if (!tile) return;
        track.detach().forEach((el) => el.remove());
        if (track.source === Track.Source.Camera) {
            tile.classList.add('is-cam-off');
        }
        if (track.source === Track.Source.ScreenShare) {
            tile.classList.remove('is-screen');
            if (focusIdentity === identity) { focusMode = false; focusIdentity = null; }
        }
        const hasMedia = tile.querySelector('video, audio');
        if (!hasMedia && identity !== room.localParticipant?.identity
            && !room.getParticipantByIdentity?.(identity)
            && !room.remoteParticipants.get(identity)) {
            tile.remove();
            tiles.delete(identity);
        }
        syncTileMediaState(identity);
        updateCount();
        refreshFocusClasses();
        renderPeople();
    }

    function allParticipants() {
        const list = [];
        if (room.localParticipant) list.push(room.localParticipant);
        room.remoteParticipants.forEach((p) => list.push(p));
        return list;
    }

    function renderPeople() {
        if (!peopleList) return;
        const rows = allParticipants().map((p) => {
            const name = p.name || p.identity;
            const cam = participantCamOn(p);
            const mic = participantMicOn(p);
            const hand = hands.has(p.identity);
            return '<div class="lk-person">'
                + '<div class="lk-person-av">' + initials(name) + '</div>'
                + '<div class="lk-person-meta"><strong>' + escapeHtml(name)
                + (p.isLocal ? ' (أنت)' : '') + '</strong>'
                + '<small>' + (cam ? 'كاميرا شغالة' : 'الكاميرا مغلقة')
                + (hand ? ' · يد مرفوعة' : '') + '</small></div>'
                + '<div class="lk-person-flags">'
                + '<i class="fas fa-microphone ' + (mic ? 'on' : '') + '"></i>'
                + '<i class="fas fa-video ' + (cam ? 'on' : '') + '"></i>'
                + (hand ? '<i class="fas fa-hand warn"></i>' : '')
                + '</div></div>';
        }).join('');
        peopleList.innerHTML = rows || '<p style="color:#64748b;font-size:12px;text-align:center">لا يوجد مشاركون بعد</p>';
        updateCount();
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function appendChat(name, text) {
        if (!chatLog) return;
        const div = document.createElement('div');
        div.className = 'lk-chat-msg';
        div.innerHTML = '<b>' + escapeHtml(name) + '</b>: ' + escapeHtml(text);
        chatLog.appendChild(div);
        chatLog.scrollTop = chatLog.scrollHeight;
    }

    async function sendPacket(payload) {
        const data = encoder.encode(JSON.stringify(payload));
        const kind = DataPacket_Kind?.RELIABLE ?? 0;
        await room.localParticipant.publishData(data, { reliable: true, kind });
    }

    async function fillDevices() {
        try {
            const mics = await Room.getLocalDevices('audioinput');
            const cams = await Room.getLocalDevices('videoinput');
            const spks = await Room.getLocalDevices('audiooutput');
            const fill = (sel, devices, kind) => {
                if (!sel) return;
                const cur = sel.value;
                sel.innerHTML = devices.map((d) =>
                    '<option value="' + d.deviceId + '">' + escapeHtml(d.label || kind) + '</option>'
                ).join('') || '<option value="">لا يوجد جهاز</option>';
                if (cur) sel.value = cur;
            };
            fill(micDeviceSel, mics, 'ميكروفون');
            fill(camDeviceSel, cams, 'كاميرا');
            fill(spkDeviceSel, spks, 'سماعات');
        } catch (e) {
            console.warn(e);
        }
    }

    room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
        attachTrack(track, participant.identity, participant.name || participant.identity);
    });
    room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
        detachTrack(track, participant.identity);
    });
    room.on(RoomEvent.LocalTrackPublished, (publication, participant) => {
        if (publication.track) {
            attachTrack(publication.track, participant.identity, (participant.name || participant.identity) + ' (أنت)');
        }
        renderPeople();
    });
    room.on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
        if (publication.track) detachTrack(publication.track, participant.identity);
        renderPeople();
    });
    room.on(RoomEvent.TrackMuted, (_pub, participant) => {
        syncTileMediaState(participant.identity);
        renderPeople();
    });
    room.on(RoomEvent.TrackUnmuted, (_pub, participant) => {
        syncTileMediaState(participant.identity);
        renderPeople();
    });
    room.on(RoomEvent.ParticipantConnected, (p) => {
        ensureTile(p.identity, p.name || p.identity);
        toast((p.name || 'مشارك') + ' انضم للاجتماع');
        renderPeople();
    });
    room.on(RoomEvent.ParticipantDisconnected, (p) => {
        const tile = tiles.get(p.identity);
        if (tile) { tile.remove(); tiles.delete(p.identity); }
        hands.delete(p.identity);
        updateCount();
        refreshFocusClasses();
        renderPeople();
    });
    room.on(RoomEvent.ActiveSpeakersChanged, (speakers) => {
        const active = new Set(speakers.map((s) => s.identity));
        tiles.forEach((tile, identity) => tile.classList.toggle('is-speaking', active.has(identity)));
    });
    room.on(RoomEvent.ConnectionQualityChanged, (quality, participant) => {
        if (!participant || participant.isLocal) setQuality(quality);
    });
    room.on(RoomEvent.DataReceived, (payload, participant) => {
        try {
            const msg = JSON.parse(decoder.decode(payload));
            if (msg?.type === 'chat' && msg.text) {
                appendChat(participant?.name || msg.name || 'مشارك', msg.text);
            } else if (msg?.type === 'hand') {
                const id = participant?.identity || msg.identity;
                if (!id) return;
                if (msg.raised) hands.add(id); else hands.delete(id);
                syncTileMediaState(id);
                renderPeople();
                if (msg.raised && id !== room.localParticipant?.identity) {
                    toast((participant?.name || 'مشارك') + ' رفع يده');
                }
            }
        } catch (e) {}
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
        if (res.status === 419) throw new Error('انتهت صلاحية الجلسة. حدّث الصفحة ثم ادخل مرة أخرى.');
        if (!res.ok || !data.ok || !data.token || !data.url) {
            const msg = data.message || '';
            if (/csrf/i.test(msg)) throw new Error('انتهت صلاحية الجلسة. حدّث الصفحة ثم ادخل مرة أخرى.');
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

            const prejoinPrefs = window.__sanaPrejoinPrefs || {};
            const wantMic = !(data.mute_on_join === true || prejoinPrefs.mute_on_join === true);
            const wantCam = !(data.video_off_on_join === true || prejoinPrefs.video_off_on_join === true);
            micEnabled = wantMic;
            camEnabled = wantCam;

            ensureTile(room.localParticipant.identity, (room.localParticipant.name || 'أنت') + ' (أنت)');

            try {
                const localTracks = await createLocalTracks({
                    audio: wantMic ? { echoCancellation: true, noiseSuppression: true, autoGainControl: true } : false,
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

            // participants already in room
            room.remoteParticipants.forEach((p) => ensureTile(p.identity, p.name || p.identity));

            micBtn?.classList.toggle('is-off', !micEnabled);
            camBtn?.classList.toggle('is-off', !camEnabled);
            syncTileMediaState(room.localParticipant.identity);
            await fillDevices();
            renderPeople();
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
        syncTileMediaState(room.localParticipant.identity);
        renderPeople();
    });
    camBtn?.addEventListener('click', async () => {
        camEnabled = !camEnabled;
        await room.localParticipant.setCameraEnabled(camEnabled, {
            resolution: camRes ? camRes.resolution : { width: 1920, height: 1080, frameRate: 30 },
        });
        camBtn.classList.toggle('is-off', !camEnabled);
        syncTileMediaState(room.localParticipant.identity);
        renderPeople();
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
                resolution: screenPreset ? screenPreset.resolution : { width: 1920, height: 1080, frameRate: 30 },
            });
            screenTrack = tracks[0];
            await room.localParticipant.publishTrack(screenTrack, {
                source: Track.Source.ScreenShare,
                screenShareEncoding: screenPreset ? screenPreset.encoding : { maxBitrate: 3_000_000, maxFramerate: 30 },
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
    handBtn?.addEventListener('click', async () => {
        handRaised = !handRaised;
        handBtn.classList.toggle('is-active', handRaised);
        const id = room.localParticipant.identity;
        if (handRaised) hands.add(id); else hands.delete(id);
        syncTileMediaState(id);
        renderPeople();
        try {
            await sendPacket({ type: 'hand', raised: handRaised, identity: id });
        } catch (e) {}
    });
    peopleBtn?.addEventListener('click', () => { renderPeople(); openDrawer('people'); });
    countPill?.addEventListener('click', () => { renderPeople(); openDrawer('people'); });
    settingsBtn?.addEventListener('click', async () => { await fillDevices(); openDrawer('settings'); });
    chatBtn?.addEventListener('click', () => openDrawer('chat'));
    root.querySelector('[data-lk-close-people]')?.addEventListener('click', closeDrawers);
    root.querySelector('[data-lk-close-settings]')?.addEventListener('click', closeDrawers);
    root.querySelector('[data-lk-close-chat]')?.addEventListener('click', closeDrawers);
    root.querySelector('[data-lk-copy-invite]')?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(inviteUrl);
            toast('تم نسخ رابط الدخول');
        } catch (e) {
            prompt('انسخ الرابط:', inviteUrl);
        }
    });
    micDeviceSel?.addEventListener('change', async () => {
        if (!micDeviceSel.value) return;
        try { await room.switchActiveDevice('audioinput', micDeviceSel.value); toast('تم تغيير الميكروفون'); } catch (e) { alert('تعذر تغيير الميكروفون'); }
    });
    camDeviceSel?.addEventListener('change', async () => {
        if (!camDeviceSel.value) return;
        try { await room.switchActiveDevice('videoinput', camDeviceSel.value); toast('تم تغيير الكاميرا'); } catch (e) { alert('تعذر تغيير الكاميرا'); }
    });
    spkDeviceSel?.addEventListener('change', async () => {
        if (!spkDeviceSel.value) return;
        try { await room.switchActiveDevice('audiooutput', spkDeviceSel.value); toast('تم تغيير السماعات'); } catch (e) { toast('المتصفح لا يدعم تبديل السماعات'); }
    });
    qualityPresetSel?.addEventListener('change', async () => {
        camRes = presetMap[qualityPresetSel.value] || camRes;
        if (camEnabled) {
            try {
                await room.localParticipant.setCameraEnabled(false);
                await room.localParticipant.setCameraEnabled(true, {
                    resolution: camRes ? camRes.resolution : { width: 1280, height: 720, frameRate: 30 },
                });
                toast('تم تحديث جودة الكاميرا');
            } catch (e) {}
        }
    });
    chatForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = (chatInput?.value || '').trim();
        if (!text) return;
        appendChat(room.localParticipant.name || 'أنت', text);
        chatInput.value = '';
        try {
            await sendPacket({ type: 'chat', text, name: room.localParticipant.name || 'مشارك' });
        } catch (err) {
            toast('تعذر إرسال الرسالة');
        }
    });
    focusBtn?.addEventListener('click', () => {
        focusMode = !focusMode;
        if (focusMode && !focusIdentity) {
            focusIdentity = tiles.keys().next().value || room.localParticipant?.identity || null;
        }
        if (!focusMode) focusIdentity = null;
        refreshFocusClasses();
    });
    fsBtn?.addEventListener('click', async () => {
        try {
            if (!document.fullscreenElement) await root.requestFullscreen();
            else await document.exitFullscreen();
        } catch (e) {}
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

    if (autoConnect) connect();
})();
</script>

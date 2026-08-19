{{--
  Shared LiveKit room — high quality + participants panel + settings + chat.
  Required: $livekitTokenUrl
  Optional: $livekitContainerId, $livekitParticipantToken, $livekitAutoConnect,
            $livekitOnReadyJs, $livekitOnLeftJs, $livekitExtraBody, $livekitInviteUrl,
            $livekitHiddenObserver, $livekitWhiteboard
--}}
@php
    $lkContainerId = $livekitContainerId ?? 'livekit-room-root';
    $lkAuto = ($livekitAutoConnect ?? true) ? 'true' : 'false';
    $lkParticipantToken = $livekitParticipantToken ?? null;
    $lkExtraBody = $livekitExtraBody ?? [];
    $lkInviteUrl = $livekitInviteUrl ?? null;
    $lkHiddenObserver = !empty($livekitHiddenObserver);
    $lkWhiteboard = ($livekitWhiteboard ?? true);
    $lkBp = rtrim((string) request()->getBasePath(), '/');
    $lkExBases = array_values(array_unique(array_filter([
        ($lkBp !== '' ? $lkBp : '') . '/mx-vendor/excalidraw/',
        '/mx-vendor/excalidraw/',
        ($lkBp !== '' ? $lkBp : '') . '/vendor/excalidraw/',
        '/vendor/excalidraw/',
    ])));
@endphp
<style>
    #{{ $lkContainerId }} {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 0;
        max-height: 100%;
        background: #0b0b0b;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        color: #f2f2f2;
    }
    #{{ $lkContainerId }} .lk-shell {
        flex: 1;
        min-height: 0;
        display: flex;
        position: relative;
        overflow: hidden;
        background: #0b0b0b;
    }
    #{{ $lkContainerId }} .lk-main {
        flex: 1;
        min-width: 0;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
    }
    #{{ $lkContainerId }} .lk-meta {
        position: absolute;
        top: 12px;
        inset-inline-start: 12px;
        z-index: 3;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        color: #e8e8e8;
        font-size: 11px;
        pointer-events: none;
    }
    #{{ $lkContainerId }} .lk-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(20, 20, 20, 0.72);
        border: 1px solid rgba(255, 255, 255, 0.08);
        cursor: pointer;
        pointer-events: auto;
        backdrop-filter: blur(10px);
    }
    #{{ $lkContainerId }} .lk-meta-pill.is-good { color: #8fdf9c; }
    #{{ $lkContainerId }} .lk-meta-pill.is-ok { color: #f5c452; }
    #{{ $lkContainerId }} .lk-meta-pill.is-bad { color: #ff8a80; }
    #{{ $lkContainerId }} .lk-stage {
        flex: 1 1 auto;
        min-height: 0;
        display: grid;
        gap: 10px;
        padding: 10px 10px 6px;
        overflow: hidden;
        align-content: stretch;
        grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
        grid-auto-rows: minmax(0, 1fr);
        background: #0b0b0b;
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
    #{{ $lkContainerId }}.lk-pip-1on1:not(.lk-focus-mode) .lk-stage {
        display: block;
        position: relative;
    }
    #{{ $lkContainerId }}.lk-pip-1on1:not(.lk-focus-mode) .lk-tile:not(.is-local) {
        position: absolute;
        inset: 8px;
        width: auto;
        height: auto;
        border-radius: 12px;
    }
    #{{ $lkContainerId }}.lk-pip-1on1:not(.lk-focus-mode) .lk-tile.is-local {
        position: absolute;
        width: min(34vw, 280px);
        height: min(22vw, 158px);
        min-height: 96px;
        bottom: 14px;
        inset-inline-end: 14px;
        z-index: 3;
        border-radius: 10px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    @media (max-width: 640px) {
        #{{ $lkContainerId }}.lk-pip-1on1:not(.lk-focus-mode) .lk-tile.is-local {
            width: 42vw;
            height: 26vw;
            min-height: 86px;
        }
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
        max-height: 104px;
        padding-bottom: 2px;
    }
    #{{ $lkContainerId }}.lk-focus-mode .lk-focus-others .lk-tile {
        flex: 0 0 148px;
        width: 148px;
        height: 88px;
    }
    #{{ $lkContainerId }} .lk-tile {
        position: relative;
        background: #1a1a1a;
        border-radius: 10px;
        overflow: hidden;
        min-width: 0;
        min-height: 0;
        width: 100%;
        height: 100%;
        border: 2px solid transparent;
        transition: border-color .15s, box-shadow .15s;
    }
    #{{ $lkContainerId }} .lk-tile.is-speaking {
        border-color: #2d8a46;
        box-shadow: 0 0 0 1px rgba(45, 138, 70, 0.35);
    }
    #{{ $lkContainerId }} .lk-tile.is-hand {
        border-color: #e6b325;
    }
    #{{ $lkContainerId }} .lk-tile video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #111;
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
        background: radial-gradient(circle at 50% 42%, #2a2a2a 0%, #141414 72%);
        z-index: 1;
    }
    #{{ $lkContainerId }} .lk-tile.is-cam-off:not(.is-screen) .lk-avatar {
        display: flex;
    }
    #{{ $lkContainerId }} .lk-avatar-circle {
        width: min(30%, 108px);
        aspect-ratio: 1;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0e71eb;
        color: #fff;
        font-weight: 700;
        font-size: clamp(1rem, 3vw, 1.8rem);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.35);
    }
    #{{ $lkContainerId }} .lk-avatar-name {
        color: #f0f0f0;
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
        inset-inline-start: 10px;
        bottom: 10px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        max-width: calc(100% - 20px);
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        background: rgba(12, 12, 12, 0.7);
        border-radius: 6px;
        padding: 4px 8px;
        pointer-events: none;
        backdrop-filter: blur(8px);
    }
    #{{ $lkContainerId }} .lk-tile-label span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #{{ $lkContainerId }} .lk-tile.is-cam-off:not(.is-screen) .lk-tile-label {
        display: inline-flex;
    }
    #{{ $lkContainerId }} .lk-toolbar {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid #1a1a1a;
        background: #242424;
        flex-shrink: 0;
    }
    #{{ $lkContainerId }} .lk-toolbar-cluster {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    #{{ $lkContainerId }} .lk-toolbar-cluster--end { margin-inline-start: auto; }
    #{{ $lkContainerId }} .lk-toolbar button {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        min-width: 58px;
        border-radius: 0;
        border: 0;
        background: transparent;
        color: #ececec;
        font-size: 11px;
        font-weight: 500;
        padding: 0;
        cursor: pointer;
    }
    #{{ $lkContainerId }} .lk-toolbar .lk-tb-icon {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #3d3d3d;
        color: #fff;
        font-size: 16px;
        transition: background .15s, transform .12s;
    }
    #{{ $lkContainerId }} .lk-toolbar button:hover .lk-tb-icon { background: #4c4c4c; }
    #{{ $lkContainerId }} .lk-toolbar button.is-off .lk-tb-icon {
        background: #de3939;
        color: #fff;
    }
    #{{ $lkContainerId }} .lk-toolbar button.is-active .lk-tb-icon {
        background: #0e71eb;
    }
    #{{ $lkContainerId }} .lk-toolbar button[data-lk-leave] {
        min-width: auto;
        flex-direction: row;
        gap: 8px;
        background: #de3939;
        color: #fff;
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 700;
        font-size: 13px;
        align-self: center;
    }
    #{{ $lkContainerId }} .lk-toolbar button[data-lk-leave] .lk-tb-icon {
        width: auto;
        height: auto;
        background: transparent;
        font-size: 14px;
    }
    #{{ $lkContainerId }} .lk-toolbar button[data-lk-leave]:hover { background: #c62f2f; }
    @media (max-width: 720px) {
        #{{ $lkContainerId }} .lk-toolbar { justify-content: center; gap: 6px; padding-inline: 8px; }
        #{{ $lkContainerId }} .lk-toolbar .lk-tb-label { display: none; }
        #{{ $lkContainerId }} .lk-toolbar button { min-width: 44px; }
        #{{ $lkContainerId }} .lk-toolbar .lk-tb-icon { width: 42px; height: 42px; }
        #{{ $lkContainerId }} .lk-toolbar button[data-lk-leave] { padding: 9px 12px; }
        #{{ $lkContainerId }} .lk-toolbar button[data-lk-leave] .lk-tb-label { display: none; }
    }
    #{{ $lkContainerId }} .lk-status {
        position: absolute; inset: 0; z-index: 5;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; color: #cfcfcf; font-size: 14px; background: #111; text-align: center; padding: 1.5rem;
    }
    #{{ $lkContainerId }} .lk-status.is-hidden { display: none; }
    #{{ $lkContainerId }} .lk-drawer {
        width: min(360px, 92vw);
        max-width: 100%;
        height: 100%;
        align-self: stretch;
        background: #2d2d2d;
        border-inline-start: 1px solid #1f1f1f;
        display: none;
        flex-direction: column;
        min-height: 0;
        z-index: 4;
        overflow: hidden;
    }
    #{{ $lkContainerId }} .lk-drawer.is-open { display: flex; }
    #{{ $lkContainerId }} .lk-drawer-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px; border-bottom: 1px solid #3a3a3a;
        color: #fff; font-weight: 700; font-size: 15px; flex-shrink: 0;
        background: #242424;
    }
    #{{ $lkContainerId }} .lk-drawer-head button {
        border: 0; background: transparent; color: #bdbdbd; cursor: pointer; font-size: 18px;
    }
    #{{ $lkContainerId }} .lk-drawer-body {
        flex: 1; min-height: 0; overflow: auto; padding: 12px;
        display: flex; flex-direction: column;
    }
    #{{ $lkContainerId }} .lk-person {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 10px; border-radius: 10px; margin-bottom: 6px;
        background: #3a3a3a; color: #f2f2f2; font-size: 13px;
    }
    #{{ $lkContainerId }} .lk-person-av {
        width: 34px; height: 34px; border-radius: 999px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: #0e71eb; color: #fff; font-weight: 700; font-size: 12px;
    }
    #{{ $lkContainerId }} .lk-person-meta { flex: 1; min-width: 0; }
    #{{ $lkContainerId }} .lk-person-meta strong { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #{{ $lkContainerId }} .lk-person-meta small { color: #b5b5b5; font-size: 11px; }
    #{{ $lkContainerId }} .lk-person-flags { display: flex; gap: 6px; color: #9a9a9a; font-size: 12px; }
    #{{ $lkContainerId }} .lk-person-flags .on { color: #8fdf9c; }
    #{{ $lkContainerId }} .lk-person-flags .warn { color: #f5c452; }
    #{{ $lkContainerId }} .lk-field { margin-bottom: 12px; }
    #{{ $lkContainerId }} .lk-field label { display: block; color: #b5b5b5; font-size: 11px; margin-bottom: 4px; }
    #{{ $lkContainerId }} .lk-field select,
    #{{ $lkContainerId }} .lk-field input {
        width: 100%; border-radius: 10px; border: 1px solid #4a4a4a;
        background: #1f1f1f; color: #f2f2f2; padding: 8px 10px; font-size: 13px;
    }
    #{{ $lkContainerId }} .lk-drawer[data-lk-chat] .lk-drawer-body {
        overflow: hidden;
        padding: 0;
        height: 100%;
    }
    #{{ $lkContainerId }} .lk-chat-log {
        flex: 1 1 auto;
        min-height: 0;
        max-height: none;
        overflow-y: auto;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: 0;
        padding: 12px 14px;
    }
    #{{ $lkContainerId }} .lk-chat-log:empty::before {
        content: 'لا توجد رسائل بعد';
        color: #8a8a8a;
        font-size: 13px;
        text-align: center;
        margin: auto;
        padding: 24px 8px;
    }
    #{{ $lkContainerId }} .lk-chat-msg {
        background: #3a3a3a; border-radius: 10px; padding: 8px 10px; color: #f2f2f2; font-size: 12px;
        word-break: break-word;
    }
    #{{ $lkContainerId }} .lk-chat-msg b { color: #8ec4ff; }
    #{{ $lkContainerId }} .lk-chat-form {
        display: flex;
        align-items: stretch;
        gap: 8px;
        width: 100%;
        flex: 0 0 auto;
        margin: 0;
        padding: 10px 12px calc(10px + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid #3a3a3a;
        background: #242424;
        box-sizing: border-box;
    }
    #{{ $lkContainerId }} .lk-chat-form input {
        flex: 1 1 auto;
        min-width: 0;
        width: 100%;
        height: 40px;
        border-radius: 10px;
        border: 1px solid #4a4a4a;
        background: #1f1f1f;
        color: #f2f2f2;
        padding: 0 12px;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }
    #{{ $lkContainerId }} .lk-chat-form input::placeholder { color: #8a8a8a; }
    #{{ $lkContainerId }} .lk-chat-form input:focus {
        border-color: #0e71eb;
        box-shadow: 0 0 0 2px rgba(14, 113, 235, 0.25);
    }
    #{{ $lkContainerId }} .lk-chat-form button {
        flex: 0 0 auto;
        height: 40px;
        border: 0;
        border-radius: 10px;
        background: #0e71eb;
        color: #fff;
        padding: 0 14px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        white-space: nowrap;
    }
    #{{ $lkContainerId }} .lk-toast {
        position: absolute; top: 12px; left: 50%; transform: translateX(-50%);
        z-index: 6; background: rgba(36, 36, 36, 0.96); color: #f2f2f2;
        border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 999px;
        padding: 8px 14px; font-size: 12px; display: none;
        box-shadow: 0 10px 28px rgba(0,0,0,.35);
    }
    #{{ $lkContainerId }} .lk-toast.is-on { display: block; }
    #{{ $lkContainerId }}.lk-covert [data-lk-mic],
    #{{ $lkContainerId }}.lk-covert [data-lk-cam],
    #{{ $lkContainerId }}.lk-covert [data-lk-screen],
    #{{ $lkContainerId }}.lk-covert [data-lk-hand],
    #{{ $lkContainerId }}.lk-covert [data-lk-chat-form],
    #{{ $lkContainerId }}.lk-covert [data-lk-settings-btn] { display: none !important; }
    @media (max-width: 820px) {
        #{{ $lkContainerId }} .lk-drawer {
            position: absolute; inset-inline-end: 0; top: 0; bottom: 0;
            box-shadow: -12px 0 40px rgba(0,0,0,.45);
        }
    }
    #{{ $lkContainerId }} .lk-wb {
        display: none;
        position: absolute;
        inset: 0;
        z-index: 8;
        flex-direction: column;
        background: #121212;
        min-height: 0;
    }
    #{{ $lkContainerId }} .lk-wb.is-open { display: flex; }
    #{{ $lkContainerId }} .lk-wb-bar {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: #242424;
        border-bottom: 1px solid #1a1a1a;
        color: #f2f2f2;
        font-size: 13px;
        font-weight: 700;
    }
    #{{ $lkContainerId }} .lk-wb-bar span:first-child {
        display: inline-flex; align-items: center; gap: 8px;
    }
    #{{ $lkContainerId }} .lk-wb-hint {
        font-weight: 500; font-size: 11px; color: #9a9a9a;
    }
    #{{ $lkContainerId }} .lk-wb-bar button {
        margin-inline-start: auto;
        border: 0; border-radius: 8px;
        background: #3d3d3d; color: #fff;
        padding: 6px 12px; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    #{{ $lkContainerId }} .lk-wb-stage {
        flex: 1; min-height: 0; position: relative; background: #121212;
    }
    #{{ $lkContainerId }} .lk-wb-host {
        position: absolute; inset: 0; width: 100%; height: 100%;
    }
    #{{ $lkContainerId }} .lk-wb-host .excalidraw { height: 100%; }
    #{{ $lkContainerId }} .lk-wb-loading {
        display: none; position: absolute; inset: 0; z-index: 2;
        align-items: center; justify-content: center;
        background: rgba(12,12,12,.78); color: #cfcfcf; font-size: 14px;
    }
    #{{ $lkContainerId }} .lk-wb-loading.is-on { display: flex; }
    #{{ $lkContainerId }} .lk-wb .excalidraw .layer-ui__library,
    #{{ $lkContainerId }} .lk-wb .excalidraw .library-menu,
    #{{ $lkContainerId }} .lk-wb .excalidraw [data-testid="collab-button"],
    #{{ $lkContainerId }} .lk-wb .excalidraw .ExcalidrawLogo,
    #{{ $lkContainerId }} .lk-wb .excalidraw .welcome-screen-center__logo,
    #{{ $lkContainerId }} .lk-wb .excalidraw a.welcome-screen-menu-item[href^="http"],
    #{{ $lkContainerId }} .lk-wb .excalidraw a.welcome-screen-menu-item[href^="https"] {
        display: none !important;
        pointer-events: none !important;
    }
    @media (max-width: 720px) {
        #{{ $lkContainerId }} .lk-wb-hint { display: none; }
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
    let hiddenObserver = {{ $lkHiddenObserver ? 'true' : 'false' }};
    const whiteboardEnabled = {{ $lkWhiteboard ? 'true' : 'false' }};
    const wbAssetBases = @json($lkExBases);
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
        + '<div class="lk-status" data-lk-status><i class="fas fa-spinner fa-spin text-2xl" style="color:#0e71eb"></i><span>جاري الاتصال بالغرفة…</span></div>'
        + '<div class="lk-toast" data-lk-toast></div>'
        + '<div class="lk-shell">'
        + '  <div class="lk-main">'
        + '    <div class="lk-meta" data-lk-meta>'
        + '      <span class="lk-meta-pill" data-lk-quality><i class="fas fa-signal"></i><span>جودة الاتصال</span></span>'
        + '      <span class="lk-meta-pill" data-lk-count title="المشاركون"><i class="fas fa-users"></i><span>0</span></span>'
        + '    </div>'
        + '    <div class="lk-stage" data-lk-stage data-count="1"></div>'
        + (whiteboardEnabled
            ? '    <div class="lk-wb" data-lk-wb>'
                + '      <div class="lk-wb-bar">'
                + '        <span><i class="fas fa-chalkboard"></i> السبورة</span>'
                + '        <span class="lk-wb-hint">متزامنة مع الجميع في الغرفة</span>'
                + '        <button type="button" data-lk-wb-close>إخفاء</button>'
                + '      </div>'
                + '      <div class="lk-wb-stage">'
                + '        <div class="lk-wb-host mx-Sana-whiteboard" data-lk-wb-host data-lang="ar"></div>'
                + '        <div class="lk-wb-loading" data-lk-wb-loading>جاري تحميل السبورة…</div>'
                + '      </div>'
                + '    </div>'
            : '')
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
        + '  <div class="lk-toolbar-cluster">'
        + '    <button type="button" data-lk-mic><span class="lk-tb-icon"><i class="fas fa-microphone"></i></span><span class="lk-tb-label">صوت</span></button>'
        + '    <button type="button" data-lk-cam><span class="lk-tb-icon"><i class="fas fa-video"></i></span><span class="lk-tb-label">فيديو</span></button>'
        + '    <button type="button" data-lk-screen><span class="lk-tb-icon"><i class="fas fa-desktop"></i></span><span class="lk-tb-label">مشاركة</span></button>'
        + (whiteboardEnabled
            ? '    <button type="button" data-lk-wb-btn><span class="lk-tb-icon"><i class="fas fa-chalkboard"></i></span><span class="lk-tb-label">سبورة</span></button>'
            : '')
        + '    <button type="button" data-lk-hand><span class="lk-tb-icon"><i class="fas fa-hand"></i></span><span class="lk-tb-label">يد</span></button>'
        + '  </div>'
        + '  <div class="lk-toolbar-cluster">'
        + '    <button type="button" data-lk-people-btn><span class="lk-tb-icon"><i class="fas fa-users"></i></span><span class="lk-tb-label">مشاركون</span></button>'
        + '    <button type="button" data-lk-chat-btn><span class="lk-tb-icon"><i class="fas fa-comments"></i></span><span class="lk-tb-label">دردشة</span></button>'
        + '    <button type="button" data-lk-settings-btn><span class="lk-tb-icon"><i class="fas fa-gear"></i></span><span class="lk-tb-label">إعدادات</span></button>'
        + '    <button type="button" data-lk-focus><span class="lk-tb-icon"><i class="fas fa-expand"></i></span><span class="lk-tb-label">عرض</span></button>'
        + '    <button type="button" data-lk-fs><span class="lk-tb-icon"><i class="fas fa-up-right-and-down-left-from-center"></i></span><span class="lk-tb-label">ملء</span></button>'
        + '  </div>'
        + '  <div class="lk-toolbar-cluster lk-toolbar-cluster--end">'
        + '    <button type="button" data-lk-leave class="is-off"><span class="lk-tb-icon"><i class="fas fa-phone-slash"></i></span><span class="lk-tb-label">مغادرة</span></button>'
        + '  </div>'
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
    const wbBtn = root.querySelector('[data-lk-wb-btn]');
    const wbPanel = root.querySelector('[data-lk-wb]');
    const wbHost = root.querySelector('[data-lk-wb-host]');
    const wbLoading = root.querySelector('[data-lk-wb-loading]');
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
            : '<i class="fas fa-spinner fa-spin text-2xl text-blue-400"></i>')
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

    function isCovertParticipant(p) {
        if (!p) return false;
        const id = String(p.identity || '');
        if (id.startsWith('obs:') || id.startsWith('observer:')) return true;
        if (p.isHidden === true) return true;
        const kind = p.kind;
        if (kind === 'hidden' || kind === 'HIDDEN' || kind === 4) return true;
        try {
            if (p.permissions && p.permissions.hidden) return true;
        } catch (e) {}
        return false;
    }

    function visibleParticipants() {
        return allParticipants().filter((p) => !isCovertParticipant(p));
    }

    function updateCount() {
        const n = visibleParticipants().length;
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
        root.classList.toggle('lk-pip-1on1', tileCount === 2 && !root.classList.contains('lk-focus-mode'));
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
        if (identity && (String(identity).startsWith('obs:') || String(identity).startsWith('observer:'))) {
            return null;
        }
        let tile = tiles.get(identity);
        if (!tile) {
            tile = document.createElement('div');
            tile.className = 'lk-tile is-cam-off';
            tile.dataset.identity = identity;
            if (room.localParticipant && identity === room.localParticipant.identity) {
                tile.classList.add('is-local');
            }
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
        const isLocal = !!(room.localParticipant && identity === room.localParticipant.identity);
        if (isLocal) tile.classList.add('is-local');
        const displayName = String(name).replace(/\s*\(أنت\)\s*$/, '') + (isLocal ? ' (أنت)' : '');
        if (nameEl) nameEl.textContent = displayName;
        if (avName) avName.textContent = displayName;
        if (avCircle) avCircle.textContent = initials(displayName);
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
        if (!tile) return;
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
        const rows = visibleParticipants().map((p) => {
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
        if (!room.localParticipant) return;
        const data = encoder.encode(JSON.stringify(payload));
        const kind = DataPacket_Kind?.RELIABLE ?? 0;
        await room.localParticipant.publishData(data, { reliable: true, kind });
    }

    let wbApi = null;
    let wbMounted = false;
    let wbMountPromise = null;
    let wbApplying = false;
    let wbSyncTimer = null;
    let wbVendorPromise = null;
    const wbChunks = new Map();
    const WB_CHUNK = 9000;

    function wbShowLoading(on) {
        wbLoading?.classList.toggle('is-on', !!on);
    }
    function loadScriptOnce(url) {
        return new Promise((resolve, reject) => {
            const abs = new URL(url, window.location.origin).href;
            if ([...document.scripts].some((s) => s.src === abs)) {
                resolve();
                return;
            }
            const s = document.createElement('script');
            s.src = abs;
            s.async = false;
            s.onload = () => resolve();
            s.onerror = () => reject(new Error('فشل تحميل: ' + url));
            document.head.appendChild(s);
        });
    }
    function getExcalidrawLib() {
        return window.ExcalidrawLib || null;
    }
    function ensureWbVendor() {
        if (window.React && window.ReactDOM && getExcalidrawLib()) return Promise.resolve();
        if (wbVendorPromise) return wbVendorPromise;
        const bases = (wbAssetBases && wbAssetBases.length)
            ? wbAssetBases
            : ['/mx-vendor/excalidraw/', '/vendor/excalidraw/'];
        const loadFrom = (base) => {
            const root = String(base || '').replace(/\/?$/, '/');
            window.EXCALIDRAW_ASSET_PATH = root + 'dist/';
            const prefix = root.charAt(0) === '/' ? (window.location.origin + root) : root;
            return loadScriptOnce(prefix + 'react.production.min.js')
                .then(() => loadScriptOnce(prefix + 'react-dom.production.min.js'))
                .then(() => loadScriptOnce(prefix + 'dist/excalidraw.production.min.js'))
                .then(() => {
                    if (!window.React || !window.ReactDOM || !getExcalidrawLib()) {
                        throw new Error('تعذّر تعريف مكوّنات السبورة');
                    }
                });
        };
        const tryNext = (i) => {
            if (i >= bases.length) return Promise.reject(new Error('تعذّر تحميل السبورة'));
            return loadFrom(bases[i]).catch(() => tryNext(i + 1));
        };
        wbVendorPromise = tryNext(0).catch((e) => {
            wbVendorPromise = null;
            throw e;
        });
        return wbVendorPromise;
    }
    function wbScenePayload() {
        if (!wbApi) return null;
        const elements = typeof wbApi.getSceneElements === 'function' ? wbApi.getSceneElements() : [];
        const appState = typeof wbApi.getAppState === 'function' ? wbApi.getAppState() : {};
        return {
            elements,
            bg: appState.viewBackgroundColor || '#ffffff',
        };
    }
    async function sendWbPacket(payload) {
        try { await sendPacket(payload); } catch (e) {}
    }
    async function broadcastWbScene() {
        if (hiddenObserver || !wbApi || wbApplying) return;
        const scene = wbScenePayload();
        if (!scene) return;
        const raw = JSON.stringify(scene);
        const id = Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
        const n = Math.max(1, Math.ceil(raw.length / WB_CHUNK));
        for (let i = 0; i < n; i++) {
            await sendWbPacket({
                type: 'wb',
                op: 'scene',
                id,
                i,
                n,
                part: raw.slice(i * WB_CHUNK, (i + 1) * WB_CHUNK),
            });
        }
    }
    function scheduleWbSync() {
        if (hiddenObserver || wbApplying) return;
        clearTimeout(wbSyncTimer);
        wbSyncTimer = setTimeout(() => { broadcastWbScene(); }, 380);
    }
    function applyWbScene(scene) {
        if (!wbApi || !scene || !Array.isArray(scene.elements)) return;
        wbApplying = true;
        try {
            wbApi.updateScene({
                elements: scene.elements,
                appState: { viewBackgroundColor: scene.bg || '#ffffff' },
                commitToHistory: false,
            });
        } catch (e) {}
        requestAnimationFrame(() => { wbApplying = false; });
    }
    function handleWbChunk(msg) {
        if (!msg?.id || typeof msg.part !== 'string') return;
        const n = parseInt(msg.n, 10) || 1;
        const i = parseInt(msg.i, 10) || 0;
        let entry = wbChunks.get(msg.id);
        if (!entry) {
            entry = { n, parts: [] };
            wbChunks.set(msg.id, entry);
        }
        entry.parts[i] = msg.part;
        if (entry.parts.filter((p) => typeof p === 'string').length < n) return;
        wbChunks.delete(msg.id);
        try {
            applyWbScene(JSON.parse(entry.parts.join('')));
        } catch (e) {}
    }
    function handleWbMessage(msg) {
        if (!msg || msg.type !== 'wb') return;
        if (msg.op === 'open') {
            openWhiteboard({ remote: true });
        } else if (msg.op === 'hello') {
            if (wbPanel?.classList.contains('is-open')) broadcastWbScene();
        } else if (msg.op === 'scene') {
            handleWbChunk(msg);
        }
    }
    function mountWhiteboard() {
        if (wbMounted) return Promise.resolve();
        if (wbMountPromise) return wbMountPromise;
        if (!wbHost || !whiteboardEnabled) return Promise.reject(new Error('no whiteboard'));
        wbShowLoading(true);
        wbMountPromise = ensureWbVendor().then(() => new Promise((resolve, reject) => {
            const deadline = Date.now() + 6000;
            const tryMount = () => {
                const Lib = getExcalidrawLib();
                const ReactMod = window.React;
                const ReactDOM = window.ReactDOM;
                if (!Lib || !ReactMod || !ReactDOM || typeof ReactDOM.createRoot !== 'function') {
                    wbShowLoading(false);
                    reject(new Error('المكتبات غير متاحة'));
                    return;
                }
                const rect = wbHost.getBoundingClientRect();
                if (rect.width < 8 || rect.height < 8) {
                    if (Date.now() > deadline) {
                        wbShowLoading(false);
                        reject(new Error('الحاوية بلا أبعاد كافية'));
                        return;
                    }
                    requestAnimationFrame(tryMount);
                    return;
                }
                try {
                    const props = {
                        langCode: 'ar-SA',
                        viewModeEnabled: !!hiddenObserver,
                        UIOptions: {
                            canvasActions: { loadScene: !hiddenObserver, export: true, saveAsImage: true },
                        },
                        excalidrawAPI: (api) => { wbApi = api; },
                        onChange: () => scheduleWbSync(),
                    };
                    ReactDOM.createRoot(wbHost).render(ReactMod.createElement(Lib.Excalidraw, props));
                    wbMounted = true;
                    wbShowLoading(false);
                    window.dispatchEvent(new Event('resize'));
                    resolve();
                } catch (err) {
                    wbShowLoading(false);
                    reject(err);
                }
            };
            requestAnimationFrame(tryMount);
        })).catch((err) => {
            wbMountPromise = null;
            if (wbLoading) {
                wbLoading.textContent = 'تعذّر تحميل السبورة' + (err && err.message ? ' — ' + err.message : '');
                wbShowLoading(true);
            }
            throw err;
        });
        return wbMountPromise;
    }
    function openWhiteboard(opts = {}) {
        if (!whiteboardEnabled || !wbPanel) return;
        const already = wbPanel.classList.contains('is-open');
        wbPanel.classList.add('is-open');
        wbBtn?.classList.add('is-active');
        mountWhiteboard().then(() => {
            setTimeout(() => window.dispatchEvent(new Event('resize')), 80);
            setTimeout(() => window.dispatchEvent(new Event('resize')), 400);
            if (!opts.remote) {
                sendWbPacket({ type: 'wb', op: 'open' });
                sendWbPacket({ type: 'wb', op: 'hello' });
            } else if (!already) {
                sendWbPacket({ type: 'wb', op: 'hello' });
            }
        }).catch(() => {});
    }
    function closeWhiteboard(opts = {}) {
        if (!wbPanel) return;
        wbPanel.classList.remove('is-open');
        wbBtn?.classList.remove('is-active');
        if (!opts.remote) window.dispatchEvent(new Event('resize'));
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
        if (isCovertParticipant(participant)) return;
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
        if (isCovertParticipant(p)) {
            updateCount();
            renderPeople();
            return;
        }
        ensureTile(p.identity, p.name || p.identity);
        toast((p.name || 'مشارك') + ' انضم للاجتماع');
        renderPeople();
        if (wbPanel?.classList.contains('is-open')) {
            setTimeout(() => { broadcastWbScene(); }, 400);
        }
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
            } else if (msg?.type === 'wb') {
                handleWbMessage(msg);
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

            if (data.role === 'hidden_observer' || isCovertParticipant(room.localParticipant)) {
                hiddenObserver = true;
            }
            if (hiddenObserver) {
                root.classList.add('lk-covert');
            }

            const prejoinPrefs = window.__sanaPrejoinPrefs || {};
            const wantMic = hiddenObserver ? false : !(data.mute_on_join === true || prejoinPrefs.mute_on_join === true);
            const wantCam = hiddenObserver ? false : !(data.video_off_on_join === true || prejoinPrefs.video_off_on_join === true);
            micEnabled = wantMic;
            camEnabled = wantCam;

            if (!hiddenObserver) {
                ensureTile(room.localParticipant.identity, (room.localParticipant.name || 'أنت') + ' (أنت)');
            }

            if (!hiddenObserver) {
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
            }

            // participants already in room
            room.remoteParticipants.forEach((p) => {
                if (!isCovertParticipant(p)) {
                    ensureTile(p.identity, p.name || p.identity);
                }
            });

            micBtn?.classList.toggle('is-off', !micEnabled);
            camBtn?.classList.toggle('is-off', !camEnabled);
            setMediaBtnState(micBtn, micEnabled, 'fa-microphone', 'fa-microphone-slash');
            setMediaBtnState(camBtn, camEnabled, 'fa-video', 'fa-video-slash');
            if (!hiddenObserver) {
                syncTileMediaState(room.localParticipant.identity);
                await fillDevices();
            }
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

    function setMediaBtnState(btn, enabled, onIcon, offIcon) {
        if (!btn) return;
        btn.classList.toggle('is-off', !enabled);
        const icon = btn.querySelector('.lk-tb-icon i');
        if (icon) icon.className = 'fas ' + (enabled ? onIcon : offIcon);
    }

    micBtn?.addEventListener('click', async () => {
        if (hiddenObserver) return;
        micEnabled = !micEnabled;
        await room.localParticipant.setMicrophoneEnabled(micEnabled);
        setMediaBtnState(micBtn, micEnabled, 'fa-microphone', 'fa-microphone-slash');
        syncTileMediaState(room.localParticipant.identity);
        renderPeople();
    });
    camBtn?.addEventListener('click', async () => {
        if (hiddenObserver) return;
        camEnabled = !camEnabled;
        await room.localParticipant.setCameraEnabled(camEnabled, {
            resolution: camRes ? camRes.resolution : { width: 1920, height: 1080, frameRate: 30 },
        });
        camBtn.classList.toggle('is-off', !camEnabled);
        setMediaBtnState(camBtn, camEnabled, 'fa-video', 'fa-video-slash');
        syncTileMediaState(room.localParticipant.identity);
        renderPeople();
    });
    screenBtn?.addEventListener('click', async () => {
        if (hiddenObserver) return;
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
    wbBtn?.addEventListener('click', () => {
        if (wbPanel?.classList.contains('is-open')) closeWhiteboard();
        else openWhiteboard();
    });
    root.querySelector('[data-lk-wb-close]')?.addEventListener('click', () => closeWhiteboard());
    handBtn?.addEventListener('click', async () => {
        if (hiddenObserver) return;
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
        if (hiddenObserver) return;
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

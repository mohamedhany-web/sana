<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('auth.dashboard')) - {{ $platformName ?? config('brand.name', config('app.name')) }}</title>
    
    @include('partials.favicon-links')

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('partials.rtl-base')
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.force-light-theme')
    <script>
        (function () {
            try {
                if (localStorage.getItem('sidebar_collapsed') === 'true') {
                    document.documentElement.classList.add('admin-sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('adminNavNotifications', function (config) {
                config = config || {};
                var initialUnread = Number(config.unread) || 0;
                var initialItems = Array.isArray(config.items) ? config.items : [];
                var pollUrl = config.pollUrl || '';
                return {
                    openNotif: false,
                    unread: initialUnread,
                    lastSynced: initialUnread,
                    firstPoll: true,
                    items: initialItems.slice(),
                    pollUrl: pollUrl,
                    audioUnlocked: false,
                    _pollTimer: null,
                    init: function () {
                        var self = this;
                        document.body.addEventListener('click', function () { self.audioUnlocked = true; }, { once: true });
                        document.body.addEventListener('keydown', function () { self.audioUnlocked = true; }, { once: true });
                        this._pollTimer = setInterval(function () { self.poll(); }, 60000);
                        setTimeout(function () { self.poll(); }, 8000);
                    },
                    destroy: function () {
                        if (this._pollTimer) clearInterval(this._pollTimer);
                    },
                    poll: async function () {
                        try {
                            var token = document.querySelector('meta[name="csrf-token"]');
                            var res = await fetch(this.pollUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
                                },
                                credentials: 'same-origin'
                            });
                            if (!res.ok) return;
                            var d = await res.json();
                            if (!this.firstPoll && d.unread_count > this.lastSynced) {
                                this.playBeep();
                            }
                            this.firstPoll = false;
                            this.lastSynced = d.unread_count;
                            this.unread = d.unread_count;
                            this.items = Array.isArray(d.items) ? d.items : [];
                        } catch (e) { /* ignore */ }
                    },
                    playBeep: function () {
                        if (!this.audioUnlocked) return;
                        try {
                            var Ctx = window.AudioContext || window.webkitAudioContext;
                            if (!Ctx) return;
                            var ctx = new Ctx();
                            var osc = ctx.createOscillator();
                            var gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.value = 880;
                            gain.gain.setValueAtTime(0.0001, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + 0.02);
                            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.22);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start(ctx.currentTime);
                            osc.stop(ctx.currentTime + 0.25);
                            setTimeout(function () { ctx.close(); }, 400);
                        } catch (e) { /* ignore */ }
                    }
                };
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Tajawal', 'IBM Plex Sans Arabic', 'sans-serif'],
                        body: ['IBM Plex Sans Arabic', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '{{ config('brand.colors.blue') }}',
                            light: '{{ config('brand.colors.blue_dark') }}',
                            dark: '{{ config('brand.colors.purple') }}',
                        }
                    }
                }
            }
        }
    </script>

    @include('layouts.partials.admin-theme')
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        * { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Tajawal', 'IBM Plex Sans Arabic', sans-serif; }
        
        :root {
            --admin-sidebar-w: 260px;
        }
        html.admin-sidebar-collapsed {
            --admin-sidebar-w: 64px;
        }

        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden;
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
            background: #f1f5f9; }
        [x-cloak] { display: none !important; }

        .admin-sidebar {
            transition: width 0.12s ease-out;
        }

        /* أيقونة الشعار — نفس أسلوب معاينة إعدادات النظام (object-fit: contain) */
        .sidebar-brand-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 12px;
            border: 2px dashed rgba(148, 163, 184, 0.35);
            background: #f8fafc;
            padding: 0.35rem;
        }
        .admin-sidebar--brand .sidebar-brand-icon {
            border-color: rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }
        .sidebar-brand-icon--sm {
            width: 2.25rem;
            height: 2.25rem;
            padding: 0.3rem;
        }
        .sidebar-brand-icon__img {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }
        .admin-sidebar.collapsed .sidebar-brand-icon {
            margin: 0 auto;
        }

        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.35); border-radius: 10px; }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.55rem 0.875rem; border-radius: 10px;
            transition: background-color 0.1s ease, color 0.1s ease;
            position: relative; font-size: 0.8125rem; font-weight: 500;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-link.active::before {
            content: ''; position: absolute; top: 0.375rem; bottom: 0.375rem;
            width: 3px; border-radius: 4px 0 0 4px;
        }
        [dir="ltr"] .sidebar-link.active::before { left: 0; border-radius: 0 4px 4px 0; }
        [dir="rtl"] .sidebar-link.active::before { right: 0; }
        .sidebar-link i { width: 1.25rem; text-align: center; font-size: 0.875rem; flex-shrink: 0; }

        .sidebar-group-btn {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; padding: 0.55rem 0.875rem; border-radius: 10px;
            transition: background-color 0.1s ease, color 0.1s ease;
            font-size: 0.8125rem; font-weight: 500;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-group-btn i.chevron { font-size: 0.5625rem; transition: transform 0.12s ease; }

        .sidebar-sub-link {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.4rem 0.75rem; border-radius: 8px;
            transition: background-color 0.1s ease, color 0.1s ease;
            font-size: 0.75rem; font-weight: 400;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-sub-link i { width: 0.875rem; text-align: center; font-size: 0.6875rem; flex-shrink: 0; }

        .sidebar-badge {
            margin-right: auto; font-size: 0.5625rem; font-weight: 700;
            padding: 0.125rem 0.4rem; border-radius: 9999px;
            min-width: 1.125rem; text-align: center; line-height: 1.4;
        }

        .sidebar-section-label {
            padding: 0.85rem 0.875rem 0.35rem;
            font-size: 0.5625rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: opacity 0.2s ease;
        }

        /* ========== COLLAPSED SIDEBAR ========== */
        .admin-sidebar.collapsed { width: 64px !important; }
        .admin-sidebar.collapsed .sidebar-link,
        .admin-sidebar.collapsed .sidebar-group-btn {
            justify-content: center; padding: 0.5rem;
        }
        .admin-sidebar.collapsed .sidebar-link span,
        .admin-sidebar.collapsed .sidebar-group-btn > span > span,
        .admin-sidebar.collapsed .sidebar-group-btn i.chevron,
        .admin-sidebar.collapsed .sidebar-badge,
        .admin-sidebar.collapsed .sidebar-section-label,
        .admin-sidebar.collapsed .sidebar-sub-link,
        .admin-sidebar.collapsed [x-show] ul,
        .admin-sidebar.collapsed .sidebar-logo-text,
        .admin-sidebar.collapsed .sidebar-user-info,
        .admin-sidebar.collapsed .border-r { 
            display: none !important; 
        }
        .admin-sidebar.collapsed .sidebar-link i,
        .admin-sidebar.collapsed .sidebar-group-btn i:first-of-type {
            width: auto; font-size: 1rem;
        }
        .admin-sidebar.collapsed .sidebar-logo { justify-content: center; }
        .admin-sidebar.collapsed .sidebar-user-wrap { justify-content: center; padding: 0.5rem; }
        .admin-sidebar.collapsed .sidebar-user-wrap img,
        .admin-sidebar.collapsed .sidebar-user-wrap > div:first-child { margin: 0 auto; }
        .admin-sidebar.collapsed .sidebar-collapse-btn i { transform: rotate(180deg); }
        [dir="rtl"] .admin-sidebar.collapsed .sidebar-collapse-btn i { transform: rotate(0deg); }

        .top-navbar { position: relative; transition: background 0.3s; }

        /* ========== CARDS ========== */
        .stat-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 60%, rgba(30, 58, 138, 0.02) 100%);
            pointer-events: none; border-radius: 16px;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
            border-color: rgba(30, 58, 138, 0.12);
        }
        .stat-card:active { transform: translateY(-1px); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.125rem; color: white;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover .stat-icon { transform: scale(1.08) rotate(2deg); }

        .section-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .section-card:hover {
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.06);
            border-color: rgba(30, 58, 138, 0.08);
        }
        .section-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(241, 245, 249, 0.9);
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(248, 250, 252, 0.4);
        }

        .list-row {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 0.75rem 1.5rem;
            transition: background 0.12s ease;
            border-bottom: 1px solid rgba(241, 245, 249, 0.7);
        }
        .list-row:last-child { border-bottom: none; }
        .list-row:hover { background: rgba(248, 250, 252, 0.7); }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 16px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.06);
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: none; }
        .animate-fade-in-1,
        .animate-fade-in-2,
        .animate-fade-in-3,
        .animate-fade-in-4,
        .animate-fade-in-5 { animation: none; }
        .admin-dashboard .stat-card,
        .admin-dashboard .section-card,
        .admin-dashboard .dash-quick-panel { animation: none !important; }

        /* ========== BUTTONS ========== */
        .btn-primary { background: #1E3A8A; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background: #1e3a6f; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25); }
        .btn-primary:active { transform: translateY(0); }
        .btn-secondary { background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-secondary:hover { background: #475569; }
        .btn-success { background: #059669; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-warning { background: #d97706; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-warning:hover { background: #b45309; }

        /* ========== COMPAT for other admin pages ========== */
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; border-radius: 0.5rem; color: #475569; transition: all 0.2s; }
        .nav-link:hover { background: #f1f5f9; color: #1e293b; }
        .nav-link.active { background: #eef2ff; color: #1E3A8A; }
        .dashboard-card { background: white; border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 16px; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .dashboard-card:hover { box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.06); }
        .card-hover-effect { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover-effect:hover { transform: translateY(-2px); }
        .card-icon { background: linear-gradient(135deg, #1E3A8A, #2563EB); box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25); }
        .card-icon:hover { transform: scale(1.08); }
        .section-header { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(226, 232, 240, 0.6); background: rgba(248, 250, 252, 0.4); }
        .list-item-card { background: white; border: 1px solid rgba(226, 232, 240, 0.6); border-radius: 12px; transition: all 0.2s; }
        .list-item-card:hover { background: #f8fafc; border-color: rgba(30, 58, 138, 0.12); }

        /* ========== تخطيط ثابت: المحتوى بجانب السايدبار وليس خلفه ========== */
        .content-wrapper {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 100vh;
            max-height: 100vh;
            width: auto;
            margin: 0 !important;
            background: #f1f5f9;
        }
        [dir="rtl"] .content-wrapper {
            right: var(--admin-sidebar-w);
        }
        [dir="ltr"] .content-wrapper {
            left: var(--admin-sidebar-w);
            right: 0;
        }

        #admin-sidebar-desktop {
            z-index: 40;
        }

        .top-navbar {
            flex-shrink: 0;
            z-index: 20;
        }

        @media (max-width: 1023px) {
            .sidebar-desktop { display: none !important; }
            .content-wrapper {
                left: 0 !important;
                right: 0 !important;
            }
        }
        @media (min-width: 1024px) {
            .sidebar-desktop { display: flex !important; }
        }

        /* ========== FOCUS STATES ========== */
        button:focus-visible, a:focus-visible, input:focus-visible {
            outline: 2px solid rgba(30, 58, 138, 0.4);
            outline-offset: 2px;
            border-radius: 0.375rem;
        }
    </style>
    
    @stack('styles')
    <style>
        turbo-progress-bar,
        #turbo-progress-bar {
            display: none !important;
        }
        .admin-nav-loader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            z-index: 9999;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.18s ease;
        }
        .admin-nav-loader.is-visible {
            opacity: 1;
        }
        .admin-nav-loader__track {
            position: relative;
            height: 100%;
            background: rgba(30, 58, 138, 0.12);
            overflow: hidden;
        }
        .admin-nav-loader__bar {
            display: block;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, var(--admin-primary, #1e3a8a), #6366f1 55%, #818cf8);
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
            will-change: transform;
        }
        html[dir="rtl"] .admin-nav-loader__bar {
            transform-origin: right center;
        }
        .admin-nav-loader__glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 28%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
            opacity: 0;
            transform: translateX(-120%);
            pointer-events: none;
        }
        .admin-nav-loader.is-visible .admin-nav-loader__glow {
            opacity: 0.85;
            animation: adminNavLoaderGlow 1.15s linear infinite;
        }
        @keyframes adminNavLoaderGlow {
            from { transform: translateX(-120%); }
            to { transform: translateX(420%); }
        }
        html.admin-turbo-busy .animate-fade-in,
        html.admin-turbo-busy .animate-fade-in-1,
        html.admin-turbo-busy .animate-fade-in-2,
        html.admin-turbo-busy .animate-fade-in-3,
        html.admin-turbo-busy .animate-fade-in-4,
        html.admin-turbo-busy .animate-fade-in-5 { animation: none !important; }
    </style>
</head>
<body class="font-body"
      data-admin-path="{{ request()->path() }}"
      data-admin-route="{{ Route::currentRouteName() }}"
      x-data="{ 
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true'
      }" 
      x-init="
          document.documentElement.classList.toggle('admin-sidebar-collapsed', sidebarCollapsed);
          $watch('sidebarCollapsed', v => {
              localStorage.setItem('sidebar_collapsed', v);
              document.documentElement.classList.toggle('admin-sidebar-collapsed', v);
          });
          window.addEventListener('close-sidebar', () => sidebarOpen = false);
          window.addEventListener('resize', () => { if (window.innerWidth >= 1024) sidebarOpen = false; });
      "
      @close-sidebar.window="sidebarOpen = false">

    <div class="admin-nav-loader" id="admin-nav-loader" aria-hidden="true">
        <div class="admin-nav-loader__track">
            <span class="admin-nav-loader__bar" id="admin-nav-loader-bar"></span>
            <span class="admin-nav-loader__glow"></span>
        </div>
    </div>

    <!-- ===== Desktop Sidebar (ثابت أثناء التنقل عبر Turbo) ===== -->
    <aside id="admin-sidebar-desktop"
           data-turbo-permanent
           class="sidebar-desktop admin-sidebar admin-sidebar--brand fixed top-0 right-0 bottom-0 z-30 flex flex-col"
           :class="sidebarCollapsed ? 'collapsed w-[64px]' : 'w-[260px]'">
        @include('layouts.admin-sidebar')
    </aside>

    <!-- ===== Mobile Sidebar Overlay ===== -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/50" @click="sidebarOpen = false"></div>
        <div class="absolute inset-y-0 right-0 w-[260px] admin-sidebar admin-sidebar--brand flex flex-col"
             x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-180"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-120"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <button @click="sidebarOpen = false" class="absolute top-4 left-3 z-50 w-8 h-8 rounded-lg bg-white/8 hover:bg-white/15 text-slate-400 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
            @include('layouts.admin-sidebar')
        </div>
    </div>

    <!-- ===== Main Content ===== -->
    <div class="content-wrapper">
        
        <!-- Top Navbar -->
        <header class="top-navbar top-navbar--brand sticky top-0 z-40 flex items-center px-4 lg:px-7 gap-3">
            <!-- Mobile hamburger -->
            <button @click="sidebarOpen = true" class="lg:hidden admin-nav-icon-btn flex items-center justify-center active:scale-95">
                <i class="fas fa-bars text-sm"></i>
            </button>

            @php $mobileAdminLogoUrl = $adminPanelLogoUrl ?? $platformLogoUrl ?? null; @endphp
            @if(! empty($mobileAdminLogoUrl))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center shrink-0 lg:hidden sidebar-brand-icon sidebar-brand-icon--sm" title="{{ $platformName ?? config('brand.name', config('app.name')) }}">
                <img src="{{ $mobileAdminLogoUrl }}" alt="{{ $platformName ?? config('brand.name', config('app.name')) }}" width="36" height="36" class="sidebar-brand-icon__img">
            </a>
            @endif

            <!-- Page title -->
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5 hidden sm:block">{{ $platformName ?? config('brand.name', config('app.name')) }} · لوحة الإدارة</p>
                <h1 class="text-base sm:text-lg font-heading font-bold text-slate-800 truncate leading-tight">
                    @hasSection('header')
                        @yield('header')
                    @else
                        @yield('page_title', 'لوحة الإدارة')
                    @endif
                </h1>
            </div>

            <!-- Right actions -->
            <div class="flex items-center gap-2">
                <!-- Search -->
                <a href="{{ route('home') }}" target="_blank" rel="noopener"
                   class="hidden md:inline-flex items-center gap-2 px-3 h-10 rounded-xl text-xs font-semibold text-slate-600 hover:text-[var(--admin-primary)] border border-slate-200 hover:border-[var(--admin-primary)]/30 bg-white transition-all"
                   title="الموقع العام">
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                    الموقع
                </a>

                <div class="hidden md:flex items-center admin-nav-search rounded-xl px-3.5 h-10 gap-2.5 w-52 lg:w-60 transition-all">
                    <i class="fas fa-search text-slate-400 text-xs"></i>
                    <input type="text" placeholder="بحث سريع..." class="bg-transparent border-none outline-none text-sm text-slate-700 w-full placeholder-slate-400">
                </div>

                <!-- Notifications (تحديث تلقائي ~8ث + صوت عند زيادة العدد) -->
                @php
                    $adminUnreadCount = \App\Models\Notification::where('user_id', auth()->id())
                        ->unread()
                        ->valid()
                        ->count();
                    $adminUnreadNotifications = \App\Models\Notification::where('user_id', auth()->id())
                        ->unread()
                        ->valid()
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                    $adminNavItems = $adminUnreadNotifications->map(fn ($n) => [
                        'id' => $n->id,
                        'title' => $n->title,
                        'message' => $n->message,
                        'priority' => $n->priority,
                        'href' => $n->action_url ?: route('admin.notifications.show', $n),
                        'time' => $n->created_at->diffForHumans(),
                        'icon' => $n->type_icon,
                    ])->values();
                    $adminNavBellConfig = [
                        'unread' => (int) $adminUnreadCount,
                        'items' => $adminNavItems->all(),
                        'pollUrl' => route('admin.api.nav-notifications'),
                    ];
                @endphp
                <div class="relative"
                     x-data="adminNavNotifications({{ \Illuminate\Support\Js::from($adminNavBellConfig) }})"
                     @click.outside="openNotif = false">
                    <button type="button"
                            @click="openNotif = !openNotif"
                            class="admin-nav-icon-btn flex items-center justify-center active:scale-95 relative">
                        <i class="fas fa-bell text-sm"></i>
                        <span x-show="unread > 0" x-cloak
                              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center ring-2 ring-white px-1"
                              x-text="unread > 9 ? '9+' : unread"></span>
                    </button>
                    <!-- Notifications dropdown -->
                    <div x-show="openNotif" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 mt-2 w-80 max-h-[420px] overflow-hidden rounded-2xl bg-white shadow-xl shadow-slate-200/60 border border-slate-100 z-50">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                            <div>
                                <p class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-bell text-amber-500"></i>
                                    {{ __('أحدث الإشعارات') }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="unread > 0 ? ('لديك ' + unread + ' إشعار غير مقروء') : 'لا توجد إشعارات جديدة حالياً'"></p>
                            </div>
                            <a href="{{ route('admin.notifications.inbox') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-700">
                                {{ __('عرض الكل') }}
                            </a>
                        </div>
                        <div class="max-h-[320px] overflow-y-auto">
                            <template x-for="item in items" :key="item.id">
                                <a :href="item.href"
                                   class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-b-0">
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-semibold"
                                              :class="{
                                                  'bg-rose-100 text-rose-600': item.priority === 'urgent',
                                                  'bg-amber-100 text-amber-600': item.priority === 'high',
                                                  'bg-sky-100 text-sky-600': item.priority !== 'urgent' && item.priority !== 'high'
                                              }">
                                            <i :class="item.icon"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate" x-text="item.title"></p>
                                        <p class="text-xs text-slate-600 mt-0.5 line-clamp-2" x-text="item.message"></p>
                                        <p class="text-[10px] text-slate-400 mt-1" x-text="item.time"></p>
                                    </div>
                                </a>
                            </template>
                            <div x-show="items.length === 0" class="px-4 py-6 text-center text-xs text-slate-500">
                                <p>{{ $adminRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $adminNavSettingsUrl = auth()->user()->hasPermission('manage.system-settings')
                        ? route('admin.system-settings.edit')
                        : route('admin.profile');
                @endphp
                <!-- إعدادات → صفحة إعدادات النظام (أو الملف الشخصي إن لم تتوفر الصلاحية) -->
                <a href="{{ $adminNavSettingsUrl }}" title="إعدادات النظام" aria-label="إعدادات النظام" class="admin-nav-icon-btn flex items-center justify-center active:scale-95">
                    <i class="fas fa-cog text-sm"></i>
                </a>

                <!-- Divider -->
                <div class="hidden lg:block w-px h-8 bg-slate-200/80 mx-1"></div>

                <!-- Profile dropdown -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click.stop="open = !open" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-slate-50 transition-all active:scale-[0.98]" :aria-expanded="open">
                        @if(auth()->user()->profile_image)
                            <img src="{{ auth()->user()->profile_image_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-xl object-cover ring-2 ring-slate-100" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <div class="w-9 h-9 rounded-xl hidden flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, var(--admin-primary), var(--admin-purple));">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                        @else
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, var(--admin-primary), var(--admin-purple));">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="hidden lg:block text-right">
                            <p class="text-[13px] font-semibold text-slate-700 max-w-[100px] truncate leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 leading-tight">مدير</p>
                        </div>
                        <i class="fas fa-chevron-down text-slate-300 text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-56 rounded-2xl bg-white shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden" style="z-index: 9999;">
                        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate mt-0.5">{{ auth()->user()->email ?? auth()->user()->phone }}</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                                <i class="fas fa-home w-4 text-slate-400 text-xs"></i> لوحة التحكم
                            </a>
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                                <i class="fas fa-user w-4 text-slate-400 text-xs"></i> الملف الشخصي
                            </a>
                            <a href="{{ $adminNavSettingsUrl }}" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition-colors">
                                <i class="fas fa-cog w-4 text-slate-400 text-xs"></i> إعدادات النظام
                            </a>
                        </div>
                        <div class="border-t border-slate-100 py-1.5">
                            <form method="POST" action="{{ route('logout') }}" data-turbo="false">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full text-right px-4 py-2.5 text-[13px] text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                    <i class="fas fa-sign-out-alt w-4 text-slate-400 text-xs"></i> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main id="admin-main-scroll" class="admin-main-scroll flex-1 overflow-y-auto overflow-x-hidden min-h-0">
            <div class="px-5 lg:px-8 pt-5 space-y-3">
                @if(session('success'))
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-emerald-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1">{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-300 hover:text-emerald-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-rose-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1">{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-rose-300 hover:text-rose-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-amber-50 border border-amber-100 text-amber-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation-triangle text-amber-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1">{{ session('warning') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-amber-300 hover:text-amber-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                @endif
            </div>

            <div class="px-5 lg:px-8 py-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    @include('partials.admin-turbo-nav')
</body>
</html>

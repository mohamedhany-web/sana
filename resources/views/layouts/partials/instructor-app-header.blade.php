@php
    $isRtl = app()->getLocale() === 'ar';
    $currentUser = auth()->user();
    $audiences = [null, 'instructor', 'teacher'];

    $navNotificationsQuery = $currentUser
        ? $currentUser->customNotifications()
            ->with('sender')
            ->whereIn('audience', $audiences)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
        : null;

    $navUnreadCount = $navNotificationsQuery
        ? (clone $navNotificationsQuery)->where('is_read', false)->count()
        : 0;

    $navRecentNotifications = $navNotificationsQuery
        ? (clone $navNotificationsQuery)->orderBy('created_at', 'desc')->limit(8)->get()
        : collect();

    $pageTitle = trim($__env->yieldContent('header')) !== ''
        ? trim($__env->yieldContent('header'))
        : (trim($__env->yieldContent('title')) !== '' ? trim($__env->yieldContent('title')) : __('instructor.dashboard'));

    $onDashboard = request()->routeIs('dashboard');
@endphp

<header class="ins-topbar flex items-center justify-between gap-3 px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="ins-icon-btn lg:hidden flex-shrink-0" aria-label="القائمة">
            <i class="fas fa-bars text-sm"></i>
        </button>

        <div class="min-w-0 hidden sm:block">
            @if(!$onDashboard)
                <p class="ins-page-breadcrumb mb-0.5">
                    <a href="{{ route('dashboard') }}" class="text-[#283593] hover:underline no-underline">{{ __('instructor.dashboard') }}</a>
                    <span class="mx-1 opacity-40">/</span>
                    <span>{{ $pageTitle }}</span>
                </p>
            @endif
            <h1 class="ins-page-title m-0">{{ $onDashboard ? __('instructor.dashboard') : $pageTitle }}</h1>
        </div>

        <form action="{{ route('instructor.courses.index') }}" method="get" class="ins-topbar-search hidden md:flex min-w-0 ms-auto max-w-xs lg:max-w-sm">
            <i class="fas fa-search text-slate-400 text-sm flex-shrink-0"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('common.nav_search_placeholder_long') }}" autocomplete="off">
        </form>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        @if(Route::has('instructor.classroom.index') && $currentUser->hasSubscriptionFeature('classroom_access'))
        <a href="{{ route('instructor.classroom.create') }}" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-[#283593] hover:bg-[#1F2A7A] text-white text-xs font-bold transition-colors no-underline shadow-sm shadow-indigo-900/15">
            <i class="fas fa-video text-[11px]"></i>
            <span>لايف</span>
        </a>
        @endif

        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="ins-icon-btn relative" aria-label="{{ $isRtl ? 'الإشعارات' : 'Notifications' }}">
                <i class="fas fa-bell text-sm"></i>
                @if($navUnreadCount > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-[#FB5607] rounded-full text-[9px] font-bold text-white flex items-center justify-center border-2 border-white">
                        {{ $navUnreadCount > 99 ? '99+' : $navUnreadCount }}
                    </span>
                @endif
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute left-0 mt-2 w-80 ins-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">{{ $isRtl ? 'الإشعارات' : 'Notifications' }}</h3>
                    @if(Route::has('notifications'))
                        <a href="{{ route('notifications') }}" class="text-xs font-bold text-[#283593] hover:underline">
                            {{ $isRtl ? 'عرض الكل' : 'View all' }}
                        </a>
                    @endif
                </div>
                @if($navRecentNotifications->isNotEmpty())
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($navRecentNotifications as $notification)
                            @php
                                $notificationUrl = $notification->action_url ?: (Route::has('notifications.show') ? route('notifications.show', $notification) : '#');
                            @endphp
                            <a href="{{ $notificationUrl }}" class="block px-4 py-3 border-b border-slate-50 hover:bg-[#FFE5F7]/40 transition-colors no-underline text-inherit">
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-[#eef2ff] text-[#283593] flex items-center justify-center flex-shrink-0">
                                        <i class="{{ $notification->type_icon }} text-xs"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-bold text-slate-800 truncate">{{ $notification->title }}</p>
                                            @if(!$notification->is_read)
                                                <span class="w-2 h-2 rounded-full bg-[#FB5607] flex-shrink-0"></span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                        <p class="text-[11px] text-slate-400 mt-1">{{ optional($notification->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400 text-sm">
                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-40"></i>
                        <p>{{ $isRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications' }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="ins-user-chip">
                <div class="u-avatar flex-shrink-0 text-sm font-bold">
                    @if($currentUser->profile_image)
                        <img src="{{ $currentUser->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-[10px]">
                    @else
                        {{ mb_substr($currentUser->name, 0, 1) }}
                    @endif
                </div>
                <span class="hidden lg:block text-sm font-bold text-slate-700 max-w-[110px] truncate">{{ $currentUser->name }}</span>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden lg:block transition-transform" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute left-0 mt-2 w-56 ins-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $currentUser->name }}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ $currentUser->email ?? '—' }}</p>
                    <span class="inline-flex mt-2 text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md bg-[#FFE5F7] text-[#283593]">{{ __('instructor.instructor_role') }}</span>
                </div>
                <div class="p-1.5 space-y-0.5">
                    <a href="{{ route('instructor.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                        <i class="fas fa-user text-slate-400 w-4 text-center text-xs"></i>
                        {{ __('instructor.profile') }}
                    </a>
                    <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                        <i class="fas fa-cog text-slate-400 w-4 text-center text-xs"></i>
                        {{ __('instructor.settings') }}
                    </a>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                        <i class="fas fa-external-link-alt text-slate-400 w-4 text-center text-xs"></i>
                        {{ $isRtl ? 'الموقع العام' : 'Public site' }}
                    </a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 text-right">
                            <i class="fas fa-sign-out-alt text-xs w-4"></i>
                            {{ __('instructor.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

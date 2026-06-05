@php
    $isRtl = app()->getLocale() === 'ar';
    $currentUser = auth()->user();
    $audiences = [null, 'student'];

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

    $coursesSearchUrl = Route::has('public.courses') ? route('public.courses') : url('/courses');
@endphp

<header class="stu-topbar flex items-center justify-between gap-3 px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="stu-icon-btn lg:hidden flex-shrink-0" aria-label="القائمة">
            <i class="fas fa-bars text-sm"></i>
        </button>

        @hasPermission('student.view.courses')
        <form action="{{ $coursesSearchUrl }}" method="get" class="stu-topbar-search hidden sm:flex min-w-0">
            <i class="fas fa-search text-slate-400 text-sm flex-shrink-0"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('common.nav_search_placeholder_long') }}" autocomplete="off">
        </form>
        @else
        <div class="hidden sm:block flex-1 max-w-md">
            <p class="text-sm font-bold text-slate-700 truncate">{{ __('student.dashboard_title') }}</p>
        </div>
        @endhasPermission
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="stu-icon-btn relative" aria-label="{{ __('student.notifications') }}">
                <i class="fas fa-bell text-sm"></i>
                @if($navUnreadCount > 0)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-[#FB5607] rounded-full text-[9px] font-bold text-white flex items-center justify-center border-2 border-white">
                        {{ $navUnreadCount > 99 ? '99+' : $navUnreadCount }}
                    </span>
                @endif
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute left-0 mt-2 w-80 stu-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('student.notifications') }}</h3>
                    @if(Route::has('notifications'))
                        <a href="{{ route('notifications') }}" class="text-xs font-bold text-[#283593] hover:underline">
                            {{ __('student.view_all') }}
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
            <button type="button" @click="open = !open" class="stu-user-chip">
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
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute left-0 mt-2 w-56 stu-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $currentUser->name }}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ $currentUser->email ?? '—' }}</p>
                </div>
                <div class="p-1.5 space-y-0.5">
                    <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                        <i class="fas fa-user text-slate-400 w-4 text-center text-xs"></i>
                        {{ __('student.profile') }}
                    </a>
                    <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 no-underline">
                        <i class="fas fa-cog text-slate-400 w-4 text-center text-xs"></i>
                        {{ __('student.settings') }}
                    </a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 text-right">
                            <i class="fas fa-sign-out-alt text-xs w-4"></i>
                            {{ $isRtl ? 'تسجيل الخروج' : 'Sign out' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

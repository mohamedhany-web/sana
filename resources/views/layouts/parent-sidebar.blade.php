<div class="flex flex-col h-full min-h-0">
    <div class="ins-sidebar-brand px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-white/80 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors z-10 border border-slate-200/80">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="relative z-10 pe-8 lg:pe-0">
            @include('partials.platform-brand', [
                'variant' => 'sidebar',
                'href' => route('parent.dashboard'),
                'subtitle' => __('parent.learning_center'),
                'showTagline' => true,
            ])
        </div>
    </div>

    @php
        $parent = auth()->user();
        $children = $parent->linkedStudents()->get();
        $studentIds = $children->pluck('id');
        $avgProgress = $studentIds->isEmpty() ? 0 : (int) round(
            \App\Models\StudentCourseEnrollment::query()
                ->whereIn('user_id', $studentIds)
                ->whereIn('status', ['active', 'completed'])
                ->avg('progress') ?? 0
        );
    @endphp

    <div class="stu-sidebar-stats">
        <div class="stu-sidebar-stats-grid">
            <a href="{{ route('parent.children.index') }}" class="stu-sidebar-stat group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-child text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-teal-700 uppercase tracking-wider">{{ __('parent.children') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums">{{ $children->count() }}</div>
            </a>
            <div class="stu-sidebar-stat">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-chart-line text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">{{ __('parent.progress') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums">{{ $avgProgress }}%</div>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        <div class="ins-nav-group">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[9px] opacity-50"></i>
                الرئيسية
            </span>
        </div>
        <a href="{{ route('parent.dashboard') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
            <span class="ins-icon bg-teal-100 text-teal-600">
                <i class="fas fa-th-large text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('parent.dashboard') }}</span>
        </a>
        @if(Route::has('parent.tutor-lessons.hub'))
        <a href="{{ route('parent.tutor-lessons.hub') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.tutor-lessons.*') ? 'active' : '' }}">
            <span class="ins-icon bg-violet-100 text-violet-600">
                <i class="fas fa-chalkboard-user text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('tutor.parent_hub_title') }}</span>
        </a>
        @endif

        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-eye text-[9px] opacity-50"></i>
                {{ __('parent.children_monitoring') }}
            </span>
        </div>
        <a href="{{ route('parent.children.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.children.*') ? 'active' : '' }}">
            <span class="ins-icon bg-emerald-100 text-emerald-600">
                <i class="fas fa-users text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('parent.children') }}</span>
            @if($children->count() > 0)
                <span class="ins-nav-badge bg-teal-50 text-teal-700">{{ $children->count() }}</span>
            @endif
        </a>
        <a href="{{ route('parent.reports.index') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.reports.*') ? 'active' : '' }}">
            <span class="ins-icon bg-cyan-100 text-cyan-600">
                <i class="fas fa-file-lines text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('parent.reports') }}</span>
        </a>

        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                {{ __('parent.account') }}
            </span>
        </div>
        <a href="{{ route('parent.profile') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.profile') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-id-card text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('parent.profile') }}</span>
        </a>
        <a href="{{ route('parent.settings') }}" @click="if (window.innerWidth < 1024) sidebarOpen = false"
           class="ins-nav {{ request()->routeIs('parent.settings') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-cog text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('parent.settings') }}</span>
        </a>
    </nav>

    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar flex-shrink-0 w-10 h-10 rounded-xl">
                @if($parent->profile_image)
                    <img src="{{ $parent->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr($parent->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $parent->name }}</p>
                <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ __('parent.guardian_role') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>

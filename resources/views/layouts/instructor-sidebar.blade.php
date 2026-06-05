<div class="flex flex-col h-full">
    {{-- Brand --}}
    <div class="ins-sidebar-brand px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-white/80 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors z-10 border border-slate-200/80">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="relative z-10 pe-8 lg:pe-0">
            @include('partials.platform-brand', [
                'variant' => 'sidebar',
                'href' => route('dashboard'),
                'subtitle' => __('instructor.instructor_panel'),
                'showTagline' => true,
            ])
        </div>
    </div>

    {{-- Stats cards --}}
    @php
        $user = auth()->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
    @endphp
    <div class="ins-sidebar-stats flex-shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('instructor.courses.index') }}" class="ins-sidebar-stat group no-underline text-inherit">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-[#FFE5F7] text-[#283593] flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-open text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-[#283593] uppercase tracking-wider">{{ __('instructor.courses') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums">{{ $myCoursesCount }}</div>
            </a>
            <a href="{{ route('instructor.courses.index') }}" class="ins-sidebar-stat group no-underline text-inherit">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">{{ __('instructor.students') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums">{{ $totalStudents }}</div>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        @php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        @endphp

        {{-- ─── نظرة عامة ─── --}}
        <div class="ins-nav-group">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[9px] opacity-50"></i>
                {{ __('instructor.overview') }}
            </span>
        </div>
        @if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks'))

            <a href="{{ route('dashboard') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="ins-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-th-large text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.dashboard') }}</span>
            </a>

            @if($isInstructor || $user->hasPermission('instructor.view.courses'))
            <a href="{{ route('instructor.courses.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}">
                <span class="ins-icon bg-indigo-100 text-indigo-600">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.my_courses') }}</span>
                @if($myCoursesCount > 0)
                    <span class="ins-nav-badge bg-indigo-100 text-indigo-700">{{ $myCoursesCount }}</span>
                @endif
            </a>
            @endif

            {{-- ─── أدوات التدريس ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-tools text-[9px] opacity-50"></i>
                    أدوات التدريس
                </span>
            </div>

            @if($isInstructor || $user->hasPermission('instructor.manage.lectures'))
            <a href="{{ route('instructor.lectures.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.lectures.*') ? 'active' : '' }}">
                <span class="ins-icon bg-violet-100 text-violet-600">
                    <i class="fas fa-chalkboard text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.lectures') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.assignments'))
            <a href="{{ route('instructor.assignments.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.assignments.*') ? 'active' : '' }}">
                <span class="ins-icon bg-amber-100 text-amber-600">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.assignments') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.exams'))
            <a href="{{ route('instructor.exams.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.exams.*') ? 'active' : '' }}">
                <span class="ins-icon bg-rose-100 text-rose-600">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.exams') }}</span>
            </a>
            @endif

            @if($isInstructor)
            <a href="{{ route('instructor.question-banks.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'active' : '' }}">
                <span class="ins-icon bg-teal-100 text-teal-600">
                    <i class="fas fa-database text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.question_banks') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.attendance'))
            <a href="{{ route('instructor.attendance.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.attendance.*') ? 'active' : '' }}">
                <span class="ins-icon bg-cyan-100 text-cyan-600">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.attendance') }}</span>
            </a>
            @endif

            @if(Route::has('instructor.classroom.index') && auth()->user()?->hasSubscriptionFeature('classroom_access'))
            <a href="{{ route('instructor.classroom.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.classroom.*') ? 'active' : '' }}">
                <span class="ins-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-chalkboard-teacher text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('platform.classroom') }}</span>
            </a>
            @endif

            @if(Route::has('instructor.tutor-lessons.hub'))
            <a href="{{ route('instructor.tutor-lessons.hub') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.tutor-lessons.*') ? 'active' : '' }}">
                <span class="ins-icon bg-violet-100 text-violet-600">
                    <i class="fas fa-user-clock text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('tutor.hub_title') }}</span>
            </a>
            @endif

            @if(Route::has('instructor.live-sessions.index'))
            <a href="{{ route('instructor.live-sessions.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.live-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon bg-red-100 text-red-600">
                    <i class="fas fa-broadcast-tower text-sm"></i>
                </span>
                <span class="flex-1 truncate">البث المباشر</span>
                @php $liveCount = \App\Models\LiveSession::where('instructor_id', auth()->id())->where('status', 'live')->count(); @endphp
                @if($liveCount > 0)
                    <span class="ins-nav-badge bg-red-100 text-red-600">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span>{{ $liveCount }}
                    </span>
                @endif
            </a>
            @endif

            @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'active' : '' }}">
                <span class="ins-icon bg-teal-100 text-teal-600">
                    <i class="fas fa-calendar-check text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.calendar') }}</span>
            </a>
            @endif

            {{-- ─── الإدارة ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-briefcase text-[9px] opacity-50"></i>
                    الإدارة
                </span>
            </div>

            @if($isInstructor || $user->hasPermission('instructor.view.tasks'))
            <a href="{{ route('instructor.tasks.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.tasks.*') ? 'active' : '' }}">
                <span class="ins-icon bg-orange-100 text-orange-600">
                    <i class="fas fa-check-square text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.tasks_from_management') }}</span>
            </a>
            @endif

            @if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index'))
            <a href="{{ route('instructor.management-requests.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.management-requests.*') ? 'active' : '' }}">
                <span class="ins-icon bg-sky-100 text-sky-600">
                    <i class="fas fa-paper-plane text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.submit_requests_to_management') }}</span>
            </a>
            @endif

            <a href="{{ route('instructor.agreements.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.agreements.*') ? 'active' : '' }}">
                <span class="ins-icon bg-purple-100 text-purple-600">
                    <i class="fas fa-handshake text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.agreements_system') }}</span>
            </a>

            {{-- ─── المالية ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-coins text-[9px] opacity-50"></i>
                    المالية
                </span>
            </div>

            <a href="{{ route('instructor.transfer-account.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.transfer-account.*') ? 'active' : '' }}">
                <span class="ins-icon bg-emerald-100 text-emerald-600">
                    <i class="fas fa-university text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.transfer_account') }}</span>
            </a>

            <a href="{{ route('instructor.withdrawals.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.withdrawals.*') ? 'active' : '' }}">
                <span class="ins-icon bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.withdrawal_requests') }}</span>
            </a>

            <a href="{{ route('instructor.personal-branding.edit') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.personal-branding.*') ? 'active' : '' }}">
                <span class="ins-icon bg-fuchsia-100 text-fuchsia-600">
                    <i class="fas fa-user-tie text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.personal_branding') }}</span>
            </a>
        @endif

        {{-- ─── الحساب ─── --}}
        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                الحساب
            </span>
        </div>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <span class="ins-icon bg-red-100 text-red-600">
                    <i class="fas fa-shield-alt text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.admin_panel') }}</span>
            </a>
        @endif

        <a href="{{ route('instructor.profile') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav {{ request()->routeIs('instructor.profile*') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-user text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('instructor.profile') }}</span>
        </a>

        @if(auth()->check() && auth()->user()->hasPermission('student.view.settings'))
        <a href="{{ route('settings') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-cog text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('instructor.settings') }}</span>
        </a>
        @endif
    </nav>

    {{-- User card --}}
    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar w-10 h-10 flex-shrink-0 rounded-xl">
                @if(auth()->user()->profile_image)
                    <img src="{{ auth()->user()->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ __('instructor.instructor_role') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="{{ __('instructor.logout') }}">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>

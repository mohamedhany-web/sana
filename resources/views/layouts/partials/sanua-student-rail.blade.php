{{-- شريط SANUA — يمين، قابل للتمرير --}}
@php
    $user = auth()->user();
    $isStudent = $user->role === 'student' || strtolower((string) $user->role) === 'student';

    $candidates = [
        ['route' => 'dashboard', 'match' => ['dashboard'], 'icon' => 'fa-house', 'label' => 'الرئيسية', 'show' => true],
        ['route' => 'student.tutor-lessons.hub', 'match' => ['student.tutor-lessons.*'], 'icon' => 'fa-chalkboard-user', 'label' => 'دروس', 'show' => Route::has('student.tutor-lessons.hub')],
        ['route' => 'my-courses.index', 'match' => ['my-courses.*'], 'icon' => 'fa-book-open', 'label' => 'كورساتي', 'show' => $isStudent || $user->hasPermission('student.view.my-courses')],
        ['route' => 'public.courses', 'match' => ['public.courses', 'public.course.*', 'courses.show', 'academic-years*', 'subjects.*'], 'icon' => 'fa-compass', 'label' => 'استكشف', 'show' => $isStudent || $user->hasPermission('student.view.courses')],
        ['route' => 'student.live-sessions.index', 'match' => ['student.live-sessions.*'], 'icon' => 'fa-tower-broadcast', 'label' => 'بث', 'show' => Route::has('student.live-sessions.index')],
        ['route' => 'student.live-recordings.index', 'match' => ['student.live-recordings.*'], 'icon' => 'fa-circle-play', 'label' => 'تسجيلات', 'show' => Route::has('student.live-recordings.index')],
        ['route' => 'student.assignments.index', 'match' => ['student.assignments.*'], 'icon' => 'fa-list-check', 'label' => 'واجبات', 'show' => $isStudent && Route::has('student.assignments.index')],
        ['route' => 'student.exams.index', 'match' => ['student.exams.*'], 'icon' => 'fa-clipboard-check', 'label' => 'اختبارات', 'show' => ($isStudent || $user->hasPermission('student.view.exams')) && Route::has('student.exams.index')],
        ['route' => 'student.certificates.index', 'match' => ['student.certificates.*'], 'icon' => 'fa-award', 'label' => 'شهادات', 'show' => ($isStudent || $user->hasPermission('student.view.certificates')) && Route::has('student.certificates.index')],
        ['route' => 'student.achievements.index', 'match' => ['student.achievements.*'], 'icon' => 'fa-trophy', 'label' => 'إنجازات', 'show' => Route::has('student.achievements.index')],
        ['route' => 'student.wallet.index', 'match' => ['student.wallet.*'], 'icon' => 'fa-wallet', 'label' => 'محفظة', 'show' => ($isStudent || $user->hasPermission('student.view.wallet')) && Route::has('student.wallet.index')],
        ['route' => 'orders.index', 'match' => ['orders.*'], 'icon' => 'fa-bag-shopping', 'label' => 'طلبات', 'show' => ($isStudent || $user->hasPermission('student.view.orders')) && Route::has('orders.index')],
        ['route' => 'calendar', 'match' => ['calendar'], 'icon' => 'fa-calendar-days', 'label' => 'تقويم', 'show' => ($isStudent || $user->hasPermission('student.view.calendar')) && Route::has('calendar')],
        ['route' => 'student.ai-usages.index', 'match' => ['student.ai-usages.*'], 'icon' => 'fa-flask', 'label' => 'AI', 'show' => $isStudent && $user->canAccessStudentAiUsages() && Route::has('student.ai-usages.index')],
        ['route' => 'student.my-subscription', 'match' => ['student.my-subscription'], 'icon' => 'fa-gem', 'label' => 'اشتراك', 'show' => Route::has('student.my-subscription') && $user->activeSubscription()],
        ['route' => 'referrals.index', 'match' => ['referrals.*'], 'icon' => 'fa-user-group', 'label' => 'إحالات', 'show' => $isStudent && Route::has('referrals.index')],
        ['route' => 'notifications', 'match' => ['notifications*'], 'icon' => 'fa-bell', 'label' => 'إشعارات', 'show' => ($isStudent || $user->hasPermission('student.view.notifications')) && Route::has('notifications')],
        ['route' => 'profile', 'match' => ['profile'], 'icon' => 'fa-user', 'label' => 'حسابي', 'show' => ($isStudent || $user->hasPermission('student.view.profile')) && Route::has('profile')],
        ['route' => 'settings', 'match' => ['settings'], 'icon' => 'fa-gear', 'label' => 'إعدادات', 'show' => ($isStudent || $user->hasPermission('student.view.settings')) && Route::has('settings')],
    ];

    $navItems = array_values(array_filter($candidates, function ($item) {
        return $item['show'] && Route::has($item['route']);
    }));
@endphp

<nav class="sanua-rail-nav" aria-label="تنقل الطالب">
    @foreach($navItems as $item)
        @php $active = request()->routeIs($item['match']); @endphp
        <a href="{{ route($item['route']) }}"
           @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
           class="sanua-rail-link {{ $active ? 'is-active' : '' }}"
           title="{{ $item['label'] }}">
            <span class="sanua-rail-icon"><i class="fas {{ $item['icon'] }}"></i></span>
            <span class="sanua-rail-label">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

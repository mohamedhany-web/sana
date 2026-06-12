@php
    $links = [
        ['route' => 'admin.tutor-lessons.index', 'label' => 'لوحة الرقابة', 'icon' => 'fa-chart-line'],
        ['route' => 'admin.tutor-lessons.bookings', 'label' => 'كل الحجوزات', 'icon' => 'fa-calendar-check'],
        ['route' => 'admin.tutor-lessons.group-offers.index', 'active' => 'admin.tutor-lessons.group-offers.*', 'label' => 'عروض المجموعات', 'icon' => 'fa-users-rectangle'],
        ['route' => 'admin.tutor-lessons.instructors', 'label' => 'المعلمون', 'icon' => 'fa-chalkboard-teacher'],
        ['route' => 'admin.tutor-lessons.assisted.index', 'label' => 'طلبات المساعدة', 'icon' => 'fa-hands-helping'],
        ['route' => 'admin.tutor-lessons.settings', 'label' => 'إعدادات حصص الطلاب', 'icon' => 'fa-cog'],
    ];
@endphp
<nav class="flex flex-wrap gap-2 mb-6">
    @foreach($links as $link)
        <a href="{{ route($link['route']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition-colors {{ request()->routeIs($link['route']) || request()->routeIs($link['active'] ?? ($link['route'].'.*')) ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-slate-700 border-slate-200 hover:border-violet-300' }}">
            <i class="fas {{ $link['icon'] }}"></i>
            {{ $link['label'] }}
        </a>
    @endforeach
</nav>

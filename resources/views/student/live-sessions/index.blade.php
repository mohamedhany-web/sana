@extends('layouts.app')

@section('title', 'جلسات البث المباشر')
@section('header', 'جلسات البث المباشر')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    {{-- رأس الصفحة --}}
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-gray-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 text-red-600 shrink-0">
                        <i class="fas fa-broadcast-tower"></i>
                    </span>
                    <span>جلسات البث المباشر</span>
                </h1>
                <p class="text-sm text-gray-500">الجلسات المباشرة والمجدولة المتاحة لك وفق كورساتك</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="{{ route('my-courses.index') }}" class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    <i class="fas fa-book-open"></i>
                    كورساتي
                </a>
                <a href="{{ route('student.live-recordings.index') }}" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold hover:border-sky-300 transition-colors">
                    <i class="fas fa-play-circle text-sky-600"></i>
                    التسجيلات
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    {{-- فلاتر --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('student.live-sessions.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-sky-500 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-sky-300' }}">الكل</a>
        <a href="{{ route('student.live-sessions.index', ['status' => 'live']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'live' ? 'bg-red-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-300' }}">مباشر</a>
        <a href="{{ route('student.live-sessions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'scheduled' ? 'bg-sky-500 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-sky-300' }}">مجدولة</a>
    </div>

    {{-- مباشر الآن --}}
    @if($liveSessions->count() > 0 && (!request('status') || request('status') === 'live'))
    <div class="space-y-3">
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            مباشر الآن
        </h2>
        @foreach($liveSessions as $live)
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4 ring-1 ring-red-100/80">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                        مباشر
                    </span>
                    @if($live->course)
                        <span class="text-xs text-gray-500 truncate max-w-full">{{ $live->course->title }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $live->title }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    <i class="fas fa-chalkboard-teacher text-sky-600 ml-1"></i>{{ $live->instructor?->name ?? '—' }}
                    @if($live->started_at)
                        <span class="text-gray-300 mx-2">•</span>
                        <span class="text-gray-500">بدأ {{ $live->started_at->diffForHumans() }}</span>
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('student.live-sessions.join', $live) }}" class="shrink-0">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-md shadow-red-500/20 transition-colors">
                    <i class="fas fa-video"></i>
                    انضم الآن
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    {{-- جلسات مجدولة (مخفية عند اختيار فلتر «مباشر» فقط) --}}
    @if(request('status') !== 'live')
    <div>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">
            {{ request('status') === 'scheduled' ? 'الجلسات المجدولة' : 'الجلسات القادمة' }}
        </h2>

        @php
            $listSessions = $sessions->filter(fn ($s) => $s->status !== 'live');
        @endphp

        @if($listSessions->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center shadow-sm">
                <i class="fas fa-calendar-alt text-4xl text-gray-300 mb-4"></i>
                <p class="font-medium text-gray-700">لا توجد جلسات مجدولة {{ request('status') === 'scheduled' ? 'حالياً' : 'في هذه الصفحة' }}</p>
                <p class="text-sm text-gray-500 mt-1">ستُعرض الجلسات عند جدولتها من قبل المدرب</p>
                <a href="{{ route('my-courses.index') }}" class="inline-flex items-center gap-2 mt-4 text-sky-600 hover:text-sky-700 font-semibold text-sm">
                    <i class="fas fa-book-open"></i> تصفح كورساتي
                </a>
            </div>
        @else
        <div class="space-y-3">
            @foreach($sessions as $session)
            @if($session->status !== 'live')
            <a href="{{ route('student.live-sessions.show', $session) }}" class="block bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-sky-300 hover:shadow transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-semibold">مجدولة</span>
                            @if($session->course)
                                <span class="text-xs text-gray-500 truncate">{{ $session->course->title }}</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $session->title }}</h3>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-600">
                            <span><i class="fas fa-chalkboard-teacher text-sky-600 ml-1"></i>{{ $session->instructor?->name ?? '—' }}</span>
                            <span><i class="fas fa-calendar ml-1 text-gray-400"></i>{{ $session->scheduled_at?->format('Y/m/d') }}</span>
                            <span><i class="fas fa-clock ml-1 text-gray-400"></i>{{ $session->scheduled_at?->format('H:i') }}</span>
                        </div>
                        @if($session->description)
                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit($session->description, 120) }}</p>
                        @endif
                    </div>
                    <div class="shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-50 text-sky-700 text-sm font-semibold">
                            التفاصيل
                            <i class="fas fa-chevron-left text-xs opacity-70"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endif
            @endforeach
        </div>
        @endif
    </div>
    @elseif($liveSessions->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center shadow-sm">
            <i class="fas fa-broadcast-tower text-4xl text-gray-300 mb-4"></i>
            <p class="font-medium text-gray-700">لا توجد جلسات مباشرة حالياً</p>
            <p class="text-sm text-gray-500 mt-1">عند بدء المدرب للبث ستظهر الجلسة أعلاه</p>
        </div>
    @endif

    @if($sessions->hasPages())
        <div class="pt-2">{{ $sessions->links() }}</div>
    @endif
</div>
@endsection

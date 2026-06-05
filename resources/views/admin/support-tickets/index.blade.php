@extends('layouts.admin')

@section('title', 'دعم الطلاب')
@section('header', 'دعم الطلاب')

@section('content')
@php
    $view = $view ?? 'needs_reply';
    $statCards = [
        [
            'label' => 'يحتاج ردّكم',
            'value' => number_format($stats['needs_reply'] ?? 0),
            'icon' => 'fa-reply',
            'bg' => 'bg-rose-100',
            'text' => 'text-rose-600',
            'desc' => 'تذكرة بانتظار فريق الدعم',
            'filter' => 'needs_reply',
        ],
        [
            'label' => 'مفتوحة',
            'value' => number_format($stats['open'] ?? 0),
            'icon' => 'fa-inbox',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'desc' => 'لم تُعالج بعد',
            'filter' => 'active',
        ],
        [
            'label' => 'عاجلة نشطة',
            'value' => number_format($stats['urgent_active'] ?? 0),
            'icon' => 'fa-bolt',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-700',
            'desc' => 'أولوية عاجلة',
            'filter' => 'active',
            'extra' => ['priority' => 'urgent'],
        ],
        [
            'label' => 'جديدة اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fa-calendar-day',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'desc' => 'وُردت خلال 24 ساعة',
            'filter' => 'all',
        ],
    ];
    $quickViews = [
        'needs_reply' => ['label' => 'بانتظار الرد', 'icon' => 'fa-hourglass-half'],
        'active' => ['label' => 'نشطة', 'icon' => 'fa-spinner'],
        'all' => ['label' => 'الكل', 'icon' => 'fa-list'],
        'closed' => ['label' => 'مغلقة / محلولة', 'icon' => 'fa-check-double'],
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-sky-50 to-white border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">دعم الطلاب</h2>
                    <p class="text-sm text-slate-600 mt-0.5">متابعة تذاكر الطلاب، الرد السريع، وإغلاق المشكلات — {{ number_format($stats['total'] ?? 0) }} تذكرة إجمالاً</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(Route::has('admin.support-inquiry-categories.index'))
                    <a href="{{ route('admin.support-inquiry-categories.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors">
                        <i class="fas fa-tags"></i>
                        تصنيفات الاستفسار
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach($statCards as $card)
                @php
                    $params = array_filter([
                        'view' => $card['filter'],
                        'priority' => $card['extra']['priority'] ?? null,
                        'status' => request('status'),
                        'category_id' => request('category_id'),
                        'search' => request('search'),
                    ]);
                @endphp
                <a href="{{ route('admin.support-tickets.index', $params) }}"
                   class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md hover:border-sky-300 transition-all block">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-slate-600">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900 mt-1">{{ $card['value'] }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $card['desc'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center shrink-0">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @if($inquiryCategories->isNotEmpty())
        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-500 mb-3">تذاكر نشطة حسب التصنيف</p>
            <div class="flex flex-wrap gap-2">
                @foreach($inquiryCategories as $ic)
                    @php $cnt = (int) ($categoryCounts[$ic->id] ?? 0); @endphp
                    <a href="{{ route('admin.support-tickets.index', array_merge(request()->only(['view', 'status', 'priority', 'search']), ['category_id' => $ic->id, 'view' => 'active'])) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                       {{ (string) ($categoryId ?? '') === (string) $ic->id ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-indigo-300' }}">
                        {{ $ic->name }}
                        @if($cnt > 0)
                            <span class="min-w-[1.25rem] text-center px-1 rounded-full {{ (string) ($categoryId ?? '') === (string) $ic->id ? 'bg-white/20' : 'bg-indigo-100 text-indigo-800' }}">{{ $cnt }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 space-y-4">
            <div class="flex flex-wrap gap-2">
                @foreach($quickViews as $key => $qv)
                    <a href="{{ route('admin.support-tickets.index', array_merge(request()->except('page'), ['view' => $key])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold transition-colors
                       {{ $view === $key ? 'bg-sky-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100' }}">
                        <i class="fas {{ $qv['icon'] }} text-xs"></i>
                        {{ $qv['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="GET" class="flex flex-col lg:flex-row flex-wrap items-stretch lg:items-center gap-2">
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="flex-1 min-w-[200px]">
                    <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="بحث: اسم الطالب، الجوال، أو عنوان التذكرة…"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500">
                </div>
                <select name="category_id" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all">كل التصنيفات</option>
                    @foreach($inquiryCategories as $ic)
                        <option value="{{ $ic->id }}" @selected((string) ($categoryId ?? '') === (string) $ic->id)>{{ $ic->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all" @selected($status === 'all')>كل الحالات</option>
                    @foreach(\App\Models\SupportTicket::statusLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected($status === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <select name="priority" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <option value="all" @selected($priority === 'all')>كل الأولويات</option>
                    @foreach(\App\Models\SupportTicket::priorityLabels() as $val => $lbl)
                        <option value="{{ $val }}" @selected($priority === $val)>{{ $lbl }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700">تصفية</button>
                @if($search || $status !== 'all' || $priority !== 'all' || ($categoryId ?? 'all') !== 'all')
                    <a href="{{ route('admin.support-tickets.index', ['view' => $view]) }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">مسح</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-bold text-slate-600 uppercase tracking-wide">
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">التصنيف / الموضوع</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">آخر نشاط</th>
                        <th class="px-4 py-3 text-right">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php $awaiting = $ticket->isAwaitingAdminResponse(); @endphp
                        <tr class="hover:bg-slate-50/80 {{ $awaiting ? 'bg-rose-50/40' : '' }}">
                            <td class="px-4 py-3">
                                <p class="text-sm font-bold text-slate-900">{{ $ticket->user->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5" dir="ltr">{{ $ticket->user->phone ?? '' }}</p>
                                @if($awaiting)
                                    <span class="inline-flex mt-1 items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">
                                        <i class="fas fa-circle text-[6px]"></i> بانتظار ردّكم
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-indigo-700 font-semibold">{{ $ticket->inquiryCategory->name ?? 'عام' }}</p>
                                <p class="text-sm font-semibold text-slate-900 mt-0.5 line-clamp-2">{{ $ticket->subject }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @include('admin.support-tickets._badges', ['priority' => $ticket->priority])
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @include('admin.support-tickets._badges', ['status' => $ticket->status])
                                @if($ticket->assignedAdmin)
                                    <p class="text-[10px] text-slate-500 mt-1">مُعيَّنة: {{ $ticket->assignedAdmin->name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ optional($ticket->last_reply_at ?? $ticket->created_at)->format('Y-m-d') }}
                                <span class="text-slate-400">{{ optional($ticket->last_reply_at ?? $ticket->created_at)->format('H:i') }}</span>
                                <p class="text-[10px] mt-0.5 text-slate-400">#{{ $ticket->id }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.support-tickets.show', $ticket) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-bold {{ $awaiting ? 'bg-rose-600 text-white hover:bg-rose-700' : 'bg-sky-600 text-white hover:bg-sky-700' }}">
                                    {{ $awaiting ? 'رد الآن' : 'فتح' }}
                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="max-w-sm mx-auto">
                                    <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                    <p class="text-sm font-semibold text-slate-700">لا توجد تذاكر في هذا العرض</p>
                                    <p class="text-xs text-slate-500 mt-1">عندما يفتح الطالب تذكرة من لوحته ستظهر هنا فوراً.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">{{ $tickets->links() }}</div>
        @endif
    </section>
</div>
@endsection

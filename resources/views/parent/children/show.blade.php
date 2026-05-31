@extends('layouts.app')

@section('title', __('parent.child_progress', ['name' => $student->name]))

@section('content')
<div class="par-page space-y-5 sm:space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('parent.children.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 shrink-0" aria-label="رجوع">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="par-page-title truncate">{{ $student->name }}</h1>
            <p class="par-page-lead">{{ __('parent.progress') }}: <strong class="text-teal-600">{{ $progress }}%</strong></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-4 gap-4 sm:gap-5">
        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-book-open text-teal-600"></i> {{ __('parent.courses_enrolled') }}</h2>
            @if($enrollments->count() > 0)
            <ul class="space-y-3 text-sm">
                @foreach($enrollments as $enrollment)
                <li class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 border-b border-slate-100 pb-2">
                    <span class="text-slate-800 min-w-0">{{ $enrollment->course->title ?? '—' }}</span>
                    <span class="font-mono font-bold text-teal-600 shrink-0">{{ (int)($enrollment->progress ?? 0) }}%</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-slate-500 text-sm">لا توجد كورسات مسجّلة حالياً</p>
            @endif
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-tasks text-amber-600"></i> {{ __('parent.upcoming_assignments') }}</h2>
            @if($upcomingAssignments->count() > 0)
            <ul class="space-y-2 text-sm">
                @foreach($upcomingAssignments as $assignment)
                <li class="p-3 rounded-lg bg-slate-50">
                    <p class="font-semibold text-slate-800">{{ $assignment->title }}</p>
                    @if($assignment->due_date)
                    <p class="text-xs text-slate-500 mt-1">موعد التسليم: {{ $assignment->due_date->format('d/m/Y') }}</p>
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-slate-500 text-sm">لا توجد واجبات قادمة</p>
            @endif
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-receipt text-violet-600"></i> {{ __('parent.recent_orders') }}</h2>
            @if($recentOrders->count() > 0)
            <ul class="space-y-2 text-sm">
                @foreach($recentOrders as $order)
                <li class="flex flex-col sm:flex-row sm:justify-between gap-1">
                    <span class="min-w-0">{{ $order->course->title ?? $order->order_number ?? 'طلب' }}</span>
                    <span class="font-mono text-slate-600 shrink-0">{{ $order->status }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-slate-500 text-sm">لا توجد طلبات</p>
            @endif
        </div>

        <div class="par-card p-4 sm:p-6">
            <h2 class="font-bold mb-4 flex items-center gap-2 text-sm sm:text-base"><i class="fas fa-file-lines text-cyan-600"></i> {{ __('parent.student_reports') }}</h2>
            @if($reports->count() > 0)
            <ul class="space-y-2 text-sm">
                @foreach($reports as $report)
                <li class="flex flex-wrap items-center justify-between gap-2">
                    <span>{{ $report->report_month ?? $report->created_at->format('Y-m') }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100">{{ $report->status }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-slate-500 text-sm">لا توجد تقارير بعد</p>
            @endif
        </div>
    </div>
</div>
@endsection

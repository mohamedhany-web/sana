@extends('layouts.app')

@section('title', __('parent.dashboard'))

@section('content')
<div class="par-page space-y-5 sm:space-y-6">
    @if($showPasswordNotice ?? false)
    <div class="par-card rounded-2xl border-amber-200 bg-amber-50/90 px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <p class="font-bold text-amber-900">{{ __('parent.password_notice_title') }}</p>
            <p class="text-sm text-amber-800 mt-1">{{ __('parent.password_notice_body') }}</p>
        </div>
        <a href="{{ route('parent.profile') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shrink-0 w-full sm:w-auto">
            <i class="fas fa-key"></i> {{ __('parent.change_password') }}
        </a>
    </div>
    @endif

    <div class="par-hero par-card rounded-2xl border-teal-200 bg-gradient-to-l from-teal-50/80 to-white p-4 sm:p-6 lg:p-8">
        <h1 class="par-page-title font-heading">
            <i class="fas fa-people-roof text-teal-600 ml-2"></i>
            {{ __('parent.welcome', ['name' => auth()->user()->name]) }}
        </h1>
        <p class="par-page-lead">{{ __('parent.dashboard_subtitle') }}</p>
    </div>

    <div class="par-kpi-grid">
        @foreach([
            ['label' => __('parent.children'), 'value' => $stats['children_count'], 'icon' => 'fa-child', 'iconClass' => 'text-teal-600', 'bg' => 'bg-teal-50'],
            ['label' => __('parent.progress'), 'value' => $stats['avg_progress'].'%', 'icon' => 'fa-chart-line', 'iconClass' => 'text-amber-600', 'bg' => 'bg-amber-50'],
            ['label' => __('parent.active_courses'), 'value' => $stats['active_courses'], 'icon' => 'fa-book-open', 'iconClass' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            ['label' => __('parent.reports'), 'value' => $stats['reports_count'], 'icon' => 'fa-file-lines', 'iconClass' => 'text-cyan-600', 'bg' => 'bg-cyan-50'],
        ] as $card)
        <div class="par-card p-4 sm:p-5">
            <div class="flex items-center gap-2 mb-2 min-w-0">
                <span class="w-9 h-9 rounded-lg {{ $card['bg'] }} {{ $card['iconClass'] }} flex items-center justify-center shrink-0">
                    <i class="fas {{ $card['icon'] }} text-sm"></i>
                </span>
                <span class="text-[10px] sm:text-xs font-bold uppercase text-slate-600 truncate">{{ $card['label'] }}</span>
            </div>
            <div class="text-xl sm:text-2xl font-black text-slate-900 tabular-nums">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="par-card overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-bold text-slate-900">{{ __('parent.children') }}</h2>
            <a href="{{ route('parent.children.index') }}" class="text-sm font-semibold text-teal-600 hover:underline">{{ __('parent.view_child') }}</a>
        </div>
        @if($children->count() > 0)
        <div class="divide-y divide-slate-100">
            @foreach($children as $child)
            @php
                $enrollments = $child->courseEnrollments()->whereIn('status', ['active', 'completed'])->get();
                $prog = $enrollments->isEmpty() ? 0 : (int) round($enrollments->avg('progress') ?? 0);
                $rel = $child->pivot->relation ?? 'guardian';
                $relLabel = __('parent.relation_'.$rel);
            @endphp
            <a href="{{ route('parent.children.show', $child) }}" class="par-child-row flex items-center justify-between gap-4 px-4 sm:px-5 py-4 hover:bg-teal-50/50 transition-colors no-underline text-inherit">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <span class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold shrink-0">{{ mb_substr($child->name, 0, 1) }}</span>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900 truncate">{{ $child->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $relLabel }} · {{ $enrollments->count() }} {{ __('parent.active_courses') }}</p>
                    </div>
                </div>
                <div class="text-end shrink-0">
                    <p class="text-lg font-black text-teal-600 tabular-nums">{{ $prog }}%</p>
                    <p class="text-xs text-slate-500">{{ __('parent.progress') }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="p-8 sm:p-10 text-center text-slate-500">
            <i class="fas fa-child text-4xl opacity-30 mb-3"></i>
            <p class="font-semibold">{{ __('parent.no_children') }}</p>
            <p class="text-sm mt-1">{{ __('parent.no_children_hint') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

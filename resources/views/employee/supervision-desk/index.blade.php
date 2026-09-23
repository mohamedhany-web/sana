@extends('layouts.employee')

@section('title', __('لوحة الإشراف'))
@section('header', __('لوحة الإشراف'))

@section('content')
<div class="space-y-6">
    <p class="text-sm text-gray-600">{{ __('متابعة مهام الفريق والمهام المتأخرة عن الموعد النهائي.') }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-br from-white to-indigo-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('موظفون نشطون') }}</p>
            <p class="text-3xl font-black text-indigo-900 tabular-nums">{{ number_format($stats['employees_active']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-blue-200/60 bg-gradient-to-br from-white to-blue-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('مهام مفتوحة') }}</p>
            <p class="text-3xl font-black text-blue-900 tabular-nums">{{ number_format($stats['tasks_open']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-red-200/60 bg-gradient-to-br from-white to-red-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('مهام متأخرة') }}</p>
            <p class="text-3xl font-black text-red-800 tabular-nums">{{ number_format($stats['tasks_overdue']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200/60 bg-gradient-to-br from-white to-emerald-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">{{ __('مكتملة آخر أسبوع') }}</p>
            <p class="text-3xl font-black text-emerald-800 tabular-nums">{{ number_format($stats['tasks_done_week']) }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">{{ __('مهام متأخرة (تحتاج متابعة)') }}</h2>
            <span class="text-xs text-gray-500">{{ __('حتى 25 مهمة') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">{{ __('المهمة') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الموظف') }}</th>
                        <th class="text-right px-4 py-3">{{ __('المكلف') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الموعد') }}</th>
                        <th class="text-right px-4 py-3">{{ __('الحالة') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($atRiskTasks as $task)
                    <tr class="hover:bg-red-50/40">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $task->title }}</td>
                        <td class="px-4 py-3 text-gray-900">{{ $task->employee?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $task->assigner?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-red-700 font-semibold whitespace-nowrap">{{ $task->deadline?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            @if($task->status === 'pending')
                                <span class="text-xs font-bold text-amber-700">{{ __('معلّقة') }}</span>
                            @elseif($task->status === 'in_progress')
                                <span class="text-xs font-bold text-blue-700">{{ __('قيد التنفيذ') }}</span>
                            @else
                                <span class="text-xs text-gray-600">{{ $task->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">{{ __('لا توجد مهام متأخرة حالياً.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('parent.reports'))

@section('content')
<div class="par-page space-y-5 sm:space-y-6">
    <div>
        <h1 class="par-page-title font-heading">
            <i class="fas fa-file-lines text-teal-600 ml-2"></i>{{ __('parent.reports_monthly') }}
        </h1>
    </div>

    <div class="par-card overflow-hidden">
        @if($reports->count() > 0)
        <div class="par-report-table par-table-wrap">
            <table class="w-full text-sm min-w-[520px]">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold">الطالب</th>
                        <th class="px-4 py-3 text-right font-semibold">الشهر</th>
                        <th class="px-4 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reports as $report)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $report->student->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $report->report_month ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="text-xs px-2 py-1 rounded-full bg-teal-100 text-teal-800">{{ $report->status }}</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $report->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="par-report-cards divide-y divide-slate-100">
            @foreach($reports as $report)
            <div class="p-4 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-bold text-slate-900">{{ $report->student->name ?? '—' }}</p>
                    <span class="text-xs px-2 py-1 rounded-full bg-teal-100 text-teal-800 shrink-0">{{ $report->status }}</span>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span>الشهر: <strong class="text-slate-700">{{ $report->report_month ?? '—' }}</strong></span>
                    <span>التاريخ: <strong class="text-slate-700">{{ $report->created_at->format('d/m/Y') }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-4 py-3 border-t border-slate-200 overflow-x-auto">{{ $reports->links() }}</div>
        @else
        <p class="p-8 sm:p-10 text-center text-slate-500">لا توجد تقارير لأبنائك حالياً</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.employee')

@section('title', __('لوحة الموارد البشرية'))
@section('header', __('لوحة الموارد البشرية'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-gray-600 max-w-2xl">متابعة الموظفين، التوظيف والمقابلات، مراجعة الإجازات، وسجل HR. يمكن للإدارة أيضاً استخدام لوحة الإدارة للإعدادات المتقدمة.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('employee.hr.recruitment.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold shadow-sm">
                <i class="fas fa-user-tie"></i> التوظيف والمقابلات
            </a>
            <a href="{{ route('employee.hr.leaves.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-sm">
                <i class="fas fa-calendar-check"></i> طلبات الإجازات
            </a>
            <a href="{{ route('employee.hr.employees.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm">
                <i class="fas fa-address-book"></i> دليل الموظفين
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl border-2 border-indigo-200/60 bg-gradient-to-br from-white to-indigo-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">موظفون نشطون</p>
            <p class="text-3xl font-black text-indigo-900 tabular-nums">{{ number_format($stats['employees']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-slate-200/60 bg-gradient-to-br from-white to-slate-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">موقوفون</p>
            <p class="text-3xl font-black text-slate-800 tabular-nums">{{ number_format($stats['employees_inactive']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-rose-200/60 bg-gradient-to-br from-white to-rose-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">إجازات معلّقة</p>
            <p class="text-3xl font-black text-rose-800 tabular-nums">{{ number_format($stats['leaves_pending']) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200/60 bg-gradient-to-br from-white to-emerald-50/80 p-5 shadow-sm">
            <p class="text-sm font-semibold text-gray-600 mb-1">موافق عليها (الشهر)</p>
            <p class="text-3xl font-black text-emerald-800 tabular-nums">{{ number_format($stats['leaves_month_approved']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-900">إجازات تحتاج مراجعة</h2>
                <a href="{{ route('employee.hr.leaves.index', ['status' => 'pending']) }}" class="text-xs font-bold text-rose-700 hover:underline">الكل</a>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-semibold sticky top-0">
                        <tr>
                            <th class="text-right px-4 py-2">الموظف</th>
                            <th class="text-right px-4 py-2">النوع</th>
                            <th class="text-right px-4 py-2">الفترة</th>
                            <th class="text-right px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pendingLeaves as $leave)
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900">{{ $leave->employee?->name ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $leave->employee?->employee_code ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2">{{ $leave->type_label }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-gray-700">{{ $leave->start_date?->format('Y-m-d') }} — {{ $leave->end_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('employee.hr.leaves.show', $leave) }}" class="text-rose-700 font-bold hover:underline text-xs">مراجعة</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">لا توجد طلبات معلّقة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">إجازات معتمدة قادمة</h2>
                <p class="text-xs text-gray-500 mt-1">حتى نهاية الفترة المعتمدة ≥ اليوم</p>
            </div>
            <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto text-sm">
                @forelse($upcomingApproved as $lv)
                <li class="px-5 py-3 flex justify-between gap-2">
                    <div>
                        <span class="font-medium text-gray-900">{{ $lv->employee?->name ?? '—' }}</span>
                        <div class="text-xs text-gray-500">{{ $lv->start_date?->format('Y-m-d') }} — {{ $lv->end_date?->format('Y-m-d') }} · {{ $lv->days }} يوم</div>
                    </div>
                </li>
                @empty
                <li class="px-5 py-10 text-center text-gray-500">لا توجد إجازات معتمدة في الأفق.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">تعيينات حديثة (آخر 6 أشهر)</h2>
            <a href="{{ route('employee.hr.employees.index') }}" class="text-xs font-bold text-indigo-700 hover:underline">الدليل</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse($recentHires as $h)
            <li class="px-5 py-3 flex flex-wrap justify-between gap-2">
                <div>
                    <span class="font-semibold text-gray-900">{{ $h->name }}</span>
                    <span class="text-gray-500 text-sm mr-2">{{ $h->employeeJob?->name ?? '' }}</span>
                </div>
                <div class="text-xs text-gray-600 tabular-nums">{{ $h->hire_date?->format('Y-m-d') }} @if($h->employee_code)· {{ $h->employee_code }}@endif</div>
            </li>
            @empty
            <li class="px-5 py-8 text-center text-gray-500 text-sm">لا توجد تعيينات مسجّلة في الفترة.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@extends('layouts.employee')

@section('title', __('طلب إجازة'))
@section('header', __('تفاصيل طلب الإجازة'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">معلومات الطلب</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($leave->status === 'pending') bg-amber-100 text-amber-800
                @elseif($leave->status === 'approved') bg-emerald-100 text-emerald-800
                @elseif($leave->status === 'rejected') bg-rose-100 text-rose-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ $leave->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-gray-500 font-medium mb-1">الموظف</p>
                <p class="font-bold text-gray-900">{{ $leave->employee->name }}</p>
                @if($leave->employee->employee_code)
                    <p class="text-gray-500 mt-1">الرمز: {{ $leave->employee->employee_code }}</p>
                @endif
                @if($leave->employee->employeeJob)
                    <p class="text-gray-500">الوظيفة: {{ $leave->employee->employeeJob->name }}</p>
                @endif
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">نوع الإجازة</p>
                <p class="font-bold text-gray-900">{{ $leave->type_label }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">من</p>
                <p class="font-semibold text-gray-900">{{ $leave->start_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">إلى</p>
                <p class="font-semibold text-gray-900">{{ $leave->end_date->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">عدد الأيام</p>
                <p class="font-semibold text-gray-900">{{ $leave->days }} يوم</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">تاريخ التقديم</p>
                <p class="font-semibold text-gray-900">{{ $leave->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-500 font-medium mb-1">السبب</p>
                <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap text-gray-900">{{ $leave->reason }}</div>
            </div>
        </div>
    </div>

    @if($leave->status !== 'pending' && $leave->reviewer)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">المراجعة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">بواسطة</p>
                    <p class="font-semibold">{{ $leave->reviewer->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">التاريخ</p>
                    <p class="font-semibold">{{ $leave->reviewed_at?->format('Y-m-d H:i') }}</p>
                </div>
                @if($leave->admin_notes)
                    <div class="md:col-span-2">
                        <p class="text-gray-500 mb-1">ملاحظات</p>
                        <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap">{{ $leave->admin_notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($leave->status === 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">قرار الموارد البشرية</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <form action="{{ route('employee.hr.leaves.approve', $leave) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">ملاحظات (اختياري)</label>
                        <textarea name="admin_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-lg font-bold text-sm">
                            <i class="fas fa-check ml-2"></i>موافقة
                        </button>
                    </div>
                </form>
                <form action="{{ route('employee.hr.leaves.reject', $leave) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">سبب الرفض <span class="text-rose-600">*</span></label>
                        <textarea name="admin_notes" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"></textarea>
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white px-4 py-3 rounded-lg font-bold text-sm">
                            <i class="fas fa-times ml-2"></i>رفض
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('employee.hr.leaves.index') }}" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
            <i class="fas fa-arrow-right ml-2"></i>القائمة
        </a>
    </div>
</div>
@endsection

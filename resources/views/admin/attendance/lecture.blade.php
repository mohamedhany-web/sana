@extends('layouts.admin')

@section('title', 'تفاصيل حضور المحاضرة - ' . ($lecture->title ?? ''))
@section('header', 'تفاصيل حضور المحاضرة')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6 bg-slate-50 min-h-screen">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4">
            {{ session('error') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">{{ $lecture->title }}</h2>
                <p class="text-sm text-slate-600 mt-1">
                    {{ $lecture->course->title ?? 'بدون كورس' }}
                </p>
            </div>
            <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                <i class="fas fa-arrow-right"></i>
                الرجوع لقائمة الحضور
            </a>
        </div>

        <div class="p-5">
            <form action="{{ route('admin.attendance.upload-teams', $lecture) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                @csrf
                <input type="file" name="file" accept=".csv,.xlsx,.xls" class="block w-full sm:w-auto text-sm text-slate-700 border border-slate-300 rounded-lg bg-white file:mr-2 file:py-2 file:px-3 file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm">
                    <i class="fas fa-upload"></i>
                    رفع ملف Teams
                </button>
            </form>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900">سجلات الحضور للمحاضرة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase">الدقائق</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase">النسبة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700 uppercase">وقت التسجيل</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($attendanceRecords as $record)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $record->student->name ?? 'غير محدد' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $record->status }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ (int) ($record->attendance_minutes ?? 0) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ number_format((float) ($record->attendance_percentage ?? 0), 1) }}%</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ optional($record->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-600">لا توجد سجلات حضور لهذه المحاضرة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

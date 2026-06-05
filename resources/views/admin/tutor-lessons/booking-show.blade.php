@extends('layouts.admin')
@section('title', 'حجز '.$booking->code)
@section('header', 'تفاصيل الحجز')
@section('content')
<div class="space-y-6 max-w-3xl">
    @include('admin.tutor-lessons._nav')
    <div class="bg-white rounded-2xl border p-6 shadow-sm space-y-3 text-sm">
        <p><strong>الرمز:</strong> {{ $booking->code }}</p>
        <p><strong>الطالب:</strong> {{ $booking->student?->name }}</p>
        <p><strong>المعلم:</strong> {{ $booking->instructor?->name }}</p>
        <p><strong>الموعد:</strong> {{ $booking->scheduled_at?->format('Y-m-d H:i') }} ({{ $booking->duration_minutes }} د)</p>
        <p><strong>الحالة:</strong> {{ $booking->status }}</p>
        <p><strong>نمط الحجز:</strong> {{ $booking->matching_mode }}</p>
        <p><strong>دقائق محسوبة:</strong> {{ (int) $booking->billable_minutes }}</p>
        @if($booking->student_notes)<p><strong>ملاحظات الطالب:</strong> {{ $booking->student_notes }}</p>@endif
        @if($booking->classroomMeeting)
            <p><strong>غرفة Classroom:</strong> {{ $booking->classroomMeeting->code }}</p>
        @endif
    </div>
    @if($booking->student)
    <form method="post" action="{{ route('admin.tutor-lessons.students.quota', $booking->student) }}" class="bg-white rounded-2xl border p-6 shadow-sm space-y-3">
        @csrf
        <h3 class="font-bold">تعديل باقة ساعات الطالب (يدوي)</h3>
        @php $sp = \App\Models\StudentLearningProfile::firstOrCreate(['user_id' => $booking->student_id]); @endphp
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold">ساعات الباقة (-1 = غير محدود)</label>
                <input type="number" name="lesson_hours_quota" value="{{ $sp->lesson_hours_quota }}" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-bold">ساعات مستهلكة</label>
                <input type="number" name="lesson_hours_used" value="{{ $sp->lesson_hours_used }}" class="w-full border rounded-lg px-3 py-2">
            </div>
        </div>
        <button class="px-4 py-2 bg-violet-600 text-white rounded-lg font-bold text-sm">حفظ الباقة</button>
    </form>
    @endif
</div>
@endsection

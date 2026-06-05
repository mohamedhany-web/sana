@extends('layouts.app')
@section('title', 'حجز #'.$booking->code)
@section('header', 'تفاصيل الحصة')
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="id-tutor-page space-y-6 pb-6 w-full max-w-3xl">
    @if(session('success'))<div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="ins-flash bg-rose-50 border border-rose-200 text-rose-800">{{ session('error') }}</div>@endif
    <a href="{{ route('instructor.tutor-lessons.bookings.index') }}" class="id-link inline-flex items-center gap-1"><i class="fas fa-arrow-right text-xs"></i> العودة للحجوزات</a>
    <div class="id-panel">
        <div class="id-panel-head">
            <h3 class="font-bold m-0">حجز #{{ $booking->code }}</h3>
            <span class="id-badge id-badge-{{ $booking->status === 'confirmed' ? 'confirmed' : 'pending' }}">{{ $booking->statusLabel() }}</span>
        </div>
        <div class="id-panel-body space-y-3">
            <div class="flex justify-between text-sm"><span class="text-slate-500">الطالب</span><strong>{{ $booking->student?->name }}</strong></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">الموعد</span><strong>{{ $booking->scheduled_at?->format('Y-m-d H:i') }}</strong></div>
            <div class="flex justify-between text-sm"><span class="text-slate-500">دقائق مشتركة</span><strong>{{ $booking->billable_minutes }}</strong></div>
            @if($booking->student_notes)<p class="text-sm bg-slate-50 p-3 rounded-xl m-0">{{ $booking->student_notes }}</p>@endif
            <div class="flex flex-wrap gap-2 pt-3">
                @if(in_array($booking->status, ['confirmed', 'in_progress'], true))
                    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.send-reminder', $booking) }}" data-turbo="false">
                        @csrf
                        <button type="submit" class="id-btn-ghost inline-flex items-center gap-2"
                                onclick="return confirm('إرسال تذكير للطالب بموعد الحصة؟')">
                            <i class="fas fa-bell"></i>
                            {{ __('tutor.send_reminder') }}
                        </button>
                    </form>
                @endif
                @if($booking->status === 'pending')
                    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.confirm', $booking) }}" data-turbo="false">@csrf<button type="submit" class="id-btn-primary">تأكيد وإنشاء الغرفة</button></form>
                    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.cancel', $booking) }}" data-turbo="false">@csrf<button type="submit" class="id-btn-ghost">إلغاء</button></form>
                @endif
                @if(in_array($booking->status, ['confirmed','in_progress']) && $booking->classroomMeeting)
                    <a href="{{ route('instructor.classroom.room', $booking->classroomMeeting) }}" class="id-btn-primary" data-turbo="false">{{ __('tutor.enter_lesson') }}</a>
                    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.complete', $booking) }}" data-turbo="false">@csrf<button type="submit" class="id-btn-ghost">إنهاء وتسجيل الدقائق</button></form>
                @endif
                @if($booking->status === 'completed')
                    <a href="{{ route('instructor.tutor-lessons.bookings.rate', $booking) }}" class="id-btn-ghost">{{ __('tutor.rate_lesson') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

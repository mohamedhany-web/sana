@extends('layouts.app')
@section('title', __('tutor.rate_lesson'))
@section('header', __('tutor.rate_lesson'))
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="id-tutor-page pb-6 w-full max-w-md">
    @if(session('success'))
        <div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="ins-flash bg-amber-50 border border-amber-200 text-amber-900 mb-4">{{ session('info') }}</div>
    @endif
    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.rate.store',$booking) }}" class="id-panel id-form" data-turbo="false">
        <div class="id-panel-head"><h3 class="font-bold m-0">تقييم الطالب: {{ $booking->student?->name }}</h3></div>
        <div class="id-panel-body space-y-4">@csrf
            <p class="text-sm text-slate-600 m-0">بعد الإرسال سيصل التقييم لولي الأمر كإشعار داخل المنصة.</p>
            <div><label>التقييم (1-5) *</label><input type="number" name="rating" min="1" max="5" required value="{{ old('rating') }}"></div>
            <div><label>ملاحظات عن أداء الطالب</label><textarea name="comment" rows="4" placeholder="نقاط القوة والتحسين...">{{ old('comment') }}</textarea></div>
            <button type="submit" class="id-btn-primary w-full justify-center">حفظ التقييم وإشعار ولي الأمر</button>
        </div>
    </form>
</div>
@endsection

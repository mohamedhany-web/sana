@extends('layouts.app')
@section('title', __('tutor.rate_lesson'))
@section('header', __('tutor.rate_lesson'))
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page w-full pb-8 max-w-md">
    <form method="post" action="{{ route('student.tutor-lessons.bookings.rate.store',$booking) }}" class="sd-panel sd-form">
        <div class="sd-panel-head"><h2 class="font-bold">{{ __('tutor.rate_lesson') }}</h2></div>
        <div class="sd-panel-body space-y-4">@csrf
            <div><label>التقييم (1-5)</label><input type="number" name="rating" min="1" max="5" required></div>
            <div><label>تعليق</label><textarea name="comment" rows="3"></textarea></div>
            <button type="submit" class="sd-btn-primary w-full">إرسال</button>
        </div>
    </form>
</div>
@endsection

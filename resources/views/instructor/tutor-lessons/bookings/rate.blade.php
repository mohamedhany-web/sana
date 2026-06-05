@extends('layouts.app')
@section('title', __('tutor.rate_lesson'))
@section('header', __('tutor.rate_lesson'))
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="id-tutor-page pb-6 w-full max-w-md">
    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.rate.store',$booking) }}" class="id-panel id-form">
        <div class="id-panel-head"><h3 class="font-bold m-0">{{ __('tutor.rate_lesson') }}</h3></div>
        <div class="id-panel-body space-y-4">@csrf
            <div><label>التقييم (1-5)</label><input type="number" name="rating" min="1" max="5" required></div>
            <div><label>تعليق</label><textarea name="comment" rows="3"></textarea></div>
            <button type="submit" class="id-btn-primary w-full justify-center">إرسال</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')
@section('header', __('tutor.parent_hub_title'))
@section('title', __('tutor.parent_hub_title'))
@section('content')
@include('partials.tutor-lesson-ui')
<div class="tl-page max-w-4xl mx-auto py-4 space-y-4">
    <div class="tl-hero"><h1>{{ __('tutor.parent_hub_title') }}</h1><p>متابعة حصص الأبناء وطلب مساعدة في إيجاد معلم.</p>
        <a href="{{ route('parent.tutor-lessons.assisted') }}" class="tl-btn tl-btn-primary mt-3 inline-flex">طلب مساعدة</a>
    </div>
    @foreach($bookings as $b)
        <a href="{{ route('parent.tutor-lessons.bookings.show',$b) }}" class="tl-card block no-underline text-inherit">{{ $b->student?->name }} — {{ $b->instructor?->name }} — {{ $b->scheduled_at?->format('Y-m-d H:i') }}</a>
    @endforeach
</div>
@endsection

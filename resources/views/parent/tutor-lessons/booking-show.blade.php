@extends('layouts.app')
@section('title', 'حصة الابن')
@include('partials.tutor-lesson-ui')
@section('content')
<div class="tl-page max-w-xl mx-auto py-2"><div class="tl-card space-y-2">
<p><strong>{{ $booking->student?->name }}</strong> مع {{ $booking->instructor?->name }}</p>
<p>{{ $booking->scheduled_at?->format('Y-m-d H:i') }} — {{ $booking->statusLabel() }}</p>
@if($booking->classroomMeeting)<a href="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}" class="tl-btn tl-btn-primary">{{ __('tutor.enter_lesson') }}</a>@endif
</div></div>
@endsection

@extends('layouts.app')
@section('title', 'طلب '.$assisted->code)
@include('partials.tutor-lesson-ui')
@section('content')
<div class="tl-page max-w-xl mx-auto py-2"><div class="tl-card"><p class="font-bold">{{ $assisted->student?->name }} — {{ $assisted->code }}</p><p>{{ $assisted->statusLabel() }}</p><p class="mt-3 text-sm">{{ $assisted->message }}</p></div></div>
@endsection

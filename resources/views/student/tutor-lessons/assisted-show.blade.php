@extends('layouts.app')
@section('title', 'طلب '.$assisted->code)
@section('header', 'طلب المساعدة')
@include('student.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="sd-page w-full pb-8 max-w-2xl">
    <div class="sd-panel">
        <div class="sd-panel-head"><h2 class="font-bold">{{ $assisted->code }}</h2><span class="sd-badge sd-badge-pending">{{ $assisted->statusLabel() }}</span></div>
        <div class="sd-panel-body">
            <p class="text-sm text-slate-600 whitespace-pre-wrap">{{ $assisted->message }}</p>
            @if($assisted->assignedInstructor)
                <p class="mt-4 text-emerald-700 font-semibold"><i class="fas fa-user-check ms-1"></i> المعلم: {{ $assisted->assignedInstructor->name }}</p>
            @endif
            <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline mt-6 inline-flex">العودة</a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', __('tutor.rate_lesson'))
@section('header', __('tutor.rate_lesson'))
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
@php
    $existing = $existing ?? null;
    $studentRating = old('rating', $existing?->rating);
    $lessonRating = old('lesson_rating', $existing?->lesson_rating);
@endphp
<div class="id-tutor-page pb-6 w-full max-w-lg">
    @if(session('success'))
        <div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="ins-flash bg-amber-50 border border-amber-200 text-amber-900 mb-4">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="ins-flash bg-rose-50 border border-rose-200 text-rose-800 mb-4">
            <ul class="m-0 pr-4 list-disc text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 mb-4 text-sm text-amber-950">
        <p class="font-black m-0">{{ __('tutor.evaluation_required_banner') }}</p>
        <p class="text-xs mt-1 mb-0 text-amber-800">{{ __('tutor.evaluation_required_help') }}</p>
    </div>

    <form method="post" action="{{ route('instructor.tutor-lessons.bookings.rate.store', $booking) }}" class="id-panel id-form" data-turbo="false">
        <div class="id-panel-head">
            <h3 class="font-bold m-0">{{ __('tutor.rate_lesson') }} — {{ $booking->student?->name }}</h3>
        </div>
        <div class="id-panel-body space-y-5">
            @csrf
            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm space-y-1">
                <div class="flex justify-between gap-2"><span class="text-slate-500">{{ __('tutor.student_label') }}</span><strong>{{ $booking->student?->name }}</strong></div>
                <div class="flex justify-between gap-2"><span class="text-slate-500">{{ __('tutor.when_label') }}</span><strong>{{ $booking->scheduled_at?->format('Y-m-d H:i') }}</strong></div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">{{ __('tutor.student_rating_label') }} <span class="text-rose-600">*</span></label>
                <p class="text-xs text-slate-500 mb-2">{{ __('tutor.student_rating_help') }}</p>
                <div class="flex flex-wrap gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="inline-flex items-center justify-center w-11 h-11 rounded-xl border cursor-pointer text-sm font-black transition border-slate-200 bg-white text-slate-700 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-800">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" @checked((int) $studentRating === $i) required>
                            {{ $i }}★
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">{{ __('tutor.lesson_rating_label') }} <span class="text-rose-600">*</span></label>
                <p class="text-xs text-slate-500 mb-2">{{ __('tutor.lesson_rating_help') }}</p>
                <div class="flex flex-wrap gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="inline-flex items-center justify-center w-11 h-11 rounded-xl border cursor-pointer text-sm font-black transition border-slate-200 bg-white text-slate-700 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50 has-[:checked]:text-sky-800">
                            <input type="radio" name="lesson_rating" value="{{ $i }}" class="sr-only" @checked((int) $lessonRating === $i) required>
                            {{ $i }}★
                        </label>
                    @endfor
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">{{ __('tutor.evaluation_comment_label') }} <span class="text-rose-600">*</span></label>
                <p class="text-xs text-slate-500 mb-2">{{ __('tutor.evaluation_comment_help') }}</p>
                <textarea name="comment" rows="5" required minlength="10" maxlength="2000"
                          class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                          placeholder="{{ __('tutor.evaluation_comment_placeholder') }}">{{ old('comment', $existing?->comment) }}</textarea>
            </div>

            <button type="submit" class="id-btn-primary w-full justify-center">
                <i class="fas fa-paper-plane text-xs"></i>
                {{ $existing ? __('tutor.update_evaluation') : __('tutor.submit_evaluation') }}
            </button>
            <p class="text-[11px] text-slate-500 text-center m-0">{{ __('tutor.evaluation_goes_to_parent_note') }}</p>
        </div>
    </form>
</div>
@endsection

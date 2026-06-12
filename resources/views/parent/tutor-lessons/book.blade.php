@extends('layouts.app')

@section('title', 'حجز حصة لابنك')
@section('header', 'حجز مع '.$instructor->name)

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $duration = (int) ($profile->tutor_default_duration_minutes ?? 60);
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $supportedSessions = $profile->tutor_session_types ?? ['one_to_one'];
    if (($groupOffers ?? collect())->isEmpty()) {
        $supportedSessions = array_values(array_filter($supportedSessions, fn ($s) => $s !== 'small_group'));
    }
    $defaultSession = old('session_type', $studentProfile->preferred_session_type ?? 'one_to_one');
    $availByDay = $availabilities->groupBy('day_of_week')->sortKeys();
@endphp

<div class="sd-page space-y-6 pb-8 w-full max-w-3xl mx-auto">
    <div class="sd-panel">
        <div class="sd-panel-head">
            <h1 class="font-heading font-bold text-slate-800 m-0">حجز حصة لـ {{ $student->name }}</h1>
            <p class="text-sm text-slate-600 mt-1 m-0">مع المعلم {{ $instructor->name }}</p>
        </div>
        <div class="sd-panel-body">
            <form method="post" action="{{ route('parent.tutor-lessons.book.store', $instructor) }}" class="sd-form space-y-5">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                @if($errors->any())
                    <div class="sd-alert sd-alert-error space-y-1">
                        @foreach($errors->all() as $e)<p class="m-0">{{ $e }}</p>@endforeach
                    </div>
                @endif

                <div>
                    <label for="scheduled_at">الموعد *</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" required
                           value="{{ old('scheduled_at') }}"
                           min="{{ now()->addHour()->format('Y-m-d\TH:i') }}">
                </div>

                @if(($subjects ?? collect())->isNotEmpty())
                <div>
                    <label for="academic_subject_id">المادة</label>
                    <select id="academic_subject_id" name="academic_subject_id">
                        <option value="">— اختر —</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" @selected(old('academic_subject_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(count($supportedSessions) > 1)
                    <div>
                        <label class="mb-2">نوع الحصة</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($supportedSessions as $stype)
                                @if(isset($sessionLabels[$stype]))
                                    <label class="sd-chip">
                                        <input type="radio" name="session_type" value="{{ $stype }}" @checked($defaultSession === $stype)>
                                        {{ $sessionLabels[$stype] }}
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <input type="hidden" name="session_type" value="{{ $supportedSessions[0] ?? 'one_to_one' }}">
                @endif

                @if(($groupOffers ?? collect())->isNotEmpty())
                    <div id="group-offers-block" class="{{ $defaultSession === 'small_group' ? '' : 'hidden' }}">
                        <label class="mb-2">عرض المجموعة *</label>
                        <div class="space-y-2">
                            @foreach($groupOffers as $offer)
                                <label class="sd-chip block !items-start w-full p-3">
                                    <input type="radio" name="tutor_group_offer_id" value="{{ $offer->id }}"
                                           @checked((int) old('tutor_group_offer_id') === (int) $offer->id)>
                                    <span class="flex-1">
                                        <strong class="block">{{ $offer->title }}</strong>
                                        <span class="text-xs text-slate-500">
                                            {{ $offer->min_group_size }}–{{ $offer->max_group_size }} طلاب · {{ $offer->duration_minutes }} دقيقة
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="student_notes">ملاحظات</label>
                    <textarea id="student_notes" name="student_notes" rows="3">{{ old('student_notes') }}</textarea>
                </div>

                <button type="submit" class="sd-btn-primary">إرسال طلب الحصة</button>
            </form>
        </div>
    </div>
</div>

@if(($groupOffers ?? collect())->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var block = document.getElementById('group-offers-block');
    if (!block) return;
    document.querySelectorAll('input[name="session_type"]').forEach(function (r) {
        r.addEventListener('change', function () {
            var sel = document.querySelector('input[name="session_type"]:checked');
            block.classList.toggle('hidden', !sel || sel.value !== 'small_group');
        });
    });
});
</script>
@endpush
@endif
@endsection

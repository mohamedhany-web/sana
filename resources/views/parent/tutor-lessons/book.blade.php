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
                    <label class="mb-2">الموعد المتاح *</label>
                    <input type="hidden" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required>
                    @php
                        $slots = collect($availableSlots ?? []);
                        $slotsPayload = $slots->map(function ($s) {
                            $at = \Carbon\Carbon::parse($s['scheduled_at']);

                            return [
                                'value' => $at->format('Y-m-d\TH:i'),
                                'date' => $at->toDateString(),
                                'date_label' => $at->locale('ar')->translatedFormat('l j F Y'),
                                'time' => $at->format('H:i'),
                                'label' => $s['label'] ?? $at->locale('ar')->translatedFormat('l j F — H:i'),
                            ];
                        })->values();
                        $oldSlot = old('scheduled_at');
                        $oldSlotNorm = $oldSlot ? \Carbon\Carbon::parse($oldSlot)->format('Y-m-d\TH:i') : '';
                        $oldDate = $oldSlotNorm ? \Carbon\Carbon::parse($oldSlotNorm)->toDateString() : '';
                    @endphp
                    @if($slots->isEmpty())
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            لا توجد مواعيد متاحة خلال الأسبوعين القادمين لهذا المعلم.
                        </div>
                    @else
                        <p class="text-[11px] text-slate-500 mb-3 m-0">اختر اليوم ثم الساعة ({{ $duration }} دقيقة).</p>
                        <div class="grid sm:grid-cols-2 gap-3" id="slot-picker"
                             data-slots='@json($slotsPayload)'
                             data-old-date="{{ $oldDate }}"
                             data-old-slot="{{ $oldSlotNorm }}">
                            <div>
                                <label for="slot_day" class="!mb-1.5">اليوم</label>
                                <select id="slot_day" class="w-full">
                                    <option value="">— اختر اليوم —</option>
                                </select>
                            </div>
                            <div>
                                <label for="slot_time" class="!mb-1.5">الساعة</label>
                                <select id="slot_time" class="w-full" disabled>
                                    <option value="">— اختر الساعة —</option>
                                </select>
                            </div>
                        </div>
                        <p id="slot-selected-label" class="text-xs font-bold text-violet-700 mt-3 m-0 {{ $oldSlotNorm ? '' : 'hidden' }}"></p>
                    @endif
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

                <button type="submit" class="sd-btn-primary" @if(empty($availableSlots)) disabled @endif>إرسال طلب الحصة</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('scheduled_at');
    var label = document.getElementById('slot-selected-label');
    var picker = document.getElementById('slot-picker');
    var daySelect = document.getElementById('slot_day');
    var timeSelect = document.getElementById('slot_time');
    var form = picker ? picker.closest('form') : null;

    function parseSlots() {
        if (!picker) return [];
        try { return JSON.parse(picker.getAttribute('data-slots') || '[]') || []; }
        catch (e) { return []; }
    }

    function fillDays(slots, preferredDate) {
        if (!daySelect) return;
        var seen = {};
        daySelect.innerHTML = '<option value="">— اختر اليوم —</option>';
        slots.forEach(function (s) {
            if (seen[s.date]) return;
            seen[s.date] = true;
            var opt = document.createElement('option');
            opt.value = s.date;
            opt.textContent = s.date_label;
            daySelect.appendChild(opt);
        });
        if (preferredDate && seen[preferredDate]) daySelect.value = preferredDate;
    }

    function fillTimes(slots, date, preferredSlot) {
        if (!timeSelect) return;
        timeSelect.innerHTML = '<option value="">— اختر الساعة —</option>';
        timeSelect.disabled = !date;
        if (!date) { if (input) input.value = ''; return; }
        slots.filter(function (s) { return s.date === date; }).forEach(function (s) {
            var opt = document.createElement('option');
            opt.value = s.value;
            opt.textContent = s.time;
            opt.setAttribute('data-label', s.label || s.time);
            timeSelect.appendChild(opt);
        });
        if (preferredSlot) timeSelect.value = preferredSlot;
        syncHidden();
    }

    function syncHidden() {
        if (!input || !timeSelect) return;
        var val = timeSelect.value || '';
        input.value = val;
        if (!label) return;
        if (!val) { label.classList.add('hidden'); label.textContent = ''; return; }
        var opt = timeSelect.options[timeSelect.selectedIndex];
        label.classList.remove('hidden');
        label.textContent = 'الموعد المختار: ' + (opt ? (opt.getAttribute('data-label') || opt.textContent) : val);
    }

    if (picker && daySelect && timeSelect && input) {
        var slots = parseSlots();
        fillDays(slots, picker.getAttribute('data-old-date') || '');
        fillTimes(slots, daySelect.value, picker.getAttribute('data-old-slot') || '');
        daySelect.addEventListener('change', function () { fillTimes(slots, daySelect.value, ''); });
        timeSelect.addEventListener('change', syncHidden);
    }

    if (form && input) {
        form.addEventListener('submit', function (e) {
            if (!input.value) {
                e.preventDefault();
                alert('اختر اليوم ثم الساعة أولاً.');
            }
        });
    }

    var block = document.getElementById('group-offers-block');
    if (block) {
        document.querySelectorAll('input[name="session_type"]').forEach(function (r) {
            r.addEventListener('change', function () {
                var sel = document.querySelector('input[name="session_type"]:checked');
                block.classList.toggle('hidden', !sel || sel.value !== 'small_group');
            });
        });
    }
});
</script>
@endpush
@endsection

@extends('layouts.app')
@section('title', 'إعداد معلم الحصص')
@section('header', 'إعداد معلم الحصص')
@include('instructor.tutor-lessons.partials.dashboard-styles')
@section('content')
<div class="id-tutor-page space-y-6 pb-6 w-full">
    @if(session('success'))
        <div class="ins-flash bg-emerald-50 border border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="ins-flash bg-sky-50 border border-sky-200 text-sky-800">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="ins-flash bg-rose-50 border border-rose-200 text-rose-800">
            <p class="font-bold mb-2">تعذّر الحفظ — راجع الحقول التالية:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($profile->isTutorActivated())
        <div class="ins-flash bg-emerald-50 border border-emerald-300 text-emerald-900">
            <i class="fas fa-check-circle me-1"></i>
            حسابك <strong>مفعّل</strong> — يظهر للطلاب في «اختيار معلم» عند تطابق المادة ونمط الحجز.
        </div>
    @elseif(!empty($activationBlockers))
        <div class="ins-flash bg-amber-50 border border-amber-200 text-amber-900">
            <p class="font-bold mb-1">لإظهار حسابك للطلاب:</p>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach($activationBlockers as $hint)
                    <li>{{ $hint }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="id-hero">
        <div class="id-hero-main relative z-[1]">
            <p class="text-xs font-bold id-tag mb-1">إعداد الحصص</p>
            <h2 class="text-xl font-black text-slate-900 m-0">ملف المعلم والجدول</h2>
            <p class="text-sm text-slate-600 mt-2 mb-0">احفظ ملفك، أضف أوقاتك الأسبوعية، وفعّل «اختيار معلم» — بعدها يظهر اسمك للطلاب تلقائياً.</p>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <form method="post" action="{{ route('instructor.tutor-lessons.setup.profile') }}" class="id-panel id-form">
            <div class="id-panel-head"><h3 class="font-bold m-0">الملف التعريفي</h3></div>
            <div class="id-panel-body space-y-4">
                @csrf
                <div>
                    <label>العنوان المختصر</label>
                    <input name="headline" value="{{ old('headline', $profile->headline) }}" required>
                    @error('headline')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label>نبذة</label>
                    <textarea name="bio" rows="4" required>{{ old('bio', $profile->bio) }}</textarea>
                    @error('bio')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label>سنوات الخبرة</label>
                    <input type="number" name="years_experience" min="0" value="{{ old('years_experience', $profile->tutor_years_experience ?? 0) }}" required>
                </div>
                <div>
                    <label>المواد *</label>
                    @if($subjects->isEmpty())
                        <p class="text-sm text-rose-600">لا توجد مواد نشطة — أضفها من لوحة الإدارة أولاً.</p>
                    @else
                        <div class="flex flex-wrap gap-2" id="tutor-subject-chips">
                            @foreach($subjects as $s)
                                <label class="id-chip" data-subject-year="{{ $s->academic_year_id }}">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, old('subject_ids', $profile->tutor_subject_ids ?? [])))>
                                    {{ $s->name }}@if($s->academicYear)<span class="text-[10px] opacity-70"> · {{ $s->academicYear->name }}</span>@endif
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('subject_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label>المراحل / المسارات *</label>
                    @if($years->isEmpty())
                        <p class="text-sm text-rose-600">لا توجد مراحل نشطة — أضفها من لوحة الإدارة.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($years as $y)
                                <label class="id-chip">
                                    <input type="checkbox" name="academic_year_ids[]" value="{{ $y->id }}" @checked(in_array($y->id, old('academic_year_ids', $profile->tutor_academic_year_ids ?? [])))>
                                    {{ $y->name }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('academic_year_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label>أنماط التوافق * <span class="text-xs font-normal text-slate-500">(يجب تفعيل «اختيار معلم» على الأقل)</span></label>
                    @php $oldModes = old('matching_modes', $profile->tutor_matching_modes ?? ['pick_teacher']); @endphp
                    @foreach(['assisted'=>'مساعدة','self_schedule'=>'حجز ذاتي','pick_teacher'=>'اختيار معلم'] as $k=>$lbl)
                        <label class="id-chip me-1">
                            <input type="checkbox" name="matching_modes[]" value="{{ $k }}" @checked(in_array($k, $oldModes, true))> {{ $lbl }}
                        </label>
                    @endforeach
                    @error('matching_modes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label>أنواع الحصص *</label>
                    <label class="id-chip"><input type="checkbox" name="session_types[]" value="one_to_one" @checked(in_array('one_to_one', old('session_types', $profile->tutor_session_types ?? ['one_to_one']), true))> فردي</label>
                    <label class="id-chip"><input type="checkbox" name="session_types[]" value="small_group" @checked(in_array('small_group', old('session_types', $profile->tutor_session_types ?? []), true))> مجموعة</label>
                </div>
                <div>
                    <label>مدة الحصة الافتراضية (دقيقة)</label>
                    <input type="number" name="default_duration" value="{{ old('default_duration', $profile->tutor_default_duration_minutes ?? 60) }}" min="30" max="180">
                </div>
                <button type="submit" class="id-btn-primary w-full justify-center">حفظ الملف</button>
            </div>
        </form>

        <div class="space-y-6">
            <div class="id-panel id-form">
                <div class="id-panel-head"><h3 class="font-bold m-0">الجدول الأسبوعي</h3></div>
                <div class="id-panel-body">
                    <form method="post" action="{{ route('instructor.tutor-lessons.availability.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end mb-4">
                        @csrf
                        <div>
                            <label>اليوم</label>
                            <select name="day_of_week">
                                @foreach(\App\Models\TutorAvailability::dayLabels() as $d=>$l)
                                    <option value="{{ $d }}" @selected(old('day_of_week') == $d)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>من</label>
                            <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                            @error('start_time')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label>إلى</label>
                            <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" required>
                            @error('end_time')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="id-btn-ghost w-full justify-center">إضافة</button>
                    </form>
                    @forelse($availabilities as $a)
                        <div class="flex justify-between items-center text-sm py-2 border-b border-slate-100">
                            <span class="font-semibold">{{ $a->dayLabel() }} {{ substr($a->start_time,0,5) }}-{{ substr($a->end_time,0,5) }}</span>
                            <form method="post" action="{{ route('instructor.tutor-lessons.availability.destroy', $a) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 text-xs font-bold">حذف</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">لم تُضف فترات بعد — أضف فترة واحدة على الأقل.</p>
                    @endforelse
                </div>
            </div>

            @if($needsTrial && ! $profile->isTutorActivated())
            <div class="id-panel border-2 border-amber-200">
                <div class="id-panel-head bg-amber-50/80"><h3 class="font-bold text-amber-900 m-0">الجلسة التجريبية (اختياري)</h3></div>
                <div class="id-panel-body">
                    <p class="text-sm text-amber-900 mb-3">إن كان التفعيل التلقائي معطّلاً، يمكن إتمام جلسة تجريبية ثم إنهاؤها من «حجوزاتي».</p>
                    @if($trialBooking)
                        <p class="text-sm mb-3">طلب تجريبي: {{ $trialBooking->scheduled_at?->format('Y-m-d H:i') }} — {{ $trialBooking->statusLabel() }}</p>
                    @endif
                    <form method="post" action="{{ route('instructor.tutor-lessons.setup.trial') }}" class="flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="datetime-local" name="scheduled_at" class="flex-1" required>
                        <button type="submit" class="id-btn-primary">حجز تجريبي</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var chips = document.querySelectorAll('#tutor-subject-chips [data-subject-year]');
    var yearInputs = document.querySelectorAll('input[name="academic_year_ids[]"]');
    function syncSubjects() {
        var selected = Array.from(yearInputs).filter(function (i) { return i.checked; }).map(function (i) { return i.value; });
        chips.forEach(function (chip) {
            var yearId = chip.getAttribute('data-subject-year');
            var show = selected.length === 0 || selected.indexOf(yearId) !== -1;
            chip.style.display = show ? '' : 'none';
            if (!show) {
                var input = chip.querySelector('input[type="checkbox"]');
                if (input) input.checked = false;
            }
        });
    }
    yearInputs.forEach(function (input) { input.addEventListener('change', syncSubjects); });
    if (chips.length) syncSubjects();
})();
</script>
@endpush

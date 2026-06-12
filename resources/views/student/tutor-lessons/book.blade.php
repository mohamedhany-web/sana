@extends('layouts.app')

@section('title', 'حجز حصة')
@section('header', 'حجز مع '.$instructor->name)

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $remainingHours = max(0, (int) $studentProfile->lesson_hours_quota - (int) $studentProfile->lesson_hours_used);
    $duration = (int) ($profile->tutor_default_duration_minutes ?? 60);
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $supportedSessions = $profile->tutor_session_types ?? ['one_to_one'];
    if (($groupOffers ?? collect())->isEmpty()) {
        $supportedSessions = array_values(array_filter($supportedSessions, fn ($s) => $s !== 'small_group'));
    }
    $availByDay = $availabilities->groupBy('day_of_week')->sortKeys();
    $instructorSubjects = \App\Models\AcademicSubject::whereIn('id', $profile->tutor_subject_ids ?? [])->get();
    $defaultSession = old('session_type', $studentProfile->preferred_session_type ?? 'one_to_one');
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    {{-- Hero --}}
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="sd-teacher-card__avatar !w-16 !h-16 !text-xl flex-shrink-0">
                    @if($profile->photo_url)
                        <img src="{{ $profile->photo_url }}" alt="">
                    @else
                        {{ mb_substr($instructor->name, 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold sd-tag mb-2">حجز حصة</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight m-0">
                        {{ $instructor->name }}
                    </h1>
                    @if($profile->headline)
                        <p class="text-slate-600 text-sm mt-1">{{ $profile->headline }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            العودة للمعلمين
                        </a>
                        <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline">
                            <i class="fas fa-home text-xs"></i>
                            الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-calendar-check"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">أكمل بيانات الحجز أدناه</p>
            <p class="text-xs text-white/85">متبقي من باقتك: <strong>{{ $remainingHours }}</strong> ساعة</p>
            <p class="text-[11px] text-white/75">مدة الحصة: {{ $duration }} دقيقة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- نموذج الحجز --}}
        <div class="xl:col-span-2">
            <form method="post" action="{{ route('student.tutor-lessons.book.store', $instructor) }}" class="sd-panel sd-form">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">تفاصيل الحجز</h2>
                </div>
                <div class="sd-panel-body space-y-5">
                    @csrf

                    @if($errors->any())
                        <div class="sd-alert sd-alert-error space-y-1">
                            @foreach($errors->all() as $e)
                                <p class="m-0 flex items-start gap-2">
                                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                                    <span>{{ $e }}</span>
                                </p>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label for="scheduled_at">الموعد *</label>
                        <input
                            type="datetime-local"
                            id="scheduled_at"
                            name="scheduled_at"
                            value="{{ old('scheduled_at') }}"
                            required
                            min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                        >
                        <p class="text-[11px] text-slate-500 mt-1.5">اختر وقتاً ضمن جدول المعلم الأسبوعي (انظر الجانب).</p>
                    </div>

                    <div>
                        <label for="academic_subject_id">المادة</label>
                        <select id="academic_subject_id" name="academic_subject_id">
                            <option value="">— اختر مادة —</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" @selected(old('academic_subject_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @if($subjects->isEmpty())
                            <p class="text-xs text-amber-700 mt-1">حدّث موادك من <a href="{{ route('student.tutor-lessons.profile') }}" class="sd-link">الملف الدراسي</a>.</p>
                        @endif
                    </div>

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
                                    <label class="sd-chip block !items-start !text-right w-full p-3">
                                        <input type="radio" name="tutor_group_offer_id" value="{{ $offer->id }}"
                                               data-duration="{{ $offer->duration_minutes }}"
                                               @checked((int) old('tutor_group_offer_id') === (int) $offer->id)>
                                        <span class="flex-1">
                                            <strong class="block text-slate-800">{{ $offer->title }}</strong>
                                            <span class="text-xs text-slate-500">
                                                {{ $offer->min_group_size }}–{{ $offer->max_group_size }} طلاب
                                                · {{ $offer->duration_minutes }} دقيقة
                                                @if($offer->display_price)
                                                    · {{ number_format((float) $offer->display_price, 0) }} ر.س
                                                @endif
                                            </span>
                                            @if($offer->description)
                                                <span class="text-[11px] text-slate-500 block mt-0.5">{{ Str::limit($offer->description, 120) }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @if(($groupLimits['max_size'] ?? 0) > 0)
                                <p class="text-[11px] text-slate-500 mt-1.5">باقتك تسمح بمجموعات حتى {{ $groupLimits['max_size'] }} طلاب.</p>
                            @endif
                        </div>
                    @endif

                    <div>
                        <label for="student_notes">ملاحظات للمعلم</label>
                        <textarea
                            id="student_notes"
                            name="student_notes"
                            rows="4"
                            placeholder="المنهج، نقاط الضعف، الوقت المناسب، أو أي تفاصيل تساعد المعلم..."
                        >{{ old('student_notes') }}</textarea>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <button type="submit" class="sd-btn-primary w-full sm:w-auto min-w-[200px]">
                            <i class="fas fa-paper-plane text-xs"></i>
                            إرسال طلب الحصة
                        </button>
                        <p class="text-[11px] text-slate-500 mt-2 m-0">
                            يُخصم من باقتك بعد تأكيد وإنهاء الحصة. يصل للمعلم إشعار بالطلب.
                        </p>
                    </div>
                </div>
            </form>
        </div>

        {{-- معلومات المعلم والجدول --}}
        <div class="space-y-4">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">عن المعلم</h2>
                </div>
                <div class="sd-panel-body">
                    <ul class="sd-info-list m-0 p-0 list-none">
                        <li class="sd-info-item">
                            <i class="fas fa-briefcase"></i>
                            <span>{{ (int) ($profile->tutor_years_experience ?? 0) }} سنوات خبرة</span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-clock"></i>
                            <span>مدة الحصة الافتراضية: {{ $duration }} دقيقة</span>
                        </li>
                        @if($instructorSubjects->isNotEmpty())
                            <li class="sd-info-item">
                                <i class="fas fa-book"></i>
                                <span>
                                    المواد:
                                    {{ $instructorSubjects->pluck('name')->join('، ') }}
                                </span>
                            </li>
                        @endif
                    </ul>
                    @if($profile->bio)
                        <p class="text-xs text-slate-500 leading-relaxed mt-3 mb-0 border-t border-slate-100 pt-3">
                            {{ Str::limit(strip_tags($profile->bio), 200) }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">الجدول الأسبوعي</h2>
                </div>
                <div class="sd-panel-body">
                    @if($availByDay->isNotEmpty())
                        @foreach($availByDay as $day => $slots)
                            @php $dayLabel = \App\Models\TutorAvailability::dayLabels()[$day] ?? $day; @endphp
                            <div class="mb-3 last:mb-0">
                                <p class="text-xs font-bold text-slate-700 mb-1">{{ $dayLabel }}</p>
                                @foreach($slots as $a)
                                    <div class="sd-avail-row">
                                        <span class="sd-avail-time">
                                            {{ substr($a->start_time, 0, 5) }} – {{ substr($a->end_time, 0, 5) }}
                                        </span>
                                        <span class="text-[10px] text-emerald-600 font-bold">متاح</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6 text-slate-500 text-sm">
                            <i class="fas fa-calendar-xmark text-2xl opacity-30 block mb-2"></i>
                            لم يُحدد جدول أسبوعي بعد — اختر موعداً يتوافق مع تواصل المعلم.
                        </div>
                    @endif
                </div>
            </div>

            <div class="sd-panel border border-amber-100 bg-amber-50/40">
                <div class="sd-panel-body text-sm text-amber-900">
                    <p class="font-bold m-0 mb-1 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-600"></i>
                        نصيحة
                    </p>
                    <p class="text-xs leading-relaxed m-0 text-amber-800/90">
                        إذا لم يُقبل الموعد، جرّب وقتاً آخر ضمن الفترات المعروضة أو غيّر المعلم من قائمة المعلمين.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(($groupOffers ?? collect())->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var block = document.getElementById('group-offers-block');
    if (!block) return;
    var radios = document.querySelectorAll('input[name="session_type"]');
    function sync() {
        var selected = document.querySelector('input[name="session_type"]:checked');
        var isGroup = selected && selected.value === 'small_group';
        block.classList.toggle('hidden', !isGroup);
    }
    radios.forEach(function (r) { r.addEventListener('change', sync); });
    sync();
});
</script>
@endpush
@endif
@endsection

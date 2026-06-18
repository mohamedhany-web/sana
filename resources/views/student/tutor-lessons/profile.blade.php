@extends('layouts.app')

@section('title', 'ملفي الدراسي')
@section('header', 'ملفي الدراسي')

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $matchingLabels = \App\Models\StudentLearningProfile::matchingModeLabels();
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $matchingLabel = $matchingLabels[$profile->matching_mode] ?? $profile->matching_mode;
    $remainingHours = max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used);
    $selectedSubjects = $subjects->whereIn('id', $profile->subject_ids ?? []);
    $yearName = $years->firstWhere('id', $profile->academic_year_id)?->name;
    $modeHints = [
        'assisted' => 'المنصة تساعدك في إيجاد معلم مناسب عبر طلب مساعدة.',
        'self_schedule' => 'تختار موعداً من الأوقات المتاحة ويُعيَّن معلم تلقائياً.',
        'pick_teacher' => 'تتصفح المعلمين وتحجز مع من يناسبك مباشرة.',
    ];
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    {{-- Hero --}}
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2">حصص مع المعلمين</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        ملفي الدراسي
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        حدّد مرحلتك وموادك ونمط التوافق مع المعلم — يُستخدم هذا لعرض المعلمين المناسبين وطريقة الحجز.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            {{ __('tutor.student_hub_title') }}
                        </a>
                        @if($profile->matching_mode === 'assisted')
                            <a href="{{ route('student.tutor-lessons.assisted') }}" class="sd-btn-primary">
                                <i class="fas fa-hands-helping text-xs"></i>
                                طلب مساعدة
                            </a>
                        @elseif($profile->matching_mode === 'self_schedule')
                            <a href="{{ route('student.tutor-lessons.schedule') }}" class="sd-btn-primary">
                                <i class="fas fa-calendar-plus text-xs"></i>
                                {{ __('tutor.self_schedule_title') }}
                            </a>
                        @else
                            <a href="{{ route('student.tutor-lessons.teachers') }}" class="sd-btn-primary">
                                <i class="fas fa-user-graduate text-xs"></i>
                                اختيار معلم
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-id-card"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">نمطك الحالي: {{ $matchingLabel }}</p>
            <p class="text-xs text-white/85">متبقي: <strong>{{ $remainingHours }}</strong> من {{ (int) $profile->lesson_hours_quota }} ساعة</p>
            @if($profile->assessed_at)
                <p class="text-[11px] text-white/75">آخر تحديث: {{ $profile->updated_at?->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    {{-- ملخص الباقة --}}
    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3">ملخص باقة الحصص</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandBlue }},#2563eb)"><i class="fas fa-box"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) $profile->lesson_hours_quota }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات الباقة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) $profile->lesson_hours_used }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات مستهلكة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $remainingHours }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">ساعات متبقية</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandPurple }},#6d28d9)"><i class="fas fa-book-open"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ $selectedSubjects->count() }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">مواد محددة</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- النموذج — عرض كامل --}}
        <div class="xl:col-span-2 space-y-4">
            @if(session('success'))
                <div class="sd-alert sd-alert-success">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="post" action="{{ route('student.tutor-lessons.profile.update') }}" class="sd-panel sd-form">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">بيانات التعلم</h2>
                </div>
                <div class="sd-panel-body space-y-6">
                    @csrf

                    @if($errors->any())
                        <div class="sd-alert sd-alert-error space-y-1">
                            @foreach($errors->all() as $e)
                                <p class="m-0">{{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <section class="space-y-3">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-violet-600 text-xs"></i>
                            المرحلة والمواد
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="academic_year_id">المرحلة / المسار</label>
                                <select id="academic_year_id" name="academic_year_id">
                                    <option value="">— اختر —</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y->id }}" @selected(old('academic_year_id', $profile->academic_year_id) == $y->id)>{{ $y->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>الصف / المرحلة الدراسية</label>
                                <input name="grade_stage" value="{{ old('grade_stage', $profile->grade_stage) }}" placeholder="مثال: الصف الثالث الثانوي">
                            </div>
                        </div>
                        <div>
                            <label>المنهج</label>
                            <input name="curriculum_label" value="{{ old('curriculum_label', $profile->curriculum_label) }}" placeholder="مثال: منهج وطني، IGCSE، SAT...">
                        </div>
                        <div>
                            <label>المواد *</label>
                            @if($subjects->isEmpty())
                                <p class="text-sm text-rose-600">لا توجد مواد نشطة في المنصة.</p>
                            @else
                                <div class="flex flex-wrap gap-2 mt-1" id="subject-chips">
                                    @foreach($subjects as $s)
                                        <label class="sd-chip" data-subject-year="{{ $s->academic_year_id }}">
                                            <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, old('subject_ids', $profile->subject_ids ?? [])))>
                                            {{ $s->name }}@if($s->academicYear)<span class="text-[10px] opacity-70"> · {{ $s->academicYear->name }}</span>@endif
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('subject_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </section>

                    <section class="space-y-3 pt-2 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-route text-violet-600 text-xs"></i>
                            نمط التوافق مع المعلم *
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @foreach($matchingLabels as $k => $lbl)
                                <label class="sd-panel !rounded-2xl cursor-pointer border-2 border-slate-100 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50/50 p-0 overflow-hidden">
                                    <div class="p-4">
                                        <input type="radio" name="matching_mode" value="{{ $k }}" class="sr-only" @checked(old('matching_mode', $profile->matching_mode) === $k)>
                                        <span class="block font-bold text-slate-800 text-sm">{{ $lbl }}</span>
                                        <span class="block text-xs text-slate-500 mt-1 leading-relaxed">{{ $modeHints[$k] ?? '' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('matching_mode')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                    </section>

                    <section class="space-y-3 pt-2 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-800 m-0 flex items-center gap-2">
                            <i class="fas fa-users text-violet-600 text-xs"></i>
                            تفضيلات الحصة
                        </h3>
                        <div>
                            <label class="mb-2 block">نوع الحصة المفضل *</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($sessionLabels as $sk => $slbl)
                                    <label class="sd-chip">
                                        <input type="radio" name="preferred_session_type" value="{{ $sk }}" @checked(old('preferred_session_type', $profile->preferred_session_type) === $sk)>
                                        {{ $slbl }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="assessment_notes">تقييمك واحتياجاتك / الوقت المناسب</label>
                            <textarea id="assessment_notes" name="assessment_notes" rows="4" placeholder="اذكر نقاط ضعفك، أهدافك، وأوقاتك المناسبة للحصص...">{{ old('assessment_notes', $profile->assessment_notes) }}</textarea>
                            <p class="text-[11px] text-slate-500 mt-1">يساعد المعلمين والإدارة على اقتراح الأنسب لك.</p>
                        </div>
                    </section>

                    <div class="pt-2 border-t border-slate-100 flex flex-wrap gap-3">
                        <button type="submit" class="sd-btn-primary min-w-[180px]">
                            <i class="fas fa-save text-xs"></i>
                            حفظ الملف
                        </button>
                        <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline">إلغاء</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ملخص جانبي --}}
        <div class="space-y-4">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">ملخصك الحالي</h2>
                </div>
                <div class="sd-panel-body">
                    <ul class="sd-info-list m-0 p-0 list-none">
                        <li class="sd-info-item">
                            <i class="fas fa-layer-group"></i>
                            <span><strong>المرحلة:</strong> {{ $yearName ?? '—' }}</span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-book"></i>
                            <span>
                                <strong>المواد:</strong>
                                @if($selectedSubjects->isNotEmpty())
                                    {{ $selectedSubjects->pluck('name')->join('، ') }}
                                @else
                                    لم تُحدد بعد
                                @endif
                            </span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-compass"></i>
                            <span><strong>نمط التوافق:</strong> {{ $matchingLabel }}</span>
                        </li>
                        <li class="sd-info-item">
                            <i class="fas fa-chalkboard"></i>
                            <span><strong>نوع الحصة:</strong> {{ $sessionLabels[$profile->preferred_session_type] ?? $profile->preferred_session_type }}</span>
                        </li>
                        @if($profile->curriculum_label)
                            <li class="sd-info-item">
                                <i class="fas fa-map"></i>
                                <span><strong>المنهج:</strong> {{ $profile->curriculum_label }}</span>
                            </li>
                        @endif
                        @if($profile->grade_stage)
                            <li class="sd-info-item">
                                <i class="fas fa-school"></i>
                                <span><strong>الصف:</strong> {{ $profile->grade_stage }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 text-sm m-0">ماذا بعد الحفظ؟</h2>
                </div>
                <div class="sd-panel-body space-y-2 text-sm">
                    @if($profile->matching_mode === 'assisted')
                        <a href="{{ route('student.tutor-lessons.assisted') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="fas fa-hands-helping"></i></span>
                            <span class="font-bold text-slate-700">إرسال طلب مساعدة</span>
                        </a>
                    @elseif($profile->matching_mode === 'self_schedule')
                        <a href="{{ route('student.tutor-lessons.schedule') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:var(--sd-gradient)"><i class="fas fa-calendar-plus"></i></span>
                            <span class="font-bold text-slate-700">حجز موعد تلقائي</span>
                        </a>
                    @else
                        <a href="{{ route('student.tutor-lessons.teachers') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                            <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-user-graduate"></i></span>
                            <span class="font-bold text-slate-700">اختيار معلم وحجز</span>
                        </a>
                    @endif
                    <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                        <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,{{ $brandPurple }},#6d28d9)"><i class="fas fa-list"></i></span>
                        <span class="font-bold text-slate-700">حجوزاتي</span>
                    </a>
                </div>
            </div>

            @if($remainingHours <= 0 && (int) $profile->lesson_hours_quota > 0)
                <div class="sd-panel border border-amber-200 bg-amber-50/50">
                    <div class="sd-panel-body text-sm text-amber-900">
                        <p class="font-bold m-0 mb-1">انتهت ساعات الباقة</p>
                        <p class="text-xs m-0">تواصل مع الإدارة أو رقِّ اشتراكك لمواصلة الحجز.</p>
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
    var yearSelect = document.getElementById('academic_year_id');
    var chips = document.querySelectorAll('[data-subject-year]');
    function syncSubjects() {
        var yearId = yearSelect ? yearSelect.value : '';
        chips.forEach(function (chip) {
            var show = !yearId || chip.getAttribute('data-subject-year') === yearId;
            chip.style.display = show ? '' : 'none';
            if (!show) {
                var input = chip.querySelector('input[type="checkbox"]');
                if (input) input.checked = false;
            }
        });
    }
    if (yearSelect && chips.length) {
        yearSelect.addEventListener('change', syncSubjects);
        syncSubjects();
    }
})();
</script>
@endpush

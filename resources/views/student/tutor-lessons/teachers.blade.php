@extends('layouts.app')

@section('title', 'اختيار معلم')
@section('header', 'اختيار معلم')

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $matchingLabel = \App\Models\StudentLearningProfile::matchingModeLabels()[$profile->matching_mode] ?? $profile->matching_mode;
    $remainingHours = max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used);
    $subjectMap = $subjects->keyBy('id');
    $teacherCount = $profiles->count();
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    {{-- Hero --}}
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2">حصص مع المعلمين</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        اختيار معلم
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-2xl leading-relaxed">
                        تصفّح المعلمين المفعّلين لموادك ونمط حجزك، ثم احجز حصة مباشرة.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            {{ __('tutor.student_hub_title') }}
                        </a>
                        <a href="{{ route('student.tutor-lessons.profile') }}" class="sd-btn-outline">
                            <i class="fas fa-sliders text-xs"></i>
                            ملفي الدراسي
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center text-xl">
                <i class="fas fa-user-graduate"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">
                {{ $teacherCount > 0 ? $teacherCount.' معلم متاح' : 'لا معلمين لهذه المادة حالياً' }}
            </p>
            <p class="text-xs text-white/85">متبقي من باقتك: <strong>{{ $remainingHours }}</strong> ساعة</p>
            <p class="text-[11px] text-white/75">نمط التوافق: {{ $matchingLabel }}</p>
        </div>
    </div>

    {{-- فلتر المادة --}}
    @if($subjects->isNotEmpty())
        <div class="sd-panel">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800 text-sm m-0">تصفية حسب المادة</h2>
            </div>
            <div class="sd-panel-body">
                <form method="get" action="{{ route('student.tutor-lessons.teachers') }}" class="sd-filter-bar">
                    <div>
                        <label class="text-xs font-bold text-slate-600 block mb-1">المادة</label>
                        <select name="subject_id" onchange="this.form.submit()">
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" @selected((int) $subjectId === (int) $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($subjectId && $subjectMap->has($subjectId))
                        <p class="text-xs text-slate-500 mb-0 self-center">
                            تعرض المعلمين الذين يدرّسون: <strong class="text-slate-700">{{ $subjectMap[$subjectId]->name }}</strong>
                        </p>
                    @endif
                </form>
            </div>
        </div>
    @endif

    {{-- بطاقات المعلمين --}}
    @if($profiles->isNotEmpty())
        <div>
            <h2 class="text-sm font-bold text-slate-700 mb-3">المعلمون المتاحون</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($profiles as $p)
                    @php
                        $instructor = $p->user;
                        $teacherSubjects = collect($p->tutor_subject_ids ?? [])
                            ->map(fn ($id) => $subjectMap->get($id)?->name)
                            ->filter()
                            ->take(3);
                    @endphp
                    <article class="sd-teacher-card">
                        <div class="sd-teacher-card__head">
                            <div class="flex gap-3 items-start">
                                <div class="sd-teacher-card__avatar">
                                    @if($p->photo_url)
                                        <img src="{{ $p->photo_url }}" alt="">
                                    @else
                                        {{ mb_substr($instructor?->name ?? '?', 0, 1) }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-heading font-bold text-slate-800 truncate text-base m-0">
                                        {{ $instructor?->name }}
                                    </h3>
                                    @if($p->headline)
                                        <p class="text-sm text-slate-600 mt-1 line-clamp-2 leading-snug">{{ $p->headline }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                                        <i class="fas fa-briefcase text-[10px]"></i>
                                        {{ (int) ($p->tutor_years_experience ?? 0) }} سنوات خبرة
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="sd-teacher-card__body">
                            @if($teacherSubjects->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($teacherSubjects as $subName)
                                        <span class="sd-pill">{{ $subName }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if($p->bio)
                                <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed m-0">{{ Str::limit(strip_tags($p->bio), 120) }}</p>
                            @endif
                            <p class="text-[11px] text-slate-400 mt-auto flex items-center gap-1">
                                <i class="fas fa-clock text-[10px]"></i>
                                مدة الحصة الافتراضية: {{ (int) ($p->tutor_default_duration_minutes ?? 60) }} دقيقة
                            </p>
                        </div>
                        <div class="sd-teacher-card__foot">
                            <a href="{{ route('student.tutor-lessons.book', $instructor) }}" class="sd-btn-primary w-full">
                                <i class="fas fa-calendar-plus text-xs"></i>
                                حجز حصة
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @else
        <div class="sd-panel">
            <div class="sd-empty">
                <i class="fas fa-user-slash"></i>
                <p class="font-bold text-slate-700 mb-2">لا يوجد معلمون متاحون حالياً</p>
                <p class="text-sm max-w-md mx-auto leading-relaxed">
                    قد يكون السبب عدم تطابق المادة، أو أن المعلمين لم يُفعَّلوا بعد.
                    جرّب مادة أخرى من ملفك، أو تواصل مع الدعم.
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-5">
                    <a href="{{ route('student.tutor-lessons.profile') }}" class="sd-btn-outline">تعديل موادي</a>
                    <a href="{{ route('student.tutor-lessons.hub') }}" class="sd-btn-primary">العودة للرئيسية</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

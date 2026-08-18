@extends('layouts.app')

@section('title', 'تفاصيل الحصة')
@section('header', 'تفاصيل الحصة')

@include('student.tutor-lessons.partials.dashboard-styles')

@section('content')
@php
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $instructor = $booking->instructor;
    $profile = $instructor?->instructorProfile;
    $sessionLabels = \App\Models\StudentLearningProfile::sessionTypeLabels();
    $sessionLabel = $sessionLabels[$booking->session_type] ?? null;
    $myRating = $booking->ratings->firstWhere('rater_id', auth()->id());
    $teacherEvaluation = $booking->instructorEvaluation();
    $lessonRecording = $lessonRecording ?? null;
    $canJoin = $booking->isLiveJoinable() && $booking->liveJoinUrl();
    $badgeClass = match ($booking->status) {
        'confirmed' => 'sd-badge-confirmed',
        'in_progress' => 'sd-badge-live',
        'completed' => 'sd-badge-done',
        'cancelled' => 'sd-badge-off',
        default => 'sd-badge-pending',
    };
    $statusHint = match ($booking->status) {
        'pending' => 'بانتظار تأكيد المعلم لموعد الحصة.',
        'confirmed' => 'الحصة مؤكدة. ادخل الغرفة في الموعد المحدد.',
        'in_progress' => 'الحصة شغّالة الآن. يمكنك الدخول أو إعادة الدخول.',
        'completed' => 'انتهت الحصة. يمكنك تقييم المعلم ومشاهدة التسجيل إن وُجد.',
        'cancelled' => 'تم إلغاء هذا الحجز.',
        default => 'تابع حالة حصتك من هنا.',
    };
@endphp

<div class="sd-page space-y-6 pb-8 w-full">
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                <div class="sd-teacher-card__avatar !w-16 !h-16 !text-xl flex-shrink-0">
                    @if($profile?->photo_url)
                        <img src="{{ $profile->photo_url }}" alt="">
                    @else
                        {{ mb_substr($instructor?->name ?? '?', 0, 1) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold sd-tag mb-2">حجز #{{ $booking->code }}</p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight m-0">
                        حصة مع {{ $instructor?->name ?? 'المعلم' }}
                    </h1>
                    <p class="text-slate-600 text-sm mt-2 flex flex-wrap items-center gap-2">
                        <span class="sd-badge {{ $badgeClass }}">{{ $booking->statusLabel() }}</span>
                        @if($booking->subject)
                            <span class="sd-pill">{{ $booking->subject->name }}</span>
                        @endif
                        @if($booking->is_trial)
                            <span class="sd-pill">حصة تجريبية</span>
                        @endif
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('student.tutor-lessons.bookings.index') }}" class="sd-btn-outline">
                            <i class="fas fa-arrow-right text-xs"></i>
                            كل الحصص
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
                <i class="fas {{ $canJoin ? 'fa-video' : 'fa-chalkboard-user' }}"></i>
            </span>
            <p class="font-bold text-sm leading-relaxed">{{ $statusHint }}</p>
            @if($canJoin)
                <a href="{{ $booking->liveJoinUrl() }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white text-sm font-black px-4 py-2.5 no-underline" style="color: {{ $brandPurple }}">
                    <i class="fas fa-video text-xs"></i>
                    {{ $booking->liveJoinLabel() }}
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="sd-alert sd-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sd-alert sd-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="sd-alert sd-alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
    @endif

    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3">ملخص الحصة</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandBlue }},#2563eb)"><i class="fas fa-calendar-day"></i></span>
                </div>
                <p class="text-sm font-black text-slate-800 leading-snug">{{ display_datetime($booking->scheduled_at) }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">موعد الحصة</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#f59e0b,#ea580c)"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">{{ (int) ($booking->duration_minutes ?: 60) }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">مدة الحصة (دقيقة)</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">
                    @if((int) $booking->billable_minutes > 0)
                        {{ (int) $booking->billable_minutes }}
                    @else
                        {{ (int) ($booking->billable_seconds ?? 0) }}
                    @endif
                </p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">{{ (int) $booking->billable_minutes > 0 ? 'دقائق اللايف مع المعلم' : 'ثواني اللايف مع المعلم' }}</p>
            </div>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,{{ $brandPurple }},#6d28d9)"><i class="fas fa-user-check"></i></span>
                </div>
                <p class="text-base font-black text-slate-800 leading-snug">{{ $booking->statusLabel() }}</p>
                <p class="text-xs font-bold text-slate-600 mt-0.5">حالة الحجز</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">بيانات الحجز</h2>
                    <span class="sd-badge {{ $badgeClass }}">{{ $booking->statusLabel() }}</span>
                </div>
                <div class="sd-panel-body">
                    <div class="sd-detail-row">
                        <span>المعلم</span>
                        <strong>{{ $instructor?->name ?? '—' }}</strong>
                    </div>
                    <div class="sd-detail-row">
                        <span>رقم الحجز</span>
                        <strong class="font-mono tracking-wide">{{ $booking->code }}</strong>
                    </div>
                    <div class="sd-detail-row">
                        <span>الموعد</span>
                        <strong>{{ display_datetime($booking->scheduled_at) }}</strong>
                    </div>
                    @if($booking->subject)
                        <div class="sd-detail-row">
                            <span>المادة</span>
                            <strong>{{ $booking->subject->name }}</strong>
                        </div>
                    @endif
                    @if($sessionLabel)
                        <div class="sd-detail-row">
                            <span>نوع الحصة</span>
                            <strong>{{ $sessionLabel }}</strong>
                        </div>
                    @endif
                    <div class="sd-detail-row">
                        <span>المدة المخططة</span>
                        <strong>{{ (int) ($booking->duration_minutes ?: 60) }} دقيقة</strong>
                    </div>
                    @if((int) $booking->billable_minutes > 0 || (int) ($booking->billable_seconds ?? 0) > 0)
                        <div class="sd-detail-row">
                            <span>وقت الحضور المشترك</span>
                            <strong>
                                @if((int) $booking->billable_minutes > 0)
                                    {{ (int) $booking->billable_minutes }} دقيقة
                                @else
                                    {{ (int) $booking->billable_seconds }} ثانية
                                @endif
                            </strong>
                        </div>
                    @endif
                    @if($booking->student_notes)
                        <div class="pt-3">
                            <p class="text-xs font-bold text-slate-500 mb-2 m-0">ملاحظاتك</p>
                            <p class="text-sm text-slate-700 bg-slate-50 border border-slate-100 rounded-xl p-3 m-0 whitespace-pre-line">{{ $booking->student_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($booking->status === 'completed' && ($teacherEvaluation || $myRating))
                <div class="sd-panel">
                    <div class="sd-panel-head">
                        <h2 class="font-heading font-bold text-slate-800 m-0">التقييم</h2>
                    </div>
                    <div class="sd-panel-body space-y-4">
                        @if($teacherEvaluation)
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-4">
                                <p class="text-sm font-black text-emerald-900 m-0 mb-2">تقييم المعلم للحصة</p>
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <span>الطالب: <strong>{{ (int) $teacherEvaluation->rating }}★</strong></span>
                                    <span>الحصة: <strong>{{ (int) ($teacherEvaluation->lesson_rating ?? $teacherEvaluation->rating) }}★</strong></span>
                                </div>
                                @if($teacherEvaluation->comment)
                                    <p class="text-sm text-slate-700 mt-2 mb-0 whitespace-pre-line">{{ $teacherEvaluation->comment }}</p>
                                @endif
                            </div>
                        @endif
                        @if($myRating)
                            <div class="rounded-xl border border-violet-100 bg-violet-50/70 p-4">
                                <p class="text-sm font-black text-violet-900 m-0 mb-2">تقييمك للمعلم</p>
                                <p class="text-sm m-0"><strong>{{ (int) $myRating->rating }}★</strong></p>
                                @if($myRating->comment)
                                    <p class="text-sm text-slate-700 mt-2 mb-0 whitespace-pre-line">{{ $myRating->comment }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">إجراءات الحصة</h2>
                </div>
                <div class="sd-panel-body space-y-3">
                    @if($canJoin)
                        <a href="{{ $booking->liveJoinUrl() }}" class="sd-btn-primary w-full">
                            <i class="fas fa-video"></i>
                            {{ $booking->liveJoinLabel() }}
                        </a>
                        @if($booking->status === 'in_progress')
                            <p class="text-xs text-slate-500 m-0 leading-relaxed">{{ __('tutor.rejoin_lesson_hint') }}</p>
                        @endif
                    @elseif(in_array($booking->status, ['confirmed', 'in_progress'], true) && ! $booking->classroomMeeting)
                        <p class="text-sm text-slate-500 m-0">غرفة الحصة لم تُفتح بعد. ستظهر هنا عند تأكيد المعلم.</p>
                    @endif

                    @if($booking->status === 'pending')
                        <form method="post" action="{{ route('student.tutor-lessons.bookings.cancel', $booking) }}" onsubmit="return confirm('إلغاء هذا الحجز؟');">
                            @csrf
                            <button type="submit" class="sd-btn-outline w-full justify-center text-rose-600 border-rose-200 hover:border-rose-400">
                                <i class="fas fa-times"></i>
                                إلغاء الحجز
                            </button>
                        </form>
                    @endif

                    @if($booking->status === 'completed')
                        @if($myRating)
                            <a href="{{ route('student.tutor-lessons.bookings.rate', $booking) }}" class="sd-btn-outline w-full">
                                <i class="fas fa-star"></i>
                                تعديل تقييمك
                            </a>
                        @else
                            <a href="{{ route('student.tutor-lessons.bookings.rate', $booking) }}" class="sd-btn-primary w-full">
                                <i class="fas fa-star"></i>
                                {{ __('tutor.rate_lesson') }}
                            </a>
                        @endif
                    @endif

                    @if($lessonRecording)
                        @if($lessonRecording->isReady())
                            <a href="{{ route('student.live-recordings.lesson', $lessonRecording) }}" class="sd-btn-outline w-full justify-center">
                                <i class="fas fa-play-circle"></i>
                                مشاهدة التسجيل
                            </a>
                        @else
                            <div class="rounded-xl border border-amber-100 bg-amber-50 text-amber-900 text-xs font-bold p-3">
                                جاري تجهيز تسجيل الحصة… حدّث الصفحة بعد قليل.
                            </div>
                        @endif
                    @endif

                    <a href="{{ route('student.tutor-lessons.teachers') }}" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition no-underline text-inherit">
                        <span class="sd-kpi-icon !w-10 !h-10 text-sm" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-calendar-plus"></i></span>
                        <span class="text-sm font-bold text-slate-700">حجز حصة جديدة</span>
                    </a>
                </div>
            </div>

            <div class="sd-panel">
                <div class="sd-panel-head">
                    <h2 class="font-heading font-bold text-slate-800 m-0">المعلم</h2>
                </div>
                <div class="sd-panel-body">
                    <div class="flex items-center gap-3">
                        <div class="sd-teacher-card__avatar">
                            @if($profile?->photo_url)
                                <img src="{{ $profile->photo_url }}" alt="">
                            @else
                                {{ mb_substr($instructor?->name ?? '?', 0, 1) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-heading font-black text-slate-800 m-0 truncate">{{ $instructor?->name }}</p>
                            @if($profile?->headline)
                                <p class="text-xs text-slate-500 mt-1 mb-0 line-clamp-2">{{ $profile->headline }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

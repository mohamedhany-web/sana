@extends('layouts.app')

@section('title', __('student.calendar_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.calendar_title') }}</h1>
            <p class="sanua-page-head__sub">{{ __('student.calendar_subtitle') }}</p>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-calendar-alt"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['total'] ?? 0 }}</strong>
                <span>{{ __('student.total_events') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-clipboard-check"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['exams'] ?? 0 }}</strong>
                <span>{{ __('student.legend_exams') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-chalkboard-teacher"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['lectures'] ?? 0 }}</strong>
                <span>{{ __('student.legend_lectures') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                <i class="fas fa-arrow-up"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['upcoming'] ?? 0 }}</strong>
                <span>قادمة</span>
            </div>
        </div>
    </div>

    <div class="sanua-calendar-layout">
        <div class="sanua-calendar-panel sanua-fc-wrap">
            <div id="calendar"></div>
            <div class="sanua-event-legend">
                <span><i style="background:#EF4444"></i>{{ __('student.legend_exams') }}</span>
                <span><i style="background:#8B5CF6"></i>{{ __('student.legend_lectures') }}</span>
                <span><i style="background:#F59E0B"></i>{{ __('student.legend_assignments') }}</span>
                <span><i style="background:#22C55E"></i>{{ __('student.other_events') }}</span>
            </div>
        </div>

        <aside class="sanua-calendar-sidebar">
            <div class="sanua-panel">
                <div class="sanua-panel__head">
                    <h3><i class="fas fa-clock text-violet-500 ml-1"></i> الأحداث القادمة</h3>
                </div>
                <div class="sanua-panel__body" style="display:flex;flex-direction:column;gap:8px;max-height:360px;overflow-y:auto;">
                    @forelse($events->where('start_date', '>=', now())->take(10) as $event)
                        <div class="sanua-calendar-event" onclick="window.location.href='{{ $event->url ?? '#' }}'">
                            <div class="sanua-calendar-event__title">{{ $event->title }}</div>
                            <div class="sanua-calendar-event__meta">
                                <i class="fas fa-calendar ml-1" style="color:#8B5CF6"></i>
                                {{ $event->start_date->format('d/m/Y') }}
                                @if(! $event->is_all_day)
                                    {{ $event->start_date->format('h:i A') }}
                                @endif
                                ·
                                @if($event->type == 'exam') امتحان
                                @elseif($event->type == 'lecture') محاضرة
                                @elseif($event->type == 'assignment') واجب
                                @else حدث
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-6 m-0">لا توجد أحداث قادمة</p>
                    @endforelse
                </div>
            </div>

            <div class="sanua-calendar-stats">
                <h3><i class="fas fa-chart-pie ml-1"></i> إحصائيات</h3>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-clipboard-check ml-1"></i> الامتحانات</span>
                    <strong>{{ $stats['exams'] ?? 0 }}</strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-chalkboard-teacher ml-1"></i> المحاضرات</span>
                    <strong>{{ $stats['lectures'] ?? 0 }}</strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-tasks ml-1"></i> الواجبات</span>
                    <strong>{{ $stats['assignments'] ?? 0 }}</strong>
                </div>
                <div class="sanua-calendar-stats__row">
                    <span><i class="fas fa-arrow-up ml-1"></i> القادمة</span>
                    <strong>{{ $stats['upcoming'] ?? 0 }}</strong>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم' },
        events: {
            url: '{{ route("calendar.events") }}',
            failure: function() { alert('حدث خطأ في تحميل الأحداث'); }
        },
        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url, '_self');
                info.jsEvent.preventDefault();
            }
        },
        height: 'auto',
        contentHeight: 560,
        firstDay: 6,
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });
    calendar.render();
});
</script>
@endpush

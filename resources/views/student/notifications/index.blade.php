@extends('layouts.app')

@section('title', __('student.notifications_title'))

@push('styles')
@include('dashboard.partials.sanua-theme')
@endpush

@section('content')
@php
    $notifIconClass = fn ($color) => match ($color) {
        'blue' => 'sanua-notif-icon--blue',
        'green' => 'sanua-notif-icon--green',
        'yellow' => 'sanua-notif-icon--yellow',
        'red' => 'sanua-notif-icon--red',
        'purple' => 'sanua-notif-icon--purple',
        'orange' => 'sanua-notif-icon--orange',
        default => 'sanua-notif-icon--gray',
    };
    $priorityBadgeClass = fn ($color) => match ($color) {
        'red' => 'sanua-badge--danger',
        'yellow' => 'sanua-badge--pending',
        default => 'sanua-badge--locked',
    };
@endphp

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title">{{ __('student.notifications_title') }}</h1>
            <p class="sanua-page-head__sub">آخر التحديثات والرسائل المهمة من المنصة</p>
        </div>
        <div class="sanua-page-head__actions">
            @if($stats['unread'] > 0)
                <button type="button" onclick="markAllAsRead()" class="sanua-page-head__btn">
                    <i class="fas fa-check-double"></i>
                    {{ __('student.mark_all_read') }}
                </button>
            @endif
            <button type="button" onclick="cleanup()" class="sanua-page-head__btn sanua-page-head__btn--ghost">
                <i class="fas fa-broom"></i>
                {{ __('student.cleanup_btn') }}
            </button>
        </div>
    </header>

    <div class="sanua-stats-row">
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                <i class="fas fa-bell"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['total'] }}</strong>
                <span>{{ __('student.total_notifications') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                <i class="fas fa-envelope"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['unread'] }}</strong>
                <span>{{ __('student.unread_label') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                <i class="fas fa-calendar-day"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['today'] }}</strong>
                <span>{{ __('student.today_label') }}</span>
            </div>
        </div>
        <div class="sanua-stat-pill">
            <span class="sanua-stat-pill__icon sanua-stat-pill__icon--red" aria-hidden="true">
                <i class="fas fa-exclamation-circle"></i>
            </span>
            <div class="sanua-stat-pill__body">
                <strong>{{ $stats['urgent'] }}</strong>
                <span>{{ __('student.urgent_label') }}</span>
            </div>
        </div>
    </div>

    <section class="sanua-section">
        <div class="sanua-panel">
            <div class="sanua-panel__head">
                <h3><i class="fas fa-filter ml-1"></i> تصفية الإشعارات</h3>
            </div>
            <div class="sanua-panel__body">
                <form method="GET" class="sanua-filter-form">
                    <div class="sanua-filter-form__field">
                        <label for="type">{{ __('student.notification_type_label') }}</label>
                        <select name="type" id="type">
                            <option value="">{{ __('student.all_types') }}</option>
                            @foreach($notificationTypes as $key => $type)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <label for="status">{{ __('common.status') }}</label>
                        <select name="status" id="status">
                            <option value="">{{ __('student.all_statuses') }}</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('student.unread_label') }}</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('student.read_filter') }}</option>
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <label for="priority">{{ __('student.priority_label') }}</label>
                        <select name="priority" id="priority">
                            <option value="">{{ __('student.all_priorities') }}</option>
                            @foreach($priorities as $key => $priority)
                                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $priority }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sanua-filter-form__field">
                        <button type="submit" class="sanua-btn sanua-btn--purple" style="width:100%;justify-content:center;">
                            <i class="fas fa-search"></i>
                            {{ __('student.filter_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @if($notifications->count() > 0)
        <section class="sanua-section">
            <div class="sanua-notification-list">
                @foreach($notifications as $notification)
                    <article class="sanua-notification-card {{ !$notification->is_read ? 'sanua-notification-card--unread' : '' }}">
                        <div class="sanua-notification-card__row">
                            <div class="sanua-notification-card__main">
                                <span class="sanua-notif-icon {{ $notifIconClass($notification->type_color) }}" aria-hidden="true">
                                    <i class="{{ $notification->type_icon }}"></i>
                                </span>
                                <div class="sanua-notification-card__content">
                                    <div class="sanua-notification-card__head">
                                        <h3 class="sanua-notification-card__title">{{ $notification->title }}</h3>
                                        @if($notification->priority !== 'normal')
                                            <span class="sanua-badge {{ $priorityBadgeClass($notification->priority_color) }}">
                                                {{ $priorities[$notification->priority] ?? $notification->priority }}
                                            </span>
                                        @endif
                                        @if(!$notification->is_read)
                                            <span class="sanua-badge sanua-badge--submitted">
                                                <span class="sanua-badge__dot"></span> جديد
                                            </span>
                                        @endif
                                    </div>
                                    <p class="sanua-notification-card__message">{{ $notification->message }}</p>
                                    <div class="sanua-notification-card__meta">
                                        <span><i class="fas fa-user"></i> من: {{ $notification->sender->name ?? 'النظام' }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                        @if($notification->expires_at)
                                            <span><i class="fas fa-hourglass-end"></i> ينتهي {{ $notification->expires_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    @if($notification->action_url && $notification->action_text)
                                        <a href="{{ route('notifications.go', $notification) }}" class="sanua-notification-card__link">
                                            {{ $notification->action_text }}
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="sanua-notification-card__actions">
                                @if(!$notification->is_read)
                                    <button type="button" onclick="markAsRead({{ $notification->id }})" class="sanua-icon-btn sanua-icon-btn--read" title="تحديد كمقروء">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <a href="{{ route('notifications.show', $notification) }}" class="sanua-icon-btn sanua-icon-btn--view" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" onclick="deleteNotification({{ $notification->id }})" class="sanua-icon-btn sanua-icon-btn--delete" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @if($notifications->hasPages())
            <div class="sanua-pagination">{{ $notifications->appends(request()->query())->links() }}</div>
        @endif
    @else
        <div class="sanua-empty">
            <div class="sanua-empty__icon"><i class="fas fa-bell-slash"></i></div>
            <h3>لا توجد إشعارات</h3>
            <p>ستظهر هنا آخر التحديثات والرسائل المهمة</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    if (confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('هل تريد حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function cleanup() {
    if (confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) {
        fetch('/notifications/cleanup', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endpush
@endsection

@extends('layouts.admin')

@section('title', 'وارد الإشعارات - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'وارد الإشعارات')

@section('content')
<div class="admin-dashboard admin-list-page space-y-7">

    <x-admin.page-hero
        title="وارد الإشعارات"
        subtitle="التنبيهات الموجهة لحسابك (مثل تذاكر الدعم). «مركز الإشعارات» في القائمة مخصص لإرسال تنبيهات للطلاب."
        icon="fas fa-inbox"
    >
        @if($stats['unread'] > 0)
            <form action="{{ route('admin.notifications.inbox.mark-all-read') }}" method="post" class="inline" id="inbox-mark-all-form">
                @csrf
                <button type="submit" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-check-double"></i>
                    تعيين الكل كمقروء
                </button>
            </form>
        @endif
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.notifications.index') }}" class="admin-btn admin-btn--primary">
                <i class="fas fa-paper-plane"></i>
                إرسال للطلاب
            </a>
        @endif
    </x-admin.page-hero>

    <div class="admin-mini-stats admin-mini-stats--3">
        <div class="admin-mini-stat {{ $stats['unread'] > 0 ? 'admin-mini-stat--highlight' : '' }}">
            <div class="admin-mini-stat__label">غير مقروء</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['unread']) }}</div>
            <div class="admin-mini-stat__meta">يحتاج مراجعة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">الإجمالي</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['total']) }}</div>
            <div class="admin-mini-stat__meta">كل الإشعارات</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">مقروء</div>
            <div class="admin-mini-stat__value">{{ number_format(max(0, $stats['total'] - $stats['unread'])) }}</div>
            <div class="admin-mini-stat__meta">تمت معالجتها</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-list"></i> قائمة الإشعارات</h2>
                <p class="admin-panel__sub">{{ $notifications->total() }} إشعار في هذه الصفحة</p>
            </div>
        </div>

        <div class="admin-filter-tabs">
            <a href="{{ route('admin.notifications.inbox') }}"
               class="admin-filter-tab {{ ! request()->filled('status') ? 'is-active' : '' }}">الكل</a>
            <a href="{{ route('admin.notifications.inbox', ['status' => 'unread']) }}"
               class="admin-filter-tab {{ request('status') === 'unread' ? 'is-active' : '' }}">غير مقروء فقط</a>
            <a href="{{ route('admin.notifications.inbox', ['status' => 'read']) }}"
               class="admin-filter-tab {{ request('status') === 'read' ? 'is-active' : '' }}">مقروء</a>
        </div>

        <div class="admin-panel__body--flush">
            @forelse ($notifications as $notification)
                @php
                    $notificationHref = $notification->action_url ?: route('admin.notifications.show', $notification);
                @endphp
                <div class="admin-inbox-item {{ ! $notification->is_read ? 'is-unread' : '' }}">
                    <a href="{{ $notificationHref }}"
                       class="admin-inbox-item__link"
                       data-turbo="false">
                        <span class="admin-inbox-item__icon {{ $notification->is_read ? 'admin-inbox-item__icon--read' : 'admin-inbox-item__icon--unread' }}">
                            <i class="{{ $notification->type_icon }}"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-bold text-slate-800 truncate">{{ $notification->title }}</span>
                            <span class="block text-xs text-slate-500 mt-1 line-clamp-2">{{ $notification->message }}</span>
                            <span class="block text-[10px] text-slate-400 mt-1.5">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        @if(! $notification->is_read)
                            <span class="admin-inbox-item__dot" title="غير مقروء"></span>
                        @endif
                    </a>
                    <form action="{{ route('admin.notifications.inbox.destroy', $notification) }}"
                          method="post"
                          class="admin-inbox-item__delete"
                          data-turbo="false"
                          onsubmit="return confirm('هل تريد حذف هذا الإشعار من الوارد؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="admin-inbox-item__delete-btn" title="حذف" aria-label="حذف الإشعار">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="admin-empty">
                    <i class="fas fa-inbox"></i>
                    <p class="text-sm font-medium">لا توجد إشعارات في الوارد حالياً.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="admin-pagination">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.getElementById('inbox-mark-all-form')?.addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    var token = document.querySelector('meta[name="csrf-token"]');
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
        },
        body: new FormData(form),
        credentials: 'same-origin'
    }).then(function () { window.location.reload(); }).catch(function () { form.submit(); });
});
</script>
@endpush
@endsection

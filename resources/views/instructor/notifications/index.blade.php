@extends('layouts.app')

@section('title', __('instructor.notifications'))
@section('header', __('instructor.notifications'))

@section('content')
@php
    $priorities = $priorities ?? \App\Models\Notification::getPriorities();
@endphp

<div class="space-y-6 w-full max-w-full">
    <div class="rounded-2xl p-5 sm:p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-[#283593] via-indigo-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-black leading-tight m-0">{{ __('instructor.notifications') }}</h1>
                <p class="text-sm text-white/90 mt-1 m-0">كل التحديثات الخاصة بحسابك كمدرب</p>
            </div>
            <div class="flex flex-wrap gap-2 shrink-0">
                @if($stats['unread'] > 0)
                    <button type="button" onclick="instructorMarkAllRead()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-sm font-semibold transition-colors">
                        <i class="fas fa-check-double text-xs"></i>
                        تحديد الكل كمقروء
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl p-4 sm:p-5 bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide m-0 mb-1">الكل</p>
            <p class="text-2xl font-black text-slate-800 m-0 tabular-nums">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide m-0 mb-1">غير مقروء</p>
            <p class="text-2xl font-black text-slate-800 m-0 tabular-nums">{{ $stats['unread'] }}</p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide m-0 mb-1">اليوم</p>
            <p class="text-2xl font-black text-slate-800 m-0 tabular-nums">{{ $stats['today'] }}</p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white border border-slate-200 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide m-0 mb-1">عاجل</p>
            <p class="text-2xl font-black text-slate-800 m-0 tabular-nums">{{ $stats['urgent'] }}</p>
        </div>
    </div>

    <form method="get" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1" for="type">النوع</label>
            <select name="type" id="type" class="w-full rounded-xl border-slate-200 text-sm">
                <option value="">الكل</option>
                @foreach($notificationTypes as $key => $type)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1" for="status">الحالة</label>
            <select name="status" id="status" class="w-full rounded-xl border-slate-200 text-sm">
                <option value="">الكل</option>
                <option value="unread" @selected(request('status') === 'unread')>غير مقروء</option>
                <option value="read" @selected(request('status') === 'read')>مقروء</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1" for="priority">الأولوية</label>
            <select name="priority" id="priority" class="w-full rounded-xl border-slate-200 text-sm">
                <option value="">الكل</option>
                @foreach($priorities as $key => $priority)
                    <option value="{{ $key }}" @selected(request('priority') === $key)>{{ $priority }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#283593] hover:bg-[#1F2A7A] text-white text-sm font-bold">
                <i class="fas fa-filter text-xs"></i>
                تصفية
            </button>
        </div>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            <div class="flex items-start gap-3 px-4 sm:px-5 py-4 border-b border-slate-100 last:border-b-0 {{ $notification->is_read ? '' : 'bg-indigo-50/40' }}">
                <span class="w-10 h-10 rounded-xl bg-[#eef2ff] text-[#283593] flex items-center justify-center shrink-0">
                    <i class="{{ $notification->type_icon }} text-sm"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('instructor.notifications.show', $notification) }}" class="font-bold text-slate-800 no-underline hover:text-[#283593]">
                            {{ $notification->title }}
                        </a>
                        @if(! $notification->is_read)
                            <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-md bg-[#FB5607] text-white">جديد</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600 mt-1 m-0 leading-relaxed">{{ $notification->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-1.5 m-0">
                        {{ optional($notification->created_at)->diffForHumans() }}
                        · {{ $notification->sender->name ?? 'النظام' }}
                    </p>
                    @if($notification->action_url)
                        <a href="{{ route('instructor.notifications.go', $notification) }}" class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-[#283593] no-underline hover:underline">
                            {{ $notification->action_text ?: 'فتح' }}
                            <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    @if(! $notification->is_read)
                        <button type="button" onclick="instructorMarkRead({{ $notification->id }})" class="w-8 h-8 rounded-lg text-emerald-600 hover:bg-emerald-50" title="تحديد كمقروء">
                            <i class="fas fa-check text-xs"></i>
                        </button>
                    @endif
                    <a href="{{ route('instructor.notifications.show', $notification) }}" class="w-8 h-8 rounded-lg text-[#283593] hover:bg-indigo-50 flex items-center justify-center no-underline" title="عرض">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                    <button type="button" onclick="instructorDelete({{ $notification->id }})" class="w-8 h-8 rounded-lg text-rose-500 hover:bg-rose-50" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="p-10 text-center text-slate-400">
                <i class="fas fa-bell-slash text-3xl mb-3 opacity-40"></i>
                <p class="font-bold text-slate-600 m-0">لا توجد إشعارات</p>
                <p class="text-sm mt-1 m-0">ستظهر هنا حجوزات الحصص وتحديثات المنصة</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->appends(request()->query())->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
const instructorNotifMarkReadTpl = @json(route('instructor.notifications.mark-read', ['notification' => 0]));
const instructorNotifDestroyTpl = @json(route('instructor.notifications.destroy', ['notification' => 0]));
function instructorNotifUrl(tpl, id) {
    return tpl.replace('/0/', '/' + id + '/').replace(/\/0$/, '/' + id);
}
function instructorMarkRead(id) {
    fetch(instructorNotifUrl(instructorNotifMarkReadTpl, id), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}
function instructorMarkAllRead() {
    if (!confirm('تحديد جميع الإشعارات كمقروءة؟')) return;
    fetch(@json(route('instructor.notifications.mark-all-read')), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}
function instructorDelete(id) {
    if (!confirm('حذف هذا الإشعار؟')) return;
    fetch(instructorNotifUrl(instructorNotifDestroyTpl, id), {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); });
}
</script>
@endpush
@endsection

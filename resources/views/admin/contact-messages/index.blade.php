@extends('layouts.admin')

@section('title', 'رسائل التواصل - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'رسائل التواصل')

@section('content')
<div class="admin-dashboard admin-list-page space-y-7">

    <x-admin.page-hero
        title="رسائل التواصل"
        subtitle="عرض وإدارة رسائل الزوار من نموذج التواصل في الموقع العام."
        icon="fas fa-envelope-open-text"
    >
        @if($stats['unread'] > 0)
            <span class="admin-alert-inline">
                <i class="fas fa-circle text-[6px]"></i>
                {{ number_format($stats['unread']) }} غير مقروءة
            </span>
        @endif
    </x-admin.page-hero>

    <div class="admin-mini-stats">
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">إجمالي الرسائل</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['total']) }}</div>
            <div class="admin-mini-stat__meta">جميع الرسائل</div>
        </div>
        <div class="admin-mini-stat {{ $stats['unread'] > 0 ? 'admin-mini-stat--highlight' : '' }}">
            <div class="admin-mini-stat__label">غير مقروءة</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['unread']) }}</div>
            <div class="admin-mini-stat__meta">تحتاج مراجعة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">مقروءة</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['read']) }}</div>
            <div class="admin-mini-stat__meta">تمت المعالجة</div>
        </div>
        <div class="admin-mini-stat">
            <div class="admin-mini-stat__label">رسائل اليوم</div>
            <div class="admin-mini-stat__value">{{ number_format($stats['today']) }}</div>
            <div class="admin-mini-stat__meta">وصلت اليوم</div>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <h3><i class="fas fa-filter"></i> فلترة وبحث</h3>
        </div>
        <div class="admin-panel__body">
            <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="admin-field">
                    <label>البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="الاسم، البريد، أو الموضوع..."
                           class="admin-input">
                </div>
                <div class="admin-field">
                    <label>الحالة</label>
                    <select name="status" class="admin-input">
                        <option value="">جميع الرسائل</option>
                        <option value="unread" @selected(request('status') === 'unread')>غير مقروءة</option>
                        <option value="read" @selected(request('status') === 'read')>مقروءة</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn admin-btn--primary flex-1">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.contact-messages.index') }}" class="admin-btn admin-btn--outline" title="مسح الفلتر">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-list"></i> سجل الرسائل</h2>
                <p class="admin-panel__sub"><span class="font-bold text-[var(--admin-primary)]">{{ $messages->total() }}</span> رسالة</p>
            </div>
            <span class="text-xs text-slate-400">آخر تحديث {{ now()->format('H:i') }}</span>
        </div>

        @if($messages->count() > 0)
            <div class="admin-panel__body--flush overflow-x-auto">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>المرسل</th>
                            <th>الموضوع</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="{{ ! $message->read_at ? 'is-unread' : '' }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="admin-avatar-sm">{{ mb_substr($message->name, 0, 1, 'UTF-8') }}</span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 truncate">{{ $message->name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $message->email }}</p>
                                            @if($message->phone)
                                                <p class="text-xs text-slate-400">{{ $message->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="max-w-xs">
                                    <p class="font-semibold text-slate-800 mb-0.5">{{ $message->subject }}</p>
                                    <p class="text-xs text-slate-500 line-clamp-2">{{ Str::limit($message->message, 100) }}</p>
                                    @if(strlen($message->message) > 100)
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="text-xs font-semibold mt-1 inline-block" style="color: var(--admin-primary);">قراءة المزيد</a>
                                    @endif
                                </td>
                                <td>
                                    @if($message->read_at)
                                        <span class="admin-badge admin-badge--success"><i class="fas fa-check-circle"></i> مقروءة</span>
                                    @else
                                        <span class="admin-badge admin-badge--danger"><i class="fas fa-circle text-[6px]"></i> غير مقروءة</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap">
                                    <p class="font-semibold text-slate-700">{{ $message->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $message->created_at->format('H:i') }}</p>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="admin-icon-btn" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($message->read_at)
                                            <form action="{{ route('admin.contact-messages.mark-as-unread', $message) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="admin-icon-btn" title="غير مقروءة">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.contact-messages.mark-as-read', $message) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="admin-icon-btn admin-icon-btn--success" title="مقروءة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.contact-messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-icon-btn admin-icon-btn--danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="admin-empty">
                <i class="fas fa-inbox"></i>
                <p class="text-sm font-bold text-slate-600 mb-1">لا توجد رسائل</p>
                <p class="text-xs">لم يُستلم أي رسالة تواصل بعد.</p>
            </div>
        @endif
    </div>
</div>
@endsection

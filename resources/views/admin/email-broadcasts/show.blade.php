@extends('layouts.admin')

@section('title', __('تفاصيل البريد'))
@section('header', __('إشعارات البريد (Gmail) — تفاصيل'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.email-broadcasts.index', $audience) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-right ml-1"></i> رجوع
        </a>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">الفئة: {{ $audienceLabel }}</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-2">
        <h2 class="text-xl font-black text-slate-900">{{ $broadcast->subject }}</h2>
        <p class="text-sm text-slate-600">الحالة: <span class="font-bold">{{ $broadcast->status }}</span> @if($broadcast->sent_at) · {{ $broadcast->sent_at->format('Y-m-d H:i') }} @endif</p>
        <p class="text-xs text-slate-500">المنشئ: {{ $broadcast->creator?->name ?? '—' }}</p>
        <div class="mt-4 bg-slate-50 border border-slate-100 rounded-xl p-4 whitespace-pre-wrap text-sm text-slate-800">{{ $broadcast->body }}</div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs text-slate-500 font-semibold">الإجمالي</p>
            <p class="text-2xl font-black tabular-nums">{{ $broadcast->stats['total'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
            <p class="text-xs text-emerald-800 font-semibold">تم الإرسال</p>
            <p class="text-2xl font-black tabular-nums">{{ $broadcast->stats['sent'] ?? ($counts['sent'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4">
            <p class="text-xs text-rose-800 font-semibold">فشل</p>
            <p class="text-2xl font-black tabular-nums">{{ $broadcast->stats['failed'] ?? ($counts['failed'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4">
            <p class="text-xs text-amber-800 font-semibold">متبقي</p>
            <p class="text-2xl font-black tabular-nums">{{ $broadcast->stats['remaining'] ?? ($counts['queued'] ?? 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">البريد</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">وقت الإرسال</th>
                        <th class="text-right px-4 py-3">خطأ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recipients as $r)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $r->email }}</div>
                                <div class="text-xs text-slate-500">{{ $r->name }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $r->status }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $r->sent_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-rose-700">{{ $r->error }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">{{ $recipients->links() }}</div>
    </div>
</div>
@endsection

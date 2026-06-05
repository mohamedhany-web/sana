@extends('layouts.admin')

@section('title', 'تذكرة دعم — ' . $ticket->subject)
@section('header', 'تذكرة دعم طالب')

@section('content')
@php
    $student = $ticket->user;
    $awaiting = $ticket->isAwaitingAdminResponse();
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.support-tickets.index', ['view' => 'needs_reply']) }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-900">
            <i class="fas fa-arrow-right"></i>
            العودة لقائمة الدعم
        </a>
        @if($awaiting)
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                <i class="fas fa-bell"></i> الطالب بانتظار ردّكم
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- معلومات الطالب --}}
        <aside class="space-y-4">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-l from-violet-50 to-white border-b border-slate-200">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-violet-600"></i>
                        بيانات الطالب
                    </h3>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500">الاسم</p>
                        <p class="font-bold text-slate-900">{{ $student->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">الجوال</p>
                        <p class="font-semibold text-slate-800" dir="ltr">{{ $student->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">البريد</p>
                        <p class="font-semibold text-slate-800 text-xs break-all">{{ $student->email ?? '—' }}</p>
                    </div>
                    @if(Route::has('admin.users.edit') && $student?->id)
                        <a href="{{ route('admin.users.edit', $student->id) }}"
                           class="inline-flex w-full justify-center items-center gap-2 mt-2 px-4 py-2.5 rounded-xl border border-violet-200 text-violet-800 text-xs font-bold hover:bg-violet-50">
                            <i class="fas fa-user-cog"></i>
                            فتح حساب الطالب
                        </a>
                    @endif
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-900">إدارة التذكرة</h3>
                <form method="POST" action="{{ route('admin.support-tickets.status', $ticket) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                            @foreach(\App\Models\SupportTicket::statusLabels() as $val => $lbl)
                                <option value="{{ $val }}" @selected($ticket->status === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">الأولوية</label>
                        <select name="priority" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm">
                            @foreach(\App\Models\SupportTicket::priorityLabels() as $val => $lbl)
                                <option value="{{ $val }}" @selected($ticket->priority === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-bold hover:bg-slate-900">
                        حفظ التحديث
                    </button>
                </form>
                <dl class="text-xs text-slate-500 space-y-1 pt-2 border-t border-slate-100">
                    <div class="flex justify-between"><dt>رقم التذكرة</dt><dd class="font-mono font-bold text-slate-700">#{{ $ticket->id }}</dd></div>
                    <div class="flex justify-between"><dt>تاريخ الفتح</dt><dd>{{ $ticket->created_at->format('Y-m-d H:i') }}</dd></div>
                    @if($ticket->resolved_at)
                        <div class="flex justify-between"><dt>تاريخ الحل</dt><dd>{{ $ticket->resolved_at->format('Y-m-d H:i') }}</dd></div>
                    @endif
                    @if($ticket->assignedAdmin)
                        <div class="flex justify-between"><dt>المسؤول</dt><dd>{{ $ticket->assignedAdmin->name }}</dd></div>
                    @endif
                </dl>
            </section>
        </aside>

        {{-- المحادثة والرد --}}
        <div class="xl:col-span-2 space-y-4">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold text-indigo-700">{{ $ticket->inquiryCategory->name ?? 'استفسار عام' }}</p>
                        <h1 class="text-xl font-black text-slate-900 mt-1">{{ $ticket->subject }}</h1>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @include('admin.support-tickets._badges', ['status' => $ticket->status, 'priority' => $ticket->priority])
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4 max-h-[32rem] overflow-y-auto">
                @foreach($ticket->replies as $reply)
                    <div class="rounded-xl p-4 {{ $reply->sender_type === 'admin' ? 'bg-emerald-50 border border-emerald-200 ml-0 mr-8' : 'bg-slate-50 border border-slate-200 mr-0 ml-8' }}">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <p class="text-xs font-bold {{ $reply->sender_type === 'admin' ? 'text-emerald-800' : 'text-slate-800' }}">
                                @if($reply->sender_type === 'admin')
                                    <i class="fas fa-headset ml-1"></i> فريق الدعم
                                    @if($reply->user)
                                        <span class="font-normal text-emerald-700">({{ $reply->user->name }})</span>
                                    @endif
                                @else
                                    <i class="fas fa-user-graduate ml-1"></i> {{ $reply->user->name ?? 'الطالب' }}
                                @endif
                            </p>
                            <p class="text-[11px] text-slate-500 shrink-0">{{ $reply->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <p class="text-sm text-slate-800 whitespace-pre-line leading-relaxed">{{ $reply->message }}</p>
                    </div>
                @endforeach
            </section>

            @if(!in_array($ticket->status, ['closed'], true))
                <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}" class="rounded-2xl bg-white border border-slate-200 shadow-lg p-5 space-y-4" data-turbo="false">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-900">رد للطالب</label>
                        <p class="text-xs text-slate-500 mt-0.5">سيصل الطالب إشعاراً داخل المنصة عند الإرسال.</p>
                    </div>
                    <textarea name="message" rows="5" required placeholder="اكتب ردّاً واضحاً يشرح الخطوات أو الحل…"
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="checkbox" name="mark_resolved" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>إغلاق التذكرة بعد الرد (تم الحل)</span>
                        </label>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-600 text-white text-sm font-bold hover:bg-sky-700 shadow-sm">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الرد للطالب
                        </button>
                    </div>
                </form>
            @else
                <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                    هذه التذكرة مغلقة. غيّر الحالة من اللوحة الجانبية إن احتجت إعادة فتحها.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

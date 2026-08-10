@extends('layouts.admin')

@section('title', 'تفاصيل الإشعار')
@section('header', 'تفاصيل الإشعار: ' . htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8'))

@section('content')
@php
    $isInboxItem = (int) $notification->user_id === (int) auth()->id();
    $notificationsBackUrl = $isInboxItem
        ? route('admin.notifications.inbox')
        : route('admin.notifications.index', array_filter(['audience' => $notification->audience]));
    $actionLabel = $notification->action_text ?: 'فتح الرابط المرتبط';
    $replyTarget = $replyTarget ?? null;
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-bell text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ $notificationsBackUrl }}" class="text-blue-600 hover:text-blue-700">{{ $isInboxItem ? 'وارد الإشعارات' : 'الإشعارات' }}</a>
                        <span>/</span>
                        <span class="text-slate-600">{{ htmlspecialchars(Str::limit($notification->title, 30), ENT_QUOTES, 'UTF-8') }}</span>
                    </nav>
                    <h1 class="text-2xl font-black text-slate-900 mt-1">تفاصيل الإشعار</h1>
                </div>
            </div>
            <a href="{{ $notificationsBackUrl }}"
               data-turbo="false"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-bell text-lg"></i>
                        </div>
                        تفاصيل الإشعار
                    </h3>
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $notification->is_read ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-blue-100 text-blue-700 border border-blue-200' }}">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">العنوان</label>
                            <div class="font-bold text-xl text-slate-900">{{ htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8') }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">النص</label>
                            <div class="text-slate-900 bg-slate-50 p-4 rounded-lg whitespace-pre-wrap border border-slate-200">{{ htmlspecialchars($notification->message, ENT_QUOTES, 'UTF-8') }}</div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">نوع الإشعار</label>
                                <div class="text-sm font-bold text-slate-900">{{ htmlspecialchars(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type, ENT_QUOTES, 'UTF-8') }}</div>
                            </div>
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">الأولوية</label>
                                <div class="text-sm font-bold text-slate-900">{{ htmlspecialchars(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority, ENT_QUOTES, 'UTF-8') }}</div>
                            </div>
                        </div>

                        @if($notification->audience)
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">الجمهور</label>
                                <div class="text-sm font-bold text-slate-900">{{ \App\Models\Notification::getAudiences()[$notification->audience] ?? $notification->audience }}</div>
                            </div>
                        @endif

                        @if($notification->action_url)
                            <div class="p-4 rounded-lg border border-blue-200 bg-blue-50">
                                <label class="block text-xs font-semibold text-slate-700 mb-2">الرابط المرتبط</label>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                    <a href="{{ $notification->action_url }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors"
                                       data-turbo="false">
                                        {{ htmlspecialchars($actionLabel, ENT_QUOTES, 'UTF-8') }}
                                        <i class="fas fa-arrow-left text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($replyTarget)
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-reply text-blue-600"></i>
                        الرد بالبريد الإلكتروني
                    </h3>
                    <p class="text-xs text-slate-600 mt-1">
                        إلى: <strong>{{ $replyTarget['name'] ?? '' }}</strong>
                        &lt;{{ $replyTarget['email'] }}&gt;
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.notifications.reply-email', $notification) }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">الموضوع</label>
                        <input type="text" name="subject" value="{{ old('subject', 'Re: '.$notification->title) }}" required maxlength="255"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                        @error('subject')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">نص الرد</label>
                        <textarea name="body" rows="6" required maxlength="5000"
                                  class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm" placeholder="اكتب ردك هنا...">{{ old('body') }}</textarea>
                        @error('body')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white">
                        <i class="fas fa-paper-plane"></i>
                        إرسال الرد
                    </button>
                </form>

                @if($notification->emailReplies && $notification->emailReplies->count())
                    <div class="px-6 pb-6">
                        <h4 class="text-sm font-bold text-slate-800 mb-3">سجل الردود</h4>
                        <div class="space-y-3">
                            @foreach($notification->emailReplies as $reply)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <span class="font-semibold text-slate-800">{{ $reply->subject }}</span>
                                        <span class="text-xs {{ $reply->status === 'sent' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $reply->status === 'sent' ? 'أُرسل' : 'فشل' }} — {{ $reply->created_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 whitespace-pre-wrap">{{ $reply->body }}</p>
                                    <p class="text-xs text-slate-400 mt-1">بواسطة {{ $reply->user->name ?? '—' }} → {{ $reply->to_email }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        المستقبل
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                            {{ mb_substr(htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8'), 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">{{ htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                            <div class="text-xs text-slate-600 mt-0.5">{{ htmlspecialchars($notification->user->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900">إحصائيات</h3>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-slate-600">تاريخ الإرسال</span><span class="font-semibold">{{ $notification->created_at->format('d/m/Y H:i') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-600">تاريخ القراءة</span><span class="font-semibold">{{ $notification->read_at ? $notification->read_at->format('d/m/Y H:i') : 'لم يُقرأ بعد' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-600">ID</span><span class="font-semibold">{{ $notification->id }}</span></div>
                </div>
            </div>

            <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-black text-slate-900">إجراءات</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.notifications.create') }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white">
                        <i class="fas fa-plus"></i>
                        إرسال إشعار جديد
                    </a>

                    @if($isInboxItem)
                        <form action="{{ route('admin.notifications.inbox.destroy', $notification) }}" method="POST" onsubmit="return confirm('حذف هذا الإشعار من الوارد؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white">
                                <i class="fas fa-trash"></i>
                                حذف من الوارد
                            </button>
                        </form>
                    @elseif((int) $notification->sender_id === (int) auth()->id())
                        <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white">
                                <i class="fas fa-trash"></i>
                                حذف الإشعار
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

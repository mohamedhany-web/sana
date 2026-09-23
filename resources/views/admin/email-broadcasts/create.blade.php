@extends('layouts.admin')

@section('title', 'إرسال بريد — '.$audienceLabel)
@section('header', __('إشعارات البريد (Gmail) — إرسال جديد'))

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.email-broadcasts.index', $audience) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-right ml-1"></i> رجوع
        </a>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">الفئة: {{ $audienceLabel }}</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.email-broadcasts.store', $audience) }}" class="space-y-4" x-data="{ mode: {{ json_encode(old('mode', 'audience')) }} }">
            @csrf
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                <p class="font-bold text-slate-900 mb-2">نوع الإرسال</p>
                <label class="inline-flex items-center gap-2 ml-4">
                    <input type="radio" name="mode" value="audience" x-model="mode">
                    <span>إرسال للجمهور ({{ $audienceLabel }})</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="mode" value="single_email" x-model="mode">
                    <span>إرسال لإيميل محدد</span>
                </label>
                @error('mode')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div x-show="mode === 'single_email'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الإيميل *</label>
                    <input type="email" name="single_email" value="{{ old('single_email') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="example@gmail.com">
                    @error('single_email')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم (اختياري)</label>
                    <input type="text" name="single_name" value="{{ old('single_name') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    @error('single_name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الرسالة (Subject) *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                @error('subject')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">نص الرسالة *</label>
                <textarea name="body" rows="10" required maxlength="20000"
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6">{{ old('body') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">سيتم إرسالها كبريد من Gmail حسب إعدادات `.env`.</p>
                @error('body')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900" x-show="mode === 'audience'" x-cloak>
                سيتم إرسال هذه الرسالة إلى <strong>{{ $audienceLabel }}</strong> (كل المستخدمين النشطين الذين لديهم بريد).
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900" x-show="mode === 'single_email'" x-cloak>
                سيتم إرسال هذه الرسالة إلى <strong>إيميل واحد</strong> فقط (المحدد أعلاه).
            </div>

            <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
                <i class="fas fa-paper-plane ml-2"></i> بدء الإرسال
            </button>
        </form>
    </div>
</div>
@endsection

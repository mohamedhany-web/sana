@extends('layouts.admin')
@section('title', 'إضافة فيديو دعائي')
@section('header', 'إضافة فيديو دعائي')
@section('content')
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">فيديو دعائي جديد</h1>
            <p class="text-slate-500 mt-1">الصق رابط YouTube فقط — سيُعرض في أسفل الصفحة الرئيسية.</p>
        </div>
        <form action="{{ route('admin.promotional-videos.store') }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الفيديو <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: تعرف على منصة سنا" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رابط YouTube <span class="text-rose-500">*</span></label>
                <input type="url" name="youtube_url" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=..." required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500" dir="ltr">
                <p class="mt-1 text-xs text-slate-500">يدعم روابط youtube.com و youtu.be و Shorts.</p>
                @error('youtube_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">وصف مختصر (اختياري)</label>
                <textarea name="description" rows="3" placeholder="وصف يظهر تحت الفيديو في الصفحة الرئيسية..."
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="9999"
                       class="w-full max-w-xs px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                <p class="mt-1 text-xs text-slate-500">الأرقام الأصغر تظهر أولاً.</p>
                @error('sort_order')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">تفعيل الفيديو</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold">حفظ الفيديو</button>
                <a href="{{ route('admin.promotional-videos.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'تعديل فيديو دعائي')
@section('header', 'تعديل فيديو دعائي')
@section('content')
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل: {{ $promotionalVideo->title }}</h1>
        </div>
        <form action="{{ route('admin.promotional-videos.update', $promotionalVideo) }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الفيديو <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $promotionalVideo->title) }}" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">رابط YouTube <span class="text-rose-500">*</span></label>
                <input type="url" name="youtube_url" value="{{ old('youtube_url', $promotionalVideo->youtube_url) }}" required dir="ltr"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                @error('youtube_url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">وصف مختصر (اختياري)</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">{{ old('description', $promotionalVideo->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $promotionalVideo->sort_order) }}" min="0" max="9999"
                       class="w-full max-w-xs px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                @error('sort_order')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $promotionalVideo->is_active) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">تفعيل الفيديو</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold">حفظ التعديلات</button>
                <a href="{{ route('admin.promotional-videos.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

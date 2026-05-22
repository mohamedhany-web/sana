@extends('layouts.admin')

@section('title', 'تعديل رأي - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'تعديل رأي')

@section('content')
@php
    $t = $siteTestimonial;
    $oldType = old('content_type', $t->content_type);
@endphp

<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full w-full max-w-none space-y-7" x-data="{ type: '{{ $oldType }}' }">

    <x-admin.page-hero
        title="تعديل رأي"
        subtitle="{{ $t->author_name ? 'صاحب الرأي: ' . $t->author_name : 'بدون اسم معروض' }} — {{ $t->is_active ? 'نشط' : 'معطل' }}"
        icon="fas fa-pen"
    >
        <a href="{{ route('admin.site-testimonials.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
        </a>
    </x-admin.page-hero>

    <div class="admin-panel w-full">
        <div class="admin-panel__head">
            <h2><i class="fas fa-edit"></i> بيانات الرأي</h2>
        </div>
        <div class="admin-panel__body">
            <form action="{{ route('admin.site-testimonials.update', $t) }}" method="POST" enctype="multipart/form-data" class="admin-form-layout space-y-6">
                @csrf
                @method('PUT')

                <div class="admin-field">
                    <label>نوع العرض <span class="text-rose-500">*</span></label>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <label class="admin-radio-card" :class="type === 'text' ? 'is-selected' : ''">
                            <input type="radio" name="content_type" value="text" x-model="type" class="sr-only">
                            <i class="fas fa-align-right text-lg mb-2" style="color: var(--admin-primary);"></i>
                            <span class="font-bold text-sm">نص</span>
                        </label>
                        <label class="admin-radio-card" :class="type === 'image' ? 'is-selected' : ''">
                            <input type="radio" name="content_type" value="image" x-model="type" class="sr-only">
                            <i class="fas fa-image text-lg mb-2 text-amber-600"></i>
                            <span class="font-bold text-sm">صورة</span>
                        </label>
                    </div>
                    @error('content_type')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="admin-form-layout__grid">
                    <div class="space-y-5">
                        <template x-if="type === 'text'">
                            <div class="admin-field">
                                <label>نص الرأي <span class="text-rose-500">*</span></label>
                                <textarea name="body" rows="8" class="admin-textarea admin-textarea--tall">{{ old('body', $t->body) }}</textarea>
                                @error('body')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </template>

                        <template x-if="type === 'image'">
                            <div class="space-y-5">
                                @if($t->publicImageUrl())
                                    <div class="admin-field">
                                        <label>الصورة الحالية</label>
                                        <div class="rounded-xl border border-slate-200 overflow-hidden max-w-md bg-slate-50">
                                            <img src="{{ $t->publicImageUrl() }}" alt="" class="w-full h-auto max-h-56 object-contain">
                                        </div>
                                    </div>
                                @endif
                                <div class="admin-field">
                                    <label>استبدال الصورة (اختياري)</label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                                    @error('image')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                                    @if($t->image_path)
                                        <input type="hidden" name="remove_image" value="0">
                                        <label class="admin-checkbox-row mt-3 text-rose-700">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-rose-600">
                                            <span>حذف الصورة الحالية (يلزم رفع صورة جديدة)</span>
                                        </label>
                                    @endif
                                </div>
                                <div class="admin-field">
                                    <label>وصف تحت الصورة (اختياري)</label>
                                    <textarea name="body" rows="3" class="admin-textarea">{{ old('body', $t->body) }}</textarea>
                                </div>
                            </div>
                        </template>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label>اسم صاحب الرأي</label>
                                <input type="text" name="author_name" value="{{ old('author_name', $t->author_name) }}" maxlength="190" class="admin-input">
                            </div>
                            <div class="admin-field">
                                <label>المسمى (اختياري)</label>
                                <input type="text" name="role_label" value="{{ old('role_label', $t->role_label) }}" maxlength="190" class="admin-input">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="admin-form-side-card">
                            <div class="admin-field">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $t->sort_order) }}" min="0" class="admin-input">
                            </div>
                            <div class="admin-field pt-1 space-y-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="hidden" name="is_featured" value="0">
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $t->is_active ? '1' : '0') === '1')>
                                    <span>نشط — يظهر في الموقع</span>
                                </label>
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $t->is_featured ? '1' : '0') === '1')>
                                    <span>بطاقة مميزة</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <a href="{{ route('admin.site-testimonials.index') }}" class="admin-btn admin-btn--outline">إلغاء</a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.admin-radio-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 8.5rem;
    padding: 1rem 1.25rem;
    border: 2px solid #e2e8f0;
    border-radius: 1rem;
    background: #fff;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    text-align: center;
}
.admin-radio-card:hover { border-color: #cbd5e1; }
.admin-radio-card.is-selected {
    border-color: var(--admin-primary, #1d4edb);
    background: rgba(29, 78, 219, 0.04);
    box-shadow: 0 0 0 3px rgba(29, 78, 219, 0.12);
}
</style>
@endpush
@endsection

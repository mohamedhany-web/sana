@extends('layouts.admin')

@section('title', 'إضافة رأي - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'إضافة رأي')

@section('content')
<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full w-full max-w-none space-y-7" x-data="{ type: '{{ old('content_type', 'text') }}' }">

    <x-admin.page-hero
        title="رأي جديد"
        subtitle="«نص» لاقتباس مكتوب مع اسم المعلّم، أو «صورة» لشهادة/لقطة شاشة. التخزين على R2 عند ضبط SITE_TESTIMONIALS_DISK."
        icon="fas fa-plus-circle"
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
            <form action="{{ route('admin.site-testimonials.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-layout space-y-6">
                @csrf

                <div class="admin-field">
                    <label>نوع العرض <span class="text-rose-500">*</span></label>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <label class="admin-radio-card" :class="type === 'text' ? 'is-selected' : ''">
                            <input type="radio" name="content_type" value="text" x-model="type" class="sr-only">
                            <i class="fas fa-align-right text-lg mb-2" style="color: var(--admin-primary);"></i>
                            <span class="font-bold text-sm">نص</span>
                            <span class="text-xs text-slate-500 mt-1">اقتباس + اسم</span>
                        </label>
                        <label class="admin-radio-card" :class="type === 'image' ? 'is-selected' : ''">
                            <input type="radio" name="content_type" value="image" x-model="type" class="sr-only">
                            <i class="fas fa-image text-lg mb-2 text-amber-600"></i>
                            <span class="font-bold text-sm">صورة</span>
                            <span class="text-xs text-slate-500 mt-1">شهادة / لقطة</span>
                        </label>
                    </div>
                    @error('content_type')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="admin-form-layout__grid">
                    <div class="space-y-5">
                        <template x-if="type === 'text'">
                            <div class="admin-field">
                                <label>نص الرأي <span class="text-rose-500">*</span></label>
                                <textarea name="body" rows="8" class="admin-textarea admin-textarea--tall" placeholder="«ما قاله المعلّم أو ولي الأمر...»">{{ old('body') }}</textarea>
                                @error('body')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </template>

                        <template x-if="type === 'image'">
                            <div class="space-y-5">
                                <div class="admin-field">
                                    <label>صورة الشهادة <span class="text-rose-500">*</span></label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                                    <p class="admin-field-hint">jpg, png, webp, gif — حتى 10 ميجابايت.</p>
                                    @error('image')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                                </div>
                                <div class="admin-field">
                                    <label>وصف قصير تحت الصورة (اختياري)</label>
                                    <textarea name="body" rows="3" class="admin-textarea">{{ old('body') }}</textarea>
                                </div>
                            </div>
                        </template>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label>اسم صاحب الرأي</label>
                                <input type="text" name="author_name" value="{{ old('author_name') }}" maxlength="190" class="admin-input" placeholder="مثال: أ. محمد أحمد">
                                @error('author_name')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field">
                                <label>المسمى (اختياري)</label>
                                <input type="text" name="role_label" value="{{ old('role_label') }}" maxlength="190" placeholder="معلّم لغة عربية" class="admin-input">
                                @error('role_label')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="admin-form-side-card">
                            <div class="admin-field">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="admin-input">
                                <p class="admin-field-hint">الأقل يظهر أولاً (بعد البطاقات المميزة).</p>
                            </div>
                            <div class="admin-field pt-1 space-y-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="hidden" name="is_featured" value="0">
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') !== '0')>
                                    <span>نشط — يظهر في الموقع</span>
                                </label>
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                                    <span>بطاقة مميزة (خلفية كحلية في الرئيسية)</span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-xs text-slate-600 leading-6">
                            <p class="font-bold text-slate-800 mb-2"><i class="fas fa-lightbulb text-amber-500 ml-1"></i> نصائح</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>الآراء النشطة فقط تظهر للزوار.</li>
                                <li>صفحة المعاينة: <a href="{{ route('public.testimonials') }}" target="_blank" rel="noopener" class="font-semibold" style="color: var(--admin-primary);">/testimonials</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="admin-form-actions">
                    <a href="{{ route('admin.site-testimonials.index') }}" class="admin-btn admin-btn--outline">إلغاء</a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ الرأي
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

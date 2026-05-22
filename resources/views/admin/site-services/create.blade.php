@extends('layouts.admin')

@section('title', 'إضافة خدمة - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'إضافة خدمة')

@section('content')
<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full w-full max-w-none space-y-7">

    <x-admin.page-hero
        title="خدمة جديدة"
        subtitle="يُنشأ الرابط تلقائياً من الاسم إن تركت حقل الرابط فارغاً (أحرف إنجليزية وشرطة)."
        icon="fas fa-plus-circle"
    >
        <a href="{{ route('admin.site-services.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
        </a>
    </x-admin.page-hero>

    <div class="admin-panel w-full">
        <div class="admin-panel__head">
            <h2><i class="fas fa-edit"></i> بيانات الخدمة</h2>
        </div>
        <div class="admin-panel__body">
            <form action="{{ route('admin.site-services.store') }}" method="POST" enctype="multipart/form-data" class="admin-form-layout space-y-6">
                @csrf

                <div class="admin-form-layout__grid">
                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>اسم الخدمة <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255" class="admin-input">
                            @error('name')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label>الرابط في المتصفح (اختياري)</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="teacher-training" class="admin-input admin-input--mono">
                            <p class="admin-field-hint">فقط a-z و 0-9 و شرطة. يُترك فارغاً للإنشاء التلقائي من الاسم.</p>
                            @error('slug')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label>مقدمة قصيرة (بطاقة القائمة)</label>
                            <textarea name="summary" rows="4" maxlength="2000" class="admin-textarea">{{ old('summary') }}</textarea>
                            @error('summary')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>صورة الخدمة (اختياري)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                            <p class="admin-field-hint">للبطاقة وصفحة الخدمة. عند ضبط <code>SITE_SERVICES_DISK=r2</code> في ملف البيئة تُرفع على R2.</p>
                            @error('image')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-form-side-card">
                            <div class="admin-field">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="admin-input">
                                @error('sort_order')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field pt-1">
                                <input type="hidden" name="is_active" value="0">
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', '1') !== '0')>
                                    <span>نشط ويظهر في الموقع</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-field">
                    <label>تفاصيل الخدمة <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="14" required class="admin-textarea admin-textarea--tall">{{ old('body') }}</textarea>
                    @error('body')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="admin-form-actions">
                    <a href="{{ route('admin.site-services.index') }}" class="admin-btn admin-btn--outline">إلغاء</a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ الخدمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

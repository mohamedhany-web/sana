@extends('layouts.admin')

@section('title', 'تعديل خدمة - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'تعديل خدمة')

@section('content')
<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full w-full max-w-none space-y-7">

    <x-admin.page-hero
        :title="'تعديل: ' . $siteService->name"
        subtitle="معاينة الصفحة العامة للخدمة على الموقع."
        icon="fas fa-pen"
    >
        <a href="{{ route('public.services.show', $siteService) }}" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost">
            <i class="fas fa-external-link-alt"></i>
            معاينة
        </a>
        <a href="{{ route('admin.site-services.index') }}" class="admin-btn admin-btn--ghost">
            <i class="fas fa-arrow-right"></i>
            القائمة
        </a>
    </x-admin.page-hero>

    <div class="admin-panel w-full">
        <div class="admin-panel__head">
            <h2><i class="fas fa-edit"></i> بيانات الخدمة</h2>
            <span class="text-xs font-mono text-slate-500">/services/{{ $siteService->slug }}</span>
        </div>
        <div class="admin-panel__body">
            <form action="{{ route('admin.site-services.update', $siteService) }}" method="POST" enctype="multipart/form-data" class="admin-form-layout space-y-6">
                @csrf
                @method('PUT')

                <div class="admin-form-layout__grid">
                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>اسم الخدمة <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $siteService->name) }}" required maxlength="255" class="admin-input">
                            @error('name')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label>الرابط في المتصفح (اختياري)</label>
                            <input type="text" name="slug" value="{{ old('slug', $siteService->slug) }}" class="admin-input admin-input--mono">
                            <p class="admin-field-hint">اتركه فارغاً لإعادة توليد الرابط من الاسم.</p>
                            @error('slug')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-field">
                            <label>مقدمة قصيرة</label>
                            <textarea name="summary" rows="4" maxlength="2000" class="admin-textarea">{{ old('summary', $siteService->summary) }}</textarea>
                            @error('summary')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="admin-field">
                            <label>صورة الخدمة</label>
                            @if($siteService->publicImageUrl())
                                <div class="admin-thumb admin-thumb--lg">
                                    <img src="{{ $siteService->publicImageUrl() }}" alt="">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                            <p class="admin-field-hint">اترك الحقل فارغاً للإبقاء على الصورة الحالية.</p>
                            @if($siteService->image_path)
                                <input type="hidden" name="remove_image" value="0">
                                <label class="admin-checkbox-row mt-3">
                                    <input type="checkbox" name="remove_image" value="1">
                                    <span>حذف الصورة الحالية</span>
                                </label>
                            @endif
                            @error('image')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                        </div>

                        <div class="admin-form-side-card">
                            <div class="admin-field">
                                <label>ترتيب العرض</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $siteService->sort_order) }}" min="0" class="admin-input">
                                @error('sort_order')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field pt-1">
                                <input type="hidden" name="is_active" value="0">
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $siteService->is_active ? '1' : '0') === '1')>
                                    <span>نشط ويظهر في الموقع</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-field">
                    <label>تفاصيل الخدمة <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="14" required class="admin-textarea admin-textarea--tall">{{ old('body', $siteService->body) }}</textarea>
                    @error('body')<p class="text-rose-600 text-xs mt-1.5 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="admin-form-actions">
                    <a href="{{ route('admin.site-services.index') }}" class="admin-btn admin-btn--outline">إلغاء</a>
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

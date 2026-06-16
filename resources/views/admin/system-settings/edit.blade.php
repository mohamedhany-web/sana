@extends('layouts.admin')

@section('title', 'إعدادات النظام - ' . ($platformName ?? config('brand.name', config('app.name'))))
@section('page_title', 'إعدادات النظام')

@section('content')
@php
    $brandName = config('brand.name', config('app.name'));
@endphp

<div class="admin-dashboard admin-list-page admin-form-page admin-form-page--full space-y-6 pb-10"
     x-data="{ tab: '{{ old('_tab', 'logo') }}' }">

    @include('admin.partials.alert-success')
    @include('admin.partials.alert-info')
    @include('admin.partials.alert-errors')

    <x-admin.page-hero
        title="إعدادات النظام"
        subtitle="اضبط الشعار، بيانات الفوتر، والمصادقة الثنائية من مكان واحد."
        icon="fas fa-sliders-h"
    >
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">
            <i class="fas fa-external-link-alt"></i>
            معاينة الموقع
        </a>
    </x-admin.page-hero>

    <form method="post" action="{{ route('admin.system-settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="admin-panel">
            <div class="admin-panel__head">
                <div>
                    <h2><i class="fas fa-cog"></i> الإعدادات العامة</h2>
                    <p class="admin-panel__sub">اختر التبويب ثم احفظ التغييرات من الأسفل.</p>
                </div>
            </div>
            <div class="admin-filter-tabs" role="tablist">
                <button type="button" role="tab" class="admin-filter-tab border-0 cursor-pointer" :class="{ 'is-active': tab === 'logo' }" @click="tab = 'logo'">الشعار</button>
                <button type="button" role="tab" class="admin-filter-tab border-0 cursor-pointer" :class="{ 'is-active': tab === 'footer' }" @click="tab = 'footer'">الفوتر</button>
            </div>

            <div class="admin-panel__body">

                {{-- تبويب الشعار --}}
                <div x-show="tab === 'logo'" x-cloak class="space-y-5">
                    <p class="text-sm text-slate-600 leading-7">
                        يظهر الشعار في صفحات الدخول والموقع العام. في لوحة الإدارة: أيقونة + اسم <strong>{{ $brandName }}</strong>.
                    </p>
                    <div class="sys-settings-note">
                        <strong>محلياً:</strong> <code>php artisan storage:link</code> —
                        <strong>الإنتاج (Cloudflare R2):</strong> <code>USE_CLOUDFLARE_R2=true</code> + مفاتيح <code>AWS_*</code> + <code>AWS_URL</code> (رابط Public من لوحة R2) ثم <code>php artisan config:clear</code> وأعد رفع الشعار مرة واحدة.
                    </div>
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="shrink-0">
                            @if($adminPanelLogoUrl)
                                <div class="sys-settings-logo-preview">
                                    <img src="{{ $adminPanelLogoUrl }}" alt="">
                                </div>
                            @else
                                <div class="sys-settings-logo-preview sys-settings-logo-preview--letter">
                                    {{ mb_substr($brandName, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 w-full space-y-4">
                            <div class="admin-field">
                                <label>رفع شعار جديد</label>
                                <input type="file" name="admin_panel_logo" accept="image/jpeg,image/png,image/webp,image/gif" class="admin-file-input">
                                <p class="admin-field-hint">JPG, PNG, WebP, GIF — حتى 2 ميغابايت. يُفضّل خلفية شفافة.</p>
                            </div>
                            @if($adminPanelLogoUrl)
                                <label class="admin-checkbox-row">
                                    <input type="checkbox" name="remove_admin_panel_logo" value="1" @checked(old('remove_admin_panel_logo'))>
                                    <span>حذف الشعار والعودة للحرف الافتراضي</span>
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- تبويب الفوتر --}}
                <div x-show="tab === 'footer'" x-cloak class="space-y-8">
                    <p class="text-sm text-slate-600 leading-7">
                        الحقول الفارغة عند الحفظ تُعيد القيمة الافتراضية لذلك الحقل فقط.
                    </p>

                    <section class="sys-settings-block">
                        <h3 class="sys-settings-block__title"><i class="fas fa-id-card"></i> الهوية والنص</h3>
                        <div class="space-y-4">
                            <div class="admin-field">
                                <label>السطر تحت اسم العلامة</label>
                                <input type="text" name="footer_brand_tagline" value="{{ old('footer_brand_tagline', $values['footer_brand_tagline']) }}" class="admin-input" placeholder="{{ $defaults['footer_brand_tagline'] }}">
                            </div>
                            <div class="admin-field">
                                <label>فقرة تعريفية قصيرة</label>
                                <textarea name="footer_blurb" rows="3" class="admin-textarea" placeholder="{{ $defaults['footer_blurb'] }}">{{ old('footer_blurb', $values['footer_blurb']) }}</textarea>
                            </div>
                            <div class="admin-field">
                                <label>سطر حقوق النشر أسفل الفوتر</label>
                                <input type="text" name="footer_bottom_tagline" value="{{ old('footer_bottom_tagline', $values['footer_bottom_tagline']) }}" class="admin-input" placeholder="{{ $defaults['footer_bottom_tagline'] }}">
                            </div>
                        </div>
                    </section>

                    <section class="sys-settings-block">
                        <h3 class="sys-settings-block__title"><i class="fas fa-phone"></i> التواصل</h3>
                        <p class="admin-field-hint mb-3">البريد والهاتف وواتساب والعنوان وأوقات الدعم تظهر في الفوتر، صفحة «تواصل معنا»، أزرار الموقع، واستفسارات الدورات. إذا تركت حقل واتساب فارغاً ووضعت رقم هاتف سعودي (+966…) يُفعَّل زر الواتساب تلقائياً بنفس الرقم في كل الموقع.</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label>البريد الإلكتروني</label>
                                <input type="email" name="footer_email" value="{{ old('footer_email', $values['footer_email']) }}" class="admin-input" dir="ltr" placeholder="{{ $defaults['footer_email'] }}">
                            </div>
                            <div class="admin-field">
                                <label>رقم الهاتف</label>
                                <input type="text" name="footer_phone" value="{{ old('footer_phone', $values['footer_phone']) }}" class="admin-input" dir="ltr" placeholder="+9665XXXXXXXX">
                            </div>
                            <div class="admin-field sm:col-span-2">
                                <label>واتساب (رقم أو رابط)</label>
                                <input type="text" name="footer_whatsapp_url" value="{{ old('footer_whatsapp_url', $values['footer_whatsapp_url']) }}" class="admin-input" dir="ltr" placeholder="+9665XXXXXXXX أو https://wa.me/9665XXXXXXXX">
                                @php
                                    $previewWa = \App\Support\PublicContactInfo::normalizeWhatsappInput($values['footer_whatsapp_url'] ?? '');
                                    if ($previewWa === '' && ($values['footer_phone'] ?? '') !== '') {
                                        $previewWa = \App\Support\PublicContactInfo::normalizeWhatsappInput($values['footer_phone']);
                                    }
                                @endphp
                                @if($previewWa !== '')
                                    <p class="admin-field-hint mt-1">رابط واتساب المعروض في الموقع: <a href="{{ $previewWa }}" target="_blank" rel="noopener" dir="ltr">{{ $previewWa }}</a></p>
                                @else
                                    <p class="admin-field-hint mt-1">لتفعيل واتساب: أضف رقم +966 في «الهاتف» أو «واتساب».</p>
                                @endif
                            </div>
                            <div class="admin-field sm:col-span-2">
                                <label>العنوان</label>
                                <input type="text" name="footer_address" value="{{ old('footer_address', $values['footer_address']) }}" class="admin-input" placeholder="{{ $defaults['footer_address'] }}">
                            </div>
                            <div class="admin-field sm:col-span-2">
                                <label>أوقات الدعم (نص يظهر للزوار)</label>
                                <input type="text" name="footer_support_hours" value="{{ old('footer_support_hours', $values['footer_support_hours']) }}" class="admin-input" placeholder="{{ $defaults['footer_support_hours'] }}">
                            </div>
                        </div>
                    </section>

                    <section class="sys-settings-block">
                        <h3 class="sys-settings-block__title"><i class="fas fa-share-alt"></i> وسائل التواصل</h3>
                        <p class="admin-field-hint mb-3">تظهر الأيقونة فقط عند ملء الرابط.</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach([
                                'social_facebook_url' => 'Facebook',
                                'social_x_url' => 'X',
                                'social_instagram_url' => 'Instagram',
                                'social_youtube_url' => 'YouTube',
                                'social_linkedin_url' => 'LinkedIn',
                                'social_tiktok_url' => 'TikTok',
                                'social_telegram_url' => 'Telegram',
                                'social_snapchat_url' => 'Snapchat',
                            ] as $field => $label)
                                <div class="admin-field">
                                    <label>{{ $label }}</label>
                                    <input type="url" name="{{ $field }}" value="{{ old($field, $values[$field]) }}" class="admin-input" dir="ltr" placeholder="https://">
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

            </div>

            <div class="admin-panel__body border-t border-slate-100 bg-slate-50/50">
                <div class="admin-form-actions !border-0 !mt-0 !pt-0">
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">
                        <i class="fas fa-external-link-alt"></i>
                        معاينة الموقع
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- الأمان (نماذج منفصلة — لا داخل form الحفظ) --}}
    <div class="admin-panel" id="security-2fa">
        <div class="admin-panel__head">
            <div>
                <h2><i class="fas fa-shield-alt"></i> المصادقة الثنائية</h2>
                <p class="admin-panel__sub">للمدير العام والأدمن فقط — رمز بريد بعد كلمة المرور.</p>
            </div>
            @if($adminTwoFactorRequired)
                <span class="admin-badge admin-badge--success"><i class="fas fa-check"></i> مفعّل</span>
            @else
                <span class="admin-badge admin-badge--warn">غير مفعّل</span>
            @endif
        </div>
        <div class="admin-panel__body space-y-4">
            @if($errors->has('two_factor'))
                <div class="admin-alert" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">
                    <span class="admin-alert__icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-exclamation-circle"></i></span>
                    <p class="font-semibold">{{ $errors->first('two_factor') }}</p>
                </div>
            @endif

            <div class="sys-settings-note sys-settings-note--warn">
                تأكد من عمل البريد قبل التفعيل. يمكن ضبط <code dir="ltr">ADMIN_2FA_REQUIRED</code> في ملف البيئة.
            </div>

            @if(!$admin2faAppliesToCurrentUserRole)
                <div class="sys-settings-note">
                    دورك (<strong>{{ auth()->user()->role }}</strong>) لن يُطلب منه رمز 2FA عند الدخول.
                </div>
            @endif

            @if(!$adminTwoFactorRequired)
                <p class="text-sm text-slate-600">أرسل رمزاً إلى بريدك ثم أكّد التفعيل في الصفحة التالية.</p>
                <form method="post" action="{{ route('admin.system-settings.two-factor.enable-request') }}">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn--primary">
                        <i class="fas fa-paper-plane"></i>
                        تفعيل وإرسال الرمز
                    </button>
                </form>
            @else
                <p class="text-sm text-slate-600">لتعطيل الإلزام أدخل كلمة مرورك.</p>
                <form method="post" action="{{ route('admin.system-settings.two-factor.disable') }}" class="max-w-sm space-y-3">
                    @csrf
                    <div class="admin-field">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" required autocomplete="current-password" class="admin-input">
                        @error('password')<p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="admin-btn admin-btn--outline" style="color:#b91c1c;border-color:#fecaca;">
                        <i class="fas fa-power-off"></i>
                        تعطيل المصادقة الثنائية
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }
.sys-settings-block__title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eef2f7;
}
.sys-settings-block__title i { color: #94a3b8; width: 1.1rem; text-align: center; }
.sys-settings-note {
    font-size: 0.8125rem;
    line-height: 1.55;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}
.sys-settings-note code {
    font-size: 0.7rem;
    background: #fff;
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    font-family: ui-monospace, monospace;
}
.sys-settings-note--warn {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}
.sys-settings-logo-preview {
    width: 7rem;
    height: 7rem;
    border-radius: 14px;
    border: 2px dashed #e2e8f0;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
}
.sys-settings-logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
.sys-settings-logo-preview--letter {
    font-size: 2rem;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, var(--admin-primary, #1d4edb), #1e3a8a);
    border-style: solid;
    border-color: transparent;
}
</style>
@endpush
@endsection

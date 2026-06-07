@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $bc = config('brand.colors');
    $brand = config('brand.name', config('app.name', 'Sana'));
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $itemTitle = isset($course) ? ($course->title ?? 'الكورس') : (isset($learningPath) ? ($learningPath->name ?? 'الطلب') : 'الطلب');
    $thumbUrl = null;
    if (isset($course) && ($course->thumbnail ?? null)) {
        $thumbUrl = public_storage_url($course->thumbnail);
    }
    $platformLogoUrl = $platformLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $appName = $brand;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ __('public.checkout_page_label') }} - {{ $itemTitle }} - {{ $appName }}</title>
    <meta name="theme-color" content="{{ $bc['blue'] }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @include('landing.eduvalt.theme')
    @include('landing.eduvalt.courses-page')
    @include('landing.eduvalt.checkout-page')
    @include('partials.rtl-base')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white" x-data="{ isSubmitting: false }">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>
@include('landing.eduvalt.navbar')

<main class="pt-[76px] lg:pt-[84px]">
{{-- هيرو — عرض كامل --}}
<section class="edu-checkout-hero py-8 lg:py-12">
    <div class="edu-checkout-hero__blob edu-checkout-hero__blob--1" aria-hidden="true"></div>
    <div class="edu-checkout-hero__blob edu-checkout-hero__blob--2" aria-hidden="true"></div>
    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <nav class="edu-breadcrumb mb-6 reveal" aria-label="مسار التنقل">
                <a href="{{ route('home') }}">{{ $tr('nav.home') }}</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <a href="{{ route('public.courses') }}">{{ __('public.courses') }}</a>
                @if(isset($course))
                    <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                    <a href="{{ route('public.course.show', $course->id) }}">{{ Str::limit($course->title ?? '', 36) }}</a>
                @endif
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 font-semibold">{{ __('public.checkout_breadcrumb_current') }}</span>
            </nav>

            <div class="edu-checkout-steps reveal mb-8 lg:mb-10" aria-label="{{ __('public.checkout_steps_label') }}">
                <div class="flex items-center gap-3 shrink-0">
                    <span class="edu-checkout-step-num is-done">1</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-emerald-700">{{ __('public.checkout_step_1_title') }}</p>
                        <p class="text-[11px] text-slate-500">{{ __('public.checkout_step_1_desc') }}</p>
                    </div>
                </div>
                <span class="edu-checkout-step-line" aria-hidden="true"></span>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="edu-checkout-step-num is-active">2</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-[var(--edu-primary)]">{{ __('public.checkout_step_2_title') }}</p>
                        <p class="text-[11px] text-slate-500">{{ __('public.checkout_step_2_desc') }}</p>
                    </div>
                </div>
                <span class="edu-checkout-step-line" aria-hidden="true"></span>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="edu-checkout-step-num is-pending">3</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-600">{{ __('public.checkout_step_3_title') }}</p>
                        <p class="text-[11px] text-slate-500">{{ __('public.checkout_step_3_desc') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <div class="reveal">
                    <span class="edu-badge mb-4"><i class="fas fa-credit-card"></i> {{ isset($course) ? __('public.secure_checkout_badge') : __('public.checkout_page_label') }}</span>
                    <h1 class="edu-section-title text-slate-900 mb-4">{{ __('public.checkout_page_label') }}</h1>
                    <p class="text-slate-600 leading-8 mb-6 max-w-xl">
                        {{ __('public.checkout_payment_section_desc') }}
                        <span class="font-bold text-[var(--edu-primary)]">{{ $itemTitle }}</span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span class="edu-checkout-chip"><i class="fas fa-shield-halved text-[var(--edu-primary)]"></i> دفع موثوق</span>
                        <span class="edu-checkout-chip"><i class="fas fa-bolt text-[var(--edu-accent-dark)]"></i> تفعيل بعد الموافقة</span>
                    </div>
                </div>
                <div class="reveal">
                    @if($thumbUrl)
                        <div class="rounded-[var(--edu-radius)] overflow-hidden border border-slate-200 shadow-lg aspect-[4/3] bg-slate-100">
                            <img src="{{ $thumbUrl }}" alt="{{ $itemTitle }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="edu-card p-8 min-h-[200px] flex flex-col justify-center" style="background:linear-gradient(145deg,var(--edu-primary-light),#fff)">
                            <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white mb-4" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))"><i class="fas fa-graduation-cap"></i></span>
                            <p class="font-bold text-xl text-slate-900 leading-snug">{{ Str::limit($itemTitle, 80) }}</p>
                            @if(isset($course) && $course->academicSubject)
                                <p class="text-sm text-slate-500 mt-2">{{ $course->academicSubject->name }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="edu-checkout-body py-10 lg:py-14">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
                    {{-- ملخص الطلب --}}
                    <aside class="lg:col-span-4 order-2 lg:order-1">
                        <div class="reveal edu-checkout-card sticky top-24 p-6 sm:p-8 lg:top-[100px]">
                            <div class="edu-checkout-summary-head">
                                <p class="text-xs font-bold opacity-90 mb-0.5">{{ $appName }}</p>
                                <p class="text-lg font-extrabold flex items-center gap-2">
                                    <i class="fas fa-receipt"></i>
                                    {{ __('public.checkout_order_summary_title') }}
                                </p>
                            </div>
                            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-100">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 text-white text-xl shadow-md" style="background:linear-gradient(135deg,var(--edu-primary),var(--edu-purple))">
                                    @if(isset($course))
                                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                                    @else
                                        <i class="fas fa-route text-white text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-slate-900 text-base line-clamp-2 leading-snug">
                                        @if(isset($course)){{ $course->title }}@elseif(isset($learningPath)){{ $learningPath->name }}@else الطلب @endif
                                    </h4>
                                    <p class="text-sm text-slate-500 mt-1">
                                        @if(isset($course)){{ $course->academicSubject->name ?? 'غير محدد' }}@else مسار تعليمي شامل @endif
                                    </p>
                                </div>
                            </div>
                            @php
                                $baseCoursePrice = isset($course) ? (float) $course->effectivePurchasePrice() : (float) (isset($learningPath) ? ($learningPath->price ?? 0) : 0);
                                $studentBal = isset($studentWalletBalance) ? (float) $studentWalletBalance : 0;
                            @endphp
                            <div class="space-y-3 mb-6" id="checkout-pricing-summary"
                                 data-base-price="{{ $baseCoursePrice }}"
                                 data-student-balance="{{ $studentBal }}"
                                 data-has-course="{{ isset($course) ? '1' : '0' }}">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600">السعر الأساسي</span>
                                    <span class="font-bold text-[var(--edu-primary)] text-lg" id="sum-original">
                                        {{ number_format($baseCoursePrice, 2) }}
                                        <span class="text-slate-500 text-sm font-medium">{{ __('public.currency') }}</span>
                                    </span>
                                </div>
                                <div class="hidden flex justify-between items-center text-sm text-emerald-700" id="sum-coupon-row">
                                    <span>خصم الكوبون</span>
                                    <span class="font-bold" id="sum-coupon">—</span>
                                </div>
                                <div class="hidden flex justify-between items-center text-sm text-sky-700" id="sum-wallet-row">
                                    <span>رصيد المحفظة</span>
                                    <span class="font-bold" id="sum-wallet">—</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t-2 border-slate-100">
                                    <span class="font-bold text-slate-900">المستحق للدفع</span>
                                    <span class="text-2xl font-black text-[var(--edu-primary)]" id="sum-final">
                                        {{ number_format($baseCoursePrice, 2) }}
                                        <span class="text-slate-500 text-base font-medium">{{ __('public.currency') }}</span>
                                    </span>
                                </div>
                            </div>
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> وصول مدى الحياة</li>
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> شهادة إتمام</li>
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> دعم فني</li>
                            </ul>
                        </div>
                    </aside>

                    {{-- طرق الدفع --}}
                    <div class="lg:col-span-8 order-1 lg:order-2">
                        <div class="reveal edu-checkout-card p-6 sm:p-8 lg:p-10">
                            <div class="flex items-start gap-4 mb-8 pb-6 border-b border-slate-100">
                                <span class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0" style="background:var(--edu-primary-light);color:var(--edu-primary)"><i class="fas fa-wallet"></i></span>
                                <div>
                                    <span class="edu-sub-title">{{ __('public.checkout_page_label') }}</span>
                                    <h2 class="edu-section-title text-slate-900 text-2xl">{{ __('public.checkout_payment_section_title') }}</h2>
                                    <p class="text-slate-500 text-sm mt-2 max-w-xl leading-relaxed">{{ __('public.checkout_payment_section_desc') }}</p>
                                </div>
                            </div>

                            @if(session('error'))
                                <div class="edu-checkout-alert edu-checkout-alert--error mb-6">
                                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                                    <p class="flex-1">{{ session('error') }}</p>
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="edu-checkout-alert edu-checkout-alert--error mb-6">
                                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                                    <ul class="list-disc list-inside space-y-1 flex-1">
                                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="edu-checkout-alert edu-checkout-alert--success mb-6">
                                    <i class="fas fa-check-circle"></i>
                                    <p>{{ session('success') }}</p>
                                </div>
                            @endif
                            @if(session('info'))
                                <div class="edu-checkout-alert edu-checkout-alert--info mb-6">
                                    <i class="fas fa-info-circle"></i>
                                    <p>{{ session('info') }}</p>
                                </div>
                            @endif

                            @if(isset($course))
                                @php
                                    $checkoutHasWalletBalance = isset($studentWalletBalance) && (float) $studentWalletBalance > 0;
                                @endphp
                                <div class="mb-8 edu-card p-5 sm:p-6 space-y-4"
                                     id="checkout-discount-panel"
                                     data-quote-url="{{ route('public.course.checkout.quote', $course->id) }}"
                                     data-has-wallet="{{ $checkoutHasWalletBalance ? '1' : '0' }}">
                                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-tags text-[var(--edu-primary)]"></i>
                                        @if($checkoutHasWalletBalance)
                                            كوبون الخصم ورصيد المحفظة
                                        @else
                                            كوبون الخصم
                                        @endif
                                    </h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        @if($checkoutHasWalletBalance)
                                            أضف كوبوناً صالحاً (من التسويق) و/أو استخدم رصيد محفظتك على المنصة. يُخصم الكوبون أولاً ثم رصيد المحفظة من المبلغ المتبقي.
                                        @else
                                            أدخل كوبوناً صالحاً من التسويق إن وُجد، ثم اضغط «تحديث السعر».
                                        @endif
                                    </p>
                                    @if($checkoutHasWalletBalance)
                                        <p class="text-xs font-semibold text-sky-700">رصيد محفظتك الحالي: {{ number_format($studentWalletBalance, 2) }} {{ __('public.currency') }}</p>
                                    @endif
                                    <div class="grid grid-cols-1 {{ $checkoutHasWalletBalance ? 'sm:grid-cols-2' : '' }} gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">كود الكوبون</label>
                                            <input type="text" id="checkout_coupon_code" dir="ltr" autocomplete="off"
                                                   class="edu-checkout-input uppercase font-mono text-sm"
                                                   placeholder="مثال: SAVE10">
                                        </div>
                                        @if($checkoutHasWalletBalance)
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1">مبلغ من المحفظة ({{ __('public.currency') }})</label>
                                                <input type="number" id="checkout_wallet_credit" step="0.01" min="0"
                                                       value="0"
                                                       max="{{ max(0, $studentWalletBalance ?? 0) }}"
                                                       class="edu-checkout-input">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <button type="button" id="checkout_apply_pricing"
                                                class="edu-btn-primary text-sm !py-2.5 !px-5">
                                            <i class="fas fa-rotate"></i> تحديث السعر
                                        </button>
                                        <span id="checkout_pricing_msg" class="text-sm font-medium text-slate-600 hidden"></span>
                                    </div>
                                </div>
                            @endif

                            <div class="edu-checkout-alert edu-checkout-alert--info mb-6">
                                    <i class="fas fa-circle-info mt-0.5"></i>
                                    <div>
                                    <p class="font-bold mb-1">الدفع اليدوي</p>
                                    <p class="font-normal leading-relaxed">ارفع إيصال التحويل وسيظهر الطلب في صفحة الطلبات حتى تتم مراجعته والموافقة عليه.</p>
                                    </div>
                                </div>

                                <form action="{{ isset($course) ? route('public.course.checkout.complete', $course->id) : (isset($learningPath) ? route('public.learning-path.checkout.complete', Str::slug($learningPath->name)) : '#') }}" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" x-data="{paymentMethod:'bank_transfer'}" id="manual-checkout-form">
                                    @csrf
                                    @if(isset($course))
                                        <input type="hidden" name="coupon_code" id="form_coupon_code" value="{{ old('coupon_code', '') }}">
                                        <input type="hidden" name="wallet_credit" id="form_wallet_credit" value="{{ old('wallet_credit', '0') }}">
                                    @endif
                                    <div class="space-y-5 mb-8">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">طريقة الدفع</label>
                                            <select name="payment_method" x-model="paymentMethod" class="edu-checkout-input" required>
                                                <option value="bank_transfer">تحويل بنكي / محفظة</option>
                                                <option value="cash">دفع نقدي</option>
                                                <option value="other">طريقة أخرى</option>
                                            </select>
                                        </div>

                                        <div x-show="paymentMethod === 'bank_transfer'" x-cloak>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">اختر حساب التحويل</label>
                                            <select name="wallet_id" class="edu-checkout-input" :required="paymentMethod === 'bank_transfer'">
                                                <option value="">اختر الحساب</option>
                                                @foreach(($wallets ?? []) as $wallet)
                                                    <option value="{{ $wallet->id }}">
                                                        {{ $wallet->name ?? 'حساب منصة' }} — {{ $wallet->account_number ?? $wallet->phone ?? 'بدون رقم' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">إيصال الدفع</label>
                                            <input type="file" name="payment_proof" accept="image/*" required
                                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 file:me-3 file:rounded-lg file:border-0 file:bg-[var(--edu-primary)] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white transition-colors">
                                            <p class="mt-2 text-xs text-slate-500">الصيغ المسموحة: JPG, PNG — حتى 40 ميجابايت.</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                                            <textarea name="notes" rows="3" class="edu-checkout-input resize-y min-h-[100px]" placeholder="أي تفاصيل إضافية عن التحويل"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button type="submit" :disabled="isSubmitting"
                                                class="edu-btn-primary flex-1 disabled:opacity-60 disabled:cursor-not-allowed">
                                            <i class="fas fa-file-upload" x-show="!isSubmitting"></i>
                                            <i class="fas fa-spinner fa-spin" x-show="isSubmitting" x-cloak></i>
                                            <span x-text="isSubmitting ? 'جاري إرسال الطلب...' : 'إرسال الطلب ورفع الإيصال'"></span>
                                        </button>
                                        <a href="{{ route('orders.index') }}"
                                           :class="{ 'pointer-events-none opacity-50': isSubmitting }"
                                           class="edu-btn-outline flex-1 justify-center">
                                            <i class="fas fa-arrow-right"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <p class="mt-6 text-xs text-slate-500 text-center flex items-center justify-center gap-2">
                                        <i class="fas fa-shield-halved text-[var(--edu-primary)]"></i>
                                        يظهر الطلب في صفحة الطلبات ويتم التفعيل بعد الموافقة
                                    </p>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

@include('landing.eduvalt.footer')

<script>
(function () {
    var nav = document.getElementById('edu-nav');
    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('is-scrolled', y > 20);
        var bar = document.getElementById('scroll-progress');
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
        });
    }, { threshold: 0.06, rootMargin: '0px 0px -32px 0px' });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
    var pre = document.getElementById('edu-preloader');
    if (pre) window.addEventListener('load', function () { pre.style.opacity = '0'; setTimeout(function () { pre.remove(); }, 400); });
})();
</script>
    @if(isset($course))
    <script>
    (function(){
        var panel = document.getElementById('checkout-discount-panel');
        var summary = document.getElementById('checkout-pricing-summary');
        if (!panel || !summary) return;
        var quoteUrl = panel.getAttribute('data-quote-url');
        var meta = document.querySelector('meta[name="csrf-token"]');
        var csrf = (meta && meta.getAttribute('content')) || '';
        function el(id){ return document.getElementById(id); }
        function fmt(n){
            var x = parseFloat(n);
            if (isNaN(x)) x = 0;
            return x.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function setMsg(text, isErr) {
            var m = el('checkout_pricing_msg');
            if (!m) return;
            m.textContent = text || '';
            m.classList.toggle('hidden', !text);
            m.classList.toggle('text-red-600', !!isErr);
            m.classList.toggle('text-emerald-700', !isErr && !!text);
        }
        function updateSummary(data) {
            var base = parseFloat(summary.getAttribute('data-base-price')) || 0;
            if (!data || !data.ok) {
                el('sum-original').innerHTML = fmt(base) + ' <span class="text-slate-500 text-sm font-medium">{{ __('public.currency') }}</span>';
                el('sum-final').innerHTML = fmt(base) + ' <span class="text-slate-500 text-base font-medium">{{ __('public.currency') }}</span>';
                el('sum-coupon-row').classList.add('hidden');
                el('sum-wallet-row').classList.add('hidden');
                return;
            }
            el('sum-original').innerHTML = fmt(data.original_amount) + ' <span class="text-slate-500 text-sm font-medium">{{ __('public.currency') }}</span>';
            if (data.discount_amount > 0) {
                el('sum-coupon-row').classList.remove('hidden');
                el('sum-coupon').textContent = '− ' + fmt(data.discount_amount) + ' {{ __('public.currency') }}';
            } else {
                el('sum-coupon-row').classList.add('hidden');
            }
            if (data.wallet_credit_amount > 0) {
                el('sum-wallet-row').classList.remove('hidden');
                el('sum-wallet').textContent = '− ' + fmt(data.wallet_credit_amount) + ' {{ __('public.currency') }}';
            } else {
                el('sum-wallet-row').classList.add('hidden');
            }
            el('sum-final').innerHTML = fmt(data.final_amount) + ' <span class="text-slate-500 text-base font-medium">{{ __('public.currency') }}</span>';
        }
        function quote() {
            setMsg('', false);
            var fd = new FormData();
            fd.append('_token', csrf);
            var c = el('checkout_coupon_code');
            var w = el('checkout_wallet_credit');
            fd.append('coupon_code', c ? (c.value || '').trim() : '');
            fd.append('wallet_credit', w && w.value !== '' ? w.value : '0');
            return fetch(quoteUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            }).then(function(r){
                return r.json().then(function(j){ return { ok: r.ok, status: r.status, data: j }; });
            }).then(function(res){
                if (res.ok && res.data && res.data.ok) {
                    updateSummary(res.data);
                    setMsg('تم تحديث السعر.', false);
                    var hfC = el('form_coupon_code');
                    var hfW = el('form_wallet_credit');
                    if (hfC) hfC.value = (el('checkout_coupon_code').value || '').trim();
                    if (hfW) {
                        var wIn = el('checkout_wallet_credit');
                        hfW.value = wIn && wIn.value !== '' ? wIn.value : '0';
                    }
                    return res.data;
                }
                var msg = (res.data && res.data.message) ? res.data.message : 'تعذّر حساب السعر.';
                setMsg(msg, true);
                updateSummary(null);
                return null;
            }).catch(function(){
                setMsg('خطأ في الاتصال.', true);
                updateSummary(null);
                return null;
            });
        }
        var btn = el('checkout_apply_pricing');
        if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); quote(); });
        var form = el('manual-checkout-form');
        if (form) {
            form.addEventListener('submit', function(){
                var c = el('checkout_coupon_code');
                var w = el('checkout_wallet_credit');
                if (el('form_coupon_code')) el('form_coupon_code').value = c ? (c.value || '').trim() : '';
                if (el('form_wallet_credit')) el('form_wallet_credit').value = w && w.value !== '' ? w.value : '0';
            });
        }
        var oc = el('checkout_coupon_code');
        var ow = el('checkout_wallet_credit');
        if (el('form_coupon_code') && el('form_coupon_code').value && oc) oc.value = el('form_coupon_code').value;
        if (ow && el('form_wallet_credit') && el('form_wallet_credit').value) ow.value = el('form_wallet_credit').value;
        quote();
    })();
    </script>
    @endif
</body>
</html>

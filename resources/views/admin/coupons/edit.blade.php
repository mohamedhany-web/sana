@extends('layouts.admin')

@section('title', 'تعديل الكوبون')
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.coupons.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-edit text-amber-500 ml-2"></i>تعديل الكوبون
            </h1>
            <p class="text-sm text-slate-500 mt-1 font-mono">{{ $coupon->code }}</p>
        </div>
    </div>

    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الكود <span class="text-red-500">*</span></label>
                <input type="text" name="code" required value="{{ old('code', $coupon->code) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-mono">
                @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">العنوان <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="{{ old('title', $coupon->title ?? $coupon->name) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">نوع الخصم <span class="text-red-500">*</span></label>
                <select name="discount_type" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="percentage" {{ old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                </select>
                @error('discount_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">قيمة الخصم <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" step="0.01" min="0" required value="{{ old('discount_value', $coupon->discount_value) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('discount_value')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأدنى للطلب ({{ __('public.currency') }})</label>
                <input type="number" name="minimum_amount" step="0.01" min="0" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('minimum_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى للخصم ({{ __('public.currency') }})</label>
                <input type="number" name="maximum_discount" step="0.01" min="0" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('maximum_discount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى لعدد الاستخدامات</label>
                <input type="number" name="max_uses" min="1" value="{{ old('max_uses', $coupon->usage_limit) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="فارغ = غير محدود">
                @error('max_uses')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد لكل مستخدم</label>
                <input type="number" name="usage_limit_per_user" min="1" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user ?? 1) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('usage_limit_per_user')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">تاريخ البداية</label>
                <input type="date" name="valid_from" value="{{ old('valid_from', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('valid_from')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">تاريخ الانتهاء</label>
                <input type="date" name="valid_until" value="{{ old('valid_until', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                @error('valid_until')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الوصف</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">{{ old('description', $coupon->description) }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @php
            $oldCourseIds = old('applicable_course_ids', $coupon->applicable_course_ids ?? []);
            $oldUserIdsText = old('applicable_user_ids_text', is_array($coupon->applicable_user_ids) && count($coupon->applicable_user_ids) ? implode(', ', $coupon->applicable_user_ids) : '');
        @endphp

        <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-5 space-y-4 bg-slate-50/80 dark:bg-slate-900/30">
            <h2 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2"><i class="fas fa-bullseye text-violet-500"></i> نطاق التطبيق (الكورسات)</h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">ينطبق على <span class="text-red-500">*</span></label>
                <select name="applicable_to" required class="w-full max-w-xl rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="all" {{ old('applicable_to', $coupon->applicable_to ?? 'all') === 'all' ? 'selected' : '' }}>جميع الكورسات (صفحة دفع الكورس)</option>
                    <option value="courses" {{ old('applicable_to', $coupon->applicable_to ?? 'all') === 'courses' ? 'selected' : '' }}>كورسات محددة فقط</option>
                    <option value="specific" {{ old('applicable_to', $coupon->applicable_to ?? 'all') === 'specific' ? 'selected' : '' }}>كورسات محددة + تقييد مستخدمين (اختياري بالأسفل)</option>
                    <option value="subscriptions" {{ old('applicable_to', $coupon->applicable_to ?? 'all') === 'subscriptions' ? 'selected' : '' }}>الاشتراكات فقط (لا يُطبَّق على دفع الكورس)</option>
                </select>
                @error('applicable_to')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">الكورسات (عند اختيار «محددة»)</label>
                <div class="max-h-56 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-600 p-3 space-y-2 bg-white dark:bg-slate-800">
                    @forelse($courses ?? [] as $c)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="applicable_course_ids[]" value="{{ $c->id }}" {{ in_array($c->id, array_map('intval', (array) $oldCourseIds), true) ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-slate-800 dark:text-slate-200">{{ $c->title }}</span>
                    </label>
                    @empty
                    <p class="text-sm text-slate-500">لا توجد كورسات في النظام.</p>
                    @endforelse
                </div>
                @error('applicable_course_ids')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-xl border border-amber-200 dark:border-amber-900/50 p-5 space-y-4 bg-amber-50/50 dark:bg-amber-950/20">
            <h2 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2"><i class="fas fa-user-tag text-amber-600"></i> كوبون تسويقي شخصي + عمولة</h2>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">معرّفات المستخدمين المسموح لهم (اختياري)</label>
                <textarea name="applicable_user_ids_text" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono text-sm" placeholder="مثال: 12, 45">{{ $oldUserIdsText }}</textarea>
                @error('applicable_user_ids_text')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">مستفيد العمولة (معرّف مستخدم)</label>
                    <input type="number" name="beneficiary_user_id" min="1" value="{{ old('beneficiary_user_id', $coupon->beneficiary_user_id) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono" placeholder="فارغ = بدون عمولة">
                    @error('beneficiary_user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">نسبة العمولة %</label>
                    <input type="number" name="commission_percent" step="0.01" min="0" max="100" value="{{ old('commission_percent', $coupon->commission_percent) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    @error('commission_percent')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">احتساب العمولة من</label>
                    <select name="commission_on" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="final_paid" {{ old('commission_on', $coupon->commission_on ?? 'final_paid') === 'final_paid' ? 'selected' : '' }}>المبلغ النهائي بعد الخصم</option>
                        <option value="original_price" {{ old('commission_on', $coupon->commission_on ?? 'final_paid') === 'original_price' ? 'selected' : '' }}>السعر الأصلي قبل الخصم</option>
                    </select>
                    @error('commission_on')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">كوبون نشط</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $coupon->is_public ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">ظاهر للجميع</span>
            </label>
        </div>

        <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
            <a href="{{ route('admin.coupons.show', $coupon) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 dark:hover:bg-slate-600">عرض التفاصيل</a>
            <a href="{{ route('admin.coupons.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white">إلغاء</a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'إنشاء برنامج إحالة')
@section('header', '')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.referral-programs.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-600"><i class="fas fa-arrow-right"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-heading"><i class="fas fa-gift text-emerald-600 ml-2"></i>برنامج إحالة جديد</h1>
            <p class="text-sm text-slate-500 mt-1">حدّد خصم الصديق المُحال ومكافأة من دعاه</p>
        </div>
    </div>
    @include('admin.partials.referral-program-how-it-works')
    <div class="max-w-4xl">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8">
            <form action="{{ route('admin.referral-programs.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- اسم البرنامج -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">اسم البرنامج *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الوصف -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- نوع الخصم للمحال -->
                    <div>
                        <label for="discount_type" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">نوع الخصم للمحال *</label>
                        <select name="discount_type" id="discount_type" required
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        </select>
                    </div>

                    <!-- قيمة الخصم -->
                    <div>
                        <label for="discount_value" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">قيمة الخصم *</label>
                        <input type="number" name="discount_value" id="discount_value" step="0.01" min="0" required value="{{ old('discount_value') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>

                    <!-- الحد الأقصى للخصم -->
                    <div>
                        <label for="maximum_discount" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">الحد الأقصى للخصم</label>
                        <input type="number" name="maximum_discount" id="maximum_discount" step="0.01" min="0" value="{{ old('maximum_discount') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">لنوع الخصم النسبة المئوية</p>
                    </div>

                    <!-- الحد الأدنى لمبلغ الطلب -->
                    <div>
                        <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">الحد الأدنى لمبلغ الطلب</label>
                        <input type="number" name="minimum_order_amount" id="minimum_order_amount" step="0.01" min="0" value="{{ old('minimum_order_amount') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100 mb-4">مكافأة المحيل</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- نوع المكافأة -->
                        <div>
                            <label for="referrer_reward_type" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">نوع المكافأة *</label>
                            <select name="referrer_reward_type" id="referrer_reward_type" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                <option value="fixed" {{ old('referrer_reward_type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                <option value="percentage" {{ old('referrer_reward_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية</option>
                                <option value="points" {{ old('referrer_reward_type') == 'points' ? 'selected' : '' }}>نقاط</option>
                            </select>
                        </div>

                        <!-- قيمة المكافأة -->
                        <div>
                            <label for="referrer_reward_value" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">قيمة المكافأة</label>
                            <input type="number" name="referrer_reward_value" id="referrer_reward_value" step="0.01" min="0" value="{{ old('referrer_reward_value') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100 mb-4">الإعدادات</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- مدة صلاحية الخصم -->
                        <div>
                            <label for="discount_valid_days" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">مدة صلاحية الخصم (بالأيام) *</label>
                            <input type="number" name="discount_valid_days" id="discount_valid_days" min="1" required value="{{ old('discount_valid_days', 30) }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>

                        <!-- الحد الأقصى لاستخدام الخصم -->
                        <div>
                            <label for="max_discount_uses_per_referred" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">الحد الأقصى لاستخدام الخصم للمحال *</label>
                            <input type="number" name="max_discount_uses_per_referred" id="max_discount_uses_per_referred" min="1" required value="{{ old('max_discount_uses_per_referred', 1) }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>

                        <!-- الحد الأقصى للإحالات لكل مستخدم -->
                        <div>
                            <label for="max_referrals_per_user" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">الحد الأقصى للإحالات لكل مستخدم</label>
                            <input type="number" name="max_referrals_per_user" id="max_referrals_per_user" min="1" value="{{ old('max_referrals_per_user') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">اتركه فارغاً للسماح بإحالات غير محدودة</p>
                        </div>

                        <div>
                            <label for="referral_code_valid_days" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">صلاحية رابط الإحالة (أيام)</label>
                            <input type="number" name="referral_code_valid_days" id="referral_code_valid_days" min="1" value="{{ old('referral_code_valid_days') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">اختياري — للأرشفة فقط؛ التسجيل يعتمد على كود المحيل الحالي</p>
                        </div>

                        <!-- السماح بالإحالة الذاتية -->
                        <div class="flex items-center gap-3 pt-8">
                            <input type="checkbox" name="allow_self_referral" id="allow_self_referral" value="1" {{ old('allow_self_referral') ? 'checked' : '' }}
                                class="h-5 w-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                            <label for="allow_self_referral" class="text-sm font-medium text-gray-700 dark:text-slate-300">السماح بالإحالة الذاتية</label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100 mb-4">الفترة الزمنية</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- تاريخ البدء -->
                        <div>
                            <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">تاريخ البدء</label>
                            <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>

                        <!-- تاريخ الانتهاء -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">تاريخ الانتهاء</label>
                            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                                class="w-full px-4 py-3 border-2 border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-violet-200 dark:border-violet-800 bg-violet-50/50 dark:bg-violet-900/20 p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
                            class="h-5 w-5 text-violet-600 focus:ring-violet-500 border-gray-300 rounded">
                        <label for="is_default" class="text-sm font-medium text-gray-800 dark:text-violet-200">جعل هذا البرنامج <strong>الافتراضي</strong> لإحالات التسجيل الجديدة</label>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-slate-300 mr-8">يُلغى الافتراضي عن البرامج الأخرى تلقائياً. يُفضّل أن يكون البرنامج الافتراضي مفعّلاً وضمن فترة صلاحية.</p>
                </div>

                <!-- الحالة -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-5 w-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-slate-300">تفعيل البرنامج</label>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-slate-700">
                    <a href="{{ route('admin.referral-programs.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        إلغاء
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">
                        <i class="fas fa-save"></i> حفظ البرنامج
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


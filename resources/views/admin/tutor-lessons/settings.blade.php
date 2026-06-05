@extends('layouts.admin')

@section('title', __('tutor.admin_settings_title'))
@section('header', __('tutor.admin_settings_title'))

@section('content')
@php
    $hours = (int) old('default_student_lesson_hours', $settings['default_student_lesson_hours'] ?? 0);
    $advanceDays = (int) old('booking_advance_days', $settings['booking_advance_days'] ?? 14);
    $slotStep = (int) old('slot_step_minutes', $settings['slot_step_minutes'] ?? 30);
    $duration = (int) old('default_duration_minutes', $settings['default_duration_minutes'] ?? 60);
    $selfSchedule = (bool) old('self_schedule_enabled', $settings['self_schedule_enabled'] ?? true);

    $summaryCards = [
        [
            'label' => 'ساعات افتراضية للطالب',
            'value' => $hours,
            'suffix' => 'ساعة',
            'icon' => 'fas fa-clock',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'desc' => 'عند عدم وجود اشتراك مخصّص',
        ],
        [
            'label' => 'الحجز الذاتي',
            'value' => $selfSchedule ? 'مفعّل' : 'معطّل',
            'suffix' => '',
            'icon' => 'fas fa-calendar-plus',
            'bg' => $selfSchedule ? 'bg-emerald-100' : 'bg-slate-100',
            'text' => $selfSchedule ? 'text-emerald-600' : 'text-slate-500',
            'desc' => 'اختيار الموعد ثم تعيين معلم',
        ],
        [
            'label' => 'نافذة الحجز المسبق',
            'value' => $advanceDays,
            'suffix' => 'يوم',
            'icon' => 'fas fa-calendar-week',
            'bg' => 'bg-sky-100',
            'text' => 'text-sky-600',
            'desc' => 'عدد الأيام المعروضة للطالب',
        ],
        [
            'label' => 'مدة الحصة الافتراضية',
            'value' => $duration,
            'suffix' => 'د',
            'icon' => 'fas fa-hourglass-half',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
            'desc' => 'خطوة المواعيد: '.$slotStep.' د',
        ],
    ];
@endphp

<div class="space-y-6 w-full max-w-none">
    @include('admin.tutor-lessons._nav')

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium shadow-sm">
            <i class="fas fa-check-circle ml-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">
            <p class="font-bold mb-1">يرجى تصحيح الأخطاء التالية:</p>
            <ul class="list-disc pr-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- هيدر الصفحة --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-cog text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">{{ __('tutor.admin_settings_title') }}</h2>
                    <p class="text-sm text-slate-600 mt-1">إعدادات حصص الطلاب مع المعلمين: الساعات الافتراضية، الحجز الذاتي، ونافذة المواعيد.</p>
                </div>
            </div>
            <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-violet-700 bg-violet-50 border border-violet-200 rounded-xl hover:bg-violet-100 transition-colors">
                <i class="fas fa-credit-card"></i>
                إدارة الاشتراكات
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach($summaryCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900">
                                {{ $card['value'] }}@if($card['suffix'])<span class="text-base font-bold text-slate-500 mr-1">{{ $card['suffix'] }}</span>@endif
                            </p>
                        </div>
                        <div class="w-11 h-11 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} shadow-sm shrink-0">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $card['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- نموذج الإعدادات --}}
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-violet-500"></i>
                    تعديل الإعدادات
                </h3>
                <p class="text-xs text-slate-600 mt-1">تُطبَّق على الطلاب الجدد وتُزامَن مع الاشتراكات عند التفعيل.</p>
            </div>

            <form method="post" action="{{ route('admin.tutor-lessons.settings.update') }}" class="p-6 space-y-6">
                @csrf

                <div class="rounded-xl border border-violet-100 bg-violet-50/40 p-5">
                    <label for="default_student_lesson_hours" class="block text-sm font-bold text-slate-800 mb-2">
                        <i class="fas fa-box text-violet-500 ml-1"></i>
                        ساعات الحصص الافتراضية
                    </label>
                    <p class="text-xs text-slate-600 mb-3">تُمنح للطالب إذا لم يُحدَّد عدد ساعات في اشتراكه الفردي.</p>
                    <div class="flex items-center gap-3 max-w-xs">
                        <input type="number" id="default_student_lesson_hours" name="default_student_lesson_hours"
                               min="0" max="10000" required
                               value="{{ $hours }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-900 text-lg font-bold focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        <span class="text-sm font-semibold text-slate-600 shrink-0">ساعة / شهر</span>
                    </div>
                    @error('default_student_lesson_hours')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-slate-800">تفعيل الحجز الذاتي للطلاب</p>
                            <p class="text-xs text-slate-600 mt-1">يسمح للطالب باختيار موعد من الشبكة المتاحة ثم تعيين معلم تلقائياً (نمط «حجز بنفسك»).</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="hidden" name="self_schedule_enabled" value="0">
                            <input type="checkbox" name="self_schedule_enabled" value="1" id="self_schedule_enabled"
                                   class="sr-only peer" @checked($selfSchedule)>
                            <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-violet-500 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-violet-600"></div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="booking_advance_days" class="block text-sm font-bold text-slate-800 mb-2">أيام الحجز المسبق</label>
                        <p class="text-xs text-slate-500 mb-2">كم يوماً للأمام يُعرض للطالب عند الحجز الذاتي.</p>
                        <input type="number" id="booking_advance_days" name="booking_advance_days"
                               min="1" max="60" required value="{{ $advanceDays }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @error('booking_advance_days')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="default_duration_minutes" class="block text-sm font-bold text-slate-800 mb-2">مدة الحصة الافتراضية</label>
                        <p class="text-xs text-slate-500 mb-2">بالدقائق — تُستخدم عند إنشاء حجز جديد.</p>
                        <div class="relative">
                            <input type="number" id="default_duration_minutes" name="default_duration_minutes"
                                   min="30" max="180" required value="{{ $duration }}"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-slate-500">د</span>
                        </div>
                        @error('default_duration_minutes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="slot_step_minutes" class="block text-sm font-bold text-slate-800 mb-2">خطوة تقسيم المواعيد</label>
                        <p class="text-xs text-slate-500 mb-2">الفاصل بين كل موعد معروض في صفحة الحجز الذاتي (مثلاً 30 = كل نصف ساعة).</p>
                        <input type="number" id="slot_step_minutes" name="slot_step_minutes"
                               min="15" max="120" step="15" required value="{{ $slotStep }}"
                               class="w-full max-w-xs px-4 py-3 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                        @error('slot_step_minutes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-200">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-violet-500 text-white text-sm font-bold shadow-lg shadow-violet-500/25 hover:from-violet-700 hover:to-violet-600 transition-all">
                        <i class="fas fa-save"></i>
                        حفظ الإعدادات
                    </button>
                    <a href="{{ route('admin.tutor-lessons.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                        إلغاء
                    </a>
                </div>
            </form>
        </section>

        {{-- الشريط الجانبي --}}
        <aside class="space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-sm font-black text-slate-900">ربط سريع</h3>
                </div>
                <div class="p-5 space-y-3">
                    <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center justify-between gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 group-hover:text-blue-700">الاشتراكات</p>
                                <p class="text-xs text-slate-500">ساعات مخصّصة لكل طالب</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-left text-xs text-slate-400"></i>
                    </a>
                    <a href="{{ route('admin.tutor-lessons.index') }}" class="flex items-center justify-between gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-violet-300 hover:bg-violet-50/50 transition-all group">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 group-hover:text-violet-700">لوحة الرقابة</p>
                                <p class="text-xs text-slate-500">الحجوزات والمعلمون</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-left text-xs text-slate-400"></i>
                    </a>
                </div>
            </section>

            <section class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-5 shadow-sm">
                <h3 class="text-sm font-black text-violet-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    كيف تُستخدم هذه القيم؟
                </h3>
                <ul class="text-xs text-violet-900/90 space-y-2 leading-relaxed">
                    <li class="flex gap-2"><span class="text-violet-500">•</span> عند تفعيل اشتراك طالب يُنسخ حقل «ساعات الحصص» إلى ملفه الدراسي.</li>
                    <li class="flex gap-2"><span class="text-violet-500">•</span> الحجز الذاتي يعتمد على جداول توفر المعلمين المفعّلين.</li>
                    <li class="flex gap-2"><span class="text-violet-500">•</span> قوالب الباقات (أساسية / قياسية / مميزة) تُعدَّل في أسفل الصفحة وتُطبَّق من «إضافة اشتراك».</li>
                    <li class="flex gap-2"><span class="text-violet-500">•</span> يمكن تجاوز الساعات بباقة مخصصة من صفحة الاشتراك.</li>
                </ul>
            </section>
        </aside>
    </div>

    @include('admin.tutor-lessons._student-plans-form')
</div>
@endsection

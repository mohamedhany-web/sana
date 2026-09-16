@extends('layouts.admin')

@section('title', 'انضمام المعلمين - ' . config('app.name', 'Sana'))
@section('header', 'طلبات انضمام المعلمين')

@section('content')
<div class="space-y-6 sm:space-y-10">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">انضمام المعلمين</h2>
                <p class="text-sm text-slate-500 mt-2">إدارة كاملة لطلبات التسجيل — مراجعة، تعديل، تفعيل/إيقاف الحساب، وحذف.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <input type="text" readonly value="{{ $publicApplyUrl }}" id="publicApplyUrl"
                       class="flex-1 min-w-0 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-mono" dir="ltr">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('publicApplyUrl').value); this.textContent='تم النسخ!'; setTimeout(() => this.textContent='نسخ الرابط', 2000)"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700 whitespace-nowrap">
                    <i class="fas fa-copy"></i>
                    نسخ الرابط
                </button>
                <a href="{{ $publicApplyUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 whitespace-nowrap">
                    <i class="fas fa-external-link-alt"></i>
                    فتح النموذج
                </a>
                <a href="{{ $formPreviewUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-violet-700 whitespace-nowrap"
                   title="معاينة داخلية للإدارة — بدون إرسال طلب">
                    <i class="fas fa-eye"></i>
                    معاينة النموذج
                </a>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 p-5 sm:p-8">
            <div class="rounded-2xl border border-slate-200 bg-white/70 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">الإجمالي</p>
                <p class="mt-2 text-xl font-bold text-slate-900">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-amber-700">بانتظار</p>
                <p class="mt-2 text-xl font-bold text-amber-800">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">مقبولة</p>
                <p class="mt-2 text-xl font-bold text-emerald-800">{{ $stats['approved'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-rose-700">مرفوضة</p>
                <p class="mt-2 text-xl font-bold text-rose-800">{{ $stats['rejected'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">حسابات مفعّلة</p>
                <p class="mt-2 text-xl font-bold text-emerald-800">{{ $stats['active_accounts'] ?? 0 }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-600">حسابات موقوفة</p>
                <p class="mt-2 text-xl font-bold text-slate-800">{{ $stats['inactive_accounts'] ?? 0 }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12">
            @php
                $activeFilters = $activeFilters ?? [];
                $filterOptions = $filterOptions ?? [];
                $hasActiveFilters = count($activeFilters) > 0;
            @endphp
            <form method="GET" class="space-y-5" id="instructor-join-filters">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 m-0">
                            <i class="fas fa-filter text-sky-600"></i>
                            فلاتر سريعة
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 m-0">ابحث بالبيانات الأساسية وبيانات نموذج التوظيف للوصول السريع لأي معلم.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($hasActiveFilters)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 text-sky-800 px-3 py-1 text-xs font-bold">
                                {{ count($activeFilters) }} فلتر نشط
                                · {{ $applications->total() }} نتيجة
                            </span>
                            <a href="{{ route('admin.instructor-applications.index') }}"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                <i class="fas fa-times"></i>
                                مسح الكل
                            </a>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 mb-2">بحث شامل</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="اسم، بريد، جوال، عنوان، جنسية، مدينة، مؤهل، رقم الطلب…"
                               class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">حالة الطلب</label>
                        <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                            <option value="">جميع الحالات</option>
                            <option value="{{ \App\Models\InstructorProfile::STATUS_PENDING_REVIEW }}" @selected(request('status') === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW)>بانتظار الموافقة</option>
                            <option value="{{ \App\Models\InstructorProfile::STATUS_APPROVED }}" @selected(request('status') === \App\Models\InstructorProfile::STATUS_APPROVED)>مقبول</option>
                            <option value="{{ \App\Models\InstructorProfile::STATUS_REJECTED }}" @selected(request('status') === \App\Models\InstructorProfile::STATUS_REJECTED)>مرفوض</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-2">حالة الحساب</label>
                        <select name="account" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                            <option value="">الكل</option>
                            <option value="active" @selected(request('account') === 'active')>مفعّل</option>
                            <option value="inactive" @selected(request('account') === 'inactive')>موقوف</option>
                        </select>
                    </div>
                </div>

                <details class="rounded-2xl border border-slate-200 bg-slate-50/70 open:bg-white" @if($hasActiveFilters) open @endif>
                    <summary class="cursor-pointer select-none list-none px-4 py-3 flex items-center justify-between gap-3">
                        <span class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-sliders text-violet-600"></i>
                            فلاتر البيانات الأساسية ونموذج التوظيف
                        </span>
                        <span class="text-xs text-slate-500">اضغط للتوسيع</span>
                    </summary>
                    <div class="px-4 pb-4 pt-1 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">لوحة المعلم</label>
                            <select name="portal_mode" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['portal_modes'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('portal_mode') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">تفعيل الحجز</label>
                            <select name="booking" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                <option value="activated" @selected(request('booking') === 'activated')>مفعّل للحجز</option>
                                <option value="not_activated" @selected(request('booking') === 'not_activated')>غير مفعّل</option>
                                <option value="offering" @selected(request('booking') === 'offering')>يعرض الحجز</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">الظهور على الرئيسية</label>
                            <select name="homepage" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                <option value="yes" @selected(request('homepage') === 'yes')>ظاهر</option>
                                <option value="no" @selected(request('homepage') === 'no')>مخفي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">نوع النموذج</label>
                            <select name="form" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                <option value="full" @selected(request('form') === 'full')>نموذج توظيف كامل</option>
                                <option value="basic" @selected(request('form') === 'basic')>بيانات أساسية فقط</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">مادة المنصة</label>
                            <select name="subject_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">كل المواد</option>
                                @foreach(($filterOptions['subjects'] ?? []) as $subject)
                                    <option value="{{ $subject->id }}" @selected((int) request('subject_id') === (int) $subject->id)>
                                        {{ $subject->name }}@if($subject->academicYear) — {{ $subject->academicYear->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">مسار / سنة المنصة</label>
                            <select name="academic_year_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['years'] ?? []) as $year)
                                    <option value="{{ $year->id }}" @selected((int) request('academic_year_id') === (int) $year->id)>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">نمط الحجز</label>
                            <select name="matching_mode" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['matching_modes'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('matching_mode') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">نوع الحصة</label>
                            <select name="session_type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['session_types'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('session_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">تخصص التوظيف</label>
                            <select name="specialization" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['specializations'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('specialization') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">المنهج</label>
                            <select name="curriculum" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['curricula'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('curriculum') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">المرحلة</label>
                            <select name="stage" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['stages'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('stage') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">صيغة الحصص (النموذج)</label>
                            <select name="lesson_format" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['lesson_formats'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('lesson_format') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">الجنسية</label>
                            <input type="text" name="nationality" value="{{ request('nationality') }}" placeholder="مثال: سعودي"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">الدولة / المدينة</label>
                            <input type="text" name="country_city" value="{{ request('country_city') }}" placeholder="مثال: الرياض"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">خبرة من (سنوات)</label>
                            <input type="number" min="0" name="experience_min" value="{{ request('experience_min') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">خبرة إلى (سنوات)</label>
                            <input type="number" min="0" name="experience_max" value="{{ request('experience_max') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">فيديو تعريفي</label>
                            <select name="has_video" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                <option value="yes" @selected(request('has_video') === 'yes')>يوجد فيديو</option>
                                <option value="no" @selected(request('has_video') === 'no')>بدون فيديو</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">قرار التقييم</label>
                            <select name="eval_decision" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                                <option value="">الكل</option>
                                @foreach(($filterOptions['evaluation_decisions'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(request('eval_decision') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">تاريخ التقديم من</label>
                            <input type="date" name="submitted_from" value="{{ request('submitted_from') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2">تاريخ التقديم إلى</label>
                            <input type="date" name="submitted_to" value="{{ request('submitted_to') }}"
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm">
                        </div>
                    </div>
                </details>

                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="sm:w-56">
                        <label class="block text-xs font-semibold text-slate-500 mb-2">الترتيب</label>
                        <select name="sort" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm">
                            <option value="" @selected(! request('sort'))>الأحدث تقديماً</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>الأقدم تقديماً</option>
                            <option value="updated" @selected(request('sort') === 'updated')>آخر تحديث</option>
                            <option value="name" @selected(request('sort') === 'name')>الاسم أ→ي</option>
                            <option value="experience_desc" @selected(request('sort') === 'experience_desc')>خبرة أعلى</option>
                            <option value="experience_asc" @selected(request('sort') === 'experience_asc')>خبرة أقل</option>
                        </select>
                    </div>
                    <div class="flex flex-1 items-center gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                            <i class="fas fa-search"></i>
                            تطبيق الفلاتر
                        </button>
                        @if($hasActiveFilters)
                            <a href="{{ route('admin.instructor-applications.index') }}"
                               class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                <i class="fas fa-undo"></i>
                                إعادة تعيين
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-inbox text-sky-600"></i>
                قائمة الطلبات
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                        <th class="px-6 py-4 text-right">المعلم</th>
                        <th class="px-6 py-4 text-right">البيانات الأساسية</th>
                        <th class="px-6 py-4 text-right">حالة الطلب</th>
                        <th class="px-6 py-4 text-right">لوحة المعلم</th>
                        <th class="px-6 py-4 text-right">الحساب</th>
                        <th class="px-6 py-4 text-right">تاريخ التقديم</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($applications as $app)
                    @php
                        $isActive = (bool) ($app->user?->is_active);
                        $canManage = $app->user && !\App\Services\InstructorApplicationService::mustKeepAccountActive($app->user);
                        $personal = $app->application_data['personal'] ?? [];
                        $teaching = $app->application_data['teaching'] ?? [];
                        $specLabels = collect($teaching['specializations'] ?? [])
                            ->map(fn ($k) => ($filterOptions['specializations'][$k] ?? null))
                            ->filter()
                            ->take(3);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ $app->user?->name ?? '—' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5" dir="ltr">{{ $app->user?->email }}</div>
                            @if($app->user?->phone)
                                <div class="text-xs text-slate-400 mt-0.5" dir="ltr">{{ $app->user->phone }}</div>
                            @endif
                            @if(!empty($app->application_data))
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 text-[10px] font-bold">نموذج توظيف كامل</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            <div class="font-medium">{{ Str::limit($app->headline, 48) ?: '—' }}</div>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @if($app->tutor_years_experience)
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold">{{ (int) $app->tutor_years_experience }} سنة خبرة</span>
                                @endif
                                @if(!empty($personal['nationality']))
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[10px] font-bold">{{ $personal['nationality'] }}</span>
                                @endif
                                @if(!empty($personal['country_city']))
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 text-[10px] font-bold">{{ Str::limit($personal['country_city'], 24) }}</span>
                                @endif
                                @foreach($specLabels as $label)
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-violet-50 text-violet-800 text-[10px] font-bold">{{ $label }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($app->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">بانتظار</span>
                            @elseif($app->status === \App\Models\InstructorProfile::STATUS_APPROVED)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مقبول</span>
                            @elseif($app->status === \App\Models\InstructorProfile::STATUS_REJECTED)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">مرفوض</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $app->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            @if($app->status === \App\Models\InstructorProfile::STATUS_APPROVED)
                                {{ $app->portalModeLabel() }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($isActive)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fas fa-circle text-[6px]"></i> مفعّل
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    <i class="fas fa-ban text-[10px]"></i> موقوف
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                            {{ $app->submitted_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center justify-center gap-1.5">
                                <a href="{{ route('admin.instructor-applications.show', $app) }}" data-turbo="false"
                                   class="inline-flex items-center gap-1 rounded-xl bg-sky-50 text-sky-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-sky-100" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($app->user && Route::has('admin.quality-control.instructors.show'))
                                    <a href="{{ route('admin.quality-control.instructors.show', $app->user) }}" data-turbo="false"
                                       class="inline-flex items-center gap-1 rounded-xl bg-indigo-50 text-indigo-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-indigo-100" title="رقابة شاملة">
                                        <i class="fas fa-shield-alt"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.instructor-applications.edit', $app) }}" data-turbo="false"
                                   class="inline-flex items-center gap-1 rounded-xl bg-violet-50 text-violet-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-violet-100" title="تعديل">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @if($canManage)
                                    <form method="POST" action="{{ route('admin.instructor-applications.toggle-account', $app) }}" data-turbo="false" class="inline">
                                        @csrf
                                        <button type="submit" title="{{ $isActive ? 'إيقاف' : 'تفعيل' }}"
                                                class="inline-flex items-center gap-1 rounded-xl {{ $isActive ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} px-2.5 py-1.5 text-xs font-semibold">
                                            <i class="fas {{ $isActive ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.instructor-applications.destroy', $app) }}" data-turbo="false" class="inline"
                                          onsubmit="return confirm('حذف هذا الطلب؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف"
                                                class="inline-flex items-center gap-1 rounded-xl bg-rose-50 text-rose-700 px-2.5 py-1.5 text-xs font-semibold hover:bg-rose-100">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">لا توجد طلبات مطابقة للفلاتر الحالية.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">{{ $applications->links() }}</div>
        @endif
    </section>
</div>
@endsection

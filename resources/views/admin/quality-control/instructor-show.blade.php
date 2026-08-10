@extends('layouts.admin')

@section('title', 'رقابة المعلم — ' . $instructor->name)
@section('header', 'رقابة المعلم')

@section('content')
@php
    $statusLabels = [
        \App\Models\InstructorProfile::STATUS_DRAFT => 'مسودة',
        \App\Models\InstructorProfile::STATUS_PENDING_REVIEW => 'بانتظار المراجعة',
        \App\Models\InstructorProfile::STATUS_APPROVED => 'مقبول',
        \App\Models\InstructorProfile::STATUS_REJECTED => 'مرفوض',
    ];
    $subscriptionStatusLabels = [
        'active' => 'نشط',
        'expired' => 'منتهي',
        'cancelled' => 'ملغي',
    ];
    $ticketStatusLabels = \App\Models\SupportTicket::statusLabels();
    $appData = is_array($profile?->application_data) ? $profile->application_data : [];
@endphp

<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 flex flex-wrap items-center gap-1">
        <a href="{{ route('admin.quality-control.index') }}" class="text-sky-600 hover:text-sky-800 font-semibold">الرقابة والجودة</a>
        <span>/</span>
        <a href="{{ route('admin.quality-control.instructors') }}" class="text-sky-600 hover:text-sky-800 font-semibold">المعلمين</a>
        <span>/</span>
        <span class="text-slate-700 font-medium">{{ $instructor->name }}</span>
    </nav>

    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($instructor->profile_image)
                    <img src="{{ $instructor->profile_image_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-sky-600 text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-slate-900">{{ $instructor->name }}</h1>
                    <p class="text-sm text-slate-500" dir="ltr">{{ $instructor->phone ?? '—' }} · {{ $instructor->email ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        آخر دخول: {{ $instructor->last_login_at ? $instructor->last_login_at->format('Y-m-d H:i') . ' (' . $instructor->last_login_at->diffForHumans() . ')' : '—' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($profile && Route::has('admin.instructor-applications.show'))
                    <a href="{{ route('admin.instructor-applications.show', $profile) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-violet-200 text-sm font-bold text-violet-700 bg-violet-50 hover:bg-violet-100">
                        <i class="fas fa-file-alt"></i>
                        طلب الانضمام
                    </a>
                @endif
                @if(Route::has('admin.users.edit'))
                    <a href="{{ route('admin.users.edit', $instructor->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-user-cog"></i>
                        تعديل الحساب
                    </a>
                @endif
                <a href="{{ route('admin.quality-control.instructors.export', $instructor) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-sm font-bold text-white hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            </div>
        </div>
    </section>

    {{-- ملخص أرقام --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">ملخص النشاط</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 p-5 sm:p-6">
            <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-3">
                <p class="text-[11px] font-bold text-sky-700">كورسات أونلاين</p>
                <p class="text-xl font-black text-slate-900">{{ $advancedCourses->count() }}</p>
                <p class="text-[10px] text-slate-500">تسجيلات {{ $enrollmentsCount }}</p>
            </div>
            <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-3">
                <p class="text-[11px] font-bold text-violet-700">حصص مع طلاب</p>
                <p class="text-xl font-black text-slate-900">{{ $bookingStats['total'] }}</p>
                <p class="text-[10px] text-slate-500">مكتمل {{ $bookingStats['completed'] }} · قادم {{ $bookingStats['upcoming'] }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-3">
                <p class="text-[11px] font-bold text-emerald-700">اشتراكات</p>
                <p class="text-xl font-black text-slate-900">{{ $subscriptions->count() }}</p>
                <p class="text-[10px] text-slate-500">{{ $activeSubscription ? 'باقة نشطة' : 'لا يوجد نشط' }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-3">
                <p class="text-[11px] font-bold text-amber-800">تذاكر دعم</p>
                <p class="text-xl font-black text-slate-900">{{ $supportTickets->count() }}</p>
                <p class="text-[10px] text-slate-500">{{ $openSupportCount }} مفتوحة</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-3">
                <p class="text-[11px] font-bold text-rose-700">اتفاقيات</p>
                <p class="text-xl font-black text-slate-900">{{ $agreements->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                <p class="text-[11px] font-bold text-slate-600">محاضرات / واجبات</p>
                <p class="text-xl font-black text-slate-900">{{ $lectures->count() }} / {{ $assignments->count() }}</p>
            </div>
        </div>
    </section>

    {{-- البيانات الشخصية --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">البيانات الشخصية الحالية</h2>
        </div>
        <div class="p-5 sm:p-8">
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الاسم</dt><dd class="font-bold text-slate-900">{{ $instructor->name }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">البريد</dt><dd class="text-slate-900 break-all">{{ $instructor->email ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الجوال</dt><dd class="text-slate-900" dir="ltr">{{ $instructor->phone ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ الميلاد</dt><dd class="text-slate-900">{{ $instructor->birth_date ? $instructor->birth_date->format('Y-m-d') : '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الحالة</dt>
                    <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $instructor->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $instructor->is_active ? 'مفعّل' : 'غير مفعّل' }}</span></dd>
                </div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الدور</dt><dd class="text-slate-900">{{ $instructor->role }}</dd></div>
                <div class="md:col-span-2 lg:col-span-3"><dt class="text-slate-500 text-xs font-semibold mb-0.5">العنوان</dt><dd class="text-slate-900">{{ $instructor->address ?? '—' }}</dd></div>
                <div class="md:col-span-2 lg:col-span-3"><dt class="text-slate-500 text-xs font-semibold mb-0.5">النبذة</dt><dd class="text-slate-900">{{ $instructor->bio ?? $profile?->bio ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ التسجيل</dt><dd class="text-slate-900">{{ $instructor->created_at->format('Y-m-d H:i') }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">آخر تحديث</dt><dd class="text-slate-900">{{ $instructor->updated_at->format('Y-m-d H:i') }}</dd></div>
            </dl>
        </div>
    </section>

    {{-- ملف المعلم / طلب التوظيف --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">ملف المعلم وطلب الانضمام</h2>
        </div>
        @if($profile)
            <div class="p-5 sm:p-8 space-y-5">
                <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">حالة الطلب</dt>
                        <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">{{ $statusLabels[$profile->status] ?? $profile->status }}</span></dd>
                    </div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">لوحة المعلم</dt><dd class="font-semibold text-slate-900">{{ $profile->portalModeLabel() }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">حجز الحصص</dt>
                        <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $profile->isTutorActivated() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $profile->isTutorActivated() ? 'مفعّل' : 'غير مفعّل' }}</span></dd>
                    </div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">العنوان المهني</dt><dd class="text-slate-900">{{ $profile->headline ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">سنوات الخبرة</dt><dd class="text-slate-900">{{ $profile->tutor_years_experience ?? ($appData['years_experience'] ?? '—') }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ التقديم</dt><dd class="text-slate-900">{{ $profile->submitted_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ التفعيل</dt><dd class="text-slate-900">{{ $profile->tutor_activated_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">إكمال الإعداد</dt><dd class="text-slate-900">{{ $profile->tutor_onboarding_completed_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الحصة التجريبية</dt><dd class="text-slate-900">{{ $profile->tutor_trial_completed_at?->format('Y-m-d H:i') ?? '—' }}</dd></div>
                </dl>

                @if($subjectNames->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-2">المواد</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($subjectNames as $name)
                                <span class="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-800 text-sm font-semibold">{{ $name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($yearNames->isNotEmpty())
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-2">المراحل / السنوات</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($yearNames as $name)
                                <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-800 text-sm font-semibold">{{ $name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($profile->skills_list))
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-2">المهارات</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($profile->skills_list as $skill)
                                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-sm">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($profile->rejection_reason)
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <strong>سبب الرفض:</strong> {{ $profile->rejection_reason }}
                    </div>
                @endif
            </div>
        @else
            <p class="p-8 text-center text-sm text-slate-500">لا يوجد ملف معلّم مرتبط بهذا الحساب.</p>
        @endif
    </section>

    {{-- الاشتراكات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-2">
            <h2 class="text-lg font-black text-slate-900">الاشتراكات والباقات</h2>
            @if(Route::has('admin.subscriptions.index'))
                <a href="{{ route('admin.subscriptions.index') }}" class="text-xs font-bold text-sky-600 hover:underline">إدارة الاشتراكات</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            @if($subscriptions->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الخطة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">السعر</th>
                            <th class="px-4 py-3">من — إلى</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($subscriptions as $sub)
                            @php $isActive = $sub->id === ($activeSubscription?->id); @endphp
                            <tr class="hover:bg-slate-50 {{ $isActive ? 'bg-sky-50/50' : '' }}">
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    {{ $sub->plan_name }}
                                    @if($isActive)<span class="mr-1 text-[10px] font-bold text-sky-700 bg-sky-100 px-1.5 py-0.5 rounded">نشط</span>@endif
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $subscriptionStatusLabels[$sub->status] ?? $sub->status }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $sub->price, 0) }} {{ __('public.currency') }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600">
                                    {{ optional($sub->start_date)->format('Y-m-d') ?? '—' }}
                                    → {{ optional($sub->end_date)->format('Y-m-d') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد اشتراكات مسجّلة.</p>
            @endif
        </div>
    </section>

    {{-- حصص مع الطلاب --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">حصص مع الطلاب</h2>
        </div>
        <div class="px-5 py-3 bg-violet-50/40 border-b border-violet-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div><p class="text-xs text-slate-500">الإجمالي</p><p class="font-black text-slate-900">{{ $bookingStats['total'] }}</p></div>
            <div><p class="text-xs text-slate-500">مكتمل</p><p class="font-black text-emerald-700">{{ $bookingStats['completed'] }}</p></div>
            <div><p class="text-xs text-slate-500">قادم</p><p class="font-black text-sky-700">{{ $bookingStats['upcoming'] }}</p></div>
            <div><p class="text-xs text-slate-500">ملغي</p><p class="font-black text-rose-700">{{ $bookingStats['cancelled'] }}</p></div>
        </div>
        <div class="overflow-x-auto">
            @if($lessonBookings->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الموعد</th>
                            <th class="px-4 py-3">الطالب</th>
                            <th class="px-4 py-3">المادة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">المدة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($lessonBookings as $booking)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-xs">{{ optional($booking->scheduled_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">
                                    @if($booking->student && Route::has('admin.quality-control.students.show'))
                                        <a href="{{ route('admin.quality-control.students.show', $booking->student) }}" class="text-sky-600 hover:underline">{{ $booking->student->name }}</a>
                                    @else
                                        {{ $booking->student->name ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $booking->subject->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $booking->statusLabel() }}</td>
                                <td class="px-4 py-3 text-xs">{{ $booking->duration_minutes ?? '—' }} د</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد حجوزات حصص.</p>
            @endif
        </div>
    </section>

    {{-- أوقات التوفر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">أوقات التوفر</h2>
        </div>
        <div class="overflow-x-auto">
            @if($availabilities->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">اليوم</th>
                            <th class="px-4 py-3">من</th>
                            <th class="px-4 py-3">إلى</th>
                            <th class="px-4 py-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($availabilities as $slot)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium">{{ $slot->dayLabel() }}</td>
                                <td class="px-4 py-3 text-xs" dir="ltr">{{ \Illuminate\Support\Str::of((string) $slot->start_time)->substr(0, 5) }}</td>
                                <td class="px-4 py-3 text-xs" dir="ltr">{{ \Illuminate\Support\Str::of((string) $slot->end_time)->substr(0, 5) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $slot->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $slot->is_active ? 'نشط' : 'متوقف' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لم يُضبط جدول توفر بعد.</p>
            @endif
        </div>
    </section>

    {{-- الكورسات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">الكورسات (أونلاين)</h2>
        </div>
        <div class="overflow-x-auto">
            @if($advancedCourses->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">السعر</th>
                        <th class="px-4 py-3">نشط</th>
                        <th class="px-4 py-3">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($advancedCourses as $c)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $c->id }}</td>
                        <td class="px-4 py-3">
                            @if(Route::has('admin.advanced-courses.show'))
                                <a href="{{ route('admin.advanced-courses.show', $c) }}" class="font-medium text-sky-600 hover:underline">{{ $c->title }}</a>
                            @else
                                {{ $c->title }}
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $c->price ? number_format($c->price, 2) . currency_suffix() : '—' }}</td>
                        <td class="px-4 py-3">{{ $c->is_active ? 'نعم' : 'لا' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $c->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا توجد كورسات أونلاين.</p>
            @endif
        </div>
    </section>

    {{-- المحاضرات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">المحاضرات (أونلاين)</h2>
        </div>
        <div class="overflow-x-auto">
            @if($lectures->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">الكورس</th>
                        <th class="px-4 py-3">مجدولة في</th>
                        <th class="px-4 py-3">المدة</th>
                        <th class="px-4 py-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($lectures as $l)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $l->id }}</td>
                        <td class="px-4 py-3">{{ $l->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $l->course ? $l->course->title : '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $l->scheduled_at ? $l->scheduled_at->format('Y-m-d H:i') : '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $l->duration_minutes ?? '—' }} د</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs {{ $l->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($l->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">{{ $l->status ?? '—' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا توجد محاضرات أونلاين.</p>
            @endif
        </div>
    </section>

    {{-- الاتفاقيات والمدفوعات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">اتفاقيات المدرب</h2>
        </div>
        <div class="overflow-x-auto">
            @if($agreements->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">رقم الاتفاقية</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">نوع الفوترة</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">من - إلى</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agreements as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $a->agreement_number ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->billing_type ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->total_amount ? number_format($a->total_amount, 2) . currency_suffix() : ($a->monthly_amount ? number_format($a->monthly_amount, 2) . currency_suffix().'/شهر' : '—') }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $a->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $a->status ?? '—' }}</span></td>
                        <td class="px-4 py-3 text-xs">{{ $a->start_date ? $a->start_date->format('Y-m-d') : '—' }} — {{ $a->end_date ? $a->end_date->format('Y-m-d') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا توجد اتفاقيات.</p>
            @endif
        </div>
    </section>

    @if($agreementPayments->isNotEmpty())
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">مدفوعات الاتفاقيات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agreementPayments as $pay)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $pay->payment_number ?? $pay->id }}</td>
                        <td class="px-4 py-3">{{ number_format((float) $pay->amount, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-4 py-3 text-xs">{{ $pay->type ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $pay->status ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ optional($pay->paid_at ?? $pay->payment_date ?? $pay->created_at)->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- طلبات السحب وبيانات التحويل --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">طلبات السحب وبيانات التحويل</h2>
        </div>
        @if($instructor->payoutDetail && $instructor->payoutDetail->hasAnyDetails())
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                @include('admin.partials.instructor-payout-details', ['payoutDetail' => $instructor->payoutDetail])
            </div>
        @endif
        <div class="overflow-x-auto">
            @if($withdrawals->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">المبلغ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($withdrawals as $w)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $w->id }}</td>
                        <td class="px-4 py-3">{{ number_format($w->amount ?? 0, 2) }} {{ __('public.currency') }}</td>
                        <td class="px-4 py-3">{{ $w->status ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $w->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا توجد طلبات سحب.</p>
            @endif
        </div>
    </section>

    {{-- الواجبات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">الواجبات التي أنشأها المعلم</h2>
        </div>
        <div class="overflow-x-auto">
            @if($assignments->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">الكورس</th>
                        <th class="px-4 py-3">النقاط</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">استحقاق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($assignments as $a)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">{{ $a->id }}</td>
                        <td class="px-4 py-3">{{ $a->title ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->course ? $a->course->title : '—' }}</td>
                        <td class="px-4 py-3">{{ $a->max_score ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $a->status ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $a->due_date ? $a->due_date->format('Y-m-d') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا توجد واجبات.</p>
            @endif
        </div>
    </section>

    {{-- سجل العمل --}}
    @if($workLogs->isNotEmpty())
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">سجل ساعات العمل</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الدقائق</th>
                        <th class="px-4 py-3">المصدر</th>
                        <th class="px-4 py-3">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($workLogs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-xs">{{ optional($log->work_date)->format('Y-m-d') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $log->minutes ?? 0 }}</td>
                        <td class="px-4 py-3 text-xs">{{ $log->source ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ \Illuminate\Support\Str::limit($log->notes ?? '—', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- تذاكر الدعم --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">تذاكر الدعم</h2>
        </div>
        <div class="overflow-x-auto">
            @if($supportTickets->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">الموضوع</th>
                            <th class="px-4 py-3">التصنيف</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($supportTickets as $ticket)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $ticket->id }}</td>
                                <td class="px-4 py-3 font-medium">
                                    @if(Route::has('admin.support-tickets.show'))
                                        <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="text-sky-600 hover:underline">{{ $ticket->subject ?? '—' }}</a>
                                    @else
                                        {{ $ticket->subject ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $ticket->inquiryCategory->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $ticketStatusLabels[$ticket->status] ?? $ticket->status }}</td>
                                <td class="px-4 py-3 text-xs">{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد تذاكر دعم.</p>
            @endif
        </div>
    </section>

    {{-- سجل النشاط --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">سجل النشاط (آخر 100)</h2>
        </div>
        <div class="overflow-x-auto">
            @if($activityLogs->count() > 0)
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الإجراء</th>
                        <th class="px-4 py-3">الوصف</th>
                        <th class="px-4 py-3">النموذج</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($activityLogs as $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-xs">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">{{ $log->action ?? '—' }}</td>
                        <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($log->description ?? '—', 60) }}</td>
                        <td class="px-4 py-2 text-xs">{{ class_basename($log->model_type ?? '') ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="p-8 text-center text-sm text-slate-500">لا يوجد سجل نشاط.</p>
            @endif
        </div>
    </section>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'رقابة الطالب — ' . $student->name)
@section('header', 'رقابة الطالب')

@section('content')
@php
    $profile = $student->studentLearningProfile;
    $matchingLabels = [
        'assisted' => 'بمساعدة الإدارة',
        'self_schedule' => 'حجز ذاتي',
        'pick_teacher' => 'اختيار معلم',
    ];
    $ticketStatusLabels = \App\Models\SupportTicket::statusLabels();
    $subscriptionStatusLabels = [
        'active' => 'نشط',
        'expired' => 'منتهي',
        'cancelled' => 'ملغي',
    ];
@endphp

<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 flex flex-wrap items-center gap-1">
        <a href="{{ route('admin.quality-control.index') }}" class="text-violet-600 hover:text-violet-800 font-semibold">الرقابة والجودة</a>
        <span>/</span>
        <a href="{{ route('admin.quality-control.students') }}" class="text-violet-600 hover:text-violet-800 font-semibold">الطلاب</a>
        <span>/</span>
        <span class="text-slate-700 font-medium">{{ $student->name }}</span>
    </nav>

    {{-- الهيدر --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                @if($student->profile_image)
                    <img src="{{ $student->profile_image_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-violet-100 flex items-center justify-center">
                        <i class="fas fa-user-graduate text-violet-600 text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-slate-900">{{ $student->name }}</h1>
                    <p class="text-sm text-slate-500" dir="ltr">{{ $student->phone ?? '—' }} · {{ $student->email ?? '—' }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        آخر دخول: {{ $student->last_login_at ? $student->last_login_at->format('Y-m-d H:i') . ' (' . $student->last_login_at->diffForHumans() . ')' : '—' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(Route::has('admin.users.edit'))
                    <a href="{{ route('admin.users.edit', $student->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        <i class="fas fa-user-cog"></i>
                        تعديل الحساب
                    </a>
                @endif
                @if(Route::has('admin.students-accounts.index'))
                    <a href="{{ route('admin.students-accounts.index', ['search' => $student->phone ?? $student->name]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-violet-200 text-sm font-bold text-violet-700 bg-violet-50 hover:bg-violet-100">
                        <i class="fas fa-id-card"></i>
                        حسابات الطلاب
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ملخص أرقام --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">ملخص النشاط</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 p-5 sm:p-6">
            <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-3">
                <p class="text-[11px] font-bold text-violet-700">تسجيلات كورس</p>
                <p class="text-xl font-black text-slate-900">{{ $enrollmentStats['total'] }}</p>
                <p class="text-[10px] text-slate-500">نشط {{ $enrollmentStats['active'] }} · مكتمل {{ $enrollmentStats['completed'] }}</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50/50 p-3">
                <p class="text-[11px] font-bold text-sky-700">حصص مع معلم</p>
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
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-3">
                <p class="text-[11px] font-bold text-indigo-700">اختبارات</p>
                <p class="text-xl font-black text-slate-900">{{ $examAttempts->count() }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                <p class="text-[11px] font-bold text-slate-600">شهادات</p>
                <p class="text-xl font-black text-slate-900">{{ $certificates->count() }}</p>
            </div>
        </div>
    </section>

    {{-- البيانات الشخصية --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">البيانات الشخصية</h2>
        </div>
        <div class="p-5 sm:p-8">
            <dl class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الاسم</dt><dd class="font-bold text-slate-900">{{ $student->name }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">البريد</dt><dd class="text-slate-900 break-all">{{ $student->email ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الجوال</dt><dd class="text-slate-900" dir="ltr">{{ $student->phone ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">السنة الدراسية</dt><dd class="text-slate-900">{{ $student->academicYear->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ الميلاد</dt><dd class="text-slate-900">{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">الحالة</dt>
                    <dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $student->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $student->is_active ? 'نشط' : 'معطّل' }}</span></dd>
                </div>
                <div class="md:col-span-2 lg:col-span-3"><dt class="text-slate-500 text-xs font-semibold mb-0.5">العنوان</dt><dd class="text-slate-900">{{ $student->address ?? '—' }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">تاريخ التسجيل</dt><dd class="text-slate-900">{{ $student->created_at->format('Y-m-d H:i') }}</dd></div>
                <div><dt class="text-slate-500 text-xs font-semibold mb-0.5">آخر تحديث للحساب</dt><dd class="text-slate-900">{{ $student->updated_at->format('Y-m-d H:i') }}</dd></div>
            </dl>
            @if($student->guardians->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <p class="text-xs font-bold text-slate-600 mb-2">أولياء الأمور</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($student->guardians as $guardian)
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-sm text-slate-800">
                                <i class="fas fa-user-friends text-slate-400"></i>
                                {{ $guardian->name }}
                                @if($guardian->pivot->relation)<span class="text-xs text-slate-500">({{ $guardian->pivot->relation }})</span>@endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            @if($student->academicSupervisors->isNotEmpty())
                <div class="mt-4">
                    <p class="text-xs font-bold text-slate-600 mb-2">المشرفون الأكاديميون</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($student->academicSupervisors as $sup)
                            <span class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-800 text-sm font-semibold">{{ $sup->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
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
                            <th class="px-4 py-3">ساعات حصص</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($subscriptions as $sub)
                            @php
                                $hours = is_array($sub->feature_limits) ? ($sub->feature_limits['tutor_lesson_hours'] ?? null) : null;
                                $isActive = $sub->id === ($activeSubscription?->id);
                            @endphp
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
                                <td class="px-4 py-3">{{ $hours !== null ? (int) $hours . ' س' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد اشتراكات مسجّلة.</p>
            @endif
        </div>
    </section>

    {{-- ملف الحصص مع المعلم --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">حصص مع المعلمين</h2>
        </div>
        @if($profile)
            <div class="px-5 py-4 bg-violet-50/40 border-b border-violet-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div><p class="text-xs text-slate-500">رصيد الساعات</p><p class="font-black text-slate-900">{{ (int) $profile->lesson_hours_quota }} س</p></div>
                <div><p class="text-xs text-slate-500">المستخدم</p><p class="font-black text-slate-900">{{ (int) $profile->lesson_hours_used }} س</p></div>
                <div><p class="text-xs text-slate-500">المتبقي</p><p class="font-black text-emerald-700">{{ max(0, (int) $profile->lesson_hours_quota - (int) $profile->lesson_hours_used) }} س</p></div>
                <div><p class="text-xs text-slate-500">نمط المطابقة</p><p class="font-semibold text-slate-800">{{ $matchingLabels[$profile->matching_mode] ?? $profile->matching_mode }}</p></div>
            </div>
        @else
            <p class="px-5 py-3 text-xs text-amber-800 bg-amber-50 border-b border-amber-100">لم يُنشأ ملف تعلّم بعد (يُنشأ عادةً مع الاشتراك أو أول حصة).</p>
        @endif
        <div class="overflow-x-auto">
            @if($lessonBookings->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الموعد</th>
                            <th class="px-4 py-3">المعلم</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">المدة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($lessonBookings as $booking)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-xs">{{ optional($booking->scheduled_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $booking->instructor->name ?? '—' }}</td>
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

    {{-- تسجيلات الكورسات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">تسجيلات الكورسات (أونلاين)</h2>
        </div>
        <div class="overflow-x-auto">
            @if($enrollments->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الكورس</th>
                            <th class="px-4 py-3">المدرب</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التقدّم</th>
                            <th class="px-4 py-3">السعر</th>
                            <th class="px-4 py-3">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($enrollments as $enr)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    @if($enr->course && Route::has('admin.advanced-courses.show'))
                                        <a href="{{ route('admin.advanced-courses.show', $enr->course) }}" class="font-semibold text-sky-600 hover:underline">{{ $enr->course->title }}</a>
                                    @else
                                        {{ $enr->course->title ?? '—' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">{{ $enr->course?->instructor?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs font-semibold">{{ $enr->status }}</td>
                                <td class="px-4 py-3">{{ $enr->progress !== null ? $enr->progress . '%' : '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $enr->final_price ? number_format($enr->final_price, 0) : '—' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ optional($enr->enrolled_at ?? $enr->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد تسجيلات كورسات.</p>
            @endif
        </div>
    </section>

    @if($installmentAgreements->isNotEmpty())
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">اتفاقيات التقسيط</h2>
        </div>
        <div class="overflow-x-auto">
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
                    @foreach($installmentAgreements as $agr)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $agr->id }}</td>
                            <td class="px-4 py-3">{{ number_format((float) ($agr->total_amount ?? 0), 0) }}</td>
                            <td class="px-4 py-3 text-xs">{{ $agr->status ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $agr->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- تذاكر الدعم --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-black text-slate-900">تذاكر الدعم الفني</h2>
            @if(Route::has('admin.support-tickets.index'))
                <a href="{{ route('admin.support-tickets.index') }}" class="text-xs font-bold text-sky-600 hover:underline">كل التذاكر</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            @if($supportTickets->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الموضوع</th>
                            <th class="px-4 py-3">التصنيف</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($supportTickets as $ticket)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $ticket->subject }}</td>
                                <td class="px-4 py-3 text-xs">{{ $ticket->inquiryCategory->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $ticketStatusLabels[$ticket->status] ?? $ticket->status }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ $ticket->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @if(Route::has('admin.support-tickets.show'))
                                        <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="text-xs font-bold text-sky-600">فتح</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد تذاكر دعم.</p>
            @endif
        </div>
    </section>

    {{-- الاختبارات --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">محاولات الاختبارات</h2>
        </div>
        <div class="overflow-x-auto">
            @if($examAttempts->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">الاختبار</th>
                            <th class="px-4 py-3">الدرجة</th>
                            <th class="px-4 py-3">النسبة</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($examAttempts as $attempt)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium">{{ $attempt->exam->title ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $attempt->score ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $attempt->percentage !== null ? $attempt->percentage . '%' : '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $attempt->status ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ optional($attempt->submitted_at ?? $attempt->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="p-8 text-center text-sm text-slate-500">لا توجد محاولات اختبار.</p>
            @endif
        </div>
    </section>

    {{-- الشهادات --}}
    @if($certificates->isNotEmpty())
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">الشهادات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                    <tr>
                        <th class="px-4 py-3">العنوان / الكورس</th>
                        <th class="px-4 py-3">تاريخ الإصدار</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($certificates as $cert)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $cert->title ?? $cert->course_name ?? 'شهادة #' . $cert->id }}</td>
                            <td class="px-4 py-3 text-xs">{{ optional($cert->issued_at ?? $cert->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- سجل النشاط --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-lg font-black text-slate-900">سجل النشاط (آخر 100)</h2>
        </div>
        <div class="overflow-x-auto">
            @if($activityLogs->isNotEmpty())
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-600">
                        <tr>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">الإجراء</th>
                            <th class="px-4 py-3">الوصف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($activityLogs as $log)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2 text-xs text-slate-500">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 text-xs">{{ $log->action ?? '—' }}</td>
                                <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($log->description ?? '—', 80) }}</td>
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

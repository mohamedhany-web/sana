@extends('layouts.admin')

@section('title', 'إرسال إشعار جديد')
@section('header', 'إرسال إشعار جديد')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-paper-plane text-lg"></i>
                </div>
                <div>
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.notifications.index') }}" class="text-blue-600 hover:text-blue-700">الإشعارات</a>
                        <span>/</span>
                        <span class="text-slate-600">إرسال جديد</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">إنشاء إشعار جديد</h2>
                    <p class="text-sm text-slate-600 mt-1">أرسل للطلاب أو المدربين أو الموظفين من نفس الشاشة.</p>
                </div>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-right"></i>
                العودة إلى الإشعارات
            </a>
        </div>
    </section>

    <form action="{{ route('admin.notifications.store') }}" method="POST" id="notificationForm" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600">
                                <i class="fas fa-layer-group text-lg"></i>
                            </div>
                            الجمهور
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2" id="audience-tabs">
                            @foreach(($audiences ?? []) as $audKey => $audLabel)
                                <button type="button"
                                        data-audience="{{ $audKey }}"
                                        class="audience-tab px-4 py-2 rounded-xl text-sm font-semibold border transition {{ ($selectedAudience ?? 'student') === $audKey ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                                    {{ $audLabel }}
                                </button>
                            @endforeach
                        </div>
                        <p class="mt-3 text-xs text-slate-500">اختر الشريحة ثم نوع المستهدفين بالأسفل.</p>
                    </div>
                </section>

                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-edit text-lg"></i>
                            </div>
                            محتوى الإشعار
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">اكتب النص الأساسي وحدد نوع الإشعار وأولويته.</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="title" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-heading text-blue-600 text-sm"></i>
                                عنوان الإشعار <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', '') }}" required maxlength="255" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="مثال: تذكير بالامتحان النهائي" />
                            @error('title')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="message" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-align-right text-blue-600 text-sm"></i>
                                نص الإشعار <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="message" id="message" rows="5" required maxlength="2000" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none" placeholder="اكتب تفاصيل الإشعار والنقاط المهمة...">{{ old('message', '') }}</textarea>
                            <p class="mt-1.5 text-xs text-slate-600">الحد الأقصى 2000 حرف. سيتم تنقية HTML تلقائياً.</p>
                            @error('message')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-tag text-blue-600 text-sm"></i>
                                    نوع الإشعار <span class="text-rose-500">*</span>
                                </label>
                                <select name="type" id="type" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر نوع الإشعار</option>
                                    @foreach ($notificationTypes as $key => $type)
                                        <option value="{{ htmlspecialchars($key, ENT_QUOTES, 'UTF-8') }}" {{ old('type') == $key ? 'selected' : '' }}>{{ htmlspecialchars($type, ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                                @error('type')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="priority" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-flag text-blue-600 text-sm"></i>
                                    الأولوية <span class="text-rose-500">*</span>
                                </label>
                                <select name="priority" id="priority" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر الأولوية</option>
                                    @foreach ($priorities as $key => $priority)
                                        <option value="{{ htmlspecialchars($key, ENT_QUOTES, 'UTF-8') }}" {{ old('priority', 'normal') == $key ? 'selected' : '' }}>{{ htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                                @error('priority')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="action_url" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-link text-blue-600 text-sm"></i>
                                    رابط الإجراء (اختياري)
                                </label>
                                <input type="url" name="action_url" id="action_url" value="{{ old('action_url', '') }}" maxlength="500" pattern="https?://.+" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="https://example.com/action" />
                                @error('action_url')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="action_text" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-mouse-pointer text-blue-600 text-sm"></i>
                                    نص زر الإجراء
                                </label>
                                <input type="text" name="action_text" id="action_text" value="{{ old('action_text', '') }}" maxlength="100" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="مثال: عرض التفاصيل" />
                                @error('action_text')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            تحديد الجمهور
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">اختر من سيستلم الإشعار واحصل على عدد المستهدفين المتوقع.</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="target_type" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-bullseye text-blue-600 text-sm"></i>
                                المستهدفون <span class="text-rose-500">*</span>
                            </label>
                            <select name="target_type" id="target_type" required onchange="updateTargetOptions()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">اختر المستهدفين</option>
                                @foreach ($targetTypes as $key => $type)
                                    @php
                                        $optAudience = \App\Models\Notification::audienceForTargetType($key);
                                    @endphp
                                    <option value="{{ htmlspecialchars($key, ENT_QUOTES, 'UTF-8') }}"
                                            data-audience="{{ $optAudience }}"
                                            {{ old('target_type') == $key ? 'selected' : '' }}>{{ htmlspecialchars($type, ENT_QUOTES, 'UTF-8') }}</option>
                                @endforeach
                            </select>
                            @error('target_type')<p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div id="target-options" style="display: none;" class="space-y-4">
                            <div id="course-selection" style="display: none;">
                                <label for="course_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-graduation-cap text-blue-600 text-sm"></i>
                                    اختر المسار / الكورس
                                </label>
                                <select id="course_target" name="target_id_course" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر الكورس</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ htmlspecialchars($course->title, ENT_QUOTES, 'UTF-8') }} - {{ htmlspecialchars($course->academicSubject->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="year-selection" style="display: none;">
                                <label for="year_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-route text-blue-600 text-sm"></i>
                                    اختر المسار التعليمي
                                </label>
                                <select id="year_target" name="target_id_year" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر المسار</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ htmlspecialchars($year->name, ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="subject-selection" style="display: none;">
                                <label for="subject_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-layer-group text-blue-600 text-sm"></i>
                                    اختر مجموعة المهارات
                                </label>
                                <select id="subject_target" name="target_id_subject" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر المجموعة</option>
                                    @foreach ($academicSubjects as $subject)
                                        <option value="{{ $subject->id }}">{{ htmlspecialchars($subject->name, ENT_QUOTES, 'UTF-8') }} - {{ htmlspecialchars($subject->academicYear->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="student-selection" style="display: none;">
                                <label for="student_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                    اختر طالباً محدداً
                                </label>
                                <select id="student_target" name="target_id_student" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر الطالب</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ htmlspecialchars($student->name, ENT_QUOTES, 'UTF-8') }} - {{ htmlspecialchars($student->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="instructor-selection" style="display: none;">
                                <label for="instructor_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-chalkboard-teacher text-blue-600 text-sm"></i>
                                    اختر مدرباً محدداً
                                </label>
                                <select id="instructor_target" name="target_id_instructor" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر المدرب</option>
                                    @foreach (($instructors ?? []) as $instructor)
                                        <option value="{{ $instructor->id }}">{{ htmlspecialchars($instructor->name, ENT_QUOTES, 'UTF-8') }} - {{ htmlspecialchars($instructor->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="employee-selection" style="display: none;">
                                <label for="employee_target" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-user-tie text-blue-600 text-sm"></i>
                                    اختر موظفاً محدداً
                                </label>
                                <select id="employee_target" name="target_id_employee" onchange="updateTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <option value="">اختر الموظف</option>
                                    @foreach (($employees ?? []) as $employee)
                                        <option value="{{ $employee->id }}">{{ htmlspecialchars($employee->name, ENT_QUOTES, 'UTF-8') }} - {{ htmlspecialchars($employee->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="target-count-display" style="display: none;" class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                            <span class="inline-flex items-center gap-2 font-semibold">
                                <i class="fas fa-users"></i>
                                سيتم الإرسال إلى
                                <span id="target-count" class="text-blue-700 font-bold">0</span>
                                مستلم
                            </span>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="fas fa-eye text-lg"></i>
                            </div>
                            معاينة فورية
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">تظهر المعاينة تلقائياً عند كتابة المحتوى.</p>
                    </div>
                    <div class="p-6">
                        <div id="notification-preview" class="rounded-lg border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600 min-h-[150px]">
                            <div class="text-center text-slate-400">
                                <i class="fas fa-bell text-2xl mb-3"></i>
                                <p>اكتب عنوان الإشعار ومحتواه لعرض المعاينة هنا.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-cog text-blue-600"></i>
                            إعدادات إضافية
                        </h3>
                        <p class="text-xs text-slate-600 mt-1">تحكم في موعد انتهاء الإشعار وخيارات الإرسال.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="expires_at" class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-clock text-blue-600 text-sm"></i>
                                انتهاء الصلاحية
                            </label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" min="{{ now()->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                            <p class="mt-1.5 text-xs text-slate-600">اترك الحقل فارغاً إذا كان الإشعار دائماً.</p>
                        </div>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-50 transition-colors cursor-pointer">
                            <input type="checkbox" name="send_email" value="1" {{ old('send_email', '1') === '1' ? 'checked' : '' }} class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-slate-800 font-medium">
                                أرسل أيضاً إلى البريد الإلكتروني
                                <span class="block mt-1 text-xs font-normal text-slate-600">بدون هذا الخيار يصل الإشعار داخل المنصة فقط (جرس الإشعارات)، وليس إلى الإيميل.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                        <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                            <i class="fas fa-lightbulb text-blue-600"></i>
                            نصائح سريعة
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm text-slate-600">
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <p class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-lightbulb"></i>
                                كتابة فعالة
                            </p>
                            <ul class="list-disc pr-5 space-y-1 text-xs">
                                <li>اجعل العنوان مختصراً وواضحاً.</li>
                                <li>استخدم لغة ودودة ومباشرة.</li>
                                <li>حدد الأولوية بعناية لجذب الانتباه الصحيح.</li>
                                <li>أضف رابطاً واضحاً إذا كان هناك إجراء مطلوب.</li>
                            </ul>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <p class="font-semibold text-emerald-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-bullseye"></i>
                                استهداف دقيق
                            </p>
                            <ul class="list-disc pr-5 space-y-1 text-xs">
                                <li>جميع الطلاب: يصل لكل الطلاب النشطين.</li>
                                <li>كورس محدد: يستهدف مساراً تعليمياً بعينه.</li>
                                <li>مسار أو مجموعة مهارات: يركز على فئة محددة.</li>
                                <li>طالب محدد: رسائل شخصية تحتاج متابعة خاصة.</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                    <div class="p-6 space-y-3">
                        <button type="submit" id="submitBtn" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الإشعار الآن
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            <i class="fas fa-times"></i>
                            إلغاء والعودة
                        </a>
                    </div>
                </section>
            </div>
        </div>

        <input type="hidden" name="target_id" id="target_id">
    </form>
</div>

@push('scripts')
<script>
    // حماية من Double Submit
    let formSubmitting = false;

    // حماية من XSS - تنقية البيانات
    function sanitizeInput(input) {
        if (!input) return '';
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML.replace(/[<>]/g, '');
    }

    // حماية من XSS في URLs
    function sanitizeUrl(url) {
        if (!url) return '';
        try {
            const urlObj = new URL(url);
            return urlObj.toString();
        } catch (e) {
            return '';
        }
    }

    let currentAudience = @json($selectedAudience ?? 'student');

    function applyAudienceFilter(audience) {
        currentAudience = audience;
        document.querySelectorAll('.audience-tab').forEach(btn => {
            const active = btn.getAttribute('data-audience') === audience;
            btn.classList.toggle('bg-blue-600', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('border-blue-600', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('text-slate-700', !active);
            btn.classList.toggle('border-slate-200', !active);
        });

        const targetTypeEl = document.getElementById('target_type');
        if (!targetTypeEl) return;
        Array.from(targetTypeEl.options).forEach(opt => {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }
            const show = opt.getAttribute('data-audience') === audience;
            opt.hidden = !show;
            opt.disabled = !show;
        });
        const selected = targetTypeEl.options[targetTypeEl.selectedIndex];
        if (selected && selected.disabled) {
            targetTypeEl.value = '';
        }
        updateTargetOptions();
    }

    function updateTargetOptions() {
        const targetTypeEl = document.getElementById('target_type');
        if (!targetTypeEl) return;

        const targetType = targetTypeEl.value.trim();
        const targetOptions = document.getElementById('target-options');
        const targetCountDisplay = document.getElementById('target-count-display');

        ['course-selection', 'year-selection', 'subject-selection', 'student-selection', 'instructor-selection', 'employee-selection'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });

        if (targetType && targetOptions && targetCountDisplay) {
            targetOptions.style.display = 'block';
            targetCountDisplay.style.display = 'block';

            switch (targetType) {
                case 'course_students':
                    if (document.getElementById('course-selection')) document.getElementById('course-selection').style.display = 'block';
                    break;
                case 'year_students':
                    if (document.getElementById('year-selection')) document.getElementById('year-selection').style.display = 'block';
                    break;
                case 'subject_students':
                    if (document.getElementById('subject-selection')) document.getElementById('subject-selection').style.display = 'block';
                    break;
                case 'individual':
                    if (document.getElementById('student-selection')) document.getElementById('student-selection').style.display = 'block';
                    break;
                case 'individual_instructor':
                    if (document.getElementById('instructor-selection')) document.getElementById('instructor-selection').style.display = 'block';
                    break;
                case 'individual_employee':
                    if (document.getElementById('employee-selection')) document.getElementById('employee-selection').style.display = 'block';
                    break;
                case 'all_students':
                case 'all_instructors':
                case 'all_employees':
                    targetOptions.style.display = 'none';
                    break;
            }

            updateTargetCount();
        } else if (targetOptions && targetCountDisplay) {
            targetOptions.style.display = 'none';
            targetCountDisplay.style.display = 'none';
        }
    }

    function setHiddenTargetId(value) {
        const targetIdEl = document.getElementById('target_id');
        if (targetIdEl) targetIdEl.value = value || '';
    }

    function updateTargetCount() {
        const targetTypeEl = document.getElementById('target_type');
        if (!targetTypeEl) return;

        const targetType = targetTypeEl.value.trim();
        let targetId = null;

        switch (targetType) {
            case 'course_students':
                targetId = parseInt(document.getElementById('course_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'year_students':
                targetId = parseInt(document.getElementById('year_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'subject_students':
                targetId = parseInt(document.getElementById('subject_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'individual':
                targetId = parseInt(document.getElementById('student_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'individual_instructor':
                targetId = parseInt(document.getElementById('instructor_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'individual_employee':
                targetId = parseInt(document.getElementById('employee_target')?.value) || null;
                setHiddenTargetId(targetId);
                break;
            case 'all_students':
            case 'all_instructors':
            case 'all_employees':
                setHiddenTargetId('');
                break;
        }

        if (targetType) {
            const safeTargetType = encodeURIComponent(targetType);
            const safeTargetId = targetId ? encodeURIComponent(targetId) : '';
            fetch(`{{ route('admin.notifications.target-count') }}?target_type=${safeTargetType}&target_id=${safeTargetId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const countEl = document.getElementById('target-count');
                    if (countEl) {
                        countEl.textContent = parseInt(data.count) || 0;
                    }
                })
                .catch(() => {
                    const countEl = document.getElementById('target-count');
                    if (countEl) {
                        countEl.textContent = '0';
                    }
                });
        }
    }

    document.querySelectorAll('.audience-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            applyAudienceFilter(this.getAttribute('data-audience'));
        });
    });
    applyAudienceFilter(currentAudience);

    function updatePreview() {
        const titleEl = document.getElementById('title');
        const messageEl = document.getElementById('message');
        const typeEl = document.getElementById('type');
        const priorityEl = document.getElementById('priority');
        const actionUrlEl = document.getElementById('action_url');
        const actionTextEl = document.getElementById('action_text');
        const preview = document.getElementById('notification-preview');

        if (!preview) return;

        const title = titleEl ? sanitizeInput(titleEl.value) : '';
        const message = messageEl ? sanitizeInput(messageEl.value) : '';
        const type = typeEl ? typeEl.value : '';
        const priority = priorityEl ? priorityEl.value : '';
        const actionUrl = actionUrlEl ? sanitizeUrl(actionUrlEl.value) : '';
        const actionText = actionTextEl ? sanitizeInput(actionTextEl.value) : '';

        if (!title && !message) {
            preview.innerHTML = `
                <div class="text-center text-slate-400">
                    <i class="fas fa-bell text-2xl mb-3"></i>
                    <p>اكتب محتوى الإشعار لرؤية المعاينة</p>
                </div>
            `;
            return;
        }

        const typeIcons = {
            'general': 'fas fa-info-circle',
            'course': 'fas fa-graduation-cap',
            'exam': 'fas fa-clipboard-check',
            'assignment': 'fas fa-tasks',
            'grade': 'fas fa-star',
            'announcement': 'fas fa-bullhorn',
            'reminder': 'fas fa-bell',
            'warning': 'fas fa-exclamation-triangle',
            'system': 'fas fa-cog',
        };

        const typeColors = {
            'general': 'blue',
            'course': 'emerald',
            'exam': 'violet',
            'assignment': 'amber',
            'grade': 'yellow',
            'announcement': 'rose',
            'reminder': 'blue',
            'warning': 'rose',
            'system': 'slate',
        };

        const priorityLabels = {
            'low': 'منخفضة',
            'high': 'عالية',
            'urgent': 'عاجلة',
        };

        const typeColor = typeColors[type] || 'blue';
        const icon = typeIcons[type] || 'fas fa-info-circle';
        
        let priorityBadge = '';
        if (priority && priority !== 'normal' && priorityLabels[priority]) {
            const priorityClasses = {
                'low': 'bg-slate-100 text-slate-700 border border-slate-200',
                'high': 'bg-amber-100 text-amber-700 border border-amber-200',
                'urgent': 'bg-rose-100 text-rose-700 border border-rose-200',
            };
            priorityBadge = `<span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold ${priorityClasses[priority]}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>${priorityLabels[priority]}</span>`;
        }

        const actionButton = (actionUrl && actionText) ? 
            `<div class="mt-4"><a href="${actionUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">${actionText} <i class="fas fa-external-link-alt text-[10px]"></i></a></div>` 
            : '';

        preview.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 flex-shrink-0 items-center justify-center rounded-xl bg-${typeColor}-100 text-${typeColor}-600 flex">
                    <i class="${icon} text-lg"></i>
                </div>
                <div class="flex-1 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="text-sm font-bold text-slate-900">${title || 'عنوان الإشعار'}</h4>
                        ${priorityBadge}
                    </div>
                    <p class="text-sm leading-6 text-slate-600">${message || 'نص الإشعار سيظهر هنا...'}</p>
                    ${actionButton}
                    <span class="block text-xs text-slate-400">منذ لحظات</span>
                </div>
            </div>
        `;
    }

    // منع الإرسال المتكرر
    const notificationForm = document.getElementById('notificationForm');
    if (notificationForm) {
        notificationForm.addEventListener('submit', function(e) {
            if (formSubmitting) {
                e.preventDefault();
                return false;
            }
            formSubmitting = true;
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
            }

            // Sanitization - تنقية البيانات قبل الإرسال
            const titleEl = this.querySelector('#title');
            const messageEl = this.querySelector('#message');
            const actionUrlEl = this.querySelector('#action_url');
            const actionTextEl = this.querySelector('#action_text');

            if (titleEl) titleEl.value = sanitizeInput(titleEl.value);
            if (messageEl) messageEl.value = sanitizeInput(messageEl.value);
            if (actionUrlEl && actionUrlEl.value) {
                const sanitizedUrl = sanitizeUrl(actionUrlEl.value);
                if (!sanitizedUrl && actionUrlEl.value) {
                    e.preventDefault();
                    formSubmitting = false;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الإشعار الآن';
                    }
                    alert('رابط الإجراء غير صحيح. يرجى إدخال رابط صالح.');
                    return false;
                }
                actionUrlEl.value = sanitizedUrl;
            }
            if (actionTextEl) actionTextEl.value = sanitizeInput(actionTextEl.value);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['title', 'message', 'type', 'priority', 'action_url', 'action_text'].forEach(id => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('input', updatePreview);
                field.addEventListener('change', updatePreview);
            }
        });

        ['course_target', 'year_target', 'subject_target', 'student_target'].forEach(id => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('change', updateTargetCount);
            }
        });

        updatePreview();
        updateTargetOptions();
    });
</script>
@endpush
@endsection

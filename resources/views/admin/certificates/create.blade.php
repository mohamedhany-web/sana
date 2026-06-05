@extends('layouts.admin')

@section('title', 'إصدار شهادة جديدة')
@section('header', 'إصدار شهادة جديدة')

@section('content')
@php
    $platformAutoIssue = $platformAutoIssue ?? (bool) config('certificates.platform_auto_issue', true);
    $systemIssueAvailable = $systemIssueAvailable ?? true;
    $defaultMode = old('issue_mode', $systemIssueAvailable ? 'system' : 'manual');
@endphp
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('admin.certificates.design') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 px-4 py-2.5 text-sm font-bold transition-colors w-full sm:w-auto">
            <i class="fas fa-palette text-indigo-600"></i>
            عرض تصميم شهادة المنصة (معاينة)
        </a>
    </div>

    @if($platformAutoIssue)
        <div class="rounded-2xl border border-sky-200 bg-sky-50/90 px-5 py-4 text-sm text-sky-950 leading-relaxed">
            <p class="font-bold text-sky-900 mb-2"><i class="fas fa-info-circle ml-1 text-sky-600"></i> خياران للإصدار</p>
            <ul class="list-disc pr-5 space-y-1 text-sky-900/95">
                <li><strong>شهادة النظام:</strong> نفس تصميم المنصة (PDF) يُولَّد تلقائياً بعد اختيار الطالب والكورس — يمكنك تعديل العنوان/الوصف ومعاينة الشكل قبل الحفظ.</li>
                <li><strong>رفع PDF يدوي:</strong> عندما تريد ملفاً مُصمماً خارج المنصة.</li>
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">إصدار شهادة جديدة</h1>
        <p class="text-sm text-gray-500 mb-6">اختر نوع الإصدار، ثم الطالب والكورس. بيانات العنوان والوصف تُقترح تلقائياً في وضع «شهادة النظام».</p>

        <form id="certificate-create-form" action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 space-y-3">
                <span class="block text-sm font-bold text-slate-800">نوع الإصدار *</span>
                @if($systemIssueAvailable)
                    <div class="flex flex-col sm:flex-row gap-3">
                        <label class="flex items-center gap-3 cursor-pointer rounded-lg border-2 px-4 py-3 transition-colors cert-mode-opt"
                               data-mode="system">
                            <input type="radio" name="issue_mode" value="system" class="text-indigo-600 focus:ring-indigo-500"
                                   {{ $defaultMode === 'system' ? 'checked' : '' }}>
                            <span>
                                <span class="font-bold text-slate-900 block">شهادة النظام (تصميم المنصة)</span>
                                <span class="text-xs text-slate-600">توليد PDF تلقائياً — نفس القالب المعروض في صفحة المعاينة</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer rounded-lg border-2 px-4 py-3 transition-colors cert-mode-opt"
                               data-mode="manual">
                            <input type="radio" name="issue_mode" value="manual" class="text-indigo-600 focus:ring-indigo-500"
                                   {{ $defaultMode === 'manual' ? 'checked' : '' }}>
                            <span>
                                <span class="font-bold text-slate-900 block">رفع PDF يدوي</span>
                                <span class="text-xs text-slate-600">تحميل ملف PDF أنت تجهّزه</span>
                            </span>
                        </label>
                    </div>
                @else
                    <input type="hidden" name="issue_mode" value="manual">
                    <p class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        شهادة النظام غير متاحة مع إعداد الجداول الحالي (ربط course_id بجدول آخر). استخدم رفع PDF يدوي فقط.
                    </p>
                @endif
            </div>

            @if($systemIssueAvailable)
                <div id="system-actions" class="flex flex-wrap gap-2 {{ $defaultMode === 'system' ? '' : 'hidden' }}">
                    <button type="button" id="btn-preview-draft"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-eye text-xs"></i>
                        معاينة بالطالب والكورس المختارين
                    </button>
                    <span class="text-xs text-slate-500 self-center">تفتح في تبويب جديد مع علامة «معاينة».</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب *</label>
                    <select id="certificate-user" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الطالب</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (string) old('user_id') === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} — {{ $user->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select id="certificate-course" name="course_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-2">بعد اختيار الطالب تُحدَّث قائمة الكورسات حسب تسجيلاته إن وُجدت.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        العنوان
                        <span id="title-hint-manual" class="text-rose-600 text-xs font-bold {{ $defaultMode === 'manual' ? '' : 'hidden' }}">(مطلوب للرفع اليدوي)</span>
                    </label>
                    <input type="text" name="title" id="field-title" value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="يُقترح تلقائياً في وضع شهادة النظام">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإصدار</label>
                    <input type="date" name="issued_at" value="{{ old('issued_at', date('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="issued" {{ old('status', 'issued') == 'issued' ? 'selected' : '' }}>مُصدرة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" id="field-description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="يُقترح تلقائياً في وضع شهادة النظام">{{ old('description') }}</textarea>
            </div>

            <div id="manual-file-block" class="{{ $defaultMode === 'manual' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">ملف الشهادة (PDF) <span class="text-rose-600">*</span></label>
                <input type="file" name="certificate_file" id="certificate-file" accept=".pdf,application/pdf"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                @error('certificate_file')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">الحد وفق إعدادات الرفع في المنصة.</p>
            </div>

            @error('issue_mode')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('course_id')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-wrap gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إصدار الشهادة
                </button>
                <a href="{{ route('admin.certificates.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const userSelect = document.getElementById('certificate-user');
    const courseSelect = document.getElementById('certificate-course');
    const systemIssueAvailable = @json($systemIssueAvailable);
    const prefillUrl = @json(route('admin.certificates.prefill-data'));
    const previewDraftUrl = @json(route('admin.certificates.preview-draft'));
    const modeRadios = document.querySelectorAll('input[name="issue_mode"]');
    const manualFileBlock = document.getElementById('manual-file-block');
    const certificateFile = document.getElementById('certificate-file');
    const systemActions = document.getElementById('system-actions');
    const btnPreviewDraft = document.getElementById('btn-preview-draft');
    const titleInput = document.getElementById('field-title');
    const descInput = document.getElementById('field-description');
    const titleHintManual = document.getElementById('title-hint-manual');

    if (!userSelect || !courseSelect) return;

    const allCourseOptions = Array.from(courseSelect.querySelectorAll('option')).map(function (o) {
        return { value: o.value, text: o.textContent };
    });

    function currentMode() {
        const c = document.querySelector('input[name="issue_mode"]:checked');
        return c ? c.value : 'manual';
    }

    function setCourseOptions(options) {
        courseSelect.innerHTML = '';
        options.forEach(function (opt) {
            const el = document.createElement('option');
            el.value = opt.value;
            el.textContent = opt.text;
            courseSelect.appendChild(el);
        });
    }

    function toggleModeUi() {
        const mode = currentMode();
        if (manualFileBlock) manualFileBlock.classList.toggle('hidden', mode !== 'manual');
        if (certificateFile) {
            certificateFile.required = (mode === 'manual');
            if (mode !== 'manual') certificateFile.value = '';
        }
        if (systemActions) systemActions.classList.toggle('hidden', mode !== 'system' || !systemIssueAvailable);
        if (titleInput) titleInput.required = (mode === 'manual');
        if (titleHintManual) titleHintManual.classList.toggle('hidden', mode !== 'manual');
        document.querySelectorAll('.cert-mode-opt').forEach(function (el) {
            const active = el.getAttribute('data-mode') === mode;
            el.classList.toggle('border-indigo-500', active);
            el.classList.toggle('bg-white', active);
            el.classList.toggle('border-slate-200', !active);
        });
    }

    async function loadUserCourses(userId) {
        setCourseOptions([{ value: '', text: 'اختر الكورس' }]);
        if (!userId) return;
        try {
            const res = await fetch('{{ url('/admin/certificates/user') }}/' + encodeURIComponent(userId) + '/courses', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('fail');
            const data = await res.json();
            const courses = Array.isArray(data.courses) ? data.courses : [];
            if (courses.length > 0) {
                setCourseOptions([{ value: '', text: 'اختر الكورس' }].concat(
                    courses.map(function (c) { return { value: String(c.id), text: c.title }; })
                ));
            } else {
                setCourseOptions(allCourseOptions);
            }
        } catch (e) {
            setCourseOptions(allCourseOptions);
        }
    }

    async function maybePrefill() {
        if (!systemIssueAvailable || currentMode() !== 'system') return;
        const uid = userSelect.value;
        const cid = courseSelect.value;
        if (!uid || !cid) return;
        try {
            const url = prefillUrl + '?user_id=' + encodeURIComponent(uid) + '&course_id=' + encodeURIComponent(cid);
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            if (data.title && titleInput) titleInput.value = data.title;
            if (data.description && descInput) descInput.value = data.description;
        } catch (e) {}
    }

    userSelect.addEventListener('change', function () {
        loadUserCourses(userSelect.value).then(maybePrefill);
    });
    courseSelect.addEventListener('change', maybePrefill);

    modeRadios.forEach(function (r) {
        r.addEventListener('change', toggleModeUi);
    });

    if (btnPreviewDraft) {
        btnPreviewDraft.addEventListener('click', function () {
            const uid = userSelect.value;
            const cid = courseSelect.value;
            if (!uid || !cid) {
                alert('اختر الطالب والكورس أولاً.');
                return;
            }
            const url = previewDraftUrl + '?user_id=' + encodeURIComponent(uid) + '&course_id=' + encodeURIComponent(cid);
            window.open(url, '_blank', 'noopener');
        });
    }

    toggleModeUi();
    @if(old('user_id'))
        loadUserCourses(String(@json(old('user_id')))).then(function () {
            @if(old('course_id'))
                courseSelect.value = @json(old('course_id'));
            @endif
            maybePrefill();
        });
    @endif
})();
</script>
@endpush

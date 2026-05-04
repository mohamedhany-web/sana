@extends('layouts.admin')

@section('title', 'تعديل الشهادة')
@section('header', 'تعديل الشهادة')

@section('content')
@php
    $platformAutoIssue = $platformAutoIssue ?? (bool) config('certificates.platform_auto_issue', true);
    $meta = is_array($certificate->metadata ?? null) ? $certificate->metadata : [];
    $isPlatformCert = ($certificate->template ?? '') === 'platform_academic' || (($meta['source'] ?? '') === 'platform_auto');
@endphp
<div class="space-y-6">
    @if($isPlatformCert)
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50/90 px-5 py-4 text-sm text-indigo-950">
            <p class="font-bold text-indigo-900 mb-1"><i class="fas fa-info-circle text-indigo-600 ml-1"></i> شهادة المنصة التلقائية</p>
            <p>هذه الشهادة أُنشئت آلياً بعد إتمام الكورس. إذا رفعت ملف PDF جديداً هنا، سيُستبدل ملف المنصة للطالب.</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">تعديل الشهادة</h1>
        
        <form action="{{ route('admin.certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب *</label>
                    <select id="certificate-user" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $certificate->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select id="certificate-course" name="course_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $certificate->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" required value="{{ old('title', $certificate->title ?? $certificate->course_name ?? '') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإصدار</label>
                    <input type="date" name="issued_at" value="{{ old('issued_at', $certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : date('Y-m-d'))) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @php
                            $currentStatus = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                        @endphp
                        <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="issued" {{ $currentStatus == 'issued' ? 'selected' : '' }}>مُصدرة</option>
                        <option value="revoked" {{ $currentStatus == 'revoked' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description', $certificate->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملف الشهادة (PDF فقط)</label>
                @if(!empty($certificate->pdf_path))
                    <div class="mb-3 text-sm">
                        <span class="text-gray-500">ملف حالي:</span>
                        <a class="text-sky-600 hover:underline font-semibold"
                           href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($certificate->pdf_path) }}"
                           target="_blank" rel="noopener">عرض الملف</a>
                    </div>
                @endif
                <input type="file" name="certificate_file" accept=".pdf,application/pdf"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                @error('certificate_file')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">اتركه فارغًا إن لم تغيّر الملف. PDF فقط — حتى 40 ميجابايت.</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    تحديث الشهادة
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
        if (!userSelect || !courseSelect) return;

        const currentCourseId = @json((string) $certificate->course_id);
        const allCourseOptions = Array.from(courseSelect.querySelectorAll('option')).map(o => ({
            value: o.value,
            text: o.textContent,
            selected: o.value === currentCourseId
        }));

        function setCourseOptions(options) {
            courseSelect.innerHTML = '';
            for (const opt of options) {
                const el = document.createElement('option');
                el.value = opt.value;
                el.textContent = opt.text;
                if (opt.selected) el.selected = true;
                courseSelect.appendChild(el);
            }
        }

        async function loadUserCourses(userId) {
            setCourseOptions([{ value: '', text: 'اختر الكورس', selected: false }]);
            if (!userId) return;

            try {
                const res = await fetch(`{{ url('/admin/certificates/user') }}/${encodeURIComponent(userId)}/courses`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed');
                const data = await res.json();
                const courses = Array.isArray(data.courses) ? data.courses : [];

                if (courses.length > 0) {
                    const opts = [{ value: '', text: 'اختر الكورس', selected: false }].concat(
                        courses.map(c => ({
                            value: String(c.id),
                            text: c.title,
                            selected: String(c.id) === currentCourseId
                        }))
                    );
                    setCourseOptions(opts);
                } else {
                    setCourseOptions(allCourseOptions.map(o => ({
                        ...o,
                        selected: o.value === currentCourseId
                    })));
                }
            } catch (e) {
                setCourseOptions(allCourseOptions.map(o => ({
                    ...o,
                    selected: o.value === currentCourseId
                })));
            }
        }

        userSelect.addEventListener('change', function () {
            loadUserCourses(userSelect.value);
        });
    })();
</script>
@endpush

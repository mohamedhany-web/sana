@extends('layouts.admin')

@section('title', __('admin.enroll_student_in_course'))
@section('header', __('admin.enroll_student_in_course'))

@section('content')
<div class="space-y-6">
    {{-- مسار التنقل --}}
    <nav class="text-sm text-slate-500 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.online-enrollments.index') }}" class="hover:text-sky-600 font-medium">
            {{ __('admin.online_enrollments') }}
        </a>
        <i class="fas fa-chevron-left text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">{{ __('admin.enroll_student_in_course') }}</span>
    </nav>

    @if($errors->any())
        <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> تحقق من الحقول</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-l from-sky-50 to-white border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-600 to-cyan-600 flex items-center justify-center text-white shadow-md shadow-sky-500/25">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">{{ __('admin.enroll_student_in_course') }}</h2>
                    <p class="text-sm text-slate-600 mt-0.5">اختر الطالب والكورس، ثم حدّد حالة التسجيل</p>
                </div>
            </div>
            <a href="{{ route('admin.online-enrollments.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>

        <form method="POST" action="{{ route('admin.online-enrollments.store') }}" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_id" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        الطالب <span class="text-rose-500">*</span>
                    </label>
                    <input id="studentSearchInput" type="text"
                           placeholder="بحث بالاسم أو الهاتف داخل القائمة"
                           class="w-full mb-2 rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500">
                    <select name="user_id" id="user_id" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">اختر الطالب</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                    {{ (old('user_id', request('student_id')) == $student->id) ? 'selected' : '' }}
                                    data-phone="{{ $student->phone }}">
                                {{ $student->name }} — {{ $student->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="advanced_course_id" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        الكورس <span class="text-rose-500">*</span>
                    </label>
                    <input id="courseSearchInput" type="text"
                           placeholder="بحث باسم الكورس داخل القائمة"
                           class="w-full mb-2 rounded-xl border border-slate-200 px-3 py-2 text-xs focus:ring-2 focus:ring-sky-500">
                    <select name="advanced_course_id" id="advanced_course_id" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">اختر الكورس</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }} — {{ $course->academicYear->name ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                    @error('advanced_course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        حالة التسجيل <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                        <option value="">اختر الحالة</option>
                        <option value="pending" @selected(old('status') === 'pending')>في الانتظار</option>
                        <option value="active" @selected(old('status', 'active') === 'active')>نشط</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    <p class="mt-1.5 text-xs text-slate-500">
                        «نشط» يفتح الكورس فوراً ويرسل بريد التفعيل إن وُجد بريد للطالب.
                    </p>
                </div>

                <div id="final_price_wrap" class="{{ old('status', 'active') !== 'active' ? 'hidden' : '' }}">
                    <label for="final_price" class="block text-xs font-semibold text-slate-600 mb-1.5">
                        مبلغ التفعيل (ج.م) <span class="text-slate-400 font-normal">اختياري</span>
                    </label>
                    <input type="number" name="final_price" id="final_price" value="{{ old('final_price') }}" min="0" step="0.01"
                           placeholder="اتركه فارغاً لاستخدام سعر الكورس"
                           class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                    @error('final_price')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-600 mb-1.5">ملاحظات إدارية</label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="اختياري"
                          class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div id="studentInfo" class="hidden rounded-xl bg-sky-50 border border-sky-200 p-4">
                <p class="text-xs font-bold text-sky-900 mb-2">الطالب المختار</p>
                <div id="studentDetails" class="text-sm text-sky-800"></div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <p class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <i class="fas fa-mobile-alt text-sky-600"></i>
                    بحث سريع بالهاتف
                </p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="quickPhoneSearch" placeholder="رقم هاتف الطالب..."
                           class="flex-1 rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-500">
                    <button type="button" onclick="searchByPhone()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
                <div id="phoneSearchResult" class="mt-3 hidden"></div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('admin.online-enrollments.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                    <i class="fas fa-times"></i> إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 shadow-sm">
                    <i class="fas fa-check"></i>
                    {{ __('admin.enroll_student_in_course') }}
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script>
document.getElementById('status')?.addEventListener('change', function () {
    document.getElementById('final_price_wrap')?.classList.toggle('hidden', this.value !== 'active');
});

document.getElementById('user_id')?.addEventListener('change', function () {
    const studentInfo = document.getElementById('studentInfo');
    const studentDetails = document.getElementById('studentDetails');
    if (!studentInfo || !studentDetails) return;
    if (this.value) {
        const opt = this.options[this.selectedIndex];
        const phone = opt.getAttribute('data-phone') || '—';
        const name = opt.text.split(' — ')[0];
        studentDetails.innerHTML = `<p><span class="text-slate-500">الاسم:</span> <strong>${name}</strong></p><p class="mt-1"><span class="text-slate-500">الهاتف:</span> ${phone}</p>`;
        studentInfo.classList.remove('hidden');
    } else {
        studentInfo.classList.add('hidden');
    }
});

function searchByPhone() {
    const phone = document.getElementById('quickPhoneSearch')?.value?.trim();
    const resultDiv = document.getElementById('phoneSearchResult');
    if (!phone) { alert('يرجى إدخال رقم الهاتف'); return; }
    resultDiv.innerHTML = '<p class="text-center text-sm text-sky-600 py-2"><i class="fas fa-spinner fa-spin"></i> جاري البحث...</p>';
    resultDiv.classList.remove('hidden');

    fetch(`{{ route('admin.online-enrollments.search-by-phone') }}?phone=${encodeURIComponent(phone)}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.student) {
            const sel = document.getElementById('user_id');
            sel.value = data.student.id;
            sel.dispatchEvent(new Event('change'));
            resultDiv.innerHTML = '<div class="rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-800"><i class="fas fa-check-circle"></i> تم اختيار الطالب</div>';
            setTimeout(() => resultDiv.classList.add('hidden'), 3000);
        } else {
            resultDiv.innerHTML = `<div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm text-rose-800">${data.error || 'لم يُعثر على الطالب'}</div>`;
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm text-rose-800">حدث خطأ في البحث</div>';
    });
}

document.getElementById('quickPhoneSearch')?.addEventListener('keypress', e => {
    if (e.key === 'Enter') { e.preventDefault(); searchByPhone(); }
});

document.addEventListener('DOMContentLoaded', function () {
    const userSelect = document.getElementById('user_id');
    if (userSelect?.value) userSelect.dispatchEvent(new Event('change'));

    function filterSelect(selectEl, inputEl) {
        if (!selectEl || !inputEl) return;
        inputEl.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            Array.from(selectEl.options).forEach((opt, i) => {
                if (i === 0) return;
                opt.hidden = q && !opt.text.toLowerCase().includes(q);
            });
        });
    }
    filterSelect(userSelect, document.getElementById('studentSearchInput'));
    filterSelect(document.getElementById('advanced_course_id'), document.getElementById('courseSearchInput'));
});
</script>
@endpush
@endsection

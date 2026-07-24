@extends('layouts.admin')
@section('title', 'حجز من الإدارة')
@section('header', 'حجز طلاب للمعلمين')
@section('content')
@php
    $offersJson = $groupOffers->map(fn ($o) => [
        'id' => $o->id,
        'instructor_id' => $o->instructor_id,
        'title' => $o->title,
        'max_group_size' => (int) $o->max_group_size,
        'duration_minutes' => (int) ($o->duration_minutes ?: 60),
        'subject_id' => $o->academic_subject_id,
        'label' => $o->title.' — '.($o->instructor?->name ?? '').' (حد '.((int) $o->max_group_size).')',
    ])->values();
@endphp
<div class="space-y-6" x-data="adminBookForm(@js($offersJson))">
    @include('admin.tutor-lessons._nav')

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc pr-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('admin.tutor-lessons.book.store') }}" class="space-y-6" @submit="onSubmit">
        @csrf

        {{-- نوع الحجز --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-900 mb-1">نوع الحجز</h2>
            <p class="text-sm text-slate-500 mb-4">فردي لطالب واحد، أو مجموعة لعدة طلاب في نفس الموعد مع المعلم.</p>
            <div class="grid sm:grid-cols-2 gap-3">
                <label class="cursor-pointer rounded-xl border-2 p-4 transition"
                       :class="sessionType === 'one_to_one' ? 'border-violet-600 bg-violet-50' : 'border-slate-200 hover:border-violet-300'">
                    <input type="radio" name="session_type" value="one_to_one" class="sr-only" x-model="sessionType" @change="onSessionTypeChange">
                    <div class="font-bold text-slate-900"><i class="fas fa-user ml-2 text-violet-600"></i> فردي</div>
                    <div class="text-xs text-slate-500 mt-1">طالب واحد مع المعلم</div>
                </label>
                <label class="cursor-pointer rounded-xl border-2 p-4 transition"
                       :class="sessionType === 'small_group' ? 'border-violet-600 bg-violet-50' : 'border-slate-200 hover:border-violet-300'">
                    <input type="radio" name="session_type" value="small_group" class="sr-only" x-model="sessionType" @change="onSessionTypeChange">
                    <div class="font-bold text-slate-900"><i class="fas fa-users ml-2 text-violet-600"></i> مجموعة</div>
                    <div class="text-xs text-slate-500 mt-1">عدة طلاب — نفس الموعد ونفس Classroom</div>
                </label>
            </div>
        </section>

        {{-- الطرفان --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-1">المعلم والطلاب</h2>
                <p class="text-sm text-slate-500">اختر معلماً مفعّلاً لحصص خاصة، ثم أضف الطلاب.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">المعلم</label>
                <select name="instructor_id" x-model="instructorId" required
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500">
                    <option value="">— اختر المعلم —</option>
                    @foreach ($instructors as $ip)
                        <option value="{{ $ip->user_id }}" @selected((string) old('instructor_id') === (string) $ip->user_id)>
                            {{ $ip->user?->name }} @if($ip->user?->email) ({{ $ip->user->email }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">
                    الطلاب
                    <span class="font-normal text-slate-400" x-text="sessionType === 'one_to_one' ? '(طالب واحد)' : '(واحد أو أكثر)'"></span>
                </label>
                <div class="relative">
                    <input type="search" x-model="studentQuery" @input.debounce.300ms="searchStudents"
                           placeholder="ابحث بالاسم أو البريد أو الجوال…"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500">
                    <div x-show="searchResults.length" x-cloak
                         class="absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-56 overflow-auto">
                        <template x-for="s in searchResults" :key="s.id">
                            <button type="button" @click="addStudent(s)"
                                    class="w-full text-right px-3 py-2 text-sm hover:bg-violet-50 border-b border-slate-100 last:border-0">
                                <span class="font-bold" x-text="s.name"></span>
                                <span class="text-slate-400 text-xs block" x-text="(s.email || '') + (s.phone ? ' · ' + s.phone : '')"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2" x-show="selectedStudents.length">
                    <template x-for="s in selectedStudents" :key="s.id">
                        <span class="inline-flex items-center gap-2 bg-violet-100 text-violet-900 rounded-full px-3 py-1 text-sm font-bold">
                            <span x-text="s.name"></span>
                            <button type="button" @click="removeStudent(s.id)" class="text-violet-600 hover:text-red-600" title="إزالة">&times;</button>
                            <input type="hidden" name="student_ids[]" :value="s.id">
                        </span>
                    </template>
                </div>
                <p class="text-xs text-slate-400 mt-2" x-show="!selectedStudents.length">لم يُختر أي طالب بعد.</p>

                {{-- قائمة سريعة من أول الطلاب --}}
                <details class="mt-3 text-sm">
                    <summary class="cursor-pointer text-violet-700 font-bold">اختيار سريع من القائمة</summary>
                    <div class="mt-2 max-h-40 overflow-auto border rounded-xl divide-y">
                        @foreach ($students->take(80) as $st)
                            <button type="button"
                                    @click="addStudent({id: {{ $st->id }}, name: @js($st->name), email: @js($st->email), phone: @js($st->phone)})"
                                    class="w-full text-right px-3 py-2 hover:bg-slate-50">
                                {{ $st->name }}
                                <span class="text-xs text-slate-400">{{ $st->email }}</span>
                            </button>
                        @endforeach
                    </div>
                </details>
            </div>
        </section>

        {{-- الجلسة --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-1">تفاصيل الجلسة</h2>
                <p class="text-sm text-slate-500">المادة والموعد والمدة.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">المادة</label>
                    <select name="academic_subject_id" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm">
                        <option value="">— اختياري —</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) old('academic_subject_id') === (string) $subject->id)>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">المدة (دقيقة)</label>
                    <input type="number" name="duration_minutes" x-model.number="durationMinutes" min="15" max="240" step="15" required
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm" value="{{ old('duration_minutes', 60) }}">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4" x-show="sessionType === 'small_group'" x-cloak>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">عرض مجموعة (اختياري)</label>
                    <select name="tutor_group_offer_id" x-model="offerId" @change="applyOffer"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm">
                        <option value="">— بدون عرض / حد يدوي —</option>
                        <template x-for="o in filteredOffers" :key="o.id">
                            <option :value="o.id" x-text="o.label"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">الحد الأقصى للمجموعة</label>
                    <input type="number" name="max_group_size" x-model.number="maxGroupSize" min="2" max="30"
                           class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">الموعد</label>
                <input type="datetime-local" name="scheduled_at" required
                       value="{{ old('scheduled_at') }}"
                       class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">ملاحظات للطالب / المعلم</label>
                    <textarea name="student_notes" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm" placeholder="اختياري">{{ old('student_notes') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">ملاحظات إدارية للمعلم</label>
                    <textarea name="instructor_notes" rows="2" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm" placeholder="اختياري">{{ old('instructor_notes') }}</textarea>
                </div>
            </div>
        </section>

        {{-- التنفيذ --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 mb-1">التنفيذ</h2>
                <p class="text-sm text-slate-500">كيف يُنشأ الحجز بعد الإرسال.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
                <label class="cursor-pointer rounded-xl border-2 p-4 transition"
                       :class="confirmationMode === 'await_instructor' ? 'border-amber-500 bg-amber-50' : 'border-slate-200'">
                    <input type="radio" name="confirmation_mode" value="await_instructor" class="sr-only" x-model="confirmationMode">
                    <div class="font-bold text-slate-900">بانتظار تأكيد المعلم</div>
                    <div class="text-xs text-slate-500 mt-1">الحجز pending حتى يؤكد المعلم وينشئ الغرفة</div>
                </label>
                <label class="cursor-pointer rounded-xl border-2 p-4 transition"
                       :class="confirmationMode === 'confirm_now' ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200'">
                    <input type="radio" name="confirmation_mode" value="confirm_now" class="sr-only" x-model="confirmationMode">
                    <div class="font-bold text-slate-900">تأكيد الآن + Classroom</div>
                    <div class="text-xs text-slate-500 mt-1">تأكيد فوري وغرفة واحدة للمجموعة إن وُجدت</div>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 text-sm">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="enforce_quota" value="0">
                    <input type="checkbox" name="enforce_quota" value="1" checked class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                    <span>التحقق من رصيد ساعات الطلاب</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="ignore_availability_window" value="0">
                    <input type="checkbox" name="ignore_availability_window" value="1" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                    <span>السماح خارج نوافذ توفر المعلم</span>
                </label>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-700">
                <strong>الملخص:</strong>
                <span x-text="sessionType === 'one_to_one' ? 'فردي' : 'مجموعة'"></span>
                —
                <span x-text="selectedStudents.length"></span> طالب
                <template x-if="sessionType === 'small_group'">
                    <span> / حد <span x-text="maxGroupSize"></span></span>
                </template>
                —
                <span x-text="confirmationMode === 'confirm_now' ? 'تأكيد فوري' : 'بانتظار المعلم'"></span>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-sm shadow-sm disabled:opacity-50"
                        :disabled="submitting || selectedStudents.length === 0 || !instructorId">
                    <i class="fas fa-calendar-check"></i>
                    <span x-text="submitting ? 'جاري الإنشاء…' : 'إنشاء الحجز'"></span>
                </button>
                <a href="{{ route('admin.tutor-lessons.bookings') }}" class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50">
                    إلغاء
                </a>
            </div>
        </section>
    </form>
</div>

<script>
function adminBookForm(offers) {
    return {
        sessionType: @js(old('session_type', 'one_to_one')),
        confirmationMode: @js(old('confirmation_mode', 'await_instructor')),
        instructorId: @js(old('instructor_id', '')),
        offerId: @js(old('tutor_group_offer_id', '')),
        maxGroupSize: {{ (int) old('max_group_size', 5) }},
        durationMinutes: {{ (int) old('duration_minutes', 60) }},
        offers: offers || [],
        selectedStudents: [],
        studentQuery: '',
        searchResults: [],
        submitting: false,
        searchUrl: @js(route('admin.tutor-lessons.book.students.search')),
        get filteredOffers() {
            const iid = String(this.instructorId || '');
            if (!iid) return this.offers;
            return this.offers.filter(o => String(o.instructor_id) === iid);
        },
        onSessionTypeChange() {
            if (this.sessionType === 'one_to_one' && this.selectedStudents.length > 1) {
                this.selectedStudents = this.selectedStudents.slice(0, 1);
            }
        },
        applyOffer() {
            const o = this.offers.find(x => String(x.id) === String(this.offerId));
            if (!o) return;
            this.maxGroupSize = o.max_group_size || 5;
            if (o.duration_minutes) this.durationMinutes = o.duration_minutes;
        },
        addStudent(s) {
            if (!s || !s.id) return;
            if (this.selectedStudents.some(x => x.id === s.id)) return;
            if (this.sessionType === 'one_to_one') {
                this.selectedStudents = [s];
            } else {
                if (this.selectedStudents.length >= this.maxGroupSize) {
                    alert('تم بلوغ الحد الأقصى للمجموعة (' + this.maxGroupSize + ').');
                    return;
                }
                this.selectedStudents.push(s);
            }
            this.studentQuery = '';
            this.searchResults = [];
        },
        removeStudent(id) {
            this.selectedStudents = this.selectedStudents.filter(s => s.id !== id);
        },
        async searchStudents() {
            const q = (this.studentQuery || '').trim();
            if (q.length < 2) {
                this.searchResults = [];
                return;
            }
            try {
                const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.searchResults = await res.json();
            } catch (e) {
                this.searchResults = [];
            }
        },
        onSubmit(e) {
            if (!this.instructorId || this.selectedStudents.length === 0) {
                e.preventDefault();
                alert('اختر المعلم وطالباً واحداً على الأقل.');
                return;
            }
            if (this.sessionType === 'one_to_one' && this.selectedStudents.length !== 1) {
                e.preventDefault();
                alert('الحجز الفردي يتطلب طالباً واحداً فقط.');
                return;
            }
            this.submitting = true;
        }
    }
}
</script>
@endsection

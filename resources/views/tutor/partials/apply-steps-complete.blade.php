@php
    $formOptions = $formOptions ?? config('tutor_application');
    $oldWeekly = old('weekly_availability', []);
    $oldSpecs = old('specializations', []);
    $oldCurricula = old('curricula', []);
    $oldStages = old('stages', []);
    $oldFormats = old('lesson_formats', []);
    $oldTech = old('tech_skills', []);
    $oldSubjects = array_map('intval', (array) old('subject_ids', []));
    $oldYears = array_map('intval', (array) old('academic_year_ids', []));
    $oldCommitments = old('commitments', []);
    $videoMaxMb = \App\Services\TutorApplicationFormService::videoMaxMb();
    $prefill = $prefill ?? [];
    $completeSteps = [
        1 => 'المؤهل والخبرة',
        2 => 'التخصصات والمواد',
        3 => 'التوفر والمهارات',
        4 => 'الفيديو والمستندات',
        5 => 'أسئلة التقييم',
        6 => 'الإقرار والإرسال',
    ];
    $completeTotal = count($completeSteps);
@endphp

{{-- مراحل إكمال الملف بعد التسجيل — JS عادي بدون Alpine/x-cloak --}}
<div id="tutor-complete-start" class="space-y-5" data-complete-wizard="1" data-initial-step="{{ (int) ($completeResumeStep ?? 1) }}">
    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex items-center justify-between gap-3 mb-2">
            <p class="text-sm font-bold text-slate-800 m-0">
                المرحلة <span id="tc-step-num">1</span> من {{ $completeTotal }}
                — <span id="tc-step-title">{{ $completeSteps[1] }}</span>
            </p>
            <p class="text-xs font-bold text-slate-500 m-0"><span id="tc-step-pct">0</span>%</p>
        </div>
        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
            <div id="tc-step-bar" class="h-full rounded-full transition-all" style="width:0%;background:var(--edu-primary)"></div>
        </div>
        <div class="flex flex-wrap gap-1.5 mt-3">
            @foreach($completeSteps as $n => $label)
                <button type="button" class="tc-dot text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 text-slate-500 bg-slate-50"
                        data-tc-goto="{{ $n }}" title="{{ $label }}">{{ $n }}</button>
            @endforeach
        </div>
    </div>

    <p id="tc-step-error" class="ta-step-error" style="display:none"></p>

    {{-- 1 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="1" style="display:block">
        <h2 class="ta-headline" style="font-size:1.5rem">١. المؤهل والخبرة</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="ta-label">الاسم الكامل *</label>
                <input type="text" name="name" class="ta-field" required value="{{ old('name', $prefill['name'] ?? '') }}">
                @if(!empty($prefill['email']))
                    <p class="text-xs text-slate-500 mt-1.5 mb-0">البريد: <span dir="ltr">{{ $prefill['email'] }}</span> (يوزر الدخول)</p>
                @endif
            </div>
            <div><label class="ta-label">المؤهل الدراسي *</label><input type="text" name="degree_qualification" class="ta-field" required value="{{ old('degree_qualification') }}"></div>
            <div><label class="ta-label">التخصص *</label><input type="text" name="specialization" class="ta-field" required value="{{ old('specialization') }}"></div>
            <div><label class="ta-label">سنوات الخبرة *</label><input type="number" name="years_experience" class="ta-field" min="0" max="50" required value="{{ old('years_experience', 1) }}"></div>
            <div><label class="ta-label">آخر جهة عمل *</label><input type="text" name="last_workplace" class="ta-field" required value="{{ old('last_workplace') }}"></div>
            <div class="sm:col-span-2"><label class="ta-label">المراحل التي درستها *</label><textarea name="grades_taught" class="ta-field ta-textarea" rows="2" required>{{ old('grades_taught') }}</textarea></div>
            <div class="sm:col-span-2"><label class="ta-label">المناهج التي لديك خبرة بها *</label><textarea name="curricula_experience_text" class="ta-field ta-textarea" rows="2" required>{{ old('curricula_experience_text') }}</textarea></div>
            <div class="sm:col-span-2"><label class="ta-label">عنوان مختصر للملف *</label><input type="text" name="headline" class="ta-field" required placeholder="مثال: معلّم رياضيات — ثانوي" value="{{ old('headline') }}"></div>
            <div class="sm:col-span-2"><label class="ta-label">نبذة مختصرة *</label><textarea name="bio" class="ta-field ta-textarea" required maxlength="5000">{{ old('bio') }}</textarea></div>
        </div>
        <div class="ta-actions justify-end">
            <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
        </div>
    </section>

    {{-- 2 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="2" style="display:none">
        <h2 class="ta-headline" style="font-size:1.5rem">٢. التخصصات والمناهج ونوع الحصص</h2>
        <p class="ta-label">التخصصات المطلوبة *</p>
        <div class="ta-check-grid mb-3" style="max-height:none" data-tc-group="specializations[]">
            @foreach($formOptions['specializations'] ?? [] as $key => $label)
            <label class="ta-check-item"><input type="checkbox" name="specializations[]" value="{{ $key }}" @checked(in_array($key, $oldSpecs, true))> {{ $label }}</label>
            @endforeach
        </div>
        <label class="ta-label">تخصصات أخرى (حدّد إن اخترت «أخرى»)</label>
        <input type="text" name="specializations_other" class="ta-field mb-4" placeholder="إن لم ينطبق اكتب «لا يوجد»" value="{{ old('specializations_other') }}">

        <p class="ta-label">المناهج *</p>
        <div class="ta-check-grid mb-4" style="max-height:none" data-tc-group="curricula[]">
            @foreach($formOptions['curricula'] ?? [] as $key => $label)
            <label class="ta-check-item"><input type="checkbox" name="curricula[]" value="{{ $key }}" @checked(in_array($key, $oldCurricula, true))> {{ $label }}</label>
            @endforeach
        </div>

        <p class="ta-label">المراحل *</p>
        <div class="ta-check-grid mb-4" style="max-height:none" data-tc-group="stages[]">
            @foreach($formOptions['stages'] ?? [] as $key => $label)
            <label class="ta-check-item"><input type="checkbox" name="stages[]" value="{{ $key }}" @checked(in_array($key, $oldStages, true))> {{ $label }}</label>
            @endforeach
        </div>

        <p class="ta-label">نوع الحصص المناسبة *</p>
        <div class="ta-check-grid mb-4" style="max-height:none" data-tc-group="lesson_formats[]">
            @foreach($formOptions['lesson_formats'] ?? [] as $key => $label)
            <label class="ta-check-item"><input type="checkbox" name="lesson_formats[]" value="{{ $key }}" @checked(in_array($key, $oldFormats, true))> {{ $label }}</label>
            @endforeach
        </div>

        <p class="ta-label">مواد المنصة *</p>
        <div class="ta-check-grid mb-3" data-tc-group="subject_ids[]">
            @foreach($subjects as $s)
            <label class="ta-check-item"><input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, $oldSubjects, true))> {{ $s->name }}</label>
            @endforeach
        </div>
        <p class="ta-label">مسارات المنصة *</p>
        <div class="ta-check-grid" data-tc-group="academic_year_ids[]">
            @foreach($years as $y)
            <label class="ta-check-item"><input type="checkbox" name="academic_year_ids[]" value="{{ $y->id }}" @checked(in_array($y->id, $oldYears, true))> {{ $y->name }}</label>
            @endforeach
        </div>
        <div class="ta-actions flex justify-between gap-2">
            <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
            <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
        </div>
    </section>

    {{-- 3 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="3" style="display:none">
        <h2 class="ta-headline" style="font-size:1.5rem">٣. التوفر الأسبوعي والمهارات</h2>
        <p class="text-sm text-slate-600 m-0">الجدول اختياري — يمكنك تحديده لاحقاً بعد القبول.</p>
        <div class="overflow-x-auto rounded-xl border border-slate-200 mb-4">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات</th><th class="p-2 text-right">ملاحظات</th></tr></thead>
                <tbody>
                @foreach($formOptions['weekdays'] ?? [] as $day => $dayLabel)
                <tr class="border-t border-slate-100">
                    <td class="p-2 font-bold whitespace-nowrap">{{ $dayLabel }}</td>
                    <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][periods]" class="ta-field text-xs" value="{{ $oldWeekly[$day]['periods'] ?? '' }}"></td>
                    <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][notes]" class="ta-field text-xs" value="{{ $oldWeekly[$day]['notes'] ?? '' }}"></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <p class="ta-label">المهارات التقنية *</p>
        <div class="ta-check-grid" style="max-height:none" data-tc-group="tech_skills[]">
            @foreach($formOptions['tech_skills'] ?? [] as $key => $label)
            <label class="ta-check-item"><input type="checkbox" name="tech_skills[]" value="{{ $key }}" @checked(in_array($key, $oldTech, true))> {{ $label }}</label>
            @endforeach
        </div>
        <div class="ta-actions flex justify-between gap-2">
            <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
            <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
        </div>
    </section>

    {{-- 4 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="4" style="display:none">
        <h2 class="ta-headline" style="font-size:1.5rem">٤. فيديو الشرح والمستندات</h2>
        <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-xs text-sky-900 space-y-1">
            <p class="font-bold m-0">فيديو ٣–٥ دقائق</p>
            <p class="m-0">ارفع ملفاً حتى {{ $videoMaxMb }} ميجا، أو ضع رابط YouTube / Drive. مطلوب أحدهما على الأقل.</p>
        </div>
        <div>
            <label class="ta-label">رفع ملف الفيديو</label>
            <input type="file" name="demo_video" class="ta-field" accept="video/mp4,video/quicktime,video/webm,video/*" data-tc-video-file>
        </div>
        <label class="ta-check-item cursor-pointer">
            <input type="checkbox" name="video_use_external_link" value="1" @checked(old('video_use_external_link')) data-tc-video-ext>
            <span>سأستخدم رابطاً خارجياً</span>
        </label>
        <div>
            <label class="ta-label">رابط الفيديو</label>
            <input type="url" name="demo_video_link" class="ta-field" dir="ltr" placeholder="https://" value="{{ old('demo_video_link') }}" data-tc-video-link>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="ta-label">عنوان موضوع الفيديو *</label><input type="text" name="video_topic_title" class="ta-field" required value="{{ old('video_topic_title') }}"></div>
            <div><label class="ta-label">الصف / المرحلة *</label><input type="text" name="video_grade_level" class="ta-field" required value="{{ old('video_grade_level') }}"></div>
        </div>
        <hr class="border-slate-100">
        <div><label class="ta-label">السيرة الذاتية (CV) *</label><input type="file" name="cv" class="ta-field" required accept=".pdf,.doc,.docx"></div>
        <div><label class="ta-label">صورة المؤهل الدراسي *</label><input type="file" name="degree_photo" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
        <div><label class="ta-label">صورة هوية / إقامة *</label><input type="file" name="id_photo" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
        <div><label class="ta-label">شهادات خبرة *</label><input type="file" name="experience_certs" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
        <div><label class="ta-label">شهادات تدريبية *</label><input type="file" name="training_certs" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
        <div><label class="ta-label">نماذج أعمال *</label><input type="file" name="portfolio_file" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png,.ppt,.pptx"></div>
        <div class="ta-actions flex justify-between gap-2">
            <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
            <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
        </div>
    </section>

    {{-- 5 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="5" style="display:none">
        <h2 class="ta-headline" style="font-size:1.5rem">٥. أسئلة تقييم مبدئية</h2>
        @foreach([
            'why_sana' => 'لماذا ترغب/ترغبين في العمل مع أكاديمية سنا؟',
            'weak_student_approach' => 'كيف تتعامل/ين مع طالب ضعيف جدًا في الأساسيات؟',
            'online_interactivity' => 'كيف تجعل/ين الحصة الأونلاين تفاعلية؟',
            'teaching_tools' => 'ما الأدوات التي تستخدمها/تستخدمينها في شرح الدروس؟',
            'expected_rate' => 'ما متوسط المقابل المتوقع للحصة أو الساعة؟',
            'available_start_date' => 'متى يمكنك البدء؟',
        ] as $field => $q)
        <div><label class="ta-label">{{ $q }} *</label><textarea name="{{ $field }}" class="ta-field ta-textarea" rows="3" required>{{ old($field) }}</textarea></div>
        @endforeach
        <div class="ta-actions flex justify-between gap-2">
            <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
            <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
        </div>
    </section>

    {{-- 6 --}}
    <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="6" style="display:none">
        <h2 class="ta-headline" style="font-size:1.5rem">٦. الإقرار والإرسال للإدارة</h2>
        <p class="text-sm text-slate-600 mb-3">
            راجع <a href="{{ route('tutor.policy') }}" class="font-bold text-violet-700 underline" target="_blank" rel="noopener">سياسة انضمام المعلمين</a> قبل الموافقة.
        </p>
        <div class="space-y-2" data-tc-commitments>
            @foreach($formOptions['commitments'] ?? [] as $key => $text)
            <label class="ta-check-item block">
                <input type="hidden" name="commitments[{{ $key }}]" value="0">
                <input type="checkbox" name="commitments[{{ $key }}]" value="1" @checked(filter_var($oldCommitments[$key] ?? false, FILTER_VALIDATE_BOOLEAN)) data-tc-commitment required>
                <span>{{ $text }}</span>
            </label>
            @endforeach
        </div>
        <div class="rounded-xl border border-slate-200 p-4 text-sm text-slate-700 bg-slate-50">
            أقر بأن جميع البيانات صحيحة، وأتعهد بالالتزام بسياسات أكاديمية سنا.
        </div>
        <label class="ta-check-item">
            <input type="hidden" name="declaration_agreed" value="0">
            <input type="checkbox" name="declaration_agreed" value="1" required @checked(old('declaration_agreed'))>
            أوافق على الإقرار أعلاه *
        </label>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="ta-label">الاسم (كما في الهوية) *</label><input type="text" name="declaration_name" class="ta-field" required value="{{ old('declaration_name') }}"></div>
            <div><label class="ta-label">التوقيع (اكتب اسمك) *</label><input type="text" name="declaration_signature" class="ta-field" required value="{{ old('declaration_signature') }}"></div>
        </div>
        <p class="ta-label mt-2">أنماط استقبال الطلاب *</p>
        <div class="ta-check-grid mb-4" style="max-height:none" data-tc-group="matching_modes[]">
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" @checked(in_array('pick_teacher', old('matching_modes', ['pick_teacher']), true))> {{ __('tutor.matching_pick_teacher') }}</label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" @checked(in_array('self_schedule', old('matching_modes', []), true))> {{ __('tutor.matching_self_schedule') }}</label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted" @checked(in_array('assisted', old('matching_modes', []), true))> {{ __('tutor.matching_assisted') }}</label>
        </div>
        <p class="text-sm text-slate-500">بعد الإرسال يصلك تأكيد على بريدك من info@sanaedu.com وتراجع الإدارة الملف.</p>
        <div class="ta-actions flex justify-between gap-2">
            <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
            @if(!empty($formPreview))
                <button type="button" class="ta-btn-accent" disabled>معاينة فقط — لا إرسال</button>
            @else
                <button type="submit" class="ta-btn-accent ix-cta-pulse" id="tutor-complete-submit">
                    إرسال الملف للإدارة <i class="fas fa-paper-plane"></i>
                </button>
            @endif
        </div>
    </section>
</div>

<script>
(function () {
    var root = document.getElementById('tutor-complete-start');
    if (!root) return;
    var titles = @json(array_values($completeSteps));
    var total = {{ $completeTotal }};
    var step = 1;
    var errEl = document.getElementById('tc-step-error');
    var numEl = document.getElementById('tc-step-num');
    var titleEl = document.getElementById('tc-step-title');
    var pctEl = document.getElementById('tc-step-pct');
    var barEl = document.getElementById('tc-step-bar');
    var form = document.getElementById('tutorApplyForm');

    function panels() { return Array.prototype.slice.call(root.querySelectorAll('.tc-panel')); }
    function panel(n) { return root.querySelector('.tc-panel[data-tc-step="' + n + '"]'); }

    function showError(msg) {
        if (!errEl) return;
        if (!msg) { errEl.style.display = 'none'; errEl.textContent = ''; return; }
        errEl.style.display = 'block';
        errEl.textContent = msg;
        errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setStep(n) {
        step = Math.max(1, Math.min(total, n));
        panels().forEach(function (p) {
            var s = parseInt(p.getAttribute('data-tc-step'), 10);
            p.style.display = s === step ? 'block' : 'none';
        });
        var pct = Math.round(((step - 1) / Math.max(total - 1, 1)) * 100);
        if (numEl) numEl.textContent = String(step);
        if (titleEl) titleEl.textContent = titles[step - 1] || '';
        if (pctEl) pctEl.textContent = String(pct);
        if (barEl) barEl.style.width = pct + '%';
        root.querySelectorAll('[data-tc-goto]').forEach(function (btn) {
            var s = parseInt(btn.getAttribute('data-tc-goto'), 10);
            btn.style.background = s === step ? 'var(--edu-primary)' : '';
            btn.style.color = s === step ? '#fff' : '';
            btn.style.borderColor = s === step ? 'transparent' : '';
        });
        showError('');
        root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validatePanel(p) {
        if (!p) return true;
        var fields = p.querySelectorAll('input, select, textarea');
        for (var i = 0; i < fields.length; i++) {
            var el = fields[i];
            if (el.disabled || el.type === 'hidden') continue;
            if (el.type === 'checkbox' || el.type === 'radio') continue;
            if (el.hasAttribute('required') && !String(el.value || '').trim()) {
                el.classList.add('is-invalid');
                el.focus();
                showError('يرجى إكمال الحقول المطلوبة في هذه المرحلة.');
                return false;
            }
            if (el.type === 'file' && el.hasAttribute('required') && (!el.files || !el.files.length)) {
                el.classList.add('is-invalid');
                el.focus();
                showError('يرجى رفع الملفات المطلوبة في هذه المرحلة.');
                return false;
            }
            el.classList.remove('is-invalid');
        }

        var groups = p.querySelectorAll('[data-tc-group]');
        for (var g = 0; g < groups.length; g++) {
            var name = groups[g].getAttribute('data-tc-group');
            var boxes = groups[g].querySelectorAll('input[type="checkbox"]');
            var ok = Array.prototype.some.call(boxes, function (b) { return b.checked; });
            if (!ok) {
                showError('اختر عنصراً واحداً على الأقل من: ' + (name || 'المجموعة'));
                return false;
            }
        }

        // تخصصات أخرى مطلوبة عند اختيار other
        if (p.getAttribute('data-tc-step') === '2') {
            var otherBox = p.querySelector('input[name="specializations[]"][value="other"]');
            var otherInput = p.querySelector('input[name="specializations_other"]');
            if (otherBox && otherBox.checked && otherInput && !String(otherInput.value || '').trim()) {
                otherInput.focus();
                showError('حدّد التخصصات الأخرى.');
                return false;
            }
        }

        // فيديو: ملف أو رابط
        if (p.getAttribute('data-tc-step') === '4') {
            var file = p.querySelector('[data-tc-video-file]');
            var link = p.querySelector('[data-tc-video-link]');
            var hasFile = file && file.files && file.files.length;
            var hasLink = link && String(link.value || '').trim();
            if (!hasFile && !hasLink) {
                showError('ارفع فيديو أو أدخل رابط فيديو خارجي.');
                return false;
            }
        }

        var commits = p.querySelectorAll('[data-tc-commitment]');
        if (commits.length) {
            for (var c = 0; c < commits.length; c++) {
                if (!commits[c].checked) {
                    showError('يجب الموافقة على كل بنود الالتزام.');
                    commits[c].focus();
                    return false;
                }
            }
        }
        return true;
    }

    root.addEventListener('click', function (e) {
        var next = e.target.closest('.tc-next');
        var prev = e.target.closest('.tc-prev');
        var goto = e.target.closest('[data-tc-goto]');
        if (next) {
            e.preventDefault();
            if (!validatePanel(panel(step))) return;
            setStep(step + 1);
        } else if (prev) {
            e.preventDefault();
            setStep(step - 1);
        } else if (goto) {
            e.preventDefault();
            var target = parseInt(goto.getAttribute('data-tc-goto'), 10);
            if (target <= step) { setStep(target); return; }
            // السماح بالتقدم للأمام فقط بعد التحقق من المراحل السابقة
            for (var s = step; s < target; s++) {
                if (!validatePanel(panel(s))) { setStep(s); return; }
            }
            setStep(target);
        }
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            // تأكد أننا على آخر مرحلة وكل المراحل صالحة
            for (var s = 1; s <= total; s++) {
                if (!validatePanel(panel(s))) {
                    e.preventDefault();
                    setStep(s);
                    return;
                }
            }
            var overlay = document.getElementById('tutor-complete-uploading');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.setAttribute('aria-hidden', 'false');
            }
        });
    }

    setStep(parseInt(root.getAttribute('data-initial-step') || '1', 10) || 1);
})();
</script>

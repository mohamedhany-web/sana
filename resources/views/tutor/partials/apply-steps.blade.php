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
@endphp

{{-- الخطوة 1: مقدمة --}}
<div x-show="step === 1" x-cloak class="ix-step-panel">
    <span class="edu-badge mb-4">أكاديمية سنا — بيانات المتقدم</span>
    <h1 class="ta-headline mb-4">{{ __('tutor.apply_title') }}</h1>
    <p class="ta-lead mb-4 max-w-lg">{{ __('tutor.apply_subtitle') }}</p>
    <ul class="text-sm text-slate-600 space-y-2 mb-6 list-disc list-inside">
        <li>البيانات الشخصية والمؤهل والخبرة</li>
        <li>التخصصات، المناهج، والتوفر الأسبوعي</li>
        <li>فيديو شرح تجريبي (٣–٥ دقائق) والمستندات</li>
        <li>أسئلة الفرز والإقرار بالالتزام والسرية</li>
    </ul>
    <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6">
        {{ __('tutor.apply_policy_note') }}
        <a href="{{ route('tutor.policy') }}" class="font-bold underline hover:text-amber-900" target="_blank" rel="noopener">اقرأ سياسة انضمام المعلمين</a>
    </p>
    <button type="button" class="ta-btn-accent ix-cta-pulse" @click="next()">ابدأ التقديم <i class="fas fa-arrow-left"></i></button>
</div>

{{-- 2: بيانات شخصية --}}
<div x-show="step === 2" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١. البيانات الشخصية</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2"><label class="ta-label">الاسم الكامل *</label><input type="text" name="name" class="ta-field" required value="{{ old('name') }}"></div>
        <div><label class="ta-label">الجنسية *</label><input type="text" name="nationality" class="ta-field" required value="{{ old('nationality') }}"></div>
        <div><label class="ta-label">الدولة / المدينة *</label><input type="text" name="country_city" class="ta-field" required value="{{ old('country_city') }}"></div>
        <div class="sm:col-span-2">
            <label class="ta-label">رقم الجوال / واتساب *</label>
            <div class="ta-phone">
                <select name="country_code" class="ta-field" dir="ltr">
                    @foreach($phoneCountries ?? [] as $c)
                    <option value="{{ $c['dial_code'] }}" @selected(old('country_code', $defaultDialCode ?? '+966') === $c['dial_code'])>{{ $c['dial_code'] }}</option>
                    @endforeach
                </select>
                <input type="tel" name="phone" class="ta-field flex-1" required dir="ltr" value="{{ old('phone') }}">
            </div>
        </div>
        <div><label class="ta-label">البريد الإلكتروني *</label><input type="email" name="email" class="ta-field" required dir="ltr" value="{{ old('email') }}"></div>
        <div><label class="ta-label">LinkedIn (اختياري)</label><input type="url" name="linkedin_url" class="ta-field" dir="ltr" placeholder="https://" value="{{ old('linkedin_url') }}"></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 3: حساب --}}
<div x-show="step === 3" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">حساب الدخول</h2>
    <p class="ta-lead text-sm">للمتابعة بعد موافقة الأكاديمية</p>
    <div><label class="ta-label">كلمة المرور *</label><input type="password" name="password" class="ta-field" required minlength="8" autocomplete="new-password"></div>
    <div><label class="ta-label">تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" class="ta-field" required autocomplete="new-password"></div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 4: مؤهل وخبرة --}}
<div x-show="step === 4" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٢. المؤهل والخبرة</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="ta-label">المؤهل الدراسي *</label><input type="text" name="degree_qualification" class="ta-field" required value="{{ old('degree_qualification') }}"></div>
        <div><label class="ta-label">التخصص *</label><input type="text" name="specialization" class="ta-field" required value="{{ old('specialization') }}"></div>
        <div><label class="ta-label">سنوات الخبرة *</label><input type="number" name="years_experience" class="ta-field" min="0" max="50" required value="{{ old('years_experience', 1) }}"></div>
        <div><label class="ta-label">آخر جهة عمل *</label><input type="text" name="last_workplace" class="ta-field" required value="{{ old('last_workplace') }}"></div>
        <div class="sm:col-span-2"><label class="ta-label">المراحل التي درستها *</label><textarea name="grades_taught" class="ta-field ta-textarea" rows="2" required>{{ old('grades_taught') }}</textarea></div>
        <div class="sm:col-span-2"><label class="ta-label">المناهج التي لديك خبرة بها *</label><textarea name="curricula_experience_text" class="ta-field ta-textarea" rows="2" required>{{ old('curricula_experience_text') }}</textarea></div>
        <div class="sm:col-span-2"><label class="ta-label">عنوان مختصر للملف *</label><input type="text" name="headline" class="ta-field" required placeholder="مثال: معلّم رياضيات — ثانوي" value="{{ old('headline') }}"></div>
        <div class="sm:col-span-2"><label class="ta-label">نبذة مختصرة *</label><textarea name="bio" class="ta-field ta-textarea" required maxlength="5000">{{ old('bio') }}</textarea></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 5: تخصصات ومناهج --}}
<div x-show="step === 5" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٣–٥. التخصصات والمناهج ونوع الحصص</h2>
    <p class="ta-label">التخصصات المطلوبة *</p>
    <div class="ta-check-grid mb-3" style="max-height:none">
        @foreach($formOptions['specializations'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="specializations[]" value="{{ $key }}" @checked(in_array($key, $oldSpecs, true))> {{ $label }}</label>
        @endforeach
    </div>
    <input type="text" name="specializations_other" class="ta-field mb-4" placeholder="أخرى (حدّد)" value="{{ old('specializations_other') }}">

    <p class="ta-label">المناهج *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        @foreach($formOptions['curricula'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="curricula[]" value="{{ $key }}" @checked(in_array($key, $oldCurricula, true))> {{ $label }}</label>
        @endforeach
    </div>

    <p class="ta-label">المراحل *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        @foreach($formOptions['stages'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="stages[]" value="{{ $key }}" @checked(in_array($key, $oldStages, true))> {{ $label }}</label>
        @endforeach
    </div>

    <p class="ta-label">نوع الحصص المناسبة *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        @foreach($formOptions['lesson_formats'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="lesson_formats[]" value="{{ $key }}" @checked(in_array($key, $oldFormats, true))> {{ $label }}</label>
        @endforeach
    </div>

    <p class="ta-label">مواد المنصة *</p>
    <div class="ta-check-grid mb-3">
        @foreach($subjects as $s)
        <label class="ta-check-item"><input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, $oldSubjects, true))> {{ $s->name }}</label>
        @endforeach
    </div>
    <p class="ta-label">مسارات المنصة *</p>
    <div class="ta-check-grid">
        @foreach($years as $y)
        <label class="ta-check-item"><input type="checkbox" name="academic_year_ids[]" value="{{ $y->id }}" @checked(in_array($y->id, $oldYears, true))> {{ $y->name }}</label>
        @endforeach
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 6: توفر أسبوعي --}}
<div x-show="step === 6" x-cloak class="ix-step-panel space-y-3">
    <h2 class="ta-headline" style="font-size:1.5rem">٦. التوفر الأسبوعي (توقيت السعودية)</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات</th><th class="p-2 text-right">ملاحظات</th></tr></thead>
            <tbody>
            @foreach($formOptions['weekdays'] ?? [] as $day => $dayLabel)
            <tr class="border-t border-slate-100">
                <td class="p-2 font-bold whitespace-nowrap">{{ $dayLabel }}</td>
                <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][periods]" class="ta-field text-xs" placeholder="مثال: 4–8 م" value="{{ $oldWeekly[$day]['periods'] ?? '' }}"></td>
                <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][notes]" class="ta-field text-xs" value="{{ $oldWeekly[$day]['notes'] ?? '' }}"></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 7: مهارات تقنية --}}
<div x-show="step === 7" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٧. المهارات التقنية *</h2>
    <div class="ta-check-grid" style="max-height:none">
        @foreach($formOptions['tech_skills'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="tech_skills[]" value="{{ $key }}" @checked(in_array($key, $oldTech, true))> {{ $label }}</label>
        @endforeach
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 8: فيديو ومستندات --}}
<div x-show="step === 8" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٨–٩. فيديو الشرح والمرفقات</h2>
    <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-xs text-sky-900 space-y-1">
        <p class="font-bold m-0">تعليمات الفيديو (٣–٥ دقائق)</p>
        <p class="m-0">اشرح مفهوماً بسيطاً من تخصصك — صوت وصورة واضحان، بدون أسعار أو بيانات طلاب.</p>
        <p class="m-0 text-sky-700"><i class="fas fa-cloud"></i> المرفقات تُحفظ بشكل آمن على Cloudflare وتظهر لفريق التوظيف فقط.</p>
    </div>
    <div><label class="ta-label">رفع ملف الفيديو (MP4/MOV/WebM)</label>
        <input type="file" name="demo_video" class="ta-field" accept="video/mp4,video/quicktime,video/webm,video/*"></div>
    <div><label class="ta-label">أو رابط فيديو (Drive / YouTube)</label>
        <input type="url" name="demo_video_link" class="ta-field" dir="ltr" placeholder="https://" value="{{ old('demo_video_link') }}"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="ta-label">عنوان موضوع الفيديو *</label><input type="text" name="video_topic_title" class="ta-field" required value="{{ old('video_topic_title') }}"></div>
        <div><label class="ta-label">الصف / المرحلة *</label><input type="text" name="video_grade_level" class="ta-field" required value="{{ old('video_grade_level') }}"></div>
    </div>
    <hr class="border-slate-100">
    <div><label class="ta-label">السيرة الذاتية (CV) *</label><input type="file" name="cv" class="ta-field" accept=".pdf,.doc,.docx"></div>
    <div><label class="ta-label">صورة المؤهل الدراسي *</label><input type="file" name="degree_photo" class="ta-field" accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">صورة هوية / إقامة</label><input type="file" name="id_photo" class="ta-field" accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">شهادات خبرة</label><input type="file" name="experience_certs" class="ta-field" accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">شهادات تدريبية</label><input type="file" name="training_certs" class="ta-field" accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">نماذج أعمال</label><input type="file" name="portfolio_file" class="ta-field" accept=".pdf,.jpg,.jpeg,.png,.ppt,.pptx"></div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 9: أسئلة فرز --}}
<div x-show="step === 9" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١٠. أسئلة تقييم مبدئية</h2>
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
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 10: التزام وإقرار --}}
<div x-show="step === 10" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١١–١٢. الالتزام والإقرار</h2>
    <p class="text-sm text-slate-600 mb-3">
        راجع <a href="{{ route('tutor.policy') }}" class="font-bold text-violet-700 underline hover:text-violet-900" target="_blank" rel="noopener">سياسة انضمام المعلمين</a> قبل الموافقة على البنود أدناه.
    </p>
  <div class="space-y-2">
        @foreach($formOptions['commitments'] ?? [] as $key => $text)
        <label class="ta-check-item block">
            <input type="hidden" name="commitments[{{ $key }}]" value="0">
            <input type="checkbox" name="commitments[{{ $key }}]" value="1" @checked(filter_var($oldCommitments[$key] ?? false, FILTER_VALIDATE_BOOLEAN)) required>
            <span>{{ $text }}</span>
        </label>
        @endforeach
    </div>
    <div class="rounded-xl border border-slate-200 p-4 text-sm text-slate-700 bg-slate-50">
        أقر بأن جميع البيانات صحيحة، وأتعهد بالالتزام بسياسات أكاديمية سنا للسرية وحماية البيانات وعدم التواصل خارج القنوات الرسمية.
    </div>
    <label class="ta-check-item"><input type="hidden" name="declaration_agreed" value="0"><input type="checkbox" name="declaration_agreed" value="1" required @checked(old('declaration_agreed'))> أوافق على الإقرار أعلاه *</label>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="ta-label">الاسم (كما في الهوية) *</label><input type="text" name="declaration_name" class="ta-field" required value="{{ old('declaration_name') }}"></div>
        <div><label class="ta-label">التوقيع (اكتب اسمك) *</label><input type="text" name="declaration_signature" class="ta-field" required value="{{ old('declaration_signature') }}"></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>

{{-- 11: تفضيلات حجز + إرسال --}}
<div x-show="step === 11" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">مراجعة وإرسال</h2>
    <p class="ta-label">أنماط استقبال الطلاب على المنصة *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" checked> {{ __('tutor.matching_pick_teacher') }}</label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule"> {{ __('tutor.matching_self_schedule') }}</label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted"> {{ __('tutor.matching_assisted') }}</label>
    </div>
    <p class="text-sm text-slate-500">بعد الإرسال تراجع الأكاديمية طلبك والفيديو والمرفقات — ثم نتواصل معك بالبريد.</p>
    <div class="ta-actions">
        <button type="submit" class="ta-btn-accent ix-cta-pulse" :disabled="submitting">
            <span x-text="submitting ? 'جاري الإرسال...' : 'إرسال طلب التوظيف'"></span>
            <i class="fas fa-paper-plane" x-show="!submitting"></i>
        </button>
    </div>
</div>

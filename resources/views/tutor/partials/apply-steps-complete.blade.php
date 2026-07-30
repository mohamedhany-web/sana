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
@endphp

{{-- نموذج إكمال الملف بعد التسجيل — كل الأقسام ظاهرة بدون wizard/Alpine --}}
<div id="tutor-complete-start" class="space-y-8">

<section class="ix-step-panel space-y-4">
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
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٢. التخصصات والمناهج ونوع الحصص</h2>
    <p class="ta-label">التخصصات المطلوبة *</p>
    <div class="ta-check-grid mb-3" style="max-height:none">
        @foreach($formOptions['specializations'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="specializations[]" value="{{ $key }}" @checked(in_array($key, $oldSpecs, true))> {{ $label }}</label>
        @endforeach
    </div>
    <label class="ta-label">تخصصات أخرى (حدّد) *</label>
    <input type="text" name="specializations_other" class="ta-field mb-4" required placeholder="إن لم ينطبق اكتب «لا يوجد»" value="{{ old('specializations_other') }}">

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
</section>

<section class="ix-step-panel space-y-3">
    <h2 class="ta-headline" style="font-size:1.5rem">٣. التوفر الأسبوعي (اختياري)</h2>
    <p class="text-sm text-slate-600 m-0">يمكنك ترك الجدول فارغاً وتحديد المواعيد لاحقاً بعد قبول طلبك.</p>
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
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٤. المهارات التقنية *</h2>
    <div class="ta-check-grid" style="max-height:none">
        @foreach($formOptions['tech_skills'] ?? [] as $key => $label)
        <label class="ta-check-item"><input type="checkbox" name="tech_skills[]" value="{{ $key }}" @checked(in_array($key, $oldTech, true))> {{ $label }}</label>
        @endforeach
    </div>
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٥. فيديو الشرح والمرفقات</h2>
    <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-xs text-sky-900 space-y-1">
        <p class="font-bold m-0">تعليمات الفيديو (٣–٥ دقائق)</p>
        <p class="m-0">اشرح مفهوماً بسيطاً من تخصصك. الحد الأقصى للملف: <strong>{{ $videoMaxMb }}</strong> ميجا — أو استخدم رابط YouTube / Drive.</p>
    </div>

    <div class="space-y-2">
        <label class="ta-label">رفع ملف الفيديو (MP4/MOV/WebM)</label>
        <input type="file" name="demo_video" class="ta-field" accept="video/mp4,video/quicktime,video/webm,video/*">
        <p class="text-xs text-slate-500 m-0">مطلوب ملف أو رابط خارجي على الأقل.</p>
    </div>

    <label class="ta-check-item cursor-pointer">
        <input type="checkbox" name="video_use_external_link" value="1" @checked(old('video_use_external_link'))>
        <span>سأستخدم رابطاً خارجياً بدل رفع الملف</span>
    </label>

    <div>
        <label class="ta-label">رابط الفيديو (YouTube / Google Drive)</label>
        <input type="url" name="demo_video_link" class="ta-field" dir="ltr" placeholder="https://" value="{{ old('demo_video_link') }}">
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
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٦. أسئلة تقييم مبدئية</h2>
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
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٧. الالتزام والإقرار</h2>
    <p class="text-sm text-slate-600 mb-3">
        راجع <a href="{{ route('tutor.policy') }}" class="font-bold text-violet-700 underline" target="_blank" rel="noopener">سياسة انضمام المعلمين</a> قبل الموافقة.
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
</section>

<section class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٨. مراجعة وإرسال للإدارة</h2>
    <p class="ta-label">أنماط استقبال الطلاب على المنصة *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" @checked(in_array('pick_teacher', old('matching_modes', ['pick_teacher']), true))> {{ __('tutor.matching_pick_teacher') }}</label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" @checked(in_array('self_schedule', old('matching_modes', []), true))> {{ __('tutor.matching_self_schedule') }}</label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted" @checked(in_array('assisted', old('matching_modes', []), true))> {{ __('tutor.matching_assisted') }}</label>
    </div>
    <p class="text-sm text-slate-500">بعد الإرسال تراجع الأكاديمية طلبك — ويصلك تأكيد على بريدك من info@sanaedu.com.</p>
    <div class="ta-actions">
        <button type="submit" class="ta-btn-accent ix-cta-pulse" id="tutor-complete-submit">
            @if(!empty($formPreview))
                <span>معاينة فقط — لا إرسال</span>
            @else
                <span>إرسال الملف للإدارة</span>
                <i class="fas fa-paper-plane"></i>
            @endif
        </button>
    </div>
</section>

</div>

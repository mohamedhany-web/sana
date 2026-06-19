<?php
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
?>


<div x-show="step === 1" x-cloak class="ix-step-panel">
    <span class="edu-badge mb-4">أكاديمية سنا — بيانات المتقدم</span>
    <h1 class="ta-headline mb-4"><?php echo e(__('tutor.apply_title')); ?></h1>
    <p class="ta-lead mb-4 max-w-lg"><?php echo e(__('tutor.apply_subtitle')); ?></p>
    <ul class="text-sm text-slate-600 space-y-2 mb-6 list-disc list-inside">
        <li>البيانات الشخصية والمؤهل والخبرة</li>
        <li>التخصصات، المناهج، والتوفر الأسبوعي</li>
        <li>فيديو شرح تجريبي (٣–٥ دقائق) والمستندات</li>
        <li>أسئلة الفرز والإقرار بالالتزام والسرية</li>
    </ul>
    <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6">
        <?php echo e(__('tutor.apply_policy_note')); ?>

        <a href="<?php echo e(route('tutor.policy')); ?>" class="font-bold underline hover:text-amber-900" target="_blank" rel="noopener">اقرأ سياسة انضمام المعلمين</a>
    </p>
    <button type="button" class="ta-btn-accent ix-cta-pulse" @click="next()">ابدأ التقديم <i class="fas fa-arrow-left"></i></button>
</div>


<div x-show="step === 2" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١. البيانات الشخصية</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2"><label class="ta-label">الاسم الكامل *</label><input type="text" name="name" class="ta-field" required value="<?php echo e(old('name')); ?>"></div>
        <div><label class="ta-label">الجنسية *</label><input type="text" name="nationality" class="ta-field" required value="<?php echo e(old('nationality')); ?>"></div>
        <div><label class="ta-label">الدولة / المدينة *</label><input type="text" name="country_city" class="ta-field" required value="<?php echo e(old('country_city')); ?>"></div>
        <div class="sm:col-span-2">
            <label class="ta-label">رقم الجوال / واتساب *</label>
            <div class="ta-phone">
                <select name="country_code" class="ta-field" dir="ltr" required>
                    <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c['dial_code']); ?>" <?php if(old('country_code', $defaultDialCode ?? '+966') === $c['dial_code']): echo 'selected'; endif; ?>><?php echo e($c['dial_code']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="tel" name="phone" class="ta-field flex-1" required dir="ltr" value="<?php echo e(old('phone')); ?>">
            </div>
        </div>
        <div><label class="ta-label">البريد الإلكتروني *</label><input type="email" name="email" class="ta-field" required dir="ltr" value="<?php echo e(old('email')); ?>"></div>
        <div><label class="ta-label">LinkedIn *</label><input type="url" name="linkedin_url" class="ta-field" dir="ltr" required placeholder="https://" value="<?php echo e(old('linkedin_url')); ?>"></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 3" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">حساب الدخول</h2>
    <p class="ta-lead text-sm">للمتابعة بعد موافقة الأكاديمية</p>
    <div><label class="ta-label">كلمة المرور *</label><input type="password" name="password" class="ta-field" required minlength="8" autocomplete="new-password"></div>
    <div><label class="ta-label">تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" class="ta-field" required autocomplete="new-password"></div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 4" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٢. المؤهل والخبرة</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="ta-label">المؤهل الدراسي *</label><input type="text" name="degree_qualification" class="ta-field" required value="<?php echo e(old('degree_qualification')); ?>"></div>
        <div><label class="ta-label">التخصص *</label><input type="text" name="specialization" class="ta-field" required value="<?php echo e(old('specialization')); ?>"></div>
        <div><label class="ta-label">سنوات الخبرة *</label><input type="number" name="years_experience" class="ta-field" min="0" max="50" required value="<?php echo e(old('years_experience', 1)); ?>"></div>
        <div><label class="ta-label">آخر جهة عمل *</label><input type="text" name="last_workplace" class="ta-field" required value="<?php echo e(old('last_workplace')); ?>"></div>
        <div class="sm:col-span-2"><label class="ta-label">المراحل التي درستها *</label><textarea name="grades_taught" class="ta-field ta-textarea" rows="2" required><?php echo e(old('grades_taught')); ?></textarea></div>
        <div class="sm:col-span-2"><label class="ta-label">المناهج التي لديك خبرة بها *</label><textarea name="curricula_experience_text" class="ta-field ta-textarea" rows="2" required><?php echo e(old('curricula_experience_text')); ?></textarea></div>
        <div class="sm:col-span-2"><label class="ta-label">عنوان مختصر للملف *</label><input type="text" name="headline" class="ta-field" required placeholder="مثال: معلّم رياضيات — ثانوي" value="<?php echo e(old('headline')); ?>"></div>
        <div class="sm:col-span-2"><label class="ta-label">نبذة مختصرة *</label><textarea name="bio" class="ta-field ta-textarea" required maxlength="5000"><?php echo e(old('bio')); ?></textarea></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 5" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٣–٥. التخصصات والمناهج ونوع الحصص</h2>
    <p class="ta-label">التخصصات المطلوبة *</p>
    <div class="ta-check-grid mb-3" style="max-height:none">
        <?php $__currentLoopData = $formOptions['specializations'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="specializations[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $oldSpecs, true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <label class="ta-label">تخصصات أخرى (حدّد) *</label>
    <input type="text" name="specializations_other" class="ta-field mb-4" required placeholder="إن لم ينطبق اكتب «لا يوجد»" value="<?php echo e(old('specializations_other')); ?>">

    <p class="ta-label">المناهج *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <?php $__currentLoopData = $formOptions['curricula'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="curricula[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $oldCurricula, true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="ta-label">المراحل *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <?php $__currentLoopData = $formOptions['stages'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="stages[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $oldStages, true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="ta-label">نوع الحصص المناسبة *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <?php $__currentLoopData = $formOptions['lesson_formats'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="lesson_formats[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $oldFormats, true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <p class="ta-label">مواد المنصة *</p>
    <div class="ta-check-grid mb-3">
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="subject_ids[]" value="<?php echo e($s->id); ?>" <?php if(in_array($s->id, $oldSubjects, true)): echo 'checked'; endif; ?>> <?php echo e($s->name); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p class="ta-label">مسارات المنصة *</p>
    <div class="ta-check-grid">
        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="academic_year_ids[]" value="<?php echo e($y->id); ?>" <?php if(in_array($y->id, $oldYears, true)): echo 'checked'; endif; ?>> <?php echo e($y->name); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 6" x-cloak class="ix-step-panel space-y-3">
    <h2 class="ta-headline" style="font-size:1.5rem">٦. التوفر الأسبوعي (توقيت السعودية)</h2>
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات *</th><th class="p-2 text-right">ملاحظات *</th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $formOptions['weekdays'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t border-slate-100">
                <td class="p-2 font-bold whitespace-nowrap"><?php echo e($dayLabel); ?></td>
                <td class="p-2"><input type="text" name="weekly_availability[<?php echo e($day); ?>][periods]" class="ta-field text-xs" required placeholder="مثال: 4–8 م أو «غير متاح»" value="<?php echo e($oldWeekly[$day]['periods'] ?? ''); ?>"></td>
                <td class="p-2"><input type="text" name="weekly_availability[<?php echo e($day); ?>][notes]" class="ta-field text-xs" required placeholder="—" value="<?php echo e($oldWeekly[$day]['notes'] ?? ''); ?>"></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 7" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٧. المهارات التقنية *</h2>
    <div class="ta-check-grid" style="max-height:none">
        <?php $__currentLoopData = $formOptions['tech_skills'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item"><input type="checkbox" name="tech_skills[]" value="<?php echo e($key); ?>" <?php if(in_array($key, $oldTech, true)): echo 'checked'; endif; ?>> <?php echo e($label); ?></label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 8" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">٨–٩. فيديو الشرح والمرفقات</h2>
    <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-xs text-sky-900 space-y-1">
        <p class="font-bold m-0">تعليمات الفيديو (٣–٥ دقائق)</p>
        <p class="m-0">اشرح مفهوماً بسيطاً من تخصصك — صوت وصورة واضحان، بدون أسعار أو بيانات طلاب.</p>
        <p class="m-0 text-sky-700"><i class="fas fa-cloud"></i> المرفقات تُحفظ بشكل آمن على Cloudflare وتظهر لفريق التوظيف فقط.</p>
    </div>
    <div><label class="ta-label">رفع ملف الفيديو (MP4/MOV/WebM) *</label>
        <input type="file" name="demo_video" class="ta-field" required accept="video/mp4,video/quicktime,video/webm,video/*"></div>
    <div><label class="ta-label">رابط الفيديو (Drive / YouTube) *</label>
        <input type="url" name="demo_video_link" class="ta-field" required dir="ltr" placeholder="https://" value="<?php echo e(old('demo_video_link')); ?>"></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="ta-label">عنوان موضوع الفيديو *</label><input type="text" name="video_topic_title" class="ta-field" required value="<?php echo e(old('video_topic_title')); ?>"></div>
        <div><label class="ta-label">الصف / المرحلة *</label><input type="text" name="video_grade_level" class="ta-field" required value="<?php echo e(old('video_grade_level')); ?>"></div>
    </div>
    <hr class="border-slate-100">
    <div><label class="ta-label">السيرة الذاتية (CV) *</label><input type="file" name="cv" class="ta-field" required accept=".pdf,.doc,.docx"></div>
    <div><label class="ta-label">صورة المؤهل الدراسي *</label><input type="file" name="degree_photo" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">صورة هوية / إقامة *</label><input type="file" name="id_photo" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">شهادات خبرة *</label><input type="file" name="experience_certs" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">شهادات تدريبية *</label><input type="file" name="training_certs" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png"></div>
    <div><label class="ta-label">نماذج أعمال *</label><input type="file" name="portfolio_file" class="ta-field" required accept=".pdf,.jpg,.jpeg,.png,.ppt,.pptx"></div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 9" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١٠. أسئلة تقييم مبدئية</h2>
    <?php $__currentLoopData = [
        'why_sana' => 'لماذا ترغب/ترغبين في العمل مع أكاديمية سنا؟',
        'weak_student_approach' => 'كيف تتعامل/ين مع طالب ضعيف جدًا في الأساسيات؟',
        'online_interactivity' => 'كيف تجعل/ين الحصة الأونلاين تفاعلية؟',
        'teaching_tools' => 'ما الأدوات التي تستخدمها/تستخدمينها في شرح الدروس؟',
        'expected_rate' => 'ما متوسط المقابل المتوقع للحصة أو الساعة؟',
        'available_start_date' => 'متى يمكنك البدء؟',
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div><label class="ta-label"><?php echo e($q); ?> *</label><textarea name="<?php echo e($field); ?>" class="ta-field ta-textarea" rows="3" required><?php echo e(old($field)); ?></textarea></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 10" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">١١–١٢. الالتزام والإقرار</h2>
    <p class="text-sm text-slate-600 mb-3">
        راجع <a href="<?php echo e(route('tutor.policy')); ?>" class="font-bold text-violet-700 underline hover:text-violet-900" target="_blank" rel="noopener">سياسة انضمام المعلمين</a> قبل الموافقة على البنود أدناه.
    </p>
  <div class="space-y-2">
        <?php $__currentLoopData = $formOptions['commitments'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="ta-check-item block">
            <input type="hidden" name="commitments[<?php echo e($key); ?>]" value="0">
            <input type="checkbox" name="commitments[<?php echo e($key); ?>]" value="1" <?php if(filter_var($oldCommitments[$key] ?? false, FILTER_VALIDATE_BOOLEAN)): echo 'checked'; endif; ?> required>
            <span><?php echo e($text); ?></span>
        </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="rounded-xl border border-slate-200 p-4 text-sm text-slate-700 bg-slate-50">
        أقر بأن جميع البيانات صحيحة، وأتعهد بالالتزام بسياسات أكاديمية سنا للسرية وحماية البيانات وعدم التواصل خارج القنوات الرسمية.
    </div>
    <label class="ta-check-item"><input type="hidden" name="declaration_agreed" value="0"><input type="checkbox" name="declaration_agreed" value="1" required <?php if(old('declaration_agreed')): echo 'checked'; endif; ?>> أوافق على الإقرار أعلاه *</label>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="ta-label">الاسم (كما في الهوية) *</label><input type="text" name="declaration_name" class="ta-field" required value="<?php echo e(old('declaration_name')); ?>"></div>
        <div><label class="ta-label">التوقيع (اكتب اسمك) *</label><input type="text" name="declaration_signature" class="ta-field" required value="<?php echo e(old('declaration_signature')); ?>"></div>
    </div>
    <div class="ta-actions"><button type="button" class="ta-btn-primary" @click="next()">التالي</button></div>
</div>


<div x-show="step === 11" x-cloak class="ix-step-panel space-y-4">
    <h2 class="ta-headline" style="font-size:1.5rem">مراجعة وإرسال</h2>
    <p class="ta-label">أنماط استقبال الطلاب على المنصة *</p>
    <div class="ta-check-grid mb-4" style="max-height:none">
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" <?php if(in_array('pick_teacher', old('matching_modes', ['pick_teacher']), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_pick_teacher')); ?></label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" <?php if(in_array('self_schedule', old('matching_modes', []), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_self_schedule')); ?></label>
        <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted" <?php if(in_array('assisted', old('matching_modes', []), true)): echo 'checked'; endif; ?>> <?php echo e(__('tutor.matching_assisted')); ?></label>
    </div>
    <p class="text-sm text-slate-500">بعد الإرسال تراجع الأكاديمية طلبك والفيديو والمرفقات — ثم نتواصل معك بالبريد.</p>
    <div class="ta-actions">
        <button type="submit" class="ta-btn-accent ix-cta-pulse" :disabled="submitting">
            <span x-text="submitting ? 'جاري الإرسال...' : 'إرسال طلب التوظيف'"></span>
            <i class="fas fa-paper-plane" x-show="!submitting"></i>
        </button>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/tutor/partials/apply-steps.blade.php ENDPATH**/ ?>
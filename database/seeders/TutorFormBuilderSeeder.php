<?php

namespace Database\Seeders;

use App\Models\TutorFormField;
use App\Models\TutorFormStep;
use Illuminate\Database\Seeder;

/**
 * يزرع هيكل نموذج التوظيف الحالي كما هو — بدون تغيير سلوك التسجيل الافتراضي.
 */
class TutorFormBuilderSeeder extends Seeder
{
    public function run(): void
    {
        if (TutorFormStep::query()->exists()) {
            return;
        }

        $steps = [
            ['slug' => 'intro', 'title' => 'مقدمة التقديم', 'description' => 'مرحباً بك في نموذج توظيف معلمي أكاديمية سنا', 'sort_order' => 1, 'step_type' => 'intro'],
            ['slug' => 'personal', 'title' => 'البيانات الشخصية', 'description' => null, 'sort_order' => 2, 'step_type' => 'fields'],
            ['slug' => 'account', 'title' => 'إنشاء الحساب', 'description' => null, 'sort_order' => 3, 'step_type' => 'fields'],
            ['slug' => 'qualification', 'title' => 'المؤهل والخبرة', 'description' => null, 'sort_order' => 4, 'step_type' => 'fields'],
            ['slug' => 'teaching', 'title' => 'التخصصات والمناهج', 'description' => null, 'sort_order' => 5, 'step_type' => 'fields'],
            ['slug' => 'availability', 'title' => 'التوفر الأسبوعي', 'description' => 'اختياري — توقيت السعودية', 'sort_order' => 6, 'step_type' => 'fields'],
            ['slug' => 'tech', 'title' => 'المهارات التقنية', 'description' => null, 'sort_order' => 7, 'step_type' => 'fields'],
            ['slug' => 'video_docs', 'title' => 'فيديو الشرح والمرفقات', 'description' => null, 'sort_order' => 8, 'step_type' => 'fields'],
            ['slug' => 'screening', 'title' => 'أسئلة تقييم مبدئية', 'description' => null, 'sort_order' => 9, 'step_type' => 'fields'],
            ['slug' => 'commitments', 'title' => 'الالتزام والإقرار', 'description' => null, 'sort_order' => 10, 'step_type' => 'fields'],
            ['slug' => 'review', 'title' => 'أنماط الاستقبال والإرسال', 'description' => null, 'sort_order' => 11, 'step_type' => 'review'],
        ];

        $stepIds = [];
        foreach ($steps as $step) {
            $model = TutorFormStep::create([
                ...$step,
                'is_active' => true,
                'is_system' => true,
            ]);
            $stepIds[$step['slug']] = $model->id;
        }

        $fields = $this->systemFields($stepIds);
        foreach ($fields as $i => $field) {
            TutorFormField::create([
                ...$field,
                'is_system' => true,
                'is_active' => true,
                'sort_order' => $field['sort_order'] ?? ($i + 1),
            ]);
        }
    }

    private function systemFields(array $stepIds): array
    {
        return [
            // personal
            ['step_id' => $stepIds['personal'], 'field_key' => 'name', 'label' => 'الاسم الكامل', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 1, 'placeholder' => null, 'settings' => ['max' => 120]],
            ['step_id' => $stepIds['personal'], 'field_key' => 'nationality', 'label' => 'الجنسية', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 2, 'settings' => ['max' => 80]],
            ['step_id' => $stepIds['personal'], 'field_key' => 'country_city', 'label' => 'الدولة / المدينة', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 3, 'settings' => ['max' => 120]],
            ['step_id' => $stepIds['personal'], 'field_key' => 'phone', 'label' => 'رقم الجوال', 'field_type' => 'country_phone', 'is_required' => true, 'width' => 'full', 'sort_order' => 4],
            ['step_id' => $stepIds['personal'], 'field_key' => 'email', 'label' => 'البريد الإلكتروني', 'field_type' => 'email', 'is_required' => true, 'width' => 'half', 'sort_order' => 5, 'settings' => ['max' => 255]],
            ['step_id' => $stepIds['personal'], 'field_key' => 'linkedin_url', 'label' => 'رابط LinkedIn', 'field_type' => 'url', 'is_required' => false, 'width' => 'half', 'sort_order' => 6, 'placeholder' => 'https://', 'settings' => ['max' => 500]],

            // account
            ['step_id' => $stepIds['account'], 'field_key' => 'password', 'label' => 'كلمة المرور', 'field_type' => 'password', 'is_required' => true, 'width' => 'half', 'sort_order' => 1],
            ['step_id' => $stepIds['account'], 'field_key' => 'password_confirmation', 'label' => 'تأكيد كلمة المرور', 'field_type' => 'password', 'is_required' => true, 'width' => 'half', 'sort_order' => 2],

            // qualification
            ['step_id' => $stepIds['qualification'], 'field_key' => 'degree_qualification', 'label' => 'المؤهل الدراسي', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 1, 'settings' => ['max' => 200]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'specialization', 'label' => 'التخصص', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 2, 'settings' => ['max' => 200]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'years_experience', 'label' => 'سنوات الخبرة', 'field_type' => 'number', 'is_required' => true, 'width' => 'half', 'sort_order' => 3, 'settings' => ['min' => 0, 'max' => 50]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'last_workplace', 'label' => 'آخر جهة عمل', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 4, 'settings' => ['max' => 300]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'grades_taught', 'label' => 'المراحل التي درّستها', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 5, 'settings' => ['max' => 2000, 'rows' => 3]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'curricula_experience_text', 'label' => 'خبرة المناهج', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 6, 'settings' => ['max' => 2000, 'rows' => 3]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'headline', 'label' => 'عنوان مختصر (يظهر للطلاب)', 'field_type' => 'text', 'is_required' => true, 'width' => 'full', 'sort_order' => 7, 'settings' => ['max' => 200]],
            ['step_id' => $stepIds['qualification'], 'field_key' => 'bio', 'label' => 'نبذة تعريفية', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 8, 'settings' => ['max' => 5000, 'rows' => 4]],

            // teaching
            ['step_id' => $stepIds['teaching'], 'field_key' => 'specializations', 'label' => 'التخصصات المطلوبة', 'field_type' => 'checkbox_group', 'is_required' => true, 'width' => 'full', 'sort_order' => 1, 'options' => ['source' => 'specializations']],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'specializations_other', 'label' => 'تخصصات أخرى', 'field_type' => 'text', 'is_required' => false, 'width' => 'full', 'sort_order' => 2, 'settings' => ['max' => 200]],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'curricula', 'label' => 'المناهج', 'field_type' => 'checkbox_group', 'is_required' => true, 'width' => 'full', 'sort_order' => 3, 'options' => ['source' => 'curricula']],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'stages', 'label' => 'المراحل', 'field_type' => 'checkbox_group', 'is_required' => true, 'width' => 'full', 'sort_order' => 4, 'options' => ['source' => 'stages']],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'lesson_formats', 'label' => 'نوع الحصص', 'field_type' => 'checkbox_group', 'is_required' => true, 'width' => 'full', 'sort_order' => 5, 'options' => ['source' => 'lesson_formats']],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'subject_ids', 'label' => 'مواد المنصة', 'field_type' => 'subjects', 'is_required' => true, 'width' => 'full', 'sort_order' => 6],
            ['step_id' => $stepIds['teaching'], 'field_key' => 'academic_year_ids', 'label' => 'مسارات المنصة', 'field_type' => 'academic_years', 'is_required' => true, 'width' => 'full', 'sort_order' => 7],

            // availability
            ['step_id' => $stepIds['availability'], 'field_key' => 'weekly_availability', 'label' => 'التوفر الأسبوعي', 'field_type' => 'weekly_availability', 'is_required' => false, 'width' => 'full', 'sort_order' => 1],

            // tech
            ['step_id' => $stepIds['tech'], 'field_key' => 'tech_skills', 'label' => 'المهارات التقنية', 'field_type' => 'checkbox_group', 'is_required' => true, 'width' => 'full', 'sort_order' => 1, 'options' => ['source' => 'tech_skills']],

            // video + docs
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'demo_video', 'label' => 'فيديو الشرح التجريبي', 'field_type' => 'video_pair', 'is_required' => true, 'width' => 'full', 'sort_order' => 1],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'video_topic_title', 'label' => 'عنوان موضوع الفيديو', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 2, 'settings' => ['max' => 300]],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'video_grade_level', 'label' => 'الصف / المرحلة', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 3, 'settings' => ['max' => 120]],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'cv', 'label' => 'السيرة الذاتية (CV)', 'field_type' => 'file', 'is_required' => true, 'width' => 'full', 'sort_order' => 4, 'settings' => ['accept' => '.pdf,.doc,.docx', 'mimes' => 'pdf,doc,docx']],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'degree_photo', 'label' => 'صورة المؤهل الدراسي', 'field_type' => 'file', 'is_required' => true, 'width' => 'full', 'sort_order' => 5, 'settings' => ['accept' => '.pdf,.jpg,.jpeg,.png', 'mimes' => 'jpg,jpeg,png,pdf']],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'id_photo', 'label' => 'صورة هوية / إقامة', 'field_type' => 'file', 'is_required' => false, 'width' => 'full', 'sort_order' => 6, 'settings' => ['accept' => '.pdf,.jpg,.jpeg,.png', 'mimes' => 'jpg,jpeg,png,pdf']],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'experience_certs', 'label' => 'شهادات خبرة', 'field_type' => 'file', 'is_required' => false, 'width' => 'full', 'sort_order' => 7, 'settings' => ['accept' => '.pdf,.jpg,.jpeg,.png', 'mimes' => 'pdf,jpg,jpeg,png']],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'training_certs', 'label' => 'شهادات تدريبية', 'field_type' => 'file', 'is_required' => false, 'width' => 'full', 'sort_order' => 8, 'settings' => ['accept' => '.pdf,.jpg,.jpeg,.png', 'mimes' => 'pdf,jpg,jpeg,png']],
            ['step_id' => $stepIds['video_docs'], 'field_key' => 'portfolio_file', 'label' => 'نماذج أعمال', 'field_type' => 'file', 'is_required' => false, 'width' => 'full', 'sort_order' => 9, 'settings' => ['accept' => '.pdf,.jpg,.jpeg,.png,.ppt,.pptx', 'mimes' => 'pdf,jpg,jpeg,png,ppt,pptx']],

            // screening
            ['step_id' => $stepIds['screening'], 'field_key' => 'why_sana', 'label' => 'لماذا ترغب في العمل مع أكاديمية سنا؟', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 1, 'settings' => ['max' => 5000, 'rows' => 3]],
            ['step_id' => $stepIds['screening'], 'field_key' => 'weak_student_approach', 'label' => 'كيف تتعامل مع طالب ضعيف في الأساسيات؟', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 2, 'settings' => ['max' => 5000, 'rows' => 3]],
            ['step_id' => $stepIds['screening'], 'field_key' => 'online_interactivity', 'label' => 'كيف تجعل الحصة الأونلاين تفاعلية؟', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 3, 'settings' => ['max' => 5000, 'rows' => 3]],
            ['step_id' => $stepIds['screening'], 'field_key' => 'teaching_tools', 'label' => 'ما الأدوات التي تستخدمها في الشرح؟', 'field_type' => 'textarea', 'is_required' => true, 'width' => 'full', 'sort_order' => 4, 'settings' => ['max' => 5000, 'rows' => 3]],
            ['step_id' => $stepIds['screening'], 'field_key' => 'expected_rate', 'label' => 'متوسط المقابل المتوقع للحصة أو الساعة', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 5, 'settings' => ['max' => 200]],
            ['step_id' => $stepIds['screening'], 'field_key' => 'available_start_date', 'label' => 'متى يمكنك البدء؟', 'field_type' => 'text', 'is_required' => true, 'width' => 'half', 'sort_order' => 6, 'settings' => ['max' => 120]],

            // commitments
            ['step_id' => $stepIds['commitments'], 'field_key' => 'commitments', 'label' => 'بنود الالتزام', 'field_type' => 'commitments', 'is_required' => true, 'width' => 'full', 'sort_order' => 1, 'options' => ['source' => 'commitments']],
            ['step_id' => $stepIds['commitments'], 'field_key' => 'declaration', 'label' => 'الإقرار والتوقيع', 'field_type' => 'declaration', 'is_required' => true, 'width' => 'full', 'sort_order' => 2],

            // review
            ['step_id' => $stepIds['review'], 'field_key' => 'matching_modes', 'label' => 'أنماط استقبال الطلاب', 'field_type' => 'matching_modes', 'is_required' => true, 'width' => 'full', 'sort_order' => 1],
        ];
    }
}

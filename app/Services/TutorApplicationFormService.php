<?php

namespace App\Services;

use App\Support\AcademicSubjectCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class TutorApplicationFormService
{
    public static function optionKeys(string $section): array
    {
        return array_keys(config('tutor_application.'.$section, []));
    }

    public static function validationRules(): array
    {
        $specKeys = implode(',', self::optionKeys('specializations'));
        $curriculaKeys = implode(',', self::optionKeys('curricula'));
        $stageKeys = implode(',', self::optionKeys('stages'));
        $formatKeys = implode(',', self::optionKeys('lesson_formats'));
        $techKeys = implode(',', self::optionKeys('tech_skills'));
        $videoMax = (int) config('tutor_application.video_max_mb', 150);
        $docMax = (int) config('tutor_application.document_max_mb', 15);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nationality' => ['required', 'string', 'max:80'],
            'country_city' => ['required', 'string', 'max:120'],
            'country_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:30'],
            'linkedin_url' => ['required', 'url', 'max:500'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],

            'degree_qualification' => ['required', 'string', 'max:200'],
            'specialization' => ['required', 'string', 'max:200'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'last_workplace' => ['required', 'string', 'max:300'],
            'grades_taught' => ['required', 'string', 'max:2000'],
            'curricula_experience_text' => ['required', 'string', 'max:2000'],
            'headline' => ['required', 'string', 'max:200'],
            'bio' => ['required', 'string', 'max:5000'],

            'specializations' => ['required', 'array', 'min:1'],
            'specializations.*' => ['string', 'in:'.$specKeys],
            'specializations_other' => ['required', 'string', 'max:200'],

            'curricula' => ['required', 'array', 'min:1'],
            'curricula.*' => ['string', 'in:'.$curriculaKeys],

            'stages' => ['required', 'array', 'min:1'],
            'stages.*' => ['string', 'in:'.$stageKeys],

            'lesson_formats' => ['required', 'array', 'min:1'],
            'lesson_formats.*' => ['string', 'in:'.$formatKeys],

            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'exists:academic_subjects,id'],
            'academic_year_ids' => ['required', 'array', 'min:1'],
            'academic_year_ids.*' => ['integer', 'exists:academic_years,id'],

            'matching_modes' => ['required', 'array', 'min:1'],
            'matching_modes.*' => ['in:assisted,self_schedule,pick_teacher'],

            'weekly_availability' => ['required', 'array'],

            'tech_skills' => ['required', 'array', 'min:1'],
            'tech_skills.*' => ['string', 'in:'.$techKeys],

            'demo_video' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo', 'max:'.($videoMax * 1024)],
            'demo_video_link' => ['required', 'url', 'max:1000'],
            'video_topic_title' => ['required', 'string', 'max:300'],
            'video_grade_level' => ['required', 'string', 'max:120'],

            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:'.($docMax * 1024)],
            'degree_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.($docMax * 1024)],
            'id_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.($docMax * 1024)],
            'experience_certs' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:'.($docMax * 1024)],
            'training_certs' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:'.($docMax * 1024)],
            'portfolio_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,ppt,pptx', 'max:'.($docMax * 1024)],

            'why_sana' => ['required', 'string', 'max:5000'],
            'weak_student_approach' => ['required', 'string', 'max:5000'],
            'online_interactivity' => ['required', 'string', 'max:5000'],
            'teaching_tools' => ['required', 'string', 'max:5000'],
            'expected_rate' => ['required', 'string', 'max:200'],
            'available_start_date' => ['required', 'string', 'max:120'],

            'commitments' => ['required', 'array'],

            'declaration_agreed' => ['accepted'],
            'declaration_name' => ['required', 'string', 'max:120'],
            'declaration_signature' => ['required', 'string', 'max:200'],
        ];

        foreach (array_keys(config('tutor_application.weekdays', [])) as $day) {
            $rules["weekly_availability.{$day}.periods"] = ['required', 'string', 'max:500'];
            $rules["weekly_availability.{$day}.notes"] = ['required', 'string', 'max:500'];
        }

        return $rules;
    }

    public static function validate(Request $request): array
    {
        $data = $request->validate(
            self::validationRules(),
            self::validationMessages(),
            self::validationAttributes()
        );

        AcademicSubjectCatalog::assertActiveSubjectIds($data['subject_ids']);

        $commitmentKeys = array_keys(config('tutor_application.commitments', []));
        foreach ($commitmentKeys as $key) {
            if (! filter_var($data['commitments'][$key] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'commitments.'.$key => __('tutor.apply_validation.commitment_required'),
                ]);
            }
        }

        return $data;
    }

    public static function buildApplicationData(array $data, array $files = []): array
    {
        $weekly = [];
        foreach (config('tutor_application.weekdays', []) as $day => $label) {
            $row = $data['weekly_availability'][$day] ?? [];
            $weekly[$day] = [
                'label' => $label,
                'periods' => trim((string) ($row['periods'] ?? '')),
                'notes' => trim((string) ($row['notes'] ?? '')),
            ];
        }

        return [
            'personal' => [
                'nationality' => $data['nationality'],
                'country_city' => $data['country_city'],
                'linkedin_url' => $data['linkedin_url'] ?? null,
            ],
            'qualification' => [
                'degree_qualification' => $data['degree_qualification'],
                'specialization' => $data['specialization'],
                'last_workplace' => $data['last_workplace'],
                'grades_taught' => $data['grades_taught'],
                'curricula_experience_text' => $data['curricula_experience_text'],
            ],
            'teaching' => [
                'specializations' => $data['specializations'],
                'specializations_other' => $data['specializations_other'] ?? null,
                'curricula' => $data['curricula'],
                'stages' => $data['stages'],
                'lesson_formats' => $data['lesson_formats'],
            ],
            'weekly_availability' => $weekly,
            'tech_skills' => $data['tech_skills'],
            'video' => [
                'topic_title' => $data['video_topic_title'],
                'grade_level' => $data['video_grade_level'],
                'file_path' => $files['demo_video'] ?? null,
                'link' => $data['demo_video_link'] ?? null,
            ],
            'documents' => [
                'cv' => $files['cv'] ?? null,
                'degree_photo' => $files['degree_photo'] ?? null,
                'id_photo' => $files['id_photo'] ?? null,
                'experience_certs' => $files['experience_certs'] ?? null,
                'training_certs' => $files['training_certs'] ?? null,
                'portfolio_file' => $files['portfolio_file'] ?? null,
            ],
            'screening' => [
                'why_sana' => $data['why_sana'],
                'weak_student_approach' => $data['weak_student_approach'],
                'online_interactivity' => $data['online_interactivity'],
                'teaching_tools' => $data['teaching_tools'],
                'expected_rate' => $data['expected_rate'],
                'available_start_date' => $data['available_start_date'],
            ],
            'commitments' => array_fill_keys(array_keys(config('tutor_application.commitments', [])), true),
            'declaration' => [
                'name' => $data['declaration_name'],
                'signature' => $data['declaration_signature'],
                'date' => now()->toDateString(),
                'agreed_at' => now()->toIso8601String(),
            ],
            'form_version' => '2026-05-sana',
        ];
    }

    public static function sessionTypesFromFormats(array $formats): array
    {
        $types = [];
        if (in_array('one_to_one', $formats, true)) {
            $types[] = 'one_to_one';
        }
        if (in_array('small_group', $formats, true)) {
            $types[] = 'small_group';
        }

        return $types !== [] ? $types : ['one_to_one'];
    }

    public static function storeUploadedFiles(Request $request, int $userId): array
    {
        $map = [
            'demo_video' => 'demo-video',
            'cv' => 'cv',
            'degree_photo' => 'degree',
            'id_photo' => 'id',
            'experience_certs' => 'experience-certs',
            'training_certs' => 'training-certs',
            'portfolio_file' => 'portfolio',
        ];

        $stored = [];
        foreach ($map as $field => $subdir) {
            $file = $request->file($field);
            if ($file instanceof UploadedFile) {
                $stored[$field] = TutorApplicationStorage::store($file, $userId, $subdir);
            }
        }

        return $stored;
    }

    /** @return array<string, string> */
    public static function validationMessages(): array
    {
        return [
            'required' => __('tutor.apply_validation.required'),
            'email' => __('tutor.apply_validation.email'),
            'email.unique' => __('tutor.apply_validation.email_unique'),
            'confirmed' => __('tutor.apply_validation.password_confirmed'),
            'password.min' => __('tutor.apply_validation.password_min'),
            'accepted' => __('tutor.apply_validation.commitment_required'),
            'url' => __('tutor.apply_validation.url_invalid'),
            'demo_video.mimetypes' => __('tutor.apply_validation.video_type'),
            'demo_video.max' => __('tutor.apply_validation.video_size'),
        ];
    }

    /** @return array<string, string> */
    public static function validationAttributes(): array
    {
        return [
            'name' => __('tutor.apply_validation.attr_name'),
            'email' => __('tutor.apply_validation.attr_email'),
            'password' => __('tutor.apply_validation.attr_password'),
            'nationality' => __('tutor.apply_validation.attr_nationality'),
            'country_city' => __('tutor.apply_validation.attr_country_city'),
            'phone' => __('tutor.apply_validation.attr_phone'),
            'linkedin_url' => __('tutor.apply_validation.attr_linkedin'),
            'specializations_other' => __('tutor.apply_validation.attr_specializations_other'),
            'demo_video_link' => __('tutor.apply_validation.attr_demo_video_link'),
            'id_photo' => __('tutor.apply_validation.attr_id_photo'),
            'experience_certs' => __('tutor.apply_validation.attr_experience_certs'),
            'training_certs' => __('tutor.apply_validation.attr_training_certs'),
            'portfolio_file' => __('tutor.apply_validation.attr_portfolio'),
            'matching_modes' => __('tutor.apply_validation.attr_matching_modes'),
            'headline' => __('tutor.apply_validation.attr_headline'),
            'bio' => __('tutor.apply_validation.attr_bio'),
            'years_experience' => __('tutor.apply_validation.attr_years'),
            'subject_ids' => __('tutor.apply_validation.attr_subjects'),
            'academic_year_ids' => __('tutor.apply_validation.attr_years_study'),
            'demo_video' => __('tutor.apply_validation.attr_demo_video'),
            'cv' => __('tutor.apply_validation.attr_cv'),
            'degree_photo' => __('tutor.apply_validation.attr_degree'),
            'why_sana' => __('tutor.apply_validation.attr_why_sana'),
            'declaration_name' => __('tutor.apply_validation.attr_declaration_name'),
            'declaration_signature' => __('tutor.apply_validation.attr_signature'),
        ];
    }

    public static function resumeStepFromErrors(\Illuminate\Support\MessageBag|\Illuminate\Support\ViewErrorBag $errors): int
    {
        if ($errors instanceof \Illuminate\Support\ViewErrorBag) {
            $errors = $errors->getBag('default');
        }

        $map = [
            2 => ['name', 'email', 'nationality', 'country_city', 'country_code', 'phone', 'linkedin_url'],
            3 => ['password', 'password_confirmation'],
            4 => ['degree_qualification', 'specialization', 'years_experience', 'last_workplace', 'grades_taught', 'curricula_experience_text', 'headline', 'bio'],
            5 => ['specializations', 'specializations_other', 'curricula', 'stages', 'lesson_formats', 'subject_ids', 'academic_year_ids'],
            6 => ['weekly_availability'],
            7 => ['tech_skills'],
            8 => ['demo_video', 'demo_video_link', 'video_topic_title', 'video_grade_level', 'cv', 'degree_photo', 'id_photo', 'experience_certs', 'training_certs', 'portfolio_file'],
            9 => ['why_sana', 'weak_student_approach', 'online_interactivity', 'teaching_tools', 'expected_rate', 'available_start_date'],
            10 => ['commitments', 'declaration_agreed', 'declaration_name', 'declaration_signature'],
            11 => ['matching_modes'],
        ];

        foreach ($map as $step => $fields) {
            foreach ($fields as $field) {
                if ($errors->has($field) || $errors->has($field.'.*')) {
                    return $step;
                }
            }
        }

        foreach ($errors->keys() as $key) {
            if (str_starts_with((string) $key, 'weekly_availability.')) {
                return 6;
            }
        }

        return 2;
    }
}

<?php

namespace App\Services;

use App\Models\TutorFormField;
use App\Models\TutorFormStep;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class TutorFormSchemaService
{
    public const CACHE_KEY = 'tutor_form_schema_v1';

    public static function tablesReady(): bool
    {
        try {
            return Schema::hasTable('tutor_form_steps') && Schema::hasTable('tutor_form_fields');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isEnabled(): bool
    {
        if (! self::tablesReady()) {
            return false;
        }

        return TutorFormStep::query()->active()->exists();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return Collection<int, TutorFormStep>
     */
    public static function activeSteps(): Collection
    {
        if (! self::tablesReady()) {
            return collect();
        }

        return Cache::remember(self::CACHE_KEY, 300, function () {
            return TutorFormStep::query()
                ->active()
                ->ordered()
                ->with(['activeFields' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
                ->get();
        });
    }

    /**
     * @return Collection<int, TutorFormField>
     */
    public static function activeFields(): Collection
    {
        return self::activeSteps()->flatMap(fn (TutorFormStep $s) => $s->activeFields);
    }

    public static function fieldByKey(string $key): ?TutorFormField
    {
        return self::activeFields()->firstWhere('field_key', $key);
    }

    /**
     * خريطة الخطوة → مفاتيح الحقول (للتحقق المرحلي وأخطاء السيرفر).
     *
     * @return array<int, list<string>>
     */
    public static function stepFieldMap(): array
    {
        $map = [];
        $index = 1;
        foreach (self::activeSteps() as $step) {
            $keys = [];
            foreach ($step->activeFields as $field) {
                $keys = array_merge($keys, self::inputKeysForField($field));
            }
            $map[$index] = array_values(array_unique($keys));
            $index++;
        }

        return $map;
    }

    /**
     * @return list<string>
     */
    public static function inputKeysForField(TutorFormField $field): array
    {
        return match ($field->field_type) {
            'country_phone' => ['country_code', 'phone'],
            'video_pair' => ['demo_video', 'demo_video_link', 'video_use_external_link'],
            'declaration' => ['declaration_agreed', 'declaration_name', 'declaration_signature'],
            'weekly_availability' => ['weekly_availability'],
            'info' => [],
            default => [$field->field_key],
        };
    }

    /**
     * هل الحقل مطلوب حسب المخطط؟ (للحقول النظامية والمعرّفة).
     */
    public static function isFieldRequired(string $fieldKey, bool $legacyDefault = true): bool
    {
        if (! self::isEnabled()) {
            return $legacyDefault;
        }

        $field = self::fieldByKey($fieldKey);
        if (! $field) {
            // حقول مشتقة من أنواع مركبة
            if (in_array($fieldKey, ['demo_video_link', 'video_use_external_link'], true)) {
                $pair = self::fieldByKey('demo_video');

                return $pair ? $pair->is_required : $legacyDefault;
            }
            if (in_array($fieldKey, ['declaration_agreed', 'declaration_name', 'declaration_signature'], true)) {
                $decl = self::fieldByKey('declaration');

                return $decl ? $decl->is_required : $legacyDefault;
            }
            if ($fieldKey === 'country_code') {
                $phone = self::fieldByKey('phone');

                return $phone ? $phone->is_required : $legacyDefault;
            }
            if ($fieldKey === 'password_confirmation') {
                $pwd = self::fieldByKey('password');

                return $pwd ? $pwd->is_required : $legacyDefault;
            }

            return $legacyDefault;
        }

        return (bool) $field->is_required;
    }

    public static function isFieldActive(string $fieldKey): bool
    {
        if (! self::isEnabled()) {
            return true;
        }

        // حقول مشتقة
        if (in_array($fieldKey, ['demo_video_link', 'video_use_external_link'], true)) {
            return self::fieldByKey('demo_video') !== null;
        }
        if (in_array($fieldKey, ['declaration_agreed', 'declaration_name', 'declaration_signature'], true)) {
            return self::fieldByKey('declaration') !== null;
        }
        if ($fieldKey === 'country_code') {
            return self::fieldByKey('phone') !== null;
        }
        if ($fieldKey === 'password_confirmation') {
            return self::fieldByKey('password') !== null
                || self::fieldByKey('password_confirmation') !== null;
        }

        return self::fieldByKey($fieldKey) !== null;
    }

    /**
     * قواعد تحقق للحقول المخصصة فقط (غير النظامية).
     *
     * @return array<string, list<string|\Illuminate\Contracts\Validation\ValidationRule>>
     */
    public static function customFieldValidationRules(): array
    {
        $rules = [];
        $docMax = (int) config('tutor_application.document_max_mb', 15);

        foreach (self::activeFields() as $field) {
            if ($field->is_system) {
                continue;
            }

            $key = $field->field_key;
            $req = $field->is_required ? 'required' : 'nullable';
            $settings = $field->settings ?? [];
            $max = (int) ($settings['max'] ?? 2000);
            $optionValues = array_keys($field->resolvedOptions());

            $rules[$key] = match ($field->field_type) {
                'textarea', 'text', 'tel' => [$req, 'string', 'max:'.$max],
                'email' => [$req, 'email', 'max:'.((int) ($settings['max'] ?? 255))],
                'url' => [$req, 'string', 'max:'.((int) ($settings['max'] ?? 1000)), 'url'],
                'number' => array_values(array_filter([
                    $req,
                    'integer',
                    isset($settings['min']) ? 'min:'.(int) $settings['min'] : null,
                    isset($settings['max']) ? 'max:'.(int) $settings['max'] : null,
                ])),
                'date' => [$req, 'date'],
                'select', 'radio' => array_values(array_filter([
                    $req,
                    'string',
                    $optionValues !== [] ? 'in:'.implode(',', $optionValues) : null,
                ])),
                'multiselect', 'checkbox_group' => array_values(array_filter([
                    $req,
                    'array',
                    $field->is_required ? 'min:1' : null,
                ])),
                'file' => array_values(array_filter([
                    $req,
                    'file',
                    isset($settings['mimes']) ? 'mimes:'.$settings['mimes'] : null,
                    'max:'.($docMax * 1024),
                ])),
                default => [$req, 'string', 'max:'.$max],
            };

            if (in_array($field->field_type, ['multiselect', 'checkbox_group'], true) && $optionValues !== []) {
                $rules[$key.'.*'] = ['string', 'in:'.implode(',', $optionValues)];
            }
        }

        return $rules;
    }

    /**
     * استخراج قيم الحقول المخصصة من الطلب بعد التحقق.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<string, string>  $uploadedCustomFiles  field_key => stored path
     * @return array<string, mixed>
     */
    public static function extractCustomValues(array $validated, array $uploadedCustomFiles = []): array
    {
        $custom = [];
        foreach (self::activeFields() as $field) {
            if ($field->is_system) {
                continue;
            }
            $key = $field->field_key;
            if ($field->field_type === 'file') {
                if (isset($uploadedCustomFiles[$key])) {
                    $custom[$key] = [
                        'label' => $field->label,
                        'type' => 'file',
                        'path' => $uploadedCustomFiles[$key],
                    ];
                }
                continue;
            }
            if (! array_key_exists($key, $validated)) {
                continue;
            }
            $custom[$key] = [
                'label' => $field->label,
                'type' => $field->field_type,
                'value' => $validated[$key],
            ];
        }

        return $custom;
    }

    /**
     * حقول التسجيل الأولي — لا تظهر في صفحة إكمال الملف بعد الدخول.
     *
     * @return list<string>
     */
    public static function registrationOnlyFieldKeys(): array
    {
        return [
            'email',
            'password',
            'password_confirmation',
            'nationality',
            'country_city',
            'country_code',
            'phone',
            'linkedin_url',
        ];
    }

    /**
     * خطوات نموذج إكمال الملف بعد التسجيل (من منشئ النماذج).
     * يستبعد المقدمة وحقول إنشاء الحساب والبيانات التي جُمعت عند التسجيل.
     *
     * @return Collection<int, object{title:string,description:?string,step_type:string,activeFields:Collection}>
     */
    public static function completionSteps(): Collection
    {
        if (! self::isEnabled()) {
            return collect();
        }

        $skipKeys = self::registrationOnlyFieldKeys();
        $out = collect();

        foreach (self::activeSteps() as $step) {
            if ($step->step_type === 'intro') {
                continue;
            }

            $fields = $step->activeFields
                ->filter(function (TutorFormField $field) use ($skipKeys) {
                    if (in_array($field->field_key, $skipKeys, true)) {
                        return false;
                    }
                    if ($field->field_type === 'password' || $field->field_type === 'country_phone') {
                        return false;
                    }

                    return true;
                })
                ->values();

            if ($fields->isEmpty()) {
                continue;
            }

            $out->push((object) [
                'id' => $step->id,
                'title' => $step->title,
                'description' => $step->description,
                'step_type' => $step->step_type,
                'slug' => $step->slug,
                'activeFields' => $fields,
            ]);
        }

        return $out;
    }

    /**
     * خريطة مراحل إكمال الملف للتحقق من أخطاء السيرفر.
     *
     * @return array<int, list<string>>
     */
    public static function completionStepFieldMap(): array
    {
        $map = [];
        $index = 1;
        foreach (self::completionSteps() as $step) {
            $keys = [];
            foreach ($step->activeFields as $field) {
                $keys = array_merge($keys, self::inputKeysForField($field));
            }
            $map[$index] = array_values(array_unique($keys));
            $index++;
        }

        return $map;
    }

    public static function typeOptionsForAdmin(): array
    {
        $out = [];
        foreach (TutorFormField::TYPES as $type) {
            $tmp = new TutorFormField(['field_type' => $type]);
            $out[$type] = $tmp->typeLabel();
        }

        return $out;
    }
}

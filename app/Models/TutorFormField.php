<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorFormField extends Model
{
    public const TYPES = [
        'text',
        'textarea',
        'email',
        'password',
        'tel',
        'number',
        'url',
        'select',
        'multiselect',
        'checkbox_group',
        'radio',
        'file',
        'date',
        'country_phone',
        'weekly_availability',
        'subjects',
        'academic_years',
        'video_pair',
        'commitments',
        'matching_modes',
        'declaration',
        'info',
    ];

    protected $fillable = [
        'step_id',
        'field_key',
        'label',
        'help_text',
        'placeholder',
        'field_type',
        'is_required',
        'is_active',
        'is_system',
        'sort_order',
        'width',
        'options',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
        'options' => 'array',
        'settings' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(TutorFormStep::class, 'step_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function typeLabel(): string
    {
        return match ($this->field_type) {
            'text' => 'نص قصير',
            'textarea' => 'نص طويل',
            'email' => 'بريد إلكتروني',
            'password' => 'كلمة مرور',
            'tel' => 'هاتف',
            'number' => 'رقم',
            'url' => 'رابط',
            'select' => 'قائمة اختيار',
            'multiselect' => 'اختيار متعدد',
            'checkbox_group' => 'مجموعة مربعات',
            'radio' => 'اختيار واحد',
            'file' => 'ملف',
            'date' => 'تاريخ',
            'country_phone' => 'جوال + رمز الدولة',
            'weekly_availability' => 'توفر أسبوعي',
            'subjects' => 'مواد المنصة',
            'academic_years' => 'مسارات المنصة',
            'video_pair' => 'فيديو / رابط خارجي',
            'commitments' => 'بنود الالتزام',
            'matching_modes' => 'أنماط الاستقبال',
            'declaration' => 'إقرار وتوقيع',
            'info' => 'نص توضيحي',
            default => $this->field_type,
        };
    }

    public function resolvedOptions(): array
    {
        $options = $this->options ?? [];
        if (isset($options['source']) && is_string($options['source'])) {
            return config('tutor_application.'.$options['source'], []);
        }
        if (isset($options['items']) && is_array($options['items'])) {
            $out = [];
            foreach ($options['items'] as $item) {
                if (is_array($item) && isset($item['value'], $item['label'])) {
                    $out[(string) $item['value']] = (string) $item['label'];
                }
            }

            return $out;
        }

        // flat map value => label
        if ($options !== [] && ! isset($options['source']) && ! isset($options['items'])) {
            $flat = [];
            foreach ($options as $k => $v) {
                if (is_string($k) || is_int($k)) {
                    $flat[(string) $k] = is_string($v) ? $v : (string) $v;
                }
            }

            return $flat;
        }

        return [];
    }
}

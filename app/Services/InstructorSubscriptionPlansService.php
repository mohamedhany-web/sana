<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * إعدادات باقات اشتراك المدرب (بيانات قديمة): الأسعار، المزايا، حدود Classroom والتسويق.
 * تُخزَّن في settings.key = teacher_features (توافق البيانات القديمة).
 */
class InstructorSubscriptionPlansService
{
    public const SETTINGS_KEY = 'teacher_features';

    protected const CACHE_KEY = 'instructor_subscription_plans';

    protected const LEGACY_CACHE_KEY = 'teacher_features_settings';

    public static function getPlans(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            if (! DB::getSchemaBuilder()->hasTable('settings')) {
                return self::defaultPlans();
            }

            $row = DB::table('settings')->where('key', self::SETTINGS_KEY)->first();

            if (! $row) {
                return self::defaultPlans();
            }

            $decoded = json_decode($row->value, true);

            if (! is_array($decoded)) {
                return self::defaultPlans();
            }

            $defaults = self::defaultPlans();
            $merged = [];
            foreach (['teacher_starter', 'teacher_pro'] as $planKey) {
                $merged[$planKey] = array_merge(
                    $defaults[$planKey] ?? [],
                    is_array($decoded[$planKey] ?? null) ? $decoded[$planKey] : []
                );
                if (isset($merged[$planKey]['features']) && is_array($merged[$planKey]['features'])) {
                    $merged[$planKey]['features'] = array_values(array_filter(
                        $merged[$planKey]['features'],
                        static fn ($f) => $f !== 'zoom_access'
                    ));
                }
                if ($planKey === 'teacher_starter') {
                    $merged[$planKey]['features'] = array_values(array_filter(
                        $merged[$planKey]['features'],
                        static fn ($f) => $f !== 'classroom_access'
                    ));
                    $merged[$planKey]['limits']['classroom_meetings_per_month'] = 0;
                }
                $defaultFeatureDescriptions = $defaults[$planKey]['feature_descriptions'] ?? [];
                $decodedFeatureDescriptions = is_array($merged[$planKey]['feature_descriptions'] ?? null)
                    ? $merged[$planKey]['feature_descriptions']
                    : [];
                $merged[$planKey]['feature_descriptions'] = array_merge($defaultFeatureDescriptions, $decodedFeatureDescriptions);
            }

            return $merged;
        });
    }

    public static function savePlans(array $plans): void
    {
        foreach (['teacher_starter', 'teacher_pro'] as $key) {
            $plans[$key]['label'] = $plans[$key]['label'] ?? self::defaultPlans()[$key]['label'] ?? '';
            $plans[$key]['billing_cycle'] = 'monthly';
            $plans[$key]['features'] = isset($plans[$key]['features']) && is_array($plans[$key]['features'])
                ? array_values(array_filter(
                    $plans[$key]['features'],
                    static fn ($f) => $f !== null && $f !== '' && $f !== 'zoom_access'
                ))
                : [];
            if ($key === 'teacher_starter') {
                $plans[$key]['features'] = array_values(array_filter(
                    $plans[$key]['features'],
                    static fn ($f) => $f !== 'classroom_access'
                ));
            }
            $defaults = self::defaultPlans()[$key]['limits'] ?? [];
            $limits = $plans[$key]['limits'] ?? [];
            $plans[$key]['limits'] = [
                'classroom_meetings_per_month' => (int) ($limits['classroom_meetings_per_month'] ?? ($defaults['classroom_meetings_per_month'] ?? 0)),
                'classroom_max_participants' => (int) ($limits['classroom_max_participants'] ?? ($defaults['classroom_max_participants'] ?? 25)),
                'classroom_default_duration_minutes' => (int) ($limits['classroom_default_duration_minutes'] ?? ($defaults['classroom_default_duration_minutes'] ?? 60)),
                'classroom_max_duration_minutes' => (int) ($limits['classroom_max_duration_minutes'] ?? ($defaults['classroom_max_duration_minutes'] ?? 120)),
                'personal_marketing_profile_sections' => (int) ($limits['personal_marketing_profile_sections'] ?? ($defaults['personal_marketing_profile_sections'] ?? 5)),
                'personal_marketing_priority_score' => (int) ($limits['personal_marketing_priority_score'] ?? ($defaults['personal_marketing_priority_score'] ?? 0)),
                'personal_marketing_monthly_featured_days' => (int) ($limits['personal_marketing_monthly_featured_days'] ?? ($defaults['personal_marketing_monthly_featured_days'] ?? 0)),
            ];
            if ($plans[$key]['limits']['classroom_default_duration_minutes'] > $plans[$key]['limits']['classroom_max_duration_minutes']) {
                $plans[$key]['limits']['classroom_default_duration_minutes'] = $plans[$key]['limits']['classroom_max_duration_minutes'];
            }
            if ($key === 'teacher_starter') {
                $plans[$key]['limits']['classroom_meetings_per_month'] = 0;
                $plans[$key]['limits']['classroom_max_participants'] = 1;
                $plans[$key]['limits']['classroom_default_duration_minutes'] = 60;
                $plans[$key]['limits']['classroom_max_duration_minutes'] = 60;
            }

            $defaultDescriptions = self::defaultPlans()[$key]['feature_descriptions'] ?? [];
            $rawDescriptions = $plans[$key]['feature_descriptions'] ?? [];
            $normalizedDescriptions = [];
            if (is_array($rawDescriptions)) {
                foreach ($rawDescriptions as $featureKey => $descriptionValue) {
                    if (! is_string($featureKey)) {
                        continue;
                    }
                    $normalizedDescriptions[$featureKey] = is_string($descriptionValue)
                        ? trim($descriptionValue)
                        : '';
                }
            }
            foreach ($defaultDescriptions as $featureKey => $defaultDescription) {
                if (! array_key_exists($featureKey, $normalizedDescriptions) || $normalizedDescriptions[$featureKey] === '') {
                    $normalizedDescriptions[$featureKey] = (string) $defaultDescription;
                }
            }
            $plans[$key]['feature_descriptions'] = $normalizedDescriptions;

            $defCard = self::defaultPlans()[$key];
            foreach (['card_subtitle', 'card_badge', 'card_price_hint', 'card_cta', 'card_footer_note'] as $cf) {
                $raw = $plans[$key][$cf] ?? ($defCard[$cf] ?? '');
                $plans[$key][$cf] = is_string($raw) ? trim($raw) : '';
            }
        }

        $now = now();
        if (DB::table('settings')->where('key', self::SETTINGS_KEY)->exists()) {
            DB::table('settings')->where('key', self::SETTINGS_KEY)->update([
                'value' => json_encode($plans, JSON_UNESCAPED_UNICODE),
                'updated_at' => $now,
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => self::SETTINGS_KEY,
                'value' => json_encode($plans, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::LEGACY_CACHE_KEY);
        Cache::forget('teacher_features_settings_limits');
    }

    public static function defaultPlans(): array
    {
        return [
            'teacher_starter' => [
                'label' => 'الباقة الأساسية',
                'price' => 200,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'كل الخدمات التعليمية بدون الميتينج وخدماته',
                'card_badge' => '',
                'card_price_hint' => 'اشتراك شهري مناسب للبدء.',
                'card_cta' => 'ابدأ الآن',
                'card_footer_note' => '',
                'features' => [
                    'ai_tools',
                    'support',
                    'full_ai_suite',
                    'teacher_evaluation',
                    'direct_support',
                ],
                'feature_descriptions' => [
                    'ai_tools' => 'أدوات ذكاء اصطناعي تساعدك على تجهيز المحتوى بسرعة.',
                    'support' => 'دعم فني لمساعدتك في أي مشكلة تشغيلية داخل المنصة.',
                    'full_ai_suite' => 'مجموعة AI موسعة لتخطيط الدروس والمتابعة.',
                    'teacher_evaluation' => 'تقييم احترافي يساعدك على تحسين أدائك.',
                    'direct_support' => 'دعم مباشر وسريع للحالات المهمة.',
                ],
                'limits' => [
                    'classroom_meetings_per_month' => 0,
                    'classroom_max_participants' => 1,
                    'classroom_default_duration_minutes' => 60,
                    'classroom_max_duration_minutes' => 60,
                    'personal_marketing_profile_sections' => 5,
                    'personal_marketing_priority_score' => 40,
                    'personal_marketing_monthly_featured_days' => 0,
                ],
            ],
            'teacher_pro' => [
                'label' => 'الباقة الشاملة',
                'price' => 600,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'كل الخدمات + الميتينج وجميع خدماته',
                'card_badge' => 'الأكثر شمولاً',
                'card_price_hint' => 'اشتراك شهري يشمل جميع الأدوات واللايف ميتينج.',
                'card_cta' => 'ابدأ العمل الآن',
                'card_footer_note' => 'تشمل كامل المزايا بدون استثناء.',
                'features' => [
                    'ai_tools',
                    'classroom_access',
                    'support',
                    'full_ai_suite',
                    'teacher_evaluation',
                    'direct_support',
                ],
                'feature_descriptions' => [
                    'ai_tools' => 'أدوات ذكاء اصطناعي تساعدك على تجهيز المحتوى بسرعة.',
                    'classroom_access' => 'استخدام الفصول الافتراضية لعقد لايف ميتينج وإدارة الجلسات.',
                    'support' => 'دعم فني لمساعدتك في أي مشكلة تشغيلية داخل المنصة.',
                    'full_ai_suite' => 'مجموعة AI موسعة لتخطيط الدروس والمتابعة.',
                    'teacher_evaluation' => 'تقييم احترافي يساعدك على تحسين أدائك.',
                    'direct_support' => 'دعم مباشر وسريع للحالات المهمة.',
                ],
                'limits' => [
                    'classroom_meetings_per_month' => 9999,
                    'classroom_max_participants' => 150,
                    'classroom_default_duration_minutes' => 90,
                    'classroom_max_duration_minutes' => 480,
                    'personal_marketing_profile_sections' => 12,
                    'personal_marketing_priority_score' => 90,
                    'personal_marketing_monthly_featured_days' => 12,
                ],
            ],
        ];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * قوالب باقات اشتراك الطالب (حصص مع المعلمين).
 * تُخزَّن في settings.key = student_subscription_plans
 */
class StudentSubscriptionPlansService
{
    public const SETTINGS_KEY = 'student_subscription_plans';

    protected const CACHE_KEY = 'student_subscription_plans';

    /** @return list<string> */
    public static function planKeys(): array
    {
        return ['student_basic', 'student_standard', 'student_premium'];
    }

    public static function isStudentPlanKey(?string $key): bool
    {
        return $key !== null && $key !== '' && str_starts_with($key, 'student_')
            && in_array($key, self::planKeys(), true);
    }

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
            foreach (self::planKeys() as $planKey) {
                $merged[$planKey] = array_merge(
                    $defaults[$planKey] ?? [],
                    is_array($decoded[$planKey] ?? null) ? $decoded[$planKey] : []
                );
                $defLimits = $defaults[$planKey]['limits'] ?? ['tutor_lesson_hours' => 0];
                $limits = is_array($merged[$planKey]['limits'] ?? null) ? $merged[$planKey]['limits'] : [];
                $merged[$planKey]['limits'] = [
                    'tutor_lesson_hours' => max(0, (int) ($limits['tutor_lesson_hours'] ?? $defLimits['tutor_lesson_hours'] ?? 0)),
                ];
                $merged[$planKey]['billing_cycle'] = in_array($merged[$planKey]['billing_cycle'] ?? '', ['monthly', 'quarterly', 'yearly'], true)
                    ? $merged[$planKey]['billing_cycle']
                    : 'monthly';
                $merged[$planKey]['price'] = max(0, (float) ($merged[$planKey]['price'] ?? 0));
            }

            return $merged;
        });
    }

    public static function savePlans(array $plans): void
    {
        $normalized = [];
        $defaults = self::defaultPlans();

        foreach (self::planKeys() as $key) {
            $row = is_array($plans[$key] ?? null) ? $plans[$key] : [];
            $def = $defaults[$key] ?? [];
            $limits = is_array($row['limits'] ?? null) ? $row['limits'] : [];
            $normalized[$key] = [
                'label' => trim((string) ($row['label'] ?? $def['label'] ?? '')),
                'price' => max(0, (float) ($row['price'] ?? $def['price'] ?? 0)),
                'billing_cycle' => in_array($row['billing_cycle'] ?? '', ['monthly', 'quarterly', 'yearly'], true)
                    ? $row['billing_cycle']
                    : ($def['billing_cycle'] ?? 'monthly'),
                'card_subtitle' => trim((string) ($row['card_subtitle'] ?? $def['card_subtitle'] ?? '')),
                'card_badge' => trim((string) ($row['card_badge'] ?? $def['card_badge'] ?? '')),
                'card_price_hint' => trim((string) ($row['card_price_hint'] ?? $def['card_price_hint'] ?? '')),
                'limits' => [
                    'tutor_lesson_hours' => max(0, (int) ($limits['tutor_lesson_hours'] ?? ($def['limits']['tutor_lesson_hours'] ?? 0))),
                ],
            ];
        }

        $now = now();
        if (DB::table('settings')->where('key', self::SETTINGS_KEY)->exists()) {
            DB::table('settings')->where('key', self::SETTINGS_KEY)->update([
                'value' => json_encode($normalized, JSON_UNESCAPED_UNICODE),
                'updated_at' => $now,
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => self::SETTINGS_KEY,
                'value' => json_encode($normalized, JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function defaultPlans(): array
    {
        return [
            'student_basic' => [
                'label' => 'باقة أساسية',
                'price' => 400,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'حصص فردية مع معلم — مناسبة للبداية',
                'card_badge' => '',
                'card_price_hint' => 'شهرياً · 8 ساعات حصص',
                'limits' => ['tutor_lesson_hours' => 8],
            ],
            'student_standard' => [
                'label' => 'باقة قياسية',
                'price' => 750,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'متابعة أسبوعية مع معلم مخصّص',
                'card_badge' => 'الأكثر طلباً',
                'card_price_hint' => 'شهرياً · 16 ساعة حصص',
                'limits' => ['tutor_lesson_hours' => 16],
            ],
            'student_premium' => [
                'label' => 'باقة مميزة',
                'price' => 1400,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'مكثّف — حصص أكثر ومرونة في الحجز',
                'card_badge' => 'مكثّف',
                'card_price_hint' => 'شهرياً · 32 ساعة حصص',
                'limits' => ['tutor_lesson_hours' => 32],
            ],
        ];
    }
}

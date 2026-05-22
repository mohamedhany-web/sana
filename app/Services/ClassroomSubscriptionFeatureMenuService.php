<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * روابط مزايا باقة المعلم لعرضها داخل غرفة Classroom (تاب جديد).
 */
class ClassroomSubscriptionFeatureMenuService
{
    /**
     * مفاتيح لا نعرضها داخل الغرفة (المستخدم بالفعل هنا أو ليست صفحة مستخدم).
     */
    private const EXCLUDED_KEYS = [
        'classroom_access',
    ];

    /**
     * @return list<array{key: string, label: string, url: string, icon: string, icon_bg: string, icon_text: string}>
     */
    public static function menuItemsForUser(User $user, bool $hostUsesInstructorRoutes): array
    {
        $config = config('student_subscription_features', []);
        if (! is_array($config)) {
            return [];
        }

        $out = [];
        foreach ($config as $featureKey => $meta) {
            if (! is_string($featureKey) || $featureKey === '') {
                continue;
            }
            if (in_array($featureKey, self::EXCLUDED_KEYS, true)) {
                continue;
            }
            if (! $user->hasSubscriptionFeature($featureKey)) {
                continue;
            }
            if (! is_array($meta)) {
                continue;
            }
            // مفاتيح إدارية فقط في الإعدادات
            if (! isset($meta['route']) || ! is_string($meta['route'])) {
                continue;
            }

            $routeName = $meta['route'];
            $routeParams = isset($meta['route_params']) && is_array($meta['route_params']) ? $meta['route_params'] : [];

            if (! Route::has($routeName)) {
                continue;
            }

            try {
                $url = route($routeName, $routeParams);
            } catch (\Throwable) {
                continue;
            }

            $out[] = [
                'key' => $featureKey,
                'label' => __('student.subscription_feature.'.$featureKey),
                'url' => $url,
                'icon' => is_string($meta['icon'] ?? null) ? $meta['icon'] : 'fa-star',
                'icon_bg' => is_string($meta['icon_bg'] ?? null) ? $meta['icon_bg'] : 'bg-slate-100',
                'icon_text' => is_string($meta['icon_text'] ?? null) ? $meta['icon_text'] : 'text-slate-600',
            ];
        }

        return $out;
    }
}

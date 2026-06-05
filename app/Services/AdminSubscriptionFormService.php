<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;

class AdminSubscriptionFormService
{
    /**
     * @return array<string, mixed>
     */
    public static function formContext(?Subscription $subscription = null): array
    {
        $teacherPlans = InstructorSubscriptionPlansService::getPlans();
        $studentPlans = StudentSubscriptionPlansService::getPlans();
        $tutorSettings = TutorLessonQuotaService::settings();
        $defaultTutorHours = (int) ($tutorSettings['default_student_lesson_hours'] ?? 0);

        $typeOptions = Subscription::typeLabels();
        $featureKeysOrder = [
            'ai_tools', 'classroom_access', 'support',
            'full_ai_suite', 'teacher_evaluation', 'direct_support',
        ];

        $starter = $teacherPlans['teacher_starter'] ?? [];
        $pro = $teacherPlans['teacher_pro'] ?? [];
        $sRow = is_array($starter) ? $starter : [];
        $pRow = is_array($pro) ? $pro : [];

        $planFeatures = [
            'teacher_starter' => is_array($sRow['features'] ?? null) ? $sRow['features'] : [],
            'teacher_pro' => is_array($pRow['features'] ?? null) ? $pRow['features'] : [],
        ];

        $planApplyMeta = [];
        foreach (['teacher_starter', 'teacher_pro'] as $planKey) {
            if (! isset($teacherPlans[$planKey]) || ! is_array($teacherPlans[$planKey])) {
                continue;
            }
            $row = $teacherPlans[$planKey];
            $billingCycle = is_string($row['billing_cycle'] ?? null) ? $row['billing_cycle'] : 'monthly';
            $subscriptionType = in_array($billingCycle, array_keys($typeOptions), true) ? $billingCycle : 'monthly';
            $planApplyMeta[$planKey] = [
                'subscription_type' => $subscriptionType,
                'plan_name' => (string) ($row['label'] ?? ''),
                'price' => (float) ($row['price'] ?? 0),
                'billing_cycle' => $billingCycle,
                'limits' => SubscriptionLimitService::limitsArrayForPlanKey($teacherPlans, $planKey),
            ];
        }

        $starterF = is_array($sRow['features'] ?? null) ? $sRow['features'] : [];
        $proF = is_array($pRow['features'] ?? null) ? $pRow['features'] : [];
        $manualDefaultFeatures = array_values(array_unique(array_merge(
            array_map('strval', $starterF),
            array_map('strval', $proF)
        )));

        $sDesc = is_array($sRow['feature_descriptions'] ?? null) ? $sRow['feature_descriptions'] : [];
        $pDesc = is_array($pRow['feature_descriptions'] ?? null) ? $pRow['feature_descriptions'] : [];
        $featureDisplayLines = [];
        foreach ($featureKeysOrder as $fk) {
            $hint = trim((string) ($pDesc[$fk] ?? ''));
            if ($hint === '') {
                $hint = trim((string) ($sDesc[$fk] ?? ''));
            }
            $featureDisplayLines[$fk] = $hint !== '' ? $hint : __('student.subscription_feature.'.$fk);
        }

        $manualTemplateLimits = SubscriptionLimitService::limitsArrayForPlanKey($teacherPlans, 'teacher_pro');
        $manualTemplateLimits['tutor_lesson_hours'] = $defaultTutorHours;

        $studentPlanApplyMeta = [];
        foreach (StudentSubscriptionPlansService::planKeys() as $planKey) {
            if (! isset($studentPlans[$planKey]) || ! is_array($studentPlans[$planKey])) {
                continue;
            }
            $row = $studentPlans[$planKey];
            $billingCycle = is_string($row['billing_cycle'] ?? null) ? $row['billing_cycle'] : 'monthly';
            $subscriptionType = in_array($billingCycle, array_keys($typeOptions), true) ? $billingCycle : 'monthly';
            $limits = is_array($row['limits'] ?? null) ? $row['limits'] : [];
            $studentPlanApplyMeta[$planKey] = [
                'subscription_type' => $subscriptionType,
                'plan_name' => (string) ($row['label'] ?? ''),
                'price' => (float) ($row['price'] ?? 0),
                'billing_cycle' => $billingCycle,
                'limits' => [
                    'tutor_lesson_hours' => max(0, (int) ($limits['tutor_lesson_hours'] ?? $defaultTutorHours)),
                ],
            ];
        }

        $firstStudentKey = StudentSubscriptionPlansService::planKeys()[0] ?? 'student_basic';
        $defaultStudentPlanKey = isset($studentPlanApplyMeta[$firstStudentKey]) ? $firstStudentKey : '';

        if ($subscription) {
            $storedKey = (string) ($subscription->teacher_plan_key ?? '');
            if (StudentSubscriptionPlansService::isStudentPlanKey($storedKey)) {
                $editPlanKey = $storedKey;
            } elseif ($storedKey !== '' && isset($teacherPlans[$storedKey])) {
                $editPlanKey = $storedKey;
            } else {
                $editPlanKey = 'teacher_starter';
            }

            if (StudentSubscriptionPlansService::isStudentPlanKey($editPlanKey)) {
                $planLimits = $studentPlanApplyMeta[$editPlanKey]['limits'] ?? ['tutor_lesson_hours' => $defaultTutorHours];
            } else {
                $planLimits = SubscriptionLimitService::limitsArrayForPlanKey($teacherPlans, $editPlanKey);
            }

            $initialLimits = array_merge(
                $planLimits,
                is_array($subscription->feature_limits) ? $subscription->feature_limits : [],
                is_array(old('limits')) ? old('limits') : []
            );
            $checkedFeatures = array_keys(array_filter((array) old('features', [])));
            if ($checkedFeatures === []) {
                $checkedFeatures = Subscription::normalizeFeatureKeys($subscription->features ?? []);
            }
            $initialSubscriberRole = $subscription->user?->role ?? '';
            $initialStudentPlanKey = StudentSubscriptionPlansService::isStudentPlanKey($storedKey) ? $storedKey : '';
            $initialStudentPackageMode = $initialStudentPlanKey !== '' ? 'template' : 'custom';
        } else {
            $defaultStudentLimits = $defaultStudentPlanKey !== '' && isset($studentPlanApplyMeta[$defaultStudentPlanKey])
                ? ($studentPlanApplyMeta[$defaultStudentPlanKey]['limits'] ?? ['tutor_lesson_hours' => $defaultTutorHours])
                : ['tutor_lesson_hours' => $defaultTutorHours];
            $initialLimits = array_merge(
                ['tutor_lesson_hours' => $defaultTutorHours],
                $defaultStudentLimits,
                is_array(old('limits')) ? old('limits') : []
            );
            $checkedFeatures = array_keys(array_filter((array) old('features', [])));
            $initialSubscriberRole = '';
            $initialStudentPlanKey = old('teacher_plan_key', $defaultStudentPlanKey);
            if (! StudentSubscriptionPlansService::isStudentPlanKey((string) $initialStudentPlanKey)) {
                $initialStudentPlanKey = $defaultStudentPlanKey;
            }
            $initialStudentPackageMode = old('teacher_plan_key') && ! StudentSubscriptionPlansService::isStudentPlanKey((string) old('teacher_plan_key'))
                ? 'custom'
                : 'template';
        }

        return compact(
            'teacherPlans',
            'studentPlans',
            'tutorSettings',
            'defaultTutorHours',
            'typeOptions',
            'featureKeysOrder',
            'planFeatures',
            'planApplyMeta',
            'studentPlanApplyMeta',
            'defaultStudentPlanKey',
            'manualDefaultFeatures',
            'featureDisplayLines',
            'manualTemplateLimits',
            'initialLimits',
            'checkedFeatures',
            'initialSubscriberRole',
            'initialStudentPlanKey',
            'initialStudentPackageMode'
        ) + [
            'starter' => $starter,
            'pro' => $pro,
        ];
    }

    public static function subscribersForSelect()
    {
        return User::query()
            ->whereIn('role', ['student', 'instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'role']);
    }
}

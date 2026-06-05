<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\StudentControlOverviewService;
use App\Services\StudentSubscriptionPlansService;
use App\Services\TutorLessonQuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MySubscriptionController extends Controller
{
    /**
     * عرض صفحة اشتراك الطالب الحالي (الباقة، المدة، تاريخ الانتهاء، المزايا).
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription();

        if (! $subscription) {
            return redirect()->route('public.pricing')
                ->with('info', 'ليس لديك اشتراك نشط. يمكنك الاشتراك في إحدى الباقات من صفحة التسعير.');
        }

        $planKey = (string) ($subscription->teacher_plan_key ?? '');
        $isStudentPlan = StudentSubscriptionPlansService::isStudentPlanKey($planKey);
        $planPackage = $isStudentPlan
            ? (StudentSubscriptionPlansService::getPlans()[$planKey] ?? null)
            : null;

        $displayPlanName = trim((string) $subscription->plan_name);
        if ($displayPlanName === '' && is_array($planPackage)) {
            $displayPlanName = (string) ($planPackage['label'] ?? 'باقة اشتراك');
        }
        if ($displayPlanName === '') {
            $displayPlanName = (string) ($subscription->instructorPlanLabel() ?? 'باقة اشتراك');
        }

        TutorLessonQuotaService::syncProfileForUser($user);
        $learningProfile = $user->studentLearningProfile;

        $tutorHoursQuota = TutorLessonQuotaService::quotaHoursForUser($user);
        $tutorHoursFromLimits = $subscription->tutorLessonHoursFromLimits();
        $tutorHoursUsed = (int) ($learningProfile?->lesson_hours_used ?? 0);
        $tutorHoursRemaining = max(0, $tutorHoursQuota - $tutorHoursUsed);
        $tutorHoursPercent = $tutorHoursQuota > 0
            ? min(100, round(($tutorHoursUsed / $tutorHoursQuota) * 100, 1))
            : 0;

        $displayFeatures = $user->subscriptionResolvedFeatureKeys();
        $hasSupport = StudentControlOverviewService::subscriptionHasFeature($subscription, 'support')
            || StudentControlOverviewService::subscriptionHasFeature($subscription, 'direct_support');

        $daysRemaining = null;
        if ($subscription->end_date) {
            $daysRemaining = (int) now()->startOfDay()->diffInDays($subscription->end_date->startOfDay(), false);
        }

        $teacherUpgradeOptions = $this->teacherUpgradeOptions($subscription);
        $studentUpgradeOptions = $this->studentUpgradeOptions($planKey, $isStudentPlan);

        $displayPrice = (float) $subscription->price;
        if ($displayPrice <= 0 && is_array($planPackage)) {
            $displayPrice = (float) ($planPackage['price'] ?? 0);
        }

        return view('student.my-subscription', [
            'subscription' => $subscription,
            'displayPlanName' => $displayPlanName,
            'isStudentPlan' => $isStudentPlan,
            'planKey' => $planKey,
            'planPackage' => $planPackage,
            'displayPrice' => $displayPrice,
            'displayFeatures' => $displayFeatures,
            'hasSupport' => $hasSupport,
            'tutorHoursQuota' => $tutorHoursQuota,
            'tutorHoursFromLimits' => $tutorHoursFromLimits,
            'tutorHoursUsed' => $tutorHoursUsed,
            'tutorHoursRemaining' => $tutorHoursRemaining,
            'tutorHoursPercent' => $tutorHoursPercent,
            'daysRemaining' => $daysRemaining,
            'teacherUpgradeOptions' => $teacherUpgradeOptions,
            'studentUpgradeOptions' => $studentUpgradeOptions,
        ]);
    }

    /** @return Collection<int, string> */
    private function teacherUpgradeOptions(Subscription $subscription): Collection
    {
        $planRank = ['teacher_starter' => 1, 'teacher_pro' => 2];
        $currentKey = (string) ($subscription->teacher_plan_key ?? '');
        $currentRank = $planRank[$currentKey] ?? 0;

        return collect(['teacher_starter', 'teacher_pro'])
            ->filter(fn ($k) => ($planRank[$k] ?? 0) > $currentRank)
            ->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function studentUpgradeOptions(string $planKey, bool $isStudentPlan): Collection
    {
        if (! $isStudentPlan) {
            return collect();
        }

        $planRank = ['student_basic' => 1, 'student_standard' => 2, 'student_premium' => 3];
        $currentRank = $planRank[$planKey] ?? 0;
        $allPlans = StudentSubscriptionPlansService::getPlans();

        return collect(StudentSubscriptionPlansService::planKeys())
            ->filter(fn ($k) => ($planRank[$k] ?? 0) > $currentRank)
            ->map(fn ($k) => array_merge(['key' => $k], is_array($allPlans[$k] ?? null) ? $allPlans[$k] : []))
            ->values();
    }
}

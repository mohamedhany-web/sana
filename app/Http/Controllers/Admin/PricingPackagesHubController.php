<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\TutorHourPurchase;
use App\Services\StudentSubscriptionPlansService;
use App\Services\TutorLessonQuotaService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class PricingPackagesHubController extends Controller
{
    public function index()
    {
        $studentPlans = StudentSubscriptionPlansService::getPlans();
        $buyableCount = count(TutorLessonQuotaService::purchasablePlans());

        $stats = [
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'pending_hour_purchases' => Schema::hasTable('tutor_hour_purchases')
                ? TutorHourPurchase::where('status', TutorHourPurchase::STATUS_PENDING)->count()
                : 0,
            'course_packages' => Schema::hasTable('packages')
                ? Package::query()->count()
                : 0,
            'student_plan_templates' => count(StudentSubscriptionPlansService::planKeys()),
            'buyable_plans' => $buyableCount,
        ];

        $cards = [
            [
                'title' => 'الاشتراكات',
                'desc' => 'تفعيل وإدارة اشتراكات الطلاب والمدربين',
                'icon' => 'fa-calendar-check',
                'color' => 'from-blue-500 to-blue-600',
                'route' => 'admin.subscriptions.index',
                'permission' => 'manage.subscriptions',
                'meta' => number_format($stats['active_subscriptions']).' نشط',
            ],
            [
                'title' => 'باقات واشتراكات الطلاب',
                'desc' => 'ملخص الباقات والطلاب على كل قالب اشتراك',
                'icon' => 'fa-layer-group',
                'color' => 'from-violet-500 to-indigo-600',
                'route' => 'admin.students-control.paid-features',
                'permission' => null,
                'meta' => $stats['student_plan_templates'].' قوالب',
            ],
            [
                'title' => 'قوالب باقات الحصص والأسعار',
                'desc' => 'أسعار باقات الطلاب، الساعات، وتفعيل الشراء من المنصة',
                'icon' => 'fa-tags',
                'color' => 'from-emerald-500 to-teal-600',
                'route' => 'admin.tutor-lessons.settings',
                'permission' => 'manage.tutor-lessons',
                'meta' => $stats['buyable_plans'].' متاحة للشراء',
            ],
            [
                'title' => 'طلبات شراء الساعات',
                'desc' => 'مراجعة إيصالات شراء ساعات الحصص وإضافتها للرصيد',
                'icon' => 'fa-clock',
                'color' => 'from-amber-500 to-orange-600',
                'route' => 'admin.tutor-lessons.hour-purchases.index',
                'permission' => 'manage.tutor-lessons',
                'meta' => number_format($stats['pending_hour_purchases']).' قيد المراجعة',
            ],
            [
                'title' => 'باقات وأسعار الكورسات',
                'desc' => 'باقات الكورسات المجمّعة وأسعار الكورسات الفردية',
                'icon' => 'fa-box',
                'color' => 'from-sky-500 to-cyan-600',
                'route' => 'admin.packages.index',
                'permission' => 'manage.packages',
                'meta' => number_format($stats['course_packages']).' باقة كورس',
            ],
        ];

        $u = auth()->user();
        $isFull = method_exists($u, 'isAdmin') ? $u->isAdmin() : false;
        $cards = array_values(array_filter($cards, function (array $card) use ($u, $isFull) {
            if (! Route::has($card['route'])) {
                return false;
            }
            if ($card['permission'] === null) {
                return $isFull
                    || $u->hasPermission('manage.subscriptions')
                    || $u->hasPermission('manage.student-control')
                    || $u->hasPermission('manage.packages')
                    || $u->hasPermission('manage.tutor-lessons');
            }

            return $isFull || $u->hasPermission($card['permission']);
        }));

        return view('admin.pricing-packages.hub', compact('cards', 'stats', 'studentPlans'));
    }
}

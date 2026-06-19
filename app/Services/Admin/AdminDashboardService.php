<?php

namespace App\Services\Admin;

use App\Models\ActivityLog;
use App\Models\AdvancedCourse;
use App\Models\ExamAttempt;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\StatisticsCacheService;
use App\Support\SqlGroupExpressions;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AdminDashboardService
{
    public function __construct(
        private StatisticsCacheService $statsCache,
    ) {}

    public function payload(User $user): array
    {
        return Cache::remember(
            'admin_dashboard_payload:v2:'.$user->id,
            now()->addMinutes(3),
            fn () => $this->buildPayload($user)
        );
    }

    public static function bustCache(int $userId): void
    {
        Cache::forget('admin_dashboard_payload:v2:'.$userId);
    }

    private function buildPayload(User $user): array
    {
        $dashboardUnrestricted = $user->isAdmin() && ! $user->hasAssignedRbacRoles();
        $canDash = function (array $permissions) use ($user, $dashboardUnrestricted): bool {
            if ($dashboardUnrestricted) {
                return true;
            }
            foreach ($permissions as $perm) {
                if ($user->hasPermission($perm)) {
                    return true;
                }
            }

            return false;
        };

        $dashboardShow = $this->dashboardVisibility($canDash);
        $cachedStats = $this->statsCache->getDashboardStats();
        $monthly = $this->statsCache->getDashboardMonthlyComparisons();

        $stats = $cachedStats;
        if ($dashboardShow['activity_feed']) {
            $stats['recent_activities'] = ActivityLog::query()
                ->with('user:id,name')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $stats['recent_activities'] = collect();
        }

        if ($dashboardShow['exam_attempts']) {
            $stats['recent_exam_attempts'] = ExamAttempt::query()
                ->with(['exam:id,title', 'student:id,name'])
                ->where('status', 'submitted')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $stats['recent_exam_attempts'] = collect();
        }

        $metrics = $this->buildMetrics($cachedStats, $monthly, $dashboardShow);

        if (! $dashboardShow['monthly_revenue']) {
            $stats['monthly_revenue'] = 0;
        }
        if (! $dashboardShow['revenue_total']) {
            $stats['total_revenue'] = 0;
        }

        $recent_users = $dashboardShow['recent_users']
            ? User::query()->latest()->limit(5)->get(['id', 'name', 'email', 'phone', 'role', 'created_at'])
            : null;

        $recent_courses = $dashboardShow['recent_courses']
            ? AdvancedCourse::query()->with(['academicSubject:id,name'])->latest()->limit(5)->get()
            : null;

        $pending_invoices = $dashboardShow['invoices_panel']
            ? Invoice::query()->where('status', 'pending')->with('user:id,name')->latest()->limit(5)->get()
            : collect();

        $recent_payments = $dashboardShow['payments_panel']
            ? Payment::query()->where('status', 'completed')->with(['user:id,name', 'invoice:id,invoice_number'])->latest()->limit(5)->get()
            : collect();

        $salesSection = $dashboardShow['sales_section'] ? $this->salesSection($monthly['periods']) : null;
        $hrSection = $dashboardShow['hr_section'] ? $this->hrSection($monthly['periods']) : null;
        $subscriptionPackages = $dashboardShow['subscriptions_section']
            ? $this->subscriptionPackages()
            : null;

        $summaries = $this->statsCache->getDashboardActionSummaries();
        $quickActions = $this->quickActions($canDash, $summaries);

        return compact(
            'stats',
            'metrics',
            'recent_users',
            'recent_courses',
            'pending_invoices',
            'recent_payments',
            'quickActions',
            'salesSection',
            'hrSection',
            'subscriptionPackages',
            'dashboardShow'
        );
    }

    private function dashboardVisibility(callable $canDash): array
    {
        return [
            'users_metric' => $canDash(['manage.users', 'manage.students-accounts', 'view.statistics', 'view.reports', 'manage.student-control', 'manage.quality-control']),
            'students_metric' => $canDash(['manage.students-accounts', 'manage.users', 'view.statistics', 'view.reports', 'manage.student-control']),
            'instructors_metric' => $canDash(['manage.users', 'view.statistics', 'view.reports', 'view.academic-reports']),
            'courses_metric' => $canDash(['manage.courses', 'view.statistics', 'view.academic-reports', 'manage.lectures', 'manage.enrollments', 'manage.assignments']),
            'revenue_total' => $canDash(['manage.payments', 'manage.invoices', 'view.financial-reports', 'view.statistics', 'manage.transactions', 'manage.wallets', 'view.wallets', 'manage.orders', 'manage.expenses', 'manage.salaries', 'manage.instructor-accounts']),
            'monthly_revenue' => $canDash(['manage.payments', 'manage.invoices', 'view.financial-reports', 'view.statistics', 'manage.transactions', 'manage.wallets', 'view.wallets', 'manage.orders', 'manage.expenses', 'manage.salaries', 'manage.instructor-accounts']),
            'pending_invoices_metric' => $canDash(['manage.invoices', 'view.financial-reports']),
            'enrollments_metric' => $canDash(['manage.enrollments', 'manage.courses', 'view.statistics', 'view.reports', 'view.academic-reports', 'manage.student-control']),
            'activity_feed' => $canDash(['view.activity-log', 'view.reports']),
            'exam_attempts' => $canDash(['manage.exams', 'manage.question-bank']),
            'recent_users' => $canDash(['manage.users', 'manage.students-accounts', 'manage.student-control', 'view.reports']),
            'recent_courses' => $canDash(['manage.courses', 'manage.lectures', 'manage.enrollments']),
            'sales_section' => $canDash(['manage.orders', 'manage.leads', 'view.sales-analytics', 'manage.coupons', 'manage.referrals']),
            'hr_section' => $canDash(['manage.users', 'manage.leaves', 'manage.employee-agreements', 'manage.instructor-requests']),
            'subscriptions_section' => $canDash(['manage.subscriptions']),
            'invoices_panel' => $canDash(['manage.invoices', 'view.financial-reports']),
            'payments_panel' => $canDash(['manage.payments', 'view.financial-reports', 'manage.transactions']),
        ];
    }

    private function buildMetrics(array $cachedStats, array $monthly, array $dashboardShow): array
    {
        $metrics = [
            'users' => [
                'total' => $dashboardShow['users_metric'] ? $cachedStats['total_users'] : 0,
                'new_this_month' => $dashboardShow['users_metric'] ? $monthly['new_users']['current'] : 0,
                'trend' => $dashboardShow['users_metric'] ? $monthly['new_users'] : null,
            ],
            'students' => [
                'total' => $dashboardShow['students_metric'] ? $cachedStats['total_students'] : 0,
                'new_this_month' => $dashboardShow['students_metric'] ? $monthly['new_students']['current'] : 0,
                'trend' => $dashboardShow['students_metric'] ? $monthly['new_students'] : null,
            ],
            'instructors' => [
                'total' => $dashboardShow['instructors_metric'] ? $cachedStats['total_instructors'] : 0,
                'new_this_month' => $dashboardShow['instructors_metric'] ? $monthly['new_instructors']['current'] : 0,
                'trend' => $dashboardShow['instructors_metric'] ? $monthly['new_instructors'] : null,
            ],
            'courses' => [
                'total' => $dashboardShow['courses_metric'] ? $cachedStats['total_courses'] : 0,
                'new_this_month' => $dashboardShow['courses_metric'] ? $monthly['new_courses']['current'] : 0,
                'trend' => $dashboardShow['courses_metric'] ? $monthly['new_courses'] : null,
            ],
            'enrollments' => [
                'total' => $dashboardShow['enrollments_metric'] ? $cachedStats['total_enrollments'] : 0,
                'new_this_month' => $dashboardShow['enrollments_metric'] ? $monthly['active_enrollments']['current'] : 0,
                'trend' => $dashboardShow['enrollments_metric'] ? $monthly['active_enrollments'] : null,
            ],
            'monthly_revenue' => [
                'current' => $dashboardShow['monthly_revenue'] ? $cachedStats['monthly_revenue'] : 0,
                'trend' => $dashboardShow['monthly_revenue'] ? $monthly['monthly_revenue'] : null,
            ],
            'pending_invoices' => [
                'total' => $dashboardShow['pending_invoices_metric'] ? $cachedStats['pending_invoices'] : 0,
                'new_this_month' => $dashboardShow['pending_invoices_metric'] ? $monthly['pending_invoices']['current'] : 0,
                'trend' => $dashboardShow['pending_invoices_metric'] ? $monthly['pending_invoices'] : null,
            ],
        ];

        if (! $dashboardShow['revenue_total']) {
            $cachedStats['total_revenue'] = 0;
        }

        return $metrics;
    }

    private function salesSection(array $periods): array
    {
        [$currentStart, $currentEnd] = $periods['current'];

        return [
            'recent_orders' => Order::query()
                ->with(['user:id,name', 'course:id,title'])
                ->latest()
                ->limit(5)
                ->get(),
            'orders_pending' => Order::query()->where('status', Order::STATUS_PENDING)->count(),
            'orders_approved_month' => Order::query()
                ->where('status', Order::STATUS_APPROVED)
                ->whereBetween('approved_at', [$currentStart, $currentEnd])
                ->count(),
            'revenue_month' => Order::query()
                ->where('status', Order::STATUS_APPROVED)
                ->whereBetween('approved_at', [$currentStart, $currentEnd])
                ->sum('amount'),
        ];
    }

    private function hrSection(array $periods): array
    {
        [$currentStart, $currentEnd] = $periods['current'];

        return [
            'employees_total' => User::employees()->count(),
            'employees_active' => User::employees()->where('is_active', true)->whereNull('termination_date')->count(),
            'leaves_pending' => LeaveRequest::query()->where('status', 'pending')->count(),
            'leaves_approved_month' => LeaveRequest::query()
                ->where('status', 'approved')
                ->whereBetween('reviewed_at', [$currentStart, $currentEnd])
                ->count(),
            'recent_leaves' => LeaveRequest::query()
                ->with(['employee:id,name', 'employee.employeeJob:id,name'])
                ->latest()
                ->limit(5)
                ->get(),
            'recent_employees' => User::employees()
                ->with('employeeJob:id,name')
                ->latest('hire_date')
                ->limit(5)
                ->get(['id', 'name', 'hire_date', 'created_at', 'employee_job_id']),
        ];
    }

    private function subscriptionPackages(): Collection
    {
        $planExpr = SqlGroupExpressions::subscriptionPlanLabel('plan_name');

        $planRows = Subscription::query()
            ->selectRaw("{$planExpr} as plan_label, COUNT(*) as aggregate_count")
            ->groupByRaw($planExpr)
            ->orderByRaw($planExpr)
            ->limit(12)
            ->get();

        return $planRows->map(function ($row) {
            $label = (string) $row->plan_label;
            $query = Subscription::query()
                ->with(['user:id,name,phone,email'])
                ->latest()
                ->limit(15);

            if ($label === 'غير محدد') {
                $query->where(function ($q) {
                    $q->whereNull('plan_name')->orWhere('plan_name', '');
                });
            } else {
                $query->where('plan_name', $label);
            }

            return [
                'plan_name' => $label,
                'count' => (int) $row->aggregate_count,
                'subscriptions' => $query->get(),
            ];
        })->values();
    }

    private function quickActions(callable $canDash, array $summaries): array
    {
        $inactiveLatest = $summaries['inactive_student_latest_created_at'] ?? null;

        $actions = [
            [
                'title' => 'فواتير معلقة',
                'count' => (int) ($summaries['pending_invoices_count'] ?? 0),
                'meta' => 'إجمالي '.number_format($summaries['pending_invoices_amount'] ?? 0, 2).currency_suffix(),
                'icon' => 'fas fa-file-invoice-dollar',
                'icon_background' => 'from-amber-500 to-orange-600',
                'route' => route('admin.invoices.index', ['status' => 'pending']),
                'permissions' => ['manage.invoices'],
            ],
            [
                'title' => 'فواتير متأخرة',
                'count' => (int) ($summaries['overdue_invoices_count'] ?? 0),
                'meta' => 'قيمة '.number_format($summaries['overdue_invoices_amount'] ?? 0, 2).currency_suffix(),
                'icon' => 'fas fa-exclamation-triangle',
                'icon_background' => 'from-rose-500 to-red-600',
                'route' => route('admin.invoices.index', ['status' => 'overdue']),
                'permissions' => ['manage.invoices'],
            ],
            [
                'title' => 'طلبات في الانتظار',
                'count' => (int) ($summaries['pending_orders_count'] ?? 0),
                'meta' => 'قيمة '.number_format($summaries['pending_orders_amount'] ?? 0, 2).currency_suffix(),
                'icon' => 'fas fa-shopping-bag',
                'icon_background' => 'from-sky-500 to-slate-600',
                'route' => route('admin.orders.index', ['status' => 'pending']),
                'permissions' => ['manage.orders'],
            ],
            [
                'title' => 'طلاب يحتاجون التفعيل',
                'count' => (int) ($summaries['inactive_students_count'] ?? 0),
                'meta' => ($summaries['inactive_students_count'] ?? 0) > 0
                    ? 'آخر تسجيل: '.(Carbon::parse($inactiveLatest)->diffForHumans() ?? 'غير متوفر')
                    : 'كل الحسابات مفعلة',
                'icon' => 'fas fa-user-clock',
                'icon_background' => 'from-gray-500 to-slate-600',
                'route' => route('admin.users.index', ['role' => 'student', 'status' => 0]),
                'permissions' => ['manage.users', 'manage.students-accounts'],
            ],
            [
                'title' => 'اتفاقيات تقسيط معلقة',
                'count' => (int) ($summaries['pending_installments_count'] ?? 0),
                'meta' => 'قيمة '.number_format($summaries['pending_installments_amount'] ?? 0, 2).currency_suffix(),
                'icon' => 'fas fa-hand-holding-usd',
                'icon_background' => 'from-emerald-500 to-green-600',
                'route' => route('admin.installments.agreements.index', ['status' => 'pending']),
                'permissions' => ['manage.installments'],
            ],
            [
                'title' => 'تذاكر دعم مفتوحة',
                'count' => (int) ($summaries['open_support_tickets_count'] ?? 0),
                'meta' => 'تحتاج متابعة',
                'icon' => 'fas fa-headset',
                'icon_background' => 'from-violet-500 to-purple-600',
                'route' => route('admin.support-tickets.index'),
                'permissions' => ['manage.support-tickets'],
            ],
            [
                'title' => 'طلبات سحب معلقة',
                'count' => (int) ($summaries['pending_withdrawals_count'] ?? 0),
                'meta' => 'في انتظار المراجعة',
                'icon' => 'fas fa-money-bill-wave',
                'icon_background' => 'from-orange-500 to-amber-600',
                'route' => route('admin.withdrawals.index', ['status' => 'pending']),
                'permissions' => ['manage.withdrawals'],
            ],
            [
                'title' => 'عملاء محتملون جدد',
                'count' => (int) ($summaries['new_sales_leads_count'] ?? 0),
                'meta' => 'Leads بحالة «جديد»',
                'icon' => 'fas fa-user-plus',
                'icon_background' => 'from-emerald-500 to-teal-600',
                'route' => route('admin.sales.leads.index', ['status' => 'new']),
                'permissions' => ['manage.leads'],
            ],
            [
                'title' => 'كوبونات نشطة',
                'count' => (int) ($summaries['active_coupons_count'] ?? 0),
                'meta' => 'متاحة للاستخدام',
                'icon' => 'fas fa-ticket-alt',
                'icon_background' => 'from-pink-500 to-rose-600',
                'route' => route('admin.coupons.index'),
                'permissions' => ['manage.coupons'],
            ],
        ];

        return collect($actions)
            ->filter(fn (array $a) => $canDash($a['permissions'] ?? []))
            ->map(function (array $a) {
                unset($a['permissions']);

                return $a;
            })
            ->values()
            ->all();
    }
}

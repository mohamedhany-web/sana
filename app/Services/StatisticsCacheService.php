<?php

namespace App\Services;

use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\Coupon;
use Illuminate\Support\Facades\Cache;

class StatisticsCacheService
{
    /**
     * الحصول على إحصائيات لوحة التحكم مع التخزين المؤقت
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', now()->addMinutes(5), function () {
            $now = now();
            $currentPeriodStart = $now->copy()->startOfMonth();
            $currentPeriodEnd = $now;
            $previousPeriodStart = $now->copy()->subMonth()->startOfMonth();
            $previousPeriodEnd = $now->copy()->subMonth()->endOfMonth();

            $stats = [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_instructors' => User::where('role', 'instructor')->count(),
                'total_courses' => AdvancedCourse::count(),
                'published_courses' => AdvancedCourse::where('is_active', true)->count(),
                'total_subjects' => \App\Models\AcademicSubject::count(),
                'total_exams' => \App\Models\AdvancedExam::count(),
                'total_question_banks' => \App\Models\QuestionBank::count(),
                'active_students' => User::where('role', 'student')
                                       ->where('is_active', true)
                                       ->whereHas('courseEnrollments')
                                       ->count(),
                'total_revenue' => Payment::where('status', 'completed')->sum('amount') ?? 0,
                'pending_invoices' => Invoice::where('status', 'pending')->count() ?? 0,
                'overdue_invoices' => Invoice::where('status', 'overdue')->count() ?? 0,
                'total_enrollments' => StudentCourseEnrollment::where('status', 'active')->count() ?? 0,
                'monthly_revenue' => Payment::where('status', 'completed')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('amount') ?? 0,
                'total_wallet_balance' => Wallet::where('is_active', true)->sum('balance') ?? 0,
            ];

            return $stats;
        });
    }

    /**
     * الحصول على إحصائيات التسجيلات مع التخزين المؤقت
     */
    public function getEnrollmentStats(): array
    {
        return Cache::remember('enrollment_stats', now()->addMinutes(10), function () {
            return [
                'total' => StudentCourseEnrollment::count(),
                'pending' => StudentCourseEnrollment::where('status', 'pending')->count(),
                'active' => StudentCourseEnrollment::where('status', 'active')->count(),
                'completed' => StudentCourseEnrollment::where('status', 'completed')->count(),
                'suspended' => StudentCourseEnrollment::where('status', 'suspended')->count(),
            ];
        });
    }

    /**
     * الحصول على إحصائيات المستخدمين مع التخزين المؤقت
     */
    public function getUserStats(): array
    {
        return Cache::remember('user_stats', now()->addMinutes(10), function () {
            return [
                'total' => User::count(),
                'students' => User::where('role', 'student')->count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'active_students' => User::where('role', 'student')
                    ->where('is_active', true)
                    ->count(),
            ];
        });
    }

    /**
     * مسح جميع الإحصائيات من الكاش
     */
    public function clearAllStats(): void
    {
        Cache::forget('dashboard_stats');
        Cache::forget('enrollment_stats');
        Cache::forget('user_stats');
        Cache::forget('dashboard_monthly_comparisons');
        Cache::forget('dashboard_action_summaries');
    }

    /**
     * مقارنات شهرية للوحة الإدارة (مخزّنة مؤقتاً).
     */
    public function getDashboardMonthlyComparisons(): array
    {
        return Cache::remember('dashboard_monthly_comparisons', now()->addMinutes(5), function () {
            $now = now();
            $currentPeriodStart = $now->copy()->startOfMonth();
            $currentPeriodEnd = $now;
            $previousPeriodStart = $now->copy()->subMonth()->startOfMonth();
            $previousPeriodEnd = $now->copy()->subMonth()->endOfMonth();

            $newUsersThisMonth = User::whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $newUsersLastMonth = User::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $newStudentsThisMonth = User::where('role', 'student')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $newStudentsLastMonth = User::where('role', 'student')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $newInstructorsThisMonth = User::where('role', 'instructor')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $newInstructorsLastMonth = User::where('role', 'instructor')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $newCoursesThisMonth = AdvancedCourse::whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $newCoursesLastMonth = AdvancedCourse::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $activeEnrollmentsThisMonth = StudentCourseEnrollment::where('status', 'active')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $activeEnrollmentsLastMonth = StudentCourseEnrollment::where('status', 'active')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            $monthlyRevenueCurrent = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$currentPeriodStart, $currentPeriodEnd])
                ->sum('amount') ?? 0;
            $monthlyRevenuePrevious = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$previousPeriodStart, $previousPeriodEnd])
                ->sum('amount') ?? 0;

            $pendingInvoicesThisMonth = Invoice::where('status', 'pending')->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])->count();
            $pendingInvoicesLastMonth = Invoice::where('status', 'pending')->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();

            return [
                'periods' => [
                    'current' => [$currentPeriodStart, $currentPeriodEnd],
                    'previous' => [$previousPeriodStart, $previousPeriodEnd],
                ],
                'new_users' => $this->calculateChange($newUsersThisMonth, $newUsersLastMonth),
                'new_students' => $this->calculateChange($newStudentsThisMonth, $newStudentsLastMonth),
                'new_instructors' => $this->calculateChange($newInstructorsThisMonth, $newInstructorsLastMonth),
                'new_courses' => $this->calculateChange($newCoursesThisMonth, $newCoursesLastMonth),
                'active_enrollments' => $this->calculateChange($activeEnrollmentsThisMonth, $activeEnrollmentsLastMonth),
                'monthly_revenue' => $this->calculateChange($monthlyRevenueCurrent, $monthlyRevenuePrevious),
                'pending_invoices' => $this->calculateChange($pendingInvoicesThisMonth, $pendingInvoicesLastMonth),
            ];
        });
    }

    /**
     * ملخصات بطاقات الإجراءات السريعة في لوحة الإدارة.
     */
    public function getDashboardActionSummaries(): array
    {
        return Cache::remember('dashboard_action_summaries', now()->addMinutes(3), function () {
            $summaries = [
                'pending_invoices_count' => 0,
                'pending_invoices_amount' => 0.0,
                'overdue_invoices_count' => 0,
                'overdue_invoices_amount' => 0.0,
                'pending_orders_count' => 0,
                'pending_orders_amount' => 0.0,
                'inactive_students_count' => 0,
                'inactive_student_latest_created_at' => null,
                'pending_installments_count' => 0,
                'pending_installments_amount' => 0.0,
                'open_support_tickets_count' => 0,
                'pending_withdrawals_count' => 0,
                'new_sales_leads_count' => 0,
                'active_coupons_count' => 0,
            ];

            $pendingInvoices = Invoice::query()
                ->selectRaw('COUNT(*) as aggregate_count, COALESCE(SUM(total_amount), 0) as aggregate_amount')
                ->where('status', 'pending')
                ->first();
            $summaries['pending_invoices_count'] = (int) ($pendingInvoices->aggregate_count ?? 0);
            $summaries['pending_invoices_amount'] = (float) ($pendingInvoices->aggregate_amount ?? 0);

            $overdueInvoices = Invoice::query()
                ->selectRaw('COUNT(*) as aggregate_count, COALESCE(SUM(total_amount), 0) as aggregate_amount')
                ->where('status', 'overdue')
                ->first();
            $summaries['overdue_invoices_count'] = (int) ($overdueInvoices->aggregate_count ?? 0);
            $summaries['overdue_invoices_amount'] = (float) ($overdueInvoices->aggregate_amount ?? 0);

            $pendingOrders = \App\Models\Order::query()
                ->selectRaw('COUNT(*) as aggregate_count, COALESCE(SUM(amount), 0) as aggregate_amount')
                ->where('status', 'pending')
                ->first();
            $summaries['pending_orders_count'] = (int) ($pendingOrders->aggregate_count ?? 0);
            $summaries['pending_orders_amount'] = (float) ($pendingOrders->aggregate_amount ?? 0);

            $summaries['inactive_students_count'] = User::query()
                ->where('role', 'student')
                ->where('is_active', false)
                ->count();
            $summaries['inactive_student_latest_created_at'] = User::query()
                ->where('role', 'student')
                ->where('is_active', false)
                ->latest()
                ->value('created_at');

            $pendingInstallments = \App\Models\InstallmentAgreement::query()
                ->selectRaw('COUNT(*) as aggregate_count, COALESCE(SUM(total_amount), 0) as aggregate_amount')
                ->whereIn('status', ['pending', 'awaiting_approval'])
                ->first();
            $summaries['pending_installments_count'] = (int) ($pendingInstallments->aggregate_count ?? 0);
            $summaries['pending_installments_amount'] = (float) ($pendingInstallments->aggregate_amount ?? 0);

            try {
                $summaries['open_support_tickets_count'] = \App\Models\SupportTicket::query()
                    ->whereIn('status', ['open', 'in_progress'])
                    ->count();
            } catch (\Throwable) {
            }

            try {
                $summaries['pending_withdrawals_count'] = \App\Models\WithdrawalRequest::query()
                    ->where('status', 'pending')
                    ->count();
            } catch (\Throwable) {
            }

            try {
                $summaries['new_sales_leads_count'] = \App\Models\SalesLead::query()
                    ->where('status', \App\Models\SalesLead::STATUS_NEW)
                    ->count();
            } catch (\Throwable) {
            }

            try {
                $summaries['active_coupons_count'] = Coupon::query()->where('is_active', true)->count();
            } catch (\Throwable) {
            }

            return $summaries;
        });
    }

    private function calculateChange($current, $previous): array
    {
        $current = (float) $current;
        $previous = (float) $previous;
        $difference = $current - $previous;
        $percent = $previous > 0
            ? round(($difference / $previous) * 100, 1)
            : ($current > 0 ? 100.0 : 0.0);

        return [
            'current' => $current,
            'previous' => $previous,
            'difference' => $difference,
            'percent' => $percent,
        ];
    }

    /**
     * مسح إحصائيات محددة
     */
    public function clearStats(string $key): void
    {
        Cache::forget($key);
    }
}

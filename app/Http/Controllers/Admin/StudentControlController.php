<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\Subscription;
use App\Models\User;
use App\Services\StudentControlOverviewService;
use App\Services\StudentSubscriptionPlansService;
use App\Services\SubscriptionLimitService;
use App\Services\TutorLessonQuotaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentControlController extends Controller
{
    public function paidFeatures(Request $request)
    {
        $stats = StudentControlOverviewService::dashboardStats();
        $planCards = StudentControlOverviewService::planCards();
        $customPlanStats = StudentControlOverviewService::customPlanStats();
        $capabilityCards = StudentControlOverviewService::optionalCapabilityCards();
        $tutorDefaults = TutorLessonQuotaService::settings();

        $recentSubscriptions = StudentControlOverviewService::activeStudentSubscriptionQuery()
            ->with(['user:id,name,phone,email'])
            ->latest()
            ->limit(12)
            ->get();

        return view('admin.student-control.paid-features', compact(
            'stats',
            'planCards',
            'customPlanStats',
            'capabilityCards',
            'tutorDefaults',
            'recentSubscriptions'
        ));
    }

    public function featureUsers(string $featureKey)
    {
        if ($featureKey === 'custom') {
            return $this->planStudentsList(null, 'باقات مخصصة', 'اشتراكات بدون قالب student_* أو بأسماء يدوية');
        }

        if (StudentSubscriptionPlansService::isStudentPlanKey($featureKey)) {
            $plans = StudentSubscriptionPlansService::getPlans();
            $label = (string) ($plans[$featureKey]['label'] ?? $featureKey);

            return $this->planStudentsList($featureKey, $label, 'طلاب لديهم اشتراك نشط مرتبط بهذا القالب');
        }

        if ($featureKey === 'tutor_lessons') {
            return $this->capabilityStudentsList('tutor_lessons');
        }

        if ($featureKey === 'support') {
            return $this->capabilityStudentsList('support');
        }

        abort(404);
    }

    private function planStudentsList(?string $planKey, string $title, string $subtitle)
    {
        $users = User::query()
            ->where('role', 'student')
            ->whereHas('subscriptions', function ($q) use ($planKey) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    });
                if ($planKey === null) {
                    $keys = StudentSubscriptionPlansService::planKeys();
                    $q->where(function ($inner) use ($keys) {
                        $inner->whereNull('teacher_plan_key')
                            ->orWhereNotIn('teacher_plan_key', $keys);
                    });
                } else {
                    $q->where('teacher_plan_key', $planKey);
                }
            })
            ->with([
                'studentLearningProfile',
                'subscriptions' => function ($q) use ($planKey) {
                    $q->where('status', 'active')
                        ->where(function ($d) {
                            $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                        });
                    if ($planKey === null) {
                        $keys = StudentSubscriptionPlansService::planKeys();
                        $q->where(function ($inner) use ($keys) {
                            $inner->whereNull('teacher_plan_key')
                                ->orWhereNotIn('teacher_plan_key', $keys);
                        });
                    } else {
                        $q->where('teacher_plan_key', $planKey);
                    }
                    $q->latest();
                },
            ])
            ->latest()
            ->paginate(20);

        return view('admin.student-control.plan-students', [
            'users' => $users,
            'planKey' => $planKey ?? 'custom',
            'pageTitle' => $title,
            'pageSubtitle' => $subtitle,
        ]);
    }

    private function capabilityStudentsList(string $capabilityKey)
    {
        $activeQuery = StudentControlOverviewService::activeStudentSubscriptionQuery();

        $userQuery = User::query()->where('role', 'student');

        if ($capabilityKey === 'tutor_lessons') {
            $userIds = (clone $activeQuery)
                ->get(['user_id', 'feature_limits'])
                ->filter(fn ($s) => (int) (is_array($s->feature_limits) ? ($s->feature_limits['tutor_lesson_hours'] ?? 0) : 0) > 0)
                ->pluck('user_id')
                ->unique();
            $userQuery->whereIn('id', $userIds);
            $title = 'طلاب لديهم رصيد حصص';
            $subtitle = 'اشتراك نشط يتضمن ساعات حصص مع المعلمين';
        } elseif ($capabilityKey === 'support') {
            $userQuery->whereHas('subscriptions', function ($q) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    })
                    ->where(function ($inner) {
                        $inner->whereJsonContains('features', 'support')
                            ->orWhereJsonContains('features', 'direct_support');
                    });
            });
            $title = 'طلاب بصلاحية الدعم الفني';
            $subtitle = 'ميزة support أو direct_support في الاشتراك النشط';
        } else {
            abort(404);
        }

        $users = $userQuery
            ->with(['studentLearningProfile', 'subscriptions' => function ($q) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    })
                    ->latest();
            }])
            ->latest()
            ->paginate(20);

        return view('admin.student-control.plan-students', [
            'users' => $users,
            'planKey' => $capabilityKey,
            'pageTitle' => $title,
            'pageSubtitle' => $subtitle,
        ]);
    }

    public function consumption(Request $request)
    {
        [$windowDays, $startAt, $endAt] = $this->resolveRange($request);

        $activeSubsQuery = Subscription::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        $activeSubs = (clone $activeSubsQuery)->get(['id', 'user_id', 'features', 'plan_name', 'end_date']);
        $activeUserIds = $activeSubs->pluck('user_id')->unique()->values();

        $stats = [
            'active_subscriptions' => $activeSubs->count(),
            'active_students' => $activeUserIds->count(),
            'avg_lesson_hours' => round((float) $activeSubs->map(function ($s) {
                return (int) (is_array($s->feature_limits) ? ($s->feature_limits['tutor_lesson_hours'] ?? 0) : 0);
            })->avg(), 1),
        ];

        $planUsage = StudentControlOverviewService::planCards()
            ->map(fn ($p) => [
                'key' => $p['key'],
                'label' => $p['label'],
                'count' => $p['students_count'],
                'icon' => 'fa-layer-group',
            ])
            ->values();

        $customPlanStats = StudentControlOverviewService::customPlanStats();

        $featureConsumption = $this->calculateStudentActivityConsumption($activeUserIds, $startAt, $endAt);

        $users = User::whereIn('id', $activeUserIds)->get(['id', 'name', 'phone']);
        $userMap = $users->keyBy('id');

        $enrollCounts = DB::table('student_course_enrollments')
            ->select('user_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $examCounts = DB::table('exam_attempts')
            ->select('user_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $orderCounts = DB::table('orders')
            ->select('user_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $activityCounts = DB::table('activity_logs')
            ->select('user_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        $topUsers = $activeUserIds->map(function ($uid) use ($userMap, $enrollCounts, $examCounts, $orderCounts, $activityCounts, $activeSubs) {
            $subscription = $activeSubs->firstWhere('user_id', $uid);
            $score = (int) ($enrollCounts[$uid] ?? 0) + (int) ($examCounts[$uid] ?? 0) + (int) ($orderCounts[$uid] ?? 0) + (int) ($activityCounts[$uid] ?? 0);

            return [
                'user' => $userMap[$uid] ?? null,
                'subscription' => $subscription,
                'enrollments' => (int) ($enrollCounts[$uid] ?? 0),
                'exam_attempts' => (int) ($examCounts[$uid] ?? 0),
                'orders' => (int) ($orderCounts[$uid] ?? 0),
                'activities' => (int) ($activityCounts[$uid] ?? 0),
                'score' => $score,
            ];
        })->filter(fn ($row) => $row['user'] !== null)
            ->sortByDesc('score')
            ->take(30)
            ->values();

        return view('admin.student-control.consumption', compact(
            'stats',
            'planUsage',
            'customPlanStats',
            'featureConsumption',
            'topUsers',
            'windowDays'
        ));
    }

    public function userConsumption(Request $request, User $user)
    {
        [$windowDays, $startAt, $endAt] = $this->resolveRange($request);

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->latest()
            ->first();

        $limits = SubscriptionLimitService::limitsForUser($user);
        $monthlyMeetingsUsed = SubscriptionLimitService::monthlyClassroomUsage($user);
        $limitMeetings = (int) $limits['classroom_meetings_per_month'];
        $usagePercent = $limitMeetings > 0 ? round(($monthlyMeetingsUsed / $limitMeetings) * 100, 1) : 0.0;
        $alertLevel = $usagePercent >= 100 ? 'danger' : ($usagePercent >= 80 ? 'warning' : 'normal');

        $meetings = collect();
        $totals = [
            'meetings' => 0,
            'participants_peak' => 0,
            'participants_total' => 0,
            'duration_minutes' => 0,
        ];
        $timeline = collect();

        if (Schema::hasTable('classroom_meetings')) {
            $meetings = ClassroomMeeting::query()
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->withCount('participants')
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($m) {
                    $durationMinutes = null;
                    if ($m->started_at && $m->ended_at) {
                        $durationMinutes = max(0, $m->started_at->diffInMinutes($m->ended_at));
                    } elseif ($m->started_at) {
                        $durationMinutes = max(0, $m->started_at->diffInMinutes(now()));
                    }

                    return [
                        'id' => $m->id,
                        'code' => $m->code,
                        'title' => $m->title ?: 'اجتماع بدون عنوان',
                        'created_at' => $m->created_at,
                        'started_at' => $m->started_at,
                        'ended_at' => $m->ended_at,
                        'participants_peak' => (int) ($m->participants_peak ?? 0),
                        'participants_total' => (int) ($m->participants_count ?? 0),
                        'duration_minutes' => $durationMinutes,
                    ];
                });

            $totals['meetings'] = $meetings->count();
            $totals['participants_peak'] = (int) $meetings->max('participants_peak');
            $totals['participants_total'] = (int) $meetings->sum('participants_total');
            $totals['duration_minutes'] = (int) $meetings->sum(function ($m) {
                return (int) ($m['duration_minutes'] ?? 0);
            });
        }

        if (Schema::hasTable('classroom_meeting_participants') && Schema::hasTable('classroom_meetings')) {
            $timeline = ClassroomMeetingParticipant::query()
                ->join('classroom_meetings as m', 'm.id', '=', 'classroom_meeting_participants.classroom_meeting_id')
                ->where('m.user_id', $user->id)
                ->whereBetween('classroom_meeting_participants.created_at', [$startAt, $endAt])
                ->select([
                    'classroom_meeting_participants.*',
                    'm.code as meeting_code',
                    'm.title as meeting_title',
                ])
                ->orderByDesc('classroom_meeting_participants.created_at')
                ->limit(300)
                ->get()
                ->map(function ($row) {
                    $join = $row->joined_at ? Carbon::parse($row->joined_at) : null;
                    $left = $row->left_at ? Carbon::parse($row->left_at) : null;
                    $lastSeen = $row->last_seen_at ? Carbon::parse($row->last_seen_at) : null;
                    $duration = null;
                    if ($join && $left) {
                        $duration = max(0, $join->diffInMinutes($left));
                    }
                    return [
                        'meeting_code' => $row->meeting_code,
                        'meeting_title' => $row->meeting_title ?: 'اجتماع بدون عنوان',
                        'display_name' => $row->display_name ?: 'ضيف',
                        'joined_at' => $join,
                        'left_at' => $left,
                        'last_seen_at' => $lastSeen,
                        'duration_minutes' => $duration,
                    ];
                });
        }

        return view('admin.student-control.user-consumption', compact(
            'user',
            'subscription',
            'windowDays',
            'limits',
            'monthlyMeetingsUsed',
            'usagePercent',
            'alertLevel',
            'meetings',
            'totals',
            'timeline'
        ));
    }

    /**
     * نشاط الطلاب المشتركين ضمن الفترة (حصص، تسجيلات، دعم).
     */
    private function calculateStudentActivityConsumption($activeUserIds, Carbon $startAt, Carbon $endAt): \Illuminate\Support\Collection
    {
        if ($activeUserIds->isEmpty()) {
            return collect();
        }

        $lessonBookings = 0;
        $lessonUsers = collect();
        if (Schema::hasTable('lesson_bookings')) {
            $lessonUsers = DB::table('lesson_bookings')
                ->whereIn('student_id', $activeUserIds)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->distinct()
                ->pluck('student_id');
            $lessonBookings = DB::table('lesson_bookings')
                ->whereIn('student_id', $activeUserIds)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->count();
        }

        $enrollments = DB::table('student_course_enrollments')
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->count();
        $enrollUsers = DB::table('student_course_enrollments')
            ->whereIn('user_id', $activeUserIds)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->distinct()
            ->pluck('user_id');

        $supportTickets = 0;
        $supportUsers = collect();
        if (Schema::hasTable('support_tickets')) {
            $supportUsers = DB::table('support_tickets')
                ->whereIn('user_id', $activeUserIds)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->distinct()
                ->pluck('user_id');
            $supportTickets = DB::table('support_tickets')
                ->whereIn('user_id', $activeUserIds)
                ->whereBetween('created_at', [$startAt, $endAt])
                ->count();
        }

        $enabled = $activeUserIds->count();

        return collect([
            [
                'feature_key' => 'tutor_lessons',
                'label' => 'حجوزات حصص مع المعلم',
                'enabled_users' => $enabled,
                'used_users' => $lessonUsers->count(),
                'events_count' => $lessonBookings,
                'usage_rate' => $enabled > 0 ? round(($lessonUsers->count() / $enabled) * 100, 1) : 0,
            ],
            [
                'feature_key' => 'enrollments',
                'label' => 'تسجيلات كورسات',
                'enabled_users' => $enabled,
                'used_users' => $enrollUsers->count(),
                'events_count' => $enrollments,
                'usage_rate' => $enabled > 0 ? round(($enrollUsers->count() / $enabled) * 100, 1) : 0,
            ],
            [
                'feature_key' => 'support',
                'label' => 'تذاكر الدعم الفني',
                'enabled_users' => $enabled,
                'used_users' => $supportUsers->count(),
                'events_count' => $supportTickets,
                'usage_rate' => $enabled > 0 ? round(($supportUsers->count() / $enabled) * 100, 1) : 0,
            ],
        ])->sortByDesc('usage_rate')->values();
    }

    private function fromActivityUrl($userIds, array $needles, Carbon $startAt, Carbon $endAt): array
    {
        if (!Schema::hasTable('activity_logs') || empty($needles) || collect($userIds)->isEmpty()) {
            return ['used_user_ids' => collect(), 'events_count' => 0];
        }

        $query = DB::table('activity_logs')
            ->whereIn('user_id', $userIds)
            ->whereNotNull('url')
            ->whereBetween('created_at', [$startAt, $endAt]);
        $query->where(function ($q) use ($needles) {
            foreach ($needles as $needle) {
                $q->orWhere('url', 'like', '%' . $needle . '%');
            }
        });

        return [
            'used_user_ids' => (clone $query)->distinct()->pluck('user_id'),
            'events_count' => (clone $query)->count(),
        ];
    }

    private function resolveRange(Request $request): array
    {
        $allowed = [7, 30, 90];
        $windowDays = (int) $request->integer('days', 30);
        if (!in_array($windowDays, $allowed, true)) {
            $windowDays = 30;
        }

        $endAt = now();
        $startAt = now()->subDays($windowDays)->startOfDay();

        return [$windowDays, $startAt, $endAt];
    }
}


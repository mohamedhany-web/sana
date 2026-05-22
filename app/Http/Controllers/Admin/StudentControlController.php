<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionLimitService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentControlController extends Controller
{
    public function paidFeatures(Request $request)
    {
        $featureConfig = config('student_subscription_features', []);

        $activeSubscriptions = Subscription::query()
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        $stats = [
            'active_subscriptions' => (clone $activeSubscriptions)->count(),
            'active_students_with_subscription' => (clone $activeSubscriptions)->distinct('user_id')->count('user_id'),
        ];

        $features = collect($featureConfig)->map(function ($cfg, $key) {
            $usersCount = Subscription::query()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->whereJsonContains('features', $key)
                ->distinct('user_id')
                ->count('user_id');

            return [
                'key' => $key,
                'label' => __("student.subscription_feature.{$key}"),
                'icon' => $cfg['icon'] ?? 'fa-star',
                'icon_bg' => $cfg['icon_bg'] ?? 'bg-slate-100 dark:bg-slate-700/60',
                'icon_text' => $cfg['icon_text'] ?? 'text-slate-600 dark:text-slate-300',
                'users_count' => $usersCount,
            ];
        })->sortByDesc('users_count')->values();

        return view('admin.student-control.paid-features', compact('features', 'stats'));
    }

    public function featureUsers(string $featureKey)
    {
        $featureConfig = config('student_subscription_features', []);
        abort_unless(array_key_exists($featureKey, $featureConfig), 404);

        $users = User::query()
            ->where('role', 'student')
            ->whereHas('subscriptions', function ($q) use ($featureKey) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    })
                    ->whereJsonContains('features', $featureKey);
            })
            ->with(['subscriptions' => function ($q) use ($featureKey) {
                $q->where('status', 'active')
                    ->where(function ($d) {
                        $d->whereNull('end_date')->orWhere('end_date', '>=', now());
                    })
                    ->whereJsonContains('features', $featureKey)
                    ->latest();
            }])
            ->latest()
            ->paginate(20);

        $featureLabel = __("student.subscription_feature.{$featureKey}");
        $featureCfg = $featureConfig[$featureKey];

        return view('admin.student-control.feature-users', compact('users', 'featureKey', 'featureLabel', 'featureCfg'));
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
            'avg_features_per_subscription' => round((float) $activeSubs->map(fn($s) => is_array($s->features) ? count($s->features) : 0)->avg(), 2),
        ];

        $featureConfig = config('student_subscription_features', []);
        $featureUsage = collect($featureConfig)->map(function ($cfg, $key) use ($activeSubs) {
            $count = $activeSubs->filter(function ($s) use ($key) {
                return is_array($s->features) && in_array($key, $s->features, true);
            })->count();

            return [
                'key' => $key,
                'label' => __("student.subscription_feature.{$key}"),
                'count' => $count,
                'icon' => $cfg['icon'] ?? 'fa-star',
            ];
        })->sortByDesc('count')->values();

        $featureConsumption = $this->calculateFeatureConsumption($activeUserIds, $activeSubs, $featureConfig, $startAt, $endAt);

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
            'featureUsage',
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
     * استهلاك فعلي لكل ميزة (من الجداول والأحداث الفعلية).
     */
    private function calculateFeatureConsumption($activeUserIds, $activeSubs, array $featureConfig, Carbon $startAt, Carbon $endAt)
    {
        if ($activeUserIds->isEmpty()) {
            return collect();
        }

        $enabledPerFeature = collect($featureConfig)->mapWithKeys(function ($cfg, $featureKey) use ($activeSubs) {
            $enabledUsers = $activeSubs
                ->filter(fn ($s) => is_array($s->features) && in_array($featureKey, $s->features, true))
                ->pluck('user_id')
                ->unique();
            return [$featureKey => $enabledUsers];
        });

        $signalResolvers = [
            'library_access' => function ($userIds) use ($startAt, $endAt) {
                return $this->fromActivityUrl($userIds, ['/curriculum-library'], $startAt, $endAt);
            },
            'ai_tools' => function ($userIds) use ($startAt, $endAt) {
                return $this->fromActivityUrl($userIds, ['/features/ai_tools', '/curriculum-library'], $startAt, $endAt);
            },
            'full_ai_suite' => function ($userIds) use ($startAt, $endAt) {
                return $this->fromActivityUrl($userIds, ['/features/full_ai_suite', '/full-ai-suite'], $startAt, $endAt);
            },
            'classroom_access' => function ($userIds) use ($startAt, $endAt) {
                $meetingUsers = collect();
                $meetingEvents = 0;
                if (Schema::hasTable('classroom_meetings')) {
                    $meetingUsers = DB::table('classroom_meetings')
                        ->whereIn('user_id', $userIds)
                        ->whereBetween('created_at', [$startAt, $endAt])
                        ->distinct()
                        ->pluck('user_id');
                    $meetingEvents = DB::table('classroom_meetings')
                        ->whereIn('user_id', $userIds)
                        ->whereBetween('created_at', [$startAt, $endAt])
                        ->count();
                }

                $participantEvents = 0;
                if (Schema::hasTable('classroom_meeting_participants') && Schema::hasTable('classroom_meetings')) {
                    $participantEvents = DB::table('classroom_meeting_participants as p')
                        ->join('classroom_meetings as m', 'm.id', '=', 'p.classroom_meeting_id')
                        ->whereIn('m.user_id', $userIds)
                        ->whereBetween('p.created_at', [$startAt, $endAt])
                        ->count();
                }

                $logs = $this->fromActivityUrl($userIds, ['/classroom'], $startAt, $endAt);

                return [
                    'used_user_ids' => $meetingUsers->merge($logs['used_user_ids'])->unique()->values(),
                    'events_count' => $meetingEvents + $participantEvents + $logs['events_count'],
                ];
            },
        ];

        $defaultResolver = function ($userIds, $featureKey) use ($startAt, $endAt) {
            return $this->fromActivityUrl($userIds, ['/features/' . $featureKey], $startAt, $endAt);
        };

        return collect($featureConfig)->map(function ($cfg, $featureKey) use ($enabledPerFeature, $signalResolvers, $defaultResolver) {
            $enabledUsers = $enabledPerFeature[$featureKey] ?? collect();
            $enabledCount = $enabledUsers->count();

            if ($enabledCount === 0) {
                return [
                    'feature_key' => $featureKey,
                    'label' => __("student.subscription_feature.{$featureKey}"),
                    'enabled_users' => 0,
                    'used_users' => 0,
                    'events_count' => 0,
                    'usage_rate' => 0,
                ];
            }

            $resolver = $signalResolvers[$featureKey] ?? function ($userIds) use ($defaultResolver, $featureKey) {
                return $defaultResolver($userIds, $featureKey);
            };

            $signal = $resolver($enabledUsers->values());
            $usedUsers = collect($signal['used_user_ids'] ?? [])->intersect($enabledUsers)->unique()->values();
            $eventsCount = (int) ($signal['events_count'] ?? 0);
            $usedCount = $usedUsers->count();
            $rate = $enabledCount > 0 ? round(($usedCount / $enabledCount) * 100, 1) : 0;

            return [
                'feature_key' => $featureKey,
                'label' => __("student.subscription_feature.{$featureKey}"),
                'enabled_users' => $enabledCount,
                'used_users' => $usedCount,
                'events_count' => $eventsCount,
                'usage_rate' => $rate,
            ];
        })->sortByDesc('usage_rate')->values();
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


<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LessonBooking;
use App\Models\LessonProgress;
use App\Models\Referral;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PlayLearnController extends Controller
{
    public function activities(): View
    {
        $user = auth()->user();

        $tiles = array_values(array_filter([
            $this->tile('📡', __('student.play.live_sessions'), __('student.play.live_sessions_hint'), 'student.live-sessions.index'),
            $this->tile('🎬', __('student.play.recordings'), __('student.play.recordings_hint'), 'student.live-recordings.index'),
            ($user->canAccessStudentAiUsages()
                ? $this->tile('🎮', __('student.play.games'), __('student.play.games_hint'), 'student.ai-usages.index')
                : null),
            $this->tile('📅', __('student.play.calendar'), __('student.play.calendar_hint'), 'calendar'),
            $this->tile('👨‍🏫', __('student.play.tutor_lessons'), __('student.play.tutor_lessons_hint'), 'student.tutor-lessons.hub'),
        ]));

        return view('student.play.activities', compact('tiles'));
    }

    public function challenges(): View
    {
        $user = auth()->user();
        $goal = 3;
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $completedLessons = 0;

        if (Schema::hasTable('lesson_bookings')) {
            $completedLessons += (int) LessonBooking::query()
                ->where('student_id', $user->id)
                ->where('status', LessonBooking::STATUS_COMPLETED)
                ->where(function ($q) use ($weekStart, $weekEnd) {
                    $q->whereBetween('completed_at', [$weekStart, $weekEnd])
                        ->orWhere(function ($inner) use ($weekStart, $weekEnd) {
                            $inner->whereNull('completed_at')
                                ->whereBetween('updated_at', [$weekStart, $weekEnd]);
                        });
                })
                ->count();
        }

        if (config('student.courses_enabled') && Schema::hasTable('lesson_progress')) {
            $completedLessons += (int) LessonProgress::query()
                ->where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->count();
        }

        $completedLessons = min($goal, $completedLessons);
        $percent = (int) round(($completedLessons / max(1, $goal)) * 100);
        $xpReward = 50;
        $done = $completedLessons >= $goal;

        $startUrl = config('student.courses_enabled') && Route::has('my-courses.index')
            ? route('my-courses.index')
            : (Route::has('student.tutor-lessons.hub') ? route('student.tutor-lessons.hub') : route('dashboard'));

        return view('student.play.challenges', compact(
            'goal',
            'completedLessons',
            'percent',
            'xpReward',
            'startUrl',
            'done'
        ));
    }

    public function rewards(): View
    {
        $user = auth()->user();

        $achievements = UserAchievement::query()
            ->where('user_id', $user->id)
            ->with('achievement')
            ->orderByDesc('earned_at')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $points = (int) UserAchievement::query()
            ->where('user_id', $user->id)
            ->sum('points_earned');

        $referralRewards = 0;
        if (Schema::hasTable('referrals') && Schema::hasColumn('referrals', 'reward_amount')) {
            $referralRewards = (float) Referral::query()
                ->where('referrer_id', $user->id)
                ->where('status', 'completed')
                ->sum('reward_amount');
        }

        $tiles = array_values(array_filter([
            $this->tile('🏆', __('student.play.achievements'), __('student.play.achievements_hint'), 'student.achievements.index'),
            $this->tile('🎁', __('student.play.referrals'), __('student.play.referrals_hint'), 'referrals.index'),
            $this->tile('👛', __('student.play.wallet'), __('student.play.wallet_hint'), 'student.wallet.index'),
        ]));

        return view('student.play.rewards', compact(
            'achievements',
            'points',
            'referralRewards',
            'tiles'
        ));
    }

    /**
     * @return array{emoji: string, label: string, hint: string, url: string}|null
     */
    private function tile(string $emoji, string $label, string $hint, string $route, array $params = []): ?array
    {
        if (! Route::has($route)) {
            return null;
        }

        return [
            'emoji' => $emoji,
            'label' => $label,
            'hint' => $hint,
            'url' => route($route, $params),
        ];
    }
}

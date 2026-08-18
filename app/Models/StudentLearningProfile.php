<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class StudentLearningProfile extends Model
{
    public const MODE_ASSISTED = 'assisted';

    public const MODE_SELF_SCHEDULE = 'self_schedule';

    public const MODE_PICK_TEACHER = 'pick_teacher';

    public const SESSION_ONE_TO_ONE = 'one_to_one';

    public const SESSION_SMALL_GROUP = 'small_group';

    protected $fillable = [
        'user_id',
        'academic_year_id',
        'subject_ids',
        'curriculum_label',
        'grade_stage',
        'matching_mode',
        'preferred_session_type',
        'lesson_hours_quota',
        'lesson_hours_used',
        'lesson_hours_bonus',
        'lesson_minutes_used',
        'assessment_notes',
        'assessed_at',
    ];

    protected $casts = [
        'subject_ids' => 'array',
        'assessed_at' => 'datetime',
        'lesson_hours_bonus' => 'integer',
        'lesson_minutes_used' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function remainingHours(): int
    {
        if ((int) $this->lesson_hours_quota < 0) {
            return PHP_INT_MAX;
        }

        return (int) floor($this->remainingMinutes() / 60);
    }

    public function remainingLabel(): string
    {
        if ((int) $this->lesson_hours_quota < 0) {
            return 'غير محدود';
        }

        return self::minutesLabel($this->remainingMinutes());
    }

    public function usedLabel(): string
    {
        return self::minutesLabel($this->usedMinutes());
    }

    public static function minutesLabel(int $minutes): string
    {
        $minutes = max(0, $minutes);
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;
        if ($hours > 0 && $rest > 0) {
            return $hours.' ساعة و '.$rest.' دقيقة';
        }
        if ($hours > 0) {
            return $hours.' ساعة';
        }

        return $rest.' دقيقة';
    }

    public function remainingMinutes(): int
    {
        if ((int) $this->lesson_hours_quota < 0) {
            return PHP_INT_MAX;
        }

        $quota = (int) $this->lesson_hours_quota * 60;
        $used = $this->usedMinutes();

        return max(0, $quota - $used);
    }

    public function usedMinutes(): int
    {
        if (Schema::hasColumn($this->getTable(), 'lesson_minutes_used')) {
            $minutesUsed = (int) ($this->lesson_minutes_used ?? 0);
            if ($minutesUsed > 0) {
                return $minutesUsed;
            }
        }

        return (int) $this->lesson_hours_used * 60;
    }

    public function hasMinutesFor(int $minutes): bool
    {
        if ($this->lesson_hours_quota < 0) {
            return true;
        }

        if ($this->lesson_hours_quota <= 0) {
            return false;
        }

        return $this->remainingMinutes() >= $minutes;
    }

    public function deductMinutes(int $minutes): void
    {
        if ($minutes <= 0 || $this->lesson_hours_quota < 0) {
            return;
        }
        if ($this->lesson_hours_quota <= 0) {
            return;
        }
        if (Schema::hasColumn($this->getTable(), 'lesson_minutes_used')) {
            $this->increment('lesson_minutes_used', $minutes);
            $this->refresh();
            $hoursUsed = (int) floor($this->usedMinutes() / 60);
            $cap = max(0, (int) $this->lesson_hours_quota);
            $this->update(['lesson_hours_used' => min($hoursUsed, $cap)]);

            return;
        }

        $hours = (int) floor($minutes / 60);
        $this->increment('lesson_hours_used', min($hours, max(0, $this->lesson_hours_quota - $this->lesson_hours_used)));
    }

    public static function matchingModeLabels(): array
    {
        return [
            self::MODE_ASSISTED => __('tutor.matching_assisted'),
            self::MODE_SELF_SCHEDULE => __('tutor.matching_self_schedule'),
            self::MODE_PICK_TEACHER => __('tutor.matching_pick_teacher'),
        ];
    }

    public static function sessionTypeLabels(): array
    {
        return [
            self::SESSION_ONE_TO_ONE => __('tutor.session_one_to_one'),
            self::SESSION_SMALL_GROUP => __('tutor.session_small_group'),
        ];
    }
}

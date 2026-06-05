<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'assessment_notes',
        'assessed_at',
    ];

    protected $casts = [
        'subject_ids' => 'array',
        'assessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function remainingMinutes(): int
    {
        $quota = (int) $this->lesson_hours_quota * 60;
        $used = (int) $this->lesson_hours_used * 60;

        return max(0, $quota - $used);
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
        if ($minutes <= 0 || $this->lesson_hours_quota <= 0) {
            return;
        }
        $hours = (int) ceil($minutes / 60);
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

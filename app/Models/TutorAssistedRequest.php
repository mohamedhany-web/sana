<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TutorAssistedRequest extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'code',
        'student_id',
        'parent_id',
        'requested_by_user_id',
        'subject_ids',
        'academic_year_id',
        'preferred_session_type',
        'message',
        'status',
        'assigned_instructor_id',
        'lesson_booking_id',
        'assigned_at',
        'closed_at',
    ];

    protected $casts = [
        'subject_ids' => 'array',
        'assigned_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (TutorAssistedRequest $request) {
            if (empty($request->code)) {
                do {
                    $code = 'AR-'.strtoupper(Str::random(6));
                } while (static::where('code', $code)->exists());
                $request->code = $code;
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function assignedInstructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_instructor_id');
    }

    public function lessonBooking(): BelongsTo
    {
        return $this->belongsTo(LessonBooking::class, 'lesson_booking_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => __('tutor.assisted_open'),
            self::STATUS_ASSIGNED => __('tutor.assisted_assigned'),
            self::STATUS_CLOSED => __('tutor.assisted_closed'),
            default => $this->status,
        };
    }
}

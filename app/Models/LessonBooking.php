<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LessonBooking extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'code',
        'student_id',
        'instructor_id',
        'parent_id',
        'requested_by_user_id',
        'academic_subject_id',
        'tutor_assisted_request_id',
        'matching_mode',
        'session_type',
        'tutor_group_offer_id',
        'max_group_size',
        'status',
        'is_trial',
        'scheduled_at',
        'duration_minutes',
        'classroom_meeting_id',
        'student_notes',
        'instructor_notes',
        'confirmed_at',
        'cancelled_at',
        'cancelled_by',
        'completed_at',
        'billable_minutes',
        'co_presence_started_at',
        'co_presence_ended_at',
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'scheduled_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'co_presence_started_at' => 'datetime',
        'co_presence_ended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LessonBooking $booking) {
            if (empty($booking->code)) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (static::where('code', $code)->exists());
                $booking->code = $code;
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function classroomMeeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function assistedRequest(): BelongsTo
    {
        return $this->belongsTo(TutorAssistedRequest::class, 'tutor_assisted_request_id');
    }

    public function groupOffer(): BelongsTo
    {
        return $this->belongsTo(TutorGroupOffer::class, 'tutor_group_offer_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(LessonBookingRating::class);
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true)
            && $this->scheduled_at
            && $this->scheduled_at->isFuture();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('tutor.status_pending'),
            self::STATUS_CONFIRMED => __('tutor.status_confirmed'),
            self::STATUS_IN_PROGRESS => __('tutor.status_in_progress'),
            self::STATUS_COMPLETED => __('tutor.status_completed'),
            self::STATUS_CANCELLED => __('tutor.status_cancelled'),
            default => $this->status,
        };
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => __('tutor.status_pending'),
            self::STATUS_CONFIRMED => __('tutor.status_confirmed'),
            self::STATUS_IN_PROGRESS => __('tutor.status_in_progress'),
            self::STATUS_COMPLETED => __('tutor.status_completed'),
            self::STATUS_CANCELLED => __('tutor.status_cancelled'),
        ];
    }
}

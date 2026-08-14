<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonBookingRating extends Model
{
    protected $fillable = [
        'lesson_booking_id',
        'rater_id',
        'rated_user_id',
        'rating',
        'lesson_rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
        'lesson_rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(LessonBooking::class, 'lesson_booking_id');
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }

    public function isInstructorEvaluation(): bool
    {
        return (int) $this->rater_id === (int) $this->booking?->instructor_id;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorAvailability extends Model
{
    protected $fillable = [
        'instructor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_active' => 'boolean',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public static function dayLabels(): array
    {
        return [
            0 => __('tutor.day_sunday'),
            1 => __('tutor.day_monday'),
            2 => __('tutor.day_tuesday'),
            3 => __('tutor.day_wednesday'),
            4 => __('tutor.day_thursday'),
            5 => __('tutor.day_friday'),
            6 => __('tutor.day_saturday'),
        ];
    }

    public function dayLabel(): string
    {
        return self::dayLabels()[$this->day_of_week] ?? (string) $this->day_of_week;
    }
}

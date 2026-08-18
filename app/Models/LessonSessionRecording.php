<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LessonSessionRecording extends Model
{
    public const STATUS_STARTING = 'starting';

    public const STATUS_RECORDING = 'recording';

    public const STATUS_UPLOADING = 'uploading';

    public const STATUS_READY = 'ready';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'classroom_meeting_id',
        'lesson_booking_id',
        'student_id',
        'instructor_id',
        'egress_id',
        'status',
        'disk',
        'file_path',
        'file_size',
        'duration_seconds',
        'mime_type',
        'started_at',
        'ended_at',
        'uploaded_at',
        'error_message',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration_seconds' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(LessonBooking::class, 'lesson_booking_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY && ! empty($this->file_path);
    }

    public function getUrl(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        $disk = $this->disk ?: 'live_recordings_r2';

        try {
            return Storage::disk($disk)->temporaryUrl($this->file_path, now()->addHours(2));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getFileSizeForHumansAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }
        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1).' MB';
        }

        return round($bytes / 1073741824, 2).' GB';
    }

    public function getDurationForHumansAttribute(): string
    {
        $s = (int) $this->duration_seconds;
        if ($s < 60) {
            return $s.' ثانية';
        }
        $m = intdiv($s, 60);
        if ($m < 60) {
            return $m.' دقيقة';
        }
        $h = intdiv($m, 60);
        $rm = $m % 60;

        return $h.' ساعة'.($rm > 0 ? ' و'.$rm.' دقيقة' : '');
    }

    public function getTitleAttribute(): string
    {
        $student = $this->student?->name ?? 'طالب';
        $teacher = $this->instructor?->name ?? 'معلم';

        return 'حصة '.$student.' مع '.$teacher;
    }
}

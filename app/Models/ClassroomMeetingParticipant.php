<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomMeetingParticipant extends Model
{
    protected $fillable = [
        'classroom_meeting_id',
        'user_id',
        'participant_role',
        'token',
        'display_name',
        'ip_address',
        'user_agent',
        'joined_at',
        'last_seen_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(ClassroomMeeting::class, 'classroom_meeting_id');
    }
}


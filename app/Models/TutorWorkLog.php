<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorWorkLog extends Model
{
    protected $fillable = [
        'instructor_id',
        'work_date',
        'minutes',
        'source',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}

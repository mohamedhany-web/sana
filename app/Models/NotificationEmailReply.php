<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationEmailReply extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'to_email',
        'to_name',
        'subject',
        'body',
        'status',
        'error_message',
    ];

    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

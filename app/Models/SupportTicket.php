<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'support_inquiry_category_id',
        'subject',
        'priority',
        'status',
        'message',
        'last_reply_at',
        'resolved_at',
        'assigned_admin_id',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function inquiryCategory(): BelongsTo
    {
        return $this->belongsTo(SupportInquiryCategory::class, 'support_inquiry_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class)->orderBy('created_at');
    }

    public function latestReply(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SupportTicketReply::class)->latestOfMany();
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            'open' => 'مفتوحة',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلقة',
        ];
    }

    /** @return array<string, string> */
    public static function priorityLabels(): array
    {
        return [
            'low' => 'منخفضة',
            'normal' => 'عادية',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function priorityLabel(): string
    {
        return self::priorityLabels()[$this->priority] ?? $this->priority;
    }

    public function isAwaitingAdminResponse(): bool
    {
        if (! in_array($this->status, ['open', 'in_progress'], true)) {
            return false;
        }

        $latest = $this->relationLoaded('latestReply')
            ? $this->latestReply
            : $this->latestReply()->first();

        return $latest === null || $latest->sender_type === 'student';
    }

    public function scopeFromStudents($query)
    {
        return $query->whereHas('user', fn ($q) => $q->where('role', 'student'));
    }

    public function scopeAwaitingAdminResponse($query)
    {
        return $query->whereIn('status', ['open', 'in_progress'])
            ->where(function ($q) {
                $q->whereDoesntHave('replies')
                    ->orWhereHas('latestReply', fn ($r) => $r->where('sender_type', 'student'));
            });
    }
}


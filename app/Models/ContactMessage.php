<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'source',
        'ip_address',
        'user_agent',
        'referrer',
        'status',
        'admin_notes',
        'replied_by',
        'replied_at',
        'read_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public static function sourceLabel(?string $source): string
    {
        return match ($source) {
            'contact_page' => 'صفحة التواصل (/contact)',
            default => $source ? (string) $source : 'غير معروف',
        };
    }

    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeUnread($query)
    {
        return $query->whereIn('status', ['new', 'read']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_type',
        'teacher_plan_key',
        'plan_name',
        'price',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'billing_cycle',
        'invoice_id',
        'features',
        'feature_limits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'features' => 'array',
        'feature_limits' => 'array',
    ];

    public static function typeLabels(): array
    {
        return [
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
            'lifetime' => 'مدى الحياة',
            'trial' => 'تجريبي',
            'custom' => 'مخصص',
        ];
    }

    public static function billingCycleLabels(): array
    {
        return [
            'monthly' => 'كل شهر',
            'quarterly' => 'كل 3 أشهر',
            'yearly' => 'سنوياً',
            'biannual' => 'كل 6 أشهر',
            'weekly' => 'أسبوعي',
        ];
    }

    public static function typeLabel(?string $type): string
    {
        if (!$type) {
            return 'غير محدد';
        }

        return static::typeLabels()[strtolower($type)] ?? $type;
    }

    public static function billingCycleLabel(?string $cycle): string
    {
        if (!$cycle) {
            return 'غير محدد';
        }

        return static::billingCycleLabels()[strtolower($cycle)] ?? $cycle;
    }

    /**
     * مدة الباقة بصيغة مقروءة (شهر واحد، 3 أشهر، سنة واحدة)
     */
    public static function durationLabels(): array
    {
        return [
            'monthly' => 'شهر واحد',
            'quarterly' => '3 أشهر',
            'yearly' => 'سنة واحدة',
            'biannual' => '6 أشهر',
            'weekly' => 'أسبوع واحد',
        ];
    }

    public static function getDurationLabel(?string $billingCycle): string
    {
        if (!$billingCycle) {
            return 'غير محدد';
        }
        return static::durationLabels()[strtolower($billingCycle)] ?? static::billingCycleLabel($billingCycle);
    }

    /**
     * مدة الباقة للاشتراك الحالي
     */
    public function getDurationLabelAttribute(): string
    {
        return static::getDurationLabel($this->billing_cycle);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * المدفوعات عبر الفاتورة (الاشتراك مرتبط بفاتورة واحدة عبر invoice_id)
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Invoice::class,
            'id',           // مفتاح على الفاتورة يطابق subscription.invoice_id
            'invoice_id',   // مفتاح على Payment يربطها بالفاتورة
            'invoice_id',   // مفتاح على Subscription للربط بالفاتورة
            'id'            // مفتاح على الفاتورة
        );
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isActive()
    {
        if ($this->status !== 'active') {
            return false;
        }
        if (! $this->end_date) {
            return true;
        }

        return $this->end_date->toDateString() >= now()->toDateString();
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date->toDateString() < now()->toDateString();
    }

    /**
     * @param  array<int, mixed>  $keys
     * @return array<int, string>
     */
    public static function normalizeFeatureKeys(array $keys): array
    {
        return array_values(array_filter($keys, static function ($k) {
            return is_string($k) && $k !== '' && $k !== 'zoom_access';
        }));
    }

    public function subscriberIsInstructor(): bool
    {
        $role = $this->user?->role;

        return in_array($role, ['instructor', 'teacher'], true)
            || filled($this->teacher_plan_key);
    }

    public function subscriberIsStudent(): bool
    {
        return $this->user?->isStudent() ?? false;
    }

    public function tutorLessonHoursFromLimits(): ?int
    {
        $limits = $this->feature_limits;
        if (! is_array($limits) || ! array_key_exists('tutor_lesson_hours', $limits)) {
            return null;
        }

        return (int) $limits['tutor_lesson_hours'];
    }

    public function instructorPlanLabel(): ?string
    {
        if (! $this->teacher_plan_key) {
            return null;
        }

        return match ($this->teacher_plan_key) {
            'teacher_starter' => 'باقة المدرب الأساسية',
            'teacher_pro' => 'باقة المدرب الشاملة',
            default => $this->teacher_plan_key,
        };
    }
}

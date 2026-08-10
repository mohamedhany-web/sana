<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorAgreement extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUS_COMPLETED = 'completed';

    /** نوع الاتفاقية: بالجلسة | راتب شهري | باكورس كامل | نسبة من الكورس */
    public const BILLING_PER_SESSION = 'per_session';
    public const BILLING_MONTHLY = 'monthly';
    public const BILLING_FULL_COURSE = 'full_course';
    public const BILLING_COURSE_PERCENTAGE = 'course_percentage';
    public const BILLING_CONSULTATION = 'consultation_session';
    /** أجر بالساعة حسب دقائق ميتينج الحصص مع الطلبة */
    public const BILLING_HOURLY_LESSON = 'hourly_lesson';

    protected $fillable = [
        'instructor_id',
        'advanced_course_id',
        'course_percentage',
        'billing_type',
        'type',
        'rate',
        'agreement_number',
        'title',
        'description',
        'start_date',
        'end_date',
        'salary_per_session',
        'sessions_count',
        'total_amount',
        'monthly_amount',
        'months_count',
        'payment_status',
        'status',
        'terms',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rate' => 'decimal:2',
        'course_percentage' => 'decimal:2',
        'salary_per_session' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_amount' => 'decimal:2',
    ];

    /**
     * علاقة مع المدرب
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * علاقة مع الكورس الأونلاين (عند نوع الاتفاقية: نسبة من الكورس)
     */
    public function advancedCourse(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * علاقة مع مدفوعات الاتفاقية
     */
    public function payments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id');
    }

    /**
     * علاقة مع المدفوعات المكتملة (المدفوعة) فقط
     */
    public function paidPayments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id')
            ->where('status', AgreementPayment::STATUS_PAID);
    }

    /**
     * علاقة مع المدفوعات الموافق عليها (لم تُدفع بعد)
     */
    public function approvedPayments(): HasMany
    {
        return $this->hasMany(AgreementPayment::class, 'agreement_id')
            ->where('status', AgreementPayment::STATUS_APPROVED);
    }

    /**
     * علاقة مع من أنشأ الاتفاقية
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * إنشاء رقم اتفاقية تلقائي
     */
    public static function generateAgreementNumber(): string
    {
        return 'AGR-' . date('Y') . '-' . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * تسمية نوع الاتفاقية للعرض
     */
    public static function billingTypeLabels(): array
    {
        return [
            self::BILLING_PER_SESSION => 'بالجلسة',
            self::BILLING_MONTHLY => 'راتب شهري',
            self::BILLING_FULL_COURSE => 'باكورس كامل',
            self::BILLING_COURSE_PERCENTAGE => 'نسبة من الكورس',
            self::BILLING_CONSULTATION => 'استشارات',
            self::BILLING_HOURLY_LESSON => 'بالساعة (ميتينج الطلبة)',
        ];
    }

    public function getBillingTypeLabelAttribute(): string
    {
        return self::billingTypeLabels()[$this->billing_type] ?? $this->billing_type;
    }

    public function isHourlyLessonBilling(): bool
    {
        return ($this->type ?? '') === 'hourly_rate'
            || ($this->billing_type ?? '') === self::BILLING_HOURLY_LESSON;
    }

    /** تسمية نوع الاتفاقية (type أو billing_type عند نسبة من الكورس) */
    public function getTypeLabelAttribute(): string
    {
        if (($this->billing_type ?? '') === self::BILLING_COURSE_PERCENTAGE) {
            return 'نسبة من الكورس';
        }
        if (($this->billing_type ?? '') === self::BILLING_CONSULTATION) {
            return 'استشارات';
        }
        if ($this->isHourlyLessonBilling()) {
            return 'سعر بالساعة (وقت الميتينج)';
        }
        $labels = [
            'course_price' => 'سعر للكورس كاملاً',
            'hourly_rate' => 'سعر بالساعة (وقت الميتينج)',
            'monthly_salary' => 'راتب شهري',
            'consultation_session' => 'استشارات',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    /** تسمية الحالة */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_ACTIVE => 'نشط',
            self::STATUS_SUSPENDED => 'معلق',
            self::STATUS_TERMINATED => 'منتهي',
            self::STATUS_COMPLETED => 'مكتمل',
        ];
        return $labels[$this->status] ?? $this->status;
    }
}

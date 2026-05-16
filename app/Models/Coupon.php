<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'applicable_to',
        'applicable_course_ids',
        'applicable_user_ids',
        'is_active',
        'is_public',
        'beneficiary_user_id',
        'commission_percent',
        'commission_on',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'starts_at' => 'date',
        'expires_at' => 'date',
        'applicable_course_ids' => 'array',
        'applicable_user_ids' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    // العلاقات
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'beneficiary_user_id');
    }

    public function commissionAccruals()
    {
        return $this->hasMany(CouponCommissionAccrual::class);
    }

    /** هل ينطبق الكوبون على كورس متقدّم (صفحة الدفع الحالية)؟ */
    public function appliesToAdvancedCourseId(?int $courseId): bool
    {
        if (! $courseId) {
            return false;
        }

        if ($this->applicable_to === 'subscriptions') {
            return false;
        }

        if ($this->applicable_to === 'all') {
            return true;
        }

        if (in_array($this->applicable_to, ['courses', 'specific'], true)) {
            $ids = $this->applicable_course_ids ?? [];

            return is_array($ids) && count($ids) > 0
                && in_array((int) $courseId, array_map('intval', $ids), true);
        }

        return true;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', now());
            });
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        if ($this->usage_limit && $this->totalUsageCount() >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /** عدد الاستخدامات الفعلي (سجل coupon_usages مع مزامنة used_count). */
    public function totalUsageCount(): int
    {
        if ($this->relationLoaded('usages')) {
            return max((int) $this->used_count, $this->usages->count());
        }

        if (isset($this->usages_count)) {
            return max((int) $this->used_count, (int) $this->usages_count);
        }

        return max((int) $this->used_count, (int) $this->usages()->count());
    }

    /**
     * تسجيل استخدام الكوبون (آمن للتكرار — لا يُنشئ سجلاً مكرراً لنفس الطلب/الفاتورة).
     */
    public function recordUsage(
        int $userId,
        float $discountAmount,
        float $orderAmount,
        float $finalAmount,
        ?int $orderId = null,
        ?int $invoiceId = null
    ): ?CouponUsage {
        if ($discountAmount <= 0) {
            return null;
        }

        if ($orderId !== null) {
            $existing = CouponUsage::where('order_id', $orderId)->first();
            if ($existing) {
                return $existing;
            }
        }

        if ($invoiceId !== null) {
            $existing = CouponUsage::where('coupon_id', $this->id)
                ->where('invoice_id', $invoiceId)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($userId, $discountAmount, $orderAmount, $finalAmount, $orderId, $invoiceId) {
            $usage = CouponUsage::create([
                'coupon_id' => $this->id,
                'user_id' => $userId,
                'order_id' => $orderId,
                'invoice_id' => $invoiceId,
                'discount_amount' => $discountAmount,
                'order_amount' => $orderAmount,
                'final_amount' => $finalAmount,
            ]);

            $this->update(['used_count' => $this->usages()->count()]);

            return $usage;
        });
    }

    /** مزامنة used_count من جدول الاستخدامات (للبيانات القديمة). */
    public function syncUsedCountFromUsages(): void
    {
        $count = $this->usages()->count();
        if ((int) $this->used_count !== $count) {
            $this->update(['used_count' => $count]);
        }
    }

    public function canBeUsedByUser($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        // التحقق من حد الاستخدام لكل مستخدم
        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->count();

        if ($userUsageCount >= $this->usage_limit_per_user) {
            return false;
        }

        // التحقق من المستخدمين المحددين
        $allowedIds = $this->applicable_user_ids ?? [];
        if (is_array($allowedIds) && count($allowedIds) > 0) {
            $uid = (int) $userId;
            $normalized = array_map('intval', $allowedIds);
            if (! in_array($uid, $normalized, true)) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        // تطبيق الحد الأقصى للخصم
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        // التأكد من عدم تجاوز المبلغ
        if ($discount > $amount) {
            $discount = $amount;
        }

        return round($discount, 2);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}

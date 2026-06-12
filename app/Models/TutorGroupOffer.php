<?php

namespace App\Models;

use App\Services\StudentSubscriptionPlansService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutorGroupOffer extends Model
{
    protected $fillable = [
        'instructor_id',
        'package_id',
        'academic_subject_id',
        'title',
        'description',
        'max_group_size',
        'min_group_size',
        'duration_minutes',
        'display_price',
        'subscription_plan_keys',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'max_group_size' => 'integer',
        'min_group_size' => 'integer',
        'duration_minutes' => 'integer',
        'display_price' => 'decimal:2',
        'subscription_plan_keys' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(LessonBooking::class, 'tutor_group_offer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function planKeysLabel(): string
    {
        $keys = $this->subscription_plan_keys ?? [];
        if ($keys === []) {
            return __('tutor.group_offer_all_plans');
        }

        $plans = StudentSubscriptionPlansService::getPlans();
        $labels = [];
        foreach ($keys as $key) {
            $labels[] = $plans[$key]['label'] ?? $key;
        }

        return implode('، ', $labels);
    }
}

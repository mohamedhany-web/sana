<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorProfile extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const PORTAL_TUTOR_LESSONS = 'tutor_lessons';
    const PORTAL_COURSES = 'courses';
    const PORTAL_BOTH = 'both';

    public const PORTAL_MODES = [
        self::PORTAL_TUTOR_LESSONS,
        self::PORTAL_COURSES,
        self::PORTAL_BOTH,
    ];

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'photo_path',
        'experience',
        'skills',
        'social_links',
        'status',
        'instructor_portal_mode',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
        'submitted_at',
        'offers_tutor_booking',
        'tutor_matching_modes',
        'tutor_session_types',
        'tutor_subject_ids',
        'tutor_academic_year_ids',
        'tutor_years_experience',
        'tutor_default_duration_minutes',
        'tutor_onboarding_completed_at',
        'tutor_trial_completed_at',
        'tutor_activated_at',
        'application_data',
        'application_evaluation',
    ];

    protected $casts = [
        'application_data' => 'array',
        'application_evaluation' => 'array',
        'social_links' => 'array',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'offers_tutor_booking' => 'boolean',
        'tutor_matching_modes' => 'array',
        'tutor_session_types' => 'array',
        'tutor_subject_ids' => 'array',
        'tutor_academic_year_ids' => 'array',
        'tutor_onboarding_completed_at' => 'datetime',
        'tutor_trial_completed_at' => 'datetime',
        'tutor_activated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    public function scopeOffersTutorBooking($query)
    {
        return $query->where('offers_tutor_booking', true)
            ->whereNotNull('tutor_activated_at');
    }

    public function isTutorActivated(): bool
    {
        return (bool) $this->offers_tutor_booking && $this->tutor_activated_at !== null;
    }

    public function hasTutorLessonsPortal(): bool
    {
        $mode = $this->instructor_portal_mode ?? self::PORTAL_BOTH;

        return in_array($mode, [self::PORTAL_TUTOR_LESSONS, self::PORTAL_BOTH], true);
    }

    public function hasCoursesPortal(): bool
    {
        $mode = $this->instructor_portal_mode ?? self::PORTAL_BOTH;

        return in_array($mode, [self::PORTAL_COURSES, self::PORTAL_BOTH], true);
    }

    public function portalModeLabel(): string
    {
        return \App\Support\InstructorPortalAccess::modeLabel($this->instructor_portal_mode);
    }

    public function supportsMatchingMode(string $mode): bool
    {
        $modes = $this->tutor_matching_modes ?? [];

        return in_array($mode, $modes, true);
    }

    public function supportsSessionType(string $type): bool
    {
        $types = $this->tutor_session_types ?? [];

        return in_array($type, $types, true);
    }

    /**
     * رابط صورة الملف التعريفي (محلي /storage أو R2 عام/موقّع).
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($this->photo_path));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \App\Support\CloudStorage::publicUrlForPath('user_profile_disk', $path)
            ?? public_storage_url($path);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PENDING_REVIEW => 'قيد المراجعة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            default => $status,
        };
    }

    /**
     * المهارات كقائمة مرتبة (سطر لكل مهارة أو مفصولة بفاصلة)
     */
    public function getSkillsListAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        $raw = preg_split('/[\r\n,،]+/u', $this->skills, -1, PREG_SPLIT_NO_EMPTY);
        $list = array_map('trim', $raw);
        return array_values(array_filter($list));
    }

    /**
     * الخبرات كقائمة (كل سطر = نقطة/فقرة للعرض المنظم)
     */
    public function getExperienceListAttribute(): array
    {
        if (empty($this->experience)) {
            return [];
        }
        $lines = preg_split('/\r\n|\r|\n/', $this->experience, -1, PREG_SPLIT_NO_EMPTY);
        $list = array_map('trim', $lines);
        return array_values(array_filter($list));
    }
}

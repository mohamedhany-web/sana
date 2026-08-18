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
        'show_on_homepage',
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
        'show_on_homepage' => 'boolean',
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

    /**
     * ربط مسار الإدارة: يقبل id ملف الطلب أو user_id للمعلم.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();

        $profile = static::query()->where($field, $value)->first();
        if ($profile) {
            return $profile;
        }

        if ($field === $this->getRouteKeyName() && ctype_digit((string) $value)) {
            $byUser = static::query()->where('user_id', (int) $value)->first();
            if ($byUser) {
                return $byUser;
            }
        }

        return null;
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeListedOnHomepage($query)
    {
        return $query->where('show_on_homepage', true);
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
        $modes = is_array($this->tutor_matching_modes) ? $this->tutor_matching_modes : [];
        if ($modes === []) {
            $modes = [StudentLearningProfile::MODE_PICK_TEACHER];
        }

        return in_array($mode, $modes, true);
    }

    public function supportsSessionType(string $type): bool
    {
        $type = trim($type);
        if ($type === '') {
            $type = StudentLearningProfile::SESSION_ONE_TO_ONE;
        }

        return in_array($type, $this->normalizedSessionTypes(), true);
    }

    /**
     * أنواع الحصص الفعلية التي يمكن الحجز عليها (فردي افتراضياً إذا الملف فارغ أو قديم).
     *
     * @return list<string>
     */
    public function normalizedSessionTypes(): array
    {
        return self::normalizeSessionTypes($this->tutor_session_types);
    }

    public function resolveSessionType(?string $requested): string
    {
        $supported = $this->normalizedSessionTypes();
        $requested = trim((string) $requested);
        if ($requested !== '' && in_array($requested, $supported, true)) {
            return $requested;
        }

        return $supported[0];
    }

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    public static function normalizeSessionTypes(mixed $raw): array
    {
        $allowed = [
            StudentLearningProfile::SESSION_ONE_TO_ONE,
            StudentLearningProfile::SESSION_SMALL_GROUP,
        ];
        $aliases = [
            '1on1' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'one-to-one' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'individual' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'private' => StudentLearningProfile::SESSION_ONE_TO_ONE,
            'group' => StudentLearningProfile::SESSION_SMALL_GROUP,
            'small-group' => StudentLearningProfile::SESSION_SMALL_GROUP,
        ];

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [$raw];
        }
        if (! is_array($raw)) {
            $raw = [];
        }

        $flat = [];
        array_walk_recursive($raw, function ($value) use (&$flat) {
            if (is_string($value) || is_numeric($value)) {
                $flat[] = strtolower(trim((string) $value));
            }
        });

        $types = [];
        foreach ($flat as $item) {
            $item = $aliases[$item] ?? $item;
            if (in_array($item, $allowed, true)) {
                $types[] = $item;
            }
        }

        $types = array_values(array_unique($types));

        return $types !== [] ? $types : [StudentLearningProfile::SESSION_ONE_TO_ONE];
    }

    /**
     * رابط صورة الملف التعريفي (محلي /storage أو R2 عام/موقّع).
     * إن لم تُضبط photo_path نستخدم صورة الحساب profile_image.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        // إن وُجدت صورة الحساب نعرضها عبر مسار /avatars الموثوق
        if ($this->user && ! empty($this->user->profile_image)) {
            return $this->user->profile_image_url;
        }

        $raw = $this->photo_path;
        if (empty($raw)) {
            return null;
        }

        $path = str_replace('\\', '/', trim((string) $raw));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \App\Services\UserProfileImageStorage::publicUrl($path)
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

    /** حساب مُنشأ ولم يُرسل ملف التقديم الكامل للإدارة بعد */
    public function needsApplicationCompletion(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->submitted_at === null;
    }

    /**
     * مدربون أنشأوا حساباً ولم يكملوا رفع/إرسال بيانات الانضمام — لذلك لا يظهرون في طلبات الانضمام.
     */
    public static function incompleteSignupUserQuery()
    {
        return User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where(function ($q) {
                $q->whereDoesntHave('instructorProfile')
                    ->orWhereHas('instructorProfile', function ($p) {
                        $p->where('status', self::STATUS_DRAFT)
                            ->whereNull('submitted_at');
                    });
            })
            ->orderBy('name');
    }

    /** تم إرسال الملف للإدارة وبانتظار القرار */
    public function isAwaitingAdminReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW && $this->submitted_at !== null;
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

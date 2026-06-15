<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use Illuminate\Support\Collection;

class PublicCourseCatalog
{
    /** @var list<string> */
    private const PLACEHOLDER_TITLE_FRAGMENTS = [
        'تجريب', 'تجريبي', 'تجربة', 'وهمي', 'demo', 'test', 'placeholder', 'مسودة', 'draft', 'sample', 'fake', 'lorem',
    ];

    public static function defaultCardImage(): string
    {
        return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';
    }

    public static function isPlaceholderTitle(?string $title): bool
    {
        $normalized = mb_strtolower(trim((string) $title));

        if ($normalized === '') {
            return true;
        }

        foreach (self::PLACEHOLDER_TITLE_FRAGMENTS as $fragment) {
            if (str_contains($normalized, mb_strtolower($fragment))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<AdvancedCourse>  $query
     */
    public static function applyListableConstraints($query): void
    {
        $query->where('is_active', true)
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->where(function ($query) {
                $query->whereHas('lessons')->orWhereHas('lectures');
            })
            ->where(function ($query) {
                foreach (self::PLACEHOLDER_TITLE_FRAGMENTS as $fragment) {
                    $query->where('title', 'not like', '%'.$fragment.'%');
                }
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<AdvancedCourse>
     */
    public static function publiclyListableQuery()
    {
        $query = AdvancedCourse::query();
        self::applyListableConstraints($query);

        return $query;
    }

    public static function hasPublicCourses(): bool
    {
        return self::publiclyListableQuery()->exists();
    }

    public static function isPubliclyVisible(AdvancedCourse $course): bool
    {
        if (! ($course->is_active ?? false)) {
            return false;
        }

        if (self::isPlaceholderTitle($course->title)) {
            return false;
        }

        if ($course->relationLoaded('lessons_count') || $course->relationLoaded('lectures_count')) {
            return ((int) ($course->lessons_count ?? 0)) > 0 || ((int) ($course->lectures_count ?? 0)) > 0;
        }

        return $course->lessons()->exists() || $course->lectures()->exists();
    }

    /**
     * @param  Collection<int, AdvancedCourse>  $courses
     * @return Collection<int, AdvancedCourse>
     */
    public static function filterListable(Collection $courses): Collection
    {
        return $courses
            ->filter(fn (AdvancedCourse $course) => self::isPubliclyVisible($course))
            ->values();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<AdvancedCourse>
     */
    public static function publiclyVisibleQuery()
    {
        return self::publiclyListableQuery();
    }

    /**
     * @param  Collection<int, AdvancedCourse>  $courses
     * @param  array<int, float>  $enrollmentProgress
     * @return array<int, array<string, mixed>>
     */
    public static function mapForCards(Collection $courses, array $enrollmentProgress = []): array
    {
        $defaultCourseImage = self::defaultCardImage();

        return $courses->map(function (AdvancedCourse $course) use ($defaultCourseImage, $enrollmentProgress) {
            $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
            $thumbnailUrl = null;
            if ($thumbPath) {
                $thumbnailUrl = public_storage_url($thumbPath);
            }

            $courseId = (int) $course->id;
            $progress = isset($enrollmentProgress[$courseId]) ? (float) $enrollmentProgress[$courseId] : null;

            return [
                'id' => $courseId,
                'title' => $course->title ?? 'بدون عنوان',
                'description' => $course->description ?? '',
                'level' => $course->level ?? 'beginner',
                'level_label' => self::levelLabel($course->level ?? 'beginner'),
                'contact_support_for_pricing' => $course->usesContactSupportPricing(),
                'whatsapp_url' => $course->usesContactSupportPricing() ? $course->supportWhatsAppUrl() : null,
                'price' => (float) ($course->price ?? 0),
                'sale_price' => $course->effectivePurchasePrice(),
                'has_promo_price' => $course->hasPromotionalPrice(),
                'duration_hours' => (int) ($course->duration_hours ?? 0),
                'is_featured' => (bool) ($course->is_featured ?? false),
                'is_free' => ! $course->usesContactSupportPricing()
                    && (($course->is_free ?? false) || ($course->listPriceAmount() <= 0 && $course->effectivePurchasePrice() <= 0)),
                'lectures_count' => (int) ($course->lectures_count ?? 0),
                'reviews_count' => (int) ($course->reviews_count ?? 0),
                'rating' => (int) ($course->reviews_count ?? 0) > 0 && $course->rating
                    ? round((float) $course->rating, 1)
                    : null,
                'students_count' => (int) ($course->students_count ?? 0),
                'thumbnail' => $thumbPath,
                'thumbnail_url' => $thumbnailUrl,
                'card_image_url' => $thumbnailUrl ?: $defaultCourseImage,
                'academic_subject_id' => $course->academic_subject_id ? (int) $course->academic_subject_id : null,
                'academic_subject' => $course->academicSubject ? [
                    'name' => $course->academicSubject->name ?? 'غير محدد',
                ] : null,
                'academic_year_id' => $course->academic_year_id ? (int) $course->academic_year_id : null,
                'academic_year' => $course->academicYear ? [
                    'name' => $course->academicYear->name ?? '',
                ] : null,
                'course_category_id' => $course->course_category_id ? (int) $course->course_category_id : null,
                'course_category' => $course->courseCategory ? [
                    'name' => $course->courseCategory->name ?? '',
                ] : null,
                'instructor_id' => $course->instructor_id ? (int) $course->instructor_id : null,
                'instructor' => $course->instructor ? [
                    'id' => (int) $course->instructor_id,
                    'name' => $course->instructor->name,
                    'avatar_url' => $course->instructor->profile_image_url,
                ] : null,
                'enrollment_progress' => $progress,
                'is_enrolled' => $progress !== null,
            ];
        })->values()->toArray();
    }

    public static function levelLabel(?string $level): string
    {
        return match ($level) {
            'intermediate', 'medium' => 'متوسط',
            'advanced', 'expert' => 'متقدم',
            default => 'مبتدئ',
        };
    }

    /** @return array<int, int> */
    public static function savedCourseIdsFor(?\App\Models\User $user): array
    {
        if (! $user) {
            return [];
        }

        return \App\Models\StudentSavedCourse::query()
            ->where('user_id', $user->id)
            ->pluck('advanced_course_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}

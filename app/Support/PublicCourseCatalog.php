<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PublicCourseCatalog
{
    public static function defaultCardImage(): string
    {
        return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';
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
                if (str_starts_with($thumbPath, 'http://') || str_starts_with($thumbPath, 'https://')) {
                    $thumbnailUrl = $thumbPath;
                } elseif (Storage::disk('public')->exists($thumbPath)) {
                    $thumbnailUrl = asset('storage/'.$thumbPath);
                }
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
                'rating' => $course->rating ? round((float) $course->rating, 1) : 4.9,
                'reviews_count' => (int) ($course->reviews_count ?? 0),
                'students_count' => max((int) ($course->students_count ?? 0), (int) ($course->lectures_count ?? 0)),
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

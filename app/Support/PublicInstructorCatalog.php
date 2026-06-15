<?php

namespace App\Support;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\InstructorProfile;
use App\Services\InstructorMarketingRankingService;
use App\Support\CloudStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PublicInstructorCatalog
{
    /**
     * معلّمون يظهرون للجمهور: لديهم دورات جاهزة للعرض أو حجز حصص مفعّل مع مواد محددة.
     *
     * @return Builder<InstructorProfile>
     */
    public static function publiclyListableQuery(): Builder
    {
        $courseInstructorIds = PublicCourseCatalog::publiclyListableQuery()
            ->whereNotNull('instructor_id')
            ->distinct()
            ->pluck('instructor_id')
            ->filter()
            ->values()
            ->all();

        return InstructorProfile::query()
            ->approved()
            ->with('user')
            ->where(function ($query) use ($courseInstructorIds) {
                if ($courseInstructorIds !== []) {
                    $query->whereIn('user_id', $courseInstructorIds);
                }

                $query->orWhere(function ($sub) {
                    $sub->where('offers_tutor_booking', true)
                        ->whereNotNull('tutor_activated_at')
                        ->whereNotNull('tutor_subject_ids')
                        ->whereJsonLength('tutor_subject_ids', '>', 0);
                });
            });
    }

    public static function hasPublicInstructors(): bool
    {
        return self::publiclyListableQuery()
            ->get()
            ->contains(fn (InstructorProfile $profile) => self::hasMinimumPublicProfile($profile));
    }

    /**
     * @return Collection<int, InstructorProfile>
     */
    public static function rankForPublic(): Collection
    {
        $profiles = self::publiclyListableQuery()
            ->get()
            ->filter(fn (InstructorProfile $profile) => self::hasMinimumPublicProfile($profile))
            ->values();

        if ($profiles->isEmpty()) {
            return $profiles;
        }

        $profiles = InstructorMarketingRankingService::rankProfilesCollection($profiles);

        return self::enrichProfiles($profiles);
    }

    public static function hasMinimumPublicProfile(InstructorProfile $profile): bool
    {
        if ($profile->status !== InstructorProfile::STATUS_APPROVED) {
            return false;
        }

        $name = trim((string) ($profile->user?->name ?? ''));
        if ($name === '') {
            return false;
        }

        $hasListableCourses = PublicCourseCatalog::publiclyListableQuery()
            ->where('instructor_id', $profile->user_id)
            ->exists();

        if ($hasListableCourses) {
            return true;
        }

        if (! $profile->isTutorActivated()) {
            return false;
        }

        $subjectIds = is_array($profile->tutor_subject_ids) ? $profile->tutor_subject_ids : [];
        if (count($subjectIds) === 0) {
            return false;
        }

        $hasHeadline = trim((string) ($profile->headline ?? '')) !== '';
        $hasBio = trim((string) ($profile->bio ?? '')) !== '';

        return $hasHeadline || $hasBio;
    }

    public static function isPubliclyListable(InstructorProfile $profile): bool
    {
        return self::hasMinimumPublicProfile($profile);
    }

    /**
     * @param  Collection<int, InstructorProfile>  $profiles
     * @return Collection<int, InstructorProfile>
     */
    public static function enrichProfiles(Collection $profiles): Collection
    {
        if ($profiles->isEmpty()) {
            return $profiles;
        }

        $userIds = $profiles->pluck('user_id')->unique()->values()->all();
        $subjectIds = $profiles
            ->flatMap(fn (InstructorProfile $p) => is_array($p->tutor_subject_ids) ? $p->tutor_subject_ids : [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $subjectNames = $subjectIds === []
            ? collect()
            : AcademicSubject::query()->whereIn('id', $subjectIds)->pluck('name', 'id');

        $yearIds = $profiles
            ->flatMap(fn (InstructorProfile $p) => is_array($p->tutor_academic_year_ids) ? $p->tutor_academic_year_ids : [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $yearNames = $yearIds === []
            ? collect()
            : AcademicYear::query()->whereIn('id', $yearIds)->pluck('name', 'id');

        $listableCourseIds = PublicCourseCatalog::publiclyListableQuery()->pluck('id');

        $publicCourseCounts = AdvancedCourse::query()
            ->selectRaw('instructor_id, COUNT(*) as aggregate')
            ->whereIn('instructor_id', $userIds)
            ->whereIn('id', $listableCourseIds)
            ->groupBy('instructor_id')
            ->pluck('aggregate', 'instructor_id');

        $profiles->each(function (InstructorProfile $profile) use ($subjectNames, $yearNames, $publicCourseCounts) {
            $profile->courses_count = (int) ($publicCourseCounts[$profile->user_id] ?? 0);
            $profile->is_bookable = $profile->isTutorActivated()
                && is_array($profile->tutor_subject_ids)
                && count($profile->tutor_subject_ids) > 0;

            $labels = [];
            foreach ($profile->tutor_subject_ids ?? [] as $subjectId) {
                $name = $subjectNames->get((int) $subjectId);
                if (is_string($name) && $name !== '') {
                    $labels[] = $name;
                }
            }

            foreach ($profile->skills_list as $skill) {
                if (is_string($skill) && $skill !== '' && ! in_array($skill, $labels, true)) {
                    $labels[] = $skill;
                }
            }

            $profile->public_subject_labels = array_values(array_slice(array_unique($labels), 0, 6));

            $gradeLabels = [];
            foreach ($profile->tutor_academic_year_ids ?? [] as $yearId) {
                $name = $yearNames->get((int) $yearId);
                if (is_string($name) && $name !== '') {
                    $gradeLabels[] = $name;
                }
            }
            $profile->public_grade_labels = array_values(array_slice(array_unique($gradeLabels), 0, 6));

            $app = is_array($profile->application_data) ? $profile->application_data : [];
            $profile->public_curriculum_labels = self::applicationOptionLabels(
                $app['curricula'] ?? [],
                config('tutor_application.curricula', [])
            );
            $profile->public_stage_labels = self::applicationOptionLabels(
                $app['stages'] ?? [],
                config('tutor_application.stages', [])
            );
            $profile->public_session_labels = self::sessionTypeLabels($profile->tutor_session_types ?? []);
            $profile->public_years_experience = self::resolveYearsExperience($profile, $app);
            $profile->public_booking_label = self::resolveBookingLabel($profile, $app, $profile->public_session_labels);
            $profile->public_demo_video = self::resolveDemoVideo($app);
            $profile->public_book_url = self::bookUrlFor($profile);
        });

        return $profiles;
    }

    public static function bookUrlFor(InstructorProfile $profile): string
    {
        $user = $profile->user;
        if (! $user) {
            return route('register');
        }

        if (! auth()->check()) {
            return route('register', ['redirect' => route('public.instructors.show', $user)]);
        }

        $authUser = auth()->user();
        if ($authUser->role === 'parent') {
            return route('parent.tutor-lessons.book', $user);
        }

        if (in_array($authUser->role, ['student'], true)) {
            return route('student.tutor-lessons.book', $user);
        }

        return route('public.pricing');
    }

    /**
     * @param  list<string>  $keys
     * @param  array<string, string>  $map
     * @return list<string>
     */
    private static function applicationOptionLabels(array $keys, array $map): array
    {
        $labels = [];
        foreach ($keys as $key) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            $label = $map[$key] ?? null;
            if (is_string($label) && $label !== '' && ! in_array($label, $labels, true)) {
                $labels[] = $label;
            }
        }

        return array_values(array_slice($labels, 0, 6));
    }

    /**
     * @param  list<string>  $types
     * @return list<string>
     */
    private static function sessionTypeLabels(array $types): array
    {
        $map = config('tutor_application.lesson_formats', []);
        $labels = [];
        foreach ($types as $type) {
            if (! is_string($type) || $type === '') {
                continue;
            }
            $label = match ($type) {
                'one_to_one' => 'حصة فردية',
                'small_group' => 'مجموعة صغيرة',
                default => $map[$type] ?? null,
            };
            if (is_string($label) && $label !== '' && ! in_array($label, $labels, true)) {
                $labels[] = $label;
            }
        }

        return array_values(array_slice($labels, 0, 4));
    }

    /**
     * @param  array<string, mixed>  $application
     */
    private static function resolveYearsExperience(InstructorProfile $profile, array $application): ?int
    {
        if ($profile->tutor_years_experience !== null && (int) $profile->tutor_years_experience > 0) {
            return (int) $profile->tutor_years_experience;
        }

        $years = (int) ($application['years_experience'] ?? 0);

        return $years > 0 ? $years : null;
    }

    /**
     * @param  array<string, mixed>  $application
     */
    private static function resolveBookingLabel(InstructorProfile $profile, array $application, array $sessionLabels): string
    {
        $expected = trim((string) ($application['expected_rate'] ?? ''));
        if ($expected !== '') {
            return $expected;
        }

        if ($sessionLabels !== []) {
            return implode(' · ', $sessionLabels);
        }

        if ($profile->isTutorActivated()) {
            return __('public.instructor_booking_via_packages');
        }

        return __('public.instructor_booking_contact');
    }

    /**
     * @param  array<string, mixed>  $application
     * @return array{embed: ?string, direct: ?string, title: ?string}|null
     */
    private static function resolveDemoVideo(array $application): ?array
    {
        $video = is_array($application['video'] ?? null) ? $application['video'] : [];
        $link = trim((string) ($video['link'] ?? $application['demo_video_link'] ?? ''));
        $title = trim((string) ($video['topic_title'] ?? $application['video_topic_title'] ?? ''));

        if ($link !== '') {
            $embed = \App\Helpers\VideoHelper::getEmbedUrl($link);
            if (! $embed && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $link, $m)) {
                $embed = 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1';
            } elseif (! $embed && preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $link, $m)) {
                $embed = 'https://player.vimeo.com/video/'.$m[1];
            }

            if ($embed) {
                return ['embed' => $embed, 'direct' => null, 'title' => $title !== '' ? $title : null];
            }

            if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $link)) {
                return ['embed' => null, 'direct' => $link, 'title' => $title !== '' ? $title : null];
            }
        }

        $filePath = trim((string) ($video['file_path'] ?? ''));
        if ($filePath !== '') {
            $direct = CloudStorage::publicUrlForPath('tutor_application_disk', $filePath)
                ?? public_storage_url($filePath);

            if ($direct) {
                return ['embed' => null, 'direct' => $direct, 'title' => $title !== '' ? $title : null];
            }
        }

        return null;
    }
}

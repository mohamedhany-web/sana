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
            ->listedOnHomepage()
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
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
        if (! $profile->show_on_homepage) {
            return false;
        }

        if (! ($profile->user?->is_active ?? false)) {
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
            $teaching = is_array($app['teaching'] ?? null) ? $app['teaching'] : [];
            $curriculumKeys = $teaching['curricula'] ?? ($app['curricula'] ?? []);
            $stageKeys = $teaching['stages'] ?? ($app['stages'] ?? []);
            $specializationKeys = $teaching['specializations'] ?? ($app['specializations'] ?? []);

            $profile->public_curriculum_keys = self::normalizeOptionKeys($curriculumKeys);
            $profile->public_stage_keys = self::normalizeOptionKeys($stageKeys);
            $profile->public_specialization_keys = self::normalizeOptionKeys($specializationKeys);
            $profile->public_curriculum_labels = self::applicationOptionLabels(
                $profile->public_curriculum_keys,
                config('tutor_application.curricula', [])
            );
            $profile->public_stage_labels = self::applicationOptionLabels(
                $profile->public_stage_keys,
                config('tutor_application.stages', [])
            );
            $profile->public_specialization_labels = self::applicationOptionLabels(
                $profile->public_specialization_keys,
                config('tutor_application.specializations', [])
            );
            $profile->public_session_labels = self::sessionTypeLabels($profile->tutor_session_types ?? []);
            $profile->public_years_experience = self::resolveYearsExperience($profile, $app);
            $profile->public_booking_label = self::resolveBookingLabel($profile, $app, $profile->public_session_labels);
            $profile->public_demo_video = self::resolveDemoVideo($app);
            $profile->public_book_url = self::bookUrlFor($profile);
            $profile->public_has_video = self::resolveDemoVideo($app) !== null;
        });

        return $profiles;
    }

    /**
     * فلاتر صفحة المعلمين العامة — تفيد الطالب في الوصول السريع للمعلّم المناسب.
     *
     * @param  Collection<int, InstructorProfile>  $profiles
     * @return Collection<int, InstructorProfile>
     */
    public static function applyPublicFilters(Collection $profiles, \Illuminate\Http\Request $request): Collection
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $subjectId = $request->integer('subject_id') ?: null;
        $yearId = $request->integer('academic_year_id') ?: null;
        $curriculum = trim((string) $request->input('curriculum', ''));
        $stage = trim((string) $request->input('stage', ''));
        $specialization = trim((string) $request->input('specialization', ''));
        $sessionType = trim((string) $request->input('session_type', ''));
        $availability = trim((string) $request->input('availability', ''));
        $experienceMin = $request->filled('experience_min') ? max(0, $request->integer('experience_min')) : null;
        $hasVideo = trim((string) $request->input('has_video', ''));
        $sort = trim((string) $request->input('sort', ''));

        $filtered = $profiles->filter(function (InstructorProfile $profile) use (
            $search,
            $subjectId,
            $yearId,
            $curriculum,
            $stage,
            $specialization,
            $sessionType,
            $availability,
            $experienceMin,
            $hasVideo
        ) {
            if ($search !== '') {
                $haystack = mb_strtolower(implode(' ', array_filter([
                    $profile->user?->name,
                    $profile->headline,
                    $profile->bio,
                    implode(' ', $profile->public_subject_labels ?? []),
                    implode(' ', $profile->public_grade_labels ?? []),
                    implode(' ', $profile->public_curriculum_labels ?? []),
                    implode(' ', $profile->public_stage_labels ?? []),
                    implode(' ', $profile->public_specialization_labels ?? []),
                    implode(' ', $profile->skills_list ?? []),
                    (string) ($profile->application_data['personal']['nationality'] ?? ''),
                    (string) ($profile->application_data['personal']['country_city'] ?? ''),
                ])));

                if (! str_contains($haystack, mb_strtolower($search))) {
                    return false;
                }
            }

            if ($subjectId) {
                $ids = collect($profile->tutor_subject_ids ?? [])->map(fn ($id) => (int) $id)->all();
                if (! in_array($subjectId, $ids, true)) {
                    return false;
                }
            }

            if ($yearId) {
                $ids = collect($profile->tutor_academic_year_ids ?? [])->map(fn ($id) => (int) $id)->all();
                if (! in_array($yearId, $ids, true)) {
                    return false;
                }
            }

            if ($curriculum !== '' && ! in_array($curriculum, $profile->public_curriculum_keys ?? [], true)) {
                return false;
            }

            if ($stage !== '' && ! in_array($stage, $profile->public_stage_keys ?? [], true)) {
                return false;
            }

            if ($specialization !== '' && ! in_array($specialization, $profile->public_specialization_keys ?? [], true)) {
                return false;
            }

            if ($sessionType !== '') {
                $types = collect($profile->tutor_session_types ?? [])->map(fn ($t) => (string) $t)->all();
                if (! in_array($sessionType, $types, true)) {
                    return false;
                }
            }

            if ($availability === 'bookable' && empty($profile->is_bookable)) {
                return false;
            }
            if ($availability === 'courses' && (int) ($profile->courses_count ?? 0) <= 0) {
                return false;
            }
            if ($availability === 'bookable_courses'
                && (empty($profile->is_bookable) || (int) ($profile->courses_count ?? 0) <= 0)
            ) {
                return false;
            }

            if ($experienceMin !== null) {
                $years = (int) ($profile->public_years_experience ?? 0);
                if ($years < $experienceMin) {
                    return false;
                }
            }

            if ($hasVideo === 'yes' && empty($profile->public_has_video)) {
                return false;
            }
            if ($hasVideo === 'no' && ! empty($profile->public_has_video)) {
                return false;
            }

            return true;
        })->values();

        return self::sortPublicProfiles($filtered, $sort);
    }

    /**
     * خيارات الفلاتر المبنية من المعلّمين الظاهرين فعلياً (بدون نتائج فارغة).
     *
     * @param  Collection<int, InstructorProfile>  $profiles
     * @return array<string, mixed>
     */
    public static function publicFilterOptions(Collection $profiles): array
    {
        $subjectMap = [];
        $yearMap = [];
        $curriculumMap = config('tutor_application.curricula', []);
        $stageMap = config('tutor_application.stages', []);
        $specializationMap = config('tutor_application.specializations', []);
        $curriculumCounts = [];
        $stageCounts = [];
        $specializationCounts = [];
        $sessionCounts = ['one_to_one' => 0, 'small_group' => 0];

        foreach ($profiles as $profile) {
            foreach ($profile->tutor_subject_ids ?? [] as $id) {
                $id = (int) $id;
                if ($id > 0) {
                    $subjectMap[$id] = ($subjectMap[$id] ?? 0) + 1;
                }
            }
            foreach ($profile->tutor_academic_year_ids ?? [] as $id) {
                $id = (int) $id;
                if ($id > 0) {
                    $yearMap[$id] = ($yearMap[$id] ?? 0) + 1;
                }
            }
            foreach ($profile->public_curriculum_keys ?? [] as $key) {
                $curriculumCounts[$key] = ($curriculumCounts[$key] ?? 0) + 1;
            }
            foreach ($profile->public_stage_keys ?? [] as $key) {
                $stageCounts[$key] = ($stageCounts[$key] ?? 0) + 1;
            }
            foreach ($profile->public_specialization_keys ?? [] as $key) {
                $specializationCounts[$key] = ($specializationCounts[$key] ?? 0) + 1;
            }
            foreach ($profile->tutor_session_types ?? [] as $type) {
                $type = (string) $type;
                if (isset($sessionCounts[$type])) {
                    $sessionCounts[$type]++;
                }
            }
        }

        $subjectNames = $subjectMap === []
            ? collect()
            : AcademicSubject::query()->whereIn('id', array_keys($subjectMap))->pluck('name', 'id');
        $yearNames = $yearMap === []
            ? collect()
            : AcademicYear::query()->whereIn('id', array_keys($yearMap))->orderBy('order')->pluck('name', 'id');

        $subjects = collect($subjectMap)
            ->map(fn ($count, $id) => [
                'id' => (int) $id,
                'name' => (string) ($subjectNames[$id] ?? ('#'.$id)),
                'count' => (int) $count,
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->sortBy('name', SORT_NATURAL)
            ->values()
            ->all();

        $years = collect($yearMap)
            ->map(fn ($count, $id) => [
                'id' => (int) $id,
                'name' => (string) ($yearNames[$id] ?? ('#'.$id)),
                'count' => (int) $count,
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->values()
            ->all();

        $mapOptions = function (array $counts, array $labels): array {
            $rows = [];
            foreach ($counts as $key => $count) {
                $label = $labels[$key] ?? null;
                if (! is_string($label) || $label === '') {
                    continue;
                }
                $rows[] = ['key' => (string) $key, 'label' => $label, 'count' => (int) $count];
            }

            return $rows;
        };

        return [
            'subjects' => $subjects,
            'years' => $years,
            'curricula' => $mapOptions($curriculumCounts, $curriculumMap),
            'stages' => $mapOptions($stageCounts, $stageMap),
            'specializations' => $mapOptions($specializationCounts, $specializationMap),
            'session_types' => array_values(array_filter([
                $sessionCounts['one_to_one'] > 0 ? [
                    'key' => 'one_to_one',
                    'label' => __('tutor.session_one_to_one'),
                    'count' => $sessionCounts['one_to_one'],
                ] : null,
                $sessionCounts['small_group'] > 0 ? [
                    'key' => 'small_group',
                    'label' => __('tutor.session_small_group'),
                    'count' => $sessionCounts['small_group'],
                ] : null,
            ])),
            'bookable_count' => (int) $profiles->filter(fn ($p) => ! empty($p->is_bookable))->count(),
            'courses_count' => (int) $profiles->filter(fn ($p) => (int) ($p->courses_count ?? 0) > 0)->count(),
            'video_count' => (int) $profiles->filter(fn ($p) => ! empty($p->public_has_video))->count(),
            'total' => $profiles->count(),
        ];
    }

    public static function publicFilterKeys(): array
    {
        return [
            'q', 'search', 'subject_id', 'academic_year_id', 'curriculum', 'stage',
            'specialization', 'session_type', 'availability', 'experience_min',
            'has_video', 'sort', 'tutors', 'mode',
        ];
    }

    /**
     * @param  Collection<int, InstructorProfile>  $profiles
     * @return Collection<int, InstructorProfile>
     */
    private static function sortPublicProfiles(Collection $profiles, string $sort): Collection
    {
        return match ($sort) {
            'name' => $profiles->sortBy(fn (InstructorProfile $p) => mb_strtolower((string) ($p->user?->name ?? '')), SORT_NATURAL)->values(),
            'experience_desc' => $profiles->sortByDesc(fn (InstructorProfile $p) => (int) ($p->public_years_experience ?? 0))->values(),
            'courses_desc' => $profiles->sortByDesc(fn (InstructorProfile $p) => (int) ($p->courses_count ?? 0))->values(),
            'bookable_first' => $profiles->sortByDesc(fn (InstructorProfile $p) => ! empty($p->is_bookable) ? 1 : 0)->values(),
            default => $profiles,
        };
    }

    /**
     * @param  mixed  $keys
     * @return list<string>
     */
    private static function normalizeOptionKeys($keys): array
    {
        if (! is_array($keys)) {
            return [];
        }

        $out = [];
        foreach ($keys as $key) {
            if (! is_string($key) && ! is_numeric($key)) {
                continue;
            }
            $key = trim((string) $key);
            if ($key !== '' && ! in_array($key, $out, true)) {
                $out[] = $key;
            }
        }

        return $out;
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

            if (! $embed && preg_match('#drive\.google\.com/(?:file/d/|open\?id=)([a-zA-Z0-9_-]+)#', $link, $m)) {
                $embed = 'https://drive.google.com/file/d/'.$m[1].'/preview';
            }

            if ($embed) {
                return ['embed' => $embed, 'direct' => null, 'title' => $title !== '' ? $title : null];
            }

            if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $link)) {
                return ['embed' => null, 'direct' => $link, 'title' => $title !== '' ? $title : null];
            }
        }

        $filePath = trim((string) ($video['file_path'] ?? $application['demo_video_path'] ?? ''));
        if ($filePath !== '') {
            $direct = CloudStorage::publicUrlForPath('tutor_application_disk', $filePath)
                ?? CloudStorage::publicUploadUrl($filePath)
                ?? CloudStorage::localPublicStorageUrl(ltrim(str_replace('\\', '/', $filePath), '/'));

            if ($direct) {
                return ['embed' => null, 'direct' => $direct, 'title' => $title !== '' ? $title : null];
            }
        }

        return null;
    }
}

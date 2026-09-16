<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\LessonBookingService;
use App\Support\PublicCourseCatalog;
use App\Support\PublicInstructorCatalog;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $tutorBookingMode = $request->boolean('tutors') || $request->input('mode') === 'pick_teacher';

        if ($tutorBookingMode) {
            $profiles = LessonBookingService::bookableInstructorsQuery(
                \App\Models\StudentLearningProfile::MODE_PICK_TEACHER,
                null
            )->get()
                ->filter(fn (InstructorProfile $profile) => PublicInstructorCatalog::hasMinimumPublicProfile($profile))
                ->values();

            $profiles = PublicInstructorCatalog::enrichProfiles($profiles);
        } else {
            $profiles = PublicInstructorCatalog::rankForPublic();
        }

        $filterOptions = PublicInstructorCatalog::publicFilterOptions($profiles);
        $filteredProfiles = PublicInstructorCatalog::applyPublicFilters($profiles, $request);

        $activeFilters = collect(PublicInstructorCatalog::publicFilterKeys())
            ->reject(fn (string $key) => in_array($key, ['tutors', 'mode'], true))
            ->filter(fn (string $key) => filled($request->input($key)))
            ->values()
            ->all();

        return view('instructors.index', [
            'profiles' => $filteredProfiles,
            'allProfilesCount' => $profiles->count(),
            'filterOptions' => $filterOptions,
            'activeFilters' => $activeFilters,
            'tutorBookingMode' => $tutorBookingMode,
        ]);
    }

    public function show(User $instructor)
    {
        if (! $instructor->isInstructor()) {
            abort(404);
        }

        $profile = InstructorProfile::where('user_id', $instructor->id)
            ->listedOnHomepage()
            ->with('user')
            ->firstOrFail();

        if (! PublicInstructorCatalog::isPubliclyListable($profile)) {
            abort(404);
        }

        PublicInstructorCatalog::enrichProfiles(collect([$profile]));

        $courses = PublicCourseCatalog::publiclyListableQuery()
            ->where('instructor_id', $instructor->id)
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->get();

        $savedCourseIds = PublicCourseCatalog::savedCourseIdsFor(auth()->user());

        return view('instructors.show', compact('profile', 'courses', 'savedCourseIds'));
    }
}

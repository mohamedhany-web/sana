<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Services\LessonBookingService;
use App\Support\PublicCourseCatalog;
use App\Support\PublicInstructorCatalog;

class InstructorController extends Controller
{
    public function index()
    {
        if (request()->boolean('tutors') || request('mode') === 'pick_teacher') {
            $profiles = LessonBookingService::bookableInstructorsQuery(
                \App\Models\StudentLearningProfile::MODE_PICK_TEACHER,
                request()->integer('subject_id') ?: null
            )->get()
                ->filter(fn (InstructorProfile $profile) => PublicInstructorCatalog::hasMinimumPublicProfile($profile))
                ->values();

            return view('instructors.index', [
                'profiles' => PublicInstructorCatalog::enrichProfiles($profiles),
                'tutorBookingMode' => true,
            ]);
        }

        $profiles = PublicInstructorCatalog::rankForPublic();

        return view('instructors.index', [
            'profiles' => $profiles,
            'tutorBookingMode' => false,
        ]);
    }

    public function show(User $instructor)
    {
        if (! $instructor->isInstructor()) {
            abort(404);
        }

        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();

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

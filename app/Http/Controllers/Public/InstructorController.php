<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Services\InstructorMarketingRankingService;
use App\Services\LessonBookingService;

class InstructorController extends Controller
{
    public function index()
    {
        if (request()->boolean('tutors') || request('mode') === 'pick_teacher') {
            $profiles = LessonBookingService::bookableInstructorsQuery(
                \App\Models\StudentLearningProfile::MODE_PICK_TEACHER,
                request()->integer('subject_id') ?: null
            )->get();

            return view('instructors.index', [
                'profiles' => $profiles,
                'tutorBookingMode' => true,
            ]);
        }

        $profiles = InstructorMarketingRankingService::rankApprovedProfiles();

        return view('instructors.index', [
            'profiles' => $profiles,
            'tutorBookingMode' => false,
        ]);
    }

    public function show(User $instructor)
    {
        if (!$instructor->isInstructor()) {
            abort(404);
        }
        $profile = InstructorProfile::where('user_id', $instructor->id)->approved()->with('user')->firstOrFail();
        $courses = \App\Models\AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->withCount('lessons')
            ->orderBy('is_featured', 'desc')
            ->get();

        $savedCourseIds = \App\Support\PublicCourseCatalog::savedCourseIdsFor(auth()->user());

        return view('instructors.show', compact('profile', 'courses', 'savedCourseIds'));
    }
}

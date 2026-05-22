<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\StudentSavedCourse;
use App\Support\PublicCourseCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseFavoriteController extends Controller
{
    public function ids(): JsonResponse
    {
        return response()->json([
            'ids' => PublicCourseCatalog::savedCourseIdsFor(auth()->user()),
        ]);
    }

    public function toggle(AdvancedCourse $course): JsonResponse
    {
        abort_unless($course->is_active, 404);

        $user = auth()->user();
        $existing = StudentSavedCourse::query()
            ->where('user_id', $user->id)
            ->where('advanced_course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            StudentSavedCourse::create([
                'user_id' => $user->id,
                'advanced_course_id' => $course->id,
            ]);
            $saved = true;
        }

        return response()->json([
            'saved' => $saved,
            'ids' => PublicCourseCatalog::savedCourseIdsFor($user),
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'min:1'],
        ]);

        $user = auth()->user();
        $validIds = AdvancedCourse::query()
            ->where('is_active', true)
            ->whereIn('id', $data['ids'])
            ->pluck('id');

        foreach ($validIds as $courseId) {
            StudentSavedCourse::firstOrCreate([
                'user_id' => $user->id,
                'advanced_course_id' => $courseId,
            ]);
        }

        return response()->json([
            'ids' => PublicCourseCatalog::savedCourseIdsFor($user),
        ]);
    }
}

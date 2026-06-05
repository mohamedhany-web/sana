<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\TutorAvailability;
use App\Services\TutorInstructorActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TutorAvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $user = Auth::user();
        $user->tutorAvailabilities()->create([
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'].':00',
            'end_time' => $data['end_time'].':00',
            'is_active' => true,
        ]);

        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );
        $activated = TutorInstructorActivationService::attemptAutoActivate($profile->fresh(), $user);

        $message = $activated
            ? 'تمت إضافة الفترة وتفعيل حسابك للطلاب.'
            : 'تمت إضافة الفترة للجدول الأسبوعي.';

        return back()->with('success', $message);
    }

    public function destroy(TutorAvailability $availability)
    {
        if ($availability->instructor_id !== Auth::id()) {
            abort(403);
        }
        $availability->delete();

        return back()->with('success', 'تم حذف الفترة.');
    }

    public function toggle(TutorAvailability $availability)
    {
        if ($availability->instructor_id !== Auth::id()) {
            abort(403);
        }
        $availability->update(['is_active' => ! $availability->is_active]);

        return back();
    }
}

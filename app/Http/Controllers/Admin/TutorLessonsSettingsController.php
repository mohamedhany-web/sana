<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentLearningProfile;
use App\Models\User;
use App\Services\StudentSubscriptionPlansService;
use App\Services\TutorLessonQuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TutorLessonsSettingsController extends Controller
{
    public function edit()
    {
        $settings = TutorLessonQuotaService::settings();
        $studentPlans = StudentSubscriptionPlansService::getPlans();

        return view('admin.tutor-lessons.settings', compact('settings', 'studentPlans'));
    }

    public function updateStudentPlans(Request $request)
    {
        $validated = $request->validate([
            'plans' => ['required', 'array'],
            'plans.*.label' => ['required', 'string', 'max:120'],
            'plans.*.price' => ['required', 'numeric', 'min:0'],
            'plans.*.billing_cycle' => ['required', 'in:monthly,quarterly,yearly'],
            'plans.*.card_subtitle' => ['nullable', 'string', 'max:200'],
            'plans.*.card_badge' => ['nullable', 'string', 'max:40'],
            'plans.*.card_price_hint' => ['nullable', 'string', 'max:80'],
            'plans.*.limits.tutor_lesson_hours' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        $payload = [];
        foreach (StudentSubscriptionPlansService::planKeys() as $key) {
            $row = $validated['plans'][$key] ?? null;
            if (! is_array($row)) {
                continue;
            }
            $payload[$key] = $row;
        }

        if ($payload === []) {
            return back()->with('error', 'لم تُرسل بيانات الباقات.');
        }

        StudentSubscriptionPlansService::savePlans($payload);

        return back()->with('success', 'تم حفظ قوالب باقات الطلاب.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_student_lesson_hours' => ['required', 'integer', 'min:0', 'max:10000'],
            'self_schedule_enabled' => ['nullable', 'boolean'],
            'booking_advance_days' => ['required', 'integer', 'min:1', 'max:60'],
            'slot_step_minutes' => ['required', 'integer', 'min:15', 'max:120'],
            'default_duration_minutes' => ['required', 'integer', 'min:30', 'max:180'],
        ]);

        $payload = [
            'default_student_lesson_hours' => (int) $validated['default_student_lesson_hours'],
            'self_schedule_enabled' => $request->boolean('self_schedule_enabled'),
            'booking_advance_days' => (int) $validated['booking_advance_days'],
            'slot_step_minutes' => (int) $validated['slot_step_minutes'],
            'default_duration_minutes' => (int) $validated['default_duration_minutes'],
        ];

        $key = config('tutor_lessons.settings_key', 'tutor_lessons');
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $now = now();
        if (DB::table('settings')->where('key', $key)->exists()) {
            DB::table('settings')->where('key', $key)->update(['value' => $json, 'updated_at' => $now]);
        } else {
            DB::table('settings')->insert(['key' => $key, 'value' => $json, 'created_at' => $now, 'updated_at' => $now]);
        }

        TutorLessonQuotaService::clearSettingsCache();

        return back()->with('success', 'تم حفظ إعدادات حصص الطلاب.');
    }

    public function updateStudentQuota(Request $request, User $user)
    {
        if (! $user->isStudent()) {
            abort(404);
        }

        $data = $request->validate([
            'lesson_hours_quota' => ['required', 'integer', 'min:-1', 'max:10000'],
            'lesson_hours_used' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        $profile = StudentLearningProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['matching_mode' => StudentLearningProfile::MODE_PICK_TEACHER]
        );

        $profile->update([
            'lesson_hours_quota' => (int) $data['lesson_hours_quota'],
            'lesson_hours_used' => (int) ($data['lesson_hours_used'] ?? $profile->lesson_hours_used),
        ]);

        return back()->with('success', 'تم تحديث باقة ساعات الطالب.');
    }
}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Services\UserProfileImageStorage;
use Illuminate\Http\Request;

class PersonalBrandingController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $skillsCount = count($profile->skills_list);
        $canSubmit = filled($profile->headline)
            && filled($profile->bio)
            && $skillsCount >= 3;

        return view('instructor.personal-branding.edit', compact('profile', 'user', 'skillsCount', 'canSubmit'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $data = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'experience' => 'nullable|string|max:50000',
            'skills' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|max:'.config('upload_limits.max_upload_kb'),
        ], [
            'experience.max' => 'الخبرات في المجال يجب ألا تتجاوز 50 ألف حرف. إن احتجت مساحة أكبر تواصل مع الإدارة.',
            'skills.max' => 'المهارات يجب ألا تتجاوز 5 آلاف حرف.',
            'photo.image' => 'الملف الذي تم رفعه يجب أن يكون صورة',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ]);

        if ($request->hasFile('photo')) {
            try {
                UserProfileImageStorage::delete($profile->photo_path);
                $data['photo_path'] = UserProfileImageStorage::storeInDirectory(
                    $request->file('photo'),
                    'instructor-profiles'
                );
                UserProfileImageStorage::syncInstructorDisplayPhoto((int) $user->id, $data['photo_path']);
            } catch (\Throwable $e) {
                report($e);

                return back()
                    ->withErrors(['photo' => 'تعذّر رفع الصورة إلى التخزين السحابي. أعد المحاولة أو تواصل مع الإدارة.'])
                    ->withInput();
            }
        }

        unset($data['photo']);
        $data['social_links'] = [];

        $profile->update($data);

        return back()->with('success', 'تم حفظ الملف التعريفي.');
    }

    public function submit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::where('user_id', $user->id)->firstOrFail();

        // حد أدنى للجودة قبل طلب الظهور على الرئيسية
        if (! $profile->headline || ! $profile->bio || count($profile->skills_list) < 3) {
            return back()->with('error', 'أكمل الملف قبل الإرسال: عنوان تعريفي + نبذة + 3 مهارات على الأقل.');
        }

        // لا نغيّر status (قبول المعلم) — فقط نعلّم أن المحتوى جاهز لمراجعة الظهور على الرئيسية
        $updates = [
            'rejection_reason' => null,
        ];
        if ($profile->submitted_at === null) {
            $updates['submitted_at'] = now();
        }
        // إن كان ما زال مسودة طلب تقديم، نبقيه قيد المراجعة للقبول من صفحة الطلبات فقط
        // أما إن كان مقبولاً مسبقاً فلا نلمسه
        if (in_array($profile->status, [InstructorProfile::STATUS_DRAFT, InstructorProfile::STATUS_REJECTED], true)) {
            // لا نحوّل المقبول لـ pending؛ المسودة فقط تبقى كما هي حتى تُقبل من «طلبات المعلمين»
        }

        $profile->update($updates);

        return back()->with('success', 'تم حفظ الملف التعريفي. الظهور على الصفحة الرئيسية يتم من لوحة التسويق الشخصي دون التأثير على قبول حسابك.');
    }
}

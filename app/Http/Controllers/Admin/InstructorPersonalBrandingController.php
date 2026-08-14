<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Services\UserProfileImageStorage;
use Illuminate\Http\Request;

class InstructorPersonalBrandingController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::with(['user', 'reviewedByUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $counts = [
            'listed' => InstructorProfile::listedOnHomepage()->count(),
            'hidden' => InstructorProfile::where('show_on_homepage', false)->count(),
            'pending' => InstructorProfile::pending()->count(),
            'approved' => InstructorProfile::approved()->count(),
            'rejected' => InstructorProfile::where('status', InstructorProfile::STATUS_REJECTED)->count(),
            'draft' => InstructorProfile::where('status', InstructorProfile::STATUS_DRAFT)->count(),
        ];

        if ($request->filled('visibility')) {
            if ($request->visibility === 'listed') {
                $query->where('show_on_homepage', true);
            } elseif ($request->visibility === 'hidden') {
                $query->where('show_on_homepage', false);
            }
        }

        $profiles = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('admin.marketing.personal-branding.index', compact('profiles', 'counts'));
    }

    public function show(InstructorProfile $personal_branding)
    {
        $personal_branding->load(['user', 'reviewedByUser']);

        return view('admin.marketing.personal-branding.show', compact('personal_branding'));
    }

    public function edit(InstructorProfile $personal_branding)
    {
        $personal_branding->load('user');

        return view('admin.marketing.personal-branding.edit', compact('personal_branding'));
    }

    public function update(Request $request, InstructorProfile $personal_branding)
    {
        $data = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'experience' => 'nullable|string|max:50000',
            'skills' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|max:'.config('upload_limits.max_upload_kb'),
        ], [
            'experience.max' => 'الخبرات في المجال يجب ألا تتجاوز 50 ألف حرف.',
            'skills.max' => 'المهارات يجب ألا تتجاوز 5 آلاف حرف.',
            'photo.image' => 'الملف المرفوع يجب أن يكون صورة.',
            'photo.max' => 'حجم الصورة يتجاوز الحد المسموح.',
        ]);

        if ($request->hasFile('photo')) {
            try {
                UserProfileImageStorage::delete($personal_branding->photo_path);
                $data['photo_path'] = UserProfileImageStorage::storeInDirectory(
                    $request->file('photo'),
                    'instructor-profiles'
                );
                if ($personal_branding->user_id) {
                    UserProfileImageStorage::syncInstructorDisplayPhoto(
                        (int) $personal_branding->user_id,
                        $data['photo_path']
                    );
                }
            } catch (\Throwable $e) {
                report($e);

                return back()
                    ->withErrors(['photo' => 'تعذّر رفع الصورة (Cloudflare/التخزين). أعد المحاولة.'])
                    ->withInput();
            }
        }

        unset($data['photo']);
        $data['social_links'] = $personal_branding->social_links ?? [];

        $personal_branding->update($data);

        return redirect()
            ->route('admin.personal-branding.show', $personal_branding)
            ->with('success', 'تم تحديث الملف التعريفي للمدرب.');
    }

    public function destroy(InstructorProfile $personal_branding)
    {
        $userName = $personal_branding->user?->name ?? 'المدرب';

        UserProfileImageStorage::delete($personal_branding->photo_path);

        $personal_branding->delete();

        return redirect()
            ->route('admin.personal-branding.index')
            ->with('success', 'تم حذف الملف التعريفي لـ '.$userName.'. يمكن للمدرب إنشاء ملف جديد من لوحته.');
    }

    /**
     * إظهار الملف على الصفحة الرئيسية / قائمة المعلمين.
     * لا يغيّر حالة قبول الطلب (status) ولا تفعيل الحساب.
     */
    /**
     * إظهار الملف على الصفحة الرئيسية / قائمة المعلمين.
     * لا يغيّر حالة قبول الطلب (status) ولا تفعيل الحساب.
     */
    public function approve(InstructorProfile $personal_branding)
    {
        $personal_branding->update([
            'show_on_homepage' => true,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إظهار ملف المدرب على الصفحة الرئيسية. قبول الحساب لم يتأثر.');
    }

    /**
     * إخفاء الملف من الصفحة الرئيسية فقط — بدون رفض/إلغاء قبول المعلم.
     */
    public function reject(Request $request, InstructorProfile $personal_branding)
    {
        $personal_branding->update([
            'show_on_homepage' => false,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إخفاء الملف من الصفحة الرئيسية. حالة قبول المعلم لم تتغير.');
    }

    /**
     * إخفاء من الرئيسية (كان يُسمّى «إعادة للمراجعة» ويُلغي القبول بالخطأ).
     */
    public function sendBackForReview(InstructorProfile $personal_branding)
    {
        $personal_branding->update([
            'show_on_homepage' => false,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إخفاء الملف من الصفحة الرئيسية دون التأثير على قبول المعلم.');
    }
}

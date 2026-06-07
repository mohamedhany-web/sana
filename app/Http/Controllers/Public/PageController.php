<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Models\CourseReview;
use App\Models\FAQ;
use App\Models\InstructorProfile;
use App\Models\SiteTestimonial;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\StudentSubscriptionPlansService;
use App\Support\PlatformFaqDefaults;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Home page is handled by welcome.blade.php route

    public function about()
    {
        $reviewsQuery = CourseReview::query()->where('is_approved', true);

        $stats = [
            'courses' => AdvancedCourse::query()->where('is_active', true)->count(),
            'students' => User::query()->where('role', 'student')->where('is_active', true)->count(),
            'instructors' => InstructorProfile::approved()->count(),
            'certificates' => Certificate::query()
                ->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })
                ->count(),
            'completed' => StudentCourseEnrollment::query()->where('status', 'completed')->count(),
            'reviews_count' => (int) $reviewsQuery->count(),
            'avg_rating' => round((float) ($reviewsQuery->avg('rating') ?: 4.9), 1),
            'satisfaction' => 98,
        ];

        $instructors = \App\Services\InstructorMarketingRankingService::rankApprovedProfiles()->take(6);

        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.about', compact('stats', 'instructors', 'testimonials'));
    }

    public function faq()
    {
        $faqs = FAQ::active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $categories = FAQ::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        // عند وجود أسئلة في لوحة التحكم نعرضها فقط؛ الأسئلة الافتراضية تظهر عند عدم وجود أي سجل نشط
        $defaultFaqs = FAQ::active()->doesntExist()
            ? PlatformFaqDefaults::items()
            : [];

        return view('public.faq', compact('faqs', 'categories', 'defaultFaqs'));
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function pricing()
    {
        $plans = StudentSubscriptionPlansService::getPlans();
        $planKeys = StudentSubscriptionPlansService::planKeys();

        $reviewsQuery = CourseReview::query()->where('is_approved', true);
        $reviewsCount = (int) $reviewsQuery->count();
        $avgRating = round((float) ($reviewsQuery->avg('rating') ?: 4.9), 1);

        $stats = [
            'students' => User::query()->where('role', 'student')->where('is_active', true)->count(),
            'courses' => AdvancedCourse::query()->where('is_active', true)->count(),
            'instructors' => InstructorProfile::approved()->count(),
            'certificates' => Certificate::query()
                ->where(function ($q) {
                    $q->where('status', 'issued')->orWhere('is_verified', true);
                })
                ->count(),
            'completed' => StudentCourseEnrollment::query()->where('status', 'completed')->count(),
            'reviews_count' => $reviewsCount,
            'avg_rating' => $avgRating > 0 ? $avgRating : 4.9,
            'satisfaction' => 98,
        ];

        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.pricing', compact('plans', 'planKeys', 'stats', 'testimonials'));
    }

    public function team()
    {
        return view('public.team');
    }

    public function certificates()
    {
        return view('public.certificates');
    }

    public function help()
    {
        return view('public.help');
    }

    public function refund()
    {
        return view('public.refund');
    }

    public function testimonials()
    {
        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.testimonials', compact('testimonials'));
    }

    public function events()
    {
        return view('public.events');
    }

    public function partners()
    {
        return view('public.partners');
    }
}

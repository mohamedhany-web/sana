<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\SiteTestimonial;
use App\Services\StudentSubscriptionPlansService;
use App\Support\PlatformFaqDefaults;
use App\Support\PublicInstructorCatalog;
use App\Support\PublicLegalInfo;
use App\Support\PublicTrustMetrics;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Home page is handled by welcome.blade.php route

    public function about()
    {
        $stats = PublicTrustMetrics::payload();

        $instructors = PublicInstructorCatalog::rankForPublic()->take(6);

        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->limit(3)
            ->get();

        return view('public.about', compact('stats', 'instructors', 'testimonials'));
    }

    public function howItWorks()
    {
        return view('public.how-it-works');
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

        $defaultCategories = FAQ::active()->doesntExist()
            ? PlatformFaqDefaults::categories()
            : collect();

        return view('public.faq', compact('faqs', 'categories', 'defaultFaqs', 'defaultCategories'));
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy', [
            'legal' => PublicLegalInfo::payload(),
        ]);
    }

    public function teacherPolicy()
    {
        return view('public.teacher-policy');
    }

    public function pricing()
    {
        $plans = StudentSubscriptionPlansService::getPlans();
        $planKeys = StudentSubscriptionPlansService::planKeys();
        $stats = PublicTrustMetrics::payload();

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

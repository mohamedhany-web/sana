<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\PopupAd;
use App\Models\CourseCategory;
use App\Models\SiteTestimonial;
use App\Support\PublicCourseCatalog;
use App\Support\PublicInstructorCatalog;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * الصفحة الرئيسية (Landing).
 * اللغة تُحدد عبر Middleware SetLandingLocale من ?lang= أو الجلسة.
 */
class LandingController extends Controller
{
    public function index(): View
    {
        $popupAd = null;
        $ad = PopupAd::activeNow()->first();
        if ($ad) {
            $key = 'popup_ad_' . $ad->id . '_views';
            $views = (int) session($key, 0);
            if ($views < $ad->max_views_per_visitor) {
                session([$key => $views + 1]);
                $popupAd = $ad;
            }
        }

        // نفس مسارات صفحة المسارات التعليمية بكل بياناتها (سعر المسار المستقل، عدد الكورسات، الصورة، إلخ)
        $landingPaths = $this->getPublicLearningPaths(12);

        $teacherPlans = \App\Services\InstructorSubscriptionPlansService::getPlans();

        $featuredCourses = PublicCourseCatalog::publiclyListableQuery()
            ->with(['instructor:id,name', 'courseCategory:id,name'])
            ->withCount('lessons')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->filter(fn (AdvancedCourse $course) => ! PublicCourseCatalog::isPlaceholderTitle($course->title))
            ->values();

        $hasPublishedCourses = $featuredCourses->isNotEmpty();

        $homeCategories = $this->buildHomeCategories();
        $homeSubjects = $this->buildHomeSubjects();
        $courseCategories = $this->buildCourseCategories();
        $spotlightCourse = $featuredCourses->first();

        $homeInstructors = PublicInstructorCatalog::rankForPublic();

        $homeTestimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->limit(24)
            ->get();

        $savedCourseIds = PublicCourseCatalog::savedCourseIdsFor(auth()->user());

        return view('welcome', compact(
            'popupAd',
            'landingPaths',
            'teacherPlans',
            'featuredCourses',
            'spotlightCourse',
            'homeCategories',
            'homeSubjects',
            'courseCategories',
            'homeInstructors',
            'homeTestimonials',
            'hasPublishedCourses',
            'savedCourseIds'
        ));
    }

    /**
     * تصنيفات كورسات نشطة مع عدد الكورسات الحقيقي.
     *
     * @return \Illuminate\Support\Collection<int, array{name: string, count: int, url: string}>
     */
    private function buildCourseCategories(): \Illuminate\Support\Collection
    {
        $categories = CourseCategory::query()
            ->active()
            ->ordered()
            ->withCount(['advancedCourses as courses_count' => function ($q) {
                PublicCourseCatalog::applyListableConstraints($q);
            }])
            ->get()
            ->filter(fn ($cat) => (int) $cat->courses_count > 0)
            ->take(8);

        if ($categories->isEmpty()) {
            return collect();
        }

        return $categories->map(fn ($cat) => [
            'id' => $cat->id,
            'name' => $cat->name,
            'count' => (int) $cat->courses_count,
            'url' => route('public.courses', ['category' => $cat->id]),
        ])->values();
    }

    /**
     * مواد دراسية نشطة للصفحة الرئيسية — مرتبطة بصفحة الكورسات والمعلّمين.
     *
     * @return \Illuminate\Support\Collection<int, array{id:int,name:string,count:int,url:string,icon:string,color:string,bg:string,tutors_url:string}>
     */
    private function buildHomeSubjects(): \Illuminate\Support\Collection
    {
        $subjects = AcademicSubject::query()
            ->where('is_active', true)
            ->with(['academicYear:id,name'])
            ->withCount(['advancedCourses as courses_count' => function ($q) {
                PublicCourseCatalog::applyListableConstraints($q);
            }])
            ->orderBy('order')
            ->orderBy('name')
            ->limit(12)
            ->get();

        if ($subjects->isEmpty()) {
            return collect();
        }

        return $subjects
            ->filter(fn (AcademicSubject $subject) => (int) $subject->courses_count > 0)->map(function (AcademicSubject $subject) {
            $color = $subject->color ?: '#1D4EDB';

            return [
                'id' => (int) $subject->id,
                'name' => $subject->name,
                'year' => $subject->academicYear?->name,
                'count' => (int) $subject->courses_count,
                'url' => route('public.courses', ['subject' => $subject->id]),
                'tutors_url' => route('public.instructors.index', ['tutors' => 1, 'subject_id' => $subject->id]),
                'icon' => $this->normalizeFaIcon($subject->icon),
                'color' => $color,
                'bg' => $this->hexToRgba($color, 0.12),
            ];
        })->values();
    }

    private function normalizeFaIcon(?string $icon): string
    {
        if (! is_string($icon) || $icon === '') {
            return 'fa-book';
        }
        if (preg_match('/\bfa-[a-z0-9-]+\b/i', $icon, $matches)) {
            return strtolower($matches[0]);
        }

        return 'fa-book';
    }

    private function hexToRgba(string $hex, float $alpha = 0.12): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return 'rgba(29, 78, 219, '.$alpha.')';
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }

    private function buildHomeCategories(): \Illuminate\Support\Collection
    {
        $servicesUrl = route('public.services.index');

        return collect(range(1, 6))->map(function (int $i) use ($servicesUrl) {
            $iconKey = 'public.home_category_fallback_'.$i.'_icon';
            $iconRaw = __($iconKey);
            $icon = is_string($iconRaw) && preg_match('/\bfa-[a-z0-9-]+\b/i', $iconRaw, $m)
                ? strtolower($m[0])
                : 'fa-circle';

            return [
                'name' => __('public.home_category_fallback_'.$i.'_name'),
                'description' => __('public.home_category_fallback_'.$i.'_desc'),
                'icon' => $icon,
                'url' => $servicesUrl,
            ];
        });
    }

    /**
     * جلب المسارات التعليمية بنفس منطق صفحة المسارات (للاستخدام في الصفحة الرئيسية أو أي عرض عام).
     * @param int|null $limit عدد المسارات (null = بدون حد)
     */
    public static function getPublicLearningPaths(?int $limit = null): \Illuminate\Support\Collection
    {
        $query = AcademicYear::where('is_active', true)
            ->with(['linkedCourses' => function ($q) {
                $q->where('is_active', true);
            }, 'academicSubjects' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['linkedCourses', 'academicSubjects'])
            ->orderBy('order');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $academicYears = $query->get();

        return $academicYears->map(function ($year) {
            $linkedIds = ($year->linkedCourses ?? collect())->pluck('id')->filter()->values()->all();

            $linkedCourses = $linkedIds === []
                ? collect()
                : PublicCourseCatalog::publiclyListableQuery()->whereIn('id', $linkedIds)->get();

            $subjectCourses = collect();
            if ($year->academicSubjects && $year->academicSubjects->isNotEmpty()) {
                $subjectIds = $year->academicSubjects->pluck('id')->toArray();
                if (! empty($subjectIds)) {
                    $subjectCourses = PublicCourseCatalog::publiclyListableQuery()
                        ->whereIn('academic_subject_id', $subjectIds)
                        ->get();
                }
            }

            $courses = $linkedCourses->merge($subjectCourses)->unique('id');
            if ($courses->isEmpty()) {
                return null;
            }

            $slug = Str::slug($year->name);
            $thumb = $year->thumbnail ? str_replace('\\', '/', $year->thumbnail) : null;
            $imageUrl = $thumb ? public_storage_url($thumb) : null;

            return (object) [
                'id' => $year->id,
                'name' => $year->name,
                'description' => $year->description,
                'slug' => $slug,
                'price' => (float) ($year->price ?? 0),
                'courses_count' => $courses->count(),
                'thumbnail' => $year->thumbnail,
                'image_url' => $imageUrl,
                'icon' => $year->icon,
                'color' => $year->color,
                'code' => $year->code,
            ];
        })->filter()->values();
    }
}

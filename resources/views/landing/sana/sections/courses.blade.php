@if(($featuredCourses ?? collect())->isNotEmpty())
<section class="sana-section sana-section--white" id="courses">
    <div class="sana-container">
        <div class="sana-head-row sana-reveal">
            <div class="sana-head">
                <h2 class="sana-head__title">أحدث <span class="hl">الدورات</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <a href="{{ route('public.courses') }}" class="sana-link-more">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="sana-courses-m">
            @foreach(($featuredCourses ?? collect())->take(3) as $course)
                @include('landing.sana.partials.course-card', ['course' => $course, 'featured' => (bool) ($course->is_featured ?? false)])
            @endforeach
        </div>
    </div>
</section>
@endif

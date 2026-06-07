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
            @forelse(($featuredCourses ?? collect())->take(3) as $course)
                @include('landing.sana.partials.course-card', ['course' => $course, 'featured' => (bool) ($course->is_featured ?? false)])
            @empty
            @foreach([
                ['title' => 'الذكاء الاصطناعي للأطفال', 'price' => 129, 'img' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600&q=80', 'level_label' => 'مبتدئ', 'rating' => 4.9, 'students_count' => 240, 'duration_hours' => 12, 'lectures_count' => 18],
                ['title' => 'اللغة الإنجليزية — مستوى 1', 'price' => 99, 'img' => 'https://images.unsplash.com/photo-1546410531-bb4ca050e403?w=600&q=80', 'level_label' => 'مبتدئ', 'rating' => 4.8, 'students_count' => 180, 'duration_hours' => 8, 'lectures_count' => 14],
                ['title' => 'علوم ممتعة للصغار', 'price' => 149, 'img' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600&q=80', 'level_label' => 'مبتدئ', 'rating' => 4.9, 'students_count' => 320, 'duration_hours' => 10, 'lectures_count' => 16],
            ] as $d)
                @include('landing.sana.partials.course-card', ['course' => array_merge($d, [
                    'id' => 0,
                    'card_image_url' => $d['img'],
                    'description' => 'دورة تعليمية تفاعلية مصمّمة لتجربة تعلّم ممتعة وفعّالة.',
                    'is_free' => false,
                    'sale_price' => $d['price'],
                    'course_category' => ['name' => 'علوم'],
                    'instructor' => ['name' => 'معلّم سنا'],
                ])])
            @endforeach
            @endforelse
        </div>
    </div>
</section>

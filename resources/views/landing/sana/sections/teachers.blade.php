@php
    $homeInstructorsList = ($homeInstructors ?? collect())->take(5);
@endphp
@if($homeInstructorsList->isNotEmpty())
<section class="sana-section" id="instructors">
    <div class="sana-container">
        <div class="sana-head-row sana-reveal">
            <div class="sana-head">
                <h2 class="sana-head__title">تعرّف على <span class="hl">معلّمينا</span></h2>
                <span class="sana-head__line"></span>
            </div>
            <a href="{{ route('public.instructors.index') }}" class="sana-link-more">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="sana-teachers-m">
            @foreach($homeInstructorsList as $p)
            @php
                $name = $p->user->name ?? 'معلّم';
                $headline = $p->headline ?? 'معلّم على المنصة';
                $photo = $p->photo_path ? $p->photo_url : null;
                $subjects = array_slice($p->public_subject_labels ?? $p->skills_list ?? [], 0, 2);
            @endphp
            <a href="{{ route('public.instructors.show', $p->user) }}" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
                <div class="sana-teacher-m__ring">
                    @if($photo)
                        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy">
                    @else
                        <span class="av">{{ mb_substr($name, 0, 1) }}</span>
                    @endif
                </div>
                <h3>{{ $name }}</h3>
                <p class="role">{{ Str::limit($headline, 28) }}</p>
                @if(count($subjects) > 0)
                <p class="sana-teacher-m__tags">{{ implode(' · ', $subjects) }}</p>
                @endif
                @if(!empty($p->is_bookable))
                <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> {{ __('public.instructor_book_with') }}</span>
                @elseif(($p->courses_count ?? 0) > 0)
                <span class="sana-teacher-m__book"><i class="fas fa-book-open"></i> {{ (int) $p->courses_count }} دورة</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

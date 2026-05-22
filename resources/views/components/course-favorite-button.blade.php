@props([
    'courseId',
    'saved' => false,
])
<button
    type="button"
    class="edu-course-fav-btn {{ $saved ? 'is-saved' : '' }}"
    data-course-fav
    data-course-id="{{ (int) $courseId }}"
    data-saved="{{ $saved ? '1' : '0' }}"
    aria-pressed="{{ $saved ? 'true' : 'false' }}"
    aria-label="{{ $saved ? __('public.course_unsave') : __('public.course_save') }}"
    title="{{ $saved ? __('public.course_unsave') : __('public.course_save') }}"
    onclick="SanaCourseFavorites.handleClick(event)"
>
    <i class="fa-heart {{ $saved ? 'fas' : 'far' }}"></i>
</button>

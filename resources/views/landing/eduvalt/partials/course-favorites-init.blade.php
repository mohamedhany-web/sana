<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('js/course-favorites.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.SanaCourseFavorites) return;
    window.SanaCourseFavorites.init({
        authenticated: @json(auth()->check()),
        loginUrl: @json(route('login')),
        toggleUrlTemplate: @json(url('/saved-courses/__ID__/toggle')),
        syncUrl: @json(route('public.saved-courses.sync')),
        savedIds: @json($savedCourseIds ?? []),
    });
});
</script>

<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<script src="<?php echo e(asset('js/course-favorites.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.SanaCourseFavorites) return;
    window.SanaCourseFavorites.init({
        authenticated: <?php echo json_encode(auth()->check(), 15, 512) ?>,
        loginUrl: <?php echo json_encode(route('login'), 15, 512) ?>,
        toggleUrlTemplate: <?php echo json_encode(url('/saved-courses/__ID__/toggle'), 15, 512) ?>,
        syncUrl: <?php echo json_encode(route('public.saved-courses.sync'), 15, 512) ?>,
        savedIds: <?php echo json_encode($savedCourseIds ?? [], 15, 512) ?>,
    });
});
</script>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\landing\eduvalt\partials\course-favorites-init.blade.php ENDPATH**/ ?>
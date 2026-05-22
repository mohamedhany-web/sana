<?php
    $brand = config('app.name', 'Sana');
    $locale = 'ar';
    $isRtl = true;
    $thumbUrl = ($course->thumbnail ?? null) ? asset('storage/' . str_replace('\\', '/', $course->thumbnail)) : null;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    if (!$introEmbedUrl && $introVideoUrl !== '') {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        } elseif (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $introVideoUrl, $m)) {
            $introEmbedUrl = 'https://player.vimeo.com/video/' . $m[1];
        }
    }
    $introDirectVideo = null;
    if (!$introEmbedUrl && $introVideoUrl !== '' && filter_var($introVideoUrl, FILTER_VALIDATE_URL)) {
        if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $introVideoUrl)) {
            $introDirectVideo = $introVideoUrl;
        }
    }
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $courseOgImg = $thumbUrl ?? asset('images/og-image.jpg');
    $courseDesc = Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')) . ' | ' . $brand;
    $courseUrl = url('/course/' . ($course->id ?? ''));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($courseTitle); ?></title>
    <meta name="title" content="<?php echo e($courseTitle); ?>">
    <meta name="description" content="<?php echo e($courseDesc); ?>">
    <meta name="keywords" content="<?php echo e($course->title ?? 'كورس'); ?>, تعلم أونلاين, كورسات عربية, <?php echo e($brand); ?>, <?php echo e($categoryDisplay); ?>">
    <meta name="author" content="<?php echo e(($course->instructor->name ?? null) ?? $brand); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <link rel="canonical" href="<?php echo e($courseUrl); ?>">
    <link rel="alternate" hreflang="ar" href="<?php echo e($courseUrl); ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo e($courseUrl); ?>">
    <meta property="og:title" content="<?php echo e($courseTitle); ?>">
    <meta property="og:description" content="<?php echo e($courseDesc); ?>">
    <meta property="og:image" content="<?php echo e($courseOgImg); ?>">
    <meta property="og:image:alt" content="<?php echo e($course->title ?? 'كورس'); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="<?php echo e($brand); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo e($courseUrl); ?>">
    <meta name="twitter:title" content="<?php echo e($courseTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($courseDesc); ?>">
    <meta name="twitter:image" content="<?php echo e($courseOgImg); ?>">
    <meta name="twitter:image:alt" content="<?php echo e($course->title ?? 'كورس'); ?>">
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.course-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php
        $savedCourseIds = \App\Support\PublicCourseCatalog::savedCourseIdsFor(auth()->user());
    ?>
    <?php echo $__env->make('landing.eduvalt.partials.course-favorites-init', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main>
    <?php $__currentLoopData = ['success' => 'emerald', 'info' => 'blue', 'error' => 'red']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($type)): ?>
        <div class="edu-container-full pt-28 pb-2" x-data="{ s: true }" x-show="s" <?php if($type !== 'error'): ?> x-init="setTimeout(() => s = false, 6000)" <?php endif; ?>>
            <div class="edu-courses-inner">
                <div class="rounded-2xl border border-<?php echo e($color); ?>-200 bg-<?php echo e($color); ?>-50 px-5 py-4 flex items-center gap-3">
                    <i class="fas fa-<?php echo e($type === 'success' ? 'check-circle' : ($type === 'info' ? 'info-circle' : 'exclamation-circle')); ?> text-<?php echo e($color); ?>-600"></i>
                    <p class="text-<?php echo e($color); ?>-800 font-semibold flex-1"><?php echo e(session($type)); ?></p>
                    <button type="button" @click="s = false" class="text-<?php echo e($color); ?>-600"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <section class="edu-course-hero pt-28 sm:pt-32 pb-10 sm:pb-14">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <nav class="reveal text-sm text-slate-500 mb-6 flex items-center gap-2 flex-wrap">
                    <a href="<?php echo e(url('/')); ?>" class="hover:text-[var(--edu-primary)] transition-colors"><?php echo e(__('public.home')); ?></a>
                    <i class="fas fa-chevron-left text-[8px] text-slate-400"></i>
                    <a href="<?php echo e(route('public.courses')); ?>" class="hover:text-[var(--edu-primary)] transition-colors"><?php echo e(__('public.courses')); ?></a>
                    <i class="fas fa-chevron-left text-[8px] text-slate-400"></i>
                    <span class="text-[var(--edu-primary)] font-semibold"><?php echo e(Str::limit($course->title ?? '', 48)); ?></span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-10 items-start">
                    <div class="lg:col-span-3 reveal">
                        <?php if($course->is_featured ?? false): ?>
                            <span class="edu-badge mb-4" style="background:var(--edu-accent-light);color:var(--edu-accent-dark)">
                                <i class="fas fa-star"></i>
                                <?php echo e(__('public.featured_course_badge')); ?>

                            </span>
                        <?php endif; ?>

                        <h1 class="text-3xl sm:text-4xl lg:text-[2.35rem] font-bold text-slate-900 leading-tight mb-4">
                            <?php echo e($course->title ?? __('public.course_title_fallback')); ?>

                        </h1>

                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed mb-6 max-w-2xl line-clamp-3">
                            <?php echo e($course->description ?? __('public.course_desc_fallback')); ?>

                        </p>

                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="edu-course-stat"><i class="fas fa-chalkboard-teacher text-[var(--edu-primary)]"></i> <?php echo e(($course->lessons_count ?? 0)); ?> <?php echo e(__('public.lecture_single')); ?></span>
                            <span class="edu-course-stat"><i class="fas fa-clock text-[var(--edu-primary)]"></i> <?php echo e(($course->duration_hours ?? 0)); ?> <?php echo e(__('public.hours')); ?></span>
                            <span class="edu-course-stat"><i class="fas fa-folder-open text-[var(--edu-primary)]"></i> <?php echo e($categoryDisplay); ?></span>
                        </div>

                        <?php if($course->instructor): ?>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-[var(--edu-primary)]"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500"><?php echo e(__('public.instructor_label')); ?></p>
                                    <?php if(\App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists()): ?>
                                        <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>" class="font-bold text-slate-900 hover:text-[var(--edu-primary)] transition-colors"><?php echo e($course->instructor->name); ?></a>
                                    <?php else: ?>
                                        <span class="font-bold text-slate-900"><?php echo e($course->instructor->name); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-3">
                            <?php echo $__env->make('landing.eduvalt.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled ?? false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-outline">
                                <i class="fas fa-arrow-right text-sm"></i>
                                <?php echo e(__('public.all_courses')); ?>

                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-2 reveal">
                        <?php if($introEmbedUrl): ?>
                            <div class="edu-course-media">
                                <div class="aspect-video w-full bg-slate-900">
                                    <iframe src="<?php echo e($introEmbedUrl); ?>" title="<?php echo e(__('public.course_intro_video')); ?>"
                                        class="w-full h-full min-h-[220px]"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                        allowfullscreen loading="lazy"></iframe>
                                </div>
                            </div>
                        <?php elseif($introDirectVideo): ?>
                            <div class="edu-course-media">
                                <div class="aspect-video w-full bg-black">
                                    <video src="<?php echo e($introDirectVideo); ?>" controls playsinline preload="metadata" class="w-full h-full object-contain">
                                        <?php echo e(__('public.course_intro_video_unsupported')); ?>

                                    </video>
                                </div>
                            </div>
                        <?php elseif($thumbUrl): ?>
                            <div class="edu-course-media aspect-video relative">
                                <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-full object-cover">
                                <?php if (isset($component)) { $__componentOriginal1bcdbb4473df18087eeee20994007123 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bcdbb4473df18087eeee20994007123 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course-favorite-button','data' => ['courseId' => $course->id,'saved' => in_array((int) $course->id, $savedCourseIds, true)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course-favorite-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->id),'saved' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array((int) $course->id, $savedCourseIds, true))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $attributes = $__attributesOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__attributesOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $component = $__componentOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__componentOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-12 md:py-16 bg-white">
        <div class="edu-container-full">
            <div class="edu-courses-inner">
                <div class="reveal grid grid-cols-2 sm:grid-cols-4 gap-4 mb-12">
                    <?php
                    $infoCards = [
                        ['icon' => 'fa-clock', 'label' => __('public.duration'), 'value' => ($course->duration_hours ?? 0) . ' ' . __('public.hours')],
                        ['icon' => 'fa-chalkboard-teacher', 'label' => __('public.lectures_count_label'), 'value' => ($course->lessons_count ?? 0) . ' ' . __('public.lecture_single')],
                        ['icon' => 'fa-folder-open', 'label' => __('public.course_category_label'), 'value' => $categoryDisplay],
                        ['icon' => 'fa-book', 'label' => __('public.subject_label'), 'value' => $course->academicSubject->name ?? __('public.course_category_not_set')],
                    ];
                    ?>
                    <?php $__currentLoopData = $infoCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="edu-card text-center !p-5">
                            <div class="w-11 h-11 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center mx-auto mb-2">
                                <i class="fas <?php echo e($ic['icon']); ?> text-[var(--edu-primary)]"></i>
                            </div>
                            <p class="text-xl font-bold text-slate-900 mb-0.5"><?php echo e($ic['value']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo e($ic['label']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="reveal edu-card !p-6 sm:!p-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-[var(--edu-primary-light)] flex items-center justify-center">
                                    <i class="fas fa-info-circle text-[var(--edu-primary)]"></i>
                                </div>
                                <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.about_course')); ?></h2>
                            </div>
                            <div class="text-slate-600 leading-relaxed text-base">
                                <p><?php echo e($course->description ?? __('public.course_desc_fallback')); ?></p>
                                <?php if($course->objectives): ?>
                                    <div class="mt-6">
                                        <h3 class="text-lg font-bold text-slate-900 mb-3"><?php echo e(__('public.course_objectives')); ?></h3>
                                        <div class="rounded-2xl p-5 bg-[var(--edu-primary-light)]/50 border border-[var(--edu-primary)]/15">
                                            <p class="whitespace-pre-line text-slate-700"><?php echo e($course->objectives); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($course->what_you_learn): ?>
                            <div class="reveal stagger-1 edu-card !p-6 sm:!p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-emerald-600"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.what_you_learn')); ?></h2>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <?php $__currentLoopData = array_filter(explode("\n", $course->what_you_learn)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="edu-learn-item">
                                            <i class="fas fa-check-circle text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                            <span><?php echo e(trim($point)); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($course->requirements): ?>
                            <div class="reveal stagger-2 edu-card !p-6 sm:!p-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                                        <i class="fas fa-list-check text-amber-600"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-slate-900"><?php echo e(__('public.requirements')); ?></h2>
                                </div>
                                <div class="rounded-2xl p-5 bg-amber-50/40 border border-amber-100">
                                    <p class="text-slate-700 whitespace-pre-line leading-relaxed"><?php echo e($course->requirements); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="edu-sidebar-sticky space-y-5">
                            <div class="reveal edu-card !p-0 overflow-hidden">
                                <div class="edu-price-card-head">
                                    <?php if($course->usesContactSupportPricing()): ?>
                                        <div class="text-xl font-bold flex items-center justify-center gap-2">
                                            <i class="fab fa-whatsapp text-[#25D366]"></i>
                                            <?php echo e(__('public.course_contact_support_short')); ?>

                                        </div>
                                        <p class="text-sm text-white/80 text-center mt-2"><?php echo e(__('public.course_contact_support_hint')); ?></p>
                                    <?php elseif($course->effectivePurchasePrice() > 0 && !($course->is_free ?? false)): ?>
                                        <?php if($course->hasPromotionalPrice()): ?>
                                            <div class="text-sm text-white/80 line-through mb-1 tabular-nums"><?php echo e(number_format($course->listPriceAmount(), 0)); ?> <?php echo e(__('public.currency')); ?></div>
                                            <div class="text-3xl font-bold tabular-nums"><?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?> <span class="text-lg font-medium text-white/80"><?php echo e(__('public.currency')); ?></span></div>
                                        <?php else: ?>
                                            <div class="text-3xl font-bold tabular-nums"><?php echo e(number_format($course->effectivePurchasePrice(), 0)); ?> <span class="text-lg font-medium text-white/80"><?php echo e(__('public.currency')); ?></span></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-2xl font-bold flex items-center justify-center gap-2">
                                            <i class="fas fa-gift"></i><?php echo e(__('public.free_price')); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-5">
                                    <dl class="space-y-2 mb-5 text-sm">
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-clock text-[var(--edu-primary)] ml-1"></i> <?php echo e(__('public.duration')); ?></span>
                                            <span class="font-bold text-slate-900"><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-chalkboard-teacher text-[var(--edu-primary)] ml-1"></i> <?php echo e(__('public.lectures_count_label')); ?></span>
                                            <span class="font-bold text-slate-900"><?php echo e($course->lessons_count ?? 0); ?></span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                            <span class="text-slate-500"><i class="fas fa-folder-open text-[var(--edu-primary)] ml-1"></i> <?php echo e(__('public.course_category_label')); ?></span>
                                            <span class="font-bold text-slate-900"><?php echo e($categoryDisplay); ?></span>
                                        </div>
                                    </dl>
                                    <?php echo $__env->make('landing.eduvalt.partials.course-enroll-cta', ['course' => $course, 'isEnrolled' => $isEnrolled ?? false, 'block' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                </div>
                            </div>

                            <?php if(isset($relatedCourses) && $relatedCourses->isNotEmpty()): ?>
                                <div class="reveal edu-card !p-5">
                                    <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-bookmark text-[var(--edu-primary)]"></i>
                                        كورسات ذات صلة
                                    </h3>
                                    <div class="space-y-3">
                                        <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $relThumb = $related->thumbnail ? str_replace('\\', '/', $related->thumbnail) : null;
                                                $relImg = $relThumb && \Illuminate\Support\Facades\Storage::disk('public')->exists($relThumb)
                                                    ? asset('storage/' . $relThumb)
                                                    : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=200&h=200&fit=crop';
                                            ?>
                                            <div class="flex gap-3 p-3 rounded-xl border border-slate-100 hover:border-[var(--edu-primary)]/30 hover:shadow-md transition-all group">
                                                <div class="edu-related-thumb relative shrink-0">
                                                    <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="block w-full h-full">
                                                        <img src="<?php echo e($relImg); ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">
                                                    </a>
                                                    <?php if (isset($component)) { $__componentOriginal1bcdbb4473df18087eeee20994007123 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bcdbb4473df18087eeee20994007123 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course-favorite-button','data' => ['courseId' => $related->id,'saved' => in_array((int) $related->id, $savedCourseIds, true)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course-favorite-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($related->id),'saved' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array((int) $related->id, $savedCourseIds, true))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $attributes = $__attributesOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__attributesOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bcdbb4473df18087eeee20994007123)): ?>
<?php $component = $__componentOriginal1bcdbb4473df18087eeee20994007123; ?>
<?php unset($__componentOriginal1bcdbb4473df18087eeee20994007123); ?>
<?php endif; ?>
                                                </div>
                                                <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="flex-1 min-w-0">
                                                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-[var(--edu-primary)] transition-colors line-clamp-2"><?php echo e($related->title); ?></h4>
                                                    <div class="mt-1">
                                                        <?php if (isset($component)) { $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.advanced-course-card-price','data' => ['course' => $related,'size' => 'sm','class' => '!items-start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('advanced-course-card-price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($related),'size' => 'sm','class' => '!items-start']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $attributes = $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $component = $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
                                                    </div>
                                                </a>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="py-12 md:py-16 bg-[var(--edu-bg)]">
        <div class="edu-container-full">
            <div class="edu-courses-inner reveal">
                <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-3">
                        جاهز للانطلاق في هذا الكورس؟
                    </h2>
                    <p class="text-white/85 text-base sm:text-lg mb-8 max-w-xl mx-auto">
                        سجّل الآن وابدأ التعلم بخطوات واضحة وتجربة احترافية متكاملة.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="edu-btn-white">
                                <?php echo e(__('public.start_learning_now')); ?>

                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(route('register')); ?>" class="edu-btn-white">
                                <?php echo e(__('public.register_free_now')); ?>

                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light">
                            <?php echo e(__('public.all_courses')); ?>

                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php echo $__env->make('landing.eduvalt.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
(function () {
    var nav = document.getElementById('edu-nav');
    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        if (nav) nav.classList.toggle('is-scrolled', y > 20);
        var bar = document.getElementById('scroll-progress');
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    window.addEventListener('load', function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    });
    setTimeout(function () {
        document.getElementById('edu-preloader')?.classList.add('is-done');
    }, 2000);
    document.getElementById('edu-mobile-toggle')?.addEventListener('click', function () {
        document.getElementById('edu-mobile-menu')?.classList.toggle('hidden');
    });
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('revealed'); io.unobserve(e.target); }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('revealed'); });
    }
})();
</script>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\course-show.blade.php ENDPATH**/ ?>
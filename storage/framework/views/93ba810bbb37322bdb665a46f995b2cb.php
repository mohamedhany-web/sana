<?php $__env->startSection('title', __('student.dashboard_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php $b = config('brand.colors'); ?>
<style>
    .sd-page {
        --sd-blue: <?php echo e($b['blue']); ?>;
        --sd-purple: <?php echo e($b['purple']); ?>;
        --sd-gold: <?php echo e($b['yellow']); ?>;
        --sd-gradient: linear-gradient(135deg, <?php echo e($b['blue']); ?> 0%, <?php echo e($b['purple']); ?> 100%);
        --sd-gradient-warm: linear-gradient(135deg, <?php echo e($b['purple']); ?> 0%, <?php echo e($b['yellow']); ?> 100%);
    }
    .sd-hero {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 1024px) {
        .sd-hero { grid-template-columns: 1fr 280px; align-items: stretch; }
    }
    .sd-hero-main {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
        background:
            radial-gradient(circle at 100% 0%, rgba(<?php echo e($b['purple_rgb']); ?>, 0.12) 0%, transparent 45%),
            radial-gradient(circle at 0% 100%, rgba(<?php echo e($b['yellow_rgb']); ?>, 0.1) 0%, transparent 40%),
            linear-gradient(135deg, #fff 0%, rgba(<?php echo e($b['blue_rgb']); ?>, 0.04) 100%);
        padding: 1.5rem 1.75rem;
        box-shadow: 0 20px 50px -24px rgba(<?php echo e($b['blue_rgb']); ?>, 0.25);
    }
    .sd-hero-main::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: var(--sd-gradient);
        border-radius: 24px 24px 0 0;
    }
    .sd-motivation {
        border-radius: 24px;
        border: 1px solid rgba(<?php echo e($b['purple_rgb']); ?>, 0.15);
        background: var(--sd-gradient);
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.75rem;
        box-shadow: 0 16px 40px -16px rgba(<?php echo e($b['purple_rgb']); ?>, 0.4);
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .sd-motivation::after {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
        bottom: -30px;
        left: -20px;
    }
    .sd-motivation .fa-rocket { color: var(--sd-gold); }
    .sd-motivation p { color: #fff; position: relative; z-index: 1; }
    .sd-motivation a { color: rgba(255,255,255,.9) !important; position: relative; z-index: 1; }
    .sd-motivation > span {
        background: rgba(255,255,255,.18) !important;
        color: #fff !important;
        position: relative;
        z-index: 1;
    }
    .sd-kpi {
        border-radius: 20px;
        border: 1px solid #e8ecff;
        background: #fff;
        padding: 1.15rem 1.25rem;
        transition: transform .2s, box-shadow .2s, border-color .2s;
        position: relative;
        overflow: hidden;
    }
    .sd-kpi::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 3px;
        background: var(--sd-gradient);
        opacity: 0;
        transition: opacity .2s;
    }
    .sd-kpi:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px -18px rgba(<?php echo e($b['blue_rgb']); ?>, 0.22);
        border-color: rgba(<?php echo e($b['purple_rgb']); ?>, 0.2);
    }
    .sd-kpi:hover::before { opacity: 1; }
    .sd-kpi-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        box-shadow: 0 6px 16px -6px rgba(0,0,0,.15);
    }
    .sd-panel {
        border-radius: 22px;
        border: 1px solid #e8ecff;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 10px 36px -20px rgba(<?php echo e($b['blue_rgb']); ?>, 0.12);
    }
    .sd-panel-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        background: linear-gradient(180deg, rgba(<?php echo e($b['blue_rgb']); ?>, 0.03) 0%, transparent 100%);
    }
    .sd-courses-wrap { position: relative; padding: 0 0 1rem; }
    .sd-courses-track {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding: 0 1.25rem 0.5rem;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }
    .sd-courses-track::-webkit-scrollbar { height: 6px; }
    .sd-course-card {
        flex: 0 0 min(300px, 88vw);
        scroll-snap-align: start;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #fff;
        padding: 1.1rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .sd-course-card:hover {
        border-color: rgba(<?php echo e($b['purple_rgb']); ?>, .3);
        box-shadow: 0 16px 40px -18px rgba(<?php echo e($b['blue_rgb']); ?>, .22);
    }
    .sd-course-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
        margin-bottom: 0.85rem;
    }
    .sd-progress {
        height: 7px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
    }
    .sd-progress > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: var(--sd-gradient);
    }
    .sd-scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: var(--sd-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0,0,0,.08);
        z-index: 2;
    }
    .sd-scroll-btn--prev { right: 0.35rem; }
    .sd-scroll-btn--next { left: 0.35rem; }
    .sd-event {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        transition: background .2s;
    }
    .sd-event:hover { background: #f8fafc; }
    .sd-event-dot {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 0.8rem;
    }
    .sd-activity-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .sd-activity-item:last-child { border-bottom: none; }
    .sd-activity-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 10px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sd-activity-icon--emerald { background: #ecfdf5; color: #059669; }
    .sd-activity-icon--amber { background: #fffbeb; color: #d97706; }
    .sd-activity-icon--violet { background: #ede9fe; color: #7c3aed; }
    .sd-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem 1.1rem;
        border-radius: 13px;
        background: var(--sd-gradient);
        color: #fff;
        font-size: 0.8125rem;
        font-weight: 700;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 6px 20px -8px rgba(<?php echo e($b['purple_rgb']); ?>, 0.45);
        text-decoration: none;
    }
    .sd-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 28px -8px rgba(<?php echo e($b['purple_rgb']); ?>, 0.5);
        color: #fff;
    }
    .sd-ring { width: 88px; height: 88px; flex-shrink: 0; }
    .sd-tag { color: var(--sd-purple); }
    .sd-link { color: var(--sd-blue); font-weight: 700; }
    .sd-link:hover { color: var(--sd-purple); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $progress = min((int) $stats['total_progress'], 100);
    $circumference = 2 * 3.14159 * 36;
    $strokeDashoffset = $circumference - ($progress / 100) * $circumference;
    $brandBlue = config('brand.colors.blue');
    $brandPurple = config('brand.colors.purple');
    $brandGold = config('brand.colors.yellow');
    $courseGradients = [
        ['from' => $brandBlue, 'to' => $brandPurple, 'icon' => 'fa-book-open'],
        ['from' => '#0ea5e9', 'to' => '#06b6d4', 'icon' => 'fa-code'],
        ['from' => $brandGold, 'to' => '#f59e0b', 'icon' => 'fa-palette'],
        ['from' => '#8b5cf6', 'to' => $brandPurple, 'icon' => 'fa-graduation-cap'],
    ];
    $levelLabels = [
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'advanced' => 'متقدم',
    ];
?>

<div class="sd-page space-y-6 pb-8">
    <?php if(isset($activeSubscription) && $activeSubscription): ?>
    <div class="rounded-2xl border border-purple-100 bg-gradient-to-l from-purple-50/80 to-white px-5 py-4 flex flex-wrap items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3 min-w-0">
            <span class="w-10 h-10 rounded-xl text-white flex items-center justify-center sd-btn-primary !p-0 !shadow-md"><i class="fas fa-gem"></i></span>
            <div>
                <p class="font-bold text-slate-800"><?php echo e($activeSubscription->plan_name); ?></p>
                <p class="text-xs text-slate-500">
                    <?php echo e(\App\Models\Subscription::getDurationLabel($activeSubscription->billing_cycle)); ?>

                    · ينتهي <?php echo e($activeSubscription->end_date?->format('Y-m-d') ?? '—'); ?>

                </p>
            </div>
        </div>
        <a href="<?php echo e(route('student.my-subscription')); ?>" class="sd-btn-primary text-sm"><?php echo e(__('student.view_all')); ?></a>
    </div>
    <?php endif; ?>

    
    <div class="sd-hero">
        <div class="sd-hero-main relative z-[1]">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-bold sd-tag mb-2"><?php echo e(__('student.dashboard_overview_today')); ?></p>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 leading-tight">
                        <?php echo e(__('student.welcome_name', ['name' => $user->name])); ?>

                    </h1>
                    <p class="text-slate-600 text-sm mt-2 max-w-lg leading-relaxed"><?php echo e(__('student.dashboard_subtitle')); ?></p>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="sd-ring relative">
                        <svg class="w-full h-full" viewBox="0 0 80 80" style="transform:rotate(-90deg)">
                            <circle cx="40" cy="40" r="36" fill="none" class="stroke-slate-200" stroke-width="6"/>
                            <circle cx="40" cy="40" r="36" fill="none" stroke="url(#sdProg)" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="<?php echo e($circumference); ?>" stroke-dashoffset="<?php echo e($strokeDashoffset); ?>"/>
                            <defs>
                                <linearGradient id="sdProg" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="<?php echo e($brandBlue); ?>"/>
                                    <stop offset="100%" stop-color="<?php echo e($brandPurple); ?>"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center font-black text-lg sd-tag"><?php echo e($progress); ?>%</span>
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-bold text-slate-700"><?php echo e(__('student.dashboard_general_progress')); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e(__('student.from_course_completion')); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="sd-motivation">
            <span class="w-11 h-11 rounded-xl bg-[#FFE5F7] text-[#283593] flex items-center justify-center text-xl">
                <i class="fas fa-rocket"></i>
            </span>
            <p class="font-bold text-slate-800 text-sm leading-relaxed"><?php echo e(__('student.dashboard_motivation')); ?></p>
            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
            <a href="<?php echo e(route('public.courses')); ?>" class="text-xs font-bold text-[#283593] hover:underline"><?php echo e(__('student.explore_courses')); ?> →</a>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <h2 class="text-sm font-bold text-slate-700 mb-3"><?php echo e(__('student.dashboard_progress_summary')); ?></h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb)"><i class="fas fa-clock"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e((int) $stats['total_learning_hours']); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5"><?php echo e(__('student.dashboard_learning_hours')); ?></p>
                <p class="text-[11px] text-slate-500 mt-1"><?php echo e(__('student.dashboard_learning_hours_hint')); ?></p>
            </div>
            <a href="<?php echo e(route('student.certificates.index')); ?>" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="fas fa-check-circle"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($stats['completed_courses']); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5"><?php echo e(__('student.dashboard_completed_this_month')); ?></p>
            </a>
            <a href="<?php echo e(route('student.exams.index')); ?>" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)"><i class="fas fa-clipboard-check"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums">
                    <?php echo e($stats['average_score'] > 0 ? number_format($stats['average_score'], 0).'%' : '—'); ?>

                </p>
                <p class="text-xs font-bold text-slate-600 mt-0.5"><?php echo e(__('student.dashboard_exam_average')); ?></p>
                <?php if($stats['completed_exams'] > 0): ?>
                <p class="text-[11px] text-slate-500 mt-1"><?php echo e(__('student.dashboard_exams_taken', ['count' => $stats['completed_exams']])); ?></p>
                <?php endif; ?>
            </a>
            <?php if(Route::has('student.achievements.index')): ?>
            <a href="<?php echo e(route('student.achievements.index')); ?>" class="sd-kpi block no-underline text-inherit">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,<?php echo e($brandGold); ?>,#ea580c)"><i class="fas fa-medal"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($achievementsCount); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5"><?php echo e(__('student.dashboard_achievements')); ?></p>
                <p class="text-[11px] text-slate-500 mt-1"><?php echo e(__('student.dashboard_achievements_hint')); ?></p>
            </a>
            <?php else: ?>
            <div class="sd-kpi">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <span class="sd-kpi-icon" style="background:linear-gradient(135deg,<?php echo e($brandGold); ?>,#ea580c)"><i class="fas fa-medal"></i></span>
                </div>
                <p class="text-2xl font-black text-slate-800 tabular-nums"><?php echo e($achievementsCount); ?></p>
                <p class="text-xs font-bold text-slate-600 mt-0.5"><?php echo e(__('student.dashboard_achievements')); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="sd-panel">
        <div class="sd-panel-head">
            <h2 class="font-heading font-bold text-slate-800"><?php echo e(__('student.dashboard_current_courses')); ?></h2>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="text-sm sd-link hover:underline">
                <?php echo e(__('student.view_all')); ?> <i class="fas fa-arrow-left text-[10px]"></i>
            </a>
        </div>
        <?php if($activeCourses->isNotEmpty()): ?>
        <div class="sd-courses-wrap">
            <button type="button" class="sd-scroll-btn sd-scroll-btn--prev hidden sm:flex" onclick="sdScrollCourses(1)" aria-label="السابق">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button type="button" class="sd-scroll-btn sd-scroll-btn--next hidden sm:flex" onclick="sdScrollCourses(-1)" aria-label="التالي">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div id="sd-courses-track" class="sd-courses-track" dir="ltr">
                <?php $__currentLoopData = $activeCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $prog = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0);
                        $g = $courseGradients[$index % count($courseGradients)];
                        $lessonsCount = (int) ($course->lessons_count ?? 0);
                        $levelKey = strtolower((string) ($course->level ?? ''));
                        $levelLabel = $levelLabels[$levelKey] ?? ($course->level ?: __('student.not_specified'));
                    ?>
                    <article class="sd-course-card" dir="rtl">
                        <div class="sd-course-icon" style="background:linear-gradient(135deg,<?php echo e($g['from']); ?>,<?php echo e($g['to']); ?>)">
                            <i class="fas <?php echo e($g['icon']); ?>"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm leading-snug mb-2 line-clamp-2 min-h-[2.5rem]"><?php echo e($course->title); ?></h3>
                        <div class="flex flex-wrap gap-2 text-[11px] text-slate-500 mb-3">
                            <?php if($lessonsCount > 0): ?>
                            <span><i class="fas fa-layer-group ms-1 opacity-60"></i><?php echo e(__('student.dashboard_lessons_count', ['count' => $lessonsCount])); ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-signal ms-1 opacity-60"></i><?php echo e($levelLabel); ?></span>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="sd-progress flex-1"><span style="width:<?php echo e($prog); ?>%"></span></div>
                            <span class="text-xs font-bold text-slate-700 tabular-nums"><?php echo e($prog); ?>%</span>
                        </div>
                        <a href="<?php echo e(route('my-courses.show', $course->id)); ?>" class="sd-btn-primary w-full">
                            <i class="fas fa-play text-[10px]"></i>
                            <?php echo e(__('student.dashboard_continue')); ?>

                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-14 px-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book-open text-2xl text-slate-300"></i>
            </div>
            <p class="font-bold text-slate-700"><?php echo e(__('student.no_active_courses')); ?></p>
            <p class="text-sm text-slate-500 mt-1 mb-4"><?php echo e(__('student.start_journey_now')); ?></p>
            <a href="<?php echo e(route('public.courses')); ?>" class="sd-btn-primary"><?php echo e(__('student.explore_courses')); ?></a>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 sd-panel min-w-0">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800"><?php echo e(__('student.dashboard_calendar')); ?></h2>
                <?php if(Route::has('calendar')): ?>
                <a href="<?php echo e(route('calendar')); ?>" class="text-xs sd-link"><?php echo e(__('student.view_all')); ?></a>
                <?php endif; ?>
            </div>
            <div class="p-3 space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $upcomingCalendar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $at = $event['at']; ?>
                    <?php if($event['url']): ?>
                    <a href="<?php echo e($event['url']); ?>" class="sd-event block no-underline text-inherit">
                    <?php else: ?>
                    <div class="sd-event">
                    <?php endif; ?>
                        <span class="sd-event-dot" style="background:<?php echo e($event['color']); ?>"><i class="fas <?php echo e($event['icon']); ?>"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-sm text-slate-800 truncate"><?php echo e($event['title']); ?></p>
                            <?php if($event['subtitle']): ?>
                            <p class="text-xs text-slate-500 truncate"><?php echo e($event['subtitle']); ?></p>
                            <?php endif; ?>
                            <p class="text-[11px] font-semibold sd-tag mt-1">
                                <?php echo e($event['type_label']); ?> · <?php echo e($dashboardService->humanEventTime($at)); ?>

                            </p>
                        </div>
                    <?php if($event['url']): ?>
                    </a>
                    <?php else: ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-sm text-slate-500 py-10"><?php echo e(__('student.dashboard_no_events')); ?></p>
                <?php endif; ?>
            </div>
            <?php if(Route::has('calendar')): ?>
            <div class="px-4 pb-4">
                <a href="<?php echo e(route('calendar')); ?>" class="sd-btn-primary w-full text-center"><?php echo e(__('student.dashboard_view_calendar')); ?></a>
            </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-7 sd-panel min-w-0">
            <div class="sd-panel-head">
                <h2 class="font-heading font-bold text-slate-800"><?php echo e(__('student.dashboard_recent_activity')); ?></h2>
            </div>
            <div class="px-4 py-2">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if(!empty($activity['url'])): ?>
                    <a href="<?php echo e($activity['url']); ?>" class="sd-activity-item block no-underline text-inherit hover:bg-slate-50/80 rounded-xl px-2 -mx-2">
                    <?php else: ?>
                    <div class="sd-activity-item">
                    <?php endif; ?>
                        <span class="sd-activity-icon sd-activity-icon--<?php echo e($activity['tone']); ?>"><i class="fas <?php echo e($activity['icon']); ?> text-sm"></i></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed"><?php echo e($activity['text']); ?></p>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-[11px] text-slate-500"><?php echo e($dashboardService->humanActivityTime($activity['at'])); ?></span>
                                <?php if(!empty($activity['meta'])): ?>
                                <span class="text-[11px] font-bold text-emerald-600"><?php echo e($activity['meta']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php if(!empty($activity['url'])): ?>
                    </a>
                    <?php else: ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-sm text-slate-500 py-12"><?php echo e(__('student.dashboard_no_activity')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function sdScrollCourses(direction) {
    var track = document.getElementById('sd-courses-track');
    if (!track) return;
    track.scrollBy({ left: direction * 320, behavior: 'smooth' });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/dashboard/student.blade.php ENDPATH**/ ?>
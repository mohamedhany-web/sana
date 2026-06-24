<?php $__env->startSection('title', __('student.exams_page_title')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('dashboard.partials.sanua-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $completedExams = $availableExams->filter(function ($exam) {
        return $exam->last_attempt && $exam->last_attempt->status === 'completed';
    });
    $canAttemptCount = $availableExams->where('can_attempt', true)->count();
    $avgScore = $completedExams->where('best_score', '!=', null)->avg('best_score');
?>

<div class="sanua-dash">

    <header class="sanua-page-head">
        <div>
            <h1 class="sanua-page-head__title"><?php echo e(__('student.exams_page_title')); ?></h1>
            <p class="sanua-page-head__sub"><?php echo e(__('student.exams_subtitle')); ?></p>
        </div>
        <div class="sanua-page-head__actions">
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-page-head__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.my_courses_link')); ?>

            </a>
        </div>
    </header>

    <?php if($availableExams->count() > 0): ?>
        <div class="sanua-stats-row">
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--purple" aria-hidden="true">
                    <i class="fas fa-clipboard-check"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($availableExams->count()); ?></strong>
                    <span><?php echo e(__('student.available')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--green" aria-hidden="true">
                    <i class="fas fa-check"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($completedExams->count()); ?></strong>
                    <span><?php echo e(__('student.completed')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--gold" aria-hidden="true">
                    <i class="fas fa-play"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($canAttemptCount); ?></strong>
                    <span><?php echo e(__('student.can_attempt_label')); ?></span>
                </div>
            </div>
            <div class="sanua-stat-pill">
                <span class="sanua-stat-pill__icon sanua-stat-pill__icon--amber" aria-hidden="true">
                    <i class="fas fa-percentage"></i>
                </span>
                <div class="sanua-stat-pill__body">
                    <strong><?php echo e($avgScore ? number_format($avgScore, 1) : 0); ?>%</strong>
                    <span><?php echo e(__('student.avg_results_label')); ?></span>
                </div>
            </div>
        </div>

        <section class="sanua-section">
            <h2 class="sanua-section-title">✅ <?php echo e(__('student.exams_page_title')); ?></h2>
            <div class="sanua-exam-grid">
                <?php $__currentLoopData = $availableExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="sanua-exam-card">
                        <div class="sanua-exam-card__head">
                            <h3 class="sanua-exam-card__title"><?php echo e($exam->title); ?></h3>
                            <?php if($exam->can_attempt): ?>
                                <span class="sanua-badge sanua-badge--available">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo e(__('student.available')); ?>

                                </span>
                            <?php else: ?>
                                <span class="sanua-badge sanua-badge--locked">
                                    <i class="fas fa-times-circle"></i>
                                    <?php echo e(__('student.not_available_now')); ?>

                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="sanua-exam-card__body">
                            <p class="sanua-exam-card__course">
                                <strong><?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?></strong>
                                <?php if($exam->offline_course_id): ?>
                                    <span class="text-amber-600">(<?php echo e(__('student.offline_badge')); ?>)</span>
                                <?php elseif(optional($exam->course)->academicSubject): ?>
                                    · <?php echo e($exam->course->academicSubject->name); ?>

                                <?php endif; ?>
                            </p>

                            <div class="sanua-metric-grid">
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.duration_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e($exam->duration_minutes); ?> <?php echo e(__('student.minutes')); ?></span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.questions_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e($exam->questions_count); ?> <?php echo e(__('student.question_singular')); ?></span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.passing_marks_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e($exam->passing_marks); ?>%</span>
                                </div>
                                <div class="sanua-metric">
                                    <span class="sanua-metric__label"><?php echo e(__('student.attempts_label')); ?></span>
                                    <span class="sanua-metric__value"><?php echo e($exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed); ?></span>
                                </div>
                            </div>

                            <?php if($exam->user_attempts > 0): ?>
                                <div class="sanua-exam-card__attempt">
                                    <span>
                                        <?php echo e(__('student.your_attempts')); ?>:
                                        <strong><?php echo e($exam->user_attempts); ?></strong>
                                        <?php echo e(__('student.of_attempts')); ?>

                                        <?php echo e($exam->attempts_allowed == 0 ? __('student.unlimited_attempts') : $exam->attempts_allowed); ?>

                                    </span>
                                    <?php if($exam->best_score !== null): ?>
                                        <span><?php echo e(__('student.best_score_label')); ?>: <strong><?php echo e(number_format($exam->best_score, 1)); ?>%</strong></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($exam->description): ?>
                                <p class="sanua-exam-card__desc"><?php echo e($exam->description); ?></p>
                            <?php endif; ?>

                            <div class="sanua-exam-card__foot">
                                <div class="sanua-exam-card__dates">
                                    <?php if($exam->start_time): ?>
                                        <?php echo e(__('student.starts_at')); ?>: <?php echo e($exam->start_time->format('Y-m-d H:i')); ?><br>
                                    <?php endif; ?>
                                    <?php if($exam->end_time): ?>
                                        <?php echo e(__('student.ends_at')); ?>: <?php echo e($exam->end_time->format('Y-m-d H:i')); ?>

                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if($exam->can_attempt): ?>
                                        <a href="<?php echo e(route('student.exams.show', $exam)); ?>" class="sanua-btn sanua-btn--purple">
                                            <i class="fas fa-play"></i>
                                            <?php echo e(__('student.start_exam')); ?>

                                        </a>
                                    <?php elseif($exam->user_attempts >= $exam->attempts_allowed && $exam->attempts_allowed > 0): ?>
                                        <span class="sanua-btn sanua-btn--danger-soft">
                                            <i class="fas fa-ban"></i>
                                            <?php echo e(__('student.attempts_exhausted')); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="sanua-btn sanua-btn--muted">
                                            <i class="fas fa-lock"></i>
                                            <?php echo e(__('student.not_available_now')); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if($exam->prevent_tab_switch || $exam->require_camera || $exam->require_microphone): ?>
                                <div class="sanua-exam-card__tags">
                                    <span><i class="fas fa-shield-alt"></i> <?php echo e(__('student.protected_exam')); ?></span>
                                    <?php if($exam->prevent_tab_switch): ?><span><?php echo e(__('student.no_tab_switch')); ?></span><?php endif; ?>
                                    <?php if($exam->require_camera): ?><span><?php echo e(__('student.camera_label')); ?></span><?php endif; ?>
                                    <?php if($exam->require_microphone): ?><span><?php echo e(__('student.microphone_label')); ?></span><?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <?php if($completedExams->count() > 0): ?>
            <section class="sanua-section">
                <div class="sanua-panel">
                    <div class="sanua-panel__head">
                        <h3><?php echo e(__('student.completed_exams_title')); ?></h3>
                    </div>
                    <div class="sanua-panel__body">
                        <?php $__currentLoopData = $completedExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="sanua-result-row">
                                <div>
                                    <p class="sanua-result-row__title"><?php echo e($exam->title); ?></p>
                                    <p class="sanua-result-row__sub">
                                        <?php echo e($exam->offlineCourse->title ?? $exam->course->title ?? '—'); ?>

                                        · <?php echo e($exam->last_attempt->created_at->diffForHumans()); ?>

                                    </p>
                                </div>
                                <div class="flex items-center gap-3 flex-wrap">
                                    <div class="sanua-result-row__score <?php echo e($exam->last_attempt->result_color == 'green' ? 'is-pass' : 'is-fail'); ?>">
                                        <?php echo e(number_format($exam->last_attempt->percentage, 1)); ?>%
                                        <div class="text-[10px] font-bold text-slate-400"><?php echo e($exam->last_attempt->result_status); ?></div>
                                    </div>
                                    <?php if($exam->show_results_immediately): ?>
                                        <a href="<?php echo e(route('student.exams.result', [$exam, $exam->last_attempt])); ?>" class="sanua-result-row__link">
                                            <i class="fas fa-chart-line"></i>
                                            <?php echo e(__('student.view_result')); ?>

                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <div class="sanua-empty">
            <div class="sanua-empty__icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <h3><?php echo e(__('student.no_exams_available')); ?></h3>
            <p><?php echo e(__('student.no_exams_desc')); ?></p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="sanua-empty__btn">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\student\exams\index.blade.php ENDPATH**/ ?>
<div class="flex flex-col h-full">
    
    <div class="ins-sidebar-brand px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-white/80 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors z-10 border border-slate-200/80">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="relative z-10 pe-8 lg:pe-0">
            <?php echo $__env->make('partials.platform-brand', [
                'variant' => 'sidebar',
                'href' => route('dashboard'),
                'subtitle' => __('instructor.instructor_panel'),
                'showTagline' => true,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    
    <?php
        $user = auth()->user();
        $hasCoursesPortal = \App\Support\InstructorPortalAccess::hasCoursesPortal($user);
        $hasTutorPortal = \App\Support\InstructorPortalAccess::hasTutorLessonsPortal($user);
        $homeRoute = \App\Support\InstructorPortalAccess::homeRoute($user);
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
    ?>
    <?php if($hasCoursesPortal): ?>
    <div class="ins-sidebar-stats flex-shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="ins-sidebar-stat group no-underline text-inherit">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-[#FFE5F7] text-[#283593] flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-open text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-[#283593] uppercase tracking-wider"><?php echo e(__('instructor.courses')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums"><?php echo e($myCoursesCount); ?></div>
            </a>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="ins-sidebar-stat group no-underline text-inherit">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider"><?php echo e(__('instructor.students')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 leading-none tabular-nums"><?php echo e($totalStudents); ?></div>
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        <?php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        ?>

        
        <div class="ins-nav-group">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[9px] opacity-50"></i>
                <?php echo e(__('instructor.overview')); ?>

            </span>
        </div>
        <?php if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks')): ?>

            <a href="<?php echo e(route($homeRoute)); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('dashboard') || ($homeRoute === 'instructor.tutor-lessons.hub' && request()->routeIs('instructor.tutor-lessons.hub')) ? 'active' : ''); ?>">
                <span class="ins-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-th-large text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.dashboard')); ?></span>
            </a>

            <?php if($hasCoursesPortal && ($isInstructor || $user->hasPermission('instructor.view.courses'))): ?>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.courses.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-indigo-100 text-indigo-600">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.my_courses')); ?></span>
                <?php if($myCoursesCount > 0): ?>
                    <span class="ins-nav-badge bg-indigo-100 text-indigo-700"><?php echo e($myCoursesCount); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if($hasCoursesPortal): ?>
            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-tools text-[9px] opacity-50"></i>
                    أدوات التدريس
                </span>
            </div>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.lectures')): ?>
            <a href="<?php echo e(route('instructor.lectures.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.lectures.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-violet-100 text-violet-600">
                    <i class="fas fa-chalkboard text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.lectures')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.assignments')): ?>
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.assignments.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-amber-100 text-amber-600">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.assignments')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.exams')): ?>
            <a href="<?php echo e(route('instructor.exams.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.exams.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-rose-100 text-rose-600">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.exams')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor): ?>
            <a href="<?php echo e(route('instructor.question-banks.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-teal-100 text-teal-600">
                    <i class="fas fa-database text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.question_banks')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.attendance')): ?>
            <a href="<?php echo e(route('instructor.attendance.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.attendance.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-cyan-100 text-cyan-600">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.attendance')); ?></span>
            </a>
            <?php endif; ?>

            <?php if(Route::has('instructor.classroom.index') && auth()->user()?->hasSubscriptionFeature('classroom_access')): ?>
            <a href="<?php echo e(route('instructor.classroom.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.classroom.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-blue-100 text-blue-600">
                    <i class="fas fa-chalkboard-teacher text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('platform.classroom')); ?></span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if($hasTutorPortal && Route::has('instructor.tutor-lessons.hub')): ?>
            <a href="<?php echo e(route('instructor.tutor-lessons.hub')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.tutor-lessons.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-violet-100 text-violet-600">
                    <i class="fas fa-user-clock text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('tutor.hub_title')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($hasTutorPortal && Route::has('instructor.calendar')): ?>
            <a href="<?php echo e(route('instructor.calendar')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'active' : ''); ?>">
                <span class="ins-icon bg-teal-100 text-teal-600">
                    <i class="fas fa-calendar-check text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.calendar')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($hasCoursesPortal && Route::has('instructor.live-sessions.index')): ?>
            <a href="<?php echo e(route('instructor.live-sessions.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.live-sessions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-red-100 text-red-600">
                    <i class="fas fa-broadcast-tower text-sm"></i>
                </span>
                <span class="flex-1 truncate">البث المباشر</span>
                <?php $liveCount = \App\Models\LiveSession::where('instructor_id', auth()->id())->where('status', 'live')->count(); ?>
                <?php if($liveCount > 0): ?>
                    <span class="ins-nav-badge bg-red-100 text-red-600">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span><?php echo e($liveCount); ?>

                    </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-briefcase text-[9px] opacity-50"></i>
                    الإدارة
                </span>
            </div>

            <?php if($hasCoursesPortal && ($isInstructor || $user->hasPermission('instructor.view.tasks'))): ?>
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.tasks.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-orange-100 text-orange-600">
                    <i class="fas fa-check-square text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.tasks_from_management')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($hasCoursesPortal && ($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index')): ?>
            <a href="<?php echo e(route('instructor.management-requests.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.management-requests.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-sky-100 text-sky-600">
                    <i class="fas fa-paper-plane text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.submit_requests_to_management')); ?></span>
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('instructor.agreements.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.agreements.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-purple-100 text-purple-600">
                    <i class="fas fa-handshake text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.agreements_system')); ?></span>
            </a>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-coins text-[9px] opacity-50"></i>
                    المالية
                </span>
            </div>

            <a href="<?php echo e(route('instructor.transfer-account.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.transfer-account.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 text-emerald-600">
                    <i class="fas fa-university text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.transfer_account')); ?></span>
            </a>

            <a href="<?php echo e(route('instructor.withdrawals.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.withdrawals.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-green-100 text-green-600">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.withdrawal_requests')); ?></span>
            </a>

            <a href="<?php echo e(route('instructor.personal-branding.edit')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.personal-branding.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-fuchsia-100 text-fuchsia-600">
                    <i class="fas fa-user-tie text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.personal_branding')); ?></span>
            </a>
        <?php endif; ?>

        
        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                الحساب
            </span>
        </div>

        <?php if(auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-red-100 text-red-600">
                    <i class="fas fa-shield-alt text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.admin_panel')); ?></span>
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('instructor.profile')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav <?php echo e(request()->routeIs('instructor.profile*') ? 'active' : ''); ?>">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-user text-sm"></i>
            </span>
            <span class="flex-1 truncate"><?php echo e(__('instructor.profile')); ?></span>
        </a>

        <?php if(auth()->check() && auth()->user()->hasPermission('student.view.settings')): ?>
        <a href="<?php echo e(route('settings')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav <?php echo e(request()->routeIs('settings') ? 'active' : ''); ?>">
            <span class="ins-icon bg-gray-100 text-gray-600">
                <i class="fas fa-cog text-sm"></i>
            </span>
            <span class="flex-1 truncate"><?php echo e(__('instructor.settings')); ?></span>
        </a>
        <?php endif; ?>
    </nav>

    
    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar w-10 h-10 flex-shrink-0 rounded-xl">
                <?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="" class="w-full h-full object-cover rounded-xl">
                <?php else: ?>
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-[10px] text-gray-500 truncate mt-0.5"><?php echo e(__('instructor.instructor_role')); ?></p>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="<?php echo e(__('instructor.logout')); ?>">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/layouts/instructor-sidebar.blade.php ENDPATH**/ ?>
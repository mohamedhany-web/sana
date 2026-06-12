<?php
    $currentUser = auth()->user();
    $audiences = [null, 'student'];

    $navNotificationsQuery = $currentUser
        ? $currentUser->customNotifications()
            ->with('sender')
            ->whereIn('audience', $audiences)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
        : null;

    $navUnreadCount = $navNotificationsQuery
        ? (clone $navNotificationsQuery)->where('is_read', false)->count()
        : 0;

    $navRecentNotifications = $navNotificationsQuery
        ? (clone $navNotificationsQuery)->orderBy('created_at', 'desc')->limit(6)->get()
        : collect();

    $gradeLabel = $currentUser->academicYear?->name ?? ($currentUser->studentLearningProfile?->grade_stage ?? 'طالب');
    $firstName = explode(' ', trim($currentUser->name))[0];
?>

<header class="stu-topbar">
    
    <div class="stu-topbar__brand">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="stu-icon-btn lg:hidden" aria-label="القائمة">
            <i class="fas fa-bars"></i>
        </button>
        <?php echo $__env->make('partials.platform-brand', [
            'variant' => 'header',
            'href' => route('dashboard'),
            'showTagline' => false,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    
    <div class="stu-topbar__search">
        <i class="fas fa-search"></i>
        <input type="search" placeholder="ابحث عن دروس، كورسات، أنشطة..." aria-label="بحث">
    </div>

    
    <div class="stu-topbar__actions">
        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="stu-icon-btn relative" aria-label="إشعارات">
                <i class="fas fa-bell"></i>
                <?php if($navUnreadCount > 0): ?>
                    <span class="stu-notif-dot"></span>
                <?php endif; ?>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute end-0 mt-2 w-72 stu-dd z-50">
                <div class="px-4 py-3 border-b border-violet-50 flex justify-between items-center">
                    <span class="text-sm font-black text-slate-800">الإشعارات</span>
                    <?php if(Route::has('notifications')): ?>
                        <a href="<?php echo e(route('notifications')); ?>" class="text-xs font-bold text-violet-600 no-underline">الكل</a>
                    <?php endif; ?>
                </div>
                <?php $__empty_1 = true; $__currentLoopData = $navRecentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $url = $notification->action_url ?: (Route::has('notifications.show') ? route('notifications.show', $notification) : '#'); ?>
                    <a href="<?php echo e($url); ?>" class="block px-4 py-2.5 text-sm hover:bg-violet-50 no-underline text-slate-700 border-b border-slate-50">
                        <span class="font-bold block truncate"><?php echo e($notification->title); ?></span>
                        <span class="text-[10px] text-slate-400"><?php echo e(optional($notification->created_at)->diffForHumans()); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="p-5 text-center text-sm text-slate-400 m-0">لا إشعارات جديدة</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="stu-user-chip">
                <div class="u-avatar text-xs font-black text-white">
                    <?php if($currentUser->profile_image): ?>
                        <img src="<?php echo e($currentUser->profile_image_url); ?>" alt="">
                    <?php else: ?>
                        <?php echo e(mb_substr($currentUser->name, 0, 1)); ?>

                    <?php endif; ?>
                </div>
                <div class="stu-user-meta hidden md:block">
                    <span class="stu-user-name"><?php echo e($firstName); ?></span>
                    <span class="stu-user-role"><?php echo e($gradeLabel); ?></span>
                </div>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute end-0 mt-2 w-48 stu-dd z-50 p-1.5">
                <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-violet-50 no-underline">
                    <i class="fas fa-user text-violet-400 text-xs w-4"></i> الملف
                </a>
                <a href="<?php echo e(route('settings')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-violet-50 no-underline">
                    <i class="fas fa-gear text-violet-400 text-xs w-4"></i> الإعدادات
                </a>
                <?php if(Route::has('student.my-subscription')): ?>
                <a href="<?php echo e(route('student.my-subscription')); ?>" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-violet-50 no-underline">
                    <i class="fas fa-gem text-violet-400 text-xs w-4"></i> باقتي
                </a>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-1 pt-1 border-t border-slate-100">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold text-red-500 hover:bg-red-50 text-start border-0 bg-transparent cursor-pointer">
                        <i class="fas fa-sign-out-alt text-xs w-4"></i> خروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\student-app-header.blade.php ENDPATH**/ ?>
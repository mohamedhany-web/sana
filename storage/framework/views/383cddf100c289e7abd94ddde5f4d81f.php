<?php
    $isRtl = app()->getLocale() === 'ar';
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
        ? (clone $navNotificationsQuery)->orderBy('created_at', 'desc')->limit(8)->get()
        : collect();

    $coursesSearchUrl = Route::has('public.courses') ? route('public.courses') : url('/courses');
?>

<header class="stu-topbar flex items-center justify-between gap-3 px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="stu-icon-btn lg:hidden flex-shrink-0" aria-label="القائمة">
            <i class="fas fa-bars text-sm"></i>
        </button>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
        <form action="<?php echo e($coursesSearchUrl); ?>" method="get" class="stu-topbar-search hidden sm:flex min-w-0">
            <i class="fas fa-search text-slate-400 text-sm flex-shrink-0"></i>
            <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('common.nav_search_placeholder_long')); ?>" autocomplete="off">
        </form>
        <?php else: ?>
        <div class="hidden sm:block flex-1 max-w-md">
            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate"><?php echo e(__('student.dashboard_title')); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        <div x-data="themeManager()" x-init="init()">
            <button type="button" @click="toggle()" class="stu-icon-btn"
                    :title="dark ? '<?php echo e($isRtl ? 'الوضع النهاري' : 'Light mode'); ?>' : '<?php echo e($isRtl ? 'الوضع الليلي' : 'Dark mode'); ?>'">
                <i class="text-sm" :class="dark ? 'fas fa-sun text-amber-500' : 'fas fa-moon'"></i>
            </button>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="stu-icon-btn relative" aria-label="<?php echo e(__('student.notifications')); ?>">
                <i class="fas fa-bell text-sm"></i>
                <?php if($navUnreadCount > 0): ?>
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-[#FB5607] rounded-full text-[9px] font-bold text-white flex items-center justify-center border-2 border-white dark:border-slate-900">
                        <?php echo e($navUnreadCount > 99 ? '99+' : $navUnreadCount); ?>

                    </span>
                <?php endif; ?>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute left-0 mt-2 w-80 stu-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100"><?php echo e(__('student.notifications')); ?></h3>
                    <?php if(Route::has('notifications')): ?>
                        <a href="<?php echo e(route('notifications')); ?>" class="text-xs font-bold text-[#283593] dark:text-indigo-400 hover:underline">
                            <?php echo e(__('student.view_all')); ?>

                        </a>
                    <?php endif; ?>
                </div>
                <?php if($navRecentNotifications->isNotEmpty()): ?>
                    <div class="max-h-96 overflow-y-auto">
                        <?php $__currentLoopData = $navRecentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $notificationUrl = $notification->action_url ?: (Route::has('notifications.show') ? route('notifications.show', $notification) : '#');
                            ?>
                            <a href="<?php echo e($notificationUrl); ?>" class="block px-4 py-3 border-b border-slate-50 dark:border-slate-700/80 hover:bg-[#FFE5F7]/40 dark:hover:bg-slate-800/80 transition-colors no-underline text-inherit">
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-[#eef2ff] dark:bg-indigo-950/50 text-[#283593] dark:text-indigo-300 flex items-center justify-center flex-shrink-0">
                                        <i class="<?php echo e($notification->type_icon); ?> text-xs"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate"><?php echo e($notification->title); ?></p>
                                            <?php if(!$notification->is_read): ?>
                                                <span class="w-2 h-2 rounded-full bg-[#FB5607] flex-shrink-0"></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 line-clamp-2"><?php echo e($notification->message); ?></p>
                                        <p class="text-[11px] text-slate-400 mt-1"><?php echo e(optional($notification->created_at)->diffForHumans()); ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="p-8 text-center text-slate-400 text-sm">
                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-40"></i>
                        <p><?php echo e($isRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="stu-user-chip">
                <div class="u-avatar flex-shrink-0 text-sm font-bold">
                    <?php if($currentUser->profile_image): ?>
                        <img src="<?php echo e($currentUser->profile_image_url); ?>" alt="" class="w-full h-full object-cover rounded-[10px]">
                    <?php else: ?>
                        <?php echo e(mb_substr($currentUser->name, 0, 1)); ?>

                    <?php endif; ?>
                </div>
                <span class="hidden lg:block text-sm font-bold text-slate-700 dark:text-slate-200 max-w-[110px] truncate"><?php echo e($currentUser->name); ?></span>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden lg:block transition-transform" :class="{ 'rotate-180': open }"></i>
            </button>
            <div x-show="open" @click.away="open = false" x-cloak x-transition
                 class="absolute left-0 mt-2 w-56 stu-dd z-50">
                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate"><?php echo e($currentUser->name); ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5"><?php echo e($currentUser->email ?? '—'); ?></p>
                </div>
                <div class="p-1.5 space-y-0.5">
                    <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 no-underline">
                        <i class="fas fa-user text-slate-400 w-4 text-center text-xs"></i>
                        <?php echo e(__('student.profile')); ?>

                    </a>
                    <a href="<?php echo e(route('settings')); ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 no-underline">
                        <i class="fas fa-cog text-slate-400 w-4 text-center text-xs"></i>
                        <?php echo e(__('student.settings')); ?>

                    </a>
                    <hr class="my-1 border-slate-100 dark:border-slate-700">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 text-right">
                            <i class="fas fa-sign-out-alt text-xs w-4"></i>
                            <?php echo e($isRtl ? 'تسجيل الخروج' : 'Sign out'); ?>

                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\partials\student-app-header.blade.php ENDPATH**/ ?>
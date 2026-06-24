<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Sana')); ?> - <?php echo $__env->yieldContent('title', __('auth.dashboard')); ?></title>

    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50:'#f0f4ff',100:'#dbe4ff',200:'#bac8ff',300:'#91a7ff',400:'#748ffc',500:'#5c7cfa',600:'#4c6ef5',700:'#4263eb',800:'#3b5bdb',900:'#364fc7',950:'#0c1222' },
                        brand: { 50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00' },
                        mx: { navy:'#283593', indigo:'#1F2A7A', orange:'#FB5607', rose:'#FFE5F7', gold:'#FFE569', soft:'#F7F8FF' },
                        surface: { 50:'#fafbfc', 100:'#f4f5f7', 200:'#e8eaed', 300:'#dadce0' }
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $showContentProtection = !empty(trim((string) ($__env->yieldContent('enable-content-protection') ?? '')));
    ?>
    <?php if($showContentProtection): ?>
    <script>
        window.Laravel = { user: { name: '<?php echo e(auth()->check() ? auth()->user()->name : "زائر"); ?>' } };
    </script>
    <script src="<?php echo e(asset('js/platform-protection.js')); ?>"></script>
    <?php endif; ?>

    <?php echo $__env->make('partials.force-light-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        * { font-family: 'Cairo', 'IBM Plex Sans Arabic', 'Tajawal', system-ui, -apple-system, sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif; }
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        html.light { color-scheme: light; }
        body { background: #f7f8ff; overflow-x: hidden; }

        /* ── Sidebar ── */
        .app-sidebar {
            width: 260px;
            background: #fff;
            border-left: 1px solid #e8eaf6;
        }
        @media (max-width: 1023px) {
            .app-sidebar { width: 280px; }
        }

        .app-sidebar::-webkit-scrollbar,
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .app-sidebar::-webkit-scrollbar-thumb,
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* Sidebar nav items */
        .s-nav {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: #4b5563; transition: all .15s;
            border: 1px solid transparent;
        }
        .s-nav:hover { background: #f3f4f6; color: #111827; }
        .s-nav.active { background: #f2f4ff; color: #1F2A7A; border-color: #d6dcff; }

        .s-nav .s-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0; transition: all .15s;
        }

        /* ── Header ── */
        .app-header {
            height: 56px; background: #fff;
            border-bottom: 1px solid #e8eaf6;
            backdrop-filter: blur(10px);
        }

        /* Header buttons */
        .h-btn {
            width: 36px; height: 36px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #6b7280; border: 1px solid #e5e7eb;
            transition: all .15s; background: transparent;
        }
        .h-btn:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }

        /* Search input */
        .search-box {
            background: #f3f4f6; border: 1px solid transparent;
            border-radius: 8px; padding: 7px 12px;
            transition: all .2s;
        }
        .search-box:focus-within { background: #fff; border-color: #283593; box-shadow: 0 0 0 3px rgba(40,53,147,.1); }

        /* Dropdown */
        .dd-menu {
            background: #fff; border: 1px solid #e5e7eb;
            border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .dd-item { display: flex; align-items: center; transition: background .1s; }
        .dd-item:hover { background: #f3f4f6; }

        /* User avatar */
        .u-avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #283593, #FB5607);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 13px;
        }
        .u-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

        /* Logo fix */
        .logo-section img,
        .app-sidebar img[alt*="Logo"] {
            transform: none !important; rotate: 0deg !important;
            object-fit: contain !important; object-position: center !important;
        }

        /* Notification badge */
        .n-badge {
            position: absolute; top: -3px; right: -3px;
            min-width: 16px; height: 16px; padding: 0 4px;
            background: #ef4444; border-radius: 99px;
            font-size: 9px; color: #fff; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }

        /* Stat mini cards (student sidebar) */
        .stat-mini { border-radius: 8px; padding: 8px 10px; }

        /* Student sidebar nav-item compat */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: #4b5563; transition: all .15s;
        }
        .nav-item:hover { background: #f3f4f6; color: #111827; }
        .nav-item.active { background: #eff6ff; color: #1d4ed8; }
        .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }

        /* Student sidebar bottom card */
        .user-card-bottom { border-top: 1px solid #e5e7eb; }
        .logo-area { border-bottom: 1px solid #e5e7eb; }

        /* ── Instructor sidebar: clean light header ── */
        .ins-sidebar-brand {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }
        .ins-stat-card {
            border-radius: 12px; padding: 12px 14px;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .ins-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(0,0,0,.12); border-color: #c7d2fe !important; }
        .ins-nav-group {
            font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            color: #6b7280; padding: 14px 16px 6px;
            display: flex; align-items: center;
        }
        .ins-nav-group > span { display: inline-flex; align-items: center; gap: 5px; }
        .ins-nav {
            display: flex; align-items: center; gap: 11px;
            padding: 8px 12px; border-radius: 10px; margin: 1px 8px;
            font-size: 13px; font-weight: 500; color: #374151;
            transition: all .2s cubic-bezier(0.4,0,0.2,1);
            border: 1px solid transparent;
            position: relative;
        }
        .ins-nav::before {
            content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 0; border-radius: 3px 0 0 3px;
            background: linear-gradient(180deg, #283593, #FB5607);
            transition: height .2s ease;
        }
        .ins-nav:hover { background: #f8fafc; color: #0f172a; }
        .ins-nav.active { background: #f2f4ff; color: #1F2A7A; border-color: #d6dcff; font-weight: 600; }
        .ins-nav.active::before { height: 22px; }
        .ins-nav .ins-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            transition: transform .2s, box-shadow .2s;
        }
        .ins-nav:hover .ins-icon { transform: scale(1.08); }
        .ins-nav.active .ins-icon { box-shadow: 0 2px 8px -2px rgba(40, 53, 147, 0.3); }
        .ins-nav-badge {
            min-width: 20px; height: 20px; padding: 0 6px;
            border-radius: 10px; font-size: 11px; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .ins-user-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 12px 14px; transition: all .2s;
        }
        .ins-user-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px -4px rgba(0,0,0,.08); }

        /* نصوص بألوان hex / براند ثابتة (لا تُطابق text-slate-*) */
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php
        $useParentShell = auth()->check() && auth()->user()->isParent();
        $useStudentShell = auth()->check()
            && ! auth()->user()->isInstructor()
            && ! auth()->user()->isTeacher()
            && ! auth()->user()->isParent();
        $useInstructorShell = auth()->check()
            && (auth()->user()->isInstructor() || auth()->user()->isTeacher());
    ?>
    <?php if($useParentShell): ?>
        <?php echo $__env->make('layouts.partials.parent-app-shell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <?php if($useStudentShell): ?>
        <?php echo $__env->make('layouts.partials.student-app-shell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <?php if($useInstructorShell ?? false): ?>
        <?php echo $__env->make('layouts.partials.instructor-app-shell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</head>
<body x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      x-init="window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024; })">

    <div class="<?php echo e(($useStudentShell ?? false) ? 'app-shell-student' : 'flex h-screen overflow-hidden'); ?> <?php echo e(($useParentShell ?? false) ? 'app-shell-parent' : ''); ?> <?php echo e(($useInstructorShell ?? false) ? 'app-shell-instructor' : ''); ?>">
        <?php if(auth()->guard()->check()): ?>
            <?php if(!($useStudentShell ?? false)): ?>
            <aside x-show="sidebarOpen || window.innerWidth >= 1024"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 translate-x-full"
                   x-transition:enter-end="opacity-100 translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 translate-x-0"
                   x-transition:leave-end="opacity-0 translate-x-full"
                   class="app-sidebar flex-shrink-0 fixed lg:static inset-y-0 right-0 overflow-hidden flex flex-col h-full lg:h-screen z-50 lg:z-auto">
                <?php if(auth()->user()->isParent()): ?>
                    <?php echo $__env->make('layouts.parent-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php elseif(auth()->user()->isInstructor() || auth()->user()->isTeacher()): ?>
                    <?php echo $__env->make('layouts.instructor-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </aside>
            <div x-show="sidebarOpen && window.innerWidth < 1024"
                 @click="sidebarOpen = false"
                 x-transition
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="flex flex-col <?php echo e(($useStudentShell ?? false) ? 'sanua-main-wrap' : 'flex-1 min-w-0'); ?>">
            <?php if(auth()->guard()->check()): ?>
                <?php if($useParentShell ?? false): ?>
                    <?php echo $__env->make('layouts.partials.parent-app-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php elseif($useStudentShell ?? false): ?>
                    <?php echo $__env->make('layouts.partials.student-app-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php elseif($useInstructorShell ?? false): ?>
                    <?php echo $__env->make('layouts.partials.instructor-app-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                <?php $appRtl = app()->getLocale() === 'ar'; ?>
                <header class="app-header flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden h-btn flex-shrink-0">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <div class="hidden md:flex items-center flex-1 max-w-md">
                            <div class="search-box flex items-center gap-2 w-full">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                                <input type="text" placeholder="<?php echo e(__('common.nav_search_placeholder_long')); ?>" class="flex-1 bg-transparent border-none outline-none text-sm text-gray-700 placeholder-gray-400 min-w-0">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php
                            $currentUser = auth()->user();
                            $isInstructorLike = $currentUser && ($currentUser->isInstructor() || $currentUser->isTeacher());
                            $audiences = $isInstructorLike
                                ? [null, 'instructor', 'teacher']
                                : [null, 'student'];
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
                        ?>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="h-btn relative">
                                <i class="fas fa-bell text-sm"></i>
                                <?php if($navUnreadCount > 0): ?>
                                    <span class="n-badge"><?php echo e($navUnreadCount > 99 ? '99+' : $navUnreadCount); ?></span>
                                <?php endif; ?>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-80 dd-menu z-50">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900"><?php echo e($appRtl ? 'الإشعارات' : 'Notifications'); ?></h3>
                                    <?php if(Route::has('notifications')): ?>
                                        <a href="<?php echo e(route('notifications')); ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-700"><?php echo e($appRtl ? 'عرض الكل' : 'View all'); ?></a>
                                    <?php endif; ?>
                                </div>
                                <?php if($navRecentNotifications->isNotEmpty()): ?>
                                    <div class="max-h-96 overflow-y-auto">
                                        <?php $__currentLoopData = $navRecentNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $notificationUrl = $notification->action_url ?: (Route::has('notifications.show') ? route('notifications.show', $notification) : '#'); ?>
                                            <a href="<?php echo e($notificationUrl); ?>" class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-start gap-3">
                                                    <div class="mt-0.5"><i class="<?php echo e($notification->type_icon); ?> text-blue-500 text-sm"></i></div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($notification->title); ?></p>
                                                            <?php if(!$notification->is_read): ?><span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span><?php endif; ?>
                                                        </div>
                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2"><?php echo e($notification->message); ?></p>
                                                        <p class="text-[11px] text-gray-400 mt-1"><?php echo e(optional($notification->created_at)->diffForHumans()); ?></p>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="p-6 text-center text-gray-400 text-sm">
                                        <i class="fas fa-bell-slash text-xl mb-2 block"></i>
                                        <p><?php echo e($appRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications'); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="u-avatar flex-shrink-0">
                                    <?php if(auth()->user()->profile_image): ?>
                                        <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="">
                                    <?php else: ?>
                                        <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                                    <?php endif; ?>
                                </div>
                                <span class="hidden lg:block text-sm font-medium text-gray-700 max-w-[120px] truncate"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 hidden lg:block transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-56 dd-menu z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e(auth()->user()->name); ?></p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5"><?php echo e(auth()->user()->email ?? '—'); ?></p>
                                </div>
                                <div class="p-1.5">
                                    <?php
                                        $profileRoute = auth()->user()->isParent()
                                            ? route('parent.profile')
                                            : ((auth()->user()->isInstructor() || auth()->user()->isTeacher() || in_array(strtolower(auth()->user()->role ?? ''), ['instructor', 'teacher']))
                                                ? route('instructor.profile')
                                                : route('profile'));
                                    ?>
                                    <a href="<?php echo e($profileRoute); ?>" class="dd-item px-3 py-2 rounded-lg text-sm text-gray-700 gap-2.5">
                                        <i class="fas fa-user text-gray-400 text-xs w-4"></i>
                                        <?php echo e($appRtl ? 'الملف الشخصي' : 'Profile'); ?>

                                    </a>
                                    <a href="<?php echo e(route('settings')); ?>" class="dd-item px-3 py-2 rounded-lg text-sm text-gray-700 gap-2.5">
                                        <i class="fas fa-cog text-gray-400 text-xs w-4"></i>
                                        <?php echo e($appRtl ? 'الإعدادات' : 'Settings'); ?>

                                    </a>
                                    <hr class="my-1.5 border-gray-100">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="w-full dd-item px-3 py-2 rounded-lg text-sm text-red-600 gap-2.5 text-right">
                                            <i class="fas fa-sign-out-alt text-xs w-4"></i>
                                            <?php echo e($appRtl ? 'تسجيل الخروج' : 'Sign out'); ?>

                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <?php endif; ?>
            <?php endif; ?>

            <main class="flex-1 overflow-auto <?php echo e(($useStudentShell ?? false) ? 'sanua-main' : 'bg-surface-50'); ?>">
                <div class="<?php echo e(($useStudentShell ?? false) ? 'sanua-main-inner' : 'p-4 md:p-6 lg:p-8 w-full max-w-full'); ?>">
                    <?php if(session('success')): ?>
                        <div class="mb-5 <?php echo e(($useInstructorShell ?? false) ? 'ins-flash' : 'flex items-center gap-3'); ?> bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                            <i class="fas fa-check-circle flex-shrink-0"></i>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="mb-5 <?php echo e(($useInstructorShell ?? false) ? 'ins-flash' : 'flex items-center gap-3'); ?> bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('info')): ?>
                        <div class="mb-5 <?php echo e(($useInstructorShell ?? false) ? 'ins-flash' : 'flex items-center gap-3'); ?> bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 rounded-xl text-sm">
                            <i class="fas fa-circle-info flex-shrink-0"></i>
                            <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>

        <?php if(auth()->guard()->check()): ?>
            <?php if($useStudentShell ?? false): ?>
            <aside class="app-sidebar sanua-rail flex flex-col overflow-visible z-50">
                <?php echo $__env->make('layouts.partials.sanua-student-rail', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\layouts\app.blade.php ENDPATH**/ ?>
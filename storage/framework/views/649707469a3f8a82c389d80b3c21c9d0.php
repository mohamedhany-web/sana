<?php $__env->startSection('title', __('instructor.profile')); ?>
<?php $__env->startSection('header', __('instructor.profile')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : __('instructor.not_specified');
    $membershipId = '#'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);
    $planLabel = $subscription?->plan_name;
    $defaultAvatarUrl = $user->profile_image ? $user->profile_image_url : '';
    $initialTab = 'info';
    if ($errors->has('current_password') || $errors->has('password')) {
        $initialTab = 'security';
    } elseif ($errors->has('profile_image')) {
        $initialTab = 'photo';
    }
?>

<div class="space-y-6 w-full max-w-full" x-data="instructorProfilePage(<?php echo \Illuminate\Support\Js::from($initialTab)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($defaultAvatarUrl)->toHtml() ?>)">

    
    <div class="rounded-2xl p-5 sm:p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-[#283593] via-indigo-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-circle text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black leading-tight m-0"><?php echo e(__('instructor.profile')); ?></h1>
                    <p class="text-sm text-white/90 mt-0.5 m-0"><?php echo e(__('instructor.manage_profile_data')); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-sm font-semibold transition-colors no-underline shrink-0">
                <i class="fas fa-arrow-right text-xs"></i>
                <?php echo e(__('instructor.back_to_dashboard')); ?>

            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-2xl p-4 sm:p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide m-0 mb-1"><?php echo e(__('instructor.my_courses')); ?></p>
            <p class="text-2xl font-black text-slate-800 dark:text-slate-100 m-0 tabular-nums"><?php echo e($myCoursesCount); ?></p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide m-0 mb-1"><?php echo e(__('instructor.total_students')); ?></p>
            <p class="text-2xl font-black text-slate-800 dark:text-slate-100 m-0 tabular-nums"><?php echo e($totalStudents); ?></p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide m-0 mb-1"><?php echo e(__('instructor.join_date')); ?></p>
            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 m-0 leading-snug"><?php echo e($memberSince); ?></p>
        </div>
        <div class="rounded-2xl p-4 sm:p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide m-0 mb-1"><?php echo e(__('common.status')); ?></p>
            <p class="text-sm font-bold m-0 flex items-center gap-2 <?php echo e($user->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'); ?>">
                <span class="w-2 h-2 rounded-full <?php echo e($user->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                <?php echo e($user->is_active ? __('instructor.active_status') : __('instructor.not_active')); ?>

            </p>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-5 md:gap-6">
            <div class="shrink-0 mx-auto md:mx-0">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e($user->profile_image_url); ?>" alt="" id="ip-avatar-img" class="w-full h-full object-cover" onerror="this.style.display='none'; document.getElementById('ip-avatar-fallback')?.classList.remove('hidden');">
                        <span id="ip-avatar-fallback" class="hidden text-3xl font-black text-[#283593]"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php else: ?>
                        <span id="ip-avatar-fallback" class="text-3xl font-black text-[#283593]"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                        <img src="" alt="" id="ip-avatar-img" class="hidden w-full h-full object-cover">
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex-1 min-w-0 text-center md:text-right">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 mb-2">
                    <i class="fas fa-chalkboard-teacher text-[10px]"></i><?php echo e(__('instructor.instructor_role')); ?>

                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white m-0 break-words"><?php echo e($user->name); ?></h2>
                <div class="mt-2 flex flex-wrap items-center justify-center md:justify-end gap-x-4 gap-y-1 text-sm text-slate-600 dark:text-slate-400">
                    <?php if($user->email): ?>
                        <span class="inline-flex items-center gap-1.5 break-all"><i class="fas fa-envelope text-slate-400 text-xs"></i><?php echo e($user->email); ?></span>
                    <?php endif; ?>
                    <?php if($user->phone): ?>
                        <span class="inline-flex items-center gap-1.5" dir="ltr"><i class="fas fa-phone text-slate-400 text-xs"></i><?php echo e($user->phone); ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center justify-center md:justify-end gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                        <i class="fas fa-id-badge text-[10px]"></i><?php echo e($membershipId); ?>

                    </span>
                    <?php if($planLabel): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200">
                            <i class="fas fa-crown text-[10px]"></i><?php echo e($planLabel); ?>

                        </span>
                    <?php endif; ?>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        <i class="fas fa-clock-rotate-left ml-1"></i><?php echo e(__('instructor.last_login')); ?>: <?php echo e($lastLogin); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-8 space-y-5">
            <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex flex-wrap gap-1 p-2 border-b border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/40">
                    <button type="button" @click="tab = 'info'" :class="tab === 'info' ? 'bg-white dark:bg-slate-800 text-[#283593] dark:text-indigo-300 shadow-sm border-slate-200 dark:border-slate-600' : 'text-slate-600 dark:text-slate-400 border-transparent hover:bg-white/60'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors">
                        <i class="fas fa-user-pen text-xs"></i><?php echo e(__('instructor.update_data')); ?>

                    </button>
                    <button type="button" @click="tab = 'photo'" :class="tab === 'photo' ? 'bg-white dark:bg-slate-800 text-[#283593] dark:text-indigo-300 shadow-sm border-slate-200 dark:border-slate-600' : 'text-slate-600 dark:text-slate-400 border-transparent hover:bg-white/60'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors">
                        <i class="fas fa-camera text-xs"></i><?php echo e(__('instructor.profile_image')); ?>

                    </button>
                    <button type="button" @click="tab = 'security'" :class="tab === 'security' ? 'bg-white dark:bg-slate-800 text-[#283593] dark:text-indigo-300 shadow-sm border-slate-200 dark:border-slate-600' : 'text-slate-600 dark:text-slate-400 border-transparent hover:bg-white/60'" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold border transition-colors">
                        <i class="fas fa-shield-halved text-xs"></i><?php echo e(__('instructor.change_password')); ?>

                    </button>
                </div>

                <form method="POST" action="<?php echo e(route('instructor.profile.update')); ?>" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div x-show="tab === 'info'" x-cloak class="space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white m-0"><?php echo e(__('instructor.account_info')); ?></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 m-0"><?php echo e(__('instructor.update_data_subtitle')); ?></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="ip-name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.full_name')); ?></label>
                                <input type="text" id="ip-name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="ip-phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.phone')); ?></label>
                                <input type="text" id="ip-phone" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required dir="ltr"
                                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-left focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="md:col-span-2">
                                <label for="ip-email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.email_optional')); ?></label>
                                <input type="email" id="ip-email" name="email" value="<?php echo e(old('email', $user->email)); ?>" dir="ltr"
                                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 text-left focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="md:col-span-2">
                                <label for="ip-bio" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.bio_optional')); ?></label>
                                <textarea id="ip-bio" name="bio" rows="4" placeholder="<?php echo e(__('instructor.bio_placeholder_short')); ?>"
                                          class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"><?php echo e(old('bio', $user->bio)); ?></textarea>
                                <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div x-show="tab === 'photo'" x-cloak class="space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white m-0"><?php echo e(__('instructor.profile_image')); ?></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 m-0"><?php echo e(__('instructor.choose_image_label')); ?></p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-5 items-center">
                            <div class="w-28 h-28 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-900 flex items-center justify-center shrink-0 relative">
                                <img :src="previewUrl || defaultAvatarUrl" alt="" class="w-full h-full object-cover absolute inset-0" x-show="previewUrl || defaultAvatarUrl">
                                <span class="text-3xl font-black text-[#283593]" x-show="!previewUrl && !defaultAvatarUrl"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                            </div>
                            <label class="flex-1 w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-indigo-400 dark:hover:border-indigo-500 bg-slate-50 dark:bg-slate-900/50 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 p-6 text-center transition-colors"
                                   @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="onDrop($event)" :class="{ 'border-indigo-500 bg-indigo-50/60': dragover }">
                                <input type="file" name="profile_image" accept="image/*" class="hidden" x-ref="fileInput" @change="onFileSelect($event)">
                                <span @click="$refs.fileInput.click()" class="block">
                                    <i class="fas fa-cloud-arrow-up text-2xl text-indigo-500 mb-2"></i>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 m-0">انقر أو اسحب الصورة هنا</p>
                                    <p class="text-xs text-slate-500 mt-1 m-0" x-text="fileName || 'JPG, PNG — حتى 2MB'"></p>
                                </span>
                            </label>
                        </div>
                        <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div x-show="tab === 'security'" x-cloak class="space-y-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white m-0"><?php echo e(__('instructor.change_password')); ?></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 m-0"><?php echo e(__('instructor.leave_empty_if_no_change')); ?></p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label for="ip-current-pw" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.current_password')); ?></label>
                                <input type="password" id="ip-current-pw" name="current_password" autocomplete="current-password"
                                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="ip-new-pw" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.new_password')); ?></label>
                                    <input type="password" id="ip-new-pw" name="password" autocomplete="new-password"
                                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div>
                                    <label for="ip-confirm-pw" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.confirm_password')); ?></label>
                                    <input type="password" id="ip-confirm-pw" name="password_confirmation" autocomplete="new-password"
                                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                </div>
                            </div>
                        </div>
                        <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 px-4 py-3 text-xs text-amber-800 dark:text-amber-200 flex gap-2">
                            <i class="fas fa-lock mt-0.5"></i>
                            <span><?php echo e(__('instructor.change_password_regularly')); ?></span>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400 m-0"><?php echo e(__('instructor.membership_number')); ?> <?php echo e($membershipId); ?></p>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#283593] hover:bg-[#1F2A7A] text-white text-sm font-bold border-0 cursor-pointer transition-colors">
                            <i class="fas fa-save"></i><?php echo e(__('instructor.save_changes')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <aside class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-2">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white m-0 mb-2 px-1">اختصارات الحساب</h3>
                <?php if(Route::has('instructor.personal-branding.edit')): ?>
                <a href="<?php echo e(route('instructor.personal-branding.edit')); ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors no-underline text-inherit group">
                    <span class="w-10 h-10 rounded-xl bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform"><i class="fas fa-user-tie"></i></span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm font-semibold text-slate-800 dark:text-slate-100"><?php echo e(__('instructor.personal_branding')); ?></span>
                        <span class="block text-xs text-slate-500 truncate"><?php echo e(__('instructor.profile_for_publishing')); ?></span>
                    </span>
                    <i class="fas fa-chevron-left text-slate-300 text-xs"></i>
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('instructor.courses.index')); ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors no-underline text-inherit group">
                    <span class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 flex items-center justify-center shrink-0"><i class="fas fa-book-open"></i></span>
                    <span class="flex-1 text-sm font-semibold text-slate-800 dark:text-slate-100"><?php echo e(__('instructor.my_courses')); ?></span>
                    <i class="fas fa-chevron-left text-slate-300 text-xs"></i>
                </a>
                <?php if(Route::has('instructor.classroom.index') && $user->hasSubscriptionFeature('classroom_access')): ?>
                <a href="<?php echo e(route('instructor.classroom.index')); ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors no-underline text-inherit group">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-chalkboard-teacher"></i></span>
                    <span class="flex-1 text-sm font-semibold text-slate-800 dark:text-slate-100">Muallimx Classroom</span>
                    <i class="fas fa-chevron-left text-slate-300 text-xs"></i>
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('settings')); ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors no-underline text-inherit group">
                    <span class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 flex items-center justify-center shrink-0"><i class="fas fa-cog"></i></span>
                    <span class="flex-1 text-sm font-semibold text-slate-800 dark:text-slate-100"><?php echo e(__('instructor.settings')); ?></span>
                    <i class="fas fa-chevron-left text-slate-300 text-xs"></i>
                </a>
            </div>

            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white m-0 mb-3"><?php echo e(__('instructor.tips_for_instructor')); ?></h3>
                <ul class="space-y-3 m-0 p-0 list-none text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                        <span><strong class="text-slate-800 dark:text-slate-200"><?php echo e(__('instructor.update_bio')); ?></strong> — <?php echo e(__('instructor.add_bio_for_students')); ?></span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-shield-halved text-indigo-500 mt-0.5 shrink-0"></i>
                        <span><?php echo e(__('instructor.strong_password')); ?></span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function instructorProfilePage(initialTab, defaultAvatarUrl) {
    return {
        tab: initialTab || 'info',
        defaultAvatarUrl: defaultAvatarUrl || '',
        previewUrl: null,
        fileName: '',
        dragover: false,
        onFileSelect(e) {
            this.applyFile(e.target.files?.[0]);
        },
        onDrop(e) {
            this.dragover = false;
            const file = e.dataTransfer?.files?.[0];
            if (file && file.type.startsWith('image/')) {
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.applyFile(file);
            }
        },
        applyFile(file) {
            if (!file) return;
            this.fileName = file.name;
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = URL.createObjectURL(file);
            this.tab = 'photo';
            const img = document.getElementById('ip-avatar-img');
            const fb = document.getElementById('ip-avatar-fallback');
            if (img) { img.src = this.previewUrl; img.classList.remove('hidden'); }
            if (fb) fb.classList.add('hidden');
        },
    };
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views/instructor/profile/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', __('instructor.personal_branding')); ?>
<?php $__env->startSection('header', __('instructor.personal_branding')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $status = $profile->status;
    $statusLabel = \App\Models\InstructorProfile::statusLabel($status);
    $statusStyles = match ($status) {
        'approved' => ['ring' => 'ring-emerald-500/20', 'badge' => 'bg-emerald-100 text-emerald-800', 'icon' => 'fa-circle-check', 'iconColor' => 'text-emerald-500'],
        'pending_review' => ['ring' => 'ring-amber-500/20', 'badge' => 'bg-amber-100 text-amber-800', 'icon' => 'fa-hourglass-half', 'iconColor' => 'text-amber-500'],
        'rejected' => ['ring' => 'ring-rose-500/20', 'badge' => 'bg-rose-100 text-rose-800', 'icon' => 'fa-circle-xmark', 'iconColor' => 'text-rose-500'],
        default => ['ring' => 'ring-slate-500/10', 'badge' => 'bg-slate-100 text-slate-700', 'icon' => 'fa-pen', 'iconColor' => 'text-slate-500'],
    };
    $skillsPreview = $profile->skills_list;
    if (old('skills') !== null) {
        $split = preg_split('/[\r\n,،]+/u', (string) old('skills'), -1, PREG_SPLIT_NO_EMPTY);
        $skillsPreview = array_values(array_filter(array_map('trim', $split)));
    }
?>

<div class="space-y-6 w-full max-w-full" x-data="personalBrandingForm(<?php echo \Illuminate\Support\Js::from(old('skills', $profile->skills))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($skillsPreview)->toHtml() ?>)">

    
    <div class="rounded-2xl p-5 sm:p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-fuchsia-700 via-[#283593] to-indigo-500">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-tie text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black leading-tight m-0"><?php echo e(__('instructor.personal_branding')); ?></h1>
                    <p class="text-sm text-white/90 mt-0.5 m-0"><?php echo e(__('instructor.profile_for_publishing')); ?></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <?php if($profile->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
                    <a href="<?php echo e(route('public.instructors.show', $profile->user_id)); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-sm font-semibold no-underline transition-colors">
                        <i class="fas fa-external-link-alt text-xs"></i>
                        عرض الملف العام
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('instructor.profile')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-sm font-semibold no-underline transition-colors">
                    <i class="fas fa-user text-xs"></i>
                    <?php echo e(__('instructor.profile')); ?>

                </a>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-4 sm:p-5 ring-2 <?php echo e($statusStyles['ring']); ?>">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
            <div class="flex items-start gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
                    <i class="fas <?php echo e($statusStyles['icon']); ?> <?php echo e($statusStyles['iconColor']); ?>"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide m-0 mb-1"><?php echo e(__('instructor.status_label')); ?></p>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-sm font-bold <?php echo e($statusStyles['badge']); ?>">
                        <?php echo e($statusLabel); ?>

                    </span>
                    <?php if($profile->rejection_reason): ?>
                        <p class="text-sm text-rose-600 mt-2 m-0">
                            <strong><?php echo e(__('instructor.rejection_reason_label')); ?>:</strong> <?php echo e($profile->rejection_reason); ?>

                        </p>
                    <?php endif; ?>
                    <?php if($profile->status === \App\Models\InstructorProfile::STATUS_PENDING_REVIEW): ?>
                        <p class="text-xs text-slate-500 mt-2 m-0">جاري مراجعة ملفك من الإدارة. يمكنك التعديل بعد الرفض فقط.</p>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-xs text-slate-500 m-0 sm:text-left max-w-md"><?php echo e(__('instructor.personal_branding_desc')); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-8">
            <form method="POST" action="<?php echo e(route('instructor.personal-branding.update')); ?>" enctype="multipart/form-data"
                  class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                
                <div class="p-5 sm:p-6 border-b border-slate-200">
                    <h2 class="text-base font-bold text-slate-900 m-0 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-fuchsia-100 text-fuchsia-600 flex items-center justify-center text-sm"><i class="fas fa-camera"></i></span>
                        <?php echo e(__('instructor.profile_photo')); ?>

                    </h2>
                    <div class="mt-4 flex flex-col sm:flex-row gap-5 items-center">
                        <div class="w-28 h-28 rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center shrink-0 relative">
                            <?php if($profile->photo_path): ?>
                                <img src="<?php echo e($profile->photo_url); ?>" alt="" class="w-full h-full object-cover absolute inset-0" id="pb-photo-preview"
                                     onerror="this.classList.add('hidden'); document.getElementById('pb-photo-fallback')?.classList.remove('hidden');">
                                <div id="pb-photo-fallback" class="hidden absolute inset-0 flex items-center justify-center text-slate-400"><i class="fas fa-user text-3xl"></i></div>
                            <?php else: ?>
                                <div id="pb-photo-fallback" class="absolute inset-0 flex items-center justify-center text-3xl font-black text-[#283593]"><?php echo e(mb_substr($user->name, 0, 1)); ?></div>
                                <img src="" alt="" id="pb-photo-preview" class="hidden w-full h-full object-cover absolute inset-0">
                            <?php endif; ?>
                        </div>
                        <label class="flex-1 w-full cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 hover:border-indigo-400 bg-slate-50 hover:bg-indigo-50/40 p-5 text-center transition-colors">
                            <input type="file" name="photo" accept="image/*" class="hidden" @change="previewPhoto($event)">
                            <i class="fas fa-cloud-arrow-up text-2xl text-indigo-500 mb-2"></i>
                            <p class="text-sm font-semibold text-slate-700 m-0"><?php echo e(__('instructor.choose_image_label')); ?></p>
                            <p class="text-xs text-slate-500 mt-1 m-0">JPG, PNG — حتى 2MB</p>
                        </label>
                    </div>
                    <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-2 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="p-5 sm:p-6 border-b border-slate-200 space-y-4">
                    <h2 class="text-base font-bold text-slate-900 m-0 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm"><i class="fas fa-id-card"></i></span>
                        التعريف العام
                    </h2>
                    <div>
                        <label for="pb-headline" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.intro_title')); ?></label>
                        <input type="text" id="pb-headline" name="headline" value="<?php echo e(old('headline', $profile->headline)); ?>"
                               placeholder="<?php echo e(__('instructor.headline_placeholder')); ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm">
                        <?php $__errorArgs = ['headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="pb-bio" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo e(__('instructor.bio')); ?></label>
                        <textarea id="pb-bio" name="bio" rows="5" placeholder="<?php echo e(__('instructor.bio_placeholder')); ?>"
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm"><?php echo e(old('bio', $profile->bio)); ?></textarea>
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

                
                <div class="p-5 sm:p-6 border-b border-slate-200">
                    <h2 class="text-base font-bold text-slate-900 m-0 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-sm"><i class="fas fa-briefcase"></i></span>
                        <?php echo e(__('instructor.experience')); ?>

                    </h2>
                    <p class="text-xs text-slate-500 mt-2 mb-3 m-0"><?php echo e(__('instructor.experience_placeholder')); ?></p>
                    <textarea name="experience" rows="8" placeholder="سطر لكل خبرة أو فقرة..."
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-mono leading-relaxed"><?php echo e(old('experience', $profile->experience)); ?></textarea>
                    <?php $__errorArgs = ['experience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="p-5 sm:p-6">
                    <h2 class="text-base font-bold text-slate-900 m-0 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm"><i class="fas fa-tags"></i></span>
                        <?php echo e(__('instructor.skills')); ?>

                    </h2>
                    <p class="text-xs text-slate-500 mt-2 mb-3 m-0"><?php echo e(__('instructor.skills_hint')); ?></p>
                    <textarea name="skills" rows="5" placeholder="<?php echo e(__('instructor.skills_placeholder')); ?>" x-model="skillsText" @input="parseSkills()"
                              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm"></textarea>
                    <?php $__errorArgs = ['skills'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1 m-0"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="mt-3" x-show="skillTags.length > 0" x-cloak>
                        <p class="text-xs font-bold text-slate-500 m-0 mb-2">
                            <?php echo e(__('instructor.skills_preview')); ?> (<span x-text="skillTags.length"></span> <?php echo e(__('instructor.skill_count')); ?>)
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(skill, i) in skillTags" :key="i">
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 text-indigo-800 px-2.5 py-1 text-xs font-semibold" x-text="skill"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="px-5 sm:px-6 py-4 bg-slate-50/80 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-slate-500 m-0">
                        <i class="fas fa-info-circle ml-1"></i>احفظ التعديلات قبل الإرسال للمراجعة
                    </p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-[#283593] hover:bg-[#1F2A7A] text-white text-sm font-bold border-0 cursor-pointer transition-colors">
                        <i class="fas fa-save"></i><?php echo e(__('instructor.save_changes')); ?>

                    </button>
                </div>
            </form>

            <?php if(in_array($profile->status, ['draft', 'rejected'], true)): ?>
            <form method="POST" action="<?php echo e(route('instructor.personal-branding.submit')); ?>" class="mt-4">
                <?php echo csrf_field(); ?>
                <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-amber-900 m-0"><?php echo e(__('instructor.submit_for_review')); ?></p>
                        <p class="text-xs text-amber-800/80 mt-1 m-0">بعد اكتمال المتطلبات في الجانب، أرسل الملف للمراجعة والنشر.</p>
                    </div>
                    <button type="submit" <?php if(!$canSubmit): echo 'disabled'; endif; ?>
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold border-0 transition-colors shrink-0
                            <?php echo e($canSubmit ? 'bg-amber-500 hover:bg-amber-600 text-white cursor-pointer' : 'bg-slate-300 text-slate-500 cursor-not-allowed'); ?>">
                        <i class="fas fa-paper-plane"></i><?php echo e(__('instructor.submit_for_review')); ?>

                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>

        
        <aside class="lg:col-span-4 space-y-4">
            
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/80">
                    <h3 class="text-sm font-bold text-slate-900 m-0">معاينة للزوار</h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                            <?php if($profile->photo_path): ?>
                                <img src="<?php echo e($profile->photo_url); ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="w-full h-full flex items-center justify-center text-lg font-black text-[#283593]"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 m-0 truncate"><?php echo e($user->name); ?></p>
                            <p class="text-xs text-indigo-600 m-0 truncate"><?php echo e($profile->headline ?: '—'); ?></p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 line-clamp-4 m-0 mb-3"><?php echo e(Str::limit($profile->bio ?: 'النبذة التعريفية...', 160)); ?></p>
                    <div class="flex flex-wrap gap-1.5">
                        <?php $__empty_1 = true; $__currentLoopData = array_slice($skillsPreview, 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600"><?php echo e($skill); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="text-[10px] text-slate-400">المهارات...</span>
                        <?php endif; ?>
                        <?php if(count($skillsPreview) > 6): ?>
                            <span class="text-[10px] text-slate-400">+<?php echo e(count($skillsPreview) - 6); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 m-0 mb-3">متطلبات الإرسال</h3>
                <ul class="space-y-2.5 m-0 p-0 list-none text-sm">
                    <?php
                        $checks = [
                            ['ok' => filled($profile->headline), 'label' => __('instructor.intro_title')],
                            ['ok' => filled($profile->bio), 'label' => __('instructor.bio')],
                            ['ok' => $skillsCount >= 3, 'label' => '3 مهارات على الأقل ('.$skillsCount.'/3)'],
                        ];
                    ?>
                    <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-2 <?php echo e($check['ok'] ? 'text-emerald-700' : 'text-slate-500'); ?>">
                        <i class="fas <?php echo e($check['ok'] ? 'fa-check-circle' : 'fa-circle text-[8px] opacity-40'); ?> shrink-0"></i>
                        <span><?php echo e($check['label']); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>

            
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-slate-900 m-0 mb-3">مسار النشر</h3>
                <ol class="space-y-3 m-0 p-0 list-none text-xs text-slate-600">
                    <li class="flex gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold shrink-0">1</span>
                        <span>أكمل البيانات واحفظ التعديلات</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold shrink-0">2</span>
                        <span>أرسل للمراجعة</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold shrink-0">3</span>
                        <span>بعد الاعتماد يظهر ملفك في الموقع والكورسات</span>
                    </li>
                </ol>
            </div>
        </aside>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function personalBrandingForm(initialText, initialSkills) {
    return {
        skillsText: initialText || '',
        skillTags: initialSkills || [],
        parseSkills() {
            const raw = (this.skillsText || '').split(/[\r\n,،]+/u);
            this.skillTags = raw.map(s => s.trim()).filter(Boolean);
        },
        previewPhoto(e) {
            const file = e.target.files?.[0];
            if (!file || !file.type.startsWith('image/')) return;
            const url = URL.createObjectURL(file);
            const img = document.getElementById('pb-photo-preview');
            const fb = document.getElementById('pb-photo-fallback');
            if (img) {
                img.src = url;
                img.classList.remove('hidden');
            }
            if (fb) fb.classList.add('hidden');
        },
    };
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sana\resources\views\instructor\personal-branding\edit.blade.php ENDPATH**/ ?>
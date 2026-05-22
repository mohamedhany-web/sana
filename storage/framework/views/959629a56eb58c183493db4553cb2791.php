<?php
    $brand = config('app.name', 'Sana');
    $bc = config('brand.colors');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $hasChannels = ($supportEmail ?? '') !== '' || ($supportPhone ?? '') !== '' || ($whatsappUrl ?? '') !== '';
    $topics = ['استفسار عام', 'الدفع والاشتراكات', 'دعم فني', 'الشراكات والتعاون'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e(__('public.contact_page_title')); ?> - <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e(__('public.contact_meta_description', ['brand' => $brand])); ?>">
    <meta name="theme-color" content="<?php echo e($bc['blue']); ?>">
    <link rel="canonical" href="<?php echo e(url('/contact')); ?>">
    <meta property="og:title" content="<?php echo e(__('public.contact_page_title')); ?> - <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e(__('public.contact_meta_description', ['brand' => $brand])); ?>">
    <meta property="og:image" content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{edu:{primary:'<?php echo e($bc['blue']); ?>',purple:'<?php echo e($bc['purple']); ?>',accent:'<?php echo e($bc['yellow']); ?>'}}}}}</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php echo $__env->make('landing.eduvalt.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.courses-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.eduvalt.contact-page', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased bg-white">
<div id="edu-preloader" aria-hidden="true"><div class="edu-preloader-spinner"></div></div>
<div id="scroll-progress"></div>

<?php echo $__env->make('landing.eduvalt.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="pt-[76px] lg:pt-[84px]">


<section class="edu-contact-hero relative overflow-hidden py-10 lg:py-14">
    <div class="absolute top-16 start-0 w-64 h-64 rounded-full bg-sky-200/40 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 end-0 w-80 h-80 rounded-full bg-blue-200/30 blur-3xl pointer-events-none"></div>
    <svg class="absolute top-24 end-[10%] w-20 h-20 text-[var(--edu-primary)]/10 pointer-events-none edu-float hidden sm:block" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true"><circle cx="20" cy="20" r="4"/><path d="M10 50 Q50 10 90 50" fill="none" stroke="currentColor" stroke-width="2"/></svg>

    <div class="edu-container-full relative z-10">
        <div class="edu-courses-inner">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 reveal">
                <div class="max-w-2xl text-center lg:text-start mx-auto lg:mx-0">
                    <nav class="edu-breadcrumb justify-center lg:justify-start mb-4" aria-label="مسار التنقل">
                        <a href="<?php echo e(route('home')); ?>"><?php echo e($tr('nav.home')); ?></a>
                        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                        <span class="text-slate-800 font-semibold"><?php echo e(__('public.contact_page_title')); ?></span>
                    </nav>
                    <span class="edu-badge mb-4"><i class="fas fa-headset"></i> <?php echo e(__('public.contact_hero_badge')); ?></span>
                    <h1 class="edu-section-title text-slate-900">
                        <?php echo e(__('public.contact_hero_title')); ?>

                        <?php echo $__env->make('landing.eduvalt.partials.title-mark', ['text' => __('public.contact_hero_highlight')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </h1>
                    <p class="text-slate-600 leading-8 mt-3 text-sm lg:text-base max-w-xl mx-auto lg:mx-0">
                        <?php echo e(__('public.contact_hero_sub', ['brand' => $brand])); ?>

                    </p>
                    <div class="edu-hero-actions mt-6 justify-center lg:justify-start">
                        <a href="#contact-form" class="edu-btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            <?php echo e(__('public.contact_form_title')); ?>

                        </a>
                        <?php if($whatsappUrl !== ''): ?>
                        <a href="<?php echo e($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="edu-btn-outline">
                            <i class="fab fa-whatsapp text-emerald-600"></i>
                            <?php echo e(__('public.contact_whatsapp_cta')); ?>

                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full lg:max-w-xl shrink-0">
                    <div class="edu-contact-trust reveal s1">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:var(--edu-primary-light);color:var(--edu-primary)"><i class="fas fa-clock"></i></span>
                        <span class="text-sm font-bold text-slate-700"><?php echo e(__('public.contact_trust_response')); ?></span>
                    </div>
                    <div class="edu-contact-trust reveal s2">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-emerald-50 text-emerald-600"><i class="fas fa-shield-halved"></i></span>
                        <span class="text-sm font-bold text-slate-700"><?php echo e(__('public.contact_trust_secure')); ?></span>
                    </div>
                    <div class="edu-contact-trust reveal">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-orange-50 text-orange-600"><i class="fas fa-users"></i></span>
                        <span class="text-sm font-bold text-slate-700"><?php echo e(__('public.contact_trust_team')); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-10 lg:py-14 bg-white">
    <div class="edu-container-full">
        <div class="edu-courses-inner">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <div class="lg:col-span-7 reveal">
                    <div class="edu-card edu-contact-form-card">
                        <div class="edu-contact-form-card__head">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0" style="background:var(--edu-primary)"><i class="fas fa-envelope"></i></span>
                                <?php echo e(__('public.contact_form_title')); ?>

                            </h2>
                            <p class="text-slate-500 text-sm mt-2"><?php echo e(__('public.contact_form_sub')); ?></p>
                        </div>
                        <div class="edu-contact-form-card__body">
                            <?php if(session('success')): ?>
                                <div class="edu-contact-alert mb-6" role="status">
                                    <i class="fas fa-circle-check text-lg"></i>
                                    <span><?php echo e(session('success')); ?></span>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="<?php echo e(route('public.contact.store')); ?>" class="space-y-5" id="contact-form">
                                <?php echo csrf_field(); ?>
                                <div>
                                    <label for="name" class="edu-contact-label"><i class="fas fa-user"></i> <?php echo e(__('auth.full_name')); ?> *</label>
                                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255" class="edu-contact-input" placeholder="اسمك الكامل">
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-sm text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="grid sm:grid-cols-2 gap-5">
                                    <div>
                                        <label for="email" class="edu-contact-label"><i class="fas fa-envelope"></i> البريد الإلكتروني *</label>
                                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required maxlength="255" class="edu-contact-input" dir="ltr" placeholder="you@example.com">
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-sm text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div>
                                        <label for="phone" class="edu-contact-label"><i class="fas fa-mobile-screen"></i> الجوال <span class="text-slate-400 font-normal">(اختياري)</span></label>
                                        <input type="text" name="phone" id="phone" value="<?php echo e(old('phone')); ?>" maxlength="20" class="edu-contact-input" dir="ltr" placeholder="05xxxxxxxx">
                                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-sm text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-500 mb-2"><?php echo e(__('public.contact_topics_label')); ?></p>
                                    <div class="flex flex-wrap gap-2" id="contact-topic-chips">
                                        <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button type="button" class="edu-contact-chip" data-topic="<?php echo e($topic); ?>"><?php echo e($topic); ?></button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                                <div>
                                    <label for="subject" class="edu-contact-label"><i class="fas fa-tag"></i> الموضوع *</label>
                                    <input type="text" name="subject" id="subject" value="<?php echo e(old('subject')); ?>" required maxlength="255" class="edu-contact-input" placeholder="موجز لطلبك">
                                    <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-sm text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div>
                                    <label for="message" class="edu-contact-label"><i class="fas fa-message"></i> الرسالة *</label>
                                    <textarea name="message" id="message" rows="5" required maxlength="5000" class="edu-contact-input resize-y min-h-[140px]" placeholder="اكتب تفاصيل رسالتك..."><?php echo e(old('message')); ?></textarea>
                                    <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-sm text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <button type="submit" class="edu-btn-primary w-full sm:w-auto">
                                    <i class="fas fa-paper-plane"></i>
                                    إرسال الرسالة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <aside class="lg:col-span-5 space-y-5 reveal s1">
                    <div class="edu-contact-side-panel">
                        <h2 class="text-lg font-bold mb-1"><?php echo e(__('public.contact_channels_title')); ?></h2>
                        <p class="text-white/85 text-sm mb-5 leading-relaxed"><?php echo e(__('public.contact_channels_sub')); ?></p>
                        <?php if($hasChannels): ?>
                            <ul class="space-y-3 mb-4">
                                <?php if($supportEmail !== ''): ?>
                                <li>
                                    <a href="mailto:<?php echo e($supportEmail); ?>" class="edu-contact-channel">
                                        <span class="edu-contact-channel-icon" style="background:linear-gradient(135deg,var(--edu-purple),var(--edu-primary-dark))"><i class="fas fa-envelope"></i></span>
                                        <div class="min-w-0">
                                            <span class="block text-[10px] font-bold uppercase tracking-wide text-white/60">البريد</span>
                                            <span class="font-semibold text-white break-all text-sm"><?php echo e($supportEmail); ?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if($supportPhone !== ''): ?>
                                <li>
                                    <a href="tel:<?php echo e(preg_replace('/\s+/', '', $supportPhone)); ?>" class="edu-contact-channel">
                                        <span class="edu-contact-channel-icon" style="background:linear-gradient(135deg,var(--edu-accent),var(--edu-accent-dark))"><i class="fas fa-phone"></i></span>
                                        <div>
                                            <span class="block text-[10px] font-bold uppercase tracking-wide text-white/60">الهاتف</span>
                                            <span class="font-semibold text-white block text-sm" dir="ltr"><?php echo e($supportPhone); ?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                            <?php if($whatsappUrl !== ''): ?>
                                <a href="<?php echo e($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="edu-contact-wa">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    <?php echo e(__('public.contact_whatsapp_cta')); ?>

                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-sm text-white/80 rounded-xl border border-dashed border-white/25 px-4 py-3 leading-relaxed">
                                <?php echo e(__('public.contact_channels_empty_hint')); ?>

                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="edu-card edu-contact-help-card">
                        <h3 class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                            <i class="fas fa-circle-question text-[var(--edu-accent)]"></i>
                            <?php echo e(__('public.contact_faq_card_title')); ?>

                        </h3>
                        <p class="text-slate-600 text-sm mb-4 leading-relaxed"><?php echo e(__('public.contact_faq_card_sub')); ?></p>
                        <div class="flex flex-col gap-2.5">
                            <a href="<?php echo e(route('public.faq')); ?>" class="edu-btn-primary w-full justify-center text-sm !py-2.5">
                                <i class="fas fa-comments"></i> <?php echo e(__('public.faq_page_title')); ?>

                            </a>
                            <a href="<?php echo e(route('public.help')); ?>" class="edu-btn-outline w-full justify-center text-sm !py-2.5">
                                <i class="fas fa-book-open"></i> <?php echo e(__('public.help_page_title')); ?>

                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>


<section class="py-12 lg:py-14 bg-[var(--edu-bg)]">
    <div class="edu-container-full">
        <div class="edu-courses-inner reveal">
            <div class="edu-cta-wrap px-8 py-10 lg:py-12 text-center text-white">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3"><?php echo e(__('public.contact_page_title')); ?></h2>
                <p class="text-white/90 text-sm sm:text-base max-w-xl mx-auto mb-8 leading-7">
                    <?php echo e(__('public.contact_cta_bottom')); ?>

                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('public.faq')); ?>" class="edu-btn-white !text-[var(--edu-primary)] hover:!text-[var(--edu-primary-dark)]">
                        <i class="fas fa-circle-question"></i>
                        <?php echo e(__('public.faq_page_title')); ?>

                    </a>
                    <a href="<?php echo e(route('home')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-house"></i>
                        <?php echo e($tr('nav.home')); ?>

                    </a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="edu-btn-ghost-light">
                        <i class="fas fa-graduation-cap"></i>
                        <?php echo e($tr('nav.courses')); ?>

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

    document.querySelectorAll('#contact-topic-chips .edu-contact-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var subject = document.getElementById('subject');
            if (subject) subject.value = chip.getAttribute('data-topic') || '';
            document.querySelectorAll('#contact-topic-chips .edu-contact-chip').forEach(function (c) {
                c.classList.toggle('is-active', c === chip);
            });
            subject?.focus();
        });
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\contact.blade.php ENDPATH**/ ?>
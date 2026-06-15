<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $c = fn (string $key) => str_replace(':brand', $brand, __('sana_contact.'.$key));
    $hero = __('sana_contact.hero');
    $heroTrust = __('sana_contact.hero_trust');
    $channelsCopy = __('sana_contact.channels');
    $categories = __('sana_contact.categories');
    $faqItems = __('sana_contact.faq');
    $fields = __('sana_contact.fields');
    $contact = $contact ?? \App\Support\PublicContactInfo::payload();
    $responseCards = $responseCards ?? \App\Support\PublicContactInfo::responseExpectations();
    $address = trim((string) ($contact['address'] ?? '')) ?: trim(__('sana_contact.address'));
    $addressNote = trim(__('sana_contact.address_note'));
    $serviceScope = trim((string) ($contact['service_scope'] ?? ''));
    $mapEmbed = trim(__('sana_contact.map_embed'));
    $supportEmail = $supportEmail ?? $contact['email'];
    $supportPhone = $supportPhone ?? $contact['phone'];
    $whatsappUrl = $whatsappUrl ?? $contact['whatsapp_url'];
    $socials = $socials ?? $contact['socials'];
    $phoneTel = $supportPhone !== '' ? preg_replace('/\s+/', '', $supportPhone) : '';
    $hasPhone = $supportPhone !== '';
    $hasEmail = $supportEmail !== '';
    $hasWhatsapp = $whatsappUrl !== '';
    $hasSocials = !empty($socials);
    $supportChip = __('sana_contact.support_chip');
    $prefillCategory = old('category', request('topic') === 'assessment' ? 'assessment' : 'general');
    $prefillSubject = old('subject', request('topic') === 'assessment' ? 'طلب تقييم مستوى مجاني' : 'استفسار عام');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($c('meta_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($c('meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/contact')); ?>">
    <meta property="og:title" content="<?php echo e($c('meta_title')); ?> — <?php echo e($brand); ?>">
    <meta property="og:description" content="<?php echo e($c('meta_description')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.contact-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page" x-data="{ category: '<?php echo e($prefillCategory); ?>' }">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-contact-page">


<section class="sana-ct-hero">
    <div class="sana-container">
        <div class="sana-ct-hero__grid sana-reveal">
            <div class="sana-ct-hero__content">
                <span class="sana-ct-hero__eyebrow"><i class="fas fa-headset"></i> <?php echo e($hero['eyebrow']); ?></span>
                <h1 class="sana-ct-hero__title">
                    <?php echo e($hero['title']); ?>

                    <span class="hl"><?php echo e($hero['highlight']); ?></span>
                </h1>
                <p class="sana-ct-hero__sub"><?php echo e(str_replace(':brand', $brand, $hero['sub'])); ?></p>
                <div class="sana-ct-hero__actions">
                    <a href="#contact-form" class="sana-btn sana-btn--yellow sana-btn--lg">
                        <i class="fas fa-paper-plane"></i> <?php echo e($hero['cta_form']); ?>

                    </a>
                    <?php if($hasWhatsapp): ?>
                    <a href="<?php echo e($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="sana-btn sana-btn--wa sana-btn--lg">
                        <i class="fab fa-whatsapp"></i> <?php echo e($hero['cta_whatsapp']); ?>

                    </a>
                    <?php endif; ?>
                </div>
                <div class="sana-ct-hero__trust">
                    <span><i class="fas fa-bolt"></i> <?php echo e($heroTrust['response']); ?></span>
                    <span><i class="fas fa-shield-halved"></i> <?php echo e($heroTrust['privacy']); ?></span>
                    <span><i class="fas fa-globe-americas"></i> <?php echo e($heroTrust['regions']); ?></span>
                </div>
            </div>
            <div class="sana-ct-hero__visual">
                <?php echo $__env->make('landing.sana.partials.contact-hero-scene', ['supportChip' => $supportChip], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <span class="sana-head__eyebrow"><?php echo e($brand); ?></span>
            <h2 class="sana-head__title"><?php echo e(__('sana_contact.channels_title')); ?> <span class="hl"><?php echo e(__('sana_contact.channels_highlight')); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('sana_contact.channels_sub')); ?></p>
        </div>
        <?php if($serviceScope !== ''): ?>
        <p class="sana-ct-scope sana-reveal" role="note">
            <i class="fas fa-globe-americas"></i>
            <span><?php echo e($serviceScope); ?></span>
        </p>
        <?php endif; ?>
        <?php if($hasEmail): ?>
        <p class="sana-ct-official-email sana-reveal" role="note">
            <i class="fas fa-envelope-circle-check"></i>
            <span><?php echo e(__('sana_contact.official_email_note')); ?> <strong dir="ltr"><?php echo e($supportEmail); ?></strong></span>
        </p>
        <?php endif; ?>
        <?php if(!$hasPhone && !$hasWhatsapp): ?>
        <p class="sana-ct-channels-empty sana-reveal"><?php echo e(__('public.contact_channels_empty_hint')); ?></p>
        <?php endif; ?>
        <div class="sana-ct-channels" id="social-links">
            <?php if($hasPhone): ?>
            <a href="tel:<?php echo e($phoneTel); ?>" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--phone"><i class="fas fa-phone"></i></span>
                <strong><?php echo e($channelsCopy['phone']['title']); ?></strong>
                <p><?php echo e($channelsCopy['phone']['desc']); ?></p>
                <span class="sana-ct-channel__info" dir="ltr"><?php echo e($supportPhone); ?></span>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['phone']['action']); ?> <i class="fas fa-arrow-left"></i></span>
            </a>
            <?php endif; ?>

            <?php if($hasWhatsapp): ?>
            <a href="<?php echo e($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--wa"><i class="fab fa-whatsapp"></i></span>
                <strong><?php echo e($channelsCopy['whatsapp']['title']); ?></strong>
                <p><?php echo e($channelsCopy['whatsapp']['desc']); ?></p>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['whatsapp']['action']); ?> <i class="fas fa-arrow-left"></i></span>
            </a>
            <?php endif; ?>

            <?php if($hasEmail): ?>
            <a href="mailto:<?php echo e($supportEmail); ?>" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--email"><i class="fas fa-envelope"></i></span>
                <strong><?php echo e($channelsCopy['email']['title']); ?></strong>
                <p><?php echo e($channelsCopy['email']['desc']); ?></p>
                <span class="sana-ct-channel__info"><?php echo e($supportEmail); ?></span>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['email']['action']); ?> <i class="fas fa-arrow-left"></i></span>
            </a>
            <?php endif; ?>

            <a href="<?php echo e($hasWhatsapp ? $whatsappUrl : '#contact-form'); ?>" <?php if($hasWhatsapp): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?> class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--chat"><i class="fas fa-comments"></i></span>
                <strong><?php echo e($channelsCopy['chat']['title']); ?></strong>
                <p><?php echo e($channelsCopy['chat']['desc']); ?></p>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['chat']['action']); ?> <i class="fas fa-arrow-left"></i></span>
            </a>

            <div class="sana-ct-channel sana-reveal <?php echo e(!$hasSocials ? 'is-disabled' : ''); ?>">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--social"><i class="fas fa-share-nodes"></i></span>
                <strong><?php echo e($channelsCopy['social']['title']); ?></strong>
                <p><?php echo e($channelsCopy['social']['desc']); ?></p>
                <?php if($hasSocials): ?>
                <div class="sana-ct-socials">
                    <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo e($social['label']); ?>">
                        <i class="<?php echo e($social['icon']); ?>"></i>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['social']['action']); ?></span>
            </div>

            <a href="<?php echo e(route('public.help')); ?>" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--help"><i class="fas fa-circle-question"></i></span>
                <strong><?php echo e($channelsCopy['help']['title']); ?></strong>
                <p><?php echo e($channelsCopy['help']['desc']); ?></p>
                <span class="sana-ct-channel__btn"><?php echo e($channelsCopy['help']['action']); ?> <i class="fas fa-arrow-left"></i></span>
            </a>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft" id="contact-form">
    <div class="sana-container">
        <div class="sana-ct-form-wrap">
            <div class="sana-reveal">
                <span class="sana-head__eyebrow"><?php echo e($brand); ?></span>
                <h2 class="sana-head__title" style="text-align:right;margin-bottom:8px">
                    <?php echo e(__('sana_contact.categories_title')); ?> <span class="hl"><?php echo e(__('sana_contact.categories_highlight')); ?></span>
                </h2>
                <p class="sana-head__sub" style="margin:0 0 20px;text-align:right;max-width:none"><?php echo e(__('sana_contact.categories_sub')); ?></p>
                <div class="sana-ct-categories">
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                            class="sana-ct-cat"
                            :class="{ 'is-active': category === '<?php echo e($cat['key']); ?>' }"
                            @click="category = '<?php echo e($cat['key']); ?>'; document.getElementById('subject').value = '<?php echo e($cat['subject']); ?>'">
                        <i class="fas <?php echo e($cat['icon']); ?>"></i>
                        <span><?php echo e($cat['label']); ?></span>
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="sana-ct-form-card sana-reveal">
                <div class="sana-head" style="margin-bottom:24px;text-align:right">
                    <h2 class="sana-head__title" style="font-size:1.35rem">
                        <?php echo e(__('sana_contact.form_title')); ?> <span class="hl"><?php echo e(__('sana_contact.form_highlight')); ?></span>
                    </h2>
                    <p class="sana-head__sub" style="margin:8px 0 0;text-align:right;max-width:none"><?php echo e(__('sana_contact.form_sub')); ?></p>
                </div>

                <?php if(session('success')): ?>
                <div class="sana-ct-alert" role="status">
                    <i class="fas fa-circle-check"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
                <?php endif; ?>

                <form method="post" action="<?php echo e(route('public.contact.store')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="sana-ct-field">
                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255" placeholder=" " class="<?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="name">
                        <label for="name"><?php echo e($fields['name']); ?> *</label>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sana-ct-field__err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="sana-ct-form-row">
                        <div class="sana-ct-field">
                            <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required maxlength="255" placeholder=" " dir="ltr" class="<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="email">
                            <label for="email"><?php echo e($fields['email']); ?> *</label>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sana-ct-field__err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="sana-ct-field">
                            <input type="tel" name="phone" id="phone" value="<?php echo e(old('phone')); ?>" maxlength="20" placeholder=" " dir="ltr" class="<?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="tel">
                            <label for="phone"><?php echo e($fields['phone']); ?> <?php echo e($fields['phone_optional']); ?></label>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sana-ct-field__err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="sana-ct-field">
                        <input type="text" name="subject" id="subject" value="<?php echo e($prefillSubject); ?>" required maxlength="255" placeholder=" " class="<?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <label for="subject"><?php echo e($fields['subject']); ?> *</label>
                        <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sana-ct-field__err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="sana-ct-field">
                        <textarea name="message" id="message" required maxlength="5000" placeholder=" " class="<?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('message')); ?></textarea>
                        <label for="message"><?php echo e($fields['message']); ?> *</label>
                        <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="sana-ct-field__err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="sana-btn sana-btn--purple sana-ct-submit">
                        <i class="fas fa-paper-plane"></i> <?php echo e(__('sana_contact.form_submit')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title"><?php echo e(__('sana_contact.response_title')); ?> <span class="hl"><?php echo e(__('sana_contact.response_highlight')); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('sana_contact.response_sub')); ?></p>
        </div>
        <div class="sana-ct-response">
            <?php $__currentLoopData = $responseCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-ct-response__card sana-reveal">
                <i class="fas <?php echo e($card['icon']); ?>"></i>
                <strong><?php echo e($card['title']); ?></strong>
                <em><?php echo e($card['value']); ?></em>
                <span><?php echo e($card['desc']); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
            <h2 class="sana-head__title"><?php echo e(__('sana_contact.faq_title')); ?> <span class="hl"><?php echo e(__('sana_contact.faq_highlight')); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('sana_contact.faq_sub')); ?></p>
        </div>
        <div class="sana-faq sana-reveal" id="sana-faq" style="max-width:720px;margin-inline:auto">
            <?php $__currentLoopData = $faqItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-faq-item <?php echo e($i === 0 ? 'is-open' : ''); ?>">
                <button type="button" class="sana-faq-q" aria-expanded="<?php echo e($i === 0 ? 'true' : 'false'); ?>">
                    <?php echo e($faq['q']); ?> <i class="fas fa-chevron-down"></i>
                </button>
                <div class="sana-faq-a"><?php echo e($faq['a']); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="text-center sana-reveal" style="margin-top:24px">
            <a href="<?php echo e(route('public.faq')); ?>" class="sana-btn sana-btn--outline-purple" style="display:inline-flex">
                <i class="fas fa-circle-question"></i> <?php echo e(__('public.faq_page_title')); ?>

            </a>
        </p>
    </div>
</section>


<?php if($address !== ''): ?>
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title"><?php echo e(__('sana_contact.location_title')); ?> <span class="hl"><?php echo e(__('sana_contact.location_highlight')); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e(__('sana_contact.location_sub')); ?></p>
        </div>
        <div class="sana-ct-location sana-reveal">
            <div class="sana-ct-location__info">
                <h3><i class="fas fa-building" style="color:var(--p)"></i> <?php echo e($brand); ?></h3>
                <div class="sana-ct-location__row">
                    <i class="fas fa-location-dot"></i>
                    <span><?php echo e($address); ?></span>
                </div>
                <?php if($addressNote !== ''): ?>
                <div class="sana-ct-location__row sana-ct-location__row--note">
                    <i class="fas fa-circle-info"></i>
                    <span><?php echo e($addressNote); ?></span>
                </div>
                <?php endif; ?>
                <div class="sana-ct-location__row">
                    <i class="fas fa-clock"></i>
                    <span><?php echo e($contact['support_hours_full']); ?> (<?php echo e($contact['timezone_label']); ?>)</span>
                </div>
                <?php if($hasPhone): ?>
                <div class="sana-ct-location__row">
                    <i class="fas fa-phone"></i>
                    <div>
                        <a href="tel:<?php echo e($phoneTel); ?>" dir="ltr" style="color:var(--p);font-weight:800;text-decoration:none"><?php echo e($supportPhone); ?></a>
                        <small class="sana-ct-location__hint"><?php echo e(__('sana_contact.service_scope_phone')); ?></small>
                    </div>
                </div>
                <?php endif; ?>
                <?php if($hasEmail): ?>
                <div class="sana-ct-location__row">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:<?php echo e($supportEmail); ?>" style="color:var(--p);font-weight:800;text-decoration:none"><?php echo e($supportEmail); ?></a>
                </div>
                <?php endif; ?>
            </div>
            <?php if($mapEmbed !== ''): ?>
            <div class="sana-ct-location__map">
                <iframe src="<?php echo e($mapEmbed); ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="موقع <?php echo e($brand); ?>"></iframe>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="sana-ct-final">
    <div class="sana-container sana-reveal">
        <div class="sana-ct-final__box">
            <h2><?php echo e(__('sana_contact.final_title')); ?></h2>
            <p><?php echo e(__('sana_contact.final_sub')); ?></p>
            <div class="sana-ct-final__actions">
                <?php if($hasWhatsapp): ?>
                <a href="<?php echo e($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="sana-btn sana-btn--wa sana-btn--lg">
                    <i class="fab fa-whatsapp"></i> <?php echo e(__('sana_contact.final_whatsapp')); ?>

                </a>
                <?php endif; ?>
                <a href="#contact-form" class="sana-btn sana-btn--yellow sana-btn--lg">
                    <i class="fas fa-paper-plane"></i> <?php echo e(__('sana_contact.final_form')); ?>

                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views/public/contact.blade.php ENDPATH**/ ?>
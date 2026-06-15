<?php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $p = fn (string $key) => str_replace(':brand', $brand, __('sana_pricing.'.$key));
    $v2 = __('sana_pricing.v2');
    $planMarketing = __('sana_pricing.plan_marketing');
    $comparisonFeatures = __('sana_pricing.comparison_features');
    $pricingFaq = __('sana_pricing.pricing_faq');
    $whyUpgrade = __('sana_pricing.why_upgrade');
    $currency = __('public.currency');

    $fmtStat = function (int $n): string {
        if ($n >= 100000) {
            return round($n / 1000) . 'K+';
        }
        if ($n >= 1000) {
            return number_format($n / 1000, 1, '.', '') . 'K+';
        }

        return number_format($n);
    };

    $realStudents = (int) ($stats['students'] ?? 0);
    $realCourses = (int) ($stats['courses'] ?? 0);
    $realCerts = (int) ($stats['certificates'] ?? 0);
    $realCompleted = (int) ($stats['completed'] ?? 0);
    $realReviews = (int) ($stats['reviews_count'] ?? 0);
    $realRating = $stats['avg_rating'] ?? null;

    $hasTrustStats = \App\Support\PublicTrustMetrics::hasTrustStats($stats ?? []);

    $plansJson = collect($planKeys)->map(function (string $key) use ($plans, $planMarketing) {
        $plan = $plans[$key] ?? [];
        $marketing = $planMarketing[$key] ?? [];
        $contactForPricing = \App\Services\StudentSubscriptionPlansService::requiresContactForPricing($plan);
        $monthlyPrice = \App\Services\StudentSubscriptionPlansService::publicMonthlyPrice($plan);

        if ($contactForPricing || $monthlyPrice === null || $monthlyPrice <= 0) {
            $contactForPricing = true;
            $monthlyPrice = null;
        }

        return [
            'key' => $key,
            'label' => $plan['label'] ?? $key,
            'monthlyPrice' => $monthlyPrice ?? 0,
            'contactForPricing' => $contactForPricing,
            'priceHint' => $plan['card_price_hint'] ?? '',
            'subtitle' => $plan['card_subtitle'] ?? '',
            'hours' => (int) ($plan['limits']['tutor_lesson_hours'] ?? 0),
            'featured' => (bool) ($marketing['featured'] ?? false),
            'bestValue' => (bool) ($marketing['best_value'] ?? false),
            'featuredLabel' => $marketing['featured_label'] ?? '',
            'bestValueLabel' => $marketing['best_value_label'] ?? '',
        ];
    })->values();

    $showBillingToggle = collect($planKeys)->contains(function (string $key) use ($plans) {
        $plan = $plans[$key] ?? [];
        if (\App\Services\StudentSubscriptionPlansService::requiresContactForPricing($plan)) {
            return false;
        }
        $monthlyPrice = \App\Services\StudentSubscriptionPlansService::publicMonthlyPrice($plan);

        return $monthlyPrice !== null && $monthlyPrice > 0;
    });

    $contactUrl = route('public.contact');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title><?php echo e($v2['hero_title'] ?? $p('meta_title')); ?> — <?php echo e($brand); ?></title>
    <meta name="description" content="<?php echo e($p('meta_description')); ?>">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="<?php echo e(url('/pricing')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('partials.rtl-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.courses-catalog-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('landing.sana.pricing-theme', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page"
      x-data="sanaPricingPage({
          plans: <?php echo \Illuminate\Support\Js::from($plansJson)->toHtml() ?>,
          currency: <?php echo \Illuminate\Support\Js::from($currency)->toHtml() ?>,
          contactUrl: <?php echo \Illuminate\Support\Js::from($contactUrl)->toHtml() ?>,
          showBillingToggle: <?php echo json_encode($showBillingToggle, 15, 512) ?>,
          labels: <?php echo \Illuminate\Support\Js::from([
              'perMonth' => $v2['per_month'],
              'billedQuarterly' => $v2['billed_quarterly'],
              'billedYearly' => $v2['billed_yearly'],
              'equivalentMonthly' => $v2['equivalent_monthly'],
              'contactPrice' => $v2['contact_price'],
              'contactSub' => $v2['contact_price_sub'],
              'contactCta' => $v2['contact_cta'],
              'pricingNote' => $v2['pricing_note'],
          ])->toHtml() ?>,
      })">

<div id="sana-scroll-progress"></div>
<?php echo $__env->make('landing.sana.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-pricing-page">


<section class="sana-prx-hero">
    <div class="sana-container sana-prx-hero__inner sana-reveal">
        <span class="sana-prx-hero__eyebrow"><i class="fas fa-sparkles"></i> <?php echo e($v2['hero_eyebrow']); ?></span>
        <h1 class="sana-prx-hero__title">
            <?php echo e($v2['hero_title']); ?>

            <span class="hl"><?php echo e($v2['hero_highlight']); ?></span>
        </h1>
        <p class="sana-prx-hero__sub"><?php echo e($v2['hero_sub']); ?></p>
        <div class="sana-prx-hero__actions">
            <a href="<?php echo e(\App\Support\PublicSiteCta::assessmentUrl()); ?>" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-clipboard-check"></i> <?php echo e(__('public.cta_assessment_free')); ?></a>
            <a href="#plans" class="sana-btn sana-btn--white-outline sana-btn--lg"><i class="fas fa-layer-group"></i> <?php echo e($v2['hero_cta_primary']); ?></a>
        </div>
        <?php if($hasTrustStats): ?>
        <div class="sana-prx-trust">
            <?php if($realReviews > 0 && $realRating): ?>
            <div class="sana-prx-trust__item">
                <i class="fas fa-star"></i>
                <strong><?php echo e($realRating); ?>/5</strong>
                <span><?php echo e($v2['trust_rating']); ?></span>
            </div>
            <?php endif; ?>
            <?php if($realStudents > 0): ?>
            <div class="sana-prx-trust__item">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="<?php echo e(min($realStudents, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($realStudents)); ?></strong>
                <span><?php echo e($v2['trust_students']); ?></span>
            </div>
            <?php endif; ?>
            <?php if($realCerts > 0): ?>
            <div class="sana-prx-trust__item">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="<?php echo e(min($realCerts, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($realCerts)); ?></strong>
                <span><?php echo e($v2['trust_certificates']); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="sana-prx-trust">
            <div class="sana-prx-trust__item">
                <i class="fas fa-comments"></i>
                <strong><?php echo e($v2['launch_trust_pricing']); ?></strong>
                <span><?php echo e($v2['launch_trust_sub']); ?></span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-layer-group"></i>
                <strong><?php echo e($v2['launch_trust_flexible']); ?></strong>
                <span><?php echo e($brand); ?></span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-chalkboard-user"></i>
                <strong><?php echo e($v2['launch_trust_tutors']); ?></strong>
                <span><?php echo e($brand); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>


<section class="sana-section" id="plans">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <span class="sana-head__eyebrow"><?php echo e($brand); ?></span>
            <h2 class="sana-head__title"><?php echo e($v2['plans_title']); ?> <span class="hl"><?php echo e($v2['plans_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['plans_sub']); ?></p>
            <?php if (! ($showBillingToggle)): ?>
            <p class="sana-prx-pricing-note sana-reveal"><?php echo e($v2['pricing_note']); ?></p>
            <?php endif; ?>
        </div>

        <?php if($showBillingToggle): ?>
        <div class="sana-prx-billing sana-reveal">
            <div class="sana-prx-billing__save" x-show="billing === 'yearly'" x-cloak>
                <i class="fas fa-bolt"></i>
                <span><?php echo e($v2['billing_save_yearly']); ?> — <?php echo e($v2['billing_yearly_hint']); ?></span>
            </div>
            <div class="sana-prx-billing__save" x-show="billing === 'quarterly'" x-cloak style="background:linear-gradient(135deg,rgba(109,40,217,0.12),rgba(109,40,217,0.05));border-color:rgba(109,40,217,0.25);color:var(--p-dark)">
                <i class="fas fa-percent"></i>
                <span><?php echo e($v2['billing_save_quarterly']); ?></span>
            </div>
            <div class="sana-prx-toggle" role="group" aria-label="<?php echo e($v2['billing_title']); ?>">
                <button type="button" :class="{ 'is-active': billing === 'monthly' }" @click="billing = 'monthly'"><?php echo e($v2['billing_monthly']); ?></button>
                <button type="button" :class="{ 'is-active': billing === 'quarterly' }" @click="billing = 'quarterly'">
                    <?php echo e($v2['billing_quarterly']); ?>

                    <small><?php echo e($v2['billing_save_quarterly']); ?></small>
                </button>
                <button type="button" :class="{ 'is-active': billing === 'yearly' }" @click="billing = 'yearly'">
                    <?php echo e($v2['billing_yearly']); ?>

                    <small><?php echo e($v2['billing_save_yearly']); ?></small>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <div class="sana-prx-plans">
            <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $plan = $plans[$key] ?? [];
                $marketing = $planMarketing[$key] ?? [];
                $isFeatured = !empty($marketing['featured']);
                $isBestValue = !empty($marketing['best_value']);
                $contactPlan = \App\Services\StudentSubscriptionPlansService::requiresContactForPricing($plan)
                    || \App\Services\StudentSubscriptionPlansService::publicMonthlyPrice($plan) === null
                    || (float) ($plan['price'] ?? 0) <= 0;
            ?>
            <article class="sana-prx-plan sana-reveal <?php echo e($isFeatured ? 'is-featured' : ''); ?>"
                     :data-plan="<?php echo e(json_encode($key)); ?>">
                <?php if($isFeatured): ?>
                    <span class="sana-prx-plan__ribbon"><?php echo e($marketing['featured_label'] ?? 'الأكثر طلباً'); ?></span>
                <?php elseif($isBestValue): ?>
                    <span class="sana-prx-plan__ribbon sana-prx-plan__ribbon--value"><?php echo e($marketing['best_value_label'] ?? 'أفضل قيمة'); ?></span>
                <?php endif; ?>
                <div class="sana-prx-plan__head">
                    <div class="sana-prx-plan__icon"><i class="fas <?php echo e($marketing['icon'] ?? 'fa-layer-group'); ?>"></i></div>
                    <p class="sana-prx-plan__tagline"><?php echo e($marketing['tagline'] ?? ''); ?></p>
                    <h3 class="sana-prx-plan__name"><?php echo e($plan['label'] ?? $key); ?></h3>
                    <p class="sana-prx-plan__desc"><?php echo e($marketing['description'] ?? ($plan['card_subtitle'] ?? '')); ?></p>
                </div>
                <div class="sana-prx-plan__price-block">
                    <div class="sana-prx-plan__price">
                        <strong class="<?php echo e($contactPlan ? 'is-contact-price' : ''); ?>" x-text="formatPrice('<?php echo e($key); ?>')"><?php echo e($contactPlan ? $v2['contact_price'] : '—'); ?></strong>
                        <span x-show="!isContactPlan('<?php echo e($key); ?>')" x-cloak x-text="periodSuffix()"><?php echo e($v2['per_month']); ?></span>
                    </div>
                    <p class="sana-prx-plan__contact-sub" x-show="isContactPlan('<?php echo e($key); ?>')" x-cloak><?php echo e($v2['contact_price_sub']); ?></p>
                    <?php if(!empty($plan['card_price_hint'])): ?>
                    <p class="sana-prx-plan__hint"><?php echo e($plan['card_price_hint']); ?></p>
                    <?php endif; ?>
                    <p class="sana-prx-plan__equiv" x-show="billing !== 'monthly' && !isContactPlan('<?php echo e($key); ?>')" x-cloak x-text="equivalentText('<?php echo e($key); ?>')"></p>
                    <p class="sana-prx-plan__period" x-show="!isContactPlan('<?php echo e($key); ?>')" x-cloak x-text="billingNote()"></p>
                </div>
                <div class="sana-prx-plan__body">
                    <ul class="sana-prx-plan__benefits">
                        <?php $__currentLoopData = $marketing['benefits'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><i class="fas fa-check"></i><span><?php echo e($benefit); ?></span></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <?php if($contactPlan): ?>
                    <a href="<?php echo e(route('public.contact')); ?>?subject=<?php echo e(urlencode('استفسار عن باقة: '.($plan['label'] ?? $key))); ?>"
                       class="sana-btn <?php echo e($isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple'); ?> sana-prx-plan__cta">
                        <?php echo e($v2['contact_cta']); ?>

                        <i class="fas fa-comments"></i>
                    </a>
                    <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>?plan=<?php echo e($key); ?>"
                       class="sana-btn <?php echo e($isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple'); ?> sana-prx-plan__cta">
                        <?php echo e($marketing['cta'] ?? 'اشترك الآن'); ?>

                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="sana-section sana-section--soft" id="compare">
    <div class="sana-container sana-reveal">
        <div class="sana-head sana-head--center" style="margin-bottom:32px">
            <h2 class="sana-head__title"><?php echo e($v2['compare_title']); ?> <span class="hl"><?php echo e($v2['compare_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['compare_sub']); ?></p>
        </div>
        <div class="sana-prx-compare-wrap">
            <table class="sana-prx-compare">
                <thead>
                    <tr>
                        <th>الميزة</th>
                        <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isFeatured = !empty($planMarketing[$key]['featured']); ?>
                        <th class="<?php echo e($isFeatured ? 'is-featured-col' : ''); ?>"><?php echo e($plans[$key]['label'] ?? $key); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $comparisonFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <span class="feat-label">
                                <i class="fas <?php echo e($row['icon']); ?>"></i>
                                <?php echo e($row['label']); ?>

                            </span>
                        </td>
                        <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $val = $row['plans'][$key] ?? false;
                            $isFeatured = !empty($planMarketing[$key]['featured']);
                        ?>
                        <td class="<?php echo e($isFeatured ? 'is-featured-col' : ''); ?>">
                            <?php if($val === true): ?>
                                <i class="fas fa-check-circle cell-yes" aria-label="متاح"></i>
                            <?php elseif($val === false): ?>
                                <i class="fas fa-minus cell-no" aria-label="غير متاح"></i>
                            <?php else: ?>
                                <span class="cell-text"><?php echo e($val); ?></span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</section>


<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e($v2['why_title']); ?> <span class="hl"><?php echo e($v2['why_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['why_sub']); ?></p>
        </div>
        <div class="sana-prx-why">
            <?php $__currentLoopData = $whyUpgrade; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="sana-prx-why__card sana-reveal">
                <div class="sana-prx-why__icon"><i class="fas <?php echo e($item['icon']); ?>"></i></div>
                <strong><?php echo e($item['title']); ?></strong>
                <p><?php echo e($item['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<?php if($hasTrustStats): ?>
<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e($v2['metrics_title']); ?> <span class="hl"><?php echo e($v2['metrics_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['metrics_sub']); ?></p>
        </div>
        <div class="sana-prx-metrics">
            <?php if($realStudents > 0): ?>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="<?php echo e(min($realStudents, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($realStudents)); ?></strong>
                <span>طالب مسجّل</span>
            </div>
            <?php endif; ?>
            <?php if($realCompleted > 0): ?>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-book-open"></i>
                <strong data-sana-counter="<?php echo e(min($realCompleted, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($realCompleted)); ?></strong>
                <span>كورس مكتمل</span>
            </div>
            <?php endif; ?>
            <?php if($realCerts > 0): ?>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="<?php echo e(min($realCerts, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($realCerts)); ?></strong>
                <span>شهادة صادرة</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($testimonials->isNotEmpty()): ?>
<section class="sana-section" id="testimonials">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e($v2['testimonials_title']); ?> <span class="hl"><?php echo e($v2['testimonials_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['testimonials_sub']); ?></p>
        </div>
        <div class="sana-test-m">
            <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e(Str::limit(strip_tags($t->body ?? ''), 180)); ?>»</p>
                <div class="stars"><?php for($s = 0; $s < 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author">
                    <?php if($t->isImageType() && $t->publicImageUrl()): ?>
                        <img src="<?php echo e($t->publicImageUrl()); ?>" alt="">
                    <?php else: ?>
                        <span class="av"><?php echo e($t->author_name ? mb_substr($t->author_name, 0, 1) : '؟'); ?></span>
                    <?php endif; ?>
                    <div>
                        <strong><?php echo e($t->author_name ?? 'عميل'); ?></strong>
                        <?php if($t->role_label): ?><small><?php echo e($t->role_label); ?></small><?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="sana-section sana-section--soft" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title"><?php echo e($v2['faq_title']); ?> <span class="hl"><?php echo e($v2['faq_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['faq_sub']); ?></p>
        </div>
        <?php
            $faqChar = public_static_exists('img/sanua/landing-hero-boy.png')
                ? public_static_url('img/sanua/landing-hero-boy.png')
                : public_static_url('img/sanua/hero-boy.png');
        ?>
        <div class="sana-faq-m sana-reveal">
            <div class="sana-faq-m__visual">
                <img src="<?php echo e($faqChar); ?>" alt="">
                <span class="bubble bubble--1">💳</span>
                <span class="bubble bubble--2">✅</span>
            </div>
            <div class="sana-faq" id="sana-faq">
                <?php $__currentLoopData = $pricingFaq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sana-faq-item <?php echo e($i === 0 ? 'is-open' : ''); ?>">
                    <button type="button" class="sana-faq-q" aria-expanded="<?php echo e($i === 0 ? 'true' : 'false'); ?>">
                        <?php echo e($faq['q']); ?> <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="sana-faq-a"><?php echo e($faq['a']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>


<section class="sana-prx-final">
    <div class="sana-container sana-reveal">
        <div class="sana-prx-final__box">
            <h2><?php echo e($v2['final_title']); ?></h2>
            <p><?php echo e(str_replace(':brand', $brand, $v2['final_sub'])); ?></p>
            <div class="sana-prx-final__actions">
                <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow sana-btn--lg">
                    <?php echo e($v2['final_cta']); ?> <i class="fas fa-arrow-left"></i>
                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--ghost-light sana-btn--lg">
                    <?php echo e($v2['final_secondary']); ?> <i class="fas fa-comments"></i>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

<?php echo $__env->make('landing.sana.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
function sanaPricingPage(config) {
    var planMap = {};
    (config.plans || []).forEach(function (p) { planMap[p.key] = p; });

    return {
        billing: 'monthly',
        currency: config.currency || 'ر.س',
        labels: config.labels || {},

        planPrice(key) {
            if (this.isContactPlan(key)) return 0;
            var plan = planMap[key];
            var m = plan.monthlyPrice || 0;
            if (this.billing === 'monthly') return m;
            if (this.billing === 'quarterly') return Math.round(m * 3 * 0.9);
            return Math.round(m * 12 * 0.8);
        },

        isContactPlan(key) {
            var plan = planMap[key];
            if (!plan) return true;
            if (plan.contactForPricing) return true;
            return !plan.monthlyPrice || plan.monthlyPrice <= 0;
        },

        monthlyEquivalent(key) {
            if (this.isContactPlan(key)) return 0;
            var plan = planMap[key];
            var m = plan.monthlyPrice || 0;
            if (this.billing === 'quarterly') return Math.round(m * 0.9);
            if (this.billing === 'yearly') return Math.round(m * 0.8);
            return m;
        },

        formatPrice(key) {
            if (this.isContactPlan(key)) {
                return this.labels.contactPrice || 'تواصل لمعرفة السعر';
            }
            return this.planPrice(key).toLocaleString('ar-EG') + ' ' + this.currency;
        },

        periodSuffix() {
            if (this.billing === 'monthly') return this.labels.perMonth || '/ شهر';
            if (this.billing === 'quarterly') return '/ 3 أشهر';
            return '/ سنة';
        },

        billingNote() {
            if (this.billing === 'quarterly') return this.labels.billedQuarterly || '';
            if (this.billing === 'yearly') return this.labels.billedYearly || '';
            return '';
        },

        equivalentText(key) {
            var eq = this.monthlyEquivalent(key);
            var tpl = this.labels.equivalentMonthly || 'يعادل :amount ر.س / شهر';
            return tpl.replace(':amount', eq.toLocaleString('ar-EG'));
        },
    };
}
</script>

<?php echo $__env->make('landing.sana.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.pwa-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sana\resources\views\public\pricing.blade.php ENDPATH**/ ?>
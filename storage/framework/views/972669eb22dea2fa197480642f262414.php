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

    $displayStudents = max($stats['students'] ?? 0, 1200);
    $displayCourses = max($stats['courses'] ?? 0, 50);
    $displayCerts = max($stats['certificates'] ?? 0, 100);
    $displayCompleted = max($stats['completed'] ?? 0, 500);
    $displayRating = $stats['avg_rating'] ?? 4.9;
    $displaySatisfaction = $stats['satisfaction'] ?? 98;

    $plansJson = collect($planKeys)->map(function (string $key) use ($plans, $planMarketing) {
        $plan = $plans[$key] ?? [];
        $marketing = $planMarketing[$key] ?? [];

        return [
            'key' => $key,
            'label' => $plan['label'] ?? $key,
            'monthlyPrice' => (float) ($plan['price'] ?? 0),
            'subtitle' => $plan['card_subtitle'] ?? '',
            'hours' => (int) ($plan['limits']['tutor_lesson_hours'] ?? 0),
            'featured' => (bool) ($marketing['featured'] ?? false),
            'bestValue' => (bool) ($marketing['best_value'] ?? false),
            'featuredLabel' => $marketing['featured_label'] ?? '',
            'bestValueLabel' => $marketing['best_value_label'] ?? '',
        ];
    })->values();
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
          labels: <?php echo \Illuminate\Support\Js::from([
              'perMonth' => $v2['per_month'],
              'billedQuarterly' => $v2['billed_quarterly'],
              'billedYearly' => $v2['billed_yearly'],
              'equivalentMonthly' => $v2['equivalent_monthly'],
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
            <a href="#plans" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-rocket"></i> <?php echo e($v2['hero_cta_primary']); ?></a>
            <a href="#compare" class="sana-btn sana-btn--white-outline sana-btn--lg"><i class="fas fa-table-columns"></i> <?php echo e($v2['hero_cta_secondary']); ?></a>
        </div>
        <div class="sana-prx-trust">
            <div class="sana-prx-trust__item">
                <i class="fas fa-star"></i>
                <strong><?php echo e($displayRating); ?>/5</strong>
                <span><?php echo e($v2['trust_rating']); ?></span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="<?php echo e(min($displayStudents, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayStudents)); ?></strong>
                <span><?php echo e($v2['trust_students']); ?></span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="<?php echo e(min($displayCerts, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayCerts)); ?></strong>
                <span><?php echo e($v2['trust_certificates']); ?></span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-heart"></i>
                <strong data-sana-counter="<?php echo e($displaySatisfaction); ?>" data-sana-suffix="%"><?php echo e($displaySatisfaction); ?>%</strong>
                <span><?php echo e($v2['trust_satisfaction']); ?></span>
            </div>
        </div>
    </div>
</section>


<section class="sana-section" id="plans">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <span class="sana-head__eyebrow"><?php echo e($brand); ?></span>
            <h2 class="sana-head__title"><?php echo e($v2['plans_title']); ?> <span class="hl"><?php echo e($v2['plans_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['plans_sub']); ?></p>
        </div>

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

        <div class="sana-prx-plans">
            <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $plan = $plans[$key] ?? [];
                $marketing = $planMarketing[$key] ?? [];
                $isFeatured = !empty($marketing['featured']);
                $isBestValue = !empty($marketing['best_value']);
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
                        <strong x-text="formatPrice('<?php echo e($key); ?>')">0</strong>
                        <span x-text="periodSuffix()"><?php echo e($v2['per_month']); ?></span>
                    </div>
                    <p class="sana-prx-plan__equiv" x-show="billing !== 'monthly'" x-cloak x-text="equivalentText('<?php echo e($key); ?>')"></p>
                    <p class="sana-prx-plan__period" x-text="billingNote()"></p>
                </div>
                <div class="sana-prx-plan__body">
                    <ul class="sana-prx-plan__benefits">
                        <?php $__currentLoopData = $marketing['benefits'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><i class="fas fa-check"></i><span><?php echo e($benefit); ?></span></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <a href="<?php echo e(route('register')); ?>?plan=<?php echo e($key); ?>"
                       class="sana-btn <?php echo e($isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple'); ?> sana-prx-plan__cta">
                        <?php echo e($marketing['cta'] ?? 'اشترك الآن'); ?>

                        <i class="fas fa-arrow-left"></i>
                    </a>
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


<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e($v2['metrics_title']); ?> <span class="hl"><?php echo e($v2['metrics_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['metrics_sub']); ?></p>
        </div>
        <div class="sana-prx-metrics">
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="<?php echo e(min($displayStudents, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayStudents)); ?></strong>
                <span>طالب مسجّل</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-book-open"></i>
                <strong data-sana-counter="<?php echo e(min($displayCompleted, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayCompleted)); ?></strong>
                <span>كورس مكتمل</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="<?php echo e(min($displayCerts, 999999)); ?>" data-sana-suffix="+"><?php echo e($fmtStat($displayCerts)); ?></strong>
                <span>شهادة صادرة</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-face-smile"></i>
                <strong data-sana-counter="<?php echo e($displaySatisfaction); ?>" data-sana-suffix="%"><?php echo e($displaySatisfaction); ?>%</strong>
                <span>رضا الطلاب</span>
            </div>
        </div>
    </div>
</section>


<section class="sana-section" id="testimonials">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title"><?php echo e($v2['testimonials_title']); ?> <span class="hl"><?php echo e($v2['testimonials_highlight']); ?></span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub"><?php echo e($v2['testimonials_sub']); ?></p>
        </div>
        <div class="sana-test-m">
            <?php $__empty_1 = true; $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php $__currentLoopData = [
                ['n' => 'أم ليان', 'r' => 'وليّة أمر · تحسّن ملحوظ في الرياضيات', 't' => 'بعد الاشتراك في الباقة القياسية، ابنتي أصبحت تتفاعل أكثر ونتائجها تحسّنت خلال شهرين فقط.'],
                ['n' => 'محمد', 'r' => 'طالب · شهادة إتمام كورس', 't' => 'المنصة سهلة والمعلّمون ممتازون. حصلت على شهادتي بعد إنهاء الكورس — تجربة تستحق.'],
                ['n' => 'أ. خالد', 'r' => 'وليّ أمر · متابعة واضحة', 't' => 'لوحة المتابعة تعطيني صورة حقيقية عن تقدّم أبنائي. أفضل استثمار في تعليمهم هذا العام.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«<?php echo e($d['t']); ?>»</p>
                <div class="stars"><?php for($s = 0; $s < 5; $s++): ?><i class="fas fa-star"></i><?php endfor; ?></div>
                <div class="author">
                    <span class="av"><?php echo e(mb_substr($d['n'], 0, 1)); ?></span>
                    <div><strong><?php echo e($d['n']); ?></strong><small><?php echo e($d['r']); ?></small></div>
                </div>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>


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
            var plan = planMap[key];
            if (!plan) return 0;
            var m = plan.monthlyPrice || 0;
            if (this.billing === 'monthly') return m;
            if (this.billing === 'quarterly') return Math.round(m * 3 * 0.9);
            return Math.round(m * 12 * 0.8);
        },

        monthlyEquivalent(key) {
            var plan = planMap[key];
            if (!plan) return 0;
            var m = plan.monthlyPrice || 0;
            if (this.billing === 'quarterly') return Math.round(m * 0.9);
            if (this.billing === 'yearly') return Math.round(m * 0.8);
            return m;
        },

        formatPrice(key) {
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
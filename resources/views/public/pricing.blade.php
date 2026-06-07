@php
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
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $v2['hero_title'] ?? $p('meta_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $p('meta_description') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/pricing') }}">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.pricing-theme')
</head>
<body class="sana-home sana-courses-page"
      x-data="sanaPricingPage({
          plans: @js($plansJson),
          currency: @js($currency),
          labels: @js([
              'perMonth' => $v2['per_month'],
              'billedQuarterly' => $v2['billed_quarterly'],
              'billedYearly' => $v2['billed_yearly'],
              'equivalentMonthly' => $v2['equivalent_monthly'],
          ]),
      })">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-pricing-page">

{{-- §1 Hero + trust --}}
<section class="sana-prx-hero">
    <div class="sana-container sana-prx-hero__inner sana-reveal">
        <span class="sana-prx-hero__eyebrow"><i class="fas fa-sparkles"></i> {{ $v2['hero_eyebrow'] }}</span>
        <h1 class="sana-prx-hero__title">
            {{ $v2['hero_title'] }}
            <span class="hl">{{ $v2['hero_highlight'] }}</span>
        </h1>
        <p class="sana-prx-hero__sub">{{ $v2['hero_sub'] }}</p>
        <div class="sana-prx-hero__actions">
            <a href="#plans" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-rocket"></i> {{ $v2['hero_cta_primary'] }}</a>
            <a href="#compare" class="sana-btn sana-btn--white-outline sana-btn--lg"><i class="fas fa-table-columns"></i> {{ $v2['hero_cta_secondary'] }}</a>
        </div>
        <div class="sana-prx-trust">
            <div class="sana-prx-trust__item">
                <i class="fas fa-star"></i>
                <strong>{{ $displayRating }}/5</strong>
                <span>{{ $v2['trust_rating'] }}</span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="{{ min($displayStudents, 999999) }}" data-sana-suffix="+">{{ $fmtStat($displayStudents) }}</strong>
                <span>{{ $v2['trust_students'] }}</span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="{{ min($displayCerts, 999999) }}" data-sana-suffix="+">{{ $fmtStat($displayCerts) }}</strong>
                <span>{{ $v2['trust_certificates'] }}</span>
            </div>
            <div class="sana-prx-trust__item">
                <i class="fas fa-heart"></i>
                <strong data-sana-counter="{{ $displaySatisfaction }}" data-sana-suffix="%">{{ $displaySatisfaction }}%</strong>
                <span>{{ $v2['trust_satisfaction'] }}</span>
            </div>
        </div>
    </div>
</section>

{{-- §2 Toggle + §3 Cards --}}
<section class="sana-section" id="plans">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <span class="sana-head__eyebrow">{{ $brand }}</span>
            <h2 class="sana-head__title">{{ $v2['plans_title'] }} <span class="hl">{{ $v2['plans_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['plans_sub'] }}</p>
        </div>

        <div class="sana-prx-billing sana-reveal">
            <div class="sana-prx-billing__save" x-show="billing === 'yearly'" x-cloak>
                <i class="fas fa-bolt"></i>
                <span>{{ $v2['billing_save_yearly'] }} — {{ $v2['billing_yearly_hint'] }}</span>
            </div>
            <div class="sana-prx-billing__save" x-show="billing === 'quarterly'" x-cloak style="background:linear-gradient(135deg,rgba(109,40,217,0.12),rgba(109,40,217,0.05));border-color:rgba(109,40,217,0.25);color:var(--p-dark)">
                <i class="fas fa-percent"></i>
                <span>{{ $v2['billing_save_quarterly'] }}</span>
            </div>
            <div class="sana-prx-toggle" role="group" aria-label="{{ $v2['billing_title'] }}">
                <button type="button" :class="{ 'is-active': billing === 'monthly' }" @click="billing = 'monthly'">{{ $v2['billing_monthly'] }}</button>
                <button type="button" :class="{ 'is-active': billing === 'quarterly' }" @click="billing = 'quarterly'">
                    {{ $v2['billing_quarterly'] }}
                    <small>{{ $v2['billing_save_quarterly'] }}</small>
                </button>
                <button type="button" :class="{ 'is-active': billing === 'yearly' }" @click="billing = 'yearly'">
                    {{ $v2['billing_yearly'] }}
                    <small>{{ $v2['billing_save_yearly'] }}</small>
                </button>
            </div>
        </div>

        <div class="sana-prx-plans">
            @foreach($planKeys as $key)
            @php
                $plan = $plans[$key] ?? [];
                $marketing = $planMarketing[$key] ?? [];
                $isFeatured = !empty($marketing['featured']);
                $isBestValue = !empty($marketing['best_value']);
            @endphp
            <article class="sana-prx-plan sana-reveal {{ $isFeatured ? 'is-featured' : '' }}"
                     :data-plan="{{ json_encode($key) }}">
                @if($isFeatured)
                    <span class="sana-prx-plan__ribbon">{{ $marketing['featured_label'] ?? 'الأكثر طلباً' }}</span>
                @elseif($isBestValue)
                    <span class="sana-prx-plan__ribbon sana-prx-plan__ribbon--value">{{ $marketing['best_value_label'] ?? 'أفضل قيمة' }}</span>
                @endif
                <div class="sana-prx-plan__head">
                    <div class="sana-prx-plan__icon"><i class="fas {{ $marketing['icon'] ?? 'fa-layer-group' }}"></i></div>
                    <p class="sana-prx-plan__tagline">{{ $marketing['tagline'] ?? '' }}</p>
                    <h3 class="sana-prx-plan__name">{{ $plan['label'] ?? $key }}</h3>
                    <p class="sana-prx-plan__desc">{{ $marketing['description'] ?? ($plan['card_subtitle'] ?? '') }}</p>
                </div>
                <div class="sana-prx-plan__price-block">
                    <div class="sana-prx-plan__price">
                        <strong x-text="formatPrice('{{ $key }}')">0</strong>
                        <span x-text="periodSuffix()">{{ $v2['per_month'] }}</span>
                    </div>
                    <p class="sana-prx-plan__equiv" x-show="billing !== 'monthly'" x-cloak x-text="equivalentText('{{ $key }}')"></p>
                    <p class="sana-prx-plan__period" x-text="billingNote()"></p>
                </div>
                <div class="sana-prx-plan__body">
                    <ul class="sana-prx-plan__benefits">
                        @foreach($marketing['benefits'] ?? [] as $benefit)
                        <li><i class="fas fa-check"></i><span>{{ $benefit }}</span></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}?plan={{ $key }}"
                       class="sana-btn {{ $isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple' }} sana-prx-plan__cta">
                        {{ $marketing['cta'] ?? 'اشترك الآن' }}
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

{{-- §4 Feature comparison --}}
<section class="sana-section sana-section--soft" id="compare">
    <div class="sana-container sana-reveal">
        <div class="sana-head sana-head--center" style="margin-bottom:32px">
            <h2 class="sana-head__title">{{ $v2['compare_title'] }} <span class="hl">{{ $v2['compare_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['compare_sub'] }}</p>
        </div>
        <div class="sana-prx-compare-wrap">
            <table class="sana-prx-compare">
                <thead>
                    <tr>
                        <th>الميزة</th>
                        @foreach($planKeys as $key)
                        @php $isFeatured = !empty($planMarketing[$key]['featured']); @endphp
                        <th class="{{ $isFeatured ? 'is-featured-col' : '' }}">{{ $plans[$key]['label'] ?? $key }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($comparisonFeatures as $row)
                    <tr>
                        <td>
                            <span class="feat-label">
                                <i class="fas {{ $row['icon'] }}"></i>
                                {{ $row['label'] }}
                            </span>
                        </td>
                        @foreach($planKeys as $key)
                        @php
                            $val = $row['plans'][$key] ?? false;
                            $isFeatured = !empty($planMarketing[$key]['featured']);
                        @endphp
                        <td class="{{ $isFeatured ? 'is-featured-col' : '' }}">
                            @if($val === true)
                                <i class="fas fa-check-circle cell-yes" aria-label="متاح"></i>
                            @elseif($val === false)
                                <i class="fas fa-minus cell-no" aria-label="غير متاح"></i>
                            @else
                                <span class="cell-text">{{ $val }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- §5 Why upgrade --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title">{{ $v2['why_title'] }} <span class="hl">{{ $v2['why_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['why_sub'] }}</p>
        </div>
        <div class="sana-prx-why">
            @foreach($whyUpgrade as $item)
            <div class="sana-prx-why__card sana-reveal">
                <div class="sana-prx-why__icon"><i class="fas {{ $item['icon'] }}"></i></div>
                <strong>{{ $item['title'] }}</strong>
                <p>{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §6 Success metrics --}}
<section class="sana-section sana-section--soft">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title">{{ $v2['metrics_title'] }} <span class="hl">{{ $v2['metrics_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['metrics_sub'] }}</p>
        </div>
        <div class="sana-prx-metrics">
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-user-graduate"></i>
                <strong data-sana-counter="{{ min($displayStudents, 999999) }}" data-sana-suffix="+">{{ $fmtStat($displayStudents) }}</strong>
                <span>طالب مسجّل</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-book-open"></i>
                <strong data-sana-counter="{{ min($displayCompleted, 999999) }}" data-sana-suffix="+">{{ $fmtStat($displayCompleted) }}</strong>
                <span>كورس مكتمل</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-certificate"></i>
                <strong data-sana-counter="{{ min($displayCerts, 999999) }}" data-sana-suffix="+">{{ $fmtStat($displayCerts) }}</strong>
                <span>شهادة صادرة</span>
            </div>
            <div class="sana-prx-metric sana-reveal">
                <i class="fas fa-face-smile"></i>
                <strong data-sana-counter="{{ $displaySatisfaction }}" data-sana-suffix="%">{{ $displaySatisfaction }}%</strong>
                <span>رضا الطلاب</span>
            </div>
        </div>
    </div>
</section>

{{-- §7 Testimonials --}}
<section class="sana-section" id="testimonials">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:36px">
            <h2 class="sana-head__title">{{ $v2['testimonials_title'] }} <span class="hl">{{ $v2['testimonials_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['testimonials_sub'] }}</p>
        </div>
        <div class="sana-test-m">
            @forelse($testimonials as $t)
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«{{ Str::limit(strip_tags($t->body ?? ''), 180) }}»</p>
                <div class="stars">@for($s = 0; $s < 5; $s++)<i class="fas fa-star"></i>@endfor</div>
                <div class="author">
                    @if($t->isImageType() && $t->publicImageUrl())
                        <img src="{{ $t->publicImageUrl() }}" alt="">
                    @else
                        <span class="av">{{ $t->author_name ? mb_substr($t->author_name, 0, 1) : '؟' }}</span>
                    @endif
                    <div>
                        <strong>{{ $t->author_name ?? 'عميل' }}</strong>
                        @if($t->role_label)<small>{{ $t->role_label }}</small>@endif
                    </div>
                </div>
            </article>
            @empty
            @foreach([
                ['n' => 'أم ليان', 'r' => 'وليّة أمر · تحسّن ملحوظ في الرياضيات', 't' => 'بعد الاشتراك في الباقة القياسية، ابنتي أصبحت تتفاعل أكثر ونتائجها تحسّنت خلال شهرين فقط.'],
                ['n' => 'محمد', 'r' => 'طالب · شهادة إتمام كورس', 't' => 'المنصة سهلة والمعلّمون ممتازون. حصلت على شهادتي بعد إنهاء الكورس — تجربة تستحق.'],
                ['n' => 'أ. خالد', 'r' => 'وليّ أمر · متابعة واضحة', 't' => 'لوحة المتابعة تعطيني صورة حقيقية عن تقدّم أبنائي. أفضل استثمار في تعليمهم هذا العام.'],
            ] as $d)
            <article class="sana-test-m__card sana-reveal">
                <div class="quote"><i class="fas fa-quote-right"></i></div>
                <p>«{{ $d['t'] }}»</p>
                <div class="stars">@for($s = 0; $s < 5; $s++)<i class="fas fa-star"></i>@endfor</div>
                <div class="author">
                    <span class="av">{{ mb_substr($d['n'], 0, 1) }}</span>
                    <div><strong>{{ $d['n'] }}</strong><small>{{ $d['r'] }}</small></div>
                </div>
            </article>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- §8 FAQ --}}
<section class="sana-section sana-section--soft" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title">{{ $v2['faq_title'] }} <span class="hl">{{ $v2['faq_highlight'] }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ $v2['faq_sub'] }}</p>
        </div>
        @php
            $faqChar = public_static_exists('img/sanua/landing-hero-boy.png')
                ? public_static_url('img/sanua/landing-hero-boy.png')
                : public_static_url('img/sanua/hero-boy.png');
        @endphp
        <div class="sana-faq-m sana-reveal">
            <div class="sana-faq-m__visual">
                <img src="{{ $faqChar }}" alt="">
                <span class="bubble bubble--1">💳</span>
                <span class="bubble bubble--2">✅</span>
            </div>
            <div class="sana-faq" id="sana-faq">
                @foreach($pricingFaq as $i => $faq)
                <div class="sana-faq-item {{ $i === 0 ? 'is-open' : '' }}">
                    <button type="button" class="sana-faq-q" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                        {{ $faq['q'] }} <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="sana-faq-a">{{ $faq['a'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- §9 Final CTA --}}
<section class="sana-prx-final">
    <div class="sana-container sana-reveal">
        <div class="sana-prx-final__box">
            <h2>{{ $v2['final_title'] }}</h2>
            <p>{{ str_replace(':brand', $brand, $v2['final_sub']) }}</p>
            <div class="sana-prx-final__actions">
                <a href="{{ route('register') }}" class="sana-btn sana-btn--yellow sana-btn--lg">
                    {{ $v2['final_cta'] }} <i class="fas fa-arrow-left"></i>
                </a>
                <a href="{{ route('public.contact') }}" class="sana-btn sana-btn--ghost-light sana-btn--lg">
                    {{ $v2['final_secondary'] }} <i class="fas fa-comments"></i>
                </a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')

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

@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
<style>[x-cloak]{display:none!important}</style>
</body>
</html>

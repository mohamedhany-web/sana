@php
    $faqs = [
        ['q' => __('public.home_faq_1_q'), 'a' => __('public.home_faq_1_a')],
        ['q' => __('public.home_faq_2_q'), 'a' => __('public.home_faq_2_a')],
        ['q' => __('public.home_faq_3_q'), 'a' => __('public.home_faq_3_a')],
        ['q' => __('public.home_faq_4_q'), 'a' => __('public.home_faq_4_a')],
        ['q' => __('public.home_faq_5_q'), 'a' => __('public.home_faq_5_a')],
    ];
    $faqChar = public_static_exists('img/sanua/landing-hero-boy.png')
        ? public_static_url('img/sanua/landing-hero-boy.png')
        : public_static_url('img/sanua/hero-boy.png');
@endphp
<section class="sana-section sana-section--white" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">{{ __('public.home_faq_title') }} <span class="hl">{{ __('public.home_faq_title_hl') }}</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-faq-m sana-reveal">
            <div class="sana-faq-m__visual">
                <img src="{{ $faqChar }}" alt="">
                <span class="bubble bubble--1">🤔</span>
                <span class="bubble bubble--2">💡</span>
            </div>
            <div class="sana-faq" id="sana-faq">
                @foreach($faqs as $i => $faq)
                <div class="sana-faq-item {{ $i === 0 ? 'is-open' : '' }}">
                    <button type="button" class="sana-faq-q">{{ $faq['q'] }} <i class="fas fa-chevron-down"></i></button>
                    <div class="sana-faq-a">{{ $faq['a'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

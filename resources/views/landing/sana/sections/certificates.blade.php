<section class="sana-section" id="achievements">
    <div class="sana-container">
        <div class="sana-achieve-box sana-reveal">
            <div class="sana-achieve-box__glow sana-achieve-box__glow--1"></div>
            <div class="sana-achieve-box__glow sana-achieve-box__glow--2"></div>
            <div class="sana-achieve-box__inner">
                <div class="sana-achieve-box__content">
                    <span class="sana-achieve-box__tag"><i class="fas fa-certificate"></i> {{ __('public.home_certs_tag') }}</span>
                    <h2 class="sana-achieve-box__title">{{ __('public.home_certs_title') }} <span class="hl">{{ __('public.home_certs_title_hl') }}</span></h2>
                    <p class="sana-achieve-box__desc">{{ __('public.home_certs_desc') }}</p>
                    <ul class="sana-achieve-box__highlights">
                        <li><i class="fas fa-check-circle"></i> {{ __('public.home_certs_b1') }}</li>
                        <li><i class="fas fa-qrcode"></i> {{ __('public.home_certs_b2') }}</li>
                        <li><i class="fas fa-shield-check"></i> {{ __('public.home_certs_b3') }}</li>
                    </ul>
                </div>
                <div class="sana-achieve-box__visual" aria-hidden="true">
                    <div class="sana-cert-mock">
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--tl"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--tr"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--bl"></div>
                        <div class="sana-cert-mock__corner sana-cert-mock__corner--br"></div>
                        <div class="sana-cert-mock__seal"><i class="fas fa-star"></i></div>
                        <p class="sana-cert-mock__label">{{ __('public.home_certs_mock_label') }}</p>
                        <h3 class="sana-cert-mock__brand">{{ config('app.name') }}</h3>
                        <div class="sana-cert-mock__line"></div>
                        <p class="sana-cert-mock__to">{{ __('public.home_certs_mock_to') }}</p>
                        <p class="sana-cert-mock__name">{{ __('public.home_certs_mock_name') }}</p>
                        <p class="sana-cert-mock__course">{{ __('public.home_certs_mock_course') }}</p>
                        <div class="sana-cert-mock__footer">
                            <span><i class="fas fa-qrcode"></i> {{ __('public.home_certs_mock_verify') }}</span>
                            <span><i class="fas fa-shield-check"></i> {{ __('public.home_certs_mock_issued') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

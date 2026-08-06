<section class="sana-section" id="app">
    <div class="sana-container">
        <div class="sana-app-m sana-reveal">
            <div class="sana-app-m__content">
                <h2>{{ __('public.home_app_title') }}<br><span class="hl">{{ __('public.home_app_title_hl') }}</span></h2>
                <p>{{ str_replace(':brand', config('app.name'), __('public.home_app_sub')) }}</p>
                <div class="sana-app-m__stores">
                    <span class="store"><i class="fab fa-apple"></i> App Store</span>
                    <span class="store"><i class="fab fa-google-play"></i> Google Play</span>
                </div>
            </div>
            <div class="sana-app-m__phone">
                @php
                    $heroChar = public_static_exists('img/sanua/landing-hero-boy.png')
                        ? public_static_url('img/sanua/landing-hero-boy.png')
                        : public_static_url('img/sanua/hero-boy.png');
                @endphp
                <img src="{{ $heroChar }}" alt="" class="sana-app-m__char">
                <div class="sana-app-m__device">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=320&auto=format&fit=crop&q=80" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

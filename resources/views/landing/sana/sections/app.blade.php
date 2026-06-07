<section class="sana-section" id="app">
    <div class="sana-container">
        <div class="sana-app-m sana-reveal">
            <div class="sana-app-m__content">
                <h2>تعلّم في أي وقت<br><span class="hl">من أي مكان</span></h2>
                <p>حمّل تطبيق {{ config('app.name') }} وتابع دروسك وإشعاراتك من هاتفك.</p>
                <div class="sana-app-m__stores">
                    <span class="store"><i class="fab fa-apple"></i> App Store</span>
                    <span class="store"><i class="fab fa-google-play"></i> Google Play</span>
                </div>
            </div>
            <div class="sana-app-m__phone">
                @php $heroChar = file_exists(public_path('img/sanua/landing-hero-boy.png')) ? asset('img/sanua/landing-hero-boy.png') : asset('img/sanua/hero-boy.png'); @endphp
                <img src="{{ $heroChar }}" alt="" class="sana-app-m__char">
                <div class="sana-app-m__device">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=320&auto=format&fit=crop&q=80" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

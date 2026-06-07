@php
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
@endphp
<footer class="sana-foot-m">
    <div class="sana-container">
        <div class="sana-foot-m__grid">
            <div class="sana-foot-m__brand">
                <a href="{{ route('home') }}" class="sana-foot-m__logo">
                    @if($logoUrl)<img src="{{ $logoUrl }}" alt="">@endif
                    <span>{{ strtoupper($brand) }}</span>
                </a>
                <p>منصة تعليمية عربية تفاعلية للأطفال والطلاب — تعلّم بمتعة وثقة.</p>
                <div class="sana-foot-m__social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div>
                <h4>تصفّح</h4>
                <ul>
                    <li><a href="{{ route('public.courses') }}">الكورسات</a></li>
                    <li><a href="#instructors">المعلّمون</a></li>
                    <li><a href="{{ route('public.pricing') }}">الأسعار</a></li>
                    <li><a href="#categories">التصنيفات</a></li>
                </ul>
            </div>
            <div>
                <h4>روابط مهمة</h4>
                <ul>
                    <li><a href="{{ route('public.about') }}">من نحن</a></li>
                    <li><a href="{{ route('public.contact') }}">اتصل بنا</a></li>
                    <li><a href="{{ route('public.faq') }}">الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('public.privacy') }}">الخصوصية</a></li>
                </ul>
            </div>
            <div>
                <h4>اشترك في النشرة</h4>
                <p class="sub">آخر الدورات والعروض إلى بريدك.</p>
                <form class="sana-foot-m__form" onsubmit="return false;">
                    <input type="email" placeholder="بريدك الإلكتروني">
                    <button type="button" class="sana-btn sana-btn--yellow">اشترك</button>
                </form>
            </div>
        </div>
        <p class="sana-foot-m__copy">&copy; {{ date('Y') }} {{ $brand }}. جميع الحقوق محفوظة.</p>
    </div>
</footer>

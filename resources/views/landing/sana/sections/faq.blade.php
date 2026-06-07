@php
    $faqs = [
        ['q' => 'هل المنصة مناسبة لجميع الأعمار؟', 'a' => 'نعم، محتوىنا مصمّم للأطفال من 8 إلى 16 سنة مع مسارات حسب العمر والمستوى.'],
        ['q' => 'هل يمكنني تجربة المنصة مجاناً؟', 'a' => 'بالتأكيد! سجّل مجاناً واستكشف دروساً تجريبية قبل الاشتراك.'],
        ['q' => 'كيف أتابع تقدّم ابني؟', 'a' => 'لوحة وليّ الأمر تعرض التقدّم والحضور والاختبارات بوضوح.'],
        ['q' => 'هل الشهادات معتمدة؟', 'a' => 'نعم، شهادات رقمية قابلة للتحقق عبر رابط فريد.'],
        ['q' => 'هل الحصص المباشرة مسجّلة؟', 'a' => 'نعم، معظم الحصص متاحة للمشاهدة لاحقاً من حساب الطالب.'],
    ];
    $faqChar = file_exists(public_path('img/sanua/landing-hero-boy.png'))
        ? asset('img/sanua/landing-hero-boy.png')
        : asset('img/sanua/hero-boy.png');
@endphp
<section class="sana-section sana-section--white" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">الأسئلة <span class="hl">الشائعة</span></h2>
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

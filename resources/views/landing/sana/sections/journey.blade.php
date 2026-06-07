@php
    $steps = [
        ['icon' => '📝', 'title' => 'سجّل حسابك'],
        ['icon' => '📚', 'title' => 'اختر الدورة'],
        ['icon' => '🎬', 'title' => 'ابدأ التعلّم'],
        ['icon' => '💬', 'title' => 'تفاعل وتمرّن'],
        ['icon' => '📜', 'title' => 'احصل على الشهادة'],
        ['icon' => '🏆', 'title' => 'حقّق هدفك'],
    ];
@endphp
<section class="sana-section sana-section--white" id="journey">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">رحلتك التعليمية <span class="hl">في 6 خطوات</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-journey-m sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            @foreach($steps as $step)
            <div class="sana-journey-m__step">
                <div class="sana-journey-m__icon">{{ $step['icon'] }}</div>
                <span>{{ $step['title'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

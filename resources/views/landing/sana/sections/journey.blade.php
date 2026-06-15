@php
    $steps = [
        ['icon' => 'fa-clipboard-check', 'title' => __('public.journey_step_1')],
        ['icon' => 'fa-user-check', 'title' => __('public.journey_step_2')],
        ['icon' => 'fa-video', 'title' => __('public.journey_step_3')],
        ['icon' => 'fa-chart-line', 'title' => __('public.journey_step_4')],
    ];
@endphp
<section class="sana-section sana-section--white" id="journey">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">{{ __('public.journey_title') }} <span class="hl">{{ __('public.journey_highlight') }}</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-journey-m sana-reveal">
            <div class="sana-journey-m__line" aria-hidden="true"></div>
            @foreach($steps as $step)
            <div class="sana-journey-m__step">
                <div class="sana-journey-m__icon"><i class="fas {{ $step['icon'] }}"></i></div>
                <span>{{ $step['title'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="sana-reveal" style="margin-top:28px;display:flex;flex-direction:column;align-items:center;gap:12px">
            @include('landing.sana.partials.site-cta-buttons')
            <a href="{{ route('public.how_it_works') }}" class="sana-link-more">{{ __('public.how_it_works_page_title') }} <i class="fas fa-arrow-left"></i></a>
        </div>
    </div>
</section>

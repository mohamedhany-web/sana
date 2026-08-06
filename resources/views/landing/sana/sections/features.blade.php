@php
    $features = [
        ['emoji' => '🗺️', 'bg' => '#EDE9FE', 'title' => __('public.home_feature_1_title'), 'desc' => __('public.home_feature_1_desc')],
        ['emoji' => '🎧', 'bg' => '#DBEAFE', 'title' => __('public.home_feature_2_title'), 'desc' => __('public.home_feature_2_desc')],
        ['emoji' => '📜', 'bg' => '#FEF3C7', 'title' => __('public.home_feature_3_title'), 'desc' => __('public.home_feature_3_desc')],
        ['emoji' => '📊', 'bg' => '#D1FAE5', 'title' => __('public.home_feature_4_title'), 'desc' => __('public.home_feature_4_desc')],
        ['emoji' => '📈', 'bg' => '#FCE7F3', 'title' => __('public.home_feature_5_title'), 'desc' => __('public.home_feature_5_desc')],
        ['emoji' => '🎬', 'bg' => '#E0E7FF', 'title' => __('public.home_feature_6_title'), 'desc' => __('public.home_feature_6_desc')],
    ];
@endphp
<section class="sana-section sana-section--white" id="features">
    <div class="sana-container">
        <div class="sana-head sana-reveal">
            <h2 class="sana-head__title">{{ __('public.home_features_title') }} <span class="hl">{{ __('public.home_features_title_hl') }}</span></h2>
            <span class="sana-head__line"></span>
        </div>
        <div class="sana-features-m">
            @foreach($features as $f)
            <article class="sana-feature-m sana-reveal">
                <div class="sana-feature-m__icon" style="background:{{ $f['bg'] }}">{{ $f['emoji'] }}</div>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['desc'] }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

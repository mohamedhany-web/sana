@php
    $compact = !empty($compact);
    $cta = \App\Support\PublicSiteCta::payload();
    $studentSteps = [
        ['icon' => 'fa-clipboard-check', 'text' => __('public.audience_student_step_1')],
        ['icon' => 'fa-user-check', 'text' => __('public.audience_student_step_2')],
        ['icon' => 'fa-graduation-cap', 'text' => __('public.audience_student_step_3')],
    ];
@endphp

@if($compact)
<div class="sana-audience sana-audience--compact">
    <div class="sana-audience__grid sana-audience__grid--single sana-reveal">
        <article class="sana-audience__card sana-audience__card--student">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-child-reaching"></i> {{ __('public.audience_student_badge') }}</span>
                <h3>{{ __('public.audience_student_title') }}</h3>
                <p>{{ __('public.audience_student_desc') }}</p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات العائلات">
                @foreach($studentSteps as $step)
                <li><i class="fas fa-check" aria-hidden="true"></i> {{ $step['text'] }}</li>
                @endforeach
            </ol>
            <div class="sana-audience__card-foot">
                @include('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--stack sana-audience__cta'])
            </div>
        </article>
    </div>
</div>
@else
<div class="sana-paths-band sana-paths-band--wide sana-reveal">
    <div class="sana-paths-band__dots" aria-hidden="true"></div>
    <div class="sana-paths-band__glow sana-paths-band__glow--1" aria-hidden="true"></div>
    <div class="sana-paths-band__glow sana-paths-band__glow--2" aria-hidden="true"></div>

    <div class="sana-paths-band__shell">
        <div class="sana-paths-band__row-top">
            <div class="sana-paths-band__main">
                <span class="sana-paths-band__eyebrow"><i class="fas fa-child-reaching"></i> {{ __('public.audience_student_badge') }}</span>
                <h2 class="sana-paths-band__title">{{ __('public.audience_section_title') }}</h2>
                <p class="sana-paths-band__sub">{{ __('public.audience_section_sub') }}</p>
                <div class="sana-paths-band__meta">
                    <h3>{{ __('public.audience_student_title') }}</h3>
                    <p>{{ __('public.audience_student_desc') }}</p>
                </div>
            </div>

            <div class="sana-paths-band__actions">
                @include('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-paths-band__cta'])
                <a href="{{ $cta['how_it_works_url'] }}" class="sana-paths-band__link">
                    <i class="fas fa-circle-info"></i> {{ __('public.how_it_works_page_title') }}
                </a>
            </div>
        </div>

        <div class="sana-paths-band__steps" aria-label="خطوات العائلات">
            @foreach($studentSteps as $index => $step)
            <article class="sana-paths-band__step">
                <span class="sana-paths-band__step-num">الخطوة {{ $index + 1 }}</span>
                <div class="sana-paths-band__step-icon"><i class="fas {{ $step['icon'] }}"></i></div>
                <p class="sana-paths-band__step-text">{{ $step['text'] }}</p>
            </article>
            @endforeach
        </div>
    </div>
</div>
@endif

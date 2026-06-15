@php
    $compact = !empty($compact);
    $cta = \App\Support\PublicSiteCta::payload();
    $studentSteps = [
        __('public.audience_student_step_1'),
        __('public.audience_student_step_2'),
        __('public.audience_student_step_3'),
    ];
    $teacherSteps = [
        __('public.audience_teacher_step_1'),
        __('public.audience_teacher_step_2'),
        __('public.audience_teacher_step_3'),
    ];
@endphp
<div class="sana-audience {{ $compact ? 'sana-audience--compact' : '' }}">
    @unless($compact)
    <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <h2 class="sana-head__title">{{ __('public.audience_section_title') }}</h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub">{{ __('public.audience_section_sub') }}</p>
    </div>
    @endunless
    <div class="sana-audience__grid sana-reveal">
        <article class="sana-audience__card sana-audience__card--student">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-child-reaching"></i> {{ __('public.audience_student_badge') }}</span>
                <h3>{{ __('public.audience_student_title') }}</h3>
                <p>{{ __('public.audience_student_desc') }}</p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات العائلات">
                @foreach($studentSteps as $step)
                <li><i class="fas fa-check" aria-hidden="true"></i> {{ $step }}</li>
                @endforeach
            </ol>
            <div class="sana-audience__card-foot">
                @include('landing.sana.partials.site-cta-buttons', ['hero' => true, 'class' => 'sana-site-cta--stack sana-audience__cta'])
                <p class="sana-audience__foot-link">
                    <a href="{{ $cta['how_it_works_url'] }}"><i class="fas fa-circle-info"></i> {{ __('public.how_it_works_page_title') }}</a>
                </p>
            </div>
        </article>
        <article class="sana-audience__card sana-audience__card--teacher">
            <div class="sana-audience__card-head">
                <span class="sana-audience__badge"><i class="fas fa-chalkboard-user"></i> {{ __('public.audience_teacher_badge') }}</span>
                <h3>{{ __('public.audience_teacher_title') }}</h3>
                <p>{{ __('public.audience_teacher_desc') }}</p>
            </div>
            <ol class="sana-audience__steps" aria-label="خطوات المعلّمين">
                @foreach($teacherSteps as $step)
                <li><i class="fas fa-check" aria-hidden="true"></i> {{ $step }}</li>
                @endforeach
            </ol>
            <div class="sana-audience__card-foot">
                <a href="{{ route('tutor.apply') }}" class="sana-btn sana-btn--purple sana-btn--lg sana-audience__cta">
                    <i class="fas fa-chalkboard-teacher"></i> {{ __('public.audience_teacher_cta') }}
                </a>
                <p class="sana-audience__foot-link">
                    <a href="{{ route('tutor.policy') }}"><i class="fas fa-file-contract"></i> {{ __('public.audience_teacher_link_policy') }}</a>
                </p>
            </div>
        </article>
    </div>
</div>

@php
    $pf = $publicFooter ?? \App\Services\PublicFooterSettings::payload();
    $contact = $publicContact ?? \App\Support\PublicContactInfo::payload();
    $brand = config('app.name');
    $logoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $telHref = '';
    if (! empty($pf['phone'])) {
        $digits = preg_replace('/[^\d+]/', '', $pf['phone']);
        $telHref = $digits !== '' ? 'tel:'.$digits : '';
    }
@endphp
<footer class="sana-foot-m">
    <div class="sana-container">
        <div class="sana-foot-m__grid">
            <div class="sana-foot-m__brand">
                <a href="{{ route('home') }}" class="sana-foot-m__logo">
                    @if($logoUrl)<img src="{{ $logoUrl }}" alt="{{ $brand }}">@endif
                    <span>{{ strtoupper($brand) }}</span>
                </a>
                <p>{{ $pf['blurb'] ?: __('public.footer_default_blurb') }}</p>
                @if(! empty($pf['socials']))
                <div class="sana-foot-m__social">
                    @foreach($pf['socials'] as $soc)
                        <a href="{{ e($soc['url']) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ e($soc['label']) }}" title="{{ e($soc['label']) }}">
                            <i class="{{ e($soc['icon']) }}"></i>
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
            <div>
                <h4>{{ __('public.footer_browse') }}</h4>
                <ul>
                    <li><a href="{{ route('public.how_it_works') }}">{{ __('public.footer_how_it_works') }}</a></li>
                    @if($hasPublishedCourses ?? false)
                        <li><a href="{{ route('public.courses') }}">{{ __('public.footer_courses') }}</a></li>
                    @endif
                    @if($hasPublicInstructors ?? false)
                        <li><a href="{{ route('public.instructors.index') }}">{{ __('public.footer_instructors') }}</a></li>
                    @endif
                    <li><a href="{{ route('public.pricing') }}">{{ __('public.footer_pricing') }}</a></li>
                    @if($hasPublishedCourses ?? false)
                        <li><a href="{{ route('home') }}#categories">{{ __('public.footer_categories') }}</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4>{{ __('public.footer_important') }}</h4>
                <ul>
                    <li><a href="{{ route('public.about') }}">{{ __('public.footer_about') }}</a></li>
                    <li><a href="{{ route('public.help') }}">{{ __('public.footer_help') }}</a></li>
                    <li><a href="{{ route('public.contact') }}">{{ __('public.footer_contact_us') }}</a></li>
                    <li><a href="{{ route('public.faq') }}">{{ __('public.footer_faq') }}</a></li>
                    <li><a href="{{ route('public.privacy') }}">{{ __('public.footer_privacy') }}</a></li>
                    <li><a href="{{ route('public.terms') }}">{{ __('public.footer_terms') }}</a></li>
                    <li><a href="{{ route('tutor.apply') }}">{{ __('public.footer_teacher_apply') }}</a></li>
                </ul>
            </div>
            <div>
                <h4>{{ __('public.footer_contact') }}</h4>
                <ul>
                    @if(! empty($pf['email']))
                        <li><a href="mailto:{{ e($pf['email']) }}">{{ $pf['email'] }}</a></li>
                    @endif
                    @if(! empty($pf['phone']))
                        <li>
                            @if($telHref !== '')
                                <a href="{{ $telHref }}" rel="nofollow">{{ $pf['phone'] }}</a>
                            @else
                                <span>{{ $pf['phone'] }}</span>
                            @endif
                        </li>
                    @endif
                    @if(! empty($pf['whatsapp_url']))
                        <li>
                            <a href="{{ e($pf['whatsapp_url']) }}" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-whatsapp"></i> {{ __('public.footer_whatsapp') }}
                            </a>
                        </li>
                    @endif
                    @if(! empty($pf['address']))
                        <li><span>{{ $pf['address'] }}</span></li>
                    @endif
                    @if(! empty($pf['support_hours']))
                        <li><span>{{ $pf['support_hours'] }}</span></li>
                    @endif
                </ul>
            </div>
        </div>
        <p class="sana-foot-m__copy">
            &copy; {{ date('Y') }} {{ $brand }}.
            {{ $pf['bottom_tagline'] ?: __('public.footer_rights') }}
        </p>
    </div>
</footer>

@include('partials.whatsapp-fab')

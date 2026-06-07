@php
    $brand = config('app.name', 'Sana');
    $tr = fn (string $key) => str_replace(':brand', $brand, __('sana_home.'.$key));
    $c = fn (string $key) => str_replace(':brand', $brand, __('sana_contact.'.$key));
    $hero = __('sana_contact.hero');
    $channelsCopy = __('sana_contact.channels');
    $categories = __('sana_contact.categories');
    $responseCards = __('sana_contact.response_cards');
    $faqItems = __('sana_contact.faq');
    $fields = __('sana_contact.fields');
    $address = trim(__('sana_contact.address'));
    $mapEmbed = trim(__('sana_contact.map_embed'));
    $phoneTel = $supportPhone ? preg_replace('/\s+/', '', $supportPhone) : '';
    $hasPhone = $supportPhone !== '';
    $hasEmail = $supportEmail !== '';
    $hasWhatsapp = $whatsappUrl !== '';
    $hasSocials = !empty($socials);
    $chatUrl = $hasWhatsapp ? $whatsappUrl : '#contact-form';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>{{ $c('meta_title') }} — {{ $brand }}</title>
    <meta name="description" content="{{ $c('meta_description') }}">
    <meta name="theme-color" content="#5B21B6">
    <link rel="canonical" href="{{ url('/contact') }}">
    <meta property="og:title" content="{{ $c('meta_title') }} — {{ $brand }}">
    <meta property="og:description" content="{{ $c('meta_description') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('partials.rtl-base')
    @include('landing.sana.theme')
    @include('landing.sana.courses-catalog-theme')
    @include('landing.sana.contact-theme')
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="sana-home sana-courses-page" x-data="{ category: '{{ old('subject') ? '' : 'general' }}' }">

<div id="sana-scroll-progress"></div>
@include('landing.sana.navbar')

<main class="sana-contact-page">

{{-- §1 Hero --}}
<section class="sana-ct-hero">
    <div class="sana-container">
        <div class="sana-ct-hero__grid sana-reveal">
            <div class="sana-ct-hero__content">
                <span class="sana-ct-hero__eyebrow"><i class="fas fa-headset"></i> {{ $hero['eyebrow'] }}</span>
                <h1 class="sana-ct-hero__title">
                    {{ $hero['title'] }}
                    <span class="hl">{{ $hero['highlight'] }}</span>
                </h1>
                <p class="sana-ct-hero__sub">{{ str_replace(':brand', $brand, $hero['sub']) }}</p>
                <div class="sana-ct-hero__actions">
                    <a href="#contact-form" class="sana-btn sana-btn--yellow sana-btn--lg">
                        <i class="fas fa-paper-plane"></i> {{ $hero['cta_form'] }}
                    </a>
                    @if($hasWhatsapp)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="sana-btn sana-btn--wa sana-btn--lg">
                        <i class="fab fa-whatsapp"></i> {{ $hero['cta_whatsapp'] }}
                    </a>
                    @endif
                </div>
                <div class="sana-ct-hero__trust">
                    <span><i class="fas fa-bolt"></i> رد خلال 24 ساعة</span>
                    <span><i class="fas fa-shield-halved"></i> بياناتك محمية</span>
                    <span><i class="fas fa-users"></i> فريق عربي متخصص</span>
                </div>
            </div>
            <div class="sana-ct-hero__visual">
                @include('landing.sana.partials.contact-hero-scene')
            </div>
        </div>
    </div>
</section>

{{-- §2 Contact options --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <span class="sana-head__eyebrow">{{ $brand }}</span>
            <h2 class="sana-head__title">{{ __('sana_contact.channels_title') }} <span class="hl">{{ __('sana_contact.channels_highlight') }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('sana_contact.channels_sub') }}</p>
        </div>
        <div class="sana-ct-channels" id="social-links">
            @if($hasPhone)
            <a href="tel:{{ $phoneTel }}" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--phone"><i class="fas fa-phone"></i></span>
                <strong>{{ $channelsCopy['phone']['title'] }}</strong>
                <p>{{ $channelsCopy['phone']['desc'] }}</p>
                <span class="sana-ct-channel__info" dir="ltr">{{ $supportPhone }}</span>
                <span class="sana-ct-channel__btn">{{ $channelsCopy['phone']['action'] }} <i class="fas fa-arrow-left"></i></span>
            </a>
            @endif

            @if($hasWhatsapp)
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--wa"><i class="fab fa-whatsapp"></i></span>
                <strong>{{ $channelsCopy['whatsapp']['title'] }}</strong>
                <p>{{ $channelsCopy['whatsapp']['desc'] }}</p>
                <span class="sana-ct-channel__btn">{{ $channelsCopy['whatsapp']['action'] }} <i class="fas fa-arrow-left"></i></span>
            </a>
            @endif

            @if($hasEmail)
            <a href="mailto:{{ $supportEmail }}" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--email"><i class="fas fa-envelope"></i></span>
                <strong>{{ $channelsCopy['email']['title'] }}</strong>
                <p>{{ $channelsCopy['email']['desc'] }}</p>
                <span class="sana-ct-channel__info">{{ $supportEmail }}</span>
                <span class="sana-ct-channel__btn">{{ $channelsCopy['email']['action'] }} <i class="fas fa-arrow-left"></i></span>
            </a>
            @endif

            <a href="{{ $hasWhatsapp ? $whatsappUrl : '#contact-form' }}" @if($hasWhatsapp) target="_blank" rel="noopener noreferrer" @endif class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--chat"><i class="fas fa-comments"></i></span>
                <strong>{{ $channelsCopy['chat']['title'] }}</strong>
                <p>{{ $channelsCopy['chat']['desc'] }}</p>
                <span class="sana-ct-channel__btn">{{ $channelsCopy['chat']['action'] }} <i class="fas fa-arrow-left"></i></span>
            </a>

            <div class="sana-ct-channel sana-reveal {{ !$hasSocials ? 'is-disabled' : '' }}">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--social"><i class="fas fa-share-nodes"></i></span>
                <strong>{{ $channelsCopy['social']['title'] }}</strong>
                <p>{{ $channelsCopy['social']['desc'] }}</p>
                @if($hasSocials)
                <div class="sana-ct-socials">
                    @foreach($socials as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                        <i class="{{ $social['icon'] }}"></i>
                    </a>
                    @endforeach
                </div>
                @endif
                <span class="sana-ct-channel__btn">{{ $channelsCopy['social']['action'] }}</span>
            </div>

            <a href="{{ route('public.help') }}" class="sana-ct-channel sana-reveal">
                <span class="sana-ct-channel__icon sana-ct-channel__icon--help"><i class="fas fa-circle-question"></i></span>
                <strong>{{ $channelsCopy['help']['title'] }}</strong>
                <p>{{ $channelsCopy['help']['desc'] }}</p>
                <span class="sana-ct-channel__btn">{{ $channelsCopy['help']['action'] }} <i class="fas fa-arrow-left"></i></span>
            </a>
        </div>
    </div>
</section>

{{-- §3 Form + §4 Categories --}}
<section class="sana-section sana-section--soft" id="contact-form">
    <div class="sana-container">
        <div class="sana-ct-form-wrap">
            <div class="sana-reveal">
                <span class="sana-head__eyebrow">{{ $brand }}</span>
                <h2 class="sana-head__title" style="text-align:right;margin-bottom:8px">
                    {{ __('sana_contact.categories_title') }} <span class="hl">{{ __('sana_contact.categories_highlight') }}</span>
                </h2>
                <p class="sana-head__sub" style="margin:0 0 20px;text-align:right;max-width:none">{{ __('sana_contact.categories_sub') }}</p>
                <div class="sana-ct-categories">
                    @foreach($categories as $cat)
                    <button type="button"
                            class="sana-ct-cat"
                            :class="{ 'is-active': category === '{{ $cat['key'] }}' }"
                            @click="category = '{{ $cat['key'] }}'; document.getElementById('subject').value = '{{ $cat['subject'] }}'">
                        <i class="fas {{ $cat['icon'] }}"></i>
                        <span>{{ $cat['label'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="sana-ct-form-card sana-reveal">
                <div class="sana-head" style="margin-bottom:24px;text-align:right">
                    <h2 class="sana-head__title" style="font-size:1.35rem">
                        {{ __('sana_contact.form_title') }} <span class="hl">{{ __('sana_contact.form_highlight') }}</span>
                    </h2>
                    <p class="sana-head__sub" style="margin:8px 0 0;text-align:right;max-width:none">{{ __('sana_contact.form_sub') }}</p>
                </div>

                @if(session('success'))
                <div class="sana-ct-alert" role="status">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="post" action="{{ route('public.contact.store') }}" novalidate>
                    @csrf
                    <div class="sana-ct-field">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255" placeholder=" " class="@error('name') is-error @enderror" autocomplete="name">
                        <label for="name">{{ $fields['name'] }} *</label>
                        @error('name')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
                    </div>
                    <div class="sana-ct-form-row">
                        <div class="sana-ct-field">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="255" placeholder=" " dir="ltr" class="@error('email') is-error @enderror" autocomplete="email">
                            <label for="email">{{ $fields['email'] }} *</label>
                            @error('email')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
                        </div>
                        <div class="sana-ct-field">
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" maxlength="20" placeholder=" " dir="ltr" class="@error('phone') is-error @enderror" autocomplete="tel">
                            <label for="phone">{{ $fields['phone'] }} {{ $fields['phone_optional'] }}</label>
                            @error('phone')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="sana-ct-field">
                        <input type="text" name="subject" id="subject" value="{{ old('subject', 'استفسار عام') }}" required maxlength="255" placeholder=" " class="@error('subject') is-error @enderror">
                        <label for="subject">{{ $fields['subject'] }} *</label>
                        @error('subject')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
                    </div>
                    <div class="sana-ct-field">
                        <textarea name="message" id="message" required maxlength="5000" placeholder=" " class="@error('message') is-error @enderror">{{ old('message') }}</textarea>
                        <label for="message">{{ $fields['message'] }} *</label>
                        @error('message')<p class="sana-ct-field__err">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="sana-btn sana-btn--purple sana-ct-submit">
                        <i class="fas fa-paper-plane"></i> {{ __('sana_contact.form_submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

{{-- §5 Response expectations --}}
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title">{{ __('sana_contact.response_title') }} <span class="hl">{{ __('sana_contact.response_highlight') }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('sana_contact.response_sub') }}</p>
        </div>
        <div class="sana-ct-response">
            @foreach($responseCards as $card)
            <div class="sana-ct-response__card sana-reveal">
                <i class="fas {{ $card['icon'] }}"></i>
                <strong>{{ $card['title'] }}</strong>
                <em>{{ $card['value'] }}</em>
                <span>{{ $card['desc'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- §6 FAQ --}}
<section class="sana-section sana-section--soft" id="faq">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
            <h2 class="sana-head__title">{{ __('sana_contact.faq_title') }} <span class="hl">{{ __('sana_contact.faq_highlight') }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('sana_contact.faq_sub') }}</p>
        </div>
        <div class="sana-faq sana-reveal" id="sana-faq" style="max-width:720px;margin-inline:auto">
            @foreach($faqItems as $i => $faq)
            <div class="sana-faq-item {{ $i === 0 ? 'is-open' : '' }}">
                <button type="button" class="sana-faq-q" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                    {{ $faq['q'] }} <i class="fas fa-chevron-down"></i>
                </button>
                <div class="sana-faq-a">{{ $faq['a'] }}</div>
            </div>
            @endforeach
        </div>
        <p class="text-center sana-reveal" style="margin-top:24px">
            <a href="{{ route('public.faq') }}" class="sana-btn sana-btn--outline-purple" style="display:inline-flex">
                <i class="fas fa-circle-question"></i> {{ __('public.faq_page_title') }}
            </a>
        </p>
    </div>
</section>

{{-- §7 Location (optional) --}}
@if($address !== '')
<section class="sana-section">
    <div class="sana-container">
        <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:32px">
            <h2 class="sana-head__title">{{ __('sana_contact.location_title') }} <span class="hl">{{ __('sana_contact.location_highlight') }}</span></h2>
            <span class="sana-head__line"></span>
            <p class="sana-head__sub">{{ __('sana_contact.location_sub') }}</p>
        </div>
        <div class="sana-ct-location sana-reveal">
            <div class="sana-ct-location__info">
                <h3><i class="fas fa-building" style="color:var(--p)"></i> {{ $brand }}</h3>
                <div class="sana-ct-location__row">
                    <i class="fas fa-location-dot"></i>
                    <span>{{ $address }}</span>
                </div>
                <div class="sana-ct-location__row">
                    <i class="fas fa-clock"></i>
                    <span>{{ __('sana_contact.location_hours') }}</span>
                </div>
                @if($hasPhone)
                <div class="sana-ct-location__row">
                    <i class="fas fa-phone"></i>
                    <a href="tel:{{ $phoneTel }}" dir="ltr" style="color:var(--p);font-weight:800;text-decoration:none">{{ $supportPhone }}</a>
                </div>
                @endif
                @if($hasEmail)
                <div class="sana-ct-location__row">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:{{ $supportEmail }}" style="color:var(--p);font-weight:800;text-decoration:none">{{ $supportEmail }}</a>
                </div>
                @endif
            </div>
            @if($mapEmbed !== '')
            <div class="sana-ct-location__map">
                <iframe src="{{ $mapEmbed }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="موقع {{ $brand }}"></iframe>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- §8 Final CTA --}}
<section class="sana-ct-final">
    <div class="sana-container sana-reveal">
        <div class="sana-ct-final__box">
            <h2>{{ __('sana_contact.final_title') }}</h2>
            <p>{{ __('sana_contact.final_sub') }}</p>
            <div class="sana-ct-final__actions">
                @if($hasWhatsapp)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="sana-btn sana-btn--wa sana-btn--lg">
                    <i class="fab fa-whatsapp"></i> {{ __('sana_contact.final_whatsapp') }}
                </a>
                @endif
                <a href="#contact-form" class="sana-btn sana-btn--yellow sana-btn--lg">
                    <i class="fas fa-paper-plane"></i> {{ __('sana_contact.final_form') }}
                </a>
            </div>
        </div>
    </div>
</section>

</main>

@include('landing.sana.footer')
@include('landing.sana.scripts')
@include('partials.pwa-service-worker')
</body>
</html>

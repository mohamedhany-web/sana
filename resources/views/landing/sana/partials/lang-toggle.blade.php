@php
    $locale = app()->getLocale();
    $isEn = str_starts_with((string) $locale, 'en');
    $redirect = url()->full();
    $toAr = route('locale.switch', ['locale' => 'ar', 'redirect' => $redirect]);
    $toEn = route('locale.switch', ['locale' => 'en', 'redirect' => $redirect]);
    $compact = ! empty($compact);
@endphp
<div class="sana-lang{{ $compact ? ' sana-lang--compact' : '' }}" role="group" aria-label="{{ __('landing.language_switcher.ar') }} / {{ __('landing.language_switcher.en') }}">
    <a href="{{ $toAr }}"
       class="sana-lang__opt{{ ! $isEn ? ' is-active' : '' }}"
       hreflang="ar"
       lang="ar"
       @if(! $isEn) aria-current="true" @endif>
        ع
    </a>
    <a href="{{ $toEn }}"
       class="sana-lang__opt{{ $isEn ? ' is-active' : '' }}"
       hreflang="en"
       lang="en"
       @if($isEn) aria-current="true" @endif>
        EN
    </a>
</div>

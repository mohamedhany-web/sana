@php
    $pf = $publicFooter ?? \App\Services\PublicFooterSettings::payload();
    $isRtl = app()->getLocale() === 'ar';
    $telHref = '';
    if (! empty($pf['phone'])) {
        $digits = preg_replace('/[^\d+]/', '', $pf['phone']);
        $telHref = $digits !== '' ? 'tel:'.$digits : '';
    }
    $brandName = config('app.name', 'Sana');
    $footerLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
@endphp
{{-- فوتر موحّد — نفس الصفحة الرئيسية؛ بيانات من إعدادات النظام --}}
<footer style="background:#283593;font-family:Tajawal,Cairo,sans-serif" class="text-white {{ $footerExtraClass ?? '' }}">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 pt-12 pb-8">
        <div class="grid md:grid-cols-4 gap-8 pb-8 border-b border-white/15">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 group">
                        @if(! empty($footerLogoUrl))
                            <span class="relative w-11 h-11 overflow-hidden rounded-full ring-1 ring-white/30 shadow-lg [box-shadow:0_4px_14px_-4px_rgba(0,0,0,.35)] transition-transform duration-200 group-hover:scale-[1.03]">
                                <img src="{{ $footerLogoUrl }}" alt="{{ $brandName }}" class="h-full w-full object-cover object-center" width="44" height="44" decoding="async">
                            </span>
                        @else
                            <span class="w-11 h-11 rounded-full bg-[#FB5607] text-white font-black flex items-center justify-center shadow-lg transition-transform duration-200 group-hover:scale-[1.03]" style="box-shadow:0 4px 16px -4px rgba(251,86,7,.35)">M</span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xl font-black text-white group-hover:text-[#FFE569] transition-colors">{{ $brandName }}</p>
                            <p class="text-xs text-white/70">{{ $pf['brand_tagline'] }}</p>
                        </div>
                    </a>
                </div>
                <p class="text-sm text-white/85 leading-7 max-w-md">{{ $pf['blurb'] }}</p>
            </div>
            <div>
                <h3 class="font-bold mb-3 text-white">{{ __('public.quick_links') }}</h3>
                <ul class="space-y-2 text-sm text-white/85">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('home') }}">{{ __('public.home') }}</a></li>
                    @if(\Illuminate\Support\Facades\Route::has('public.services.index'))
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.services.index') }}">{{ __('public.services_page_title') }}</a></li>
                    @endif
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.courses') }}">{{ __('public.courses') }}</a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.about') }}">{{ __('public.about') }}</a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.faq') }}">{{ __('public.faq') }}</a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.help') }}">{{ __('public.help_center') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-3 text-white">{{ __('public.contact_us') }}</h3>
                <ul class="space-y-2 text-sm text-white/85">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.contact') }}">{{ __('public.contact_page_title') }}</a></li>
                    @if(! empty($pf['email']))
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors break-all" href="mailto:{{ e($pf['email']) }}">{{ $pf['email'] }}</a>
                    </li>
                    @endif
                    @if(! empty($pf['phone']) && $telHref !== '')
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors" href="{{ $telHref }}" rel="nofollow">{{ $pf['phone'] }}</a>
                    </li>
                    @elseif(! empty($pf['phone']))
                    <li><span class="text-white/85">{{ $pf['phone'] }}</span></li>
                    @endif
                    @if(! empty($pf['whatsapp_url']))
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors inline-flex items-center gap-2" href="{{ e($pf['whatsapp_url']) }}" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp text-lg"></i>
                            @if(! empty($pf['phone']))
                                {{ $isRtl ? 'واتساب: ' : 'WhatsApp: ' }}{{ $pf['phone'] }}
                            @else
                                WhatsApp
                            @endif
                        </a>
                    </li>
                    @endif
                </ul>
                <ul class="mt-4 space-y-2 text-xs text-white/70">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.terms') }}">{{ __('public.terms_conditions') }}</a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="{{ route('public.privacy') }}">{{ __('public.privacy_policy') }}</a></li>
                </ul>
                @if(! empty($pf['socials']))
                <p class="text-xs font-bold text-white/90 mt-4 mb-2">{{ __('public.follow_us') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($pf['socials'] as $soc)
                    <a href="{{ e($soc['url']) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                       aria-label="{{ e($soc['label']) }}"
                       title="{{ e($soc['label']) }}">
                        <i class="{{ e($soc['icon']) }} text-sm"></i>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        <div class="pt-5 flex flex-col sm:flex-row gap-2 justify-between text-xs text-white/75">
            <p>&copy; {{ date('Y') }} {{ $brandName }} — {{ $isRtl ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}</p>
            @if(! empty($pf['bottom_tagline']))
            <p>{{ $pf['bottom_tagline'] }}</p>
            @endif
        </div>
    </div>
</footer>

@if(! empty($pf['whatsapp_url']))
    {{-- زر واتساب عائم للواجهة العامة (يستخدم نفس رابط إعدادات النظام) --}}
    <a href="{{ e($pf['whatsapp_url']) }}"
       target="_blank"
       rel="noopener noreferrer"
       class="fixed z-[9998] flex items-center justify-center rounded-full shadow-lg"
       style="
           width: 56px;
           height: 56px;
           {{ $isRtl ? 'left' : 'right' }}: 18px;
           bottom: 18px;
           background-color: #25D366;
           color: #ffffff;
           box-shadow: 0 10px 25px -10px rgba(0,0,0,.45);
       "
       aria-label="{{ $isRtl ? 'تواصل عبر واتساب' : 'Chat on WhatsApp' }}">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
@endif

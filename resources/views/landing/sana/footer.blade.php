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
                <p>{{ $pf['blurb'] ?: 'منصة تعليمية عربية تفاعلية للأطفال والطلاب — تعلّم بمتعة وثقة.' }}</p>
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
                <h4>تصفّح</h4>
                <ul>
                    <li><a href="{{ route('public.how_it_works') }}">كيف تعمل سنا؟</a></li>
                    @if($hasPublishedCourses ?? false)
                        <li><a href="{{ route('public.courses') }}">الكورسات</a></li>
                    @endif
                    @if($hasPublicInstructors ?? false)
                        <li><a href="{{ route('public.instructors.index') }}">المعلّمون</a></li>
                    @endif
                    <li><a href="{{ route('public.pricing') }}">الأسعار</a></li>
                    @if($hasPublishedCourses ?? false)
                        <li><a href="{{ route('home') }}#categories">التصنيفات</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4>روابط مهمة</h4>
                <ul>
                    <li><a href="{{ route('public.about') }}">من نحن</a></li>
                    <li><a href="{{ route('public.help') }}">مركز المساعدة</a></li>
                    <li><a href="{{ route('public.contact') }}">اتصل بنا</a></li>
                    <li><a href="{{ route('public.faq') }}">الأسئلة الشائعة</a></li>
                    <li><a href="{{ route('public.privacy') }}">الخصوصية</a></li>
                    <li><a href="{{ route('public.terms') }}">الشروط والأحكام</a></li>
                </ul>
            </div>
            <div>
                <h4>تواصل معنا</h4>
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
                                <i class="fab fa-whatsapp"></i> واتساب
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
            {{ $pf['bottom_tagline'] ?: 'جميع الحقوق محفوظة.' }}
        </p>
    </div>
</footer>

@include('partials.whatsapp-fab')

{{--
  شعار الرابط للرئيسية في صفحات المصادقة: من إعدادات النظام (لوحة التحكم) أو احتياطي حرف M.
  المتغيرات: $size = 'lg'|'sm'، $fallback = 'orange'|'gradient'|'primary'
--}}
@php
    $logoUrl = $adminPanelLogoUrl ?? null;
    $size = $size ?? 'lg';
    $fallback = $fallback ?? 'orange';
    $isSm = $size === 'sm';
    $box = $isSm ? 'w-10 h-10' : 'w-12 h-12';
    $brandText = $isSm ? 'text-xl' : 'text-2xl';
    $mText = $isSm ? 'text-lg' : 'text-xl';
    $mb = $mb ?? ($isSm ? 'mb-8' : 'mb-10');
@endphp
<a href="{{ route('home') }}" class="inline-flex items-center gap-3 group {{ $mb }}">
    @if($logoUrl)
        <div class="{{ $box }} rounded-xl flex items-center justify-center overflow-hidden bg-white border border-slate-200/80 shadow-lg shadow-slate-200/40 group-hover:shadow-md transition-shadow ring-1 ring-slate-100">
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}" class="w-full h-full object-contain p-1" width="48" height="48" loading="eager" decoding="async">
        </div>
    @elseif($fallback === 'gradient' && $isSm)
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-lg">
            <span class="text-white font-black text-lg">M</span>
        </div>
    @elseif($fallback === 'primary')
        <div class="{{ $box }} rounded-xl flex items-center justify-center shadow-lg text-white font-black {{ $mText }}" style="background:var(--edu-primary,{{ config('brand.colors.blue') }})">
            {{ mb_substr(config('app.name', 'M'), 0, 1) }}
        </div>
    @else
        <div class="{{ $box }} rounded-xl bg-[#FB5607] flex items-center justify-center shadow-lg shadow-orange-500/25 group-hover:shadow-orange-500/40 transition-shadow">
            <span class="text-white font-black {{ $mText }}">M</span>
        </div>
    @endif
    <span class="font-extrabold text-slate-800 {{ $brandText }}">{{ config('app.name', 'Sana') }}</span>
</a>

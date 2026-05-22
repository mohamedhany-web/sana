@props([
    'course',
    'size' => 'default',
    'asButton' => false,
])
@php
    /** @var \App\Models\AdvancedCourse $course */
    $small = $size === 'sm';
    if ($course->usesContactSupportPricing()) {
        $waUrl = $course->supportWhatsAppUrl();
        $btnClass = $small
            ? 'inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-[#25D366] text-white text-xs font-bold hover:bg-[#1da851] transition-colors'
            : 'inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#25D366] text-white text-sm font-bold hover:bg-[#1da851] transition-colors shadow-sm';
    } else {
        $list = (float) ($course->price ?? 0);
        $pay = $course->effectivePurchasePrice();
        $promo = $course->hasPromotionalPrice();
        $isFree = ($course->is_free ?? false) || ($list <= 0 && $pay <= 0);
    }
@endphp
@if($course->usesContactSupportPricing())
    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
       {{ $attributes->class([$btnClass]) }}
       onclick="event.stopPropagation();">
        <i class="fab fa-whatsapp {{ $small ? 'text-sm' : 'text-base' }}"></i>
        {{ __('public.course_contact_support') }}
    </a>
@elseif($isFree)
    <span {{ $attributes->class(['inline-flex items-center gap-1 font-bold text-emerald-600', $small ? 'text-xs' : 'text-sm']) }}>
        <i class="fas fa-gift {{ $small ? 'text-[9px]' : 'text-[10px]' }}"></i>
        {{ __('public.free_price') }}
    </span>
@elseif($promo)
    <span {{ $attributes->class(['inline-flex flex-col items-end gap-0.5']) }}>
        <span class="{{ $small ? 'text-[10px]' : 'text-xs' }} text-slate-400 line-through tabular-nums">{{ number_format($list, 0) }} {{ __('public.currency') }}</span>
        <span class="{{ $small ? 'text-xs font-bold' : 'text-sm font-black' }} text-mx-orange tabular-nums">{{ number_format($pay, 0) }} {{ __('public.currency') }}</span>
    </span>
@else
    <span {{ $attributes->class(['font-bold text-mx-orange tabular-nums', $small ? 'text-xs' : 'text-sm']) }}>
        {{ number_format($pay, 0) }} {{ __('public.currency') }}
    </span>
@endif

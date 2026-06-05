{{-- بطاقة رأي — شريط رئيسية (عرض ثابت) أو شبكة صفحة الآراء ($fluid) --}}
@php
    /** @var \App\Models\SiteTestimonial $t */
    $fluid = $fluid ?? false;
    $widthClass = $fluid
        ? 'w-full'
        : 'min-w-[min(88vw,300px)] max-w-[min(88vw,300px)] sm:min-w-[320px] sm:max-w-[320px]';
    $cardClass = 'rounded-2xl bg-white border border-slate-200 shadow-[0_4px_24px_-8px_rgba(15,23,42,.08)] transition-shadow hover:shadow-[0_12px_40px_-12px_rgba(15,23,42,.12)]';
@endphp
<article class="{{ $widthClass }} {{ $fluid ? '' : 'shrink-0' }} {{ $cardClass }} p-6 sm:p-8 flex flex-col h-full">
    @if($t->isImageType() && $t->publicImageUrl())
        <div class="w-full aspect-[4/3] max-h-[14rem] rounded-xl overflow-hidden bg-slate-50 mb-5 flex items-center justify-center">
            <img src="{{ $t->publicImageUrl() }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
        </div>
    @else
        <span class="w-11 h-11 rounded-xl bg-blue-50 text-[var(--edu-primary)] flex items-center justify-center text-lg mb-5 shrink-0">
            <i class="fas fa-quote-right"></i>
        </span>
    @endif
    <div class="flex flex-col flex-1">
        @if($t->body)
            <p class="text-sm leading-8 flex-1 text-slate-600">
                @if($t->isImageType())
                    {{ Str::limit(strip_tags($t->body), 160) }}
                @else
                    «{{ Str::limit(strip_tags($t->body), 260) }}»
                @endif
            </p>
        @endif
        <div class="mt-5 pt-4 border-t border-slate-100">
            @if($t->author_name)
                <p class="font-bold text-sm text-slate-900">{{ $t->author_name }}</p>
            @endif
            @if($t->role_label)
                <p class="text-xs mt-1 text-slate-500">{{ $t->role_label }}</p>
            @endif
            <div class="flex gap-0.5 text-amber-500 text-xs mt-2">
                @for($s = 0; $s < 5; $s++)<i class="fas fa-star"></i>@endfor
            </div>
        </div>
    </div>
</article>

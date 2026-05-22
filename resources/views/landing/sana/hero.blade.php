@php
    $spotlight = $spotlightCourse ?? null;
    $thumbUrl = null;
    if ($spotlight?->thumbnail) {
        $thumbUrl = asset('storage/' . str_replace('\\', '/', $spotlight->thumbnail));
    }
    $instName = $spotlight?->instructor?->name;
    $isFree = $spotlight && (($spotlight->is_free ?? false) || ($spotlight->listPriceAmount() <= 0 && $spotlight->effectivePurchasePrice() <= 0));
@endphp
<section class="relative pt-28 sm:pt-32 lg:pt-36 pb-16 sm:pb-20 overflow-hidden" id="top" style="background:linear-gradient(180deg,#F8FAFF 0%,#fff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-50" style="background:radial-gradient(ellipse 60% 50% at 80% 20%,rgba(129,140,248,.12),transparent),radial-gradient(ellipse 50% 40% at 10% 80%,rgba(34,211,238,.08),transparent)"></div>

    <div class="relative z-10 max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div class="text-center lg:text-start reveal">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-5">
                    <i class="fas fa-circle text-[6px] text-emerald-500"></i>
                    {{ $tr('hero.badge') }}
                </span>
                <h1 class="font-display text-[2rem] sm:text-[2.65rem] lg:text-[3.25rem] font-extrabold text-slate-900 leading-[1.15] mb-5">
                    {{ $tr('hero.title') }}
                    <span class="block text-indigo-600">{{ $tr('hero.highlight') }}</span>
                </h1>
                <p class="text-slate-600 text-base sm:text-lg leading-8 mb-8 max-w-xl mx-auto lg:mx-0">
                    {{ $tr('hero.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-8">
                    <a href="{{ route('register') }}" class="sana-btn-primary text-base px-7 py-3.5 justify-center">
                        {{ $tr('hero.cta_primary') }}
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <a href="{{ route('public.courses') }}" class="sana-btn-ghost text-base px-7 py-3.5 justify-center bg-white">
                        {{ $tr('hero.cta_courses') }}
                    </a>
                </div>
                <div class="flex flex-wrap gap-4 justify-center lg:justify-start text-sm">
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="font-extrabold text-slate-900" dir="ltr">{{ $fmt((int)($hs['learners'] ?? 0)) }}</span>
                        <span>{{ $tr('hero.stat_students') }}</span>
                    </div>
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="font-extrabold text-slate-900" dir="ltr">{{ $fmt((int)($hs['courses'] ?? 0)) }}</span>
                        <span>{{ $tr('hero.stat_courses') }}</span>
                    </div>
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="font-extrabold text-slate-900" dir="ltr">{{ $fmt((int)($hs['certificates'] ?? 0)) }}</span>
                        <span>{{ $tr('hero.stat_certificates') }}</span>
                    </div>
                </div>
            </div>

            <div class="reveal">
                @if($spotlight)
                <a href="{{ route('public.course.show', $spotlight->id) }}" class="block sana-card overflow-hidden !shadow-[0_24px_56px_-28px_rgba(99,102,241,.35)] hover:!translate-y-0 hover:scale-[1.01] transition-transform">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-2 bg-slate-50/80">
                        <span class="text-xs font-bold text-indigo-700">{{ $tr('hero.spotlight_label') }}</span>
                        @if($spotlight->is_featured)
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800">{{ __('public.featured_course_badge') }}</span>
                        @endif
                    </div>
                    <div class="aspect-[16/10] bg-slate-100 relative">
                        @if($thumbUrl)
                            <img src="{{ $thumbUrl }}" alt="{{ $spotlight->title }}" class="w-full h-full object-cover" loading="eager">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-book-open text-5xl"></i></div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h2 class="font-display font-bold text-lg text-slate-900 leading-snug mb-2 line-clamp-2">{{ $spotlight->title }}</h2>
                        @if($instName)
                            <p class="text-sm text-slate-500 mb-3">{{ $tr('hero.spotlight_by') }}: <span class="font-semibold text-slate-700">{{ $instName }}</span></p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mb-4">
                            <span><i class="fas fa-list-ul me-1"></i> {{ $spotlight->lessons_count ?? 0 }} {{ $tr('hero.spotlight_lessons') }}</span>
                            @if((int)($spotlight->students_count ?? 0) > 0)
                                <span><i class="fas fa-users me-1"></i> {{ number_format((int)$spotlight->students_count) }} {{ $tr('courses.students') }}</span>
                            @endif
                            @if($spotlight->rating)
                                <span class="text-amber-600 font-semibold"><i class="fas fa-star"></i> {{ number_format((float)$spotlight->rating, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            @if($isFree)
                                <span class="font-bold text-emerald-600">{{ $tr('courses.free') }}</span>
                            @else
                                <span class="font-bold text-indigo-600 tabular-nums">{{ number_format($spotlight->effectivePurchasePrice(), 0) }} {{ __('public.currency') }}</span>
                            @endif
                            <span class="text-sm font-bold text-indigo-600">{{ $tr('hero.spotlight_view') }} <i class="fas fa-arrow-left text-xs"></i></span>
                        </div>
                    </div>
                </a>
                @else
                <div class="sana-card p-8 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mb-4"><i class="fas fa-graduation-cap"></i></div>
                    <p class="text-slate-600 mb-4">{{ $tr('hero.no_spotlight') }}</p>
                    <a href="{{ route('public.courses') }}" class="sana-btn-primary justify-center">{{ $tr('hero.cta_courses') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

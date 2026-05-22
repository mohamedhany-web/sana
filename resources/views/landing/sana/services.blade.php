@if(isset($homeCategories) && $homeCategories->isNotEmpty())
<section class="py-14 sm:py-20 bg-white border-t border-slate-100" id="services">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-violet-50 text-violet-700 mb-3">{{ $tr('services.badge') }}</span>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $tr('services.title') }}</h2>
                <p class="text-slate-500 mt-2 max-w-xl">{{ $tr('services.subtitle') }}</p>
            </div>
            <a href="{{ route('public.services.index') }}" class="sana-btn-ghost shrink-0">{{ $tr('services.view_all') }}</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($homeCategories as $svc)
            <a href="{{ $svc['url'] }}" class="sana-card p-5 reveal block hover:border-indigo-200">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                    <i class="fas {{ $svc['icon'] }}"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">{{ $svc['name'] }}</h3>
                <p class="text-sm text-slate-500 leading-7">{{ $svc['description'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

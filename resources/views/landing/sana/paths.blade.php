@if(isset($landingPaths) && $landingPaths->isNotEmpty())
<section class="py-14 sm:py-20 bg-slate-50" id="paths">
    <div class="max-w-[1280px] mx-auto px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white text-indigo-700 border border-indigo-100 mb-3">{{ $tr('paths.badge') }}</span>
                <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $tr('paths.title') }}</h2>
                <p class="text-slate-500 mt-2 max-w-xl text-sm sm:text-base">{{ $tr('paths.subtitle') }}</p>
            </div>
            <a href="{{ route('public.courses') }}" class="sana-btn-ghost shrink-0 bg-white">{{ $tr('paths.view_all') }}</a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($landingPaths->take(6) as $path)
            @php
                $pathFree = (float)($path->price ?? 0) <= 0;
                $pathUrl = route('public.learning-path.show', $path->slug);
            @endphp
            <a href="{{ $pathUrl }}" class="sana-card reveal overflow-hidden flex flex-col group !p-0">
                <div class="aspect-[16/9] bg-slate-100 relative overflow-hidden">
                    @if($path->image_url ?? null)
                        <img src="{{ $path->image_url }}" alt="{{ $path->name }}" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-cyan-50 text-indigo-300">
                            <i class="fas fa-route text-4xl"></i>
                        </div>
                    @endif
                    @if($path->courses_count > 0)
                        <span class="absolute top-3 start-3 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white/95 text-slate-700 shadow-sm">
                            {{ $path->courses_count }} {{ $tr('paths.courses_in_path') }}
                        </span>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-display font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $path->name }}</h3>
                    @if(!empty($path->description))
                        <p class="text-xs text-slate-500 line-clamp-2 mb-4 flex-1">{{ Str::limit(strip_tags($path->description), 100) }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-slate-100">
                        @if($pathFree)
                            <span class="font-bold text-emerald-600 text-sm">{{ $tr('paths.free') }}</span>
                        @else
                            <span class="font-bold text-indigo-600 text-sm tabular-nums">{{ number_format($path->price, 0) }} {{ __('public.currency') }}</span>
                        @endif
                        <span class="text-xs font-bold text-indigo-600">{{ $tr('paths.view_path') }} <i class="fas fa-arrow-left text-[10px]"></i></span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

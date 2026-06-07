@if(!empty($curriculum))
<div class="sana-cd-curriculum" x-data="{ openModules: @js(collect($curriculum)->pluck('id')->map(fn ($id) => (string) $id)->values()->all()) }">
    @foreach($curriculum as $module)
        @include('landing.sana.partials.course-curriculum-module', ['module' => $module, 'depth' => 0])
    @endforeach
</div>
@else
<p class="sana-cd-section__sub">سيتم إضافة محتوى المنهج قريباً.</p>
@endif

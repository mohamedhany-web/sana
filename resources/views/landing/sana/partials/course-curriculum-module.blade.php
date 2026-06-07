@php
    $moduleId = is_string($module['id'] ?? null) ? $module['id'] : (string) ($module['id'] ?? uniqid());
    $itemCount = count($module['items'] ?? []);
    $padding = ($depth ?? 0) > 0 ? 'margin-inline-start:16px' : '';
@endphp
<div class="sana-cd-module" style="{{ $padding }}"
     :class="openModules.includes('{{ $moduleId }}') && 'is-open'">
    <button type="button" class="sana-cd-module__toggle"
            @click="openModules.includes('{{ $moduleId }}') ? openModules = openModules.filter(id => id !== '{{ $moduleId }}') : openModules.push('{{ $moduleId }}')">
        <div>
            <strong>{{ $module['title'] ?? 'وحدة' }}</strong>
            @if(!empty($module['description']))
                <span style="display:block;font-size:0.75rem;color:var(--muted);font-weight:600;margin-top:4px">{{ Str::limit($module['description'], 80) }}</span>
            @endif
        </div>
        <span>
            {{ $itemCount }} عنصر
            <i class="fas fa-chevron-down chevron"></i>
        </span>
    </button>
    <div class="sana-cd-module__body">
        @foreach($module['items'] ?? [] as $item)
            <div class="sana-cd-lesson">
                <span class="sana-cd-lesson__icon"><i class="fas {{ $item['icon'] ?? 'fa-play-circle' }}"></i></span>
                <span>{{ $item['title'] ?? '' }}</span>
                <span class="sana-cd-lesson__meta">{{ $item['type_label'] ?? '' }}@if(!empty($item['duration'])) · {{ $item['duration'] }} د@endif</span>
            </div>
        @endforeach
        @foreach($module['children'] ?? [] as $child)
            @include('landing.sana.partials.course-curriculum-module', ['module' => $child, 'depth' => ($depth ?? 0) + 1])
        @endforeach
    </div>
</div>

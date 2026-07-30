{{-- رندر ديناميكي لحقل واحد من منشئ النموذج --}}
@php
    /** @var \App\Models\TutorFormField $field */
    $key = $field->field_key;
    $label = $field->label;
    $required = $field->is_required;
    $help = $field->help_text;
    $placeholder = $field->placeholder;
    $width = $field->width === 'half' ? 'sm:col-span-1' : 'sm:col-span-2';
    $settings = $field->settings ?? [];
    $opts = $field->resolvedOptions();
    $reqMark = $required ? ' *' : '';
    $oldWeekly = $oldWeekly ?? old('weekly_availability', []);
    $prefill = $prefill ?? [];
@endphp

@if($field->field_type === 'info')
    <div class="{{ $width }} rounded-xl bg-sky-50 border border-sky-100 p-4 text-sm text-sky-900">
        <p class="font-bold m-0">{{ $label }}</p>
        @if($help)<p class="m-0 mt-1 text-xs">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'country_phone')
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <div class="grid grid-cols-[7.5rem_1fr] gap-2">
            <select name="country_code" class="ta-field" dir="ltr" @if($required) required @endif>
                @foreach($phoneCountries ?? [] as $c)
                    <option value="{{ $c['dial_code'] }}" @selected(old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'])>{{ $c['dial_code'] }}</option>
                @endforeach
            </select>
            <input type="tel" name="phone" class="ta-field flex-1" dir="ltr" value="{{ old('phone') }}" @if($required) required @endif placeholder="5xxxxxxxx">
        </div>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'password')
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <input type="password" name="{{ $key }}" class="ta-field" @if($required) required @endif autocomplete="new-password">
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'textarea')
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <textarea name="{{ $key }}" class="ta-field" rows="{{ (int) ($settings['rows'] ?? 3) }}" @if($required) required @endif placeholder="{{ $placeholder }}">{{ old($key) }}</textarea>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif(in_array($field->field_type, ['text', 'email', 'tel', 'url', 'number', 'date'], true))
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <input type="{{ $field->field_type === 'tel' ? 'tel' : $field->field_type }}"
               name="{{ $key }}" class="ta-field"
               value="{{ old($key, $prefill[$key] ?? '') }}"
               @if($required) required @endif
               @if($placeholder) placeholder="{{ $placeholder }}" @endif
               @if(isset($settings['min'])) min="{{ $settings['min'] }}" @endif
               @if(isset($settings['max']) && $field->field_type === 'number') max="{{ $settings['max'] }}" @endif
               @if($field->field_type === 'url') dir="ltr" @endif>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'select')
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <select name="{{ $key }}" class="ta-field" @if($required) required @endif>
            <option value="">— اختر —</option>
            @foreach($opts as $ov => $ol)
                <option value="{{ $ov }}" @selected(old($key) == $ov)>{{ $ol }}</option>
            @endforeach
        </select>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'radio')
    <div class="{{ $width }}">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        <div class="ta-check-grid">
            @foreach($opts as $ov => $ol)
                <label class="ta-check-item">
                    <input type="radio" name="{{ $key }}" value="{{ $ov }}" @checked(old($key) == $ov) @if($required) required @endif>
                    {{ $ol }}
                </label>
            @endforeach
        </div>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif(in_array($field->field_type, ['checkbox_group', 'multiselect'], true))
    <div class="{{ $width }}">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        <div class="ta-check-grid" data-tutor-check-group="{{ $key }}" @if($required) data-required-group="1" @endif>
            @foreach($opts as $ov => $ol)
                <label class="ta-check-item">
                    <input type="checkbox" name="{{ $key }}[]" value="{{ $ov }}" @checked(in_array((string) $ov, array_map('strval', old($key, [])), true))>
                    {{ $ol }}
                </label>
            @endforeach
        </div>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'subjects')
    <div class="{{ $width }}">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        <div class="ta-check-grid" data-tutor-check-group="subject_ids" @if($required) data-required-group="1" @endif>
            @foreach($subjects as $s)
                <label class="ta-check-item">
                    <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, old('subject_ids', []), true))>
                    {{ $s->name }}
                </label>
            @endforeach
        </div>
    </div>
@elseif($field->field_type === 'academic_years')
    <div class="{{ $width }}">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        <div class="ta-check-grid" data-tutor-check-group="academic_year_ids" @if($required) data-required-group="1" @endif>
            @foreach($years as $y)
                <label class="ta-check-item">
                    <input type="checkbox" name="academic_year_ids[]" value="{{ $y->id }}" @checked(in_array($y->id, old('academic_year_ids', []), true))>
                    {{ $y->name }}
                </label>
            @endforeach
        </div>
    </div>
@elseif($field->field_type === 'file')
    <div class="{{ $width }}">
        <label class="ta-label">{{ $label }}{{ $reqMark }}</label>
        <input type="file" name="{{ $key }}" class="ta-field"
               @if($required) required @endif
               @if(!empty($settings['accept'])) accept="{{ $settings['accept'] }}" @endif>
        @if($help)<p class="text-xs text-slate-500 m-0 mt-1">{{ $help }}</p>@endif
    </div>
@elseif($field->field_type === 'weekly_availability')
    <div class="{{ $width }} space-y-2">
        <p class="ta-label m-0">{{ $label }}{{ $reqMark }}</p>
        @if($help)<p class="text-sm text-slate-600 m-0">{{ $help }}</p>@endif
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-sm">
                <thead class="bg-slate-50"><tr><th class="p-2 text-right">اليوم</th><th class="p-2 text-right">الفترات</th><th class="p-2 text-right">ملاحظات</th></tr></thead>
                <tbody>
                @foreach($formOptions['weekdays'] ?? [] as $day => $dayLabel)
                <tr class="border-t border-slate-100">
                    <td class="p-2 font-bold whitespace-nowrap">{{ $dayLabel }}</td>
                    <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][periods]" class="ta-field text-xs" placeholder="مثال: 4–8 م" value="{{ $oldWeekly[$day]['periods'] ?? '' }}"></td>
                    <td class="p-2"><input type="text" name="weekly_availability[{{ $day }}][notes]" class="ta-field text-xs" placeholder="—" value="{{ $oldWeekly[$day]['notes'] ?? '' }}"></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif($field->field_type === 'video_pair')
    @include('tutor.partials.field-video-pair', ['field' => $field, 'required' => $required])
@elseif($field->field_type === 'commitments')
    <div class="{{ $width }} space-y-2">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        @foreach($opts as $ck => $ct)
            <label class="ta-check-item">
                <input type="hidden" name="commitments[{{ $ck }}]" value="0">
                <input type="checkbox" name="commitments[{{ $ck }}]" value="1" @checked(filter_var(old('commitments.'.$ck), FILTER_VALIDATE_BOOLEAN)) @if($required) required data-tc-commitment @endif>
                {{ $ct }}
            </label>
        @endforeach
    </div>
@elseif($field->field_type === 'declaration')
    <div class="{{ $width }} space-y-3">
        <label class="ta-check-item">
            <input type="checkbox" name="declaration_agreed" value="1" @checked(old('declaration_agreed')) @if($required) required @endif>
            أقرّ بأن جميع البيانات صحيحة وأوافق على سياسات الأكاديمية
        </label>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="ta-label">الاسم{{ $reqMark }}</label>
                <input type="text" name="declaration_name" class="ta-field" value="{{ old('declaration_name') }}" @if($required) required @endif>
            </div>
            <div>
                <label class="ta-label">التوقيع{{ $reqMark }}</label>
                <input type="text" name="declaration_signature" class="ta-field" value="{{ old('declaration_signature') }}" @if($required) required @endif placeholder="اكتب اسمك كتوقيع">
            </div>
        </div>
    </div>
@elseif($field->field_type === 'matching_modes')
    <div class="{{ $width }}">
        <p class="ta-label">{{ $label }}{{ $reqMark }}</p>
        <div class="ta-check-grid" data-tutor-check-group="matching_modes" @if($required) data-required-group="1" @endif>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="pick_teacher" @checked(in_array('pick_teacher', old('matching_modes', []), true))> {{ __('tutor.matching_pick_teacher') }}</label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="self_schedule" @checked(in_array('self_schedule', old('matching_modes', []), true))> {{ __('tutor.matching_self_schedule') }}</label>
            <label class="ta-check-item"><input type="checkbox" name="matching_modes[]" value="assisted" @checked(in_array('assisted', old('matching_modes', []), true))> {{ __('tutor.matching_assisted') }}</label>
        </div>
    </div>
@endif

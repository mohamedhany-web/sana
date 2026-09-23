@extends('layouts.admin')

@section('title', __('منشئ نموذج التوظيف - ') . config('app.name'))
@section('header', __('منشئ نموذج توظيف المعلمين'))

@section('content')
@php
    $typeOptions = $typeOptions ?? [];
    $optionSources = [
        'specializations' => __('التخصصات (من الإعدادات)'),
        'curricula' => __('المناهج'),
        'stages' => __('المراحل'),
        'lesson_formats' => __('أنواع الحصص'),
        'tech_skills' => __('المهارات التقنية'),
        'commitments' => __('بنود الالتزام'),
        'weekdays' => __('أيام الأسبوع'),
    ];
@endphp

<div class="space-y-6">
    @foreach(['success', 'error', 'info'] as $flash)
        @if(session($flash))
            <div class="rounded-xl px-4 py-3 text-sm border
                {{ $flash === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : '' }}
                {{ $flash === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : '' }}
                {{ $flash === 'info' ? 'bg-sky-50 border-sky-200 text-sky-800' : '' }}
            ">{{ session($flash) }}</div>
        @endif
    @endforeach

    <section class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 m-0">{{ __('التحكم الكامل في نموذج `/tutor/apply`') }}</h2>
                <p class="text-sm text-slate-500 mt-2 m-0">
                    {{ __('حدّد الخطوات، انقل الحقول بينها، غيّر الإلزامي/الاختياري، وأضف خانات بأنواع مختلفة.') }}
                    {{ __('الحقول النظامية لا تُحذف حتى لا ينكسر التسجيل الحالي — يمكن إيقافها أو جعلها اختيارية (ما عدا الاسم والبريد وكلمة المرور).') }}
                </p>
                <p class="text-xs mt-2 m-0">
                    {{ __('الحالة') }}:
                    @if($enabled)
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">{{ __('مفعّل — النموذج الديناميكي يعمل') }}</span>
                    @else
                        <span class="inline-flex px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">{{ __('غير مفعّل — يُستخدم النموذج القديم حتى تُزرع الخطوات') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.instructor-applications.form-preview') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">
                    <i class="fas fa-eye"></i> {{ __('معاينة النموذج') }}
                </a>
                <a href="{{ route('tutor.apply') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-external-link-alt"></i> {{ __('الرابط العام') }}
                </a>
                @if($steps->isEmpty())
                <form method="POST" action="{{ route('admin.tutor-form-builder.seed') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-violet-700">
                        <i class="fas fa-magic"></i> {{ __('زرع الهيكل الافتراضي') }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </section>

    {{-- إضافة خطوة --}}
    <section class="rounded-3xl bg-white border border-slate-200 shadow p-6">
        <h3 class="font-bold text-slate-900 mb-4">{{ __('إضافة خطوة جديدة') }}</h3>
        <form method="POST" action="{{ route('admin.tutor-form-builder.steps.store') }}" class="grid sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('عنوان الخطوة') }}</label>
                <input type="text" name="title" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="{{ __('مثال: أسئلة إضافية') }}">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('النوع') }}</label>
                <select name="step_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="fields">{{ __('حقول') }}</option>
                    <option value="intro">{{ __('مقدمة') }}</option>
                    <option value="review">{{ __('مراجعة / إرسال') }}</option>
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-sky-600 text-white px-4 py-2.5 text-sm font-bold hover:bg-sky-700">{{ __('إضافة') }}</button>
            <div class="sm:col-span-4">
                <input type="text" name="description" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="{{ __('وصف اختياري يظهر أعلى الخطوة') }}">
            </div>
        </form>
    </section>

    @forelse($steps as $step)
    <section class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-900 m-0 flex items-center gap-2">
                    <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-sky-100 text-sky-700 text-sm font-black">{{ $step->sort_order }}</span>
                    {{ $step->title }}
                    @if($step->is_system)<span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">{{ __('نظامي') }}</span>@endif
                    @if(!$step->is_active)<span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full">{{ __('متوقف') }}</span>@endif
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-1">slug: {{ $step->slug }} — {{ __('نوع') }}: {{ $step->step_type }} — {{ $step->fields->count() }} {{ __('حقل') }}</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            <form method="POST" action="{{ route('admin.tutor-form-builder.steps.update', $step) }}" class="grid sm:grid-cols-6 gap-3 items-end border border-slate-100 rounded-2xl p-4">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('العنوان') }}</label>
                    <input type="text" name="title" value="{{ $step->title }}" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('الترتيب') }}</label>
                    <input type="number" name="sort_order" value="{{ $step->sort_order }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('النوع') }}</label>
                    <select name="step_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach(['fields'=>__('حقول'),'intro'=>__('مقدمة'),'review'=>__('مراجعة')] as $tv => $tl)
                            <option value="{{ $tv }}" @selected($step->step_type === $tv)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 pb-2">
                    <input type="checkbox" name="is_active" value="1" @checked($step->is_active)> {{ __('نشطة') }}
                </label>
                <button type="submit" class="rounded-xl bg-slate-800 text-white px-3 py-2 text-sm font-semibold">{{ __('حفظ الخطوة') }}</button>
                <div class="sm:col-span-6">
                    <input type="text" name="description" value="{{ $step->description }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="{{ __('الوصف') }}">
                </div>
            </form>

            @unless($step->is_system)
            <form method="POST" action="{{ route('admin.tutor-form-builder.steps.destroy', $step) }}" onsubmit="return confirm(@json(__('حذف الخطوة وكل حقولها؟')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-rose-600 font-semibold hover:underline">{{ __('حذف الخطوة') }}</button>
            </form>
            @endunless

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-right">#</th>
                            <th class="px-3 py-2 text-right">{{ __('الحقل') }}</th>
                            <th class="px-3 py-2 text-right">{{ __('النوع') }}</th>
                            <th class="px-3 py-2 text-right">{{ __('إلزامي') }}</th>
                            <th class="px-3 py-2 text-right">{{ __('نشط') }}</th>
                            <th class="px-3 py-2 text-right">{{ __('الخطوة') }}</th>
                            <th class="px-3 py-2 text-center">{{ __('إجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($step->fields as $field)
                        <tr class="{{ $field->is_active ? '' : 'opacity-50' }}">
                            <td class="px-3 py-3 align-top">{{ $field->sort_order }}</td>
                            <td class="px-3 py-3 align-top">
                                <div class="font-semibold text-slate-900">{{ $field->label }}</div>
                                <div class="text-[11px] text-slate-400 font-mono" dir="ltr">{{ $field->field_key }}</div>
                                @if($field->is_system)<span class="text-[10px] bg-slate-100 px-1.5 rounded">{{ __('نظامي') }}</span>@endif
                            </td>
                            <td class="px-3 py-3 align-top whitespace-nowrap">{{ $field->typeLabel() }}</td>
                            <td class="px-3 py-3 align-top">{{ $field->is_required ? __('نعم') : __('لا') }}</td>
                            <td class="px-3 py-3 align-top">{{ $field->is_active ? __('نعم') : __('لا') }}</td>
                            <td class="px-3 py-3 align-top text-xs text-slate-500">{{ $step->title }}</td>
                            <td class="px-3 py-3 align-top">
                                <details class="text-right">
                                    <summary class="cursor-pointer text-sky-700 font-semibold text-xs list-none">{{ __('تعديل') }}</summary>
                                    <form method="POST" action="{{ route('admin.tutor-form-builder.fields.update', $field) }}" class="mt-3 space-y-2 min-w-[18rem] bg-slate-50 border border-slate-200 rounded-xl p-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="label" value="{{ $field->label }}" required class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="{{ __('العنوان') }}">
                                        <select name="step_id" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                            @foreach($steps as $s)
                                                <option value="{{ $s->id }}" @selected($s->id === $field->step_id)>{{ $s->sort_order }}. {{ $s->title }}</option>
                                            @endforeach
                                        </select>
                                        @if($field->is_system)
                                            <input type="hidden" name="field_type" value="{{ $field->field_type }}">
                                            <p class="text-[10px] text-slate-500 m-0">{{ __('النوع ثابت للحقول النظامية') }}: {{ $field->typeLabel() }}</p>
                                        @else
                                            <select name="field_type" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                                @foreach($typeOptions as $tv => $tl)
                                                    <option value="{{ $tv }}" @selected($field->field_type === $tv)>{{ $tl }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="width" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                                <option value="full" @selected($field->width === 'full')>{{ __('عرض كامل') }}</option>
                                                <option value="half" @selected($field->width === 'half')>{{ __('نصف') }}</option>
                                            </select>
                                            <input type="number" name="sort_order" value="{{ $field->sort_order }}" class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="{{ __('ترتيب') }}">
                                        </div>
                                        <input type="text" name="help_text" value="{{ $field->help_text }}" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="{{ __('نص مساعدة') }}">
                                        <input type="text" name="placeholder" value="{{ $field->placeholder }}" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="placeholder">
                                        @unless($field->is_system)
                                        @php
                                            $optionsText = '';
                                            if (! empty($field->options['items']) && is_array($field->options['items'])) {
                                                foreach ($field->options['items'] as $it) {
                                                    $optionsText .= ($it['value'] ?? '').'|'.($it['label'] ?? '')."\n";
                                                }
                                            }
                                        @endphp
                                        <textarea name="options_text" rows="3" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs" placeholder="{{ __('خيارات (سطر لكل خيار) قيمة|عنوان أو عنوان فقط') }}">{{ $optionsText }}</textarea>
                                        <select name="options_source" class="w-full rounded-lg border border-slate-200 px-2 py-1.5 text-xs">
                                            <option value="">{{ __('بدون مصدر إعدادات') }}</option>
                                            @foreach($optionSources as $sv => $sl)
                                                <option value="{{ $sv }}" @selected(($field->options['source'] ?? null) === $sv)>{{ $sl }}</option>
                                            @endforeach
                                        </select>
                                        @endunless
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_required" value="1" @checked($field->is_required) @disabled(in_array($field->field_key, ['name','email','password'], true))>
                                            {{ __('إلزامي') }}
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-xs">
                                            <input type="checkbox" name="is_active" value="1" @checked($field->is_active) @disabled(in_array($field->field_key, ['name','email','password'], true))>
                                            {{ __('نشط') }}
                                        </label>
                                        <button type="submit" class="w-full rounded-lg bg-sky-600 text-white text-xs font-bold py-2">{{ __('حفظ الحقل') }}</button>
                                    </form>
                                    @unless($field->is_system)
                                    <form method="POST" action="{{ route('admin.tutor-form-builder.fields.destroy', $field) }}" class="mt-2" onsubmit="return confirm(@json(__('حذف الحقل؟')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-600 font-semibold">{{ __('حذف') }}</button>
                                    </form>
                                    @endunless
                                </details>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($step->step_type === 'fields' || $step->step_type === 'review')
            <details class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/40 p-4">
                <summary class="cursor-pointer font-bold text-violet-800 text-sm">{{ __('+ إضافة حقل مخصص لهذه الخطوة') }}</summary>
                <form method="POST" action="{{ route('admin.tutor-form-builder.fields.store') }}" class="mt-4 grid sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="step_id" value="{{ $step->id }}">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('عنوان الحقل') }}</label>
                        <input type="text" name="label" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('مفتاح تقني (اختياري)') }}</label>
                        <input type="text" name="field_key" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" dir="ltr" placeholder="custom_question_1">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('النوع') }}</label>
                        <select name="field_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            @foreach(['text','textarea','email','url','number','tel','date','select','radio','checkbox_group','multiselect','file','info'] as $tv)
                                <option value="{{ $tv }}">{{ $typeOptions[$tv] ?? $tv }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('العرض') }}</label>
                        <select name="width" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="full">{{ __('كامل') }}</option>
                            <option value="half">{{ __('نصف') }}</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('خيارات القائمة (سطر لكل خيار: قيمة|عنوان)') }}</label>
                        <textarea name="options_text" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="{{ __('yes|نعم&#10;no|لا') }}"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('أو مصدر من الإعدادات') }}</label>
                        <select name="options_source" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                            <option value="">—</option>
                            @foreach($optionSources as $sv => $sl)
                                <option value="{{ $sv }}">{{ $sl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_required" value="1" checked> {{ __('إلزامي') }}</label>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="rounded-xl bg-violet-600 text-white px-4 py-2.5 text-sm font-bold hover:bg-violet-700">{{ __('إضافة الحقل') }}</button>
                    </div>
                </form>
            </details>
            @endif
        </div>
    </section>
    @empty
    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8 text-center text-amber-900">
        {{ __('لا يوجد مخطط بعد. اضغط «زرع الهيكل الافتراضي» لنسخ النموذج الحالي كما هو، ثم عدّله بحرية.') }}
    </div>
    @endforelse
</div>
@endsection

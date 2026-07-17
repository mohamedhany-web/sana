@php
    $formSteps = $formSteps ?? collect();
    $oldWeekly = old('weekly_availability', []);
@endphp

@foreach($formSteps as $stepIndex => $step)
@php
    $stepNum = $stepIndex + 1;
    $isLast = $stepNum === $formSteps->count();
    $isIntro = $step->step_type === 'intro';
@endphp
<div x-show="step === {{ $stepNum }}" x-cloak class="ix-step-panel space-y-4" data-tutor-step="{{ $stepNum }}">
    <h2 class="ta-headline" style="font-size:1.5rem">{{ $step->title }}</h2>
    @if($step->description)
        <p class="text-sm text-slate-600 m-0">{{ $step->description }}</p>
    @endif

    @if($isIntro)
        <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-sm text-sky-900 space-y-2">
            <ul class="m-0 pr-4 space-y-1">
                <li>بياناتك الشخصية والمؤهلات</li>
                <li>التخصصات والمناهج والتوفر</li>
                <li>فيديو الشرح والمستندات</li>
            </ul>
            <p class="m-0 text-xs"><a href="{{ route('tutor.policy') }}" class="text-sky-700 font-bold" target="_blank" rel="noopener">اطّلع على سياسة انضمام المعلمين</a></p>
        </div>
        <div class="ta-actions">
            <button type="button" class="ta-btn-primary" @click="next()">ابدأ التقديم</button>
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($step->activeFields as $field)
                @include('tutor.partials.field-renderer', [
                    'field' => $field,
                    'subjects' => $subjects,
                    'years' => $years,
                    'phoneCountries' => $phoneCountries,
                    'defaultCountry' => $defaultCountry,
                    'formOptions' => $formOptions,
                    'oldWeekly' => $oldWeekly,
                ])
            @endforeach
        </div>

        <div class="ta-actions flex flex-wrap gap-2">
            @if($stepNum > 1)
                <button type="button" class="ta-btn-ghost" @click="prev()">السابق</button>
            @endif
            @if($isLast)
                <button type="submit" class="ta-btn-primary" :disabled="submitting" @click="submitting = true">
                    إرسال الطلب
                </button>
            @else
                <button type="button" class="ta-btn-primary" @click="next()">التالي</button>
            @endif
        </div>
    @endif
</div>
@endforeach

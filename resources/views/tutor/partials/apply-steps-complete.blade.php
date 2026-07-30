@php
    $formOptions = $formOptions ?? config('tutor_application');
    $prefill = $prefill ?? [];
    $formSteps = $formSteps ?? collect();
    $useSchema = $formSteps->isNotEmpty();
    $oldWeekly = old('weekly_availability', []);
    $videoMaxMb = \App\Services\TutorApplicationFormService::videoMaxMb();

    if (! $useSchema) {
        $completeSteps = [1 => 'غير متاح'];
    } else {
        $completeSteps = [];
        foreach ($formSteps as $i => $step) {
            $completeSteps[$i + 1] = $step->title;
        }
    }
    $completeTotal = count($completeSteps);
    $completeResumeStep = (int) ($completeResumeStep ?? 1);
@endphp

{{-- مراحل إكمال الملف — JS عادي؛ الحقول من منشئ النماذج عند التفعيل --}}
<div id="tutor-complete-start" class="space-y-5" data-complete-wizard="1" data-initial-step="{{ max(1, min($completeTotal, $completeResumeStep)) }}">
    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex items-center justify-between gap-3 mb-2">
            <p class="text-sm font-bold text-slate-800 m-0">
                المرحلة <span id="tc-step-num">1</span> من {{ $completeTotal }}
                — <span id="tc-step-title">{{ $completeSteps[1] ?? '' }}</span>
            </p>
            <p class="text-xs font-bold text-slate-500 m-0"><span id="tc-step-pct">0</span>%</p>
        </div>
        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
            <div id="tc-step-bar" class="h-full rounded-full transition-all" style="width:0%;background:var(--edu-primary)"></div>
        </div>
        <div class="flex flex-wrap gap-1.5 mt-3">
            @foreach($completeSteps as $n => $label)
                <button type="button" class="tc-dot text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 text-slate-500 bg-slate-50"
                        data-tc-goto="{{ $n }}" title="{{ $label }}">{{ $n }}</button>
            @endforeach
        </div>
    </div>

    <p id="tc-step-error" class="ta-step-error" style="display:none"></p>

    @if($useSchema)
        @foreach($formSteps as $stepIndex => $step)
            @php
                $stepNum = $stepIndex + 1;
                $isLast = $stepNum === $completeTotal;
            @endphp
            <section class="ix-step-panel space-y-4 tc-panel" data-tc-step="{{ $stepNum }}" style="{{ $stepNum === 1 ? 'display:block' : 'display:none' }}">
                <h2 class="ta-headline" style="font-size:1.5rem">{{ $step->title }}</h2>
                @if(!empty($step->description))
                    <p class="text-sm text-slate-600 m-0">{{ $step->description }}</p>
                @endif

                @if($stepNum === 1 && !empty($prefill['email']))
                    <p class="text-xs text-slate-500 m-0">البريد (يوزر الدخول): <span dir="ltr">{{ $prefill['email'] }}</span></p>
                @endif

                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($step->activeFields as $field)
                        @include('tutor.partials.field-renderer', [
                            'field' => $field,
                            'subjects' => $subjects,
                            'years' => $years,
                            'phoneCountries' => $phoneCountries ?? [],
                            'defaultCountry' => $defaultCountry ?? null,
                            'formOptions' => $formOptions,
                            'oldWeekly' => $oldWeekly,
                            'prefill' => $prefill,
                        ])
                    @endforeach
                </div>

                <div class="ta-actions flex justify-between gap-2">
                    @if($stepNum > 1)
                        <button type="button" class="ta-btn-ghost tc-prev">السابق</button>
                    @else
                        <span></span>
                    @endif
                    @if($isLast)
                        @if(!empty($formPreview))
                            <button type="button" class="ta-btn-accent" disabled>معاينة فقط — لا إرسال</button>
                        @else
                            <button type="submit" class="ta-btn-accent ix-cta-pulse" id="tutor-complete-submit">
                                إرسال الملف للإدارة <i class="fas fa-paper-plane"></i>
                            </button>
                        @endif
                    @else
                        <button type="button" class="ta-btn-primary tc-next">التالي <i class="fas fa-arrow-left text-xs"></i></button>
                    @endif
                </div>
            </section>
        @endforeach
    @else
        <div class="ta-alert-err">
            منشئ نماذج التوظيف غير مفعّل أو لا توجد خطوات نشطة. من لوحة الإدارة فعّل الخطوات ثم أعد المحاولة.
        </div>
    @endif
</div>

<script>
(function () {
    var root = document.getElementById('tutor-complete-start');
    if (!root) return;
    var titles = @json(array_values($completeSteps));
    var total = {{ $completeTotal }};
    var step = 1;
    var errEl = document.getElementById('tc-step-error');
    var numEl = document.getElementById('tc-step-num');
    var titleEl = document.getElementById('tc-step-title');
    var pctEl = document.getElementById('tc-step-pct');
    var barEl = document.getElementById('tc-step-bar');
    var form = document.getElementById('tutorApplyForm');

    function panels() { return Array.prototype.slice.call(root.querySelectorAll('.tc-panel')); }
    function panel(n) { return root.querySelector('.tc-panel[data-tc-step="' + n + '"]'); }

    function showError(msg) {
        if (!errEl) return;
        if (!msg) { errEl.style.display = 'none'; errEl.textContent = ''; return; }
        errEl.style.display = 'block';
        errEl.textContent = msg;
        errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setStep(n) {
        step = Math.max(1, Math.min(total, n));
        panels().forEach(function (p) {
            var s = parseInt(p.getAttribute('data-tc-step'), 10);
            p.style.display = s === step ? 'block' : 'none';
        });
        var pct = Math.round(((step - 1) / Math.max(total - 1, 1)) * 100);
        if (numEl) numEl.textContent = String(step);
        if (titleEl) titleEl.textContent = titles[step - 1] || '';
        if (pctEl) pctEl.textContent = String(pct);
        if (barEl) barEl.style.width = pct + '%';
        root.querySelectorAll('[data-tc-goto]').forEach(function (btn) {
            var s = parseInt(btn.getAttribute('data-tc-goto'), 10);
            btn.style.background = s === step ? 'var(--edu-primary)' : '';
            btn.style.color = s === step ? '#fff' : '';
            btn.style.borderColor = s === step ? 'transparent' : '';
        });
        showError('');
        root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function validatePanel(p) {
        if (!p) return true;
        var fields = p.querySelectorAll('input, select, textarea');
        for (var i = 0; i < fields.length; i++) {
            var el = fields[i];
            if (el.disabled || el.type === 'hidden') continue;
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.hasAttribute('required') && el.type === 'checkbox' && !el.checked) {
                    // مجموعات الالتزام / الإقرار
                    if (el.name && (el.name.indexOf('commitments') === 0 || el.name === 'declaration_agreed')) {
                        el.focus();
                        showError('يرجى الموافقة على البنود المطلوبة.');
                        return false;
                    }
                }
                continue;
            }
            if (el.hasAttribute('required') && !String(el.value || '').trim()) {
                if (el.type === 'file') {
                    el.focus();
                    showError('يرجى رفع الملفات المطلوبة في هذه المرحلة.');
                    return false;
                }
                el.classList.add('is-invalid');
                el.focus();
                showError('يرجى إكمال الحقول المطلوبة في هذه المرحلة.');
                return false;
            }
            if (el.type === 'file' && el.hasAttribute('required') && (!el.files || !el.files.length)) {
                el.focus();
                showError('يرجى رفع الملفات المطلوبة في هذه المرحلة.');
                return false;
            }
            el.classList.remove('is-invalid');
        }

        var groups = p.querySelectorAll('[data-tutor-check-group][data-required-group="1"], [data-tc-group]');
        for (var g = 0; g < groups.length; g++) {
            var boxes = groups[g].querySelectorAll('input[type="checkbox"]');
            var ok = Array.prototype.some.call(boxes, function (b) { return b.checked; });
            if (!ok) {
                showError('اختر عنصراً واحداً على الأقل من الحقول المطلوبة.');
                return false;
            }
        }

        // فيديو: ملف أو رابط — فقط إن كان الحقل ظاهراً ومطلوباً
        var videoWrap = p.querySelector('[data-tc-video-pair]');
        var legacyVideo = p.querySelector('[data-tc-video-file]');
        if ((videoWrap && videoWrap.getAttribute('data-required') === '1') || legacyVideo) {
            var file = p.querySelector('[name="demo_video"]');
            var link = p.querySelector('[name="demo_video_link"]');
            var hasFile = file && file.files && file.files.length;
            var hasLink = link && String(link.value || '').trim();
            if (!hasFile && !hasLink) {
                showError('ارفع فيديو أو أدخل رابط فيديو خارجي.');
                return false;
            }
        }

        var otherBox = p.querySelector('input[name="specializations[]"][value="other"]');
        var otherInput = p.querySelector('input[name="specializations_other"]');
        if (otherBox && otherBox.checked && otherInput && !String(otherInput.value || '').trim()) {
            otherInput.focus();
            showError('حدّد التخصصات الأخرى.');
            return false;
        }

        return true;
    }

    root.addEventListener('click', function (e) {
        var next = e.target.closest('.tc-next');
        var prev = e.target.closest('.tc-prev');
        var goto = e.target.closest('[data-tc-goto]');
        if (next) {
            e.preventDefault();
            if (!validatePanel(panel(step))) return;
            setStep(step + 1);
        } else if (prev) {
            e.preventDefault();
            setStep(step - 1);
        } else if (goto) {
            e.preventDefault();
            var target = parseInt(goto.getAttribute('data-tc-goto'), 10);
            if (target <= step) { setStep(target); return; }
            for (var s = step; s < target; s++) {
                if (!validatePanel(panel(s))) { setStep(s); return; }
            }
            setStep(target);
        }
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            for (var s = 1; s <= total; s++) {
                if (!validatePanel(panel(s))) {
                    e.preventDefault();
                    setStep(s);
                    return;
                }
            }
            var overlay = document.getElementById('tutor-complete-uploading');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.setAttribute('aria-hidden', 'false');
            }
        });
    }

    setStep(parseInt(root.getAttribute('data-initial-step') || '1', 10) || 1);
})();
</script>

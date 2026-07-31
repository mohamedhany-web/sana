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
<div id="tutor-complete-start" class="space-y-5" data-complete-wizard="1"
     data-initial-step="{{ max(1, min($completeTotal, $completeResumeStep)) }}"
     data-draft-key="sana_tutor_complete_draft_v2_{{ md5((string) ($prefill['email'] ?? auth()->id() ?? 'guest')) }}"
     data-has-server-errors="{{ ($applyStepErrors ?? null) && $applyStepErrors->isNotEmpty() ? '1' : '0' }}">

    <div id="tc-draft-banner" class="mb-1 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex flex-wrap items-center justify-between gap-3" style="display:none">
        <div>
            <strong class="font-bold">تم استعادة تقدمك السابق</strong>
            <span class="block text-xs mt-0.5 text-emerald-800">الملفات (فيديو / مستندات) لا يحفظها المتصفح — أعد رفعها قبل الإرسال إن لزم.</span>
        </div>
        <button type="button" id="tc-draft-clear" class="rounded-xl border border-emerald-300 bg-white px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-100">بدء من جديد</button>
    </div>

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
    var draftKey = root.getAttribute('data-draft-key') || 'sana_tutor_complete_draft_v2';
    var hasServerErrors = root.getAttribute('data-has-server-errors') === '1';
    var draftBanner = document.getElementById('tc-draft-banner');
    var draftTimer = null;
    var skipDraftSave = false;

    function panels() { return Array.prototype.slice.call(root.querySelectorAll('.tc-panel')); }
    function panel(n) { return root.querySelector('.tc-panel[data-tc-step="' + n + '"]'); }

    function showError(msg) {
        if (!errEl) return;
        if (!msg) { errEl.style.display = 'none'; errEl.textContent = ''; return; }
        errEl.style.display = 'block';
        errEl.textContent = msg;
        errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function setStep(n, opts) {
        opts = opts || {};
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
        if (!opts.silentScroll) {
            root.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        scheduleSaveDraft();
    }

    function collectDraftFields() {
        if (!form) return {};
        var data = {};
        Array.from(form.elements).forEach(function (el) {
            if (!el.name || el.name === '_token' || el.type === 'file') return;
            if (el.type === 'checkbox') {
                if (el.name.slice(-2) === '[]') return;
                if (el.name.indexOf('commitments[') === 0 || el.name === 'declaration_agreed' || el.name === 'video_use_external_link') {
                    data[el.name] = el.checked ? (el.value || '1') : '';
                } else if (el.checked) {
                    data[el.name] = el.value || '1';
                }
                return;
            }
            if (el.type === 'radio') {
                if (el.checked) data[el.name] = el.value;
                return;
            }
            if (el.name.slice(-2) === '[]') return;
            data[el.name] = el.value;
        });

        var arrayNames = {};
        Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(function (el) {
            if (!el.name || el.name.slice(-2) !== '[]') return;
            arrayNames[el.name] = true;
        });
        Object.keys(arrayNames).forEach(function (name) {
            var values = [];
            Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(function (el) {
                if (el.name === name && el.checked) values.push(el.value);
            });
            data[name] = values;
        });

        return data;
    }

    function saveDraft() {
        if (skipDraftSave || !form || form.getAttribute('data-preview') === '1') return;
        try {
            localStorage.setItem(draftKey, JSON.stringify({
                step: step,
                totalSteps: total,
                savedAt: Date.now(),
                fields: collectDraftFields(),
            }));
        } catch (e) {}
    }

    function scheduleSaveDraft() {
        if (draftTimer) clearTimeout(draftTimer);
        draftTimer = setTimeout(saveDraft, 300);
    }

    function applyFieldValue(name, value) {
        if (!form || name === '_token') return;
        if (name.slice(-2) === '[]' || Array.isArray(value)) {
            var values = Array.isArray(value) ? value.map(String) : [String(value)];
            Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(function (el) {
                if (el.name === name) el.checked = values.indexOf(String(el.value)) !== -1;
            });
            return;
        }
        if (name.indexOf('commitments[') === 0 || name === 'declaration_agreed' || name === 'video_use_external_link') {
            Array.from(form.querySelectorAll('input[type="checkbox"]')).forEach(function (el) {
                if (el.name === name) {
                    el.checked = value === '1' || value === el.value || value === true || value === 'true';
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            var hidden = form.querySelector('input[type="hidden"][name="' + name + '"]');
            if (hidden && typeof value === 'string') hidden.value = value;
            return;
        }
        Array.from(form.elements).forEach(function (el) {
            if (el.name !== name || el.type === 'file') return;
            if (el.type === 'radio') {
                el.checked = String(el.value) === String(value);
            } else if (el.type === 'checkbox') {
                el.checked = value === '1' || value === el.value || value === true;
            } else {
                el.value = value == null ? '' : String(value);
            }
        });
    }

    function restoreDraft() {
        try {
            var raw = localStorage.getItem(draftKey);
            if (!raw) return false;
            var payload = JSON.parse(raw);
            if (!payload || typeof payload !== 'object') return false;
            if (payload.savedAt && (Date.now() - payload.savedAt) > 14 * 24 * 60 * 60 * 1000) {
                localStorage.removeItem(draftKey);
                return false;
            }
            var fields = payload.fields || {};
            Object.keys(fields).forEach(function (name) {
                applyFieldValue(name, fields[name]);
            });

            // مزامنة Alpine لخطوة الفيديو إن وُجدت
            try {
                var wantExternal = String(fields.video_use_external_link || '') === '1';
                if (typeof Alpine !== 'undefined' && Alpine.$data) {
                    document.querySelectorAll('[x-data]').forEach(function (el) {
                        try {
                            var data = Alpine.$data(el);
                            if (data && Object.prototype.hasOwnProperty.call(data, 'useExternalLink')) {
                                data.useExternalLink = wantExternal;
                            }
                        } catch (e) {}
                    });
                }
            } catch (e) {}

            var resume = parseInt(payload.step, 10) || 1;
            var serverStep = parseInt(root.getAttribute('data-initial-step') || '1', 10) || 1;
            if (hasServerErrors && serverStep > 1) resume = serverStep;
            setStep(resume, { silentScroll: true });
            if (draftBanner) draftBanner.style.display = 'flex';
            return true;
        } catch (e) {
            return false;
        }
    }

    function clearDraft(resetUi) {
        try { localStorage.removeItem(draftKey); } catch (e) {}
        if (draftBanner) draftBanner.style.display = 'none';
        if (resetUi && form) {
            skipDraftSave = true;
            form.reset();
            skipDraftSave = false;
            setStep(1);
        }
    }

    function validatePanel(p) {
        if (!p) return true;
        var fields = p.querySelectorAll('input, select, textarea');
        for (var i = 0; i < fields.length; i++) {
            var el = fields[i];
            if (el.disabled || el.type === 'hidden') continue;
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.hasAttribute('required') && el.type === 'checkbox' && !el.checked) {
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

    var clearBtn = document.getElementById('tc-draft-clear');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (window.confirm('هل تريد مسح البيانات المحفوظة وبدء النموذج من جديد؟')) {
                clearDraft(true);
            }
        });
    }

    if (form) {
        form.addEventListener('input', scheduleSaveDraft, true);
        form.addEventListener('change', scheduleSaveDraft, true);
        form.addEventListener('submit', function (e) {
            for (var s = 1; s <= total; s++) {
                if (!validatePanel(panel(s))) {
                    e.preventDefault();
                    setStep(s);
                    return;
                }
            }
            try { localStorage.removeItem(draftKey); } catch (err) {}
            var overlay = document.getElementById('tutor-complete-uploading');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.setAttribute('aria-hidden', 'false');
            }
        });
    }

    var initial = parseInt(root.getAttribute('data-initial-step') || '1', 10) || 1;
    setStep(initial, { silentScroll: true });
    if (!hasServerErrors) {
        restoreDraft();
    } else {
        scheduleSaveDraft();
    }
})();
</script>

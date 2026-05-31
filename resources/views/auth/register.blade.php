@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
    $phoneCountriesJson = collect($phoneCountries ?? [])->map(fn ($c) => [
        'dial_code' => $c['dial_code'],
        'name_ar' => $c['name_ar'],
        'regex' => $c['validation']['regex'] ?? null,
        'placeholder' => $c['placeholder'] ?? '',
        'example' => $c['example'] ?? '',
    ])->values();
    $resumeStep = 1;
    if ($errors->any()) {
        if ($errors->has('name')) { $resumeStep = 2; }
        elseif ($errors->has('phone') || $errors->has('country_code')) { $resumeStep = 3; }
        elseif ($errors->has('email')) { $resumeStep = 4; }
        elseif ($errors->has('password') || $errors->has('password_confirmation')) { $resumeStep = 7; }
        else { $resumeStep = 8; }
    }
    $defaultInterests = old('onboarding_interests')
        ? array_values(array_filter(explode(',', (string) old('onboarding_interests'))))
        : ['math', 'science'];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ابدأ رحلتك — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.geometric-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('auth.partials.geometric-engine')
</head>
<body class="geo-page" x-data="onboardingWizard()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            <template x-if="step > 1 && step <= 8">
                <button type="button" class="geo-nav-btn" @click="prev()">→ السابق</button>
            </template>
            <template x-if="step === 1">
                <a href="{{ route('home') }}" class="geo-nav-btn">الرئيسية</a>
            </template>
            @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav', 'geoBrandShowName' => false])
            <a href="{{ route('login') }}" class="geo-nav-link" x-show="step === 1">دخول</a>
            <span x-show="step > 1 && step <= 8" style="width:4rem"></span>
        </nav>

        {{-- Living geometric progress --}}
        <div class="geo-lattice" x-show="step >= 2 && step <= 8" x-cloak aria-hidden="true">
            <template x-for="i in 7" :key="'lat-' + i">
                <div class="geo-lattice-item">
                    <div class="geo-lattice-node"
                         :class="{ 'is-lit': step > i + 1, 'is-current': step === i + 1 }"></div>
                    <div class="geo-lattice-bridge" x-show="i < 7"
                         :class="{ 'is-lit': step > i + 1 }"></div>
                </div>
            </template>
        </div>

        <main class="geo-stage">
            <div class="geo-panel" :key="step + '-' + subStep">

                @if($errors->any())
                <div class="geo-alert geo-alert--err" style="margin-bottom:1.25rem">
                    @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
                </div>
                @endif

                @if(!empty($pendingReferralCode))
                <p class="geo-hint geo-hint--ok" style="margin-bottom:1rem">
                    دعوة: <span dir="ltr">{{ $pendingReferralCode }}</span>
                </p>
                @endif

                <form action="{{ route('register') }}" method="POST" @submit="onSubmit" id="registerForm">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ old('referral_code', $pendingReferralCode ?? '') }}">
                    <input type="hidden" name="onboarding_goal" :value="goal">
                    <input type="hidden" name="onboarding_level" :value="level">
                    <input type="hidden" name="onboarding_interests" :value="interests.join(',')">
                    <input type="hidden" name="onboarding_style" :value="style">

                    {{-- Step 1: Welcome --}}
                    <div x-show="step === 1" x-cloak>
                        <span class="geo-step-tag">رحلة جديدة</span>
                        <h1 class="geo-headline">تعلّم<br><em>بطريقة مختلفة</em></h1>
                        <p class="geo-lead">كل إجابة تُشكّل العالم من حولك — خطوة بخطوة، بدون نماذج تقليدية.</p>
                        <button type="button" class="geo-cta magnetic" x-ref="startBtn" @click="next()">
                            <span>ابدأ</span><span>→</span>
                        </button>
                        <p style="margin-top:2rem;font-size:.85rem;color:var(--edu-muted)">
                            عندك حساب؟ <a href="{{ route('login') }}" class="geo-link">سجّل دخول</a>
                        </p>
                    </div>

                    {{-- Step 2: Name --}}
                    <div x-show="step === 2" x-cloak>
                        <span class="geo-step-tag">01 — الهوية</span>
                        <p class="geo-whisper" x-show="name.trim().length > 1" x-text="'مرحباً ' + firstName"></p>
                        <h2 class="geo-headline" style="font-size:clamp(1.6rem,4vw,2.1rem)">وش اسمك؟</h2>
                        <p class="geo-lead">كل حرف يربط الأشكال ببعضها</p>
                        <div class="geo-field-wrap">
                            <input type="text" name="name" x-model="name" class="geo-field"
                                   :class="nameValid ? 'is-valid' : (nameTouched && !nameValid ? 'is-error' : '')"
                                   placeholder="اسمك الكامل"
                                   @input="onNameInput()" @keydown.enter.prevent="nameValid && next()"
                                   autocomplete="name">
                            <span class="geo-field-line"></span>
                        </div>
                        <p class="geo-hint" :class="nameValid ? 'geo-hint--ok' : (nameTouched && !nameValid ? 'geo-hint--err' : '')"
                           x-text="nameValid ? 'متصل' : (nameTouched ? 'اسمك الكامل من فضلك' : ' ')"></p>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()" :disabled="!nameValid">التالي →</button>
                        </div>
                    </div>

                    {{-- Step 3: Phone --}}
                    <div x-show="step === 3" x-cloak>
                        <span class="geo-step-tag">02 — التواصل</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.6rem,4vw,2.1rem)">رقم جوّالك</h2>
                        <p class="geo-lead">موجات خفيفة تنتشر مع كل رقم</p>
                        <div class="geo-phone" :class="phoneFieldClass">
                            <select name="country_code" x-model="countryCode" dir="ltr" @change="onPhoneInput()">
                                @foreach($phoneCountries ?? [] as $c)
                                <option value="{{ $c['dial_code'] }}">{{ $c['dial_code'] }} {{ $c['name_ar'] }}</option>
                                @endforeach
                            </select>
                            <input type="tel" name="phone" x-model="phone" dir="ltr" inputmode="tel"
                                   :placeholder="currentCountry.placeholder || '5xxxxxxxx'"
                                   @input="onPhoneInput()"
                                   @keydown.enter.prevent="phoneReady && next()">
                        </div>
                        <p class="geo-hint" :class="phoneHintClass" x-text="phoneHint"></p>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()" :disabled="!phoneReady || phoneChecking">
                                <span x-text="phoneChecking ? 'جاري التحقق...' : 'التالي →'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Step 4: Email --}}
                    <div x-show="step === 4" x-cloak>
                        <span class="geo-step-tag">03 — البريد</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.6rem,4vw,2.1rem)">بريدك الإلكتروني</h2>
                        <p class="geo-lead">دوائر مت expanding — بياناتك آمنة ومشفّرة</p>
                        <div class="geo-field-wrap">
                            <input type="email" name="email" x-model="email" dir="ltr" class="geo-field"
                                   :class="emailFieldClass"
                                   placeholder="you@email.com"
                                   @input="onEmailInput()"
                                   @keydown.enter.prevent="emailReady && next()"
                                   autocomplete="email">
                            <span class="geo-field-line"></span>
                        </div>
                        <p class="geo-hint" :class="emailHintClass" x-text="emailHint"></p>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()" :disabled="!emailReady || emailChecking">
                                <span x-text="emailChecking ? 'جاري التحقق...' : 'التالي →'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Step 5: Onboarding --}}
                    <div x-show="step === 5" x-cloak>
                        <span class="geo-step-tag" x-text="'04 — ' + subStepLabel"></span>
                        <div x-show="subStep === 0">
                            <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">هدفك؟</h2>
                            <div class="geo-choices">
                                <template x-for="opt in goalOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': goal === opt.id }" @click="goal = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong><span x-text="opt.desc"></span></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="subStep === 1">
                            <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">مستواك؟</h2>
                            <div class="geo-choices">
                                <template x-for="opt in levelOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': level === opt.id }" @click="level = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong><span x-text="opt.desc"></span></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="subStep === 2">
                            <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">اهتماماتك؟</h2>
                            <div class="geo-choices geo-choices--grid">
                                <template x-for="opt in interestOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': interests.includes(opt.id) }"
                                            @click="toggleInterest(opt.id)">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="subStep === 3">
                            <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">أسلوبك؟</h2>
                            <div class="geo-choices geo-choices--grid">
                                <template x-for="opt in styleOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': style === opt.id }" @click="style = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong><span x-text="opt.desc"></span></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="nextSubOrStep()" :disabled="!subStepValid">
                                <span x-text="subStep < 3 ? 'التالي →' : 'الملخص →'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Step 6: Summary --}}
                    <div x-show="step === 6" x-cloak>
                        <span class="geo-step-tag">05 — التكوين</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">رحلتك</h2>
                        <p class="geo-lead" x-text="personalizeMessage"></p>
                        <div style="margin-bottom:1.5rem">
                            <div class="geo-summary-row"><small>الهدف</small><strong x-text="labelFor(goalOptions, goal)"></strong></div>
                            <div class="geo-summary-row"><small>المستوى</small><strong x-text="labelFor(levelOptions, level)"></strong></div>
                            <div class="geo-summary-row"><small>الاهتمامات</small><strong x-text="interestsLabels"></strong></div>
                            <div class="geo-summary-row"><small>الأسلوب</small><strong x-text="labelFor(styleOptions, style)"></strong></div>
                        </div>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()">كمّل →</button>
                            <button type="button" class="geo-cta geo-cta--ghost" @click="step = 5; subStep = 0">تعديل</button>
                        </div>
                    </div>

                    {{-- Step 7: Password --}}
                    <div x-show="step === 7" x-cloak>
                        <span class="geo-step-tag">06 — الأمان</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">كلمة المرور</h2>
                        <p class="geo-lead">8 أحرف على الأقل</p>
                        <div class="geo-field-wrap">
                            <input :type="showPw ? 'text' : 'password'" name="password" x-model="password"
                                   class="geo-field" placeholder="كلمة المرور" autocomplete="new-password"
                                   @input="onPasswordInput()">
                            <button type="button" class="geo-pw-toggle" @click="showPw = !showPw" tabindex="-1">
                                <span x-text="showPw ? 'إخفاء' : 'إظهار'" style="font-size:.7rem;font-weight:600"></span>
                            </button>
                            <span class="geo-field-line"></span>
                        </div>
                        <div class="geo-strength" x-show="password.length > 0">
                            <template x-for="i in 4" :key="i">
                                <div class="geo-strength-seg" :class="{ 'is-on': pwStrengthScore() >= i }"></div>
                            </template>
                        </div>
                        <div class="geo-field-wrap" style="margin-top:1.25rem">
                            <input :type="showPwConfirm ? 'text' : 'password'" name="password_confirmation" x-model="passwordConfirm"
                                   class="geo-field" :class="passwordsMatch ? 'is-valid' : (passwordConfirm.length > 0 ? 'is-error' : '')"
                                   placeholder="تأكيد كلمة المرور" autocomplete="new-password">
                            <span class="geo-field-line"></span>
                        </div>
                        <p class="geo-hint" :class="passwordsMatch ? 'geo-hint--ok' : 'geo-hint--err'"
                           x-show="passwordConfirm.length > 0"
                           x-text="passwordsMatch ? 'متطابقة' : 'غير متطابقة'"></p>
                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()" :disabled="!passwordValid">التالي →</button>
                        </div>
                    </div>

                    {{-- Step 8: Terms --}}
                    <div x-show="step === 8" x-cloak>
                        <span class="geo-step-tag">07 — الإتمام</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.5rem,4vw,2rem)">جاهز؟</h2>
                        <div class="geo-terms-scroll">
                            <p><strong>شروط الاستخدام:</strong> باستخدام {{ config('app.name') }} توافق على قواعد المنصة واستخدام المحتوى للأغراض التعليمية.</p>
                            <p style="margin-top:.75rem"><strong>الخصوصية:</strong> بياناتك محمية — لا نشاركها مع أطراف ثالثة.</p>
                        </div>
                        <label class="geo-terms-check">
                            <input type="checkbox" x-model="termsAccepted">
                            <span>أوافق على <a href="{{ route('public.terms') }}" class="geo-link" target="_blank" rel="noopener">الشروط</a> و<a href="{{ route('public.privacy') }}" class="geo-link" target="_blank" rel="noopener">الخصوصية</a></span>
                        </label>
                        <div class="geo-actions">
                            <button type="submit" class="geo-cta magnetic" x-ref="submitBtn" :disabled="!termsAccepted || submitting">
                                <span x-text="submitting ? 'جاري الإنشاء...' : 'إنشاء الحساب'"></span>
                                <span x-show="!submitting">→</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

<script>
function onboardingWizard() {
    return {
        step: {{ $resumeStep }},
        subStep: 0,
        submitting: false,
        geo: null,
        validateUrl: @json(route('register.validate-field')),
        _debounceTimers: {},
        _lastPhoneChecked: '',
        _lastEmailChecked: '',

        phoneChecking: false,
        phoneAvailable: null,
        phoneCheckMsg: '',

        emailChecking: false,
        emailAvailable: null,
        emailCheckMsg: '',

        name: @json(old('name', '')),
        countryCode: @json(old('country_code', $defaultDialCode)),
        phone: @json(old('phone', '')),
        email: @json(old('email', '')),
        password: '',
        passwordConfirm: '',
        showPw: false,
        showPwConfirm: false,

        nameTouched: {{ $errors->has('name') ? 'true' : 'false' }},
        phoneTouched: {{ ($errors->has('phone') || $errors->has('country_code')) ? 'true' : 'false' }},
        emailTouched: {{ $errors->has('email') ? 'true' : 'false' }},

        goal: @json(old('onboarding_goal', 'grades')),
        level: @json(old('onboarding_level', 'intermediate')),
        interests: @json($defaultInterests),
        style: @json(old('onboarding_style', 'mixed')),
        termsAccepted: false,

        phoneCountries: @json($phoneCountriesJson),

        goalOptions: [
            { id: 'grades', label: 'تحسين الدرجات', desc: 'رفع المستوى' },
            { id: 'exams', label: 'الاختبارات', desc: 'قدرات وتحصيلي' },
            { id: 'skills', label: 'مهارات جديدة', desc: 'تعلّم مفيد' },
            { id: 'curriculum', label: 'متابعة المنهج', desc: 'على المنهج' },
        ],
        levelOptions: [
            { id: 'beginner', label: 'مبتدئ', desc: 'بداية جديدة' },
            { id: 'intermediate', label: 'متوسط', desc: 'عندي أساس' },
            { id: 'advanced', label: 'متقدّم', desc: 'تحدي' },
        ],
        interestOptions: [
            { id: 'math', label: 'رياضيات' },
            { id: 'science', label: 'علوم' },
            { id: 'arabic', label: 'عربي' },
            { id: 'english', label: 'إنجليزي' },
            { id: 'life', label: 'مهارات حياتية' },
            { id: 'tech', label: 'تقنية' },
        ],
        styleOptions: [
            { id: 'video', label: 'فيديو', desc: 'مشاهدة' },
            { id: 'interactive', label: 'تفاعلي', desc: 'تمارين' },
            { id: 'reading', label: 'قراءة', desc: 'مراجع' },
            { id: 'mixed', label: 'مختلط', desc: 'الكل' },
        ],

        init() {
            this.geo = window.createGeoEngine(document.getElementById('geo-canvas'));
            this.validatePhone();
            if (this.step >= 5) this.subStep = 3;
            this.syncGeo();
            this.$watch('step', (v) => {
                this.syncGeo();
                if (v === 3 && this._phoneValid) this.schedulePhoneCheck();
                if (v === 4 && this.emailValid) this.scheduleEmailCheck();
            });
            this.$nextTick(() => {
                if (this.$refs.startBtn) window.initMagnetic(this.$refs.startBtn);
                if (this.$refs.submitBtn) window.initMagnetic(this.$refs.submitBtn);
            });
        },

        syncGeo() {
            if (!this.geo) return;
            this.geo.setStep(this.step);
            if (this.step === 2) this.geo.setConnectStrength(Math.min(1, this.name.length / 10));
            if (this.step === 3) this.geo.triggerWave();
            if (this.step === 4) this.geo.setEmailPulse(0.7);
        },

        onNameInput() {
            this.nameTouched = true;
            this.geo?.setConnectStrength(Math.min(1, this.name.trim().length / 10));
        },
        onPhoneInput() {
            this.phoneTouched = true;
            this.validatePhone();
            const sig = this.phoneSignature();
            if (sig !== this._lastPhoneChecked) {
                this.phoneAvailable = null;
                this.phoneCheckMsg = '';
            }
            this.geo?.triggerWave();
            if (this._phoneValid) {
                this.schedulePhoneCheck();
            }
        },
        onEmailInput() {
            this.emailTouched = true;
            const sig = this.email.trim().toLowerCase();
            if (sig !== this._lastEmailChecked) {
                this.emailAvailable = null;
                this.emailCheckMsg = '';
            }
            this.geo?.setEmailPulse(0.5);
            if (this.emailValid) {
                this.scheduleEmailCheck();
            }
        },
        onPasswordInput() {
            this.geo?.setConnectStrength(Math.min(1, this.password.length / 12));
        },

        get subStepLabel() {
            return ['الهدف', 'المستوى', 'الاهتمامات', 'الأسلوب'][this.subStep] || '';
        },
        get firstName() {
            const p = this.name.trim().split(' ');
            return p.length ? p[0] : '';
        },
        get nameValid() { return this.name.trim().length >= 2; },
        get emailValid() { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim()); },
        get phoneValid() { return this._phoneValid; },
        get phoneReady() {
            return this.phoneValid && this.phoneAvailable !== false && !this.phoneChecking;
        },
        get emailReady() {
            return this.emailValid && this.emailAvailable !== false && !this.emailChecking;
        },
        get passwordsMatch() { return this.password === this.passwordConfirm && this.password.length > 0; },
        get passwordValid() { return this.password.length >= 8 && this.passwordsMatch; },
        get currentCountry() {
            return this.phoneCountries.find(c => c.dial_code === this.countryCode) || {};
        },
        get phoneHint() {
            if (this.phoneChecking) return 'جاري التحقق من توفر الرقم...';
            if (this.phoneAvailable === false) return this.phoneCheckMsg || 'رقم الهاتف مسجل مسبقاً';
            if (this.phoneAvailable === true) return this.phoneCheckMsg || 'رقم الجوال متاح ✓';
            if (this.phoneValid) return ' ';
            if (!this.phoneTouched) return ' ';
            const ex = this.currentCountry.example || '';
            return ex ? 'مثال: ' + ex : 'رقم غير صحيح';
        },
        get phoneHintClass() {
            if (this.phoneChecking) return '';
            if (this.phoneAvailable === true) return 'geo-hint--ok';
            if (this.phoneAvailable === false || (this.phoneTouched && !this.phoneValid)) return 'geo-hint--err';
            return '';
        },
        get phoneFieldClass() {
            if (this.phoneChecking) return '';
            if (this.phoneAvailable === true) return 'is-valid';
            if (this.phoneAvailable === false || (this.phoneTouched && !this.phoneValid)) return 'is-error';
            return '';
        },
        get emailHint() {
            if (this.emailChecking) return 'جاري التحقق من توفر البريد...';
            if (this.emailAvailable === false) return this.emailCheckMsg || 'البريد الإلكتروني مسجل مسبقاً';
            if (this.emailAvailable === true) return this.emailCheckMsg || 'البريد متاح ✓';
            if (this.emailValid) return ' ';
            if (!this.emailTouched) return ' ';
            return 'بريد غير صالح';
        },
        get emailHintClass() {
            if (this.emailChecking) return '';
            if (this.emailAvailable === true) return 'geo-hint--ok';
            if (this.emailAvailable === false || (this.emailTouched && !this.emailValid)) return 'geo-hint--err';
            return '';
        },
        get emailFieldClass() {
            if (this.emailChecking) return '';
            if (this.emailAvailable === true) return 'is-valid';
            if (this.emailAvailable === false || (this.emailTouched && !this.emailValid)) return 'is-error';
            return '';
        },
        get subStepValid() {
            if (this.subStep === 0) return !!this.goal;
            if (this.subStep === 1) return !!this.level;
            if (this.subStep === 2) return this.interests.length > 0;
            if (this.subStep === 3) return !!this.style;
            return true;
        },
        get interestsLabels() {
            return this.interests.map(id => this.labelFor(this.interestOptions, id)).join('، ') || '—';
        },
        get personalizeMessage() {
            const g = this.labelFor(this.goalOptions, this.goal);
            const s = this.labelFor(this.styleOptions, this.style);
            const n = this.firstName || 'يا بطل';
            return n + ' — «' + g + '» بأسلوب «' + s + '».';
        },

        _phoneValid: false,

        validatePhone() {
            const c = this.currentCountry;
            if (!c.regex) { this._phoneValid = this.phone.replace(/\D/g, '').length >= 8; return; }
            const national = this.phone.replace(/\D/g, '').replace(/^0+/, '');
            try {
                const re = new RegExp(c.regex.replace(/^\//, '').replace(/\/$/, ''));
                this._phoneValid = re.test(national);
            } catch (e) {
                this._phoneValid = national.length >= 8;
            }
        },

        phoneSignature() {
            return this.countryCode + '|' + this.phone.replace(/\D/g, '').replace(/^0+/, '');
        },

        schedulePhoneCheck() {
            clearTimeout(this._debounceTimers.phone);
            this._debounceTimers.phone = setTimeout(() => this.checkPhoneAvailability(), 800);
        },
        scheduleEmailCheck() {
            clearTimeout(this._debounceTimers.email);
            this._debounceTimers.email = setTimeout(() => this.checkEmailAvailability(), 800);
        },

        async postFieldCheck(payload) {
            const res = await fetch(this.validateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(payload),
            });
            if (res.status === 429) {
                return { valid: true, available: null, throttled: true, message: 'تحقّق مؤقتاً — يمكنك المتابعة' };
            }
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                return { valid: false, available: false, message: err.message || 'تعذّر التحقق' };
            }
            return res.json();
        },

        async checkPhoneAvailability(force = false) {
            if (!this.phoneValid) {
                this.phoneAvailable = null;
                this.phoneCheckMsg = '';
                return false;
            }
            const sig = this.phoneSignature();
            if (!force && this.phoneAvailable === true && this._lastPhoneChecked === sig) {
                return true;
            }
            this.phoneChecking = true;
            try {
                const data = await this.postFieldCheck({
                    field: 'phone',
                    value: this.phone,
                    country_code: this.countryCode,
                });
                this._lastPhoneChecked = sig;
                if (data.throttled) {
                    this.phoneCheckMsg = data.message;
                    return this.phoneAvailable === true;
                }
                this.phoneAvailable = data.valid && data.available;
                this.phoneCheckMsg = data.message || '';
                return this.phoneAvailable === true;
            } catch (e) {
                this.phoneCheckMsg = 'تعذّر التحقق — يمكنك المتابعة';
                return this.phoneAvailable === true;
            } finally {
                this.phoneChecking = false;
            }
        },

        async checkEmailAvailability(force = false) {
            if (!this.emailValid) {
                this.emailAvailable = null;
                this.emailCheckMsg = '';
                return false;
            }
            const sig = this.email.trim().toLowerCase();
            if (!force && this.emailAvailable === true && this._lastEmailChecked === sig) {
                return true;
            }
            this.emailChecking = true;
            try {
                const data = await this.postFieldCheck({
                    field: 'email',
                    value: sig,
                });
                this._lastEmailChecked = sig;
                if (data.throttled) {
                    this.emailCheckMsg = data.message;
                    return this.emailAvailable === true;
                }
                this.emailAvailable = data.valid && data.available;
                this.emailCheckMsg = data.message || '';
                return this.emailAvailable === true;
            } catch (e) {
                this.emailCheckMsg = 'تعذّر التحقق — يمكنك المتابعة';
                return this.emailAvailable === true;
            } finally {
                this.emailChecking = false;
            }
        },

        labelFor(options, id) {
            const o = options.find(x => x.id === id);
            return o ? o.label : id;
        },

        toggleInterest(id) {
            const i = this.interests.indexOf(id);
            if (i >= 0) this.interests.splice(i, 1);
            else this.interests.push(id);
        },

        pwStrengthScore() {
            let s = 0;
            if (this.password.length >= 8) s++;
            if (this.password.length >= 12) s++;
            if (/[A-Z]/.test(this.password) || /[\u0600-\u06FF]/.test(this.password)) s++;
            if (/\d/.test(this.password)) s++;
            return Math.min(4, s);
        },

        nextSubOrStep() {
            if (this.subStep < 3) { this.subStep++; return; }
            this.subStep = 0;
            this.step = 6;
            this.syncGeo();
        },

        async next() {
            if (this.step === 2 && !this.nameValid) return;
            if (this.step === 3) {
                if (!this.phoneValid) return;
                if (this.phoneAvailable === false) return;
                if (this.phoneAvailable !== true) {
                    await this.checkPhoneAvailability(true);
                    if (this.phoneAvailable === false) return;
                }
            }
            if (this.step === 4) {
                if (!this.emailValid) return;
                if (this.emailAvailable === false) return;
                if (this.emailAvailable !== true) {
                    await this.checkEmailAvailability(true);
                    if (this.emailAvailable === false) return;
                }
            }
            if (this.step === 5) { this.nextSubOrStep(); return; }
            if (this.step === 7 && !this.passwordValid) return;
            if (this.step < 8) this.step++;
            this.syncGeo();
        },

        prev() {
            if (this.step === 5 && this.subStep > 0) { this.subStep--; return; }
            if (this.step === 6) { this.step = 5; this.subStep = 3; this.syncGeo(); return; }
            if (this.step > 1) { this.step--; this.syncGeo(); }
        },

        async onSubmit(e) {
            e.preventDefault();
            if (!this.termsAccepted || this.submitting) return;

            if (this.phoneAvailable === false || this.emailAvailable === false) {
                if (this.phoneAvailable === false) { this.step = 3; this.syncGeo(); return; }
                if (this.emailAvailable === false) { this.step = 4; this.syncGeo(); return; }
            }

            this.submitting = true;
            if (this.geo) this.geo.startConverge();
            e.target.submit();
        },
    };
}
</script>
</body>
</html>

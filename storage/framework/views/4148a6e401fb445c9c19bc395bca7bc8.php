<?php
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
        if ($errors->has('name') || $errors->has('phone') || $errors->has('country_code') || $errors->has('email')) {
            $resumeStep = 1;
        } else {
            $resumeStep = 3;
        }
    }
    $defaultInterests = old('onboarding_interests')
        ? array_values(array_filter(explode(',', (string) old('onboarding_interests'))))
        : ['math', 'science'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>ابدأ رحلتك — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="<?php echo e(config('brand.colors.blue')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('auth.partials.geometric-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php echo $__env->make('auth.partials.geometric-engine', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="geo-page" x-data="onboardingWizard()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            <template x-if="step > 1">
                <button type="button" class="geo-nav-btn" @click="prev()">→ السابق</button>
            </template>
            <template x-if="step === 1">
                <a href="<?php echo e(route('home')); ?>" class="geo-nav-btn">الرئيسية</a>
            </template>
            <?php echo $__env->make('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav', 'geoBrandShowName' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <a href="<?php echo e(route('login')); ?>" class="geo-nav-link" x-show="step === 1">دخول</a>
            <span x-show="step > 1" style="width:4rem"></span>
        </nav>

        <div class="geo-lattice" aria-hidden="true">
            <template x-for="i in 3" :key="'lat-' + i">
                <div class="geo-lattice-item">
                    <div class="geo-lattice-node"
                         :class="{ 'is-lit': step > i, 'is-current': step === i }"></div>
                    <div class="geo-lattice-bridge" x-show="i < 3"
                         :class="{ 'is-lit': step > i }"></div>
                </div>
            </template>
        </div>

        <main class="geo-stage" :class="{ 'geo-stage--scroll': step === 2 }">
            <div class="geo-panel" :class="{ 'geo-panel--wide': step === 2 }" :key="step">

                <?php if($errors->any()): ?>
                <div class="geo-alert geo-alert--err" style="margin-bottom:1.25rem">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e($err); ?><?php if(!$loop->last): ?><br><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <?php if(!empty($pendingReferralCode)): ?>
                <p class="geo-hint geo-hint--ok" style="margin-bottom:1rem">
                    دعوة: <span dir="ltr"><?php echo e($pendingReferralCode); ?></span>
                </p>
                <?php endif; ?>

                <form action="<?php echo e(route('register')); ?>" method="POST" @submit="onSubmit" id="registerForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="referral_code" value="<?php echo e(old('referral_code', $pendingReferralCode ?? '')); ?>">
                    <input type="hidden" name="onboarding_goal" :value="goal">
                    <input type="hidden" name="onboarding_level" :value="level">
                    <input type="hidden" name="onboarding_interests" :value="interests.join(',')">
                    <input type="hidden" name="onboarding_style" :value="style">

                    
                    <div x-show="step === 1" x-cloak>
                        <span class="geo-step-tag">الخطوة ١ من ٣ — بياناتك</span>
                        <h1 class="geo-headline" style="font-size:clamp(1.55rem,4vw,2rem)">
                            <span x-show="name.trim().length < 2">ابدأ رحلتك<br><em>مع سنا</em></span>
                            <span x-show="name.trim().length >= 2" x-cloak>أهلاً <em x-text="firstName"></em></span>
                        </h1>
                        <p class="geo-lead">اسمك، جوّالك، وبريدك — ثلاثة حقول فقط للبدء.</p>

                        <div class="geo-form-stack">
                            <div>
                                <label class="geo-inline-label">الاسم الكامل</label>
                                <div class="geo-field-wrap">
                                    <input type="text" name="name" x-model="name" class="geo-field"
                                           :class="nameValid ? 'is-valid' : (nameTouched && !nameValid ? 'is-error' : '')"
                                           placeholder="مثال: أحمد محمد"
                                           @input="onNameInput()" autocomplete="name">
                                    <span class="geo-field-line"></span>
                                </div>
                            </div>
                            <div>
                                <label class="geo-inline-label">رقم الجوال</label>
                                <div class="geo-phone" :class="phoneFieldClass">
                                    <select name="country_code" x-model="countryCode" dir="ltr" @change="onPhoneInput()">
                                        <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c['dial_code']); ?>"><?php echo e($c['dial_code']); ?> <?php echo e($c['name_ar']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="tel" name="phone" x-model="phone" dir="ltr" inputmode="tel"
                                           :placeholder="currentCountry.placeholder || '5xxxxxxxx'"
                                           @input="onPhoneInput()" autocomplete="tel">
                                </div>
                                <p class="geo-hint" :class="phoneHintClass" x-text="phoneHint"></p>
                            </div>
                            <div>
                                <label class="geo-inline-label">البريد الإلكتروني</label>
                                <div class="geo-field-wrap">
                                    <input type="email" name="email" x-model="email" dir="ltr" class="geo-field"
                                           :class="emailFieldClass"
                                           placeholder="you@email.com"
                                           @input="onEmailInput()" autocomplete="email">
                                    <span class="geo-field-line"></span>
                                </div>
                                <p class="geo-hint" :class="emailHintClass" x-text="emailHint"></p>
                            </div>
                        </div>

                        <div class="geo-actions">
                            <button type="button" class="geo-cta magnetic" x-ref="nextBtn" @click="next()"
                                    :disabled="step1Checking">
                                <span x-text="step1Checking ? 'جاري التحقق...' : 'التالي — رحلتك التعليمية'"></span>
                                <span x-show="!step1Checking">→</span>
                            </button>
                        </div>
                        <p style="margin-top:1.5rem;font-size:.85rem;color:var(--edu-muted)">
                            عندك حساب؟ <a href="<?php echo e(route('login')); ?>" class="geo-link">سجّل دخول</a>
                        </p>
                    </div>

                    
                    <div x-show="step === 2" x-cloak>
                        <span class="geo-step-tag">الخطوة ٢ من ٣ — رحلتك</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.45rem,4vw,1.85rem)">خصّص تجربتك</h2>
                        <p class="geo-lead" x-text="personalizeMessage"></p>

                        <div class="geo-onboard-block">
                            <span class="geo-onboard-block__label">هدفك</span>
                            <div class="geo-choices geo-choices--compact geo-choices--grid">
                                <template x-for="opt in goalOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': goal === opt.id }" @click="goal = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="geo-onboard-block">
                            <span class="geo-onboard-block__label">مستواك</span>
                            <div class="geo-choices geo-choices--compact">
                                <template x-for="opt in levelOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': level === opt.id }" @click="level = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong><span x-text="opt.desc"></span></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="geo-onboard-block">
                            <span class="geo-onboard-block__label">اهتماماتك</span>
                            <div class="geo-choices geo-choices--compact geo-choices--grid">
                                <template x-for="opt in interestOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': interests.includes(opt.id) }"
                                            @click="toggleInterest(opt.id)">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="geo-onboard-block">
                            <span class="geo-onboard-block__label">أسلوب التعلّم</span>
                            <div class="geo-choices geo-choices--compact geo-choices--grid">
                                <template x-for="opt in styleOptions" :key="opt.id">
                                    <button type="button" class="geo-choice" :class="{ 'is-selected': style === opt.id }" @click="style = opt.id">
                                        <span class="geo-choice-dot"></span>
                                        <span class="geo-choice-text"><strong x-text="opt.label"></strong></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="geo-actions">
                            <button type="button" class="geo-cta" @click="next()" :disabled="!onboardingValid">التالي — كلمة المرور →</button>
                        </div>
                    </div>

                    
                    <div x-show="step === 3" x-cloak>
                        <span class="geo-step-tag">الخطوة ٣ من ٣ — الأمان</span>
                        <h2 class="geo-headline" style="font-size:clamp(1.45rem,4vw,1.85rem)">أنشئ كلمة المرور</h2>
                        <p class="geo-lead">8 أحرف على الأقل — ثم أكّد ووافق على الشروط.</p>

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
                        <div class="geo-field-wrap" style="margin-top:1.15rem">
                            <input :type="showPwConfirm ? 'text' : 'password'" name="password_confirmation" x-model="passwordConfirm"
                                   class="geo-field" :class="passwordsMatch ? 'is-valid' : (passwordConfirm.length > 0 ? 'is-error' : '')"
                                   placeholder="تأكيد كلمة المرور" autocomplete="new-password">
                            <span class="geo-field-line"></span>
                        </div>
                        <p class="geo-hint" :class="passwordsMatch ? 'geo-hint--ok' : 'geo-hint--err'"
                           x-show="passwordConfirm.length > 0"
                           x-text="passwordsMatch ? 'متطابقة ✓' : 'غير متطابقة'"></p>

                        <div class="geo-terms-scroll" style="margin-top:1.25rem">
                            <p><strong>شروط الاستخدام:</strong> باستخدام <?php echo e(config('app.name')); ?> توافق على قواعد المنصة واستخدام المحتوى للأغراض التعليمية.</p>
                            <p style="margin-top:.65rem"><strong>الخصوصية:</strong> بياناتك محمية — لا نشاركها مع أطراف ثالثة.</p>
                        </div>
                        <label class="geo-terms-check">
                            <input type="checkbox" x-model="termsAccepted">
                            <span>أوافق على <a href="<?php echo e(route('public.terms')); ?>" class="geo-link" target="_blank" rel="noopener">الشروط</a> و<a href="<?php echo e(route('public.privacy')); ?>" class="geo-link" target="_blank" rel="noopener">الخصوصية</a></span>
                        </label>
                        <div class="geo-actions">
                            <button type="submit" class="geo-cta magnetic" x-ref="submitBtn"
                                    :disabled="!passwordValid || !termsAccepted || submitting">
                                <span x-text="submitting ? 'جاري إنشاء الحساب...' : 'إنشاء الحساب'"></span>
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
        step: <?php echo e($resumeStep); ?>,
        submitting: false,
        step1Checking: false,
        geo: null,
        validateUrl: <?php echo json_encode(route('register.validate-field'), 15, 512) ?>,
        _debounceTimers: {},
        _lastPhoneChecked: '',
        _lastEmailChecked: '',

        phoneChecking: false,
        phoneAvailable: null,
        phoneCheckMsg: '',

        emailChecking: false,
        emailAvailable: null,
        emailCheckMsg: '',

        name: <?php echo json_encode(old('name', ''), 512) ?>,
        countryCode: <?php echo json_encode(old('country_code', $defaultDialCode), 512) ?>,
        phone: <?php echo json_encode(old('phone', ''), 512) ?>,
        email: <?php echo json_encode(old('email', ''), 512) ?>,
        password: '',
        passwordConfirm: '',
        showPw: false,
        showPwConfirm: false,

        nameTouched: <?php echo e($errors->has('name') ? 'true' : 'false'); ?>,
        phoneTouched: <?php echo e(($errors->has('phone') || $errors->has('country_code')) ? 'true' : 'false'); ?>,
        emailTouched: <?php echo e($errors->has('email') ? 'true' : 'false'); ?>,

        goal: <?php echo json_encode(old('onboarding_goal', 'grades'), 512) ?>,
        level: <?php echo json_encode(old('onboarding_level', 'intermediate'), 512) ?>,
        interests: <?php echo json_encode($defaultInterests, 15, 512) ?>,
        style: <?php echo json_encode(old('onboarding_style', 'mixed'), 512) ?>,
        termsAccepted: false,

        phoneCountries: <?php echo json_encode($phoneCountriesJson, 15, 512) ?>,

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
            this.syncGeo();
            this.$watch('step', () => {
                this.syncGeo();
                if (this.step === 1) {
                    if (this._phoneValid) this.schedulePhoneCheck();
                    if (this.emailValid) this.scheduleEmailCheck();
                }
            });
            this.$nextTick(() => {
                if (this.$refs.nextBtn) window.initMagnetic(this.$refs.nextBtn);
                if (this.$refs.submitBtn) window.initMagnetic(this.$refs.submitBtn);
            });
        },

        syncGeo() {
            if (!this.geo) return;
            this.geo.setStep(this.step);
            if (this.step === 1) {
                this.geo.setConnectStrength(Math.min(1, this.name.trim().length / 10));
                if (this.phoneTouched) this.geo.triggerWave();
                if (this.emailTouched) this.geo.setEmailPulse(0.5);
            }
            if (this.step === 2) this.geo.setConnectStrength(0.85);
            if (this.step === 3) this.geo.setConnectStrength(Math.min(1, this.password.length / 12));
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

        get onboardingValid() {
            return !!this.goal && !!this.level && this.interests.length > 0 && !!this.style;
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
        get personalizeMessage() {
            const n = this.firstName || 'يا بطل';
            return n + ' — اختر ما يناسبك، ويمكنك تعديله لاحقاً من حسابك.';
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

        async next() {
            if (this.step === 1) {
                this.nameTouched = true;
                this.phoneTouched = true;
                this.emailTouched = true;
                if (!this.nameValid || !this.phoneValid || !this.emailValid) return;
                if (this.phoneAvailable === false || this.emailAvailable === false) return;

                this.step1Checking = true;
                try {
                    if (this.phoneAvailable !== true) {
                        await this.checkPhoneAvailability(true);
                        if (this.phoneAvailable === false) return;
                    }
                    if (this.emailAvailable !== true) {
                        await this.checkEmailAvailability(true);
                        if (this.emailAvailable === false) return;
                    }
                } finally {
                    this.step1Checking = false;
                }
            }
            if (this.step === 2 && !this.onboardingValid) return;
            if (this.step < 3) {
                this.step++;
                this.syncGeo();
            }
        },

        prev() {
            if (this.step > 1) {
                this.step--;
                this.syncGeo();
            }
        },

        async onSubmit(e) {
            e.preventDefault();
            if (!this.passwordValid || !this.termsAccepted || this.submitting) return;

            if (this.phoneAvailable === false || this.emailAvailable === false) {
                this.step = 1;
                this.syncGeo();
                return;
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
<?php /**PATH C:\xampp\htdocs\sana\resources\views\auth\register.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.geometric-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('auth.partials.geometric-engine')
</head>
@php
    $loginAsDefault = in_array(old('login_as'), ['student', 'parent'], true) ? old('login_as') : 'student';
@endphp
<body class="geo-page" x-data="loginExperience()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav'])
            <a href="{{ route('register') }}" class="geo-nav-link">إنشاء حساب</a>
        </nav>

        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'])

                <h1 class="geo-headline">مرحباً<br><em>بعودتك</em></h1>
                <p class="geo-lead">للطلاب وأولياء الأمور</p>

                @if (session('status'))
                <div class="geo-alert geo-alert--ok">{{ session('status') }}</div>
                @endif
                @if (session('warning'))
                <div class="geo-alert geo-alert--warn">{{ session('warning') }}</div>
                @endif
                @if ($errors->any())
                <div class="geo-alert geo-alert--err">
                    @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST" @submit="onSubmit" x-ref="loginForm">
                    @csrf
                    <div style="display:none" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <input type="hidden" name="login_as" :value="loginAs">

                    <div class="geo-role-switch">
                        <button type="button" class="geo-role-btn" :class="{ 'is-active': loginAs === 'student' }"
                                @click="loginAs = 'student'">طالب</button>
                        <button type="button" class="geo-role-btn" :class="{ 'is-active': loginAs === 'parent' }"
                                @click="loginAs = 'parent'">ولي أمر</button>
                    </div>
                    <p class="geo-hint" style="margin-top:-1rem;margin-bottom:1.25rem" x-show="loginAs === 'parent'" x-cloak>
                        نفس بريد الطالب — كلمة مرور ولي الأمر
                    </p>

                    <div class="geo-field-wrap">
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               required autocomplete="email" autofocus
                               class="geo-field @error('email') is-error @enderror"
                               placeholder="البريد الإلكتروني" dir="ltr"
                               @focus="onEmailFocus()" @input="onEmailInput()">
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-field-wrap">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                               class="geo-field @error('password') is-error @enderror"
                               placeholder="كلمة المرور"
                               @focus="onPasswordFocus()" @input="onPasswordInput()">
                        <button type="button" class="geo-pw-toggle" @click="showPassword = !showPassword" tabindex="-1">
                            <span x-text="showPassword ? 'إخفاء' : 'إظهار'" style="font-size:.7rem;font-weight:600"></span>
                        </button>
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-row">
                        <label class="geo-check">
                            <input type="checkbox" name="remember">
                            <span>تذكّرني</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="geo-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="geo-cta magnetic" x-ref="submitBtn" :disabled="submitting">
                        <span x-text="submitting ? 'جاري الدخول...' : 'دخول'"></span>
                        <span x-show="!submitting">→</span>
                    </button>
                </form>

                <p style="margin-top:2rem;font-size:.85rem;color:var(--edu-muted)">
                    جديد؟ <a href="{{ route('register') }}" class="geo-link">ابدأ رحلتك</a>
                </p>
                <p style="margin-top:.75rem;font-size:.8rem;color:var(--edu-muted)">
                    فريق العمل؟ <a href="{{ route('staff.login') }}" class="geo-link">دخول الإدارة والمدربين</a>
                </p>
            </div>
        </main>
    </div>

<script>
function loginExperience() {
    return {
        loginAs: @json($loginAsDefault),
        showPassword: false,
        submitting: false,
        geo: null,

        init() {
            this.geo = window.createGeoEngine(document.getElementById('geo-canvas'));
            this.geo.setPhase('idle');
            this.$nextTick(() => {
                window.initMagnetic(this.$refs.submitBtn);
            });
        },

        onEmailFocus() { this.geo?.setPhase('email'); this.geo?.setEmailPulse(0.3); },
        onEmailInput() { this.geo?.setEmailPulse(0.6); },
        onPasswordFocus() { this.geo?.setPhase('password'); this.geo?.triggerWave(); },
        onPasswordInput() { this.geo?.setConnectStrength(Math.min(1, (document.getElementById('password')?.value.length || 0) / 12)); },

        async onSubmit(e) {
            if (this.submitting) { e.preventDefault(); return; }
            const form = this.$refs.loginForm;
            if (!form.checkValidity()) return;
            e.preventDefault();
            this.submitting = true;
            if (this.geo) await this.geo.waitConverge(850);
            form.submit();
        },
    };
}
</script>
</body>
</html>

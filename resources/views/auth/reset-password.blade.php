<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.reset_password') }} — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.geometric-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('auth.partials.geometric-engine')
</head>
<body class="geo-page" x-data="resetPasswordExperience()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav'])
            <a href="{{ route('login') }}" class="geo-nav-link">{{ __('auth.login') }}</a>
        </nav>

        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'])

                <h1 class="geo-headline">كلمة مرور<br><em>جديدة</em></h1>
                <p class="geo-lead">{{ __('auth.reset_password_help') }}</p>

                @if ($errors->any())
                <div class="geo-alert geo-alert--err">
                    @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" @submit="onSubmit" x-ref="resetForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="geo-field-wrap">
                        <input type="email" name="email" id="email" value="{{ old('email', $email) }}"
                               required autocomplete="email"
                               class="geo-field @error('email') is-error @enderror"
                               placeholder="{{ __('auth.email') }}" dir="ltr"
                               @focus="onEmailFocus()" @input="onEmailInput()">
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-field-wrap">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                               required autocomplete="new-password"
                               class="geo-field @error('password') is-error @enderror"
                               placeholder="{{ __('auth.new_password') }}"
                               @focus="onPasswordFocus()" @input="onPasswordInput()">
                        <button type="button" class="geo-pw-toggle" @click="showPassword = !showPassword" tabindex="-1">
                            <span x-text="showPassword ? 'إخفاء' : 'إظهار'" style="font-size:.7rem;font-weight:600"></span>
                        </button>
                        <span class="geo-field-line"></span>
                    </div>

                    <div class="geo-field-wrap">
                        <input :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation"
                               id="password_confirmation" required autocomplete="new-password"
                               class="geo-field @error('password_confirmation') is-error @enderror"
                               placeholder="{{ __('auth.password_confirmation') }}"
                               @focus="onPasswordFocus()">
                        <button type="button" class="geo-pw-toggle" @click="showPasswordConfirmation = !showPasswordConfirmation" tabindex="-1">
                            <span x-text="showPasswordConfirmation ? 'إخفاء' : 'إظهار'" style="font-size:.7rem;font-weight:600"></span>
                        </button>
                        <span class="geo-field-line"></span>
                    </div>

                    <button type="submit" class="geo-cta magnetic" x-ref="submitBtn" :disabled="submitting">
                        <span x-text="submitting ? 'جاري الحفظ...' : @json(__('auth.reset_password'))"></span>
                        <span x-show="!submitting">→</span>
                    </button>
                </form>

                <p style="margin-top:2rem;font-size:.85rem;color:var(--edu-muted)">
                    <a href="{{ route('login') }}" class="geo-link">{{ __('auth.go_to_login') }}</a>
                </p>
            </div>
        </main>
    </div>

<script>
function resetPasswordExperience() {
    return {
        showPassword: false,
        showPasswordConfirmation: false,
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
        onPasswordInput() {
            this.geo?.setConnectStrength(Math.min(1, (document.getElementById('password')?.value.length || 0) / 12));
        },

        async onSubmit(e) {
            if (this.submitting) { e.preventDefault(); return; }
            const form = this.$refs.resetForm;
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

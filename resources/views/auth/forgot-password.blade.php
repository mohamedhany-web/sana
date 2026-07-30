<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.forgot_password') }} — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.geometric-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @include('auth.partials.geometric-engine')
</head>
<body class="geo-page" x-data="forgotPasswordExperience()" x-init="init()">
    <canvas id="geo-canvas" aria-hidden="true"></canvas>

    <div class="geo-layer">
        <nav class="geo-nav">
            @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'nav'])
            <a href="{{ route('login') }}" class="geo-nav-link">{{ __('auth.login') }}</a>
        </nav>

        <main class="geo-stage">
            <div class="geo-panel geo-login-panel">
                @include('auth.partials.geo-brand-logo', ['geoBrandSize' => 'mark'])

                <h1 class="geo-headline">استعادة<br><em>الوصول</em></h1>
                <p class="geo-lead">{{ __('auth.forgot_password_help') }}</p>

                @if (session('status'))
                <div class="geo-alert geo-alert--ok">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                <div class="geo-alert geo-alert--err">
                    @foreach($errors->all() as $err){{ $err }}@if(!$loop->last)<br>@endif @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" @submit="onSubmit" x-ref="forgotForm">
                    @csrf

                    <div class="geo-field-wrap">
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               required autocomplete="email" autofocus
                               class="geo-field @error('email') is-error @enderror"
                               placeholder="{{ __('auth.email') }}" dir="ltr"
                               @focus="onEmailFocus()" @input="onEmailInput()">
                        <span class="geo-field-line"></span>
                    </div>

                    <button type="submit" class="geo-cta magnetic" x-ref="submitBtn" :disabled="submitting">
                        <span x-text="submitting ? 'جاري الإرسال...' : @json(__('auth.send_reset_link'))"></span>
                        <span x-show="!submitting">→</span>
                    </button>
                </form>

                <p style="margin-top:2rem;font-size:.85rem;color:var(--edu-muted)">
                    تذكرت كلمة المرور؟ <a href="{{ route('login') }}" class="geo-link">{{ __('auth.go_to_login') }}</a>
                </p>
                <p style="margin-top:.75rem;font-size:.8rem;color:var(--edu-muted)">
                    <a href="{{ route('home') }}" class="geo-link">العودة للرئيسية</a>
                </p>
            </div>
        </main>
    </div>

<script>
function forgotPasswordExperience() {
    return {
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

        async onSubmit(e) {
            if (this.submitting) { e.preventDefault(); return; }
            const form = this.$refs.forgotForm;
            if (!form.checkValidity()) return;
            e.preventDefault();
            this.submitting = true;
            if (this.geo) await this.geo.waitConverge(650);
            form.submit();
        },
    };
}
</script>
</body>
</html>

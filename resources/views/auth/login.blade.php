<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.eduvalt-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ showPassword: false }">
    <div class="auth-shell">

        @include('auth.partials.eduvalt-visual')

        <main class="auth-form-panel">
            <div class="auth-form-inner">
                <div class="auth-mobile-brand">
                    @include('auth.partials.eduvalt-brand')
                </div>

                <div class="auth-form-head">
                    <h2 class="auth-form-title">تسجيل الدخول</h2>
                    <p class="auth-form-subtitle">أدخل بياناتك للوصول إلى حسابك</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="auth-form">
                    @csrf

                    @if (session('status'))
                    <div class="auth-alert-ok">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                    @endif

                    @if (session('warning'))
                    <div class="auth-alert-warn">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    @endif

                    <div style="display:none" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div>
                        <label for="email" class="auth-label">البريد الإلكتروني</label>
                        <div class="auth-field">
                            <span class="auth-field-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   required autocomplete="email" autofocus
                                   class="auth-input auth-input--icon @error('email') is-error @enderror"
                                   placeholder="example@email.com" dir="ltr">
                        </div>
                        @error('email')<p class="auth-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="auth-label">كلمة المرور</label>
                        <div class="auth-field">
                            <span class="auth-field-icon"><i class="fas fa-lock"></i></span>
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                   class="auth-input auth-input--icon auth-input--toggle @error('password') is-error @enderror"
                                   placeholder="••••••••">
                            <button type="button" class="auth-toggle" @click="showPassword = !showPassword"
                                    aria-label="إظهار كلمة المرور">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password')<p class="auth-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="auth-row">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember">
                            <span>تذكّرني</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="auth-link">نسيت كلمة المرور؟</a>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        <span>تسجيل الدخول</span>
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    ليس لديك حساب؟
                    <a href="{{ route('register') }}" class="auth-link">أنشئ حسابك مجاناً</a>
                </div>

                <div class="auth-back-wrap">
                    <a href="{{ route('home') }}" class="auth-back">
                        <i class="fas fa-arrow-right"></i>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

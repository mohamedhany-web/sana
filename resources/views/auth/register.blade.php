@php
    $defaultDialCode = is_array($defaultCountry ?? null) ? ($defaultCountry['dial_code'] ?? '+966') : '+966';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء حساب — {{ config('app.name') }}</title>
    <meta name="theme-color" content="{{ config('brand.colors.blue') }}">
    @include('partials.favicon-links')
    @include('auth.partials.eduvalt-styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ showPassword: false, showPasswordConfirm: false }">
    <div class="auth-shell auth-shell--register">

        @include('auth.partials.eduvalt-visual', [
            'compact' => true,
            'eyebrow' => 'لمن صُمّمت المنصة',
            'titleMain' => 'بيئة واحدة',
            'titleEm' => 'للطلاب وأولياء الأمور والمعلمين',
            'captionText' => 'حصص مباشرة، كورسات، وتقارير متابعة',
        ])

        <main class="auth-form-panel">
            <div class="auth-form-inner auth-form-inner--wide">
                <div class="auth-mobile-brand">
                    @include('auth.partials.eduvalt-brand')
                </div>

                <div class="auth-form-head">
                    <h2 class="auth-form-title">إنشاء حساب جديد</h2>
                    <p class="auth-form-subtitle">انضم إلى {{ config('app.name') }} كولي أمر، طالب، أو معلم</p>
                </div>

                <div class="auth-notices">
                    <div class="auth-notice auth-notice--info">
                        <i class="fas fa-info-circle"></i>
                        <span>هذا النموذج لتسجيل أولياء الأمور والطلاب والمعلمين. للتسجيل كمدرب مؤسسي تواصل مع فريق الدعم.</span>
                    </div>

                    @if(!empty($pendingReferralCode))
                    <div class="auth-notice auth-notice--referral">
                        <i class="fas fa-gift"></i>
                        <div>
                            <strong>أنت تسجّل عبر رابط دعوة</strong><br>
                            سيتم ربط حسابك بكود الإحالة
                            <span dir="ltr" style="font-family: ui-monospace, monospace;">{{ $pendingReferralCode }}</span>
                            بعد إتمام التسجيل (إن كان البرنامج مفعّلاً).
                        </div>
                    </div>
                    @endif
                </div>

                <form action="{{ route('register') }}" method="POST" class="auth-form">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ old('referral_code', $pendingReferralCode ?? '') }}">

                    <div class="auth-form-grid">
                        <div>
                            <label for="name" class="auth-label">الاسم الكامل</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-user"></i></span>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                       class="auth-input auth-input--icon @error('name') is-error @enderror"
                                       placeholder="أدخل اسمك الكامل">
                            </div>
                            @error('name')<p class="auth-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="auth-field-full">
                            <label class="auth-label">رقم الهاتف</label>
                            <div class="auth-phone-row @error('phone') is-error @enderror">
                                <select name="country_code" required dir="ltr" aria-label="رمز الدولة">
                                    @foreach($phoneCountries ?? [] as $c)
                                    <option value="{{ $c['dial_code'] }}" {{ old('country_code', $defaultDialCode) === $c['dial_code'] ? 'selected' : '' }}>
                                        {{ $c['dial_code'] }} {{ $c['name_ar'] }}
                                    </option>
                                    @endforeach
                                </select>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="5xxxxxxxx" dir="ltr" inputmode="tel">
                            </div>
                            @error('phone')<p class="auth-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="email" class="auth-label">البريد الإلكتروني</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
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
                                       placeholder="كلمة مرور قوية">
                                <button type="button" class="auth-toggle" @click="showPassword = !showPassword"
                                        aria-label="إظهار كلمة المرور">
                                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="auth-span-2">
                            <label for="password_confirmation" class="auth-label">تأكيد كلمة المرور</label>
                            <div class="auth-field">
                                <span class="auth-field-icon"><i class="fas fa-lock"></i></span>
                                <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                       class="auth-input auth-input--icon auth-input--toggle"
                                       placeholder="أعد كتابة كلمة المرور">
                                <button type="button" class="auth-toggle" @click="showPasswordConfirm = !showPasswordConfirm"
                                        aria-label="إظهار تأكيد كلمة المرور">
                                    <i class="fas" :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="auth-terms">
                        <input type="checkbox" id="terms" required>
                        <span>
                            أوافق على
                            <a href="{{ route('public.terms') }}" class="auth-link" target="_blank" rel="noopener">شروط الاستخدام</a>
                            و
                            <a href="{{ route('public.privacy') }}" class="auth-link" target="_blank" rel="noopener">سياسة الخصوصية</a>
                        </span>
                    </label>

                    <button type="submit" class="auth-btn-primary">
                        <span>إنشاء الحساب</span>
                        <i class="fas fa-user-plus"></i>
                    </button>
                </form>

                <div class="auth-divider">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="auth-link">سجّل دخولك</a>
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

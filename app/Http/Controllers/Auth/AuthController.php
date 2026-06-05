<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Support\RbacAdminRouteAccess;
use App\Models\InstructorProfile;
use App\Models\TwoFactorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showStaffLogin()
    {
        return view('auth.staff-login');
    }

    public function login(Request $request)
    {
        return $this->authenticatePortalLogin($request, 'public');
    }

    public function staffLogin(Request $request)
    {
        return $this->authenticatePortalLogin($request, 'staff');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    private function authenticatePortalLogin(Request $request, string $portal)
    {
        $isPublic = $portal === 'public';
        $failureMessage = 'بيانات الدخول غير صحيحة.';

        if ($request->filled('website')) {
            \Log::warning('Bot detected - Honeypot field filled', [
                'ip' => $request->ip(),
                'portal' => $portal,
            ]);

            return back()->withErrors(['email' => $failureMessage])->withInput(
                $request->except('password', 'password_confirmation')
            );
        }

        $email = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');
        $loginAs = $isPublic ? (string) $request->input('login_as', 'student') : null;

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/[<>"\';()&|`$]/', (string) $value)) {
                        $fail('البريد الإلكتروني يحتوي على أحرف غير مسموحة.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];

        if ($isPublic) {
            $rules['login_as'] = ['required', 'in:student,parent'];
        }

        $validator = Validator::make(
            array_filter(['email' => $email, 'password' => $password, 'login_as' => $loginAs]),
            $rules,
            [
                'email.required' => 'البريد الإلكتروني مطلوب',
                'email.email' => 'البريد الإلكتروني غير صحيح',
                'login_as.in' => 'نوع الحساب غير صالح',
                'password.required' => 'كلمة المرور مطلوبة',
                'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(
                $request->except('password', 'password_confirmation')
            );
        }

        try {
            $key = 'login_attempts_'.$portal.'_'.$request->ip();
            $maxAttempts = 10;
            $decayMinutes = 15;

            if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);

                return back()->withErrors([
                    'email' => "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.",
                ])->withInput($request->except('password', 'password_confirmation'));
            }

            $emailLower = strtolower($email);
            $user = $this->resolvePortalLoginUser($portal, $loginAs ?? 'student', $emailLower);

            if (! $user || ! Hash::check($password, $user->password)) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

                return back()->withErrors(['email' => $failureMessage])->withInput(
                    $request->except('password', 'password_confirmation')
                );
            }

            if (! $user->is_active) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);
                $pendingInstructor = ! $isPublic
                    && $user->isInstructor()
                    && $user->instructorProfile?->status === InstructorProfile::STATUS_PENDING_REVIEW;

                return back()->withErrors([
                    'email' => $pendingInstructor
                        ? 'طلب انضمامك قيد المراجعة من الأكاديمية. ستتمكن من تسجيل الدخول بعد الموافقة.'
                        : ($isPublic ? $failureMessage : 'حسابك غير مفعّل. تواصل مع الإدارة.'),
                ])->withInput($request->except('password', 'password_confirmation'));
            }

            if ($isPublic) {
                if (! $user->canUsePublicLoginPortal()) {
                    \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

                    return back()->withErrors(['email' => $failureMessage])->withInput(
                        $request->except('password', 'password_confirmation')
                    );
                }

                if (($loginAs === 'parent' && ! $user->isParent()) || ($loginAs === 'student' && ! $user->isStudent())) {
                    \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

                    return back()->withErrors(['email' => $failureMessage])->withInput(
                        $request->except('password', 'password_confirmation')
                    );
                }
            } elseif (! $user->canUseStaffLoginPortal()) {
                \Illuminate\Support\Facades\RateLimiter::hit($key, $decayMinutes * 60);

                return back()->withErrors(['email' => $failureMessage])->withInput(
                    $request->except('password', 'password_confirmation')
                );
            }

            \Illuminate\Support\Facades\RateLimiter::clear($key);

            if ($user->requiresTwoFactor()) {
                $request->session()->put('login.id', $user->id);
                $request->session()->put('login.remember', $request->boolean('remember'));
                $request->session()->save();
                $code = (string) random_int(100000, 999999);
                Cache::put('2fa_code_'.$user->id, $code, now()->addMinutes(10));
                try {
                    Mail::to($user->email)->send(new TwoFactorCodeMail($code));
                    TwoFactorLog::create([
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'event' => TwoFactorLog::EVENT_CHALLENGE_SENT,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Throwable $e) {
                    report($e);
                    Cache::forget('2fa_code_'.$user->id);

                    return back()->withErrors(['email' => 'تعذر إرسال رمز التحقق. حاول لاحقاً.'])->withInput(
                        $request->except('password', 'password_confirmation')
                    );
                }

                return redirect()->route('two-factor.challenge');
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            \Cache::put('user_session_'.$user->id, $request->session()->getId(), now()->addDays(7));
            $user->update(['last_login_at' => now()]);

            if ($isPublic) {
                return $user->isParent()
                    ? redirect()->intended(route('parent.dashboard'))
                    : redirect()->intended(route('dashboard'));
            }

            if ($user->isEmployee()) {
                if ($user->roles()->exists()) {
                    $adminRoute = RbacAdminRouteAccess::firstPostLoginAdminRouteName($user);
                    if ($adminRoute !== null) {
                        return redirect()->intended(route($adminRoute));
                    }

                    return redirect()->intended(route('employee.dashboard'));
                }

                return redirect()->intended(route('employee.dashboard'));
            }

            if (in_array((string) $user->role, ['super_admin', 'admin'], true)) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->isInstructor()) {
                return redirect()->intended(route('instructor.dashboard'));
            }

            return redirect()->intended(route('dashboard'));
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('خطأ في قاعدة البيانات أثناء تسجيل الدخول', [
                'portal' => $portal,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors(['email' => 'حدث خطأ في النظام. يرجى المحاولة لاحقاً.'])->withInput(
                $request->except('password', 'password_confirmation')
            );
        } catch (\Exception $e) {
            \Log::error('خطأ في تسجيل الدخول', [
                'portal' => $portal,
                'ip' => $request->ip(),
            ]);

            return back()->withErrors(['email' => 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة لاحقاً.'])->withInput(
                $request->except('password', 'password_confirmation')
            );
        }
    }

    private function resolvePortalLoginUser(string $portal, string $loginAs, string $emailLower): ?User
    {
        if ($portal === 'public') {
            if ($loginAs === 'parent') {
                return app(\App\Services\Parent\ParentGuardianService::class)
                    ->resolveParentByStudentEmail($emailLower);
            }

            return User::query()
                ->where('role', 'student')
                ->whereRaw('LOWER(email) = ?', [$emailLower])
                ->first();
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [$emailLower])
            ->where(function ($query) {
                $query->whereIn('role', ['super_admin', 'admin', 'instructor', 'teacher'])
                    ->orWhere('is_employee', true);
            })
            ->first();
    }

    public function showRegister(Request $request)
    {
        // حفظ redirect URL إذا كان موجوداً في session
        if ($request->has('redirect')) {
            session(['register_redirect' => $request->input('redirect')]);
        }

        if ($request->filled('ref')) {
            session([
                'pending_referral_code' => strtoupper(preg_replace('/\s+/', '', (string) $request->query('ref'))),
            ]);
        }

        $pendingReferralCode = session('pending_referral_code');

        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
        $authBackgroundUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            ? asset('storage/' . \App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            : asset('images/brainstorm-meeting.jpg');
        return view('auth.register', compact('phoneCountries', 'defaultCountry', 'authBackgroundUrl', 'pendingReferralCode'));
    }


    public function register(Request $request)
    {
        $countries = config('phone_countries.countries', []);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'onboarding_goal' => 'nullable|string|max:50',
            'onboarding_level' => 'nullable|string|max:50',
            'onboarding_interests' => 'nullable|string|max:500',
            'onboarding_style' => 'nullable|string|max:50',
        ], [
            'name.required' => 'الاسم مطلوب',
            'country_code.required' => 'كود الدولة مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $phoneCountries = $countries;
        $defaultCountry = collect($countries)->firstWhere('code', config('phone_countries.default_country', 'SA'));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with(compact('phoneCountries', 'defaultCountry'));
        }

        // التحقق من صحة رقم الهاتف حسب الدولة
        $resolved = $this->resolveRegistrationPhone(
            (string) $request->country_code,
            (string) $request->phone
        );

        if (! $resolved['valid']) {
            return back()->withErrors(['phone' => $resolved['message']])->withInput()->with(compact('phoneCountries', 'defaultCountry'));
        }

        $fullPhone = $resolved['full_phone'];
        if (User::where('phone', $fullPhone)->exists()) {
            return back()->withErrors(['phone' => 'رقم الهاتف مسجل مسبقاً'])->withInput()->with(compact('phoneCountries', 'defaultCountry'));
        }

        // التسجيل متاح فقط للطلاب
        $onboardingPreferences = array_filter([
            'goal' => $request->input('onboarding_goal'),
            'level' => $request->input('onboarding_level'),
            'interests' => $request->filled('onboarding_interests')
                ? array_values(array_filter(explode(',', (string) $request->input('onboarding_interests'))))
                : null,
            'style' => $request->input('onboarding_style'),
            'completed_at' => now()->toIso8601String(),
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $fullPhone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // فقط طالب
            'is_active' => true,
            'onboarding_preferences' => $onboardingPreferences ?: null,
        ]);

        $referralCode = $request->input('referral_code');
        if ($referralCode === null || $referralCode === '') {
            $referralCode = session('pending_referral_code');
        }
        if ($referralCode === null || $referralCode === '') {
            $referralCode = $request->query('ref');
        }
        $referralCode = $referralCode ? strtoupper(preg_replace('/\s+/', '', (string) $referralCode)) : null;
        session()->forget('pending_referral_code');

        app(\App\Services\Parent\ParentGuardianService::class)->ensureGuardianForStudent($user);

        // معالجة كود الإحالة في Queue لتقليل الضغط على السيرفر
        \App\Jobs\ProcessStudentRegistration::dispatch(
            $user->id,
            $referralCode
        )->onQueue('registrations');

        Auth::login($user);

        // التحقق من وجود redirect URL في session (من صفحة التسجيل)
        $redirectUrl = session('register_redirect');
        if ($redirectUrl) {
            session()->forget('register_redirect');
            $safe = $this->safeRedirectUrl($redirectUrl);
            if ($safe !== null) {
                return redirect($safe);
            }
        }

        if ($request->has('redirect')) {
            $safe = $this->safeRedirectUrl($request->input('redirect'));
            if ($safe !== null) {
                return redirect($safe);
            }
        }

        // بعد إنشاء الحساب نوجّه لصفحة النجاح ثم الداشبورد
        session()->forget('url.intended');
        session([
            'register_complete_name' => $user->name,
            'register_complete_hint' => $this->buildOnboardingHint($onboardingPreferences),
        ]);

        return redirect()->route('register.complete');
    }

    public function showRegisterComplete(Request $request)
    {
        $userName = session('register_complete_name', Auth::user()?->name ?? '');
        $personalizeHint = session('register_complete_hint', '');

        if ($userName === '' && Auth::check()) {
            return redirect()->route('dashboard');
        }

        session()->forget(['register_complete_name', 'register_complete_hint']);

        return view('auth.register-complete', compact('userName', 'personalizeHint'));
    }

    private function buildOnboardingHint(array $prefs): string
    {
        $labels = [
            'grades' => 'تحسين الدرجات',
            'exams' => 'الاستعداد للاختبارات',
            'skills' => 'مهارات جديدة',
            'curriculum' => 'متابعة المنهج',
            'video' => 'محتوى فيديو',
            'interactive' => 'تمارين تفاعلية',
            'reading' => 'مراجع وقراءة',
            'mixed' => 'تعلّم مختلط',
        ];

        $goal = $labels[$prefs['goal'] ?? ''] ?? null;
        $style = $labels[$prefs['style'] ?? ''] ?? null;

        if ($goal && $style) {
            return "جهّزنا لك تجربة تركّز على «{$goal}» بأسلوب «{$style}».";
        }

        return 'جهّزنا لك تجربة تعلّم مخصّصة حسب اختياراتك.';
    }

    /**
     * تحقق فوري أثناء التسجيل: هل البريد/الجوال متاح؟
     */
    public function validateRegisterField(Request $request)
    {
        $field = (string) $request->input('field', '');

        if (! in_array($field, ['name', 'email', 'phone'], true)) {
            return response()->json(['valid' => false, 'available' => false, 'message' => 'حقل غير مدعوم'], 422);
        }

        if ($field === 'name') {
            $name = trim((string) $request->input('value', ''));
            if (mb_strlen($name) < 2) {
                return response()->json(['valid' => false, 'available' => true, 'message' => 'الاسم قصير جداً']);
            }
            if (mb_strlen($name) > 255) {
                return response()->json(['valid' => false, 'available' => true, 'message' => 'الاسم طويل جداً']);
            }

            return response()->json(['valid' => true, 'available' => true, 'message' => 'الاسم مقبول']);
        }

        if ($field === 'email') {
            $email = strtolower(trim((string) $request->input('value', '')));
            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['valid' => false, 'available' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
            }
            $exists = User::query()->whereRaw('LOWER(email) = ?', [$email])->exists();

            return response()->json([
                'valid' => true,
                'available' => ! $exists,
                'message' => $exists ? 'البريد الإلكتروني مسجل مسبقاً' : 'البريد متاح',
            ]);
        }

        $countryCode = (string) $request->input('country_code', '');
        $phone = (string) $request->input('value', '');
        $resolved = $this->resolveRegistrationPhone($countryCode, $phone);

        if (! $resolved['valid']) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'message' => $resolved['message'],
            ]);
        }

        $exists = User::where('phone', $resolved['full_phone'])->exists();

        return response()->json([
            'valid' => true,
            'available' => ! $exists,
            'message' => $exists ? 'رقم الهاتف مسجل مسبقاً' : 'رقم الجوال متاح',
        ]);
    }

    /**
     * @return array{valid: bool, full_phone?: string, message: string}
     */
    private function resolveRegistrationPhone(string $countryCode, string $phone): array
    {
        $countries = config('phone_countries.countries', []);
        $country = collect($countries)->firstWhere('dial_code', $countryCode);

        if (! $country || ! isset($country['validation']['regex'])) {
            return ['valid' => false, 'message' => 'كود الدولة غير مدعوم'];
        }

        $nationalNumber = preg_replace('/\D/', '', $phone);
        $nationalNumber = ltrim($nationalNumber, '0');

        if (! preg_match($country['validation']['regex'], $nationalNumber)) {
            $example = $country['example'] ?? $country['placeholder'] ?? '';

            return ['valid' => false, 'message' => 'رقم الهاتف غير صحيح. مثال: '.$example];
        }

        $dial = $country['dial_code'] ?? '';
        $fullPhone = ($dial === '' || $dial === 'OTHER')
            ? ('OTHER_'.$nationalNumber)
            : ($dial.$nationalNumber);

        return ['valid' => true, 'full_phone' => $fullPhone, 'message' => ''];
    }

    private function safeRedirectUrl(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $targetHost = parse_url($url, PHP_URL_HOST);

        if ($appHost && $targetHost && strcasecmp($appHost, $targetHost) === 0) {
            return $url;
        }

        return null;
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // مسح معرف الجلسة من الكاش لتجنب مشاكل تسجيل الدخول اللاحق
        if ($user) {
            $cacheKey = "user_session_{$user->id}";
            \Cache::forget($cacheKey);
            \Log::info('تسجيل خروج للمستخدم: ' . $user->id . ' - تم مسح الجلسة من الكاش');
        }
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}

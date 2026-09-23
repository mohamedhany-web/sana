<?php

namespace App\Http\Middleware;

use App\Support\UserAppPreferences;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يفرض اللغة الأساسية للتطبيق (اللهجة السعودية ar_SA افتراضياً).
 */
class SetLocale
{
    public const ALLOWED_LOCALES = ['ar_SA', 'ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if ($locale === null && $request->user()) {
            $locale = UserAppPreferences::localeForUser($request->user());
        }

        if ($locale === null) {
            $locale = (string) config('app.locale', 'ar_SA');
        }

        if ($request->has('lang')) {
            $requested = $request->query('lang');
            if ($requested === 'en') {
                $locale = 'en';
            } elseif (in_array($requested, ['ar_SA', 'ar'], true)) {
                $locale = 'ar_SA';
            }
        }

        // توحيد العربية على ar_SA حتى تُحمَّل ملفات lang/ar_SA (أو ar عبر الملفات الموحّدة)
        if ($locale === 'ar' || str_starts_with((string) $locale, 'ar_')) {
            $locale = 'ar_SA';
        } elseif (! str_starts_with((string) $locale, 'en')) {
            $locale = 'ar_SA';
        } else {
            $locale = 'en';
        }

        App::setLocale($locale);
        session(['locale' => $locale, 'landing_locale' => $locale]);

        $isEn = $locale === 'en';
        $isRtl = ! $isEn;
        view()->share([
            'htmlLang' => $isEn ? 'en' : 'ar',
            'htmlDir' => $isEn ? 'ltr' : 'rtl',
            'appLocaleIsEn' => $isEn,
            'appLocale' => $locale,
            'appRtl' => $isRtl,
            'adminRtl' => $isRtl,
            'empRtl' => $isRtl,
            'isRtl' => $isRtl,
            'rtl' => $isRtl,
        ]);

        return $next($request);
    }
}

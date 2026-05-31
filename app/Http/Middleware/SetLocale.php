<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يفرض اللغة الأساسية للتطبيق (اللهجة السعودية ar_SA افتراضياً).
 */
class SetLocale
{
    public const ALLOWED_LOCALES = ['ar_SA', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) config('app.locale', 'ar_SA');

        if ($request->has('lang')) {
            $requested = $request->query('lang');
            if (in_array($requested, self::ALLOWED_LOCALES, true)) {
                $locale = $requested;
            }
        }

        App::setLocale($locale);
        session(['locale' => $locale, 'landing_locale' => $locale]);

        return $next($request);
    }
}

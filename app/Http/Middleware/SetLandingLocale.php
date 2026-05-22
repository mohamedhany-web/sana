<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يفرض اللغة العربية على صفحات الهبوط العامة.
 * يُطبّق فقط على المسارات التي نحددها (الصفحة الرئيسية والصفحات العامة) دون التأثير على لوحة التحكم.
 */
class SetLandingLocale
{
    public const ALLOWED_LOCALES = ['ar'];

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale('ar');
        session(['landing_locale' => 'ar']);

        return $next($request);
    }
}

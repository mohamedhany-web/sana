<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * يفرض اللغة العربية على جميع مسارات الويب.
 */
class SetLocale
{
    public const ALLOWED_LOCALES = ['ar'];

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale('ar');
        session(['locale' => 'ar', 'landing_locale' => 'ar']);

        return $next($request);
    }
}

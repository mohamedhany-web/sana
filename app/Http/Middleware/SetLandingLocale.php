<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLandingLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) config('app.locale', 'ar_SA');
        App::setLocale($locale);
        session(['landing_locale' => $locale]);

        return $next($request);
    }
}

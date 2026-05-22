<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يمنع المتصفح من الاحتفاظ بنسخة قديمة من صفحات HTML أثناء التطوير
 * (وعند تفعيل PREVENT_BROWSER_HTML_CACHE في الإنتاج).
 */
class PreventBrowserHtmlCacheMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldApply($request, $response)) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    private function shouldApply(Request $request, Response $response): bool
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return false;
        }

        if (config('app.debug') || filter_var(env('PREVENT_BROWSER_HTML_CACHE', false), FILTER_VALIDATE_BOOLEAN)) {
            $type = (string) $response->headers->get('Content-Type', '');

            return str_contains($type, 'text/html');
        }

        return false;
    }
}

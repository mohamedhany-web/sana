<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * صفحة استخدامات AI: صلاحية RBAC + باقة نشطة تتضمن أدوات AI.
 */
class EnsureStudentAiUsagesAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (! $user->hasPermission('student.view.ai-usages')) {
            abort(403, __('student.ai_usages.no_permission'));
        }

        if (! $user->hasSubscriptionFeature('full_ai_suite') && ! $user->hasSubscriptionFeature('ai_tools')) {
            abort(403, __('student.ai_usages.subscription_required'));
        }

        return $next($request);
    }
}

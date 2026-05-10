<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * صفحة استخدامات AI: طالب + باقة AI؛ صلاحية RBAC عند ربطها بدور الطالب (انظر User::canAccessStudentAiUsages).
 */
class EnsureStudentAiUsagesAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if ($user->canAccessStudentAiUsages()) {
            return $next($request);
        }

        if (! $user->isStudent()) {
            abort(403, __('student.ai_usages.no_permission'));
        }

        $hasAi = $user->hasSubscriptionFeature('full_ai_suite') || $user->hasSubscriptionFeature('ai_tools');
        abort(403, $hasAi ? __('student.ai_usages.no_permission') : __('student.ai_usages.subscription_required'));
    }
}

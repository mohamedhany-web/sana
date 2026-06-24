<?php

namespace App\Http\Middleware;

use App\Models\InstructorProfile;
use App\Services\TutorApplicationFormService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTutorApplyPolicyAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return $next($request);
        }

        $profile = $user->instructorProfile;
        if (! $profile || $profile->status !== InstructorProfile::STATUS_PENDING_REVIEW) {
            return $next($request);
        }

        if (TutorApplicationFormService::hasAcceptedPolicy($profile)) {
            return $next($request);
        }

        if ($request->routeIs('tutor.apply.policy', 'tutor.apply.policy.accept', 'logout')) {
            return $next($request);
        }

        return redirect()
            ->route('tutor.apply.policy')
            ->with('info', 'يرجى قراءة سياسة انضمام المعلمين والموافقة عليها لإكمال حسابك.');
    }
}

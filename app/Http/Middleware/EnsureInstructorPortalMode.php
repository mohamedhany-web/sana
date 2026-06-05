<?php

namespace App\Http\Middleware;

use App\Support\InstructorPortalAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstructorPortalMode
{
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $allowed = match ($portal) {
            'tutor_lessons' => InstructorPortalAccess::hasTutorLessonsPortal($user),
            'courses' => InstructorPortalAccess::hasCoursesPortal($user),
            default => false,
        };

        if (! $allowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'هذا القسم غير متاح لنوع حسابك كمعلم.'], 403);
            }

            return redirect()
                ->route(InstructorPortalAccess::homeRoute($user))
                ->with('error', 'هذا القسم غير متاح لنوع حسابك كمعلم.');
        }

        return $next($request);
    }
}

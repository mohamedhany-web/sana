<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentCoursesEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('student.courses_enabled')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'نظام الكورسات غير متاح حالياً.'], 403);
        }

        return redirect()
            ->route('dashboard')
            ->with('info', 'نظام الكورسات غير متاح حالياً.');
    }
}

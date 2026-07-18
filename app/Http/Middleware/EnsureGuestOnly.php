<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من أن المستخدم غير مسجل دخول
 * يستخدم لصفحات التسجيل والدخول فقط
 */
class EnsureGuestOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // إذا كان المستخدم مسجل دخول، يتم توجيهه للـ dashboard المناسب
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('info', 'أنت مسجل دخول بالفعل');
            }

            if ($user->isInstructor() || $user->isTeacher()) {
                // معلّم بانتظار التفعيل / إكمال السياسة — لا يُرمى لصفحة كورسات قد تفشل
                if (! $user->is_active) {
                    return redirect(\App\Services\TutorApplicationFormService::postApplyRedirect($user))
                        ->with('info', 'لديك طلب انضمام قيد المراجعة. أكمل الخطوات من هنا.');
                }

                return redirect()->route(\App\Support\InstructorPortalAccess::homeRoute($user))
                    ->with('info', 'أنت مسجل دخول بالفعل');
            }

            return redirect()->route('dashboard')->with('info', 'أنت مسجل دخول بالفعل');
        }

        return $next($request);
    }
}

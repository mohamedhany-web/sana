<?php

namespace App\Http\Middleware;

use App\Support\AuthLoginRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من الصلاحيات المحددة
 * يستخدم للتحقق من أن المستخدم لديه صلاحية معينة للوصول إلى المورد
 */
class EnsurePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  اسم الصلاحية المطلوبة
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect(AuthLoginRedirect::guestLoginUrl())->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        // Super Admin بدون أدوار RBAC مخصصة → يتجاوز كل الفحوصات
        if ($user->isAdmin() && ! $user->hasAssignedRbacRoles()) {
            return $next($request);
        }

        // admin.access: السماح لأي موظف يملك دوراً RBAC مخصصاً
        // (كل الأدوار الأخرى تُتحقق منها بصلاحياتها الفعلية في السايدبار)
        if ($permission === 'admin.access' && $user->is_employee && $user->hasAssignedRbacRoles()) {
            return $next($request);
        }

        // التحقق من الصلاحية
        if (!$user->hasPermission($permission)) {
            abort(403, 'ليس لديك الصلاحية للوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}

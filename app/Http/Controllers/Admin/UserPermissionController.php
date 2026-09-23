<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * عرض قائمة المستخدمين وصلاحياتهم
     */
    public function index()
    {
        $users = User::with(['roles.permissions', 'directPermissions'])
            ->orderBy('name')
            ->paginate(20);
        
        $allPermissions = Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');
        
        return view('admin.user-permissions.index', compact('users', 'allPermissions'));
    }

    /**
     * عرض صلاحيات مستخدم معين
     */
    public function show(User $user)
    {
        $user->loadMissing(['roles']);

        // الحصول على جميع الصلاحيات (من الأدوار + المباشرة)
        $rolePermissions = $user->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->unique('id');
        
        $directPermissions = $user->directPermissions()->get();
        
        // دمج الصلاحيات مع إزالة التكرار
        $allUserPermissions = $rolePermissions->merge($directPermissions)->unique('id')->sortBy(function ($p) {
            return ($p->group ?? '') . '|' . ($p->display_name ?? '');
        })->values();

        $userPermissionsGrouped = $allUserPermissions->groupBy('group');

        $allRoles = Role::orderBy('is_system', 'desc')
            ->orderBy('display_name')
            ->get();

        return view('admin.user-permissions.show', compact(
            'user',
            'allUserPermissions',
            'userPermissionsGrouped',
            'rolePermissions',
            'directPermissions',
            'allRoles',
        ));
    }

    /**
     * تحديث أدوار مستخدم
     */
    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $roleIds = array_values(array_unique(array_map('intval', $request->roles ?? [])));
        $user->roles()->sync($roleIds);

        // صلاحيات الدور تُفعَّل لمسارات الأدمن عندما يكون المستخدم موظفاً (RBAC + وسيط rbac.strict)
        if ($roleIds !== [] && ! $user->is_employee) {
            $user->forceFill(['is_employee' => true])->save();
        }

        return redirect()
            ->route('admin.user-permissions.show', $user)
            ->with('success', __('تم تحديث أدوار المستخدم بنجاح'));
    }

    /**
     * تحديث صلاحيات مستخدم
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // تحديث الصلاحيات المباشرة
        $user->directPermissions()->sync($request->permissions ?? []);

        return redirect()
            ->route('admin.user-permissions.show', $user)
            ->with('success', __('تم تحديث صلاحيات المستخدم بنجاح'));
    }

    /**
     * إضافة صلاحية لمستخدم
     */
    public function attachPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        if (!$user->directPermissions()->where('permission_id', $request->permission_id)->exists()) {
            $user->directPermissions()->attach($request->permission_id);
            
            return response()->json([
                'success' => true,
                'message' => __('تم إضافة الصلاحية بنجاح')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('الصلاحية موجودة بالفعل')
        ], 400);
    }

    /**
     * إزالة صلاحية من مستخدم
     */
    public function detachPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $user->directPermissions()->detach($request->permission_id);
        
        return response()->json([
            'success' => true,
            'message' => __('تم إزالة الصلاحية بنجاح')
        ]);
    }
}

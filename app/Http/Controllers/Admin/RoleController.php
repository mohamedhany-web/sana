<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Support\AdminSidebarRoleMap;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])
            ->orderBy('is_system', 'desc')
            ->orderBy('display_name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('roles', 'name'),
            ],
            'display_name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['numeric', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        $permIds = array_map('intval', $validated['permissions'] ?? []);
        $role->permissions()->sync($this->permissionIdsAlwaysIncludeDashboard($permIds));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('تم إنشاء الدور بنجاح'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        $permissions = Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        $adminSidebarBlocks = AdminSidebarRoleMap::blocksForView();
        $sidebarMapPermissionIds = AdminSidebarRoleMap::permissionIdsInSidebarMap();
        $otherPermissions = Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->filter(static fn (Permission $p) => ! in_array($p->id, $sidebarMapPermissionIds, true))
            ->groupBy('group');

        return view('admin.roles.show', compact(
            'role',
            'permissions',
            'adminSidebarBlocks',
            'otherPermissions',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $role->load(['permissions']);

        $permissions = Permission::orderBy('group')
            ->orderBy('display_name')
            ->get()
            ->groupBy('group');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'display_name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['numeric', 'exists:permissions,id'],
        ]);

        // Protect system roles from name change
        if ($role->is_system) {
            unset($validated['name']);
        }

        $role->update([
            'name' => $validated['name'] ?? $role->name,
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
        ]);

        $permIds = array_map('intval', $validated['permissions'] ?? []);
        $role->permissions()->sync($this->permissionIdsAlwaysIncludeDashboard($permIds));

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('تم تحديث الدور بنجاح'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', __('لا يمكن حذف دور نظامي'));
        }

        // Detach relations first (safety)
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('تم حذف الدور بنجاح'));
    }

    /**
     * تحديث صلاحيات دور معين من صفحة التفاصيل
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['numeric', 'exists:permissions,id'],
        ]);

        $ids = array_map('intval', $validated['permissions'] ?? []);
        $role->permissions()->sync($this->permissionIdsAlwaysIncludeDashboard($ids));

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('success', 'تم تحديث صلاحيات الدور "' . $role->display_name . '" بنجاح');
    }

    /**
     * صلاحية لوحة التحكم (view.dashboard) تُربَط تلقائياً بكل دور؛ باقي الصلاحيات تُحدَّد يدوياً.
     *
     * @param  array<int, int|string>  $ids
     * @return array<int, int>
     */
    private function permissionIdsAlwaysIncludeDashboard(array $ids): array
    {
        $dashboardId = Permission::where('name', 'view.dashboard')->value('id');
        $ids = array_map('intval', array_filter($ids, static fn ($v) => $v !== null && $v !== ''));

        if ($dashboardId) {
            $ids[] = (int) $dashboardId;
        }

        return array_values(array_unique($ids));
    }
}

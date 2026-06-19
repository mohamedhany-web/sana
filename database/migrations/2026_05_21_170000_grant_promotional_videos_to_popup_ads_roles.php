<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'manage.promotional-videos')->value('id');
        if (! $permissionId) {
            return;
        }

        $popupAdsPermissionId = DB::table('permissions')->where('name', 'manage.popup-ads')->value('id');
        if (! $popupAdsPermissionId) {
            return;
        }

        $roleIds = DB::table('role_permissions')
            ->where('permission_id', $popupAdsPermissionId)
            ->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (! $exists) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // لا حذف تلقائي — قد تكون الصلاحية مُمنحة يدوياً
    }
};

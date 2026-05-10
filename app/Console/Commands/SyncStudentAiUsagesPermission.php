<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

/**
 * للإنتاج بعد النشر: إنشاء الصلاحية وربطها بدور الطالب دون إعادة تشغيل كامل PermissionsAndRolesSeeder.
 */
class SyncStudentAiUsagesPermission extends Command
{
    protected $signature = 'student:sync-ai-usages-permission';

    protected $description = 'Ensure permission student.view.ai-usages exists and is assigned to the student role';

    public function handle(): int
    {
        $perm = Permission::updateOrCreate(
            ['name' => 'student.view.ai-usages'],
            [
                'display_name' => 'عرض استخدامات AI',
                'description' => 'الوصول لصفحة الألعاب/الملفات المحفوظة من أدوات AI (مع اشتراك يتضمن AI)',
                'group' => 'صلاحيات الطالب',
            ]
        );

        $studentRole = Role::where('name', 'student')->first();
        if (! $studentRole) {
            $this->error('دور الطالب (student) غير موجود. شغّل PermissionsAndRolesSeeder أولاً.');

            return self::FAILURE;
        }

        if (! $studentRole->permissions()->where('permissions.id', $perm->id)->exists()) {
            $studentRole->permissions()->attach($perm->id);
            $this->info('تم ربط الصلاحية student.view.ai-usages بدور الطالب.');
        } else {
            $this->info('الصلاحية student.view.ai-usages مرتبطة بدور الطالب مسبقاً.');
        }

        return self::SUCCESS;
    }
}
